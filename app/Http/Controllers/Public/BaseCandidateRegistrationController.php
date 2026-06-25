<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadDetail;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

abstract class BaseCandidateRegistrationController extends Controller
{
    abstract protected function courseId(): int;

    abstract protected function courseTitle(): string;

    abstract protected function registerRouteName(): string;

    abstract protected function storeRouteName(): string;

    abstract protected function successRouteName(): string;

    public function showForm($leadId = null)
    {
        $lead = null;

        if ($leadId) {
            $lead = Lead::find($leadId);

            if ($lead) {
                $studentDetail = LeadDetail::where('lead_id', $leadId)
                    ->where('course_id', $this->courseId())
                    ->first();

                if ($studentDetail) {
                    return view('public.candidate-registration-success', [
                        'courseTitle' => $this->courseTitle(),
                    ]);
                }
            }
        }

        $countryCodes = \App\Helpers\CountriesHelper::get_country_code();

        return view('public.candidate-registration', [
            'lead' => $lead,
            'countryCodes' => $countryCodes,
            'registrationTitle' => $this->courseTitle() . ' – Candidate Registration',
            'courseTitle' => $this->courseTitle(),
            'storeRouteName' => $this->storeRouteName(),
        ]);
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
            'parents_number' => 'required|string|max:20',
            'parents_code' => 'required|string|max:10',
            'medium_of_study' => 'required|in:english,malayalam',
            'previous_qualification' => 'required|in:sslc,plus_two,degree',
            'technology_performance_category' => 'required|in:excellent,average,needs_support',
            'passport_photo' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'adhar_front' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'sslc_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'other_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'message' => 'nullable|string',
            'terms_accepted' => 'required|accepted',
        ], [
            'lead_id.required' => 'Lead ID is required.',
            'lead_id.exists' => 'Invalid lead.',
            'student_name.required' => 'Candidate full name is required.',
            'gender.required' => 'Gender is required.',
            'date_of_birth.required' => 'Date of birth is required.',
            'email.required' => 'Email address is required.',
            'personal_number.required' => 'Candidate mobile number is required.',
            'personal_code.required' => 'Country code is required.',
            'whatsapp_number.required' => 'WhatsApp number is required.',
            'whatsapp_code.required' => 'WhatsApp country code is required.',
            'parents_number.required' => 'Parent / guardian contact number is required.',
            'parents_code.required' => 'Parent / guardian country code is required.',
            'medium_of_study.required' => 'Medium of study is required.',
            'previous_qualification.required' => 'Highest qualification is required.',
            'technology_performance_category.required' => 'Technology proficiency level is required.',
            'passport_photo.required' => 'Passport-size photograph is required.',
            'adhar_front.required' => 'Aadhaar card is required.',
            'sslc_certificate.required' => 'SSLC certificate is required.',
            'terms_accepted.accepted' => 'You must accept the Declaration & Consent.',
        ]);

        $filePaths = [];

        try {
            $fileFields = ['passport_photo', 'adhar_front', 'sslc_certificate', 'other_document'];
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
                'course_id' => $this->courseId(),
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
                'other_document' => $filePaths['other_document'] ?? null,
                'message' => $request->message,
                'status' => 'pending',
            ]);

            try {
                MailService::sendStudentRegistrationEmail($studentDetail, $this->courseTitle());
            } catch (\Exception $e) {
                \Log::error($this->courseTitle() . ' registration email failed: ' . $e->getMessage());
            }

            try {
                \App\Models\LeadActivity::create([
                    'lead_id' => $request->lead_id,
                    'activity_type' => 'registration_submitted',
                    'description' => $this->courseTitle() . ' registration form submitted',
                    'remarks' => 'Registration form submitted on ' . now()->format('d-m-Y') . ' at ' . now()->format('h:i A'),
                    'created_by' => $lead->telecaller_id,
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to create lead activity for ' . $this->courseTitle() . ' registration: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Registration submitted successfully! We will review your application and get back to you soon.',
                'data' => $studentDetail,
                'redirect' => route($this->successRouteName(), $request->lead_id),
            ]);
        } catch (\Exception $e) {
            foreach ($filePaths as $path) {
                if ($path && Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting your registration. Please try again.',
            ], 500);
        }
    }

    public function showSuccess($leadId)
    {
        return view('public.candidate-registration-success', [
            'courseTitle' => $this->courseTitle(),
        ]);
    }
}
