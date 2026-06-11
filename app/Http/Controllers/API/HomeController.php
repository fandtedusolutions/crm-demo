<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\ConvertedLead;
use App\Models\Notification;
use App\Models\User;
use App\Models\MarketingLead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Get home dashboard data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // Get date ranges
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        // Base query for leads (excluding soft deleted)
        $baseLeadQuery = Lead::query();
        
        // Apply role-based filtering
        $this->applyRoleBasedFilter($baseLeadQuery, $user);

        // Total leads count
        $totalLead = (clone $baseLeadQuery)->count();

        // This week total leads
        $thisWeekTotalLead = (clone $baseLeadQuery)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();

        // Converted leads count (all time) - with role-based filtering
        $convertedLeadsQuery = ConvertedLead::query();
        $this->applyRoleBasedFilterToConvertedLeads($convertedLeadsQuery, $user);
        $actualConvertedLeads = $convertedLeadsQuery->count();

        // Not converted leads (active)
        $notConvertedLeads = (clone $baseLeadQuery)
            ->where('is_converted', 0)
            ->count();

        // Conversion rate
        $conversionRate = $totalLead > 0 
            ? round(($actualConvertedLeads / $totalLead) * 100, 2) 
            : 0;

        // Today's leads
        $todaysLead = (clone $baseLeadQuery)
            ->whereDate('created_at', $today)
            ->count();

        // Active leads (not converted)
        $activeLeads = $notConvertedLeads;

        // Active leads this week (created this week and not converted)
        // Get all converted lead IDs (not filtered by role) to check conversion status
        $allConvertedLeadIds = ConvertedLead::pluck('lead_id')->toArray();
        $activeThisWeekLead = (clone $baseLeadQuery)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->whereNotIn('id', $allConvertedLeadIds)
            ->count();

        // Lead status overview with role-based filtering
        $leadStatusOverview = LeadStatus::where('is_active', true)
            ->get()
            ->map(function ($status) use ($user) {
                $leadsQuery = Lead::where('lead_status_id', $status->id)->where('is_converted', 0);
                $this->applyRoleBasedFilter($leadsQuery, $user);
                $leadsCount = $leadsQuery->count();
                
                return [
                    'id' => $status->id,
                    'title' => $status->title,
                    'leads_count' => $leadsCount,
                    'not_converted_leads_count' => $leadsCount, // Same as leads_count for not converted
                    'color' => $status->color ?? null
                ];
            });

        // Recent leads (latest 10) with role-based filtering
        $recentLeadsQuery = Lead::with(['leadStatus', 'leadSource']);
        $this->applyRoleBasedFilter($recentLeadsQuery, $user);
        $recentLeads = $recentLeadsQuery
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($lead) {
                // Format phone with code
                $phone = '';
                if ($lead->code && $lead->phone) {
                    $phone = '+' . $lead->code . ' ' . $lead->phone;
                } elseif ($lead->phone) {
                    $phone = $lead->phone;
                }

                return [
                    'id' => $lead->id,
                    'name' => $lead->title,
                    'lead_status' => $lead->leadStatus ? $lead->leadStatus->title : '',
                    'lead_source' => $lead->leadSource ? $lead->leadSource->title : '',
                    'phone' => $phone,
                    'created_at' => $lead->created_at->format('d-m-Y')
                ];
            });

        // Unread notification count
        $notifications = Notification::forUser($user->id, $user->role_id)->get();
        $unreadNotificationCount = $notifications->filter(function ($notification) use ($user) {
            return !$notification->isReadBy($user->id);
        })->count();

        // Get current token from request (bearer token)
        $currentToken = $request->bearerToken();

        // Get user data using model method
        $userData = $user->getApiUserData($currentToken);

        // Prepare response data
        $data = [
            'count' => [
                'total_lead' => $totalLead,
                'this_week_total_lead' => $thisWeekTotalLead,
                'converted_leads' => $actualConvertedLeads,
                'convertion_rate' => $conversionRate . '%',
                'todays_lead' => $todaysLead,
                'active_leads' => $activeLeads,
                'active_this_week_lead' => $activeThisWeekLead
            ],
            'lead_status_overview' => $leadStatusOverview,
            'recent_leads' => $recentLeads,
            'unread_notification_count' => $unreadNotificationCount,
            'user_data' => $userData
        ];

        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);
    }

    /**
     * Apply role-based filtering to leads queries
     */
    private function applyRoleBasedFilter($query, $user)
    {
        // Roles that can see all leads (admin, super admin, managers, etc.)
        if ($user->role_id == 1 || // Super Admin
            $user->role_id == 2 || // Admin
            $user->is_senior_manager ||
            in_array($user->role_id, [4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16])) { // Other privileged roles
            // Can see all leads
            return $query;
        }

        if ($user->is_team_lead) {
            // Team Lead: Can see their own leads + their team members' leads
            $teamId = $user->team_id;
            if ($teamId) {
                $teamMemberIds = User::where('team_id', $teamId)
                    ->where('role_id', 3)
                    ->whereNull('deleted_at')
                    ->pluck('id')
                    ->toArray();
                $teamMemberIds[] = $user->id;
                $query->whereIn('telecaller_id', $teamMemberIds);
            } else {
                // If no team assigned, only show their own leads
                $query->where('telecaller_id', $user->id);
            }
        } elseif ($user->role_id == 3) {
            // Telecaller: Can only see their own leads
            $query->where('telecaller_id', $user->id);
        }

        return $query;
    }

    /**
     * Apply role-based filtering to converted leads queries
     */
    private function applyRoleBasedFilterToConvertedLeads($query, $user)
    {
        // Roles that can see all converted leads
        if ($user->role_id == 1 || // Super Admin
            $user->role_id == 2 || // Admin
            $user->is_senior_manager ||
            in_array($user->role_id, [4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16])) { // Other privileged roles
            // Can see all converted leads
            return $query;
        }

        if ($user->is_team_lead) {
            // Team Lead: Can see converted leads they created + their team members' converted leads
            $teamId = $user->team_id;
            if ($teamId) {
                $teamMemberIds = User::where('team_id', $teamId)
                    ->where('role_id', 3)
                    ->whereNull('deleted_at')
                    ->pluck('id')
                    ->toArray();
                $teamMemberIds[] = $user->id;
                $query->whereIn('created_by', $teamMemberIds);
            } else {
                // If no team assigned, only show their own converted leads
                $query->where('created_by', $user->id);
            }
        } elseif ($user->role_id == 3) {
            // Telecaller: Can only see converted leads they created
            $query->where('created_by', $user->id);
        }

        return $query;
    }

    /**
     * Get marketing home dashboard data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function marketingHome(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // Check access: Only marketing users, admins, and senior managers can access
        $isMarketing = $user->role_id == 13;
        $isAdminOrManager = $user->role_id == 1 || $user->role_id == 2 || $user->is_senior_manager;

        if (!$isMarketing && !$isAdminOrManager) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied.'
            ], 403);
        }

        // Get date ranges
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        // Base query for marketing leads
        $baseMarketingLeadQuery = MarketingLead::query();
        
        // Apply role-based filtering for marketing leads
        $this->applyRoleBasedFilterToMarketingLeads($baseMarketingLeadQuery, $user);

        // Total marketing leads count
        $totalMarketingLeads = (clone $baseMarketingLeadQuery)->count();

        // Today's marketing leads count
        $todaysMarketingLeads = (clone $baseMarketingLeadQuery)
            ->whereDate('created_at', $today)
            ->count();

        // This week total marketing leads
        $thisWeekTotalLead = (clone $baseMarketingLeadQuery)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();

        // Assigned leads count (all time)
        $assignedLeads = (clone $baseMarketingLeadQuery)
            ->where('is_telecaller_assigned', true)
            ->count();

        // This week assigned leads
        $thisWeekAssigned = (clone $baseMarketingLeadQuery)
            ->where('is_telecaller_assigned', true)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();

        // Not assigned leads count (all time)
        $notAssigned = (clone $baseMarketingLeadQuery)
            ->where('is_telecaller_assigned', false)
            ->count();

        // This week not assigned leads
        $thisWeekNotAssigned = (clone $baseMarketingLeadQuery)
            ->where('is_telecaller_assigned', false)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();

        // Recent marketing leads (latest 10) with role-based filtering
        $recentMarketingLeadsQuery = MarketingLead::with(['marketingBde:id,name', 'assignedTo:id,name', 'lead.leadStatus']);
        $this->applyRoleBasedFilterToMarketingLeads($recentMarketingLeadsQuery, $user);
        $recentMarketingLeads = $recentMarketingLeadsQuery
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($marketingLead) {
                // Format phone with code
                $phone = '';
                if ($marketingLead->code && $marketingLead->phone) {
                    $phone = '+' . $marketingLead->code . ' ' . $marketingLead->phone;
                } elseif ($marketingLead->phone) {
                    $phone = $marketingLead->phone;
                }

                // Format whatsapp with code
                $whatsapp = '';
                if ($marketingLead->whatsapp_code && $marketingLead->whatsapp) {
                    $whatsapp = '+' . $marketingLead->whatsapp_code . ' ' . $marketingLead->whatsapp;
                } elseif ($marketingLead->whatsapp) {
                    $whatsapp = $marketingLead->whatsapp;
                }

                $dateOfVisit = '';
                if ($marketingLead->date_of_visit) {
                    try {
                        $dateOfVisit = Carbon::parse($marketingLead->date_of_visit)->format('d-m-Y');
                    } catch (\Exception $e) {
                        $dateOfVisit = '';
                    }
                }

                return [
                    'id' => $marketingLead->id,
                    'lead_name' => $marketingLead->lead_name,
                    'phone' => $phone,
                    'whatsapp' => $whatsapp,
                    'location' => $marketingLead->location,
                    'date_of_visit' => $dateOfVisit,
                    'lead_type' => $marketingLead->lead_type,
                    'is_telecaller_assigned' => $marketingLead->is_telecaller_assigned ? 1 : 0,
                    'marketing_bde' => $marketingLead->marketingBde ? $marketingLead->marketingBde->name : '',
                    'assigned_to' => $marketingLead->assignedTo ? $marketingLead->assignedTo->name : '',
                    'lead_status' => $marketingLead->lead && $marketingLead->lead->leadStatus ? $marketingLead->lead->leadStatus->title : '',
                    'created_at' => $marketingLead->created_at->format('d-m-Y')
                ];
            });

        // Unread notification count
        $notifications = Notification::forUser($user->id, $user->role_id)->get();
        $unreadNotificationCount = $notifications->filter(function ($notification) use ($user) {
            return !$notification->isReadBy($user->id);
        })->count();

        // Get current token from request (bearer token)
        $currentToken = $request->bearerToken();

        // Get user data using model method
        $userData = $user->getApiUserData($currentToken);

        // Prepare response data
        $data = [
            'count' => [
                'total_marketing_leads' => $totalMarketingLeads,
                'todays_marketing_leads' => $todaysMarketingLeads,
                'this_week_total_lead' => $thisWeekTotalLead,
                'assigned_leads' => $assignedLeads,
                'this_week_assigned' => $thisWeekAssigned,
                'not_assigned' => $notAssigned,
                'this_week_not_assigned' => $thisWeekNotAssigned
            ],
            'recent_marketing_leads' => $recentMarketingLeads,
            'unread_notification_count' => $unreadNotificationCount,
            'user_data' => $userData
        ];

        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);
    }

    /**
     * Apply role-based filtering to marketing leads queries
     */
    private function applyRoleBasedFilterToMarketingLeads($query, $user)
    {
        // Check if user is marketing (role_id == 13)
        $isMarketing = $user->role_id == 13;
        
        // Check if user is admin or senior manager (can see all marketing leads)
        $isAdminOrManager = $user->role_id == 1 || // Super Admin
            $user->role_id == 2 || // Admin
            $user->is_senior_manager;

        // If marketing user, only show their own leads
        if ($isMarketing && !$isAdminOrManager) {
            $query->where('marketing_bde_id', $user->id);
        }

        return $query;
    }
}

