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

class LeadEduMasterRegistrationController extends Controller
{
    public function showEduMasterForm($leadId = null)
    {
        // Get lead if ID is provided
        $lead = null;
        if ($leadId) {
            $lead = Lead::find($leadId);
            
            // Check if student has already registered
            if ($lead) {
                $studentDetail = LeadDetail::where('lead_id', $leadId)
                    ->where('course_id', 23)
                    ->first();
                
                if ($studentDetail) {
                    return view('public.edumaster-registration-success');
                }
            }
        }
        
        // Get EduMaster course subjects (course_id = 23)
        $subjects = Subject::where('course_id', 23)->where('is_active', true)->get();
        
        // Get EduMaster course batches (course_id = 23)
        $batches = Batch::where('course_id', 23)->where('is_active', true)->get();
        
        // Get active universities
        $universities = \App\Models\University::where('is_active', true)->get();
        
        // Get country codes
        $countryCodes = \App\Helpers\CountriesHelper::get_country_code();
        
        return view('public.edumaster-registration', compact('subjects', 'batches', 'universities', 'lead', 'countryCodes'));
    }
    
    public function store(Request $request)
    {
        // Get selected courses
        $selectedCourses = $request->input('selected_courses', []);
        $hasSSLC = in_array('SSLC', $selectedCourses);
        $hasPlusTwo = in_array('Plus two', $selectedCourses);
        $hasUG = in_array('UG', $selectedCourses);
        $hasPG = in_array('PG', $selectedCourses);
        
        // Build validation rules
        $rules = [
            'lead_id' => 'required|exists:leads,id',
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
            'selected_courses' => 'required|array|min:1',
            'selected_courses.*' => 'in:SSLC,Plus two,UG,PG',
            'passport_photo' => 'required|file|mimes:jpg,jpeg,png|max:1024',
            'adhar_front' => 'required|file|mimes:pdf,jpg,jpeg,png|max:1024',
            'adhar_back' => 'required|file|mimes:pdf,jpg,jpeg,png|max:1024',
            'signature' => 'required|file|mimes:jpg,jpeg,png|max:1024',
            'other_documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:1024',
            'message' => 'nullable|string',
        ];
        
        // Conditional validation for SSLC
        if ($hasSSLC) {
            $rules['sslc_back_year'] = 'required|integer|min:2018|max:' . date('Y');
        }
        
        // Conditional validation for Plus Two
        if ($hasPlusTwo) {
            $rules['plustwo_back_year'] = 'required|integer|min:2018|max:' . date('Y');
            $rules['plustwo_subject'] = 'required|string|max:255';
            $rules['sslc_certificate'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:1024';
            
            // Plus Two back year must be at least 2 years after SSLC back year
            if ($hasSSLC && $request->has('sslc_back_year')) {
                $sslcBackYear = (int) $request->sslc_back_year;
                $rules['plustwo_back_year'] .= '|min:' . ($sslcBackYear + 2);
            }
        }
        
        // Conditional validation for UG
        if ($hasUG) {
            $rules['university_id'] = 'required|exists:universities,id';
            $rules['course_type'] = 'required|in:UG';
            $rules['edumaster_course_name'] = 'required|string|max:255';
            $rules['degree_back_year'] = 'required|integer|min:2018|max:' . date('Y');
            $rules['plustwo_certificate'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:1024';
        }
        
        // Conditional validation for PG
        if ($hasPG) {
            $rules['university_id'] = 'required|exists:universities,id';
            $rules['course_type'] = 'required|in:PG';
            $rules['edumaster_course_name'] = 'required|string|max:255';
            $rules['degree_back_year'] = 'required|integer|min:2018|max:' . date('Y');
            $rules['ug_certificate'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:1024';
        }
        
        // If both UG and PG are selected, we need to handle this differently
        if ($hasUG && $hasPG) {
            // Require university and course selection (user selects one course type at a time)
            $rules['university_id'] = 'required|exists:universities,id';
            $rules['course_type'] = 'required|in:UG,PG';
            $rules['edumaster_course_name'] = 'required|string|max:255';
            $rules['degree_back_year'] = 'required|integer|min:2018|max:' . date('Y');
            // Both certificates are required when both UG and PG are selected
            $rules['plustwo_certificate'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:1024';
            $rules['ug_certificate'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:1024';
        }
        
        // Degree Back Year must be at least 2 years after Plus Two Back Year (when provided)
        if (($hasUG || $hasPG) && $hasPlusTwo && $request->has('plustwo_back_year')) {
            $plustwoBackYear = (int) $request->plustwo_back_year;
            $rules['degree_back_year'] .= '|min:' . ($plustwoBackYear + 2);
        }
        
        // Add batch validation if needed
        if ($request->has('batch_id')) {
            $rules['batch_id'] = 'exists:batches,id';
        }
        
        // Custom validation messages
        $messages = [
            'lead_id.required' => 'Lead ID is required.',
            'lead_id.exists' => 'Invalid lead.',
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
            'selected_courses.required' => 'Please select at least one course.',
            'selected_courses.min' => 'Please select at least one course.',
            'degree_back_year.required' => 'Degree back year is required.',
            'degree_back_year.min' => 'Degree back year must be at least 2 years after Plus Two back year.',
            'sslc_back_year.required' => 'SSLC back year is required.',
            'plustwo_back_year.required' => 'Plus Two back year is required.',
            'plustwo_back_year.min' => 'Plus Two back year must be at least 2 years after SSLC back year.',
            'plustwo_subject.required' => 'Plus Two Subject is required.',
            'plustwo_subject.string' => 'Plus Two Subject must be a valid text.',
            'plustwo_subject.max' => 'Plus Two Subject must not exceed 255 characters.',
            'university_id.required' => 'University selection is required.',
            'university_id.exists' => 'Selected university is invalid.',
            'back_year.required_if' => 'Back year is required for the selected university.',
            'course_type.required' => 'Course type is required.',
            'edumaster_course_name.required' => 'Course name is required.',
            'sslc_certificate.required' => 'Secondary (10th) Certificate is required when Plus Two is selected.',
            'sslc_certificate.file' => 'Secondary (10th) Certificate must be a valid file.',
            'sslc_certificate.mimes' => 'Secondary (10th) Certificate must be a PDF or image file.',
            'sslc_certificate.max' => 'Secondary (10th) Certificate file size must not exceed 1MB.',
            'plustwo_certificate.required' => 'Senior Secondary (12th) Certificate is required when UG is selected.',
            'plustwo_certificate.file' => 'Senior Secondary (12th) Certificate must be a valid file.',
            'plustwo_certificate.mimes' => 'Senior Secondary (12th) Certificate must be a PDF or image file.',
            'plustwo_certificate.max' => 'Senior Secondary (12th) Certificate file size must not exceed 1MB.',
            'ug_certificate.required' => 'Graduation Certificate is required when PG is selected.',
            'ug_certificate.file' => 'Graduation Certificate must be a valid file.',
            'ug_certificate.mimes' => 'Graduation Certificate must be a PDF or image file.',
            'ug_certificate.max' => 'Graduation Certificate file size must not exceed 1MB.',
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
        ];
        
        $request->validate($rules, $messages);
        
        try {
            // Handle file uploads
            $filePaths = [];
            $fileFields = ['passport_photo', 'adhar_front', 'adhar_back', 'signature'];
            
            // Add conditional file fields
            if ($hasPlusTwo && $request->hasFile('sslc_certificate')) {
                $fileFields[] = 'sslc_certificate';
            }
            if ($hasUG && $request->hasFile('plustwo_certificate')) {
                $fileFields[] = 'plustwo_certificate';
            }
            if ($hasPG && $request->hasFile('ug_certificate')) {
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
                'batch_id' => $request->batch_id ?? null,
            ]);
            
            // Prepare data for LeadDetail
            $detailData = [
                'lead_id' => $request->lead_id,
                'course_id' => 23, // EduMaster course ID
                'student_name' => $request->student_name,
                'father_name' => $request->father_name,
                'mother_name' => $request->mother_name,
                'date_of_birth' => $request->date_of_birth,
                'gender' => strtolower($request->gender), // Convert to lowercase for database enum
                'is_employed' => $request->is_employed === 'Yes', // Convert Yes/No to boolean
                'email' => $request->email,
                'personal_number' => $request->personal_number,
                'personal_code' => $request->personal_code,
                'father_contact_number' => $request->father_number,
                'father_contact_code' => $request->father_code,
                'mother_contact_number' => $request->mother_number,
                'mother_contact_code' => $request->mother_code,
                'whatsapp_number' => $request->whatsapp_number,
                'whatsapp_code' => $request->whatsapp_code,
                'residential_address' => $request->residential_address,
                'selected_courses' => json_encode($selectedCourses),
                'passport_photo' => $filePaths['passport_photo'] ?? null,
                'adhar_front' => $filePaths['adhar_front'] ?? null,
                'adhar_back' => $filePaths['adhar_back'] ?? null,
                'signature' => $filePaths['signature'] ?? null,
                'other_document' => !empty($otherDocuments) ? json_encode($otherDocuments) : null,
                'message' => $request->message,
                'status' => 'pending',
            ];
            
            // Add conditional fields
            if ($hasSSLC) {
                $detailData['sslc_back_year'] = $request->sslc_back_year;
            }
            
            if ($hasPlusTwo) {
                $detailData['plustwo_back_year'] = $request->plustwo_back_year;
                $detailData['plustwo_subject'] = $request->plustwo_subject;
                $detailData['sslc_certificate'] = $filePaths['sslc_certificate'] ?? null;
            }
            
            if ($hasUG || $hasPG) {
                $detailData['university_id'] = $request->university_id;
                $detailData['course_type'] = $request->course_type;
                $detailData['edumaster_course_name'] = $request->edumaster_course_name;
                if ($request->has('degree_back_year')) {
                    $detailData['degree_back_year'] = $request->degree_back_year;
                }
            }
            
            if ($hasUG) {
                $detailData['plustwo_certificate'] = $filePaths['plustwo_certificate'] ?? null;
            }
            
            if ($hasPG) {
                $detailData['ug_certificate'] = $filePaths['ug_certificate'] ?? null;
            }
            
            if ($request->has('batch_id')) {
                $detailData['batch_id'] = $request->batch_id;
            }
            
            // Create student detail record
            $studentDetail = LeadDetail::create($detailData);
            
            // Send registration confirmation email
            try {
                MailService::sendStudentRegistrationEmail($studentDetail, \App\Helpers\CourseTitleHelper::title(23, 'EduMaster'));
            } catch (\Exception $e) {
                // Log error but don't fail the registration
                Log::error('Email sending failed for EduMaster registration: ' . $e->getMessage());
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
                Log::error('Failed to create lead activity for EduMaster registration: ' . $e->getMessage());
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Registration submitted successfully! We will review your application and get back to you soon.',
                'data' => $studentDetail,
                'redirect' => route('public.lead.edumaster.register.success', $request->lead_id)
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
        $courseId = $request->query('course_id', 23);
        $subjects = Subject::where('course_id', $courseId)->where('is_active', true)->get(['id', 'title']);
        return response()->json($subjects);
    }

    public function getBatches(Request $request)
    {
        $courseId = $request->query('course_id', 23);
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
        return view('public.edumaster-registration-success');
    }
}
