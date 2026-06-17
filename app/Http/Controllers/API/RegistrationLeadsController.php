<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ClassTime;
use App\Models\Lead;
use App\Models\LeadDetail;
use App\Models\LeadStatus;
use App\Models\LeadSource;
use App\Models\Course;
use App\Models\Country;
use App\Models\User;
use App\Models\ConvertedLead;
use App\Models\Board;
use App\Models\Batch;
use App\Models\University;
use App\Models\LeadActivity;
use App\Models\Subject;
use App\Models\SubCourse;
use App\Models\SSLCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Log;
use App\Helpers\PaymentProofHelper;

class RegistrationLeadsController extends Controller
{
    /**
     * List registration-form-submitted leads with lazy loading & filters.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        if (!$this->canAccessRegistrationData($user)) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied.',
            ], 403);
        }

        $query = $this->buildBaseQuery();
        $this->applyFilters($query, $request, ['skip_registration_status' => true]); // Skip status filter to get all leads
        $this->applyRoleRestrictions($query, $user, $request);

        // Get all leads (no pagination) to group by status
        $allLeads = (clone $query)
            ->orderBy('id', 'desc')
            ->get();

        // Transform and group leads by registration status
        $groupedLeads = [
            'pending' => [],
            'approved' => [],
            'rejected' => [],
        ];

        foreach ($allLeads as $lead) {
            $transformedLead = $this->transformLead($lead);
            $status = $transformedLead['registration_status'] ?? 'pending';
            
            // Only include in grouped array if status is one of the expected values
            if (in_array($status, ['pending', 'approved', 'rejected'])) {
                $groupedLeads[$status][] = $transformedLead;
            }
        }

        $counts = $this->calculateRegistrationCounts($user, $request);
        
        // Get count of registration leads (filtered data count)
        $registrationLeadsCount = $allLeads->count();

        return response()->json([
            'status' => true,
            'data' => [
                'leads' => $groupedLeads,
                'counts' => $counts,
                'registration_leads_count' => $registrationLeadsCount,
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => $allLeads->count(),
                    'total' => $allLeads->count(),
                    'last_page' => 1,
                    'from' => $allLeads->count() > 0 ? 1 : 0,
                    'to' => $allLeads->count(),
                ],
            ],
        ]);
    }

    /**
     * Provide filter metadata for registration leads list.
     */
    public function filters(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        if (!$this->canAccessRegistrationData($user)) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied.',
            ], 403);
        }

        $leadStatuses = LeadStatus::select('id', 'title')
            ->orderBy('title')
            ->get()
            ->map(function ($status) {
                return [
                    'value' => $status->id,
                    'label' => $status->title,
                ];
            });

        $leadSources = LeadSource::select('id', 'title')
            ->orderBy('title')
            ->get()
            ->map(function ($source) {
                return [
                    'value' => $source->id,
                    'label' => $source->title,
                ];
            });

        $courses = Course::select('id', 'title')
            ->orderBy('title')
            ->get()
            ->map(function ($course) {
                return [
                    'value' => $course->id,
                    'label' => $course->title,
                ];
            });

        // Only load telecallers who belong to non-marketing teams (marketing_team = 0)
        $telecallersQuery = User::where('role_id', 3)
            ->whereHas('team', function ($q) {
                $q->where('marketing_team', false);
            })
            ->select('id', 'name', 'team_id')
            ->orderBy('name');

        if ($user->role_id == 3 && !$user->is_team_lead) {
            $telecallersQuery->where('id', $user->id);
        } elseif ($user->is_team_lead && $user->team_id) {
            $telecallersQuery->where('team_id', $user->team_id);
        }

        $telecallers = $telecallersQuery->get()->map(function ($telecaller) {
            return [
                'value' => $telecaller->id,
                'label' => $telecaller->name,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => [
                'filters' => [
                    'lead_statuses' => $leadStatuses,
                    'lead_sources' => $leadSources,
                    'courses' => $courses,
                    'telecallers' => $telecallers,
                    'registration_statuses' => [
                        ['value' => 'all', 'label' => 'All'],
                        ['value' => 'pending', 'label' => 'Pending'],
                        ['value' => 'approved', 'label' => 'Approved'],
                        ['value' => 'rejected', 'label' => 'Rejected'],
                    ],
                    'default_registration_status' => 'all',
                    'can_filter_by_telecaller' => $user->role_id != 3 || $user->is_team_lead,
                ],
            ],
        ]);
    }

    /**
     * Detailed registration data for a single lead.
     */
    public function show(Request $request, $leadId)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        if (!$this->canAccessRegistrationData($user)) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied.',
            ], 403);
        }

        $lead = Lead::with([
            'leadStatus:id,title',
            'leadSource:id,title',
            'course:id,title,needs_time',
            'telecaller:id,name,team_id',
            'team:id,name',
            'studentDetails' => function ($query) {
                $query->with([
                    'course:id,title,needs_time',
                    'subject:id,title',
                    'batch:id,title',
                    'subCourse:id,title',
                    'classTime:id,course_id,from_time,to_time',
                    'university:id,title',
                    'universityCourse:id,title',
                    'reviewedBy:id,name',
                    'sslcCertificates:id,lead_detail_id,certificate_path,original_filename,file_type,file_size,verification_status,verification_notes,verified_at,verified_by,created_at',
                    'sslcCertificates.verifiedBy:id,name',
                    'sslcVerifiedBy:id,name',
                    'birthCertificateVerifiedBy:id,name',
                    'passportPhotoVerifiedBy:id,name',
                    'adharFrontVerifiedBy:id,name',
                    'adharBackVerifiedBy:id,name',
                    'signatureVerifiedBy:id,name',
                    'otherDocumentVerifiedBy:id,name',
                    'plustwoVerifiedBy:id,name',
                    'ugVerifiedBy:id,name',
                    'postGraduationCertificateVerifiedBy:id,name',
                ]);
            },
        ])->findOrFail($leadId);

        if (!$this->canViewLead($lead, $user)) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied for this lead.',
            ], 403);
        }

        $studentDetail = $lead->studentDetails;

        if (!$studentDetail) {
            return response()->json([
                'status' => false,
                'message' => 'Registration details not found for this lead.',
            ], 404);
        }

        $detailData = [
            'lead' => $this->transformLead($lead),
            'student_detail' => $this->transformStudentDetail($studentDetail),
            'documents' => $this->buildDocumentPayload($studentDetail),
        ];

        return response()->json([
            'status' => true,
            'data' => $detailData,
        ]);
    }

    /**
     * Fetch metadata required for converting a lead (mirrors web convert view).
     */
    public function convert(Request $request, Lead $lead)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        if (!$this->canViewLead($lead, $user)) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied for this lead.',
            ], 403);
        }

        if ($lead->is_converted) {
            return response()->json([
                'status' => false,
                'message' => 'Lead already converted.',
            ], 409);
        }

        $lead->loadMissing([
            'studentDetails.course',
            'studentDetails.subject',
            'studentDetails.batch',
            'studentDetails.subCourse',
            'studentDetails.classTime',
            'studentDetails.university',
            'batch',
            'course',
        ]);

        $boards = Board::where('is_active', true)
            ->orderBy('title')
            ->get(['id', 'title'])
            ->map(function ($board) {
                return [
                    'value' => $board->id,
                    'label' => $board->title,
                ];
            });

        $countryCodes = get_country_code();

        $course = $lead->course;
        $isB2b = (int) ($lead->is_b2b ?? 0) === 1;
        $currentBatch = $lead->batch ?: ($lead->studentDetails?->batch);
        $selectedBatchId = $currentBatch?->id;

        $courseAmount = $course ? (float) ($course->amount ?? 0) : 0.0;
        $extraAmount = 0.0;
        $universityAmount = 0.0;
        $courseType = null;
        $university = $lead->studentDetails?->university;

        if ($isB2b) {
            $courseAmount = 0.0;
        }

        if (!$isB2b && $lead->course_id == 16 && $lead->studentDetails && $lead->studentDetails->class === 'sslc') {
            $extraAmount = 10000.0;
        }

        if (!$isB2b && $lead->course_id == 9 && $lead->studentDetails) {
            $courseType = $lead->studentDetails->course_type;
            $universityId = $lead->studentDetails->university_id;

            if ($universityId) {
                $universityModel = $university ?: University::find($universityId);
                if ($universityModel) {
                    $university = $universityModel;
                    if ($courseType === 'UG') {
                        $universityAmount = (float) ($universityModel->ug_amount ?? 0);
                    } elseif ($courseType === 'PG') {
                        $universityAmount = (float) ($universityModel->pg_amount ?? 0);
                    }
                }
            }
        }

        if ($isB2b) {
            $extraAmount = 0.0;
            $universityAmount = 0.0;
        }

        $additionalAmount = $extraAmount + $universityAmount;
        // Summary totals exclude batch fee (each batch carries its own amount; client adds selection).
        $totalAmount = $courseAmount + $additionalAmount;

        $batchesPayload = collect();
        if ($lead->course_id) {
            $allBatches = Batch::where('course_id', $lead->course_id)
                ->select('id', 'title', 'amount', 'sslc_amount', 'plustwo_amount', 'b2b_amount', 'is_active')
                ->orderBy('is_active', 'desc')
                ->orderBy('title')
                ->get();

            $batchesPayload = $allBatches->map(function (Batch $batch) use ($lead, $selectedBatchId) {
                $display = $this->batchDisplayAmountForLead($lead, $batch);
                $isSelected = $selectedBatchId !== null && (int) $selectedBatchId === (int) $batch->id;

                return [
                    'id' => $batch->id,
                    'title' => $batch->title,
                    'is_active' => (bool) $batch->is_active,
                    'is_selected' => $isSelected ? 1 : 0,
                    'amount' => $display['amount'],
                    'amount_label' => $display['label'],
                    'amount_base' => $batch->amount !== null ? (float) $batch->amount : null,
                    'sslc_amount' => $batch->sslc_amount !== null ? (float) $batch->sslc_amount : null,
                    'plustwo_amount' => $batch->plustwo_amount !== null ? (float) $batch->plustwo_amount : null,
                    'b2b_amount' => $batch->b2b_amount !== null ? (float) $batch->b2b_amount : null,
                ];
            })->values();
        }

        $dob = ($lead->studentDetails && $lead->studentDetails->date_of_birth)
            ? Carbon::parse($lead->studentDetails->date_of_birth)->format('Y-m-d')
            : null;

        return response()->json([
            'status' => true,
            'data' => [
                'lead' => [
                    'id' => $lead->id,
                    'name' => $lead->title,
                    'code' => $lead->code,
                    'phone' => $lead->phone,
                    'email' => $lead->email,
                    'dob' => $dob,
                    'course_id' => $lead->course_id,
                    'batch_id' => $lead->batch_id,
                ],
                'student_detail' => $lead->studentDetails
                    ? $this->transformStudentDetail($lead->studentDetails)
                    : null,
                'form_meta' => [
                    'boards' => $boards,
                    'country_codes' => $countryCodes,
                    'payment_types' => ['Cash', 'Online', 'Bank', 'Cheque', 'Card', 'Other'],
                    'is_b2b' => $isB2b,
                    'course' => $course ? [
                        'id' => $course->id,
                        'title' => $course->title,
                        'amount' => $courseAmount,
                    ] : null,
                    'selected_batch_id' => $selectedBatchId,
                    'batches' => $batchesPayload,
                    'course_type' => $courseType,
                    'university' => $university ? [
                        'id' => $university->id,
                        'title' => $university->title,
                    ] : null,
                    'amounts' => [
                        'course' => $courseAmount,
                        'batch' => 0,
                        'extra' => $extraAmount,
                        'university' => $universityAmount,
                        'additional' => $additionalAmount,
                        'total' => $totalAmount,
                    ],
                ],
            ],
        ]);
    }

    /**
     * Batches for the lead's course with per-batch amounts (B2B / course 16 rules match web convert).
     */
    public function batchesForLead(Request $request, Lead $lead)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        if (!$this->canViewLead($lead, $user)) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied for this lead.',
            ], 403);
        }

        if ($lead->is_converted) {
            return response()->json([
                'status' => false,
                'message' => 'Lead already converted.',
            ], 409);
        }

        $lead->loadMissing(['studentDetails.university', 'studentDetails.batch', 'batch', 'course']);

        if (!$lead->course_id) {
            return response()->json([
                'status' => true,
                'data' => [
                    'lead_id' => $lead->id,
                    'course_id' => null,
                    'is_b2b' => (int) ($lead->is_b2b ?? 0) === 1,
                    'selected_batch_id' => null,
                    'default_batch_id' => $lead->batch_id ?? $lead->studentDetails?->batch_id,
                    'course' => null,
                    'batches' => [],
                    'amounts_context' => [
                        'course' => 0.0,
                        'extra' => 0.0,
                        'university' => 0.0,
                        'additional' => 0.0,
                    ],
                    'message' => 'Lead has no course assigned.',
                ],
            ]);
        }

        $course = $lead->course ?: Course::find($lead->course_id);
        $isB2b = (int) ($lead->is_b2b ?? 0) === 1;

        $batches = Batch::where('course_id', $lead->course_id)
            ->select('id', 'title', 'amount', 'sslc_amount', 'plustwo_amount', 'b2b_amount', 'is_active')
            ->orderBy('is_active', 'desc')
            ->orderBy('title')
            ->get();

        $currentBatch = $lead->batch ?: ($lead->studentDetails?->batch);
        $selectedBatchId = $currentBatch?->id;

        $batchRows = $batches->map(function (Batch $batch) use ($lead, $selectedBatchId) {
            $display = $this->batchDisplayAmountForLead($lead, $batch);
            $isSelected = $selectedBatchId !== null && (int) $selectedBatchId === (int) $batch->id;

            return [
                'id' => $batch->id,
                'title' => $batch->title,
                'is_active' => (bool) $batch->is_active,
                'is_selected' => $isSelected ? 1 : 0,
                'amount' => $display['amount'],
                'amount_label' => $display['label'],
                'amount_base' => $batch->amount !== null ? (float) $batch->amount : null,
                'sslc_amount' => $batch->sslc_amount !== null ? (float) $batch->sslc_amount : null,
                'plustwo_amount' => $batch->plustwo_amount !== null ? (float) $batch->plustwo_amount : null,
                'b2b_amount' => $batch->b2b_amount !== null ? (float) $batch->b2b_amount : null,
            ];
        })->values();

        $courseAmount = $course ? (float) ($course->amount ?? 0) : 0.0;
        if ($isB2b) {
            $courseAmount = 0.0;
        }

        $extraAmount = 0.0;
        if (!$isB2b && (int) $lead->course_id === 16 && $lead->studentDetails && strtolower((string) $lead->studentDetails->class) === 'sslc') {
            $extraAmount = 10000.0;
        }

        $universityAmount = 0.0;
        $courseType = null;
        if (!$isB2b && (int) $lead->course_id === 9 && $lead->studentDetails) {
            $courseType = $lead->studentDetails->course_type;
            $universityId = $lead->studentDetails->university_id;
            if ($universityId) {
                $universityModel = $lead->studentDetails->university ?: University::find($universityId);
                if ($universityModel) {
                    if ($courseType === 'UG') {
                        $universityAmount = (float) ($universityModel->ug_amount ?? 0);
                    } elseif ($courseType === 'PG') {
                        $universityAmount = (float) ($universityModel->pg_amount ?? 0);
                    }
                }
            }
        }

        $additionalAmount = $extraAmount + $universityAmount;

        return response()->json([
            'status' => true,
            'data' => [
                'lead_id' => $lead->id,
                'course_id' => (int) $lead->course_id,
                'is_b2b' => $isB2b,
                'student_class' => $lead->studentDetails?->class,
                'course_type' => $courseType,
                'selected_batch_id' => $selectedBatchId,
                'default_batch_id' => $lead->batch_id ?? $lead->studentDetails?->batch_id,
                'course' => $course ? [
                    'id' => $course->id,
                    'title' => $course->title,
                    'amount' => $courseAmount,
                ] : null,
                'batches' => $batchRows,
                'amounts_context' => [
                    'course' => $courseAmount,
                    'extra' => $isB2b ? 0.0 : $extraAmount,
                    'university' => $isB2b ? 0.0 : $universityAmount,
                    'additional' => $isB2b ? 0.0 : $additionalAmount,
                ],
            ],
        ]);
    }

    /**
     * Convert a lead into a student (mirrors admin LeadController::convertSubmit).
     */
    public function convertSubmit(Request $request, Lead $lead)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        if (!$this->canViewLead($lead, $user)) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied for this lead.',
            ], 403);
        }

        if ($lead->is_converted) {
            return response()->json([
                'status' => false,
                'message' => 'Lead already converted.',
            ], 409);
        }

        $rules = [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'dob' => 'nullable|date|before_or_equal:today',
            'board_id' => 'nullable|exists:boards,id',
            'batch_id' => 'nullable|exists:batches,id',
            'remarks' => 'nullable|string|max:1000',
            'payment_collected' => 'boolean',
            'payment_amount' => 'required_if:payment_collected,1|required_if:payment_collected,true|required_if:payment_collected,"1"|nullable|numeric|min:0.01',
            'payment_type' => 'required_if:payment_collected,1|required_if:payment_collected,true|required_if:payment_collected,"1"|nullable|in:Cash,Online,Bank,Cheque,Card,Other',
            'transaction_id' => 'nullable|string|max:255',
            'payment_proofs' => 'nullable|array',
            'payment_proofs.*.transaction_id' => 'nullable|string|max:255',
            'payment_proofs.*.file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'payment_date' => 'nullable|date',
            'payment_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'custom_total_amount' => 'nullable|numeric|min:0',
        ];

        if ((int) $lead->course_id === 23) {
            $rules['fee_pg_amount'] = 'nullable|numeric|min:0';
            $rules['fee_ug_amount'] = 'nullable|numeric|min:0';
            $rules['fee_plustwo_amount'] = 'nullable|numeric|min:0';
            $rules['fee_sslc_amount'] = 'nullable|numeric|min:0';
            $rules['payment_pg_amount'] = 'nullable|numeric|min:0';
            $rules['payment_ug_amount'] = 'nullable|numeric|min:0';
            $rules['payment_plustwo_amount'] = 'nullable|numeric|min:0';
            $rules['payment_sslc_amount'] = 'nullable|numeric|min:0';
            $rules['payment_pg_file'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048';
            $rules['payment_ug_file'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048';
            $rules['payment_plustwo_file'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048';
            $rules['payment_sslc_file'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048';
            $rules['payment_amount'] = 'nullable|numeric|min:0';
            $rules['payment_file'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048';
        }

        if ($lead->course_id && (int) $lead->course_id !== 23) {
            $rules['batch_id'] = 'required|exists:batches,id';
        }

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($validator) use ($request, $lead) {
            if ($request->filled('batch_id')) {
                $batchBelongsToCourse = Batch::where('id', $request->batch_id)
                    ->where('course_id', $lead->course_id)
                    ->exists();
                if (!$batchBelongsToCourse) {
                    $validator->errors()->add('batch_id', 'Selected batch does not belong to this lead course.');
                }
            }

            if (!$request->boolean('payment_collected')) {
                return;
            }

            $paymentProofs = PaymentProofHelper::normalizeFromRequest($request);
            $transactionIds = PaymentProofHelper::collectTransactionIds($paymentProofs);

            foreach (PaymentProofHelper::findDuplicateWithinSubmission($transactionIds) as $duplicateId) {
                $validator->errors()->add('payment_proofs', "Duplicate transaction ID in submission: {$duplicateId}");
            }

            foreach (PaymentProofHelper::findExistingTransactionIds($transactionIds) as $existingId) {
                $validator->errors()->add('payment_proofs', "Transaction ID already exists: {$existingId}");
            }

            if ((int) $lead->course_id !== 23) {
                return;
            }

            $pgPaid = (float) ($request->input('payment_pg_amount') ?: 0);
            $ugPaid = (float) ($request->input('payment_ug_amount') ?: 0);
            $plustwoPaid = (float) ($request->input('payment_plustwo_amount') ?: 0);
            $sslcPaid = (float) ($request->input('payment_sslc_amount') ?: 0);
            $totalPaid = $pgPaid + $ugPaid + $plustwoPaid + $sslcPaid;

            if ($totalPaid <= 0) {
                $validator->errors()->add('payment_pg_amount', 'At least one payment amount (PG/UG/Plus Two/SSLC) is required.');
            }

            $customTotal = $request->filled('custom_total_amount') ? (float) $request->input('custom_total_amount') : null;
            if ($customTotal !== null && $totalPaid > $customTotal) {
                $validator->errors()->add('custom_total_amount', 'Total paid amount cannot exceed the total amount.');
            }

            if ($pgPaid > 0 && !$request->hasFile('payment_pg_file')) {
                $validator->errors()->add('payment_pg_file', 'PG payment proof file is required when PG paid amount is entered.');
            }
            if ($ugPaid > 0 && !$request->hasFile('payment_ug_file')) {
                $validator->errors()->add('payment_ug_file', 'UG payment proof file is required when UG paid amount is entered.');
            }
            if ($plustwoPaid > 0 && !$request->hasFile('payment_plustwo_file')) {
                $validator->errors()->add('payment_plustwo_file', 'Plus Two payment proof file is required when Plus Two paid amount is entered.');
            }
            if ($sslcPaid > 0 && !$request->hasFile('payment_sslc_file')) {
                $validator->errors()->add('payment_sslc_file', 'SSLC payment proof file is required when SSLC paid amount is entered.');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Please correct the errors below.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $leadDetail = LeadDetail::firstOrCreate(
                ['lead_id' => $lead->id],
                ['course_id' => $lead->course_id]
            );

            if ($request->filled('dob')) {
                $leadDetail->update(['date_of_birth' => $request->dob]);
            }

            $selectedBatchId = $request->filled('batch_id') ? (int) $request->batch_id : null;
            if (!is_null($selectedBatchId)) {
                $leadDetail->update(['batch_id' => $selectedBatchId]);
            }

            $dob = $request->dob ?? ($leadDetail->date_of_birth ?? null);
            $subjectId = $leadDetail->subject_id;

            $convertedLead = ConvertedLead::create([
                'lead_id' => $lead->id,
                'name' => $request->name,
                'code' => $request->code,
                'phone' => $request->phone,
                'email' => $request->email,
                'dob' => $dob,
                'course_id' => $lead->course_id,
                'batch_id' => $selectedBatchId,
                'board_id' => $request->board_id,
                'subject_id' => $subjectId,
                'candidate_status_id' => 1,
                'remarks' => $request->remarks,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            $leadUpdateData = [
                'is_converted' => true,
                'updated_by' => $user->id,
            ];
            if (!is_null($selectedBatchId)) {
                $leadUpdateData['batch_id'] = $selectedBatchId;
            }
            $lead->update($leadUpdateData);

            $invoice = null;
            if ($lead->course_id) {
                $invoiceController = new \App\Http\Controllers\InvoiceController();
                $customTotalAmount = null;
                $feeBreakdown = null;

                if ((int) $lead->course_id === 23) {
                    $feeBreakdown = [
                        'fee_pg_amount' => $request->filled('fee_pg_amount') ? (float) $request->input('fee_pg_amount') : null,
                        'fee_ug_amount' => $request->filled('fee_ug_amount') ? (float) $request->input('fee_ug_amount') : null,
                        'fee_plustwo_amount' => $request->filled('fee_plustwo_amount') ? (float) $request->input('fee_plustwo_amount') : null,
                        'fee_sslc_amount' => $request->filled('fee_sslc_amount') ? (float) $request->input('fee_sslc_amount') : null,
                    ];
                    if ($request->filled('custom_total_amount')) {
                        $customTotalAmount = (float) $request->input('custom_total_amount');
                    } else {
                        $customTotalAmount =
                            (float) (($feeBreakdown['fee_pg_amount'] ?? 0)
                                + ($feeBreakdown['fee_ug_amount'] ?? 0)
                                + ($feeBreakdown['fee_plustwo_amount'] ?? 0)
                                + ($feeBreakdown['fee_sslc_amount'] ?? 0));
                    }
                } elseif ($request->filled('custom_total_amount')) {
                    $customTotalAmount = (float) $request->input('custom_total_amount');
                }

                $invoice = $invoiceController->autoGenerate(
                    $convertedLead->id,
                    (int) $lead->course_id,
                    $customTotalAmount,
                    $feeBreakdown,
                    $user->id
                );
            }

            if ($request->boolean('payment_collected') && $invoice) {
                $paymentController = new \App\Http\Controllers\PaymentController();
                $paymentProofs = PaymentProofHelper::normalizeFromRequest($request);

                if ((int) $lead->course_id === 23) {
                    $paymentDate = $request->payment_date;
                    $paymentType = $request->payment_type;

                    $splitPayments = [
                        'PG' => ['amount' => (float) ($request->input('payment_pg_amount') ?: 0), 'file' => $request->file('payment_pg_file')],
                        'UG' => ['amount' => (float) ($request->input('payment_ug_amount') ?: 0), 'file' => $request->file('payment_ug_file')],
                        'PLUS_TWO' => ['amount' => (float) ($request->input('payment_plustwo_amount') ?: 0), 'file' => $request->file('payment_plustwo_file')],
                        'SSLC' => ['amount' => (float) ($request->input('payment_sslc_amount') ?: 0), 'file' => $request->file('payment_sslc_file')],
                    ];

                    $attachSharedProofs = true;
                    foreach ($splitPayments as $feeHead => $payload) {
                        if (($payload['amount'] ?? 0) <= 0) {
                            continue;
                        }

                        $proofsForPayment = [];
                        if ($attachSharedProofs) {
                            $proofsForPayment = $paymentProofs;
                            $attachSharedProofs = false;
                        }

                        if (!empty($payload['file'])) {
                            $proofsForPayment[] = [
                                'transaction_id' => null,
                                'file' => $payload['file'],
                            ];
                        }

                        $paymentController->autoCreate(
                            $invoice->id,
                            $payload['amount'],
                            $paymentType,
                            $request->transaction_id,
                            $payload['file'],
                            $paymentDate,
                            $feeHead,
                            $user->id,
                            $proofsForPayment
                        );
                    }
                } else {
                    $paymentController->autoCreate(
                        $invoice->id,
                        (float) $request->payment_amount,
                        $request->payment_type,
                        $request->transaction_id,
                        $request->file('payment_file'),
                        $request->payment_date,
                        null,
                        $user->id,
                        $paymentProofs
                    );
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Lead converted successfully!',
                'data' => [
                    'converted_lead_id' => $convertedLead->id,
                    'invoice_id' => $invoice?->id,
                    'batch_id' => $selectedBatchId,
                ],
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[RegistrationLeadsController@convertSubmit] ' . $e->getMessage(), [
                'lead_id' => $lead->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while converting the lead. Please try again.',
            ], 500);
        }
    }

    /**
     * Inline update for registration detail fields (mirrors web inline edit).
     */
    public function inlineUpdate(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        if (!$this->canAccessRegistrationData($user)) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'lead_detail_id' => 'required|exists:leads_details,id',
            'field' => 'required|string',
            'value' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Please correct the errors below.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $studentDetail = LeadDetail::with('lead.telecaller')->findOrFail($request->lead_detail_id);

            if (!$studentDetail->lead || !$this->canViewLead($studentDetail->lead, $user)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Access denied for this lead.',
                ], 403);
            }

            $field = $request->field;
            $value = $request->value;
            $allowedFields = [
                'student_name', 'father_name', 'mother_name', 'date_of_birth', 'gender', 'is_employed',
                'email', 'phone', 'whatsapp', 'parents_phone', 'father_contact_number', 'father_contact_code',
                'mother_contact_number', 'mother_contact_code', 'street', 'locality', 'post_office', 'district', 'state', 'pin_code',
                'message', 'subject_id', 'batch_id', 'sub_course_id', 'passed_year', 'programme_type', 'location', 'class_time_id', 'class'
            ];

            if (!in_array($field, $allowedFields)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid field for editing.',
                ], 400);
            }

            if (in_array($field, ['phone', 'whatsapp', 'parents_phone', 'father_contact', 'mother_contact'])) {
                if (strpos($value, '|') === false) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid phone number format.',
                    ], 400);
                }

                [$code, $number] = explode('|', $value, 2);

                if ($field === 'phone') {
                    $studentDetail->update([
                        'personal_code' => $code,
                        'personal_number' => $number,
                    ]);
                } elseif ($field === 'whatsapp') {
                    $studentDetail->update([
                        'whatsapp_code' => $code,
                        'whatsapp_number' => $number,
                    ]);
                } elseif ($field === 'parents_phone') {
                    $studentDetail->update([
                        'parents_code' => $code,
                        'parents_number' => $number,
                    ]);
                } elseif ($field === 'father_contact') {
                    $studentDetail->update([
                        'father_contact_code' => $code,
                        'father_contact_number' => $number,
                    ]);
                } elseif ($field === 'mother_contact') {
                    $studentDetail->update([
                        'mother_contact_code' => $code,
                        'mother_contact_number' => $number,
                    ]);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Contact updated successfully.',
                ]);
            }

            if ($field === 'class_time_id') {
                $value = $value ? (int) $value : null;

                if ($value) {
                    $course = Course::find($studentDetail->course_id);
                    if (!$course || !$course->needs_time) {
                        return response()->json([
                            'status' => false,
                            'message' => 'This course does not require class time.',
                        ], 400);
                    }

                    $classTime = ClassTime::where('id', $value)
                        ->where('course_id', $studentDetail->course_id)
                        ->where('is_active', true)
                        ->first();

                    if (!$classTime) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Invalid class time selected.',
                        ], 400);
                    }
                }

                $studentDetail->update([$field => $value]);
                $studentDetail->load('classTime');
                $newValue = $studentDetail->classTime
                    ? $this->formatClassTimeLabel($studentDetail->classTime)
                    : 'N/A';

                return response()->json([
                    'status' => true,
                    'message' => 'Class time updated successfully.',
                    'data' => [
                        'new_value' => $newValue,
                    ],
                ]);
            }

            if (in_array($field, ['subject_id', 'batch_id', 'sub_course_id'])) {
                $value = $value ? (int) $value : null;

                if ($field === 'subject_id' && $value) {
                    if (!in_array($studentDetail->course_id, [1, 2])) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Subject selection is not applicable for this course.',
                        ], 400);
                    }

                    $subject = Subject::where('id', $value)
                        ->where('course_id', $studentDetail->course_id)
                        ->first();

                    if (!$subject) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Invalid subject selected.',
                        ], 400);
                    }
                } elseif ($field === 'batch_id' && $value) {
                    $batch = Batch::where('id', $value)
                        ->where('course_id', $studentDetail->course_id)
                        ->first();

                    if (!$batch) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Invalid batch selected.',
                        ], 400);
                    }
                } elseif ($field === 'sub_course_id' && $value) {
                    $subCourse = SubCourse::where('id', $value)
                        ->where('course_id', $studentDetail->course_id)
                        ->first();

                    if (!$subCourse) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Invalid sub course selected.',
                        ], 400);
                    }
                }

                $studentDetail->update([$field => $value]);

                if ($field === 'batch_id') {
                    Lead::where('id', $studentDetail->lead_id)->update(['batch_id' => $value]);
                }

                $studentDetail->loadMissing('subject', 'batch', 'subCourse');
                $newValue = null;

                if ($field === 'subject_id') {
                    $newValue = $studentDetail->subject->title ?? 'N/A';
                } elseif ($field === 'batch_id') {
                    $newValue = $studentDetail->batch->title ?? 'N/A';
                } elseif ($field === 'sub_course_id') {
                    $newValue = $studentDetail->subCourse->title ?? 'N/A';
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Registration details updated successfully.',
                    'data' => [
                        'new_value' => $newValue,
                        'updated_id' => $value,
                    ],
                ]);
            }

            if ($field === 'passed_year') {
                $value = $value ? (int) $value : null;
                $studentDetail->update([$field => $value]);

                return response()->json([
                    'status' => true,
                    'message' => 'Registration details updated successfully.',
                    'data' => [
                        'new_value' => $value ?? 'N/A',
                    ],
                ]);
            }

            if ($field === 'is_employed') {
                $value = $value === '1' || $value === 1 || $value === 'true' || $value === true ? 1 : 0;
                $studentDetail->update([$field => $value]);

                return response()->json([
                    'status' => true,
                    'message' => 'Registration details updated successfully.',
                    'data' => [
                        'new_value' => $value ? 'Yes' : 'No',
                    ],
                ]);
            }

            if ($field === 'gender') {
                if (!in_array($value, ['male', 'female'])) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid gender value.',
                    ], 400);
                }

                $studentDetail->update([$field => $value]);

                return response()->json([
                    'status' => true,
                    'message' => 'Registration details updated successfully.',
                    'data' => [
                        'new_value' => ucfirst($value),
                    ],
                ]);
            }

            if ($field === 'programme_type') {
                if (!in_array($value, ['online', 'offline'])) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid programme type value.',
                    ], 400);
                }

                if ($value === 'online') {
                    $studentDetail->update([
                        $field => $value,
                        'location' => null,
                    ]);
                } else {
                    $studentDetail->update([$field => $value]);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Registration details updated successfully.',
                    'data' => [
                        'new_value' => ucfirst($value),
                        'hide_location' => $value === 'online',
                        'show_location' => $value === 'offline',
                    ],
                ]);
            }

            if ($field === 'location') {
                if (!in_array($value, ['Ernakulam', 'Malappuram'])) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid location value.',
                    ], 400);
                }

                $studentDetail->update([$field => $value]);

                return response()->json([
                    'status' => true,
                    'message' => 'Registration details updated successfully.',
                    'data' => [
                        'new_value' => $value,
                    ],
                ]);
            }

            if ($field === 'class') {
                // Validate class value (for GMVSS course)
                $value = strtolower($value);
                if (!in_array($value, ['sslc', 'plustwo'])) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid class value. Must be SSLC or Plus Two.',
                    ], 400);
                }

                // Only allow editing class for GMVSS course (course_id = 16)
                if ($studentDetail->course_id != 16) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Class field is only applicable for GMVSS course.',
                    ], 400);
                }

                $studentDetail->update([$field => $value]);

                // Format display value
                $displayValue = ($value === 'sslc') ? 'SSLC' : 'Plus Two';

                return response()->json([
                    'status' => true,
                    'message' => 'Registration details updated successfully.',
                    'data' => [
                        'new_value' => $displayValue,
                    ],
                ]);
            }

            $studentDetail->update([$field => $value]);

            return response()->json([
                'status' => true,
                'message' => 'Registration details updated successfully.',
                'data' => [
                    'new_value' => $value,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('API inline update failed: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Error updating registration details.',
            ], 500);
        }
    }

    /**
     * Update document verification details for registration leads.
     */
    public function verifyDocument(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        if (!$this->canAccessRegistrationData($user)) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied.',
            ], 403);
        }

        if ($request->has('need_to_change_document')) {
            $request->merge([
                'need_to_change_document' => $request->boolean('need_to_change_document'),
            ]);
        }

        $validator = Validator::make($request->all(), [
            'lead_detail_id' => 'required|exists:leads_details,id',
            'document_type' => 'required|in:sslc_certificate,plustwo_certificate,plus_two_certificate,ug_certificate,post_graduation_certificate,birth_certificate,passport_photo,adhar_front,adhar_back,signature,other_document',
            'verification_status' => 'required|in:pending,verified',
            'need_to_change_document' => 'sometimes|boolean',
            'new_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'sslc_certificate_id' => 'nullable|integer|exists:sslc_certificates,id', // for SSLC multi-doc verification
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Please correct the errors below.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $leadDetail = LeadDetail::with('lead.telecaller')->findOrFail($request->lead_detail_id);

            if (!$leadDetail->lead || !$this->canViewLead($leadDetail->lead, $user)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Access denied for this lead.',
                ], 403);
            }

            $documentType = $request->document_type;
            $verificationStatus = $request->verification_status;
            
            // Check need_to_change_document - handle both string and boolean values (exact same as web)
            $needToChangeDocument = false;
            if ($request->has('need_to_change_document')) {
                $value = $request->input('need_to_change_document');
                $needToChangeDocument = ($value == '1' 
                    || $value === 'true' 
                    || $value === true
                    || $value === 1
                    || $request->boolean('need_to_change_document'));
            }

            // If need to change document is checked, file upload is required (exact same as web)
            if ($needToChangeDocument && !$request->hasFile('new_file')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please upload a new document file.',
                ], 400);
            }

            /**
             * SPECIAL CASE: SSLC with sslc_certificates table (multi-doc)
             * When sslc_certificate_id is provided, mirror LeadController::verifySSLCertificate exactly
             * and update the SSLCCertificates row instead of only leads_details.
             */
            if ($documentType === 'sslc_certificate' && $request->filled('sslc_certificate_id')) {
                /** @var \App\Models\SSLCertificate $sslcCertificate */
                $sslcCertificate = SSLCertificate::findOrFail($request->sslc_certificate_id);

                // Handle new file upload if needed (exact same as web)
                $isDocumentChange = false;
                if ($needToChangeDocument && $request->hasFile('new_file')) {
                    $isDocumentChange = true;
                    
                    // Delete old file
                    if (Storage::disk('public')->exists($sslcCertificate->certificate_path)) {
                        Storage::disk('public')->delete($sslcCertificate->certificate_path);
                    }
                    
                    // Upload new file
                    $file = $request->file('new_file');
                    $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('student-documents', $fileName, 'public');
                    
                    // Update certificate with new file
                    $sslcCertificate->update([
                        'certificate_path' => $filePath,
                        'original_filename' => $file->getClientOriginalName(),
                        'file_type' => $file->getClientOriginalExtension(),
                        'file_size' => $file->getSize(),
                    ]);
                }

                // Update verification status (exact same as web)
                $updateData = [
                    'verification_status' => $verificationStatus,
                    'verified_by' => $user->id,
                    'verified_at' => now(),
                ];

                if ($request->filled('verification_notes')) {
                    $updateData['verification_notes'] = $request->verification_notes;
                }

                $sslcCertificate->update($updateData);
                
                // Get lead detail for activity logging (after update, same as web)
                $leadDetail = LeadDetail::findOrFail($request->lead_detail_id);
                
                // If "Need to change document" is checked, update registration status to pending (exact same as web)
                if ($needToChangeDocument) {
                    $leadDetail->status = 'pending';
                    $leadDetail->reviewed_by = null;
                    $leadDetail->reviewed_at = null;
                    $leadDetail->save();
                    $leadDetail->refresh();
                }

                // Log activity for SSLC certificate operations (exact same as web)
                if ($isDocumentChange) {
                    LeadActivity::create([
                        'lead_id' => $leadDetail->lead_id,
                        'activity_type' => 'document_change',
                        'description' => 'SSLC certificate changed',
                        'reason' => 'SSLC certificate file replaced on: ' . now()->format('d-m-Y h:i A') . '. Registration status reset to pending',
                        'created_by' => $user->id,
                    ]);
                } else {
                    LeadActivity::create([
                        'lead_id' => $leadDetail->lead_id,
                        'activity_type' => 'document_verification',
                        'description' => 'SSLC certificate verification updated',
                        'reason' => 'SSLC certificate verification status: ' . ucfirst($verificationStatus) . 
                                   ($request->filled('verification_notes') ? ' | Notes: ' . $request->verification_notes : ''),
                        'created_by' => $user->id,
                    ]);
                }

                // Build latest SSLC document URL (from certificate)
                $documentUrl = $this->buildFileUrl($sslcCertificate->certificate_path);

                return response()->json([
                    'status' => true,
                    'message' => 'SSLC certificate verification updated successfully.',
                    'data' => [
                        'document_type' => $documentType,
                        'verification_status' => $verificationStatus,
                        'need_to_change_document' => $needToChangeDocument,
                        'lead_status' => $leadDetail->status,
                        'document_url' => $documentUrl,
                        'status_updated' => $needToChangeDocument,
                        'new_status' => $needToChangeDocument ? 'pending' : null,
                    ],
                ]);
            }

            $fieldMapping = [
                'plustwo_certificate' => 'plustwo',
                'plus_two_certificate' => 'plus_two',
                'birth_certificate' => 'birth_certificate',
                'sslc_certificate' => 'sslc',
                'ug_certificate' => 'ug',
                'post_graduation_certificate' => 'post_graduation_certificate',
                'passport_photo' => 'passport_photo',
                'adhar_front' => 'adhar_front',
                'adhar_back' => 'adhar_back',
                'signature' => 'signature',
                'other_document' => 'other_document',
            ];

            $baseField = $fieldMapping[$documentType] ?? $documentType;
            $verificationField = $baseField . '_verification_status';
            $verifiedByField = $baseField . '_verified_by';
            $verifiedAtField = $baseField . '_verified_at';

            // Check current status before update
            $currentStatus = $leadDetail->status;

            $updateData = [
                $verificationField => $verificationStatus,
                $verifiedByField => $user->id,
                $verifiedAtField => now(),
            ];

            // If status is rejected, automatically change to pending when document is updated
            // Also reset if need_to_change_document is checked
            if ($currentStatus === 'rejected' || $needToChangeDocument) {
                $updateData['status'] = 'pending';
                $updateData['reviewed_by'] = null;
                $updateData['reviewed_at'] = null;
            }

            $fileFieldMapping = [
                'plustwo_certificate' => 'plustwo_certificate',
                'plus_two_certificate' => 'plus_two_certificate',
                'birth_certificate' => 'birth_certificate',
                'sslc_certificate' => 'sslc_certificate',
                'ug_certificate' => 'ug_certificate',
                'post_graduation_certificate' => 'post_graduation_certificate',
                'passport_photo' => 'passport_photo',
                'adhar_front' => 'adhar_front',
                'adhar_back' => 'adhar_back',
                'signature' => 'signature',
                'other_document' => 'other_document',
            ];

            $fileField = $fileFieldMapping[$documentType] ?? $documentType;
            $documentUrl = $this->buildFileUrl($leadDetail->$fileField);
            $isDocumentUpload = false;
            $isDocumentChange = false;

            if ($request->hasFile('new_file')) {
                $file = $request->file('new_file');
                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('student-documents', $fileName, 'public');

                $oldDocumentPath = $leadDetail->$fileField;
                $isDocumentChange = !empty($oldDocumentPath) && $needToChangeDocument;
                $isDocumentUpload = empty($oldDocumentPath) || !$needToChangeDocument;

                $updateData[$fileField] = $filePath;
                $documentUrl = $this->buildFileUrl($filePath);
            }

            $leadDetail->update($updateData);

            // If status was rejected, automatically change to pending when document is updated
            // Also reset if need_to_change_document is checked
            if ($currentStatus === 'rejected' || $needToChangeDocument) {
                $leadDetail->status = 'pending';
                $leadDetail->reviewed_by = null;
                $leadDetail->reviewed_at = null;
                $leadDetail->save();
            }

            $documentName = ucfirst(str_replace('_', ' ', $documentType));

            if ($isDocumentUpload && !$isDocumentChange) {
                LeadActivity::create([
                    'lead_id' => $leadDetail->lead_id,
                    'activity_type' => 'document_upload',
                    'description' => $documentName . ' uploaded',
                    'reason' => 'Document: ' . $documentName . ' | Status: ' . ucfirst($verificationStatus),
                    'created_by' => $user->id,
                ]);
            } elseif ($isDocumentChange) {
                LeadActivity::create([
                    'lead_id' => $leadDetail->lead_id,
                    'activity_type' => 'document_change',
                    'description' => $documentName . ' changed',
                    'reason' => 'Document: ' . $documentName . ' | Old document replaced with new file | Registration status reset to pending',
                    'created_by' => $user->id,
                ]);
            } else {
                LeadActivity::create([
                    'lead_id' => $leadDetail->lead_id,
                    'activity_type' => 'document_verification',
                    'description' => $documentName . ' verification updated',
                    'reason' => 'Document: ' . $documentName . ' | Status: ' . ucfirst($verificationStatus),
                    'created_by' => $user->id,
                ]);
            }

            $leadDetail->refresh();

            return response()->json([
                'status' => true,
                'message' => 'Document verification updated successfully.',
                'data' => [
                    'document_type' => $documentType,
                    'verification_status' => $verificationStatus,
                    'need_to_change_document' => $needToChangeDocument,
                    'lead_status' => $leadDetail->status,
                    'document_url' => $documentUrl,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('API document verification failed: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Error updating document verification.',
            ], 500);
        }
    }

    /**
     * Add one or more SSLC certificates (mirrors the web add flow).
     */
    public function addSSLCCertificates(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            if (!$this->canAccessRegistrationData($user)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Access denied.',
                ], 403);
            }

            $request->validate([
                'lead_detail_id' => 'required|exists:leads_details,id',
                'certificates' => 'required|array|min:1',
                'certificates.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ]);

            $leadDetail = LeadDetail::with('lead')->findOrFail($request->lead_detail_id);
            $lead = $leadDetail->lead;

            if (!$lead || !$this->canViewLead($lead, $user)) {
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have permission to upload documents for this lead.',
                ], 403);
            }

            $certificateIds = [];
            $uploadedFiles = [];

            foreach ($request->file('certificates') as $file) {
                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('student-documents', $fileName, 'public');

                $sslcCertificate = SSLCertificate::create([
                    'lead_detail_id' => $leadDetail->id,
                    'certificate_path' => $filePath,
                    'original_filename' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientOriginalExtension(),
                    'file_size' => $file->getSize(),
                    'verification_status' => 'pending',
                ]);

                $certificateIds[] = $sslcCertificate->id;
                $uploadedFiles[] = $sslcCertificate->original_filename;
            }

            $fileCount = count($uploadedFiles);

            LeadActivity::create([
                'lead_id' => $leadDetail->lead_id,
                'activity_type' => 'document_upload',
                'description' => $fileCount . ' SSLC certificate(s) uploaded',
                'reason' => 'SSLC certificate(s) uploaded: ' . implode(', ', $uploadedFiles),
                'created_by' => $user->id,
            ]);

            $certificatesPayload = SSLCertificate::whereIn('id', $certificateIds)
                ->get()
                ->map(function (SSLCertificate $certificate) {
                    return [
                        'id' => $certificate->id,
                        'url' => $this->buildFileUrl($certificate->certificate_path),
                        'original_filename' => $certificate->original_filename,
                        'status' => $certificate->verification_status ?? 'pending',
                        'uploaded_at' => optional($certificate->created_at)->format('d-m-Y h:i A'),
                    ];
                });

            return response()->json([
                'status' => true,
                'message' => 'SSLC certificate(s) added successfully.',
                'data' => [
                    'certificate_ids' => $certificateIds,
                    'certificates' => $certificatesPayload,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('API add SSLC certificate error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Error adding SSLC certificate: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload/replace non-SSLC registration documents (plustwo, UG, etc.).
     */
    public function addDocument(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        if (!$this->canAccessRegistrationData($user)) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'lead_detail_id' => 'required|exists:leads_details,id',
            'document_type' => 'required|in:plustwo_certificate,plus_two_certificate,ug_certificate,post_graduation_certificate,birth_certificate,passport_photo,adhar_front,adhar_back,signature,other_document',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Please correct the errors below.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $leadDetail = LeadDetail::with('lead.telecaller')->findOrFail($request->lead_detail_id);

            if (!$leadDetail->lead || !$this->canViewLead($leadDetail->lead, $user)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Access denied for this lead.',
                ], 403);
            }

            $documentType = $request->document_type;

            $baseFieldMapping = [
                'plustwo_certificate' => 'plustwo',
                'plus_two_certificate' => 'plus_two',
                'ug_certificate' => 'ug',
                'post_graduation_certificate' => 'post_graduation_certificate',
                'birth_certificate' => 'birth_certificate',
                'passport_photo' => 'passport_photo',
                'adhar_front' => 'adhar_front',
                'adhar_back' => 'adhar_back',
                'signature' => 'signature',
                'other_document' => 'other_document',
            ];

            $fileFieldMapping = [
                'plustwo_certificate' => 'plustwo_certificate',
                'plus_two_certificate' => 'plus_two_certificate',
                'ug_certificate' => 'ug_certificate',
                'post_graduation_certificate' => 'post_graduation_certificate',
                'birth_certificate' => 'birth_certificate',
                'passport_photo' => 'passport_photo',
                'adhar_front' => 'adhar_front',
                'adhar_back' => 'adhar_back',
                'signature' => 'signature',
                'other_document' => 'other_document',
            ];

            $baseField = $baseFieldMapping[$documentType] ?? null;
            $fileField = $fileFieldMapping[$documentType] ?? null;

            if (!$baseField || !$fileField) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unsupported document type.',
                ], 422);
            }

            $verificationField = $baseField . '_verification_status';
            $verifiedByField = $baseField . '_verified_by';
            $verifiedAtField = $baseField . '_verified_at';

            $file = $request->file('file');
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('student-documents', $fileName, 'public');

            $oldPath = $leadDetail->$fileField;
            if (!empty($oldPath) && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            $leadDetail->update([
                $fileField => $filePath,
                $verificationField => 'pending',
                $verifiedByField => null,
                $verifiedAtField => null,
                'status' => 'pending',
                'reviewed_by' => null,
                'reviewed_at' => null,
            ]);

            $documentName = ucfirst(str_replace('_', ' ', $documentType));
            LeadActivity::create([
                'lead_id' => $leadDetail->lead_id,
                'activity_type' => $oldPath ? 'document_change' : 'document_upload',
                'description' => $oldPath ? ($documentName . ' changed') : ($documentName . ' uploaded'),
                'reason' => 'Document: ' . $documentName . ' added via API. Status set to pending review.',
                'created_by' => $user->id,
            ]);

            $documentUrl = $this->buildFileUrl($filePath);

            return response()->json([
                'status' => true,
                'message' => 'Document uploaded successfully.',
                'data' => [
                    'document_type' => $documentType,
                    'document_url' => $documentUrl,
                    'verification_status' => 'pending',
                    'lead_status' => $leadDetail->status,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('API add document error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Error uploading document: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Build the base query with eager loaded relationships.
     */
    private function buildBaseQuery()
    {
        return Lead::select([
                'id',
                'title',
                'code',
                'phone',
                'email',
                'lead_status_id',
                'lead_source_id',
                'course_id',
                'telecaller_id',
                'team_id',
                'place',
                'rating',
                'interest_status',
                'followup_date',
                'remarks',
                'is_converted',
                'created_at',
                'updated_at',
            ])
            ->with([
                'leadStatus:id,title',
                'leadSource:id,title',
                'course:id,title',
                'telecaller:id,name,team_id',
                'studentDetails' => function ($query) {
                    $query->select([
                        'id',
                        'lead_id',
                        'status',
                        'course_id',
                        'subject_id',
                        'batch_id',
                        'class_time_id',
                        'sslc_certificate',
                        'plustwo_certificate',
                        'ug_certificate',
                        'post_graduation_certificate',
                        'birth_certificate',
                        'passport_photo',
                        'adhar_front',
                        'adhar_back',
                        'signature',
                        'other_document',
                        'sslc_verification_status',
                        'sslc_verified_by',
                        'sslc_verified_at',
                        'plustwo_verification_status',
                        'plustwo_verified_by',
                        'plustwo_verified_at',
                        'ug_verification_status',
                        'ug_verified_by',
                        'ug_verified_at',
                        'post_graduation_certificate_verification_status',
                        'post_graduation_certificate_verified_by',
                        'post_graduation_certificate_verified_at',
                        'birth_certificate_verification_status',
                        'birth_certificate_verified_by',
                        'birth_certificate_verified_at',
                        'passport_photo_verification_status',
                        'passport_photo_verified_by',
                        'passport_photo_verified_at',
                        'adhar_front_verification_status',
                        'adhar_front_verified_by',
                        'adhar_front_verified_at',
                        'adhar_back_verification_status',
                        'adhar_back_verified_by',
                        'adhar_back_verified_at',
                        'signature_verification_status',
                        'signature_verified_by',
                        'signature_verified_at',
                        'other_document_verification_status',
                        'other_document_verified_by',
                        'other_document_verified_at',
                        'admin_remarks',
                        'reviewed_by',
                        'reviewed_at',
                    ])->with([
                        'course:id,title',
                        'subject:id,title',
                        'batch:id,title',
                        'subCourse:id,title',
                        'classTime:id,course_id,from_time,to_time',
                        'reviewedBy:id,name',
                        'sslcCertificates:id,lead_detail_id,certificate_path,original_filename,file_type,file_size,verification_status,verification_notes,verified_at,verified_by,created_at',
                        'sslcCertificates.verifiedBy:id,name',
                        'sslcVerifiedBy:id,name',
                    ]);
                },
                'leadActivities' => function ($query) {
                    $query->select('id', 'lead_id', 'reason', 'created_at', 'activity_type')
                        ->whereNotNull('reason')
                        ->where('reason', '!=', '')
                        ->orderByDesc('created_at');
                },
            ])
            ->whereHas('studentDetails')
            ->notConverted()
            ->notDropped();
    }

    /**
     * Apply filters from request.
     */
    private function applyFilters($query, Request $request, $options = [])
    {
        $skipRegistrationStatus = $options['skip_registration_status'] ?? false;

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->byDateRange($request->date_from, $request->date_to);
        }

        if ($request->filled('lead_status_id')) {
            $query->where('lead_status_id', $request->lead_status_id);
        }

        if ($request->filled('lead_source_id')) {
            $query->where('lead_source_id', $request->lead_source_id);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('telecaller_id')) {
            $query->where('telecaller_id', $request->telecaller_id);
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if (!$skipRegistrationStatus) {
            $registrationStatus = $request->get('registration_status', 'all');

            if (in_array($registrationStatus, ['pending', 'approved', 'rejected'])) {
                $query->whereHas('studentDetails', function ($q) use ($registrationStatus) {
                    $q->where('status', $registrationStatus);
                });
            }
            // If 'all' or not provided, don't filter by status - show all leads
        }

        if ($request->filled('search_key')) {
            $searchKey = $request->search_key;
            $query->where(function ($q) use ($searchKey) {
                $q->where('title', 'like', "%{$searchKey}%")
                    ->orWhere('phone', 'like', "%{$searchKey}%")
                    ->orWhere('email', 'like', "%{$searchKey}%");
            });
        }
    }

    /**
     * Restrict query data based on user role.
     */
    private function applyRoleRestrictions($query, $user, Request $request)
    {
        if ($user->is_team_lead) {
            if ($user->team_id) {
                $teamMemberIds = User::where('team_id', $user->team_id)
                    ->where('role_id', 3)
                    ->whereNull('deleted_at')
                    ->pluck('id')
                    ->toArray();
                $teamMemberIds[] = $user->id;
                $query->whereIn('telecaller_id', $teamMemberIds);
            } else {
                $query->where('telecaller_id', $user->id);
            }
        } elseif ($user->role_id == 3) {
            $query->where('telecaller_id', $user->id);
        } elseif ($request->filled('telecaller_id')) {
            $query->where('telecaller_id', $request->telecaller_id);
        }
    }

    /**
     * Determine if the user can view registration data.
     */
    private function canAccessRegistrationData($user): bool
    {
        $allowedRoleIds = [1, 2, 3, 4, 5]; // super admin, admin, telecaller, admission counsellor, academic assistant

        return in_array($user->role_id, $allowedRoleIds)
            || $user->is_team_lead
            || $user->is_senior_manager;
    }

    /**
     * Restrict lead detail view based on role/ownership.
     */
    private function canViewLead(Lead $lead, $user): bool
    {
        if ($this->canAccessRegistrationData($user)) {
            if ($user->role_id == 3 && !$user->is_team_lead) {
                return (int) $lead->telecaller_id === (int) $user->id;
            }

            if ($user->is_team_lead && $user->team_id) {
                return (int) $lead->telecaller?->team_id === (int) $user->team_id
                    || (int) $lead->telecaller_id === (int) $user->id;
            }

            return true;
        }

        return false;
    }

    /**
     * Batch fee for this lead context (matches LeadController::convert).
     *
     * @return array{amount: float, label: string}
     */
    private function batchDisplayAmountForLead(Lead $lead, Batch $batch): array
    {
        if ((int) ($lead->is_b2b ?? 0) === 1) {
            $amount = $batch->b2b_amount !== null ? (float) $batch->b2b_amount : 0.0;
            $label = $batch->b2b_amount !== null ? 'B2B Amount' : 'B2B Amount (not set)';

            return ['amount' => $amount, 'label' => $label];
        }

        if ((int) $lead->course_id === 16) {
            $studentClass = $lead->studentDetails?->class;
            $normalizedClass = $studentClass ? strtolower((string) $studentClass) : null;
            if ($normalizedClass === 'sslc' && !is_null($batch->sslc_amount)) {
                return ['amount' => (float) $batch->sslc_amount, 'label' => 'SSLC Amount'];
            }
            if (!is_null($batch->plustwo_amount)) {
                return ['amount' => (float) $batch->plustwo_amount, 'label' => 'Plus Two Amount'];
            }

            return ['amount' => (float) ($batch->amount ?? 0), 'label' => 'Amount'];
        }

        return ['amount' => (float) ($batch->amount ?? 0), 'label' => 'Amount'];
    }

    /**
     * Transform lead for API response.
     */
    private function transformLead(Lead $lead): array
    {
        $studentDetail = $lead->studentDetails;
        $latestActivity = $lead->leadActivities->first();
        $documentsStatus = $studentDetail ? $studentDetail->getDocumentVerificationStatus() : null;

        return [
            'id' => $lead->id,
            'name' => $lead->title,
            'phone' => $this->formatPhone($lead->code, $lead->phone),
            'email' => $lead->email,
            'course' => $lead->course ? $lead->course->title : null,
            'lead_status' => $lead->leadStatus ? $lead->leadStatus->title : null,
            'lead_source' => $lead->leadSource ? $lead->leadSource->title : null,
            'telecaller' => $lead->telecaller ? $lead->telecaller->name : null,
            'telecaller_id' => $lead->telecaller_id,
            'rating' => $lead->rating,
            'interest_status' => $lead->interest_status,
            'registration_status' => $studentDetail ? $studentDetail->status : null,
            'admin_remarks' => $studentDetail?->admin_remarks,
            'documents_status' => $documentsStatus,
            'documents_status_label' => $documentsStatus
                ? $this->formatDocumentStatusLabel($documentsStatus)
                : null,
            'last_activity' => $latestActivity ? [
                'reason' => $latestActivity->reason,
                'created_at' => $latestActivity->created_at?->format('Y-m-d H:i:s'),
            ] : null,
            'documents_summary' => $this->buildDocumentSummary($studentDetail),
            'submitted_at' => $studentDetail ? optional($studentDetail->created_at)->format('Y-m-d H:i:s') : null,
            'created_at' => $lead->created_at ? $lead->created_at->format('Y-m-d H:i:s') : null,
        ];
    }

    /**
     * Prepare student detail payload for detail endpoint.
     */
    private function transformStudentDetail(LeadDetail $detail): array
    {
        $documentsStatus = $detail->getDocumentVerificationStatus();

        return [
            'id' => $detail->id,
            'status' => $detail->status,
            'course' => $detail->course ? $detail->course->title : null,
            'subject' => $detail->subject ? $detail->subject->title : null,
            'batch' => $detail->batch ? $detail->batch->title : null,
            'sub_course' => $detail->subCourse ? $detail->subCourse->title : null,
            'class_time' => $detail->classTime ? [
                'title' => $this->formatClassTimeLabel($detail->classTime),
                'start_time' => $detail->classTime->from_time,
                'end_time' => $detail->classTime->to_time,
            ] : null,
            'second_language' => $detail->second_language,
            'passed_year' => $detail->passed_year,
            'programme_type' => $detail->programme_type,
            'student_name' => $detail->student_name,
            'father_name' => $detail->father_name,
            'mother_name' => $detail->mother_name,
            'date_of_birth' => $detail->date_of_birth
                ? Carbon::parse($detail->date_of_birth)->format('Y-m-d')
                : null,
            'gender' => $detail->gender,
            'contact' => [
                'personal' => $this->formatPhone($detail->personal_code, $detail->personal_number),
                'parents' => $this->formatPhone($detail->parents_code, $detail->parents_number),
                'father' => $this->formatPhone($detail->father_contact_code, $detail->father_contact_number),
                'mother' => $this->formatPhone($detail->mother_contact_code, $detail->mother_contact_number),
                'whatsapp' => $this->formatPhone($detail->whatsapp_code, $detail->whatsapp_number),
            ],
            'address' => [
                'street' => $detail->street,
                'locality' => $detail->locality,
                'post_office' => $detail->post_office,
                'district' => $detail->district,
                'state' => $detail->state,
                'pin_code' => $detail->pin_code,
            ],
            'admin_remarks' => $detail->admin_remarks,
            'reviewed_by' => $detail->reviewedBy ? $detail->reviewedBy->name : null,
            'reviewed_at' => $detail->reviewed_at ? $detail->reviewed_at->format('Y-m-d H:i:s') : null,
            'documents_status' => $documentsStatus,
            'documents_status_label' => $documentsStatus
                ? $this->formatDocumentStatusLabel($documentsStatus)
                : null,
        ];
    }

    /**
     * Summaries of uploaded documents & verification status.
     */
    private function buildDocumentSummary(?LeadDetail $detail): array
    {
        if (!$detail) {
            return [];
        }

        $documentTypes = $this->documentTypeConfigs();
        $summary = [];

        // Check if SSLC certificates exist in sslc_certificates table
        $hasSslcCertificates = $detail->sslcCertificates && $detail->sslcCertificates->count() > 0;

        foreach ($documentTypes as $field => $config) {
            // Skip legacy sslc_certificate entry if certificates exist in sslc_certificates table
            if ($field === 'sslc_certificate' && $hasSslcCertificates) {
                continue;
            }

            $statusField = $config['status_field'] ?? ($field . '_verification_status');
            $verifiedAtField = $config['verified_at_field'] ?? ($field . '_verified_at');
            $label = $config['label'];
            $filePath = $this->resolveDocumentPath($detail, $field, $config);
            [$uploaded, $status, $verifiedAt] = $this->resolveDocumentStatusMeta(
                $detail,
                $field,
                $statusField,
                $verifiedAtField,
                $filePath
            );

            $summary[$field] = [
                'label' => $label,
                'uploaded' => $uploaded,
                'status' => $status,
                'verified_at' => $verifiedAt,
                'url' => $this->buildFileUrl($filePath),
            ];
        }

        $formattedSslcCertificates = $this->formatSslcCertificates($detail);

        if (!empty($formattedSslcCertificates)) {
            $summary['sslc_certificates'] = $formattedSslcCertificates;
            $summary['sslc_multiple'] = $formattedSslcCertificates;
        }

        return $summary;
    }

    /**
     * Build document payload for detail endpoint.
     */
    private function buildDocumentPayload(LeadDetail $detail): array
    {
        $documentTypes = $this->documentTypeConfigs();
        $documents = [];

        // Check if SSLC certificates exist in sslc_certificates table
        $hasSslcCertificates = $detail->sslcCertificates && $detail->sslcCertificates->count() > 0;

        foreach ($documentTypes as $field => $config) {
            // Skip legacy sslc_certificate entry if certificates exist in sslc_certificates table
            if ($field === 'sslc_certificate' && $hasSslcCertificates) {
                continue;
            }

            $statusField = $config['status_field'] ?? ($field . '_verification_status');
            $verifiedByField = $config['verified_by_field'] ?? ($field . '_verified_by');
            $verifiedAtField = $config['verified_at_field'] ?? ($field . '_verified_at');
            $filePath = $this->resolveDocumentPath($detail, $field, $config);
            [$uploaded, $status, $verifiedAt] = $this->resolveDocumentStatusMeta(
                $detail,
                $field,
                $statusField,
                $verifiedAtField,
                $filePath
            );

            $verifiedByName = $this->resolveVerifiedBy(
                $detail,
                $verifiedByField,
                $config['verified_relation'] ?? null
            );

            if ($field === 'sslc_certificate') {
                $verifiedByName = $this->resolveSslcVerifiedByName($detail) ?? $verifiedByName;
            }

            $documents[$field] = [
                'label' => $config['label'],
                'url' => $this->buildFileUrl($filePath),
                'status' => $status,
                'verified_by' => $verifiedByName,
                'verified_at' => $verifiedAt,
            ];
        }

        // Add SSLC certificates from sslc_certificates table
        $formattedSslcCertificates = $this->formatSslcCertificates($detail);

        if (!empty($formattedSslcCertificates)) {
            $documents['sslc_certificates'] = $formattedSslcCertificates;
            $documents['sslc_multiple'] = $formattedSslcCertificates;
        }

        return $documents;
    }

    /**
     * Build a usable file URL.
     */
    private function buildFileUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        $publicDisk = Storage::disk('public');

        if ($publicDisk->exists($path)) {
            /** @var FilesystemAdapter $publicDisk */
            return $publicDisk->url($path);
        }

        return asset('storage/' . ltrim($path, '/'));
    }

    /**
     * Format phone number with code.
     */
    private function formatPhone(?string $code, ?string $number): ?string
    {
        if (!$number) {
            return null;
        }

        if ($code) {
            return '+' . ltrim($code, '+') . ' ' . $number;
        }

        return $number;
    }

    /**
     * Build a readable class time label.
     */
    private function formatClassTimeLabel(?ClassTime $classTime): ?string
    {
        if (!$classTime) {
            return null;
        }

        $from = $this->formatTimeValue($classTime->from_time);
        $to = $this->formatTimeValue($classTime->to_time);

        if ($from && $to) {
            return "{$from} - {$to}";
        }

        return $from ?? $to;
    }

    /**
     * Format a single time value to h:i A, fallback to raw string.
     */
    private function formatTimeValue(?string $time): ?string
    {
        if (!$time) {
            return null;
        }

        try {
            return Carbon::createFromFormat('H:i:s', $time)->format('h:i A');
        } catch (\Exception $e) {
            try {
                return Carbon::parse($time)->format('h:i A');
            } catch (\Exception $e) {
                return $time;
            }
        }
    }

    /**
     * Format a datetime value to Y-m-d H:i:s.
     */
    private function formatDateTimeValue($value): ?string
    {
        if (!$value) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        try {
            return Carbon::parse($value)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Build human friendly label for document status badge.
     */
    private function formatDocumentStatusLabel(string $status): string
    {
        return $status === 'verified' ? 'Documents Verified' : 'Documents Pending';
    }

    /**
     * Resolve upload/status info for document summary.
     */
    private function resolveDocumentStatusMeta(
        LeadDetail $detail,
        string $field,
        string $statusField,
        string $verifiedAtField,
        ?string $filePath
    ): array {
        // For SSLC, prioritize sslc_certificates table over legacy leads_details fields
        if ($field === 'sslc_certificate' && $detail->sslcCertificates && $detail->sslcCertificates->count() > 0) {
            $uploaded = true;
            $hasPending = $detail->sslcCertificates->contains(function ($certificate) {
                return ($certificate->verification_status ?? 'pending') !== 'verified';
            });

            $status = $hasPending ? 'pending' : 'verified';

            $firstVerified = $detail->sslcCertificates->firstWhere('verification_status', 'verified');

            $verifiedAt = null;
            if ($firstVerified && $firstVerified->verified_at) {
                $verifiedAt = $this->formatDateTimeValue($firstVerified->verified_at);
            }

            return [$uploaded, $status, $verifiedAt];
        }

        // For other documents or legacy SSLC (no certificates in sslc_certificates table)
        $uploaded = !empty($filePath);
        $status = $detail->$statusField ?? null;
        $verifiedAt = $this->formatDateTimeValue($detail->$verifiedAtField);

        if ($uploaded) {
            $status = $status ?? 'pending';
        }

        return [$uploaded, $status, $verifiedAt];
    }

    private function resolveDocumentPath(LeadDetail $detail, string $field, array $config): ?string
    {
        // For SSLC, prioritize sslc_certificates table over legacy leads_details field
        if ($field === 'sslc_certificate' && $detail->sslcCertificates && $detail->sslcCertificates->count() > 0) {
            $firstCertificate = $detail->sslcCertificates->first();
            if ($firstCertificate) {
                return $firstCertificate->certificate_path ?? $firstCertificate->file_path ?? null;
            }
        }

        // For other documents or legacy SSLC (no certificates in sslc_certificates table)
        $pathResolver = $config['path_resolver'] ?? null;
        return $pathResolver ? $pathResolver($detail) : ($detail->$field ?? null);
    }

    private function resolveVerifiedBy(LeadDetail $detail, string $verifiedByField, ?string $relationName = null)
    {
        // For SSLC, prioritize sslc_certificates table over legacy leads_details fields
        if ($verifiedByField === 'sslc_verified_by' && $detail->sslcCertificates && $detail->sslcCertificates->count() > 0) {
            $verifiedCertificate = $detail->sslcCertificates->firstWhere('verification_status', 'verified');
            if ($verifiedCertificate && $verifiedCertificate->verifiedBy) {
                return $verifiedCertificate->verifiedBy->name;
            }
        }

        // For other documents or legacy SSLC (no certificates in sslc_certificates table)
        if ($relationName && $detail->relationLoaded($relationName) && $detail->$relationName) {
            return $detail->$relationName->name;
        }

        return $detail->$verifiedByField;
    }

    /**
     * Document configuration (label & path resolver).
     */
    private function documentTypeConfigs(): array
    {
        return [
            'sslc_certificate' => [
                'label' => 'SSLC Certificate',
                'path_resolver' => fn ($detail) => $detail->sslc_certificate,
                'verified_relation' => 'sslcVerifiedBy',
                'status_field' => 'sslc_verification_status',
                'verified_by_field' => 'sslc_verified_by',
                'verified_at_field' => 'sslc_verified_at',
            ],
            'plustwo_certificate' => [
                'label' => 'Plus Two Certificate',
                'path_resolver' => fn ($detail) => $detail->plustwo_certificate,
                'verified_relation' => 'plustwoVerifiedBy',
            ],
            'ug_certificate' => [
                'label' => 'UG Certificate',
                'path_resolver' => fn ($detail) => $detail->ug_certificate,
                'verified_relation' => 'ugVerifiedBy',
            ],
            'post_graduation_certificate' => [
                'label' => 'Post Graduation Certificate',
                'path_resolver' => fn ($detail) => $detail->post_graduation_certificate,
                'verified_relation' => 'postGraduationCertificateVerifiedBy',
            ],
            'birth_certificate' => [
                'label' => 'Birth Certificate',
                'path_resolver' => fn ($detail) => $detail->birth_certificate,
                'verified_relation' => 'birthCertificateVerifiedBy',
            ],
            'passport_photo' => [
                'label' => 'Passport Photo',
                'path_resolver' => fn ($detail) => $detail->passport_photo,
                'verified_relation' => 'passportPhotoVerifiedBy',
            ],
            'adhar_front' => [
                'label' => 'Aadhar Front',
                'path_resolver' => fn ($detail) => $detail->adhar_front,
                'verified_relation' => 'adharFrontVerifiedBy',
            ],
            'adhar_back' => [
                'label' => 'Aadhar Back',
                'path_resolver' => fn ($detail) => $detail->adhar_back,
                'verified_relation' => 'adharBackVerifiedBy',
            ],
            'signature' => [
                'label' => 'Signature',
                'path_resolver' => fn ($detail) => $detail->signature,
                'verified_relation' => 'signatureVerifiedBy',
            ],
            'other_document' => [
                'label' => 'Other Document',
                'path_resolver' => fn ($detail) => $detail->other_document,
                'verified_relation' => 'otherDocumentVerifiedBy',
            ],
        ];
    }

    /**
     * Prepare SSLC certificate payloads (mirrors web view listing).
     */
    private function formatSslcCertificates(LeadDetail $detail): array
    {
        if (!$detail->sslcCertificates || $detail->sslcCertificates->isEmpty()) {
            return [];
        }

        return $detail->sslcCertificates
            ->map(function ($certificate, $index) {
                $path = $certificate->certificate_path ?? $certificate->file_path ?? null;

                return [
                    'id' => $certificate->id,
                    'label' => 'SSLC Certificate ' . ($index + 1),
                    'original_filename' => $certificate->original_filename ?? ($path ? basename($path) : null),
                    'file_type' => $certificate->file_type ?? null,
                    'file_size' => $certificate->file_size ?? null,
                    'url' => $this->buildFileUrl($path),
                    'status' => $certificate->verification_status ?? 'pending',
                    'status_label' => ($certificate->verification_status ?? 'pending') === 'verified' ? 'Verified' : 'Pending',
                    'verified_by' => $certificate->verifiedBy ? $certificate->verifiedBy->name : null,
                    'verified_at' => $this->formatDateTimeValue($certificate->verified_at),
                    'verification_notes' => $certificate->verification_notes ?? null,
                    'uploaded_at' => $this->formatDateTimeValue($certificate->created_at ?? null),
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Resolve SSLC verified by name (prioritise certificate verifier data).
     */
    private function resolveSslcVerifiedByName(LeadDetail $detail): ?string
    {
        if ($detail->sslcCertificates && $detail->sslcCertificates->count() > 0) {
            $verifiedCertificate = $detail->sslcCertificates->firstWhere('verification_status', 'verified');

            if ($verifiedCertificate && $verifiedCertificate->verifiedBy) {
                return $verifiedCertificate->verifiedBy->name;
            }
        }

        if ($detail->relationLoaded('sslcVerifiedBy') && $detail->sslcVerifiedBy) {
            return $detail->sslcVerifiedBy->name;
        }

        return null;
    }

    /**
     * Calculate counts for registration statuses.
     */
    private function calculateRegistrationCounts($user, Request $request): array
    {
        $baseQuery = $this->buildBaseQuery();
        $this->applyFilters($baseQuery, $request, ['skip_registration_status' => true]);
        $this->applyRoleRestrictions($baseQuery, $user, $request);

        $allCount = (clone $baseQuery)->count();
        $pendingCount = (clone $baseQuery)->whereHas('studentDetails', function ($q) {
            $q->where('status', 'pending');
        })->count();
        $approvedCount = (clone $baseQuery)->whereHas('studentDetails', function ($q) {
            $q->where('status', 'approved');
        })->count();
        $rejectedCount = (clone $baseQuery)->whereHas('studentDetails', function ($q) {
            $q->where('status', 'rejected');
        })->count();

        return [
            'all' => $allCount,
            'pending' => $pendingCount,
            'approved' => $approvedCount,
            'rejected' => $rejectedCount,
        ];
    }
}


