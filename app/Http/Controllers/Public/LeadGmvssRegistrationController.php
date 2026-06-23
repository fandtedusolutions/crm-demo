<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\LeadDetail;
use App\Models\Subject;
use App\Models\Batch;
use App\Services\MailService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LeadGmvssRegistrationController extends Controller
{
    public function showGmvssForm($leadId = null)
    {
        // Get lead if ID is provided
        $lead = null;
        if ($leadId) {
            $lead = Lead::find($leadId);
            
            // Check if student has already registered
            if ($lead && $lead->gmvssStudentDetails) {
                return view('public.gmvss-registration-success');
            }
        }
        
        // Get Grameen Mukt Vidhyalayi Shiksha Sansthan course subjects (course_id = 16)
        $subjects = Subject::where('course_id', 16)->where('is_active', true)->get();
        
        // Get Grameen Mukt Vidhyalayi Shiksha Sansthan course batches (course_id = 16)
        $batches = Batch::where('course_id', 16)->where('is_active', true)->get();
        
        // Get country codes
        $countryCodes = \App\Helpers\CountriesHelper::get_country_code();
        
        return view('public.gmvss-registration', compact('subjects', 'batches', 'lead', 'countryCodes'));
    }
    
    public function store(Request $request)
    {
        // Add conditional validation for SSLC certificate
        $validationRules = [
            'lead_id' => 'required|exists:leads,id',
            'student_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'email' => 'required|email|max:255',
            'personal_number' => 'required|string|max:20',
            'personal_code' => 'required|string|max:10',
            'parents_number' => 'required|string|max:20',
            'parents_code' => 'required|string|max:10',
            'whatsapp_number' => 'required|string|max:20',
            'whatsapp_code' => 'required|string|max:10',
            'subject_id' => 'required|exists:subjects,id',
            'batch_id' => 'required|exists:batches,id',
            'class' => 'required|in:sslc,plustwo',
            'second_language' => 'required|in:malayalam,hindi',
            'passed_year' => 'required|integer|min:2018|max:' . date('Y'),
            'street' => 'required|string',
            'locality' => 'required|string|max:255',
            'post_office' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'pin_code' => 'required|string|regex:/^[0-9]{6}$/',
            'birth_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'passport_photo' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'adhar_front' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'adhar_back' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'signature' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'sslc_certificates' => 'nullable|array',
            'sslc_certificates.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
            'message' => 'nullable|string',
        ];
        
        // Add conditional validation for SSLC certificates if class is plustwo
        if ($request->class === 'plustwo') {
            $validationRules['sslc_certificates'] = 'required|array|min:1';
            $validationRules['sslc_certificates.*'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:2048';
        }
        
        $request->validate($validationRules, [
            'lead_id.required' => 'Lead ID is required.',
            'lead_id.exists' => 'Invalid lead.',
            'student_name.required' => 'Student name is required.',
            'father_name.required' => 'Father name is required.',
            'mother_name.required' => 'Mother name is required.',
            'date_of_birth.required' => 'Date of birth is required.',
            'date_of_birth.date' => 'Please enter a valid date of birth.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'personal_number.required' => 'Personal number is required.',
            'personal_code.required' => 'Personal country code is required.',
            'parents_number.required' => 'Parents number is required.',
            'parents_code.required' => 'Parents country code is required.',
            'whatsapp_number.required' => 'WhatsApp number is required.',
            'whatsapp_code.required' => 'WhatsApp country code is required.',
            'subject_id.required' => 'Subject selection is required.',
            'subject_id.exists' => 'Please select a valid subject.',
            'batch_id.required' => 'Batch selection is required.',
            'batch_id.exists' => 'Please select a valid batch.',
            'class.required' => 'Class selection is required.',
            'class.in' => 'Please select a valid class.',
            'second_language.required' => 'Second language selection is required.',
            'second_language.in' => 'Please select a valid second language.',
            'passed_year.required' => 'Passed year is required.',
            'passed_year.integer' => 'Passed year must be a valid year.',
            'passed_year.min' => 'Passed year must be 2018 or later.',
            'passed_year.max' => 'Passed year cannot be in the future.',
            'street.required' => 'Street address is required.',
            'locality.required' => 'Locality is required.',
            'post_office.required' => 'Post office is required.',
            'district.required' => 'District is required.',
            'state.required' => 'State is required.',
            'pin_code.required' => 'Pin code is required.',
            'pin_code.regex' => 'Pin code must be exactly 6 digits.',
            'birth_certificate.file' => 'Birth certificate must be a valid file.',
            'birth_certificate.mimes' => 'Birth certificate must be a PDF or image file.',
            'birth_certificate.max' => 'Birth certificate file size must not exceed 2MB.',
            'passport_photo.required' => 'Passport photo is required.',
            'passport_photo.file' => 'Passport photo must be a valid file.',
            'passport_photo.mimes' => 'Passport photo must be an image file (JPG, PNG).',
            'passport_photo.max' => 'Passport photo file size must not exceed 2MB.',
            'adhar_front.required' => 'Aadhar front is required.',
            'adhar_front.file' => 'Aadhar front must be a valid file.',
            'adhar_front.mimes' => 'Aadhar front must be a PDF or image file.',
            'adhar_front.max' => 'Aadhar front file size must not exceed 2MB.',
            'adhar_back.required' => 'Aadhar back is required.',
            'adhar_back.file' => 'Aadhar back must be a valid file.',
            'adhar_back.mimes' => 'Aadhar back must be a PDF or image file.',
            'adhar_back.max' => 'Aadhar back file size must not exceed 2MB.',
            'signature.required' => 'Signature is required.',
            'signature.file' => 'Signature must be a valid file.',
            'signature.mimes' => 'Signature must be an image file (JPG, PNG).',
            'signature.max' => 'Signature file size must not exceed 2MB.',
            'sslc_certificates.required' => 'SSLC certificates are required for Plus Two class.',
            'sslc_certificates.array' => 'SSLC certificates must be uploaded as files.',
            'sslc_certificates.min' => 'At least one SSLC certificate is required.',
            'sslc_certificates.*.required' => 'Each SSLC certificate file is required.',
            'sslc_certificates.*.file' => 'SSLC certificate must be a valid file.',
            'sslc_certificates.*.mimes' => 'SSLC certificate must be a PDF or image file.',
            'sslc_certificates.*.max' => 'SSLC certificate file size must not exceed 2MB.',
        ]);
        
        try {
            // Handle file uploads
            $filePaths = [];
            $fileFields = ['birth_certificate', 'passport_photo', 'adhar_front', 'adhar_back', 'signature'];
            
            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('student-documents', $fileName, 'public');
                    $filePaths[$field] = $filePath;
                }
            }
            
            // Handle multiple SSLC certificates
            $sslcCertificateIds = [];
            if ($request->hasFile('sslc_certificates')) {
                foreach ($request->file('sslc_certificates') as $file) {
                    $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('student-documents', $fileName, 'public');
                    
                    // Create SSLC certificate record
                    $sslcCertificate = \App\Models\SSLCertificate::create([
                        'lead_detail_id' => null, // Will be updated after LeadDetail is created
                        'certificate_path' => $filePath,
                        'original_filename' => $file->getClientOriginalName(),
                        'file_type' => $file->getClientOriginalExtension(),
                        'file_size' => $file->getSize(),
                        'verification_status' => 'pending',
                    ]);
                    
                    $sslcCertificateIds[] = $sslcCertificate->id;
                }
            }
            
            // Update lead with new information
            $lead = Lead::findOrFail($request->lead_id);
            $lead->update([
                'title' => $request->student_name,
                'email' => $request->email,
                'phone' => $request->personal_number,
                'code' => $request->personal_code,
                'batch_id' => $request->batch_id,
            ]);
            
            // Create student detail record
            $studentDetail = LeadDetail::create([
                'lead_id' => $request->lead_id,
                'course_id' => 16, // Grameen Mukt Vidhyalayi Shiksha Sansthan course ID
                'student_name' => $request->student_name,
                'father_name' => $request->father_name,
                'mother_name' => $request->mother_name,
                'date_of_birth' => $request->date_of_birth,
                'email' => $request->email,
                'personal_number' => $request->personal_number,
                'personal_code' => $request->personal_code,
                'parents_number' => $request->parents_number,
                'parents_code' => $request->parents_code,
                'whatsapp_number' => $request->whatsapp_number,
                'whatsapp_code' => $request->whatsapp_code,
                'subject_id' => $request->subject_id,
                'batch_id' => $request->batch_id,
                'class' => $request->class,
                'second_language' => $request->second_language,
                'passed_year' => $request->passed_year,
                'street' => $request->street,
                'locality' => $request->locality,
                'post_office' => $request->post_office,
                'district' => $request->district,
                'state' => $request->state,
                'pin_code' => $request->pin_code,
                'birth_certificate' => $filePaths['birth_certificate'] ?? null,
                'passport_photo' => $filePaths['passport_photo'] ?? null,
                'adhar_front' => $filePaths['adhar_front'] ?? null,
                'adhar_back' => $filePaths['adhar_back'] ?? null,
                'signature' => $filePaths['signature'] ?? null,
                // SSLC certificate is now handled separately in sslc_certificates table
                'message' => $request->message,
                'status' => 'pending',
            ]);
            
            // Update SSLC certificate records with the correct lead_detail_id
            if (!empty($sslcCertificateIds)) {
                \App\Models\SSLCertificate::whereIn('id', $sslcCertificateIds)
                    ->update(['lead_detail_id' => $studentDetail->id]);
            }
            
            // Send registration confirmation email
            try {
                MailService::sendStudentRegistrationEmail($studentDetail, 'Grameen Mukt Vidhyalayi Shiksha Sansthan');
            } catch (\Exception $e) {
                // Log error but don't fail the registration
                Log::error('Email sending failed for Grameen Mukt Vidhyalayi Shiksha Sansthan registration: ' . $e->getMessage());
            }
            
            // Log lead activity for form submission
            try {
                \App\Models\LeadActivity::create([
                    'lead_id' => $request->lead_id,
                    'activity_type' => 'registration_submitted',
                    'description' => 'Registration form submitted',
                    'remarks' => 'Registration form submitted on ' . now()->format('d-m-Y') . ' at ' . now()->format('h:i A'),
                    'created_by' => $lead->telecaller_id, // Use telecaller_id from lead
                ]);
            } catch (\Exception $e) {
                // Log error but don't fail the registration
                \Log::error('Failed to create lead activity for Grameen Mukt Vidhyalayi Shiksha Sansthan registration: ' . $e->getMessage());
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Registration submitted successfully! We will review your application and get back to you soon.',
                'data' => $studentDetail,
                'redirect' => route('public.lead.gmvss.register', $request->lead_id)
            ]);
            
        } catch (\Exception $e) {
            // Clean up uploaded files if there's an error
            foreach ($filePaths as $filePath) {
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }
            
            // Clean up SSLC certificate files and records
            if (!empty($sslcCertificateIds)) {
                foreach ($sslcCertificateIds as $certId) {
                    $certificate = \App\Models\SSLCertificate::find($certId);
                    if ($certificate && Storage::disk('public')->exists($certificate->certificate_path)) {
                        Storage::disk('public')->delete($certificate->certificate_path);
                    }
                    $certificate->delete();
                }
            }
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting your registration. Please try again. Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSubjects(Request $request)
    {
        $courseId = $request->query('course_id');
        $subjects = Subject::where('course_id', $courseId)->where('is_active', true)->get(['id', 'title']);
        return response()->json($subjects);
    }

    public function getBatches(Request $request)
    {
        $courseId = $request->query('course_id');
        $batches = Batch::where('course_id', $courseId)->where('is_active', true)->get(['id', 'title']);
        return response()->json($batches);
    }
}
