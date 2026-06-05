@extends('layouts.mantis')

@section('title', 'UG/PG Mentor Converted List')

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
                    <h5 class="m-b-10">UG/PG Mentor Converted List</h5>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end align-items-center gap-3">
                    <ul class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.converted-leads.index') }}">Converted Leads</a></li>
                        <li class="breadcrumb-item">UG/PG Mentor Converted List</li>
                    </ul>
                    <a href="{{ route('admin.converted-leads.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left"></i> Back to Converted Leads
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Course Filter Buttons ] start -->
@if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_finance())
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Filter by Course</h6>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin.converted-leads.index') }}" class="btn btn-outline-primary {{ request()->routeIs('admin.converted-leads.index') && !request('course_id') ? 'active' : '' }}">
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

<!-- [ Mentor List ] start -->
@if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_team_lead() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_hod())
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Mentor List</h6>
                <div class="d-flex gap-2 flex-wrap">
                    @if(\App\Helpers\RoleHelper::is_mentor() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_team_lead() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_hod())
                    <a href="{{ route('admin.converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-list"></i> All Converted Leads
                    </a>
                    @endif
                    <a href="{{ route('admin.mentor-bosse-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Bosse Converted Mentor List
                    </a>
                    <a href="{{ route('admin.mentor-nios-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> NIOS Converted Mentor List
                    </a>
                    <a href="{{ route('admin.mentor-ugpg-converted-leads.index') }}" class="btn btn-outline-primary active">
                        <i class="ti ti-user-star"></i> UG/PG Mentor Converted List
                    </a>
                    <a href="{{ route('admin.mentor-edumaster-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> EduMaster Mentor Converted List
                    </a>
                    <a href="{{ route('admin.mentor-eschool-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> E-School Converted Mentor List
                    </a>
                    <a href="{{ route('admin.mentor-eduthanzeel-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Eduthanzeel Converted Mentor List
                    </a>
                    <a href="{{ route('admin.gmvss-mentor-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> GMVSS Mentor List
                    </a>
                    <a href="{{ route('admin.digital-marketing-mentor-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Digital Marketing Mentor List
                    </a>
                    <a href="{{ route('admin.data-science-mentor-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Data Science Course Mentor List
                    </a>
                    <a href="{{ route('admin.graphic-designing-mentor-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Graphic Designing Mentor List
                    </a>
                    <a href="{{ route('admin.machine-learning-mentor-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Machine Learning Mentor List
                    </a>
                    <a href="{{ route('admin.medical-coding-mentor-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Medical Coding Mentor List
                    </a>
                    <a href="{{ route('admin.python-mentor-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Python Mentor List
                    </a>
                    <a href="{{ route('admin.flutter-mentor-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Flutter Mentor List
                    </a>
                    <a href="{{ route('admin.rpa-mentor-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> RPA Mentor List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<!-- [ Mentor List ] end -->

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
                        <i class="ti ti-headphones"></i> Machine Learning Converted Support List
                    </a>
                    <a href="{{ route('admin.support-flutter-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> Flutter Converted Support List
                    </a>
                    <a href="{{ route('admin.support-ai-python-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> AI Python Converted Support List
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
                <form method="GET" action="{{ route('admin.mentor-ugpg-converted-leads.index') }}" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search"
                                value="{{ request('search') }}" placeholder="Name, Phone, Email, Register Number">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="university_id" class="form-label">University</label>
                            <select class="form-select" id="university_id" name="university_id">
                                <option value="">All Universities</option>
                                @foreach($universities as $university)
                                <option value="{{ $university->id }}" {{ request('university_id') == $university->id ? 'selected' : '' }}>
                                    {{ $university->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="course_type" class="form-label">Course Type</label>
                            <select class="form-select" id="course_type" name="course_type">
                                <option value="">All</option>
                                <option value="UG" {{ request('course_type')==='UG' ? 'selected' : '' }}>UG</option>
                                <option value="PG" {{ request('course_type')==='PG' ? 'selected' : '' }}>PG</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="student_status" class="form-label">Student Status</label>
                            <select class="form-select" id="student_status" name="student_status">
                                <option value="">All</option>
                                <option value="Active" {{ request('student_status')==='Active' ? 'selected' : '' }}>Active</option>
                                <option value="Completed" {{ request('student_status')==='Completed' ? 'selected' : '' }}>Completed</option>
                                <option value="Discontinued" {{ request('student_status')==='Discontinued' ? 'selected' : '' }}>Discontinued</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="document_verification_status" class="form-label">Document Verification</label>
                            <select class="form-select" id="document_verification_status" name="document_verification_status">
                                <option value="">All</option>
                                <option value="Not Verified" {{ request('document_verification_status')==='Not Verified' ? 'selected' : '' }}>Not Verified</option>
                                <option value="Verified" {{ request('document_verification_status')==='Verified' ? 'selected' : '' }}>Verified</option>
                            </select>
                        </div>
                        @include('admin.converted-leads.partials.mentor-flag-filter-field')
                        
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
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-search"></i> Filter
                            </button>
                            <a href="{{ route('admin.mentor-ugpg-converted-leads.index') }}" class="btn btn-secondary">
                                <i class="ti ti-refresh"></i> Clear
                            </a>
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
        <div class="card" data-mentor-update-url="{{ route('admin.mentor-ugpg-converted-leads.update-mentor-details', ['id' => '__ID__']) }}">
            <div class="card-header">
                <h5>UG/PG Mentor Converted List</h5>
            </div>
            <div class="card-body">
                <!-- Desktop Table View -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover data_table_basic" id="mentorUGPGTable">
                            <thead>
                                <tr>
                                    <th>SL No</th>
                                    <th>Support (Verification)</th>
                                    <th>Converted Date</th>
                                    <th>Register Number</th>
                                    <th>Flag</th>
                                    <th>Call Time</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>DOB</th>
                                    <th>Phone</th>
                                    <th>WhatsApp Number</th>
                                    @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                    <th>Parent Phone</th>
                                    @endif
                                    <th>Email</th>
                                    <th>Board/University</th>
                                    <th>Course Type</th>
                                    <th>Back Year</th>
                                    <th>Online Registration Date</th>
                                    <th>Admission Form Issued Date</th>
                                    <th>Admission Form Returned Date</th>
                                    <th>Document Verification Status</th>
                                    <th>Verification Completed Date</th>
                                    <th>ID Card Issued Date</th>
                                    <th>First Year Result Declaration Date</th>
                                    <th>Second Year Result Declaration Date</th>
                                    <th>Third Year Result Declaration Date</th>
                                    <th>All Online Result Publication Date</th>
                                    <th>Certificate Issued Date</th>
                                    <th>Certificate Distribution Mode</th>
                                    <th>Courier Tracking Number</th>
                                    <th>Student Status</th>
                                    <th>Remarks / Internal Notes</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($convertedLeads as $index => $convertedLead)
                                <tr class="{{ $convertedLead->is_cancelled ? 'cancelled-row' : '' }}">
                                    @php
                                    $supportVerifiedAt = $convertedLead->support_verified_at
                                    ? $convertedLead->support_verified_at->copy()->timezone($appTimezone)->format('d-m-Y h:i A')
                                    : null;
                                    @endphp
                                    <td>{{ ($convertedLeads->currentPage() - 1) * $convertedLeads->perPage() + $index + 1 }}</td>
                                    <td>
                                        @if($supportVerifiedAt)
                                        <span class="badge bg-success">Verified</span><br>
                                        <small class="text-muted">{{ $supportVerifiedAt }}</small>
                                        @else
                                        <span class="badge bg-warning">Not Verified</span>
                                        @endif
                                    </td>
                                    <td>{{ $convertedLead->created_at->format('d-m-Y') }}</td>
                                    <td>{{ $convertedLead->register_number ?? '-' }}</td>
                                    @include('admin.converted-leads.partials.inline-flag-cell', ['convertedLead' => $convertedLead])
                                    <td>
                                        {{ $convertedLead->name }}
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
                                    </td>
                                    <td>{{ $convertedLead->is_b2b == 1 ? ('B2B' . ($convertedLead->lead?->team?->name ? ' (' . $convertedLead->lead->team->name . ')' : '')) : 'In House' }}</td>
                                    <td>{{ $convertedLead->dob ? \Carbon\Carbon::parse($convertedLead->dob)->format('d-m-Y') : '-' }}</td>
                                    <td>{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</td>
                                    <td>
                                        @if($convertedLead->leadDetail && $convertedLead->leadDetail->whatsapp_number)
                                            {{ \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->whatsapp_code, $convertedLead->leadDetail->whatsapp_number) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                    <td>
                                        @if($convertedLead->leadDetail && $convertedLead->leadDetail->parents_number)
                                            {{ \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->parents_code, $convertedLead->leadDetail->parents_number) }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    @endif
                                    <td>{{ $convertedLead->email ?? '-' }}</td>
                                    <td>
                                        @if($convertedLead->leadDetail && $convertedLead->leadDetail->university)
                                            {{ $convertedLead->leadDetail->university->title }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $convertedLead->leadDetail?->course_type ?? '-' }}</td>
                                    <td>{{ $convertedLead->leadDetail?->back_year ?? '-' }}</td>
                                    <td>
                                        <div class="inline-edit" data-field="online_registration_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->online_registration_date ? $convertedLead->mentorDetails->online_registration_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->online_registration_date ? $convertedLead->mentorDetails->online_registration_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="admission_form_issued_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->admission_form_issued_date ? $convertedLead->mentorDetails->admission_form_issued_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->admission_form_issued_date ? $convertedLead->mentorDetails->admission_form_issued_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="admission_form_returned_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->admission_form_returned_date ? $convertedLead->mentorDetails->admission_form_returned_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->admission_form_returned_date ? $convertedLead->mentorDetails->admission_form_returned_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="document_verification_status" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->document_verification_status }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->document_verification_status ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="verification_completed_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->verification_completed_date ? $convertedLead->mentorDetails->verification_completed_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->verification_completed_date ? $convertedLead->mentorDetails->verification_completed_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="id_card_issued_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->id_card_issued_date ? $convertedLead->mentorDetails->id_card_issued_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->id_card_issued_date ? $convertedLead->mentorDetails->id_card_issued_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="first_year_result_declaration_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->first_year_result_declaration_date ? $convertedLead->mentorDetails->first_year_result_declaration_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->first_year_result_declaration_date ? $convertedLead->mentorDetails->first_year_result_declaration_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="second_year_result_declaration_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->second_year_result_declaration_date ? $convertedLead->mentorDetails->second_year_result_declaration_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->second_year_result_declaration_date ? $convertedLead->mentorDetails->second_year_result_declaration_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="third_year_result_declaration_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->third_year_result_declaration_date ? $convertedLead->mentorDetails->third_year_result_declaration_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->third_year_result_declaration_date ? $convertedLead->mentorDetails->third_year_result_declaration_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="all_online_result_publication_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->all_online_result_publication_date ? $convertedLead->mentorDetails->all_online_result_publication_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->all_online_result_publication_date ? $convertedLead->mentorDetails->all_online_result_publication_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="certificate_issued_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->certificate_issued_date ? $convertedLead->mentorDetails->certificate_issued_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->certificate_issued_date ? $convertedLead->mentorDetails->certificate_issued_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="certificate_distribution_mode" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->certificate_distribution_mode }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->certificate_distribution_mode ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="courier_tracking_number" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->courier_tracking_number }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->courier_tracking_number ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="student_status" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->student_status }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->student_status ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="remarks_internal_notes" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->remarks_internal_notes }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->remarks_internal_notes ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
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
                                    <td colspan="28" class="text-center">No converted leads found for mentoring</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $convertedLeads->links() }}
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="d-lg-none">
                    @forelse($convertedLeads as $index => $convertedLead)
                    <div class="card mb-3 {{ $convertedLead->is_cancelled ? 'cancelled-card' : '' }}">
                        <div class="card-body">
                            <!-- Lead Header -->
                            <div class="d-flex align-items-center mb-3">
                                <div class="avtar avtar-s rounded-circle bg-light-primary me-3 d-flex align-items-center justify-content-center">
                                    <span class="f-16 fw-bold text-primary">{{ strtoupper(substr($convertedLead->name, 0, 1)) }}</span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">{{ $convertedLead->name }}</h6>
                                    <small class="text-muted">ID: {{ $convertedLead->lead_id }}</small>
                                    @if($convertedLead->is_cancelled)
                                    <span class="badge bg-danger ms-2">Cancelled</span>
                                    @endif
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
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.invoices.index', $convertedLead->id) }}">
                                                <i class="ti ti-receipt me-2"></i>View Invoice
                                            </a>
                                        </li>
                                        @php
                                        $canManageCancelFlag = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor();
                                        @endphp
                                        @if($canManageCancelFlag)
                                        <li>
                                            <button type="button" class="dropdown-item js-cancel-flag" 
                                                data-cancel-url="{{ route('admin.converted-leads.cancel-flag', $convertedLead->id) }}"
                                                data-modal-title="Cancellation Confirmation">
                                                <i class="ti ti-ban me-2"></i>{{ $convertedLead->is_cancelled ? 'Update Cancellation' : 'Cancel' }}
                                            </button>
                                        </li>
                                        @endif
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_support_team())
                                        <li>
                                            <button type="button" class="dropdown-item update-register-btn"
                                                data-url="{{ route('admin.converted-leads.update-register-number-modal', $convertedLead->id) }}"
                                                data-title="Update Register Number">
                                                <i class="ti ti-edit me-2"></i>Update Register
                                            </button>
                                        </li>
                                        @php $courseChanged = (bool) ($convertedLead->is_course_changed ?? false); @endphp
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                        <li>
                                            <button type="button" class="dropdown-item js-change-course-modal"
                                                data-modal-url="{{ route('admin.converted-leads.change-course-modal', $convertedLead->id) }}"
                                                data-modal-title="Change Course">
                                                <i class="ti ti-exchange me-2"></i>Change Course
                                            </button>
                                        </li>
                                        @endif
                                        @if($convertedLead->register_number)
                                        @php
                                        $idCard = \App\Models\ConvertedLeadIdCard::where('converted_lead_id', $convertedLead->id)->first();
                                        @endphp
                                        @if($idCard)
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.converted-leads.id-card-view', $convertedLead->id) }}" target="_blank">
                                                <i class="ti ti-id me-2"></i>View ID Card
                                            </a>
                                        </li>
                                        @else
                                        <li>
                                            <form action="{{ route('admin.converted-leads.id-card-generate', $convertedLead->id) }}" method="post" style="display:inline-block" class="id-card-generate-form">
                                                @csrf
                                                <button type="submit" class="dropdown-item" data-loading-text="Generating...">
                                                    <i class="ti ti-id me-2"></i>Generate ID Card
                                                </button>
                                            </form>
                                        </li>
                                        @endif
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
                                    <small class="text-muted d-block">Email</small>
                                    <span class="fw-medium">{{ $convertedLead->email ?? 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">University</small>
                                    <span class="fw-medium">{{ $convertedLead->leadDetail?->university?->title ?? 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Course Type</small>
                                    <span class="fw-medium">{{ $convertedLead->leadDetail?->course_type ?? 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Student Status</small>
                                    <span class="fw-medium">{{ $convertedLead->mentorDetails?->student_status ?? 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Document Verification</small>
                                    <span class="fw-medium">{{ $convertedLead->mentorDetails?->document_verification_status ?? 'N/A' }}</span>
                                </div>
                            </div>

                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <div class="text-muted">
                            <i class="ti ti-user-check f-48 mb-3 d-block"></i>
                            <h5>No converted leads found for mentoring</h5>
                            <p>Try adjusting your filters or check back later.</p>
                        </div>
                    </div>
                    @endforelse
                    <div class="d-flex justify-content-center mt-3">
                        {{ $convertedLeads->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

@endsection

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
        box-shadow: 0 0 0 2px rgba(115, 102, 255, 0.15);
    }

    .inline-edit .edit-form .btn-group {
        margin-top: 5px;
    }

    .inline-edit .edit-form .btn {
        padding: 2px 8px;
        font-size: 11px;
    }

    #mentorUGPGTable thead th,
    #mentorUGPGTable tbody td {
        white-space: nowrap;
    }

    #mentorUGPGTable thead th {
        position: sticky;
        top: 0;
        z-index: 5;
        background: #fff;
        box-shadow: inset 0 -1px 0 #e9ecef;
    }

    #mentorUGPGTable tbody tr:hover {
        background: #fafbff;
    }

    #mentorUGPGTable td .display-value {
        display: inline-block;
        max-width: 220px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        vertical-align: middle;
    }
</style>
@endpush

@push('scripts')
@include('admin.converted-leads.partials.placement-modal-reopen-script')
<script>
    $(document).ready(function() {
        // DataTable is automatically initialized by layout for tables with 'data_table_basic' class

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

            // Check if it's a date field
            if (['online_registration_date', 'admission_form_issued_date', 'admission_form_returned_date', 'verification_completed_date', 'id_card_issued_date', 'first_year_result_declaration_date', 'second_year_result_declaration_date', 'third_year_result_declaration_date', 'all_online_result_publication_date', 'certificate_issued_date'].includes(field)) {
                editForm = createDateField(field, currentValue);
            } else if (['document_verification_status', 'certificate_distribution_mode', 'student_status'].includes(field)) {
                editForm = createSelectField(field, currentValue);
            } else if (field === 'remarks_internal_notes') {
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
            const value = container.find('input, select, textarea').val();

            const btn = $(this);
            if (btn.data('busy')) return;
            btn.data('busy', true);
            btn.prop('disabled', true).html('<i class="ti ti-loader-2 spin"></i>');

            $.ajax({
                url: `/admin/mentor-ugpg-converted-leads/${id}/update-mentor-details`,
                method: 'POST',
                data: {
                    field: field,
                    value: value,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        container.find('.display-value').text(response.value || value || '-');
                        // Update the data-current attribute with the new value
                        container.data('current', response.value || value);
                        toast_success(response.message);
                    } else {
                        toast_error(response.error || 'Update failed');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Update failed';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        } else if (xhr.responseJSON.errors) {
                            // Handle validation errors
                            const errors = xhr.responseJSON.errors;
                            const fieldErrors = Object.values(errors).flat();
                            errorMessage = fieldErrors.join(', ');
                        }
                    }
                    toast_error(errorMessage);
                },
                complete: function() {
                    btn.data('busy', false);
                    container.removeClass('editing');
                    container.find('.edit-form').remove();
                }
            });
        });

        // Cancel edit
        $(document).on('click', '.cancel-edit', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const container = $(this).closest('.inline-edit');
            container.removeClass('editing');
            container.find('.edit-form').remove();
            container.find('.display-value').show();
            container.find('.edit-btn').show();
        });

        // Helper functions for creating form elements
        function createInputField(field, currentValue) {
            const displayValue = currentValue === '-' ? '' : currentValue;
            const inputType = field === 'call_time' ? 'time' : 'text';
            return `
                <div class="edit-form">
                    <input type="${inputType}" value="${displayValue}" class="form-control form-control-sm" autocomplete="off" autocapitalize="off" spellcheck="false">
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createDateField(field, currentValue) {
            // Convert d-m-Y to Y-m-d for date input
            let dateValue = '';
            if (currentValue && currentValue !== '-') {
                if (currentValue.includes('-') && currentValue.length === 10) {
                    // Check if it's already in Y-m-d format
                    if (currentValue.match(/^\d{4}-\d{2}-\d{2}$/)) {
                        dateValue = currentValue;
                    } else if (currentValue.match(/^\d{2}-\d{2}-\d{4}$/)) {
                        // Convert from d-m-Y to Y-m-d
                        const parts = currentValue.split('-');
                        dateValue = parts[2] + '-' + parts[1] + '-' + parts[0];
                    }
                }
            }
            return `
                <div class="edit-form">
                    <input type="date" value="${dateValue}" class="form-control form-control-sm">
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createTextareaField(field, currentValue) {
            const displayValue = currentValue === '-' ? '' : currentValue;
            return `
                <div class="edit-form">
                    <textarea rows="3" class="form-control form-control-sm" autocomplete="off" autocapitalize="off" spellcheck="false">${displayValue}</textarea>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createSelectField(field, currentValue) {
            let options = '';

            if (field === 'document_verification_status') {
                options = `
                    <option value="">Select Status</option>
                    <option value="Not Verified" ${currentValue === 'Not Verified' ? 'selected' : ''}>Not Verified</option>
                    <option value="Verified" ${currentValue === 'Verified' ? 'selected' : ''}>Verified</option>
                `;
            } else if (field === 'certificate_distribution_mode') {
                options = `
                    <option value="">Select Mode</option>
                    <option value="In Person" ${currentValue === 'In Person' ? 'selected' : ''}>In Person</option>
                    <option value="Courier" ${currentValue === 'Courier' ? 'selected' : ''}>Courier</option>
                `;
            } else if (field === 'student_status') {
                options = `
                    <option value="">Select Status</option>
                    <option value="Active" ${currentValue === 'Active' ? 'selected' : ''}>Active</option>
                    <option value="Completed" ${currentValue === 'Completed' ? 'selected' : ''}>Completed</option>
                    <option value="Discontinued" ${currentValue === 'Discontinued' ? 'selected' : ''}>Discontinued</option>
                `;
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
    });
</script>
@include('admin.converted-leads.partials.mentor-flag-inline-scripts')
@endpush