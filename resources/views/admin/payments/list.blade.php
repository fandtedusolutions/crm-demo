@extends('layouts.mantis')

@section('title', 'Payments Overview')

@php
    $canApprovePayments = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_finance();
@endphp

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="bg-primary bg-opacity-10 px-4 py-4 rounded-top">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    <i class="ti ti-cash f-20"></i>
                                </div>
                                <div>
                                    <h4 class="mb-1 fw-semibold text-primary mb-0">Payment Approval Center</h4>
                                    <small class="text-muted">Review, approve, and track every payment submitted against invoices.</small>
                                </div>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                <div class="card border-0 bg-white shadow-sm text-center px-3 py-2">
                                    <div class="text-uppercase text-muted small">Pending</div>
                                    <div class="h5 mb-0 text-warning">{{ $counts['pending'] ?? 0 }}</div>
                                </div>
                                <div class="card border-0 bg-white shadow-sm text-center px-3 py-2">
                                    <div class="text-uppercase text-muted small">Approved</div>
                                    <div class="h5 mb-0 text-success">{{ $counts['approved'] ?? 0 }}</div>
                                </div>
                                <div class="card border-0 bg-white shadow-sm text-center px-3 py-2">
                                    <div class="text-uppercase text-muted small">Rejected</div>
                                    <div class="h5 mb-0 text-danger">{{ $counts['rejected'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                        <hr class="border-light opacity-25 mt-4 mb-4">
                        <form method="GET" action="{{ route('admin.payments.list') }}" class="row g-3 align-items-end">
                            <input type="hidden" name="active_tab" id="active_tab" value="{{ request('active_tab', '#pending-payments') }}">
                            <div class="col-12 col-lg-3">
                                <label for="from_date" class="form-label text-muted small text-uppercase fw-semibold">From Date</label>
                                <input type="date" class="form-control" id="from_date" name="from_date" value="{{ $filters['from_date'] ?? '' }}">
                            </div>
                            <div class="col-12 col-lg-3">
                                <label for="to_date" class="form-label text-muted small text-uppercase fw-semibold">To Date</label>
                                <input type="date" class="form-control" id="to_date" name="to_date" value="{{ $filters['to_date'] ?? '' }}">
                            </div>
                            <div class="col-12 col-lg-3">
                                <label for="payment_date_from" class="form-label text-muted small text-uppercase fw-semibold">Payment Date From</label>
                                <input type="date" class="form-control" id="payment_date_from" name="payment_date_from" value="{{ $filters['payment_date_from'] ?? '' }}">
                            </div>
                            <div class="col-12 col-lg-3">
                                <label for="payment_date_to" class="form-label text-muted small text-uppercase fw-semibold">Payment Date To</label>
                                <input type="date" class="form-control" id="payment_date_to" name="payment_date_to" value="{{ $filters['payment_date_to'] ?? '' }}">
                            </div>
                            <div class="col-12 col-lg-3">
                                <label for="student_id" class="form-label text-muted small text-uppercase fw-semibold">Student</label>
                                <select class="form-select select2" id="student_id" name="student_id" data-placeholder="All Students">
                                    <option value="">All Students</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" {{ ($filters['student_id'] ?? '') == $student->id ? 'selected' : '' }}>
                                            {{ $student->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-lg-3">
                                <label for="search" class="form-label text-muted small text-uppercase fw-semibold">Search</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="ti ti-search text-muted"></i></span>
                                    <input type="text" class="form-control" id="search" name="search" placeholder="Search by name, phone, invoice #, or transaction ID" value="{{ $filters['search'] ?? '' }}">
                                </div>
                            </div>
                            <div class="col-12 d-flex flex-wrap gap-2 justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-filter me-1"></i>Apply Filters
                                </button>
                                <a href="{{ route('admin.payments.list') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-rotate-clockwise me-1"></i>Reset
                                </a>
                            </div>
                        </form>
                    </div>
                    <div class="px-4 pb-4 pt-4">
                        <ul class="nav nav-pills nav-fill flex-column flex-md-row gap-2 gap-md-3 mb-4" id="paymentTabs" role="tablist">
                            <li class="nav-item flex-fill">
                                <button class="nav-link tab-pill flex-fill active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending-payments" type="button" role="tab" aria-controls="pending-payments" aria-selected="true">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="fw-semibold text-dark">Pending Approval</div>
                                            <small class="text-muted">Payments awaiting verification</small>
                                        </div>
                                        <span class="status-chip bg-warning text-dark">{{ $counts['pending'] ?? 0 }}</span>
                                    </div>
                                </button>
                            </li>
                            <li class="nav-item flex-fill">
                                <button class="nav-link tab-pill flex-fill" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved-payments" type="button" role="tab" aria-controls="approved-payments" aria-selected="false">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="fw-semibold text-dark">Approved Payments</div>
                                            <small class="text-muted">Successfully processed payments</small>
                                        </div>
                                        <span class="status-chip bg-success text-white">{{ $counts['approved'] ?? 0 }}</span>
                                    </div>
                                </button>
                            </li>
                            <li class="nav-item flex-fill">
                                <button class="nav-link tab-pill flex-fill" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected-payments" type="button" role="tab" aria-controls="rejected-payments" aria-selected="false">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="fw-semibold text-dark">Rejected Payments</div>
                                            <small class="text-muted">Payments sent back for correction</small>
                                        </div>
                                        <span class="status-chip bg-danger text-white">{{ $counts['rejected'] ?? 0 }}</span>
                                    </div>
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="paymentTabsContent">
                            <!-- Pending Payments Tab -->
                            <div class="tab-pane fade show active" id="pending-payments" role="tabpanel" aria-labelledby="pending-tab">
                                <div class="row align-items-center mb-3">
                                    <div class="col-md-6">
                                    </div>
                                    <div class="col-md-6 text-md-end text-start mt-2 mt-md-0">
                                        <a href="{{ route('admin.payments.export-pdf', array_merge(request()->query(), ['status' => 'pending'])) }}" class="btn btn-danger btn-sm shadow-sm hover-elevate">
                                            <i class="ti ti-file-type-pdf me-1"></i>Export PDF
                                        </a>
                                    </div>
                                </div>
                                <div class="table-responsive border rounded-3 shadow-sm bg-white">
                                    <table class="table table-hover align-middle data_table_basic mb-0" id="pendingPaymentsTable" data-order='[[0,"asc"]]' data-page-length="25">
                                    <thead class="table-light">
                                        <tr>
                                            <th>SL No</th>
                                            <th>Requested On</th>
                                            <th>Payment Date</th>
                                            <th>Invoice #</th>
                                            <th>Converted Student</th>
                                            <th>Course / Batch</th>
                                            <th class="text-end">Amount</th>
                                            <th>Fee Head</th>
                                            <th>Payment Type</th>
                                            <th>Transaction ID</th>
                                            <th>Created By</th>
                                            <th>Approved By</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pendingPayments as $index => $payment)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-semibold">{{ $payment->created_at->format('M d, Y') }}</span>
                                                    <small class="text-muted">{{ $payment->created_at->format('h:i A') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-semibold">{{ $payment->payment_date ? $payment->payment_date->format('M d, Y') : '-' }}</span>
                                                    @if($payment->payment_date)
                                                    <small class="text-muted">Payment Date</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.invoices.show', $payment->invoice_id) }}" class="fw-semibold text-decoration-none">
                                                    {{ $payment->invoice->invoice_number ?? 'N/A' }}
                                                </a>
                                            </td>
                                            <td>
                                                <div class="fw-semibold">
                                                    {{ $payment->invoice && $payment->invoice->student ? ($payment->invoice->student->name ?? $payment->invoice->student->lead->title ?? 'N/A') : 'N/A' }}
                                                </div>
                                                <small class="text-muted">{{ $payment->invoice && $payment->invoice->student ? ($payment->invoice->student->code ?? '') : '' }} {{ $payment->invoice && $payment->invoice->student ? ($payment->invoice->student->phone ?? '') : '' }}</small>
                                            </td>
                                            <td>
                                                @if($payment->invoice && $payment->invoice->invoice_type === 'course')
                                                    <div>{{ $payment->invoice->course->title ?? 'N/A' }}</div>
                                                    @if($payment->invoice->batch)
                                                        <small class="text-muted">{{ $payment->invoice->batch->title }}</small>
                                                    @endif
                                                @elseif($payment->invoice && $payment->invoice->invoice_type === 'batch_change' || ($payment->invoice && $payment->invoice->invoice_type === 'batch_postpond'))
                                                    <div>{{ $payment->invoice->batch->title ?? 'N/A' }}</div>
                                                    <small class="text-muted">{{ $payment->invoice->batch->course->title ?? 'N/A' }}</small>
                                                @elseif($payment->invoice && $payment->invoice->invoice_type === 'e-service')
                                                    <div>{{ $payment->invoice->service_name ?? 'N/A' }}</div>
                                                @elseif($payment->invoice && $payment->invoice->invoice_type === 'fine')
                                                    <div>{{ $payment->invoice->service_name ?? 'N/A' }}</div>
                                                    <small class="text-muted">Fine</small>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-bold text-success">₹{{ number_format(round($payment->amount_paid)) }}</span>
                                            </td>
                                            <td>
                                                @if($payment->fee_head)
                                                    <span class="badge bg-primary">{{ $payment->fee_head }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $payment->payment_type }}</td>
                                            <td>
                                                @include('admin.payments.partials.transaction-ids-display', ['payment' => $payment])
                                            </td>
                                            <td>{{ optional($payment->createdBy)->name ?: (optional($payment->collectedBy)->name ?: (optional($payment->approvedBy)->name ?: (optional($payment->rejectedBy)->name ?: 'N/A')) ) }}</td>
                                            <td>{{ optional($payment->approvedBy)->name ?: (optional($payment->createdBy)->name ?: 'N/A') }}</td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-outline-info" title="View Payment">
                                                        <i class="ti ti-eye"></i>
                                                    </a>
                                                    @include('admin.payments.partials.proof-files-compact', ['payment' => $payment])
                                                    @if($canApprovePayments)
                                                    <button type="button"
                                                        class="btn btn-success"
                                                        title="Approve Payment"
                                                        data-payment-id="{{ $payment->id }}"
                                                        data-amount="{{ (float) $payment->amount_paid }}"
                                                        data-previous-balance="{{ (float) $payment->previous_balance }}"
                                                        data-payment-type="{{ $payment->payment_type }}"
                                                        data-transaction-id="{{ $payment->transaction_id ?? '' }}"
                                                        data-file-upload="{{ $payment->file_upload ?? '' }}"
                                                        onclick="handleApproveClick(this)">
                                                        <i class="ti ti-check"></i>
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-danger"
                                                        title="Reject Payment"
                                                        data-payment-id="{{ $payment->id }}"
                                                        data-amount="{{ (float) $payment->amount_paid }}"
                                                        data-payment-type="{{ $payment->payment_type }}"
                                                        onclick="handleRejectClick(this)">
                                                        <i class="ti ti-x"></i>
                                                    </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="12" class="text-center py-4">
                                                <i class="ti ti-circle-check text-success me-2"></i>No pending payments. Great job!
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="approved-payments" role="tabpanel" aria-labelledby="approved-tab">
                                <div class="row align-items-center mb-3">
                                    <div class="col-md-6">
                                    </div>
                                    <div class="col-md-6 text-md-end text-start mt-2 mt-md-0">
                                        <a href="{{ route('admin.payments.export-pdf', array_merge(request()->query(), ['status' => 'approved'])) }}" class="btn btn-danger btn-sm shadow-sm hover-elevate">
                                            <i class="ti ti-file-type-pdf me-1"></i>Export PDF
                                        </a>
                                    </div>
                                </div>
                                <div class="table-responsive border rounded-3 shadow-sm bg-white">
                                    <table class="table table-hover align-middle data_table_basic mb-0" id="approvedPaymentsTable" data-order='[[0,"asc"]]' data-page-length="25">
                                    <thead class="table-light">
                                        <tr>
                                            <th>SL No</th>
                                            <th>Approved On</th>
                                            <th>Payment Date</th>
                                            <th>Invoice #</th>
                                            <th>Converted Student</th>
                                            <th>Course / Batch</th>
                                            <th class="text-end">Amount</th>
                                            <th>Fee Head</th>
                                            <th>Payment Type</th>
                                            <th>Transaction ID</th>
                                            <th>Created By</th>
                                            <th>Approved By</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($approvedPayments as $index => $payment)
                                        @php
                                            $firstApprovedPayment = $payment->invoice ? optional($payment->invoice->payments->first())->id : null;
                                            $isFirstApprovedPayment = $firstApprovedPayment === $payment->id;
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-semibold">{{ optional($payment->approved_date)->format('M d, Y') ?? 'N/A' }}</span>
                                                    @if($payment->approved_date)
                                                    <small class="text-muted">{{ $payment->approved_date->format('h:i A') }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-semibold">{{ $payment->payment_date ? $payment->payment_date->format('M d, Y') : '-' }}</span>
                                                    @if($payment->payment_date)
                                                    <small class="text-muted">Payment Date</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.invoices.show', $payment->invoice_id) }}" class="fw-semibold text-decoration-none">
                                                    {{ $payment->invoice->invoice_number ?? 'N/A' }}
                                                </a>
                                            </td>
                                            <td>
                                                <div class="fw-semibold">
                                                    {{ $payment->invoice && $payment->invoice->student ? ($payment->invoice->student->name ?? $payment->invoice->student->lead->title ?? 'N/A') : 'N/A' }}
                                                </div>
                                                <small class="text-muted">{{ $payment->invoice && $payment->invoice->student ? ($payment->invoice->student->code ?? '') : '' }} {{ $payment->invoice && $payment->invoice->student ? ($payment->invoice->student->phone ?? '') : '' }}</small>
                                            </td>
                                            <td>
                                                @if($payment->invoice && $payment->invoice->invoice_type === 'course')
                                                    <div>{{ $payment->invoice->course->title ?? 'N/A' }}</div>
                                                    @if($payment->invoice->batch)
                                                        <small class="text-muted">{{ $payment->invoice->batch->title }}</small>
                                                    @endif
                                                @elseif($payment->invoice && ($payment->invoice->invoice_type === 'batch_change' || $payment->invoice->invoice_type === 'batch_postpond'))
                                                    <div>{{ $payment->invoice->batch->title ?? 'N/A' }}</div>
                                                    <small class="text-muted">{{ $payment->invoice->batch->course->title ?? 'N/A' }}</small>
                                                @elseif($payment->invoice && $payment->invoice->invoice_type === 'e-service')
                                                    <div>{{ $payment->invoice->service_name ?? 'N/A' }}</div>
                                                @elseif($payment->invoice && $payment->invoice->invoice_type === 'fine')
                                                    <div>{{ $payment->invoice->service_name ?? 'N/A' }}</div>
                                                    <small class="text-muted">Fine</small>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-bold text-success">₹{{ number_format(round($payment->amount_paid)) }}</span>
                                            </td>
                                            <td>
                                                @if($payment->fee_head)
                                                    <span class="badge bg-primary">{{ $payment->fee_head }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $payment->payment_type }}</td>
                                            <td>
                                                @include('admin.payments.partials.transaction-ids-display', ['payment' => $payment])
                                            </td>
                                            <td>{{ optional($payment->createdBy)->name ?: (optional($payment->collectedBy)->name ?: (optional($payment->approvedBy)->name ?: (optional($payment->rejectedBy)->name ?: 'N/A')) ) }}</td>
                                            <td>{{ optional($payment->approvedBy)->name ?: (optional($payment->createdBy)->name ?: 'N/A') }}</td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-outline-info" title="View Payment">
                                                        <i class="ti ti-eye"></i>
                                                    </a>
                                                    @include('admin.payments.partials.proof-files-compact', ['payment' => $payment])
                                                    @if($payment->invoice && $payment->invoice->invoice_type === 'course' && $isFirstApprovedPayment)
                                                    <a href="{{ route('admin.payments.tax-invoice-pdf', $payment->id) }}" class="btn btn-outline-danger" title="Tax Invoice PDF" target="_blank">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                    @else
                                                    <a href="{{ route('admin.payments.payment-receipt-pdf', $payment->id) }}" class="btn btn-outline-danger" title="Payment Receipt PDF" target="_blank">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="14" class="text-center py-4">
                                                <i class="ti ti-info-circle text-muted me-2"></i>No approved payments yet.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="rejected-payments" role="tabpanel" aria-labelledby="rejected-tab">
                                <div class="row align-items-center mb-3">
                                    <div class="col-md-6">
                                    </div>
                                    <div class="col-md-6 text-md-end text-start mt-2 mt-md-0">
                                        <a href="{{ route('admin.payments.export-pdf', array_merge(request()->query(), ['status' => 'rejected'])) }}" class="btn btn-danger btn-sm shadow-sm hover-elevate">
                                            <i class="ti ti-file-type-pdf me-1"></i>Export PDF
                                        </a>
                                    </div>
                                </div>
                                <div class="table-responsive border rounded-3 shadow-sm bg-white">
                                    <table class="table table-hover align-middle data_table_basic mb-0" id="rejectedPaymentsTable" data-order='[[0,"asc"]]' data-page-length="25">
                                    <thead class="table-light">
                                        <tr>
                                            <th>SL No</th>
                                            <th>Rejected On</th>
                                            <th>Payment Date</th>
                                            <th>Invoice #</th>
                                            <th>Converted Student</th>
                                            <th>Course / Batch</th>
                                            <th class="text-end">Amount</th>
                                            <th>Fee Head</th>
                                            <th>Payment Type</th>
                                            <th>Transaction ID</th>
                                            <th>Created By</th>
                                            <th>Rejected By</th>
                                            <th>Remarks</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($rejectedPayments as $index => $payment)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-semibold">{{ optional($payment->rejected_date)->format('M d, Y') ?? 'N/A' }}</span>
                                                    @if($payment->rejected_date)
                                                    <small class="text-muted">{{ $payment->rejected_date->format('h:i A') }}</small>
                                                    @endif
                                                    @if($payment->rejection_remarks)
                                                    <div class="mt-1">
                                                        <small class="text-danger">
                                                            <i class="ti ti-message-circle me-1"></i>
                                                            <strong>Remarks:</strong> {{ $payment->rejection_remarks }}
                                                        </small>
                                                    </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-semibold">{{ $payment->payment_date ? $payment->payment_date->format('M d, Y') : '-' }}</span>
                                                    @if($payment->payment_date)
                                                    <small class="text-muted">Payment Date</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.invoices.show', $payment->invoice_id) }}" class="fw-semibold text-decoration-none">
                                                    {{ $payment->invoice->invoice_number ?? 'N/A' }}
                                                </a>
                                            </td>
                                            <td>
                                                <div class="fw-semibold">
                                                    {{ $payment->invoice && $payment->invoice->student ? ($payment->invoice->student->name ?? $payment->invoice->student->lead->title ?? 'N/A') : 'N/A' }}
                                                </div>
                                                <small class="text-muted">{{ $payment->invoice && $payment->invoice->student ? ($payment->invoice->student->code ?? '') : '' }} {{ $payment->invoice && $payment->invoice->student ? ($payment->invoice->student->phone ?? '') : '' }}</small>
                                            </td>
                                            <td>
                                                @if($payment->invoice && $payment->invoice->invoice_type === 'course')
                                                    <div>{{ $payment->invoice->course->title ?? 'N/A' }}</div>
                                                    @if($payment->invoice->batch)
                                                        <small class="text-muted">{{ $payment->invoice->batch->title }}</small>
                                                    @endif
                                                @elseif($payment->invoice && ($payment->invoice->invoice_type === 'batch_change' || $payment->invoice->invoice_type === 'batch_postpond'))
                                                    <div>{{ $payment->invoice->batch->title ?? 'N/A' }}</div>
                                                    <small class="text-muted">{{ $payment->invoice->batch->course->title ?? 'N/A' }}</small>
                                                @elseif($payment->invoice && $payment->invoice->invoice_type === 'e-service')
                                                    <div>{{ $payment->invoice->service_name ?? 'N/A' }}</div>
                                                @elseif($payment->invoice && $payment->invoice->invoice_type === 'fine')
                                                    <div>{{ $payment->invoice->service_name ?? 'N/A' }}</div>
                                                    <small class="text-muted">Fine</small>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-bold text-danger">₹{{ number_format(round($payment->amount_paid)) }}</span>
                                            </td>
                                            <td>
                                                @if($payment->fee_head)
                                                    <span class="badge bg-primary">{{ $payment->fee_head }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $payment->payment_type }}</td>
                                            <td>
                                                @include('admin.payments.partials.transaction-ids-display', ['payment' => $payment])
                                            </td>
                                            <td>{{ optional($payment->createdBy)->name ?: (optional($payment->collectedBy)->name ?: (optional($payment->approvedBy)->name ?: (optional($payment->rejectedBy)->name ?: 'N/A')) ) }}</td>
                                            <td>{{ $payment->rejectedBy->name ?? 'N/A' }}</td>
                                            <td>
                                                @if($payment->rejection_remarks)
                                                    <div class="text-danger small">
                                                        <i class="ti ti-message-circle me-1"></i>
                                                        {{ \Illuminate\Support\Str::limit($payment->rejection_remarks, 50) }}
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-outline-info" title="View Payment">
                                                        <i class="ti ti-eye"></i>
                                                    </a>
                                                    @include('admin.payments.partials.proof-files-compact', ['payment' => $payment])
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="14" class="text-center py-4">
                                                <i class="ti ti-alert-triangle text-danger me-2"></i>No rejected payments.
                                            </td>
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
                    <div class="alert alert-info d-flex align-items-center">
                        <i class="ti ti-info-circle me-2"></i>
                        <span>Please review the payment details before approving.</span>
                    </div>
                    <div class="mb-2">
                        <strong>Amount:</strong>
                        <div id="approveAmount">-</div>
                    </div>
                    <div class="mb-2">
                        <strong>Previous Approved Total:</strong>
                        <div id="approvePreviousBalance">-</div>
                    </div>
                    <div class="mb-2">
                        <strong>Payment Type:</strong>
                        <div id="approvePaymentType">-</div>
                    </div>
                    <div class="mb-2">
                        <strong>Transaction ID:</strong>
                        <input type="text" class="form-control mt-1" id="approveTransactionId" name="transaction_id" placeholder="Enter transaction ID" value="">
                    </div>
                    <div class="mb-2">
                        <strong>Receipt / Proof:</strong>
                        <div id="approveFile">-</div>
                    </div>
                    <p class="text-muted mt-3 mb-0">
                        <i class="ti ti-alert-circle me-1"></i>Once approved, this payment will update the invoice totals and cannot be undone.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="ti ti-check me-1"></i>Approve Payment
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
                <div class="alert alert-warning d-flex align-items-center">
                    <i class="ti ti-alert-triangle me-2"></i>
                    <span>Are you sure you want to reject this payment?</span>
                </div>
                <div class="mb-2">
                    <strong>Amount:</strong>
                    <div id="rejectAmount">-</div>
                </div>
                <div class="mb-2">
                    <strong>Payment Type:</strong>
                    <div id="rejectPaymentType">-</div>
                </div>
                <hr>
                <div class="mb-3">
                    <label for="rejectionRemarks" class="form-label">
                        <strong>Remarks <span class="text-danger">*</span></strong>
                    </label>
                    <textarea class="form-control" id="rejectionRemarks" name="rejection_remarks" rows="3" placeholder="Enter rejection remarks..." required></textarea>
                    <small class="form-text text-muted">Please provide a reason for rejecting this payment.</small>
                </div>
                <p class="text-muted mt-3 mb-0">
                    <i class="ti ti-info-circle me-1"></i>Rejected payments will not affect the invoice totals.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="rejectPaymentForm" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="rejection_remarks" id="rejectionRemarksInput">
                    <button type="submit" class="btn btn-danger">
                        <i class="ti ti-x me-1"></i>Reject Payment
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function formatCurrency(amount) {
        const numericAmount = parseFloat(amount ?? 0);
        return '₹' + numericAmount.toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function handleApproveClick(button) {
        const { paymentId, amount, previousBalance, paymentType, transactionId, fileUpload } = button.dataset;
        showApproveModal(paymentId, amount, previousBalance, paymentType, transactionId, fileUpload);
    }

    function handleRejectClick(button) {
        const { paymentId, amount, paymentType } = button.dataset;
        showRejectModal(paymentId, amount, paymentType);
    }

    function showApproveModal(paymentId, amount, previousBalance, paymentType, transactionId, fileUpload) {
        document.getElementById('approveAmount').textContent = formatCurrency(amount);
        document.getElementById('approvePreviousBalance').textContent = formatCurrency(previousBalance);
        document.getElementById('approvePaymentType').textContent = paymentType || 'N/A';
        document.getElementById('approveTransactionId').value = transactionId || '';

        const approveFile = document.getElementById('approveFile');
        if (fileUpload) {
            const fileUrl = '{{ route("admin.payments.view", ":id") }}'.replace(':id', paymentId);
            approveFile.innerHTML = `<a href="${fileUrl}" target="_blank" class="btn btn-sm btn-outline-primary">
                <i class="ti ti-eye me-1"></i>View Receipt
            </a>`;
        } else {
            approveFile.innerHTML = '<span class="text-muted">No file uploaded</span>';
        }

        document.getElementById('approvePaymentForm').action = '{{ route("admin.payments.approve", ":id") }}'.replace(':id', paymentId);

        const modal = new bootstrap.Modal(document.getElementById('approvePaymentModal'), {
            backdrop: 'static',
            keyboard: false
        });
        modal.show();
    }

    function showRejectModal(paymentId, amount, paymentType) {
        document.getElementById('rejectAmount').textContent = formatCurrency(amount);
        document.getElementById('rejectPaymentType').textContent = paymentType || 'N/A';
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

    document.addEventListener('DOMContentLoaded', function () {
        const paymentTabs = document.getElementById('paymentTabs');
        const tabTriggerElements = paymentTabs ? paymentTabs.querySelectorAll('button[data-bs-toggle="tab"]') : [];
        const activeTabInput = document.getElementById('active_tab');
        const initialTab = activeTabInput ? activeTabInput.value : null;
        const storedTab = localStorage.getItem('payments_active_tab');
        const targetTab = storedTab || initialTab;

        if (targetTab) {
            const triggerEl = document.querySelector(`button[data-bs-target="${targetTab}"]`);
            if (triggerEl) {
                const tab = new bootstrap.Tab(triggerEl);
                tab.show();
                if (activeTabInput) {
                    activeTabInput.value = targetTab;
                }
            }
        }

        tabTriggerElements.forEach(function (triggerEl) {
            triggerEl.addEventListener('shown.bs.tab', function (event) {
                const target = event.target.getAttribute('data-bs-target');
                localStorage.setItem('payments_active_tab', target);
                if (activeTabInput) {
                    activeTabInput.value = target;
                }
            });
        });
    });
</script>
@endpush
@endsection

@push('styles')
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .table-hover tbody tr:hover {
        background-color: rgba(102, 126, 234, 0.05);
    }

    code {
        font-size: 0.85rem;
    }

    .nav-pills .tab-pill {
        border-radius: 18px;
        border: 1px solid rgba(13, 110, 253, 0.12);
        background: #ffffff;
        color: #6c757d;
        padding: 1.2rem 1.4rem;
        transition: all 0.2s ease;
        text-align: left;
    }

    .nav-pills .tab-pill:hover {
        border-color: rgba(13, 110, 253, 0.4);
        color: #0d6efd;
        box-shadow: 0 12px 20px -14px rgba(13, 110, 253, 0.45);
    }

    .nav-pills .tab-pill.active,
    .nav-pills .tab-pill.active:hover {
        background: linear-gradient(135deg, rgba(13, 110, 253, 0.12) 0%, #ffffff 90%) !important;
        border-color: rgba(13, 110, 253, 0.45) !important;
        color: #0d6efd !important;
        box-shadow: 0 16px 32px -20px rgba(13, 110, 253, 0.6);
    }

    .nav-pills .tab-pill small {
        transition: color 0.2s ease;
    }

    .nav-pills .tab-pill.active small {
        color: rgba(13, 110, 253, 0.75) !important;
    }

    .status-chip {
        min-width: 54px;
        text-align: center;
        border-radius: 999px;
        font-weight: 600;
        padding: 0.3rem 1rem;
        box-shadow: 0 4px 16px -12px rgba(13, 110, 253, 0.55);
    }

    .nav-pills .tab-pill.active .status-chip {
        box-shadow: 0 12px 28px -18px rgba(13, 110, 253, 0.65);
    }
</style>
@endpush

