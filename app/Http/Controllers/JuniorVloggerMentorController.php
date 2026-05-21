<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConvertedLead;
use App\Models\ConvertedStudentMentorDetail;
use App\Models\LeadDetail;
use App\Models\Batch;
use App\Models\AdmissionBatch;
use App\Models\ClassTime;
use App\Helpers\AuthHelper;
use App\Helpers\RoleHelper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class JuniorVloggerMentorController extends Controller
{
    private const COURSE_ID = 25;

    /**
     * Display Junior Vlogger Converted Mentor List (course_id = 25)
     */
    public function index(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor() && !RoleHelper::is_mentor() && !RoleHelper::is_telecaller() && !RoleHelper::is_team_lead() && !RoleHelper::is_senior_manager() && !RoleHelper::is_hod() && !RoleHelper::is_academic_assistant()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $query = ConvertedLead::with([
            
            'flag','lead',
            'lead.team',
            'lead.team.detail',
            'lead.juniorVloggerStudentDetails.classTime',
            'course',
            'academicAssistant',
            'createdBy',
            'cancelledBy',
            'studentDetails',
            'mentorDetails',
            'batch',
            'admissionBatch',
        ])->where('course_id', self::COURSE_ID);

        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            if (RoleHelper::is_admin_or_super_admin() || RoleHelper::is_admission_counsellor() || RoleHelper::is_academic_assistant()) {
                // see all
            } elseif (RoleHelper::is_hod()) {
                $hodCourseIds = \App\Models\Course::where('hod_id', AuthHelper::getCurrentUserId())->pluck('id')->toArray();
                if (!empty($hodCourseIds) && in_array(self::COURSE_ID, $hodCourseIds)) {
                    // ok
                } else {
                    $query->whereRaw('1 = 0');
                }
            } elseif (RoleHelper::is_mentor()) {
                $mentorAdmissionBatchIds = AdmissionBatch::where('mentor_id', AuthHelper::getCurrentUserId())->pluck('id')->toArray();
                if (!empty($mentorAdmissionBatchIds)) {
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
        if ($request->filled('is_b2b')) {
            if ($request->is_b2b === 'b2b') {
                $query->where('is_b2b', 1);
            } elseif ($request->is_b2b === 'in_house') {
                $query->where(function ($q) {
                    $q->whereNull('is_b2b')->orWhere('is_b2b', 0);
                });
            }
        }

        $convertedLeads = $query->orderBy('created_at', 'desc')->paginate(50);

        $batches = Batch::where('course_id', self::COURSE_ID)->orderBy('title')->get();
        $course = \App\Models\Course::find(self::COURSE_ID);
        $classTimes = $course && $course->needs_time
            ? ClassTime::where('course_id', self::COURSE_ID)->where('is_active', true)->get()
            : collect();
        $country_codes = \App\Helpers\CountriesHelper::get_country_code();
        $flags = \App\Support\MentorFlagFieldSupport::forFilterSelect();

        return view('admin.converted-leads.junior-vlogger-mentor-index', compact(
            'convertedLeads',
            'batches',
            'course',
            'classTimes',
            'country_codes',
            'flags'
        ));
    }

    /**
     * Update mentor/details inline for Junior Vlogger
     */
    public function updateMentorDetails(Request $request, $id)
    {
        try {
            $convertedLead = ConvertedLead::findOrFail($id);
            if ((int) $convertedLead->course_id !== self::COURSE_ID) {
                return response()->json(['success' => false, 'error' => 'Invalid course.'], 403);
            }

            $field = $request->field;
            $value = $request->value;

            $validationRules = $this->getValidationRules($field);
            if ($validationRules) {
                $validator = Validator::make([$field => $value], [$field => $validationRules]);
                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'error' => $validator->errors()->first($field),
                    ], 422);
                }
            }

            if ($field === 'flag_id') {
                return response()->json(\App\Support\MentorFlagFieldSupport::updateOnConvertedLead($convertedLead, $value));
            }

            $convertedLeadFields = ['register_number', 'name', 'phone', 'email', 'batch_id', 'admission_batch_id', 'dob'];
            $leadDetailFields = ['class_time_id', 'medium_of_study', 'previous_qualification', 'technology_performance_category', 'whatsapp_number', 'whatsapp_code'];

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
            } elseif (in_array($field, $leadDetailFields)) {
                $leadDetail = LeadDetail::where('lead_id', $convertedLead->lead_id)->where('course_id', self::COURSE_ID)->first();
                if (!$leadDetail) {
                    $leadDetail = new LeadDetail();
                    $leadDetail->lead_id = $convertedLead->lead_id;
                    $leadDetail->course_id = self::COURSE_ID;
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
                if (in_array($field, $this->dateFieldsForDisplay())) {
                    $responseValue = $value ? \Carbon\Carbon::parse($value)->format('d-m-Y') : $value;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Updated successfully',
                'value' => $responseValue,
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating Junior Vlogger mentor details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Update failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function getValidationRules($field)
    {
        $feeOptions = 'Paid,Pending,Partially Paid,Overdue,On Hold,Cancelled';
        $rules = [
            'first_term_fee_status' => 'nullable|in:' . $feeOptions,
            'second_term_fee_status' => 'nullable|in:' . $feeOptions,
            'third_term_fee_status' => 'nullable|in:' . $feeOptions,
            'total_class_days' => 'nullable|integer|min:0',
            'first_term_number_of_days' => 'nullable|integer|min:0',
            'second_term_number_of_days' => 'nullable|integer|min:0',
            'third_term_number_of_days' => 'nullable|integer|min:0',
            'first_term_start_date' => 'nullable|date',
            'first_term_task_1_date' => 'nullable|date',
            'first_term_task_2_date' => 'nullable|date',
            'first_term_completion_date' => 'nullable|date',
            'second_term_start_date' => 'nullable|date',
            'second_term_task_1_date' => 'nullable|date',
            'second_term_task_2_date' => 'nullable|date',
            'second_term_completion_date' => 'nullable|date',
            'third_term_start_date' => 'nullable|date',
            'third_term_project_1_date' => 'nullable|date',
            'third_term_project_2_date' => 'nullable|date',
            'third_term_project_3_date' => 'nullable|date',
            'third_term_completion_date' => 'nullable|date',
            'certificate_issued_date' => 'nullable|date',
            'dob' => 'nullable|date|before_or_equal:today',
        ];
        return $rules[$field] ?? null;
    }

    private function dateFieldsForDisplay()
    {
        return [
            'first_term_start_date', 'first_term_task_1_date', 'first_term_task_2_date', 'first_term_completion_date',
            'second_term_start_date', 'second_term_task_1_date', 'second_term_task_2_date', 'second_term_completion_date',
            'third_term_start_date', 'third_term_project_1_date', 'third_term_project_2_date', 'third_term_project_3_date', 'third_term_completion_date',
            'certificate_issued_date', 'dob',
        ];
    }

    private function formatResponseValue($field, $value, ConvertedLead $convertedLead)
    {
        if ($field === 'batch_id' && $value) {
            $batch = Batch::find($value);
            return $batch ? $batch->title : $value;
        }
        if ($field === 'admission_batch_id' && $value) {
            $ab = AdmissionBatch::find($value);
            return $ab ? $ab->title : $value;
        }
        if ($field === 'class_time_id' && $value) {
            $ct = ClassTime::find($value);
            if ($ct) {
                return \Carbon\Carbon::parse($ct->from_time)->format('h:i A') . ' - ' . \Carbon\Carbon::parse($ct->to_time)->format('h:i A');
            }
            return $value;
        }
        if ($field === 'phone' && $convertedLead) {
            return \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone);
        }
        if ($field === 'dob' && $value) {
            return \Carbon\Carbon::parse($value)->format('d-m-Y');
        }
        return $value;
    }
}
