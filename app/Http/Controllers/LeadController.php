<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadDetail;
use App\Models\LeadStatus;
use App\Models\LeadSource;
use App\Models\Country;
use App\Models\Course;
use App\Models\Team;
use App\Models\User;
use App\Models\LeadActivity;
use App\Models\ConvertedLead;
use App\Models\PlusTwoFollowUpQuestionnaire;
use App\Helpers\AuthHelper;
use App\Helpers\PhoneNumberHelper;
use App\Helpers\RoleHelper;
use App\Exports\LeadsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        // Get filter options (cached for better performance - cache for 1 hour)
        $leadStatuses = cache()->remember('lead_statuses_list', 3600, function() {
            return LeadStatus::select('id', 'title')->orderBy('title')->get();
        });
        $leadSources = cache()->remember('lead_sources_list', 3600, function() {
            return LeadSource::select('id', 'title')->orderBy('title')->get();
        });
        $countries = cache()->remember('countries_list', 3600, function() {
            return Country::select('id', 'title')->orderBy('title')->get();
        });
        $courses = cache()->remember('courses_list', 3600, function() {
            return Course::select('id', 'title')->orderBy('title')->get();
        });
        
        $currentUser = AuthHelper::getCurrentUser();
        $isSeniorManager = $currentUser && RoleHelper::is_senior_manager();
        
        // Cache telecallers query based on role
        $cacheKey = 'telecallers_list_' . ($currentUser ? $currentUser->id : 'guest');
        $telecallers = cache()->remember($cacheKey, 1800, function() {
            return User::select('id', 'name')
                      ->where('role_id', 3)
                      ->orderBy('name')
                      ->get();
        });

        // Create lookup arrays
        $leadStatusList = $leadStatuses->pluck('title', 'id')->toArray();
        $leadSourceList = $leadSources->pluck('title', 'id')->toArray();
        $courseName = $courses->pluck('title', 'id')->toArray();
        $telecallerList = $telecallers->pluck('name', 'id')->toArray();

        // Get role flags
        $isTelecaller = $currentUser && $currentUser->role_id == 3;
        $isTeamLead = $currentUser && AuthHelper::isTeamLead();
        $hasB2BLeadRestrictions = $currentUser && $currentUser->is_b2b;
        
        // Filter telecallers based on role
        if ($isTeamLead && !$isSeniorManager) {
            // Team Lead: Show only their team members
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
                $teamMemberIds[] = AuthHelper::getCurrentUserId(); // Include team lead
                $telecallers = User::whereIn('id', $teamMemberIds)->get();
            } else {
                $telecallers = collect([$currentUser]); // Only themselves if no team
            }
        } elseif ($isTelecaller && !$isSeniorManager) {
            // Telecaller: Show only themselves
            $telecallers = collect([$currentUser]);
        }
        
        // Update telecallerList after filtering
        $telecallerList = $telecallers->pluck('name', 'id')->toArray();

        $showTeamTelecallerFilters = \App\Helpers\TeamTelecallerFilterHelper::canUseTeamTelecallerFilters();
        $teams = $showTeamTelecallerFilters
            ? \App\Helpers\TeamTelecallerFilterHelper::getFilterTeams()
            : collect();
        $selectedTeamIds = \App\Helpers\TeamTelecallerFilterHelper::resolveTeamIds($request) ?? [];
        $selectedTelecallerIds = \App\Helpers\TeamTelecallerFilterHelper::resolveTelecallerIds($request) ?? [];
        $filterTelecallers = $showTeamTelecallerFilters
            ? \App\Helpers\TeamTelecallerFilterHelper::getFilterTelecallers($selectedTeamIds ?: null)
            : collect();

        // Get date filters
        $fromDate = $request->get('date_from', now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', now()->format('Y-m-d'));
        
        if ($request->filled('search_key')) {
            // When searching, clear the date values to show search is across all dates
            $fromDate = '';
            $toDate = '';
        }

        // Pre-calculate role checks
        $isAdminOrSuperAdmin = RoleHelper::is_admin_or_super_admin();
        $isTeamLeadRole = RoleHelper::is_team_lead();
        $isGeneralManager = RoleHelper::is_general_manager();
        $isSeniorManager = $currentUser && RoleHelper::is_senior_manager();
        $isTelecallerRole = RoleHelper::is_telecaller();
        $isAcademicAssistant = RoleHelper::is_academic_assistant();
        $isAdmissionCounsellor = RoleHelper::is_admission_counsellor();
        $hasLeadActionPermission = \App\Helpers\PermissionHelper::has_lead_action_permission();

        return view('admin.leads.index', compact(
            'leadStatuses', 'leadSources', 'countries', 'courses', 'telecallers',
            'leadStatusList', 'leadSourceList', 'courseName', 'telecallerList',
            'fromDate', 'toDate', 'isTelecaller', 'isTeamLead', 'hasB2BLeadRestrictions',
            'isAdminOrSuperAdmin', 'isTeamLeadRole', 'isGeneralManager', 'isSeniorManager', 'isTelecallerRole',
            'isAcademicAssistant', 'isAdmissionCounsellor', 'hasLeadActionPermission',
            'showTeamTelecallerFilters', 'teams', 'selectedTeamIds', 'selectedTelecallerIds', 'filterTelecallers'
        ))->with('search_key', $request->search_key);
    }

    /**
     * Display duplicate leads page
     */
    public function duplicateLeads(Request $request)
    {
        // Get filter options (cached for better performance - cache for 1 hour)
        $leadStatuses = cache()->remember('lead_statuses_list', 3600, function() {
            return LeadStatus::select('id', 'title')->orderBy('title')->get();
        });
        $leadSources = cache()->remember('lead_sources_list', 3600, function() {
            return LeadSource::select('id', 'title')->orderBy('title')->get();
        });
        $countries = cache()->remember('countries_list', 3600, function() {
            return Country::select('id', 'title')->orderBy('title')->get();
        });
        $courses = cache()->remember('courses_list', 3600, function() {
            return Course::select('id', 'title')->orderBy('title')->get();
        });
        
        $currentUser = AuthHelper::getCurrentUser();
        $isSeniorManager = $currentUser && RoleHelper::is_senior_manager();
        
        // Cache telecallers query based on role
        $cacheKey = 'telecallers_list_' . ($currentUser ? $currentUser->id : 'guest');
        $telecallers = cache()->remember($cacheKey, 1800, function() {
            return User::select('id', 'name')
                      ->where('role_id', 3)
                      ->orderBy('name')
                      ->get();
        });

        // Create lookup arrays
        $leadStatusList = $leadStatuses->pluck('title', 'id')->toArray();
        $leadSourceList = $leadSources->pluck('title', 'id')->toArray();
        $courseName = $courses->pluck('title', 'id')->toArray();
        $telecallerList = $telecallers->pluck('name', 'id')->toArray();

        // Get role flags
        $isTelecaller = $currentUser && $currentUser->role_id == 3;
        $isTeamLead = $currentUser && AuthHelper::isTeamLead();
        
        // Filter telecallers based on role
        if ($isTeamLead && !$isSeniorManager) {
            // Team Lead: Show only their team members
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
                $teamMemberIds[] = AuthHelper::getCurrentUserId(); // Include team lead
                $telecallers = User::whereIn('id', $teamMemberIds)->get();
            } else {
                $telecallers = collect([$currentUser]); // Only themselves if no team
            }
        } elseif ($isTelecaller && !$isSeniorManager) {
            // Telecaller: Show only themselves
            $telecallers = collect([$currentUser]);
        }
        
        // Update telecallerList after filtering
        $telecallerList = $telecallers->pluck('name', 'id')->toArray();

        // Get date filters
        $fromDate = $request->get('date_from', now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', now()->format('Y-m-d'));
        
        if ($request->filled('search_key')) {
            // When searching, clear the date values to show search is across all dates
            $fromDate = '';
            $toDate = '';
        }

        // Pre-calculate role checks
        $isAdminOrSuperAdmin = RoleHelper::is_admin_or_super_admin();
        $isTeamLeadRole = RoleHelper::is_team_lead();
        $isGeneralManager = RoleHelper::is_general_manager();
        $isSeniorManager = $currentUser && RoleHelper::is_senior_manager();
        $isTelecallerRole = RoleHelper::is_telecaller();
        $isAcademicAssistant = RoleHelper::is_academic_assistant();
        $isAdmissionCounsellor = RoleHelper::is_admission_counsellor();
        $hasLeadActionPermission = \App\Helpers\PermissionHelper::has_lead_action_permission();

        return view('admin.leads.duplicate', compact(
            'leadStatuses', 'leadSources', 'countries', 'courses', 'telecallers',
            'leadStatusList', 'leadSourceList', 'courseName', 'telecallerList',
            'fromDate', 'toDate', 'isTelecaller', 'isTeamLead',
            'isAdminOrSuperAdmin', 'isTeamLeadRole', 'isGeneralManager', 'isSeniorManager', 'isTelecallerRole',
            'isAcademicAssistant', 'isAdmissionCounsellor', 'hasLeadActionPermission'
        ))->with('search_key', $request->search_key);
    }

    /**
     * Build the base query for leads with all filters applied
     * This method is reused by both index view and AJAX endpoint
     */
    private function buildLeadsQuery(Request $request)
    {
        // ULTRA-OPTIMIZED query - minimal selects and relationships
        $query = Lead::select([
            'id', 'title', 'code', 'phone', 'email', 'lead_status_id', 'lead_source_id', 
            'course_id', 'telecaller_id', 'team_id', 'place', 'rating', 'interest_status', 
            'followup_date', 'remarks', 'is_converted', 'created_at', 'updated_at',
            'gender', 'age', 'whatsapp', 'whatsapp_code', 'qualification', 'country_id', 
            'address', 'first_created_at', 'is_b2b' // is_b2b required for Type column (B2B / In House)
        ])
        ->where('is_converted', 0) // Direct condition instead of scope for better performance
        ->with([
            'leadStatus:id,title', 
            'leadSource:id,title', 
            'course:id,title', 
            // Telecaller is used as a reliable fallback for team name.
            // Need telecaller.team_id in the select so Eloquent can load telecaller->team.
            'telecaller:id,name,team_id',
            'telecaller.team:id,name',
            'team:id,name',
            // Load studentDetails with document fields for verification status
            'studentDetails' => function($query) {
                $query->select([
                    'id', 'lead_id', 'status', 'course_id',
                    'sslc_certificate', 'plustwo_certificate', 'ug_certificate',
                    'birth_certificate', 'passport_photo', 'adhar_front', 'adhar_back',
                    'signature', 'other_document',
                    'sslc_verification_status', 'plustwo_verification_status', 'ug_verification_status',
                    'birth_certificate_verification_status', 'passport_photo_verification_status',
                    'adhar_front_verification_status', 'adhar_back_verification_status',
                    'signature_verification_status', 'other_document_verification_status',
                    'reviewed_at'
                ]);
            },
            'studentDetails.sslcCertificates:id,lead_detail_id,verification_status',
            'plusTwoFollowUpQuestionnaire:id,lead_id,created_at'
        ]);

        // Apply filters - optimized date range query
        // Only apply date filters if search_key is not provided (to allow searching across all dates)
        $fromDate = $request->get('date_from', now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', now()->format('Y-m-d'));
        
        if (!$request->filled('search_key')) {
            // Use direct whereBetween for better performance than scope
            $query->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        }

        if ($request->filled('lead_status_id')) {
            $query->where('lead_status_id', $request->lead_status_id);
        }

        if ($request->filled('lead_source_id')) {
            $query->where('lead_source_id', $request->lead_source_id);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        \App\Helpers\TeamTelecallerFilterHelper::applyLeadQueryFilters($query, $request);

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('is_b2b')) {
            $value = $request->is_b2b;
            if ($value === 'b2b') {
                $query->where('is_b2b', 1);
            } elseif ($value === 'in_house') {
                $query->where(function($q) {
                    $q->whereNull('is_b2b')->orWhere('is_b2b', 0);
                });
            }
        }

        // Lead Type filter (only for Admin/Super Admin, Senior Manager, General Manager)
        $currentUser = AuthHelper::getCurrentUser();
        $isAdminOrSuperAdmin = RoleHelper::is_admin_or_super_admin();
        $isSeniorManager = $currentUser && RoleHelper::is_senior_manager();
        $isGeneralManager = RoleHelper::is_general_manager();
        
        if (($isAdminOrSuperAdmin || $isSeniorManager || $isGeneralManager) && $request->filled('lead_type')) {
            $leadType = $request->lead_type;
            if ($leadType === 'pullback') {
                // Remove global scope to show pullbacked leads
                $query->withoutGlobalScope('exclude_pullbacked');
                $query->where('is_pullbacked', 1);
            } elseif ($leadType === 'normal') {
                // Explicitly filter for normal leads (is_pullbacked = 0 or null)
                $query->where(function($q) {
                    $q->whereNull('is_pullbacked')
                      ->orWhere('is_pullbacked', 0);
                });
            }
        }

        // Add search functionality
        if ($request->filled('search_key')) {
            $searchKey = $request->search_key;
            $query->where(function($q) use ($searchKey) {
                $q->where('title', 'LIKE', "%{$searchKey}%")
                  ->orWhere('phone', 'LIKE', "%{$searchKey}%")
                  ->orWhere('email', 'LIKE', "%{$searchKey}%");
            });
        }

        // Role-based lead filtering
        if ($currentUser) {
            // Senior Manager and General Manager: Can see all leads (no filtering)
            if ($isSeniorManager || $isGeneralManager || RoleHelper::is_admin_or_super_admin()) {
                // No role-based filtering - can see all leads
            } elseif (AuthHelper::isTeamLead() == 1) {
                // Team Lead: Can see their own leads + their team members' leads
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
                    // Include current user's ID in the team member IDs
                    $teamMemberIds[] = AuthHelper::getCurrentUserId();  
                    $query->whereIn('telecaller_id', $teamMemberIds);
                } else {
                    // If no team assigned, only show their own leads
                    $query->where('telecaller_id', AuthHelper::getCurrentUserId());
                }
            } elseif (AuthHelper::isTelecaller()) {
                // Telecaller: Can only see their own leads
                $query->where('telecaller_id', AuthHelper::getCurrentUserId());
            }
        }

        return $query;
    }

    /**
     * Build the base query for duplicate leads (same code and phone)
     */
    private function buildDuplicateLeadsQuery(Request $request)
    {
        // First, build a base query with all filters applied (except duplicate detection)
        // Include both converted and non-converted leads
        $baseQuery = Lead::select([
            'id', 'title', 'code', 'phone', 'email', 'lead_status_id', 'lead_source_id', 
            'course_id', 'telecaller_id', 'team_id', 'place', 'rating', 'interest_status', 
            'followup_date', 'remarks', 'is_converted', 'created_at', 'updated_at',
            'gender', 'age', 'whatsapp', 'whatsapp_code', 'qualification', 'country_id', 
            'address', 'first_created_at'
        ])
        ->whereNotNull('code')
        ->whereNotNull('phone');

        // Apply date filters
        $fromDate = $request->get('date_from');
        $toDate = $request->get('date_to');
        
        if ($fromDate && !$request->filled('search_key')) {
            $baseQuery->whereDate('created_at', '>=', $fromDate);
        }
        if ($toDate && !$request->filled('search_key')) {
            $baseQuery->whereDate('created_at', '<=', $toDate);
        }

        // Apply other filters
        if ($request->filled('lead_status_id')) {
            $baseQuery->where('lead_status_id', $request->lead_status_id);
        }

        if ($request->filled('lead_source_id')) {
            $baseQuery->where('lead_source_id', $request->lead_source_id);
        }

        if ($request->filled('course_id')) {
            $baseQuery->where('course_id', $request->course_id);
        }

        if ($request->filled('telecaller_id')) {
            $baseQuery->where('telecaller_id', $request->telecaller_id);
        }

        if ($request->filled('rating')) {
            $baseQuery->where('rating', $request->rating);
        }

        // Lead Type filter
        $currentUser = AuthHelper::getCurrentUser();
        $isAdminOrSuperAdmin = RoleHelper::is_admin_or_super_admin();
        $isSeniorManager = $currentUser && RoleHelper::is_senior_manager();
        $isGeneralManager = RoleHelper::is_general_manager();
        
        if (($isAdminOrSuperAdmin || $isSeniorManager || $isGeneralManager) && $request->filled('lead_type')) {
            $leadType = $request->lead_type;
            if ($leadType === 'pullback') {
                $baseQuery->withoutGlobalScope('exclude_pullbacked');
                $baseQuery->where('is_pullbacked', 1);
            } elseif ($leadType === 'normal') {
                $baseQuery->where(function($q) {
                    $q->whereNull('is_pullbacked')
                      ->orWhere('is_pullbacked', 0);
                });
            }
        }

        // Add search functionality
        if ($request->filled('search_key')) {
            $searchKey = $request->search_key;
            $baseQuery->where(function($q) use ($searchKey) {
                $q->where('title', 'LIKE', "%{$searchKey}%")
                  ->orWhere('phone', 'LIKE', "%{$searchKey}%")
                  ->orWhere('email', 'LIKE', "%{$searchKey}%");
            });
        }

        // Apply role-based filtering
        if ($currentUser) {
            if ($isSeniorManager || $isGeneralManager || RoleHelper::is_admin_or_super_admin()) {
                if ($request->filled('telecaller_id')) {
                    $baseQuery->where('telecaller_id', $request->telecaller_id);
                }
            } elseif (AuthHelper::isTeamLead() == 1) {
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
                    $teamMemberIds[] = AuthHelper::getCurrentUserId();  
                    $baseQuery->whereIn('telecaller_id', $teamMemberIds);
                } else {
                    $baseQuery->where('telecaller_id', AuthHelper::getCurrentUserId());
                }
            } elseif (AuthHelper::isTelecaller()) {
                $baseQuery->where('telecaller_id', AuthHelper::getCurrentUserId());
            }
        }

        // Now find duplicates within the filtered results
        // Get all filtered lead IDs first
        $filteredLeadIds = $baseQuery->pluck('id')->toArray();
        
        if (empty($filteredLeadIds)) {
            $filteredLeadIds = [0]; // Return no results
        }

        // Find code+phone combinations that appear more than once in filtered results
        $duplicateGroups = Lead::select('code', 'phone')
            ->whereIn('id', $filteredLeadIds)
            ->groupBy('code', 'phone')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        // Get all lead IDs that match these duplicate combinations (within filtered results)
        $duplicateLeadIds = [];
        foreach ($duplicateGroups as $group) {
            $leadIds = Lead::where('code', $group->code)
                ->where('phone', $group->phone)
                ->whereIn('id', $filteredLeadIds)
                ->pluck('id')
                ->toArray();
            $duplicateLeadIds = array_merge($duplicateLeadIds, $leadIds);
        }
        
        // If no duplicates found, return empty result
        if (empty($duplicateLeadIds)) {
            $duplicateLeadIds = [0]; // Return no results
        }

        // Build final query with duplicates only (include both converted and non-converted)
        $query = Lead::select([
            'id', 'title', 'code', 'phone', 'email', 'lead_status_id', 'lead_source_id', 
            'course_id', 'telecaller_id', 'team_id', 'place', 'rating', 'interest_status', 
            'followup_date', 'remarks', 'is_converted', 'created_at', 'updated_at',
            'gender', 'age', 'whatsapp', 'whatsapp_code', 'qualification', 'country_id', 
            'address', 'first_created_at'
        ])
        ->whereIn('id', $duplicateLeadIds)
        ->with([
            'leadStatus:id,title', 
            'leadSource:id,title', 
            'course:id,title', 
            'telecaller:id,name', 
            'studentDetails' => function($query) {
                $query->select([
                    'id', 'lead_id', 'status', 'course_id',
                    'sslc_certificate', 'plustwo_certificate', 'ug_certificate',
                    'birth_certificate', 'passport_photo', 'adhar_front', 'adhar_back',
                    'signature', 'other_document',
                    'sslc_verification_status', 'plustwo_verification_status', 'ug_verification_status',
                    'birth_certificate_verification_status', 'passport_photo_verification_status',
                    'adhar_front_verification_status', 'adhar_back_verification_status',
                    'signature_verification_status', 'other_document_verification_status',
                    'reviewed_at'
                ]);
            },
            'studentDetails.sslcCertificates:id,lead_detail_id,verification_status',
            'plusTwoFollowUpQuestionnaire:id,lead_id,created_at'
        ]);

        // All filters are already applied in the base query above
        // No need to apply them again here

        return $query;
    }

    /**
     * AJAX endpoint for DataTables to fetch leads data
     */
    public function getLeadsData(Request $request): JsonResponse
    {
        try {
            set_time_limit(config('timeout.max_execution_time', 300));

            // Build the query with all filters
            $query = $this->buildLeadsQuery($request);

            // Get total count before filtering
            $totalRecords = Lead::where('is_converted', 0)->count();

            // Apply DataTables search (from DataTables search box)
            if ($request->filled('search') && is_array($request->search) && isset($request->search['value']) && !empty($request->search['value'])) {
                $searchValue = $request->search['value'];
                $query->where(function($q) use ($searchValue) {
                    $q->where('title', 'LIKE', "%{$searchValue}%")
                      ->orWhere('phone', 'LIKE', "%{$searchValue}%")
                      ->orWhere('email', 'LIKE', "%{$searchValue}%");
                });
            }

            // Get filtered count
            $filteredCount = $query->count();

            // Pre-calculate role checks (needed for column mapping)
            $currentUser = AuthHelper::getCurrentUser();
            $isAdminOrSuperAdmin = RoleHelper::is_admin_or_super_admin();
            $isTeamLeadRole = RoleHelper::is_team_lead();
            $isGeneralManager = RoleHelper::is_general_manager();
            $isSeniorManager = $currentUser && RoleHelper::is_senior_manager();
            $isTelecallerRole = RoleHelper::is_telecaller();
            $isAcademicAssistant = RoleHelper::is_academic_assistant();
            $isAdmissionCounsellor = RoleHelper::is_admission_counsellor();
            $isPostSales = RoleHelper::is_post_sales();
            $hasLeadActionPermission = \App\Helpers\PermissionHelper::has_lead_action_permission();
            $canViewFirstCreated = $isAdminOrSuperAdmin || $isGeneralManager;
            
            // Check if registration_details column is included
            $hasRegistrationDetails = $isAdminOrSuperAdmin || $isTelecallerRole || $isAcademicAssistant || $isAdmissionCounsellor || $isTeamLeadRole || $isGeneralManager;
            
            // Build dynamic column mapping based on whether registration_details is included
            $columnIndex = 0;
            $columns = [
                $columnIndex++ => 'id', // Index column
                $columnIndex++ => 'id', // Actions column - no sorting
            ];
            
            if ($hasRegistrationDetails) {
                $columns[$columnIndex++] = 'id'; // Registration Details - no sorting
            }
            
            // Continue with remaining columns
            $columns[$columnIndex++] = 'created_at'; // Created At
            if ($canViewFirstCreated) {
                $columns[$columnIndex++] = 'first_created_at'; // First Created At
            }
            $columns[$columnIndex++] = 'is_b2b'; // Type
            $columns[$columnIndex++] = 'team_id'; // Team
            $columns[$columnIndex++] = 'title'; // Name
            $columns[$columnIndex++] = 'id'; // Profile - no sorting
            $columns[$columnIndex++] = 'phone'; // Phone
            $columns[$columnIndex++] = 'email'; // Email
            $columns[$columnIndex++] = 'lead_status_id'; // Status
            $columns[$columnIndex++] = 'interest_status'; // Interest
            $columns[$columnIndex++] = 'rating'; // Rating
            $columns[$columnIndex++] = 'lead_source_id'; // Source
            $columns[$columnIndex++] = 'course_id'; // Course
            $columns[$columnIndex++] = 'telecaller_id'; // Telecaller
            $columns[$columnIndex++] = 'place'; // Place
            $columns[$columnIndex++] = 'followup_date'; // Followup Date
            $columns[$columnIndex++] = 'id'; // Last Reason - no sorting
            $columns[$columnIndex++] = 'remarks'; // Remarks
            $columns[$columnIndex++] = 'marketing_remarks'; // Marketing Remarks
            $columns[$columnIndex++] = 'created_at'; // Date
            $columns[$columnIndex++] = 'created_at'; // Time
            
            // Apply ordering
            $order = $request->get('order', []);
            $orderColumn = isset($order[0]['column']) ? (int)$order[0]['column'] : ($hasRegistrationDetails ? 3 : 2); // Default to created_at
            $orderDir = isset($order[0]['dir']) ? $order[0]['dir'] : 'desc';

            $orderColumnName = $columns[$orderColumn] ?? 'created_at';
            if ($orderColumnName !== 'id') {
                $query->orderBy($orderColumnName, $orderDir);
            } else {
                $query->orderBy('id', 'desc');
            }

            // Apply pagination
            $start = $request->get('start', 0);
            $length = $request->get('length', 25);
            $leads = $query->skip($start)->take($length)->get();
            
            // Pre-calculate role checks (needed for rendering)
            $currentUser = AuthHelper::getCurrentUser();
            $isAdminOrSuperAdmin = RoleHelper::is_admin_or_super_admin();
            $isTeamLeadRole = RoleHelper::is_team_lead();
            $isGeneralManager = RoleHelper::is_general_manager();
            $isSeniorManager = $currentUser && RoleHelper::is_senior_manager();
            $isTelecallerRole = RoleHelper::is_telecaller();
            $isAcademicAssistant = RoleHelper::is_academic_assistant();
            $isAdmissionCounsellor = RoleHelper::is_admission_counsellor();
            $hasLeadActionPermission = \App\Helpers\PermissionHelper::has_lead_action_permission();
            $canViewFirstCreated = $isAdminOrSuperAdmin || $isGeneralManager;
            
            // Check if registration_details column is included
            $hasRegistrationDetails = $isAdminOrSuperAdmin || $isTelecallerRole || $isAcademicAssistant || $isAdmissionCounsellor || $isTeamLeadRole || $isGeneralManager;
            
            // Build dynamic column mapping based on whether registration_details is included
            $columnIndex = 0;
            $columns = [
                $columnIndex++ => 'id', // Index column
                $columnIndex++ => 'id', // Actions column - no sorting
            ];
            
            if ($hasRegistrationDetails) {
                $columns[$columnIndex++] = 'id'; // Registration Details - no sorting
            }
            
            // Continue with remaining columns
            $columns[$columnIndex++] = 'created_at'; // Created At
            if ($canViewFirstCreated) {
                $columns[$columnIndex++] = 'first_created_at'; // First Created At
            }
            $columns[$columnIndex++] = 'is_b2b'; // Type
            $columns[$columnIndex++] = 'team_id'; // Team
            $columns[$columnIndex++] = 'title'; // Name
            $columns[$columnIndex++] = 'id'; // Profile - no sorting
            $columns[$columnIndex++] = 'phone'; // Phone
            $columns[$columnIndex++] = 'email'; // Email
            $columns[$columnIndex++] = 'lead_status_id'; // Status
            $columns[$columnIndex++] = 'interest_status'; // Interest
            $columns[$columnIndex++] = 'rating'; // Rating
            $columns[$columnIndex++] = 'lead_source_id'; // Source
            $columns[$columnIndex++] = 'course_id'; // Course
            $columns[$columnIndex++] = 'telecaller_id'; // Telecaller
            $columns[$columnIndex++] = 'place'; // Place
            $columns[$columnIndex++] = 'followup_date'; // Followup Date
            $columns[$columnIndex++] = 'id'; // Last Reason - no sorting
            $columns[$columnIndex++] = 'remarks'; // Remarks
            $columns[$columnIndex++] = 'marketing_remarks'; // Marketing Remarks
            $columns[$columnIndex++] = 'created_at'; // Date
            $columns[$columnIndex++] = 'created_at'; // Time

            // Pre-calculate profile data
            $fieldLabels = [
                'title' => 'Name', 'gender' => 'Gender', 'age' => 'Age', 'phone' => 'Phone',
                'code' => 'Country Code', 'whatsapp' => 'WhatsApp', 'whatsapp_code' => 'WhatsApp Code',
                'email' => 'Email', 'qualification' => 'Qualification', 'country_id' => 'Country',
                'interest_status' => 'Interest Status', 'lead_status_id' => 'Lead Status',
                'lead_source_id' => 'Lead Source', 'address' => 'Address',
                'telecaller_id' => 'Telecaller', 'team_id' => 'Team', 'place' => 'Place'
            ];
            $requiredFields = array_keys($fieldLabels);
            $totalFields = count($requiredFields);

            // Get course names for lookup
            $courses = cache()->remember('courses_list', 3600, function() {
                return Course::select('id', 'title')->orderBy('title')->get();
            });
            $courseName = $courses->pluck('title', 'id')->toArray();

            // Format data for DataTables
            $data = [];

            // Preload team names for fast + reliable type rendering.
            // We prefer `leads.team_id` but fall back to the telecaller's team_id.
            $teamIds = $leads->pluck('team_id')->filter()->unique()->values();
            $telecallerTeamIds = $leads
                ->map(fn ($lead) => $lead->telecaller?->team_id)
                ->filter()
                ->unique()
                ->values();
            $allTeamIds = $teamIds->merge($telecallerTeamIds)->unique()->values();
            $teamNameMap = $allTeamIds->isNotEmpty()
                ? Team::whereIn('id', $allTeamIds)->pluck('name', 'id')
                : collect();

            foreach ($leads as $index => $lead) {
                // Calculate profile completeness
                $completedFields = 0;
                $missingFields = [];
                
                foreach ($requiredFields as $field) {
                    if (!empty($lead->$field)) {
                        $completedFields++;
                    } else {
                        $missingFields[] = $fieldLabels[$field];
                    }
                }
                
                $profileCompleteness = round(($completedFields / $totalFields) * 100);
                $profileStatus = $profileCompleteness == 100 ? 'complete' : 
                    ($profileCompleteness >= 75 ? 'almost_complete' : 
                    ($profileCompleteness >= 50 ? 'partial' : 'incomplete'));
                $missingFieldsDisplay = implode(', ', array_slice($missingFields, 0, 5));

                // Clean all string data from database before using
                $leadTitle = $this->cleanUtf8($lead->title ?? '');
                $leadEmail = $this->cleanUtf8($lead->email ?? '');
                $leadPlace = $this->cleanUtf8($lead->place ?? '');
                $leadRemarks = $this->cleanUtf8($lead->remarks ?? '');
                $leadMarketingRemarks = $this->cleanUtf8($lead->marketing_remarks ?? '');
                $leadSourceTitle = $this->cleanUtf8($lead->leadSource->title ?? '');
                $leadCourseTitle = $this->cleanUtf8($lead->course->title ?? '');
                $leadTelecallerName = $this->cleanUtf8($lead->telecaller->name ?? 'Unassigned');
                $leadStatusTitle = $this->cleanUtf8($lead->leadStatus->title ?? '');
                $missingFieldsDisplayClean = $this->cleanUtf8($missingFieldsDisplay);
                
                // Build row data with cleaned strings
                $row = [
                    'DT_RowId' => 'lead_' . $lead->id,
                    'DT_RowData' => ['id' => $lead->id],
                    'index' => $start + $index + 1,
                    'actions' => $this->renderActions($lead, $isAdminOrSuperAdmin, $isTeamLeadRole, $isGeneralManager, $hasLeadActionPermission, $isTelecallerRole, $isAcademicAssistant, $isAdmissionCounsellor),
                    'registration_details' => ($isAdminOrSuperAdmin || $isTelecallerRole || $isAcademicAssistant || $isAdmissionCounsellor || $isTeamLeadRole || $isGeneralManager) ? $this->renderRegistrationDetails($lead, $courseName) : '',
                    'created_at' => $lead->created_at->format('d-m-Y h:i A'),
                    'name' => $this->renderName($lead),
                    'profile' => $this->renderProfile($lead, $profileCompleteness, $profileStatus, $missingFieldsDisplayClean, count($missingFields)),
                    'phone' => \App\Helpers\PhoneNumberHelper::display($lead->code, $lead->phone),
                    'email' => $leadEmail ?: '-',
                    'status' => $this->renderStatus($lead),
                    'interest' => $this->renderInterest($lead),
                    'rating' => $this->renderRating($lead),
                    'source' => $this->renderSource($lead, $leadSourceTitle),
                    'course' => $leadCourseTitle ?: '-',
                    'telecaller' => $leadTelecallerName,
                    'place' => $leadPlace ?: '-',
                    'followup_date' => $lead->followup_date ? $lead->followup_date->format('M d, Y') : '-',
                    'last_reason' => '-',
                    'remarks' => $leadRemarks ?: '-',
                    'marketing_remarks' => $leadMarketingRemarks ?: '-',
                    'date' => $lead->created_at->format('M d, Y'),
                    'time' => $lead->created_at->format('h:i A'),
                    // Mobile view data
                    'mobile_view' => $this->renderMobileView($lead, $profileCompleteness, $profileStatus, $missingFieldsDisplayClean, count($missingFields), $courseName, $isAdminOrSuperAdmin, $isTelecallerRole, $isAcademicAssistant, $isAdmissionCounsellor, $hasLeadActionPermission)
                ];

                if ($canViewFirstCreated) {
                    $row['first_created_at'] = $lead->first_created_at
                        ? $lead->first_created_at->format('d-m-Y h:i A')
                        : '-';
                }

                $isB2B = (int) ($lead->is_b2b ?? 0) === 1;
                $leadType = $isB2B ? 'B2B' : 'In House';
                $teamId = $lead->team_id ?? $lead->telecaller?->team_id;
                $leadTeamName = $this->cleanUtf8($teamId ? ($teamNameMap->get($teamId) ?? null) : null);
                $row['type'] = $leadType;
                $row['team'] = $leadTeamName ?: '-';
                $data[] = $row;
            }

            // Recursively clean all data for UTF-8 encoding
            $cleanedData = $this->cleanDataForJson($data);

            // Build response array
            $responseData = [
                'draw' => intval($request->get('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredCount,
                'data' => $cleanedData
            ];

            // Try to encode JSON, if it fails, do aggressive cleaning
            $jsonFlags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
            if (defined('JSON_INVALID_UTF8_IGNORE')) {
                $jsonFlags |= JSON_INVALID_UTF8_IGNORE;
            }
            
            $jsonData = @json_encode($responseData, $jsonFlags);
            
            if ($jsonData === false) {
                // If encoding still fails, try one more aggressive clean
                $cleanedData = $this->aggressiveCleanData($data);
                $responseData['data'] = $cleanedData;
                $jsonData = @json_encode($responseData, $jsonFlags);
                
                if ($jsonData === false) {
                    // Last resort - remove all problematic data
                    $responseData['data'] = [];
                    $jsonData = json_encode($responseData, $jsonFlags);
                }
            }

            // Decode and re-encode to ensure proper JsonResponse
            $decoded = json_decode($jsonData, true);
            return response()->json($decoded, 200, [], $jsonFlags);

        } catch (\Exception $e) {
            Log::error('Error fetching leads data: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'draw' => intval($request->get('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'An error occurred while fetching leads data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * AJAX endpoint for DataTables to fetch duplicate leads data
     */
    public function getDuplicateLeadsData(Request $request): JsonResponse
    {
        try {
            set_time_limit(config('timeout.max_execution_time', 300));

            // Build the query with all filters
            $query = $this->buildDuplicateLeadsQuery($request);

            // Get total count of duplicate leads using the same logic as buildDuplicateLeadsQuery
            // Build base query with all filters (include both converted and non-converted)
            $baseQuery = Lead::whereNotNull('code')
                ->whereNotNull('phone');

            // Apply date filters
            $fromDate = $request->get('date_from');
            $toDate = $request->get('date_to');
            
            if ($fromDate && !$request->filled('search')) {
                $baseQuery->whereDate('created_at', '>=', $fromDate);
            }
            if ($toDate && !$request->filled('search')) {
                $baseQuery->whereDate('created_at', '<=', $toDate);
            }

            // Apply other filters
            if ($request->filled('lead_status_id')) {
                $baseQuery->where('lead_status_id', $request->lead_status_id);
            }
            if ($request->filled('lead_source_id')) {
                $baseQuery->where('lead_source_id', $request->lead_source_id);
            }
            if ($request->filled('course_id')) {
                $baseQuery->where('course_id', $request->course_id);
            }
            if ($request->filled('telecaller_id')) {
                $baseQuery->where('telecaller_id', $request->telecaller_id);
            }
            if ($request->filled('rating')) {
                $baseQuery->where('rating', $request->rating);
            }

            // Apply role-based filtering
            $currentUser = AuthHelper::getCurrentUser();
            if ($currentUser) {
                $isSeniorManager = $currentUser && RoleHelper::is_senior_manager();
                $isGeneralManager = RoleHelper::is_general_manager();
                
                if (!($isSeniorManager || $isGeneralManager || RoleHelper::is_admin_or_super_admin())) {
                    if (AuthHelper::isTeamLead() == 1) {
                        $teamId = $currentUser->team_id;
                        if ($teamId) {
                            $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
                            $teamMemberIds[] = AuthHelper::getCurrentUserId();  
                            $baseQuery->whereIn('telecaller_id', $teamMemberIds);
                        } else {
                            $baseQuery->where('telecaller_id', AuthHelper::getCurrentUserId());
                        }
                    } elseif (AuthHelper::isTelecaller()) {
                        $baseQuery->where('telecaller_id', AuthHelper::getCurrentUserId());
                    }
                }
            }

            // Get filtered lead IDs
            $filteredLeadIds = $baseQuery->pluck('id')->toArray();
            
            if (empty($filteredLeadIds)) {
                $totalRecords = 0;
            } else {
                // Find duplicates within filtered results
                $duplicateGroups = Lead::select('code', 'phone')
                    ->whereIn('id', $filteredLeadIds)
                    ->groupBy('code', 'phone')
                    ->havingRaw('COUNT(*) > 1')
                    ->get();
                
                $totalRecords = 0;
                foreach ($duplicateGroups as $group) {
                    $count = Lead::where('code', $group->code)
                        ->where('phone', $group->phone)
                        ->whereIn('id', $filteredLeadIds)
                        ->count();
                    $totalRecords += $count;
                }
            }

            // Apply DataTables search (from DataTables search box)
            if ($request->filled('search') && is_array($request->search) && isset($request->search['value']) && !empty($request->search['value'])) {
                $searchValue = $request->search['value'];
                $query->where(function($q) use ($searchValue) {
                    $q->where('title', 'LIKE', "%{$searchValue}%")
                      ->orWhere('phone', 'LIKE', "%{$searchValue}%")
                      ->orWhere('email', 'LIKE', "%{$searchValue}%");
                });
                
                // After search, re-check for duplicates to ensure we only show leads that are still duplicates
                // Get the IDs that match the search
                $searchedLeadIds = (clone $query)->pluck('id')->toArray();
                
                if (empty($searchedLeadIds)) {
                    $searchedLeadIds = [0];
                } else {
                    // Find code+phone combinations that still appear more than once after search
                    $duplicateGroupsAfterSearch = Lead::select('code', 'phone')
                        ->whereIn('id', $searchedLeadIds)
                        ->groupBy('code', 'phone')
                        ->havingRaw('COUNT(*) > 1')
                        ->get();
                    
                    // Get only the IDs that are still duplicates
                    $stillDuplicateIds = [];
                    foreach ($duplicateGroupsAfterSearch as $group) {
                        $leadIds = Lead::where('code', $group->code)
                            ->where('phone', $group->phone)
                            ->whereIn('id', $searchedLeadIds)
                            ->pluck('id')
                            ->toArray();
                        $stillDuplicateIds = array_merge($stillDuplicateIds, $leadIds);
                    }
                    
                    if (empty($stillDuplicateIds)) {
                        $searchedLeadIds = [0];
                    } else {
                        $searchedLeadIds = $stillDuplicateIds;
                    }
                }
                
                // Rebuild query with only still-duplicate IDs (include both converted and non-converted)
                $query = Lead::select([
                    'id', 'title', 'code', 'phone', 'email', 'lead_status_id', 'lead_source_id', 
                    'course_id', 'telecaller_id', 'team_id', 'place', 'rating', 'interest_status', 
                    'followup_date', 'remarks', 'is_converted', 'created_at', 'updated_at',
                    'gender', 'age', 'whatsapp', 'whatsapp_code', 'qualification', 'country_id', 
                    'address', 'first_created_at'
                ])
                ->whereIn('id', $searchedLeadIds)
                ->with([
                    'leadStatus:id,title', 
                    'leadSource:id,title', 
                    'course:id,title', 
                    'telecaller:id,name', 
                    'studentDetails' => function($query) {
                        $query->select([
                            'id', 'lead_id', 'status', 'course_id',
                            'sslc_certificate', 'plustwo_certificate', 'ug_certificate',
                            'birth_certificate', 'passport_photo', 'adhar_front', 'adhar_back',
                            'signature', 'other_document',
                            'sslc_verification_status', 'plustwo_verification_status', 'ug_verification_status',
                            'birth_certificate_verification_status', 'passport_photo_verification_status',
                            'adhar_front_verification_status', 'adhar_back_verification_status',
                            'signature_verification_status', 'other_document_verification_status',
                            'reviewed_at'
                        ]);
                    },
                    'studentDetails.sslcCertificates:id,lead_detail_id,verification_status',
                    'plusTwoFollowUpQuestionnaire:id,lead_id,created_at'
                ]);
            }

            // Get filtered count
            $filteredCount = $query->count();

            // Pre-calculate role checks (same as getLeadsData)
            $currentUser = AuthHelper::getCurrentUser();
            $isAdminOrSuperAdmin = RoleHelper::is_admin_or_super_admin();
            $isTeamLeadRole = RoleHelper::is_team_lead();
            $isGeneralManager = RoleHelper::is_general_manager();
            $isSeniorManager = $currentUser && RoleHelper::is_senior_manager();
            $isTelecallerRole = RoleHelper::is_telecaller();
            $isAcademicAssistant = RoleHelper::is_academic_assistant();
            $isAdmissionCounsellor = RoleHelper::is_admission_counsellor();
            $isPostSales = RoleHelper::is_post_sales();
            $hasLeadActionPermission = \App\Helpers\PermissionHelper::has_lead_action_permission();
            $canViewFirstCreated = $isAdminOrSuperAdmin || $isGeneralManager;
            
            $hasRegistrationDetails = $isAdminOrSuperAdmin || $isTelecallerRole || $isAcademicAssistant || $isAdmissionCounsellor || $isTeamLeadRole || $isGeneralManager;
            
            // Build dynamic column mapping (same as getLeadsData)
            $columnIndex = 0;
            $columns = [
                $columnIndex++ => 'id',
                $columnIndex++ => 'id',
            ];
            
            if ($hasRegistrationDetails) {
                $columns[$columnIndex++] = 'id';
            }
            
            $columns[$columnIndex++] = 'created_at';
            if ($canViewFirstCreated) {
                $columns[$columnIndex++] = 'first_created_at';
            }
            $columns[$columnIndex++] = 'title';
            $columns[$columnIndex++] = 'id';
            $columns[$columnIndex++] = 'phone';
            $columns[$columnIndex++] = 'email';
            $columns[$columnIndex++] = 'lead_status_id';
            $columns[$columnIndex++] = 'interest_status';
            $columns[$columnIndex++] = 'rating';
            $columns[$columnIndex++] = 'is_converted';
            $columns[$columnIndex++] = 'lead_source_id';
            $columns[$columnIndex++] = 'course_id';
            $columns[$columnIndex++] = 'telecaller_id';
            $columns[$columnIndex++] = 'place';
            $columns[$columnIndex++] = 'followup_date';
            $columns[$columnIndex++] = 'id';
            $columns[$columnIndex++] = 'remarks';
            $columns[$columnIndex++] = 'marketing_remarks';
            $columns[$columnIndex++] = 'created_at';
            $columns[$columnIndex++] = 'created_at';

            // Apply sorting - group duplicates together by code and phone, then by created_at
            if ($request->filled('order') && is_array($request->order)) {
                // First order by code and phone to group duplicates together
                $query->orderBy('code', 'asc')
                      ->orderBy('phone', 'asc');
                
                // Then apply user's sorting preference
                foreach ($request->order as $order) {
                    $columnIndex = intval($order['column']);
                    $dir = $order['dir'];
                    if (isset($columns[$columnIndex]) && $columns[$columnIndex] !== 'id') {
                        $query->orderBy($columns[$columnIndex], $dir);
                    }
                }
                
                // Finally order by created_at to show duplicates in chronological order
                $query->orderBy('created_at', 'desc');
            } else {
                // Default: Group by code and phone, then by created_at
                $query->orderBy('code', 'asc')
                      ->orderBy('phone', 'asc')
                      ->orderBy('created_at', 'desc');
            }

            // Apply pagination
            $start = $request->get('start', 0);
            $length = $request->get('length', 25);
            if ($length > 0) {
                $query->skip($start)->take($length);
            }

            // Execute query
            $leads = $query->get();

            // Pre-calculate profile data (same as getLeadsData)
            $fieldLabels = [
                'title' => 'Name', 'gender' => 'Gender', 'age' => 'Age', 'phone' => 'Phone',
                'code' => 'Country Code', 'whatsapp' => 'WhatsApp', 'whatsapp_code' => 'WhatsApp Code',
                'email' => 'Email', 'qualification' => 'Qualification', 'country_id' => 'Country',
                'interest_status' => 'Interest Status', 'lead_status_id' => 'Lead Status',
                'lead_source_id' => 'Lead Source', 'address' => 'Address',
                'telecaller_id' => 'Telecaller', 'team_id' => 'Team', 'place' => 'Place'
            ];
            $requiredFields = array_keys($fieldLabels);
            $totalFields = count($requiredFields);

            // Get course names for lookup
            $courses = cache()->remember('courses_list', 3600, function() {
                return Course::select('id', 'title')->orderBy('title')->get();
            });
            $courseName = $courses->pluck('title', 'id')->toArray();

            // Build response data (same structure as getLeadsData)
            $data = [];
            foreach ($leads as $index => $lead) {
                // Calculate profile completeness
                $completedFields = 0;
                $missingFields = [];
                
                foreach ($requiredFields as $field) {
                    if (!empty($lead->$field)) {
                        $completedFields++;
                    } else {
                        $missingFields[] = $fieldLabels[$field];
                    }
                }
                
                $profileCompleteness = round(($completedFields / $totalFields) * 100);
                $profileStatus = $profileCompleteness == 100 ? 'complete' : 
                    ($profileCompleteness >= 75 ? 'almost_complete' : 
                    ($profileCompleteness >= 50 ? 'partial' : 'incomplete'));
                $missingFieldsDisplay = implode(', ', array_slice($missingFields, 0, 5));

                // Clean all string data from database before using
                $leadTitle = $this->cleanUtf8($lead->title ?? '');
                $leadEmail = $this->cleanUtf8($lead->email ?? '');
                $leadPlace = $this->cleanUtf8($lead->place ?? '');
                $leadRemarks = $this->cleanUtf8($lead->remarks ?? '');
                $leadMarketingRemarks = $this->cleanUtf8($lead->marketing_remarks ?? '');
                $leadSourceTitle = $this->cleanUtf8($lead->leadSource->title ?? '');
                $leadCourseTitle = $this->cleanUtf8($lead->course->title ?? '');
                $leadTelecallerName = $this->cleanUtf8($lead->telecaller->name ?? 'Unassigned');
                $leadStatusTitle = $this->cleanUtf8($lead->leadStatus->title ?? '');
                $missingFieldsDisplayClean = $this->cleanUtf8($missingFieldsDisplay);
                
                // Build row data with cleaned strings
                $row = [
                    'DT_RowId' => 'lead_' . $lead->id,
                    'DT_RowData' => ['id' => $lead->id],
                    'index' => $start + $index + 1,
                    'actions' => $this->renderActions($lead, $isAdminOrSuperAdmin, $isTeamLeadRole, $isGeneralManager, $hasLeadActionPermission, $isTelecallerRole, $isAcademicAssistant, $isAdmissionCounsellor),
                    'registration_details' => ($isAdminOrSuperAdmin || $isTelecallerRole || $isAcademicAssistant || $isAdmissionCounsellor || $isTeamLeadRole || $isGeneralManager) ? $this->renderRegistrationDetails($lead, $courseName) : '',
                    'created_at' => $lead->created_at->format('d-m-Y h:i A'),
                    'name' => $this->renderName($lead),
                    'profile' => $this->renderProfile($lead, $profileCompleteness, $profileStatus, $missingFieldsDisplayClean, count($missingFields)),
                    'phone' => \App\Helpers\PhoneNumberHelper::display($lead->code, $lead->phone),
                    'email' => $leadEmail ?: '-',
                    'status' => $this->renderStatus($lead),
                    'interest' => $this->renderInterest($lead),
                    'rating' => $this->renderRating($lead),
                    'converted' => $this->renderConvertedStatus($lead),
                    'source' => $this->renderSource($lead, $leadSourceTitle),
                    'course' => $leadCourseTitle ?: '-',
                    'telecaller' => $leadTelecallerName,
                    'place' => $leadPlace ?: '-',
                    'followup_date' => $lead->followup_date ? $lead->followup_date->format('M d, Y') : '-',
                    'last_reason' => '-',
                    'remarks' => $leadRemarks ?: '-',
                    'marketing_remarks' => $leadMarketingRemarks ?: '-',
                    'date' => $lead->created_at->format('M d, Y'),
                    'time' => $lead->created_at->format('h:i A'),
                    // Mobile view data
                    'mobile_view' => $this->renderMobileView($lead, $profileCompleteness, $profileStatus, $missingFieldsDisplayClean, count($missingFields), $courseName, $isAdminOrSuperAdmin, $isTelecallerRole, $isAcademicAssistant, $isAdmissionCounsellor, $hasLeadActionPermission)
                ];

                if ($canViewFirstCreated) {
                    $row['first_created_at'] = $lead->first_created_at
                        ? $lead->first_created_at->format('d-m-Y h:i A')
                        : '-';
                }
                
                $data[] = $row;
            }

            // Clean data for JSON
            $cleanedData = $this->cleanDataForJson($data);

            // Build response array
            $responseData = [
                'draw' => intval($request->get('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredCount,
                'data' => $cleanedData
            ];

            $jsonFlags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
            if (defined('JSON_INVALID_UTF8_IGNORE')) {
                $jsonFlags |= JSON_INVALID_UTF8_IGNORE;
            }
            
            $jsonData = @json_encode($responseData, $jsonFlags);
            
            if ($jsonData === false) {
                $cleanedData = $this->aggressiveCleanData($data);
                $responseData['data'] = $cleanedData;
                $jsonData = @json_encode($responseData, $jsonFlags);
                
                if ($jsonData === false) {
                    $responseData['data'] = [];
                    $jsonData = json_encode($responseData, $jsonFlags);
                }
            }

            $decoded = json_decode($jsonData, true);
            return response()->json($decoded, 200, [], $jsonFlags);

        } catch (\Exception $e) {
            Log::error('Error fetching duplicate leads data: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'draw' => intval($request->get('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'An error occurred while fetching duplicate leads data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Render actions column HTML
     */
    private function renderActions($lead, $isAdminOrSuperAdmin, $isTeamLeadRole, $isGeneralManager, $hasLeadActionPermission, $isTelecallerRole, $isAcademicAssistant, $isAdmissionCounsellor)
    {
        $html = '<div class="btn-group" role="group">';
        $html .= '<a href="javascript:void(0);" class="btn btn-sm btn-outline-primary" onclick="show_large_modal(\'' . route('leads.ajax-show', $lead->id) . '\', \'View Lead\')" title="View Lead"><i class="ti ti-eye"></i></a>';
        
        if ($isAdminOrSuperAdmin || $isTeamLeadRole || $isGeneralManager) {
            $html .= '<a href="javascript:void(0);" class="btn btn-sm btn-outline-secondary" onclick="show_ajax_modal(\'' . route('leads.ajax-edit', $lead->id) . '\', \'Edit Lead\')" title="Edit Lead"><i class="ti ti-edit"></i></a>';
        }
        
        if ($hasLeadActionPermission) {
            $html .= '<a href="javascript:void(0);" class="btn btn-sm btn-outline-success" onclick="show_ajax_modal(\'' . route('leads.status-update', $lead->id) . '\', \'Update Status\')" title="Update Status"><i class="ti ti-arrow-up"></i></a>';
            
            if (!$lead->is_converted && $lead->studentDetails && (strtolower($lead->studentDetails->status ?? '') === 'approved')) {
                $html .= '<a href="javascript:void(0);" class="btn btn-sm btn-outline-warning" onclick="show_ajax_modal(\'' . route('leads.convert', $lead->id) . '\', \'Convert Lead\')" title="Convert Lead"><i class="ti ti-refresh"></i></a>';
            }
        }
        
        $html .= '</div><br><hr><br><div class="btn-group" role="group">';
        
        if ($lead->lead_status_id == 6) {
            $html .= '<a href="https://docs.google.com/forms/d/e/1FAIpQLSchtc8xlKUJehZNmzoKTkRvwLwk4-SGjzKSHM2UFToAhgdTlQ/viewform?usp=sf_link" target="_blank" class="btn btn-sm btn-outline-info" title="Demo Conduction Form"><i class="ti ti-file-text"></i></a>';
        }
        
        /*
        if ($lead->phone && is_telecaller()) {
            $currentUserId = session('user_id') ?? (\App\Helpers\AuthHelper::getCurrentUserId() ?? 0);
            if ($currentUserId > 0) {
                $html .= '<button class="btn btn-sm btn-outline-success voxbay-call-btn" data-lead-id="' . $lead->id . '" data-telecaller-id="' . $currentUserId . '" title="Call Lead"><i class="ti ti-phone"></i></button>';
            }
        }
        
        $html .= '<a href="' . route('leads.call-logs', $lead) . '" class="btn btn-sm btn-outline-info" title="View Call Logs"><i class="ti ti-phone-call"></i></a>';
        */
        
        // WhatsApp button
        if ($lead->phone && $lead->code) {
            $phoneNumber = preg_replace('/[^0-9]/', '', $lead->code . $lead->phone);
            $leadName = $this->cleanUtf8($lead->title ?? '');
            $message = urlencode('Hi ' . $leadName);
            $whatsappUrl = 'https://wa.me/' . $phoneNumber . '?text=' . $message;
            $html .= '<a href="' . htmlspecialchars($whatsappUrl, ENT_QUOTES, 'UTF-8') . '" target="_blank" class="btn btn-sm btn-outline-success" title="WhatsApp"><i class="ti ti-brand-whatsapp"></i></a>';
        }
        
        if ($isAdminOrSuperAdmin || $isGeneralManager) {
            $html .= '<a href="javascript:void(0);" class="btn btn-sm btn-outline-danger" onclick="delete_modal(\'' . route('leads.destroy', $lead->id) . '\')" title="Delete Lead"><i class="ti ti-trash"></i></a>';
        }
        
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Render registration details column HTML
     */
    private function renderRegistrationDetails($lead, $courseName)
    {
        if ($lead->studentDetails) {
            $html = '<div class="d-flex flex-column gap-1">';
            $html .= '<span class="badge bg-success">Form Submitted</span>';
            $html .= '<small class="text-muted">' . ($lead->studentDetails->_course_title ?? ($courseName[$lead->studentDetails->course_id] ?? 'Unknown Course')) . '</small>';
            
            // Document verification status
            $docVerificationStatus = $lead->studentDetails->getDocumentVerificationStatus();
            if ($docVerificationStatus !== null) {
                $badgeClass = $docVerificationStatus === 'verified' ? 'bg-success' : 'bg-warning';
                $badgeText = $docVerificationStatus === 'verified' ? 'Documents Verified' : 'Documents Pending';
                $html .= '<span class="badge ' . $badgeClass . ' mt-1">' . $badgeText . '</span>';
            }
            
            if ($lead->studentDetails->status) {
                $statusClass = $lead->studentDetails->status == 'approved' ? 'bg-success' : 
                    ($lead->studentDetails->status == 'rejected' ? 'bg-danger' : 'bg-warning');
                $html .= '<span class="badge ' . $statusClass . '">' . ucfirst($lead->studentDetails->status) . '</span>';
                
                if (in_array($lead->studentDetails->status, ['approved', 'rejected'], true) && $lead->studentDetails->reviewed_at) {
                    $statusLabel = $lead->studentDetails->status == 'approved' ? 'Approved' : 'Rejected';
                    $formattedDate = $lead->studentDetails->reviewed_at->format('M d, Y h:i A');
                    $html .= '<small class="text-muted d-block">' . $statusLabel . ' on ' . $formattedDate . '</small>';
                }
            }
            
            $html .= '<a href="' . route('leads.registration-details', $lead->id) . '" class="btn btn-sm btn-outline-primary mt-1" title="View Registration Details"><i class="ti ti-eye me-1"></i>View Details</a>';
            $html .= '</div>';
            return $html;
        }

        if ($lead->plusTwoFollowUpQuestionnaire) {
            $html = '<div class="d-flex flex-column gap-1">';
            $html .= '<span class="badge bg-success">Questionnaire Submitted</span>';
            $html .= '<small class="text-muted">Plus Two Follow-Up</small>';
            $html .= '<small class="text-muted d-block">Submitted on ' . $lead->plusTwoFollowUpQuestionnaire->created_at->format('M d, Y h:i A') . '</small>';
            $html .= '<a href="' . route('leads.plus-two-questionnaire', $lead->id) . '" class="btn btn-sm btn-outline-primary mt-1" title="View Questionnaire Details"><i class="ti ti-eye me-1"></i>View Details</a>';
            $html .= '</div>';
            return $html;
        }

        if ((int) $lead->lead_source_id === PlusTwoFollowUpQuestionnaire::LEAD_SOURCE_ID) {
            return $this->renderPlusTwoQuestionnaireActions($lead, true);
        }
        
        // Render registration form links based on course_id
        $routeNames = \App\Helpers\LeadRegistrationRouteHelper::courseRegistrationRouteNames();
        $titles = \App\Helpers\LeadRegistrationRouteHelper::courseRegistrationTitles();

        if (isset($routeNames[$lead->course_id])) {
            $routeInfo = [
                'route' => $routeNames[$lead->course_id],
                'title' => $titles[$lead->course_id] ?? 'Registration',
            ];
            $html = '<div class="d-flex gap-1">';
            $html .= '<a href="' . route($routeInfo['route'], $lead->id) . '" target="_blank" class="btn btn-sm btn-outline-warning" title="Open ' . $routeInfo['title'] . ' Registration Form"><i class="ti ti-external-link"></i></a>';
            $html .= '<button type="button" class="btn btn-sm btn-outline-info copy-link-btn" data-url="' . route($routeInfo['route'], $lead->id) . '" title="Copy ' . $routeInfo['title'] . ' Registration Link"><i class="ti ti-copy"></i></button>';
            $html .= '</div>';
            return $html;
        }
        
        return '';
    }

    /**
     * Render Plus Two questionnaire action buttons for lead_source_id = 13.
     */
    private function renderPlusTwoQuestionnaireActions($lead, bool $compact = false)
    {
        if ($lead->plusTwoFollowUpQuestionnaire) {
            $html = '<div class="d-flex flex-column gap-1' . ($compact ? ' mt-1' : '') . '">';
            $html .= '<span class="badge bg-success">Questionnaire Submitted</span>';
            if (!$compact) {
                $html .= '<small class="text-muted d-block">Submitted on ' . $lead->plusTwoFollowUpQuestionnaire->created_at->format('M d, Y h:i A') . '</small>';
            }
            $html .= '<a href="' . route('leads.plus-two-questionnaire', $lead->id) . '" class="btn btn-sm btn-outline-primary' . ($compact ? '' : ' mt-1') . '" title="View Questionnaire Details"><i class="ti ti-eye' . ($compact ? '' : ' me-1') . '"></i>' . ($compact ? '' : 'View Details') . '</a>';
            $html .= '</div>';
            return $html;
        }

        $html = '<div class="d-flex gap-1' . ($compact ? ' mt-1' : '') . '">';
        $html .= '<a href="' . route('public.lead.plus-two-follow-up.register', $lead->id) . '" target="_blank" class="btn btn-sm btn-outline-warning" title="Open Plus Two Follow-Up Questionnaire"><i class="ti ti-external-link"></i></a>';
        $html .= '<button type="button" class="btn btn-sm btn-outline-info copy-link-btn" data-url="' . route('public.lead.plus-two-follow-up.register', $lead->id) . '" title="Copy Plus Two Follow-Up Questionnaire Link"><i class="ti ti-copy"></i></button>';
        $html .= '</div>';
        return $html;
    }

    /**
     * Render source column with Plus Two questionnaire actions when applicable.
     */
    private function renderSource($lead, $sourceTitle)
    {
        $title = htmlspecialchars($sourceTitle ?: '-', ENT_QUOTES, 'UTF-8');
        $html = '<div class="d-flex flex-column gap-1">';
        $html .= '<span>' . $title . '</span>';

        if ((int) $lead->lead_source_id === PlusTwoFollowUpQuestionnaire::LEAD_SOURCE_ID) {
            $html .= $this->renderPlusTwoQuestionnaireActions($lead, true);
        }

        $html .= '</div>';
        return $html;
    }

    /**
     * Render name column HTML
     */
    private function renderName($lead)
    {
        $title = $this->cleanUtf8($lead->title ?? '');
        $firstChar = mb_substr($title, 0, 1, 'UTF-8');
        $html = '<div class="d-flex align-items-center">';
        $html .= '<div class="avtar avtar-s rounded-circle bg-light-primary me-2 d-flex align-items-center justify-content-center">';
        $html .= '<span class="f-16 fw-bold text-primary">' . htmlspecialchars(strtoupper($firstChar), ENT_QUOTES, 'UTF-8') . '</span>';
        $html .= '</div>';
        $html .= '<div><h6 class="mb-0">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h6></div>';
        $html .= '</div>';
        return $html;
    }

    /**
     * Render profile column HTML
     */
    private function renderProfile($lead, $profileCompleteness, $profileStatus, $missingFieldsDisplay, $missingFieldsCount)
    {
        $html = '<div class="d-flex align-items-center">';
        
        if ($profileCompleteness < 100) {
            $statusClass = $profileStatus == 'incomplete' ? 'bg-danger' : 
                ($profileStatus == 'partial' ? 'bg-warning' : 'bg-info');
            
            $html .= '<div class="me-2">';
            $html .= '<div class="progress" style="width: 60px; height: 8px;">';
            $html .= '<div class="progress-bar ' . $statusClass . '" role="progressbar" style="width: ' . $profileCompleteness . '%" aria-valuenow="' . $profileCompleteness . '" aria-valuemin="0" aria-valuemax="100"></div>';
            $html .= '</div></div>';
            
            $html .= '<span class="badge ' . $statusClass . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Missing: ' . e($missingFieldsDisplay) . ($missingFieldsCount > 5 ? '...' : '') . '">' . $profileCompleteness . '%</span>';
        } else {
            $html .= '<span class="badge bg-success"><i class="ti ti-check"></i> Complete</span>';
        }
        
        $html .= '</div>';
        return $html;
    }

    /**
     * Render status column HTML
     */
    private function renderStatus($lead)
    {
        $status = $lead->leadStatus;
        if (!$status) {
            return '<span class="badge bg-secondary">Unknown</span>';
        }

        $statusTitle = $this->cleanUtf8($status->title ?? '');
        return '<span class="badge ' . \App\Helpers\StatusHelper::getLeadStatusColorClass($status->id) . '">' . htmlspecialchars($statusTitle, ENT_QUOTES, 'UTF-8') . '</span>';
    }

    /**
     * Render interest column HTML
     */
    private function renderInterest($lead)
    {
        if ($lead->interest_status) {
            $color = $lead->interest_status == 1 ? 'danger' : ($lead->interest_status == 2 ? 'warning' : 'info');
            $label = $lead->interest_status == 1 ? 'Hot' : ($lead->interest_status == 2 ? 'Warm' : 'Cold');
            return '<span class="badge bg-' . $color . '">' . $label . '</span>';
        }
        return '<span class="badge bg-secondary">Not Set</span>';
    }

    /**
     * Render rating column HTML
     */
    private function renderRating($lead)
    {
        if ($lead->rating) {
            return '<span class="badge bg-primary">' . $lead->rating . '/10</span>';
        }
        return '<span class="badge bg-secondary">Not Rated</span>';
    }

    /**
     * Render converted status column HTML
     */
    private function renderConvertedStatus($lead)
    {
        if ($lead->is_converted == 1) {
            return '<span class="badge bg-success"><i class="ti ti-check me-1"></i>Converted</span>';
        }
        return '<span class="badge bg-secondary"><i class="ti ti-x me-1"></i>Not Converted</span>';
    }

    /**
     * Clean UTF-8 string to prevent encoding errors
     */
    private function cleanUtf8($string)
    {
        if (is_null($string)) {
            return null;
        }
        
        if (!is_string($string)) {
            return $string;
        }
        
        // Remove invalid UTF-8 characters and replace with empty string
        $string = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
        
        // Remove control characters except newlines and tabs
        $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $string);
        
        // Remove any remaining invalid UTF-8 sequences
        $string = filter_var($string, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH);
        
        // Final check - ensure valid UTF-8
        if (!mb_check_encoding($string, 'UTF-8')) {
            $string = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
            $string = iconv('UTF-8', 'UTF-8//IGNORE', $string);
        }
        
        return $string;
    }
    
    /**
     * Recursively clean all strings in an array/object for UTF-8 encoding
     */
    private function cleanDataForJson($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'cleanDataForJson'], $data);
        } elseif (is_string($data)) {
            return $this->cleanUtf8($data);
        } elseif (is_object($data)) {
            $cleaned = [];
            foreach ($data as $key => $value) {
                $cleaned[$key] = $this->cleanDataForJson($value);
            }
            return $cleaned;
        }
        return $data;
    }
    
    /**
     * Aggressive cleaning for data that still fails encoding
     */
    private function aggressiveCleanData($data)
    {
        return array_map(function($row) {
            $cleanedRow = [];
            foreach ($row as $key => $value) {
                if (is_string($value)) {
                    // Remove all non-printable characters
                    $value = preg_replace('/[\x00-\x1F\x7F-\x9F]/u', '', $value);
                    // Convert to UTF-8 and ignore invalid sequences
                    $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                    $value = iconv('UTF-8', 'UTF-8//IGNORE//TRANSLIT', $value);
                } elseif (is_array($value)) {
                    $value = $this->aggressiveCleanData([$value])[0];
                }
                $cleanedRow[$key] = $value;
            }
            return $cleanedRow;
        }, $data);
    }

    private function renderMobileView($lead, $profileCompleteness, $profileStatus, $missingFieldsDisplay, $missingFieldsCount, $courseName, $isAdminOrSuperAdmin, $isTelecallerRole, $isAcademicAssistant, $isAdmissionCounsellor, $hasLeadActionPermission)
    {
        // This will be rendered on the frontend for mobile view
        // Return data as JSON-encoded string that will be parsed on frontend
        $data = [
            'id' => $lead->id,
            'title' => $this->cleanUtf8($lead->title),
            'created_at' => $lead->created_at->format('d-m-Y h:i A'),
            'phone' => \App\Helpers\PhoneNumberHelper::display($lead->code, $lead->phone),
            'phone_number' => $lead->phone ?? '',
            'code' => $lead->code ?? '',
            'email' => $this->cleanUtf8($lead->email ?? '-'),
            'status' => $lead->leadStatus ? [
                'id' => $lead->leadStatus->id,
                'title' => $this->cleanUtf8($lead->leadStatus->title),
                'color_class' => \App\Helpers\StatusHelper::getLeadStatusColorClass($lead->leadStatus->id)
            ] : [
                'id' => null,
                'title' => 'Unknown',
                'color_class' => 'bg-secondary'
            ],
            'interest' => [
                'status' => $lead->interest_status,
                'label' => $lead->interest_status ? ($lead->interest_status == 1 ? 'Hot' : ($lead->interest_status == 2 ? 'Warm' : 'Cold')) : 'Not Set',
                'color' => $lead->interest_status ? ($lead->interest_status == 1 ? 'danger' : ($lead->interest_status == 2 ? 'warning' : 'info')) : 'secondary'
            ],
            'rating' => $lead->rating ? $lead->rating . '/10' : 'Not Rated',
            'telecaller' => $this->cleanUtf8($lead->telecaller->name ?? 'Unassigned'),
            'course' => $this->cleanUtf8($lead->course->title ?? '-'),
            'source' => $this->cleanUtf8($lead->leadSource->title ?? '-'),
            'plus_two_questionnaire_html' => (int) $lead->lead_source_id === PlusTwoFollowUpQuestionnaire::LEAD_SOURCE_ID
                ? $this->renderPlusTwoQuestionnaireActions($lead, true)
                : '',
            'place' => $this->cleanUtf8($lead->place ?? '-'),
            'followup_date' => $lead->followup_date ? $lead->followup_date->format('M d, Y') : null,
            'remarks' => $this->cleanUtf8($lead->remarks),
            'profile' => [
                'completeness' => $profileCompleteness,
                'status' => $profileStatus,
                'missing_fields' => $this->cleanUtf8($missingFieldsDisplay),
                'missing_count' => $missingFieldsCount
            ],
            'student_details' => $lead->studentDetails ? [
                'status' => $lead->studentDetails->status,
                'course_title' => $this->cleanUtf8($lead->studentDetails->_course_title ?? ($courseName[$lead->studentDetails->course_id] ?? 'Unknown Course')),
                'document_verification_status' => $lead->studentDetails->getDocumentVerificationStatus(),
                'reviewed_at' => $lead->studentDetails->reviewed_at ? $lead->studentDetails->reviewed_at->format('d-m-Y h:i A') : null,
            ] : null,
            'plus_two_questionnaire' => $lead->plusTwoFollowUpQuestionnaire ? [
                'submitted_at' => $lead->plusTwoFollowUpQuestionnaire->created_at->format('d-m-Y h:i A'),
            ] : null,
            'lead_source_id' => $lead->lead_source_id,
            'course_id' => $lead->course_id,
            'lead_status_id' => $lead->lead_status_id,
            'is_converted' => $lead->is_converted,
            'routes' => [
                'view' => route('leads.ajax-show', $lead->id),
                'edit' => route('leads.ajax-edit', $lead->id),
                'status_update' => route('leads.status-update', $lead->id),
                'convert' => route('leads.convert', $lead->id),
                'call_logs' => route('leads.call-logs', $lead),
                'delete' => route('leads.destroy', $lead->id),
                'registration_details' => route('leads.registration-details', $lead->id),
                'plus_two_questionnaire_details' => route('leads.plus-two-questionnaire', $lead->id),
                'registration_link' => $this->getRegistrationLinkRoute($lead)
            ],
            'permissions' => [
                'can_edit' => $isAdminOrSuperAdmin || RoleHelper::is_team_lead() || RoleHelper::is_general_manager(),
                'can_delete' => $isAdminOrSuperAdmin || RoleHelper::is_general_manager(),
                'can_update_status' => $hasLeadActionPermission,
                'can_convert' => !$lead->is_converted && $lead->studentDetails && (strtolower($lead->studentDetails->status ?? '') === 'approved'),
                'can_view_registration' => $isAdminOrSuperAdmin || $isTelecallerRole || $isAcademicAssistant || $isAdmissionCounsellor,
                'can_view_call_logs' => true, // Show to everyone, including admission counsellor and post sales
                'can_call' => $lead->phone && is_telecaller(),
                'telecaller_id' => $lead->phone && is_telecaller() ? (session('user_id') ?? (\App\Helpers\AuthHelper::getCurrentUserId() ?? 0)) : 0
            ]
        ];
        
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Get registration link route for a lead based on course_id
     */
    private function getRegistrationLinkRoute($lead)
    {
        if ((int) $lead->lead_source_id === PlusTwoFollowUpQuestionnaire::LEAD_SOURCE_ID && !$lead->plusTwoFollowUpQuestionnaire) {
            return route('public.lead.plus-two-follow-up.register', $lead->id);
        }

        $url = \App\Helpers\LeadRegistrationRouteHelper::registrationUrlForLead($lead);

        return $url !== '' ? $url : null;
    }

    /**
     * Export leads to Excel
     * Uses the same filtering logic as the index method
     */
    public function export(Request $request)
    {
        // Set execution time limit for this operation
        set_time_limit(config('timeout.max_execution_time', 300));
        
        // Reuse the comprehensive query builder to ensure filters match the leads list
        $query = $this->buildLeadsQuery($request);

        // Capture the effective date range for the filename (mirrors buildLeadsQuery defaults)
        $fromDate = null;
        $toDate = null;
        if (!$request->filled('search_key')) {
            $fromDate = $request->get('date_from', now()->subDays(7)->format('Y-m-d'));
            $toDate = $request->get('date_to', now()->format('Y-m-d'));
        }

        // Fetch all matching leads ordered by creation date (newest first)
        $leads = $query->orderBy('created_at', 'desc')->get();

        // Resolve source/course labels by id (includes soft-deleted rows). Use full rows + fallbacks
        // so exports never rely on PhpSpreadsheet coercing short/numeric titles into plain numbers.
        $sourceIds = $leads->pluck('lead_source_id')->filter()->unique()->values();
        $courseIds = $leads->pluck('course_id')->filter()->unique()->values();

        $sourceTitles = [];
        if ($sourceIds->isNotEmpty()) {
            LeadSource::withTrashed()
                ->whereIn('id', $sourceIds)
                ->get(['id', 'title', 'description'])
                ->each(function (LeadSource $row) use (&$sourceTitles) {
                    $label = trim((string) ($row->title ?? ''));
                    if ($label === '') {
                        $label = trim((string) ($row->description ?? ''));
                    }
                    $sourceTitles[(int) $row->id] = $label;
                });
        }

        $courseTitles = [];
        if ($courseIds->isNotEmpty()) {
            Course::withTrashed()
                ->whereIn('id', $courseIds)
                ->get(['id', 'title', 'code'])
                ->each(function (Course $row) use (&$courseTitles) {
                    $label = trim((string) ($row->title ?? ''));
                    if ($label === '') {
                        $label = trim((string) ($row->code ?? ''));
                    }
                    $courseTitles[(int) $row->id] = $label;
                });
        }

        // Generate filename with date range
        $filename = 'leads_export_' . ($fromDate ? $fromDate : 'all') . '_to_' . ($toDate ? $toDate : 'all') . '_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(new LeadsExport($leads, $sourceTitles, $courseTitles), $filename);
    }

    /**
     * Display registration form submitted leads
     */
    public function registrationFormSubmittedLeads(Request $request)
    {
        // Set execution time limit for this operation
        set_time_limit(config('timeout.max_execution_time', 300));
        
        $query = Lead::select([
            'id', 'title', 'code', 'phone', 'email', 'lead_status_id', 'lead_source_id', 
            'course_id', 'telecaller_id', 'team_id', 'place', 'rating', 'interest_status', 
            'followup_date', 'remarks', 'is_converted', 'created_at', 'updated_at'
        ])
        ->with([
            'leadStatus:id,title', 
            'leadSource:id,title', 
            'course:id,title', 
            'telecaller:id,name', 
            'studentDetails' => function($query) {
                $query->select([
                    'id', 'lead_id', 'status', 'course_id',
                    'sslc_certificate', 'plustwo_certificate', 'ug_certificate',
                    'birth_certificate', 'passport_photo', 'adhar_front', 'adhar_back',
                    'signature', 'other_document',
                    'sslc_verification_status', 'plustwo_verification_status', 'ug_verification_status',
                    'birth_certificate_verification_status', 'passport_photo_verification_status',
                    'adhar_front_verification_status', 'adhar_back_verification_status',
                    'signature_verification_status', 'other_document_verification_status',
                    'reviewed_at'
                ]);
            },
            'studentDetails.sslcCertificates:id,lead_detail_id,verification_status',
            'leadActivities' => function($query) {
                $query->select('id', 'lead_id', 'reason', 'created_at', 'activity_type')
                      ->whereNotNull('reason')
                      ->where('reason', '!=', '')
                      ->orderBy('created_at', 'desc');
            }
        ])
        ->whereHas('studentDetails') // Only leads that have submitted registration forms
        ->notConverted()
        ->notDropped();

        // Apply filters
        // Only apply date filters if explicitly provided
        $fromDate = $request->get('date_from', '');
        $toDate = $request->get('date_to', '');
        
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->byDateRange($fromDate, $toDate);
        }

        if ($request->filled('lead_status_id')) {
            $query->where('lead_status_id', $request->lead_status_id);
        }

        if ($request->filled('lead_source_id')) {
            $query->where('lead_source_id', $request->lead_source_id);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('telecaller_id')) {
            $query->where('telecaller_id', $request->telecaller_id);
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('is_b2b')) {
            $value = $request->is_b2b;
            if ($value === 'b2b') {
                $query->where('is_b2b', 1);
            } elseif ($value === 'in_house') {
                $query->where(function($q) {
                    $q->whereNull('is_b2b')->orWhere('is_b2b', 0);
                });
            }
        }

        // Add registration status filter - default to 'pending' if not provided
        $registrationStatus = $request->get('registration_status', 'pending');
        if ($registrationStatus === 'approved') {
            $query->whereHas('studentDetails', function($q) {
                $q->where('status', 'approved');
            });
        } elseif ($registrationStatus === 'rejected') {
            $query->whereHas('studentDetails', function($q) {
                $q->where('status', 'rejected');
            });
        } elseif ($registrationStatus === 'pending') {
            $query->whereHas('studentDetails', function($q) {
                $q->where('status', 'pending');
            });
        }

        // Add search functionality
        if ($request->filled('search_key')) {
            $searchKey = $request->search_key;
            $query->where(function($q) use ($searchKey) {
                $q->where('title', 'LIKE', "%{$searchKey}%")
                  ->orWhere('phone', 'LIKE', "%{$searchKey}%")
                  ->orWhere('email', 'LIKE', "%{$searchKey}%");
            });
        }

        $currentUser = AuthHelper::getCurrentUser();
        $isTeamLead = $currentUser ? AuthHelper::isTeamLead() : false;
        $isTelecaller = $currentUser && $currentUser->role_id == 3;
        $isSeniorManager = $currentUser ? RoleHelper::is_senior_manager() : false;
        $canEditLead = RoleHelper::is_admin_or_super_admin() || 
                       RoleHelper::is_general_manager() || 
                       RoleHelper::is_team_lead() || 
                       RoleHelper::is_senior_manager();
        $hasLeadActionPermission = \App\Helpers\PermissionHelper::has_lead_action_permission();
        
        // Role-based lead filtering
        if ($currentUser && !$isSeniorManager) {
            if ($isTeamLead) {
                // Team Lead: Can see their own leads + their team members' leads
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
                    // Include current user's ID in the team member IDs
                    $teamMemberIds[] = AuthHelper::getCurrentUserId();  
                    $query->whereIn('telecaller_id', $teamMemberIds);
                } else {
                    // If no team assigned, only show their own leads
                    $query->where('telecaller_id', AuthHelper::getCurrentUserId());
                }
            } elseif ($isTelecaller) {
                // Telecaller: Can only see their own leads
                $query->where('telecaller_id', AuthHelper::getCurrentUserId());
            } elseif ($request->filled('telecaller_id') && !$isTelecaller) {
                // Admin/Super Admin: Can filter by specific telecaller
                $query->where('telecaller_id', $request->telecaller_id);
            }
        }

        // Get all leads without pagination
        $leads = $query->orderBy('id', 'desc')->get();

        // Get filter options (optimized with select only needed fields)
        $leadStatuses = LeadStatus::select('id', 'title')->get();
        $leadSources = LeadSource::select('id', 'title')->get();
        $countries = Country::select('id', 'title')->get();
        $courses = Course::select('id', 'title')->get();
        $telecallers = User::select('id', 'name')->nonMarketingTelecallers()->get();

        // Create lookup arrays
        $leadStatusList = $leadStatuses->pluck('title', 'id')->toArray();
        $leadSourceList = $leadSources->pluck('title', 'id')->toArray();
        $courseName = $courses->pluck('title', 'id')->toArray();
        $telecallerList = $telecallers->pluck('name', 'id')->toArray();

        // Filter telecallers based on role
        if ($isTeamLead && !$isSeniorManager) {
            // Team Lead: Show only their team members
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
                $teamMemberIds[] = AuthHelper::getCurrentUserId(); // Include team lead
                $telecallers = User::whereIn('id', $teamMemberIds)->get();
            } else {
                $telecallers = collect([$currentUser]); // Only themselves if no team
            }
        } elseif ($isTelecaller && !$isSeniorManager) {
            // Telecaller: Show only themselves
            $telecallers = collect([$currentUser]);
        }
        // Admin/Super Admin: Show all telecallers (already loaded above)
        
        // Update telecallerList after filtering
        $telecallerList = $telecallers->pluck('name', 'id')->toArray();

        // Get counts for tabs (using same base query without registration_status filter)
        $baseQuery = Lead::select(['id'])
            ->whereHas('studentDetails')
            ->notConverted()
            ->notDropped();
        
        // Apply role-based filtering
        if ($currentUser && !$isSeniorManager) {
            if ($isTeamLead) {
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
                    $teamMemberIds[] = AuthHelper::getCurrentUserId();
                    $baseQuery->whereIn('telecaller_id', $teamMemberIds);
                } else {
                    $baseQuery->where('telecaller_id', AuthHelper::getCurrentUserId());
                }
            } elseif ($isTelecaller) {
                $baseQuery->where('telecaller_id', AuthHelper::getCurrentUserId());
            }
        }
        
        // Apply date filter if explicitly provided
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $baseQuery->byDateRange($fromDate, $toDate);
        }
        
        // Get counts for each status
        $allCount = (clone $baseQuery)->count();
        $pendingCount = (clone $baseQuery)->whereHas('studentDetails', function($q) {
            $q->where('status', 'pending');
        })->count();
        $rejectedCount = (clone $baseQuery)->whereHas('studentDetails', function($q) {
            $q->where('status', 'rejected');
        })->count();
        $approvedCount = (clone $baseQuery)->whereHas('studentDetails', function($q) {
            $q->where('status', 'approved');
        })->count();

        return view('admin.leads.registration-form-submitted', compact(
            'leads', 'leadStatuses', 'leadSources', 'countries', 'courses', 'telecallers',
            'leadStatusList', 'leadSourceList', 'courseName', 'telecallerList',
            'fromDate', 'toDate', 'isTelecaller', 'isTeamLead',
            'allCount', 'pendingCount', 'rejectedCount', 'approvedCount',
            'canEditLead', 'hasLeadActionPermission'
        ))->with('search_key', $request->search_key);
    }

    /**
     * Display follow-up leads (status = 2)
     */
    public function followupLeads(Request $request)
    {
        $isTelecaller = AuthHelper::isTelecaller();
        $isTeamLead = AuthHelper::isTeamLead();
        $isSeniorManager = RoleHelper::is_senior_manager();

        // Base query for follow-up leads (status = 2)
        $query = Lead::select([
            'id', 'title', 'code', 'phone', 'email', 'lead_status_id', 'lead_source_id', 
            'course_id', 'telecaller_id', 'team_id', 'place', 'rating', 'interest_status', 
            'followup_date', 'remarks', 'is_converted', 'created_at', 'updated_at'
        ])
        ->with([
            'leadStatus:id,title', 
            'leadSource:id,title', 
            'course:id,title', 
            'telecaller:id,name', 
            'studentDetails:id,lead_id,status,course_id',
            'leadActivities' => function($query) {
                $query->select('id', 'lead_id', 'reason', 'created_at', 'activity_type')
                      ->whereNotNull('reason')
                      ->where('reason', '!=', '')
                      ->orderBy('created_at', 'desc');
            }
        ])
        ->where('lead_status_id', 2)
        ->notConverted()
        ->notDropped();

        // Apply filters
        if ($request->filled('search_key')) {
            $searchTerm = $request->search_key;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('phone', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('lead_source_id')) {
            $query->where('lead_source_id', $request->lead_source_id);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        if ($request->filled('telecaller_id')) {
            $query->where('telecaller_id', $request->telecaller_id);
        }

        // Role-based filtering (skip for senior managers)
        if (!$isSeniorManager) {
            if ($isTelecaller && !$isTeamLead) {
                // Telecaller: Can only see their own leads
                $query->where('telecaller_id', AuthHelper::getCurrentUserId());
            } elseif ($isTeamLead) {
                // Team Lead: Can see leads from their team
                $teamId = AuthHelper::getCurrentUser()->team_id ?? null;
                if ($teamId) {
                    $query->whereHas('telecaller', function($q) use ($teamId) {
                        $q->where('team_id', $teamId);
                    });
                }

                // Admin/Super Admin: Can filter by specific telecaller
                if ($request->filled('telecaller_id')) {
                    $query->where('telecaller_id', $request->telecaller_id);
                }
            }
        }

        // Order by follow-up date: current date first, then tomorrow, then future dates, then past dates
        $query->orderByRaw("
            CASE 
                WHEN DATE(followup_date) = CURDATE() THEN 1
                WHEN DATE(followup_date) = DATE_ADD(CURDATE(), INTERVAL 1 DAY) THEN 2
                WHEN DATE(followup_date) > CURDATE() THEN 3
                ELSE 4
            END,
            followup_date ASC
        ");

        // Get all follow-up leads without pagination
        $leads = $query->get();

        // Get filter options (optimized with select only needed fields)
        $leadStatuses = LeadStatus::select('id', 'title')->get();
        $leadSources = LeadSource::select('id', 'title')->get();
        $countries = Country::select('id', 'title')->get();
        $courses = Course::select('id', 'title')->get();
        $telecallers = User::select('id', 'name')->nonMarketingTelecallers()->get();

        return view('admin.leads.followup', compact('leads', 'leadStatuses', 'leadSources', 'countries', 'courses', 'telecallers', 'isTelecaller', 'isTeamLead'));
    }

    public function create()
    {
        $currentUser = AuthHelper::getCurrentUser();
        $isTeamLead = $currentUser && AuthHelper::isTeamLead();
        $isTelecaller = $currentUser && $currentUser->role_id == 3;
        $isSeniorManager = $currentUser && RoleHelper::is_senior_manager();
        $isGeneralManager = RoleHelper::is_general_manager();
        
        // Filter telecallers based on role
        if ($isTeamLead) {
            // Team Lead: Show only their team members
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
                $teamMemberIds[] = AuthHelper::getCurrentUserId(); // Include team lead
                $telecallers = User::whereIn('id', $teamMemberIds)->get();
            } else {
                $telecallers = collect([$currentUser]); // Only themselves if no team
            }
        } elseif ($isTelecaller && !$isSeniorManager) {
            // Regular Telecaller: Show only themselves
            $telecallers = collect([$currentUser]);
        } else {
            // Admin/Super Admin/Senior Manager/General Manager: Show all telecallers (excluding marketing teams)
            $telecallers = User::nonMarketingTelecallers()->get();
        }
        
        $leadStatuses = LeadStatus::where('is_active', true)->get();
        $leadSources = LeadSource::where('is_active', true)->get();
        $countries = Country::where('is_active', true)->get();
        $courses = Course::where('is_active', true)->get();
        
        // Filter teams based on role
        if ($isTeamLead) {
            // Team Lead: Show only their team
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teams = Team::where('id', $teamId)->get();
            } else {
                $teams = collect(); // No teams if not assigned to any team
            }
        } elseif ($isTelecaller && !$isSeniorManager) {
            // Regular Telecaller: Show only their team
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teams = Team::where('id', $teamId)->get();
            } else {
                $teams = collect(); // No teams if not assigned to any team
            }
        } else {
            // Admin/Super Admin/Senior Manager/General Manager: Show all teams (excluding marketing teams)
            $teams = Team::nonMarketing()->get();
        }
        
        $country_codes = get_country_code();

        return view('admin.leads.create', compact(
            'telecallers', 'leadStatuses', 'leadSources', 'countries', 'courses', 'teams', 'country_codes'
        ));
    }

    public function ajax_add()
    {
        $currentUser = AuthHelper::getCurrentUser();
        $isTeamLead = $currentUser && AuthHelper::isTeamLead();
        $isTelecaller = $currentUser && $currentUser->role_id == 3;
        $isSeniorManager = $currentUser && RoleHelper::is_senior_manager();
        $isGeneralManager = RoleHelper::is_general_manager();
        
        // Filter telecallers based on role
        if ($isTeamLead && !$isSeniorManager) {
            // Team Lead: Show only their team members
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
                $teamMemberIds[] = AuthHelper::getCurrentUserId(); // Include team lead
                $telecallers = User::whereIn('id', $teamMemberIds)->get();
            } else {
                $telecallers = collect([$currentUser]); // Only themselves if no team
            }
        } elseif ($isTelecaller && !$isSeniorManager) {
            // Regular Telecaller: Show only themselves
            $telecallers = collect([$currentUser]);
        } else {
            // Admin/Super Admin/Senior Manager/General Manager: Show all telecallers (excluding marketing teams)
            $telecallers = User::nonMarketingTelecallers()->get();
        }
        
        $leadStatuses = LeadStatus::where('is_active', true)->get();
        $leadSources = LeadSource::where('is_active', true)->get();
        $countries = Country::where('is_active', true)->get();
        $courses = Course::where('is_active', true)->get();
        
        // Filter teams based on role (same logic as bulk upload)
        if ($isTeamLead && !$isSeniorManager) {
            // Team Lead (not senior manager): Show only their team
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teams = Team::where('id', $teamId)->where('is_active', true)->get();
            } else {
                $teams = collect(); // No teams if not assigned to any team
            }
        } elseif ($isSeniorManager || RoleHelper::is_admin_or_super_admin() || $isGeneralManager) {
            // Senior Manager/General Manager/Admin/Super Admin: Show all teams (excluding marketing teams)
            $teams = Team::where('is_active', true)->nonMarketing()->get();
        } elseif ($isTelecaller) {
            // Regular Telecaller: Show only their team
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teams = Team::where('id', $teamId)->where('is_active', true)->get();
            } else {
                $teams = collect(); // No teams if not assigned to any team
            }
        } else {
            // Default: Show all teams (excluding marketing teams)
            $teams = Team::where('is_active', true)->nonMarketing()->get();
        }
        
        $country_codes = get_country_code();

        return view('admin.leads.add', compact(
            'telecallers', 'leadStatuses', 'leadSources', 'countries', 'courses', 'teams', 'country_codes'
        ));
    }

    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email| max:255',
            'code' => 'required|string|max:10',
            'whatsapp_code' => 'nullable|string|max:10',
            'whatsapp' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'age' => 'nullable|integer|min:1|max:999',
            'place' => 'nullable|string|max:255',
            'qualification' => 'nullable|string|max:255',
            'lead_status_id' => 'required|exists:lead_statuses,id',
            'lead_source_id' => 'required|exists:lead_sources,id',
            'country_id' => 'nullable|exists:countries,id',
            'course_id' => 'required|exists:courses,id',
            'team_id' => 'required|exists:teams,id',
            'telecaller_id' => 'required|exists:users,id',
            'address' => 'nullable|string|max:500',
            'followup_date' => 'nullable|date',
            'add_date' => 'nullable|date',
            'add_time' => 'nullable|date_format:H:i',
            'remarks' => 'nullable|string|max:1000',
            'is_b2b' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->back()->with('message_danger', $firstError)->withInput();
        }

        // Check for duplicate lead (code + phone + course_id combination)
        $existingLead = Lead::findDuplicateByPhoneAndCourse(
            $request->code,
            $request->phone,
            (int) $request->course_id
        );

        if ($existingLead) {
            return redirect()->back()
                ->with('message_danger', 'A lead with this phone number and course combination already exists.')
                ->withInput();
        }

        $leadData = $request->all();
        
        // Set default values
        $leadData['lead_status_id'] = $leadData['lead_status_id'] ?? 1;
        $leadData['add_date'] = $leadData['add_date'] ?? date('Y-m-d');
        $leadData['add_time'] = $leadData['add_time'] ?? date('H:i');
        
        // Get interest_status from lead_status
        $leadStatus = LeadStatus::find($leadData['lead_status_id']);
        $leadData['interest_status'] = $leadStatus ? $leadStatus->interest_status : null;
        
        $currentUserId = AuthHelper::getCurrentUserId();

        try {
            $lead = DB::transaction(function () use ($leadData, $request, $currentUserId) {
                $leadData['created_by'] = $currentUserId;
                $leadData['updated_by'] = $currentUserId;
                $leadData['first_created_at'] = now();
                
                // Handle is_b2b logic
                if (RoleHelper::is_admin_or_super_admin() && $request->has('is_b2b')) {
                    // Admin can manually set is_b2b
                    $leadData['is_b2b'] = $request->is_b2b ? 1 : 0;
                } elseif (isset($leadData['telecaller_id'])) {
                    // Auto-set is_b2b based on telecaller
                    $telecaller = User::find($leadData['telecaller_id']);
                    $leadData['is_b2b'] = $telecaller && $telecaller->is_b2b ? 1 : 0;
                } else {
                    $leadData['is_b2b'] = 0;
                }

                $lead = Lead::create($leadData);

                LeadActivity::create([
                    'lead_id' => $lead->id,
                    'lead_status_id' => $leadData['lead_status_id'],
                    'activity_type' => 'lead_created',
                    'description' => 'Lead created via add form',
                    'followup_date' => $request->followup_date,
                    'remarks' => $request->remarks,
                    'created_by' => $currentUserId,
                    'updated_by' => $currentUserId,
                ]);

                return $lead;
            });
        } catch (\Throwable $exception) {
            Log::error('Failed to create lead via submit form', [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('message_danger', 'Failed to create lead. Please try again.')
                ->withInput();
        }

        return redirect()->route('leads.index')->with('message_success', 'Lead created successfully!');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'code' => 'nullable|string|max:10',
            'whatsapp_code' => 'nullable|string|max:10',
            'whatsapp' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'age' => 'nullable|integer|min:1|max:999',
            'place' => 'nullable|string|max:255',
            'qualification' => 'nullable|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'lead_status_id' => 'required|exists:lead_statuses,id',
            'lead_source_id' => 'required|exists:lead_sources,id',
            'team_id' => 'required|exists:teams,id',
            'telecaller_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'address' => 'nullable|string|max:500',
            'followup_date' => 'nullable|date',
            'remarks' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('message_danger', $validator->errors()->first())
                ->withInput();
        }

        // Check for duplicate lead (phone + code + course)
        $existingLead = Lead::findDuplicateByPhoneAndCourse(
            $request->code,
            $request->phone,
            (int) $request->course_id
        );

        if ($existingLead) {
            return redirect()->back()
                ->with('message_danger', 'Lead with this phone number and course already exists')
                ->withInput();
        }

        // Get interest_status from lead_status
        $leadStatus = LeadStatus::find($request->lead_status_id);
        $interestStatus = $leadStatus ? $leadStatus->interest_status : null;

        $data = $request->all();
        $data['interest_status'] = $interestStatus; // Override with lead status interest_status
        $data['created_by'] = AuthHelper::getCurrentUserId();
        $data['updated_by'] = AuthHelper::getCurrentUserId();
        $data['first_created_at'] = now();
        
        // Handle is_b2b logic
        if (RoleHelper::is_admin_or_super_admin() && $request->has('is_b2b')) {
            // Admin can manually set is_b2b
            $data['is_b2b'] = $request->is_b2b ? 1 : 0;
        } elseif (isset($data['telecaller_id'])) {
            // Auto-set is_b2b based on telecaller
            $telecaller = User::find($data['telecaller_id']);
            $data['is_b2b'] = $telecaller && $telecaller->is_b2b ? 1 : 0;
        } else {
            $data['is_b2b'] = 0;
        }

        $lead = Lead::create($data);

        if ($lead) {
            // Create lead activity
            LeadActivity::create([
                'lead_id' => $lead->id,
                'lead_status_id' => $request->lead_status_id,
                'activity_type' => 'lead_created',
                'description' => 'Lead created via store form',
                'followup_date' => $request->followup_date,
                'remarks' => $request->remarks,
                'created_by' => AuthHelper::getCurrentUserId(),
                'updated_by' => AuthHelper::getCurrentUserId()
            ]);

            return redirect()->route('leads.index')
                ->with('message_success', 'Lead added successfully!');
        }

        return redirect()->back()
            ->with('message_danger', 'Something went wrong! Please try again.')
            ->withInput();
    }

    public function show($leadId)
    {
        $lead = Lead::withoutGlobalScope('exclude_pullbacked')->findOrFail($leadId);
        $canViewPullbackHistory = RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager();

        $lead->load([
            'leadStatus', 
            'leadSource', 
            'course', 
            'telecaller', 
            'leadActivities' => function($query) use ($canViewPullbackHistory) {
                $query->select('id', 'lead_id', 'reason', 'created_at', 'activity_type', 'description', 'remarks', 'rating', 'followup_date', 'created_by', 'lead_status_id', 'is_pullbacked')
                      ->with(['createdBy:id,name', 'leadStatus:id,title']);

                if (!$canViewPullbackHistory) {
                    $query->where(function ($subQuery) {
                        $subQuery->whereNull('is_pullbacked')
                                 ->orWhere('is_pullbacked', 0);
                    });
                }

                $query->orderBy('created_at', 'desc');
            }
        ]);
        
        $leadStatusList = LeadStatus::pluck('title', 'id')->toArray();
        $leadSourceList = LeadSource::pluck('title', 'id')->toArray();
        $courseName = Course::pluck('title', 'id')->toArray();
        $telecallerList = User::where('role_id', 3)->pluck('name', 'id')->toArray();

        return view('admin.leads.show', compact(
            'lead', 'leadStatusList', 'leadSourceList', 'courseName', 'telecallerList', 'canViewPullbackHistory'
        ));
    }

    public function ajax_show($leadId)
    {
        $lead = Lead::withoutGlobalScope('exclude_pullbacked')->findOrFail($leadId);
        $canViewPullbackHistory = RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager();

        $lead->load([
            'leadStatus', 
            'leadSource', 
            'course', 
            'telecaller', 
            'leadActivities' => function($query) use ($canViewPullbackHistory) {
                $query->select('id', 'lead_id', 'reason', 'created_at', 'activity_type', 'description', 'remarks', 'rating', 'followup_date', 'created_by', 'lead_status_id', 'is_pullbacked')
                      ->with(['createdBy:id,name', 'leadStatus:id,title']);

                if (!$canViewPullbackHistory) {
                    $query->where(function ($subQuery) {
                        $subQuery->whereNull('is_pullbacked')
                                 ->orWhere('is_pullbacked', 0);
                    });
                }

                $query->orderBy('created_at', 'desc');
            }
        ]);
        
        return view('admin.leads.show-modal', compact('lead', 'canViewPullbackHistory'));
    }

    public function status_update(Lead $lead)
    {
        $canViewPullbackHistory = RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager();

        $leadStatuses = LeadStatus::all();
        $courses = Course::active()->orderBy('title')->get(['id', 'title']);
        $lead->load(['leadActivities' => function($query) use ($canViewPullbackHistory) {
            $query->with(['leadStatus', 'createdBy'])->orderBy('created_at', 'desc');

            if (!$canViewPullbackHistory) {
                $query->where(function ($subQuery) {
                    $subQuery->whereNull('is_pullbacked')
                             ->orWhere('is_pullbacked', 0);
                });
            }
        }]);

        return view('admin.leads.status-update-modal', compact('lead', 'leadStatuses', 'courses', 'canViewPullbackHistory'));
    }

    public function status_update_submit(Request $request, Lead $lead)
    {
        try {
            // Debug: Log the incoming request data
            Log::info('Status Update Request Data:', $request->all());
            
            // Prepare validation rules
            $rules = [
                'lead_status_id' => 'required|exists:lead_statuses,id',
                'reason' => 'required|string|max:255',
                'remarks' => 'required|string|max:1000',
                'rating' => 'required|integer|min:1|max:10',
                'date' => 'required|date',
                'time' => 'required|date_format:H:i',
                'course_id' => 'nullable|exists:courses,id',
            ];

            // Only add followup_date validation if status requires it
            $followupRequiredStatuses = [2, 7, 8, 9];
            if (in_array((int) $request->lead_status_id, $followupRequiredStatuses, true)) {
                $rules['followup_date'] = 'required|date|after_or_equal:today';
            } else {
                $rules['followup_date'] = 'nullable|date';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please correct the errors below.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get current status before updating
            $currentStatusTitle = optional($lead->leadStatus)->title ?? 'Unknown';
            $newStatusId = (int) $request->lead_status_id;
            $leadStatus = LeadStatus::find($newStatusId);

            if (!$leadStatus) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected status is no longer available. Please refresh and try again.'
                ], 422);
            }

            $newStatusTitle = $leadStatus->title;
            $interestStatus = $leadStatus->interest_status;
            $currentUserId = AuthHelper::getCurrentUserId();

            try {
                $activityTimestamp = Carbon::parse($request->date . ' ' . $request->time);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid activity date or time provided.'
                ], 422);
            }

            // Prepare lead update data
            $leadUpdateData = [
                'lead_status_id' => $newStatusId,
                'interest_status' => $interestStatus,
                'rating' => $request->rating,
                'remarks' => $request->remarks,
                'updated_by' => $currentUserId,
                'followup_date' => null,
            ];

            if ($request->filled('course_id')) {
                $leadUpdateData['course_id'] = $request->course_id;
            }

            if (in_array($newStatusId, $followupRequiredStatuses, true) && $request->followup_date) {
                $leadUpdateData['followup_date'] = $request->followup_date;
            }

            // Generate automatic status change remark
            $statusChangeRemark = "Status changed from '{$currentStatusTitle}' to '{$newStatusTitle}'";
            
            // Combine with user remarks if provided
            $finalRemarks = $statusChangeRemark;
            if (!empty($request->remarks)) {
                $finalRemarks .= " | User Note: " . $request->remarks;
            }

            $activityPayload = [
                'lead_id' => $lead->id,
                'lead_status_id' => $newStatusId,
                'activity_type' => 'status_update',
                'description' => 'Status updated to ' . $newStatusTitle,
                'reason' => $request->reason,
                'rating' => $request->rating,
                'remarks' => $finalRemarks,
                'created_by' => $currentUserId,
                'updated_by' => $currentUserId,
            ];

            if (in_array($newStatusId, $followupRequiredStatuses, true) && $request->followup_date) {
                $activityPayload['followup_date'] = $request->followup_date;
            }

            $updatedLead = DB::transaction(function () use ($lead, $leadUpdateData, $activityPayload, $activityTimestamp) {
                $leadUpdatePayload = $leadUpdateData;
                $leadUpdatePayload['updated_at'] = $activityTimestamp;

                $updated = Lead::where('id', $lead->id)->update($leadUpdatePayload);

                if (!$updated) {
                    throw new \RuntimeException('Failed to update lead record.');
                }

                $lead->leadActivities()->create(array_merge($activityPayload, [
                    'created_at' => $activityTimestamp,
                    'updated_at' => $activityTimestamp,
                ]));

                return $lead->fresh(['leadStatus', 'leadSource']);
            });

            Log::info('Lead status updated successfully', [
                'lead_id' => $lead->id,
                'old_status' => $currentStatusTitle,
                'new_status' => $newStatusTitle,
                'new_status_id' => $newStatusId
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lead status updated successfully!',
                'data' => $updatedLead
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating lead status', [
                'lead_id' => $lead->id ?? null,
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(Lead $lead)
    {
        // Check edit permission: Admin/Super Admin, General Manager, Team Lead, or Senior Manager only
        // Regular telecallers (without team lead, senior manager, admin, or general manager roles) cannot edit
        $canEditLead = RoleHelper::is_admin_or_super_admin() || 
                       RoleHelper::is_general_manager() || 
                       RoleHelper::is_team_lead() || 
                       RoleHelper::is_senior_manager();
        
        if (!$canEditLead) {
            return redirect()->route('leads.index')
                ->with('error', 'You do not have permission to edit leads.');
        }
        
        $currentUser = AuthHelper::getCurrentUser();
        $isTeamLead = $currentUser && AuthHelper::isTeamLead();
        $isTelecaller = $currentUser && $currentUser->role_id == 3;
        $isSeniorManager = $currentUser && RoleHelper::is_senior_manager();
        $isGeneralManager = RoleHelper::is_general_manager();
        
        // Filter telecallers based on role
        if ($isTeamLead && !$isSeniorManager) {
            // Team Lead: Show only their team members
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
                $teamMemberIds[] = AuthHelper::getCurrentUserId(); // Include team lead
                $telecallers = User::whereIn('id', $teamMemberIds)->get();
            } else {
                $telecallers = collect([$currentUser]); // Only themselves if no team
            }
        } elseif ($isTelecaller && !$isSeniorManager) {
            // Regular Telecaller: Show only themselves
            $telecallers = collect([$currentUser]);
        } else {
            // Admin/Super Admin/Senior Manager/General Manager: Show all telecallers (excluding marketing teams)
            $telecallers = User::nonMarketingTelecallers()->get();
        }
        
        $leadStatuses = LeadStatus::all();
        $leadSources = LeadSource::all();
        $countries = Country::all();
        $courses = Course::all();
        
        // Filter teams based on role
        if ($isTeamLead && !$isSeniorManager) {
            // Team Lead: Show only their team
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teams = Team::where('id', $teamId)->get();
            } else {
                $teams = collect(); // No teams if not assigned to any team
            }
        } elseif ($isTelecaller && !$isSeniorManager) {
            // Regular Telecaller: Show only their team
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teams = Team::where('id', $teamId)->get();
            } else {
                $teams = collect(); // No teams if not assigned to any team
            }
        } else {
            // Admin/Super Admin/Senior Manager/General Manager: Show all teams (excluding marketing teams)
            $teams = Team::nonMarketing()->get();
        }
        
        $country_codes = get_country_code();

        return view('admin.leads.edit', compact(
            'lead', 'telecallers', 'leadStatuses', 'leadSources', 'countries', 'courses', 'teams', 'country_codes'
        ));
    }

    public function ajax_edit(Lead $lead)
    {
        // Check edit permission: Admin/Super Admin, General Manager, Team Lead, or Senior Manager only
        // Regular telecallers (without team lead, senior manager, admin, or general manager roles) cannot edit
        $canEditLead = RoleHelper::is_admin_or_super_admin() || 
                       RoleHelper::is_general_manager() || 
                       RoleHelper::is_team_lead() || 
                       RoleHelper::is_senior_manager();
        
        if (!$canEditLead) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit leads.'
            ], 403);
        }
        
        $currentUser = AuthHelper::getCurrentUser();
        $isTeamLead = $currentUser && AuthHelper::isTeamLead();
        $isTelecaller = $currentUser && $currentUser->role_id == 3;
        $isSeniorManager = $currentUser && RoleHelper::is_senior_manager();
        $isGeneralManager = RoleHelper::is_general_manager();
        
        // Filter telecallers based on role
        if ($isTeamLead && !$isSeniorManager) {
            // Team Lead: Show only their team members
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
                $teamMemberIds[] = AuthHelper::getCurrentUserId(); // Include team lead
                $telecallers = User::whereIn('id', $teamMemberIds)->get();
            } else {
                $telecallers = collect([$currentUser]); // Only themselves if no team
            }
        } elseif ($isTelecaller && !$isSeniorManager) {
            // Regular Telecaller: Show only themselves
            $telecallers = collect([$currentUser]);
        } else {
            // Admin/Super Admin/Senior Manager/General Manager: Show all telecallers (excluding marketing teams)
            $telecallers = User::nonMarketingTelecallers()->get();
        }
        
        // Filter teams based on role
        if ($isTeamLead && !$isSeniorManager) {
            // Team Lead: Show only their team
            $teams = Team::where('id', $currentUser->team_id)->get();
        } elseif ($isTelecaller && !$isSeniorManager) {
            // Regular Telecaller: Show only their team (if any)
            $teams = Team::where('id', $currentUser->team_id)->get();
        } else {
            // Admin/Super Admin/Senior Manager/General Manager: Show all teams
            $teams = Team::nonMarketing()->get();
        }
        
        $leadStatuses = LeadStatus::all();
        $leadSources = LeadSource::all();
        $countries = Country::all();
        $courses = Course::all();
        $country_codes = get_country_code();

        return view('admin.leads.edit-modal', compact(
            'lead', 'telecallers', 'leadStatuses', 'leadSources', 'countries', 'courses', 'teams', 'country_codes', 'isTelecaller', 'isTeamLead'
        ));
    }

    public function destroy(Lead $lead)
    {
        try {
            // Set deleted_by before deleting
            $lead->deleted_by = AuthHelper::getCurrentUserId();
            $lead->save();
            
            $lead->delete();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lead deleted successfully!'
                ]);
            }
            
            return redirect()->route('leads.index')->with('message_success', 'Lead deleted successfully!');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while deleting the lead. Please try again.'
                ], 500);
            }
            
            return redirect()->back()->with('message_danger', 'An error occurred while deleting the lead. Please try again.');
        }
    }

    public function update(Request $request, Lead $lead)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'nullable|string|max:255',
                'phone' => 'required|string|max:20',
                'email' => 'nullable|email|max:255',
                'code' => 'required|string|max:10',
                'whatsapp_code' => 'nullable|string|max:10',
                'whatsapp' => 'nullable|string|max:20',
                'gender' => 'nullable|in:male,female,other',
                'age' => 'nullable|integer|min:1|max:999',
                'place' => 'nullable|string|max:255',
                'qualification' => 'nullable|string|max:255',
                'lead_status_id' => 'required|exists:lead_statuses,id',
                'lead_source_id' => 'required|exists:lead_sources,id',
                'country_id' => 'nullable|exists:countries,id',
                'course_id' => 'required|exists:courses,id',
                'team_id' => 'required|exists:teams,id',
                'telecaller_id' => 'required|exists:users,id',
                'address' => 'nullable|string|max:500',
                'followup_date' => 'nullable|date',
                'add_date' => 'nullable|date',
                'add_time' => 'nullable|date_format:H:i',
                'remarks' => 'nullable|string|max:1000',
                'is_b2b' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please correct the errors below.',
                        'errors' => $validator->errors()
                    ], 422);
                }
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Check for duplicate lead (phone + code + course, excluding current lead)
            $existingLead = Lead::findDuplicateByPhoneAndCourse(
                $request->code,
                $request->phone,
                (int) $request->course_id,
                $lead->id
            );

            if ($existingLead) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Lead with this phone number and course already exists'
                    ], 422);
                }
                return redirect()->back()
                    ->with('message_danger', 'Lead with this phone number already exists')
                    ->withInput();
            }

            // Only update fields that are provided in the request
            $data = $request->only([
                'title', 'gender', 'age', 'phone', 'code', 'whatsapp', 'whatsapp_code',
                'email', 'qualification', 'country_id', 'lead_status_id', 'lead_source_id',
                'address', 'telecaller_id', 'team_id', 'place', 'course_id', 'batch_id',
                'university_id', 'followup_date', 'add_date', 'add_time', 'remarks'
            ]);
            
            // Handle is_b2b logic
            if (RoleHelper::is_admin_or_super_admin() && $request->has('is_b2b')) {
                // Admin can manually set is_b2b
                $data['is_b2b'] = $request->is_b2b ? 1 : 0;
            } elseif (isset($data['telecaller_id'])) {
                // Auto-set is_b2b based on telecaller
                $telecaller = User::find($data['telecaller_id']);
                $data['is_b2b'] = $telecaller && $telecaller->is_b2b ? 1 : 0;
            }
            
            // Check if telecaller_id is being changed (reassignment)
            $isReassignment = isset($data['telecaller_id']) && 
                             $data['telecaller_id'] != $lead->telecaller_id;
            
            // If reassigning, set lead_status_id to 1
            if ($isReassignment) {
                $data['lead_status_id'] = 1;
            } else {
                // Set default values
                $data['lead_status_id'] = $data['lead_status_id'] ?? 1;
            }
            
            $data['add_date'] = $data['add_date'] ?? date('Y-m-d');
            $data['add_time'] = $data['add_time'] ?? date('H:i');
            
            // Get interest_status from lead_status
            $leadStatus = LeadStatus::find($data['lead_status_id']);
            $data['interest_status'] = $leadStatus ? $leadStatus->interest_status : null;
            
            $data['updated_by'] = AuthHelper::getCurrentUserId();

            // Store old telecaller_id and course_id before update
            $oldTelecallerId = $lead->telecaller_id;
            $oldCourseId = $lead->course_id;

            if ($lead->update($data)) {
                // If course_id was changed, sync to leads_details for this lead
                if (isset($data['course_id']) && (string) $data['course_id'] !== (string) $oldCourseId) {
                    LeadDetail::where('lead_id', $lead->id)->update(['course_id' => $data['course_id']]);
                }

                // If telecaller was changed, create activity log
                if ($isReassignment && isset($data['telecaller_id'])) {
                    $fromTelecaller = $oldTelecallerId ? \App\Models\User::find($oldTelecallerId) : null;
                    $toTelecaller = \App\Models\User::find($data['telecaller_id']);
                    
                    $fromTelecallerName = $fromTelecaller ? $fromTelecaller->name : 'Unassigned';
                    $toTelecallerName = $toTelecaller ? $toTelecaller->name : 'Unknown';
                    
                    \App\Models\LeadActivity::create([
                        'lead_id' => $lead->id,
                        'lead_status_id' => 1, // Set status to 1 when reassigned
                        'activity_type' => 'reassign',
                        'description' => 'Lead reassigned',
                        'remarks' => "Lead has been reassigned from telecaller {$fromTelecallerName} to telecaller {$toTelecallerName}.",
                        'created_by' => AuthHelper::getCurrentUserId(),
                        'updated_by' => AuthHelper::getCurrentUserId(),
                    ]);
                }
                
                if (request()->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Lead updated successfully!',
                        'data' => $lead
                    ]);
                }
                return redirect()->route('leads.index')
                    ->with('message_success', 'Lead updated successfully!');
            }

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong! Please try again.'
                ], 500);
            }
            return redirect()->back()
                ->with('message_danger', 'Something went wrong! Please try again.')
                ->withInput();
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the lead. Please try again.'
                ], 500);
            }
            return redirect()->back()
                ->with('message_danger', 'An error occurred while updating the lead. Please try again.')
                ->withInput();
        }
    }

    public function bulkUploadView()
    {
        $currentUser = AuthHelper::getCurrentUser();
        $isTeamLead = $currentUser && AuthHelper::isTeamLead();
        $isSeniorManager = $currentUser && RoleHelper::is_senior_manager();
        
        $leadStatuses = LeadStatus::where('is_active', true)->get();
        $leadSources = LeadSource::where('is_active', true)->get();
        $courses = Course::where('is_active', true)->get();
        
        // Filter teams and telecallers based on role
        if ($isTeamLead && !$isSeniorManager) {
            // Team Lead: Show only their team
            $userTeamId = $currentUser->team_id;
            $teams = Team::where('id', $userTeamId)->where('is_active', true)->get();
            $telecallers = User::where('role_id', 3)
                              ->where('team_id', $userTeamId)
                              ->where('is_active', true)
                              ->get();
        } elseif ($isSeniorManager || RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager()) {
            // Senior Manager/General Manager/Admin/Super Admin: Show all teams and telecallers (excluding marketing teams)
            $teams = Team::where('is_active', true)->nonMarketing()->get();
            $telecallers = User::nonMarketingTelecallers()
                              ->where('is_active', true)
                              ->get();
        } else {
            // Regular telecaller: Show only their team
            $userTeamId = $currentUser ? $currentUser->team_id : null;
            if ($userTeamId) {
                $teams = Team::where('id', $userTeamId)->where('is_active', true)->get();
                $telecallers = User::where('role_id', 3)
                                  ->where('team_id', $userTeamId)
                                  ->where('is_active', true)
                                  ->get();
            } else {
                $teams = collect();
                $telecallers = collect();
            }
        }
        
        return view('admin.leads.bulk-upload', compact(
            'leadStatuses', 'leadSources', 'courses', 'teams', 'telecallers', 'isSeniorManager'
        ));
    }

    public function downloadTemplate()
    {
        $filePath = storage_path('app/public/lead-sample.xlsx');
        
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'Template file not found'], 404);
        }
        
        $currentDateTime = now()->format('Y-m-d_H-i-s');
        $filename = "Lead_Bulk_Upload_{$currentDateTime}.xlsx";
        
        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    public function bulkUploadSubmit(Request $request)
    {
        // Check permission: Admin/Super Admin, General Manager, Team Lead, or Senior Manager only
        $canBulkUpload = RoleHelper::is_admin_or_super_admin() || 
                        RoleHelper::is_general_manager() || 
                        RoleHelper::is_team_lead() || 
                        RoleHelper::is_senior_manager();
        
        if (!$canBulkUpload) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to bulk upload leads.'
            ], 403);
        }

        // Handle POST request - process the bulk upload
        // Set execution time limit for bulk operations
        set_time_limit(config('timeout.max_execution_time', 300));
        ini_set('memory_limit', config('timeout.memory_limit', '256M'));
        
        // Try to set upload limits (may not work on all servers)
        ini_set('upload_max_filesize', '4M');
        ini_set('post_max_size', '8M');
        ini_set('max_input_time', '300');

        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls|max:2048',
            'lead_source_id' => 'required|exists:lead_sources,id',
            'lead_status_id' => 'required|exists:lead_statuses,id',
            'course_id' => 'required|exists:courses,id',
            'team_id' => 'required|string',
            'assign_to_all' => 'boolean',
            'telecallers' => 'required_if:assign_to_all,false|array|min:1',
            'telecallers.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please fix the validation errors.',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = AuthHelper::getCurrentUserId();
        $uploadLock = Cache::lock('leads_bulk_upload_user_' . $userId, 600);

        if (! $uploadLock->get()) {
            return response()->json([
                'success' => false,
                'message' => 'A bulk upload is already in progress. Please wait for it to finish.',
            ], 429);
        }

        try {
            $file = $request->file('excel_file');
            
            // Check if file was uploaded successfully
            if (!$file || !$file->isValid()) {
                $errorMessage = 'File upload failed. ';
                if ($file && $file->getError() === UPLOAD_ERR_INI_SIZE) {
                    $errorMessage .= 'File exceeds server upload limit. Maximum file size: 2MB.';
                } elseif ($file && $file->getError() === UPLOAD_ERR_FORM_SIZE) {
                    $errorMessage .= 'File exceeds form upload limit. Maximum file size: 2MB.';
                } else {
                    $errorMessage .= 'Please check file size and try again. Maximum file size: 2MB.';
                }
                
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => ['excel_file' => [$errorMessage]]
                ], 422);
            }
            
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            
            // Check if the worksheet has any data
            if ($highestRow < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Excel file appears to be empty or has no data rows. Please ensure the file contains data starting from row 2.',
                    'errors' => ['excel_file' => ['Excel file is empty or has no data']]
                ], 422);
            }

            // Get telecallers based on assignment type
            if ($request->assign_to_all) {
                // When assigning to all, get telecallers from the selected team or all teams
                if ($request->team_id === 'all') {
                    $telecallers = User::nonMarketingTelecallers()
                        ->where('is_active', true)
                        ->pluck('id')->toArray();
                } else {
                    // Check if team is marketing team
                    $team = Team::find($request->team_id);
                    if ($team && $team->marketing_team) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Cannot assign leads to marketing team.',
                            'errors' => ['team_id' => ['Marketing teams cannot be assigned leads']]
                        ], 422);
                    }
                    $telecallers = User::where('team_id', $request->team_id)
                        ->where('role_id', 3)
                        ->where('is_active', true)
                        ->pluck('id')->toArray();
                }
                    
                // Check if team has telecallers
                if (empty($telecallers)) {
                    $message = $request->team_id === 'all' 
                        ? 'No telecallers found in any team. Please assign telecallers manually.'
                        : 'No telecallers found in the selected team. Please select a different team or assign telecallers manually.';
                    
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                        'errors' => ['team_id' => ['No telecallers available']]
                    ], 422);
                }
            } else {
                // When assigning manually, use selected telecallers
                $telecallers = $request->telecallers ?? [];
                
                // Check if telecallers are selected
                if (empty($telecallers)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please select at least one telecaller or choose "Assign to all telecallers in team".',
                        'errors' => ['telecallers' => ['Please select at least one telecaller']]
                    ], 422);
                }
            }

            $telecallerIndex = 0;
            $successCount = 0;
            $duplicateCount = 0;
            $seenInUpload = [];

            // Limit the number of rows to prevent timeout
            $maxRows = min($highestRow, config('timeout.bulk_upload.max_rows', 1000));
            
            for ($row = 2; $row <= $maxRows; $row++) {
                $name = $worksheet->getCell('A' . $row)->getValue();
                $phoneCell = $worksheet->getCell('B' . $row);
                $phoneRaw = $phoneCell->getFormattedValue();
                if ($phoneRaw === null || trim((string) $phoneRaw) === '') {
                    $phoneRaw = $phoneCell->getValue();
                }
                $place = $worksheet->getCell('C' . $row)->getValue();
                $remarks = $worksheet->getCell('D' . $row)->getValue();

                $phoneParts = PhoneNumberHelper::parseBulkUploadPhone($phoneRaw);
                $code = $phoneParts['code'];
                $phoneNumber = $phoneParts['phone'];

                if ($phoneNumber === '') {
                    continue;
                }

                $courseId = (int) $request->course_id;
                $duplicateKey = PhoneNumberHelper::leadDuplicateKey($code, $phoneNumber, $courseId);

                if (isset($seenInUpload[$duplicateKey])) {
                    $duplicateCount++;
                    continue;
                }

                $existingLead = Lead::findDuplicateByPhoneAndCourse($code, $phoneNumber, $courseId);
                if ($existingLead) {
                    $duplicateCount++;
                    continue;
                }

                $seenInUpload[$duplicateKey] = true;

                // Ensure we have a valid telecaller index
                $telecallerId = $telecallers[$telecallerIndex] ?? $telecallers[0];
                
                // Get interest_status from lead_status
                $leadStatus = LeadStatus::find($request->lead_status_id);
                $interestStatus = $leadStatus ? $leadStatus->interest_status : null;
                
                $lead = Lead::create([
                    'title' => $name,
                    'phone' => $phoneNumber,
                    'code' => $code,
                    'place' => $place,
                    'remarks' => $remarks,
                    'lead_source_id' => $request->lead_source_id,
                    'lead_status_id' => $request->lead_status_id,
                    'interest_status' => $interestStatus,
                    'course_id' => $request->course_id,
                    'telecaller_id' => $telecallerId,
                    'created_by' => AuthHelper::getCurrentUserId(),
                    'updated_by' => AuthHelper::getCurrentUserId(),
                    'is_converted' => false,
                    'first_created_at' => now(),
                ]);

                if ($lead) {
                    $successCount++;
                    
                    // Log activity
                    LeadActivity::create([
                        'lead_id' => $lead->id,
                        'activity_type' => 'bulk_upload',
                        'description' => 'Lead created via bulk upload',
                        'created_by' => AuthHelper::getCurrentUserId(),
                        'created_at' => now()
                    ]);
                    
                    $telecallerIndex = ($telecallerIndex + 1) % count($telecallers);
                }
            }

            $message = "Successfully uploaded {$successCount} leads!";
            if ($duplicateCount > 0) {
                $message .= " {$duplicateCount} duplicates skipped.";
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing file: ' . $e->getMessage()
            ], 500);
        } finally {
            optional($uploadLock)->release();
        }
    }


    public function statusChange(Request $request, Lead $lead)
    {
        // Handle GET request - return the status change form
        if ($request->isMethod('get')) {
            $leadStatuses = LeadStatus::where('is_active', true)->get();
            return response()->json([
                'success' => true,
                'html' => view('admin.leads.status-change-modal', compact('lead', 'leadStatuses'))->render()
            ]);
        }

        // Handle POST request - process the status change
        $validator = Validator::make($request->all(), [
            'lead_status_id' => 'required|exists:lead_statuses,id',
            'remarks' => 'required|string|max:1000',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }

        try {
            // Update lead status
            $lead->update([
                'lead_status_id' => $request->lead_status_id,
                'remarks' => $request->remarks,
                'updated_by' => AuthHelper::getCurrentUserId()
            ]);

            // Log activity
            LeadActivity::create([
                'lead_id' => $lead->id,
                'activity_type' => 'status_change',
                'description' => 'Lead status changed to: ' . $lead->leadStatus->title,
                'remarks' => $request->remarks,
                'created_by' => AuthHelper::getCurrentUserId(),
                'created_at' => $request->date . ' ' . $request->time
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lead status updated successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating lead status: ' . $e->getMessage()
            ]);
        }
    }

    public function history(Lead $lead)
    {
        $activities = LeadActivity::where('lead_id', $lead->id)
            ->with(['createdBy:id,name'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'html' => view('admin.leads.history-modal', compact('lead', 'activities'))->render()
        ]);
    }

    public function getTelecallersByTeam(Request $request)
    {
        $teamIds = $request->input('team_ids');
        $teamId = $request->get('team_id');
        $isB2B = $request->get('is_b2b', 0);
        $currentUser = AuthHelper::getCurrentUser();
        $isTeamLead = $currentUser && AuthHelper::isTeamLead();
        $isTelecaller = $currentUser && $currentUser->role_id == 3;
        $canSeeAllTeamTelecallerOptions = \App\Helpers\TeamTelecallerFilterHelper::canSeeAllTeamTelecallerFilterOptions();

        if ($teamIds === null && $teamId === null) {
            return response()->json(['telecallers' => []]);
        }

        if ($teamIds !== null) {
            if (is_string($teamIds)) {
                $teamIds = array_filter(explode(',', $teamIds));
            }

            if ($teamIds === [] || (count($teamIds) === 1 && $teamIds[0] === 'all')) {
                $teamId = 'all';
            } else {
                $teamId = null;
            }
        }

        if ($teamId === 'all' || ($teamId === null && ! empty($teamIds))) {
            if ($teamId === 'all') {
                $telecallers = \App\Helpers\TeamTelecallerFilterHelper::getFilterTelecallers(null);
            } else {
                $normalizedTeamIds = array_values(array_filter(array_map('intval', (array) $teamIds)));
                $telecallers = \App\Helpers\TeamTelecallerFilterHelper::getFilterTelecallers($normalizedTeamIds ?: null);
            }

            if ($isB2B) {
                $telecallers = $telecallers->where('is_b2b', 1)->values();
            }
        } elseif (! $teamId) {
            return response()->json(['telecallers' => []]);
        } else {
            // Get telecallers from specific team (with role filtering)
            $query = User::where('team_id', $teamId)
                        ->where('role_id', 3) // Telecaller role
                        ->where('is_active', true)
                        ->with('team:id,name')
                        ->select('id', 'name', 'email', 'team_id');
            
            // Filter by is_b2b if requested
            if ($isB2B) {
                $query->where('is_b2b', 1);
            }
            
            if ($isTeamLead && ! $canSeeAllTeamTelecallerOptions) {
                // Team Lead (not elevated manager): Only show if it's their team
                $userTeamId = $currentUser->team_id;
                if ($teamId != $userTeamId) {
                    $telecallers = collect([]);
                } else {
                    $telecallers = $query->get();
                }
            } elseif ($isTelecaller && ! $canSeeAllTeamTelecallerOptions) {
                // Regular Telecaller: Only show themselves
                $telecallers = $query->where('id', $currentUser->id)->get();
            } else {
                // Admin/Super Admin/Senior Manager/General Manager: Show all
                $telecallers = $query->get();
            }
        }

        if (! isset($telecallers)) {
            $telecallers = collect();
        }

        $telecallers = $telecallers->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'team_name' => $user->team ? $user->team->name : 'No Team'
            ];
        });

        return response()->json(['telecallers' => $telecallers]);
    }

    /**
     * Show bulk reassign form
     */
    public function ajaxBulkReassign()
    {
        $currentUser = AuthHelper::getCurrentUser();
        $isTeamLead = $currentUser && AuthHelper::isTeamLead();
        $isTelecaller = $currentUser && $currentUser->role_id == 3;
        $isSeniorManager = $currentUser && RoleHelper::is_senior_manager();
        
        // Filter telecallers based on role
        if ($isTeamLead && !$isSeniorManager) {
            // Team Lead: Show only their team members
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
                $teamMemberIds[] = AuthHelper::getCurrentUserId(); // Include team lead
                $telecallers = User::whereIn('id', $teamMemberIds)->get();
            } else {
                $telecallers = collect([$currentUser]); // Only themselves if no team
            }
        } elseif ($isTelecaller && !$isSeniorManager && !$isTeamLead) {
            // Regular Telecaller: Show only themselves
            $telecallers = collect([$currentUser]);
        } else {
            // Admin/Super Admin/Senior Manager: Show all telecallers
            $telecallers = User::where('role_id', 3)->get();
        }
        
        // No team selection needed for bulk reassign - removed teams
        // Senior managers and admins can see all telecallers
        
        $data = [
            'telecallers' => $telecallers,
            'isSeniorManager' => $isSeniorManager,
            'leadStatuses' => LeadStatus::where('is_active', 1)->get(),
            'leadSources' => LeadSource::where('is_active', 1)->get(),
            'countries' => Country::where('is_active', 1)->get(),
            'courses' => Course::where('is_active', 1)->get(),
        ];

        return view('admin.leads.ajax-bulk-reassign', $data);
    }

    /**
     * Process bulk reassign
     */
    public function bulkReassign(Request $request)
    {
        // Check permission: Admin/Super Admin, General Manager, Team Lead, or Senior Manager only
        $canBulkReassign = RoleHelper::is_admin_or_super_admin() || 
                          RoleHelper::is_general_manager() || 
                          RoleHelper::is_team_lead() || 
                          RoleHelper::is_senior_manager();
        
        if (!$canBulkReassign) {
            return redirect()->back()
                ->with('error', 'You do not have permission to bulk reassign leads.');
        }
        
        $validator = Validator::make($request->all(), [
            'telecaller_id' => 'required|exists:users,id',
            'lead_source_id' => 'required|exists:lead_sources,id',
            // lead_status_id is not required as it's always set to 1 when reassigning
            'from_telecaller_id' => 'required|exists:users,id',
            'lead_from_date' => 'required|date',
            'lead_to_date' => 'required|date',
            'lead_id' => 'required|array|min:1',
            'lead_id.*' => 'exists:leads,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Get telecaller names for activity history
        $toTelecaller = \App\Models\User::find($request->telecaller_id);
        $fromTelecaller = \App\Models\User::find($request->from_telecaller_id);
        
        $toTelecallerName = $toTelecaller ? $toTelecaller->name : 'Unknown';
        $fromTelecallerName = $fromTelecaller ? $fromTelecaller->name : 'Unknown';

        $successCount = 0;
        foreach ($request->lead_id as $leadId) {
            // Update the lead directly without loading the full model
            // Set lead_status_id to 1 when reassigning
            $updated = Lead::where('id', $leadId)->update([
                'telecaller_id' => $request->telecaller_id,
                'lead_source_id' => $request->lead_source_id,
                'lead_status_id' => 1, // Always set to 1 when reassigned
                'updated_by' => AuthHelper::getCurrentUserId(),
            ]);

            if ($updated) {
                // Create lead activity history
                \App\Models\LeadActivity::create([
                    'lead_id' => $leadId,
                    'lead_status_id' => 1, // Set status to 1 when reassigned
                    'activity_type' => 'bulk_reassign',
                    'description' => 'Lead reassigned via bulk operation',
                    'remarks' => "Lead has been reassigned from telecaller {$fromTelecallerName} to telecaller {$toTelecallerName}.",
                    'created_by' => AuthHelper::getCurrentUserId(),
                    'updated_by' => AuthHelper::getCurrentUserId(),
                ]);

                $successCount++;
            }
        }

        return redirect()->back()->with('message_success', "Successfully reassigned {$successCount} leads!");
    }

    /**
     * Show pullback leads form
     */
    public function ajaxPullbackLeads()
    {
        $canAccessPullback = RoleHelper::is_admin_or_super_admin() ||
                             RoleHelper::is_general_manager();

        if (!$canAccessPullback) {
            abort(403, 'You do not have permission to pullback leads.');
        }

        $currentUser = AuthHelper::getCurrentUser();
        $isTeamLead = $currentUser && AuthHelper::isTeamLead();
        $isTelecaller = $currentUser && $currentUser->role_id == 3;
        $isSeniorManager = $currentUser && RoleHelper::is_senior_manager();
        
        if ($isTeamLead && !$isSeniorManager) {
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
                $teamMemberIds[] = AuthHelper::getCurrentUserId();
                $telecallers = User::whereIn('id', $teamMemberIds)->get();
            } else {
                $telecallers = collect([$currentUser]);
            }
        } elseif ($isTelecaller && !$isSeniorManager && !$isTeamLead) {
            $telecallers = collect([$currentUser]);
        } else {
            $telecallers = User::where('role_id', 3)->get();
        }

        $data = [
            'telecallers' => $telecallers,
            'leadStatuses' => LeadStatus::where('is_active', 1)->get(),
            'leadSources' => LeadSource::where('is_active', 1)->get(),
        ];

        return view('admin.leads.ajax-pullback', $data);
    }

    /**
     * Process pullback operation
     */
    public function pullbackLeads(Request $request)
    {
        $canPullback = RoleHelper::is_admin_or_super_admin() || 
                       RoleHelper::is_general_manager();

        if (!$canPullback) {
            return redirect()->back()
                ->with('error', 'You do not have permission to pullback leads.');
        }

        $validator = Validator::make($request->all(), [
            'telecaller_id' => 'required|exists:users,id',
            'lead_source_id' => 'required|exists:lead_sources,id',
            'lead_status_id' => 'required|exists:lead_statuses,id',
            'lead_from_date' => 'required|date',
            'lead_to_date' => 'required|date',
            'lead_id' => 'required|array|min:1',
            'lead_id.*' => 'exists:leads,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $successCount = 0;

        DB::beginTransaction();
        try {
            foreach ($request->lead_id as $leadId) {
                $updated = Lead::where('id', $leadId)
                    ->where('is_pullbacked', 0)
                    ->update([
                        'is_pullbacked' => 1,
                        'updated_by' => AuthHelper::getCurrentUserId(),
                        'updated_at' => now(),
                    ]);

                if ($updated) {
                    LeadActivity::where('lead_id', $leadId)->update(['is_pullbacked' => 1]);

                    LeadActivity::create([
                        'lead_id' => $leadId,
                        'lead_status_id' => $request->lead_status_id,
                        'activity_type' => 'pullback',
                        'description' => 'Lead pullbacked via bulk operation',
                        'remarks' => $request->remarks ?? null,
                        'is_pullbacked' => 1,
                        'created_by' => AuthHelper::getCurrentUserId(),
                        'updated_by' => AuthHelper::getCurrentUserId(),
                    ]);

                    $successCount++;
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Pullback leads failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('message_danger', 'Failed to pullback leads. Please try again.');
        }

        return redirect()->back()->with('message_success', "Successfully pullbacked {$successCount} leads!");
    }

    /**
     * Get leads for pullback operation
     */
    public function getPullbackLeads(Request $request)
    {
        $canAccessPullback = RoleHelper::is_admin_or_super_admin() ||
                             RoleHelper::is_general_manager();

        if (!$canAccessPullback) {
            abort(403, 'You do not have permission to pullback leads.');
        }

        $fromDate = date('Y-m-d H:i:s', strtotime($request->from_date . ' 00:00:00'));
        $toDate = date('Y-m-d H:i:s', strtotime($request->to_date . ' 23:59:59'));

        $leads = Lead::select([
            'id', 'title', 'code', 'phone', 'email', 'lead_status_id', 'lead_source_id', 
            'course_id', 'telecaller_id', 'place', 'rating', 'interest_status', 
            'followup_date', 'remarks', 'is_converted', 'created_at'
        ])
        ->where('lead_source_id', $request->lead_source_id)
        ->where('telecaller_id', $request->tele_caller_id)
        ->where('lead_status_id', $request->lead_status_id)
        ->where('is_pullbacked', 0)
        ->where(function ($q) {
            $q->whereNull('is_b2b')->orWhere('is_b2b', 0);
        })
        ->where('created_at', '>=', $fromDate)
        ->where('created_at', '<=', $toDate)
        ->with([
            'leadStatus:id,title', 
            'leadSource:id,title', 
            'telecaller:id,name', 
            'course:id,title'
        ])
        ->get();

        return view('admin.leads.partials.leads-table-rows-pullback', compact('leads'));
    }

    /**
     * Get pullbacked leads eligible for assignment
     */
    public function getAssignablePullbackedLeads(Request $request)
    {
        $canAccess = RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager();

        if (!$canAccess) {
            abort(403, 'You do not have permission to view pullbacked leads.');
        }

        $validator = Validator::make($request->all(), [
            'source_telecaller_id' => 'required|exists:users,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid filters provided.',
            ], 422);
        }

        $fromDate = date('Y-m-d H:i:s', strtotime($request->from_date . ' 00:00:00'));
        $toDate = date('Y-m-d H:i:s', strtotime($request->to_date . ' 23:59:59'));

        $leads = Lead::withoutGlobalScope('exclude_pullbacked')
            ->select([
                'id', 'title', 'code', 'phone', 'email', 'lead_status_id',
                'lead_source_id', 'course_id', 'telecaller_id', 'remarks', 'created_at'
            ])
            ->where('telecaller_id', $request->source_telecaller_id)
            ->where('is_pullbacked', 1)
            ->whereBetween('updated_at', [$fromDate, $toDate])
            ->with([
                'leadStatus:id,title',
                'leadSource:id,title',
                'course:id,title',
            ])
            ->get();

        return view('admin.leads.partials.leads-table-rows-pullback', compact('leads'));
    }

    /**
     * List all pullbacked leads
     */
    public function pullbackedLeads(Request $request)
    {
        $canView = RoleHelper::is_admin_or_super_admin() ||
                   RoleHelper::is_general_manager() ||
                   RoleHelper::is_senior_manager() ||
                   RoleHelper::is_team_lead();

        if (!$canView) {
            abort(403, 'You do not have permission to view pullbacked leads.');
        }

        $filters = [
            'search_key' => $request->get('search_key'),
            'telecaller_id' => $request->get('telecaller_id'),
            'lead_status_id' => $request->get('lead_status_id'),
            'lead_source_id' => $request->get('lead_source_id'),
            'from_date' => $request->get('from_date'),
            'to_date' => $request->get('to_date'),
        ];

        $leadsQuery = Lead::withoutGlobalScope('exclude_pullbacked')
            ->with(['telecaller:id,name', 'leadStatus:id,title', 'leadSource:id,title', 'course:id,title'])
            ->where('is_pullbacked', 1);

        if ($filters['search_key']) {
            $search = $filters['search_key'];
            $leadsQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($filters['telecaller_id']) {
            $leadsQuery->where('telecaller_id', $filters['telecaller_id']);
        }

        if ($filters['lead_status_id']) {
            $leadsQuery->where('lead_status_id', $filters['lead_status_id']);
        }

        if ($filters['lead_source_id']) {
            $leadsQuery->where('lead_source_id', $filters['lead_source_id']);
        }

        if ($filters['from_date']) {
            $leadsQuery->whereDate('updated_at', '>=', $filters['from_date']);
        }

        if ($filters['to_date']) {
            $leadsQuery->whereDate('updated_at', '<=', $filters['to_date']);
        }

        return view('admin.leads.pullbacked', [
            'telecallers' => User::where('role_id', 3)->orderBy('name')->get(),
            'leadStatuses' => LeadStatus::where('is_active', 1)->orderBy('title')->get(),
            'leadSources' => LeadSource::where('is_active', 1)->orderBy('title')->get()->unique('title')->values(),
            'filters' => $filters,
        ]);
    }

    /**
     * Show assign pullbacked leads form
     */
    public function ajaxAssignPullbackedLeads()
    {
        $canAccess = RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager();

        if (!$canAccess) {
            abort(403, 'You do not have permission to assign pullbacked leads.');
        }

        $telecallers = User::where('role_id', 3)->orderBy('name')->get();

        return view('admin.leads.ajax-pullbacked-assign', [
            'telecallers' => $telecallers,
        ]);
    }

    /**
     * Assign pullbacked leads back to telecallers
     */
    public function assignPullbackedLeads(Request $request)
    {
        $canAssign = RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager();

        if (!$canAssign) {
            return redirect()->back()
                ->with('error', 'You do not have permission to assign pullbacked leads.');
        }

        $validator = Validator::make($request->all(), [
            'source_telecaller_id' => 'required|exists:users,id',
            'telecaller_id' => 'required|exists:users,id',
            'lead_from_date' => 'required|date',
            'lead_to_date' => 'required|date',
            'lead_id' => 'required|array|min:1',
            'lead_id.*' => 'exists:leads,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($request->source_telecaller_id == $request->telecaller_id) {
            return redirect()->back()->with('message_danger', 'Please select different telecallers for source and assignment.');
        }

        $telecallerId = $request->telecaller_id;
        $sourceTelecallerId = $request->source_telecaller_id;
        $successCount = 0;
        $targetTelecaller = User::find($telecallerId);

        DB::beginTransaction();
        try {
            $leads = Lead::withoutGlobalScope('exclude_pullbacked')
                ->whereIn('id', $request->lead_id)
                ->where('is_pullbacked', 1)
                ->with('telecaller:id,name')
                ->get();

            foreach ($leads as $lead) {
                $fromTelecallerId = $lead->telecaller_id;

                if ($sourceTelecallerId && $fromTelecallerId != $sourceTelecallerId) {
                    continue;
                }

                $fromTelecallerName = $lead->telecaller ? $lead->telecaller->name : 'Unknown';

                $lead->forceFill([
                    'telecaller_id' => $telecallerId,
                    'is_pullbacked' => 0,
                    'remarks' => null,
                    'lead_status_id' => 1,
                    'followup_date' => null,
                    'rating' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'updated_by' => AuthHelper::getCurrentUserId(),
                ])->save();

                LeadActivity::where('lead_id', $lead->id)->update(['is_pullbacked' => 1]);

                $remarks = sprintf(
                    'Lead reassigned from telecaller %s to %s via pullback assignment.',
                    $fromTelecallerName,
                    optional($targetTelecaller)->name ?? 'Unknown'
                );

                LeadActivity::create([
                    'lead_id' => $lead->id,
                    'lead_status_id' => 1,
                    'activity_type' => 'assign_from_pullback',
                    'description' => 'Lead assigned from pullback',
                    'remarks' => $remarks,
                    'is_pullbacked' => 1,
                    'created_by' => AuthHelper::getCurrentUserId(),
                    'updated_by' => AuthHelper::getCurrentUserId(),
                ]);

                $successCount++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Assign pullbacked leads failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('message_danger', 'Failed to assign pullbacked leads. Please try again.');
        }

        return redirect()->back()->with('message_success', "Successfully assigned {$successCount} pullbacked leads!");
    }

    /**
     * AJAX endpoint for pullbacked leads DataTable
     */
    public function pullbackedLeadsData(Request $request): JsonResponse
    {
        $canView = RoleHelper::is_admin_or_super_admin() ||
                   RoleHelper::is_general_manager() ||
                   RoleHelper::is_senior_manager() ||
                   RoleHelper::is_team_lead();

        if (!$canView) {
            return response()->json([
                'draw' => intval($request->get('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'You do not have permission to view pullbacked leads.'
            ], 403);
        }

        $baseQuery = Lead::withoutGlobalScope('exclude_pullbacked')
            ->select([
                'id', 'title', 'code', 'phone', 'email',
                'lead_status_id', 'lead_source_id', 'telecaller_id',
                'course_id', 'remarks', 'updated_at'
            ])
            ->with([
                'telecaller:id,name',
                'leadStatus:id,title',
                'leadSource:id,title',
                'course:id,title'
            ])
            ->where('is_pullbacked', 1);

        $recordsTotal = (clone $baseQuery)->count();
        $query = clone $baseQuery;

        $filters = [
            'search_key' => $request->get('search_key'),
            'telecaller_id' => $request->get('telecaller_id'),
            'lead_status_id' => $request->get('lead_status_id'),
            'lead_source_id' => $request->get('lead_source_id'),
            'from_date' => $request->get('from_date'),
            'to_date' => $request->get('to_date'),
        ];

        if ($filters['search_key']) {
            $search = $filters['search_key'];
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('title', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($filters['telecaller_id']) {
            $query->where('telecaller_id', $filters['telecaller_id']);
        }

        if ($filters['lead_status_id']) {
            $query->where('lead_status_id', $filters['lead_status_id']);
        }

        if ($filters['lead_source_id']) {
            $query->where('lead_source_id', $filters['lead_source_id']);
        }

        if ($filters['from_date']) {
            $query->whereDate('updated_at', '>=', $filters['from_date']);
        }

        if ($filters['to_date']) {
            $query->whereDate('updated_at', '<=', $filters['to_date']);
        }

        if ($request->filled('search.value')) {
            $searchValue = $request->input('search.value');
            if ($searchValue !== null && $searchValue !== '') {
                $query->where(function ($subQuery) use ($searchValue) {
                    $subQuery->where('title', 'like', "%{$searchValue}%")
                        ->orWhere('phone', 'like', "%{$searchValue}%")
                        ->orWhere('email', 'like', "%{$searchValue}%");
                });
            }
        }

        $recordsFiltered = $query->count();

        $columns = [
            0 => 'id', // actions column (non-sortable)
            1 => 'id',
            2 => 'title',
            3 => 'phone',
            4 => 'lead_status_id',
            5 => 'lead_source_id',
            6 => 'telecaller_id',
            7 => 'course_id',
            8 => 'updated_at',
            9 => 'remarks',
        ];

        $orderColumnIndex = (int) $request->input('order.0.column', 7);
        $orderDir = $request->input('order.0.dir', 'desc') === 'asc' ? 'asc' : 'desc';
        $orderColumnName = $columns[$orderColumnIndex] ?? 'updated_at';

        if ($orderColumnName === 'id') {
            $query->orderBy('id', $orderDir);
        } else {
            $query->orderBy($orderColumnName, $orderDir);
        }

        $start = (int) $request->get('start', 0);
        $length = (int) $request->get('length', 25);
        if ($length > 0) {
            $query->skip($start)->take($length);
        }

        $leads = $query->get();

        $data = [];
        foreach ($leads as $index => $lead) {
            $viewUrl = route('leads.ajax-show', $lead->id);
            $actionsHtml = '<div class="btn-group" role="group">'
                . '<a href="javascript:void(0);" class="btn btn-sm btn-outline-primary" '
                . 'onclick="show_large_modal(\'' . $viewUrl . '\', \'View Lead\')" '
                . 'title="View Lead"><i class="ti ti-eye"></i></a>'
                . '</div>';

            $nameHtml = '<strong>' . e($lead->title ?? 'N/A') . '</strong><br>'
                . '<small class="text-muted">' . trim(($lead->code ?? '') . ' ' . ($lead->phone ?? '')) . '</small>';

            $contactHtml = '<div>' . e($lead->phone ?? '-') . '</div>'
                . '<div>' . e($lead->email ?? 'No email') . '</div>';

            $statusHtml = '<span class="badge bg-primary">' . e($lead->leadStatus->title ?? 'N/A') . '</span>';
            $sourceText = e($lead->leadSource->title ?? 'N/A');
            $telecallerText = e($lead->telecaller->name ?? 'N/A');
            $courseText = e($lead->course->title ?? 'N/A');
            $pullbackedOn = optional($lead->updated_at)->format('d M Y, h:i A') ?? '-';
            $remarksText = e($lead->remarks ?? 'N/A');

            $data[] = [
                'actions' => $actionsHtml,
                'index' => $start + $index + 1,
                'name' => $nameHtml,
                'contact' => $contactHtml,
                'status' => $statusHtml,
                'source' => $sourceText,
                'telecaller' => $telecallerText,
                'course' => $courseText,
                'pullbacked_on' => $pullbackedOn,
                'remarks' => $remarksText,
            ];
        }

        return response()->json([
            'draw' => intval($request->get('draw', 1)),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    /**
     * Show followup leads modal with filters.
     */
    public function followupLeadsModal(Request $request)
    {
        $canViewFollowupModal = RoleHelper::is_admin_or_super_admin() ||
            RoleHelper::is_general_manager() ||
            RoleHelper::is_senior_manager();

        if (!$canViewFollowupModal) {
            abort(403, 'You do not have permission to view followup leads.');
        }

        $followupStatusIds = [2, 7, 8, 9];

        $telecallers = User::select('id', 'name')
            ->where('role_id', 3)
            ->orderBy('name')
            ->get();

        $leadSources = LeadSource::select('id', 'title')
            ->where('is_active', 1)
            ->orderBy('title')
            ->get()
            ->unique('title')
            ->values();

        $filtersApplied = $request->filled('followup_date')
            || $request->filled('telecaller_id')
            || $request->filled('lead_source_id');

        $leads = collect();

        if ($filtersApplied) {
            $leadsQuery = Lead::select('leads.*')
                ->with([
                    'telecaller:id,name',
                    'latestFollowupActivity' => function ($query) use ($followupStatusIds) {
                        $query->whereIn('lead_status_id', $followupStatusIds);
                    }
                ])
                ->whereIn('lead_status_id', $followupStatusIds)
                ->whereNull('deleted_at');

            if ($request->filled('followup_date')) {
                $leadsQuery->whereDate('followup_date', $request->followup_date);
            }

            if ($request->filled('telecaller_id')) {
                $leadsQuery->where('telecaller_id', $request->telecaller_id);
            }

            if ($request->filled('lead_source_id')) {
                $leadsQuery->where('lead_source_id', $request->lead_source_id);
            }

            $leads = $leadsQuery
                ->distinct('leads.id')
                ->orderByRaw('followup_date IS NULL')
                ->orderBy('followup_date')
                ->orderByDesc('updated_at')
                ->get();
        }

        if ($request->ajax() && $request->boolean('refresh')) {
            $html = view('admin.leads.partials.followup-leads-table', [
                'leads' => $leads,
                'followupStatusIds' => $followupStatusIds,
                'filtersApplied' => $filtersApplied,
            ])->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'count' => $leads->count(),
            ]);
        }

        return view('admin.leads.followup-leads-modal', [
            'leads' => $leads,
            'telecallers' => $telecallers,
            'leadSources' => $leadSources,
            'filters' => [
                'followup_date' => $request->followup_date,
                'telecaller_id' => $request->telecaller_id,
                'lead_source_id' => $request->lead_source_id,
            ],
            'followupStatusIds' => $followupStatusIds,
            'filtersApplied' => $filtersApplied,
        ]);
    }

    /**
     * Show bulk delete form
     */
    public function ajaxBulkDelete()
    {
        $currentUser = AuthHelper::getCurrentUser();
        $isTeamLead = $currentUser && AuthHelper::isTeamLead();
        $isTelecaller = $currentUser && $currentUser->role_id == 3;
        $isSeniorManager = $currentUser && RoleHelper::is_senior_manager();
        
        // Filter telecallers based on role
        if ($isTeamLead) {
            // Team Lead: Show only their team members
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
                $teamMemberIds[] = AuthHelper::getCurrentUserId(); // Include team lead
                $telecallers = User::whereIn('id', $teamMemberIds)->get();
            } else {
                $telecallers = collect([$currentUser]); // Only themselves if no team
            }
        } elseif ($isTelecaller && !$isSeniorManager) {
            // Regular Telecaller: Show only themselves
            $telecallers = collect([$currentUser]);
        } else {
            // Admin/Super Admin/Senior Manager: Show all telecallers
            $telecallers = User::where('role_id', 3)->get();
        }
        
        // No team selection needed for bulk delete - removed teams
        // Senior managers and admins can see all telecallers
        
        $data = [
            'telecallers' => $telecallers,
            'isSeniorManager' => $isSeniorManager,
            'leadStatuses' => LeadStatus::where('is_active', 1)->get(),
            'leadSources' => LeadSource::where('is_active', 1)->get(),
            'countries' => Country::where('is_active', 1)->get(),
            'courses' => Course::where('is_active', 1)->get(),
        ];

        return view('admin.leads.ajax-bulk-delete', $data);
    }

    /**
     * Process bulk delete
     */
    public function bulkDelete(Request $request)
    {
        // Check permission: Admin/Super Admin, General Manager, or Senior Manager only
        $canBulkDelete = RoleHelper::is_admin_or_super_admin() || 
                        RoleHelper::is_general_manager() || 
                        RoleHelper::is_senior_manager();
        
        if (!$canBulkDelete) {
            return redirect()->back()
                ->with('error', 'You do not have permission to bulk delete leads.');
        }
        
        $validator = Validator::make($request->all(), [
            'telecaller_id' => 'required|exists:users,id',
            'lead_date' => 'required|date',
            'lead_source_id' => 'required|exists:lead_sources,id',
            'lead_id' => 'required|array|min:1',
            'lead_id.*' => 'exists:leads,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $successCount = 0;
        foreach ($request->lead_id as $leadId) {
            // Update deleted_by and soft delete directly without loading the full model
            $updated = Lead::where('id', $leadId)->update([
                'deleted_by' => AuthHelper::getCurrentUserId()
            ]);
            
            if ($updated) {
                Lead::where('id', $leadId)->delete();
                $successCount++;
            }
        }

        return redirect()->back()->with('message_success', "Successfully deleted {$successCount} leads!");
    }

    /**
     * Show bulk convert form
     */
    public function ajaxBulkConvert()
    {
        $data = [
            'telecallers' => User::where('role_id', 3)->get(),
            'leadStatuses' => LeadStatus::where('is_active', 1)->get(),
            'leadSources' => LeadSource::where('is_active', 1)->get(),
            'countries' => Country::where('is_active', 1)->get(),
            'courses' => Course::where('is_active', 1)->get(),
        ];

        return view('admin.leads.ajax-bulk-convert', $data);
    }

    /**
     * Process bulk convert
     */
    public function bulkConvert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'telecaller_id' => 'required|exists:users,id',
            'lead_date' => 'required|date',
            'lead_source_id' => 'required|exists:lead_sources,id',
            'lead_id' => 'required|array|min:1',
            'lead_id.*' => 'exists:leads,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $successCount = 0;
        foreach ($request->lead_id as $leadId) {
            $lead = Lead::select(['id', 'title', 'code', 'phone', 'email'])->find($leadId);
            if ($lead) {
                // Get DOB and subject_id from leads_details table
                $leadDetail = \App\Models\LeadDetail::where('lead_id', $leadId)->first();
                $dob = $leadDetail ? $leadDetail->date_of_birth : null;
                $subjectId = $leadDetail ? $leadDetail->subject_id : null;
                
                // Create converted lead record with basic info
                ConvertedLead::create([
                    'lead_id' => $leadId,
                    'name' => $lead->title,
                    'code' => $lead->code,
                    'phone' => $lead->phone,
                    'email' => $lead->email,
                    'dob' => $dob,
                    'subject_id' => $subjectId,
                    'is_b2b' => $lead->is_b2b ?? 0,
                    'remarks' => $request->remarks ?? 'Converted via bulk operation',
                    'created_by' => AuthHelper::getCurrentUserId(),
                ]);

                // Update lead as converted
                Lead::where('id', $leadId)->update([
                    'is_converted' => 1,
                    'updated_by' => AuthHelper::getCurrentUserId(),
                ]);
                
                $successCount++;
            }
        }

        return redirect()->back()->with('message_success', "Successfully converted {$successCount} leads!");
    }

    /**
     * Get leads by source for bulk operations
     */
    public function getLeadsBySource(Request $request)
    {
        $leads = Lead::select([
            'id', 'title', 'code', 'phone', 'email', 'lead_status_id', 'lead_source_id', 
            'course_id', 'telecaller_id', 'place', 'rating', 'interest_status', 
            'followup_date', 'remarks', 'is_converted', 'created_at'
        ])
        ->where('lead_source_id', $request->lead_source_id)
        ->where('telecaller_id', $request->tele_caller_id)
        ->whereDate('created_at', $request->created_at)
        ->with([
            'leadStatus:id,title', 
            'leadSource:id,title', 
            'telecaller:id,name'
        ])
        ->get();

        return view('admin.leads.partials.leads-table-rows', compact('leads'));
    }

    /**
     * Get leads by source for reassign operations
     */
    public function getLeadsBySourceReassign(Request $request)
    {
        $fromDate = date('Y-m-d H:i:s', strtotime($request->from_date . ' 00:00:00'));
        $toDate = date('Y-m-d H:i:s', strtotime($request->to_date . ' 23:59:59'));
        
        $query = Lead::select([
            'id', 'title', 'code', 'phone', 'email', 'lead_status_id', 'lead_source_id', 
            'course_id', 'telecaller_id', 'place', 'rating', 'interest_status', 
            'followup_date', 'remarks', 'is_converted', 'created_at'
        ])
        ->where('lead_source_id', $request->lead_source_id)
        ->where('telecaller_id', $request->tele_caller_id)
        ->where('lead_status_id', $request->lead_status_id)
        ->where('is_converted', 0)
        ->where('created_at', '>=', $fromDate)
        ->where('created_at', '<=', $toDate)
        ->where(function ($q) {
            $q->whereNull('is_b2b')->orWhere('is_b2b', 0);
        });
        
        // Optional course filter - only apply if course_id is provided
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }
        
        $leads = $query->with([
            'leadStatus:id,title', 
            'leadSource:id,title', 
            'telecaller:id,name', 
            'course:id,title'
        ])
        ->get();
        
        return view('admin.leads.partials.leads-table-rows-reassign', compact('leads'));
    }

    /**
     * Show convert lead form
     */
    public function convert(Lead $lead)
    {
        if ($this->leadIsAlreadyConverted($lead)) {
            if (request()->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->view('admin.leads.convert-already-converted', compact('lead'));
            }

            return redirect()->route('leads.index')
                ->with('message_danger', 'This lead has already been converted.');
        }

        $boards = \App\Models\Board::where('is_active', true)->get();
        $country_codes = get_country_code();
        
        // Load lead details to get DOB and other information
        $lead->load(['studentDetails.batch', 'batch']);
        
        // Load the course information if the lead has a course_id
        $course = null;
        $courseAmount = 0;
        $batch = $lead->batch ?: ($lead->studentDetails?->batch);
        $batches = collect();
        $batchAmount = 0.0;
        $batchAmountLabel = null;
        $studentClass = $lead->studentDetails?->class;
        $universityAmount = 0;
        $additionalAmount = 0.0;
        $courseType = null;
        $university = null;
        $totalAmount = 0.0;
        
        if ($lead->course_id) {
            $course = \App\Models\Course::find($lead->course_id);
            $courseAmount = $course ? (float) $course->amount : 0.0;
            $batches = \App\Models\Batch::where('course_id', $lead->course_id)
                ->select('id', 'title', 'amount', 'sslc_amount', 'plustwo_amount', 'b2b_amount', 'is_active')
                ->orderBy('is_active', 'desc')
                ->orderBy('title')
                ->get();
            
            // Check if it's UG/PG course (course_id = 9) and has student details with course_type and university
            if ($lead->course_id == 9 && $lead->studentDetails) {
                $courseType = $lead->studentDetails->course_type;
                $universityId = $lead->studentDetails->university_id;
                
                if ($universityId) {
                    $university = \App\Models\University::find($universityId);
                    if ($university) {
                        if ($courseType === 'UG') {
                            $universityAmount = $university->ug_amount ?? 0;
                        } elseif ($courseType === 'PG') {
                            $universityAmount = $university->pg_amount ?? 0;
                        }
                    }
                }
            }
        }

        // Determine batch amount based on course rules
        if ($batch) {
            // B2B lead: use only batch B2B amount (do not show in-house/other amounts)
            if ((int) ($lead->is_b2b ?? 0) === 1) {
                $batchAmount = $batch->b2b_amount !== null ? (float) $batch->b2b_amount : 0.0;
                $batchAmountLabel = $batch->b2b_amount !== null ? 'B2B Amount' : 'B2B Amount (not set)';
            } else {
                if ($lead->course_id == 16) {
                    $normalizedClass = $studentClass ? strtolower($studentClass) : null;
                    if ($normalizedClass === 'sslc' && !is_null($batch->sslc_amount)) {
                        $batchAmount = (float) $batch->sslc_amount;
                        $batchAmountLabel = 'SSLC Amount';
                    } elseif (!is_null($batch->plustwo_amount)) {
                        $batchAmount = (float) $batch->plustwo_amount;
                        $batchAmountLabel = 'Plus Two Amount';
                    } else {
                        $batchAmount = (float) ($batch->amount ?? 0);
                    }
                } else {
                    $batchAmount = (float) ($batch->amount ?? 0);
                }
            }
        }

        $additionalAmount += (float) $universityAmount;

        // B2B: total is only the batch B2B amount (no course amount)
        if ((int) ($lead->is_b2b ?? 0) === 1) {
            $courseAmount = 0.0;
            $additionalAmount = 0.0;
            $totalAmount = $batchAmount;
        } else {
            $totalAmount = $courseAmount + $batchAmount + $additionalAmount;
        }

        return view('admin.leads.convert-modal', compact(
            'lead',
            'boards',
            'country_codes',
            'batches',
            'course',
            'courseAmount',
            'batchAmount',
            'batch',
            'studentClass',
            'universityAmount',
            'additionalAmount',
            'totalAmount',
            'courseType',
            'university',
            'batchAmountLabel'
        ));
    }

    /**
     * Process lead conversion
     */
    public function convertSubmit(Request $request, Lead $lead)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'dob' => 'nullable|date|before_or_equal:today',
            'board_id' => 'nullable|exists:boards,id',
            'batch_id' => 'nullable|exists:batches,id',
            'remarks' => 'nullable|string|max:1000',
            'payment_collected' => 'boolean',
            'payment_amount' => 'required_if:payment_collected,1|required_if:payment_collected,true|required_if:payment_collected,"1"|nullable|numeric|min:0.01',
            'payment_type' => 'required_if:payment_collected,1|required_if:payment_collected,true|required_if:payment_collected,"1"|nullable|in:Cash,Online,Bank,Cheque,Card,Other',
            'transaction_id' => 'nullable|string|max:255',
            'payment_date' => 'nullable|date',
            'payment_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'custom_total_amount' => 'nullable|numeric|min:0',
            'need_mobile' => 'nullable|boolean',
            'asset_id' => 'nullable|string|max:255',
        ];

        if ((int) $lead->course_id === 23) {
            // Fee breakdown inputs (course_id = 23)
            $rules['fee_pg_amount'] = 'nullable|numeric|min:0';
            $rules['fee_ug_amount'] = 'nullable|numeric|min:0';
            $rules['fee_plustwo_amount'] = 'nullable|numeric|min:0';
            $rules['fee_sslc_amount'] = 'nullable|numeric|min:0';

            // Course 23: payment is split across heads (optional per head)
            $rules['payment_pg_amount'] = 'nullable|numeric|min:0';
            $rules['payment_ug_amount'] = 'nullable|numeric|min:0';
            $rules['payment_plustwo_amount'] = 'nullable|numeric|min:0';
            $rules['payment_sslc_amount'] = 'nullable|numeric|min:0';

            $rules['payment_pg_file'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048';
            $rules['payment_ug_file'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048';
            $rules['payment_plustwo_file'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048';
            $rules['payment_sslc_file'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048';

            // Do not force the single payment fields for course 23
            $rules['payment_amount'] = 'nullable|numeric|min:0';
            $rules['payment_file'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048';
        }

        // Edumaster course does not require batch during conversion.
        if ($lead->course_id && (int) $lead->course_id !== 23) {
            $rules['batch_id'] = 'required|exists:batches,id';
        }

        $validator = Validator::make($request->all(), $rules);

        // Additional conditional validations for course_id = 23 split payments
        $validator->after(function ($validator) use ($request, $lead) {
            if ($request->boolean('need_mobile') && !$request->filled('asset_id')) {
                $validator->errors()->add('asset_id', 'The asset id field is required when Need Mobile is checked.');
            }

            if ($request->filled('batch_id')) {
                $batchBelongsToCourse = \App\Models\Batch::where('id', $request->batch_id)
                    ->where('course_id', $lead->course_id)
                    ->exists();
                if (!$batchBelongsToCourse) {
                    $validator->errors()->add('batch_id', 'Selected batch does not belong to this lead course.');
                }
            }

            if (!$request->boolean('payment_collected')) {
                return;
            }

            if ((int) $lead->course_id !== 23) {
                $customTotal = $request->filled('custom_total_amount') ? (float) $request->input('custom_total_amount') : null;
                $paymentAmount = (float) ($request->input('payment_amount') ?: 0);

                if ($customTotal !== null && $paymentAmount > $customTotal) {
                    $validator->errors()->add('payment_amount', 'Payment amount cannot exceed the total amount.');
                }

                return;
            }

            $pgPaid = (float) ($request->input('payment_pg_amount') ?: 0);
            $ugPaid = (float) ($request->input('payment_ug_amount') ?: 0);
            $plustwoPaid = (float) ($request->input('payment_plustwo_amount') ?: 0);
            $sslcPaid = (float) ($request->input('payment_sslc_amount') ?: 0);

            $totalPaid = $pgPaid + $ugPaid + $plustwoPaid + $sslcPaid;

            if ($totalPaid <= 0) {
                $validator->errors()->add('payment_pg_amount', 'At least one payment amount (PG/UG/Plus Two/SSLC) is required.');
            }

            // If total amount is provided, don't allow paid sum to exceed it
            $customTotal = $request->filled('custom_total_amount') ? (float) $request->input('custom_total_amount') : null;
            if ($customTotal !== null && $totalPaid > $customTotal) {
                $validator->errors()->add('custom_total_amount', 'Total paid amount cannot exceed the total amount.');
            }

            // Require file upload for each head that has a paid amount
            if ($pgPaid > 0 && !$request->hasFile('payment_pg_file')) {
                $validator->errors()->add('payment_pg_file', 'PG payment proof file is required when PG paid amount is entered.');
            }
            if ($ugPaid > 0 && !$request->hasFile('payment_ug_file')) {
                $validator->errors()->add('payment_ug_file', 'UG payment proof file is required when UG paid amount is entered.');
            }
            if ($plustwoPaid > 0 && !$request->hasFile('payment_plustwo_file')) {
                $validator->errors()->add('payment_plustwo_file', 'Plus Two payment proof file is required when Plus Two paid amount is entered.');
            }
            if ($sslcPaid > 0 && !$request->hasFile('payment_sslc_file')) {
                $validator->errors()->add('payment_sslc_file', 'SSLC payment proof file is required when SSLC paid amount is entered.');
            }
        });

        if ($validator->fails()) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please correct the errors below.',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $lead = Lead::where('id', $lead->id)->lockForUpdate()->firstOrFail();

            if ($this->leadIsAlreadyConverted($lead)) {
                DB::rollBack();

                return $this->convertAlreadyConvertedJsonResponse();
            }

            // Get or create lead detail record
            $leadDetail = \App\Models\LeadDetail::where('lead_id', $lead->id)->first();
            if (!$leadDetail) {
                $leadDetail = \App\Models\LeadDetail::create([
                    'lead_id' => $lead->id,
                    'course_id' => $lead->course_id,
                ]);
            }
            
            // Update DOB in lead details if provided
            if ($request->filled('dob')) {
                $leadDetail->update(['date_of_birth' => $request->dob]);
            }
            
            // Get DOB and subject_id for converted lead (from request or existing lead detail)
            $dob = $request->dob ?? ($leadDetail ? $leadDetail->date_of_birth : null);
            $subjectId = $leadDetail ? $leadDetail->subject_id : null;
            $selectedBatchId = $request->filled('batch_id') ? (int) $request->batch_id : null;
            if (!is_null($selectedBatchId)) {
                $leadDetail->update(['batch_id' => $selectedBatchId]);
            }
            
            // Create converted lead record
            $convertedLead = ConvertedLead::create([
                'lead_id' => $lead->id,
                'name' => $request->name,
                'code' => $request->code,
                'phone' => $request->phone,
                'email' => $request->email,
                'dob' => $dob,
                'course_id' => $lead->course_id,
                'batch_id' => $selectedBatchId,
                'board_id' => $request->board_id,
                'subject_id' => $subjectId,
                'is_b2b' => $lead->is_b2b ?? 0,
                'candidate_status_id' => 1,
                'remarks' => $request->remarks,
                'need_mobile' => $request->boolean('need_mobile'),
                'asset_id' => $request->filled('asset_id') ? $request->input('asset_id') : null,
                'created_by' => AuthHelper::getCurrentUserId(),
                'updated_by' => AuthHelper::getCurrentUserId(),
            ]);

            // Update lead as converted
            $leadUpdateData = [
                'is_converted' => true,
                'updated_by' => AuthHelper::getCurrentUserId(),
            ];
            if (!is_null($selectedBatchId)) {
                $leadUpdateData['batch_id'] = $selectedBatchId;
            }
            $lead->update($leadUpdateData);

            // Auto-generate invoice if lead has course_id
            $invoice = null;
            if ($lead->course_id) {
                $invoiceController = new \App\Http\Controllers\InvoiceController();
                $customTotalAmount = null;
                $feeBreakdown = null;

                if ($request->filled('custom_total_amount')) {
                    $customTotalAmount = (float) $request->input('custom_total_amount');
                }

                if ((int) $lead->course_id === 23) {
                    $feeBreakdown = [
                        'fee_pg_amount' => $request->filled('fee_pg_amount') ? (float) $request->input('fee_pg_amount') : null,
                        'fee_ug_amount' => $request->filled('fee_ug_amount') ? (float) $request->input('fee_ug_amount') : null,
                        'fee_plustwo_amount' => $request->filled('fee_plustwo_amount') ? (float) $request->input('fee_plustwo_amount') : null,
                        'fee_sslc_amount' => $request->filled('fee_sslc_amount') ? (float) $request->input('fee_sslc_amount') : null,
                    ];

                    if ($customTotalAmount === null) {
                        $customTotalAmount =
                            (float) (($feeBreakdown['fee_pg_amount'] ?? 0)
                                + ($feeBreakdown['fee_ug_amount'] ?? 0)
                                + ($feeBreakdown['fee_plustwo_amount'] ?? 0)
                                + ($feeBreakdown['fee_sslc_amount'] ?? 0));
                    }
                }

                $invoice = $invoiceController->autoGenerate($convertedLead->id, $lead->course_id, $customTotalAmount, $feeBreakdown);
            }

            // Process payment if collected
            if ($request->payment_collected && $invoice) {
                $paymentController = new \App\Http\Controllers\PaymentController();
                if ((int) $lead->course_id === 23) {
                    $paymentDate = $request->payment_date;
                    $paymentType = $request->payment_type;
                    $transactionId = $request->transaction_id;

                    $splitPayments = [
                        'PG' => ['amount' => (float) ($request->input('payment_pg_amount') ?: 0), 'file' => $request->file('payment_pg_file')],
                        'UG' => ['amount' => (float) ($request->input('payment_ug_amount') ?: 0), 'file' => $request->file('payment_ug_file')],
                        'PLUS_TWO' => ['amount' => (float) ($request->input('payment_plustwo_amount') ?: 0), 'file' => $request->file('payment_plustwo_file')],
                        'SSLC' => ['amount' => (float) ($request->input('payment_sslc_amount') ?: 0), 'file' => $request->file('payment_sslc_file')],
                    ];

                    foreach ($splitPayments as $feeHead => $payload) {
                        if (($payload['amount'] ?? 0) > 0) {
                            $paymentController->autoCreate(
                                $invoice->id,
                                $payload['amount'],
                                $paymentType,
                                $transactionId,
                                $payload['file'],
                                $paymentDate,
                                $feeHead
                            );
                        }
                    }
                } else {
                    $paymentController->autoCreate(
                        $invoice->id,
                        $request->payment_amount,
                        $request->payment_type,
                        $request->transaction_id,
                        $request->file('payment_file'),
                        $request->payment_date
                    );
                }
            }

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lead converted successfully!',
                    'data' => $convertedLead
                ]);
            }

            return redirect()->route('leads.index')
                ->with('message_success', 'Lead converted successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while converting the lead. Please try again.'
                ], 500);
            }

            return redirect()->back()
                ->with('message_danger', 'An error occurred while converting the lead. Please try again.')
                ->withInput();
        }
    }

    private function leadIsAlreadyConverted(Lead $lead): bool
    {
        if ($lead->is_converted) {
            return true;
        }

        return ConvertedLead::where('lead_id', $lead->id)->exists();
    }

    private function convertAlreadyConvertedJsonResponse()
    {
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'This lead has already been converted.',
            ], 409);
        }

        return redirect()->route('leads.index')
            ->with('message_danger', 'This lead has already been converted.');
    }

    /**
     * View Plus Two Follow-Up Questionnaire details for iPhone Challenge leads (lead_source_id = 13).
     */
    public function plusTwoQuestionnaireDetails(Lead $lead)
    {
        if (!RoleHelper::is_admin_or_super_admin() &&
            !RoleHelper::is_telecaller() &&
            !RoleHelper::is_academic_assistant() &&
            !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('leads.index')->with('message_danger', 'Access denied.');
        }

        $questionnaire = $lead->plusTwoFollowUpQuestionnaire;

        if (!$questionnaire) {
            return redirect()->route('leads.index')->with('message_danger', 'No questionnaire found for this lead.');
        }

        return view('admin.leads.plus-two-questionnaire-details', compact('lead', 'questionnaire'));
    }

    /**
     * Get registration details for a lead from leads_details table
     */
    public function getLeadRegistrationDetails(Lead $lead)
    {
        // Check permissions for viewing registration details
        if (!RoleHelper::is_admin_or_super_admin() &&
            !RoleHelper::is_telecaller() &&
            !RoleHelper::is_academic_assistant() &&
            !RoleHelper::is_admission_counsellor() &&
            !RoleHelper::is_general_manager() &&
            !RoleHelper::is_senior_manager()) {
            return redirect()->route('leads.index')->with('message_danger', 'Access denied.');
        }

        try {
            // Load the lead with all necessary relationships
            $lead->load([
                'studentDetails.course', 
                'studentDetails.subject', 
                'studentDetails.batch',
                'studentDetails.subCourse',
                'studentDetails.classTime',
                'studentDetails.sslcCertificates',
                'studentDetails.sslcCertificates.verifiedBy',
                'studentDetails.postGraduationCertificateVerifiedBy',
                'course',
                'leadStatus',
                'leadSource',
                'telecaller',
                'team'
            ]);
            
            if (!$lead->studentDetails) {
                return view('admin.leads.registration-details', compact('lead'))
                    ->with('error', 'No registration details found for this lead.');
            }

            $studentDetail = $lead->studentDetails;
            $country_codes = get_country_code();
            
            // Check if course has sub courses
            // Exclude E-School (course_id = 5) and Eduthanzeel (course_id = 6) from showing sub courses
            $hasSubCourses = false;
            if ($studentDetail->course_id && !in_array($studentDetail->course_id, [5, 6])) {
                $hasSubCourses = \App\Models\SubCourse::where('course_id', $studentDetail->course_id)
                    ->where('is_active', true)
                    ->exists();
            }
            
            // Get class times for the course if it needs time
            $classTimes = collect();
            if ($studentDetail->course_id) {
                $course = \App\Models\Course::find($studentDetail->course_id);
                if ($course && $course->needs_time) {
                    $classTimes = \App\Models\ClassTime::where('course_id', $studentDetail->course_id)
                        ->where('is_active', true)
                        ->get();
                }
            }
            
            return view('admin.leads.registration-details', compact('studentDetail', 'lead', 'country_codes', 'hasSubCourses', 'classTimes'));
            
        } catch (\Exception $e) {
            return view('admin.leads.registration-details', compact('lead'))
                ->with('error', 'Error loading registration details: ' . $e->getMessage());
        }
    }

    public function showApproveModal(Lead $lead)
    {
        $studentDetail = $lead->studentDetails;
        if (!$studentDetail) {
            return response('No registration found.', 404);
        }
        return view('admin.leads.partials.approve-modal', compact('lead', 'studentDetail'));
    }

    public function showRejectModal(Lead $lead)
    {
        $studentDetail = $lead->studentDetails;
        if (!$studentDetail) {
            return response('No registration found.', 404);
        }
        return view('admin.leads.partials.reject-modal', compact('lead', 'studentDetail'));
    }

    public function updateRegistrationStatus(Request $request, Lead $lead)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor() && !RoleHelper::is_academic_assistant()) {
            return response()->json(['success' => false, 'message' => 'Access denied'], 403);
        }
        
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'remark' => 'nullable|string|max:1000'
        ]);

        // Require remark if rejected
        if ($request->status === 'rejected' && !$request->filled('remark')) {
            return response()->json(['success' => false, 'message' => 'Remark is required for rejection.'], 422);
        }

        $studentDetail = $lead->studentDetails;
        if (!$studentDetail) {
            return response()->json(['success' => false, 'message' => 'Registration details not found.'], 404);
        }

        $oldStatus = $studentDetail->status;
        $studentDetail->status = $request->status;
        if ($request->status === 'rejected') {
            $studentDetail->admin_remarks = $request->remark;
        }
        $studentDetail->reviewed_by = AuthHelper::getCurrentUserId();
        $studentDetail->reviewed_at = now();
        $studentDetail->save();

        // Log activity for approval/rejection
        $activityType = $request->status === 'approved' ? 'approval' : 'rejection';
        $description = $request->status === 'approved' 
            ? 'Registration approved' 
            : 'Registration rejected';
        $reason = $request->status === 'approved' 
            ? ($request->remark ? "Approved with remark: " . $request->remark : "Registration approved")
            : ($request->remark ? "Rejected: " . $request->remark : "Registration rejected");

        LeadActivity::create([
            'lead_id' => $lead->id,
            'activity_type' => $activityType,
            'description' => $description,
            'reason' => $reason,
            'created_by' => AuthHelper::getCurrentUserId(),
        ]);

        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
    }

    /**
     * Update document verification status
     */
    public function updateDocumentVerification(Request $request)
    {
        try {
            // Normalize checkbox value before validation
            if ($request->has('need_to_change_document')) {
                $request->merge(['need_to_change_document' => $request->boolean('need_to_change_document')]);
            } else {
                $request->merge(['need_to_change_document' => false]);
            }
            
            $request->validate([
                'lead_detail_id' => 'required|exists:leads_details,id',
                'document_type' => 'required|in:sslc_certificate,plustwo_certificate,plus_two_certificate,ug_certificate,post_graduation_certificate,birth_certificate,passport_photo,adhar_front,adhar_back,signature,other_document',
                'verification_status' => 'required|in:pending,verified',
                'need_to_change_document' => 'sometimes|boolean',
                'new_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
            ]);

            $leadDetail = LeadDetail::findOrFail($request->lead_detail_id);
            $documentType = $request->document_type;
            $verificationStatus = $request->verification_status;
            
            // Check need_to_change_document - handle both string and boolean values
            $needToChangeDocument = false;
            if ($request->has('need_to_change_document')) {
                $value = $request->input('need_to_change_document');
                $needToChangeDocument = ($value == '1' 
                    || $value === 'true' 
                    || $value === true
                    || $value === 1
                    || $request->boolean('need_to_change_document'));
            }
            
            // Use AuthHelper to get the authenticated user
            $currentUserId = AuthHelper::getCurrentUserId();
            // Check if user is authenticated
            if (!$currentUserId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated. Please login again.'
                ], 401);
            }

            // If need to change document is checked, file upload is required
            if ($needToChangeDocument && !$request->hasFile('new_file')) {
                return response()->json([
                    'success' => false,
                    'message' => 'File upload is required when "Need to change document" is checked.'
                ], 422);
            }

            // Update verification fields
            // Handle special cases for field mapping
            $fieldMapping = [
                'plustwo_certificate' => 'plustwo',
                'plus_two_certificate' => 'plus_two',
                'birth_certificate' => 'birth_certificate',
                'sslc_certificate' => 'sslc',
                'ug_certificate' => 'ug',
                'post_graduation_certificate' => 'post_graduation_certificate',
                'passport_photo' => 'passport_photo',
                'adhar_front' => 'adhar_front',
                'adhar_back' => 'adhar_back',
                'signature' => 'signature',
                'other_document' => 'other_document'
            ];
            
            $baseField = $fieldMapping[$documentType] ?? $documentType;
            $verificationField = $baseField . '_verification_status';
            $verifiedByField = $baseField . '_verified_by';
            $verifiedAtField = $baseField . '_verified_at';

            $updateData = [
                $verificationField => $verificationStatus,
                $verifiedByField => $currentUserId,
                $verifiedAtField => now(),
            ];

            // If "Need to change document" is checked, update registration status to pending
            if ($needToChangeDocument) {
                $updateData['status'] = 'pending';
                $updateData['reviewed_by'] = null;
                $updateData['reviewed_at'] = null;
            }

            // Handle file upload if provided
            $isDocumentUpload = false;
            $isDocumentChange = false;
            if ($request->hasFile('new_file')) {
                $file = $request->file('new_file');
                // Use UUID for file naming to avoid conflicts
                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                // Use student-documents directory for consistency with registration forms
                $filePath = $file->storeAs('student-documents', $fileName, 'public');
                
                // Map document type to actual database field
                $fileFieldMapping = [
                    'plustwo_certificate' => 'plustwo_certificate',
                    'plus_two_certificate' => 'plus_two_certificate',
                    'birth_certificate' => 'birth_certificate',
                    'sslc_certificate' => 'sslc_certificate',
                    'ug_certificate' => 'ug_certificate',
                    'post_graduation_certificate' => 'post_graduation_certificate',
                    'passport_photo' => 'passport_photo',
                    'adhar_front' => 'adhar_front',
                    'adhar_back' => 'adhar_back',
                    'signature' => 'signature',
                    'other_document' => 'other_document'
                ];
                
                $fileField = $fileFieldMapping[$documentType] ?? $documentType;
                
                // Check if document already exists (change) or new upload
                $oldDocumentPath = $leadDetail->$fileField;
                $isDocumentChange = !empty($oldDocumentPath) && $needToChangeDocument;
                $isDocumentUpload = empty($oldDocumentPath) || !$needToChangeDocument;
                
                $updateData[$fileField] = $filePath;
            }

            // Update the lead detail
            $leadDetail->update($updateData);
            
            // If "Need to change document" is checked, ensure status is updated separately to guarantee it's saved
            if ($needToChangeDocument) {
                $leadDetail->status = 'pending';
                $leadDetail->reviewed_by = null;
                $leadDetail->reviewed_at = null;
                $leadDetail->save();
            }

            // Log activity for document operations
            $documentName = ucfirst(str_replace('_', ' ', $documentType));
            
            if ($isDocumentUpload && !$isDocumentChange) {
                // New document upload
                LeadActivity::create([
                    'lead_id' => $leadDetail->lead_id,
                    'activity_type' => 'document_upload',
                    'description' => $documentName . ' uploaded',
                    'reason' => "Document: " . $documentName . " | Status: " . ucfirst($verificationStatus),
                    'created_by' => $currentUserId,
                ]);
            } elseif ($isDocumentChange) {
                // Document change
                LeadActivity::create([
                    'lead_id' => $leadDetail->lead_id,
                    'activity_type' => 'document_change',
                    'description' => $documentName . ' changed',
                    'reason' => "Document: " . $documentName . " | Old document replaced with new file | Registration status reset to pending",
                    'created_by' => $currentUserId,
                ]);
            } else {
                // Just verification status change
                LeadActivity::create([
                    'lead_id' => $leadDetail->lead_id,
                    'activity_type' => 'document_verification',
                    'description' => $documentName . ' verification updated',
                    'reason' => "Document: " . $documentName . " | Status: " . ucfirst($verificationStatus),
                    'created_by' => $currentUserId,
                ]);
            }

            // Refresh the model to get latest data
            $leadDetail->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Document verification updated successfully!',
                'data' => $leadDetail->fresh(),
                'status_updated' => $needToChangeDocument,
                'new_status' => $needToChangeDocument ? 'pending' : $leadDetail->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating document verification: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verifySSLCertificate(Request $request)
    {
        try {
            $request->validate([
                'sslc_certificate_id' => 'required|exists:sslc_certificates,id',
                'lead_detail_id' => 'required|exists:leads_details,id',
                'verification_status' => 'required|in:pending,verified',
                'verification_notes' => 'nullable|string|max:1000',
                'need_to_change_document' => 'nullable|boolean',
                'new_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
            ]);

            $sslcCertificate = \App\Models\SSLCertificate::findOrFail($request->sslc_certificate_id);
            $verificationStatus = $request->verification_status;
            
            // Check need_to_change_document - handle both string and boolean values
            $needToChangeDocument = false;
            if ($request->has('need_to_change_document')) {
                $value = $request->input('need_to_change_document');
                $needToChangeDocument = ($value == '1' 
                    || $value === 'true' 
                    || $value === true
                    || $value === 1
                    || $request->boolean('need_to_change_document'));
            }
            
            // Use AuthHelper to get the authenticated user
            $currentUserId = AuthHelper::getCurrentUserId();
            
            // Check if user is authenticated
            if (!$currentUserId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated. Please login again.'
                ], 401);
            }

            // If need to change document is checked, file upload is required
            if ($needToChangeDocument && !$request->hasFile('new_file')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please upload a new document file.'
                ], 400);
            }

            // Handle new file upload if needed
            $isDocumentChange = false;
            if ($needToChangeDocument && $request->hasFile('new_file')) {
                $isDocumentChange = true;
                // Delete old file
                if (Storage::disk('public')->exists($sslcCertificate->certificate_path)) {
                    Storage::disk('public')->delete($sslcCertificate->certificate_path);
                }
                
                // Upload new file
                $file = $request->file('new_file');
                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('student-documents', $fileName, 'public');
                
                // Update certificate with new file
                $sslcCertificate->update([
                    'certificate_path' => $filePath,
                    'original_filename' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientOriginalExtension(),
                    'file_size' => $file->getSize(),
                ]);
            }

            // Update verification status
            $updateData = [
                'verification_status' => $verificationStatus,
                'verified_by' => $currentUserId,
                'verified_at' => now(),
            ];

            if ($request->filled('verification_notes')) {
                $updateData['verification_notes'] = $request->verification_notes;
            }

            $sslcCertificate->update($updateData);
            
            // Get lead detail for activity logging
            $leadDetail = \App\Models\LeadDetail::findOrFail($request->lead_detail_id);
            
            // If "Need to change document" is checked, update registration status to pending
            if ($needToChangeDocument) {
                $leadDetail->status = 'pending';
                $leadDetail->reviewed_by = null;
                $leadDetail->reviewed_at = null;
                $leadDetail->save();
                $leadDetail->refresh();
            }

            // Log activity for SSLC certificate operations
            if ($isDocumentChange) {
                // Document change
                LeadActivity::create([
                    'lead_id' => $leadDetail->lead_id,
                    'activity_type' => 'document_change',
                    'description' => 'SSLC certificate changed',
                    'reason' => 'SSLC certificate file replaced on: ' . now()->format('d-m-Y h:i A') . '. Registration status reset to pending',
                    'created_by' => $currentUserId,
                ]);
            } else {
                // Just verification status change
                LeadActivity::create([
                    'lead_id' => $leadDetail->lead_id,
                    'activity_type' => 'document_verification',
                    'description' => 'SSLC certificate verification updated',
                    'reason' => 'SSLC certificate verification status: ' . ucfirst($verificationStatus) . 
                               ($request->filled('verification_notes') ? ' | Notes: ' . $request->verification_notes : ''),
                    'created_by' => $currentUserId,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'SSLC certificate verification updated successfully.',
                'status_updated' => $needToChangeDocument,
                'new_status' => $needToChangeDocument ? 'pending' : null
            ]);

        } catch (\Exception $e) {
            Log::error('SSLC certificate verification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating SSLC certificate verification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update registration details inline
     */
    public function updateRegistrationDetails(Request $request)
    {
        try {
            $request->validate([
                'lead_detail_id' => 'required|exists:leads_details,id',
                'field' => 'required|string',
                'value' => 'nullable|string|max:1000'
            ]);

            $studentDetail = \App\Models\LeadDetail::findOrFail($request->lead_detail_id);
            $field = $request->field;
            $value = $request->value;

            // Define allowed fields for security
            $allowedFields = [
                'student_name', 'father_name', 'mother_name', 'date_of_birth', 'gender', 'is_employed',
                'email', 'phone', 'whatsapp', 'parents_phone', 'father_contact_number', 'father_contact_code',
                'mother_contact_number', 'mother_contact_code', 'street', 'locality', 'post_office', 'district', 'state', 'pin_code',
                'message', 'subject_id', 'batch_id', 'sub_course_id', 'passed_year', 'programme_type', 'location', 'class_time_id', 'class',
                'course_type', 'edumaster_course_name', 'selected_courses', 'sslc_back_year', 'plustwo_back_year', 'back_year', 'degree_back_year'
            ];

            if (!in_array($field, $allowedFields)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid field for editing.'
                ], 400);
            }

            // Handle phone fields specially
            if (in_array($field, ['phone', 'whatsapp', 'parents_phone', 'father_contact', 'mother_contact'])) {
                if (strpos($value, '|') !== false) {
                    [$code, $number] = explode('|', $value, 2);
                    
                    if ($field === 'phone') {
                        $studentDetail->update([
                            'personal_code' => $code,
                            'personal_number' => $number
                        ]);
                    } elseif ($field === 'whatsapp') {
                        $studentDetail->update([
                            'whatsapp_code' => $code,
                            'whatsapp_number' => $number
                        ]);
                    } elseif ($field === 'parents_phone') {
                        $studentDetail->update([
                            'parents_code' => $code,
                            'parents_number' => $number
                        ]);
                    } elseif ($field === 'father_contact') {
                        $studentDetail->update([
                            'father_contact_code' => $code,
                            'father_contact_number' => $number
                        ]);
                    } elseif ($field === 'mother_contact') {
                        $studentDetail->update([
                            'mother_contact_code' => $code,
                            'mother_contact_number' => $number
                        ]);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid phone number format.'
                    ], 400);
                }
            } elseif ($field === 'class_time_id') {
                // Handle class_time_id - validate course needs_time and class_time belongs to course
                $value = $value ? (int)$value : null;
                
                if ($value) {
                    $course = \App\Models\Course::find($studentDetail->course_id);
                    if (!$course || !$course->needs_time) {
                        return response()->json([
                            'success' => false,
                            'message' => 'This course does not require class time.'
                        ], 400);
                    }
                    
                    $classTime = \App\Models\ClassTime::where('id', $value)
                        ->where('course_id', $studentDetail->course_id)
                        ->where('is_active', true)
                        ->first();
                    if (!$classTime) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid class time selected.'
                        ], 400);
                    }
                }
                
                $studentDetail->update([$field => $value]);
                
                // Reload relationship to get updated value
                $studentDetail->load('classTime');
                $newValue = $studentDetail->classTime 
                    ? date('h:i A', strtotime($studentDetail->classTime->from_time)) . ' - ' . date('h:i A', strtotime($studentDetail->classTime->to_time))
                    : 'N/A';
                
                return response()->json([
                    'success' => true,
                    'message' => 'Class time updated successfully.',
                    'new_value' => $newValue
                ]);
            } elseif (in_array($field, ['subject_id', 'batch_id', 'sub_course_id'])) {
                // Handle ID fields - validate they exist and belong to the course
                $value = $value ? (int)$value : null;
                
                if ($field === 'subject_id' && $value) {
                    if (!in_array($studentDetail->course_id, [1, 2])) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Subject selection is not applicable for this course.'
                        ], 400);
                    }

                    $subject = \App\Models\Subject::where('id', $value)
                        ->where('course_id', $studentDetail->course_id)
                        ->first();
                    if (!$subject) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid subject selected.'
                        ], 400);
                    }
                } elseif ($field === 'batch_id' && $value) {
                    $batch = \App\Models\Batch::where('id', $value)
                        ->where('course_id', $studentDetail->course_id)
                        ->first();
                    if (!$batch) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid batch selected.'
                        ], 400);
                    }
                } elseif ($field === 'sub_course_id' && $value) {
                    $subCourse = \App\Models\SubCourse::where('id', $value)
                        ->where('course_id', $studentDetail->course_id)
                        ->first();
                    if (!$subCourse) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid sub course selected.'
                        ], 400);
                    }
                }
                
                $studentDetail->update([$field => $value]);
                
                if ($field === 'batch_id') {
                    \App\Models\Lead::where('id', $studentDetail->lead_id)->update(['batch_id' => $value]);
                }
                
                // Reload relationships to get updated values
                $studentDetail->load('subject', 'batch', 'subCourse');
                $newValue = null;
                if ($field === 'subject_id') {
                    $newValue = $studentDetail->subject->title ?? 'N/A';
                } elseif ($field === 'batch_id') {
                    $newValue = $studentDetail->batch->title ?? 'N/A';
                } elseif ($field === 'sub_course_id') {
                    $newValue = $studentDetail->subCourse->title ?? 'N/A';
                }
                
                return response()->json([
                    'success' => true,
                    'message' => 'Registration details updated successfully.',
                    'new_value' => $newValue,
                    'updated_id' => $value
                ]);
            } elseif ($field === 'passed_year') {
                // Handle passed_year as integer
                $value = $value ? (int)$value : null;
                $studentDetail->update([$field => $value]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Registration details updated successfully.',
                    'new_value' => $value ?? 'N/A'
                ]);
            } elseif ($field === 'is_employed') {
                // Handle is_employed as boolean
                $value = $value === '1' || $value === 1 || $value === 'true' || $value === true ? 1 : 0;
                $studentDetail->update([$field => $value]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Registration details updated successfully.',
                    'new_value' => $value ? 'Yes' : 'No'
                ]);
            } elseif ($field === 'gender') {
                // Validate gender value
                if (!in_array($value, ['male', 'female'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid gender value.'
                    ], 400);
                }
                $studentDetail->update([$field => $value]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Registration details updated successfully.',
                    'new_value' => ucfirst($value)
                ]);
            } elseif ($field === 'programme_type') {
                // Validate programme_type value
                if (!in_array($value, ['online', 'offline'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid programme type value.'
                    ], 400);
                }
                
                // If changing to online, clear location
                if ($value === 'online') {
                    $studentDetail->update([
                        $field => $value,
                        'location' => null
                ]);
            } else {
                    // If changing to offline, keep location as is (don't clear it)
                    $studentDetail->update([$field => $value]);
                }
                
                return response()->json([
                    'success' => true,
                    'message' => 'Registration details updated successfully.',
                    'new_value' => ucfirst($value),
                    'hide_location' => $value === 'online',
                    'show_location' => $value === 'offline'
                ]);
            } elseif ($field === 'location') {
                // Validate location value
                if (!in_array($value, ['Ernakulam', 'Malappuram'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid location value.'
                    ], 400);
                }
                $studentDetail->update([$field => $value]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Registration details updated successfully.',
                    'new_value' => $value
                ]);
            } elseif ($field === 'class') {
                // Validate class value (for Grameen Mukt Vidhyalayi Shiksha Sansthan course)
                $value = strtolower($value);
                if (!in_array($value, ['sslc', 'plustwo'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid class value. Must be SSLC or Plus Two.'
                    ], 400);
                }
                
                // Only allow editing class for Grameen Mukt Vidhyalayi Shiksha Sansthan course (course_id = 16)
                if ($studentDetail->course_id != 16) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Class field is only applicable for Grameen Mukt Vidhyalayi Shiksha Sansthan course.'
                    ], 400);
                }
                
                $studentDetail->update([$field => $value]);
                
                // Format display value
                $displayValue = ($value === 'sslc') ? 'SSLC' : 'Plus Two';
                
                return response()->json([
                    'success' => true,
                    'message' => 'Registration details updated successfully.',
                    'new_value' => $displayValue
                ]);
            } elseif (in_array($field, ['course_type', 'edumaster_course_name', 'plustwo_subject', 'selected_courses', 'sslc_back_year', 'plustwo_back_year', 'back_year', 'degree_back_year'])) {
                // EduMaster fields
                if ($field === 'course_type') {
                    if (!in_array($value, ['UG', 'PG'])) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid course type. Must be UG or PG.'
                        ], 400);
                    }
                    $studentDetail->update([$field => $value]);
                    return response()->json([
                        'success' => true,
                        'message' => 'Registration details updated successfully.',
                        'new_value' => $value
                    ]);
                }

                if ($field === 'edumaster_course_name') {
                    $studentDetail->update([$field => $value ?: null]);
                    return response()->json([
                        'success' => true,
                        'message' => 'Registration details updated successfully.',
                        'new_value' => $value ?: 'N/A'
                    ]);
                }

                if ($field === 'plustwo_subject') {
                    $studentDetail->update([$field => $value ?: null]);
                    return response()->json([
                        'success' => true,
                        'message' => 'Registration details updated successfully.',
                        'new_value' => $value ?: 'N/A'
                    ]);
                }

                if ($field === 'selected_courses') {
                    $trimmed = $value ? trim($value) : '';
                    $arr = $trimmed === '' ? [] : array_map('trim', explode(',', $trimmed));
                    $encoded = $arr === [] ? null : json_encode($arr);
                    $studentDetail->update(['selected_courses' => $encoded]);
                    $display = $arr === [] ? 'N/A' : implode(', ', $arr);
                    return response()->json([
                        'success' => true,
                        'message' => 'Registration details updated successfully.',
                        'new_value' => $display
                    ]);
                }

                foreach (['sslc_back_year', 'plustwo_back_year', 'back_year', 'degree_back_year'] as $yearField) {
                    if ($field === $yearField) {
                        $year = $value === '' || $value === null ? null : (int) $value;
                        $studentDetail->update([$field => $year]);
                        return response()->json([
                            'success' => true,
                            'message' => 'Registration details updated successfully.',
                            'new_value' => $year !== null ? (string) $year : 'N/A'
                        ]);
                    }
                }
            } else {
                $studentDetail->update([$field => $value]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Registration details updated successfully.',
                    'new_value' => $value
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Registration details update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating registration details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove SSLC certificate
     */
    public function removeSSLCertificate(Request $request)
    {
        try {
            $request->validate([
                'certificate_id' => 'required|exists:sslc_certificates,id'
            ]);

            $certificate = \App\Models\SSLCertificate::findOrFail($request->certificate_id);
            $leadDetail = $certificate->leadDetail;
            
            // Delete the file from storage
            if (Storage::disk('public')->exists($certificate->certificate_path)) {
                Storage::disk('public')->delete($certificate->certificate_path);
            }
            
            // Log activity before deletion
            LeadActivity::create([
                'lead_id' => $leadDetail->lead_id,
                'activity_type' => 'document_remove',
                'description' => 'SSLC certificate removed',
                'reason' => 'SSLC certificate file removed on: ' . now()->format('d-m-Y h:i A'),
                'created_by' => AuthHelper::getCurrentUserId(),
            ]);
            
            // Delete the database record
            $certificate->delete();

            return response()->json([
                'success' => true,
                'message' => 'SSLC certificate removed successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('SSLC certificate removal error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error removing SSLC certificate: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add new SSLC certificate
     */
    public function addSSLCCertificates(Request $request)
    {
        try {
            $request->validate([
                'lead_detail_id' => 'required|exists:leads_details,id',
                'certificates' => 'required|array|min:1',
                'certificates.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048'
            ]);

            $leadDetailId = $request->lead_detail_id;
            $certificateIds = [];

            $leadDetail = \App\Models\LeadDetail::findOrFail($leadDetailId);
            $uploadedFiles = [];

            foreach ($request->file('certificates') as $file) {
                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('student-documents', $fileName, 'public');

                // Create SSLC certificate record
                $sslcCertificate = \App\Models\SSLCertificate::create([
                    'lead_detail_id' => $leadDetailId,
                    'certificate_path' => $filePath,
                    'original_filename' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientOriginalExtension(),
                    'file_size' => $file->getSize(),
                    'verification_status' => 'pending',
                ]);

                $certificateIds[] = $sslcCertificate->id;
                $uploadedFiles[] = $file->getClientOriginalName();
            }

            // Log activity for document upload
            $fileCount = count($uploadedFiles);
            LeadActivity::create([
                'lead_id' => $leadDetail->lead_id,
                'activity_type' => 'document_upload',
                'description' => $fileCount . ' SSLC certificate(s) uploaded',
                'reason' => 'SSLC certificate(s) uploaded: ' . implode(', ', $uploadedFiles),
                'created_by' => AuthHelper::getCurrentUserId(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'SSLC certificate(s) added successfully.',
                'certificate_ids' => $certificateIds
            ]);

        } catch (\Exception $e) {
            Log::error('SSLC certificate addition error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error adding SSLC certificate: ' . $e->getMessage()
            ], 500);
        }
    }

}


