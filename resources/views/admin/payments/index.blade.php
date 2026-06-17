@extends('layouts.mantis')

@section('title', 'Payments - ' . $invoice->invoice_number)

@section('content')
@php
    $razorpayConfigured = config('razorpay.key_id') && config('razorpay.key_secret');
@endphp
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Payments for Invoice {{ $invoice->invoice_number }}</h4>
                        <div>
                            <a href="{{ route('admin.payments.create', $invoice->id) }}" class="btn btn-success">
                                <i class="fas fa-plus"></i> Add Payment
                            </a>
                            <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Invoice
                            </a>
                        </div>
                    </div>
                </div>

            <!-- Payment Links -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-gradient-info d-flex flex-wrap justify-content-between align-items-center">
                    <h6 class="mb-0 text-white d-flex align-items-center">
                        <i class="fas fa-link me-2"></i>Online Payment Links
                    </h6>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-dark">
                            Pending: ₹{{ number_format(round($invoice->pending_amount)) }}
                        </span>
                        <button class="btn btn-light btn-sm {{ ($invoice->pending_amount <= 0 || !$razorpayConfigured) ? 'disabled' : '' }}"
                            data-bs-toggle="modal" data-bs-target="#paymentLinkModal"
                            {{ $invoice->pending_amount <= 0 || !$razorpayConfigured ? 'disabled' : '' }}>
                            <i class="fas fa-plus me-1"></i>Generate Link
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @unless($razorpayConfigured)
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Configure <code>RAZORPAY_KEY_ID</code> and <code>RAZORPAY_KEY_SECRET</code> to enable payment links.
                        </div>
                    @else
                        @if($paymentLinks->isEmpty())
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-link-slash fa-2x mb-2 d-block"></i>
                                No payment links generated for this invoice yet.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Expires</th>
                                            <th>Payment Date</th>
                                            <th>Link</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($paymentLinks as $index => $link)
                                            @php
                                                $statusMap = [
                                                    'created' => 'bg-secondary',
                                                    'issued' => 'bg-primary',
                                                    'paid' => 'bg-success',
                                                    'cancelled' => 'bg-danger',
                                                    'expired' => 'bg-warning text-dark',
                                                ];
                                                $statusClass = $statusMap[$link->status] ?? 'bg-secondary';
                                            @endphp
                                            <tr id="payment-link-row-{{ $link->id }}">
                                                <td>{{ $index + 1 }}</td>
                                                <td class="fw-semibold text-success">₹{{ number_format(round($link->amount)) }}</td>
                                                <td>
                                                    <span class="badge {{ $statusClass }}">
                                                        {{ ucfirst(str_replace('_', ' ', $link->status ?? 'created')) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small class="text-muted d-block">{{ $link->created_at->format('d M Y') }}</small>
                                                    <small>{{ $link->created_at->format('h:i A') }}</small>
                                                </td>
                                                <td>
                                                    @if($link->expires_at)
                                                        <small class="text-muted d-block">{{ $link->expires_at->format('d M Y') }}</small>
                                                        <small>{{ $link->expires_at->format('h:i A') }}</small>
                                                    @else
                                                        <span class="text-muted">No expiry</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($link->paid_at)
                                                        <small class="text-success d-block">{{ $link->paid_at->format('d M Y') }}</small>
                                                        <small>{{ $link->paid_at->format('h:i A') }}</small>
                                                    @else
                                                        <span class="text-muted">Pending</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($link->short_url)
                                                        <div class="d-flex align-items-center gap-2">
                                                            <span class="text-muted text-truncate" style="max-width: 180px;">
                                                                {{ $link->short_url }}
                                                            </span>
                                                            <button class="btn btn-sm btn-outline-secondary copy-link-btn" type="button"
                                                                data-link="{{ $link->short_url }}" title="Copy link">
                                                                <i class="fas fa-copy"></i>
                                                            </button>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">Not available</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button"
                                                            class="btn btn-outline-secondary refresh-link-btn"
                                                            data-refresh-url="{{ route('admin.payments.links.refresh', [$invoice->id, $link->id]) }}"
                                                            data-row="#payment-link-row-{{ $link->id }}"
                                                            title="Refresh Status">
                                                            <i class="fas fa-sync-alt"></i>
                                                        </button>
                                                        @if($link->status === 'created')
                                                            <button type="button"
                                                                class="btn btn-outline-danger delete-link-btn"
                                                                data-delete-url="{{ route('admin.payments.links.delete', [$invoice->id, $link->id]) }}"
                                                                data-link-id="{{ $link->id }}"
                                                                title="Delete Link">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    @endunless
                </div>
            </div>
                <div class="card-body">
                    <!-- Invoice Details -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-gradient-primary">
                                    <h6 class="mb-0 text-white"><i class="fas fa-file-invoice me-2"></i>Invoice Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                                        <i class="fas fa-tag text-primary"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="mb-1 text-muted">Invoice Type</h6>
                                                    <div>
                                                        @if($invoice->invoice_type == 'course')
                                                            <span class="badge bg-primary fs-6 px-3 py-2">
                                                                <i class="fas fa-graduation-cap me-1"></i>Course
                                                            </span>
                                                        @elseif($invoice->invoice_type == 'e-service')
                                                            <span class="badge bg-info fs-6 px-3 py-2">
                                                                <i class="fas fa-laptop me-1"></i>E-Service
                                                            </span>
                                                        @elseif($invoice->invoice_type == 'batch_change')
                                                            <span class="badge bg-warning fs-6 px-3 py-2">
                                                                <i class="fas fa-exchange-alt me-1"></i>Batch Change
                                                            </span>
                                                        @elseif($invoice->invoice_type == 'batch_postpond')
                                                            <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                                                                <i class="fas fa-calendar-alt me-1"></i>Batch Postponed
                                                            </span>
                                                        @elseif($invoice->invoice_type == 'fine')
                                                            <span class="badge bg-danger fs-6 px-3 py-2">
                                                                <i class="fas fa-exclamation-circle me-1"></i>Fine
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-success bg-opacity-10 rounded-circle p-2">
                                                        <i class="fas fa-info-circle text-success"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="mb-1 text-muted">Details</h6>
                                                    <div class="fw-semibold">
                                                        @if($invoice->invoice_type == 'course')
                                                            @if($invoice->course_id == 9 && $invoice->student->leadDetail)
                                                                @php
                                                                    $studentDetail = $invoice->student->leadDetail;
                                                                    $university = $studentDetail->university;
                                                                    $courseType = $studentDetail->course_type;
                                                                @endphp
                                                                @if($university && $courseType)
                                                                    <i class="fas fa-university text-primary me-1"></i>{{ $university->title }} - {{ $courseType }}
                                                                @else
                                                                    <i class="fas fa-book text-primary me-1"></i>{{ $invoice->course->title ?? 'N/A' }}
                                                                @endif
                                                            @else
                                                                <i class="fas fa-book text-primary me-1"></i>{{ $invoice->course->title ?? 'N/A' }}
                                                            @endif
                                                        @elseif($invoice->invoice_type == 'e-service')
                                                            <i class="fas fa-laptop text-info me-1"></i>{{ $invoice->service_name ?? 'N/A' }}
                                                        @elseif($invoice->invoice_type == 'batch_change')
                                                            <i class="fas fa-exchange-alt text-warning me-1"></i>{{ $invoice->batch->title ?? 'N/A' }} ({{ $invoice->batch->course->title ?? 'N/A' }})
                                                        @elseif($invoice->invoice_type == 'batch_postpond')
                                                            <i class="fas fa-calendar-alt text-warning me-1"></i>{{ $invoice->batch->title ?? 'N/A' }} ({{ $invoice->batch->course->title ?? 'N/A' }})
                                                        @elseif($invoice->invoice_type == 'fine')
                                                            <i class="fas fa-exclamation-circle text-danger me-1"></i>{{ $invoice->service_name ?? 'N/A' }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-gradient-success text-white">
                                    <h6 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Invoice Date</h6>
                                </div>
                                <div class="card-body text-center">
                                    <div class="display-6 text-success mb-2">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <h5 class="mb-0">{{ $invoice->invoice_date->format('M d, Y') }}</h5>
                                    <small class="text-muted">{{ $invoice->invoice_date->format('l') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Summary -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class="fas fa-rupee-sign text-info fs-4"></i>
                                    </div>
                                    <h6 class="text-muted mb-2">Invoice total</h6>
                                    <h3 class="text-info mb-0">₹{{ number_format(round($invoice->net_amount)) }}</h3>
                                    <small class="text-muted">After discount</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class="fas fa-check-circle text-success fs-4"></i>
                                    </div>
                                    <h6 class="text-muted mb-2">Paid Amount</h6>
                                    <h3 class="text-success mb-0">₹{{ number_format(round($invoice->paid_amount)) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class="fas fa-clock text-warning fs-4"></i>
                                    </div>
                                    <h6 class="text-muted mb-2">Pending Amount</h6>
                                    <h3 class="text-warning mb-0">₹{{ number_format(round($invoice->pending_amount)) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payments Table -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-gradient-dark mb-3">
                            <h6 class="mb-0 text-white"><i class="fas fa-credit-card me-2"></i>Payment History</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" style="overflow-x: auto;">
                                @if($payments->count())
                                <table class="table table-hover mb-0 datatable" id="paymentsTable" style="min-width: 1200px;">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="border-0">#</th>
                                            <th class="border-0">Payment Date</th>
                                            <th class="border-0">Amount</th>
                                            <th class="border-0">Fee Head</th>
                                            <th class="border-0">Previous Balance</th>
                                            <th class="border-0">Payment Type</th>
                                            <th class="border-0">Transaction ID</th>
                                            <th class="border-0">Status</th>
                                            <th class="border-0">Created By</th>
                                            <th class="border-0">Status Updated By</th>
                                            <th class="border-0">Status Date</th>
                                            <th class="border-0">File</th>
                                            <th class="border-0">Actions</th>
                                        </tr>
                                    </thead>
                            <tbody>
                                @foreach($payments as $index => $payment)
                                <tr class="align-middle">
                                    <td class="fw-semibold">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-1 me-2">
                                                <i class="fas fa-calendar text-primary" style="font-size: 12px;"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $payment->payment_date ? $payment->payment_date->format('M d, Y') : $payment->created_at->format('M d, Y') }}</div>
                                                <small class="text-muted">{{ $payment->created_at->format('h:i A') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success bg-opacity-10 rounded-circle p-1 me-2">
                                                <i class="fas fa-rupee-sign text-success" style="font-size: 12px;"></i>
                                            </div>
                                            <span class="fw-bold text-success">₹{{ number_format(round($payment->amount_paid)) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($payment->fee_head)
                                            <span class="badge bg-primary">{{ $payment->fee_head }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-info bg-opacity-10 rounded-circle p-1 me-2">
                                                <i class="fas fa-balance-scale text-info" style="font-size: 12px;"></i>
                                            </div>
                                            <span class="fw-semibold">₹{{ number_format(round($payment->invoice->net_amount - $payment->previous_balance)) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <i class="fas fa-credit-card me-1"></i>{{ $payment->payment_type }}
                                        </span>
                                    </td>
                                    <td>
                                        @include('admin.payments.partials.transaction-ids-display', ['payment' => $payment])
                                    </td>
                                    <td>
                                        @if($payment->status == 'Pending Approval')
                                        <span class="badge bg-warning fs-6 px-3 py-2">
                                            <i class="fas fa-clock me-1"></i>Pending Approval
                                        </span>
                                        @elseif($payment->status == 'Approved')
                                        <span class="badge bg-success fs-6 px-3 py-2">
                                            <i class="fas fa-check-circle me-1"></i>Approved
                                        </span>
                                        @else
                                        <span class="badge bg-danger fs-6 px-3 py-2">
                                            <i class="fas fa-times-circle me-1"></i>Rejected
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-1 me-2">
                                                <i class="fas fa-user text-primary" style="font-size: 12px;"></i>
                                            </div>
                                            <span class="fw-semibold">{{ $payment->createdBy->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($payment->status == 'Approved' && $payment->approvedBy)
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success bg-opacity-10 rounded-circle p-1 me-2">
                                                    <i class="fas fa-check-circle text-success" style="font-size: 12px;"></i>
                                                </div>
                                                <span class="fw-semibold">{{ $payment->approvedBy->name }}</span>
                                            </div>
                                        @elseif($payment->status == 'Rejected' && $payment->rejectedBy)
                                            <div class="d-flex align-items-center">
                                                <div class="bg-danger bg-opacity-10 rounded-circle p-1 me-2">
                                                    <i class="fas fa-times-circle text-danger" style="font-size: 12px;"></i>
                                                </div>
                                                <span class="fw-semibold">{{ $payment->rejectedBy->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($payment->status == 'Approved' && $payment->approved_date)
                                            <div>
                                                <div class="fw-semibold">{{ $payment->approved_date->format('M d, Y') }}</div>
                                                <small class="text-muted">{{ $payment->approved_date->format('h:i A') }}</small>
                                            </div>
                                        @elseif($payment->status == 'Rejected' && $payment->rejected_date)
                                            <div>
                                                <div class="fw-semibold">{{ $payment->rejected_date->format('M d, Y') }}</div>
                                                <small class="text-muted">{{ $payment->rejected_date->format('h:i A') }}</small>
                                                @if($payment->rejection_remarks)
                                                    <div class="mt-1">
                                                        <small class="text-danger">
                                                            <i class="fas fa-comment-alt me-1"></i>
                                                            <strong>Remarks:</strong> {{ $payment->rejection_remarks }}
                                                        </small>
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @include('admin.payments.partials.proof-files-display', ['payment' => $payment])
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1 justify-content-start">
                                            <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-sm btn-info" title="View Payment">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($payment->status == 'Approved')
                                                @if($payment->invoice->invoice_type === 'course' && $firstPayment && $payment->id == $firstPayment->id)
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
                                                <button type="button" class="btn btn-sm btn-success" onclick="showApproveModal({{ $payment->id }}, '{{ $payment->amount_paid }}', '{{ $payment->invoice->net_amount - $payment->previous_balance }}', '{{ $payment->payment_type }}', '{{ $payment->transaction_id }}', '{{ $payment->file_upload }}')" title="Approve Payment">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="showRejectModal({{ $payment->id }}, '{{ $payment->amount_paid }}', '{{ $payment->payment_type }}')" title="Reject Payment">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                                @else
                                <div class="py-4 text-center text-muted">
                                    <i class="fas fa-info-circle me-2"></i>No payments found for this invoice.
                                </div>
                                @endif
                    </div>

                    <!-- Print Invoice Button -->
                    <!-- <div class="row mt-4">
                        <div class="col-12 text-center">
                            <button class="btn btn-primary btn-lg" onclick="printInvoice()">
                                <i class="fas fa-print"></i> Print Invoice
                            </button>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function printInvoice() {
        // Placeholder for print functionality
        alert('Print functionality will be implemented in the future.');
    }
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentLinkForm = document.getElementById('paymentLinkForm');
    const paymentLinkError = document.getElementById('paymentLinkError');

    if (paymentLinkForm) {
        paymentLinkForm.addEventListener('submit', function(event) {
            event.preventDefault();

            paymentLinkError?.classList.add('d-none');
            const submitBtn = paymentLinkForm.querySelector('button[type="submit"]');
            const spinner = submitBtn?.querySelector('.spinner-border');
            const label = submitBtn?.querySelector('.default-label');

            submitBtn.disabled = true;
            spinner?.classList.remove('d-none');
            label?.classList.add('d-none');

            fetch('{{ route('admin.payments.links.store', $invoice->id) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: new FormData(paymentLinkForm),
            })
            .then(async (response) => {
                const data = await response.json().catch(() => ({}));
                return { ok: response.ok, data };
            })
            .then(({ ok, data }) => {
                if (ok && data.success) {
                    showLinkToast(data.message || 'Payment link generated.', 'success');
                    const modalInstance = bootstrap.Modal.getInstance(document.getElementById('paymentLinkModal'));
                    modalInstance?.hide();
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    const message = data?.message || 'Unable to create payment link.';
                    if (paymentLinkError) {
                        paymentLinkError.textContent = message;
                        paymentLinkError.classList.remove('d-none');
                    }
                    showLinkToast(message, 'error');
                }
            })
            .catch(() => {
                const message = 'Unable to create payment link. Please try again.';
                if (paymentLinkError) {
                    paymentLinkError.textContent = message;
                    paymentLinkError.classList.remove('d-none');
                }
                showLinkToast(message, 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                spinner?.classList.add('d-none');
                label?.classList.remove('d-none');
            });
        });
    }

    document.querySelectorAll('.copy-link-btn').forEach(button => {
        button.addEventListener('click', () => {
            const link = button.dataset.link;
            if (!link) {
                return;
            }
            navigator.clipboard.writeText(link)
                .then(() => showLinkToast('Link copied to clipboard.', 'success'))
                .catch(() => showLinkToast('Unable to copy link.', 'error'));
        });
    });

    document.querySelectorAll('.refresh-link-btn').forEach(button => {
        button.addEventListener('click', () => {
            const url = button.dataset.refreshUrl;
            if (!url) {
                return;
            }

            const icon = button.querySelector('i');
            icon?.classList.add('fa-spin');

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
            })
            .then(async response => {
                const data = await response.json().catch(() => ({}));
                return { ok: response.ok, data };
            })
            .then(({ ok, data }) => {
                if (ok && data.success) {
                    showLinkToast(data.message || 'Payment link updated.', 'success');
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    showLinkToast(data?.message || 'Unable to refresh payment link.', 'error');
                }
            })
            .catch(() => showLinkToast('Unable to refresh payment link.', 'error'))
            .finally(() => icon?.classList.remove('fa-spin'));
        });
    });

    // Delete payment link functionality
    let deletePaymentLinkUrl = null;
    let deletePaymentLinkId = null;
    let deletePaymentLinkButton = null;

    document.querySelectorAll('.delete-link-btn').forEach(button => {
        button.addEventListener('click', () => {
            const url = button.dataset.deleteUrl;
            const linkId = button.dataset.linkId;
            if (!url) {
                return;
            }

            deletePaymentLinkUrl = url;
            deletePaymentLinkId = linkId;
            deletePaymentLinkButton = button;

            const modal = new bootstrap.Modal(document.getElementById('deletePaymentLinkModal'));
            modal.show();
        });
    });

    document.getElementById('confirmDeletePaymentLinkBtn')?.addEventListener('click', () => {
        if (!deletePaymentLinkUrl || !deletePaymentLinkId) {
            return;
        }

        const confirmBtn = document.getElementById('confirmDeletePaymentLinkBtn');
        const icon = confirmBtn?.querySelector('i');
        const originalHtml = confirmBtn?.innerHTML;
        
        confirmBtn.disabled = true;
        if (icon) {
            icon.className = 'fas fa-spinner fa-spin me-1';
        }

        fetch(deletePaymentLinkUrl, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
        })
        .then(async response => {
            const data = await response.json().catch(() => ({}));
            return { ok: response.ok, data };
        })
        .then(({ ok, data }) => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('deletePaymentLinkModal'));
            modal?.hide();

            if (ok && data.success) {
                showLinkToast(data.message || 'Payment link deleted successfully.', 'success');
                // Remove the row from the table
                const row = document.querySelector(`#payment-link-row-${deletePaymentLinkId}`);
                if (row) {
                    row.style.transition = 'opacity 0.3s';
                    row.style.opacity = '0';
                    setTimeout(() => {
                        row.remove();
                        // Reload if no links remain
                        const remainingLinks = document.querySelectorAll('[id^="payment-link-row-"]');
                        if (remainingLinks.length === 0) {
                            setTimeout(() => window.location.reload(), 500);
                        }
                    }, 300);
                } else {
                    setTimeout(() => window.location.reload(), 800);
                }
            } else {
                showLinkToast(data?.message || 'Unable to delete payment link.', 'error');
                if (deletePaymentLinkButton) {
                    deletePaymentLinkButton.disabled = false;
                }
            }
        })
        .catch(() => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('deletePaymentLinkModal'));
            modal?.hide();
            showLinkToast('Unable to delete payment link.', 'error');
            if (deletePaymentLinkButton) {
                deletePaymentLinkButton.disabled = false;
            }
        })
        .finally(() => {
            if (confirmBtn) {
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = originalHtml || '<i class="fas fa-trash me-1"></i>Delete Link';
            }
            deletePaymentLinkUrl = null;
            deletePaymentLinkId = null;
            deletePaymentLinkButton = null;
        });
    });

    function showLinkToast(message, type = 'info') {
        if (typeof showToast === 'function') {
            showToast(message, type);
        } else if (type === 'success' && typeof toast_success === 'function') {
            toast_success(message);
        } else if (type === 'error' && typeof toast_error === 'function') {
            toast_error(message);
        } else {
            alert(message);
        }
    }
});
</script>

<!-- Payment Link Modal -->
<div class="modal fade" id="paymentLinkModal" tabindex="-1" aria-labelledby="paymentLinkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentLinkModalLabel">
                    <i class="fas fa-link me-2"></i>Generate Payment Link
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="paymentLinkForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Share the generated link with the student to collect the selected amount via Razorpay.
                    </div>
                    <div class="mb-3">
                        <label for="payment-link-amount" class="form-label">Amount <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="1"
                            max="{{ max($invoice->pending_amount, 0) }}"
                            class="form-control" id="payment-link-amount" name="amount"
                            value="{{ number_format(max($invoice->pending_amount, 0), 2, '.', '') }}" required>
                        <small class="text-muted">Pending balance: ₹{{ number_format(round($invoice->pending_amount)) }}</small>
                    </div>
                    <div class="mb-3">
                        <label for="payment-link-description" class="form-label">Description</label>
                        <input type="text" class="form-control" id="payment-link-description" name="description"
                            value="Payment for invoice {{ $invoice->invoice_number }}" maxlength="190">
                    </div>
                    <div class="alert alert-warning mb-0 d-none" id="paymentLinkError"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="default-label"><i class="fas fa-link me-1"></i>Generate Link</span>
                        <span class="spinner-border spinner-border-sm me-2 d-none" role="status"></span>
                    </button>
                </div>
            </form>
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

<!-- Delete Payment Link Modal -->
<div class="modal fade" id="deletePaymentLinkModal" tabindex="-1" aria-labelledby="deletePaymentLinkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deletePaymentLinkModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Delete Payment Link
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning mb-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Are you sure you want to delete this payment link?</strong>
                </div>
                <p class="mb-0">
                    This action cannot be undone. The payment link will be cancelled in Razorpay and removed from the system.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeletePaymentLinkBtn">
                    <i class="fas fa-trash me-1"></i>Delete Link
                </button>
            </div>
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

    // Custom DataTable configuration for payments table
    $(document).ready(function() {
        // Wait for global DataTable to initialize, then customize
        setTimeout(function() {
            if ($.fn.DataTable.isDataTable('#paymentsTable')) {
                var table = $('#paymentsTable').DataTable();
                
                // Add custom styling to DataTable elements
                $('.dataTables_length select').addClass('form-select form-select-sm');
                $('.dataTables_filter input').addClass('form-control form-control-sm');
                
                // Set initial sort by Payment Date (column 1) descending
                table.order([1, 'desc']).draw();
            }
        }, 100);
    });
</script>
@endsection

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.bg-gradient-dark {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
}

.card {
    transition: all 0.3s ease;
}
/* 
.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
} */

.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
}

.badge {
    font-weight: 500;
    letter-spacing: 0.5px;
}

.btn {
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.rounded-circle {
    transition: all 0.3s ease;
}

.rounded-circle:hover {
    transform: scale(1.1);
}

code {
    font-family: 'Courier New', monospace;
    font-size: 0.85em;
}

.fw-semibold {
    font-weight: 600;
}

.fw-bold {
    font-weight: 700;
}

/* Custom scrollbar for table */
.table-responsive::-webkit-scrollbar {
    height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* DataTable horizontal scroll styling */
.dataTables_wrapper .dataTables_scroll {
    overflow-x: auto;
}

.dataTables_wrapper .dataTables_scrollBody {
    overflow-x: auto;
}

/* Ensure table columns have proper width */
#paymentsTable th,
#paymentsTable td {
    white-space: nowrap;
    min-width: 100px;
}

#paymentsTable th:nth-child(1),
#paymentsTable td:nth-child(1) {
    min-width: 50px;
    width: 50px;
}

#paymentsTable th:nth-child(2),
#paymentsTable td:nth-child(2) {
    min-width: 120px;
}

#paymentsTable th:nth-child(3),
#paymentsTable td:nth-child(3) {
    min-width: 100px;
}

#paymentsTable th:nth-child(4),
#paymentsTable td:nth-child(4) {
    min-width: 120px;
}

#paymentsTable th:nth-child(5),
#paymentsTable td:nth-child(5) {
    min-width: 100px;
}

#paymentsTable th:nth-child(6),
#paymentsTable td:nth-child(6) {
    min-width: 120px;
}

#paymentsTable th:nth-child(7),
#paymentsTable td:nth-child(7) {
    min-width: 120px;
}

#paymentsTable th:nth-child(8),
#paymentsTable td:nth-child(8) {
    min-width: 100px;
}

#paymentsTable th:nth-child(9),
#paymentsTable td:nth-child(9) {
    min-width: 120px;
}

#paymentsTable th:nth-child(10),
#paymentsTable td:nth-child(10) {
    min-width: 120px;
}

#paymentsTable th:nth-child(11),
#paymentsTable td:nth-child(11) {
    min-width: 80px;
}

#paymentsTable th:nth-child(12),
#paymentsTable td:nth-child(12) {
    min-width: 150px;
}
</style>

@include('admin.payments.add-modal')