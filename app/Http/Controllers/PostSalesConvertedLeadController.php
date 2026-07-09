<?php

namespace App\Http\Controllers;

use App\Helpers\RoleHelper;
use App\Helpers\AuthHelper;
use App\Models\Batch;
use App\Models\ConvertedLead;
use App\Models\ConvertedStudentActivity;
use App\Models\Course;
use App\Models\Invoice;
use App\Models\LeadActivity;
use App\Models\User;
use App\Services\LeadCallLogService;
use App\Support\ConvertedLeadShowFileHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Mpdf\Mpdf;

class PostSalesConvertedLeadController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureAccess();

        $courses = Course::where('is_active', 1)->orderBy('title')->get(['id', 'title']);
        $telecallers = User::select('id', 'name')->nonMarketingTelecallers()->where('is_active', true)->orderBy('name')->get();
        
        // Post-sales users (role_id 7, exclude head) for assign dropdown; only head or admin can assign
        $postSalesUsers = User::select('id', 'name')->where('role_id', 7)->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('is_head')->orWhere('is_head', 0);
            })->orderBy('name')->get();
        $canAssignPostSales = RoleHelper::is_post_sales_head() || RoleHelper::is_admin_or_super_admin();
        
        // Load batches only if a course is selected
        $batches = collect();
        if ($request->filled('course_id')) {
            $batches = Batch::where('course_id', $request->course_id)
                ->where('is_active', 1)
                ->orderBy('title')
                ->get(['id', 'title']);
        }

        return view('admin.post-sales.converted-leads.index', compact('courses', 'telecallers', 'batches', 'postSalesUsers', 'canAssignPostSales'));
    }

    /**
     * AJAX endpoint for DataTables to fetch converted students data
     */
    public function getPostSalesConvertedStudentsData(Request $request): JsonResponse
    {
        try {
            $this->ensureAccess();
            
            set_time_limit(config('timeout.max_execution_time', 300));

            // Build the query
            $query = ConvertedLead::with([
                'course',
                'batch.postponeBatch',
                'admissionBatch',
                'subject',
                'lead.telecaller:id,name',
                'cancelledBy:id,name',
                'postSalesUser:id,name',
                'invoices.payments' // For checking pending payments
            ]);

            // Post-sales members see only their assigned; head and admin see all
            if (RoleHelper::is_post_sales() && !RoleHelper::is_post_sales_head() && !RoleHelper::is_admin_or_super_admin()) {
                $query->where('post_sales_user_id', AuthHelper::getCurrentUserId());
            }

            // Apply filters
            // Handle DataTable's built-in search box (search.value) - priority for DataTable search box
            $searchValue = null;
            if ($request->filled('search') && is_array($request->search) && isset($request->search['value']) && !empty($request->search['value'])) {
                $searchValue = $request->search['value'];
            } elseif ($request->filled('filter_search')) {
                // Handle custom filter search input (filter_search) - for custom search input in filter form
                $searchValue = $request->filter_search;
            } elseif ($request->filled('search') && !is_array($request->search)) {
                // Handle search parameter from form submission (for URL compatibility)
                $searchValue = $request->search;
            }
            
            if ($searchValue) {
                $query->where(function($q) use ($searchValue) {
                    $q->where('name', 'LIKE', "%{$searchValue}%")
                      ->orWhere('email', 'LIKE', "%{$searchValue}%")
                      ->orWhere('phone', 'LIKE', "%{$searchValue}%")
                      ->orWhere('register_number', 'LIKE', "%{$searchValue}%");
                });
            }

            if ($request->filled('course_id')) {
                $query->where('course_id', $request->course_id);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            if ($request->filled('telecaller_id')) {
                $query->whereHas('lead', function($q) use ($request) {
                    $q->where('telecaller_id', $request->telecaller_id);
                });
            }

            if ($request->filled('batch_id')) {
                $query->where('batch_id', $request->batch_id);
            }

            // recordsTotal: count user can see (role filter only)
            $baseQuery = ConvertedLead::query();
            if (RoleHelper::is_post_sales() && !RoleHelper::is_post_sales_head() && !RoleHelper::is_admin_or_super_admin()) {
                $baseQuery->where('post_sales_user_id', AuthHelper::getCurrentUserId());
            }
            $totalRecords = $baseQuery->count();

            // Get filtered count (after all filters)
            $filteredCount = $query->count();

            // Always order by ID (latest first) to keep
            // stable ordering regardless of search or UI sort state.
            $query->orderBy('id', 'desc');

            // Apply pagination
            $start = $request->get('start', 0);
            $length = $request->get('length', 25);
            $convertedLeads = $query->skip($start)->take($length)->get();

            // Format data for DataTables
            $data = [];
            foreach ($convertedLeads as $index => $convertedLead) {
                $row = [
                    'DT_RowId' => 'converted_lead_' . $convertedLead->id,
                    'DT_RowData' => ['id' => $convertedLead->id],
                    'index' => $start + $index + 1,
                    'name' => $this->renderName($convertedLead),
            'phone' => \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone),
            'whatsapp' => ($convertedLead->leadDetail && $convertedLead->leadDetail->whatsapp_number) 
                ? \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->whatsapp_code, $convertedLead->leadDetail->whatsapp_number) 
                : 'N/A',
            'parent_phone' => (\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor()) 
                ? (($convertedLead->leadDetail && $convertedLead->leadDetail->parents_number) 
                    ? \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->parents_code, $convertedLead->leadDetail->parents_number) 
                    : 'N/A')
                : null,
            'show_parent_phone' => \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor(),
            'email' => $convertedLead->email ?? 'N/A',
                    'bde_name' => $convertedLead->lead?->telecaller?->name ?? 'Unassigned',
                    'post_sales_user' => $this->renderPostSalesUser($convertedLead),
                    'created_at' => $convertedLead->created_at ? $convertedLead->created_at->format('d M Y h:i A') : 'N/A',
                    'course' => $convertedLead->course?->title ?? 'N/A',
                    'batch' => $convertedLead->batch?->title ?? 'N/A',
                    'admission_batch' => $convertedLead->admissionBatch?->title ?? 'N/A',
                    'subject' => $convertedLead->subject?->title ?? 'N/A',
            'status' => $this->renderStatus($convertedLead),
                    'cancelled_by' => $this->renderCancelledBy($convertedLead),
                    'paid_status' => $this->renderPaidStatus($convertedLead),
                    'call_status' => $this->renderCallStatus($convertedLead),
                    'called_date' => $this->renderCalledDate($convertedLead),
                    'called_time' => $this->renderCalledTime($convertedLead),
                    'postsale_followup' => $this->renderPostsaleFollowup($convertedLead),
                    'post_sales_remarks' => $this->renderPostSalesRemarks($convertedLead),
                    'pending_payment' => $this->renderPendingPayment($convertedLead),
                    'paid_amount' => $this->renderPaidAmount($convertedLead),
                    'pending_amount' => $this->renderPendingAmount($convertedLead),
            'actions' => $this->renderActions($convertedLead),
            'DT_RowClass' => $this->getRowClass($convertedLead),
                    // Mobile view data
                    'mobile_view' => $this->renderMobileView($convertedLead)
                ];

                $data[] = $row;
            }

            // Build response array
            $responseData = [
                'draw' => intval($request->get('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredCount,
                'data' => $data
            ];

            return response()->json($responseData);

        } catch (\Exception $e) {
            Log::error('Error fetching post-sales converted students data: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'draw' => intval($request->get('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'An error occurred while fetching data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Render name column HTML
     */
    private function renderName($convertedLead)
    {
        $name = $convertedLead->name ?? '';
        $registerNumber = $convertedLead->register_number ?? 'No register #';
        $firstChar = mb_substr($name, 0, 1, 'UTF-8');
        
        $html = '<div class="d-flex align-items-center">';
        $html .= '<div class="avtar avtar-s rounded-circle bg-light-primary me-2 d-flex align-items-center justify-content-center">';
        $html .= '<span class="f-16 fw-bold text-primary">' . htmlspecialchars(strtoupper($firstChar), ENT_QUOTES, 'UTF-8') . '</span>';
        $html .= '</div>';
        $html .= '<div>';
        $html .= '<div class="fw-semibold">' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '</div>';
        $html .= '<small class="text-muted">' . htmlspecialchars($registerNumber, ENT_QUOTES, 'UTF-8') . '</small>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

    /**
     * Render status column HTML
     */
    private function renderStatus($convertedLead)
    {
        $status = $convertedLead->postsale_status ?? 'N/A';
        $badgeClass = match($status) {
            'paid' => 'bg-success',
            'unpaid' => 'bg-warning',
            'cancel' => 'bg-danger',
            'pending' => 'bg-info',
            'postpond' => 'bg-dark',
            'followup' => 'bg-primary',
            default => 'bg-secondary'
        };
        
        $html = '<span class="badge ' . $badgeClass . '">' . htmlspecialchars(ucfirst($status), ENT_QUOTES, 'UTF-8') . '</span>';
        
        // Add cancelled_by information if status is cancel
        if (strcasecmp($status, 'cancel') === 0 && $convertedLead->is_cancelled && $convertedLead->cancelledBy) {
            $html .= '<br><small class="text-muted d-block mt-1">By: ' . htmlspecialchars($convertedLead->cancelledBy->name, ENT_QUOTES, 'UTF-8');
            if ($convertedLead->cancelled_at) {
                $html .= '<br>' . htmlspecialchars($convertedLead->cancelled_at->format('d-m-Y h:i A'), ENT_QUOTES, 'UTF-8');
            }
            if ($convertedLead->cancel_remark) {
                $html .= '<br><strong>Remark:</strong> ' . htmlspecialchars($convertedLead->cancel_remark, ENT_QUOTES, 'UTF-8');
            }
            $html .= '</small>';
        }
        
        return $html;
    }

    /**
     * Render cancelled by column HTML
     */
    private function renderCancelledBy($convertedLead)
    {
        if (!$convertedLead->is_cancelled || !$convertedLead->cancelledBy) {
            return '<span class="text-muted">N/A</span>';
        }

        $name = htmlspecialchars($convertedLead->cancelledBy->name, ENT_QUOTES, 'UTF-8');
        $cancelledAt = $convertedLead->cancelled_at ? $convertedLead->cancelled_at->format('d-m-Y h:i A') : null;

        $html = '<span class="badge bg-danger">Cancelled</span>';
        $html .= '<div class="small text-muted mt-1">By: ' . $name;
        if ($cancelledAt) {
            $html .= '<br>' . htmlspecialchars($cancelledAt, ENT_QUOTES, 'UTF-8');
        }
        if ($convertedLead->cancel_remark) {
            $html .= '<br><strong>Remark:</strong> ' . htmlspecialchars($convertedLead->cancel_remark, ENT_QUOTES, 'UTF-8');
        }
        $html .= '</div>';

        return $html;
    }

    /**
     * Render paid status column HTML
     */
    private function renderPaidStatus($convertedLead)
    {
        $paidStatus = $convertedLead->paid_status ?? 'N/A';
        if ($paidStatus === 'N/A') {
            return '<span class="text-muted">N/A</span>';
        }
        return '<span class="badge bg-info">' . htmlspecialchars($paidStatus, ENT_QUOTES, 'UTF-8') . '</span>';
    }

    /**
     * Render call status column HTML
     */
    private function renderCallStatus($convertedLead)
    {
        $callStatus = $convertedLead->call_status ?? 'N/A';
        if ($callStatus === 'N/A') {
            return '<span class="text-muted">N/A</span>';
        }
        $badgeClass = match($callStatus) {
            'Attended' => 'bg-success',
            'Whatsapp connected' => 'bg-success',
            'RNR' => 'bg-warning',
            'Switch off' => 'bg-danger',
            default => 'bg-secondary'
        };
        return '<span class="badge ' . $badgeClass . '">' . htmlspecialchars($callStatus, ENT_QUOTES, 'UTF-8') . '</span>';
    }

    /**
     * Render called date column HTML
     */
    private function renderCalledDate($convertedLead)
    {
        if (!$convertedLead->called_date) {
            return '<span class="text-muted">N/A</span>';
        }
        
        return '<span class="fw-semibold">' . htmlspecialchars($convertedLead->called_date->format('d M Y'), ENT_QUOTES, 'UTF-8') . '</span>';
    }

    /**
     * Render called time column HTML
     */
    private function renderCalledTime($convertedLead)
    {
        if (!$convertedLead->called_time) {
            return '<span class="text-muted">N/A</span>';
        }

        return '<span class="fw-semibold">' . htmlspecialchars($convertedLead->called_time->format('h:i A'), ENT_QUOTES, 'UTF-8') . '</span>';
    }

    /**
     * Render postsale followup column HTML
     */
    private function renderPostsaleFollowup($convertedLead)
    {
        if (!$convertedLead->postsale_followupdate) {
            return '<span class="text-muted">N/A</span>';
        }
        
        $date = $convertedLead->postsale_followupdate->format('d M Y');
        $time = $convertedLead->postsale_followuptime ? date('h:i A', strtotime($convertedLead->postsale_followuptime)) : '';
        
        $html = '<div>';
        $html .= '<div class="fw-semibold">' . htmlspecialchars($date, ENT_QUOTES, 'UTF-8') . '</div>';
        if ($time) {
            $html .= '<small class="text-muted">' . htmlspecialchars($time, ENT_QUOTES, 'UTF-8') . '</small>';
        }
        $html .= '</div>';
        return $html;
    }

    /**
     * Render post-sales assigned user column HTML
     */
    private function renderPostSalesUser($convertedLead)
    {
        $name = $convertedLead->postSalesUser?->name ?? null;
        if (empty($name)) {
            return '<span class="text-muted">Unassigned</span>';
        }
        return '<span>' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '</span>';
    }

    /**
     * Render post sales remarks column HTML
     */
    private function renderPostSalesRemarks($convertedLead)
    {
        $remarks = $convertedLead->post_sales_remarks ?? '';
        if (empty($remarks)) {
            return '<span class="text-muted">N/A</span>';
        }
        // Truncate long remarks for table display
        $truncated = mb_strlen($remarks) > 50 ? mb_substr($remarks, 0, 50) . '...' : $remarks;
        return '<span title="' . htmlspecialchars($remarks, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($truncated, ENT_QUOTES, 'UTF-8') . '</span>';
    }

    /**
     * Calculate total paid amount for a converted lead
     */
    private function calculateTotalPaidAmount($convertedLead)
    {
        $totalPaid = 0;
        foreach ($convertedLead->invoices as $invoice) {
            // Sum all approved payments
            $approvedPayments = $invoice->payments->where('status', 'Approved');
            foreach ($approvedPayments as $payment) {
                $totalPaid += (float) $payment->amount_paid;
            }
        }
        return $totalPaid;
    }

    /**
     * Calculate total pending amount for a converted lead
     */
    private function calculateTotalPendingAmount($convertedLead)
    {
        $total = 0;
        foreach ($convertedLead->invoices as $invoice) {
            $total += (float) $invoice->pending_amount;
        }

        return $total;
    }

    /**
     * Check if converted lead has pending payment
     */
    private function hasPendingPayment($convertedLead)
    {
        foreach ($convertedLead->invoices as $invoice) {
            if ($invoice->payments->where('status', 'Pending Approval')->count() > 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Render pending payment column HTML
     */
    private function renderPendingPayment($convertedLead)
    {
        if ($this->hasPendingPayment($convertedLead)) {
            return '<span class="badge bg-warning">Pending</span>';
        }
        return '<span class="text-muted">No</span>';
    }

    /**
     * Render paid amount column HTML
     */
    private function renderPaidAmount($convertedLead)
    {
        $paidAmount = $this->calculateTotalPaidAmount($convertedLead);
        if ($paidAmount > 0) {
            return '<span class="fw-bold text-success">₹' . number_format($paidAmount, 2) . '</span>';
        }
        return '<span class="text-muted">₹0.00</span>';
    }

    /**
     * Render pending amount column HTML
     */
    private function renderPendingAmount($convertedLead)
    {
        $pendingAmount = $this->calculateTotalPendingAmount($convertedLead);
        if ($pendingAmount > 0) {
            return '<span class="fw-semibold text-dark">₹' . number_format($pendingAmount, 2) . '</span>';
        } elseif ($pendingAmount < 0) {
            // Overpaid case
            return '<span class="fw-semibold text-info">₹' . number_format(abs($pendingAmount), 2) . ' (Overpaid)</span>';
        }
        return '<span class="text-muted">₹0.00</span>';
    }

    /**
     * Render actions column HTML
     */
    private function renderActions($convertedLead)
    {
        $html = '<div class="text-center d-flex gap-1 justify-content-center">';
        if (RoleHelper::is_post_sales_head() || RoleHelper::is_admin_or_super_admin()) {
            $html .= '<button type="button" class="btn btn-sm btn-outline-secondary" title="Assign to Post-Sales" onclick="show_ajax_modal(\'' . route('admin.post-sales.converted-leads.assign', $convertedLead->id) . '\', \'Assign to Post-Sales\')">';
            $html .= '<i class="ti ti-user-plus"></i>';
            $html .= '</button>';
        }
        $html .= '<a href="' . route('admin.post-sales.converted-leads.show', $convertedLead->id) . '" class="btn btn-sm btn-outline-primary" title="View Details">';
        $html .= '<i class="ti ti-eye"></i>';
        $html .= '</a>';
        $html .= '<a href="' . route('admin.invoices.index', $convertedLead->id) . '" class="btn btn-sm btn-success" title="View Invoice">';
        $html .= '<i class="ti ti-receipt"></i>';
        $html .= '</a>';
        $html .= '<button type="button" class="btn btn-sm btn-outline-success" title="Status Update" onclick="show_ajax_modal(\'' . route('admin.post-sales.converted-leads.status-update', $convertedLead->id) . '\', \'Status Update\')">';
        $html .= '<i class="ti ti-edit"></i>';
        $html .= '</button>';
        
        // Check if postponed batch button should be shown
        $batch = $convertedLead->batch;
        $shouldShowPostponedButton = false;
        if ($batch && $batch->is_postpone_active == 1 && strcasecmp($convertedLead->postsale_status ?? '', 'postpond') === 0) {
            $today = now()->toDateString();
            if ($batch->postpone_start_date && $batch->postpone_end_date) {
                $startDate = $batch->postpone_start_date->toDateString();
                $endDate = $batch->postpone_end_date->toDateString();
                if ($today >= $startDate && $today <= $endDate) {
                    $shouldShowPostponedButton = true;
                }
            }
        }
        
        if ($shouldShowPostponedButton) {
            $html .= '<button type="button" class="btn btn-sm btn-warning" title="Postponed Batch" onclick="show_ajax_modal(\'' . route('admin.post-sales.converted-leads.postponed-batch', $convertedLead->id) . '\', \'Postponed Batch\')">';
            $html .= '<i class="ti ti-calendar-time"></i>';
            $html .= '</button>';
        }
        
        if (strcasecmp($convertedLead->postsale_status ?? '', 'cancel') === 0) {
            $cancelBtnClass = $convertedLead->is_cancelled ? 'btn-danger' : 'btn-outline-danger';
            $cancelBtnTitle = $convertedLead->is_cancelled ? 'Update cancellation confirmation' : 'Confirm cancellation';
            $html .= '<button type="button" class="btn btn-sm ' . $cancelBtnClass . '" title="' . $cancelBtnTitle . '" onclick="show_ajax_modal(\'' . route('admin.post-sales.converted-leads.cancel-flag', $convertedLead->id) . '\', \'Cancellation Confirmation\')">';
            $html .= '<i class="ti ti-ban"></i>';
            $html .= '</button>';
        }
        $html .= '</div>';
        return $html;
    }

    private function getRowClass($convertedLead): string
    {
        return $convertedLead->is_cancelled == 1 ? 'table-danger cancelled-row' : '';
    }

    /**
     * Render mobile view data
     */
    private function renderMobileView($convertedLead)
    {
        $data = [
            'id' => $convertedLead->id,
            'name' => $convertedLead->name ?? '',
            'register_number' => $convertedLead->register_number ?? 'No register #',
            'status' => $convertedLead->postsale_status ?? null,
            'is_cancelled' => (bool) $convertedLead->is_cancelled,
            'cancelled_by' => $convertedLead->cancelledBy ? $convertedLead->cancelledBy->name : null,
            'cancelled_at' => $convertedLead->cancelled_at ? $convertedLead->cancelled_at->format('d M Y h:i A') : null,
            'cancel_remark' => $convertedLead->cancel_remark ?? null,
            'phone' => \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone),
            'whatsapp' => ($convertedLead->leadDetail && $convertedLead->leadDetail->whatsapp_number) 
                ? \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->whatsapp_code, $convertedLead->leadDetail->whatsapp_number) 
                : 'N/A',
            'parent_phone' => (\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor()) 
                ? (($convertedLead->leadDetail && $convertedLead->leadDetail->parents_number) 
                    ? \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->parents_code, $convertedLead->leadDetail->parents_number) 
                    : 'N/A')
                : null,
            'show_parent_phone' => \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor(),
            'email' => $convertedLead->email ?? 'N/A',
            'bde_name' => $convertedLead->lead?->telecaller?->name ?? 'Unassigned',
            'post_sales_user' => $convertedLead->postSalesUser?->name ?? 'Unassigned',
            'created_at' => $convertedLead->created_at ? $convertedLead->created_at->format('d M Y h:i A') : 'N/A',
            'course' => $convertedLead->course?->title ?? 'N/A',
            'batch' => $convertedLead->batch?->title ?? 'N/A',
            'admission_batch' => $convertedLead->admissionBatch?->title ?? 'N/A',
            'subject' => $convertedLead->subject?->title ?? 'N/A',
            'called_date' => $convertedLead->called_date ? $convertedLead->called_date->format('d M Y') : null,
            'called_time' => $convertedLead->called_time ? $convertedLead->called_time->format('h:i A') : null,
            'pending_payment' => $this->hasPendingPayment($convertedLead),
            'paid_amount' => $this->calculateTotalPaidAmount($convertedLead),
            'pending_amount' => $this->calculateTotalPendingAmount($convertedLead),
            'routes' => [
                'view' => route('admin.post-sales.converted-leads.show', $convertedLead->id),
                'status_update' => route('admin.post-sales.converted-leads.status-update', $convertedLead->id),
                'invoice' => route('admin.invoices.index', $convertedLead->id),
                'cancel_flag' => route('admin.post-sales.converted-leads.cancel-flag', $convertedLead->id),
            ]
        ];
        
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function show($id)
    {
        $this->ensureAccess();

        $leadActivitiesLimit = 200;
        $convertedStudentActivitiesLimit = 150;

        $convertedLead = ConvertedLead::with([
            'lead' => function ($query) {
                $query->with([
                    'team',
                    'leadSource:id,title',
                    'leadStatus:id,title',
                    'telecaller:id,name,code,phone',
                ]);
            },
            'leadDetail' => function ($query) {
                $query->with([
                    'batch:id,title',
                    'sslcCertificates' => function ($q) {
                        $q->orderByDesc('id')->limit(30)->with(['verifiedBy:id,name']);
                    },
                    'sslcVerifiedBy:id,name',
                    'plustwoVerifiedBy:id,name',
                    'ugVerifiedBy:id,name',
                    'passportPhotoVerifiedBy:id,name',
                    'adharFrontVerifiedBy:id,name',
                    'adharBackVerifiedBy:id,name',
                    'signatureVerifiedBy:id,name',
                    'birthCertificateVerifiedBy:id,name',
                    'otherDocumentVerifiedBy:id,name',
                ]);
            },
            'course:id,title',
            'batch:id,title',
            'admissionBatch:id,title',
            'subject:id,title',
            'academicAssistant:id,name',
            'createdBy:id,name',
            'cancelledBy:id,name',
            'studentDetails.registrationLink',
            'mentorDetails.placementPassedBy:id,name',
            'mentorDetails.resumeVerifiedBy:id,name',
        ])->findOrFail($id);

        $leadActivityBase = LeadActivity::where('lead_id', $convertedLead->lead_id)
            ->where(function ($query) {
                $query->whereNull('is_pullbacked')
                    ->orWhere('is_pullbacked', 0);
            });

        $leadActivityTotal = (clone $leadActivityBase)->count();

        $leadActivities = (clone $leadActivityBase)
            ->select('id', 'lead_id', 'reason', 'created_at', 'activity_type', 'description', 'remarks', 'rating', 'followup_date', 'created_by', 'lead_status_id')
            ->with(['leadStatus:id,title', 'createdBy:id,name'])
            ->orderBy('created_at', 'desc')
            ->limit($leadActivitiesLimit)
            ->get();

        $convertedActivityBase = ConvertedStudentActivity::where('converted_lead_id', $convertedLead->id);
        $convertedStudentActivityTotal = (clone $convertedActivityBase)->count();

        $convertedStudentActivities = (clone $convertedActivityBase)
            ->with(['createdBy:id,name'])
            ->orderBy('activity_date', 'desc')
            ->orderBy('activity_time', 'desc')
            ->limit($convertedStudentActivitiesLimit)
            ->get();

        $callLogs = LeadCallLogService::forConvertedLead($convertedLead);
        $listRoute = route('admin.post-sales.converted-leads.index');
        $pdfRoute = route('admin.post-sales.converted-leads.details-pdf', $convertedLead->id);

        $fileExistenceMeta = ConvertedLeadShowFileHelper::publicExistenceMap($convertedLead);
        $leadActivitiesTruncated = $leadActivityTotal > $leadActivitiesLimit;
        $convertedStudentActivitiesTruncated = $convertedStudentActivityTotal > $convertedStudentActivitiesLimit;

        return view('admin.converted-leads.show', compact(
            'convertedLead',
            'leadActivities',
            'convertedStudentActivities',
            'callLogs',
            'listRoute',
            'pdfRoute',
            'fileExistenceMeta',
            'leadActivitiesTruncated',
            'convertedStudentActivitiesTruncated',
            'leadActivitiesLimit',
            'convertedStudentActivitiesLimit'
        ));
    }

    /**
     * Show all postponed batches
     */
    public function postponedBatches()
    {
        $this->ensureAccess();

        $postponedBatches = Batch::with(['course', 'postponeBatch'])
            ->where('is_postpone_active', 1)
            ->whereNotNull('postpone_start_date')
            ->whereNotNull('postpone_end_date')
            ->orderBy('postpone_start_date', 'asc')
            ->get();

        return view('admin.post-sales.converted-leads.postponed-batches-list', compact('postponedBatches'));
    }

    /**
     * Show postponed batch modal
     */
    public function postponedBatch($id)
    {
        $this->ensureAccess();

        $convertedLead = ConvertedLead::with([
            'batch.postponeBatch',
            'course',
            'lead.telecaller:id,name'
        ])->findOrFail($id);

        return view('admin.post-sales.converted-leads.postponed-batch-modal', compact('convertedLead'));
    }

    /**
     * Handle postponed batch form submission
     */
    public function postponedBatchSubmit(Request $request, $id)
    {
        $this->ensureAccess();

        try {
            DB::beginTransaction();

            $convertedLead = ConvertedLead::with('batch.postponeBatch')->findOrFail($id);
            
            // Validate that batch has postpone information
            if (!$convertedLead->batch || !$convertedLead->batch->is_postpone_active || !$convertedLead->batch->postponeBatch) {
                return response()->json([
                    'success' => false,
                    'message' => 'Batch does not have valid postpone information.'
                ], 422);
            }

            $oldBatch = $convertedLead->batch;
            $newBatch = $oldBatch->postponeBatch;
            $postponeAmount = $oldBatch->batch_postpone_amount ?? 0;

            // Update converted lead
            $convertedLead->batch_id = $newBatch->id;
            $convertedLead->admission_batch_id = null;
            $convertedLead->admission_batch_assigned_at = null;
            $convertedLead->is_postpond_batch = 1;
            $convertedLead->updated_by = AuthHelper::getCurrentUserId();
            $convertedLead->save();

            // Create invoice if postpone amount exists
            if ($postponeAmount > 0) {
                Invoice::create([
                    'invoice_number' => Invoice::generateNextInvoiceNumber(),
                    'invoice_type' => 'batch_postpond',
                    'batch_id' => $newBatch->id,
                    'student_id' => $convertedLead->id,
                    'total_amount' => $postponeAmount,
                    'invoice_date' => now()->toDateString(),
                    'created_by' => AuthHelper::getCurrentUserId(),
                    'updated_by' => AuthHelper::getCurrentUserId(),
                ]);
            }

            // Create activity record
            $activity = new ConvertedStudentActivity();
            $activity->converted_lead_id = $convertedLead->id;
            $activity->activity_type = 'batch_postponed';
            $activity->description = "Batch postponed from '{$oldBatch->title}' to '{$newBatch->title}'";
            $activity->remark = "Postponed batch transfer. Postponed amount: ₹" . number_format($postponeAmount, 2);
            $activity->activity_date = now()->toDateString();
            $activity->activity_time = now()->toTimeString();
            $activity->created_by = AuthHelper::getCurrentUserId();
            $activity->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Student moved to postponed batch successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[PostSalesConvertedLeadController@postponedBatchSubmit] Error: ' . $e->getMessage(), [
                'converted_lead_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the postponed batch. Please try again.'
            ], 500);
        }
    }

    /**
     * Show status update modal
     */
    public function statusUpdate($id)
    {
        $this->ensureAccess();

        $convertedLead = ConvertedLead::findOrFail($id);

        return view('admin.post-sales.converted-leads.status-update-modal', compact('convertedLead'));
    }

    /**
     * Handle status update form submission
     */
    public function statusUpdateSubmit(Request $request, $id)
    {
        $this->ensureAccess();

        try {
            $convertedLead = ConvertedLead::findOrFail($id);

            // Validate request
            $validated = $request->validate([
                'status' => 'required|in:paid,unpaid,cancel,postpond,followup',
                // Keep this list in sync with the options shown in
                // resources/views/admin/post-sales/converted-leads/status-update-modal.blade.php
                'paid_status' => 'nullable|in:Fully paid,Registration Paid,Registration Partially paid,Certificate Paid,Certificate Partially paid,Exam Fees Paid,Exam Fees Partially paid,Halticket Paid,Halticket Partially paid',
                'call_status' => ['required', Rule::in(['RNR', 'Switch off', 'Attended', 'Whatsapp connected'])],
                'called_date' => 'required|date',
                'called_time' => 'required|date_format:H:i',
                'followup_date' => 'nullable|date',
                'post_sales_remarks' => 'nullable|string|max:2000',
            ]);

            // Additional validation: paid_status required when status is 'paid'
            if ($request->status === 'paid' && !$request->paid_status) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paid status is required when status is paid.'
                ], 422);
            }

            // Additional validation: followup_date not required when paid_status is 'Fully paid'
            $isFullyPaid = $request->paid_status === 'Fully paid';
            if (
                !$isFullyPaid &&
                !in_array($request->status, ['postpond', 'cancel'], true) &&
                !$request->followup_date
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Followup date is required.'
                ], 422);
            }

            DB::beginTransaction();

            // Update converted lead - only update postsale_status, not status
            $convertedLead->postsale_status = $request->status;
            $convertedLead->paid_status = $request->paid_status;
            $convertedLead->call_status = $request->call_status;
            $convertedLead->called_date = $request->called_date;
            $convertedLead->called_time = $request->called_time;
            $convertedLead->post_sales_remarks = $request->post_sales_remarks;
            
            // Set cancelled_by and cancelled_at when status is set to 'cancel'
            if (strcasecmp($request->status, 'cancel') === 0) {
                $convertedLead->cancelled_by = AuthHelper::getCurrentUserId();
                $convertedLead->cancelled_at = now();
            }
            
            // Only set followup date/time if not fully paid
            if (!$isFullyPaid && !in_array($request->status, ['postpond', 'cancel'], true)) {
                $convertedLead->postsale_followupdate = $request->followup_date;
            } else {
                $convertedLead->postsale_followupdate = null;
            }
            $convertedLead->postsale_followuptime = null;
            
            $convertedLead->updated_by = AuthHelper::getCurrentUserId();
            $convertedLead->save();

            // Create activity record
            $activity = new ConvertedStudentActivity();
            $activity->converted_lead_id = $convertedLead->id;
            $activity->status = $request->status;
            $activity->paid_status = $request->paid_status;
            $activity->call_status = $request->call_status;
            $activity->called_date = $request->called_date;
            $activity->called_time = $request->called_time;
            $activity->activity_type = 'status_update';
            $activity->description = 'Post Sales Status updated to: ' . $request->status;
            $activity->remark = $request->post_sales_remarks;
            $activity->activity_date = now()->toDateString();
            $activity->activity_time = now()->toTimeString();
            
            // Only set followup date/time if not fully paid
            if (!$isFullyPaid && !in_array($request->status, ['postpond', 'cancel'], true)) {
                $activity->followup_date = $request->followup_date;
                $activity->followup_time = null;
            } else {
                $activity->followup_date = null;
                $activity->followup_time = null;
            }
            
            $activity->created_by = AuthHelper::getCurrentUserId();
            $activity->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating post-sales converted lead status: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show cancellation confirmation modal.
     */
    public function cancelFlag($id)
    {
        $this->ensureAccess();

        $convertedLead = ConvertedLead::findOrFail($id);

        if (strcasecmp($convertedLead->postsale_status ?? '', 'cancel') !== 0) {
            abort(404, 'Cancellation confirmation is only available for cancelled leads.');
        }

        return view('admin.post-sales.converted-leads.cancel-flag-modal', compact('convertedLead'));
    }

    /**
     * Update is_cancelled flag for a converted lead.
     */
    public function cancelFlagSubmit(Request $request, $id)
    {
        $this->ensureAccess();

        $convertedLead = ConvertedLead::findOrFail($id);

        if (strcasecmp($convertedLead->postsale_status ?? '', 'cancel') !== 0) {
            return response()->json([
                'success' => false,
                'message' => 'Only leads with status cancel can update this flag.'
            ], 422);
        }

        $validated = $request->validate([
            'is_cancelled' => 'required|boolean',
            'cancel_remark' => 'nullable|string|max:1000',
        ]);

        $convertedLead->is_cancelled = (bool) $validated['is_cancelled'];
        $convertedLead->updated_by = AuthHelper::getCurrentUserId();
        
        // Set cancelled_by and cancelled_at when cancelling
        if ($convertedLead->is_cancelled) {
            $convertedLead->cancelled_by = AuthHelper::getCurrentUserId();
            $convertedLead->cancelled_at = now();
            $convertedLead->cancel_remark = $validated['cancel_remark'] ?? null;
        } else {
            // Clear cancelled_by and cancelled_at when uncancelling
            $convertedLead->cancelled_by = null;
            $convertedLead->cancelled_at = null;
            $convertedLead->cancel_remark = null;
        }
        
        $convertedLead->save();

        $message = $convertedLead->is_cancelled
            ? 'Cancellation flagged successfully.'
            : 'Cancellation flag removed.';

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Show assign to post-sales modal (only post-sales head or admin).
     */
    public function assign($id)
    {
        $this->ensureAccess();
        if (!RoleHelper::is_post_sales_head() && !RoleHelper::is_admin_or_super_admin()) {
            abort(403, 'Only Post-Sales Head or Admin can assign.');
        }

        $convertedLead = ConvertedLead::findOrFail($id);
        // Exclude post-sales head from assignable list (only members)
        $postSalesUsers = User::select('id', 'name')->where('role_id', 7)->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('is_head')->orWhere('is_head', 0);
            })->orderBy('name')->get();

        return view('admin.post-sales.converted-leads.assign-modal', compact('convertedLead', 'postSalesUsers'));
    }

    /**
     * Submit assign to post-sales (only post-sales head or admin).
     */
    public function assignSubmit(Request $request, $id)
    {
        $this->ensureAccess();
        if (!RoleHelper::is_post_sales_head() && !RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['success' => false, 'message' => 'Only Post-Sales Head or Admin can assign.'], 403);
        }

        $convertedLead = ConvertedLead::findOrFail($id);
        $request->validate([
            'post_sales_user_id' => 'required|exists:users,id',
        ]);

        $convertedLead->post_sales_user_id = $request->post_sales_user_id;
        $convertedLead->updated_by = AuthHelper::getCurrentUserId();
        $convertedLead->save();

        return response()->json([
            'success' => true,
            'message' => 'Assigned to post-sales successfully.',
        ]);
    }

    /**
     * Show bulk assign modal (only post-sales head or admin).
     */
    public function bulkAssign()
    {
        $this->ensureAccess();
        if (!RoleHelper::is_post_sales_head() && !RoleHelper::is_admin_or_super_admin()) {
            abort(403, 'Only Post-Sales Head or Admin can bulk assign.');
        }

        $courses = Course::where('is_active', 1)->orderBy('title')->get(['id', 'title']);
        $postSalesUsers = User::select('id', 'name')->where('role_id', 7)->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('is_head')->orWhere('is_head', 0);
            })->orderBy('name')->get();

        return view('admin.post-sales.converted-leads.bulk-assign-modal', compact('courses', 'postSalesUsers'));
    }

    /**
     * AJAX: Get converted students for bulk assign list (filtered by date_from, date_to, course_id; optional batch_id, post_sales_user_id).
     */
    public function getBulkAssignData(Request $request): JsonResponse
    {
        $this->ensureAccess();
        if (!RoleHelper::is_post_sales_head() && !RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }

        $request->validate([
            'date_from' => 'required|date',
            'date_to'   => 'required|date|after_or_equal:date_from',
            'course_id' => 'required|exists:courses,id',
        ]);

        $query = ConvertedLead::with([
            'course',
            'batch',
            'postSalesUser:id,name',
        ])
            ->where('course_id', $request->course_id)
            ->whereDate('created_at', '>=', $request->date_from)
            ->whereDate('created_at', '<=', $request->date_to);

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }
        if ($request->has('post_sales_user_id') && $request->post_sales_user_id !== '' && $request->post_sales_user_id !== null) {
            if ((string) $request->post_sales_user_id === '0') {
                $query->whereNull('post_sales_user_id');
            } else {
                $query->where('post_sales_user_id', $request->post_sales_user_id);
            }
        }

        $query->orderBy('id', 'desc');
        $leads = $query->get();

        $rows = [];
        foreach ($leads as $index => $lead) {
            $rows[] = [
                'id'                => $lead->id,
                'index'             => $index + 1,
                'name'              => $lead->name ?? '',
                'register_number'   => $lead->register_number ?? 'N/A',
                'phone'             => \App\Helpers\PhoneNumberHelper::display($lead->code, $lead->phone),
                'course'            => $lead->course?->title ?? 'N/A',
                'batch'             => $lead->batch?->title ?? 'N/A',
                'post_sales_user'   => $lead->postSalesUser?->name ?? 'Unassigned',
                'created_at'        => $lead->created_at ? $lead->created_at->format('d M Y') : '',
            ];
        }

        return response()->json(['success' => true, 'data' => $rows]);
    }

    /**
     * Submit bulk assign (only post-sales head or admin).
     */
    public function bulkAssignSubmit(Request $request): JsonResponse
    {
        $this->ensureAccess();
        if (!RoleHelper::is_post_sales_head() && !RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }

        $request->validate([
            'post_sales_user_id' => 'required|exists:users,id',
            'ids'                => 'required|array|min:1',
            'ids.*'               => 'exists:converted_leads,id',
        ]);

        $count = ConvertedLead::whereIn('id', $request->ids)
            ->update([
                'post_sales_user_id' => $request->post_sales_user_id,
                'updated_by'         => AuthHelper::getCurrentUserId(),
            ]);

        return response()->json([
            'success' => true,
            'message' => $count . ' student(s) assigned to post-sales successfully.',
        ]);
    }

    /**
     * Generate PDF of converted lead details
     */
    public function generateDetailsPdf($id)
    {
        $this->ensureAccess();

        $convertedLead = ConvertedLead::with([
            'lead',
            'leadDetail',
            'course',
            'batch',
            'admissionBatch',
            'subject',
            'academicAssistant',
            'createdBy',
            'studentDetails'
        ])->findOrFail($id);

        // Lead activities
        $leadActivities = LeadActivity::where('lead_id', $convertedLead->lead_id)
            ->select('id', 'lead_id', 'reason', 'created_at', 'activity_type', 'description', 'remarks', 'rating', 'followup_date', 'created_by', 'lead_status_id')
            ->with(['leadStatus:id,title', 'createdBy:id,name'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Converted student activities
        $convertedStudentActivities = ConvertedStudentActivity::where('converted_lead_id', $convertedLead->id)
            ->with(['createdBy:id,name'])
            ->orderBy('activity_date', 'desc')
            ->orderBy('activity_time', 'desc')
            ->get();

        $html = view('admin.converted-leads.pdf', compact('convertedLead', 'leadActivities', 'convertedStudentActivities'))->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 12,
            'margin_bottom' => 12,
            'margin_left' => 12,
            'margin_right' => 12,
        ]);

        $mpdf->SetTitle('Post Sales Converted Lead Details - #' . $convertedLead->id);
        $mpdf->WriteHTML($html);

        $filename = 'post-sales-converted-lead-details-' . $convertedLead->id . '.pdf';
        return response($mpdf->Output($filename, 'I'))
            ->header('Content-Type', 'application/pdf');
    }

    protected function ensureAccess(): void
    {
        if (
            RoleHelper::is_post_sales() ||
            RoleHelper::is_admin_or_super_admin() ||
            RoleHelper::is_general_manager()
        ) {
            return;
        }

        abort(403, 'Access denied.');
    }
}

