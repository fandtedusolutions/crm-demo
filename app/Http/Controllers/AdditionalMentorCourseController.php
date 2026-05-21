<?php

namespace App\Http\Controllers;

use App\Helpers\AuthHelper;
use App\Helpers\RoleHelper;
use App\Models\AdmissionBatch;
use App\Models\Batch;
use App\Models\ConvertedLead;
use Illuminate\Http\Request;

class AdditionalMentorCourseController extends Controller
{
    public function medicalCodingIndex(Request $request)
    {
        return $this->renderMentorList($request, 3, 'Medical Coding', 'admin.medical-coding-mentor-converted-leads.index');
    }

    public function pythonIndex(Request $request)
    {
        return $this->renderMentorList($request, 10, 'Python', 'admin.python-mentor-converted-leads.index');
    }

    public function flutterIndex(Request $request)
    {
        return $this->renderMentorList($request, 21, 'Flutter', 'admin.flutter-mentor-converted-leads.index');
    }

    public function rpaIndex(Request $request)
    {
        return $this->renderMentorList($request, 27, 'RPA', 'admin.rpa-mentor-converted-leads.index');
    }

    private function renderMentorList(Request $request, int $courseId, string $courseTitle, string $routeName)
    {
        if (!RoleHelper::is_admin_or_super_admin()
            && !RoleHelper::is_admission_counsellor()
            && !RoleHelper::is_academic_assistant()
            && !RoleHelper::is_mentor()
            && !RoleHelper::is_telecaller()
            && !RoleHelper::is_team_lead()
            && !RoleHelper::is_senior_manager()
            && !RoleHelper::is_general_manager()
            && !RoleHelper::is_hod()
        ) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $query = ConvertedLead::with([
            'lead',
            'leadDetail',
            'course',
            'flag',
            'mentorDetails',
        ])->where('course_id', $courseId)
            ->where('is_support_verified', 1);

        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            if (RoleHelper::is_hod()) {
                $hodCourseIds = \App\Models\Course::where('hod_id', AuthHelper::getCurrentUserId())
                    ->pluck('id')
                    ->toArray();

                if (!empty($hodCourseIds) && in_array($courseId, $hodCourseIds, true)) {
                    // Allow data for assigned HOD course.
                } else {
                    $query->whereRaw('1 = 0');
                }
            } elseif (RoleHelper::is_mentor()) {
                $mentorAdmissionBatchIds = AdmissionBatch::where('mentor_id', AuthHelper::getCurrentUserId())
                    ->pluck('id')
                    ->toArray();

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
                    ->orWhere('register_number', 'like', "%{$search}%");
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
        $batches = Batch::where('course_id', $courseId)->orderBy('title')->get();
        $flags = \App\Support\MentorFlagFieldSupport::forFilterSelect();

        return view('admin.converted-leads.additional-mentor-course-index', compact(
            'convertedLeads',
            'courseTitle',
            'routeName',
            'courseId',
            'batches',
            'flags'
        ));
    }
}

