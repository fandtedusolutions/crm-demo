<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\LeadDetail;
use App\Models\Subject;
use App\Models\Batch;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\MailService;

class LeadESchoolRegistrationController extends Controller
{
    public function showESchoolForm($leadId = null)
    {
        // Get lead if ID is provided
        $lead = null;
        if ($leadId) {
            $lead = Lead::find($leadId);
            
            // Check if student has already registered
            if ($lead && $lead->eSchoolStudentDetails) {
                return view('public.eschool-registration-success');
            }
        }
        
        // Get E-School course subjects (course_id = 5)
        $subjects = Subject::where('course_id', 5)->where('is_active', true)->get();
        
        // Get E-School course batches (course_id = 5)
        $batches = Batch::where('course_id', 5)->where('is_active', true)->get();
        
        // Get country codes
        $countryCodes = \App\Helpers\CountriesHelper::get_country_code();
        
        return view('public.eschool-registration', compact('subjects', 'batches', 'lead', 'countryCodes'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
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
            'batch_id' => 'required|exists:batches,id',
            'street' => 'required|string',
            'locality' => 'required|string|max:255',
            'post_office' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'pin_code' => 'required|string|regex:/^[0-9]{6}$/',
            'passport_photo' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'other_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'message' => 'nullable|string',
        ], [
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
            'other_document.required' => 'Other document is required.',
            'other_document.file' => 'Other document must be a valid file.',
            'other_document.mimes' => 'Other document must be a PDF or image file.',
            'other_document.max' => 'Other document file size must not exceed 2MB.',
        ]);
        
        try {
            // Handle file uploads
            $filePaths = [];
            $fileFields = ['passport_photo', 'other_document'];
            
            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('student-documents', $fileName, 'public');
                    $filePaths[$field] = $filePath;
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
                'course_id' => 5, // E-School course ID
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
                'batch_id' => $request->batch_id,
                'street' => $request->street,
                'locality' => $request->locality,
                'post_office' => $request->post_office,
                'district' => $request->district,
                'state' => $request->state,
                'pin_code' => $request->pin_code,
                'passport_photo' => $filePaths['passport_photo'] ?? null,
                'other_document' => $filePaths['other_document'] ?? null,
                'message' => $request->message,
                'status' => 'pending',
            ]);
            
            
            
            // Send registration confirmation email
            try {
                MailService::sendStudentRegistrationEmail($studentDetail, \App\Helpers\CourseTitleHelper::title(5, 'E-School'));
            } catch (\Exception $e) {
                // Log error but don't fail the registration
                \Log::error('Email sending failed for E-School registration: ' . $e->getMessage());
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
                \Log::error('Failed to create lead activity for E-School registration: ' . $e->getMessage());
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Registration submitted successfully! We will review your application and get back to you soon.',
                'data' => $studentDetail,
                'redirect' => route('public.lead.eschool.register.success', $request->lead_id)
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

    public function showSuccess($leadId)
    {
        return view('public.eschool-registration-success');
    }
}