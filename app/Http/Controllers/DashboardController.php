<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\User;
use App\Models\LeadStatus;
use App\Models\Country;
use App\Models\Team;
use App\Models\LeadSource;
use App\Models\ConvertedLead;
use App\Models\Invoice;
use App\Models\Payment;
use App\Helpers\AuthHelper;
use App\Helpers\RoleHelper;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('custom.auth');
    }

    /**
     * Show the application dashboard.
     */
    public function index()
    {
        // Check if user is auditor - show specialized dashboard
        if (RoleHelper::is_auditor()) {
            return $this->auditorDashboard();
        }

        $data = [
            'leadStatuses' => $this->getLeadStatusesWithCount(),
            'topTelecallers' => $this->getTopTelecallers(),
            'topCountries' => $this->getTopCountries(),
            'totalLeads' => $this->getTotalLeadsCount(),
            'totalUsers' => User::whereNotIn('role_id', [1, 2])->count(),
            'totalAdmins' => User::where('role_id', 2)->count(),
            'totalTelecallers' => User::where('role_id', 3)->count(),
            'recentLeads' => $this->getRecentLeads(),
            'monthlyLeads' => $this->getMonthlyLeadsData(),
            'leadSourcesData' => $this->getLeadSourcesData(),
            'conversionRate' => $this->getConversionRate(),
            'recentActivities' => $this->getRecentActivities(),
            'weeklyStats' => $this->getWeeklyStats(),
            'todaysLeads' => $this->getTodaysLeads(),
            'todaysLeadsCount' => $this->getTodaysLeadsCount(),
            'todaysConvertedLeads' => $this->getTodaysConvertedLeads(),
            'saleCount' => $this->getSaleCount(),
            'weeklySaleCount' => $this->getWeeklySaleCount(),
        ];

        return view('dashboard', $data);
    }

    /**
     * Show specialized dashboard for auditors
     */
    private function auditorDashboard()
    {
        $data = [
            'summaryStats' => $this->getAuditorSummaryStats(),
            'telecallerStats' => $this->getAuditorTelecallerStats(),
            'teamStats' => $this->getAuditorTeamStats(),
            'leadStats' => $this->getAuditorLeadStats(),
            'convertedLeadsStats' => $this->getAuditorConvertedLeadsStats(),
            'followupStats' => $this->getAuditorFollowupStats(),
            'todaysLeads' => $this->getAuditorTodaysLeads(),
            'chartsData' => $this->getAuditorChartsData(),
        ];

        return view('dashboard-auditor', $data);
    }

    /**
     * Get top telecallers by lead count.
     */
    private function getTopTelecallers()
    {
        // Get all telecallers first
        $telecallers = User::where('users.role_id', 3)
            ->whereNull('users.deleted_at')
            ->select('users.id', 'users.name', 'users.phone', 'users.profile_picture')
            ->get();
        
        // Calculate lead count for each telecaller with role-based filtering
        $telecallersWithCounts = $telecallers->map(function ($telecaller) {
            $leadsQuery = Lead::where('telecaller_id', $telecaller->id)
                ->whereNull('deleted_at');
            $this->applyRoleBasedFilter($leadsQuery);
            $leadCount = $leadsQuery->count();
            
            return [
                'id' => $telecaller->id,
                'name' => $telecaller->name,
                'phone' => $telecaller->phone,
                'profile_picture' => $telecaller->profile_picture,
                'count' => $leadCount,
            ];
        })
        ->filter(function ($telecaller) {
            return $telecaller['count'] > 0;
        })
        ->sortByDesc('count')
        ->take(5)
        ->values();

        return $telecallersWithCounts;
    }

    /**
     * Get top countries by lead count.
     */
    private function getTopCountries()
    {
        // Build query with role-based filtering
        $query = Country::select('countries.id', 'countries.title')
            ->selectRaw('COUNT(leads.id) as lead_count')
            ->leftJoin('leads', 'countries.id', '=', 'leads.country_id')
            ->whereNull('countries.deleted_at')
            ->whereNull('leads.deleted_at')
            ->groupBy('countries.id', 'countries.title')
            ->having('lead_count', '>', 0)
            ->orderByDesc('lead_count')
            ->limit(5);
        
        // Apply role-based filtering to the leads in the join
        $this->applyRoleBasedFilter($query);
        
        $countries = $query->get()
            ->map(function ($country) {
                return [
                    'id' => $country->id,
                    'title' => $country->title,
                    'count' => $country->lead_count,
                ];
            });

        return $countries;
    }

    /**
     * Get monthly leads data for charts.
     */
    private function getMonthlyLeadsData()
    {
        $months = [];
        $leadCounts = [];
        $convertedCounts = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();
            
            $totalLeadsQuery = Lead::whereBetween('created_at', [$monthStart, $monthEnd]);
            $convertedLeadsQuery = Lead::whereBetween('created_at', [$monthStart, $monthEnd])
                ->where('is_converted', true);
            
            $totalLeads = $this->applyRoleBasedFilter($totalLeadsQuery)->count();
            $convertedLeads = $this->applyRoleBasedFilter($convertedLeadsQuery)->count();
            
            $months[] = $date->format('M Y');
            $leadCounts[] = $totalLeads;
            $convertedCounts[] = $convertedLeads;
        }
        
        return [
            'months' => $months,
            'leadCounts' => $leadCounts,
            'convertedCounts' => $convertedCounts,
        ];
    }

    /**
     * Get lead sources data for charts.
     */
    private function getLeadSourcesData()
    {
        $query = Lead::select('lead_sources.title')
            ->selectRaw('COUNT(leads.id) as count')
            ->join('lead_sources', 'leads.lead_source_id', '=', 'lead_sources.id')
            ->whereNull('leads.deleted_at')
            ->whereNull('lead_sources.deleted_at')
            ->groupBy('lead_sources.id', 'lead_sources.title')
            ->orderByDesc('count')
            ->limit(5);
        
        return $this->applyRoleBasedFilter($query)->get()
            ->map(function ($source) {
                return [
                    'name' => $source->title,
                    'value' => $source->count,
                ];
            });
    }

    /**
     * Get conversion rate data.
     */
    private function getConversionRate()
    {
        $totalLeadsQuery = Lead::query();
        $convertedLeadsQuery = Lead::where('is_converted', true);
        
        $totalLeads = $this->applyRoleBasedFilter($totalLeadsQuery)->count();
        $convertedLeads = $this->applyRoleBasedFilter($convertedLeadsQuery)->count();
        
        return $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 2) : 0;
    }

    /**
     * Get recent activities.
     */
    private function getRecentActivities()
    {
        $activities = [];
        
        // Recent leads
        $recentLeadsQuery = Lead::with('leadStatus')
            ->orderBy('created_at', 'desc')
            ->limit(5);
        
        $recentLeads = $this->applyRoleBasedFilter($recentLeadsQuery)->get();
            
        foreach ($recentLeads as $lead) {
            $activities[] = [
                'type' => 'lead_added',
                'title' => 'New Lead Added',
                'description' => $lead->title,
                'time' => $lead->created_at,
                'icon' => 'ti ti-user-plus',
                'color' => 'success',
            ];
        }
        
        // Recent conversions
        $recentConversionsQuery = Lead::with('leadStatus')
            ->where('is_converted', true)
            ->orderBy('updated_at', 'desc')
            ->limit(3);
        
        $recentConversions = $this->applyRoleBasedFilter($recentConversionsQuery)->get();
            
        foreach ($recentConversions as $lead) {
            $activities[] = [
                'type' => 'lead_converted',
                'title' => 'Lead Converted',
                'description' => $lead->title,
                'time' => $lead->updated_at,
                'icon' => 'ti ti-check-circle',
                'color' => 'primary',
            ];
        }
        
        // Sort by time and limit to 8 activities
        usort($activities, function($a, $b) {
            return $b['time']->timestamp - $a['time']->timestamp;
        });
        
        return array_slice($activities, 0, 8);
    }

    /**
     * Get weekly statistics.
     */
    private function getWeeklyStats()
    {
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        
        // Get total leads (all time)
        $totalLeadsQuery = Lead::query();
        $totalLeads = $this->applyRoleBasedFilter($totalLeadsQuery)->count();
        
        // Get total converted leads (all time) from converted_leads table
        $convertedLeadsQuery = \App\Models\ConvertedLead::query();
        $convertedLeads = $this->applyRoleBasedFilterToConvertedLeads($convertedLeadsQuery)->count();
        
        // Get weekly leads for weekly stats
        $weeklyLeadsQuery = Lead::whereBetween('created_at', [$weekStart, $weekEnd]);
        $weeklyLeads = $this->applyRoleBasedFilter($weeklyLeadsQuery)->count();
        
        // Get weekly converted leads (conversions created this week)
        $weeklyConvertedLeadsQuery = \App\Models\ConvertedLead::whereBetween('created_at', [$weekStart, $weekEnd]);
        $weeklyConvertedLeads = $this->applyRoleBasedFilterToConvertedLeads($weeklyConvertedLeadsQuery)->count();
        
        // Calculate active leads this week (leads created this week that are not converted)
        // Get leads created this week that don't have a corresponding ConvertedLead
        $convertedLeadIds = \App\Models\ConvertedLead::pluck('lead_id')->toArray();
        $activeLeadsThisWeekQuery = Lead::whereBetween('created_at', [$weekStart, $weekEnd])
            ->whereNotIn('id', $convertedLeadIds);
        $activeLeadsThisWeek = $this->applyRoleBasedFilter($activeLeadsThisWeekQuery)->count();
        
        return [
            'totalLeads' => $weeklyLeads, // Weekly leads for the weekly stat
            'convertedLeads' => $convertedLeads, // Total converted leads (all time)
            'weeklyConvertedLeads' => $weeklyConvertedLeads, // Weekly converted leads
            'activeLeadsThisWeek' => $activeLeadsThisWeek, // Active leads this week (not converted)
            'conversionRate' => $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 2) : 0,
        ];
    }

    /**
     * Get lead statuses with count based on user role
     */
    private function getLeadStatusesWithCount()
    {
        // Get status details with counts
        $leadStatuses = LeadStatus::withCount(['leads' => function ($query) {
            $this->applyRoleBasedFilter($query);
        }])->get();
        
        return $leadStatuses;
    }

    /**
     * Get total leads count based on user role
     */
    private function getTotalLeadsCount()
    {
        $query = Lead::query();
        return $this->applyRoleBasedFilter($query)->count();
    }

    /**
     * Get recent leads based on user role
     */
    private function getRecentLeads()
    {
        $query = Lead::with(['leadStatus', 'leadSource'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(10);

        return $this->applyRoleBasedFilter($query)->get();
    }

    /**
     * Get today's leads based on user role (last 10 added today).
     */
    private function getTodaysLeads()
    {
        $today = now()->startOfDay();
        $tomorrow = now()->addDay()->startOfDay();

        $query = Lead::with(['leadStatus', 'leadSource', 'telecaller'])
            ->where('created_at', '>=', $today)
            ->where('created_at', '<', $tomorrow)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(10);

        return $this->applyRoleBasedFilter($query)->get();
    }

    /**
     * Get total count of today's leads for dashboard stats.
     */
    private function getTodaysLeadsCount()
    {
        $today = now()->startOfDay();
        $tomorrow = now()->addDay()->startOfDay();

        $query = Lead::query()
            ->where('created_at', '>=', $today)
            ->where('created_at', '<', $tomorrow);

        return $this->applyRoleBasedFilter($query)->count();
    }

    /**
     * Get today's converted leads based on user role
     */
    private function getTodaysConvertedLeads()
    {
        $today = now()->startOfDay();
        $tomorrow = now()->addDay()->startOfDay();
        
        $query = ConvertedLead::query()
            ->where('created_at', '>=', $today)
            ->where('created_at', '<', $tomorrow);
        
        return $this->applyRoleBasedFilterToConvertedLeads($query)->count();
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
        
        // Roles that can see all leads (same as admin)
        if (RoleHelper::is_admin_or_super_admin() || 
            RoleHelper::is_general_manager() ||
            RoleHelper::is_senior_manager() ||
            RoleHelper::is_admission_counsellor() || 
            RoleHelper::is_finance() || 
            RoleHelper::is_academic_assistant() || 
            RoleHelper::is_post_sales()) {
            // Can see all leads
            return $query;
        }
        
        if (AuthHelper::isTeamLead()) {
            // Team Lead: Can see their own leads + their team members' leads
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
                // Include current user's ID in the team member IDs
                $teamMemberIds[] = AuthHelper::getCurrentUserId();
                // Use qualified column name to work with joins
                $query->whereIn('leads.telecaller_id', $teamMemberIds);
            } else {
                // If no team assigned, only show their own leads
                $query->where('leads.telecaller_id', AuthHelper::getCurrentUserId());
            }
        } elseif (AuthHelper::isTelecaller()) {
            // Telecaller: Can only see their own leads
            $query->where('leads.telecaller_id', AuthHelper::getCurrentUserId());
        }
        
        return $query;
    }

    /**
     * Apply role-based filtering to converted leads queries
     */
    private function applyRoleBasedFilterToConvertedLeads($query)
    {
        $currentUser = AuthHelper::getCurrentUser();
        
        // If no user is logged in, return all converted leads (for admin view)
        if (!$currentUser) {
            return $query;
        }
        
        // Roles that can see all converted leads (same as admin)
        if (RoleHelper::is_admin_or_super_admin() || 
            RoleHelper::is_general_manager() ||
            RoleHelper::is_senior_manager() ||
            RoleHelper::is_admission_counsellor() || 
            RoleHelper::is_finance() || 
            RoleHelper::is_academic_assistant() || 
            RoleHelper::is_post_sales()) {
            // Can see all converted leads
            return $query;
        }
        
        if (AuthHelper::isTeamLead()) {
            // Team Lead: Can see converted leads from leads assigned to them or their team members
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
                // Include current user's ID in the team member IDs
                $teamMemberIds[] = AuthHelper::getCurrentUserId();
                $query->whereHas('lead', function($q) use ($teamMemberIds) {
                    $q->whereIn('telecaller_id', $teamMemberIds);
                });
            } else {
                // If no team assigned, only show converted leads from their own leads
                $query->whereHas('lead', function($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
            }
        } elseif (AuthHelper::isTelecaller()) {
            // Telecaller: Can only see converted leads from leads assigned to them
            $query->whereHas('lead', function($q) {
                $q->where('telecaller_id', AuthHelper::getCurrentUserId());
            });
        }
        
        return $query;
    }

    /**
     * Get summary statistics for auditor dashboard
     */
    private function getAuditorSummaryStats()
    {
        $today = now()->startOfDay();
        $tomorrow = now()->addDay()->startOfDay();

        return [
            'totalLeads' => Lead::count(),
            'totalTelecallers' => User::where('role_id', 3)->count(),
            'totalTeams' => Team::count(),
            'totalConvertedLeads' => ConvertedLead::count(),
            'todaysLeads' => Lead::whereBetween('created_at', [$today, $tomorrow])->count(),
            'todaysConverted' => ConvertedLead::whereBetween('created_at', [$today, $tomorrow])->count(),
            'followupLeads' => Lead::where('lead_status_id', 2)->count(), // Assuming 2 is Follow-up status
            'conversionRate' => $this->getConversionRate(),
        ];
    }

    /**
     * Get detailed telecaller statistics for auditor
     */
    private function getAuditorTelecallerStats()
    {
        return User::select('users.id', 'users.name', 'users.phone', 'users.profile_picture')
            ->selectRaw('COUNT(leads.id) as total_leads')
            ->selectRaw('COUNT(CASE WHEN leads.is_converted = 1 THEN 1 END) as converted_leads')
            ->selectRaw('COUNT(CASE WHEN leads.lead_status_id = 2 THEN 1 END) as followup_leads')
            ->selectRaw('COUNT(CASE WHEN leads.created_at >= DATE(NOW()) THEN 1 END) as todays_leads')
            ->leftJoin('leads', 'users.id', '=', 'leads.telecaller_id')
            ->where('users.role_id', 3)
            ->whereNull('users.deleted_at')
            ->groupBy('users.id', 'users.name', 'users.phone', 'users.profile_picture')
            ->orderByDesc('total_leads')
            ->get()
            ->map(function ($telecaller) {
                $conversionRate = $telecaller->total_leads > 0 
                    ? round(($telecaller->converted_leads / $telecaller->total_leads) * 100, 2) 
                    : 0;
                
                return [
                    'id' => $telecaller->id,
                    'name' => $telecaller->name,
                    'phone' => $telecaller->phone,
                    'profile_picture' => $telecaller->profile_picture,
                    'total_leads' => $telecaller->total_leads,
                    'converted_leads' => $telecaller->converted_leads,
                    'followup_leads' => $telecaller->followup_leads,
                    'todays_leads' => $telecaller->todays_leads,
                    'conversion_rate' => $conversionRate,
                ];
            });
    }

    /**
     * Get detailed team statistics for auditor
     */
    private function getAuditorTeamStats()
    {
        return Team::with('teamLead')
            ->get()
            ->map(function ($team) {
                $teamUserIds = User::where('team_id', $team->id)
                    ->where('role_id', 3)
                    ->pluck('id')
                    ->toArray();

                $totalLeads = Lead::whereIn('telecaller_id', $teamUserIds)->count();
                $convertedLeads = Lead::whereIn('telecaller_id', $teamUserIds)
                    ->where('is_converted', true)
                    ->count();
                $followupLeads = Lead::whereIn('telecaller_id', $teamUserIds)
                    ->where('lead_status_id', 2)
                    ->count();
                
                $today = now()->startOfDay();
                $tomorrow = now()->addDay()->startOfDay();
                $todaysLeads = Lead::whereIn('telecaller_id', $teamUserIds)
                    ->whereBetween('created_at', [$today, $tomorrow])
                    ->count();

                $conversionRate = $totalLeads > 0 
                    ? round(($convertedLeads / $totalLeads) * 100, 2) 
                    : 0;

                $teamMembers = User::where('team_id', $team->id)
                    ->where('role_id', 3)
                    ->count();

                return [
                    'id' => $team->id,
                    'name' => $team->name,
                    'team_lead' => $team->teamLead ? $team->teamLead->name : 'N/A',
                    'total_members' => $teamMembers,
                    'total_leads' => $totalLeads,
                    'converted_leads' => $convertedLeads,
                    'followup_leads' => $followupLeads,
                    'todays_leads' => $todaysLeads,
                    'conversion_rate' => $conversionRate,
                ];
            })
            ->sortByDesc('total_leads')
            ->values();
    }

    /**
     * Get detailed lead statistics for auditor
     */
    private function getAuditorLeadStats()
    {
        $leadStatuses = LeadStatus::withCount('leads')->get();
        
        $leadSources = LeadSource::select('lead_sources.id', 'lead_sources.title')
            ->selectRaw('COUNT(leads.id) as lead_count')
            ->leftJoin('leads', 'lead_sources.id', '=', 'leads.lead_source_id')
            ->whereNull('leads.deleted_at')
            ->groupBy('lead_sources.id', 'lead_sources.title')
            ->orderByDesc('lead_count')
            ->get();

        return [
            'by_status' => $leadStatuses->map(function ($status) {
                return [
                    'id' => $status->id,
                    'title' => $status->title,
                    'count' => $status->leads_count,
                ];
            }),
            'by_source' => $leadSources->map(function ($source) {
                return [
                    'id' => $source->id,
                    'title' => $source->title,
                    'count' => $source->lead_count,
                ];
            }),
        ];
    }

    /**
     * Get converted leads statistics for auditor
     */
    private function getAuditorConvertedLeadsStats()
    {
        $today = now()->startOfDay();
        $tomorrow = now()->addDay()->startOfDay();
        $weekStart = now()->startOfWeek();
        $monthStart = now()->startOfMonth();

        return [
            'total' => ConvertedLead::count(),
            'today' => ConvertedLead::whereBetween('created_at', [$today, $tomorrow])->count(),
            'this_week' => ConvertedLead::where('created_at', '>=', $weekStart)->count(),
            'this_month' => ConvertedLead::where('created_at', '>=', $monthStart)->count(),
            'recent' => ConvertedLead::with(['lead.telecaller' => function($q) {
                $q->select('id', 'name');
            }, 'course'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
        ];
    }

    /**
     * Get follow-up leads statistics for auditor
     */
    private function getAuditorFollowupStats()
    {
        $today = now()->startOfDay();
        $tomorrow = now()->addDay()->startOfDay();

        return [
            'total' => Lead::where('lead_status_id', 2)->count(),
            'today' => Lead::where('lead_status_id', 2)
                ->whereBetween('created_at', [$today, $tomorrow])
                ->count(),
            'overdue' => Lead::where('lead_status_id', 2)
                ->where('followup_date', '<', $today)
                ->count(),
            'upcoming' => Lead::where('lead_status_id', 2)
                ->where('followup_date', '>=', $today)
                ->where('followup_date', '<=', now()->addDays(7))
                ->count(),
            'recent' => Lead::with(['leadStatus', 'telecaller', 'leadSource'])
                ->where('lead_status_id', 2)
                ->orderBy('followup_date', 'asc')
                ->limit(10)
                ->get(),
        ];
    }

    /**
     * Get today's leads for auditor
     */
    private function getAuditorTodaysLeads()
    {
        $today = now()->startOfDay();
        $tomorrow = now()->addDay()->startOfDay();

        return Lead::with(['leadStatus', 'leadSource', 'telecaller', 'team'])
            ->whereBetween('created_at', [$today, $tomorrow])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get charts data for auditor dashboard
     */
    private function getAuditorChartsData()
    {
        // Monthly leads and conversions for line chart
        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();
            
            $monthlyData[] = [
                'month' => $date->format('M Y'),
                'leads' => Lead::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                'converted' => Lead::whereBetween('created_at', [$monthStart, $monthEnd])
                    ->where('is_converted', true)
                    ->count(),
                'followups' => Lead::whereBetween('created_at', [$monthStart, $monthEnd])
                    ->where('lead_status_id', 2)
                    ->count(),
            ];
        }

        // Weekly data (last 7 days)
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();
            
            $weeklyData[] = [
                'day' => $date->format('D M d'),
                'leads' => Lead::whereBetween('created_at', [$dayStart, $dayEnd])->count(),
                'converted' => Lead::whereBetween('created_at', [$dayStart, $dayEnd])
                    ->where('is_converted', true)
                    ->count(),
            ];
        }

        // Top telecallers for bar chart
        $topTelecallersChart = User::select('users.id', 'users.name')
            ->selectRaw('COUNT(leads.id) as lead_count')
            ->leftJoin('leads', 'users.id', '=', 'leads.telecaller_id')
            ->where('users.role_id', 3)
            ->whereNull('users.deleted_at')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('lead_count')
            ->limit(10)
            ->get()
            ->map(function ($t) {
                return [
                    'name' => $t->name,
                    'count' => $t->lead_count,
                ];
            });

        // Lead sources pie chart
        $leadSourcesChart = LeadSource::select('lead_sources.id', 'lead_sources.title')
            ->selectRaw('COUNT(leads.id) as count')
            ->leftJoin('leads', 'lead_sources.id', '=', 'leads.lead_source_id')
            ->whereNull('leads.deleted_at')
            ->groupBy('lead_sources.id', 'lead_sources.title')
            ->orderByDesc('count')
            ->limit(8)
            ->get()
            ->map(function ($source) {
                return [
                    'name' => $source->title,
                    'value' => $source->count,
                ];
            });

        return [
            'monthly' => $monthlyData,
            'weekly' => $weeklyData,
            'top_telecallers' => $topTelecallersChart,
            'lead_sources' => $leadSourcesChart,
        ];
    }

    /**
     * Get sale count - converted leads where the first payment is approved
     * A sale is counted when a converted lead has at least one invoice with the first approved payment
     */
    private function getSaleCount()
    {
        // Get all converted leads with role-based filtering
        $convertedLeadsQuery = ConvertedLead::query();
        $this->applyRoleBasedFilterToConvertedLeads($convertedLeadsQuery);
        
        $convertedLeads = $convertedLeadsQuery->get();
        $saleCount = 0;
        
        foreach ($convertedLeads as $convertedLead) {
            // Get the first approved payment for this student's invoices
            $firstApprovedPayment = Payment::whereHas('invoice', function($query) use ($convertedLead) {
                $query->where('student_id', $convertedLead->id);
            })
            ->where('status', 'Approved')
            ->whereNotNull('approved_date')
            ->orderBy('approved_date', 'asc')
            ->first();
            
            // Count as sale if first payment has been approved
            if ($firstApprovedPayment) {
                $saleCount++;
            }
        }
        
        return $saleCount;
    }

    /**
     * Get weekly sale count - converted leads where the first payment was approved this week
     * A sale is counted when a converted lead has the first approved payment approved this week
     */
    private function getWeeklySaleCount()
    {
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        
        // Get all converted leads with role-based filtering
        $convertedLeadsQuery = ConvertedLead::query();
        $this->applyRoleBasedFilterToConvertedLeads($convertedLeadsQuery);
        
        $convertedLeads = $convertedLeadsQuery->get();
        $weeklySaleCount = 0;
        
        foreach ($convertedLeads as $convertedLead) {
            // Get the first approved payment for this student's invoices
            $firstApprovedPayment = Payment::whereHas('invoice', function($query) use ($convertedLead) {
                $query->where('student_id', $convertedLead->id);
            })
            ->where('status', 'Approved')
            ->whereNotNull('approved_date')
            ->orderBy('approved_date', 'asc')
            ->first();
            
            // Count as sale if first payment was approved this week
            if ($firstApprovedPayment && 
                $firstApprovedPayment->approved_date >= $weekStart && 
                $firstApprovedPayment->approved_date <= $weekEnd) {
                $weeklySaleCount++;
            }
        }
        
        return $weeklySaleCount;
    }
}
