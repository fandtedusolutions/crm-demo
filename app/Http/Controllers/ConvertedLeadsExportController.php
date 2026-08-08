<?php

namespace App\Http\Controllers;

use App\Exports\ConvertedLeadsExport;
use App\Helpers\AuthHelper;
use App\Helpers\RoleHelper;
use App\Models\AdmissionBatch;
use App\Models\ConvertedLead;
use App\Models\Course;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ConvertedLeadsExportController extends Controller
{
    protected array $courseMap = [
        'nios' => 1,
        'bosse' => 2,
        'medical-coding' => 3,
        'hospital-administration' => 4,
        'eschool' => 5,
        'eduthanzeel' => 6,
        'hotel-management' => 8,
        'ugpg' => 9,
        'ai-python' => 10,
        'python' => 10,
        'digital-marketing' => 11,
        'diploma-in-data-science' => 12,
        'data-science' => 12,
        'web-development' => 13,
        'vibe-coding' => 14,
        'graphic-designing' => 15,
        'gmvss' => 16,
        'machine-learning' => 20,
        'flutter' => 21,
        'edumaster' => 23,
        'junior-vlogger' => 25,
        'rpa' => 27,
        'ai-integrated-sales-marketing' => 29,
        'ai-integrated-video-editing' => 30,
        'ai-integrated-videography' => 31,
        'ai-integrated-photography' => 32,
        'robo-vibe' => 33,
        'prompt-engineering' => 34,
    ];

    public function export(Request $request)
    {
        set_time_limit(config('timeout.max_execution_time', 300));

        $page = (string) $request->input('page', 'converted-leads');
        $format = (string) $request->input('format', 'excel');
        if (! in_array($format, ['excel', 'pdf'], true)) {
            $format = 'excel';
        }

        $query = ConvertedLead::with([
            'lead',
            'lead.team',
            'lead.telecaller',
            'lead.createdBy',
            'course',
            'academicAssistant',
            'createdBy',
            'cancelledBy',
            'subject',
            'studentDetails',
            'mentorDetails',
            'leadDetail',
            'invoices.payments',
            'batch',
            'admissionBatch',
        ]);

        $pageType = $this->getPageType($page);
        $basePage = $this->getBasePage($page);
        $courseId = $this->courseMap[$basePage] ?? null;

        if ($courseId !== null) {
            $query->where('course_id', $courseId);
        }

        if ($pageType === 'mentor') {
            $query->where('is_support_verified', 1);
            $this->applyMentorRoleScope($query);
        } elseif ($pageType === 'faculty') {
            $query->where('is_support_verified', 1);
            $this->applyFacultyRoleScope($query);
        } elseif ($pageType === 'support') {
            $query->where('is_academic_verified', 1);
            $this->applySupportRoleScope($query);
        } elseif ($pageType === 'post-sales') {
            $this->applyPostSalesRoleScope($query);
        } else {
            $this->applyAdminRoleScope($query);
        }

        $this->applyFilters($query, $request, $pageType);

        $convertedLeads = $query->orderBy('created_at', 'desc')->get();

        $dateFrom = $request->filled('date_from') ? $request->date_from : 'all';
        $dateTo = $request->filled('date_to') ? $request->date_to : 'all';
        $filePrefix = str_replace('-', '_', $page) . '_export';
        $dateSuffix = $dateFrom . '_to_' . $dateTo . '_' . date('Y-m-d_His');

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('admin.converted-leads.exports.list-pdf', [
                'convertedLeads' => $convertedLeads,
                'pageTitle' => $this->getPageTitle($page),
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'generatedAt' => now()->format('Y-m-d H:i:s'),
            ]);
            $pdf->setPaper('A4', 'landscape');

            return $pdf->download($filePrefix . '_' . $dateSuffix . '.pdf');
        }

        return Excel::download(new ConvertedLeadsExport($convertedLeads), $filePrefix . '_' . $dateSuffix . '.xlsx');
    }

    protected function getPageType(string $page): string
    {
        if (str_starts_with($page, 'mentor-')) {
            return 'mentor';
        }
        if (str_starts_with($page, 'faculty-')) {
            return 'faculty';
        }
        if (str_starts_with($page, 'support-')) {
            return 'support';
        }
        if ($page === 'post-sales') {
            return 'post-sales';
        }

        return 'admin';
    }

    protected function getBasePage(string $page): string
    {
        foreach (['mentor-', 'faculty-', 'support-'] as $prefix) {
            if (str_starts_with($page, $prefix)) {
                return substr($page, strlen($prefix));
            }
        }

        return $page;
    }

    protected function applyAdminRoleScope(Builder $query): void
    {
        $currentUser = AuthHelper::getCurrentUser();
        if (! $currentUser) {
            return;
        }

        if (RoleHelper::is_general_manager() || RoleHelper::is_senior_manager()) {
            return;
        }

        if (RoleHelper::is_admission_counsellor() || RoleHelper::is_academic_assistant()) {
            return;
        }

        if (RoleHelper::is_hod()) {
            $hodCourseIds = Course::where('hod_id', AuthHelper::getCurrentUserId())->pluck('id')->toArray();
            if (! empty($hodCourseIds)) {
                $query->whereIn('course_id', $hodCourseIds);
            } else {
                $query->whereRaw('1 = 0');
            }

            return;
        }

        if (RoleHelper::is_team_lead()) {
            $this->applyTeamLeadOrSelfScope($query, $currentUser->team_id);

            return;
        }

        if (RoleHelper::is_telecaller()) {
            $query->whereHas('lead', function ($q) {
                $q->where('telecaller_id', AuthHelper::getCurrentUserId());
            });

            return;
        }

        if (RoleHelper::is_support_team()) {
            $query->where('is_academic_verified', 1);
        }
    }

    protected function applyMentorRoleScope(Builder $query): void
    {
        $currentUser = AuthHelper::getCurrentUser();
        if (! $currentUser) {
            return;
        }

        if (RoleHelper::is_admin_or_super_admin() || RoleHelper::is_mentor_head()) {
            return;
        }

        if (RoleHelper::is_hod()) {
            $hodCourseIds = Course::where('hod_id', AuthHelper::getCurrentUserId())->pluck('id')->toArray();
            if (! empty($hodCourseIds)) {
                $query->whereIn('course_id', $hodCourseIds);
            } else {
                $query->whereRaw('1 = 0');
            }

            return;
        }

        if (RoleHelper::is_mentor()) {
            $mentorBatchIds = AdmissionBatch::where('mentor_id', AuthHelper::getCurrentUserId())->pluck('id')->toArray();
            if (! empty($mentorBatchIds)) {
                $query->whereIn('admission_batch_id', $mentorBatchIds);
            } else {
                $query->whereRaw('1 = 0');
            }

            return;
        }

        if (RoleHelper::is_team_lead()) {
            $this->applyTeamLeadOrSelfScope($query, $currentUser->team_id);

            return;
        }

        if (RoleHelper::is_telecaller()) {
            $query->whereHas('lead', function ($q) {
                $q->where('telecaller_id', AuthHelper::getCurrentUserId());
            });
        }
    }

    protected function applyFacultyRoleScope(Builder $query): void
    {
        $currentUser = AuthHelper::getCurrentUser();
        if (! $currentUser) {
            return;
        }

        if (RoleHelper::is_admin_or_super_admin() || RoleHelper::is_mentor_head()) {
            return;
        }

        if (RoleHelper::is_hod()) {
            $hodCourseIds = Course::where('hod_id', AuthHelper::getCurrentUserId())->pluck('id')->toArray();
            if (! empty($hodCourseIds)) {
                $query->whereIn('course_id', $hodCourseIds);
            } else {
                $query->whereRaw('1 = 0');
            }

            return;
        }

        if (RoleHelper::is_faculty()) {
            $facultyBatchIds = AdmissionBatch::where('mentor_id', AuthHelper::getCurrentUserId())->pluck('id')->toArray();
            if (! empty($facultyBatchIds)) {
                $query->whereIn('admission_batch_id', $facultyBatchIds);
            } else {
                $query->whereRaw('1 = 0');
            }

            return;
        }

        if (RoleHelper::is_team_lead()) {
            $this->applyTeamLeadOrSelfScope($query, $currentUser->team_id);

            return;
        }

        if (RoleHelper::is_telecaller()) {
            $query->whereHas('lead', function ($q) {
                $q->where('telecaller_id', AuthHelper::getCurrentUserId());
            });
        }
    }

    protected function applySupportRoleScope(Builder $query): void
    {
        $currentUser = AuthHelper::getCurrentUser();
        if (! $currentUser) {
            return;
        }

        if (RoleHelper::is_admin_or_super_admin()) {
            return;
        }

        if (RoleHelper::is_team_lead()) {
            $this->applyTeamLeadOrSelfScope($query, $currentUser->team_id);

            return;
        }

        if (RoleHelper::is_telecaller()) {
            $query->whereHas('lead', function ($q) {
                $q->where('telecaller_id', AuthHelper::getCurrentUserId());
            });
        }
    }

    protected function applyPostSalesRoleScope(Builder $query): void
    {
        $currentUser = AuthHelper::getCurrentUser();
        if (! $currentUser) {
            return;
        }

        if (RoleHelper::is_admin_or_super_admin() || RoleHelper::is_post_sales_head()) {
            return;
        }

        if (RoleHelper::is_post_sales()) {
            $query->where('post_sales_user_id', AuthHelper::getCurrentUserId());

            return;
        }

        $query->whereRaw('1 = 0');
    }

    protected function applyTeamLeadOrSelfScope(Builder $query, ?int $teamId): void
    {
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
    }

    protected function applyFilters(Builder $query, Request $request, string $pageType): void
    {
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('register_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
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

        foreach (['reg_fee', 'exam_fee', 'id_card', 'tma'] as $field) {
            if ($request->filled($field)) {
                $query->whereHas('studentDetails', function ($q) use ($request, $field) {
                    $q->where($field, $request->$field);
                });
            }
        }

        foreach (['call_status', 'class_information', 'orientation_class_status', 'whatsapp_group_status', 'class_status'] as $field) {
            if ($request->filled($field)) {
                $query->whereHas('studentDetails', function ($q) use ($request, $field) {
                    $q->where($field, $request->$field);
                });
            }
        }

        if ($request->filled('programme_type')) {
            $query->whereHas('leadDetail', function ($q) use ($request) {
                $q->where('programme_type', $request->programme_type);
            });
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

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($pageType === 'admin') {
            \App\Helpers\TeamTelecallerFilterHelper::applyConvertedLeadQueryFilters($query, $request);
            \App\Support\CourseFlagFieldSupport::applyListingFilter($query, $request);
        }

        if ($pageType === 'mentor' || $pageType === 'faculty') {
            if ($request->filled('subject_id')) {
                $query->where('subject_id', $request->subject_id);
            }
            if ($request->filled('registration_status')) {
                $query->whereHas('studentDetails', function ($q) use ($request) {
                    $q->where('registration_status', $request->registration_status);
                });
            }
            if ($request->filled('student_status')) {
                $query->whereHas('mentorDetails', function ($q) use ($request) {
                    $q->where('student_status', $request->student_status);
                });
            }
            \App\Support\MentorFlagFieldSupport::applyListingFilter($query, $request);
        }

        if ($pageType === 'support') {
            \App\Support\SupportFlagFieldSupport::applyListingFilter($query, $request);
        }

        if ($pageType === 'post-sales' && $request->filled('telecaller_id')) {
            $query->whereHas('lead', function ($q) use ($request) {
                $q->where('telecaller_id', $request->telecaller_id);
            });
        }
    }

    protected function getPageTitle(string $page): string
    {
        $titles = [
            'converted-leads' => 'All Converted Leads',
            'nios' => 'NIOS Converted Leads',
            'bosse' => 'BOSSE Converted Leads',
            'ugpg' => 'UG/PG Converted Leads',
            'edumaster' => 'EduMaster Converted Leads',
            'hotel-management' => 'Hotel Management Converted Leads',
            'medical-coding' => 'Certificate Course in Medical Coding Converted Leads',
            'hospital-administration' => 'Diploma in Hospital Administration Converted Leads',
            'ai-integrated-sales-marketing' => 'AI-Integrated Sales & Marketing Converted Leads',
            'gmvss' => 'GMVSS Converted Leads',
            'python' => 'Python Converted Leads',
            'data-science' => 'Data Science Converted Leads',
            'ai-python' => 'AI with Python Converted Leads',
            'digital-marketing' => 'Digital Marketing Converted Leads',
            'diploma-in-data-science' => 'Diploma in Data Science Converted Leads',
            'web-development' => 'Web Development Converted Leads',
            'vibe-coding' => 'Vibe Coding Converted Leads',
            'graphic-designing' => 'Graphic Designing Converted Leads',
            'ai-integrated-video-editing' => 'AI Video Editing Converted Leads',
            'ai-integrated-videography' => 'AI Videography Converted Leads',
            'ai-integrated-photography' => 'AI Photography Converted Leads',
            'machine-learning' => 'Machine Learning Converted Leads',
            'flutter' => 'Flutter Converted Leads',
            'rpa' => 'RPA Converted Leads',
            'eduthanzeel' => 'Eduthanzeel Converted Leads',
            'eschool' => 'E-School Converted Leads',
            'junior-vlogger' => 'Junior Vlogger Converted Leads',
            'robo-vibe' => 'Robo Vibe Converted Leads',
            'prompt-engineering' => 'Prompt Engineering Converted Leads',
            'post-sales' => 'Post-Sales Converted Students',
        ];

        $type = $this->getPageType($page);
        $base = $this->getBasePage($page);

        if ($type !== 'admin' && $type !== 'post-sales') {
            $baseTitle = $titles[$base] ?? (ucwords(str_replace('-', ' ', $base)) . ' Converted Leads');

            return ucfirst($type) . ' - ' . $baseTitle;
        }

        return $titles[$page] ?? (ucwords(str_replace('-', ' ', $page)) . ' Converted Leads');
    }
}
