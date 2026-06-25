<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\Course;
use App\Models\Country;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FollowupLeadsController extends Controller
{
    /**
     * List follow-up leads with lazy loading (pagination) and filters.
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

        if (!$this->canAccessFollowupLeads($user)) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied.'
            ], 403);
        }

        $baseQuery = $this->buildBaseQuery($user);
        $totalRecords = (clone $baseQuery)->count();

        $filteredQuery = clone $baseQuery;
        $this->applyFilters($filteredQuery, $request, $user);
        $filteredCount = (clone $filteredQuery)->count();

        // Ordering: Today -> Tomorrow -> Future -> Past
        $filteredQuery->orderByRaw("
            CASE 
                WHEN DATE(followup_date) = CURDATE() THEN 1
                WHEN DATE(followup_date) = DATE_ADD(CURDATE(), INTERVAL 1 DAY) THEN 2
                WHEN DATE(followup_date) > CURDATE() THEN 3
                ELSE 4
            END,
            followup_date ASC
        ");

        $page = max(1, (int) $request->get('page', 1));
        $perPage = max(1, min(100, (int) $request->get('per_page', 25)));

        $leads = (clone $filteredQuery)
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        // Get count of followup leads (filtered data count)
        $followupLeadsCount = $filteredCount;

        $data = $leads->map(function ($lead) {
            $phone = '';
            if ($lead->code && $lead->phone) {
                $phone = '+' . $lead->code . ' ' . $lead->phone;
            } elseif ($lead->phone) {
                $phone = $lead->phone;
            }

            $followupDate = $lead->followup_date
                ? Carbon::parse($lead->followup_date)
                : null;
            $createdAt = $lead->created_at ? Carbon::parse($lead->created_at) : null;

            $lastReasonEntry = $lead->leadActivities->first();

            return array_merge([
                'id' => $lead->id,
                'name' => $lead->title,
                'phone' => $phone,
                'phone_code' => $lead->code,
                'phone_number' => $lead->phone,
                'email' => $lead->email,
                'lead_status' => $lead->leadStatus ? $lead->leadStatus->title : null,
                'lead_status_id' => $lead->lead_status_id,
                'lead_source' => $lead->leadSource ? $lead->leadSource->title : null,
                'lead_source_id' => $lead->lead_source_id,
                'course' => $lead->course ? $lead->course->title : null,
                'course_id' => $lead->course_id,
                'telecaller' => $lead->telecaller ? $lead->telecaller->name : null,
                'telecaller_id' => $lead->telecaller_id,
                'place' => $lead->place,
                'country_id' => $lead->country_id,
                'rating' => $lead->rating,
                'interest_status' => $lead->interest_status,
                'followup_date' => $followupDate ? $followupDate->format('Y-m-d H:i:s') : null,
                'followup_date_formatted' => $followupDate ? $followupDate->format('d M Y h:i A') : null,
                'remarks' => $lead->remarks,
                'last_reason' => $lastReasonEntry ? $lastReasonEntry->reason : null,
                'last_reason_at' => $lastReasonEntry ? $lastReasonEntry->created_at->format('Y-m-d H:i:s') : null,
                'is_converted' => (bool) $lead->is_converted,
                'student_status' => $lead->studentDetails ? $lead->studentDetails->status : null,
                'date' => $createdAt ? $createdAt->format('d-m-Y') : null,
                'time' => $createdAt ? $createdAt->format('h:i A') : null,
                'created_at' => $lead->created_at ? $lead->created_at->format('Y-m-d H:i:s') : null,
            ], \App\Helpers\LeadRegistrationRouteHelper::apiRegistrationFields($lead));
        });

        return response()->json([
            'status' => true,
            'data' => [
                'leads' => $data,
                'followup_leads_count' => $followupLeadsCount,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $filteredCount,
                    'total_all' => $totalRecords,
                    'last_page' => $perPage > 0 ? (int) ceil($filteredCount / $perPage) : 0,
                    'from' => $filteredCount > 0 ? (($page - 1) * $perPage) + 1 : 0,
                    'to' => min($page * $perPage, $filteredCount),
                ],
            ],
        ]);
    }

    /**
     * Provide filter metadata (options) for follow-up leads.
     */
    public function filters(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        if (!$this->canAccessFollowupLeads($user)) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied.'
            ], 403);
        }

        $leadSources = LeadSource::select('id', 'title')
            ->orderBy('title')
            ->get()
            ->map(function ($source) {
                return [
                    'value' => $source->id,
                    'label' => $source->title,
                ];
            });

        $courses = Course::select('id', 'title')
            ->orderBy('title')
            ->get()
            ->map(function ($course) {
                return [
                    'value' => $course->id,
                    'label' => $course->title,
                ];
            });

        $countries = Country::select('id', 'title')
            ->orderBy('title')
            ->get()
            ->map(function ($country) {
                return [
                    'value' => $country->id,
                    'label' => $country->title,
                ];
            });

        $telecallersQuery = User::nonMarketingTelecallers()
            ->select('id', 'name', 'team_id')
            ->orderBy('name');

        if ($user->role_id == 3 && !$user->is_team_lead) {
            $telecallersQuery->where('id', $user->id);
        } elseif ($user->is_team_lead && $user->team_id) {
            $telecallersQuery->where('team_id', $user->team_id);
        }

        $telecallerOptions = $telecallersQuery->get()->map(function ($telecaller) {
            return [
                'value' => $telecaller->id,
                'label' => $telecaller->name,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => [
                'filters' => [
                    'lead_sources' => $leadSources,
                    'courses' => $courses,
                    'countries' => $countries,
                    'telecallers' => $telecallerOptions,
                    'can_filter_by_telecaller' => $user->role_id != 3 || $user->is_team_lead,
                    'search_placeholder' => 'Search by name, phone, or email',
                ],
            ],
        ]);
    }

    /**
     * Build the base query (status = follow-up + role restrictions).
     */
    private function buildBaseQuery($user)
    {
        $query = Lead::select([
                'id',
                'title',
                'code',
                'phone',
                'email',
                'lead_status_id',
                'lead_source_id',
                'course_id',
                'telecaller_id',
                'place',
                'country_id',
                'rating',
                'interest_status',
                'followup_date',
                'remarks',
                'is_converted',
                'created_at',
                'updated_at',
            ])
            ->with([
                'leadStatus:id,title',
                'leadSource:id,title',
                'course:id,title',
                'telecaller:id,name,team_id',
                'studentDetails:id,lead_id,status,course_id',
                'plusTwoFollowUpQuestionnaire:id,lead_id',
                'leadActivities' => function ($query) {
                    $query->select('id', 'lead_id', 'reason', 'created_at', 'activity_type')
                        ->whereNotNull('reason')
                        ->where('reason', '!=', '')
                        ->orderByDesc('created_at')
                        ->limit(1);
                },
            ])
            ->where('lead_status_id', 2)
            ->notConverted()
            ->notDropped();

        $this->applyRoleBasedFilter($query, $user);

        return $query;
    }

    /**
     * Apply search/filter parameters.
     */
    private function applyFilters($query, Request $request, $user)
    {
        $searchKey = $request->get('search_key', $request->get('search'));
        if (!empty($searchKey)) {
            $query->where(function ($q) use ($searchKey) {
                $q->where('title', 'like', "%{$searchKey}%")
                    ->orWhere('phone', 'like', "%{$searchKey}%")
                    ->orWhere('email', 'like', "%{$searchKey}%");
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

        return $query;
    }

    /**
     * Restrict visibility based on the authenticated user's role.
     */
    private function applyRoleBasedFilter($query, $user)
    {
        if (
            $user->role_id == 1 ||
            $user->role_id == 2 ||
            $user->is_senior_manager ||
            in_array($user->role_id, [4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16])
        ) {
            return $query;
        }

        if ($user->is_team_lead) {
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
                $query->where('telecaller_id', $user->id);
            }
        } elseif ($user->role_id == 3) {
            $query->where('telecaller_id', $user->id);
        }

        return $query;
    }

    /**
     * Determine if the authenticated user can access follow-up leads.
     */
    private function canAccessFollowupLeads($user): bool
    {
        if (
            $user->role_id == 1 ||
            $user->role_id == 2 ||
            $user->role_id == 3 ||
            $user->is_team_lead ||
            $user->is_senior_manager ||
            in_array($user->role_id, [4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16])
        ) {
            return true;
        }

        return false;
    }
}


