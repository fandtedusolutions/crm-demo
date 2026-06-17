@extends('layouts.mantis')

@section('title', 'Payment Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Payment Details</h4>
                        <div>
                            @if($payment->status == 'Approved')
                                @if($payment->invoice->invoice_type === 'course' && $firstPayment && $payment->id == $firstPayment->id)
                                    <!-- Tax Invoice only for course invoices, first approved payment -->
                                    <a href="{{ route('admin.payments.tax-invoice', $payment->id) }}" class="btn btn-warning me-2" target="_blank">
                                        <i class="fas fa-file-invoice"></i> Tax Invoice
                                    </a>
                                    <a href="{{ route('admin.payments.tax-invoice-pdf', $payment->id) }}" class="btn btn-danger me-2" target="_blank">
                                        <i class="fas fa-file-pdf"></i> View PDF
                                    </a>
                                @else
                                    <!-- Receipt for all payments, and for non-course types -->
                                    <a href="{{ route('admin.payments.payment-receipt', $payment->id) }}" class="btn btn-warning me-2" target="_blank">
                                        <i class="fas fa-receipt"></i> Payment Receipt
                                    </a>
                                    <a href="{{ route('admin.payments.payment-receipt-pdf', $payment->id) }}" class="btn btn-danger me-2" target="_blank">
                                        <i class="fas fa-file-pdf"></i> View PDF
                                    </a>
                                @endif
                            @endif
                            <a href="{{ route('admin.payments.index', $payment->invoice_id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Payments
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Payment Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Payment ID:</strong></td>
                                    <td>#{{ $payment->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Amount:</strong></td>
                                    <td>₹{{ number_format(round($payment->amount_paid)) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Fee Head:</strong></td>
                                    <td>{{ $payment->fee_head ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Type:</strong></td>
                                    <td>{{ $payment->payment_type }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Transaction ID{{ $payment->getDisplayProofs()->count() > 1 ? 's' : '' }}:</strong></td>
                                    <td>
                                        @include('admin.payments.partials.transaction-ids-display', ['payment' => $payment])
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($payment->status == 'Pending Approval')
                                            <span class="badge bg-warning">Pending Approval</span>
                                        @elseif($payment->status == 'Approved')
                                            <span class="badge bg-success">Approved</span>
                                        @else
                                            <span class="badge bg-danger">Rejected</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($payment->status == 'Rejected' && $payment->rejection_remarks)
                                <tr>
                                    <td><strong>Rejection Remarks:</strong></td>
                                    <td>
                                        <div class="alert alert-danger mb-0 py-2">
                                            <i class="fas fa-comment-alt me-2"></i>
                                            {{ $payment->rejection_remarks }}
                                        </div>
                                    </td>
                                </tr>
                                @endif
                                @if($payment->status == 'Rejected' && $payment->rejected_date)
                                <tr>
                                    <td><strong>Rejected Date:</strong></td>
                                    <td>
                                        <div>
                                            <div class="fw-semibold">{{ $payment->rejected_date->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ $payment->rejected_date->format('h:i A') }}</small>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                                @if($payment->status == 'Rejected' && $payment->rejectedBy)
                                <tr>
                                    <td><strong>Rejected By:</strong></td>
                                    <td>{{ $payment->rejectedBy->name }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td><strong>Payment Date:</strong></td>
                                    <td>
                                        @if($payment->payment_date)
                                            {{ $payment->payment_date->format('M d, Y') }}
                                        @else
                                            {{ $payment->created_at->format('M d, Y') }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Created Date:</strong></td>
                                    <td>{{ $payment->created_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Created By:</strong></td>
                                    <td>{{ $payment->createdBy->name }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Invoice Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Invoice Number:</strong></td>
                                    <td>{{ $payment->invoice->invoice_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Student Name:</strong></td>
                                    <td>{{ $payment->invoice->student->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Student Phone:</strong></td>
                                    <td>{{ $payment->invoice->student->code }} {{ $payment->invoice->student->phone }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Type:</strong></td>
                                    <td>
                                        @if($payment->invoice->invoice_type === 'course')
                                            <span class="badge bg-primary">Course</span>
                                        @elseif($payment->invoice->invoice_type === 'e-service')
                                            <span class="badge bg-info">E-Service</span>
                                        @elseif($payment->invoice->invoice_type === 'batch_change')
                                            <span class="badge bg-warning">Batch Change</span>
                                        @elseif($payment->invoice->invoice_type === 'batch_postpond')
                                            <span class="badge bg-warning text-dark">Batch Postponed</span>
                                        @elseif($payment->invoice->invoice_type === 'fine')
                                            <span class="badge bg-danger">Fine</span>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Details:</strong></td>
                                    <td>
                                        @if($payment->invoice->invoice_type === 'course')
                                            @if($payment->invoice->course_id == 9 && $payment->invoice->student->leadDetail)
                                                @php
                                                    $studentDetail = $payment->invoice->student->leadDetail;
                                                    $university = $studentDetail->university;
                                                    $courseType = $studentDetail->course_type;
                                                @endphp
                                                @if($university && $courseType)
                                                    {{ $university->title }} - {{ $courseType }}
                                                @else
                                                    {{ $payment->invoice->course->title ?? 'N/A' }}
                                                @endif
                                            @else
                                                {{ $payment->invoice->course->title ?? 'N/A' }}
                                            @endif
                                        @elseif($payment->invoice->invoice_type === 'e-service')
                                            {{ $payment->invoice->service_name ?? 'N/A' }}
                                        @elseif($payment->invoice->invoice_type === 'batch_change' || $payment->invoice->invoice_type === 'batch_postpond')
                                            {{ $payment->invoice->batch->title ?? 'N/A' }} ({{ $payment->invoice->batch->course->title ?? 'N/A' }})
                                        @elseif($payment->invoice->invoice_type === 'fine')
                                            {{ $payment->invoice->service_name ?? 'N/A' }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Total Amount:</strong></td>
                                    <td>₹{{ number_format(round($payment->invoice->net_amount)) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Paid Amount:</strong></td>
                                    <td>₹{{ number_format(round($payment->invoice->paid_amount)) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Pending Amount:</strong></td>
                                    <td>₹{{ number_format(round($payment->invoice->pending_amount)) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @php $displayProofFiles = $payment->getDisplayProofs()->filter(fn ($proof) => !empty($proof->file_upload)); @endphp
                    @if($displayProofFiles->isNotEmpty())
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6>Uploaded File{{ $displayProofFiles->count() > 1 ? 's' : '' }}</h6>
                            <div class="row g-3">
                                @foreach($displayProofFiles as $proof)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100">
                                        <div class="card-body text-center">
                                            <i class="fas fa-file fa-3x text-primary mb-3"></i>
                                            <p class="mb-3">Receipt/Proof {{ $displayProofFiles->count() > 1 ? $loop->iteration : 'Document' }}</p>
                                            @php
                                                $viewUrl = !empty($proof->id)
                                                    ? route('admin.payments.proofs.view', $proof->id)
                                                    : route('admin.payments.view', $payment->id);
                                                $downloadUrl = !empty($proof->id)
                                                    ? route('admin.payments.proofs.download', $proof->id)
                                                    : route('admin.payments.download', $payment->id);
                                            @endphp
                                            <a href="{{ $downloadUrl }}" class="btn btn-primary">
                                                <i class="fas fa-download"></i> Download File
                                            </a>
                                            <a href="{{ $viewUrl }}" class="btn btn-primary me-2" target="_blank">
                                                <i class="fas fa-file-alt"></i> Receipt/Proof
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($payment->status == 'Pending Approval')
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6>Payment Actions</h6>
                            <div class="d-flex gap-2">
                                @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_finance())
                                <button type="button" class="btn btn-success approve-payment-btn" 
                                        data-payment-id="{{ $payment->id }}"
                                        data-amount="{{ $payment->amount_paid }}"
                                        data-previous-balance="{{ $payment->invoice->net_amount - $payment->previous_balance }}"
                                        data-payment-type="{{ $payment->payment_type }}"
                                        data-transaction-id="{{ $payment->transaction_id }}"
                                        data-file-upload="{{ $payment->file_upload }}">
                                    <i class="fas fa-check"></i> Approve Payment
                                </button>
                                <button type="button" class="btn btn-danger reject-payment-btn" 
                                        data-payment-id="{{ $payment->id }}"
                                        data-amount="{{ $payment->amount_paid }}"
                                        data-payment-type="{{ $payment->payment_type }}">
                                    <i class="fas fa-times"></i> Reject Payment
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
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
