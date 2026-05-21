<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConvertedLead;
use App\Models\ConvertedStudentMentorDetail;
use App\Models\Subject;
use App\Models\Batch;
use App\Models\AdmissionBatch;
use App\Models\SubCourse;
use App\Models\User;
use App\Helpers\AuthHelper;
use App\Helpers\RoleHelper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ESchoolEduthanzeelMentorController extends Controller
{
    /**
     * Display a listing of E-School converted leads for mentoring
     */
    public function eschoolIndex(Request $request)
    {
        return $this->getMentorIndex($request, 5, 'E-School');
    }

    /**
     * Display a listing of Eduthanzeel converted leads for mentoring
     */
    public function eduthanzeelIndex(Request $request)
    {
        return $this->getMentorIndex($request, 6, 'Eduthanzeel');
    }

    /**
     * Common method to get mentor index
     */
    private function getMentorIndex(Request $request, $courseId, $courseName)
    {
        // Check permissions
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor() && !RoleHelper::is_mentor() && !RoleHelper::is_telecaller() && !RoleHelper::is_team_lead() && !RoleHelper::is_senior_manager() && !RoleHelper::is_general_manager() && !RoleHelper::is_hod()) {
            return redirect()->route('dashboard')
                ->with('message_danger', 'Access denied.');
        }

        $query = ConvertedLead::with([
            
            'flag','lead',
            'leadDetail',
            'course',
            'academicAssistant',
            'createdBy',
            'cancelledBy',
            'studentDetails.teacher',
            'mentorDetails',
            'subject',
            'batch',
            'admissionBatch',
            'subCourse'
        ])->where('course_id', $courseId)
          ->where('is_academic_verified', 1); // Academic must be verified for all
        
        // For mentors, only show support verified leads
        // For admins and admission counsellors, show all leads where both support and academic are verified
        if (RoleHelper::is_mentor()) {
            $query->where('is_support_verified', 1);
        } elseif (RoleHelper::is_admin_or_super_admin() || RoleHelper::is_admission_counsellor()) {
            // Admins and admission counsellors see all leads where both support and academic are verified
            $query->where('is_support_verified', 1);
        }

        // Apply role-based filtering
        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            if (RoleHelper::is_admin_or_super_admin()) {
                // Admins can see all support verified leads
            } elseif (RoleHelper::is_hod()) {
                // HOD: Only see leads for courses where they are assigned as HOD
                $hodCourseIds = \App\Models\Course::where('hod_id', AuthHelper::getCurrentUserId())
                    ->pluck('id')
                    ->toArray();
                
                // Check if current course is in HOD's assigned courses
                if (!empty($hodCourseIds) && in_array($courseId, $hodCourseIds)) {
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
                    ->orWhere('register_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        if ($request->filled('admission_batch_id')) {
            $query->where('admission_batch_id', $request->admission_batch_id);
        }

        if ($request->filled('sub_course_id')) {
            $query->where('sub_course_id', $request->sub_course_id);
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
        $batches = Batch::where('course_id', $courseId)->orderBy('title')->get();
        $admission_batches = AdmissionBatch::whereHas('batch', function($q) use ($courseId) {
            $q->where('course_id', $courseId);
        })->orderBy('title')->get();
        $sub_courses = SubCourse::where('course_id', $courseId)->orderBy('title')->get();
        $teachers = User::where('role_id', 10)->where('is_active', 1)->orderBy('name')->get();
        $country_codes = \App\Helpers\CountriesHelper::get_country_code();

        $viewName = $courseId == 5 ? 'admin.converted-leads.eschool-mentor-index' : 'admin.converted-leads.eduthanzeel-mentor-index';
        $routeName = $courseId == 5 ? 'admin.mentor-eschool-converted-leads.index' : 'admin.mentor-eduthanzeel-converted-leads.index';

        $flags = \App\Support\MentorFlagFieldSupport::forFilterSelect();

        return view($viewName, compact(
            'convertedLeads', 
            'batches',
            'admission_batches',
            'sub_courses',
            'teachers', 
            'country_codes',
            'courseName',
            'routeName',
            'flags'
        ));
    }

    /**
     * Update mentor details inline
     */
    public function updateMentorDetails(Request $request, $id)
    {
        // Check permissions
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor() && !RoleHelper::is_mentor() && !RoleHelper::is_telecaller() && !RoleHelper::is_team_lead() && !RoleHelper::is_senior_manager() && !RoleHelper::is_general_manager()) {
            return response()->json([
                'success' => false,
                'error' => 'Access denied.'
            ], 403);
        }

        try {
            $convertedLead = ConvertedLead::findOrFail($id);
            $field = $request->field;
            $value = $request->value;

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
                return response()->json(\App\Support\MentorFlagFieldSupport::updateOnConvertedLead($convertedLead, $value));
            }

            // Special handling for tutor_id (displayed as Tutor, but uses teacher_id in DB)
            if ($field === 'tutor_id') {
                // teacher_id is stored in converted_student_details, not mentor_details
                $studentDetails = $convertedLead->studentDetails;
                if (!$studentDetails) {
                    $studentDetails = new \App\Models\ConvertedStudentDetail();
                    $studentDetails->converted_lead_id = $id;
                    $studentDetails->save();
                }
                $studentDetails->teacher_id = $value;
                $studentDetails->save();
                
                // Update tutor phone number in mentor details if teacher is selected
                $mentorDetails = $convertedLead->mentorDetails;
                if (!$mentorDetails) {
                    $mentorDetails = new ConvertedStudentMentorDetail();
                    $mentorDetails->converted_student_id = $id;
                }
                
                $teacher = null;
                if ($value) {
                    $teacher = User::find($value);
                    if ($teacher && $teacher->phone) {
                        $mentorDetails->tutor_phone_number = $teacher->phone;
                    } else {
                        $mentorDetails->tutor_phone_number = null;
                    }
                } else {
                    // Set tutor phone number to null when tutor is removed
                    $mentorDetails->tutor_phone_number = null;
                }
                
                $mentorDetails->save();

                // Always return teacher NAME in 'value' field, and formatted phone in 'tutor_phone' field
                $teacherName = ($teacher && $teacher->name) ? trim($teacher->name) : '-';
                $tutorPhoneDisplay = ($teacher && $teacher->phone) ? \App\Helpers\PhoneNumberHelper::display($teacher->code, $teacher->phone) : '-';
                
                return response()->json([
                    'success' => true,
                    'message' => 'Updated successfully',
                    'value' => $teacherName,  // This MUST be the teacher name, not phone
                    'tutor_phone' => $tutorPhoneDisplay  // This MUST be the formatted phone number
                ]);
            }


            // Handle all other fields - update in converted_student_mentor_details table
            $mentorDetails = $convertedLead->mentorDetails;
            if (!$mentorDetails) {
                $mentorDetails = new ConvertedStudentMentorDetail();
                $mentorDetails->converted_student_id = $id;
            }
            $mentorDetails->$field = $value;
            $mentorDetails->save();

            // Format the response value
            $responseValue = $this->formatResponseValue($field, $value, $mentorDetails);

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
            'call_1' => 'nullable|in:Call Not Answered,Switched Off,Line Busy,Student Asks to Call Later,Lack of Interest in Conversation,Wrong Contact,Inconsistent Responses,Task Complete',
            'app' => 'nullable|in:Provided app,OTP Problem,Task Completed,Not Respond',
            'whatsapp_group' => 'nullable|in:Sent link,Task Completed,Not Responding,Task Complete',
            'telegram_group' => 'nullable|in:Sent link,task complete',
            'screening_date' => 'nullable|date',
            'screening_officer' => 'nullable|string|max:255',
            'class_time' => 'nullable|date_format:H:i',
            'tutor_id' => 'nullable|exists:users,id', // Maps to teacher_id in converted_student_details
            'class_status' => 'nullable|in:Active,In Progress,Inactive,Dropped Out,Completed,Rejoining',
            'first_pa' => 'nullable|in:Pending,Not Written,Completed',
            'first_pa_mark' => 'nullable|string|max:50',
            'feedback_call_1' => 'nullable|string|max:1000',
            'first_pa_remarks' => 'nullable|string|max:1000',
            'second_pa' => 'nullable|in:Pending,Not Written,Completed',
            'second_pa_mark' => 'nullable|string|max:50',
            'feedback_call_2' => 'nullable|string|max:1000',
            'second_pa_remarks' => 'nullable|string|max:1000',
            'third_pa' => 'nullable|in:Pending,Not Written,Completed',
            'third_pa_mark' => 'nullable|string|max:50',
            'feedback_call_3' => 'nullable|string|max:1000',
            'third_pa_remarks' => 'nullable|string|max:1000',
            'certification_exam' => 'nullable|in:Pending,Not Written,Completed',
            'certification_exam_mark' => 'nullable|string|max:50',
            'course_completion_feedback' => 'nullable|in:yes,no',
            'certificate_collection' => 'nullable|in:Pending,Collected,Not Required',
            'continuing_studies' => 'nullable|in:yes,no',
            'reason' => 'nullable|string|max:1000',
            'remarks' => 'nullable|string|max:1000',
        ];

        return $rules[$field] ?? null;
    }

    /**
     * Format response value for display
     */
    private function formatResponseValue($field, $value, $mentorDetails)
    {
        if ($field === 'tutor_id' && $value) {
            $teacher = User::find($value);
            return $teacher ? $teacher->name : $value;
        }

        if ($field === 'screening_date' && $value) {
            return \Carbon\Carbon::parse($value)->format('d-m-Y');
        }

        if ($field === 'class_time' && $value) {
            return \Carbon\Carbon::parse($value)->format('h:i A');
        }

        return $value;
    }
}

