<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\LeadDetail;
use App\Models\Batch;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\MailService;

class LeadJuniorVloggerRegistrationController extends Controller
{
    const COURSE_ID = 25;

    public function showForm($leadId = null)
    {
        $lead = null;
        if ($leadId) {
            $lead = Lead::find($leadId);

            if ($lead && $lead->juniorVloggerStudentDetails) {
                return view('public.junior-vlogger-registration-success');
            }
        }

        $batches = Batch::where('course_id', self::COURSE_ID)->where('is_active', true)->get();
        $countryCodes = \App\Helpers\CountriesHelper::get_country_code();

        return view('public.junior-vlogger-registration', compact('batches', 'lead', 'countryCodes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'student_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date',
            'email' => 'required|email|max:255',
            'personal_number' => 'required|string|max:20',
            'personal_code' => 'required|string|max:10',
            'whatsapp_number' => 'required|string|max:20',
            'whatsapp_code' => 'required|string|max:10',
            'parents_number' => 'nullable|string|max:20',
            'parents_code' => 'nullable|string|max:10',
            'medium_of_study' => 'required|in:english,malayalam',
            'previous_qualification' => 'required|in:plus_two,sslc,other',
            'technology_performance_category' => 'required|in:excellent,average,needs_support',
            'passport_photo' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'adhar_front' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'sslc_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'message' => 'nullable|string',
        ], [
            'lead_id.required' => 'Lead ID is required.',
            'lead_id.exists' => 'Invalid lead.',
            'student_name.required' => 'Full name is required.',
            'gender.required' => 'Gender is required.',
            'gender.in' => 'Please select a valid gender.',
            'date_of_birth.required' => 'Date of birth is required.',
            'date_of_birth.date' => 'Please enter a valid date of birth.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'personal_number.required' => 'Primary mobile number is required.',
            'personal_code.required' => 'Country code is required.',
            'whatsapp_number.required' => 'WhatsApp number is required.',
            'whatsapp_code.required' => 'WhatsApp country code is required.',
            'medium_of_study.required' => 'Medium of study is required.',
            'medium_of_study.in' => 'Please select a valid medium.',
            'previous_qualification.required' => 'Previous qualification is required.',
            'previous_qualification.in' => 'Please select a valid qualification.',
            'technology_performance_category.required' => 'Technology performance category is required.',
            'technology_performance_category.in' => 'Please select a valid category.',
            'passport_photo.mimes' => 'Passport photo must be JPG or PNG. Max 2MB.',
            'adhar_front.mimes' => 'Aadhaar must be PDF or image. Max 2MB.',
            'sslc_certificate.mimes' => 'SSLC certificate must be PDF or image. Max 2MB.',
        ]);

        $filePaths = [];

        try {
            $fileFields = ['passport_photo', 'adhar_front', 'sslc_certificate'];
            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                    $filePaths[$field] = $file->storeAs('student-documents', $fileName, 'public');
                }
            }

            $lead = Lead::findOrFail($request->lead_id);
            $age = $request->date_of_birth ? now()->parse($request->date_of_birth)->age : null;

            $lead->update([
                'title' => $request->student_name,
                'gender' => $request->gender,
                'age' => $age,
                'email' => $request->email,
                'phone' => $request->personal_number,
                'code' => $request->personal_code,
                'whatsapp' => $request->whatsapp_number,
                'whatsapp_code' => $request->whatsapp_code,
            ]);

            $studentDetail = LeadDetail::create([
                'lead_id' => $request->lead_id,
                'course_id' => self::COURSE_ID,
                'student_name' => $request->student_name,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'email' => $request->email,
                'personal_number' => $request->personal_number,
                'personal_code' => $request->personal_code,
                'whatsapp_number' => $request->whatsapp_number,
                'whatsapp_code' => $request->whatsapp_code,
                'parents_number' => $request->parents_number,
                'parents_code' => $request->parents_code,
                'medium_of_study' => $request->medium_of_study,
                'previous_qualification' => $request->previous_qualification,
                'technology_performance_category' => $request->technology_performance_category,
                'passport_photo' => $filePaths['passport_photo'] ?? null,
                'adhar_front' => $filePaths['adhar_front'] ?? null,
                'sslc_certificate' => $filePaths['sslc_certificate'] ?? null,
                'message' => $request->message,
                'status' => 'pending',
            ]);

            try {
                MailService::sendStudentRegistrationEmail($studentDetail, 'CreateX AI');
            } catch (\Exception $e) {
                \Log::error('Email sending failed for CreateX AI registration: ' . $e->getMessage());
            }

            try {
                \App\Models\LeadActivity::create([
                    'lead_id' => $request->lead_id,
                    'activity_type' => 'registration_submitted',
                    'description' => 'CreateX AI registration form submitted',
                    'remarks' => 'Registration form submitted on ' . now()->format('d-m-Y') . ' at ' . now()->format('h:i A'),
                    'created_by' => $lead->telecaller_id,
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to create lead activity for CreateX AI registration: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Registration submitted successfully! We will review your application and get back to you soon.',
                'data' => $studentDetail,
                'redirect' => route('public.lead.junior-vlogger.register.success', $request->lead_id),
            ]);
        } catch (\Exception $e) {
            foreach ($filePaths as $path) {
                if ($path && Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
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
        return view('public.junior-vlogger-registration-success');
    }
}
