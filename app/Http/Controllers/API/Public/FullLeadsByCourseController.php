<?php

namespace App\Http\Controllers\API\Public;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\ConvertedLead;
use App\Models\Lead;
use App\Models\LeadDetail;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Public export API for syncing full lead → registration → student payloads
 * into another CRM (shaped to that CRM's leads / lead_details / students tables).
 *
 * GET /api/v1/public/leads/by-course/{course_id}
 * Auth: X-CRM-API-KEY header (config services.lms.api_key / CRM_API_KEY)
 */
class FullLeadsByCourseController extends Controller
{
    private const DOCUMENT_FIELDS = [
        'passport_photo' => 'passport_photo_verification_status',
        'adhar_front' => 'adhar_front_verification_status',
        'adhar_back' => 'adhar_back_verification_status',
        'birth_certificate' => 'birth_certificate_verification_status',
        'signature' => 'signature_verification_status',
        'other_document' => 'other_document_verification_status',
        'sslc_certificate' => 'sslc_verification_status',
        'plustwo_certificate' => 'plustwo_verification_status',
        'ug_certificate' => 'ug_verification_status',
        'post_graduation_certificate' => 'post_graduation_certificate_verification_status',
    ];

    public function __invoke(Request $request, int $courseId): JsonResponse
    {
        if (!$this->isAuthorized($request)) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Provide a valid X-CRM-API-KEY header.',
            ], 401);
        }

        $validator = Validator::make(
            array_merge($request->all(), ['course_id' => $courseId]),
            [
                'course_id' => 'required|integer|exists:courses,id',
                'per_page' => 'nullable|integer|min:1|max:200',
                'page' => 'nullable|integer|min:1',
                'is_converted' => 'nullable|boolean',
                'only_with_registration' => 'nullable|boolean',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $course = Course::query()->select('id', 'title')->find($courseId);

        if (!$course) {
            return response()->json([
                'status' => false,
                'message' => 'Course not found.',
            ], 404);
        }

        $query = Lead::query()
            ->withoutGlobalScope('exclude_pullbacked')
            ->where('course_id', $courseId)
            ->with([
                'studentDetails.sslcCertificates',
                'convertedLead.studentDetails',
                'batch:id,title',
            ])
            ->orderBy('id');

        if ($request->has('is_converted')) {
            $query->where('is_converted', $request->boolean('is_converted'));
        }

        if ($request->boolean('only_with_registration')) {
            $query->whereHas('studentDetails');
        }

        $perPage = (int) $request->get('per_page', 50);
        $paginator = $query->paginate($perPage);

        $records = $paginator->getCollection()->map(function (Lead $lead) {
            return $this->formatImportRecord($lead);
        })->values();

        return response()->json([
            'status' => true,
            'message' => 'Full leads fetched successfully.',
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
            ],
            'data' => $records,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ]);
    }

    private function isAuthorized(Request $request): bool
    {
        $configuredKey = (string) config('services.lms.api_key');

        if ($configuredKey === '') {
            return false;
        }

        $provided = (string) (
            $request->header('X-CRM-API-KEY')
            ?? $request->header('X-Api-Key')
            ?? $request->query('api_key')
            ?? ''
        );

        return hash_equals($configuredKey, $provided);
    }

    /**
     * Shape one lead into the destination CRM import payload.
     */
    private function formatImportRecord(Lead $lead): array
    {
        $detail = $lead->studentDetails;
        $converted = $lead->convertedLead;

        return [
            'source_lead_id' => $lead->id,
            'lead' => $this->formatLead($lead, $detail, $converted),
            'registration' => $detail ? $this->formatRegistration($detail, $lead) : null,
            'documents' => $detail ? $this->formatDocuments($detail) : null,
            'student' => $converted ? $this->formatStudent($converted) : null,
            'student_details' => $converted ? $this->formatStudentDetails($converted) : null,
            'converted_lead' => $converted ? $this->formatConvertedLeadRaw($converted) : null,
        ];
    }

    private function formatLead(Lead $lead, ?LeadDetail $detail, ?ConvertedLead $converted): array
    {
        $dob = $detail?->date_of_birth
            ? $detail->date_of_birth->format('Y-m-d')
            : ($converted?->dob ? $this->formatDate($converted->dob) : null);

        $batchId = $lead->batch_id
            ?? $detail?->batch_id
            ?? $converted?->batch_id;

        $convertedAt = null;
        if ($lead->is_converted) {
            $convertedAt = $converted?->created_at
                ? $converted->created_at->format('Y-m-d H:i:s')
                : ($lead->updated_at ? $lead->updated_at->format('Y-m-d H:i:s') : null);
        }

        return [
            'id' => $lead->id,
            'name' => $lead->title,
            'code' => $lead->code,
            'phone' => $lead->phone,
            'gender' => $lead->gender ?? $detail?->gender,
            'dob' => $dob,
            'whatsapp_code' => $lead->whatsapp_code ?? $detail?->whatsapp_code,
            'whatsapp' => $lead->whatsapp ?? $detail?->whatsapp_number,
            'email' => $lead->email ?? $detail?->email,
            'qualification' => $lead->qualification,
            'country_id' => $lead->country_id,
            'interest_status' => $this->normalizeInterestStatus($lead->interest_status),
            'rating' => $lead->rating,
            'lead_status_id' => $lead->lead_status_id,
            'first_lead_status_id' => $lead->first_lead_status_id,
            'lead_source_id' => $lead->lead_source_id,
            'first_lead_source_id' => $lead->first_lead_source_id,
            'address' => $lead->address ?: $this->buildAddressFromDetail($detail),
            'team_id' => $lead->team_id,
            'telecaller_id' => $lead->telecaller_id,
            'place' => $lead->place,
            'course_id' => $lead->course_id,
            'first_lead_course_id' => $lead->first_lead_course_id,
            'batch_id' => $batchId,
            'by_meta' => (bool) $lead->by_meta,
            'is_converted' => (bool) $lead->is_converted,
            'converted_at' => $convertedAt,
            'followup_date' => $lead->followup_date
                ? $lead->followup_date->format('Y-m-d')
                : null,
            'remarks' => $lead->remarks,
            'created_at' => $lead->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $lead->updated_at?->format('Y-m-d H:i:s'),
            'first_created_at' => $lead->first_created_at
                ? $lead->first_created_at->format('Y-m-d H:i:s')
                : $lead->created_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $lead->deleted_at?->format('Y-m-d H:i:s'),
            'created_by' => $lead->created_by,
            'updated_by' => $lead->updated_by,
            'deleted_by' => $lead->deleted_by,
            // Source-only extras (ignored by destination if unused)
            'age' => $lead->age,
            'university_id' => $lead->university_id,
            'is_b2b' => (bool) ($lead->is_b2b ?? false),
        ];
    }

    private function formatRegistration(LeadDetail $detail, Lead $lead): array
    {
        $documentProof = $this->resolveDocumentProof($detail);
        $passportPath = $this->normalizeStoragePath($detail->passport_photo);
        $documentProofPath = $this->normalizeStoragePath($documentProof['path'] ?? null);

        return [
            'id' => $detail->id,
            'lead_id' => $detail->lead_id,
            'student_name' => $detail->student_name ?: $lead->title,
            'father_name' => $detail->father_name,
            'mother_name' => $detail->mother_name,
            'date_of_birth' => $detail->date_of_birth
                ? $detail->date_of_birth->format('Y-m-d')
                : null,
            'parents_code' => $detail->parents_code ?: $detail->father_contact_code,
            'parents_number' => $detail->parents_number ?: $detail->father_contact_number,
            'whatsapp_code' => $detail->whatsapp_code ?: $lead->whatsapp_code,
            'whatsapp_number' => $detail->whatsapp_number ?: $lead->whatsapp,
            'batch_id' => $detail->batch_id ?: $lead->batch_id,
            'street' => $detail->street,
            'locality' => $detail->locality,
            'post_office' => $detail->post_office,
            'district' => $detail->district,
            'state' => $detail->state,
            'pin_code' => $detail->pin_code,
            'status' => $detail->status ?? 'pending',
            'passport_photo' => $passportPath,
            'document_proof' => $documentProofPath,
            'passport_photo_verification_status' => $detail->passport_photo_verification_status ?? 'pending',
            'document_proof_verification_status' => $documentProof['verification_status'] ?? 'pending',
            'passport_photo_verified_by' => $detail->passport_photo_verified_by,
            'passport_photo_verified_at' => $detail->passport_photo_verified_at
                ? $detail->passport_photo_verified_at->format('Y-m-d H:i:s')
                : null,
            'document_proof_verified_by' => $documentProof['verified_by'] ?? null,
            'document_proof_verified_at' => $documentProof['verified_at'] ?? null,
            'admin_remarks' => $detail->admin_remarks,
            'reviewed_by' => $detail->reviewed_by,
            'reviewed_at' => $detail->reviewed_at
                ? $detail->reviewed_at->format('Y-m-d H:i:s')
                : null,
            'created_at' => $detail->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $detail->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $detail->deleted_at?->format('Y-m-d H:i:s'),
            'created_by' => $detail->created_by ?? null,
            'updated_by' => $detail->updated_by ?? null,
            'deleted_by' => $detail->deleted_by ?? null,
            'documents' => [
                'passport_photo_file' => $this->buildFileUrl($detail->passport_photo),
                'passport_photo_path' => $passportPath,
                'document_proof_file' => $this->buildFileUrl($documentProof['path'] ?? null),
                'document_proof_path' => $documentProofPath,
                'document_proof_source_field' => $documentProof['source_field'] ?? null,
            ],
            // Source extras useful for richer imports
            'source_extras' => [
                'course_id' => $detail->course_id,
                'addon_course_id' => $detail->addon_course_id,
                'gender' => $detail->gender,
                'email' => $detail->email,
                'personal_code' => $detail->personal_code ?: $lead->code,
                'personal_number' => $detail->personal_number ?: $lead->phone,
                'residential_address' => $detail->residential_address,
                'subject_id' => $detail->subject_id,
                'sub_course_id' => $detail->sub_course_id,
                'class_time_id' => $detail->class_time_id,
                'programme_type' => $detail->programme_type,
                'location' => $detail->location,
                'university_id' => $detail->university_id,
                'university_course_id' => $detail->university_course_id,
            ],
        ];
    }

    private function formatDocuments(LeadDetail $detail): array
    {
        $documents = [];

        foreach (self::DOCUMENT_FIELDS as $field => $statusField) {
            $path = $detail->{$field} ?? null;

            if ($field === 'sslc_certificate'
                && $detail->relationLoaded('sslcCertificates')
                && $detail->sslcCertificates
                && $detail->sslcCertificates->isNotEmpty()
            ) {
                continue;
            }

            if (empty($path)) {
                continue;
            }

            $verifiedAtField = str_replace('_verification_status', '_verified_at', $statusField);
            $verifiedByField = str_replace('_verification_status', '_verified_by', $statusField);

            $documents[$field] = [
                'path' => $this->normalizeStoragePath($path),
                'disk_path' => $this->diskRelativePath($path),
                'url' => $this->buildFileUrl($path),
                'verification_status' => $detail->{$statusField} ?? 'pending',
                'verified_by' => $detail->{$verifiedByField} ?? null,
                'verified_at' => isset($detail->{$verifiedAtField}) && $detail->{$verifiedAtField}
                    ? $detail->{$verifiedAtField}->format('Y-m-d H:i:s')
                    : null,
            ];
        }

        if ($detail->relationLoaded('sslcCertificates') && $detail->sslcCertificates) {
            $documents['sslc_certificates'] = $detail->sslcCertificates->map(function ($certificate) {
                $path = $certificate->certificate_path ?? $certificate->file_path ?? null;

                return [
                    'id' => $certificate->id,
                    'path' => $this->normalizeStoragePath($path),
                    'disk_path' => $this->diskRelativePath($path),
                    'url' => $this->buildFileUrl($path),
                    'original_filename' => $certificate->original_filename ?? null,
                    'verification_status' => $certificate->verification_status ?? 'pending',
                    'verified_by' => $certificate->verified_by,
                    'verified_at' => $certificate->verified_at
                        ? $certificate->verified_at->format('Y-m-d H:i:s')
                        : null,
                ];
            })->values()->all();
        }

        return $documents;
    }

    private function formatStudent(ConvertedLead $converted): array
    {
        return [
            'id' => $converted->id,
            'lead_id' => $converted->lead_id,
            'name' => $converted->name,
            'register_number' => $converted->register_number,
            'is_academic_verified' => (bool) $converted->is_academic_verified,
            'academic_verified_by' => $converted->academic_verified_by,
            'academic_verified_at' => $converted->academic_verified_at
                ? $converted->academic_verified_at->format('Y-m-d H:i:s')
                : null,
            'is_support_verified' => (bool) $converted->is_support_verified,
            'support_verified_by' => $converted->support_verified_by,
            'support_verified_at' => $converted->support_verified_at
                ? $converted->support_verified_at->format('Y-m-d H:i:s')
                : null,
            'is_cancelled' => (bool) $converted->is_cancelled,
            'cancelled_by' => $converted->cancelled_by,
            'cancelled_at' => $converted->cancelled_at
                ? $converted->cancelled_at->format('Y-m-d H:i:s')
                : null,
            'cancel_remark' => $converted->cancel_remark,
            'remarks' => $converted->remarks,
            'created_at' => $converted->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $converted->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $converted->deleted_at?->format('Y-m-d H:i:s'),
            'created_by' => $converted->created_by,
            'updated_by' => $converted->updated_by,
            'deleted_by' => $converted->deleted_by,
        ];
    }

    private function formatStudentDetails(ConvertedLead $converted): array
    {
        $details = $converted->studentDetails;

        return [
            'id' => $details?->id,
            'converted_lead_id' => $converted->id,
            'flag_id' => $converted->flag_id,
            'support_flag_id' => $converted->support_flag_id,
            'faculty_flag_id' => $converted->course_flag_id,
            'faculty_team_id' => null,
            'faculty_team_head_id' => null,
            'faculty_id' => $converted->faculty_id,
            'called_time' => $this->formatTimeValue($converted->called_time),
            'screening_date' => $details?->screening
                ? $this->formatDate($details->screening)
                : null,
            'class_status' => $details?->class_status,
            'remarks' => $details?->remarks,
            'continuing_studies' => $details?->continuing_studies,
            'reason' => $details?->reason,
            'created_at' => $details?->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $details?->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $details?->deleted_at?->format('Y-m-d H:i:s'),
            'created_by' => null,
            'updated_by' => null,
            'deleted_by' => $details?->deleted_by,
        ];
    }

    /**
     * Full source converted_leads row (for consumers that need source fields).
     */
    private function formatConvertedLeadRaw(ConvertedLead $converted): array
    {
        return [
            'id' => $converted->id,
            'lead_id' => $converted->lead_id,
            'name' => $converted->name,
            'code' => $converted->code,
            'phone' => $converted->phone,
            'email' => $converted->email,
            'dob' => $this->formatDate($converted->dob),
            'register_number' => $converted->register_number,
            'course_id' => $converted->course_id,
            'batch_id' => $converted->batch_id,
            'admission_batch_id' => $converted->admission_batch_id,
            'sub_course_id' => $converted->sub_course_id,
            'university_id' => $converted->university_id,
            'subject_id' => $converted->subject_id,
            'board_id' => $converted->board_id,
            'flag_id' => $converted->flag_id,
            'support_flag_id' => $converted->support_flag_id,
            'course_flag_id' => $converted->course_flag_id,
            'faculty_id' => $converted->faculty_id,
            'academic_assistant_id' => $converted->academic_assistant_id,
            'status' => $converted->status,
            'postsale_status' => $converted->postsale_status,
            'paid_status' => $converted->paid_status,
            'call_status' => $converted->call_status,
            'is_academic_verified' => (bool) $converted->is_academic_verified,
            'is_support_verified' => (bool) $converted->is_support_verified,
            'is_cancelled' => (bool) $converted->is_cancelled,
            'is_b2b' => (bool) $converted->is_b2b,
            'remarks' => $converted->remarks,
            'created_at' => $converted->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $converted->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Destination CRM only has document_proof; map best available source ID/proof file.
     */
    private function resolveDocumentProof(LeadDetail $detail): array
    {
        $candidates = [
            'other_document' => [
                'status' => 'other_document_verification_status',
                'by' => 'other_document_verified_by',
                'at' => 'other_document_verified_at',
            ],
            'adhar_front' => [
                'status' => 'adhar_front_verification_status',
                'by' => 'adhar_front_verified_by',
                'at' => 'adhar_front_verified_at',
            ],
            'adhar_back' => [
                'status' => 'adhar_back_verification_status',
                'by' => 'adhar_back_verified_by',
                'at' => 'adhar_back_verified_at',
            ],
            'birth_certificate' => [
                'status' => 'birth_certificate_verification_status',
                'by' => 'birth_certificate_verified_by',
                'at' => 'birth_certificate_verified_at',
            ],
        ];

        foreach ($candidates as $field => $meta) {
            $path = $detail->{$field} ?? null;
            if (empty($path)) {
                continue;
            }

            $verifiedAt = $detail->{$meta['at']} ?? null;

            return [
                'source_field' => $field,
                'path' => $path,
                'verification_status' => $detail->{$meta['status']} ?? 'pending',
                'verified_by' => $detail->{$meta['by']} ?? null,
                'verified_at' => $verifiedAt
                    ? (is_string($verifiedAt) ? $verifiedAt : $verifiedAt->format('Y-m-d H:i:s'))
                    : null,
            ];
        }

        return [
            'source_field' => null,
            'path' => null,
            'verification_status' => 'pending',
            'verified_by' => null,
            'verified_at' => null,
        ];
    }

    private function buildAddressFromDetail(?LeadDetail $detail): ?string
    {
        if (!$detail) {
            return null;
        }

        $parts = array_filter([
            $detail->street,
            $detail->locality,
            $detail->post_office,
            $detail->district,
            $detail->state,
            $detail->pin_code,
        ], fn ($value) => filled($value));

        return $parts === [] ? ($detail->residential_address ?: null) : implode(', ', $parts);
    }

    private function normalizeInterestStatus(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return match ((int) $value) {
                1 => 'hot',
                2 => 'warm',
                3 => 'cold',
                default => null,
            };
        }

        $normalized = strtolower((string) $value);

        return in_array($normalized, ['hot', 'warm', 'cold'], true) ? $normalized : $normalized;
    }

    /**
     * Destination expects paths like storage/student-documents/{file}.
     */
    private function normalizeStoragePath(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        $path = ltrim(str_replace('\\', '/', $path), '/');

        if (Str::startsWith($path, 'storage/')) {
            return $path;
        }

        if (Str::startsWith($path, 'public/')) {
            $path = Str::after($path, 'public/');
        }

        return 'storage/' . ltrim($path, '/');
    }

    private function diskRelativePath(?string $path): ?string
    {
        if (empty($path) || Str::startsWith($path, ['http://', 'https://'])) {
            return null;
        }

        $path = ltrim(str_replace('\\', '/', $path), '/');

        if (Str::startsWith($path, 'storage/')) {
            $path = Str::after($path, 'storage/');
        }

        if (Str::startsWith($path, 'public/')) {
            $path = Str::after($path, 'public/');
        }

        return $path;
    }

    private function buildFileUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        $diskPath = $this->diskRelativePath($path);
        if (!$diskPath) {
            return null;
        }

        /** @var FilesystemAdapter $publicDisk */
        $publicDisk = Storage::disk('public');

        if ($publicDisk->exists($diskPath)) {
            return $publicDisk->url($diskPath);
        }

        return asset('storage/' . ltrim($diskPath, '/'));
    }

    private function formatDate(mixed $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            return is_string($value) ? $value : null;
        }
    }

    private function formatTimeValue(mixed $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('H:i:s');
        }

        try {
            return \Carbon\Carbon::parse($value)->format('H:i:s');
        } catch (\Throwable) {
            return is_string($value) ? $value : null;
        }
    }
}
