@extends('layouts.mantis')

@section('title', 'BOSSE Converted Leads')

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
                    <h5 class="m-b-10">BOSSE Converted Leads Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.converted-leads.index') }}">Converted Leads</a></li>
                    <li class="breadcrumb-item">BOSSE</li>
                </ul>
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
                    <a href="{{ route('admin.converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-list"></i> All Converted Leads
                    </a>
                    <a href="{{ route('admin.nios-converted-leads.index') }}" class="btn btn-outline-success">
                        <i class="ti ti-school"></i> NIOS Converted Leads
                    </a>
                    <a href="{{ route('admin.bosse-converted-leads.index') }}" class="btn btn-warning active">
                        <i class="ti ti-school-2"></i> BOSSE Converted Leads
                    </a>
                    <a href="{{ route('admin.ugpg-converted-leads.index') }}" class="btn btn-outline-warning">
                        <i class="ti ti-graduation"></i> UG/PG Converted Leads
                    </a>
                    <a href="{{ route('admin.edumaster-converted-leads.index') }}" class="btn btn-outline-warning">
                        <i class="ti ti-graduation"></i> EduMaster Converted Leads
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
                    <a href="{{ route('admin.mentor-ugpg-converted-leads.index') }}" class="btn btn-outline-primary">
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
                    <a href="{{ route('admin.junior-vlogger-mentor-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Junior Vlogger Converted Mentor List
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
                        <i class="ti ti-headphones"></i> Junior Vlogger - Course Support List
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
                <form method="GET" action="{{ route('admin.bosse-converted-leads.index') }}" id="filterForm">
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
                                <option value="Received" {{ request('status')==='Received' ? 'selected' : '' }}>Received</option>
                                <option value="Admission cancel" {{ request('status')==='Admission cancel' ? 'selected' : '' }}>Admission cancel</option>
                                <option value="Active" {{ request('status')==='Active' ? 'selected' : '' }}>Active</option>
                                <option value="Inactive" {{ request('status')==='Inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="reg_fee" class="form-label">REG. FEE</label>
                            <select class="form-select" id="reg_fee" name="reg_fee">
                                <option value="">All</option>
                                <option value="Handover -1" {{ request('reg_fee')==='Handover -1' ? 'selected' : '' }}>Handover -1</option>
                                <option value="Handover - 2" {{ request('reg_fee')==='Handover - 2' ? 'selected' : '' }}>Handover - 2</option>
                                <option value="Handover - 3" {{ request('reg_fee')==='Handover - 3' ? 'selected' : '' }}>Handover - 3</option>
                                <option value="Handover - 4" {{ request('reg_fee')==='Handover - 4' ? 'selected' : '' }}>Handover - 4</option>
                                <option value="Handover - 5" {{ request('reg_fee')==='Handover - 5' ? 'selected' : '' }}>Handover - 5</option>
                                <option value="Paid" {{ request('reg_fee')==='Paid' ? 'selected' : '' }}>Paid</option>
                                <option value="Admission cancel" {{ request('reg_fee')==='Admission cancel' ? 'selected' : '' }}>Admission cancel</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-search"></i> <span class="d-none d-sm-inline">Filter</span>
                                </button>
                                <a href="{{ route('admin.bosse-converted-leads.index') }}" class="btn btn-secondary">
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
                <h5 class="mb-0">BOSSE Converted Leads List</h5>
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
                                    <th>Converted Date</th>
                                    <th>Register Number</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>DOB</th>
                                    <th>Phone</th>
                                    <th>WhatsApp</th>
                                    @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                    <th>Parent Phone</th>
                                    @endif
                                    <th>Batch</th>
                                    <th>Admission Batch</th>
                                    <th>Registered Person</th>
                                    <th>Subject</th>
                                    <th>Subject Area</th>
                                    <th>Registration Fee</th>
                                    <th>Registration Status</th>
                                    <th>Course</th>
                                    <th>Application Number</th>
                                    <th>Enrolment Number</th>
                                    <th>Mail</th>
                                    <th>ST</th>
                                    <th>PHY</th>
                                    <th>CHE</th>
                                    <th>BIO</th>
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
                                    <td>{{ $convertedLead->created_at->format('d-m-Y') }}</td>
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
                                        <div class="inline-edit" data-field="dob" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->dob }}">
                                            @php
                                            $dobDisplay = $convertedLead->dob ? (strtotime($convertedLead->dob) ? date('d-m-Y', strtotime($convertedLead->dob)) : $convertedLead->dob) : 'N/A';
                                            @endphp
                                            <span class="display-value">{{ $dobDisplay }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="phone" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->phone }}">
                                            <span class="display-value">{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                        <div class="d-none inline-code-value" data-field="code" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->code }}"> </div>
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
                                        <div class="inline-edit" data-field="academic_assistant_id" data-id="{{ $convertedLead->id }}" data-current-id="{{ $convertedLead->academic_assistant_id }}">
                                            <span class="display-value">{{ $convertedLead->academicAssistant ? $convertedLead->academicAssistant->name : 'N/A' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="subject_id" data-id="{{ $convertedLead->id }}" data-course-id="{{ $convertedLead->course_id }}" data-current-id="{{ $convertedLead->subject_id }}">
                                            <span class="display-value">{{ $convertedLead->subject?->title ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="subject_area_id" data-id="{{ $convertedLead->id }}" data-current-id="{{ $convertedLead->subject_area_id }}">
                                            <span class="display-value">{{ $convertedLead->subjectArea?->title ?? 'N/A' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="status" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->status }}">
                                            <span class="display-value">{{ $convertedLead->status ?? 'N/A' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_finance())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="reg_fee" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->reg_fee }}">
                                            <span class="display-value">{{ $convertedLead->studentDetails?->reg_fee ?? 'N/A' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $convertedLead->course ? $convertedLead->course->title : 'N/A' }}</td>
                                    <td>
                                        <div class="inline-edit" data-field="application_number" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->application_number }}">
                                            <span class="display-value">{{ $convertedLead->studentDetails?->application_number ?? 'N/A' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="board_registration_number" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->board_registration_number }}">
                                            <span class="display-value">{{ $convertedLead->studentDetails?->board_registration_number ?? 'N/A' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $convertedLead->email ?? 'N/A' }}</td>
                                    <td>
                                        <div class="inline-edit" data-field="st" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->st }}">
                                            <span class="display-value">{{ $convertedLead->studentDetails?->st ?? '0' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="phy" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->phy }}">
                                            <span class="display-value">{{ $convertedLead->studentDetails?->phy ?? '0' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="che" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->che }}">
                                            <span class="display-value">{{ $convertedLead->studentDetails?->che ?? '0' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="bio" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->bio }}">
                                            <span class="display-value">{{ $convertedLead->studentDetails?->bio ?? '0' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="remarks" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->remarks }}">
                                            <span class="display-value">{{ $convertedLead->studentDetails?->remarks ?? '-' }}</span>
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
                                    <td colspan="23" class="text-center">No BOSSE converted leads found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="d-lg-none" id="bosseMobileCardsWrap">
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
                                    <small class="text-muted">ID: {{ $convertedLead->lead_id }}</small>
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
                                    <small class="text-muted d-block">Academic Assistant</small>
                                    <span class="fw-medium">{{ $convertedLead->academicAssistant ? $convertedLead->academicAssistant->name : 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Register Number</small>
                                    @if($convertedLead->register_number)
                                    <span class="badge bg-success">{{ $convertedLead->register_number }}</span>
                                    @else
                                    <span class="text-muted">Not Set</span>
                                    @endif
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Subject</small>
                                    <span class="fw-medium">{{ $convertedLead->subject?->title ?? 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Subject Area</small>
                                    <span class="fw-medium">{{ $convertedLead->subjectArea?->title ?? 'N/A' }}</span>
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
                            <h5>No BOSSE converted leads found</h5>
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
@endsection

<script id="country-codes-json" type="application/json">
    {!! json_encode($country_codes ?? [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
</script>

@php
    $bosseConvertedLeadsColumns = [
        ['data' => 'index', 'name' => 'index', 'orderable' => false, 'searchable' => false],
        ['data' => 'academic', 'name' => 'academic', 'orderable' => false, 'searchable' => false],
        ['data' => 'support', 'name' => 'support', 'orderable' => false, 'searchable' => false],
        ['data' => 'converted_date', 'name' => 'converted_date', 'orderable' => false, 'searchable' => false],
        ['data' => 'register_number', 'name' => 'register_number', 'orderable' => false, 'searchable' => false],
        ['data' => 'name_col', 'name' => 'name_col', 'orderable' => false, 'searchable' => false],
        ['data' => 'type', 'name' => 'type', 'orderable' => false, 'searchable' => false],
        ['data' => 'dob', 'name' => 'dob', 'orderable' => false, 'searchable' => false],
        ['data' => 'phone', 'name' => 'phone', 'orderable' => false, 'searchable' => false],
        ['data' => 'whatsapp', 'name' => 'whatsapp', 'orderable' => false, 'searchable' => false],
    ];

    if (\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor()) {
        $bosseConvertedLeadsColumns[] = ['data' => 'parent_phone', 'name' => 'parent_phone', 'orderable' => false, 'searchable' => false];
    }

    $bosseConvertedLeadsColumns = array_merge($bosseConvertedLeadsColumns, [
        ['data' => 'batch', 'name' => 'batch', 'orderable' => false, 'searchable' => false],
        ['data' => 'admission_batch', 'name' => 'admission_batch', 'orderable' => false, 'searchable' => false],
        ['data' => 'registered_person', 'name' => 'registered_person', 'orderable' => false, 'searchable' => false],
        ['data' => 'subject', 'name' => 'subject', 'orderable' => false, 'searchable' => false],
        ['data' => 'subject_area', 'name' => 'subject_area', 'orderable' => false, 'searchable' => false],
        ['data' => 'reg_fee', 'name' => 'reg_fee', 'orderable' => false, 'searchable' => false],
        ['data' => 'status', 'name' => 'status', 'orderable' => false, 'searchable' => false],
        ['data' => 'course', 'name' => 'course', 'orderable' => false, 'searchable' => false],
        ['data' => 'application_number', 'name' => 'application_number', 'orderable' => false, 'searchable' => false],
        ['data' => 'enrolment_number', 'name' => 'enrolment_number', 'orderable' => false, 'searchable' => false],
        ['data' => 'mail', 'name' => 'mail', 'orderable' => false, 'searchable' => false],
        ['data' => 'st', 'name' => 'st', 'orderable' => false, 'searchable' => false],
        ['data' => 'phy', 'name' => 'phy', 'orderable' => false, 'searchable' => false],
        ['data' => 'che', 'name' => 'che', 'orderable' => false, 'searchable' => false],
        ['data' => 'bio', 'name' => 'bio', 'orderable' => false, 'searchable' => false],
        ['data' => 'remarks', 'name' => 'remarks', 'orderable' => false, 'searchable' => false],
        ['data' => 'actions', 'name' => 'actions', 'orderable' => false, 'searchable' => false],
    ]);
@endphp

<div id="bosseConvertedLeadsConfig" data-data-url="{{ route('admin.bosse-converted-leads.data') }}" style="display:none"></div>
<script type="application/json" id="bosseConvertedLeadsColumnsData">{!! json_encode($bosseConvertedLeadsColumns) !!}</script>

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
        min-width: 120px;
    }

    #convertedLeadsTable thead th:nth-child(5),
    #convertedLeadsTable tbody td:nth-child(5) {
        min-width: 220px;
    }

    #convertedLeadsTable thead th:nth-child(6),
    #convertedLeadsTable tbody td:nth-child(6) {
        min-width: 180px;
    }

    #convertedLeadsTable thead th:nth-child(7),
    #convertedLeadsTable tbody td:nth-child(7) {
        min-width: 140px;
    }

    #convertedLeadsTable thead th:nth-child(8),
    #convertedLeadsTable tbody td:nth-child(8) {
        min-width: 180px;
    }

    #convertedLeadsTable thead th:nth-child(9),
    #convertedLeadsTable tbody td:nth-child(9) {
        min-width: 180px;
    }

    #convertedLeadsTable thead th:nth-child(10),
    #convertedLeadsTable tbody td:nth-child(10) {
        min-width: 160px;
    }

    #convertedLeadsTable thead th:nth-child(11),
    #convertedLeadsTable tbody td:nth-child(11) {
        min-width: 140px;
    }

    #convertedLeadsTable thead th:nth-child(12),
    #convertedLeadsTable tbody td:nth-child(12) {
        min-width: 140px;
    }

    #convertedLeadsTable thead th:nth-child(13),
    #convertedLeadsTable tbody td:nth-child(13) {
        min-width: 140px;
    }

    #convertedLeadsTable thead th:nth-child(14),
    #convertedLeadsTable tbody td:nth-child(14) {
        min-width: 140px;
    }

    #convertedLeadsTable thead th:nth-child(15),
    #convertedLeadsTable tbody td:nth-child(15) {
        min-width: 140px;
    }

    #convertedLeadsTable thead th:nth-child(16),
    #convertedLeadsTable tbody td:nth-child(16) {
        min-width: 140px;
    }

    #convertedLeadsTable thead th:nth-child(17),
    #convertedLeadsTable tbody td:nth-child(17) {
        min-width: 140px;
    }

    #convertedLeadsTable thead th:nth-child(18),
    #convertedLeadsTable tbody td:nth-child(18) {
        min-width: 200px;
    }

    #convertedLeadsTable thead th:nth-child(19),
    #convertedLeadsTable tbody td:nth-child(19) {
        min-width: 120px;
    }

    #convertedLeadsTable thead th:nth-child(20),
    #convertedLeadsTable tbody td:nth-child(20) {
        min-width: 120px;
    }

    #convertedLeadsTable thead th:nth-child(21),
    #convertedLeadsTable tbody td:nth-child(21) {
        min-width: 180px;
    }

    #convertedLeadsTable thead th:nth-child(22),
    #convertedLeadsTable tbody td:nth-child(22) {
        min-width: 140px;
    }

    #convertedLeadsTable thead th:nth-child(23),
    #convertedLeadsTable tbody td:nth-child(23) {
        min-width: 140px;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        const configEl = document.getElementById('bosseConvertedLeadsConfig');
        const bosseDataUrl = configEl ? configEl.dataset.dataUrl : '';
        const columnsEl = document.getElementById('bosseConvertedLeadsColumnsData');
        const bosseConvertedLeadsColumns = columnsEl ? JSON.parse(columnsEl.textContent || '[]') : [];

        function getBosseFilterParams() {
            return {
                filter_search: ($('#search').val() || '').trim(),
                batch_id: $('#batch_id').val() || '',
                admission_batch_id: ($('#admission_batch_id').val() || $('#admission_batch_id').data('selected') || '') || '',
                date_from: $('#date_from').val() || '',
                date_to: $('#date_to').val() || '',
                status: $('#status').val() || '',
                reg_fee: $('#reg_fee').val() || ''
            };
        }

        function syncBosseMobileCards(json) {
            const $wrap = $('#bosseMobileCardsWrap');
            if (!json || !Array.isArray(json.data)) {
                return;
            }

            if (json.data.length === 0) {
                $wrap.html(`
                    <div class="text-center py-5">
                        <div class="text-muted">
                            <i class="ti ti-check-circle f-48 mb-3 d-block"></i>
                            <h5>No BOSSE converted leads found</h5>
                            <p>Try adjusting your filters or check back later.</p>
                        </div>
                    </div>
                `);
                return;
            }

            let html = '';
            json.data.forEach(function(row) {
                html += row.mobile_card || '';
            });
            $wrap.html(html);
        }

        function initBosseConvertedLeadsDataTable() {
            if ($.fn.DataTable.isDataTable('#convertedLeadsTable')) {
                $('#convertedLeadsTable').DataTable().destroy();
            }

            $('#convertedLeadsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: bosseDataUrl,
                    type: 'GET',
                    data: function(d) {
                        $.extend(d, getBosseFilterParams());
                    },
                    dataSrc: function(json) {
                        syncBosseMobileCards(json);
                        return json.data;
                    },
                    error: function() {
                        if (typeof showToast === 'function') {
                            showToast('Error loading BOSSE converted leads.', 'error');
                        }
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
                columns: bosseConvertedLeadsColumns
            });
        }

        initBosseConvertedLeadsDataTable();
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
        $('a[href="{{ route("admin.bosse-converted-leads.index") }}"]').on('click', function(e) {
            e.preventDefault();
            window.location.href = '{{ route("admin.bosse-converted-leads.index") }}';
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
                            if ($.fn.DataTable.isDataTable('#convertedLeadsTable')) {
                                $('#convertedLeadsTable').DataTable().ajax.reload(null, false);
                            } else {
                                location.reload();
                            }
                        }, 600);
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

            if (field === 'subject_id') {
                const courseId = container.data('course-id');
                editForm = createSubjectSelect(courseId, currentId);
            } else if (field === 'subject_area_id') {
                editForm = createSubjectAreaSelect(currentId);
            } else if (field === 'batch_id') {
                const courseId = container.data('course-id');
                editForm = createBatchSelect(courseId, currentId);
            } else if (field === 'admission_batch_id') {
                const batchId = container.data('batch-id');
                editForm = createAdmissionBatchSelect(batchId, currentId);
            } else if (field === 'academic_assistant_id') {
                editForm = createAcademicAssistantSelect(currentId);
            } else if (['status', 'reg_fee', 'exam_fee', 'id_card', 'tma'].includes(field)) {
                editForm = createSelectField(field, currentValue);
            } else if (field === 'phone') {
                const currentCode = container.siblings('.inline-code-value').data('current') || '';
                editForm = createPhoneField(currentCode, currentValue);
            } else {
                editForm = createInputField(field, currentValue);
            }

            container.addClass('editing');
            container.append(editForm);

            // Load options for select fields that need dynamic loading
            if (field === 'subject_id') {
                const courseId = container.data('course-id');
                const select = container.find('select');
                loadSubjects(courseId, select, currentId);
            } else if (field === 'subject_area_id') {
                loadSubjectAreas(container.find('select'), currentId);
            } else if (field === 'batch_id') {
                const courseId = container.data('course-id');
                const select = container.find('select');
                loadBatches(courseId, select, currentId);
            } else if (field === 'admission_batch_id') {
                const batchId = container.data('batch-id');
                const select = container.find('select');
                loadAdmissionBatches(batchId, select, currentId);
            } else if (field === 'academic_assistant_id') {
                const select = container.find('select');
                loadAcademicAssistants(select, currentId);
            } else {
                container.find('input, select').first().focus();
            }
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
                        // Update display value with the response value (which should be the title, not ID)
                        let displayValue = response.value || 'N/A';
                        container.find('.display-value').text(displayValue);
                        // Update the data-current attribute with the new display value
                        container.data('current', displayValue);
                        // Update data-current-id for fields that use it (store the ID, not the display value)
                        if (field === 'batch_id' || field === 'subject_id' || field === 'subject_area_id' || field === 'admission_batch_id' || field === 'academic_assistant_id') {
                            container.data('current-id', value || '');
                        }

                        // Special handling for DOB field - convert Y-m-d to d-m-Y for display
                        if (field === 'dob' && displayValue && displayValue !== 'N/A') {
                            try {
                                const date = new Date(displayValue);
                                if (!isNaN(date.getTime())) {
                                    displayValue = date.toLocaleDateString('en-GB'); // d/m/Y format
                                    container.find('.display-value').text(displayValue);
                                }
                            } catch (e) {
                                // Keep original value if conversion fails
                            }
                        }

                        // If batch_id changed, update the admission_batch_id container's data-batch-id
                        if (field === 'batch_id') {
                            const row = container.closest('tr');
                            const admissionBatchContainer = row.find('.inline-edit[data-field="admission_batch_id"]');
                            if (admissionBatchContainer.length) {
                                admissionBatchContainer.data('batch-id', value || '');
                                // Clear admission batch if batch changed
                                admissionBatchContainer.find('.display-value').text('N/A');
                                admissionBatchContainer.data('current-id', '');
                            }
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
                    let errorMessage = 'Update failed';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        } else if (xhr.responseJSON.errors) {
                            // Handle validation errors - show user-friendly messages
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
            if (field === 'dob') {
                const today = new Date().toISOString().split('T')[0];
                let value = '';

                // Debug: Log the current value
                console.log('DOB currentValue:', currentValue);

                // Convert date to Y-m-d format for date input
                if (currentValue && currentValue !== 'N/A' && currentValue !== '') {
                    // Check if it's already in Y-m-d format (from database)
                    if (currentValue.match(/^\d{4}-\d{2}-\d{2}$/)) {
                        value = currentValue;
                        console.log('DOB: Using Y-m-d format:', value);
                    } else {
                        // Try to parse d-m-Y format (from display)
                        const dateParts = currentValue.split('-');
                        if (dateParts.length === 3) {
                            // Check if it's d-m-Y format (day-month-year)
                            if (dateParts[0].length <= 2 && dateParts[1].length <= 2 && dateParts[2].length === 4) {
                                const day = dateParts[0].padStart(2, '0');
                                const month = dateParts[1].padStart(2, '0');
                                const year = dateParts[2];
                                value = `${year}-${month}-${day}`;
                                console.log('DOB: Converted from d-m-Y format:', value);
                            }
                        }
                    }
                }

                console.log('DOB final value:', value);

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

            // Handle number fields with max 20
            if (['st', 'phy', 'che', 'bio'].includes(field)) {
                const inputType = 'number';
                const displayValue = currentValue === 'N/A' ? '' : currentValue;
                const commonAttrs = 'autocomplete="off" autocapitalize="off" spellcheck="false" name="inline-temp" min="0" max="20"';
                const valueAttr = `value="${displayValue}"`;
                return `
                    <div class="edit-form">
                        <input type="${inputType}" ${valueAttr} ${commonAttrs} class="form-control form-control-sm" oninput="if(this.value > 20) this.value = 20; if(this.value < 0) this.value = 0;">
                        <div class="btn-group mt-1">
                            <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                            <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                        </div>
                    </div>
                `;
            }

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
                case 'status':
                    options = '<option value="">Select Registration Fee</option>';
                    options += `<option value="Paid" ${selectedValue === 'Paid' ? 'selected' : ''}>Paid</option>`;
                    options += `<option value="Received" ${selectedValue === 'Received' ? 'selected' : ''}>Received</option>`;
                    options += `<option value="Admission cancel" ${selectedValue === 'Admission cancel' ? 'selected' : ''}>Admission cancel</option>`;
                    options += `<option value="Active" ${selectedValue === 'Active' ? 'selected' : ''}>Active</option>`;
                    options += `<option value="Inactive" ${selectedValue === 'Inactive' ? 'selected' : ''}>Inactive</option>`;
                    break;
                case 'reg_fee':
                    options = '<option value="">Select Registration Status</option>';
                    options += `<option value="Handover -1" ${selectedValue === 'Handover -1' ? 'selected' : ''}>Handover -1</option>`;
                    options += `<option value="Handover - 2" ${selectedValue === 'Handover - 2' ? 'selected' : ''}>Handover - 2</option>`;
                    options += `<option value="Handover - 3" ${selectedValue === 'Handover - 3' ? 'selected' : ''}>Handover - 3</option>`;
                    options += `<option value="Handover - 4" ${selectedValue === 'Handover - 4' ? 'selected' : ''}>Handover - 4</option>`;
                    options += `<option value="Handover - 5" ${selectedValue === 'Handover - 5' ? 'selected' : ''}>Handover - 5</option>`;
                    options += `<option value="Paid" ${selectedValue === 'Paid' ? 'selected' : ''}>Paid</option>`;
                    options += `<option value="Admission cancel" ${selectedValue === 'Admission cancel' ? 'selected' : ''}>Admission cancel</option>`;
                    break;
                case 'exam_fee':
                    options = '<option value="">Select EXAM FEE</option>';
                    options += `<option value="Pending" ${selectedValue === 'Pending' ? 'selected' : ''}>Pending</option>`;
                    options += `<option value="Not Paid" ${selectedValue === 'Not Paid' ? 'selected' : ''}>Not Paid</option>`;
                    options += `<option value="Paid" ${selectedValue === 'Paid' ? 'selected' : ''}>Paid</option>`;
                    break;
                case 'id_card':
                    options = '<option value="">Select ID CARD</option>';
                    options += `<option value="processing" ${selectedValue === 'processing' ? 'selected' : ''}>processing</option>`;
                    options += `<option value="download" ${selectedValue === 'download' ? 'selected' : ''}>download</option>`;
                    options += `<option value="not downloaded" ${selectedValue === 'not downloaded' ? 'selected' : ''}>not downloaded</option>`;
                    break;
                case 'tma':
                    options = '<option value="">Select TMA</option>';
                    options += `<option value="Uploaded" ${selectedValue === 'Uploaded' ? 'selected' : ''}>Uploaded</option>`;
                    options += `<option value="Not Upload" ${selectedValue === 'Not Upload' ? 'selected' : ''}>Not Upload</option>`;
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

        function createSubjectSelect(courseId, currentId) {
            return `
                <div class="edit-form">
                    <select class="form-select form-select-sm">
                        <option value="">Loading...</option>
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createSubjectAreaSelect(currentId) {
            return `
                <div class="edit-form">
                    <select class="form-select form-select-sm">
                        <option value="">Loading...</option>
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createBatchSelect(courseId, currentId) {
            return `
                <div class="edit-form">
                    <select class="form-select form-select-sm">
                        <option value="">Loading...</option>
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createAdmissionBatchSelect(batchId, currentId) {
            return `
                <div class="edit-form">
                    <select class="form-select form-select-sm">
                        <option value="">Loading...</option>
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createAcademicAssistantSelect(currentId) {
            return `
                <div class="edit-form">
                    <select class="form-select form-select-sm">
                        <option value="">Loading...</option>
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        // Load options for select fields
        $(document).on('click', '.edit-btn', function() {
            const container = $(this).closest('.inline-edit');
            const field = container.data('field');
            const select = container.find('select');
            const currentValue = container.data('current') !== undefined ? String(container.data('current')).trim() : container.find('.display-value').text().trim();
            const currentId = container.data('current-id') !== undefined ? String(container.data('current-id')).trim() : '';

            if (field === 'subject_id') {
                const courseId = container.data('course-id');
                loadSubjects(courseId, select, currentId);
            } else if (field === 'subject_area_id') {
                loadSubjectAreas(select, currentId);
            } else if (field === 'batch_id') {
                const courseId = container.data('course-id');
                loadBatches(courseId, select, currentId);
            } else if (field === 'admission_batch_id') {
                const batchId = container.data('batch-id');
                loadAdmissionBatches(batchId, select, currentId);
            } else if (field === 'academic_assistant_id') {
                loadAcademicAssistants(select, currentId);
            }
        });

        function loadSubjectAreas(select, currentId) {
            $.get('/api/subject-areas')
                .done(function(subjectAreas) {
                    let options = '<option value="">Select Subject Area</option>';
                    subjectAreas.forEach(function(subjectArea) {
                        const isSelected = (currentId && String(currentId) === String(subjectArea.id)) ? 'selected' : '';
                        options += `<option value="${subjectArea.id}" ${isSelected}>${subjectArea.title}</option>`;
                    });
                    select.html(options);
                    select.focus();
                })
                .fail(function() {
                    select.html('<option value="">Error loading subject areas</option>');
                });
        }

        function loadSubjects(courseId, select, currentId) {
            $.get(`/api/subjects/by-course/${courseId}`)
                .done(function(subjects) {
                    let options = '<option value="">Select Subject</option>';
                    subjects.forEach(function(subject) {
                        const isSelected = String(currentId) === String(subject.id) ? 'selected' : '';
                        options += `<option value="${subject.id}" ${isSelected}>${subject.title}</option>`;
                    });
                    select.html(options);
                })
                .fail(function() {
                    select.html('<option value="">Error loading subjects</option>');
                });
        }

        function loadBatches(courseId, select, currentId) {
            if (!courseId) {
                select.html('<option value="">No course selected</option>');
                return;
            }

            $.get(`/api/batches/by-course/${courseId}`)
                .done(function(response) {
                    let options = '<option value="">Select Batch</option>';
                    if (response.success && response.batches) {
                        response.batches.forEach(function(batch) {
                            // Only select if currentId is not empty and matches
                            const isSelected = (currentId && String(currentId) === String(batch.id)) ? 'selected' : '';
                            options += `<option value="${batch.id}" ${isSelected}>${batch.title}</option>`;
                        });
                    }
                    select.html(options);
                    select.focus();
                })
                .fail(function() {
                    select.html('<option value="">Error loading batches</option>');
                });
        }

        function loadAdmissionBatches(batchId, select, currentId) {
            $.get(`/api/admission-batches/by-batch/${batchId}`)
                .done(function(batches) {
                    let options = '<option value="">Select Admission Batch</option>';
                    batches.forEach(function(batch) {
                        const isSelected = String(currentId) === String(batch.id) ? 'selected' : '';
                        options += `<option value="${batch.id}" ${isSelected}>${batch.title}</option>`;
                    });
                    select.html(options);
                })
                .fail(function() {
                    select.html('<option value="">Error loading admission batches</option>');
                });
        }

        function loadAcademicAssistants(select, currentId) {
            $.get('/api/academic-assistants')
                .done(function(assistants) {
                    let options = '<option value="">Select Academic Assistant</option>';
                    assistants.forEach(function(assistant) {
                        const isSelected = String(currentId) === String(assistant.id) ? 'selected' : '';
                        options += `<option value="${assistant.id}" ${isSelected}>${assistant.name}</option>`;
                    });
                    select.html(options);
                })
                .fail(function() {
                    select.html('<option value="">Error loading academic assistants</option>');
                });
        }
    });

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
                        if ($.fn.DataTable.isDataTable('#convertedLeadsTable')) {
                            $('#convertedLeadsTable').DataTable().ajax.reload(null, false);
                        } else {
                            location.reload();
                        }
                    }, 400);
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
                        if ($.fn.DataTable.isDataTable('#convertedLeadsTable')) {
                            $('#convertedLeadsTable').DataTable().ajax.reload(null, false);
                        } else {
                            location.reload();
                        }
                    }, 400);
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
                        location.reload();
                    }
                } else {
                    location.reload();
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
