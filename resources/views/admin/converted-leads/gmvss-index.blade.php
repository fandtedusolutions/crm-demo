@extends('layouts.mantis')

@section('title', 'Grameen Mukt Vidhyalayi Shiksha Sansthan Converted Leads')

@section('content')
<style>
    .table td {
        white-space: nowrap;
        vertical-align: middle;
    }

    .table td .btn-group {
        white-space: nowrap;
    }

    .table td .inline-edit {
        white-space: nowrap;
    }

    .table td .display-value {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
        display: inline-block;
    }

    .cancelled-row>td {
        background-color: #fff1f0 !important;
    }

    .cancelled-card {
        border: 1px solid #f5c2c7;
        background-color: #fff5f5;
    }
</style>
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Grameen Mukt Vidhyalayi Shiksha Sansthan Converted Leads Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.converted-leads.index') }}">Converted Leads</a></li>
                    <li class="breadcrumb-item">Grameen Mukt Vidhyalayi Shiksha Sansthan</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

@include('admin.converted-leads.partials.converted-leads-course-nav')

@include('admin.converted-leads.partials.mentor-list-nav', ['activeMentorRoute' => $activeMentorRoute ?? null])

@include('admin.converted-leads.partials.faculty-list-nav', ['activeFacultyRoute' => $activeFacultyRoute ?? null])

@include('admin.converted-leads.partials.converted-leads-support-nav')

<!-- [ Filter Section ] start -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.gmvss-converted-leads.index') }}" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search"
                                value="{{ request('search') }}" placeholder="Name, Phone, Email, Register Number">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="batch_id" class="form-label">Batch</label>
                            <select class="form-select" id="batch_id" name="batch_id" data-selected="{{ request('batch_id') }}">
                                <option value="">All Batches</option>
                                @foreach($batches as $batch)
                                <option value="{{ $batch->id }}" {{ request('batch_id') == $batch->id ? 'selected' : '' }}>
                                    {{ $batch->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="admission_batch_id" class="form-label">Admission Batch</label>
                            <select class="form-select" id="admission_batch_id" name="admission_batch_id" data-selected="{{ request('admission_batch_id') }}">
                                <option value="">All Admission Batches</option>
                            </select>
                        </div>
                        @include('admin.converted-leads.partials.course-flag-filter-field')

                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from"
                                value="{{ request('date_from') }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="date_to" name="date_to"
                                value="{{ request('date_to') }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All</option>
                                <option value="Paid" {{ request('status')==='Paid' ? 'selected' : '' }}>Paid</option>
                                <option value="Admission cancel" {{ request('status')==='Admission cancel' ? 'selected' : '' }}>Admission cancel</option>
                                <option value="Active" {{ request('status')==='Active' ? 'selected' : '' }}>Active</option>
                                <option value="Inactive" {{ request('status')==='Inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="registration_link_id" class="form-label">Registration Link</label>
                            <select class="form-select" id="registration_link_id" name="registration_link_id">
                                <option value="">All</option>
                                @foreach($registration_links as $link)
                                <option value="{{ $link->id }}" {{ request('registration_link_id') == $link->id ? 'selected' : '' }}>
                                    {{ $link->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="certificate_status" class="form-label">Certificate</label>
                            <select class="form-select" id="certificate_status" name="certificate_status">
                                <option value="">All</option>
                                <option value="In Progress" {{ request('certificate_status')==='In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Online Result Not Arrived" {{ request('certificate_status')==='Online Result Not Arrived' ? 'selected' : '' }}>Online Result Not Arrived</option>
                                <option value="One Result Arrived" {{ request('certificate_status')==='One Result Arrived' ? 'selected' : '' }}>One Result Arrived</option>
                                <option value="Certificate Arrived" {{ request('certificate_status')==='Certificate Arrived' ? 'selected' : '' }}>Certificate Arrived</option>
                                <option value="Not Received" {{ request('certificate_status')==='Not Received' ? 'selected' : '' }}>Not Received</option>
                                <option value="No Admission" {{ request('certificate_status')==='No Admission' ? 'selected' : '' }}>No Admission</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-search"></i> <span class="d-none d-sm-inline">Filter</span>
                                </button>
                                <a href="{{ route('admin.gmvss-converted-leads.index') }}" class="btn btn-secondary">
                                    <i class="ti ti-x"></i> <span class="d-none d-sm-inline">Clear</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- [ Filter Section ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Grameen Mukt Vidhyalayi Shiksha Sansthan Converted Leads List</h5>
            </div>
            <div class="card-body">
                <!-- Desktop Table View -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover" id="convertedLeadsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Academic</th>
                                    <th>Support</th>
                                    <th>Registration Number</th>
                                    <th>Course Flag</th>
                                    <th>Converted Date</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Phone</th>
                                    <th>WhatsApp</th>
                                    @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                    <th>Parent Phone</th>
                                    @endif
                                    <th>Batch</th>
                                    <th>Admission Batch</th>
                                    <th>Class</th>
                                    <th>Mail</th>
                                    <th>Course</th>
                                    <th>Passed Year</th>
                                    <th>Enrollment Number</th>
                                    <th>Registration Link</th>
                                    <th>Certificate</th>
                                    <th>Certificate Received Date</th>
                                    <th>Certificate Issued Date</th>
                                    <th>Remarks</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($convertedLeads as $index => $convertedLead)
                                <tr class="{{ $convertedLead->is_cancelled ? 'cancelled-row' : '' }}">
                                    @php
                                    $canToggleAcademic = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor();
                                    $canToggleSupport = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_support_team();
                                    @endphp
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @include('admin.converted-leads.partials.status-badge', [
                                        'convertedLead' => $convertedLead,
                                        'type' => 'academic',
                                        'showToggle' => $canToggleAcademic,
                                        'toggleUrl' => $canToggleAcademic ? route('admin.converted-leads.toggle-academic-verify', $convertedLead->id) : null,
                                        'title' => 'academic',
                                        'useModal' => true
                                        ])
                                    </td>
                                    <td>
                                        @include('admin.converted-leads.partials.status-badge', [
                                        'convertedLead' => $convertedLead,
                                        'type' => 'support',
                                        'showToggle' => $canToggleSupport,
                                        'toggleUrl' => $canToggleSupport ? route('admin.support-converted-leads.toggle-support-verify', $convertedLead->id) : null,
                                        'title' => 'support',
                                        'useModal' => true
                                        ])
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="register_number" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->register_number }}">
                                            @if($convertedLead->register_number)
                                            <span class="badge bg-success"><span class="display-value">{{ $convertedLead->register_number }}</span></span>
                                            @else
                                            <span class="display-value text-muted">Not Set</span>
                                            @endif
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $convertedLead->created_at->format('d-m-Y') }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avtar avtar-s rounded-circle bg-light-success me-2 d-flex align-items-center justify-content-center">
                                                <span class="f-16 fw-bold text-success">{{ strtoupper(substr($convertedLead->name, 0, 1)) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $convertedLead->name }}</h6>
                                                <small class="text-muted">ID: {{ $convertedLead->lead_id }}</small>
                                                @if($convertedLead->is_cancelled)
                                                <div>
                                                    <span class="badge bg-danger mt-1">Cancelled</span>
                                                    @if($convertedLead->cancelledBy)
                                                        <br><small class="text-muted mt-1 d-block">By: {{ $convertedLead->cancelledBy->name }}
                                                        @if($convertedLead->cancelled_at)
                                                            <br>{{ $convertedLead->cancelled_at->format('d-m-Y h:i A') }}
                                                        @endif
                                                        </small>
                                                    @endif
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $convertedLead->is_b2b == 1 ? ('B2B' . ($convertedLead->lead?->team?->name ? ' (' . $convertedLead->lead->team->name . ')' : '')) : 'In House' }}</td>
                                    <td>
                                        <div class="inline-edit" data-field="phone" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->phone }}">
                                            <span class="display-value">{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                        <div class="d-none inline-code-value" data-field="code" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->code }}"></div>
                                    </td>
                                    <td>
                                        @if($convertedLead->leadDetail && $convertedLead->leadDetail->whatsapp_number)
                                            {{ \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->whatsapp_code, $convertedLead->leadDetail->whatsapp_number) }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                    <td>
                                        @if($convertedLead->leadDetail && $convertedLead->leadDetail->parents_number)
                                            {{ \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->parents_code, $convertedLead->leadDetail->parents_number) }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    @endif
                                    <td>
                                        <div class="inline-edit" data-field="batch_id" data-id="{{ $convertedLead->id }}" data-course-id="{{ $convertedLead->course_id }}" data-current-id="{{ $convertedLead->batch_id }}">
                                            <span class="display-value">{{ $convertedLead->batch ? $convertedLead->batch->title : 'N/A' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="admission_batch_id" data-id="{{ $convertedLead->id }}" data-batch-id="{{ $convertedLead->batch_id }}" data-current-id="{{ $convertedLead->admission_batch_id }}">
                                            <span class="display-value">{{ $convertedLead->admissionBatch ? $convertedLead->admissionBatch->title : 'N/A' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="class" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->leadDetail?->class }}">
                                            <span class="display-value">
                                                @if($convertedLead->leadDetail?->class)
                                                    {{ $convertedLead->leadDetail->class === 'sslc' ? 'SSLC' : ($convertedLead->leadDetail->class === 'plustwo' ? 'Plus Two' : $convertedLead->leadDetail->class) }}
                                                @else
                                                    -
                                                @endif
                                            </span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $convertedLead->email ?? 'N/A' }}</td>
                                    <td>{{ $convertedLead->course ? $convertedLead->course->title : 'N/A' }}</td>
                                    <td>{{ $convertedLead->lead?->studentDetails?->passed_year ?? 'N/A' }}</td>
                                    <td>
                                        <div class="inline-edit" data-field="enroll_no" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->enroll_no }}">
                                            <span class="display-value">{{ $convertedLead->enroll_no ?? 'N/A' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                        $currentRegistrationLink = optional($convertedLead->studentDetails)->registrationLink;
                                        $registrationLinkColor = optional($currentRegistrationLink)->color_code;
                                        ?>
                                        <div class="inline-edit" data-field="registration_link_id" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->registration_link_id }}">
                                            <span class="display-value fw-semibold" style="<?php echo e($registrationLinkColor ? 'color: ' . $registrationLinkColor . ';' : ''); ?>">
                                                {{ $currentRegistrationLink?->title ?? 'N/A' }}
                                            </span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_finance())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="certificate_status" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->certificate_status }}">
                                            <span class="display-value">{{ $convertedLead->studentDetails?->certificate_status ?? 'N/A' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="certificate_received_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->certificate_received_date }}">
                                            @php
                                            $receivedDate = $convertedLead->studentDetails?->certificate_received_date ? (strtotime($convertedLead->studentDetails->certificate_received_date) ? date('d-m-Y', strtotime($convertedLead->studentDetails->certificate_received_date)) : $convertedLead->studentDetails->certificate_received_date) : 'N/A';
                                            @endphp
                                            <span class="display-value">{{ $receivedDate }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="certificate_issued_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->certificate_issued_date }}">
                                            @php
                                            $issuedDate = $convertedLead->studentDetails?->certificate_issued_date ? (strtotime($convertedLead->studentDetails->certificate_issued_date) ? date('d-m-Y', strtotime($convertedLead->studentDetails->certificate_issued_date)) : $convertedLead->studentDetails->certificate_issued_date) : 'N/A';
                                            @endphp
                                            <span class="display-value">{{ $issuedDate }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="remarks" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->remarks }}">
                                            <span class="display-value">{{ $convertedLead->studentDetails?->remarks ?? 'N/A' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="" role="group">
                                            <a href="{{ route('admin.converted-leads.show', $convertedLead->id) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.invoices.index', $convertedLead->id) }}" class="btn btn-sm btn-success" title="View Invoice">
                                                <i class="ti ti-receipt"></i>
                                            </a>
                                            @php
                                            $canManageCancelFlag = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor();
                                            @endphp
                                            @if($canManageCancelFlag)
                                            @php
                                            $cancelBtnClass = $convertedLead->is_cancelled ? 'btn-danger' : 'btn-outline-danger';
                                            $cancelBtnTitle = $convertedLead->is_cancelled ? 'Update cancellation confirmation' : 'Confirm cancellation';
                                            @endphp
                                            <button type="button" class="btn btn-sm {{ $cancelBtnClass }} js-cancel-flag" title="{{ $cancelBtnTitle }}"
                                                data-cancel-url="{{ route('admin.converted-leads.cancel-flag', $convertedLead->id) }}"
                                                data-modal-title="Cancellation Confirmation">
                                                <i class="ti ti-ban"></i>
                                            </button>
                                            @endif
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_support_team())
                                            <button type="button" class="btn btn-sm btn-info update-register-btn" title="Update Register Number"
                                                data-url="{{ route('admin.converted-leads.update-register-number-modal', $convertedLead->id) }}"
                                                data-title="Update Register Number">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @php $courseChanged = (bool) ($convertedLead->is_course_changed ?? false); @endphp
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                            <button type="button" class="btn btn-sm {{ $courseChanged ? 'btn-success' : 'btn-danger' }} js-change-course-modal"
                                                title="Change Course"
                                                data-modal-url="{{ route('admin.converted-leads.change-course-modal', $convertedLead->id) }}"
                                                data-modal-title="Change Course">
                                                <i class="ti ti-exchange"></i>
                                            </button>
                                            @endif
                                            @if($convertedLead->register_number)
                                            @php
                                            $idCard = \App\Models\ConvertedLeadIdCard::where('converted_lead_id', $convertedLead->id)->first();
                                            @endphp
                                            @if($idCard)
                                            <a href="{{ route('admin.converted-leads.id-card-view', $convertedLead->id) }}" class="btn btn-sm btn-success" title="View ID Card" target="_blank">
                                                <i class="ti ti-id"></i>
                                            </a>
                                            @else
                                            <form action="{{ route('admin.converted-leads.id-card-generate', $convertedLead->id) }}" method="post" style="display:inline-block" class="id-card-generate-form">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning" title="Generate ID Card" data-loading-text="Generating...">
                                                    <i class="ti ti-id"></i>
                                                </button>
                                            </form>
                                            @endif
                                            @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="20" class="text-center">No Grameen Mukt Vidhyalayi Shiksha Sansthan converted leads found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="d-lg-none">
                    @forelse($convertedLeads as $index => $convertedLead)
                    <div class="card mb-3 {{ $convertedLead->is_cancelled ? 'cancelled-card' : '' }}">
                        <div class="card-body">
                            @php
                            $canToggleAcademic = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor();
                            $canToggleSupport = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_support_team();
                            @endphp
                            <!-- Lead Header -->
                            <div class="d-flex align-items-center mb-3">
                                <div class="avtar avtar-s rounded-circle bg-light-success me-3 d-flex align-items-center justify-content-center">
                                    <span class="f-16 fw-bold text-success">{{ strtoupper(substr($convertedLead->name, 0, 1)) }}</span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">{{ $convertedLead->name }}</h6>
                                    @if($convertedLead->is_cancelled)
                                    <div>
                                        <span class="badge bg-danger ms-2">Cancelled</span>
                                        @if($convertedLead->cancelledBy)
                                            <br><small class="text-muted ms-2">By: {{ $convertedLead->cancelledBy->name }}
                                            @if($convertedLead->cancelled_at)
                                                ({{ $convertedLead->cancelled_at->format('d-m-Y h:i A') }})
                                            @endif
                                            </small>
                                        @endif
                                    </div>
                                    @endif
                                    <small class="text-muted">ID: {{ $convertedLead->lead_id }}</small>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.converted-leads.show', $convertedLead->id) }}">
                                                <i class="ti ti-eye me-2"></i>View Details
                                            </a>
                                        </li>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_finance() || \App\Helpers\RoleHelper::is_post_sales())
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.invoices.index', $convertedLead->id) }}">
                                                <i class="ti ti-receipt me-2"></i>View Invoice
                                            </a>
                                        </li>
                                        @endif
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                        <li>
                                            <a class="dropdown-item update-register-btn" href="#"
                                                data-url="{{ route('admin.converted-leads.update-register-number-modal', $convertedLead->id) }}"
                                                data-title="Update Register Number">
                                                <i class="ti ti-edit me-2"></i>Update Register Number
                                            </a>
                                        </li>
                                        @if($convertedLead->register_number)
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.converted-leads.id-card-pdf', $convertedLead->id) }}" target="_blank">
                                                <i class="ti ti-id me-2"></i>Generate ID Card PDF
                                            </a>
                                        </li>
                                        @endif
                                        @endif
                                    </ul>
                                </div>
                            </div>

                            <!-- Lead Details -->
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <small class="text-muted d-block">Phone</small>
                                    <span class="fw-medium">{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">WhatsApp</small>
                                    <span class="fw-medium">
                                        @if($convertedLead->leadDetail && $convertedLead->leadDetail->whatsapp_number)
                                            {{ \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->whatsapp_code, $convertedLead->leadDetail->whatsapp_number) }}
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </div>
                                @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                <div class="col-6">
                                    <small class="text-muted d-block">Parent Phone</small>
                                    <span class="fw-medium">
                                        @if($convertedLead->leadDetail && $convertedLead->leadDetail->parents_number)
                                            {{ \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->parents_code, $convertedLead->leadDetail->parents_number) }}
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </div>
                                @endif
                                <div class="col-6">
                                    <small class="text-muted d-block">Email</small>
                                    <span class="fw-medium">{{ $convertedLead->email ?? 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Course</small>
                                    <span class="fw-medium">{{ $convertedLead->course ? $convertedLead->course->title : 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Session</small>
                                    <span class="fw-medium">{{ $convertedLead->batch ? $convertedLead->batch->title : 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Class</small>
                                    <span class="fw-medium">
                                        @if($convertedLead->leadDetail?->class)
                                            {{ $convertedLead->leadDetail->class === 'sslc' ? 'SSLC' : ($convertedLead->leadDetail->class === 'plustwo' ? 'Plus Two' : $convertedLead->leadDetail->class) }}
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Registration Number</small>
                                    @if($convertedLead->register_number)
                                    <span class="badge bg-success">{{ $convertedLead->register_number }}</span>
                                    @else
                                    <span class="text-muted">Not Set</span>
                                    @endif
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Academic</small>
                                    @include('admin.converted-leads.partials.status-badge', [
                                    'convertedLead' => $convertedLead,
                                    'type' => 'academic',
                                    'showToggle' => $canToggleAcademic,
                                    'toggleUrl' => $canToggleAcademic ? route('admin.converted-leads.toggle-academic-verify', $convertedLead->id) : null
                                    ])
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Support</small>
                                    @include('admin.converted-leads.partials.status-badge', [
                                    'convertedLead' => $convertedLead,
                                    'type' => 'support',
                                    'showToggle' => $canToggleSupport,
                                    'toggleUrl' => $canToggleSupport ? route('admin.support-converted-leads.toggle-support-verify', $convertedLead->id) : null
                                    ])
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Converted Date</small>
                                    <span class="fw-medium">{{ $convertedLead->created_at->format('d-m-Y') }}</span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('admin.converted-leads.show', $convertedLead->id) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="ti ti-eye me-1"></i>View Details
                                </a>
                                @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_finance() || \App\Helpers\RoleHelper::is_post_sales())
                                <a href="{{ route('admin.invoices.index', $convertedLead->id) }}"
                                    class="btn btn-sm btn-success">
                                    <i class="ti ti-receipt me-1"></i>View Invoice
                                </a>
                                @endif
                                @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                <button type="button" class="btn btn-sm btn-info update-register-btn"
                                    data-url="{{ route('admin.converted-leads.update-register-number-modal', $convertedLead->id) }}"
                                    data-title="Update Register Number">
                                    <i class="ti ti-edit me-1"></i>Update Register
                                </button>
                                @if($convertedLead->register_number)
                                <a href="{{ route('admin.converted-leads.id-card-pdf', $convertedLead->id) }}"
                                    class="btn btn-sm btn-warning" target="_blank">
                                    <i class="ti ti-id me-1"></i>ID Card PDF
                                </a>
                                @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <div class="text-muted">
                            <i class="ti ti-check-circle f-48 mb-3 d-block"></i>
                            <h5>No Grameen Mukt Vidhyalayi Shiksha Sansthan converted leads found</h5>
                            <p>Try adjusting your filters or check back later.</p>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
@include('admin.converted-leads.partials.course-flag-inline-scripts')
@endsection

<script id="country-codes-json" type="application/json">
    {!! json_encode($country_codes ?? [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
</script>
<script id="registration-links-json" type="application/json">
    {!! json_encode(
        ($registration_links ?? collect())->map(function ($link) {
            return [
                'id' => $link->id,
                'title' => $link->title,
                'color_code' => $link->color_code,
            ];
        })->values(),
        JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
    ) !!}
</script>

@php
    $gmvssConvertedLeadsColumns = [
        ['data' => 'index', 'name' => 'index', 'orderable' => false, 'searchable' => false],
        ['data' => 'academic', 'name' => 'academic', 'orderable' => false, 'searchable' => false],
        ['data' => 'support', 'name' => 'support', 'orderable' => false, 'searchable' => false],
        ['data' => 'registration_number', 'name' => 'registration_number', 'orderable' => false, 'searchable' => false],
        ['data' => 'course_flag', 'name' => 'course_flag', 'orderable' => false, 'searchable' => false],
        ['data' => 'converted_date', 'name' => 'converted_date', 'orderable' => false, 'searchable' => false],
        ['data' => 'name_col', 'name' => 'name_col', 'orderable' => false, 'searchable' => false],
        ['data' => 'type', 'name' => 'type', 'orderable' => false, 'searchable' => false],
        ['data' => 'phone', 'name' => 'phone', 'orderable' => false, 'searchable' => false],
        ['data' => 'whatsapp', 'name' => 'whatsapp', 'orderable' => false, 'searchable' => false],
    ];

    if (\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor()) {
        $gmvssConvertedLeadsColumns[] = ['data' => 'parent_phone', 'name' => 'parent_phone', 'orderable' => false, 'searchable' => false];
    }

    $gmvssConvertedLeadsColumns = array_merge($gmvssConvertedLeadsColumns, [
        ['data' => 'batch', 'name' => 'batch', 'orderable' => false, 'searchable' => false],
        ['data' => 'admission_batch', 'name' => 'admission_batch', 'orderable' => false, 'searchable' => false],
        ['data' => 'class', 'name' => 'class', 'orderable' => false, 'searchable' => false],
        ['data' => 'mail', 'name' => 'mail', 'orderable' => false, 'searchable' => false],
        ['data' => 'course', 'name' => 'course', 'orderable' => false, 'searchable' => false],
        ['data' => 'passed_year', 'name' => 'passed_year', 'orderable' => false, 'searchable' => false],
        ['data' => 'enrollment_number', 'name' => 'enrollment_number', 'orderable' => false, 'searchable' => false],
        ['data' => 'registration_link', 'name' => 'registration_link', 'orderable' => false, 'searchable' => false],
        ['data' => 'certificate', 'name' => 'certificate', 'orderable' => false, 'searchable' => false],
        ['data' => 'certificate_received_date', 'name' => 'certificate_received_date', 'orderable' => false, 'searchable' => false],
        ['data' => 'certificate_issued_date', 'name' => 'certificate_issued_date', 'orderable' => false, 'searchable' => false],
        ['data' => 'remarks', 'name' => 'remarks', 'orderable' => false, 'searchable' => false],
        ['data' => 'actions', 'name' => 'actions', 'orderable' => false, 'searchable' => false],
    ]);
@endphp

<div id="gmvssConvertedLeadsConfig" data-data-url="{{ route('admin.gmvss-converted-leads.data') }}" style="display:none"></div>
<script type="application/json" id="gmvssConvertedLeadsColumnsData">{!! json_encode($gmvssConvertedLeadsColumns) !!}</script>

@push('styles')
<style>
    .spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .inline-edit {
        position: relative;
        overflow: visible;
    }

    .inline-edit .edit-form {
        display: none;
        position: absolute;
        top: 0;
        left: -8px;
        z-index: 10;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 10px;
        min-width: 320px;
        max-width: 440px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    }

    .inline-edit.editing .edit-form {
        display: block;
    }

    .inline-edit.editing .display-value {
        display: none;
    }

    .inline-edit .edit-form input,
    .inline-edit .edit-form select {
        width: 100%;
        padding: 4px 8px;
        border: 1px solid #ccc;
        border-radius: 3px;
        font-size: 12px;
    }

    .inline-edit .edit-form input:focus,
    .inline-edit .edit-form select:focus {
        border-color: #7366ff;
        outline: none;
        box-shadow: 0 0 0 2px rgba(115, 102, 255, 0.15);
    }

    .inline-edit .edit-form .btn-group {
        margin-top: 5px;
    }

    .inline-edit .edit-form .btn {
        padding: 2px 8px;
        font-size: 11px;
    }

    #convertedLeadsTable thead th,
    #convertedLeadsTable tbody td {
        white-space: nowrap;
    }

    #convertedLeadsTable thead th {
        position: sticky;
        top: 0;
        z-index: 5;
        background: #fff;
        box-shadow: inset 0 -1px 0 #e9ecef;
    }

    #convertedLeadsTable tbody tr:hover {
        background: #fafbff;
    }

    #convertedLeadsTable td .display-value {
        display: inline-block;
        max-width: 220px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        vertical-align: middle;
    }

    #convertedLeadsTable .btn-group .btn {
        margin-right: 4px;
    }

    #convertedLeadsTable .btn-group .btn:last-child {
        margin-right: 0;
    }

    .card .card-body #filterForm {
        border-bottom: 1px dashed #e9ecef;
        padding-bottom: 8px;
    }

    /* Column-specific min-widths by position */
    #convertedLeadsTable thead th:nth-child(1),
    #convertedLeadsTable tbody td:nth-child(1) {
        min-width: 60px;
    }

    #convertedLeadsTable thead th:nth-child(2),
    #convertedLeadsTable tbody td:nth-child(2) {
        min-width: 140px;
    }

    #convertedLeadsTable thead th:nth-child(3),
    #convertedLeadsTable tbody td:nth-child(3) {
        min-width: 120px;
    }

    #convertedLeadsTable thead th:nth-child(4),
    #convertedLeadsTable tbody td:nth-child(4) {
        min-width: 220px;
    }

    #convertedLeadsTable thead th:nth-child(5),
    #convertedLeadsTable tbody td:nth-child(5) {
        min-width: 160px;
    }

    #convertedLeadsTable thead th:nth-child(6),
    #convertedLeadsTable tbody td:nth-child(6) {
        min-width: 160px;
    }

    #convertedLeadsTable thead th:nth-child(7),
    #convertedLeadsTable tbody td:nth-child(7) {
        min-width: 180px;
    }

    #convertedLeadsTable thead th:nth-child(8),
    #convertedLeadsTable tbody td:nth-child(8) {
        min-width: 140px;
    }

    #convertedLeadsTable thead th:nth-child(9),
    #convertedLeadsTable tbody td:nth-child(9) {
        min-width: 140px;
    }

    #convertedLeadsTable thead th:nth-child(10),
    #convertedLeadsTable tbody td:nth-child(10) {
        min-width: 200px;
    }

    #convertedLeadsTable thead th:nth-child(11),
    #convertedLeadsTable tbody td:nth-child(11) {
        min-width: 180px;
    }

    #convertedLeadsTable thead th:nth-child(12),
    #convertedLeadsTable tbody td:nth-child(12) {
        min-width: 120px;
    }

    #convertedLeadsTable thead th:nth-child(13),
    #convertedLeadsTable tbody td:nth-child(13) {
        min-width: 160px;
    }

    #convertedLeadsTable thead th:nth-child(14),
    #convertedLeadsTable tbody td:nth-child(14) {
        min-width: 200px;
    }

    #convertedLeadsTable thead th:nth-child(15),
    #convertedLeadsTable tbody td:nth-child(15) {
        min-width: 160px;
    }

    #convertedLeadsTable thead th:nth-child(16),
    #convertedLeadsTable tbody td:nth-child(16) {
        min-width: 200px;
    }

    #convertedLeadsTable thead th:nth-child(17),
    #convertedLeadsTable tbody td:nth-child(17) {
        min-width: 200px;
    }

    #convertedLeadsTable thead th:nth-child(18),
    #convertedLeadsTable tbody td:nth-child(18) {
        min-width: 220px;
    }

    #convertedLeadsTable thead th:nth-child(19),
    #convertedLeadsTable tbody td:nth-child(19) {
        min-width: 140px;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        const configEl = document.getElementById('gmvssConvertedLeadsConfig');
        const gmvssDataUrl = configEl ? configEl.dataset.dataUrl : '';
        const columnsEl = document.getElementById('gmvssConvertedLeadsColumnsData');
        const gmvssColumns = columnsEl ? JSON.parse(columnsEl.textContent || '[]') : [];

        const registrationLinksEl = document.getElementById('registration-links-json');
        let registrationLinks = [];
        const registrationLinkMap = {};
        try {
            registrationLinks = registrationLinksEl ? JSON.parse(registrationLinksEl.textContent || '[]') : [];
        } catch (e) {
            registrationLinks = [];
        }
        registrationLinks.forEach(function(link) {
            registrationLinkMap[String(link.id)] = link;
        });

        function applyRegistrationLinkColor(container, linkId) {
            const link = registrationLinkMap[String(linkId)];
            const color = link && link.color_code ? link.color_code : '';
            const displayValue = container.find('.display-value');
            if (color) {
                displayValue.css({
                    color: color,
                    'font-weight': 600
                });
            } else {
                displayValue.css({
                    color: '',
                    'font-weight': ''
                });
            }
        }
        function getGmvssFilterParams() {
            return {
                filter_search: ($('#search').val() || '').trim(),
                batch_id: $('#batch_id').val() || '',
                admission_batch_id: ($('#admission_batch_id').val() || $('#admission_batch_id').data('selected') || '') || '',
                course_flag_id: $('#course_flag_id').val() || '',

                date_from: $('#date_from').val() || '',
                date_to: $('#date_to').val() || '',
                status: $('#status').val() || '',
                registration_link_id: $('#registration_link_id').val() || '',
                certificate_status: $('#certificate_status').val() || ''
            };
        }

        function updateGmvssUrlWithFilters() {
            const f = getGmvssFilterParams();
            const url = new URL(window.location.href);
            url.searchParams.set('search', f.filter_search || '');
            url.searchParams.set('batch_id', f.batch_id || '');
            url.searchParams.set('admission_batch_id', f.admission_batch_id || '');
            url.searchParams.set('course_flag_id', f.course_flag_id || '');
            url.searchParams.set('date_from', f.date_from || '');
            url.searchParams.set('date_to', f.date_to || '');
            url.searchParams.set('status', f.status || '');
            url.searchParams.set('registration_link_id', f.registration_link_id || '');
            url.searchParams.set('certificate_status', f.certificate_status || '');
            history.replaceState({}, '', url.toString());
        }

        function initGmvssDataTable() {
            if ($.fn.DataTable.isDataTable('#convertedLeadsTable')) {
                $('#convertedLeadsTable').DataTable().destroy();
            }

            $('#convertedLeadsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: gmvssDataUrl,
                    type: 'GET',
                    data: function(d) {
                        $.extend(d, getGmvssFilterParams());
                    }
                },
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                order: [],
                ordering: false,
                dom: 'Bfrtip',
                buttons: ['csv', 'excel', 'print', 'pdf'],
                scrollX: true,
                autoWidth: false,
                columns: gmvssColumns
            });
        }

        window.reloadGmvssTable = function() {
            if ($.fn.DataTable.isDataTable('#convertedLeadsTable')) {
                $('#convertedLeadsTable').DataTable().ajax.reload(null, false);
            }
        };

        initGmvssDataTable();
        // Handle filter form submission
        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            updateGmvssUrlWithFilters();
            window.reloadGmvssTable();
        });

        // Handle clear button
        $('a[href="{{ route("admin.gmvss-converted-leads.index") }}"]').on('click', function(e) {
            e.preventDefault();
            $('#filterForm')[0].reset();
            $('#admission_batch_id').val('');
            updateGmvssUrlWithFilters();
            window.reloadGmvssTable();
        });

        // Dependent filters: load admission batches by batch
        function loadAdmissionBatchesByBatch(batchId, selectedId) {
            const $admission = $('#admission_batch_id');
            $admission.html('<option value="">Loading...</option>');
            if (!batchId) {
                $admission.html('<option value="">All Admission Batches</option>');
                return;
            }
            $.get(`/api/admission-batches/by-batch/${batchId}`).done(function(list) {
                let opts = '<option value="">All Admission Batches</option>';
                list.forEach(function(i) {
                    const sel = String(selectedId) === String(i.id) ? 'selected' : '';
                    opts += `<option value="${i.id}" ${sel}>${i.title}</option>`;
                });
                $admission.html(opts);
            }).fail(function() {
                $admission.html('<option value="">All Admission Batches</option>');
            });
        }

        // Initialize dependent dropdowns on load
        loadAdmissionBatchesByBatch($('#batch_id').data('selected'), $('#admission_batch_id').data('selected'));

        // On batch change â†’ reload admission batches
        $('#batch_id').on('change', function() {
            const bid = $(this).val();
            loadAdmissionBatchesByBatch(bid, '');
        });

        // Handle update register number button clicks
        $('.update-register-btn').on('click', function(e) {
            e.preventDefault();
            const url = $(this).data('url');
            const title = $(this).data('title');
            show_small_modal(url, title);
        });

        // Handle ID card generation form submission
        $(document).off('submit', '.id-card-generate-form').on('submit', '.id-card-generate-form', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            const form = $(this);
            const button = form.find('button[type="submit"]');

            if (button.prop('disabled')) {
                return false;
            }

            const originalText = button.html();
            const loadingText = button.data('loading-text');

            button.prop('disabled', true).html('<i class="ti ti-loader-2 spin"></i> ' + loadingText);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        toast_success(response.message);
                        setTimeout(function() {
                            window.reloadGmvssTable();
                        }, 1000);
                    }
                },
                error: function(xhr) {
                    console.error('Error generating ID card:', xhr);
                    toast_error('Error generating ID card. Please try again.');
                    button.prop('disabled', false).html(originalText);
                }
            });

            return false;
        });

        // Apply initial colors for registration link labels
        $('.inline-edit[data-field="registration_link_id"]').each(function() {
            const container = $(this);
            const currentId = container.data('current');
            if (currentId) {
                applyRegistrationLinkColor(container, currentId);
            }
        });

        // Inline editing functionality
        $(document).on('click', '.edit-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const container = $(this).closest('.inline-edit');
            const field = container.data('field');
            const id = container.data('id');
            const currentValue = container.data('current') !== undefined ? String(container.data('current')).trim() : container.find('.display-value').text().trim();
            const currentId = container.data('current-id') !== undefined ? String(container.data('current-id')).trim() : '';

            if (container.hasClass('editing')) {
                return;
            }

            $('.inline-edit.editing').not(container).each(function() {
                $(this).removeClass('editing');
                $(this).find('.edit-form').remove();
            });

            let editForm = '';

            if (field === 'admission_batch_id') {
                editForm = createAdmissionBatchField(container.data('batch-id'), currentId);
            } else if (['registration_link_id', 'certificate_status', 'class'].includes(field)) {
                editForm = createSelectField(field, currentValue);
            } else if (['certificate_received_date', 'certificate_issued_date'].includes(field)) {
                editForm = createDateField(field, currentValue);
            } else if (field === 'phone') {
                const currentCode = container.siblings('.inline-code-value').data('current') || '';
                editForm = createPhoneField(currentCode, currentValue);
            } else {
                editForm = createInputField(field, currentValue);
            }

            container.addClass('editing');
            container.append(editForm);

            container.find('input, select').first().focus();
        });

        // Save inline edit
        $(document).off('click.saveInline').on('click.saveInline', '.save-edit', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const container = $(this).closest('.inline-edit');
            const field = container.data('field');
            const id = container.data('id');
            let value;
            if (field === 'phone') {
                value = container.find('input[type="text"]').val();
            } else {
                value = container.find('input, select').val();
            }
            let extra = {};
            if (field === 'phone') {
                const codeVal = container.find('select[name="code"]').val();
                extra = {
                    code: codeVal
                };
            }

            const btn = $(this);
            if (btn.data('busy')) return;
            btn.data('busy', true);
            btn.prop('disabled', true).html('<i class="ti ti-loader-2 spin"></i>');

            $.ajax({
                url: `/admin/converted-leads/${id}/inline-update`,
                method: 'POST',
                data: $.extend({
                    field: field,
                    value: value,
                    _token: $('meta[name="csrf-token"]').attr('content')
                }, extra),
                success: function(response) {
                    if (response.success) {
                        if (field === 'registration_link_id') {
                            const selectedLink = registrationLinkMap[String(value)] || null;
                            container.data('current', value || '');
                            if (selectedLink) {
                                container.find('.display-value').text(selectedLink.title);
                            } else {
                                container.find('.display-value').text(response.value || value || 'N/A');
                            }
                            applyRegistrationLinkColor(container, value);
                        } else {
                            container.find('.display-value').text(response.value || value);
                        }
                        if (field === 'phone') {
                            const codeVal = extra.code || '';
                            container.siblings('.inline-code-value').data('current', codeVal);
                        }
                        toast_success(response.message);
                    } else {
                        toast_error(response.error || 'Update failed');
                    }
                },
                error: function(xhr) {
                    const error = xhr.responseJSON?.error || 'Update failed';
                    toast_error(error);
                },
                complete: function() {
                    btn.data('busy', false);
                    container.removeClass('editing');
                    container.find('.edit-form').remove();
                }
            });
        });

        // Cancel inline edit
        $(document).on('click', '.cancel-edit', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const container = $(this).closest('.inline-edit');
            container.removeClass('editing');
            container.find('.edit-form').remove();
        });

        // Helper functions for creating form elements
        function createInputField(field, currentValue) {
            const inputType = 'text';
            const displayValue = currentValue === 'N/A' ? '' : currentValue;
            const commonAttrs = 'autocomplete="off" autocapitalize="off" spellcheck="false" name="inline-temp"';
            const valueAttr = `value="${displayValue}"`;
            return `
                <div class="edit-form">
                    <input type="${inputType}" ${valueAttr} ${commonAttrs} class="form-control form-control-sm">
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createDateField(field, currentValue) {
            const today = new Date().toISOString().split('T')[0];
            const value = (currentValue && currentValue !== 'N/A') ? currentValue : '';
            return `
                <div class="edit-form">
                    <input type="date" max="${today}" value="${value}" class="form-control form-control-sm">
                    <div class="btn-group mt-1">
                        <button class="btn btn-success btn-sm save-edit">Save</button>
                        <button class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createPhoneField(currentCode, currentPhone) {
            const codeOptionsEl = document.getElementById('country-codes-json');
            let codeOptions = {};
            try {
                codeOptions = codeOptionsEl ? JSON.parse(codeOptionsEl.textContent || '{}') : {};
            } catch (e) {
                codeOptions = {};
            }
            const buildOptions = (selected) => {
                let opts = '<option value="">Select Country</option>';
                for (const c in codeOptions) {
                    const isSel = String(selected) === String(c) ? 'selected' : '';
                    opts += `<option value="${c}" ${isSel}>${c} - ${codeOptions[c]}</option>`;
                }
                return opts;
            };
            const safePhone = (currentPhone && currentPhone !== 'N/A') ? currentPhone : '';
            return `
                <div class="edit-form">
                    <div class="row g-1">
                        <div class="col-5">
                            <select class="form-select form-select-sm" name="code">
                                ${buildOptions(currentCode)}
                            </select>
                        </div>
                        <div class="col-7">
                            <input type="text" value="${safePhone}" class="form-control form-control-sm" placeholder="Phone number">
                        </div>
                    </div>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createSelectField(field, currentValue) {
            let options = '';
            const selectedValue = currentValue === 'N/A' ? '' : currentValue;

            switch (field) {
                case 'registration_link_id':
                    options = '<option value="">Select Registration Link</option>';
                    registrationLinks.forEach(function(link) {
                        const isSelected = String(selectedValue) === String(link.id) ? 'selected' : '';
                        options += `<option value="${link.id}" ${isSelected}>${link.title}</option>`;
                    });
                    break;
                case 'certificate_status':
                    options = '<option value="">Select Certificate Status</option>';
                    options += `<option value="In Progress" ${selectedValue === 'In Progress' ? 'selected' : ''}>In Progress</option>`;
                    options += `<option value="Online Result Not Arrived" ${selectedValue === 'Online Result Not Arrived' ? 'selected' : ''}>Online Result Not Arrived</option>`;
                    options += `<option value="One Result Arrived" ${selectedValue === 'One Result Arrived' ? 'selected' : ''}>One Result Arrived</option>`;
                    options += `<option value="Certificate Arrived" ${selectedValue === 'Certificate Arrived' ? 'selected' : ''}>Certificate Arrived</option>`;
                    options += `<option value="Not Received" ${selectedValue === 'Not Received' ? 'selected' : ''}>Not Received</option>`;
                    options += `<option value="No Admission" ${selectedValue === 'No Admission' ? 'selected' : ''}>No Admission</option>`;
                    break;
                case 'class':
                    // Normalize currentValue: handle both 'SSLC'/'sslc' and 'Plus Two'/'plustwo'
                    let normalizedValue = '';
                    if (selectedValue) {
                        const lowerValue = selectedValue.toLowerCase().trim();
                        if (lowerValue === 'sslc') {
                            normalizedValue = 'sslc';
                        } else if (lowerValue === 'plustwo' || lowerValue === 'plus two' || lowerValue === 'plustwo') {
                            normalizedValue = 'plustwo';
                        }
                    }
                    options = '<option value="">Select Class</option>';
                    options += `<option value="sslc" ${normalizedValue === 'sslc' ? 'selected' : ''}>SSLC</option>`;
                    options += `<option value="plustwo" ${normalizedValue === 'plustwo' ? 'selected' : ''}>Plus Two</option>`;
                    break;
            }

            return `
                <div class="edit-form">
                    <select class="form-select form-select-sm">
                        ${options}
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createAdmissionBatchField(batchId, currentAdmissionBatchId) {
            const form = `
                <div class="edit-form">
                    <select class="form-select form-select-sm">
                        <option value="">${batchId ? 'Loading...' : 'Select Admission Batch'}</option>
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
            // After inserting, populate options asynchronously
            setTimeout(function() {
                const selectEl = $('.inline-edit.editing .edit-form select');
                if (!batchId) {
                    selectEl.html('<option value="">Select Admission Batch</option>');
                    return;
                }
                $.get(`/api/admission-batches/by-batch/${batchId}`).done(function(list) {
                    let opts = '<option value="">Select Admission Batch</option>';
                    list.forEach(function(i) {
                        const sel = String(currentAdmissionBatchId) === String(i.id) ? 'selected' : '';
                        opts += `<option value="${i.id}" ${sel}>${i.title}</option>`;
                    });
                    selectEl.html(opts);
                }).fail(function() {
                    selectEl.html('<option value="">Select Admission Batch</option>');
                });
            }, 0);
            return form;
        }

        // Toggle Academic Verification with confirmation modal
        let academicVerifyUrl = null;
        $(document).off('click', '.toggle-academic-verify-btn').on('click', '.toggle-academic-verify-btn', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const url = $btn.data('url');
            const name = $btn.data('name') || 'this student';
            const isVerified = String($btn.data('verified')) === '1';

            academicVerifyUrl = url;

            const actionText = isVerified ? 'unverify' : 'verify';
            const modalText = `Are you sure you want to ${actionText} academic status for <strong>${name}</strong>?`;
            $('#academicVerifyModalText').html(modalText);
            const $confirmBtn = $('#confirmAcademicVerifyBtn');
            $confirmBtn.removeClass('btn-danger btn-success').addClass(isVerified ? 'btn-danger' : 'btn-success');
            $('#academicVerifyModal').modal('show');
        });

        $('#confirmAcademicVerifyBtn').on('click', function() {
            if (!academicVerifyUrl) return;
            const $confirmBtn = $(this);
            const originalHtml = $confirmBtn.html();
            $confirmBtn.prop('disabled', true).addClass('disabled');
            $.post(academicVerifyUrl, {
                    _token: '{{ csrf_token() }}'
                })
                .done(function(res) {
                    if (res && res.success) {
                        show_alert('success', res.message || 'Updated');
                        $('#academicVerifyModal').modal('hide');
                        setTimeout(() => {
                            window.reloadGmvssTable();
                        }, 600);
                    } else {
                        show_alert('error', (res && res.message) ? res.message : 'Failed to update');
                    }
                })
                .fail(function(xhr) {
                    let msg = 'Failed to update';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    show_alert('error', msg);
                })
                .always(function() {
                    $confirmBtn.prop('disabled', false).removeClass('disabled').html(originalHtml);
                    academicVerifyUrl = null;
                });
        });

        // Toggle Support Verification with confirmation modal
        let supportVerifyUrl = null;
        $(document).off('click', '.toggle-support-verify-btn').on('click', '.toggle-support-verify-btn', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const url = $btn.data('url');
            const name = $btn.data('name') || 'this student';
            const isVerified = String($btn.data('verified')) === '1';

            supportVerifyUrl = url;

            const actionText = isVerified ? 'unverify' : 'verify';
            const modalText = `Are you sure you want to ${actionText} support status for <strong>${name}</strong>?`;
            $('#supportVerifyModalText').html(modalText);
            const $confirmBtn = $('#confirmSupportVerifyBtn');
            $confirmBtn.removeClass('btn-danger btn-success').addClass(isVerified ? 'btn-danger' : 'btn-success');
            $('#supportVerifyModal').modal('show');
        });

        $('#confirmSupportVerifyBtn').on('click', function() {
            if (!supportVerifyUrl) return;
            const $confirmBtn = $(this);
            const originalHtml = $confirmBtn.html();
            $confirmBtn.prop('disabled', true).addClass('disabled');
            $.post(supportVerifyUrl, {
                    _token: '{{ csrf_token() }}'
                })
                .done(function(res) {
                    if (res && res.success) {
                        show_alert('success', res.message || 'Updated');
                        $('#supportVerifyModal').modal('hide');
                        setTimeout(() => {
                            window.reloadGmvssTable();
                        }, 600);
                    } else {
                        show_alert('error', (res && res.message) ? res.message : 'Failed to update');
                    }
                })
                .fail(function(xhr) {
                    let msg = 'Failed to update';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    show_alert('error', msg);
                })
                .always(function() {
                    $confirmBtn.prop('disabled', false).removeClass('disabled').html(originalHtml);
                    supportVerifyUrl = null;
                });
        });

        // Handle Change Course modal buttons
        $(document).on('click', '.js-change-course-modal', function(e) {
            e.preventDefault();
            const url = $(this).data('modal-url');
            const title = $(this).data('modal-title') || 'Change Course';
            if (typeof show_ajax_modal === 'function' && url) {
                show_ajax_modal(url, title);
            }
        });

        // Handle cancellation flag modal
        $(document).on('click', '.js-cancel-flag', function(e) {
            e.preventDefault();
            const url = $(this).data('cancel-url');
            const title = $(this).data('modal-title') || 'Cancellation Confirmation';
            if (typeof show_ajax_modal === 'function' && url) {
                show_ajax_modal(url, title);
            }
        });

        // Delegated submit handler for cancellation flag modal
        $(document).on('submit', '#cancelFlagForm', function(e) {
            e.preventDefault();
            const form = $(this);
            const submitUrl = form.data('submit-url');
            if (!submitUrl) {
                return form.off('submit').submit();
            }

            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

            $.ajax({
                url: submitUrl,
                method: 'POST',
                data: form.serialize(),
                success: function (response) {
                    $('#ajax_modal').modal('hide');
                    if (typeof showToast === 'function') {
                        showToast(response.message, 'success');
                    } else if (typeof toast_success === 'function') {
                        toast_success(response.message);
                    } else {
                        alert(response.message);
                    }
                    if ($.fn.DataTable.isDataTable('#convertedLeadsTable')) {
                        const dt = $('#convertedLeadsTable').DataTable();
                        if (dt.ajax && dt.ajax.url()) {
                            dt.ajax.reload();
                        } else {
                            dt.rows().invalidate().draw(false);
                            window.reloadGmvssTable();
                        }
                    } else {
                        window.reloadGmvssTable();
                    }
                },
                error: function (xhr) {
                    let message = 'Unable to update cancellation flag.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        message = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    }
                    if (typeof showToast === 'function') {
                        showToast(message, 'error');
                    } else if (typeof toast_error === 'function') {
                        toast_error(message);
                    } else {
                        alert(message);
                    }
                },
                complete: function () {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });
    });
</script>
@endpush

<!-- Support Verify Modal -->
<div class="modal fade" id="supportVerifyModal" tabindex="-1" aria-labelledby="supportVerifyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supportVerifyModalLabel">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="supportVerifyModalText" class="mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmSupportVerifyBtn">
                    <span class="confirm-text">Confirm</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Academic Verify Modal -->
<div class="modal fade" id="academicVerifyModal" tabindex="-1" aria-labelledby="academicVerifyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="academicVerifyModalLabel">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="academicVerifyModalText" class="mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmAcademicVerifyBtn">
                    <span class="confirm-text">Confirm</span>
                </button>
            </div>
        </div>
    </div>
</div>
