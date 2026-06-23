<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MarketingLead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Helpers\AuthHelper;

class MarketingLeadsController extends Controller
{
    /**
     * Get marketing leads list with lazy loading and filters
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

        $isMarketing = $user->role_id == 13;
        $isAdminOrManager = $user->role_id == 1 || $user->role_id == 2 || $user->is_senior_manager;

        if (!$isMarketing && !$isAdminOrManager) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied.'
            ], 403);
        }

        // Build base query
        $query = MarketingLead::with([
            'marketingBde:id,name', 
            'createdBy:id,name',
            'lead.leadStatus',
            'lead.telecaller:id,name'
        ]);
        
        // If marketing user, only show their own leads
        if ($isMarketing) {
            $query->where('marketing_bde_id', $user->id);
        }
        
        // Get total count before filtering
        $totalRecords = $query->count();
        
        // Apply filters
        if ($request->filled('date_from')) {
            $query->where('date_of_visit', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date_of_visit', '<=', $request->date_to);
        }
        if ($request->filled('bde_id')) {
            $query->where('marketing_bde_id', $request->bde_id);
        }
        if ($request->filled('is_assigned')) {
            $query->where('is_telecaller_assigned', $request->is_assigned == '1');
        }
        
        // Apply search
        if ($request->filled('search')) {
            $searchValue = $request->search;
            $query->where(function($q) use ($searchValue) {
                $q->where('lead_name', 'LIKE', "%{$searchValue}%")
                  ->orWhere('phone', 'LIKE', "%{$searchValue}%")
                  ->orWhere('whatsapp', 'LIKE', "%{$searchValue}%")
                  ->orWhere('location', 'LIKE', "%{$searchValue}%")
                  ->orWhere('address', 'LIKE', "%{$searchValue}%")
                  ->orWhere('remarks', 'LIKE', "%{$searchValue}%");
            });
        }
        
        // Get filtered count
        $filteredCount = $query->count();
        
        // Apply ordering
        $orderBy = $request->get('order_by', 'created_at');
        $orderDir = $request->get('order_dir', 'desc');
        $query->orderBy($orderBy, $orderDir);
        
        // Apply pagination (lazy loading)
        $pageInput = $request->get('page');
        $perPageInput = $request->get('per_page');
        
        $page = !empty($pageInput) && is_numeric($pageInput) ? (int)$pageInput : 1;
        $perPage = !empty($perPageInput) && is_numeric($perPageInput) ? (int)$perPageInput : 25;
        
        // Ensure minimum values and max limit
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage)); // Limit max per_page to 100
        
        $marketingLeads = $query->skip(($page - 1) * $perPage)->take($perPage)->get();
        
        // Build data array
        $data = $marketingLeads->map(function ($lead) {
            // Format phone with code
            $phone = '';
            if ($lead->code && $lead->phone) {
                $phone = '+' . $lead->code . ' ' . $lead->phone;
            } elseif ($lead->phone) {
                $phone = $lead->phone;
            }

            // Format whatsapp with code
            $whatsapp = '';
            if ($lead->whatsapp_code && $lead->whatsapp) {
                $whatsapp = '+' . $lead->whatsapp_code . ' ' . $lead->whatsapp;
            } elseif ($lead->whatsapp) {
                $whatsapp = $lead->whatsapp;
            }

            $relatedLead = $lead->lead;
            
            return [
                'id' => $lead->id,
                'date_of_visit' => $lead->date_of_visit ? $lead->date_of_visit->format('Y-m-d') : null,
                'date_of_visit_formatted' => $lead->date_of_visit ? $lead->date_of_visit->format('M d, Y') : null,
                'bde_name' => $lead->marketingBde ? $lead->marketingBde->name : null,
                'bde_id' => $lead->marketing_bde_id,
                'lead_name' => $lead->lead_name,
                'phone' => $phone,
                'code' => $lead->code,
                'phone_number' => $lead->phone,
                'whatsapp' => $whatsapp,
                'whatsapp_code' => $lead->whatsapp_code,
                'whatsapp_number' => $lead->whatsapp,
                'address' => $lead->address,
                'location' => $lead->location,
                'house_number' => $lead->house_number,
                'latitude' => $lead->latitude,
                'longitude' => $lead->longitude,
                'lead_type' => $lead->lead_type,
                'interested_courses' => $lead->interested_courses ?? [],
                'remarks' => $lead->remarks,
                'telecaller_remarks' => $relatedLead ? $relatedLead->remarks : null,
                'lead_status' => $relatedLead && $relatedLead->leadStatus ? $relatedLead->leadStatus->title : null,
                'lead_status_id' => $relatedLead ? $relatedLead->lead_status_id : null,
                'telecaller_name' => $relatedLead && $relatedLead->telecaller ? $relatedLead->telecaller->name : null,
                'telecaller_id' => $relatedLead ? $relatedLead->telecaller_id : null,
                'is_telecaller_assigned' => $lead->is_telecaller_assigned ? true : false,
                'assigned_at' => $lead->assigned_at ? $lead->assigned_at->format('Y-m-d H:i:s') : null,
                'assigned_at_formatted' => $lead->assigned_at ? $lead->assigned_at->format('M d, Y H:i') : null,
                'created_at' => $lead->created_at->format('Y-m-d H:i:s'),
                'created_at_formatted' => $lead->created_at->format('M d, Y H:i'),
                'created_by' => $lead->createdBy ? $lead->createdBy->name : null,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => [
                'leads' => $data,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $filteredCount,
                    'total_all' => $totalRecords,
                    'last_page' => ceil($filteredCount / $perPage),
                    'from' => (($page - 1) * $perPage) + 1,
                    'to' => min($page * $perPage, $filteredCount)
                ]
            ]
        ], 200);
    }

    /**
     * Get D2D form messages
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function d2dFormMessages()
    {
        return response()->json([
            'status' => true,
            'data' => [
                'top_message' => 'A door-to-door marketing campaign for Skill Park is a community-based outreach initiative designed to promote the institute\'s training programs and skill development courses directly to potential students and families. In this campaign, Skill Park representatives visit homes, schools, and local areas to create awareness about various courses, government-certified programs, and career opportunities offered by the institution. The team explains the benefits of skill-based education, distributes brochures, collects lead information, and answers queries face-to-face. This personal interaction helps build trust within the community and ensures that even those with limited digital access learn about Skill Park\'s offerings. The campaign aims to increase enrollments, strengthen community relationships, and spread the message of empowering youth through practical skills and career-oriented training.',
                'lead_information' => 'Lead information refers to the collection of details about a potential customer who has shown interest in a product or service. It typically includes the lead\'s name, contact number, email address, and location, along with the source from which the lead was generated, such as social media, referrals, or door-to-door campaigns. Additionally, it records the lead\'s specific interest or inquiry, current status (like new, contacted, or converted), and any planned follow-up dates. Notes or remarks may also be added to capture extra details from conversations or interactions. This information helps businesses track, manage, and nurture potential customers effectively, ensuring that each lead is followed up and guided smoothly through the sales process.',
                'lead_category' => 'Lead category refers to the classification of potential customers based on their level of interest and likelihood of conversion. This helps businesses or marketing teams prioritize their efforts and plan suitable follow-up actions. For Skill Park, leads can be categorized as hot, warm, cold, or not interested. Hot leads are those who are highly interested and ready to enroll, while warm leads show potential but may need more information or time to decide. Cold leads have been contacted but currently show little interest, and not interested leads are those who have declined or are not suitable for the offered programs. Categorizing leads in this way enables Skill Park to manage outreach efficiently, focus on the most promising prospects, and improve overall conversion rates.'
            ]
        ], 200);
    }

    /**
     * Get marketing leads form data (BDE list, Lead Type, Interested Courses)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function formData(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $isMarketing = $user->role_id == 13;
        $isAdminOrManager = $user->role_id == 1 || $user->role_id == 2 || $user->is_senior_manager;

        if (!$isMarketing && !$isAdminOrManager) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied.'
            ], 403);
        }

        // Get BDE list (marketing users)
        $bdeList = [];
        if ($isMarketing) {
            // If marketing user, include only their own info
            $bdeList = [
                [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ];
        } else {
            // If admin/manager, get all marketing users
            $bdeList = User::where('role_id', 13)
                ->where('is_active', true)
                ->orderBy('name')
                ->get()
                ->map(function ($bdeUser) {
                    return [
                        'id' => $bdeUser->id,
                        'name' => $bdeUser->name,
                        'email' => $bdeUser->email
                    ];
                })
                ->toArray();
        }

        // Lead Type options
        $leadTypes = [
            [
                'value' => 'Student',
                'label' => 'Student'
            ],
            [
                'value' => 'Parent',
                'label' => 'Parent'
            ],
            [
                'value' => 'Working Professional',
                'label' => 'Working Professional'
            ],
            [
                'value' => 'Institution Representative',
                'label' => 'Institution Representative'
            ],
            [
                'value' => 'Others',
                'label' => 'Others'
            ]
        ];

        // Interested Courses options (from the form)
        $interestedCourses = [
            [
                'value' => 'SSLC',
                'label' => 'SSLC'
            ],
            [
                'value' => 'Plus one Plus Two',
                'label' => 'Plus one Plus Two'
            ],
            [
                'value' => 'Degree',
                'label' => 'Degree'
            ],
            [
                'value' => 'Ai Python',
                'label' => 'Ai Python'
            ],
            [
                'value' => 'AI Integrated Digital Marketing',
                'label' => 'AI Integrated Digital Marketing'
            ],
            [
                'value' => 'Diploma in Graphic Designing',
                'label' => 'Diploma in Graphic Designing'
            ],
            [
                'value' => 'Certificate Course in Medical Coding',
                'label' => 'Certificate Course in Medical Coding'
            ],
            [
                'value' => 'Diploma in Hospital Administration',
                'label' => 'Diploma in Hospital Administration'
            ],
            [
                'value' => 'Hotel Management',
                'label' => 'Hotel Management'
            ]
        ];

        return response()->json([
            'status' => true,
            'data' => [
                'bde_list' => $bdeList,
                'lead_types' => $leadTypes,
                'interested_courses' => $interestedCourses
            ]
        ], 200);
    }

    /**
     * Get marketing lead filters (BDE list, assignment status, date filter meta)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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

        $isMarketing = $user->role_id == 13;
        $isAdminOrManager = $user->role_id == 1 || $user->role_id == 2 || $user->is_senior_manager;

        if (!$isMarketing && !$isAdminOrManager) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied.'
            ], 403);
        }

        $bdeOptions = [
            [
                'value' => '',
                'label' => 'All BDEs'
            ]
        ];

        if ($isMarketing) {
            $bdeOptions[] = [
                'value' => $user->id,
                'label' => $user->name
            ];
        } else {
            $bdeOptions = array_merge(
                $bdeOptions,
                User::where('role_id', 13)
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get()
                    ->map(function ($bdeUser) {
                        return [
                            'value' => $bdeUser->id,
                            'label' => $bdeUser->name
                        ];
                    })
                    ->toArray()
            );
        }

        $assignmentStatuses = [
            [
                'value' => '',
                'label' => 'All'
            ],
            [
                'value' => '1',
                'label' => 'Assigned'
            ],
            [
                'value' => '0',
                'label' => 'Not Assigned'
            ],
        ];

        return response()->json([
            'status' => true,
            'data' => [
                'filters' => [
                    'can_filter_by_bde' => !$isMarketing,
                    'bde_options' => $bdeOptions,
                    'assignment_statuses' => $assignmentStatuses,
                    'date_filter' => [
                        'key_from' => 'date_from',
                        'key_to' => 'date_to',
                        'label_from' => 'Date From',
                        'label_to' => 'Date To',
                        'description' => 'Filters marketing leads by Date of Visit range'
                    ],
                ]
            ]
        ], 200);
    }

    /**
     * Add new marketing lead
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $isMarketing = $user->role_id == 13;
        $isAdminOrManager = $user->role_id == 1 || $user->role_id == 2 || $user->is_senior_manager;

        if (!$isMarketing && !$isAdminOrManager) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied.'
            ], 403);
        }

        // Determine marketing_bde_id
        $marketingBdeId = null;
        if ($isMarketing) {
            // If marketing user is logged in, use their ID
            $marketingBdeId = $user->id;
        } else {
            // If admin/manager, require BDE selection
            $validator = Validator::make($request->all(), [
                'bde_id' => 'required|exists:users,id',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }
            
            // Verify BDE is a marketing user
            $bde = User::findOrFail($request->bde_id);
            if ($bde->role_id != 13) {
                return response()->json([
                    'status' => false,
                    'message' => 'Selected BDE must be a marketing user.'
                ], 422);
            }
            
            $marketingBdeId = $request->bde_id;
        }

        $validator = Validator::make($request->all(), [
            'date_of_visit' => 'required|date',
            'location' => 'required|string|max:255',
            'house_number' => 'nullable|string|max:255',
            'lead_name' => 'required|string|max:255',
            'code' => 'required|string|max:10',
            'phone' => 'required|string|max:20',
            'whatsapp_code' => 'nullable|string|max:10',
            'whatsapp' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'lead_type' => 'required|string|in:Student,Parent,Working Professional,Institution Representative,Others',
            'interested_courses' => 'nullable|array',
            'interested_courses.*' => 'string|max:255',
            'remarks' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        // Check for duplicate phone number (code + phone combination)
        $duplicatePhone = MarketingLead::where('code', $request->code)
            ->where('phone', $request->phone)
            ->whereNull('deleted_at')
            ->first();

        if ($duplicatePhone) {
            return response()->json([
                'status' => false,
                'message' => 'A lead with this phone number ('.$request->code.' '.$request->phone.') already exists in the system. Please check the existing lead or use a different phone number.',
                'duplicate_lead' => [
                    'id' => $duplicatePhone->id,
                    'lead_name' => $duplicatePhone->lead_name,
                    'date_of_visit' => $duplicatePhone->date_of_visit ? $duplicatePhone->date_of_visit->format('Y-m-d') : null
                ]
            ], 422);
        }

        // Check for duplicate submission within last 10 seconds (same data)
        $recentDuplicate = MarketingLead::where('marketing_bde_id', $marketingBdeId)
            ->where('lead_name', $request->lead_name)
            ->where('phone', $request->phone)
            ->where('code', $request->code)
            ->where('date_of_visit', $request->date_of_visit)
            ->where('created_at', '>=', now()->subSeconds(10))
            ->first();

        if ($recentDuplicate) {
            return response()->json([
                'status' => false,
                'message' => 'This form was already submitted recently. Please wait a moment before submitting again.'
            ], 422);
        }

        // Prepare marketing lead data
        $marketingLeadData = [
            'marketing_bde_id' => $marketingBdeId,
            'date_of_visit' => $request->date_of_visit,
            'location' => $request->location,
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'house_number' => $request->house_number,
            'lead_name' => $request->lead_name,
            'code' => $request->code,
            'phone' => $request->phone,
            'whatsapp_code' => $request->whatsapp_code,
            'whatsapp' => $request->whatsapp,
            'address' => $request->address,
            'lead_type' => $request->lead_type,
            'interested_courses' => $request->interested_courses ?? [],
            'remarks' => $request->remarks,
            'is_telecaller_assigned' => false,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ];

        // Create the marketing lead
        $marketingLead = MarketingLead::create($marketingLeadData);

        if ($marketingLead) {
            return response()->json([
                'status' => true,
                'message' => 'Marketing lead created successfully!'
            ], 201);
        }

        return response()->json([
            'status' => false,
            'message' => 'Something went wrong! Please try again.'
        ], 500);
    }
}

