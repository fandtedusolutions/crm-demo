<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\ConvertedLead;
use App\Models\ConvertedStudentActivity;
use App\Models\Course;
use App\Models\Batch;
use App\Helpers\AuthHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices for a specific student
     */
    public function index(Request $request, $studentId)
    {
        $student = ConvertedLead::with(['course', 'lead', 'lead.team', 'leadDetail.university', 'batch', 'academicAssistant'])->findOrFail($studentId);
        
        // Check permissions
        $this->checkStudentAccess($student);
        
        $invoices = Invoice::with(['course', 'batch', 'payments'])
            ->where('student_id', $studentId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate summary (amounts after discount / net payable)
        $summary = [
            'total_invoices' => $invoices->count(),
            'total_amount' => $invoices->sum(fn ($inv) => $inv->net_amount),
            'total_paid' => $invoices->sum('paid_amount'),
            'total_pending' => $invoices->sum(fn ($inv) => $inv->pending_amount),
        ];

        return view('admin.invoices.index', compact('student', 'invoices', 'summary'));
    }

    /**
     * Display the specified invoice
     */
    public function show($id)
    {
        $invoice = Invoice::with(['course', 'batch', 'student.lead', 'payments' => function($query) {
            $query->with('createdBy')->orderBy('created_at', 'desc');
        }])
            ->findOrFail($id);
        
        // Check permissions
        $this->checkStudentAccess($invoice->student);

        // Find the first payment (oldest approved payment) for tax invoice
        $firstPayment = \App\Models\Payment::where('invoice_id', $id)
            ->where('status', 'Approved')
            ->orderBy('created_at', 'asc')
            ->first();

        return view('admin.invoices.show', compact('invoice', 'firstPayment'));
    }

    /**
     * Create a new invoice for a student
     */
    public function create($studentId)
    {
        $student = ConvertedLead::with(['course', 'batch', 'leadDetail.university', 'lead', 'academicAssistant'])
            ->findOrFail($studentId);

        $this->checkStudentAccess($student);

        $courses = Course::where('is_active', true)->orderBy('title')->get();

        $selectedCourseId = old('course_id') !== null && old('course_id') !== ''
            ? (int) old('course_id')
            : null;
        $selectedBatchId = old('batch_id') !== null && old('batch_id') !== ''
            ? (int) old('batch_id')
            : null;

        $courseFeeContext = $this->buildCourseFeePresentationContext(
            $student,
            $selectedCourseId,
            $selectedBatchId
        );

        $existingCourseInvoices = Invoice::with(['course:id,title', 'batch:id,title'])
            ->where('student_id', $studentId)
            ->where('invoice_type', 'course')
            ->whereNotNull('course_id')
            ->orderByDesc('created_at')
            ->get()
            ->mapWithKeys(fn (Invoice $invoice) => [
                $this->courseBatchInvoiceKey((int) $invoice->course_id, $invoice->batch_id ? (int) $invoice->batch_id : null) => [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'course_id' => (int) $invoice->course_id,
                    'batch_id' => $invoice->batch_id ? (int) $invoice->batch_id : null,
                    'course_title' => $invoice->course?->title,
                    'batch_title' => $invoice->batch?->title,
                    'show_url' => route('admin.invoices.show', $invoice->id),
                ],
            ]);

        return view('admin.invoices.create', compact(
            'student',
            'courses',
            'courseFeeContext',
            'existingCourseInvoices'
        ));
    }

    /**
     * Store a newly created invoice
     */
    public function store(Request $request, $studentId)
    {
        Log::info('[InvoiceController@store] Incoming invoice create', [
            'student_id' => $studentId,
            'payload' => $request->all()
        ]);

        // Normalize/override totals per type and guard fields
        if ($request->invoice_type === 'batch_change') {
            $request->merge(['total_amount' => 2000]);
        } elseif ($request->invoice_type === 'e-service' && $request->filled('service_amount')) {
            $request->merge(['total_amount' => $request->service_amount]);
        } elseif ($request->invoice_type === 'fine' && $request->filled('fine_amount')) {
            // Keep total amount in sync with the fine amount
            $request->merge(['total_amount' => $request->fine_amount]);
        } elseif ($request->invoice_type === 'course') {
            $student = ConvertedLead::with(['leadDetail', 'lead'])->findOrFail($studentId);
            $computed = $this->computeCourseInvoiceTotals(
                $student,
                (int) $request->course_id,
                $request->filled('batch_id') ? (int) $request->batch_id : null,
                [
                    'fee_pg_amount' => $request->input('fee_pg_amount'),
                    'fee_ug_amount' => $request->input('fee_ug_amount'),
                    'fee_plustwo_amount' => $request->input('fee_plustwo_amount'),
                    'fee_sslc_amount' => $request->input('fee_sslc_amount'),
                ],
                $request->filled('custom_total_amount') ? (float) $request->custom_total_amount : null
            );
            if (! $request->filled('total_amount')) {
                $request->merge(['total_amount' => $computed['total_amount']]);
            }
        }

        $validator = Validator::make($request->all(), [
            'invoice_type' => 'required|in:course,e-service,batch_change,fine',
            'course_id' => 'nullable|required_if:invoice_type,course|exists:courses,id',
            'batch_id' => [
                'nullable',
                function ($attribute, $value, $fail) use ($request, $studentId) {
                    if ($request->invoice_type !== 'course' || ! $request->course_id) {
                        return;
                    }
                    $courseId = (int) $request->course_id;
                    if ($courseId === 23) {
                        if ($value && ! $this->batchBelongsToCourse((int) $value, $courseId)) {
                            $fail('The selected batch does not belong to the selected course.');
                        }
                        $existing = $this->findExistingCourseBatchInvoice(
                            (int) $studentId,
                            $courseId,
                            $value ? (int) $value : null
                        );
                        if ($existing) {
                            $fail($this->existingCourseBatchInvoiceMessage($existing));
                        }

                        return;
                    }
                    if (empty($value)) {
                        $fail('The batch field is required for this course (except EduMaster).');

                        return;
                    }
                    if (! $this->batchBelongsToCourse((int) $value, $courseId)) {
                        $fail('The selected batch does not belong to the selected course.');
                    }
                    $existing = $this->findExistingCourseBatchInvoice((int) $studentId, $courseId, (int) $value);
                    if ($existing) {
                        $fail($this->existingCourseBatchInvoiceMessage($existing));
                    }
                },
                'exists:batches,id',
            ],
            'service_name' => 'nullable|required_if:invoice_type,e-service|string|max:255',
            'service_amount' => 'nullable|required_if:invoice_type,e-service|numeric|min:0',
            'fine_type' => 'nullable|required_if:invoice_type,fine|string|max:255',
            'fine_amount' => 'nullable|required_if:invoice_type,fine|numeric|min:0',
            'total_amount' => 'required|numeric|gt:0',
            'invoice_date' => 'required|date',
        ], [
            'total_amount.gt' => 'Total Amount must be greater than 0.',
        ]);

        if ($validator->fails()) {
            Log::warning('[InvoiceController@store] Validation failed', [
                'errors' => $validator->errors()->toArray()
            ]);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $student = ConvertedLead::with(['leadDetail', 'lead'])->findOrFail($studentId);

            $courseComputed = null;
            if ($request->invoice_type === 'course') {
                $courseComputed = $this->computeCourseInvoiceTotals(
                    $student,
                    (int) $request->course_id,
                    $request->filled('batch_id') ? (int) $request->batch_id : null,
                    [
                        'fee_pg_amount' => $request->input('fee_pg_amount'),
                        'fee_ug_amount' => $request->input('fee_ug_amount'),
                        'fee_plustwo_amount' => $request->input('fee_plustwo_amount'),
                        'fee_sslc_amount' => $request->input('fee_sslc_amount'),
                    ],
                    $request->filled('custom_total_amount') ? (float) $request->custom_total_amount : null
                );
            }

            $invoiceData = [
                'invoice_type' => $request->invoice_type,
                'student_id' => $studentId,
                'total_amount' => $request->total_amount,
                'invoice_date' => $request->invoice_date,
                'created_by' => AuthHelper::getCurrentUserId(),
                'updated_by' => AuthHelper::getCurrentUserId(),
            ];

            // Add type-specific fields
            if ($request->invoice_type === 'course' && $courseComputed) {
                $invoiceData['course_id'] = $request->course_id;
                $invoiceData['total_amount'] = $courseComputed['total_amount'];
                if ($request->filled('batch_id')) {
                    $invoiceData['batch_id'] = $request->batch_id;
                }
                if ((int) $request->course_id === 23) {
                    $invoiceData['fee_pg_amount'] = $courseComputed['fee_pg_amount'];
                    $invoiceData['fee_ug_amount'] = $courseComputed['fee_ug_amount'];
                    $invoiceData['fee_plustwo_amount'] = $courseComputed['fee_plustwo_amount'];
                    $invoiceData['fee_sslc_amount'] = $courseComputed['fee_sslc_amount'];
                }
            } elseif ($request->invoice_type === 'batch_change') {
                $invoiceData['batch_id'] = $request->batch_id;
                $invoiceData['total_amount'] = 2000; // Fixed amount for batch change
            } elseif ($request->invoice_type === 'e-service') {
                $invoiceData['service_name'] = $request->service_name;
                $invoiceData['service_amount'] = $request->service_amount;
            } elseif ($request->invoice_type === 'fine') {
                // Reuse service fields to store fine metadata without schema changes
                $invoiceData['service_name'] = $request->fine_type;
                $invoiceData['service_amount'] = $request->fine_amount;
                $invoiceData['total_amount'] = $request->fine_amount;
            }
            
            $invoice = $this->createInvoiceWithUniqueNumber($invoiceData);

            // Handle batch transfer for batch_change invoices
            if ($request->invoice_type === 'batch_change' && $request->batch_id) {
                $this->transferStudentBatch($studentId, $request->batch_id);
            }

            Log::info('[InvoiceController@store] Invoice created', [
                'invoice_id' => $invoice->id,
                'invoice_type' => $invoice->invoice_type
            ]);

            return redirect()->route('admin.invoices.show', $invoice->id)
                ->with('message_success', 'Invoice created successfully!');

        } catch (\Exception $e) {
            Log::error('[InvoiceController@store] Invoice create failed: ' . $e->getMessage(), [
                'payload' => $request->all()
            ]);
            return redirect()->back()
                ->with('message_danger', 'An error occurred while creating the invoice. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show modal to edit invoice amount
     */
    public function editAmount($invoiceId)
    {
        $invoice = Invoice::with(['student', 'batch', 'course', 'payments'])->findOrFail($invoiceId);
        $this->checkStudentAccess($invoice->student);
        $this->assertCanEditInvoiceAmount($invoice);

        $minTotal = $this->minimumEditableInvoiceTotal($invoice);

        return view('admin.invoices.edit-amount-modal', compact('invoice', 'minTotal'));
    }

    /**
     * Update invoice amount
     */
    public function updateAmount(Request $request, $invoiceId)
    {
        $invoice = Invoice::with(['student', 'payments'])->findOrFail($invoiceId);
        $this->checkStudentAccess($invoice->student);
        $this->assertCanEditInvoiceAmount($invoice);

        $minTotal = $this->minimumEditableInvoiceTotal($invoice);
        $request->validate([
            'total_amount' => 'required|numeric|min:' . $minTotal,
        ], [
            'total_amount.min' => 'Invoice amount cannot be less than the amount already paid'
                . ' (minimum ₹' . number_format($minTotal, 2) . ').',
        ]);

        $fromAmount = (float) $invoice->total_amount;
        $toAmount = (float) $request->total_amount;

        $invoice->total_amount = $toAmount;
        $invoice->updated_by = AuthHelper::getCurrentUserId();
        $invoice->save();
        $invoice->recalculatePaidAmount();
        $invoice->updateStatus();

        if ($fromAmount !== $toAmount) {
            ConvertedStudentActivity::create([
                'converted_lead_id' => $invoice->student_id,
                'activity_type' => 'invoice_amount_update',
                'description' => sprintf(
                    'Invoice %s amount updated from ₹%s to ₹%s (status: %s).',
                    $invoice->invoice_number,
                    number_format($fromAmount, 2),
                    number_format($toAmount, 2),
                    $invoice->status
                ),
                'activity_date' => now()->toDateString(),
                'activity_time' => now()->format('H:i:s'),
                'created_by' => AuthHelper::getCurrentUserId(),
                'updated_by' => AuthHelper::getCurrentUserId(),
            ]);
        }

        return redirect()
            ->route('admin.invoices.index', $invoice->student_id)
            ->with('message_success', 'Invoice amount updated successfully.');
    }

    /**
     * Modal: set invoice discount (finance / admin)
     */
    public function editDiscount($invoiceId)
    {
        $invoice = Invoice::with(['student', 'batch', 'course'])->findOrFail($invoiceId);
        $this->checkStudentAccess($invoice->student);

        return view('admin.invoices.edit-discount-modal', compact('invoice'));
    }

    /**
     * Persist discount_amount (cannot reduce net below amount already paid)
     */
    public function updateDiscount(Request $request, $invoiceId)
    {
        $invoice = Invoice::with('student')->findOrFail($invoiceId);
        $this->checkStudentAccess($invoice->student);

        $maxDiscount = max(0, (float) $invoice->total_amount - (float) $invoice->paid_amount);

        $request->validate([
            'discount_amount' => 'required|numeric|min:0|max:' . $maxDiscount,
        ], [
            'discount_amount.max' => 'Discount cannot exceed ₹' . number_format($maxDiscount, 2) . ' (gross total minus paid amount).',
        ]);

        $invoice->discount_amount = $request->discount_amount;
        $invoice->updated_by = AuthHelper::getCurrentUserId();
        $invoice->save();
        $invoice->recalculatePaidAmount();
        $invoice->updateStatus();

        return redirect()
            ->back()
            ->with('message_success', 'Invoice discount updated successfully.');
    }

    /**
     * Auto-generate invoice when converting a lead
     */
    public function autoGenerate($studentId, $courseId, $customTotalAmount = null, $feeBreakdown = null, $createdByUserId = null)
    {
        try {
            $student = ConvertedLead::findOrFail($studentId);
            $course = Course::findOrFail($courseId);
            $actorId = $createdByUserId ?? AuthHelper::getCurrentUserId();
            $student->loadMissing('lead');
            $isB2bForFees = (int) (optional($student->lead)->is_b2b ?? $student->is_b2b ?? 0) === 1;

            // Check if invoice already exists for this student and course
            $existingInvoice = Invoice::where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->first();
                
            if ($existingInvoice) {
                return $existingInvoice;
            }
            
            // Get batch_id
            $batchId = $student->batch_id ?? optional($student->leadDetail)->batch_id;
            
            $feePgAmount = null;
            $feeUgAmount = null;
            $feePlustwoAmount = null;
            $feeSslcAmount = null;

            // For course_id 23, store fee breakdown and total (custom or derived)
            if ($courseId == 23) {
                if (is_array($feeBreakdown)) {
                    $feePgAmount = isset($feeBreakdown['fee_pg_amount']) ? (float) $feeBreakdown['fee_pg_amount'] : null;
                    $feeUgAmount = isset($feeBreakdown['fee_ug_amount']) ? (float) $feeBreakdown['fee_ug_amount'] : null;
                    $feePlustwoAmount = isset($feeBreakdown['fee_plustwo_amount']) ? (float) $feeBreakdown['fee_plustwo_amount'] : null;
                    $feeSslcAmount = isset($feeBreakdown['fee_sslc_amount']) ? (float) $feeBreakdown['fee_sslc_amount'] : null;
                }

                if ($customTotalAmount !== null) {
                    $totalAmount = (float) $customTotalAmount;
                } else {
                    $totalAmount = (float) (($feePgAmount ?? 0) + ($feeUgAmount ?? 0) + ($feePlustwoAmount ?? 0) + ($feeSslcAmount ?? 0));
                }
            } else {
                // Calculate total amount
                $batchAmount = 0.0;

                // Determine batch and add batch amount if available
                if ($batchId) {
                    $batch = Batch::find($batchId);
                    if ($batch) {
                        // B2B student: use only batch B2B amount (do not use in-house amount)
                        if ($isB2bForFees) {
                            $batchAmount = $batch->b2b_amount !== null ? (float) $batch->b2b_amount : 0.0;
                        } else {
                            if ($courseId == 16) {
                                $studentClass = optional($student->leadDetail)->class;
                                $normalizedClass = $studentClass ? strtolower($studentClass) : null;

                                if ($normalizedClass === 'sslc' && !is_null($batch->sslc_amount)) {
                                    $batchAmount = (float) $batch->sslc_amount;
                                } elseif (!is_null($batch->plustwo_amount)) {
                                    $batchAmount = (float) $batch->plustwo_amount;
                                } elseif ($batch->amount) {
                                    $batchAmount = (float) $batch->amount;
                                }
                            } elseif ($batch->amount) {
                                $batchAmount = (float) $batch->amount;
                            }
                        }
                    }
                }

                // B2B: total is only the batch B2B amount (no course amount)
                if ($isB2bForFees) {
                    $totalAmount = $batchAmount;
                } else {
                    $totalAmount = (float) ($course->amount ?? 0) + $batchAmount;
                    // Add university amount for UG/PG course (course_id = 9)
                    if ($courseId == 9 && $student->leadDetail) {
                        $courseType = $student->leadDetail->course_type;
                        $universityId = $student->leadDetail->university_id;

                        if ($universityId && $courseType) {
                            $university = \App\Models\University::find($universityId);
                            if ($university) {
                                if ($courseType === 'UG') {
                                    $totalAmount += $university->ug_amount ?? 0;
                                } elseif ($courseType === 'PG') {
                                    $totalAmount += $university->pg_amount ?? 0;
                                }
                            }
                        }
                    }
                }
            }
            
            $invoice = $this->createInvoiceWithUniqueNumber([
                'invoice_type' => 'course',
                'course_id' => $courseId,
                'batch_id' => $batchId,
                'student_id' => $studentId,
                'total_amount' => $totalAmount,
                'fee_pg_amount' => $feePgAmount,
                'fee_ug_amount' => $feeUgAmount,
                'fee_plustwo_amount' => $feePlustwoAmount,
                'fee_sslc_amount' => $feeSslcAmount,
                'invoice_date' => now()->toDateString(),
                'created_by' => $actorId,
                'updated_by' => $actorId,
            ]);

            return $invoice;

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Create an invoice, retrying when the generated number collides (e.g. concurrent requests).
     */
    private function createInvoiceWithUniqueNumber(array $invoiceData): Invoice
    {
        $maxAttempts = 5;
        $lastException = null;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $invoiceData['invoice_number'] = Invoice::generateNextInvoiceNumber();

            try {
                return Invoice::create($invoiceData);
            } catch (QueryException $e) {
                if (! $this->isDuplicateInvoiceNumberException($e)) {
                    throw $e;
                }
                $lastException = $e;
            }
        }

        throw $lastException ?? new \RuntimeException('Unable to generate a unique invoice number.');
    }

    private function isDuplicateInvoiceNumberException(QueryException $e): bool
    {
        $errorCode = (int) ($e->errorInfo[1] ?? 0);

        return $errorCode === 1062
            && str_contains($e->getMessage(), 'invoices_invoice_number_unique');
    }

    /**
     * Check if user has access to the student
     */
    private function checkStudentAccess($student)
    {
        $currentUser = AuthHelper::getCurrentUser();
        if (!$currentUser) {
            abort(403, 'Access denied.');
        }

        $currentUserId = $currentUser->id;
        $currentUserRole = AuthHelper::getCurrentUserRole();

        // Senior Manager: Can access all students
        if (\App\Helpers\RoleHelper::is_senior_manager()) {
            return;
        }

        // General Manager: Can access all students
        if (\App\Helpers\RoleHelper::is_general_manager()) {
            return;
        }

        switch ($currentUserRole) {
            case 1: // Super Admin
            case 2: // Admin
            case 11: // General Manager
            case 4: // Admission Counsellor
            case 6: // Finance
            case 7: // Post Sales
                // Can access all students
                break;

            case 3: // Telecaller (including team lead)
                $lead = optional($student->lead);
                if (!$lead) {
                    abort(403, 'Access denied.');
                }

                if (\App\Helpers\RoleHelper::is_team_lead()) {
                    $teamMemberIds = [];
                    if ($currentUser->team_id) {
                        $teamMemberIds = AuthHelper::getTeamMemberIds($currentUser->team_id);
                    }
                    $teamMemberIds[] = $currentUser->id;
                    $teamMemberIds = array_unique(array_filter($teamMemberIds));

                    if (!in_array((int) $lead->telecaller_id, $teamMemberIds, true)) {
                        abort(403, 'Access denied. You can only view students from your team.');
                    }
                } elseif ((int) $lead->telecaller_id !== (int) $currentUserId) {
                    abort(403, 'Access denied. You can only view students assigned to you.');
                }

                if ((int) ($currentUser->is_b2b ?? 0) === 1) {
                    $leadTeamIsB2B = (int) (optional($lead->team)->is_b2b ?? 0) === 1;
                    $leadIsB2B = (int) ($lead->is_b2b ?? 0) === 1;
                    if (!($leadIsB2B || $leadTeamIsB2B)) {
                        abort(403, 'Access denied.');
                    }
                }
                break;

            case 5: // Academic Assistant
                // Can only access students assigned to them
                if ($student->academic_assistant_id != $currentUserId) {
                    abort(403, 'Access denied. You can only view students assigned to you.');
                }
                break;

            default:
                abort(403, 'Access denied.');
        }
    }

    /**
     * Transfer student to a new batch
     */
    private function transferStudentBatch($studentId, $batchId)
    {
        try {
            DB::beginTransaction();

            // Update converted_leads table
            $convertedLead = ConvertedLead::findOrFail($studentId);
            $convertedLead->update(['batch_id' => $batchId]);

            // Update lead_details table via the lead relationship
            if ($convertedLead->lead && $convertedLead->lead->studentDetails) {
                $convertedLead->lead->studentDetails->update(['batch_id' => $batchId]);
            }

            DB::commit();
            
            Log::info('Student batch transferred successfully', [
                'student_id' => $studentId,
                'new_batch_id' => $batchId
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to transfer student batch: ' . $e->getMessage(), [
                'student_id' => $studentId,
                'batch_id' => $batchId
            ]);
            throw $e;
        }
    }

    /**
     * Presentation context for course invoice UI (aligned with lead convert modal).
     *
     * @return array<string, mixed>
     */
    private function buildCourseFeePresentationContext(ConvertedLead $student, ?int $courseId, ?int $batchId): array
    {
        $leadDetail = $student->leadDetail;
        $studentClass = $leadDetail?->class;
        $courseType = $leadDetail?->course_type;
        $university = $leadDetail?->university;
        $isB2b = $this->isB2bStudent($student);

        if (! $courseId) {
            return [
                'course' => null,
                'batch' => null,
                'batches' => collect(),
                'courseAmount' => 0.0,
                'batchAmount' => 0.0,
                'universityAmount' => 0.0,
                'totalAmount' => 0.0,
                'studentClass' => $studentClass,
                'courseType' => $courseType,
                'university' => $university,
                'batchAmountLabel' => null,
                'isB2b' => $isB2b,
                'useB2bBatchAmount' => false,
                'usePlanLabelsForBatch' => false,
                'isEdumasterCourse' => false,
            ];
        }

        $course = Course::find($courseId);
        $batch = $batchId ? Batch::find($batchId) : null;

        $batches = $courseId
            ? Batch::where('course_id', $courseId)
                ->select('id', 'title', 'amount', 'sslc_amount', 'plustwo_amount', 'b2b_amount', 'is_active')
                ->orderBy('is_active', 'desc')
                ->orderBy('title')
                ->get()
            : collect();

        $computed = $this->computeCourseInvoiceTotals($student, $courseId, $batchId);

        $batchAmountLabel = null;
        if ($batch && $courseId === 25) {
            $batchAmountLabel = $batch->b2b_amount !== null ? 'B2B Amount' : 'B2B Amount (not set)';
        } elseif ($batch && $courseId === 16 && $studentClass) {
            $normalizedClass = strtolower($studentClass);
            if ($normalizedClass === 'sslc' && $batch->sslc_amount !== null) {
                $batchAmountLabel = 'SSLC Amount';
            } elseif ($batch->plustwo_amount !== null) {
                $batchAmountLabel = 'Plus Two Amount';
            }
        }

        return [
            'course' => $course,
            'batch' => $batch,
            'batches' => $batches,
            'courseAmount' => $computed['course_amount'],
            'batchAmount' => $computed['batch_amount'],
            'universityAmount' => $computed['university_amount'],
            'totalAmount' => $computed['total_amount'],
            'studentClass' => $studentClass,
            'courseType' => $courseType,
            'university' => $university,
            'batchAmountLabel' => $batchAmountLabel,
            'isB2b' => $this->isB2bStudent($student),
            'useB2bBatchAmount' => $courseId === 25,
            'usePlanLabelsForBatch' => $courseId === 25,
            'isEdumasterCourse' => $courseId === 23,
        ];
    }

    private function isB2bStudent(ConvertedLead $student): bool
    {
        $student->loadMissing('lead');

        return (int) ($student->is_b2b ?? 0) === 1
            || (int) (optional($student->lead)->is_b2b ?? 0) === 1;
    }

    /**
     * CreateX AI (course 25) uses plan b2b_amount only — not the regular amount column.
     */
    private function shouldUseB2bBatchAmount(int $courseId, ConvertedLead $student): bool
    {
        return $courseId === 25;
    }

    /**
     * @param  array<string, mixed>|null  $feeBreakdown
     * @return array{total_amount: float, course_amount: float, batch_amount: float, university_amount: float, fee_pg_amount: ?float, fee_ug_amount: ?float, fee_plustwo_amount: ?float, fee_sslc_amount: ?float}
     */
    private function computeCourseInvoiceTotals(
        ConvertedLead $student,
        int $courseId,
        ?int $batchId = null,
        ?array $feeBreakdown = null,
        ?float $customTotalAmount = null
    ): array {
        $student->loadMissing(['leadDetail', 'lead']);

        if ($courseId === 23) {
            $feePg = isset($feeBreakdown['fee_pg_amount']) && $feeBreakdown['fee_pg_amount'] !== ''
                ? (float) $feeBreakdown['fee_pg_amount'] : null;
            $feeUg = isset($feeBreakdown['fee_ug_amount']) && $feeBreakdown['fee_ug_amount'] !== ''
                ? (float) $feeBreakdown['fee_ug_amount'] : null;
            $feePlustwo = isset($feeBreakdown['fee_plustwo_amount']) && $feeBreakdown['fee_plustwo_amount'] !== ''
                ? (float) $feeBreakdown['fee_plustwo_amount'] : null;
            $feeSslc = isset($feeBreakdown['fee_sslc_amount']) && $feeBreakdown['fee_sslc_amount'] !== ''
                ? (float) $feeBreakdown['fee_sslc_amount'] : null;

            $totalAmount = $customTotalAmount !== null
                ? (float) $customTotalAmount
                : (float) (($feePg ?? 0) + ($feeUg ?? 0) + ($feePlustwo ?? 0) + ($feeSslc ?? 0));

            return [
                'total_amount' => $totalAmount,
                'course_amount' => 0.0,
                'batch_amount' => 0.0,
                'university_amount' => 0.0,
                'fee_pg_amount' => $feePg,
                'fee_ug_amount' => $feeUg,
                'fee_plustwo_amount' => $feePlustwo,
                'fee_sslc_amount' => $feeSslc,
            ];
        }

        $course = Course::find($courseId);
        $batch = $batchId ? Batch::find($batchId) : null;
        $leadDetail = $student->leadDetail;

        $courseAmount = $course ? (float) ($course->amount ?? 0) : 0.0;
        $batchAmount = 0.0;
        $universityAmount = 0.0;
        $useB2bBatchAmount = $this->shouldUseB2bBatchAmount($courseId, $student);

        if ($batch) {
            if ($useB2bBatchAmount) {
                $batchAmount = (float) ($batch->b2b_amount ?? 0);
            } elseif ($this->isB2bStudent($student)) {
                $batchAmount = $batch->b2b_amount !== null ? (float) $batch->b2b_amount : 0.0;
            } elseif ($courseId === 16 && $leadDetail) {
                $studentClass = strtolower($leadDetail->class ?? '');
                if ($studentClass === 'sslc' && $batch->sslc_amount !== null) {
                    $batchAmount = (float) $batch->sslc_amount;
                } elseif ($batch->plustwo_amount !== null) {
                    $batchAmount = (float) $batch->plustwo_amount;
                } else {
                    $batchAmount = (float) ($batch->amount ?? 0);
                }
            } else {
                $batchAmount = (float) ($batch->amount ?? 0);
            }
        }

        if ($courseId === 9 && $leadDetail) {
            $universityId = $leadDetail->university_id;
            if ($universityId) {
                $university = \App\Models\University::find($universityId);
                if ($university) {
                    if ($leadDetail->course_type === 'UG') {
                        $universityAmount = (float) ($university->ug_amount ?? 0);
                    } elseif ($leadDetail->course_type === 'PG') {
                        $universityAmount = (float) ($university->pg_amount ?? 0);
                    }
                }
            }
        }

        if ($useB2bBatchAmount || $this->isB2bStudent($student)) {
            $totalAmount = $batchAmount;
            $courseAmount = 0.0;
            $universityAmount = 0.0;
        } else {
            $totalAmount = $courseAmount + $batchAmount + $universityAmount;
        }

        if ($customTotalAmount !== null) {
            $totalAmount = (float) $customTotalAmount;
        }

        return [
            'total_amount' => $totalAmount,
            'course_amount' => $courseAmount,
            'batch_amount' => $batchAmount,
            'university_amount' => $universityAmount,
            'fee_pg_amount' => null,
            'fee_ug_amount' => null,
            'fee_plustwo_amount' => null,
            'fee_sslc_amount' => null,
        ];
    }

    /**
     * Calculate total amount for invoice (API endpoint)
     */
    public function calculateAmount(Request $request, $studentId)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'batch_id' => 'nullable|exists:batches,id',
            'custom_total_amount' => 'nullable|numeric|min:0',
            'fee_pg_amount' => 'nullable|numeric|min:0',
            'fee_ug_amount' => 'nullable|numeric|min:0',
            'fee_plustwo_amount' => 'nullable|numeric|min:0',
            'fee_sslc_amount' => 'nullable|numeric|min:0',
        ]);

        $courseId = (int) $request->course_id;
        $batchId = $request->filled('batch_id') ? (int) $request->batch_id : null;
        $batchError = $this->resolveCourseBatchValidationError($courseId, $batchId);
        if ($batchError) {
            return response()->json([
                'success' => false,
                'message' => $batchError,
                'batch_error' => $batchError,
                'existing_invoice' => null,
                'can_submit' => false,
            ], 422);
        }

        $existingInvoice = $this->findExistingCourseBatchInvoice((int) $studentId, $courseId, $batchId);

        if ($existingInvoice) {
            return response()->json([
                'success' => false,
                'message' => $this->existingCourseBatchInvoiceMessage($existingInvoice),
                'batch_error' => null,
                'existing_invoice' => $this->formatExistingCourseInvoicePayload($existingInvoice),
                'can_submit' => false,
            ], 422);
        }

        $student = ConvertedLead::with(['leadDetail', 'lead'])->findOrFail($studentId);
        $computed = $this->computeCourseInvoiceTotals(
            $student,
            (int) $request->course_id,
            $request->filled('batch_id') ? (int) $request->batch_id : null,
            [
                'fee_pg_amount' => $request->input('fee_pg_amount'),
                'fee_ug_amount' => $request->input('fee_ug_amount'),
                'fee_plustwo_amount' => $request->input('fee_plustwo_amount'),
                'fee_sslc_amount' => $request->input('fee_sslc_amount'),
            ],
            $request->filled('custom_total_amount') ? (float) $request->custom_total_amount : null
        );

        $course = Course::find((int) $request->course_id);
        $useB2bBatchAmount = $this->shouldUseB2bBatchAmount($courseId, $student);

        return response()->json([
            'success' => true,
            'total_amount' => $computed['total_amount'],
            'course_amount' => $computed['course_amount'],
            'batch_amount' => $computed['batch_amount'],
            'university_amount' => $computed['university_amount'],
            'course_title' => $course?->title,
            'is_b2b' => $this->isB2bStudent($student),
            'use_b2b_batch_amount' => $useB2bBatchAmount,
            'batch_amount_label' => $useB2bBatchAmount ? 'B2B Amount' : null,
            'fee_pg_amount' => $computed['fee_pg_amount'],
            'fee_ug_amount' => $computed['fee_ug_amount'],
            'fee_plustwo_amount' => $computed['fee_plustwo_amount'],
            'fee_sslc_amount' => $computed['fee_sslc_amount'],
            'existing_invoice' => null,
            'batch_error' => null,
            'can_submit' => true,
        ]);
    }

    private function courseBatchInvoiceKey(int $courseId, ?int $batchId): string
    {
        return $courseId . ':' . ($batchId ?? 'none');
    }

    private function findExistingCourseBatchInvoice(int $studentId, int $courseId, ?int $batchId): ?Invoice
    {
        $query = Invoice::with(['course:id,title', 'batch:id,title'])
            ->where('student_id', $studentId)
            ->where('invoice_type', 'course')
            ->where('course_id', $courseId);

        if ($batchId) {
            $query->where('batch_id', $batchId);
        } else {
            $query->whereNull('batch_id');
        }

        return $query->first();
    }

    private function existingCourseBatchInvoiceMessage(Invoice $invoice): string
    {
        $invoice->loadMissing(['course', 'batch']);
        $courseTitle = $invoice->course?->title ?? 'selected course';
        $batchTitle = $invoice->batch?->title;

        if ($batchTitle) {
            return 'An invoice already exists for ' . $courseTitle . ' — ' . $batchTitle
                . ' (Invoice #' . $invoice->invoice_number . ').';
        }

        return 'An invoice already exists for ' . $courseTitle
            . ' (Invoice #' . $invoice->invoice_number . ').';
    }

    private function batchBelongsToCourse(int $batchId, int $courseId): bool
    {
        return Batch::where('id', $batchId)->where('course_id', $courseId)->exists();
    }

    private function resolveCourseBatchValidationError(int $courseId, ?int $batchId): ?string
    {
        if ($courseId === 23) {
            if ($batchId && ! $this->batchBelongsToCourse($batchId, $courseId)) {
                return 'The selected batch does not belong to the selected course.';
            }

            return null;
        }

        if ($courseId === 25) {
            if (! $batchId) {
                return 'Please select a plan for CreateX AI.';
            }
            if (! $this->batchBelongsToCourse($batchId, $courseId)) {
                return 'The selected plan does not belong to CreateX AI.';
            }
            return null;
        }

        if (! $batchId) {
            return 'Please select a batch for this course.';
        }

        if (! $this->batchBelongsToCourse($batchId, $courseId)) {
            return 'The selected batch does not belong to the selected course.';
        }

        return null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function formatExistingCourseInvoicePayload(?Invoice $invoice): ?array
    {
        if (! $invoice) {
            return null;
        }

        $invoice->loadMissing(['course', 'batch']);

        return [
            'id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'course_id' => (int) $invoice->course_id,
            'batch_id' => $invoice->batch_id ? (int) $invoice->batch_id : null,
            'course_title' => $invoice->course?->title,
            'batch_title' => $invoice->batch?->title,
            'show_url' => route('admin.invoices.show', $invoice->id),
        ];
    }

    /**
     * Minimum gross total when editing (net payable must stay >= paid amount).
     */
    private function minimumEditableInvoiceTotal(Invoice $invoice): float
    {
        return (float) $invoice->paid_amount + (float) ($invoice->discount_amount ?? 0);
    }

    /**
     * Super admin or finance after payments; admin/finance only before approved payments.
     */
    private function assertCanEditInvoiceAmount(Invoice $invoice): void
    {
        $paid = (float) $invoice->paid_amount;

        if ($paid > 0) {
            if (! \App\Helpers\RoleHelper::is_super_admin()
                && ! \App\Helpers\RoleHelper::is_finance()) {
                abort(403, 'Only super admin or finance can edit invoice amount after payments have been recorded.');
            }

            return;
        }

        if (! \App\Helpers\RoleHelper::is_admin_or_super_admin()
            && ! \App\Helpers\RoleHelper::is_finance()) {
            abort(403, 'Unauthorized to edit invoice amount.');
        }

        $hasApprovedPayments = $invoice->relationLoaded('payments')
            ? $invoice->payments->where('status', 'Approved')->isNotEmpty()
            : $invoice->payments()->where('status', 'Approved')->exists();

        if ($hasApprovedPayments) {
            abort(403, 'Invoice amount cannot be edited after approved payments.');
        }
    }
}
