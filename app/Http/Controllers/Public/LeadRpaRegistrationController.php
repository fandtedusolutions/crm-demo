<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadDetail;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LeadRpaRegistrationController extends Controller
{
    private const COURSE_ID = 27;

    public function showRpaForm($leadId = null)
    {
        $lead = null;
        if ($leadId) {
            $lead = Lead::find($leadId);

            if ($lead) {
                $studentDetail = LeadDetail::where('lead_id', $leadId)
                    ->where('course_id', self::COURSE_ID)
                    ->first();

                if ($studentDetail) {
                    return view('public.rpa-registration-success');
                }
            }
        }

        $course = \App\Models\Course::find(self::COURSE_ID);
        $countryCodes = \App\Helpers\CountriesHelper::get_country_code();

        return view('public.rpa-registration', compact('lead', 'countryCodes', 'course'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'terms_accepted' => 'required|accepted',
            'student_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'email' => 'required|email|max:255',
            'personal_number' => 'required|string|max:20',
            'personal_code' => 'required|string|max:10',
            'father_contact_number' => 'required|string|max:20',
            'father_contact_code' => 'required|string|max:10',
            'mother_contact_number' => 'required|string|max:20',
            'mother_contact_code' => 'required|string|max:10',
            'whatsapp_number' => 'required|string|max:20',
            'whatsapp_code' => 'required|string|max:10',
            'street' => 'required|string',
            'locality' => 'required|string|max:255',
            'post_office' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'pin_code' => 'required|string|regex:/^[0-9]{6}$/',
            'passport_photo' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'adhar_front' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'adhar_back' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'signature' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'sslc_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'other_relevant_documents' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'terms_accepted.accepted' => 'You must accept the Terms and Conditions.',
            'pin_code.regex' => 'Pin code must be exactly 6 digits.',
            'sslc_certificate.required' => 'Secondary (10th) certificate is required.',
        ]);

        $filePaths = [];

        try {
            $fileFields = ['passport_photo', 'adhar_front', 'adhar_back', 'signature', 'sslc_certificate', 'other_relevant_documents'];

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
                'email' => $request->email,
                'personal_number' => $request->personal_number,
                'personal_code' => $request->personal_code,
                'father_contact_number' => $request->father_contact_number,
                'father_contact_code' => $request->father_contact_code,
                'mother_contact_number' => $request->mother_contact_number,
                'mother_contact_code' => $request->mother_contact_code,
                'whatsapp_number' => $request->whatsapp_number,
                'whatsapp_code' => $request->whatsapp_code,
                'street' => $request->street,
                'locality' => $request->locality,
                'post_office' => $request->post_office,
                'district' => $request->district,
                'state' => $request->state,
                'pin_code' => $request->pin_code,
                'passport_photo' => $filePaths['passport_photo'] ?? null,
                'adhar_front' => $filePaths['adhar_front'] ?? null,
                'adhar_back' => $filePaths['adhar_back'] ?? null,
                'signature' => $filePaths['signature'] ?? null,
                'sslc_certificate' => $filePaths['sslc_certificate'] ?? null,
                'other_document' => $filePaths['other_relevant_documents'] ?? null,
                'status' => 'pending',
            ]);

            try {
                MailService::sendStudentRegistrationEmail($studentDetail, \App\Helpers\CourseTitleHelper::title(self::COURSE_ID, 'RPA'));
            } catch (\Exception $e) {
                \Log::error('Email sending failed for RPA registration: ' . $e->getMessage());
            }

            try {
                \App\Models\LeadActivity::create([
                    'lead_id' => $request->lead_id,
                    'activity_type' => 'registration_submitted',
                    'description' => 'Registration form submitted',
                    'remarks' => 'RPA registration form submitted on ' . now()->format('d-m-Y') . ' at ' . now()->format('h:i A'),
                    'created_by' => $lead->telecaller_id,
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to create lead activity for RPA registration: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Registration submitted successfully! We will review your application and get back to you soon.',
                'data' => $studentDetail,
                'redirect' => route('public.lead.rpa.register', $request->lead_id),
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
}
