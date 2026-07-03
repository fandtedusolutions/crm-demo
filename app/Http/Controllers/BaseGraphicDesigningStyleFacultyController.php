<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConvertedLead;
use App\Models\ConvertedStudentMentorDetail;
use App\Models\Batch;
use App\Models\AdmissionBatch;
use App\Models\ClassTime;
use App\Models\OfflinePlace;
use App\Helpers\AuthHelper;
use App\Helpers\RoleHelper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

abstract class BaseGraphicDesigningStyleFacultyController extends Controller
{
    abstract protected function courseId(): int;

    abstract protected function facultyViewName(): string;

    abstract protected function activeFacultyRoute(): string;

    public function index(Request $request)
    {
        $courseId = $this->courseId();

        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor() && !RoleHelper::is_faculty() && !RoleHelper::is_telecaller() && !RoleHelper::is_team_lead() && !RoleHelper::is_senior_manager() && !RoleHelper::is_general_manager() && !RoleHelper::is_hod()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $query = ConvertedLead::with([
            'flag', 'courseFlag', 'lead',
            'course',
            'academicAssistant',
            'createdBy',
            'cancelledBy',
            'studentDetails',
            'mentorDetails',
            'batch',
            'admissionBatch',
            'leadDetail',
        ])->where('course_id', $courseId)
          ->where('is_support_verified', 1);

        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            if (RoleHelper::is_admin_or_super_admin()) {
            } elseif (RoleHelper::is_hod()) {
                $hodCourseIds = \App\Models\Course::where('hod_id', AuthHelper::getCurrentUserId())
                    ->pluck('id')
                    ->toArray();

                if (empty($hodCourseIds) || !in_array($courseId, $hodCourseIds)) {
                    $query->whereRaw('1 = 0');
                }
            } elseif (RoleHelper::is_mentor_head()) {
            } elseif (RoleHelper::is_faculty()) {
                $mentorAdmissionBatchIds = AdmissionBatch::where('mentor_id', AuthHelper::getCurrentUserId())
                    ->pluck('id')
                    ->toArray();

                if (!empty($mentorAdmissionBatchIds)) {
                    $query->whereIn('admission_batch_id', $mentorAdmissionBatchIds);
                } else {
                    $query->whereRaw('1 = 0');
                }
            } elseif (RoleHelper::is_team_lead()) {
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                    $query->whereHas('lead', function ($q) use ($teamMemberIds) {
                        $q->whereIn('telecaller_id', $teamMemberIds);
                    });
                } else {
                    $query->whereHas('lead', function ($q) {
                        $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                    });
                }
            } elseif (RoleHelper::is_senior_manager()) {
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                    $query->whereHas('lead', function ($q) use ($teamMemberIds) {
                        $q->whereIn('telecaller_id', $teamMemberIds);
                    });
                } else {
                    $query->whereHas('lead', function ($q) {
                        $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                    });
                }
            } elseif (RoleHelper::is_general_manager()) {
            } elseif (RoleHelper::is_admission_counsellor()) {
            } elseif (RoleHelper::is_academic_assistant()) {
            } elseif (RoleHelper::is_telecaller()) {
                $query->whereHas('lead', function ($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
            }
        }

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

        \App\Support\MentorFlagFieldSupport::applyListingFilter($query, $request);
        \App\Support\CourseFlagFieldSupport::applyListingFilter($query, $request);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $convertedLeads = $query->orderBy('created_at', 'desc')->paginate(50);

        $batches = Batch::where('course_id', $courseId)->orderBy('title')->get();
        $country_codes = \App\Helpers\CountriesHelper::get_country_code();
        $course = \App\Models\Course::find($courseId);
        $classTimes = collect();
        if ($course && $course->needs_time) {
            $classTimes = ClassTime::where('course_id', $courseId)->where('is_active', true)->get();
        }
        $offlinePlaces = \App\Support\CourseOfflinePlaceSupport::placesFor($course);
        $flags = \App\Support\MentorFlagFieldSupport::forFilterSelect();
        $activeFacultyRoute = $this->activeFacultyRoute();

        return view($this->facultyViewName(), compact(
            'activeFacultyRoute',
            'convertedLeads',
            'batches',
            'country_codes',
            'course',
            'classTimes',
            'offlinePlaces',
            'flags'
        ));
    }

    public function updateMentorDetails(Request $request, $id)
    {
        try {
            $restrictedFields = [
                'phone', 'batch_id', 'admission_batch_id', 'internship_id', 'email', 'call_status',
                'orientation_class_date', 'class_start_date', 'class_end_date', 'whatsapp_group_status',
                'class_time_id', 'programme_type', 'location', 'total_class', 'total_present', 'total_absent',
                'final_certificate_examination_date', 'certificate_examination_marks', 'final_interview_date',
                'interview_marks', 'certificate_distribution_date', 'experience_certificate_distribution_date',
                'completed_cancelled_date', 'remarks',
            ];

            $isRestricted = in_array($request->field, $restrictedFields, true);
            $canEditRestricted = RoleHelper::is_admin_or_super_admin() || RoleHelper::is_hod() || RoleHelper::is_admission_counsellor();
            if ($isRestricted && !$canEditRestricted) {
                return response()->json(['success' => false, 'error' => 'Access denied'], 403);
            }

            $convertedLead = ConvertedLead::findOrFail($id);
            $field = $request->field;
            $value = $request->value;

            if ($denied = \App\Support\MentorFlagFieldSupport::mentorLeadScopeDeniedJsonResponse($convertedLead)) {
                return $denied;
            }

            $validationRules = $this->getValidationRules($field);
            if ($validationRules) {
                $validator = Validator::make([$field => $value], [$field => $validationRules]);
                if ($validator->fails()) {
                    return response()->json(['success' => false, 'error' => $validator->errors()->first($field)], 422);
                }
            }

            if ($field === 'flag_id') {
                return \App\Support\MentorFlagFieldSupport::flagUpdateJsonResponse($convertedLead, $value);
            }

            if ($field === 'course_flag_id') {
                return \App\Support\CourseFlagFieldSupport::courseFlagUpdateJsonResponse($convertedLead, $value);
            }

            $convertedLeadFields = ['phone', 'register_number', 'email', 'batch_id', 'admission_batch_id'];
            $studentDetailFields = ['internship_id', 'call_status'];
            $leadDetailFields = ['class_time_id', 'programme_type', 'location'];

            if (in_array($field, $convertedLeadFields)) {
                if ($field === 'phone') {
                    $convertedLead->phone = $value;
                    if ($request->has('code')) {
                        $convertedLead->code = $request->code;
                    }
                } else {
                    $convertedLead->$field = $value;
                }
                $convertedLead->save();
                $responseValue = $this->formatResponseValue($field, $value, $convertedLead);
            } elseif (in_array($field, $studentDetailFields)) {
                $studentDetails = $convertedLead->studentDetails;
                if (!$studentDetails) {
                    $studentDetails = new \App\Models\ConvertedStudentDetail();
                    $studentDetails->converted_student_id = $id;
                }
                $studentDetails->$field = $value;
                $studentDetails->save();
                $responseValue = $value;
            } elseif (in_array($field, $leadDetailFields)) {
                $leadDetail = $convertedLead->leadDetail;
                if (!$leadDetail) {
                    $leadDetail = new \App\Models\LeadDetail();
                    $leadDetail->lead_id = $convertedLead->lead_id;
                    $leadDetail->course_id = $convertedLead->course_id;
                }
                $leadDetail->$field = $value;
                $leadDetail->save();
                $responseValue = $this->formatResponseValue($field, $value, $convertedLead);
            } else {
                $mentorDetails = $convertedLead->mentorDetails;
                if (!$mentorDetails) {
                    $mentorDetails = new ConvertedStudentMentorDetail();
                    $mentorDetails->converted_student_id = $id;
                }
                $mentorDetails->$field = $value;
                $mentorDetails->save();
                $responseValue = $value;
            }

            return response()->json(['success' => true, 'message' => 'Updated successfully', 'value' => $responseValue]);
        } catch (\Exception $e) {
            Log::error('Error updating faculty details (course ' . $this->courseId() . '): ' . $e->getMessage());

            return response()->json(['success' => false, 'error' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }

    private function getValidationRules($field)
    {
        $rules = [
            'whatsapp_group_status' => 'nullable|in:sent link,task complete',
            'ai_workshop_attendance' => 'nullable|in:Attended,Not Attended',
            'graphic_design_session_attendance' => 'nullable|in:Attended,Not Attended',
            'copy_writing_session_attendance' => 'nullable|in:Attended,Not Attended',
            'completed_cancelled_date' => 'nullable|date',
            'first_term_internship_exam_date' => 'nullable|date',
            'final_internship_certification_exam_date' => 'nullable|date',
            'total_class' => 'nullable|integer|min:0',
            'total_present' => 'nullable|integer|min:0',
            'total_absent' => 'nullable|integer|min:0',
        ];

        return $rules[$field] ?? null;
    }

    private function formatResponseValue($field, $value, $convertedLead = null)
    {
        if ($field === 'batch_id' && $value) {
            $batch = Batch::find($value);

            return $batch ? $batch->title : $value;
        }

        if ($field === 'admission_batch_id' && $value) {
            $admissionBatch = AdmissionBatch::find($value);

            return $admissionBatch ? $admissionBatch->title : $value;
        }

        if ($field === 'class_time_id' && $value) {
            $classTime = ClassTime::find($value);
            if ($classTime) {
                $fromTime = \Carbon\Carbon::parse($classTime->from_time)->format('h:i A');
                $toTime = \Carbon\Carbon::parse($classTime->to_time)->format('h:i A');

                return $fromTime . ' - ' . $toTime;
            }

            return $value;
        }

        if ($field === 'phone' && $convertedLead) {
            return \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone);
        }

        return $value;
    }
}
