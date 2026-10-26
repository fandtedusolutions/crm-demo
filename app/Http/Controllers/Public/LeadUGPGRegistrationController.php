<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\LeadDetail;
use App\Models\Subject;
use App\Models\Batch;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\MailService;

class LeadUGPGRegistrationController extends Controller
{
    public function showUGPGForm($leadId = null)
    {
        // Get lead if ID is provided
        $lead = null;
        if ($leadId) {
            $lead = Lead::find($leadId);
            
            // Check if student has already registered
            if ($lead && $lead->ugpgStudentDetails) {
                return view('public.ugpg-registration-success');
            }
        }
        
        // Get UG/PG course subjects (course_id = 9)
        $subjects = Subject::where('course_id', 9)->where('is_active', true)->get();
        
        // Get UG/PG course batches (course_id = 9)
        $batches = Batch::where('course_id', 9)->where('is_active', true)->get();
        
        // Get active universities
        $universities = \App\Models\University::where('is_active', true)->get();
        
        // Get country codes
        $countryCodes = \App\Helpers\CountriesHelper::get_country_code();
        
        return view('public.ugpg-registration', compact('subjects', 'batches', 'universities', 'lead', 'countryCodes'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'course_type' => 'required|in:UG,PG',
            'university_id' => 'required|exists:universities,id',
            'student_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|string|in:Male,Female,Other',
            'is_employed' => 'required|string|in:Yes,No',
            'email' => 'required|email|max:255',
            'personal_number' => 'required|string|max:20',
            'personal_code' => 'required|string|max:10',
            'father_number' => 'required|string|max:20',
            'father_code' => 'required|string|max:10',
            'mother_number' => 'required|string|max:20',
            'mother_code' => 'required|string|max:10',
            'whatsapp_number' => 'required|string|max:20',
            'whatsapp_code' => 'required|string|max:10',
            'residential_address' => 'required|string',
            'university_course_id' => 'required|exists:university_courses,id',
            'back_year' => 'required_if:university_id,1',
            'batch_id' => 'required|exists:batches,id',
            'sslc_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:1024',
            'passport_photo' => 'required|file|mimes:jpg,jpeg,png|max:1024',
            'adhar_front' => 'required|file|mimes:pdf,jpg,jpeg,png|max:1024',
            'adhar_back' => 'required|file|mimes:pdf,jpg,jpeg,png|max:1024',
            'signature' => 'required|file|mimes:jpg,jpeg,png|max:1024',
            'plustwo_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:1024',
            'ug_certificate' => 'required_if:course_type,PG|file|mimes:pdf,jpg,jpeg,png|max:1024',
            'other_documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:1024',
            'message' => 'nullable|string',
        ], [
            'lead_id.required' => 'Lead ID is required.',
            'lead_id.exists' => 'Invalid lead.',
            'course_type.required' => 'Course type is required.',
            'course_type.in' => 'Course type must be either UG or PG.',
            'university_id.required' => 'University selection is required.',
            'university_id.exists' => 'Selected university is invalid.',
            'student_name.required' => 'Candidate name is required.',
            'father_name.required' => 'Father\'s name is required.',
            'mother_name.required' => 'Mother\'s name is required.',
            'date_of_birth.required' => 'Date of birth is required.',
            'date_of_birth.date' => 'Please enter a valid date of birth.',
            'gender.required' => 'Gender is required.',
            'gender.in' => 'Please select a valid gender.',
            'is_employed.required' => 'Employment status is required.',
            'is_employed.in' => 'Please select Yes or No for employment status.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'personal_number.required' => 'Candidate contact number is required.',
            'personal_code.required' => 'Candidate country code is required.',
            'father_number.required' => 'Father\'s contact number is required.',
            'father_code.required' => 'Father\'s country code is required.',
            'mother_number.required' => 'Mother\'s contact number is required.',
            'mother_code.required' => 'Mother\'s country code is required.',
            'whatsapp_number.required' => 'WhatsApp number is required.',
            'whatsapp_code.required' => 'WhatsApp country code is required.',
            'residential_address.required' => 'Residential address is required.',
            'course_name.required' => 'Course name is required.',
            'back_year.required_if' => 'Back year is required for the selected university.',
            'sslc_certificate.required' => 'Secondary (10th) Certificate is required.',
            'sslc_certificate.file' => 'Secondary (10th) Certificate must be a valid file.',
            'sslc_certificate.mimes' => 'Secondary (10th) Certificate must be a PDF or image file.',
            'sslc_certificate.max' => 'Secondary (10th) Certificate file size must not exceed 1MB.',
            'passport_photo.required' => 'Recent Passport Size Photograph is required.',
            'passport_photo.file' => 'Recent Passport Size Photograph must be a valid file.',
            'passport_photo.mimes' => 'Recent Passport Size Photograph must be an image file (JPG, PNG).',
            'passport_photo.max' => 'Recent Passport Size Photograph file size must not exceed 1MB.',
            'adhar_front.required' => 'Aadhar Card (Front) is required.',
            'adhar_front.file' => 'Aadhar Card (Front) must be a valid file.',
            'adhar_front.mimes' => 'Aadhar Card (Front) must be a PDF or image file.',
            'adhar_front.max' => 'Aadhar Card (Front) file size must not exceed 1MB.',
            'adhar_back.required' => 'Aadhar Card (Back) is required.',
            'adhar_back.file' => 'Aadhar Card (Back) must be a valid file.',
            'adhar_back.mimes' => 'Aadhar Card (Back) must be a PDF or image file.',
            'adhar_back.max' => 'Aadhar Card (Back) file size must not exceed 1MB.',
            'signature.required' => 'Signature is required.',
            'signature.file' => 'Signature must be a valid file.',
            'signature.mimes' => 'Signature must be an image file (JPG, PNG).',
            'signature.max' => 'Signature file size must not exceed 1MB.',
            'plustwo_certificate.required' => 'Senior Secondary (12th) Certificate is required.',
            'plustwo_certificate.file' => 'Senior Secondary (12th) Certificate must be a valid file.',
            'plustwo_certificate.mimes' => 'Senior Secondary (12th) Certificate must be a PDF or image file.',
            'plustwo_certificate.max' => 'Senior Secondary (12th) Certificate file size must not exceed 1MB.',
            'ug_certificate.required_if' => 'Graduation Certificate is required for PG courses.',
            'ug_certificate.file' => 'Graduation Certificate must be a valid file.',
            'ug_certificate.mimes' => 'Graduation Certificate must be a PDF or image file.',
            'ug_certificate.max' => 'Graduation Certificate file size must not exceed 1MB.',
        ]);
        
        try {
            // Handle file uploads
            $filePaths = [];
            $fileFields = ['sslc_certificate', 'passport_photo', 'adhar_front', 'adhar_back', 'signature', 'plustwo_certificate'];
            
            // Add ug_certificate only if it's uploaded (for PG courses)
            if ($request->hasFile('ug_certificate')) {
                $fileFields[] = 'ug_certificate';
            }
            
            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('student-documents', $fileName, 'public');
                    $filePaths[$field] = $filePath;
                }
            }
            
            // Handle other documents (multiple files)
            $otherDocuments = [];
            if ($request->hasFile('other_documents')) {
                foreach ($request->file('other_documents') as $file) {
                    $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('student-documents', $fileName, 'public');
                    $otherDocuments[] = $filePath;
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
                'course_id' => 9, // UG/PG course ID
                'university_id' => $request->university_id,
                'course_type' => $request->course_type, // Store UG or PG
                'student_name' => $request->student_name,
                'father_name' => $request->father_name,
                'mother_name' => $request->mother_name,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'is_employed' => $request->is_employed === 'Yes', // Convert Yes/No to boolean
                'email' => $request->email,
                'personal_number' => $request->personal_number,
                'personal_code' => $request->personal_code,
                'father_number' => $request->father_number,
                'father_code' => $request->father_code,
                'mother_number' => $request->mother_number,
                'mother_code' => $request->mother_code,
                'whatsapp_number' => $request->whatsapp_number,
                'whatsapp_code' => $request->whatsapp_code,
                'residential_address' => $request->residential_address,
                'university_course_id' => $request->university_course_id,
                'back_year' => $request->back_year,
                'batch_id' => $request->batch_id,
                'sslc_certificate' => $filePaths['sslc_certificate'] ?? null,
                'passport_photo' => $filePaths['passport_photo'] ?? null,
                'adhar_front' => $filePaths['adhar_front'] ?? null,
                'adhar_back' => $filePaths['adhar_back'] ?? null,
                'signature' => $filePaths['signature'] ?? null,
                'plustwo_certificate' => $filePaths['plustwo_certificate'] ?? null,
                'ug_certificate' => $filePaths['ug_certificate'] ?? null,
                'other_documents' => !empty($otherDocuments) ? json_encode($otherDocuments) : null,
                'message' => $request->message,
                'status' => 'pending',
            ]);
            
            
            
            // Send registration confirmation email
            try {
                MailService::sendStudentRegistrationEmail($studentDetail, \App\Helpers\CourseTitleHelper::title(9, 'UG/PG'));
            } catch (\Exception $e) {
                // Log error but don't fail the registration
                Log::error('Email sending failed for UG/PG registration: ' . $e->getMessage());
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
                \Log::error('Failed to create lead activity for UG/PG registration: ' . $e->getMessage());
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Registration submitted successfully! We will review your application and get back to you soon.',
                'data' => $studentDetail,
                'redirect' => route('public.lead.ugpg.register.success', $request->lead_id)
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

    public function getCourses(Request $request)
    {
        $universityId = $request->query('university_id');
        $courseType = $request->query('course_type');
        
        if (!$universityId || !$courseType) {
            return response()->json([]);
        }
        
        $courses = \App\Models\UniversityCourse::where('university_id', $universityId)
            ->where('course_type', $courseType)
            ->where('is_active', true)
            ->get(['id', 'title']);
            
        return response()->json($courses);
    }

    public function showSuccess($leadId)
    {
        return view('public.ugpg-registration-success');
    }
}