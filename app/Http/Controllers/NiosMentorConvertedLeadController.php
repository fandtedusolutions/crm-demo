<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConvertedLead;
use App\Models\ConvertedStudentMentorDetail;
use App\Models\Subject;
use App\Models\Batch;
use App\Models\AdmissionBatch;
use App\Helpers\AuthHelper;
use App\Helpers\RoleHelper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class NiosMentorConvertedLeadController extends Controller
{
    /**
     * Display a listing of NIOS converted leads for mentoring
     */
    public function index(Request $request)
    {
        $query = ConvertedLead::with([
            'lead.team',
            'leadDetail.addonCourse',
            'course',
            'academicAssistant',
            'createdBy',
            'cancelledBy',
            'studentDetails',
            'mentorDetails.subject',
            'subject',
            'subjectAreas',
            'flag',
            'batch',
            'admissionBatch'
        ])->forCourseListing(1) // NIOS course
          ->where('is_support_verified', 1);

        // Filter by post-sale flag only if explicitly requested
        if ($request->filled('is_postsale')) {
            $value = (string) $request->input('is_postsale');
            if ($value === 'postsale' || $value === '1') {
                $query->where('converted_leads.is_postsale', 1);
            } elseif ($value === 'normal' || $value === '0') {
                $query->where(function ($inner) {
                    $inner->whereNull('converted_leads.is_postsale')->orWhere('converted_leads.is_postsale', 0);
                });
            }
        }

        // On this mentor page, only regular mentors and faculty (who are not mentor heads) are restricted by their user_id / assigned admission batches.
        // Super Admin, Admin, Admission Counselor, Academic Assistant, Support Team, Mentor Head, Auditor, General Manager, etc. see all support-verified leads.
        if ((RoleHelper::is_mentor() || RoleHelper::is_faculty()) && !RoleHelper::is_mentor_head() && !RoleHelper::is_admin_or_super_admin()) {
            $mentorAdmissionBatchIds = AdmissionBatch::where('mentor_id', AuthHelper::getCurrentUserId())
                ->pluck('id')
                ->toArray();

            if (!empty($mentorAdmissionBatchIds)) {
                $query->forMentorAdmissionBatches($mentorAdmissionBatchIds);
            } else {
                // If mentor has no admission batches assigned, return empty result
                $query->whereRaw('1 = 0');
            }
        } elseif (RoleHelper::is_hod()) {
            // HOD: Only see leads for courses where they are assigned as HOD
            $hodCourseIds = \App\Models\Course::where('hod_id', AuthHelper::getCurrentUserId())
                ->pluck('id')
                ->toArray();

            if (empty($hodCourseIds) || !in_array(1, $hodCourseIds)) {
                $query->whereRaw('1 = 0');
            }
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('register_number', 'like', "%{$search}%")
                  ->orWhereHas('studentDetails', function($subQ) use ($search) {
                      $subQ->where('enroll_no', 'like', "%{$search}%")
                           ->orWhere('application_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('mentorDetails', function($subQ) use ($search) {
                      $subQ->where('application_number', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('batch_id')) {
            $query->whereListingBatch($request->batch_id);
        }

        if ($request->filled('admission_batch_id')) {
            $query->whereListingAdmissionBatch($request->admission_batch_id);
        }

        if ($request->filled('subject_id')) {
            $subjectId = $request->subject_id;
            $query->where(function($q) use ($subjectId) {
                $q->where('subject_id', $subjectId)
                  ->orWhereHas('mentorDetails', function($subQ) use ($subjectId) {
                      $subQ->where('subject_id', $subjectId);
                  });
            });
        }

        if ($request->filled('registration_status')) {
            $query->whereHas('mentorDetails', function($q) use ($request) {
                $q->where('registration_status', $request->registration_status);
            });
        }

        if ($request->filled('student_status')) {
            $query->whereHas('mentorDetails', function($q) use ($request) {
                $q->where('student_status', $request->student_status);
            });
        }

        \App\Support\MentorFlagFieldSupport::applyListingFilter($query, $request);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $perPage = (int) $request->input('per_page', 50);
        if (!in_array($perPage, [25, 50, 100, 200, 500], true)) {
            $perPage = 50;
        }

        $convertedLeads = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Get filter data
        $batches = Batch::where('course_id', 1)->orderBy('title')->get();
        $admissionBatchesQuery = AdmissionBatch::whereHas('batch', function($q) {
            $q->where('course_id', 1);
        });

        if ((RoleHelper::is_mentor() || RoleHelper::is_faculty()) && !RoleHelper::is_mentor_head() && !RoleHelper::is_admin_or_super_admin()) {
            $admissionBatchesQuery->where('mentor_id', AuthHelper::getCurrentUserId());
        }

        $admissionBatches = $admissionBatchesQuery->orderBy('title')->get();
        $subjects = Subject::where('course_id', 1)->orderBy('title')->get();
        $country_codes = \App\Helpers\CountriesHelper::get_country_code();
        $flags = \App\Support\MentorFlagFieldSupport::forFilterSelect();

        return view('admin.converted-leads.nios-mentor-index', compact(
            'convertedLeads',
            'batches',
            'admissionBatches',
            'subjects',
            'country_codes',
            'flags'
        ));
    }

    /**
     * Update mentor details inline
     */
    public function updateMentorDetails(Request $request, $id)
    {
        try {
            $convertedLead = ConvertedLead::findOrFail($id);
            $field = $request->field;
            $value = $request->value;

            if ($denied = \App\Support\MentorFlagFieldSupport::mentorFieldDeniedJsonResponse($field, ['subject_id', 'registration_status', 'status', 'admission_batch_id'])) {
                return $denied;
            }

            if ($field === 'admission_batch_id') {
                if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor() && !RoleHelper::is_academic_assistant() && !RoleHelper::is_hod()) {
                    return response()->json([
                        'success' => false,
                        'error' => 'You do not have permission to edit admission batch.'
                    ], 403);
                }
            }

            if ($denied = \App\Support\MentorFlagFieldSupport::mentorLeadScopeDeniedJsonResponse($convertedLead)) {
                return $denied;
            }

            // Validate the field and value
            $validationRules = $this->getValidationRules($field);
            if ($validationRules) {
                $validator = Validator::make([$field => $value], [$field => $validationRules]);
                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'error' => $validator->errors()->first($field)
                    ], 422);
                }
            }

            if ($field === 'flag_id') {
                return \App\Support\MentorFlagFieldSupport::flagUpdateJsonResponse($convertedLead, $value);
            }

            // Handle status and admission_batch_id fields - update in converted_leads table
            if ($field === 'status') {
                $convertedLead->status = $value;
                $convertedLead->save();
            } elseif ($field === 'admission_batch_id') {
                $oldAdmissionBatchId = $convertedLead->admission_batch_id;
                $convertedLead->admission_batch_id = $value ?: null;
                if ((string)$oldAdmissionBatchId !== (string)$value) {
                    $convertedLead->admission_batch_assigned_at = $value ? now() : null;
                }
                $convertedLead->save();
            } else {
                // Handle all other fields - update in converted_student_mentor_details table
                $mentorDetails = $convertedLead->mentorDetails;
                if (!$mentorDetails) {
                    $mentorDetails = new ConvertedStudentMentorDetail();
                    $mentorDetails->converted_student_id = $id;
                }
                $mentorDetails->$field = $value;
                $mentorDetails->save();
            }

            // Format the response value
            $responseValue = $this->formatResponseValue($field, $value);

            return response()->json([
                'success' => true,
                'message' => 'Updated successfully',
                'value' => $responseValue
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating NIOS mentor details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get validation rules for specific fields
     */
    private function getValidationRules($field)
    {
        $rules = [
            'admission_batch_id' => 'nullable|exists:admission_batches,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'flag_id' => \App\Support\MentorFlagFieldSupport::validationRule(),
            'status' => 'nullable|in:Paid,Admission cancel,Active,Inactive',
            'registration_status' => 'nullable|in:Paid,Not Paid',
            'technology_side' => 'nullable|in:No Knowledge,Limited Knowledge,Moderate Knowledge,High Knowledge',
            'student_status' => 'nullable|in:Low Level,Below Medium,Medium Level,Advanced Level',
            'problems' => 'nullable|string|max:1000',
            'exam_fees' => 'nullable|in:Not Respond,Task Complete',
            'pcp_class' => 'nullable|in:Not Respond,Task Complete',
            'practical_record' => 'nullable|in:Not Respond,1 Subject Attend,2 Subject Attend,3 Subject Attend,4 Subject Attend,5 Subject Attend,6 Subject Attend,Task Complete',
            'id_card' => 'nullable|in:Did Not,Task Complete',
            'practical_hall_ticket' => 'nullable|in:Did Not,Task Complete',
            'particle_exam' => 'nullable|in:Did not log in on time,missed the exam,technical issue,task complete',
            'theory_hall_ticket' => 'nullable|in:Did Not,Task Complete',
        ];

        // Add call status rules
        $callFields = ['call_1', 'call_2', 'call_3', 'call_4', 'call_5', 'call_6', 'call_7', 'call_8', 'call_9', 'call_10'];
        foreach ($callFields as $callField) {
            $rules[$callField] = 'nullable|in:Call Not Answered,Switched Off,Line Busy,Student Asks to Call Later,Lack of Interest in Conversation,Wrong Contact,Inconsistent Responses,Task Complete';
        }

        // Add mentor live rules
        $mentorLiveFields = ['mentor_live_1', 'mentor_live_2', 'mentor_live_3', 'mentor_live_4', 'mentor_live_5'];
        foreach ($mentorLiveFields as $mentorField) {
            $rules[$mentorField] = 'nullable|in:Not Respond,Task Complete';
        }

        // Add exam subject rules
        $examSubjectFields = ['exam_subject_1', 'exam_subject_2', 'exam_subject_3', 'exam_subject_4', 'exam_subject_5', 'exam_subject_6'];
        foreach ($examSubjectFields as $examField) {
            $rules[$examField] = 'nullable|in:Did not log in on time,missed the exam,technical issue,task complete';
        }


        // Add other field rules
        $otherFields = [
            'app' => 'nullable|in:Provided app,OTP Problem,Task Completed,Not Respond',
            'whatsapp_group' => 'nullable|in:Sent link,Task Completed,Not Responding,Task Complete',
            'telegram_group' => 'nullable|in:Sent link,task complete',
            'first_live' => 'nullable|in:Not Respond,1 subject attend,2 subject attend,3 subject attend,4 subject attend,5 subject attend,6 subject attend,Task complete',
            'first_exam' => 'nullable|in:not respond,1 subject attend,2 subject attend,3 subject attend,4 subject attend,5 subject attend,6 subject attend,task complete',
            'second_live' => 'nullable|in:Not Respond,1 subject attend,2 subject attend,3 subject attend,4 subject attend,5 subject attend,6 subject attend,Task complete',
            'second_exam' => 'nullable|in:not respond,1 subject attend,2 subject attend,3 subject attend,4 subject attend,5 subject attend,6 subject attend,task complete',
            'model_exam_live' => 'nullable|in:not respond,1 subject attend,2 subject attend,3 subject attend,4 subject attend,5 subject attend,6 subject attend,task complete',
            'model_exam' => 'nullable|in:not respond,1 subject attend,2 subject attend,3 subject attend,4 subject attend,5 subject attend,6 subject attend,task complete',
            'assignment' => 'nullable|in:not respond,1 subject attend,2 subject attend,3 subject attend,4 subject attend,5 subject attend,6 subject attend,task complete',
            'admit_card' => 'nullable|in:Did not,Task complete',
        ];

        $rules = array_merge($rules, $otherFields);

        return $rules[$field] ?? null;
    }

    /**
     * Format response value for display
     */
    private function formatResponseValue($field, $value)
    {
        if ($field === 'subject_id' && $value) {
            $subject = Subject::find($value);
            return $subject ? $subject->title : $value;
        }

        if ($field === 'admission_batch_id') {
            if ($value) {
                $admissionBatch = AdmissionBatch::find($value);
                return $admissionBatch ? $admissionBatch->title : $value;
            }
            return 'N/A';
        }
        
        return $value;
    }
}