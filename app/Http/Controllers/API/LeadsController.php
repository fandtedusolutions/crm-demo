<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\LeadSource;
use App\Models\Course;
use App\Models\LeadActivity;
use App\Models\PlusTwoFollowUpQuestionnaire;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class LeadsController extends Controller
{
    private array $followupRequiredStatusIds = [2, 7, 8, 9];
    private const DEMO_BOOKING_STATUS_ID = 6;

    /**
     * Get leads list with lazy loading (pagination)
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

        // Base query for leads - exclude converted leads (same as web page)
        $query = Lead::where('is_converted', 0)
            ->with([
                'leadStatus:id,title',
                'leadSource:id,title',
                'course:id,title',
                'telecaller:id,name',
                'studentDetails:id,lead_id,status',
                'plusTwoFollowUpQuestionnaire:id,lead_id'
            ]);

        // Apply role-based filtering
        $this->applyRoleBasedFilter($query, $user);

        // Apply filters
        if ($request->filled('lead_status_id')) {
            $query->where('lead_status_id', $request->lead_status_id);
        }

        if ($request->filled('lead_source_id')) {
            $query->where('lead_source_id', $request->lead_source_id);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('telecaller_id')) {
            $query->where('telecaller_id', $request->telecaller_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Date range filter
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $fromDate = Carbon::parse($request->date_from)->startOfDay();
            $toDate = Carbon::parse($request->date_to)->endOfDay();
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        }
        // Order by created_at desc
        $query->orderBy('created_at', 'desc');

        // Get total count before pagination
        $leadsCount = (clone $query)->count();

        // Pagination - lazy loading
        $perPage = $request->get('per_page', 15);
        $leads = $query->paginate($perPage);
        Log::info('Last query: ' . $query->toSql());

        // Format leads data
        $formattedLeads = $leads->map(function ($lead) {
            return $this->formatLeadData($lead);
        });

        return response()->json([
            'status' => true,
            'data' => $formattedLeads,
            'leads_count' => $leadsCount,
            'pagination' => [
                'current_page' => $leads->currentPage(),
                'per_page' => $leads->perPage(),
                'total' => $leads->total(),
                'last_page' => $leads->lastPage(),
                'from' => $leads->firstItem(),
                'to' => $leads->lastItem()
            ]
        ], 200);
    }

    /**
     * Get submitted Plus Two follow-up questionnaire details for a lead (lead_source_id = 13).
     */
    public function plusTwoFollowUpDetails(Request $request, $leadId)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $lead = Lead::with([
            'leadStatus:id,title',
            'leadSource:id,title',
            'course:id,title',
            'telecaller:id,name,team_id',
            'plusTwoFollowUpQuestionnaire',
        ])->find($leadId);

        if (!$lead) {
            return response()->json([
                'status' => false,
                'message' => 'Lead not found.',
            ], 404);
        }

        if (!$this->userCanAccessLead((int) $lead->id, $user)) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied for this lead.',
            ], 403);
        }

        if ((int) $lead->lead_source_id !== PlusTwoFollowUpQuestionnaire::LEAD_SOURCE_ID) {
            return response()->json([
                'status' => false,
                'message' => 'Plus Two follow-up questionnaire is not applicable for this lead source.',
            ], 422);
        }

        $questionnaire = $lead->plusTwoFollowUpQuestionnaire;

        if (!$questionnaire) {
            return response()->json([
                'status' => false,
                'message' => 'Plus Two follow-up questionnaire has not been submitted for this lead.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'lead' => [
                    'id' => $lead->id,
                    'name' => $lead->title ?? '',
                    'lead_source_id' => $lead->lead_source_id,
                    'lead_source' => $lead->leadSource?->title ?? '',
                    'course_id' => $lead->course_id,
                    'course_name' => $lead->course?->title ?? '',
                    'lead_status' => $lead->leadStatus?->title ?? '',
                    'telecaller_name' => $lead->telecaller?->name ?? '',
                ],
                'questionnaire' => $this->formatPlusTwoQuestionnaireForApi($questionnaire),
            ],
        ], 200);
    }

    /**
     * Get leads filter data (statuses, sources, courses, rating, telecallers)
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

        $leadStatuses = LeadStatus::select('id', 'title')
            ->orderBy('title')
            ->get()
            ->map(function ($status) {
                return [
                    'id' => $status->id,
                    'title' => $status->title,
                ];
            });

        $leadSources = LeadSource::select('id', 'title')
            ->orderBy('title')
            ->get()
            ->map(function ($source) {
                return [
                    'id' => $source->id,
                    'title' => $source->title,
                ];
            });

        $courses = Course::select('id', 'title')
            ->orderBy('title')
            ->get()
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                ];
            });

        $ratings = collect(range(1, 10))->map(function ($rating) {
            return [
                'value' => $rating,
                'label' => $rating . '/10',
            ];
        });

        $telecallers = $this->getTelecallersForUser($user)->map(function ($telecaller) {
            return [
                'id' => $telecaller->id,
                'name' => $telecaller->name,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => [
                'lead_statuses' => $leadStatuses,
                'lead_sources' => $leadSources,
                'courses' => $courses,
                'ratings' => $ratings,
                'telecallers' => $telecallers,
                'registration_form_courses' => collect(\App\Helpers\LeadRegistrationRouteHelper::courseRegistrationRouteNames())
                    ->map(function ($routeName, $courseId) {
                        return [
                            'course_id' => (int) $courseId,
                            'title' => \App\Helpers\LeadRegistrationRouteHelper::registrationTitle((int) $courseId),
                            'route_name' => $routeName,
                        ];
                    })
                    ->values(),
            ]
        ], 200);
    }

    /**
     * Trigger Voxbay click-to-call for a lead (mobile API parity with CI4)
     */
    public function callLead(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            Log::warning('Call API: Unauthorized access attempt', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $validated = $request->validate([
            'lead_id' => 'required|integer|exists:leads,id'
        ]);

        Log::info('Call API: Call request initiated', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'lead_id' => $validated['lead_id'],
            'ip' => $request->ip()
        ]);

        $leadQuery = Lead::query()
            ->select('id', 'phone', 'code', 'telecaller_id')
            ->where('id', $validated['lead_id']);

        $this->applyRoleBasedFilter($leadQuery, $user);

        $lead = $leadQuery->first();

        if (!$lead) {
            Log::warning('Call API: Lead not found or access denied', [
                'user_id' => $user->id,
                'lead_id' => $validated['lead_id']
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Lead not found or access denied'
            ], 404);
        }

        $extension = $user->ext_no;
        $userPhone = trim(($user->code ?? '') . ($user->phone ?? ''));
        $leadPhone = trim($lead->phone ?? '');
        $leadCode = trim($lead->code ?? '');
        $uidNumber = env('UID_NUMBER');
        $upin = env('UPIN');

        if (empty($extension)) {
            Log::warning('Call API: Extension not configured', [
                'user_id' => $user->id,
                'user_name' => $user->name
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Your extension is not configured. Please contact admin.'
            ], 422);
        }

        if (empty($userPhone)) {
            Log::warning('Call API: User phone/code not configured', [
                'user_id' => $user->id,
                'user_name' => $user->name
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Your phone/code is not configured. Please contact admin.'
            ], 422);
        }

        if (empty($leadPhone) || empty($leadCode)) {
            Log::warning('Call API: Lead phone/code not available', [
                'user_id' => $user->id,
                'lead_id' => $lead->id
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Lead phone/code not available.'
            ], 422);
        }

        if (empty($uidNumber) || empty($upin)) {
            Log::error('Call API: Voxbay credentials not configured');
            return response()->json([
                'status' => false,
                'message' => 'Voxbay credentials are not configured. Please set UID_NUMBER and UPIN.'
            ], 500);
        }

        $destination = $leadCode . $leadPhone;
        $voxbayQuery = [
            'id_dept' => 0,
            'uid' => $uidNumber,
            'upin' => $upin,
            'user_no' => $extension,
            'destination' => $destination,
            'source' => $userPhone,
        ];
        $voxbayUrl = 'https://x.voxbay.com/api/click_to_call?' . http_build_query($voxbayQuery);

        Log::info('Call API: Calling Voxbay URL', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'lead_id' => $lead->id,
            'extension' => $extension,
            'source' => $userPhone,
            'destination' => $destination,
            'url' => $voxbayUrl,
        ]);

        try {
            $response = Http::timeout(30)->get('https://x.voxbay.com/api/click_to_call', $voxbayQuery);

            Log::info('Call API: Voxbay API response received', [
                'user_id' => $user->id,
                'lead_id' => $lead->id,
                'url' => $voxbayUrl,
                'status_code' => $response->status(),
                'response_body' => $response->body()
            ]);
        } catch (\Exception $e) {
            Log::error('Call API: Voxbay service exception', [
                'user_id' => $user->id,
                'lead_id' => $lead->id,
                'url' => $voxbayUrl,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Failed to reach Voxbay service.',
                'error' => $e->getMessage()
            ], 500);
        }

        if ($response->failed()) {
            Log::error('Call API: Voxbay call request failed', [
                'user_id' => $user->id,
                'lead_id' => $lead->id,
                'url' => $voxbayUrl,
                'status_code' => $response->status(),
                'response_body' => $response->body()
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Voxbay call request failed.',
                'response' => $response->body()
            ], $response->status() ?: 500);
        }

        Log::info('Call API: Call initiated successfully', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'lead_id' => $lead->id,
            'destination' => $destination,
            'extension' => $extension,
            'source' => $userPhone,
            'url' => $voxbayUrl,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Call initiated successfully.',
            'data' => [
                'destination' => $destination,
                'extension' => $extension,
                'source' => $userPhone,
                'vox_response' => $response->json() ?? $response->body()
            ]
        ], 200);
    }

    /**
     * Fetch status update data (mirrors web modal content).
     */
    public function statusUpdateData(Request $request, Lead $lead)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        if (!$this->userCanAccessLead($lead->id, $user)) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied for this lead.'
            ], 403);
        }

        $canViewPullbackHistory = in_array($user->role_id, [1, 2, 11], true);

        $lead->load([
            'leadStatus:id,title',
            'studentDetails:id,lead_id,status,course_id',
            'plusTwoFollowUpQuestionnaire:id,lead_id',
            'leadActivities' => function ($query) use ($canViewPullbackHistory) {
                $query->select('id', 'lead_id', 'reason', 'created_at', 'activity_type', 'description', 'remarks', 'rating', 'followup_date', 'created_by', 'lead_status_id', 'is_pullbacked')
                    ->with(['leadStatus:id,title', 'createdBy:id,name'])
                    ->orderByDesc('created_at')
                    ->limit(10);

                if (!$canViewPullbackHistory) {
                    $query->where(function ($subQuery) {
                        $subQuery->whereNull('is_pullbacked')
                            ->orWhere('is_pullbacked', 0);
                    });
                }
            },
        ]);

        $leadStatuses = LeadStatus::select('id', 'title')
            ->orderBy('title')
            ->get();

        $courses = Course::active()
            ->orderBy('title')
            ->get(['id', 'title']);

        $previousReason = $lead->leadActivities
            ->firstWhere(function ($activity) {
                return !empty($activity->reason);
            })
            ?->reason;

        $activityHistory = $lead->leadActivities->map(function ($activity) {
            return [
                'id' => $activity->id,
                'activity_type' => $activity->activity_type,
                'status' => $activity->leadStatus ? [
                    'id' => $activity->leadStatus->id,
                    'title' => $activity->leadStatus->title,
                ] : null,
                'reason' => $activity->reason,
                'description' => $activity->description,
                'remarks' => $activity->remarks,
                'rating' => $activity->rating,
                'followup_date' => $activity->followup_date ? $activity->followup_date->format('Y-m-d') : null,
                'created_at' => $activity->created_at ? $activity->created_at->format('Y-m-d H:i:s') : null,
                'created_at_human' => $activity->created_at ? $activity->created_at->format('M d, h:i A') : null,
                'created_by' => $activity->createdBy ? [
                    'id' => $activity->createdBy->id,
                    'name' => $activity->createdBy->name,
                ] : null,
            ];
        })->values();

        $leadData = array_merge([
            'id' => $lead->id,
            'name' => $lead->title,
            'phone_code' => $lead->code,
            'phone_number' => $lead->phone,
            'phone_formatted' => $this->formatPhoneNumber($lead->code, $lead->phone),
            'current_status' => $lead->leadStatus ? [
                'id' => $lead->leadStatus->id,
                'title' => $lead->leadStatus->title,
            ] : null,
            'course_id' => $lead->course_id,
            'rating' => $lead->rating,
            'remarks' => $lead->remarks,
            'followup_date' => $lead->followup_date ? Carbon::parse($lead->followup_date)->format('Y-m-d') : null,
        ], \App\Helpers\LeadRegistrationRouteHelper::apiRegistrationFields($lead));

        return response()->json([
            'status' => true,
            'data' => [
                'lead' => $leadData,
                'lead_statuses' => $leadStatuses,
                'courses' => $courses,
                'followup_required_status_ids' => $this->followupRequiredStatusIds,
                'demo_booking_status_id' => self::DEMO_BOOKING_STATUS_ID,
                'defaults' => [
                    'reason' => $previousReason,
                    'remarks' => $lead->remarks,
                    'rating' => $lead->rating,
                    'date' => now()->format('Y-m-d'),
                    'time' => now()->format('H:i'),
                    'followup_date' => $lead->followup_date ? Carbon::parse($lead->followup_date)->format('Y-m-d') : null,
                ],
                'activity_history' => $activityHistory,
            ],
        ]);
    }

    /**
     * Update a lead's status (API equivalent of web status-update flow).
     */
    public function statusUpdate(Request $request, Lead $lead)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        if (!$this->userCanAccessLead($lead->id, $user)) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied for this lead.'
            ], 403);
        }

        $followupRequiredStatuses = $this->followupRequiredStatusIds;

        $rules = [
            'lead_status_id' => 'required|exists:lead_statuses,id',
            'reason' => 'required|string|max:255',
            'remarks' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:10',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'course_id' => 'nullable|exists:courses,id',
            'followup_date' => 'nullable|date',
        ];

        if (in_array((int) $request->lead_status_id, $followupRequiredStatuses, true)) {
            $rules['followup_date'] = 'required|date|after_or_equal:today';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $leadStatus = LeadStatus::find($request->lead_status_id);

        if (!$leadStatus) {
            return response()->json([
                'status' => false,
                'message' => 'Selected status is no longer available.'
            ], 422);
        }

        try {
            $activityTimestamp = Carbon::parse($request->date . ' ' . $request->time);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid activity date or time provided.'
            ], 422);
        }

        $currentStatusTitle = optional($lead->leadStatus)->title ?? 'Unknown';
        $newStatusTitle = $leadStatus->title;
        $interestStatus = $leadStatus->interest_status;
        $currentUserId = $user->id;

        $leadUpdateData = [
            'lead_status_id' => $leadStatus->id,
            'interest_status' => $interestStatus,
            'rating' => $request->rating,
            'remarks' => $request->remarks,
            'updated_by' => $currentUserId,
            'followup_date' => null,
        ];

        if ($request->filled('course_id')) {
            $leadUpdateData['course_id'] = $request->course_id;
        }

        if (in_array($leadStatus->id, $followupRequiredStatuses, true) && $request->followup_date) {
            $leadUpdateData['followup_date'] = $request->followup_date;
        }

        $statusChangeRemark = "Status changed from '{$currentStatusTitle}' to '{$newStatusTitle}'";
        $finalRemarks = $statusChangeRemark;

        if (!empty($request->remarks)) {
            $finalRemarks .= " | User Note: " . $request->remarks;
        }

        $activityPayload = [
            'lead_id' => $lead->id,
            'lead_status_id' => $leadStatus->id,
            'activity_type' => 'status_update',
            'description' => 'Status updated to ' . $newStatusTitle,
            'reason' => $request->reason,
            'rating' => $request->rating,
            'remarks' => $finalRemarks,
            'created_by' => $currentUserId,
            'updated_by' => $currentUserId,
        ];

        if (in_array($leadStatus->id, $followupRequiredStatuses, true) && $request->followup_date) {
            $activityPayload['followup_date'] = $request->followup_date;
        }

        try {
            DB::transaction(function () use ($lead, $leadUpdateData, $activityPayload, $activityTimestamp) {
                $updatePayload = $leadUpdateData;
                $updatePayload['updated_at'] = $activityTimestamp;

                $updated = Lead::where('id', $lead->id)->update($updatePayload);

                if (!$updated) {
                    throw new \RuntimeException('Failed to update lead record.');
                }

                $lead->leadActivities()->create(array_merge($activityPayload, [
                    'created_at' => $activityTimestamp,
                    'updated_at' => $activityTimestamp,
                ]));
            });
        } catch (\Exception $e) {
            Log::error('API lead status update failed', [
                'lead_id' => $lead->id,
                'user_id' => $currentUserId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while updating the status.'
            ], 500);
        }

        $lead->refresh();
        $lead->load(['leadStatus', 'leadSource', 'course', 'telecaller']);

        return response()->json([
            'status' => true,
            'message' => 'Lead status updated successfully.',
            'data' => $lead,
        ]);
    }

    /**
     * Format lead data for API response
     */
    private function formatLeadData($lead)
    {
        // Format phone with code
        $phone = '';
        if ($lead->code && $lead->phone) {
            $phone = '+' . $lead->code . ' ' . $lead->phone;
        } elseif ($lead->phone) {
            $phone = $lead->phone;
        }

        // Calculate profile completeness
        $requiredFields = [
            'title', 'gender', 'age', 'phone', 'code', 'whatsapp', 'whatsapp_code',
            'email', 'qualification', 'country_id', 'interest_status', 'lead_status_id',
            'lead_source_id', 'address', 'telecaller_id', 'team_id', 'place'
        ];
        $completedFields = 0;
        
        foreach ($requiredFields as $field) {
            if (!empty($lead->$field)) {
                $completedFields++;
            }
        }
        
        $profileCompletedPercentage = round(($completedFields / count($requiredFields)) * 100);

        $registrationFields = \App\Helpers\LeadRegistrationRouteHelper::apiRegistrationFields($lead);
        $isLeadRegFormSubmitted = $registrationFields['is_lead_reg_form_submitted'];

        // Format follow_up_date
        $followUpDate = $lead->followup_date ? Carbon::parse($lead->followup_date)->format('d-m-Y') : '';

        // Split created_at into date and time
        $createdAt = Carbon::parse($lead->created_at);
        $date = $createdAt->format('d-m-Y');
        $time = $createdAt->format('h:i A');

        $registrationDetailsStatus = $isLeadRegFormSubmitted ? $this->getRegistrationDetailsStatus($lead) : '';

        return array_merge([
            'id' => $lead->id,
            'name' => $lead->title ?? '',
            'profile_completed_percentage' => $profileCompletedPercentage,
            'lead_status_id' => $lead->lead_status_id,
            'phone' => $phone,
            'email' => $lead->email ?? '',
            'lead_status' => $lead->leadStatus ? $lead->leadStatus->title : '',
            'rating' => $lead->rating ?? '',
            'lead_source' => $lead->leadSource ? $lead->leadSource->title : '',
            'course_name' => $lead->course ? $lead->course->title : '',
            'course_id' => $lead->course_id,
            'telecaller_name' => $lead->telecaller ? $lead->telecaller->name : '',
            'remarks' => $this->stripHtmlContent($lead->remarks ?? ''),
            'marketing_remarks' => $this->stripHtmlContent($lead->marketing_remarks ?? ''),
            'date' => $date,
            'time' => $time,
            'follow_up_date' => $followUpDate,
            'registration_details_status' => $registrationDetailsStatus,
            'can_convert' => $this->canConvertLead($lead),
            'created_at' => $createdAt->format('d-m-Y h:i A'),
        ], $registrationFields);
    }


    /**
     * Apply role-based filtering to leads queries
     */
    private function applyRoleBasedFilter($query, $user)
    {
        // Roles that can see all leads (admin, super admin, managers, etc.)
        if ($this->canViewAllLeads($user)) {
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
     * Check if user can view all leads/telecallers
     */
    private function canViewAllLeads($user)
    {
        return $user->role_id == 1 || // Super Admin
            $user->role_id == 2 || // Admin
            $user->is_senior_manager ||
            in_array($user->role_id, [4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16]);
    }

    /**
     * Get telecaller list based on user role (same logic as web)
     */
    private function getTelecallersForUser($user)
    {
        $telecallerQuery = User::select('id', 'name', 'team_id')
            ->where('role_id', 3)
            ->whereNull('deleted_at')
            ->orderBy('name');

        if ($this->canViewAllLeads($user)) {
            return $telecallerQuery->get();
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

                return User::select('id', 'name')
                    ->whereIn('id', array_unique($teamMemberIds))
                    ->orderBy('name')
                    ->get();
            }

            return User::select('id', 'name')
                ->where('id', $user->id)
                ->get();
        }

        if ($user->role_id == 3) {
            return User::select('id', 'name')
                ->where('id', $user->id)
                ->get();
        }

        return collect();
    }

    /**
     * Determine whether a lead can be converted (matches admin view logic)
     */
    private function canConvertLead($lead): int
    {
        if ($lead->is_converted || !$lead->studentDetails) {
            return 0;
        }

        $studentStatus = strtolower($lead->studentDetails->status ?? '');

        return $studentStatus === 'approved' ? 1 : 0;
    }

    /**
     * Get registration details status label for API consumers
     */
    private function getRegistrationDetailsStatus($lead): string
    {
        $status = strtolower($lead->studentDetails->status ?? '');

        return match ($status) {
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => 'Pending',
        };
    }

    /**
     * Remove HTML content from text and normalize whitespace
     */
    private function stripHtmlContent(string $text): string
    {
        $stripped = strip_tags($text);

        // Normalize line endings and collapse consecutive blank lines
        $stripped = preg_replace("/\r\n|\r/", "\n", $stripped);
        $stripped = preg_replace("/\n{2,}/", "\n", $stripped);

        // Replace newlines with a single space and collapse extra whitespace
        $stripped = str_replace("\n", ' ', $stripped);
        $stripped = preg_replace("/[ \t]+/", ' ', $stripped);

        return trim($stripped);
    }

    /**
     * Ensure the authenticated user can access the specific lead.
     */
    private function userCanAccessLead(int $leadId, $user): bool
    {
        $query = Lead::query()->where('id', $leadId);
        $this->applyRoleBasedFilter($query, $user);

        return $query->exists();
    }

    /**
     * Format phone/code similar to the web modal.
     */
    private function formatPhoneNumber(?string $code, ?string $number): ?string
    {
        if (!$number) {
            return null;
        }

        if ($code) {
            return '+' . ltrim($code, '+') . ' ' . $number;
        }

        return $number;
    }

    /**
     * Format Plus Two questionnaire record for API response.
     */
    private function formatPlusTwoQuestionnaireForApi(PlusTwoFollowUpQuestionnaire $questionnaire): array
    {
        $submittedAt = Carbon::parse($questionnaire->created_at);
        $followupTime = $questionnaire->followup_time
            ? Carbon::parse($questionnaire->followup_time)->format('h:i A')
            : '';

        return [
            'id' => $questionnaire->id,
            'lead_id' => $questionnaire->lead_id,
            'submitted_at' => $submittedAt->format('d-m-Y h:i A'),
            'submitted_date' => $submittedAt->format('d-m-Y'),
            'submitted_time' => $submittedAt->format('h:i A'),
            'name' => $questionnaire->name ?? '',
            'mobile_number' => $questionnaire->mobile_number ?? '',
            'section_1_result_status' => [
                'received_plus_two_result' => $this->plusTwoLabel('yes_no', $questionnaire->received_plus_two_result),
                'result_outcome' => $this->plusTwoLabel('result_outcome', $questionnaire->result_outcome),
                'stream_completed' => $this->plusTwoLabel('stream_completed', $questionnaire->stream_completed),
            ],
            'section_2_future_plan' => [
                'current_plan' => $this->plusTwoLabel('current_plan', $questionnaire->current_plan),
                'college_selection' => $this->plusTwoLabel('college_selection', $questionnaire->college_selection),
                'planned_course' => $questionnaire->planned_course ?? '',
                'course_selection_reason' => $questionnaire->course_selection_reason ?? '',
            ],
            'section_3_decision_stage' => [
                'admission_started' => $this->plusTwoLabel('yes_no', $questionnaire->admission_started),
                'decision_maker' => $this->plusTwoLabel('decision_maker', $questionnaire->decision_maker),
            ],
            'section_4_pain_point' => [
                'career_clarity_level' => $this->plusTwoLabel('career_clarity_level', $questionnaire->career_clarity_level),
                'biggest_challenge' => $questionnaire->biggest_challenge ?? '',
            ],
            'section_5_opportunity_qualification' => [
                'guidance_interested_level' => $this->plusTwoLabel('guidance_interested_level', $questionnaire->guidance_interested_level),
                'counseling_preference' => $this->plusTwoLabel('counseling_preference', $questionnaire->counseling_preference),
                'best_contact_time' => $questionnaire->best_contact_time ?? '',
            ],
            'summary' => [
                'result_status' => $questionnaire->result_status ?? '',
                'stream' => $questionnaire->stream ?? '',
                'future_plan' => $questionnaire->future_plan ?? '',
                'course_interested' => $questionnaire->course_interested ?? '',
                'college_selected' => $questionnaire->college_selected ?? '',
                'decision_maker' => $questionnaire->decision_maker_summary ?? '',
                'career_clarity' => $questionnaire->career_clarity ?? '',
                'main_challenge' => $questionnaire->main_challenge ?? '',
                'guidance_interested' => $questionnaire->guidance_interested ?? '',
                'followup_date' => $questionnaire->followup_date
                    ? Carbon::parse($questionnaire->followup_date)->format('d-m-Y')
                    : '',
                'followup_time' => $followupTime,
            ],
        ];
    }

    /**
     * Human-readable labels for Plus Two questionnaire enum values.
     */
    private function plusTwoLabel(string $field, ?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        $maps = [
            'yes_no' => [
                'yes' => 'Yes',
                'no' => 'No',
            ],
            'result_outcome' => [
                'passed' => 'Passed',
                'failed' => 'Failed',
                'improvement' => 'Improvement',
            ],
            'stream_completed' => [
                'science' => 'Science',
                'commerce' => 'Commerce',
                'humanities' => 'Humanities',
            ],
            'current_plan' => [
                'degree' => 'Degree',
                'professional_course' => 'Professional Course',
                'government_exam' => 'Government Exam Preparation',
                'job' => 'Job',
                'abroad_studies' => 'Abroad Studies',
                'business' => 'Business',
                'not_decided' => 'Not Decided Yet',
            ],
            'college_selection' => [
                'finalized' => 'Finalized',
                'shortlisted' => 'Shortlisted',
                'not_decided' => 'Not Decided',
            ],
            'decision_maker' => [
                'self' => 'Self',
                'parents' => 'Parents',
                'both_together' => 'Both Together',
                'guardian' => 'Guardian',
            ],
            'career_clarity_level' => [
                'yes' => 'Yes',
                'somewhat' => 'Somewhat',
                'no' => 'No',
            ],
            'guidance_interested_level' => [
                'yes' => 'Yes',
                'maybe' => 'Maybe',
                'no' => 'No',
            ],
            'counseling_preference' => [
                'online' => 'Online',
                'direct' => 'Direct',
                'either' => 'Either',
            ],
        ];

        return $maps[$field][$value] ?? ucfirst(str_replace('_', ' ', $value));
    }
}

