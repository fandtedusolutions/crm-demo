@extends('layouts.mantis')

@section('title', 'Invoice Details - ' . $invoice->invoice_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Invoice Details - {{ $invoice->invoice_number }}</h4>
                        <div>
                            @php $canFinanceInvoice = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_finance(); @endphp
                            @if($canFinanceInvoice)
                            <button type="button" class="btn btn-outline-success" title="Discount"
                                onclick="show_small_modal('{{ route('admin.invoices.edit-discount', $invoice->id) }}', 'Invoice discount')">
                                <i class="fas fa-percent"></i> Discount
                            </button>
                            @endif
                            <a href="{{ route('admin.payments.create', $invoice->id) }}" class="btn btn-success">
                                <i class="fas fa-plus"></i> Add Payment
                            </a>
                            <a href="{{ route('admin.payments.index', $invoice->id) }}" class="btn btn-primary">
                                <i class="fas fa-credit-card"></i> View Payments
                            </a> 
                            <a href="{{ route('admin.invoices.index', $invoice->student_id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Invoices
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Invoice Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Invoice Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Invoice Number:</strong></td>
                                    <td>{{ $invoice->invoice_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Type:</strong></td>
                                    <td>
                                        @if($invoice->invoice_type == 'course')
                                            <span class="badge bg-primary">Course</span>
                                        @elseif($invoice->invoice_type == 'e-service')
                                            <span class="badge bg-info">E-Service</span>
                                        @elseif($invoice->invoice_type == 'batch_change')
                                            <span class="badge bg-warning">Batch Change</span>
                                        @elseif($invoice->invoice_type == 'batch_postpond')
                                            <span class="badge bg-warning text-dark">Batch Postponed</span>
                                        @elseif($invoice->invoice_type == 'fine')
                                            <span class="badge bg-danger">Fine</span>
                                        @elseif($invoice->invoice_type == 'supply')
                                            <span class="badge bg-secondary">Supply</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Invoice Date:</strong></td>
                                    <td>{{ $invoice->invoice_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($invoice->status == 'Not Paid')
                                            <span class="badge bg-danger">Not Paid</span>
                                        @elseif($invoice->status == 'Partially Paid')
                                            <span class="badge bg-warning">Partially Paid</span>
                                        @else
                                            <span class="badge bg-success">Fully Paid</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Total (gross):</strong></td>
                                    <td>₹{{ number_format(round($invoice->total_amount)) }}</td>
                                </tr>
                                @if((float) ($invoice->discount_amount ?? 0) > 0)
                                <tr>
                                    <td><strong>Discount:</strong></td>
                                    <td class="text-success">− ₹{{ number_format(round($invoice->discount_amount)) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td><strong>Net payable:</strong></td>
                                    <td>₹{{ number_format(round($invoice->net_amount)) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Paid Amount:</strong></td>
                                    <td>₹{{ number_format(round($invoice->paid_amount)) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Pending Amount:</strong></td>
                                    <td>₹{{ number_format(round($invoice->pending_amount)) }}</td>
                                </tr>
                            </table>

                            @if($invoice->invoice_type === 'course' && (int) ($invoice->course_id ?? 0) === 23)
                                @php
                                    $feeBreakdown = [
                                        'PG' => (float) ($invoice->fee_pg_amount ?? 0),
                                        'UG' => (float) ($invoice->fee_ug_amount ?? 0),
                                        'PLUS_TWO' => (float) ($invoice->fee_plustwo_amount ?? 0),
                                        'SSLC' => (float) ($invoice->fee_sslc_amount ?? 0),
                                    ];
                                    $feeHeadLabels = [
                                        'PG' => 'PG',
                                        'UG' => 'UG',
                                        'PLUS_TWO' => 'Plus Two',
                                        'SSLC' => 'SSLC',
                                    ];
                                @endphp

                                <h6 class="mt-3">EduMaster Fee Breakdown</h6>
                                <table class="table table-sm table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th>Fee Head</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $feeHeadLabels['PG'] }}</td>
                                            <td class="text-end">₹{{ number_format(round($feeBreakdown['PG'])) }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ $feeHeadLabels['UG'] }}</td>
                                            <td class="text-end">₹{{ number_format(round($feeBreakdown['UG'])) }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ $feeHeadLabels['PLUS_TWO'] }}</td>
                                            <td class="text-end">₹{{ number_format(round($feeBreakdown['PLUS_TWO'])) }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ $feeHeadLabels['SSLC'] }}</td>
                                            <td class="text-end">₹{{ number_format(round($feeBreakdown['SSLC'])) }}</td>
                                        </tr>
                                        <tr class="table-light">
                                            <td><strong>Total</strong></td>
                                            <td class="text-end"><strong>₹{{ number_format(round($invoice->total_amount)) }}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6>Student Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $invoice->student->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $invoice->student->code }} {{ $invoice->student->phone }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $invoice->student->email ?? 'N/A' }}</td>
                                </tr>
                                @if($invoice->invoice_type == 'course' && $invoice->student->need_mobile)
                                <tr>
                                    <td><strong>Needed Mobile:</strong></td>
                                    <td>
                                        Yes — <strong>₹{{ number_format(\App\Models\Invoice::NEED_MOBILE_ADDON_GROSS, 2) }}</strong>
                                        @if($invoice->student->asset_id)
                                            <br><strong>Asset ID:</strong> {{ $invoice->student->asset_id }}
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                @if($invoice->invoice_type == 'course')
                                <tr>
                                    <td><strong>Course:</strong></td>
                                    <td>
                                        @if($invoice->course_id == 9 && $invoice->student->leadDetail)
                                            @php
                                                $studentDetail = $invoice->student->leadDetail;
                                                $university = $studentDetail->university;
                                                $courseType = $studentDetail->course_type;
                                            @endphp
                                            @if($university && $courseType)
                                                {{ $university->title }} - {{ $courseType }}
                                            @else
                                                {{ $invoice->course->title }}
                                            @endif
                                        @else
                                            {{ $invoice->course->title }}
                                        @endif
                                    </td>
                                </tr>
                                @elseif($invoice->invoice_type == 'e-service')
                                <tr>
                                    <td><strong>Service:</strong></td>
                                    <td>{{ $invoice->service_name }}</td>
                                </tr>
                                @elseif($invoice->invoice_type == 'batch_change' || $invoice->invoice_type == 'batch_postpond')
                                <tr>
                                    <td><strong>{{ $invoice->invoice_type == 'batch_postpond' ? 'Postponed Batch:' : 'New Batch:' }}</strong></td>
                                    <td>{{ $invoice->batch->title ?? 'N/A' }} ({{ $invoice->batch->course->title ?? 'N/A' }})</td>
                                </tr>
                                @elseif($invoice->invoice_type == 'fine')
                                <tr>
                                    <td><strong>Fine Type:</strong></td>
                                    <td>{{ $invoice->service_name ?? 'N/A' }}</td>
                                </tr>
                                @elseif($invoice->invoice_type == 'supply')
                                <tr>
                                    <td><strong>Supply Amount:</strong></td>
                                    <td>₹{{ number_format(round($invoice->service_amount ?? $invoice->total_amount)) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td><strong>Batch:</strong></td>
                                    <td>
                                        {{ $invoice->student->batch->title ?? 'N/A' }}
                                        @if($invoice->invoice_type == 'course' && $invoice->course_id == 16 && $invoice->student->leadDetail)
                                            @php
                                                $class = $invoice->student->leadDetail->class;
                                            @endphp
                                            @if($class)
                                                <span class="badge bg-primary ms-1">{{ strtoupper($class) }}</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Academic Assistant:</strong></td>
                                    <td>{{ $invoice->student->academicAssistant->name ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Payments History -->
                    <div class="row">
                        <div class="col-12">
                            <h6>Payments History</h6>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Payment Date</th>
                                            <th>Amount</th>
                                            <th>Fee Head</th>
                                            <th>Fee Amount</th>
                                            <th>Payment Type</th>
                                            <th>Transaction ID</th>
                                            <th>Status</th>
                                            <th>Created By</th>
                                            <th>File</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($invoice->payments as $index => $payment)
                                        @php
                                            $feeHeadLabels = [
                                                'PG' => 'PG',
                                                'UG' => 'UG',
                                                'PLUS_TWO' => 'Plus Two',
                                                'SSLC' => 'SSLC',
                                            ];
                                            $feeHeadToAmount = [
                                                'PG' => (float) ($invoice->fee_pg_amount ?? 0),
                                                'UG' => (float) ($invoice->fee_ug_amount ?? 0),
                                                'PLUS_TWO' => (float) ($invoice->fee_plustwo_amount ?? 0),
                                                'SSLC' => (float) ($invoice->fee_sslc_amount ?? 0),
                                            ];
                                            $paymentFeeHead = $payment->fee_head;
                                            $paymentFeeAmount = ($invoice->invoice_type === 'course' && (int) ($invoice->course_id ?? 0) === 23 && $paymentFeeHead && isset($feeHeadToAmount[$paymentFeeHead]))
                                                ? $feeHeadToAmount[$paymentFeeHead]
                                                : null;
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                @if($payment->payment_date)
                                                    {{ $payment->payment_date->format('M d, Y') }}
                                                @else
                                                    {{ $payment->created_at->format('M d, Y') }}
                                                @endif
                                                <br><small class="text-muted">{{ $payment->created_at->format('h:i A') }}</small>
                                            </td>
                                            <td>₹{{ number_format(round($payment->amount_paid)) }}</td>
                                            <td>
                                                @if($payment->fee_head)
                                                    <span class="badge bg-primary">{{ $feeHeadLabels[$payment->fee_head] ?? $payment->fee_head }}</span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(!is_null($paymentFeeAmount))
                                                    ₹{{ number_format(round($paymentFeeAmount)) }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>{{ $payment->payment_type }}</td>
                                            <td>{{ $payment->transaction_id ?? 'N/A' }}</td>
                                            <td>
                                                @if($payment->status == 'Pending Approval')
                                                    <span class="badge bg-warning">Pending Approval</span>
                                                @elseif($payment->status == 'Approved')
                                                    <span class="badge bg-success">Approved</span>
                                                @else
                                                    <span class="badge bg-danger">Rejected</span>
                                                @endif
                                            </td>
                                            <td>{{ $payment->createdBy->name }}</td>
                                            <td>
                                                @if($payment->file_upload)
                                                    <div class="btn-group btn-group-sm" role="group" aria-label="Receipt/Proof">
                                                        <a href="{{ route('admin.payments.download', $payment->id) }}" class="btn btn-outline-primary" title="Download Receipt/Proof">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                        <a href="{{ route('admin.payments.view', $payment->id) }}" class="btn btn-primary" title="View Receipt/Proof" target="_blank">
                                                            <i class="fas fa-file-alt"></i>
                                                        </a>
                                                    </div>
                                                @else
                                                    <span class="text-muted">No file</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-1 justify-content-start">
                                                    <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-sm btn-info" title="View Payment">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($payment->status == 'Approved')
                                                        @if($invoice->invoice_type === 'course' && $firstPayment && $payment->id == $firstPayment->id)
                                                            <!-- Tax Invoice only for course invoices, first approved payment -->
                                                            <a href="{{ route('admin.payments.tax-invoice', $payment->id) }}" class="btn btn-sm btn-warning" title="Tax Invoice" target="_blank">
                                                                <i class="fas fa-file-invoice"></i>
                                                            </a>
                                                            <a href="{{ route('admin.payments.tax-invoice-pdf', $payment->id) }}" class="btn btn-sm btn-danger" title="View PDF" target="_blank">
                                                                <i class="fas fa-file-pdf"></i>
                                                            </a>
                                                        @else
                                                            <!-- Receipt for all payments, and for non-course types -->
                                                            <a href="{{ route('admin.payments.payment-receipt', $payment->id) }}" class="btn btn-sm btn-warning" title="Payment Receipt" target="_blank">
                                                                <i class="fas fa-receipt"></i>
                                                            </a>
                                                            <a href="{{ route('admin.payments.payment-receipt-pdf', $payment->id) }}" class="btn btn-sm btn-danger" title="View PDF" target="_blank">
                                                                <i class="fas fa-file-pdf"></i>
                                                            </a>
                                                        @endif
                                                    @endif
                                                    @if($payment->status == 'Pending Approval')
                                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_finance())
                                                        <button type="button" class="btn btn-sm btn-success approve-payment-btn" 
                                                                data-payment-id="{{ $payment->id }}"
                                                                data-amount="{{ $payment->amount_paid }}"
                                                                data-previous-balance="{{ $invoice->net_amount - $payment->previous_balance }}"
                                                                data-payment-type="{{ $payment->payment_type }}"
                                                                data-transaction-id="{{ $payment->transaction_id }}"
                                                                data-file-upload="{{ $payment->file_upload }}"
                                                                title="Approve Payment">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger reject-payment-btn" 
                                                                data-payment-id="{{ $payment->id }}"
                                                                data-amount="{{ $payment->amount_paid }}"
                                                                data-payment-type="{{ $payment->payment_type }}"
                                                                title="Reject Payment">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="11" class="text-center">No payments found for this invoice.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Approval Modal -->
<div class="modal fade" id="approvePaymentModal" tabindex="-1" aria-labelledby="approvePaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approvePaymentModalLabel">Approve Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="approvePaymentForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Please review the payment details before approving:</strong>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <strong>Amount:</strong>
                        </div>
                        <div class="col-6" id="approveAmount">
                            <!-- Amount will be populated here -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <strong>Previous Balance:</strong>
                        </div>
                        <div class="col-6" id="approvePreviousBalance">
                            <!-- Previous balance will be populated here -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <strong>Payment Type:</strong>
                        </div>
                        <div class="col-6" id="approvePaymentType">
                            <!-- Payment type will be populated here -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <strong>Transaction ID:</strong>
                        </div>
                        <div class="col-6">
                            <input type="text" class="form-control" id="approveTransactionId" name="transaction_id" placeholder="Enter transaction ID" value="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <strong>Receipt/Proof:</strong>
                        </div>
                        <div class="col-6" id="approveFile">
                            <!-- File will be populated here -->
                        </div>
                    </div>
                    <hr>
                    <p class="text-muted">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Once approved, this payment will be added to the invoice total and cannot be undone.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Approve Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Payment Rejection Modal -->
<div class="modal fade" id="rejectPaymentModal" tabindex="-1" aria-labelledby="rejectPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectPaymentModalLabel">Reject Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Are you sure you want to reject this payment?</strong>
                </div>
                <div class="row">
                    <div class="col-6">
                        <strong>Amount:</strong>
                    </div>
                    <div class="col-6" id="rejectAmount">
                        <!-- Amount will be populated here -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <strong>Payment Type:</strong>
                    </div>
                    <div class="col-6" id="rejectPaymentType">
                        <!-- Payment type will be populated here -->
                    </div>
                </div>
                <hr>
                <div class="mb-3">
                    <label for="rejectionRemarks" class="form-label">
                        <strong>Remarks <span class="text-danger">*</span></strong>
                    </label>
                    <textarea class="form-control" id="rejectionRemarks" name="rejection_remarks" rows="3" placeholder="Enter rejection remarks..." required></textarea>
                    <small class="form-text text-muted">Please provide a reason for rejecting this payment.</small>
                </div>
                <p class="text-muted">
                    <i class="fas fa-info-circle me-2"></i>
                    Rejected payments will not be added to the invoice total.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="rejectPaymentForm" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="rejection_remarks" id="rejectionRemarksInput">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-2"></i>Reject Payment
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle approve payment button clicks
        document.querySelectorAll('.approve-payment-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const paymentId = this.getAttribute('data-payment-id');
                const amount = this.getAttribute('data-amount');
                const previousBalance = this.getAttribute('data-previous-balance');
                const paymentType = this.getAttribute('data-payment-type');
                const transactionId = this.getAttribute('data-transaction-id');
                const fileUpload = this.getAttribute('data-file-upload');

                showApproveModal(paymentId, amount, previousBalance, paymentType, transactionId, fileUpload);
            });
        });

        // Handle reject payment button clicks
        document.querySelectorAll('.reject-payment-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const paymentId = this.getAttribute('data-payment-id');
                const amount = this.getAttribute('data-amount');
                const paymentType = this.getAttribute('data-payment-type');

                showRejectModal(paymentId, amount, paymentType);
            });
        });
    });

    // Show approve payment modal
    function showApproveModal(paymentId, amount, previousBalance, paymentType, transactionId, fileUpload) {
        document.getElementById('approveAmount').textContent = '₹' + parseFloat(amount).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        document.getElementById('approvePreviousBalance').textContent = '₹' + parseFloat(previousBalance).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        document.getElementById('approvePaymentType').textContent = paymentType;
        document.getElementById('approveTransactionId').value = transactionId || '';

        // Handle file display
        const fileElement = document.getElementById('approveFile');
        if (fileUpload && fileUpload !== '') {
            const fileName = fileUpload.split('/').pop(); // Get filename from path
            const fileUrl = '{{ route("admin.payments.view", ":id") }}'.replace(':id', paymentId);
            fileElement.innerHTML = `<a href="${fileUrl}" target="_blank" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-eye me-1"></i>View ${fileName}
        </a>`;
        } else {
            fileElement.innerHTML = '<span class="text-muted">No file uploaded</span>';
        }

        document.getElementById('approvePaymentForm').action = '{{ route("admin.payments.approve", ":id") }}'.replace(':id', paymentId);

        const modal = new bootstrap.Modal(document.getElementById('approvePaymentModal'), {
            backdrop: 'static',
            keyboard: false
        });
        modal.show();
    }

    // Show reject payment modal
    function showRejectModal(paymentId, amount, paymentType) {
        document.getElementById('rejectAmount').textContent = '₹' + parseFloat(amount).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        document.getElementById('rejectPaymentType').textContent = paymentType;
        document.getElementById('rejectPaymentForm').action = '{{ route("admin.payments.reject", ":id") }}'.replace(':id', paymentId);
        
        // Clear remarks field
        document.getElementById('rejectionRemarks').value = '';

        const modal = new bootstrap.Modal(document.getElementById('rejectPaymentModal'), {
            backdrop: 'static',
            keyboard: false
        });
        modal.show();
    }
    
    // Handle form submission to include remarks
    document.getElementById('rejectPaymentForm').addEventListener('submit', function(e) {
        const remarks = document.getElementById('rejectionRemarks').value.trim();
        if (!remarks) {
            e.preventDefault();
            alert('Please enter rejection remarks.');
            document.getElementById('rejectionRemarks').focus();
            return false;
        }
        document.getElementById('rejectionRemarksInput').value = remarks;
    });

</script>
@endsection
