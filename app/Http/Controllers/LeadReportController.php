<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\LeadSource;
use App\Models\Team;
use App\Models\User;
use App\Models\Country;
use App\Models\Course;
use App\Helpers\AuthHelper;
use App\Helpers\DateRangeHelper;
use App\Support\TelecallerPerformanceReportBuilder;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class LeadReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom.auth');
    }

    public function index(Request $request)
    {
        // Default date range (last 7 days)
        $fromDate = $request->get('date_from', Carbon::now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        // Get filter options
        $leadStatuses = LeadStatus::select('id', 'title', 'color')->get();
        $leadSources = LeadSource::select('id', 'title')->get();
        $teams = Team::select('id', 'name')->nonMarketing()->get();

        // Get reports data
        $reports = [
            'lead_status' => $this->getLeadStatusReport($fromDate, $toDate),
            'lead_source' => $this->getLeadSourceReport($fromDate, $toDate),
            'team' => $this->getTeamReport($fromDate, $toDate),
            'telecaller' => $this->getTelecallerReport($fromDate, $toDate)['rows'],
            'b2b' => $this->getB2BReport($fromDate, $toDate),
        ];

        // Get current user role information
        $currentUser = AuthHelper::getCurrentUser();
        $isTeamLead = $currentUser && \App\Helpers\RoleHelper::is_team_lead();
        $isTelecaller = $currentUser && AuthHelper::isTelecaller();

        return view('admin.reports.leads', compact(
            'reports', 'leadStatuses', 'leadSources', 'teams', 'fromDate', 'toDate', 'isTeamLead', 'isTelecaller'
        ));
    }

    public function leadStatusReport(Request $request)
    {
        $fromDate = $request->get('date_from', Carbon::now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $leadStatusId = $request->get('lead_status_id');

        // Get filter options
        $leadStatuses = LeadStatus::select('id', 'title', 'color')->get();

        // Get reports data
        $reports = [
            'lead_status' => $this->getLeadStatusReport($fromDate, $toDate),
            'monthly' => $this->getMonthlyReport($fromDate, $toDate),
            'conversion' => $this->getConversionReport($fromDate, $toDate),
        ];

        // Get leads data for the detailed view with optional lead status filter
        $leadsQuery = Lead::with(['leadStatus:id,title,color', 'leadSource:id,title', 'telecaller:id,name'])
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);

        if ($leadStatusId) {
            $leadsQuery->where('lead_status_id', $leadStatusId);
        }

        $this->applyRoleBasedFilter($leadsQuery);
        $leads = $leadsQuery->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.reports.lead-status', compact('reports', 'leads', 'fromDate', 'toDate', 'leadStatuses', 'leadStatusId'));
    }

    public function leadSourceReport(Request $request)
    {
        $fromDate = $request->get('date_from', Carbon::now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $leadSourceId = $request->get('lead_source_id');

        // Get filter options
        $leadSources = LeadSource::select('id', 'title')->get();

        // Get reports data
        $reports = [
            'lead_source' => $this->getLeadSourceReport($fromDate, $toDate),
            'monthly' => $this->getMonthlyReport($fromDate, $toDate),
        ];

        // Get leads data for the detailed view with optional lead source filter
        $leadsQuery = Lead::with(['leadStatus:id,title,color', 'leadSource:id,title', 'telecaller:id,name'])
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);

        if ($leadSourceId) {
            $leadsQuery->where('lead_source_id', $leadSourceId);
        }

        $this->applyRoleBasedFilter($leadsQuery);
        $leads = $leadsQuery->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

        return view('admin.reports.lead-source', compact('reports', 'leads', 'fromDate', 'toDate', 'leadSources', 'leadSourceId'));
    }

    public function teamReport(Request $request)
    {
        // Check if user is telecaller but not team lead - deny access
        if (AuthHelper::isTelecaller() && !\App\Helpers\RoleHelper::is_team_lead()) {
            abort(403, 'Access denied. Telecallers cannot access team reports.');
        }

        $fromDate = $request->get('date_from', Carbon::now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $teamId = $request->get('team_id');

        // Get filter options based on role
        $currentUser = AuthHelper::getCurrentUser();
        $isTeamLead = $currentUser && \App\Helpers\RoleHelper::is_team_lead();

        if ($isTeamLead) {
            // Team Lead: Show only their team
            $teamId = $currentUser->team_id;
            $teams = Team::where('id', $teamId)->select('id', 'name')->get();
        }
        else {
            // Admin/Super Admin: Show all teams
            $teams = Team::select('id', 'name')->nonMarketing()->get();
        }

        // Get reports data
        $reports = [
            'team' => $this->getTeamReport($fromDate, $toDate),
            'monthly' => $this->getMonthlyReport($fromDate, $toDate),
        ];

        // Get leads data for the detailed view with optional team filter
        $leadsQuery = Lead::with(['leadStatus:id,title,color', 'leadSource:id,title', 'telecaller:id,name,team_id', 'telecaller.team:id,name'])
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);

        if ($teamId) {
            $leadsQuery->whereHas('telecaller', function ($query) use ($teamId) {
                $query->where('team_id', $teamId);
            });
        }

        $this->applyRoleBasedFilter($leadsQuery);
        $leads = $leadsQuery->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.reports.team', compact('reports', 'leads', 'fromDate', 'toDate', 'teams', 'teamId'));
    }

    public function telecallerReport(Request $request)
    {
        // Check if user is telecaller but not team lead - deny access
        if (AuthHelper::isTelecaller() && !\App\Helpers\RoleHelper::is_team_lead()) {
            abort(403, 'Access denied. Only team leads can access telecaller reports.');
        }

        [$fromDate, $toDate, $dateRange] = $this->resolveTelecallerReportDates($request);
        $telecallerId = $request->get('telecaller_id');
        $teamId = $request->get('team_id');

        $currentUser = AuthHelper::getCurrentUser();
        $isTeamLead = $currentUser && \App\Helpers\RoleHelper::is_team_lead();
        $isTelecaller = $currentUser && $currentUser->role_id == 3;

        // Get filter options based on role
        if ($isTeamLead) {
            // Team Lead: Show only their team members
            $userTeamId = $currentUser->team_id;
            if ($userTeamId) {
                $teamMemberIds = AuthHelper::getTeamMemberIds($userTeamId);
                $teamMemberIds[] = AuthHelper::getCurrentUserId(); // Include team lead
                $telecallers = User::whereIn('id', $teamMemberIds)
                    ->where('role_id', 3)
                    ->select('id', 'name', 'phone')
                    ->get();
            }
            else {
                $telecallers = collect([$currentUser]); // Only themselves if no team
            }
        }
        elseif ($isTelecaller) {
            // Telecaller: Show only themselves
            $telecallers = collect([$currentUser]);
        }
        else {
            // Admin/Super Admin: Show all telecallers
            $telecallersQuery = User::where('role_id', 3)->select('id', 'name', 'phone');
            if ($teamId) {
                $telecallersQuery->where('team_id', $teamId);
            }
            $telecallers = $telecallersQuery->get();
        }

        // Get reports data
        $telecallerReportData = $this->getTelecallerReport($fromDate, $toDate, $teamId, $telecallerId ? (int) $telecallerId : null);
        $reports = [
            'telecaller' => $telecallerReportData['rows'],
            'monthly' => $this->getMonthlyReport($fromDate, $toDate),
        ];
        $reportSummary = $telecallerReportData['summary'];

        // Get leads data for the detailed view with optional telecaller filter
        $leadsQuery = Lead::with(['leadStatus:id,title,color', 'leadSource:id,title', 'telecaller:id,name', 'team:id,name'])
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);

        if ($telecallerId) {
            $leadsQuery->where('telecaller_id', $telecallerId);
        }

        if ($teamId) {
            $leadsQuery->where('team_id', $teamId);
        }

        $this->applyRoleBasedFilter($leadsQuery);
        $leads = $leadsQuery->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.reports.telecaller', compact(
            'reports',
            'leads',
            'fromDate',
            'toDate',
            'dateRange',
            'telecallers',
            'telecallerId',
            'teamId',
            'reportSummary'
        ));
    }

    public function telecallerCallAnalytics(Request $request, User $user): JsonResponse
    {
        if (AuthHelper::isTelecaller() && !\App\Helpers\RoleHelper::is_team_lead()) {
            return response()->json(['status' => false, 'message' => 'Access denied.'], 403);
        }

        if (!$this->canViewTelecallerInReport($user)) {
            return response()->json(['status' => false, 'message' => 'Access denied for this telecaller.'], 403);
        }

        $dateRange = $request->get('date_range');
        if (!$dateRange && ($request->filled('date_from') || $request->filled('date_to'))) {
            $dateRange = DateRangeHelper::PRESET_CUSTOM;
        }

        $dates = DateRangeHelper::resolve(
            $dateRange,
            $request->get('date_from') ?: $request->get('start_date'),
            $request->get('date_to') ?: $request->get('end_date')
        );

        $fromDate = $dates['start_date'];
        $toDate = $dates['end_date'];

        $stats = TelecallerPerformanceReportBuilder::callAnalyticsForTelecaller($user->id, $fromDate, $toDate);
        $calls = TelecallerPerformanceReportBuilder::recentCallsForTelecaller($user->id, $fromDate, $toDate);

        return response()->json([
            'status' => true,
            'telecaller' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
            ],
            'period' => [
                'date_range' => $dates['date_range'],
                'start_date' => $fromDate,
                'end_date' => $toDate,
                'label' => DateRangeHelper::displayPeriod($dates),
            ],
            'stats' => $stats,
            'calls' => $calls,
            'full_report_url' => route('admin.call-analytics.report.telecaller', array_merge(
                ['telecaller' => $user->id],
                DateRangeHelper::queryParams($dates)
            )),
        ]);
    }

    public function b2bReport(Request $request)
    {
        $fromDate = $request->get('date_from', Carbon::now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        // Get reports data
        $reports = [
            'b2b' => $this->getB2BReport($fromDate, $toDate),
            'monthly' => $this->getMonthlyReport($fromDate, $toDate),
        ];

        // Get leads data for the detailed view
        $leadsQuery = Lead::with(['leadStatus:id,title,color', 'leadSource:id,title', 'telecaller:id,name'])
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);

        $this->applyRoleBasedFilter($leadsQuery);
        $leads = $leadsQuery->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.reports.b2b', compact('reports', 'leads', 'fromDate', 'toDate'));
    }

    private function getB2BReport($fromDate, $toDate)
    {
        // Get count for B2B (is_b2b = 1)
        $b2bQuery = Lead::whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->where('is_b2b', 1);
        $this->applyRoleBasedFilter($b2bQuery);
        $b2bCount = $b2bQuery->count();

        // Get count for In House (is_b2b = 0 or null)
        $inHouseQuery = Lead::whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->where(function ($q) {
            $q->whereNull('is_b2b')->orWhere('is_b2b', 0);
        });
        $this->applyRoleBasedFilter($inHouseQuery);
        $inHouseCount = $inHouseQuery->count();

        return collect([
            (object)['title' => 'B2B', 'count' => $b2bCount, 'is_b2b' => 1],
            (object)['title' => 'In House', 'count' => $inHouseCount, 'is_b2b' => 0]
        ]);
    }

    private function getLeadStatusReport($fromDate, $toDate)
    {
        $query = Lead::select('lead_statuses.id', 'lead_statuses.title')
            ->selectRaw('COUNT(leads.id) as count')
            ->join('lead_statuses', 'leads.lead_status_id', '=', 'lead_statuses.id')
            ->whereBetween('leads.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);

        $this->applyRoleBasedFilter($query);

        return $query->groupBy('lead_statuses.id', 'lead_statuses.title')
            ->orderBy('count', 'desc')
            ->get();
    }

    private function getLeadSourceReport($fromDate, $toDate)
    {
        $query = Lead::select('lead_sources.title')
            ->selectRaw('COUNT(leads.id) as count')
            ->join('lead_sources', 'leads.lead_source_id', '=', 'lead_sources.id')
            ->whereBetween('leads.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);

        $this->applyRoleBasedFilter($query);

        return $query->groupBy('lead_sources.id', 'lead_sources.title')
            ->orderBy('count', 'desc')
            ->get();
    }

    private function getTeamReport($fromDate, $toDate)
    {
        $currentUser = AuthHelper::getCurrentUser();
        $teams = collect();

        if ($currentUser && \App\Helpers\RoleHelper::is_team_lead()) {
            // Team Lead: Only show their own team
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $team = Team::select('id', 'name')->find($teamId);
                if ($team) {
                    // Get lead count for this team through telecallers
                    $leadCountQuery = Lead::join('users', 'leads.telecaller_id', '=', 'users.id')
                        ->where('users.team_id', $team->id)
                        ->whereBetween('leads.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
                    $this->applyRoleBasedFilter($leadCountQuery);
                    $leadCount = $leadCountQuery->count();

                    // Get telecaller data for this team
                    $telecallersQuery = Lead::select('users.id', 'users.name')
                        ->selectRaw('COUNT(leads.id) as lead_count')
                        ->join('users', 'leads.telecaller_id', '=', 'users.id')
                        ->where('users.team_id', $team->id)
                        ->whereBetween('leads.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
                        ->where('users.role_id', 3); // Telecaller role
                    $this->applyRoleBasedFilter($telecallersQuery);
                    $telecallers = $telecallersQuery->groupBy('users.id', 'users.name')
                        ->orderBy('lead_count', 'desc')
                        ->get();

                    $team->count = $leadCount;
                    $team->telecallers = $telecallers;

                    $teams->push($team);
                }
            }
        }
        else {
            // Admin/Super Admin: Show all teams
            $allTeams = Team::select('id', 'name')->nonMarketing()->get();

            foreach ($allTeams as $team) {
                // Get lead count for this team through telecallers
                $leadCountQuery = Lead::join('users', 'leads.telecaller_id', '=', 'users.id')
                    ->where('users.team_id', $team->id)
                    ->whereBetween('leads.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
                $this->applyRoleBasedFilter($leadCountQuery);
                $leadCount = $leadCountQuery->count();

                // Get telecaller data for this team
                $telecallersQuery = Lead::select('users.id', 'users.name')
                    ->selectRaw('COUNT(leads.id) as lead_count')
                    ->join('users', 'leads.telecaller_id', '=', 'users.id')
                    ->where('users.team_id', $team->id)
                    ->whereBetween('leads.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
                    ->where('users.role_id', 3); // Telecaller role
                $this->applyRoleBasedFilter($telecallersQuery);
                $telecallers = $telecallersQuery->groupBy('users.id', 'users.name')
                    ->orderBy('lead_count', 'desc')
                    ->get();

                $team->count = $leadCount;
                $team->telecallers = $telecallers;

                $teams->push($team);
            }
        }

        // Sort by lead count descending
        return $teams->sortByDesc('count')->values();
    }

    private function getTelecallerReport($fromDate, $toDate, $teamId = null, ?int $telecallerId = null)
    {
        return TelecallerPerformanceReportBuilder::build(
            $fromDate,
            $toDate,
            $teamId ? (int) $teamId : null,
            $telecallerId
        );
    }

    private function getCountryReport($fromDate, $toDate)
    {
        return Lead::select('countries.title')
            ->selectRaw('COUNT(leads.id) as count')
            ->join('countries', 'leads.country_id', '=', 'countries.id')
            ->whereBetween('leads.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->groupBy('countries.id', 'countries.title')
            ->orderBy('count', 'desc')
            ->get();
    }

    private function getCourseReport($fromDate, $toDate)
    {
        return Lead::select('courses.title')
            ->selectRaw('COUNT(leads.id) as count')
            ->join('courses', 'leads.course_id', '=', 'courses.id')
            ->whereBetween('leads.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->groupBy('courses.id', 'courses.title')
            ->orderBy('count', 'desc')
            ->get();
    }


    private function getMonthlyReport($fromDate, $toDate)
    {
        $query = Lead::query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key")
            ->selectRaw("MIN(DATE_FORMAT(created_at, '%b %Y')) as month")
            ->selectRaw('COUNT(*) as total_leads')
            ->selectRaw('SUM(CASE WHEN is_converted = 1 THEN 1 ELSE 0 END) as converted')
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);

        $this->applyRoleBasedFilter($query);

        return $query
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->get()
            ->map(function ($row) {
                $totalLeads = (int) $row->total_leads;
                $convertedLeads = (int) $row->converted;

                return (object) [
                    'month' => $row->month,
                    'count' => $totalLeads,
                    'total_leads' => $totalLeads,
                    'converted' => $convertedLeads,
                    'conversion_rate' => $totalLeads > 0
                        ? round(($convertedLeads / $totalLeads) * 100, 2)
                        : 0,
                ];
            });
    }

    private function getConversionReport($fromDate, $toDate)
    {
        $totalLeadsQuery = Lead::whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        $this->applyRoleBasedFilter($totalLeadsQuery);
        $totalLeads = $totalLeadsQuery->count();

        $convertedLeadsQuery = Lead::whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->where('is_converted', true);
        $this->applyRoleBasedFilter($convertedLeadsQuery);
        $convertedLeads = $convertedLeadsQuery->count();

        return [
            'total_leads' => $totalLeads,
            'converted_leads' => $convertedLeads,
            'conversion_rate' => $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 2) : 0
        ];
    }

    /**
     * Export Lead Status Report to Excel
     */
    public function exportLeadStatusExcel(Request $request)
    {
        $fromDate = $request->get('date_from', Carbon::now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $leadStatusId = $request->get('lead_status_id');

        // Get leads data for the detailed view with optional lead status filter
        $leadsQuery = Lead::with(['leadStatus:id,title,color', 'leadSource:id,title', 'telecaller:id,name'])
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);

        if ($leadStatusId) {
            $leadsQuery->where('lead_status_id', $leadStatusId);
        }

        $this->applyRoleBasedFilter($leadsQuery);
        $leads = $leadsQuery->orderBy('created_at', 'desc')->get();

        $export = new \App\Exports\LeadStatusReportExport($leads, $fromDate, $toDate);
        $spreadsheet = $export->export();

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $filename = 'lead_status_report_' . $fromDate . '_to_' . $toDate . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    /**
     * Export Lead Status Report to PDF
     */
    public function exportLeadStatusPdf(Request $request)
    {
        $fromDate = $request->get('date_from', Carbon::now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $leadStatusId = $request->get('lead_status_id');

        // Get reports data
        $reports = [
            'lead_status' => $this->getLeadStatusReport($fromDate, $toDate),
            'monthly' => $this->getMonthlyReport($fromDate, $toDate),
            'conversion' => $this->getConversionReport($fromDate, $toDate),
        ];

        // Get leads data for the detailed view with optional lead status filter
        $leadsQuery = Lead::with(['leadStatus:id,title,color', 'leadSource:id,title', 'telecaller:id,name'])
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);

        if ($leadStatusId) {
            $leadsQuery->where('lead_status_id', $leadStatusId);
        }

        $this->applyRoleBasedFilter($leadsQuery);
        $leads = $leadsQuery->orderBy('created_at', 'desc')->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.exports.lead-status-pdf', [
            'reports' => $reports,
            'leads' => $leads,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'reportType' => 'Lead Status Report',
            'generatedAt' => now()->format('Y-m-d H:i:s')
        ]);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('lead_status_report_' . $fromDate . '_to_' . $toDate . '.pdf');
    }

    /**
     * Export Lead Source Report to Excel
     */
    public function exportLeadSourceExcel(Request $request)
    {
        $fromDate = $request->get('date_from', Carbon::now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $leadSourceId = $request->get('lead_source_id');

        // Get leads data for the detailed view with optional lead source filter
        $leadsQuery = Lead::with(['leadStatus:id,title,color', 'leadSource:id,title', 'telecaller:id,name'])
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);

        if ($leadSourceId) {
            $leadsQuery->where('lead_source_id', $leadSourceId);
        }

        $this->applyRoleBasedFilter($leadsQuery);
        $leads = $leadsQuery->orderBy('created_at', 'desc')->get();

        $export = new \App\Exports\LeadSourceReportExport($leads, $fromDate, $toDate);
        $spreadsheet = $export->export();

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $filename = 'lead_source_report_' . $fromDate . '_to_' . $toDate . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    /**
     * Export Lead Source Report to PDF
     */
    public function exportLeadSourcePdf(Request $request)
    {
        $fromDate = $request->get('date_from', Carbon::now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $leadSourceId = $request->get('lead_source_id');

        // Get reports data
        $reports = [
            'lead_source' => $this->getLeadSourceReport($fromDate, $toDate),
            'monthly' => $this->getMonthlyReport($fromDate, $toDate),
        ];

        // Get leads data for the detailed view with optional lead source filter
        $leadsQuery = Lead::with(['leadStatus:id,title,color', 'leadSource:id,title', 'telecaller:id,name'])
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);

        if ($leadSourceId) {
            $leadsQuery->where('lead_source_id', $leadSourceId);
        }

        $this->applyRoleBasedFilter($leadsQuery);
        $leads = $leadsQuery->orderBy('created_at', 'desc')->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.exports.lead-source-pdf', [
            'reports' => $reports,
            'leads' => $leads,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'reportType' => 'Lead Source Report',
            'generatedAt' => now()->format('Y-m-d H:i:s')
        ]);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('lead_source_report_' . $fromDate . '_to_' . $toDate . '.pdf');
    }

    /**
     * Export Team Report to Excel
     */
    public function exportTeamExcel(Request $request)
    {
        $fromDate = $request->get('date_from', Carbon::now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $teamId = $request->get('team_id');

        // Get leads data for the detailed view with optional team filter
        $leadsQuery = Lead::with(['leadStatus:id,title,color', 'leadSource:id,title', 'telecaller:id,name', 'team:id,name'])
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);

        if ($teamId) {
            $leadsQuery->where('team_id', $teamId);
        }

        $this->applyRoleBasedFilter($leadsQuery);
        $leads = $leadsQuery->orderBy('created_at', 'desc')->get();

        $export = new \App\Exports\TeamReportExport($leads, $fromDate, $toDate);
        $spreadsheet = $export->export();

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $filename = 'team_report_' . $fromDate . '_to_' . $toDate . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    /**
     * Export Team Report to PDF
     */
    public function exportTeamPdf(Request $request)
    {
        $fromDate = $request->get('date_from', Carbon::now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $teamId = $request->get('team_id');

        // Get reports data
        $reports = [
            'team' => $this->getTeamReport($fromDate, $toDate),
            'monthly' => $this->getMonthlyReport($fromDate, $toDate),
        ];

        // Get leads data for the detailed view with optional team filter
        $leadsQuery = Lead::with(['leadStatus:id,title,color', 'leadSource:id,title', 'telecaller:id,name', 'team:id,name'])
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);

        if ($teamId) {
            $leadsQuery->where('team_id', $teamId);
        }

        $this->applyRoleBasedFilter($leadsQuery);
        $leads = $leadsQuery->orderBy('created_at', 'desc')->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.exports.team-pdf', [
            'reports' => $reports,
            'leads' => $leads,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'reportType' => 'Team Report',
            'generatedAt' => now()->format('Y-m-d H:i:s')
        ]);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('team_report_' . $fromDate . '_to_' . $toDate . '.pdf');
    }

    /**
     * Export Telecaller Report to Excel
     */
    public function exportTelecallerExcel(Request $request)
    {
        [$fromDate, $toDate] = $this->resolveTelecallerReportDates($request);
        $telecallerId = $request->get('telecaller_id');
        $teamId = $request->get('team_id');

        $telecallerReportData = $this->getTelecallerReport($fromDate, $toDate, $teamId, $telecallerId ? (int) $telecallerId : null);
        $reports = [
            'telecaller' => $telecallerReportData['rows'],
        ];
        $reportSummary = $telecallerReportData['summary'];

        // Get leads data for the detailed view with optional telecaller filter
        $leadsQuery = Lead::with(['leadStatus:id,title,color', 'leadSource:id,title', 'telecaller:id,name'])
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);

        if ($telecallerId) {
            $leadsQuery->where('telecaller_id', $telecallerId);
        }

        if ($teamId) {
            $leadsQuery->where('team_id', $teamId);
        }

        $this->applyRoleBasedFilter($leadsQuery);
        $leads = $leadsQuery->orderBy('created_at', 'desc')->get();

        $export = new \App\Exports\TelecallerPerformanceReportExport($reports['telecaller'], $reportSummary, $fromDate, $toDate);
        $spreadsheet = $export->export();

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $filename = 'telecaller_report_' . $fromDate . '_to_' . $toDate . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    /**
     * Export Telecaller Report to PDF
     */
    public function exportTelecallerPdf(Request $request)
    {
        [$fromDate, $toDate] = $this->resolveTelecallerReportDates($request);
        $telecallerId = $request->get('telecaller_id');
        $teamId = $request->get('team_id');

        $telecallerReportData = $this->getTelecallerReport($fromDate, $toDate, $teamId, $telecallerId ? (int) $telecallerId : null);
        $reports = [
            'telecaller' => $telecallerReportData['rows'],
        ];
        $reportSummary = $telecallerReportData['summary'];

        // Get leads data for the detailed view with optional telecaller filter
        $leadsQuery = Lead::with(['leadStatus:id,title,color', 'leadSource:id,title', 'telecaller:id,name'])
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);

        if ($telecallerId) {
            $leadsQuery->where('telecaller_id', $telecallerId);
        }

        if ($teamId) {
            $leadsQuery->where('team_id', $teamId);
        }

        $this->applyRoleBasedFilter($leadsQuery);
        $leads = $leadsQuery->orderBy('created_at', 'desc')->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.exports.telecaller-pdf', [
            'reports' => $reports,
            'reportSummary' => $reportSummary,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'reportType' => 'Telecaller Report',
            'generatedAt' => now()->format('Y-m-d H:i:s')
        ]);

        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('telecaller_report_' . $fromDate . '_to_' . $toDate . '.pdf');
    }

    /**
     * Export Main Reports to Excel
     */
    public function exportMainReportsExcel(Request $request)
    {
        $fromDate = $request->get('date_from', Carbon::now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        // Get reports data
        $reports = [
            'lead_status' => $this->getLeadStatusReport($fromDate, $toDate),
            'lead_source' => $this->getLeadSourceReport($fromDate, $toDate),
            'team' => $this->getTeamReport($fromDate, $toDate),
            'telecaller' => $this->getTelecallerReport($fromDate, $toDate)['rows'],
        ];

        $export = new \App\Exports\MainReportsExport($reports, $fromDate, $toDate);
        $spreadsheet = $export->export();

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $filename = 'main_reports_' . $fromDate . '_to_' . $toDate . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    /**
     * Export Main Reports to PDF
     */
    public function exportMainReportsPdf(Request $request)
    {
        $fromDate = $request->get('date_from', Carbon::now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        // Get reports data
        $reports = [
            'lead_status' => $this->getLeadStatusReport($fromDate, $toDate),
            'lead_source' => $this->getLeadSourceReport($fromDate, $toDate),
            'team' => $this->getTeamReport($fromDate, $toDate),
            'telecaller' => $this->getTelecallerReport($fromDate, $toDate)['rows'],
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.exports.main-reports-pdf', [
            'reports' => $reports,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'reportType' => 'Main Reports',
            'generatedAt' => now()->format('Y-m-d H:i:s')
        ]);

        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('main_reports_' . $fromDate . '_to_' . $toDate . '.pdf');
    }

    private function resolveTelecallerReportDates(Request $request): array
    {
        $dateRange = $request->get('date_range');
        if (!$dateRange && ($request->filled('date_from') || $request->filled('date_to'))) {
            $dateRange = DateRangeHelper::PRESET_CUSTOM;
        }

        $dates = DateRangeHelper::resolve(
            $dateRange,
            $request->get('date_from') ?: $request->get('start_date'),
            $request->get('date_to') ?: $request->get('end_date')
        );

        return [$dates['start_date'], $dates['end_date'], $dates['date_range']];
    }

    private function canViewTelecallerInReport(User $telecaller): bool
    {
        if ((int) $telecaller->role_id !== 3) {
            return false;
        }

        $currentUser = AuthHelper::getCurrentUser();
        if (!$currentUser) {
            return true;
        }

        if (\App\Helpers\RoleHelper::is_team_lead()) {
            $teamId = $currentUser->team_id;
            if (!$teamId) {
                return (int) $telecaller->id === (int) AuthHelper::getCurrentUserId();
            }

            $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
            $teamMemberIds[] = AuthHelper::getCurrentUserId();

            return in_array((int) $telecaller->id, array_map('intval', $teamMemberIds), true);
        }

        if (AuthHelper::isTelecaller()) {
            return (int) $telecaller->id === (int) AuthHelper::getCurrentUserId();
        }

        return true;
    }

    /**
     * Apply role-based filtering to lead queries
     */
    private function applyRoleBasedFilter($query)
    {
        $currentUser = AuthHelper::getCurrentUser();

        // If no user is logged in, return all leads (for admin view)
        if (!$currentUser) {
            return $query;
        }

        // Check team lead first (higher priority)
        if ($currentUser->is_team_lead == 1) {
            // Team Lead: Can see their own leads + their team members' leads
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
                $teamMemberIds[] = AuthHelper::getCurrentUserId(); // Include team lead
                $query->whereIn('telecaller_id', $teamMemberIds);
            }
            else {
                // If no team assigned, only show their own leads
                $query->where('telecaller_id', AuthHelper::getCurrentUserId());
            }
        }
        elseif (AuthHelper::isTelecaller()) {
            // Telecaller: Can only see their own leads
            $query->where('telecaller_id', AuthHelper::getCurrentUserId());
        }

        return $query;
    }
}