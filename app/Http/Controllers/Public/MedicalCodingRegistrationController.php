<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ConvertedLead;
use App\Models\ConvertedStudentDetail;
use App\Models\Subject;
use App\Models\Batch;
use App\Services\MailService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MedicalCodingRegistrationController extends Controller
{
    public function showMedicalCodingForm($convertedLeadId = null)
    {
        // Get converted lead if ID is provided
        $convertedLead = null;
        if ($convertedLeadId) {
            $convertedLead = ConvertedLead::find($convertedLeadId);
            
            // Check if student has already registered for Certificate Course in Medical Coding
            if ($convertedLead && $convertedLead->medicalCodingStudentDetails) {
                return view('public.medical-coding-registration-success');
            }
        }
        
        // Get Certificate Course in Medical Coding course subjects (course_id = 3)
        $subjects = Subject::where('course_id', 3)->where('is_active', true)->get();
        
        // Get Certificate Course in Medical Coding course batches (course_id = 3)
        $batches = Batch::where('course_id', 3)->where('is_active', true)->get();
        
        // Get country codes
        $countryCodes = \App\Helpers\CountriesHelper::get_country_code();
        
        return view('public.medical-coding-registration', compact('subjects', 'batches', 'convertedLead', 'countryCodes'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'converted_lead_id' => 'required|exists:converted_leads,id',
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
            'plustwo_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'message' => 'nullable|string',
        ], [
            'converted_lead_id.required' => 'Converted lead ID is required.',
            'converted_lead_id.exists' => 'Invalid converted lead.',
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
            'street.required' => 'Street address is required.',
            'locality.required' => 'Locality is required.',
            'post_office.required' => 'Post office is required.',
            'district.required' => 'District is required.',
            'state.required' => 'State is required.',
            'pin_code.required' => 'Pin code is required.',
            'pin_code.regex' => 'Pin code must be exactly 6 digits.',
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
            'plustwo_certificate.required' => 'Plus Two certificate is required.',
            'plustwo_certificate.file' => 'Plus Two certificate must be a valid file.',
            'plustwo_certificate.mimes' => 'Plus Two certificate must be a PDF or image file.',
            'plustwo_certificate.max' => 'Plus Two certificate file size must not exceed 2MB.',
        ]);
        
        try {
            // Handle file uploads
            $filePaths = [];
            $fileFields = ['passport_photo', 'adhar_front', 'adhar_back', 'signature', 'plustwo_certificate'];
            
            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('student-documents', $fileName, 'public');
                    $filePaths[$field] = $filePath;
                }
            }
            
            // Update converted lead with new information
            $convertedLead = ConvertedLead::findOrFail($request->converted_lead_id);
            $convertedLead->update([
                'name' => $request->student_name,
                'email' => $request->email,
                'phone' => $request->personal_number,
                'code' => $request->personal_code,
            ]);
            
            // Create student detail record for Certificate Course in Medical Coding
            $studentDetail = ConvertedStudentDetail::create([
                'converted_lead_id' => $request->converted_lead_id,
                'course_id' => 3, // Certificate Course in Medical Coding course ID
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
                'plustwo_certificate' => $filePaths['plustwo_certificate'] ?? null,
                'message' => $request->message,
                'status' => 'pending',
            ]);
            
            // Send registration confirmation email
            try {
                MailService::sendStudentRegistrationEmail($studentDetail, 'Certificate Course in Medical Coding');
            } catch (\Exception $e) {
                // Log error but don't fail the registration
                \Log::error('Email sending failed for Certificate Course in Medical Coding registration: ' . $e->getMessage());
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Registration submitted successfully! We will review your application and get back to you soon.',
                'data' => $studentDetail,
                'redirect' => route('public.medical-coding.register', $request->converted_lead_id)
            ]);
            
        } catch (\Exception $e) {
            // Clean up uploaded files if there's an error
            foreach ($filePaths as $filePath) {
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
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
