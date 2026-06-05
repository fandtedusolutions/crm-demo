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

class MentorConvertedLeadController extends Controller
{
    /**
     * Display a listing of BOSSE converted leads for mentoring
     */
    public function index(Request $request)
    {
        $query = ConvertedLead::with([
            'lead',
            'leadDetail',
            'course',
            'academicAssistant',
            'createdBy',
            'cancelledBy',
            'studentDetails',
            'mentorDetails',
            'subject',
            'flag',
            'batch',
            'admissionBatch'
        ])->where('course_id', 2) // BOSSE course
          ->where('is_support_verified', 1);

        // Apply role-based filtering
        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            if (RoleHelper::is_admin_or_super_admin()) {
                // Admins and super admins can see all support verified leads
                // No additional filtering needed
            } elseif (RoleHelper::is_hod()) {
                // HOD: Only see leads for courses where they are assigned as HOD
                $hodCourseIds = \App\Models\Course::where('hod_id', AuthHelper::getCurrentUserId())
                    ->pluck('id')
                    ->toArray();
                
                // Check if current course (2) is in HOD's assigned courses
                if (!empty($hodCourseIds) && in_array(2, $hodCourseIds)) {
                    // HOD is assigned to this course, show data
                } else {
                    // HOD is not assigned to this course, return empty results
                    $query->whereRaw('1 = 0');
                }
            } elseif (RoleHelper::is_mentor_head()) {
                // Mentor Head: Can see all support verified leads
                // No additional filtering needed
            } elseif (RoleHelper::is_mentor()) {
                // Regular Mentor: Filter by admission_batch_id where mentor_id matches
                $mentorAdmissionBatchIds = AdmissionBatch::where('mentor_id', AuthHelper::getCurrentUserId())
                    ->pluck('id')
                    ->toArray();
                
                if (!empty($mentorAdmissionBatchIds)) {
                    $query->whereIn('admission_batch_id', $mentorAdmissionBatchIds);
                } else {
                    // If mentor has no admission batches, return empty result
                    $query->whereRaw('1 = 0');
                }
            } elseif (RoleHelper::is_team_lead()) {
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                    $query->whereHas('lead', function($q) use ($teamMemberIds) {
                        $q->whereIn('telecaller_id', $teamMemberIds);
                    });
                } else {
                    $query->whereHas('lead', function($q) {
                        $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                    });
                }
            } elseif (RoleHelper::is_senior_manager()) {
                // Senior Manager: Filter by their own leads or team leads if they have a team
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                    $query->whereHas('lead', function($q) use ($teamMemberIds) {
                        $q->whereIn('telecaller_id', $teamMemberIds);
                    });
                } else {
                    $query->whereHas('lead', function($q) {
                        $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                    });
                }
            } elseif (RoleHelper::is_general_manager()) {
                // General Manager: Can see all leads
                // No additional filtering needed
            } elseif (RoleHelper::is_admission_counsellor()) {
                // Can see all
            } elseif (RoleHelper::is_academic_assistant()) {
                // Can see all
            } elseif (RoleHelper::is_telecaller()) {
                $query->whereHas('lead', function($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
            }
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('register_number', 'like', "%{$search}%")
                    ->orWhereHas('mentorDetails', function($q) use ($search) {
                        $q->where('application_number', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        if ($request->filled('admission_batch_id')) {
            $query->where('admission_batch_id', $request->admission_batch_id);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
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

        $convertedLeads = $query->orderBy('created_at', 'desc')->paginate(50);

        // Get filter data
        $batches = Batch::where('course_id', 2)->orderBy('title')->get();
        $subjects = Subject::where('course_id', 2)->orderBy('title')->get();
        $country_codes = \App\Helpers\CountriesHelper::get_country_code();
        $flags = \App\Support\MentorFlagFieldSupport::forFilterSelect();

        return view('admin.converted-leads.mentor-bosse-index', compact(
            'convertedLeads', 
            'batches', 
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

            if ($denied = \App\Support\MentorFlagFieldSupport::mentorFieldDeniedJsonResponse($field, ['subject_id', 'registration_status', 'status'])) {
                return $denied;
            }

            if ($denied = \App\Support\MentorFlagFieldSupport::mentorLeadScopeDeniedJsonResponse($convertedLead)) {
                return $denied;
            }

            // Allow only known fields to prevent SQL errors / mass assignment of unknown columns.
            // Note: "status" belongs to converted_leads, not converted_student_mentor_details.
            $allowedMentorFields = [
                'subject_id',
                'registration_status',
                'technology_side',
                'student_status',
                'problems',
                'telegram_group',
                'app',
                'whatsapp_group',
                'call_1',
                'call_time',
                'call_2',
                'call_3',
                'call_4',
                'call_5',
                'call_6',
                'call_7',
                'call_8',
                'call_9',
                'mentor_live_1',
                'mentor_live_2',
                'mentor_live_3',
                'mentor_live_4',
                'mentor_live_5',
                'first_live',
                'first_exam_registration',
                'first_exam',
                'second_live',
                'second_exam',
                'model_exam_live',
                'model_exam',
                'practical',
                'self_registration',
                'assignment',
                'mock_test',
                'admit_card',
                'exam_subject_1',
                'exam_subject_2',
                'exam_subject_3',
                'exam_subject_4',
                'exam_subject_5',
                'exam_subject_6',
            ];

            $allowedTopLevelFields = ['status', 'flag_id'];

            if (!in_array($field, $allowedMentorFields, true) && !in_array($field, $allowedTopLevelFields, true)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid field.'
                ], 422);
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

            // "status" is a ConvertedLead field (converted_leads table), not mentor_details.
            if ($field === 'status') {
                $convertedLead->status = $value ?: null;
                $convertedLead->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Updated successfully',
                    'value' => $convertedLead->status ?: 'N/A'
                ]);
            }

            // Handle all fields - update in converted_student_mentor_details table
            $mentorDetails = $convertedLead->mentorDetails;
            if (!$mentorDetails) {
                $mentorDetails = new ConvertedStudentMentorDetail();
                $mentorDetails->converted_student_id = $id;
            }
            $mentorDetails->$field = $value;
            $mentorDetails->save();

            // Format the response value
            $responseValue = $this->formatResponseValue($field, $value);

            return response()->json([
                'success' => true,
                'message' => 'Updated successfully',
                'value' => $responseValue
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating mentor details: ' . $e->getMessage());
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
            'subject_id' => 'nullable|exists:subjects,id',
            'flag_id' => \App\Support\MentorFlagFieldSupport::validationRule(),
            'registration_status' => 'nullable|in:Paid,Not Paid',
            'technology_side' => 'nullable|in:No Knowledge,Limited Knowledge,Moderate Knowledge,High Knowledge',
            'student_status' => 'nullable|in:Low Level,Below Medium,Medium Level,Advanced Level',
            'problems' => 'nullable|string|max:1000',
            'telegram_group' => 'nullable|in:Sent link,task complete',
            'status' => 'nullable|in:Paid,Received,Admission cancel,Active,Inactive',
            'call_time' => 'nullable|date_format:H:i',
        ];

        // Add call status rules
        $callFields = ['call_1', 'call_2', 'call_3', 'call_4', 'call_5', 'call_6', 'call_7', 'call_8', 'call_9'];
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

        if ($field === 'call_time' && $value) {
            try {
                return \Carbon\Carbon::createFromFormat('H:i', $value)->format('h:i A');
            } catch (\Throwable $e) {
                return $value;
            }
        }

        return $value;
    }
}
