<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Course;
use App\Models\CourseType;
use App\Models\Lead;
use App\Models\LeadDetail;
use App\Support\CourseOfflinePlaceSupport;
use App\Models\StreamSpecialization;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LeadAiSalesMarketingRegistrationController extends Controller
{
    private const COURSE_ID = 29;

    private const COURSE_TITLE = 'AI-Integrated Sales & Marketing';

    public function showForm($leadId = null)
    {
        $lead = null;

        if ($leadId) {
            $lead = Lead::find($leadId);

            if ($lead && $lead->aiSalesMarketingStudentDetails) {
                return view('public.ai-sales-marketing-registration-success');
            }
        }

        $batches = Batch::where('course_id', self::COURSE_ID)->where('is_active', true)->get();
        $course = Course::find(self::COURSE_ID);
        $courseTypes = CourseType::where('course_id', self::COURSE_ID)->where('is_active', true)->orderBy('title')->get();
        $streamSpecializations = StreamSpecialization::where('course_id', self::COURSE_ID)->where('is_active', true)->orderBy('title')->get();
        $offlinePlaces = CourseOfflinePlaceSupport::placesFor($course);
        $countryCodes = \App\Helpers\CountriesHelper::get_country_code();

        return view('public.ai-sales-marketing-registration', compact(
            'batches',
            'lead',
            'countryCodes',
            'course',
            'courseTypes',
            'streamSpecializations',
            'offlinePlaces'
        ));
    }

    public function store(Request $request)
    {
        $course = Course::find(self::COURSE_ID);

        $validator = Validator::make($request->all(), array_merge([
            'lead_id' => 'required|exists:leads,id',
            'student_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'is_employed' => 'required|boolean',
            'email' => 'required|email|max:255',
            'personal_number' => 'required|string|max:20',
            'personal_code' => 'required|string|max:10',
            'parents_number' => 'required|string|max:20',
            'parents_code' => 'required|string|max:10',
            'whatsapp_number' => 'required|string|max:20',
            'whatsapp_code' => 'required|string|max:10',
            'course_type_id' => [
                'required',
                Rule::exists('course_types', 'id')->where(fn ($query) => $query->where('course_id', self::COURSE_ID)->where('is_active', true)),
            ],
            'stream_specialization_id' => [
                'required',
                Rule::exists('stream_specializations', 'id')->where(fn ($query) => $query->where('course_id', self::COURSE_ID)->where('is_active', true)),
            ],
            'programme_type' => 'required|in:online,offline',
            'batch_id' => [
                'required',
                Rule::exists('batches', 'id')->where(fn ($query) => $query->where('course_id', self::COURSE_ID)->where('is_active', true)),
            ],
            'class_time_id' => 'nullable|exists:class_times,id',
            'residential_address' => 'required|string|max:500',
            'street' => 'required|string|max:500',
            'locality' => 'required|string|max:255',
            'post_office' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'pin_code' => 'required|string|regex:/^[0-9]{6}$/',
            'passport_photo' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'adhar_front' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'adhar_back' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'sslc_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'plustwo_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'ug_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'other_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'signature' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'message' => 'nullable|string',
            'terms_accepted' => 'required|accepted',
        ], CourseOfflinePlaceSupport::locationValidationRules($course)), [
            'lead_id.required' => 'Lead ID is required.',
            'lead_id.exists' => 'Invalid lead.',
            'student_name.required' => 'Candidate name is required.',
            'father_name.required' => "Father's name is required.",
            'mother_name.required' => "Mother's name is required.",
            'date_of_birth.required' => 'Date of birth is required.',
            'gender.required' => 'Gender is required.',
            'is_employed.required' => 'Employment status is required.',
            'email.required' => 'Email address is required.',
            'personal_number.required' => 'Primary contact number is required.',
            'parents_number.required' => "Guardian's contact number is required.",
            'whatsapp_number.required' => 'WhatsApp number is required.',
            'course_type_id.required' => 'Course type is required.',
            'course_type_id.exists' => 'Please select a valid course type.',
            'stream_specialization_id.required' => 'Stream / specialization is required.',
            'stream_specialization_id.exists' => 'Please select a valid stream / specialization.',
            'programme_type.required' => 'Mode of study is required.',
            'location.required_if' => 'Branch / learning center is required for offline mode.',
            'batch_id.required' => 'Batch is required.',
            'batch_id.exists' => 'Please select a valid batch.',
            'class_time_id.exists' => 'Please select a valid class timing.',
            'residential_address.required' => 'House / building name is required.',
            'street.required' => 'Street address is required.',
            'locality.required' => 'Locality / area is required.',
            'post_office.required' => 'Post office is required.',
            'district.required' => 'District is required.',
            'state.required' => 'State is required.',
            'pin_code.required' => 'PIN code is required.',
            'pin_code.regex' => 'PIN code must be exactly 6 digits.',
            'ug_certificate.required' => 'Degree certificate is required.',
            'terms_accepted.accepted' => 'You must accept the Terms and Conditions.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed. Please correct the highlighted fields.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $filePaths = [];

        try {
            $fileFields = [
                'passport_photo',
                'adhar_front',
                'adhar_back',
                'sslc_certificate',
                'plustwo_certificate',
                'ug_certificate',
                'other_document',
                'signature',
            ];

            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('student-documents', $fileName, 'public');
                    $filePaths[$field] = $filePath;
                }
            }

            $lead = Lead::findOrFail($request->lead_id);
            $lead->update([
                'title' => $request->student_name,
                'email' => $request->email,
                'phone' => $request->personal_number,
                'code' => $request->personal_code,
                'gender' => $request->gender,
            ]);

            $studentDetail = LeadDetail::create([
                'lead_id' => $request->lead_id,
                'course_id' => self::COURSE_ID,
                'student_name' => $request->student_name,
                'father_name' => $request->father_name,
                'mother_name' => $request->mother_name,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'is_employed' => $request->is_employed,
                'email' => $request->email,
                'personal_number' => $request->personal_number,
                'personal_code' => $request->personal_code,
                'parents_number' => $request->parents_number,
                'parents_code' => $request->parents_code,
                'whatsapp_number' => $request->whatsapp_number,
                'whatsapp_code' => $request->whatsapp_code,
                'course_type_id' => $request->course_type_id,
                'stream_specialization_id' => $request->stream_specialization_id,
                'programme_type' => $request->programme_type,
                'edumaster_course_name' => self::COURSE_TITLE,
                'location' => $request->programme_type === 'offline' ? $request->location : null,
                'batch_id' => $request->batch_id,
                'class_time_id' => $request->class_time_id,
                'residential_address' => $request->residential_address,
                'street' => $request->street,
                'locality' => $request->locality,
                'post_office' => $request->post_office,
                'district' => $request->district,
                'state' => $request->state,
                'pin_code' => $request->pin_code,
                'passport_photo' => $filePaths['passport_photo'] ?? null,
                'adhar_front' => $filePaths['adhar_front'] ?? null,
                'adhar_back' => $filePaths['adhar_back'] ?? null,
                'sslc_certificate' => $filePaths['sslc_certificate'] ?? null,
                'plustwo_certificate' => $filePaths['plustwo_certificate'] ?? null,
                'ug_certificate' => $filePaths['ug_certificate'] ?? null,
                'other_document' => $filePaths['other_document'] ?? null,
                'signature' => $filePaths['signature'] ?? null,
                'message' => $request->message,
                'status' => 'pending',
            ]);

            try {
                MailService::sendStudentRegistrationEmail($studentDetail, self::COURSE_TITLE);
            } catch (\Exception $e) {
                \Log::error('Email sending failed for ' . self::COURSE_TITLE . ' registration: ' . $e->getMessage());
            }

            try {
                \App\Models\LeadActivity::create([
                    'lead_id' => $request->lead_id,
                    'activity_type' => 'registration_submitted',
                    'description' => 'Registration form submitted',
                    'remarks' => 'Registration form submitted on ' . now()->format('d-m-Y') . ' at ' . now()->format('h:i A'),
                    'created_by' => $lead->telecaller_id,
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to create lead activity for ' . self::COURSE_TITLE . ' registration: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Registration submitted successfully! We will review your application and get back to you soon.',
                'data' => $studentDetail,
                'redirect' => route('public.lead.ai-sales-marketing.register', $request->lead_id),
            ]);
        } catch (\Exception $e) {
            foreach ($filePaths as $filePath) {
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting your registration. Please try again. Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function showSuccess($leadId)
    {
        $lead = Lead::find($leadId);

        if (!$lead || !$lead->aiSalesMarketingStudentDetails) {
            return redirect()->route('public.lead.ai-sales-marketing.register', $leadId);
        }

        return view('public.ai-sales-marketing-registration-success');
    }
}
