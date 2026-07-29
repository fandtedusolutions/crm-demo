<?php

namespace App\Http\Controllers;

use App\Helpers\AuthHelper;
use App\Helpers\RoleHelper;
use App\Models\Batch;
use App\Models\ConvertedLead;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;

class AdditionalConvertedCourseController extends Controller
{
    public function medicalCodingIndex(Request $request)
    {
        return $this->renderConvertedList(
            $request,
            3,
            'Certificate Course in Medical Coding',
            'admin.medical-coding-converted-leads.index',
            'medical-coding'
        );
    }

    public function hospitalAdministrationIndex(Request $request)
    {
        return $this->renderConvertedList(
            $request,
            4,
            'Diploma in Hospital Administration',
            'admin.hospital-administration-converted-leads.index',
            'hospital-administration'
        );
    }

    private function renderConvertedList(
        Request $request,
        int $courseId,
        string $courseTitle,
        string $routeName,
        string $exportPage
    ) {
        if (! RoleHelper::is_admin_or_super_admin()
            && ! RoleHelper::is_admission_counsellor()
            && ! RoleHelper::is_academic_assistant()
            && ! RoleHelper::is_telecaller()
            && ! RoleHelper::is_team_lead()
            && ! RoleHelper::is_senior_manager()
            && ! RoleHelper::is_general_manager()
            && ! RoleHelper::is_hod()
            && ! RoleHelper::is_finance()
        ) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $query = ConvertedLead::with([
            'lead',
            'lead.telecaller',
            'leadDetail',
            'course',
            'cancelledBy',
            'studentDetails',
            'batch',
            'admissionBatch',
            'courseFlag',
        ])->where('course_id', $courseId);

        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            if (RoleHelper::is_hod()) {
                $hodCourseIds = Course::where('hod_id', AuthHelper::getCurrentUserId())
                    ->pluck('id')
                    ->toArray();

                if (empty($hodCourseIds) || ! in_array($courseId, $hodCourseIds, true)) {
                    $query->whereRaw('1 = 0');
                }
            } elseif (RoleHelper::is_team_lead() || RoleHelper::is_senior_manager()) {
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = User::where('team_id', $teamId)->pluck('id')->toArray();
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

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        \App\Support\CourseFlagFieldSupport::applyListingFilter($query, $request);

        $convertedLeads = $query->orderBy('created_at', 'desc')->paginate(50);
        $batches = Batch::where('course_id', $courseId)->orderBy('title')->get();

        return view('admin.converted-leads.additional-converted-course-index', compact(
            'convertedLeads',
            'courseTitle',
            'routeName',
            'courseId',
            'batches',
            'exportPage'
        ));
    }
}
