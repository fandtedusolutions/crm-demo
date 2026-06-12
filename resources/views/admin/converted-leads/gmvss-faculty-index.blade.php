@extends('layouts.mantis')

@section('title', 'GMVSS Faculty List')

@section('content')
@php $appTimezone = config('app.timezone'); @endphp
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
    .cancelled-row > td {
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
                    <h5 class="m-b-10">GMVSS Faculty List</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.converted-leads.index') }}">Converted Leads</a></li>
                    <li class="breadcrumb-item">GMVSS Faculty</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Course Filter Buttons ] start -->
@if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Filter by Course</h6>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin.converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-list"></i> All Converted Leads
                    </a>
                    <a href="{{ route('admin.nios-converted-leads.index') }}" class="btn btn-outline-success">
                        <i class="ti ti-school"></i> NIOS Converted Leads
                    </a>
                    <a href="{{ route('admin.bosse-converted-leads.index') }}" class="btn btn-outline-warning">
                        <i class="ti ti-school-2"></i> BOSSE Converted Leads
                    </a>
                    <a href="{{ route('admin.ugpg-converted-leads.index') }}" class="btn btn-outline-warning">
                        <i class="ti ti-graduation"></i> UG/PG Converted Leads
                    </a>
                    <a href="{{ route('admin.hotel-management-converted-leads.index') }}" class="btn btn-outline-info">
                        <i class="ti ti-building"></i> Hotel Management Converted Leads
                    </a>
                    <a href="{{ route('admin.gmvss-converted-leads.index') }}" class="btn btn-outline-info">
                        <i class="ti ti-certificate"></i> GMVSS Converted Leads
                    </a>
                    <a href="{{ route('admin.digital-marketing-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-marketing"></i> Digital Marketing Converted Leads
                    </a>
                    <a href="{{ route('admin.diploma-in-data-science-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-database"></i> Diploma in Data Science Converted Leads
                    </a>
                    <a href="{{ route('admin.web-development-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-world"></i> Web Development & Designing Converted Leads
                    </a>
                    <a href="{{ route('admin.vibe-coding-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-device-desktop"></i> Vibe Coding Converted Leads
                    </a>
                    <a href="{{ route('admin.graphic-designing-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-palette"></i> Graphic Designing Converted Leads
                    </a>
                    <a href="{{ route('admin.machine-learning-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-brain"></i> Diploma in Machine Learning Converted Leads
                    </a>
                    <a href="{{ route('admin.flutter-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-device-mobile"></i> Flutter Converted Leads
                    </a>
                    <a href="{{ route('admin.eduthanzeel-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-school"></i> Eduthanzeel Converted Leads
                    </a>
                    <a href="{{ route('admin.e-school-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-device-laptop"></i> E-School Converted Leads
                    </a>
                    <a href="{{ route('admin.junior-vlogger-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-video"></i> Junior Vlogger Converted Leads
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<!-- [ Course Filter Buttons ] end -->

<!-- [ Faculty List ] start -->
@if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_team_lead() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_hod())
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Faculty List</h6>
                <div class="d-flex gap-2 flex-wrap">
                    @if(\App\Helpers\RoleHelper::is_faculty() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_team_lead() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_hod())
                    <a href="{{ route('admin.converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-list"></i> All Converted Leads
                    </a>
                    @endif
                    <a href="{{ route('admin.faculty-bosse-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Bosse Converted Faculty List
                    </a>
                    <a href="{{ route('admin.faculty-nios-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> NIOS Converted Faculty List
                    </a>
                    <a href="{{ route('admin.faculty-ugpg-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> UG/PG Faculty Converted List
                    </a>
                    <a href="{{ route('admin.faculty-edumaster-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> EduMaster Faculty Converted List
                    </a>
                    <a href="{{ route('admin.faculty-eschool-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> E-School Converted Faculty List
                    </a>
                    <a href="{{ route('admin.faculty-eduthanzeel-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Eduthanzeel Converted Faculty List
                    </a>
                    <a href="{{ route('admin.gmvss-faculty-converted-leads.index') }}" class="btn btn-outline-primary active">
                        <i class="ti ti-user-star"></i> GMVSS Faculty List
                    </a>
                    <a href="{{ route('admin.digital-marketing-faculty-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Digital Marketing Faculty List
                    </a>
                    <a href="{{ route('admin.data-science-faculty-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Data Science Course Faculty List
                    </a>
                    <a href="{{ route('admin.graphic-designing-faculty-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Graphic Designing Faculty List
                    </a>
                    <a href="{{ route('admin.machine-learning-faculty-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Machine Learning Faculty List
                    </a>
                    <a href="{{ route('admin.medical-coding-faculty-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Medical Coding Faculty List
                    </a>
                    <a href="{{ route('admin.python-faculty-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Python Faculty List
                    </a>
                    <a href="{{ route('admin.flutter-faculty-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Flutter Faculty List
                    </a>
                    <a href="{{ route('admin.rpa-faculty-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> RPA Faculty List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<!-- [ Faculty List ] end -->

<!-- [ Support List ] start -->
@if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_support_team())
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Support List</h6>
                <div class="d-flex gap-2 flex-wrap">
                    @if(\App\Helpers\RoleHelper::is_support_team())
                    <a href="{{ route('admin.converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-list"></i> All Converted Leads
                    </a>
                    @endif
                    <a href="{{ route('admin.support-bosse-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> Bosse Converted Support List
                    </a>
                    <a href="{{ route('admin.support-nios-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> NIOS Converted Support List
                    </a>
                    <a href="{{ route('admin.support-ugpg-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> UG/PG Converted Support List
                    </a>
                    <a href="{{ route('admin.support-edumaster-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> EduMaster Converted Support List
                    </a>
                    <a href="{{ route('admin.support-hotel-management-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> Hotel Management Converted Support List
                    </a>
                    <a href="{{ route('admin.support-gmvss-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> GMVSS Converted Support List
                    </a>
                    <a href="{{ route('admin.support-digital-marketing-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> Digital Marketing Converted Support List
                    </a>
                    <a href="{{ route('admin.support-diploma-in-data-science-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> Diploma in Data Science Converted Support List
                    </a>
                    <a href="{{ route('admin.support-web-development-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> Web Development & Designing Converted Support List
                    </a>
                    <a href="{{ route('admin.support-vibe-coding-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> Vibe Coding Converted Support List
                    </a>
                    <a href="{{ route('admin.support-graphic-designing-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> Graphic Designing Converted Support List
                    </a>
                    <a href="{{ route('admin.support-machine-learning-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> Diploma in Machine Learning Converted Support List
                    </a>
                    <a href="{{ route('admin.support-flutter-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> Flutter Converted Support List
                    </a>
                    <a href="{{ route('admin.support-eduthanzeel-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> Eduthanzeel Converted Support List
                    </a>
                    <a href="{{ route('admin.support-e-school-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> E-School Converted Support List
                    </a>
                    <a href="{{ route('admin.support-junior-vlogger-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> Junior Vlogger â€“ Course Support List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<!-- [ Support List ] end -->

<!-- [ Filter Section ] start -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.gmvss-faculty-converted-leads.index') }}" id="filterForm">
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
                        @include('admin.converted-leads.partials.course-flag-filter-field')
                        

                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                        </div>

                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
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
                                <a href="{{ route('admin.gmvss-faculty-converted-leads.index') }}" class="btn btn-secondary">
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
        <div class="card" data-mentor-update-url="{{ route('admin.converted-leads.inline-update', ['id' => '__ID__']) }}">
            <div class="card-header">
                <h5 class="mb-0">GMVSS Faculty List</h5>
            </div>
            <div class="card-body">
                <!-- Desktop Table View -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover data_table_basic" id="convertedLeadsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Converted Date</th>
                                    <th>Academic Verified At</th>
                                    <th>Support Verified At</th>
                                    <th>Registration Number</th>
                                    <th>Course Flag</th>
                                    <th>Call Time</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Phone</th>
                                    <th>WhatsApp</th>
                                    @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                    <th>Parent Phone</th>
                                    @endif
                                    <th>Batch</th>
                                    <th>Admission Batch</th>
                                    <th>Mail</th>
                                    <th>Course</th>
                                    <th>Passed Year</th>
                                    <th>Class</th>
                                    <th>Enrollment Number</th>
                                    <th>Registration Link</th>
                                    <th>Online Result Publication Date</th>
                                    <th>Certificate Publication Date</th>
                                    <th>Certificate Issued Date</th>
                                    <th>Certificate Distribution Mode</th>
                                    <th>Courier Tracking Number</th>
                                    <th>Remarks</th>
                                    <th>Actions</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($convertedLeads as $index => $convertedLead)
                            <tr class="{{ $convertedLead->is_cancelled ? 'cancelled-row' : '' }}">
                                    @php
                                        $academicVerifiedAt = $convertedLead->academic_verified_at
                                            ? $convertedLead->academic_verified_at->copy()->timezone($appTimezone)->format('d-m-Y h:i A')
                                            : null;
                                        $supportVerifiedAt = $convertedLead->support_verified_at
                                            ? $convertedLead->support_verified_at->copy()->timezone($appTimezone)->format('d-m-Y h:i A')
                                            : null;
                                    @endphp
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $convertedLead->created_at->format('d-m-Y') }}</td>
                                    <td>
                                        @if($academicVerifiedAt)
                                            {{ $academicVerifiedAt }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($supportVerifiedAt)
                                            {{ $supportVerifiedAt }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
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
                                    @include('admin.converted-leads.partials.inline-course-flag-cell', ['convertedLead' => $convertedLead])
                                    @include('admin.converted-leads.partials.inline-call-time-cell', ['convertedLead' => $convertedLead])
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
                                    <td>{{ $convertedLead->batch ? $convertedLead->batch->title : 'N/A' }}</td>
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
                                    <td>{{ $convertedLead->email ?? 'N/A' }}</td>
                                    <td>{{ $convertedLead->course ? $convertedLead->course->title : 'N/A' }}</td>
                                    <td>{{ $convertedLead->lead?->studentDetails?->passed_year ?? 'N/A' }}</td>
                                    <td>
                                        @if($convertedLead->leadDetail?->class)
                                            {{ $convertedLead->leadDetail->class === 'sslc' ? 'SSLC' : ($convertedLead->leadDetail->class === 'plustwo' ? 'Plus Two' : $convertedLead->leadDetail->class) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="enroll_no" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->enroll_no ?? $convertedLead->enroll_no }}">
                                            <span class="display-value">{{ $convertedLead->studentDetails?->enroll_no ?? $convertedLead->enroll_no ?? 'N/A' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                            $mentorRegistrationLink = optional($convertedLead->studentDetails)->registrationLink;
                                            $mentorRegistrationLinkColor = optional($mentorRegistrationLink)->color_code;
                                        ?>
                                        <div class="inline-edit" data-field="registration_link_id" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->registration_link_id }}">
                                            <span class="display-value fw-semibold" style="<?php echo e($mentorRegistrationLinkColor ? 'color: ' . $mentorRegistrationLinkColor . ';' : ''); ?>">
                                                {{ $mentorRegistrationLink?->title ?? 'N/A' }}
                                            </span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="online_result_publication_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->online_result_publication_date ? $convertedLead->mentorDetails->online_result_publication_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->online_result_publication_date ? $convertedLead->mentorDetails->online_result_publication_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="certificate_publication_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->certificate_publication_date ? $convertedLead->mentorDetails->certificate_publication_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->certificate_publication_date ? $convertedLead->mentorDetails->certificate_publication_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="certificate_issued_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->certificate_issued_date ? (strtotime($convertedLead->studentDetails->certificate_issued_date) ? date('Y-m-d', strtotime($convertedLead->studentDetails->certificate_issued_date)) : $convertedLead->studentDetails->certificate_issued_date) : '' }}">
                                            @php
                                                $issuedDate = $convertedLead->studentDetails?->certificate_issued_date ? (strtotime($convertedLead->studentDetails->certificate_issued_date) ? date('d-m-Y', strtotime($convertedLead->studentDetails->certificate_issued_date)) : $convertedLead->studentDetails->certificate_issued_date) : 'N/A';
                                            @endphp
                                            <span class="display-value">{{ $issuedDate }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="certificate_distribution_mode" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->certificate_distribution_mode }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->certificate_distribution_mode ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="courier_tracking_number" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->courier_tracking_number }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->courier_tracking_number ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="remarks" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->remarks }}">
                                            <span class="display-value">{{ $convertedLead->studentDetails?->remarks ?? 'N/A' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="" role="group">
                                            <a href="{{ route('admin.invoices.index', $convertedLead->id) }}" class="btn btn-sm btn-success" title="View Invoice">
                                                <i class="ti ti-receipt"></i>
                                            </a>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                            <button type="button" class="btn btn-sm btn-info update-register-btn" title="Update Register Number"
                                                data-url="{{ route('admin.converted-leads.update-register-number-modal', $convertedLead->id) }}"
                                                data-title="Update Register Number">
                                                <i class="ti ti-edit"></i>
                                            </button>
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
                                    <td>
                                        @if($convertedLead->mentorDetails?->is_placement_passed)
                                            <span class="badge bg-success">Placement Passed</span>
                                            @if($convertedLead->mentorDetails?->is_placement_passed_at)
                                                <br><small class="text-muted">{{ $convertedLead->mentorDetails->is_placement_passed_at->format('d-m-Y h:i A') }}</small>
                                            @endif
                                            @if($convertedLead->mentorDetails?->placement_resume)
                                                <br><a href="{{ asset('storage/' . $convertedLead->mentorDetails->placement_resume) }}" target="_blank" class="btn btn-sm btn-link p-0 small"><i class="ti ti-file-text"></i> View Resume</a>
                                                @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                                    <br><a href="javascript:void(0);" class="btn btn-sm {{ $convertedLead->mentorDetails->is_resume_verified ? 'btn-success' : 'btn-outline-success' }} px-2 py-0"
                                                        onclick="show_small_modal('{{ route('admin.converted-leads.verify-resume-modal', $convertedLead->id) }}', 'Resume Verification')"
                                                        title="Resume Verification">
                                                        <i class="ti ti-circle-check"></i> {{ $convertedLead->mentorDetails->is_resume_verified ? 'Resume Verified' : 'Verify Resume' }}@if($convertedLead->mentorDetails->is_resume_verified && $convertedLead->mentorDetails->resume_verified_at) ({{ $convertedLead->mentorDetails->resume_verified_at->format('d M Y') }})@endif
                                                    </a>
                                                    <br><a href="javascript:void(0);" class="btn btn-sm btn-outline-primary px-2 py-0"
                                                        onclick="show_small_modal('{{ route('admin.converted-leads.move-to-placement', $convertedLead->id) }}', 'Change Resume')"
                                                        title="Change Resume">
                                                        <i class="ti ti-upload"></i> Change Resume
                                                    </a>
                                                @endif
                                            @endif
                                        @else
                                            <a href="javascript:void(0);" class="btn btn-outline-primary btn-sm px-2"
                                                onclick="show_small_modal('{{ route('admin.converted-leads.move-to-placement', $convertedLead->id) }}', 'Move to Placement')"
                                                title="Move to Placement">
                                                <i class="ti ti-user-check"></i> Move to Placement
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="21" class="text-center">No converted leads found</td>
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
<!-- [ Main Content ] end -->

<script id="registration-links-json" type="application/json">
{!! json_encode(
    ($registration_links ?? collect())->map(function($link) {
        return [
            'id' => $link->id,
            'title' => $link->title,
            'color_code' => $link->color_code,
        ];
    })->values(),
    JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
) !!}
</script>

@push('styles')
<style>
.spin {
    animation: spin 1s linear infinite;
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
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
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
}

.inline-edit.editing .edit-form {
    display: block;
}

.inline-edit.editing .display-value {
    display: none;
}

.inline-edit .edit-form input,
.inline-edit .edit-form select,
.inline-edit .edit-form textarea {
    width: 100%;
    padding: 4px 8px;
    border: 1px solid #ccc;
    border-radius: 3px;
    font-size: 12px;
}

.inline-edit .edit-form input:focus,
.inline-edit .edit-form select:focus,
.inline-edit .edit-form textarea:focus {
    border-color: #7366ff;
    outline: none;
    box-shadow: 0 0 0 2px rgba(115,102,255,0.15);
}

.inline-edit .edit-form .btn-group {
    margin-top: 5px;
}

.inline-edit .edit-form .btn {
    padding: 2px 8px;
    font-size: 11px;
}
</style>
@endpush

@push('scripts')
@include('admin.converted-leads.partials.placement-modal-reopen-script')
<script>
    $(document).ready(function() {
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
        // Handle filter form submission
        $('#filterForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const params = new URLSearchParams();

            for (let [key, value] of formData.entries()) {
                if (value.trim() !== '') {
                    params.append(key, value);
                }
            }

            const url = new URL(window.location.href);
            url.search = params.toString();
            window.location.href = url.toString();
        });

        // Handle clear button
        $('a[href="{{ route("admin.gmvss-faculty-converted-leads.index") }}"]').on('click', function(e) {
            e.preventDefault();
            window.location.href = '{{ route("admin.gmvss-faculty-converted-leads.index") }}';
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

        // Helper functions for creating edit forms
        function createSelectField(field, currentValue) {
            let html = '<div class="edit-form"><div class="mb-2">';
            if (field === 'registration_link_id') {
                html += `<select class="form-select form-select-sm">`;
                html += `<option value="">Select Registration Link</option>`;
                registrationLinks.forEach(function(link) {
                    const selected = String(currentValue) === String(link.id) ? 'selected' : '';
                    html += `<option value="${link.id}" ${selected}>${link.title}</option>`;
                });
                html += `</select>`;
            } else if (field === 'certificate_status') {
                html += `<select class="form-select form-select-sm">`;
                html += `<option value="">Select Certificate Status</option>`;
                html += `<option value="In Progress" ${currentValue === 'In Progress' ? 'selected' : ''}>In Progress</option>`;
                html += `<option value="Online Result Not Arrived" ${currentValue === 'Online Result Not Arrived' ? 'selected' : ''}>Online Result Not Arrived</option>`;
                html += `<option value="One Result Arrived" ${currentValue === 'One Result Arrived' ? 'selected' : ''}>One Result Arrived</option>`;
                html += `<option value="Certificate Arrived" ${currentValue === 'Certificate Arrived' ? 'selected' : ''}>Certificate Arrived</option>`;
                html += `<option value="Not Received" ${currentValue === 'Not Received' ? 'selected' : ''}>Not Received</option>`;
                html += `<option value="No Admission" ${currentValue === 'No Admission' ? 'selected' : ''}>No Admission</option>`;
                html += `</select>`;
            } else if (field === 'certificate_distribution_mode') {
                html += `<select class="form-select form-select-sm">`;
                html += `<option value="">Select Mode</option>`;
                html += `<option value="In Person" ${currentValue === 'In Person' ? 'selected' : ''}>In Person</option>`;
                html += `<option value="Courier" ${currentValue === 'Courier' ? 'selected' : ''}>Courier</option>`;
                html += `</select>`;
            }
            html += '</div><div class="btn-group"><button type="button" class="btn btn-sm btn-primary save-edit">Save</button><button type="button" class="btn btn-sm btn-secondary cancel-edit">Cancel</button></div></div>';
            return html;
        }

        function createDateField(field, currentValue) {
            // Convert d-m-Y to Y-m-d for input
            let dateValue = '';
            if (currentValue && currentValue !== 'N/A' && currentValue !== '-') {
                if (currentValue.match(/^\d{4}-\d{2}-\d{2}$/)) {
                    // Already in Y-m-d format
                    dateValue = currentValue;
                } else {
                    const parts = currentValue.split('-');
                    if (parts.length === 3) {
                        dateValue = `${parts[2]}-${parts[1]}-${parts[0]}`;
                    } else {
                        dateValue = currentValue;
                    }
                }
            }
            return `<div class="edit-form"><div class="mb-2"><input type="date" class="form-control form-control-sm" value="${dateValue}"></div><div class="btn-group"><button type="button" class="btn btn-sm btn-primary save-edit">Save</button><button type="button" class="btn btn-sm btn-secondary cancel-edit">Cancel</button></div></div>`;
        }

        function createInputField(field, currentValue) {
            const value = currentValue === 'N/A' ? '' : currentValue;
            const inputType = field === 'call_time' ? 'time' : 'text';
            return `<div class="edit-form"><div class="mb-2"><input type="${inputType}" class="form-control form-control-sm" value="${value}"></div><div class="btn-group"><button type="button" class="btn btn-sm btn-primary save-edit">Save</button><button type="button" class="btn btn-sm btn-secondary cancel-edit">Cancel</button></div></div>`;
        }

        function createTextareaField(field, currentValue) {
            const value = currentValue === 'N/A' ? '' : currentValue;
            return `<div class="edit-form"><div class="mb-2"><textarea class="form-control form-control-sm" rows="3">${value}</textarea></div><div class="btn-group"><button type="button" class="btn btn-sm btn-primary save-edit">Save</button><button type="button" class="btn btn-sm btn-secondary cancel-edit">Cancel</button></div></div>`;
        }

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
            
            if (container.hasClass('editing')) {
                return;
            }
            
            $('.inline-edit.editing').not(container).each(function() {
                $(this).removeClass('editing');
                $(this).find('.edit-form').remove();
            });

            let editForm = '';
            
            if (['registration_link_id', 'certificate_status', 'certificate_distribution_mode'].includes(field)) {
                editForm = createSelectField(field, currentValue);
            } else if (['certificate_received_date', 'certificate_issued_date', 'all_online_result_publication_date', 'certificate_publication_date', 'online_result_publication_date'].includes(field)) {
                editForm = createDateField(field, currentValue);
            } else if (field === 'remarks') {
                editForm = createTextareaField(field, currentValue);
            } else {
                editForm = createInputField(field, currentValue);
            }
            
            container.addClass('editing');
            container.append(editForm);
            
            container.find('input, select, textarea').first().focus();
        });

        // Save inline edit
        $(document).off('click.saveInline').on('click.saveInline', '.save-edit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const container = $(this).closest('.inline-edit');
            const field = container.data('field');
            const id = container.data('id');
            let value = container.find('input, select, textarea').val();
            
            const btn = $(this);
            if (btn.data('busy')) return;
            btn.data('busy', true);
            btn.prop('disabled', true).html('<i class="ti ti-loader-2 spin"></i>');
            
            $.ajax({
                url: `{{ route('admin.converted-leads.inline-update', ':id') }}`.replace(':id', id),
                method: 'POST',
                data: {
                    field: field,
                    value: value,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        let displayValue = response.value || value;
                        
                        // Special handling for date fields
                        if (['certificate_received_date', 'certificate_issued_date', 'all_online_result_publication_date', 'certificate_publication_date', 'online_result_publication_date'].includes(field) && displayValue) {
                            try {
                                const date = new Date(displayValue);
                                if (!isNaN(date.getTime())) {
                                    const day = String(date.getDate()).padStart(2, '0');
                                    const month = String(date.getMonth() + 1).padStart(2, '0');
                                    const year = date.getFullYear();
                                    displayValue = `${day}-${month}-${year}`;
                                }
                            } catch (e) {
                                // Keep original value if conversion fails
                            }
                        }
                        
                        // Special handling for registration_link_id
                        if (field === 'registration_link_id') {
                            const link = registrationLinkMap[String(value)] || null;
                            displayValue = link ? link.title : (value || 'N/A');
                            container.data('current', value || '');
                            applyRegistrationLinkColor(container, value);
                        } else {
                            container.data('current', response.value || value);
                        }
                        
                        container.find('.display-value').text(displayValue || 'N/A');
                        toast_success(response.message || 'Updated successfully');
                    } else {
                        toast_error(response.error || 'Update failed');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Update failed';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    toast_error(errorMessage);
                },
                complete: function() {
                    btn.data('busy', false);
                    btn.prop('disabled', false).html('Save');
                    container.removeClass('editing');
                    container.find('.edit-form').remove();
                }
            });
        });
        
        // Cancel inline edit
        $(document).off('click.cancelInline').on('click.cancelInline', '.cancel-edit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const container = $(this).closest('.inline-edit');
            container.removeClass('editing');
            container.find('.edit-form').remove();
        });
    });
</script>
@include('admin.converted-leads.partials.course-flag-inline-scripts', ['courseUpdateUrl' => route('admin.converted-leads.inline-update', ['id' => '__ID__'])])
@endpush
@endsection


