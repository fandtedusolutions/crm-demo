<?php

namespace App\Http\Controllers;

use App\Helpers\AuthHelper;
use App\Helpers\RoleHelper;
use App\Models\AdmissionBatch;
use App\Models\Batch;
use App\Models\ClassTime;
use App\Models\ConvertedLead;
use App\Models\ConvertedStudentMentorDetail;
use App\Support\MedicalCodingMentorTrackColumns;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MedicalCodingMentorController extends Controller
{
    private const COURSE_ID = 3;

    public function index(Request $request)
    {
        if (! RoleHelper::is_admin_or_super_admin()
            && ! RoleHelper::is_admission_counsellor()
            && ! RoleHelper::is_mentor()
            && ! RoleHelper::is_telecaller()
            && ! RoleHelper::is_team_lead()
            && ! RoleHelper::is_senior_manager()
            && ! RoleHelper::is_hod()
            && ! RoleHelper::is_academic_assistant()
            && ! RoleHelper::is_general_manager()
        ) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $query = ConvertedLead::with([
            'flag',
            'lead.team',
            'leadDetail.classTime',
            'course',
            'cancelledBy',
            'studentDetails',
            'mentorDetails',
            'batch',
            'admissionBatch',
        ])->where('course_id', self::COURSE_ID)
            ->where('is_support_verified', 1);

        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            if (RoleHelper::is_hod()) {
                $hodCourseIds = \App\Models\Course::where('hod_id', AuthHelper::getCurrentUserId())->pluck('id')->toArray();
                if (empty($hodCourseIds) || ! in_array(self::COURSE_ID, $hodCourseIds, true)) {
                    $query->whereRaw('1 = 0');
                }
            } elseif (RoleHelper::is_mentor()) {
                $mentorAdmissionBatchIds = AdmissionBatch::where('mentor_id', AuthHelper::getCurrentUserId())->pluck('id')->toArray();
                if (! empty($mentorAdmissionBatchIds)) {
                    $query->whereIn('admission_batch_id', $mentorAdmissionBatchIds);
                } else {
                    $query->whereRaw('1 = 0');
                }
            } elseif (RoleHelper::is_team_lead() || RoleHelper::is_senior_manager()) {
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
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $convertedLeads = $query->orderBy('created_at', 'desc')->paginate(50);
        $batches = Batch::where('course_id', self::COURSE_ID)->orderBy('title')->get();
        $course = \App\Models\Course::find(self::COURSE_ID);
        $classTimes = $course && $course->needs_time
            ? ClassTime::where('course_id', self::COURSE_ID)->where('is_active', true)->get()
            : collect();
        $country_codes = \App\Helpers\CountriesHelper::get_country_code();
        $flags = \App\Support\MentorFlagFieldSupport::forFilterSelect();
        $activeMentorRoute = 'admin.medical-coding-mentor-converted-leads.index';

        return view('admin.converted-leads.medical-coding-mentor-index', compact(
            'activeMentorRoute',
            'convertedLeads',
            'batches',
            'course',
            'classTimes',
            'country_codes',
            'flags'
        ));
    }

    public function updateMentorDetails(Request $request, $id)
    {
        try {
            $convertedLead = ConvertedLead::findOrFail($id);
            if ((int) $convertedLead->course_id !== self::COURSE_ID) {
                return response()->json(['success' => false, 'error' => 'Invalid course.'], 403);
            }

            $field = $request->field;
            $value = $request->value;

            if ($denied = \App\Support\MentorFlagFieldSupport::mentorLeadScopeDeniedJsonResponse($convertedLead)) {
                return $denied;
            }

            $rule = MedicalCodingMentorTrackColumns::validationRuleFor($field);
            if ($rule) {
                $validator = Validator::make([$field => $value], [$field => $rule]);
                if ($validator->fails()) {
                    return response()->json(['success' => false, 'error' => $validator->errors()->first($field)], 422);
                }
            }

            if ($field === 'flag_id') {
                return \App\Support\MentorFlagFieldSupport::flagUpdateJsonResponse($convertedLead, $value);
            }

            $mentorDetailFields = MedicalCodingMentorTrackColumns::mentorDetailFields();
            if (! in_array($field, $mentorDetailFields, true)) {
                return response()->json(['success' => false, 'error' => 'Unsupported field.'], 422);
            }

            $mentorDetails = $convertedLead->mentorDetails;
            if (! $mentorDetails) {
                $mentorDetails = new ConvertedStudentMentorDetail();
                $mentorDetails->converted_student_id = $id;
            }

            MedicalCodingMentorTrackColumns::setTrackValue(
                $mentorDetails,
                $field,
                ($value === '' ? null : $value)
            );
            $mentorDetails->save();

            $responseValue = $value;
            if (in_array($field, MedicalCodingMentorTrackColumns::dateFields(), true) && $value) {
                $responseValue = \Carbon\Carbon::parse($value)->format('d-m-Y');
            } elseif ($value === null || $value === '') {
                $responseValue = '-';
            }

            return response()->json([
                'success' => true,
                'message' => 'Updated successfully',
                'value' => $responseValue,
            ]);
        } catch (\Exception $e) {
            Log::error('Medical Coding mentor update error: '.$e->getMessage());

            return response()->json(['success' => false, 'error' => 'Update failed: '.$e->getMessage()], 500);
        }
    }
}
