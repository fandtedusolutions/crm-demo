<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\LeadDetail;
use App\Models\Subject;
use App\Models\Batch;
use App\Models\ClassTime;
use App\Support\CourseOfflinePlaceSupport;
use App\Support\CourseCourseTypeSupport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\MailService;

class LeadAIAutomationRegistrationController extends Controller
{
    public function showAIAutomationForm($leadId = null)
    {
        // Get lead if ID is provided
        $lead = null;
        if ($leadId) {
            $lead = Lead::find($leadId);
            
            // Check if student has already registered (course_id = 12 for Diploma in Data Science)
            if ($lead) {
                $studentDetail = LeadDetail::where('lead_id', $leadId)
                    ->where('course_id', 12)
                    ->first();
                
                if ($studentDetail) {
                    return view('public.diploma-in-data-science-registration-success');
                }
            }
        }
        
        // Get Diploma in Data Science course subjects (course_id = 12)
        $subjects = Subject::where('course_id', 12)->where('is_active', true)->get();
        
        // Get Diploma in Data Science course batches (course_id = 12)
        $batches = Batch::where('course_id', 12)->where('is_active', true)->get();
        
        // Get course data
        $course = \App\Models\Course::find(12);
        
        // Get class times for course_id = 12 (Diploma in Data Science)
        $classTimes = collect();
        if ($course && $course->needs_time) {
            $classTimes = ClassTime::where('course_id', 12)->where('is_active', true)->get();
        }
        
        // Get active offline places
        $offlinePlaces = CourseOfflinePlaceSupport::placesFor($course);
        $courseTypes = CourseCourseTypeSupport::typesFor($course);
        
        // Get country codes
        $countryCodes = \App\Helpers\CountriesHelper::get_country_code();
        
        return view('public.diploma-in-data-science-registration', compact('subjects', 'batches', 'lead', 'countryCodes', 'classTimes', 'course', 'offlinePlaces', 'courseTypes'));
    }
    
    public function store(Request $request)
    {
        $course = \App\Models\Course::find(12);

        $request->validate(array_merge([
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
            'father_contact_number' => 'required|string|max:20',
            'father_contact_code' => 'required|string|max:10',
            'mother_contact_number' => 'required|string|max:20',
            'mother_contact_code' => 'required|string|max:10',
            'whatsapp_number' => 'required|string|max:20',
            'whatsapp_code' => 'required|string|max:10',
            'programme_type' => 'required|in:online,offline',
            'class_time_id' => 'nullable|exists:class_times,id',
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
            'plus_two_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'graduation_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'post_graduation_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'other_relevant_documents' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], CourseOfflinePlaceSupport::locationValidationRules($course), CourseCourseTypeSupport::validationRules($course, 12)), [
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
            'batch_id.required' => 'Batch selection is required.',
            'batch_id.exists' => 'Please select a valid batch.',
            'street.required' => 'Street address is required.',
            'locality.required' => 'Locality is required.',
            'post_office.required' => 'Post office is required.',
            'district.required' => 'District is required.',
            'state.required' => 'State is required.',
            'pin_code.required' => 'Pin code is required.',
            'pin_code.regex' => 'Pin code must be exactly 6 digits.',
            'sslc_certificate.required' => 'SSLC certificate is required.',
            'sslc_certificate.file' => 'SSLC certificate must be a valid file.',
            'sslc_certificate.mimes' => 'SSLC certificate must be a PDF or image file.',
            'sslc_certificate.max' => 'SSLC certificate file size must not exceed 2MB.',
            'plus_two_certificate.file' => 'Plus Two certificate must be a valid file.',
            'plus_two_certificate.mimes' => 'Plus Two certificate must be a PDF or image file.',
            'plus_two_certificate.max' => 'Plus Two certificate file size must not exceed 2MB.',
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
            'gender.required' => 'Gender is required.',
            'is_employed.required' => 'Employment status is required.',
            'father_contact_number.required' => 'Father contact number is required.',
            'father_contact_code.required' => 'Father contact country code is required.',
            'mother_contact_number.required' => 'Mother contact number is required.',
            'mother_contact_code.required' => 'Mother contact country code is required.',
            'programme_type.required' => 'Course type is required.',
            'location.required_if' => 'Location is required for offline courses.',
            'class_time_id.exists' => 'Please select a valid class time.',
            'graduation_certificate.file' => 'Graduation certificate must be a valid file.',
            'post_graduation_certificate.file' => 'Post-graduation certificate must be a valid file.',
            'other_relevant_documents.file' => 'Other relevant documents must be a valid file.',
        ]);
        
        try {
            // Handle file uploads
            $filePaths = [];
            $fileFields = ['passport_photo', 'adhar_front', 'adhar_back', 'signature', 'sslc_certificate', 'plus_two_certificate', 'graduation_certificate', 'post_graduation_certificate', 'other_relevant_documents'];
            
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
                'gender' => $request->gender,
            ]);
            
            // Create student detail record
            $studentDetail = LeadDetail::create([
                'lead_id' => $request->lead_id,
                'course_id' => 12, // Diploma in Data Science course ID
                'student_name' => $request->student_name,
                'father_name' => $request->father_name,
                'mother_name' => $request->mother_name,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'is_employed' => $request->is_employed,
                'email' => $request->email,
                'personal_number' => $request->personal_number,
                'personal_code' => $request->personal_code,
                'father_contact_number' => $request->father_contact_number,
                'father_contact_code' => $request->father_contact_code,
                'mother_contact_number' => $request->mother_contact_number,
                'mother_contact_code' => $request->mother_contact_code,
                'whatsapp_number' => $request->whatsapp_number,
                'whatsapp_code' => $request->whatsapp_code,
                'course_type_id' => $request->course_type_id,
                'programme_type' => $request->programme_type,
                'location' => $request->location,
                'class_time_id' => $request->class_time_id,
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
                'plus_two_certificate' => $filePaths['plus_two_certificate'] ?? null,
                'ug_certificate' => $filePaths['graduation_certificate'] ?? null,
                'post_graduation_certificate' => $filePaths['post_graduation_certificate'] ?? null,
                'other_document' => $filePaths['other_relevant_documents'] ?? null,
                'status' => 'pending',
            ]);
            
            
            
            // Send registration confirmation email
            try {
                MailService::sendStudentRegistrationEmail($studentDetail, 'Diploma in Data Science');
            } catch (\Exception $e) {
                // Log error but don't fail the registration
                \Log::error('Email sending failed for Diploma in Data Science registration: ' . $e->getMessage());
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
                \Log::error('Failed to create lead activity for Diploma in Data Science registration: ' . $e->getMessage());
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Registration submitted successfully! We will review your application and get back to you soon.',
                'data' => $studentDetail,
                'redirect' => route('public.lead.diploma-in-data-science.register', $request->lead_id)
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
