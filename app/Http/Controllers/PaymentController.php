<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\PaymentLink;
use App\Models\ConvertedLead;
use App\Helpers\AuthHelper;
use App\Helpers\RoleHelper;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments for a specific invoice
     */
    public function index(Request $request, $invoiceId)
    {
        $invoice = Invoice::with([
            'course',
            'batch',
            'student.lead',
            'paymentLinks' => function ($query) {
                $query->latest();
            },
        ])->findOrFail($invoiceId);
        
        // Check permissions
        $this->checkInvoiceAccess($invoice);
        
        $payments = Payment::with(['createdBy', 'proofs'])
            ->where('invoice_id', $invoiceId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Find the first payment (oldest approved payment) for tax invoice
        $firstPayment = Payment::where('invoice_id', $invoiceId)
            ->where('status', 'Approved')
            ->orderBy('created_at', 'asc')
            ->first();

        $paymentLinks = $invoice->paymentLinks;

        return view('admin.payments.index', compact('invoice', 'payments', 'firstPayment', 'paymentLinks'));
    }

    /**
     * Display a consolidated list of payments grouped by status
     */
    public function listAll(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_finance() && !RoleHelper::is_telecaller() && !RoleHelper::is_post_sales()) {
            abort(403, 'Access denied.');
        }

        $filters = [
            'from_date' => $request->input('from_date'),
            'to_date' => $request->input('to_date'),
            'payment_date_from' => $request->input('payment_date_from'),
            'payment_date_to' => $request->input('payment_date_to'),
            'student_id' => $request->input('student_id'),
            'search' => $request->input('search'),
        ];

        $students = ConvertedLead::select('id', 'name')
            ->orderBy('name')
            ->get();

        $withRelations = [
            'invoice.student.lead',
            'invoice.course',
            'invoice.batch',
            'createdBy',
            'collectedBy',
            'proofs',
        ];

        $pendingQuery = Payment::with($withRelations)
            ->whereHas('invoice')
            ->pending();
        $pendingPayments = $this->applyFilters($pendingQuery, $filters, 'pending')
            ->orderByDesc('created_at')
            ->get();

        $approvedQuery = Payment::with(array_merge($withRelations, [
            'approvedBy',
            'invoice.payments' => function ($query) {
                $query->approved()->orderBy('created_at', 'asc');
            },
        ]))
            ->whereHas('invoice')
            ->approved();

        $approvedPayments = $this->applyFilters($approvedQuery, $filters, 'approved')
            ->orderByDesc('approved_date')
            ->orderByDesc('created_at')
            ->get();

        $rejectedQuery = Payment::with(array_merge($withRelations, [
            'rejectedBy',
        ]))
            ->whereHas('invoice')
            ->where('status', 'Rejected');

        $rejectedPayments = $this->applyFilters($rejectedQuery, $filters, 'rejected')
            ->orderByDesc('rejected_date')
            ->orderByDesc('created_at')
            ->get();

        $counts = [
            'pending' => $pendingPayments->count(),
            'approved' => $approvedPayments->count(),
            'rejected' => $rejectedPayments->count(),
        ];

        return view('admin.payments.list', compact(
            'pendingPayments',
            'approvedPayments',
            'counts',
            'filters',
            'students',
            'rejectedPayments'
        ));
    }

    /**
     * Show the form for creating a new payment
     */
    public function create($invoiceId)
    {
        $invoice = Invoice::with([
            'course',
            'batch',
            'student.lead',
            'student',
            'payments' => function ($query) {
                $query->approved()->orderBy('created_at', 'desc');
            },
        ])->findOrFail($invoiceId);
        
        // Check permissions
        $this->checkInvoiceAccess($invoice);

        return view('admin.payments.create', compact('invoice'));
    }

    /**
     * Store a newly created payment
     */
    public function store(Request $request, $invoiceId)
    {
        $invoice = Invoice::with([
            'payments' => function ($query) {
                $query->approved();
            },
            'student',
        ])->findOrFail($invoiceId);

        // Check permissions
        $this->checkInvoiceAccess($invoice);

        $isCourse23 = (int) ($invoice->course_id ?? 0) === 23;

        $rules = [
            'payment_type' => 'required|in:Cash,Online,Bank,Cheque,Card,Other,Razorpay',
            'transaction_id' => 'nullable|string|max:255',
            'payment_date' => 'nullable|date',

            // Legacy single-payment fields (non-course23)
            'amount_paid' => 'required_unless:is_course23,1|numeric|min:0.01',
            'fee_head' => 'nullable|string|max:50',
            'file_upload' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',

            // Course 23 split-payment fields
            'payment_pg_amount' => 'nullable|numeric|min:0',
            'payment_ug_amount' => 'nullable|numeric|min:0',
            'payment_plustwo_amount' => 'nullable|numeric|min:0',
            'payment_sslc_amount' => 'nullable|numeric|min:0',
            'payment_mobile_amount' => 'nullable|numeric|min:0',
            'payment_pg_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'payment_ug_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'payment_plustwo_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'payment_sslc_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'payment_mobile_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];

        // Helper flag for conditional rules (so we can use required_unless)
        $request->merge(['is_course23' => $isCourse23 ? 1 : 0]);

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($validator) use ($request, $invoice, $isCourse23) {
            if (!$isCourse23) {
                return;
            }

            $pgPaid = (float) ($request->input('payment_pg_amount') ?: 0);
            $ugPaid = (float) ($request->input('payment_ug_amount') ?: 0);
            $plustwoPaid = (float) ($request->input('payment_plustwo_amount') ?: 0);
            $sslcPaid = (float) ($request->input('payment_sslc_amount') ?: 0);
            $mobilePaid = (float) ($request->input('payment_mobile_amount') ?: 0);

            if (!$invoice->hasNeedMobileAddon() && $mobilePaid > 0) {
                $validator->errors()->add('payment_mobile_amount', 'Mobile payment is only available when the student has Needed Mobile on file.');
            }

            if ($invoice->hasNeedMobileAddon()) {
                $mobileRemaining = max((float) $invoice->mobileNetAmount() - (float) $invoice->payments->where('fee_head', 'MOBILE')->sum('amount_paid'), 0);
                if ($mobilePaid > $mobileRemaining + 0.00001) {
                    $validator->errors()->add('payment_mobile_amount', 'Mobile paid amount cannot exceed the remaining mobile balance of ' . number_format($mobileRemaining, 2) . '.');
                }
            }

            $totalPaid = $pgPaid + $ugPaid + $plustwoPaid + $sslcPaid + $mobilePaid;
            if ($totalPaid <= 0) {
                $validator->errors()->add('payment_pg_amount', 'At least one payment amount (PG/UG/Plus Two/SSLC' . ($invoice->hasNeedMobileAddon() ? '/Needed Mobile' : '') . ') is required.');
                return;
            }

            $remainingBalance = (float) $invoice->pending_amount;
            if ($totalPaid > $remainingBalance) {
                $validator->errors()->add('payment_pg_amount', 'Total payment amount cannot exceed the remaining balance of ' . number_format($remainingBalance, 2) . '.');
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
            if ($invoice->hasNeedMobileAddon() && $mobilePaid > 0 && !$request->hasFile('payment_mobile_file')) {
                $validator->errors()->add('payment_mobile_file', 'Needed Mobile payment proof is required when mobile paid amount is entered.');
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
            // Check if there's any pending payment
            // COMMENTED OUT: Not needed currently
            /*
            $pendingPayment = Payment::where('invoice_id', $invoiceId)
                ->where('status', 'Pending Approval')
                ->first();
                
            if ($pendingPayment) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot add new payment. There is already a pending payment waiting for approval.'
                    ], 400);
                }
                
                return redirect()->back()
                    ->with('message_danger', 'Cannot add new payment. There is already a pending payment waiting for approval.')
                    ->withInput();
            }
            */
            
            $remainingBalance = (float) $invoice->pending_amount;

            // Calculate previous balance (sum of all approved payments)
            $previousBalance = Payment::where('invoice_id', $invoiceId)
                ->where('status', 'Approved')
                ->sum('amount_paid');

            if ($isCourse23) {
                $splitPayments = [
                    'PG' => ['amount' => (float) ($request->input('payment_pg_amount') ?: 0), 'file' => $request->file('payment_pg_file')],
                    'UG' => ['amount' => (float) ($request->input('payment_ug_amount') ?: 0), 'file' => $request->file('payment_ug_file')],
                    'PLUS_TWO' => ['amount' => (float) ($request->input('payment_plustwo_amount') ?: 0), 'file' => $request->file('payment_plustwo_file')],
                    'SSLC' => ['amount' => (float) ($request->input('payment_sslc_amount') ?: 0), 'file' => $request->file('payment_sslc_file')],
                ];
                if ($invoice->hasNeedMobileAddon()) {
                    $splitPayments['MOBILE'] = [
                        'amount' => (float) ($request->input('payment_mobile_amount') ?: 0),
                        'file' => $request->file('payment_mobile_file'),
                    ];
                }

                $createdCount = 0;
                foreach ($splitPayments as $feeHead => $payload) {
                    if (($payload['amount'] ?? 0) <= 0) {
                        continue;
                    }

                    if (($payload['amount'] ?? 0) > $remainingBalance) {
                        return redirect()->back()
                            ->with('message_danger', 'Payment amount cannot exceed the remaining balance of ' . number_format($remainingBalance, 2))
                            ->withInput();
                    }

                    $filePath = null;
                    if (!empty($payload['file'])) {
                        $file = $payload['file'];
                        $fileName = Str::uuid() . '_' . $file->getClientOriginalName();
                        $filePath = $file->storeAs('payments', $fileName, 'public');
                    }

                    Payment::create([
                        'invoice_id' => $invoiceId,
                        'amount_paid' => $payload['amount'],
                        'fee_head' => $feeHead,
                        'previous_balance' => $previousBalance,
                        'payment_type' => $request->payment_type,
                        'transaction_id' => $request->transaction_id,
                        'payment_date' => $request->payment_date ?? now()->toDateString(),
                        'file_upload' => $filePath,
                        'status' => 'Pending Approval',
                        'created_by' => AuthHelper::getCurrentUserId(),
                        'collected_by' => AuthHelper::getCurrentUserId(),
                    ]);

                    $createdCount++;
                }

                if ($createdCount <= 0) {
                    return redirect()->back()
                        ->with('message_danger', 'Please enter at least one payment amount.')
                        ->withInput();
                }
            } else {
                // Check if payment amount doesn't exceed remaining balance
                if ((float) $request->amount_paid > $remainingBalance) {
                    if (request()->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Payment amount cannot exceed the remaining balance of ' . number_format($remainingBalance, 2)
                        ], 400);
                    }
                    
                    return redirect()->back()
                        ->with('message_danger', 'Payment amount cannot exceed the remaining balance of ' . number_format($remainingBalance, 2))
                        ->withInput();
                }

                $filePath = null;
                if ($request->hasFile('file_upload')) {
                    $file = $request->file('file_upload');
                    $fileName = Str::uuid() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('payments', $fileName, 'public');
                }

                $feeHead = null;
                $payment = Payment::create([
                    'invoice_id' => $invoiceId,
                    'amount_paid' => $request->amount_paid,
                    'fee_head' => $feeHead,
                    'previous_balance' => $previousBalance,
                    'payment_type' => $request->payment_type,
                    'transaction_id' => $request->transaction_id,
                    'payment_date' => $request->payment_date ?? now()->toDateString(),
                    'file_upload' => $filePath,
                    'status' => 'Pending Approval',
                    'created_by' => AuthHelper::getCurrentUserId(),
                    'collected_by' => AuthHelper::getCurrentUserId(),
                ]);
            }

            // Don't update invoice until payment is approved

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $isCourse23 ? 'Payments added successfully!' : 'Payment added successfully!'
                ]);
            }
            
            return redirect()->route('admin.payments.index', $invoiceId)
                ->with('message_success', $isCourse23 ? 'Payments added successfully!' : 'Payment added successfully!');

        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while adding the payment. Please try again.'
                ], 500);
            }
            
            return redirect()->back()
                ->with('message_danger', 'An error occurred while adding the payment. Please try again.')
                ->withInput();
        }
    }

    /**
     * Approve a payment
     */
    public function approve($id, Request $request)
    {
        // Check if user has permission to approve payments
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_finance()) {
            abort(403, 'Access denied. Only Admin, Super Admin, and Finance can approve payments.');
        }

        try {
            $payment = Payment::findOrFail($id);
            
            // Check permissions
            $this->checkInvoiceAccess($payment->invoice);
            
            // Get transaction_id from request (can be empty string to clear it)
            $transactionId = $request->input('transaction_id', null);
            
            $payment->approve($transactionId);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment approved successfully!'
                ]);
            }

            return redirect()->back()
                ->with('message_success', 'Payment approved successfully!');

        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while approving the payment. Please try again.'
                ], 500);
            }

            return redirect()->back()
                ->with('message_danger', 'An error occurred while approving the payment. Please try again.');
        }
    }

    /**
     * Reject a payment
     */
    public function reject($id, Request $request)
    {
        // Check if user has permission to reject payments
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_finance()) {
            abort(403, 'Access denied. Only Admin, Super Admin, and Finance can reject payments.');
        }

        try {
            $payment = Payment::findOrFail($id);
            
            // Check permissions
            $this->checkInvoiceAccess($payment->invoice);
            
            $request->validate([
                'rejection_remarks' => 'nullable|string|max:1000',
            ]);
            
            $payment->reject($request->input('rejection_remarks'));

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment rejected successfully!'
                ]);
            }

            return redirect()->back()
                ->with('message_success', 'Payment rejected successfully!');

        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while rejecting the payment. Please try again.'
                ], 500);
            }

            return redirect()->back()
                ->with('message_danger', 'An error occurred while rejecting the payment. Please try again.');
        }
    }

    /**
     * Display the specified payment
     */
    public function show($id)
    {
        $payment = Payment::with(['invoice.course', 'invoice.batch', 'invoice.student.lead', 'createdBy', 'proofs'])
            ->findOrFail($id);
        
        // Check permissions
        $this->checkInvoiceAccess($payment->invoice);

        // Find the first payment (oldest approved payment) for tax invoice
        $firstPayment = Payment::where('invoice_id', $payment->invoice_id)
            ->where('status', 'Approved')
            ->orderBy('created_at', 'asc')
            ->first();

        return view('admin.payments.show', compact('payment', 'firstPayment'));
    }

    /**
     * View a payment proof file.
     */
    public function viewProofFile($proofId)
    {
        $proof = \App\Models\PaymentProof::with('payment.invoice')->findOrFail($proofId);
        $this->checkInvoiceAccess($proof->payment->invoice);

        if (!$proof->file_upload || !Storage::disk('public')->exists($proof->file_upload)) {
            return redirect()->back()
                ->with('message_danger', 'File not found.');
        }

        $filePath = storage_path('app/public/' . $proof->file_upload);
        $mimeType = mime_content_type($filePath);

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($proof->file_upload) . '"',
        ]);
    }

    /**
     * Download a payment proof file.
     */
    public function downloadProofFile($proofId)
    {
        $proof = \App\Models\PaymentProof::with('payment.invoice')->findOrFail($proofId);
        $this->checkInvoiceAccess($proof->payment->invoice);

        if (!$proof->file_upload || !Storage::disk('public')->exists($proof->file_upload)) {
            return redirect()->back()
                ->with('message_danger', 'File not found.');
        }

        return response()->download(
            storage_path('app/public/' . $proof->file_upload),
            basename($proof->file_upload)
        );
    }

    /**
     * View payment file
     */
    public function viewFile($id)
    {
        $payment = Payment::findOrFail($id);
        
        // Check permissions
        $this->checkInvoiceAccess($payment->invoice);
        
        if (!$payment->file_upload || !Storage::disk('public')->exists($payment->file_upload)) {
            return redirect()->back()
                ->with('message_danger', 'File not found.');
        }

        $filePath = storage_path('app/public/' . $payment->file_upload);
        $mimeType = mime_content_type($filePath);
        
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($payment->file_upload) . '"'
        ]);
    }

    /**
     * Download payment file
     */
    public function downloadFile($id)
    {
        $payment = Payment::findOrFail($id);
        
        // Check permissions
        $this->checkInvoiceAccess($payment->invoice);
        
        if (!$payment->file_upload || !Storage::disk('public')->exists($payment->file_upload)) {
            return redirect()->back()
                ->with('message_danger', 'File not found.');
        }

        return response()->download(storage_path('app/public/' . $payment->file_upload), basename($payment->file_upload));
    }

    /**
     * Show tax invoice for payment
     */
    public function taxInvoice($id)
    {
        $payment = Payment::with(['invoice.student.leadDetail', 'invoice.course', 'invoice.batch'])
            ->findOrFail($id);
        
        // Check permissions
        $this->checkInvoiceAccess($payment->invoice);
        
        // Allow tax invoice only for course-type invoices
        // For non-course invoices, redirect to payment receipt
        if ($payment->invoice->invoice_type !== 'course') {
            return redirect()->route('admin.payments.payment-receipt', $payment->id);
        }

        // Check if payment is approved
        if ($payment->status !== 'Approved') {
            return redirect()->back()
                ->with('message_danger', 'Tax invoice can only be viewed for approved payments.');
        }

        // Add number to words conversion
        $payment->amount_in_words = $this->numberToWords($payment->amount_paid);

        $feeHeadColumn = null;
        if ((int) ($payment->invoice->course_id ?? 0) === 23 && $payment->fee_head) {
            $feeHeadToColumn = [
                'PG' => 'fee_pg_amount',
                'UG' => 'fee_ug_amount',
                'PLUS_TWO' => 'fee_plustwo_amount',
                'SSLC' => 'fee_sslc_amount',
                'MOBILE' => Invoice::TAX_LINE_FEE_HEAD_MOBILE,
            ];
            $feeHeadColumn = $feeHeadToColumn[$payment->fee_head] ?? null;
        }
        $taxInvoiceTotal = $payment->invoice->taxInvoiceLineTotal($feeHeadColumn);

        $payment->tax_invoice_total = $taxInvoiceTotal;
        $payment->tax_invoice_taxable = $taxInvoiceTotal > 0 ? ($taxInvoiceTotal / 1.18) : 0.0;
        $payment->tax_invoice_gst = $payment->tax_invoice_taxable * 0.18;
        $payment->total_amount_in_words = (int) ($payment->invoice->course_id ?? 0) === 23
            ? $this->numberToWords((float) $payment->invoice->net_amount)
            : $this->numberToWords($taxInvoiceTotal);

        return view('admin.payments.tax-invoice', compact('payment'));
    }

    /**
     * Generate PDF for tax invoice
     */
    public function taxInvoicePdf($id)
    {
        $payment = Payment::with(['invoice.student.leadDetail', 'invoice.course', 'invoice.batch'])
            ->findOrFail($id);
        
        // Check permissions
        $this->checkInvoiceAccess($payment->invoice);
        
        // Allow tax invoice only for course-type invoices
        // For non-course invoices, redirect to payment receipt PDF
        if ($payment->invoice->invoice_type !== 'course') {
            return redirect()->route('admin.payments.payment-receipt-pdf', $payment->id);
        }

        // Check if payment is approved
        if ($payment->status !== 'Approved') {
            return redirect()->back()
                ->with('message_danger', 'Tax invoice PDF can only be generated for approved payments.');
        }
        
        // Calculate previous balance if not set (for older payments)
        if ($payment->previous_balance === null) {
            $payment->previous_balance = Payment::where('invoice_id', $payment->invoice_id)
                ->where('status', 'Approved')
                ->where('id', '<', $payment->id)
                ->sum('amount_paid');
        }
        
        // Add number to words conversion
        $payment->amount_in_words = $this->numberToWords($payment->amount_paid);

        $feeHeadColumn = null;
        if ((int) ($payment->invoice->course_id ?? 0) === 23 && $payment->fee_head) {
            $feeHeadToColumn = [
                'PG' => 'fee_pg_amount',
                'UG' => 'fee_ug_amount',
                'PLUS_TWO' => 'fee_plustwo_amount',
                'SSLC' => 'fee_sslc_amount',
                'MOBILE' => Invoice::TAX_LINE_FEE_HEAD_MOBILE,
            ];
            $feeHeadColumn = $feeHeadToColumn[$payment->fee_head] ?? null;
        }
        $taxInvoiceTotal = $payment->invoice->taxInvoiceLineTotal($feeHeadColumn);

        $payment->tax_invoice_total = $taxInvoiceTotal;
        $payment->tax_invoice_taxable = $taxInvoiceTotal > 0 ? ($taxInvoiceTotal / 1.18) : 0.0;
        $payment->tax_invoice_gst = $payment->tax_invoice_taxable * 0.18;
        $payment->total_amount_in_words = (int) ($payment->invoice->course_id ?? 0) === 23
            ? $this->numberToWords((float) $payment->invoice->net_amount)
            : $this->numberToWords($taxInvoiceTotal);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.payments.tax-invoice-pdf-inline', compact('payment'));
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Arial',
            'isPhpEnabled' => true,
            'isJavascriptEnabled' => false,
            'dpi' => 96,
            'defaultMediaType' => 'print',
            'isFontSubsettingEnabled' => true,
            'debugKeepTemp' => false,
            'debugCss' => false,
            'debugLayout' => false,
            'debugLayoutLines' => false,
            'debugLayoutBlocks' => false,
            'debugLayoutInline' => false,
            'debugLayoutPaddingBox' => false,
        ]);
        
        $filename = 'tax_invoice_' . $payment->invoice->invoice_number . '_' . $payment->id . '.pdf';
        
        return $pdf->stream($filename);
    }

    /**
     * Show payment receipt for payment
     */
    public function paymentReceipt($id)
    {
        $payment = Payment::with(['invoice.student.leadDetail', 'invoice.course', 'invoice.batch'])
            ->findOrFail($id);
        
        // Check permissions
        $this->checkInvoiceAccess($payment->invoice);
        
        // Check if payment is approved
        if ($payment->status !== 'Approved') {
            return redirect()->back()
                ->with('message_danger', 'Payment receipt can only be viewed for approved payments.');
        }
        
        // Add number to words conversion
        $payment->amount_in_words = $this->numberToWords($payment->amount_paid);
        
        return view('admin.payments.payment-receipt', compact('payment'));
    }

    /**
     * Generate PDF for payment receipt
     */
    public function paymentReceiptPdf($id)
    {
        $payment = Payment::with(['invoice.student.leadDetail', 'invoice.course', 'invoice.batch'])
            ->findOrFail($id);
        
        // Check permissions
        $this->checkInvoiceAccess($payment->invoice);
        
        // Check if payment is approved
        if ($payment->status !== 'Approved') {
            return redirect()->back()
                ->with('message_danger', 'Payment receipt PDF can only be generated for approved payments.');
        }
        
        // Calculate previous balance if not set (for older payments)
        if ($payment->previous_balance === null) {
            $payment->previous_balance = Payment::where('invoice_id', $payment->invoice_id)
                ->where('status', 'Approved')
                ->where('id', '<', $payment->id)
                ->sum('amount_paid');
        }
        
        // Add number to words conversion
        $payment->amount_in_words = $this->numberToWords($payment->amount_paid);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.payments.payment-receipt-pdf-inline', compact('payment'));
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Arial',
            'isPhpEnabled' => true,
            'isJavascriptEnabled' => false,
            'dpi' => 96,
            'defaultMediaType' => 'print',
            'isFontSubsettingEnabled' => true,
            'debugKeepTemp' => false,
            'debugCss' => false,
            'debugLayout' => false,
            'debugLayoutLines' => false,
            'debugLayoutBlocks' => false,
            'debugLayoutInline' => false,
            'debugLayoutPaddingBox' => false,
        ]);
        
        $filename = 'payment_receipt_' . $payment->invoice->invoice_number . '_' . $payment->id . '.pdf';
        
        return $pdf->stream($filename);
    }

    /**
     * Convert number to words
     */
    private function numberToWords($number)
    {
        // Normalize value and support decimal (paise) amounts stored as strings
        $number = (float) $number;
        $number = round($number, 2);

        $rupees = (int) floor($number);
        $paise = (int) round(($number - $rupees) * 100);

        $words = $this->convertNumberPortionToWords($rupees);

        if ($paise > 0) {
            $words .= ' and ' . $this->convertNumberPortionToWords($paise) . ' Paise';
        }

        return trim($words);
    }

    /**
     * Recursively convert integer portion to words.
     */
    private function convertNumberPortionToWords(int $number): string
    {
        $ones = [
            0 => 'Zero', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
            6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
            11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
            16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen'
        ];

        $tens = [
            20 => 'Twenty', 30 => 'Thirty', 40 => 'Forty', 50 => 'Fifty',
            60 => 'Sixty', 70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
        ];

        if ($number < 20) {
            return $ones[$number];
        }

        if ($number < 100) {
            return $tens[10 * floor($number / 10)] . ($number % 10 ? ' ' . $ones[$number % 10] : '');
        }

        if ($number < 1000) {
            return $ones[floor($number / 100)] . ' Hundred' . ($number % 100 ? ' ' . $this->convertNumberPortionToWords($number % 100) : '');
        }

        if ($number < 100000) {
            return $this->convertNumberPortionToWords(floor($number / 1000)) . ' Thousand' . ($number % 1000 ? ' ' . $this->convertNumberPortionToWords($number % 1000) : '');
        }

        if ($number < 10000000) {
            return $this->convertNumberPortionToWords(floor($number / 100000)) . ' Lakh' . ($number % 100000 ? ' ' . $this->convertNumberPortionToWords($number % 100000) : '');
        }

        return $this->convertNumberPortionToWords(floor($number / 10000000)) . ' Crore' . ($number % 10000000 ? ' ' . $this->convertNumberPortionToWords($number % 10000000) : '');
    }

    /**
     * Generate a Razorpay payment link for an invoice.
     */
    public function storePaymentLink(Request $request, Invoice $invoice, RazorpayService $razorpayService)
    {
        $this->checkInvoiceAccess($invoice);

        if (!$razorpayService->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'Razorpay credentials are not configured. Please update the environment variables.',
            ], 422);
        }

        $pendingAmount = max((float) $invoice->pending_amount, 0);

        if ($pendingAmount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'This invoice is already fully paid.',
            ], 422);
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'description' => ['nullable', 'string', 'max:190'],
        ]);

        if ($validated['amount'] > $pendingAmount) {
            return response()->json([
                'success' => false,
                'message' => 'Amount cannot exceed the pending balance of ₹' . number_format($pendingAmount, 2),
            ], 422);
        }

        $amount = round($validated['amount'], 2);
        $referenceId = 'INV-' . $invoice->id . '-' . strtoupper(Str::random(6));
        $description = $validated['description'] ?? 'Payment for invoice ' . $invoice->invoice_number;
        $currency = config('razorpay.default_currency', 'INR');
        $customerContact = $this->formatCustomerContact($invoice->student->code ?? null, $invoice->student->phone ?? null);
        $customerEmail = $invoice->student->email;

        // Razorpay requires either email or contact; fail fast with a clear message
        if (!$customerContact && !$customerEmail) {
            return response()->json([
                'success' => false,
                'message' => 'Student phone or email is required to generate a payment link. Please update the student contact details.',
            ], 422);
        }

        $payload = [
            'amount' => (int) round($amount * 100),
            'currency' => $currency,
            'reference_id' => $referenceId,
            'description' => $description,
            'customer' => array_filter([
                'name' => $invoice->student->name,
                'email' => $customerEmail,
                'contact' => $customerContact,
            ]),
            'notify' => [
                'sms' => false,
                'email' => false,
            ],
            'notes' => [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'student_id' => $invoice->student_id,
            ],
        ];

        if (config('razorpay.payment_link.reminder_enable')) {
            $payload['reminder_enable'] = true;
        }

        $expireMinutes = (int) config('razorpay.payment_link.expire_minutes', 0);
        $expireTimestamp = null;
        if ($expireMinutes > 0) {
            $expireTimestamp = now()->addMinutes($expireMinutes)->timestamp;
            $payload['expire_by'] = $expireTimestamp;
        }

        try {
            $response = $razorpayService->createPaymentLink($payload);

            $paymentLink = $invoice->paymentLinks()->create([
                'amount' => $amount,
                'currency' => $currency,
                'status' => $response['status'] ?? 'created',
                'reference_id' => $response['reference_id'] ?? $referenceId,
                'razorpay_id' => $response['id'] ?? null,
                'short_url' => $response['short_url'] ?? null,
                'description' => $description,
                'token' => Str::uuid(),
                'customer_name' => $invoice->student->name,
                'customer_email' => $invoice->student->email,
                'customer_phone' => trim(($invoice->student->code ?? '') . ' ' . ($invoice->student->phone ?? '')),
                'expires_at' => isset($response['expire_by']) && $response['expire_by'] ? Carbon::createFromTimestamp($response['expire_by']) : ($expireTimestamp ? Carbon::createFromTimestamp($expireTimestamp) : null),
                'meta' => $response,
                'created_by' => AuthHelper::getCurrentUserId(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment link generated successfully.',
                'data' => [
                    'id' => $paymentLink->id,
                    'status' => $paymentLink->status,
                    'short_url' => $paymentLink->short_url,
                    'amount' => $paymentLink->amount,
                ],
            ]);
        } catch (\Throwable $exception) {
            Log::error('Failed to generate payment link', [
                'invoice_id' => $invoice->id,
                'message' => $exception->getMessage(),
                'payload' => $payload ?? [],
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to create payment link. Please try again later. Ensure student contact (phone/email) is valid.',
            ], 500);
        }
    }

    /**
     * Refresh payment link status from Razorpay.
     */
    public function refreshPaymentLink(Invoice $invoice, PaymentLink $paymentLink, RazorpayService $razorpayService)
    {
        $this->checkInvoiceAccess($invoice);

        if ($paymentLink->invoice_id !== $invoice->id) {
            abort(404);
        }

        if (!$razorpayService->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'Razorpay credentials are not configured.',
            ], 422);
        }

        if (!$paymentLink->razorpay_id) {
            return response()->json([
                'success' => false,
                'message' => 'Payment link reference is missing.',
            ], 400);
        }

        $response = $razorpayService->safeFetchPaymentLink($paymentLink->razorpay_id);

        if (!$response) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to refresh the payment link at the moment.',
            ], 500);
        }

        $paymentLink->status = $response['status'] ?? $paymentLink->status;
        $paymentLink->short_url = $response['short_url'] ?? $paymentLink->short_url;
        $paymentLink->reference_id = $response['reference_id'] ?? $paymentLink->reference_id;

        if (!empty($response['expire_by'])) {
            $paymentLink->expires_at = Carbon::createFromTimestamp($response['expire_by']);
        } elseif ($paymentLink->expires_at && $paymentLink->expires_at->equalTo(Carbon::createFromTimestamp(0))) {
            $paymentLink->expires_at = null;
        }

        $paymentLink->meta = $response;

        if (!empty($response['payments']) && is_array($response['payments'])) {
            $latestPayment = collect($response['payments'])->sortByDesc('created_at')->first();
            if ($latestPayment) {
                // Extract payment ID - could be 'id', 'payment_id', or nested
                $paymentId = $latestPayment['id'] ?? $latestPayment['payment_id'] ?? $latestPayment['payment']['id'] ?? null;
                
                if ($paymentId) {
                    $paymentLink->razorpay_payment_id = $paymentId;
                }
                
                if (!empty($latestPayment['created_at'])) {
                    $paymentLink->paid_at = Carbon::createFromTimestamp($latestPayment['created_at']);
                }

                // Fetch full payment details from Razorpay to get all payment method information
                if ($paymentId) {
                    $razorpayService = app(RazorpayService::class);
                    try {
                        $fullPaymentDetails = $razorpayService->fetchPayment($paymentId);
                        $this->syncPaymentLinkToInvoice($invoice, $paymentLink, $fullPaymentDetails);
                    } catch (\Exception $e) {
                        // If fetching full details fails, try to use payment data from payment link
                        Log::warning('Failed to fetch full payment details from Razorpay', [
                            'payment_id' => $paymentId,
                            'error' => $e->getMessage(),
                        ]);
                        
                        // Ensure the payment data has an 'id' field for syncing
                        if (!isset($latestPayment['id']) && $paymentId) {
                            $latestPayment['id'] = $paymentId;
                        }
                        
                        $this->syncPaymentLinkToInvoice($invoice, $paymentLink, $latestPayment);
                    }
                } else {
                    // If no payment ID found, log and try to use payment link's stored payment ID
                    Log::warning('Payment ID not found in payment link response', [
                        'invoice_id' => $invoice->id,
                        'payment_link_id' => $paymentLink->id,
                        'payment_data' => $latestPayment,
                    ]);
                    
                    // Try using the stored razorpay_payment_id if available
                    if ($paymentLink->razorpay_payment_id) {
                        try {
                            $razorpayService = app(RazorpayService::class);
                            $fullPaymentDetails = $razorpayService->fetchPayment($paymentLink->razorpay_payment_id);
                            $this->syncPaymentLinkToInvoice($invoice, $paymentLink, $fullPaymentDetails);
                        } catch (\Exception $e) {
                            Log::error('Failed to fetch payment using stored payment ID', [
                                'payment_id' => $paymentLink->razorpay_payment_id,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    } else {
                        // Last resort: try to sync with available data if it has an id field
                        if (isset($latestPayment['id'])) {
                            $this->syncPaymentLinkToInvoice($invoice, $paymentLink, $latestPayment);
                        }
                    }
                }
            }
        } else {
            // Check if payment link has a stored payment ID but no payments array
            // This might happen if payment was made but response structure is different
            if ($paymentLink->razorpay_payment_id && empty($response['payments'])) {
                try {
                    $razorpayService = app(RazorpayService::class);
                    $fullPaymentDetails = $razorpayService->fetchPayment($paymentLink->razorpay_payment_id);
                    $this->syncPaymentLinkToInvoice($invoice, $paymentLink, $fullPaymentDetails);
                } catch (\Exception $e) {
                    Log::warning('Failed to fetch payment using stored payment ID', [
                        'payment_id' => $paymentLink->razorpay_payment_id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        $paymentLink->save();

        return response()->json([
            'success' => true,
            'message' => 'Payment link status refreshed.',
            'data' => [
                'status' => $paymentLink->status,
                'paid_at' => optional($paymentLink->paid_at)->format('d M Y h:i A'),
                'short_url' => $paymentLink->short_url,
            ],
        ]);
    }

    /**
     * Delete a payment link (only if status is 'created')
     */
    public function deletePaymentLink(Invoice $invoice, PaymentLink $paymentLink)
    {
        $this->checkInvoiceAccess($invoice);

        if ($paymentLink->invoice_id !== $invoice->id) {
            return response()->json([
                'success' => false,
                'message' => 'Payment link does not belong to this invoice.',
            ], 400);
        }

        if ($paymentLink->status !== 'created') {
            return response()->json([
                'success' => false,
                'message' => 'Only payment links with status "created" can be deleted.',
            ], 400);
        }

        try {
            // Optionally cancel the link in Razorpay before deleting
            $razorpayService = app(RazorpayService::class);
            try {
                $razorpayService->cancelPaymentLink($paymentLink->razorpay_id);
            } catch (\Exception $e) {
                // Log but don't fail if Razorpay cancellation fails
                Log::warning('Failed to cancel payment link in Razorpay: ' . $e->getMessage());
            }

            $paymentLink->delete();

            return response()->json([
                'success' => true,
                'message' => 'Payment link deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete payment link', [
                'payment_link_id' => $paymentLink->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete payment link. Please try again.',
            ], 500);
        }
    }

    /**
     * Auto-create payment during lead conversion
     */
    public function autoCreate($invoiceId, $amount, $paymentType, $transactionId = null, $fileUpload = null, $paymentDate = null, $feeHead = null, $createdByUserId = null, array $proofs = [])
    {
        try {
            $invoice = Invoice::findOrFail($invoiceId);
            $actorId = $createdByUserId ?? AuthHelper::getCurrentUserId();

            // Calculate previous balance (sum of all approved payments)
            $previousBalance = Payment::where('invoice_id', $invoiceId)
                ->where('status', 'Approved')
                ->sum('amount_paid');

            if (empty($proofs) && ($transactionId || $fileUpload)) {
                $proofs = [
                    [
                        'transaction_id' => $transactionId,
                        'file' => $fileUpload,
                    ],
                ];
            }

            $storedProofs = \App\Helpers\PaymentProofHelper::storeProofFiles($proofs);
            $primaryTxn = $storedProofs[0]['transaction_id'] ?? $transactionId;
            $primaryFile = $storedProofs[0]['file_upload'] ?? null;

            $payment = Payment::create([
                'invoice_id' => $invoiceId,
                'amount_paid' => $amount,
                'fee_head' => $feeHead,
                'previous_balance' => $previousBalance,
                'payment_type' => $paymentType,
                'transaction_id' => $primaryTxn,
                'payment_date' => $paymentDate ?? now()->toDateString(),
                'file_upload' => $primaryFile,
                'status' => 'Pending Approval', // Keep as pending for manual approval
                'created_by' => $actorId,
                'collected_by' => $actorId,
            ]);

            if (!empty($storedProofs)) {
                \App\Helpers\PaymentProofHelper::attachToPayment($payment->id, $storedProofs);
            }

            // Don't update invoice until payment is approved

            return $payment;

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Check if user has access to the invoice
     */
    private function checkInvoiceAccess($invoice)
    {
        $currentUser = AuthHelper::getCurrentUser();
        if (!$currentUser) {
            abort(403, 'Access denied.');
        }

        $currentUserRole = AuthHelper::getCurrentUserRole();
        switch ($currentUserRole) {
            case 1: // Super Admin
            case 2: // Admin
            case 4: // Admission Counsellor
            case 5: // Academic Assistant
            case 6: // Finance
            case 7: // Post-sales
            case 11: // General Manager
                // Keep existing broad access for non-telecaller/team-lead roles.
                return;
            case 3: // Telecaller (including team lead flag)
                $lead = optional(optional($invoice->student)->lead);
                if (!$lead) {
                    abort(403, 'Access denied.');
                }

                // Team lead: own + team members. Telecaller: own only.
                if (RoleHelper::is_team_lead()) {
                    $teamMemberIds = [];
                    if ($currentUser->team_id) {
                        $teamMemberIds = AuthHelper::getTeamMemberIds($currentUser->team_id);
                    }
                    $teamMemberIds[] = $currentUser->id;
                    $teamMemberIds = array_unique(array_filter($teamMemberIds));

                    if (!in_array((int) $lead->telecaller_id, $teamMemberIds, true)) {
                        abort(403, 'Access denied.');
                    }
                } else {
                    if ((int) $lead->telecaller_id !== (int) $currentUser->id) {
                        abort(403, 'Access denied.');
                    }
                }

                // If current user is B2B, allow only B2B channel leads.
                if ((int) ($currentUser->is_b2b ?? 0) === 1) {
                    $leadTeamIsB2B = (int) (optional($lead->team)->is_b2b ?? 0) === 1;
                    $leadIsB2B = (int) ($lead->is_b2b ?? 0) === 1;
                    if (!($leadIsB2B || $leadTeamIsB2B)) {
                        abort(403, 'Access denied.');
                    }
                }
                return;
            default:
                abort(403, 'Access denied.');
        }
    }

    /**
     * Apply filter conditions for payment listings
     */
    private function applyFilters($query, array $filters, string $status)
    {
        $this->applyRoleBasedScope($query);

        if (!empty($filters['student_id'])) {
            $studentId = (int) $filters['student_id'];
            $query->whereHas('invoice', function ($invoiceQuery) use ($studentId) {
                $invoiceQuery->where('student_id', $studentId);
            });
        }

        if (!empty($filters['search'])) {
            $search = '%' . trim($filters['search']) . '%';

            $query->where(function ($paymentQuery) use ($search) {
                $paymentQuery->whereHas('invoice', function ($invoiceQuery) use ($search) {
                    $invoiceQuery->where('invoice_number', 'like', $search)
                        ->orWhereHas('student', function ($studentQuery) use ($search) {
                            $studentQuery->where('name', 'like', $search)
                                ->orWhere('phone', 'like', $search);
                        })
                        ->orWhereHas('student.lead', function ($leadQuery) use ($search) {
                            $leadQuery->where('title', 'like', $search)
                                ->orWhere('phone', 'like', $search);
                        });
                })
                ->orWhere('transaction_id', 'like', $search)
                ->orWhereHas('proofs', function ($proofQuery) use ($search) {
                    $proofQuery->where('transaction_id', 'like', $search);
                });
            });
        }

        switch ($status) {
            case 'approved':
                $dateColumn = 'approved_date';
                break;
            case 'rejected':
                $dateColumn = 'rejected_date';
                break;
            default:
                $dateColumn = 'created_at';
        }

        if (!empty($filters['from_date'])) {
            $query->whereDate($dateColumn, '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate($dateColumn, '<=', $filters['to_date']);
        }

        // Filter by payment_date if provided
        if (!empty($filters['payment_date_from'])) {
            $query->whereDate('payment_date', '>=', $filters['payment_date_from']);
        }

        if (!empty($filters['payment_date_to'])) {
            $query->whereDate('payment_date', '<=', $filters['payment_date_to']);
        }

        return $query;
    }

    /**
     * Restrict telecaller/team-lead payments visibility to their own scope.
     * For B2B users, additionally restrict to B2B leads/teams.
     */
    private function applyRoleBasedScope($query): void
    {
        $currentUser = AuthHelper::getCurrentUser();
        if (!$currentUser) {
            return;
        }

        // Keep existing visibility for roles other than telecaller/team lead.
        if (!RoleHelper::is_telecaller()) {
            return;
        }

        if (RoleHelper::is_team_lead()) {
            $teamMemberIds = [];
            if ($currentUser->team_id) {
                $teamMemberIds = AuthHelper::getTeamMemberIds($currentUser->team_id);
            }
            $teamMemberIds[] = $currentUser->id;
            $teamMemberIds = array_unique(array_filter($teamMemberIds));

            $query->whereHas('invoice.student.lead', function ($leadQuery) use ($teamMemberIds) {
                $leadQuery->whereIn('telecaller_id', $teamMemberIds);
            });
        } else {
            $query->whereHas('invoice.student.lead', function ($leadQuery) use ($currentUser) {
                $leadQuery->where('telecaller_id', $currentUser->id);
            });
        }

        if ((int) ($currentUser->is_b2b ?? 0) === 1) {
            $query->whereHas('invoice.student.lead', function ($leadQuery) {
                $leadQuery->where(function ($q) {
                    $q->where('is_b2b', 1)
                        ->orWhereHas('team', function ($teamQuery) {
                            $teamQuery->where('is_b2b', 1)
                                ->whereNull('deleted_at');
                        });
                });
            });
        }
    }

    private function formatCustomerContact(?string $code, ?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        $numericPhone = preg_replace('/\D+/', '', $phone);
        if (!$numericPhone) {
            return null;
        }

        $numericCode = $code ? preg_replace('/\D+/', '', $code) : '91';
        if (!$numericCode) {
            $numericCode = '91';
        }

        if (str_starts_with($numericPhone, $numericCode)) {
            return '+' . $numericPhone;
        }

        return '+' . ltrim($numericCode, '+') . $numericPhone;
    }

    private function syncPaymentLinkToInvoice(Invoice $invoice, PaymentLink $paymentLink, array $latestPayment): void
    {
        $transactionId = $latestPayment['id'] ?? null;
        if (!$transactionId) {
            Log::warning('Cannot sync payment: missing transaction ID', [
                'invoice_id' => $invoice->id,
                'payment_link_id' => $paymentLink->id,
            ]);
            return;
        }

        $amount = isset($latestPayment['amount']) ? round(((float) $latestPayment['amount']) / 100, 2) : $paymentLink->amount;
        if ($amount <= 0) {
            Log::warning('Cannot sync payment: invalid amount', [
                'invoice_id' => $invoice->id,
                'transaction_id' => $transactionId,
                'amount' => $amount,
            ]);
            return;
        }

        // Check for existing payment by base transaction_id (before details are appended)
        // Check both exact match and match with appended details
        $existingPayment = Payment::where('invoice_id', $invoice->id)
            ->where(function($query) use ($transactionId) {
                $query->where('transaction_id', $transactionId)
                      ->orWhere('transaction_id', 'like', $transactionId . ' (%');
            })
            ->first();

        if ($existingPayment) {
            // Payment exists - update and approve if needed
            Log::info('Updating existing payment from Razorpay', [
                'payment_id' => $existingPayment->id,
                'invoice_id' => $invoice->id,
                'transaction_id' => $transactionId,
                'current_status' => $existingPayment->status,
            ]);

            // Set collected_by to payment link creator if not already set
            if (!$existingPayment->collected_by && $paymentLink->created_by) {
                $existingPayment->collected_by = $paymentLink->created_by;
            }

            // Update with full payment details
            $this->updatePaymentWithFullDetails($existingPayment, $latestPayment);
            
            // Approve if not already approved
            if ($existingPayment->status !== 'Approved') {
                $existingPayment->approve();
                Log::info('Existing payment auto-approved', [
                    'payment_id' => $existingPayment->id,
                ]);
            } else {
                // Save updates even if already approved
                $existingPayment->save();
            }
            return;
        }

        // Payment doesn't exist - create new one
        $previousBalance = Payment::where('invoice_id', $invoice->id)
            ->where('status', 'Approved')
            ->sum('amount_paid');

        try {
            // Create payment with base transaction_id first
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'amount_paid' => $amount,
                'previous_balance' => $previousBalance,
                'payment_type' => 'Razorpay',
                'transaction_id' => $transactionId, // Base ID, will be updated with details
                'status' => 'Pending Approval',
                'created_by' => $paymentLink->created_by ?? AuthHelper::getCurrentUserId(),
                'collected_by' => $paymentLink->created_by ?? AuthHelper::getCurrentUserId(), // Set to payment link creator
            ]);

            Log::info('Payment created from Razorpay payment link', [
                'payment_id' => $payment->id,
                'invoice_id' => $invoice->id,
                'transaction_id' => $transactionId,
                'amount' => $amount,
            ]);

            // Update with full payment details (this may modify transaction_id)
            $this->updatePaymentWithFullDetails($payment, $latestPayment);
            
            // Refresh payment to ensure we have latest data
            $payment->refresh();
            
            // Automatically approve the payment
            $payment->approve();
            
            Log::info('Payment automatically approved from Razorpay payment link', [
                'payment_id' => $payment->id,
                'invoice_id' => $invoice->id,
                'transaction_id' => $payment->transaction_id,
                'status' => $payment->status,
                'amount' => $payment->amount_paid,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create payment from Razorpay payment link', [
                'invoice_id' => $invoice->id,
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Update payment with full details from Razorpay payment response
     */
    private function updatePaymentWithFullDetails(Payment $payment, array $razorpayPayment): void
    {
        // Extract payment method details
        $method = $razorpayPayment['method'] ?? null;
        $bank = $razorpayPayment['bank'] ?? null;
        $wallet = $razorpayPayment['wallet'] ?? null;
        $vpa = $razorpayPayment['vpa'] ?? null;
        $card = $razorpayPayment['card'] ?? null;

        // Build description with payment details
        $details = [];
        if ($method) {
            $details[] = 'Method: ' . ucfirst($method);
        }
        if ($bank) {
            $details[] = 'Bank: ' . $bank;
        }
        if ($wallet) {
            $details[] = 'Wallet: ' . $wallet;
        }
        if ($vpa) {
            $details[] = 'UPI: ' . $vpa;
        }
        if ($card) {
            $cardDetails = [];
            if (isset($card['network'])) {
                $cardDetails[] = $card['network'];
            }
            if (isset($card['type'])) {
                $cardDetails[] = $card['type'];
            }
            if (isset($card['last4'])) {
                $cardDetails[] = '****' . $card['last4'];
            }
            if (!empty($cardDetails)) {
                $details[] = 'Card: ' . implode(' ', $cardDetails);
            }
        }

        // Store additional details in transaction_id or create a notes field
        // For now, we'll append to transaction_id if there are additional details
        if (!empty($details)) {
            $payment->transaction_id = $razorpayPayment['id'] . ' (' . implode(', ', $details) . ')';
        }

        // Update amount if different
        if (isset($razorpayPayment['amount'])) {
            $razorpayAmount = round(((float) $razorpayPayment['amount']) / 100, 2);
            if ($razorpayAmount > 0 && abs($razorpayAmount - (float) $payment->amount_paid) > 0.01) {
                // Eloquent decimal casts are string-backed; assign as string to avoid type warnings
                $payment->amount_paid = (string) $razorpayAmount;
            }
        }

        $payment->save();
    }

    /**
     * Export all payments to PDF based on filters
     */
    public function exportPdf(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_finance() && !RoleHelper::is_telecaller() && !RoleHelper::is_post_sales()) {
            abort(403, 'Access denied.');
        }

        $filters = [
            'from_date' => $request->input('from_date'),
            'to_date' => $request->input('to_date'),
            'payment_date_from' => $request->input('payment_date_from'),
            'payment_date_to' => $request->input('payment_date_to'),
            'student_id' => $request->input('student_id'),
            'search' => $request->input('search'),
        ];

        $status = $request->input('status', 'pending');
        
        $withRelations = [
            'invoice.student.lead',
            'invoice.course',
            'invoice.batch',
            'createdBy',
        ];

        if ($status === 'approved') {
            $query = Payment::with(array_merge($withRelations, [
                'approvedBy',
                'invoice.payments' => function ($query) {
                    $query->approved()->orderBy('created_at', 'asc');
                },
            ]))
                ->whereHas('invoice')
                ->approved();
            
            $title = 'Approved Payments Report';
        } elseif ($status === 'rejected') {
            $query = Payment::with(array_merge($withRelations, [
                'rejectedBy',
            ]))
                ->whereHas('invoice')
                ->where('status', 'Rejected');
            
            $title = 'Rejected Payments Report';
        } else {
            // Default to pending
            $query = Payment::with($withRelations)
                ->whereHas('invoice')
                ->pending();
            
            $title = 'Pending Payments Report';
        }
        
        $paymentsQuery = $this->applyFilters($query, $filters, $status);
        
        // Apply sorting based on status like in listAll
        if ($status === 'approved') {
            $paymentsQuery->orderByDesc('approved_date');
        } elseif ($status === 'rejected') {
            $paymentsQuery->orderByDesc('rejected_date');
        }
        
        $payments = $paymentsQuery->orderByDesc('created_at')->get();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.payments.pdf_export', compact('payments', 'title', 'filters', 'status'));
        $pdf->setPaper('A4', 'landscape');
        
        $filename = Str::slug($title) . '_' . date('Y_m_d_H_i') . '.pdf';
        
        return $pdf->download($filename);
    }
}

