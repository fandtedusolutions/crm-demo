@extends('layouts.mantis')

@section('title', 'Hotel Management Converted Leads')

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
                    <h5 class="m-b-10">Hotel Management Converted Leads Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.converted-leads.index') }}">Converted Leads</a></li>
                    <li class="breadcrumb-item">Hotel Management</li>
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
                    <a href="{{ route('admin.bosse-converted-leads.index') }}" class="btn btn-outline-warning">
                        <i class="ti ti-school-2"></i> BOSSE Converted Leads
                    </a>
                    <a href="{{ route('admin.ugpg-converted-leads.index') }}" class="btn btn-outline-warning">
                        <i class="ti ti-graduation"></i> UG/PG Converted Leads
                    </a>
                    <a href="{{ route('admin.edumaster-converted-leads.index') }}" class="btn btn-outline-warning">
                        <i class="ti ti-graduation"></i> EduMaster Converted Leads
                    </a>
                    <a href="{{ route('admin.hotel-management-converted-leads.index') }}" class="btn btn-info active">
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

@include('admin.converted-leads.partials.faculty-list-nav', ['activeFacultyRoute' => $activeFacultyRoute ?? null])

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
                <form method="GET" action="{{ route('admin.hotel-management-converted-leads.index') }}" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search"
                                value="{{ request('search') }}" placeholder="Name, Phone, Email, Register Number">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="batch_id" class="form-label">Batch</label>
                            <select class="form-select" id="batch_id" name="batch_id">
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
                            <label for="app" class="form-label">App</label>
                            <select class="form-select" id="app" name="app">
                                <option value="">All</option>
                                <option value="Provided" {{ request('app')==='Provided' ? 'selected' : '' }}>Provided</option>
                                <option value="Ad cancel" {{ request('app')==='Ad cancel' ? 'selected' : '' }}>Ad cancel</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="group" class="form-label">Group</label>
                            <select class="form-select" id="group" name="group">
                                <option value="">All</option>
                                <option value="Assigned" {{ request('group')==='Assigned' ? 'selected' : '' }}>Assigned</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="interview" class="form-label">Interview</label>
                            <select class="form-select" id="interview" name="interview">
                                <option value="">All</option>
                                <option value="Failed" {{ request('interview')==='Failed' ? 'selected' : '' }}>Failed</option>
                                <option value="Passed" {{ request('interview')==='Passed' ? 'selected' : '' }}>Passed</option>
                                <option value="Ad cancel" {{ request('interview')==='Ad cancel' ? 'selected' : '' }}>Ad cancel</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="howmany_interview" class="form-label">How Many Interview</label>
                            <select class="form-select" id="howmany_interview" name="howmany_interview">
                                <option value="">All</option>
                                <option value="0" {{ request('howmany_interview')==='0' ? 'selected' : '' }}>0</option>
                                <option value="1" {{ request('howmany_interview')==='1' ? 'selected' : '' }}>1</option>
                                <option value="2" {{ request('howmany_interview')==='2' ? 'selected' : '' }}>2</option>
                                <option value="3" {{ request('howmany_interview')==='3' ? 'selected' : '' }}>3</option>
                                <option value="4" {{ request('howmany_interview')==='4' ? 'selected' : '' }}>4</option>
                                <option value="5" {{ request('howmany_interview')==='5' ? 'selected' : '' }}>5</option>
                                <option value="6" {{ request('howmany_interview')==='6' ? 'selected' : '' }}>6</option>
                                <option value="7" {{ request('howmany_interview')==='7' ? 'selected' : '' }}>7</option>
                                <option value="8" {{ request('howmany_interview')==='8' ? 'selected' : '' }}>8</option>
                                <option value="9" {{ request('howmany_interview')==='9' ? 'selected' : '' }}>9</option>
                                <option value="10" {{ request('howmany_interview')==='10' ? 'selected' : '' }}>10</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-search"></i> Filter
                            </button>
                            <a href="{{ route('admin.hotel-management-converted-leads.index') }}" class="btn btn-secondary" id="hotelClearFilters">
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
        <div class="card">
            <div class="card-header">
                <h5>Hotel Management Converted Leads</h5>
                <div class="card-header-right">
                    <div class="btn-group card-option">
                        <button type="button" class="btn dropdown-toggle btn-icon" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="feather icon-more-vertical"></i>
                        </button>
                        <ul class="list-unstyled card-option dropdown-menu dropdown-menu-end">
                            <li class="dropdown-item full-card"><a href="#!"><span><i class="feather icon-maximize-2"></i> maximize</span><span style="display:none"><i class="feather icon-minimize-2"></i> Restore</span></a></li>
                            <li class="dropdown-item minimize-card"><a href="#!"><span><i class="feather icon-minus"></i> collapse</span><span style="display:none"><i class="feather icon-plus"></i> expand</span></a></li>
                            <li class="dropdown-item reload-card"><a href="#!"><i class="feather icon-refresh-cw"></i> reload</a></li>
                            <li class="dropdown-item close-card"><a href="#!"><i class="feather icon-trash-2"></i> remove</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="hotelManagementTable">
                        <thead>
                            <tr>
                                <th>SL No</th>
                                <th>Academic</th>
                                <th>Support</th>
                                <th>Registration Number</th>
                                <th>Course Flag</th>
                                <th>Converted Date</th>
                                <th>DOB</th>
                                <th>Name</th>
                                    <th>Type</th>
                                <th>Phone</th>
                                <th>WhatsApp</th>
                                @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                <th>Parent Phone</th>
                                @endif
                                <th>Batch</th>
                                <th>Admission Batch</th>
                                <th>Internship ID</th>
                                <th>App</th>
                                <th>Group</th>
                                <th>Interview</th>
                                <th>How Many Interview</th>
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
                                    <div class="inline-edit" data-field="registration_number" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->registration_number }}">
                                        <span class="display-value">{{ $convertedLead->studentDetails?->registration_number ?? '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $convertedLead->converted_date ? \Carbon\Carbon::parse($convertedLead->converted_date)->format('d-m-Y') : '-' }}</td>
                                <td>
                                    <div class="inline-edit" data-field="dob" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->dob }}">
                                        <span class="display-value">{{ $convertedLead->dob ? \Carbon\Carbon::parse($convertedLead->dob)->format('d-m-Y') : '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
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
                                <td>
                                    <div class="inline-edit" data-field="phone" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->phone }}" data-code="{{ $convertedLead->code }}">
                                        <span class="display-value">
                                            @if($convertedLead->code && $convertedLead->phone)
                                            {{ $convertedLead->code }} {{ $convertedLead->phone }}
                                            @else
                                            {{ $convertedLead->phone ?? '-' }}
                                            @endif
                                        </span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
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
                                    <div class="inline-edit" data-field="internship_id" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->internship_id }}">
                                        <span class="display-value">{{ $convertedLead->studentDetails?->internship_id ?? 'N/A' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="app" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->app }}">
                                        <span class="display-value">{{ $convertedLead->studentDetails?->app ?? '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="group" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->group }}">
                                        <span class="display-value">{{ $convertedLead->studentDetails?->group ?? '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="interview" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->interview }}">
                                        <span class="display-value">{{ $convertedLead->studentDetails?->interview ?? '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="howmany_interview" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->howmany_interview }}">
                                        <span class="display-value">{{ $convertedLead->studentDetails?->howmany_interview ?? '0' }}</span>
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
                                        <!-- ID Card Generation/View Buttons -->
                                        @php
                                        $idCardRecord = \App\Models\ConvertedLeadIdCard::where('converted_lead_id', $convertedLead->id)->first();
                                        @endphp

                                        @if($idCardRecord)
                                        <a href="{{ route('admin.converted-leads.id-card-view', $convertedLead->id) }}" class="btn btn-sm btn-success" title="View ID Card" target="_blank">
                                            <i class="ti ti-id"></i>
                                        </a>
                                        @else
                                        <form class="d-inline" action="{{ route('admin.converted-leads.id-card-generate', $convertedLead->id) }}" method="POST" class="id-card-generate-form">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning" title="Generate ID Card" data-loading-text="Generating...">
                                                <i class="ti ti-id"></i>
                                            </button>
                                        </form>
                                        @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="19" class="text-center">No Hotel Management converted leads found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
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

@php
    $hotelManagementColumns = [
        ['data' => 'index', 'name' => 'index', 'orderable' => false, 'searchable' => false],
        ['data' => 'academic', 'name' => 'academic', 'orderable' => false, 'searchable' => false],
        ['data' => 'support', 'name' => 'support', 'orderable' => false, 'searchable' => false],
        ['data' => 'registration_number', 'name' => 'registration_number', 'orderable' => false, 'searchable' => false],
        ['data' => 'course_flag', 'name' => 'course_flag', 'orderable' => false, 'searchable' => false],
        ['data' => 'converted_date', 'name' => 'converted_date', 'orderable' => false, 'searchable' => false],
        ['data' => 'dob', 'name' => 'dob', 'orderable' => false, 'searchable' => false],
        ['data' => 'name_col', 'name' => 'name_col', 'orderable' => false, 'searchable' => false],
        ['data' => 'type', 'name' => 'type', 'orderable' => false, 'searchable' => false],
        ['data' => 'phone', 'name' => 'phone', 'orderable' => false, 'searchable' => false],
        ['data' => 'whatsapp', 'name' => 'whatsapp', 'orderable' => false, 'searchable' => false],
    ];

    if (\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor()) {
        $hotelManagementColumns[] = ['data' => 'parent_phone', 'name' => 'parent_phone', 'orderable' => false, 'searchable' => false];
    }

    $hotelManagementColumns = array_merge($hotelManagementColumns, [
        ['data' => 'batch', 'name' => 'batch', 'orderable' => false, 'searchable' => false],
        ['data' => 'admission_batch', 'name' => 'admission_batch', 'orderable' => false, 'searchable' => false],
        ['data' => 'internship_id', 'name' => 'internship_id', 'orderable' => false, 'searchable' => false],
        ['data' => 'app', 'name' => 'app', 'orderable' => false, 'searchable' => false],
        ['data' => 'group', 'name' => 'group', 'orderable' => false, 'searchable' => false],
        ['data' => 'interview', 'name' => 'interview', 'orderable' => false, 'searchable' => false],
        ['data' => 'howmany_interview', 'name' => 'howmany_interview', 'orderable' => false, 'searchable' => false],
        ['data' => 'remarks', 'name' => 'remarks', 'orderable' => false, 'searchable' => false],
        ['data' => 'actions', 'name' => 'actions', 'orderable' => false, 'searchable' => false],
    ]);
@endphp

<div id="hotelManagementConvertedLeadsConfig" data-data-url="{{ route('admin.hotel-management-converted-leads.data') }}" style="display:none"></div>
<script type="application/json" id="hotelManagementConvertedLeadsColumnsData">{!! json_encode($hotelManagementColumns) !!}</script>

@push('scripts')
<script>
    $(document).ready(function() {
        const configEl = document.getElementById('hotelManagementConvertedLeadsConfig');
        const hotelDataUrl = configEl ? configEl.dataset.dataUrl : '';
        const columnsEl = document.getElementById('hotelManagementConvertedLeadsColumnsData');
        const hotelManagementColumns = columnsEl ? JSON.parse(columnsEl.textContent || '[]') : [];

        function getHotelFilterParams() {
            return {
                filter_search: ($('#search').val() || '').trim(),
                batch_id: $('#batch_id').val() || '',
                admission_batch_id: ($('#admission_batch_id').val() || $('#admission_batch_id').data('selected') || '') || '',
                course_flag_id: $('#course_flag_id').val() || '',

                date_from: $('#date_from').val() || '',
                date_to: $('#date_to').val() || '',
                status: $('#status').val() || '',
                app: $('#app').val() || '',
                group: $('#group').val() || '',
                interview: $('#interview').val() || '',
                howmany_interview: $('#howmany_interview').val() || ''
            };
        }

        function updateHotelUrlWithFilters() {
            const f = getHotelFilterParams();
            const url = new URL(window.location.href);
            url.searchParams.set('search', f.filter_search || '');
            url.searchParams.set('batch_id', f.batch_id || '');
            url.searchParams.set('admission_batch_id', f.admission_batch_id || '');
            url.searchParams.set('course_flag_id', f.course_flag_id || '');
            url.searchParams.set('date_from', f.date_from || '');
            url.searchParams.set('date_to', f.date_to || '');
            url.searchParams.set('status', f.status || '');
            url.searchParams.set('app', f.app || '');
            url.searchParams.set('group', f.group || '');
            url.searchParams.set('interview', f.interview || '');
            url.searchParams.set('howmany_interview', f.howmany_interview || '');
            history.replaceState({}, '', url.toString());
        }

        window.reloadHotelManagementTable = function() {
            if ($.fn.DataTable.isDataTable('#hotelManagementTable')) {
                $('#hotelManagementTable').DataTable().ajax.reload(null, false);
            }
        };

        if ($.fn.DataTable.isDataTable('#hotelManagementTable')) {
            $('#hotelManagementTable').DataTable().destroy();
        }

        $('#hotelManagementTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: hotelDataUrl,
                type: 'GET',
                data: function(d) {
                    $.extend(d, getHotelFilterParams());
                },
                dataSrc: function(json) {
                    return (json && json.data) ? json.data : [];
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
            columns: hotelManagementColumns
        });

        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            updateHotelUrlWithFilters();
            window.reloadHotelManagementTable();
        });

        $('#hotelClearFilters').on('click', function(e) {
            e.preventDefault();
            $('#filterForm')[0].reset();
            $('#admission_batch_id').val('');
            updateHotelUrlWithFilters();
            window.reloadHotelManagementTable();
        });

        // Batch filter enhancement
        $('#batch_id').on('change', function() {
            updateHotelUrlWithFilters();
            window.reloadHotelManagementTable();
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
        loadAdmissionBatchesByBatch($('#batch_id').val(), $('#admission_batch_id').data('selected'));

        // On batch change â†’ reload admission batches
        $('#batch_id').on('change', function() {
            const bid = $(this).val();
            loadAdmissionBatchesByBatch(bid, '');
        });

        // Inline editing functionality
        $('.inline-edit .edit-btn').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const container = $(this).closest('.inline-edit');
            const field = container.data('field');
            const currentValue = container.data('current');
            const id = container.data('id');
            const code = container.data('code');

            if (container.hasClass('editing')) {
                return;
            }

            container.addClass('editing');

            let formHtml = '';

            if (field === 'phone') {
                formHtml = createPhoneField(code, currentValue);
            } else if (['app', 'group', 'interview', 'howmany_interview'].includes(field)) {
                formHtml = createSelectField(field, currentValue);
            } else if (field === 'admission_batch_id') {
                const batchId = container.data('batch-id');
                formHtml = createAdmissionBatchField(batchId, currentValue);
            } else {
                formHtml = createInputField(field, currentValue);
            }

            container.find('.display-value').hide();
            container.find('.edit-btn').hide();
            container.append(formHtml);

            // Load admission batches if it's an admission batch field
            if (field === 'admission_batch_id') {
                const batchId = container.data('batch-id');
                const $select = container.find('select');
                loadAdmissionBatchesForEdit($select, batchId, currentValue);
            }

            // Focus on input
            container.find('input, select').first().focus();
        });

        // Save edit
        $(document).off('click', '.save-edit').on('click', '.save-edit', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const $button = $(this);
            const container = $button.closest('.inline-edit');
            const field = container.data('field');
            const id = container.data('id');

            // Prevent double submission
            if ($button.prop('disabled') || $button.data('submitting')) {
                return false;
            }

            let value = '';
            if (field === 'phone') {
                const code = container.find('select[name="code"]').val();
                const phone = container.find('input[name="phone"]').val();
                value = code + '|' + phone;
            } else {
                value = container.find('input, select').val();
            }

            // Mark as submitting and show loading
            $button.data('submitting', true).prop('disabled', true).text('Saving...');

            $.ajax({
                url: '{{ route("admin.converted-leads.inline-update", ":id") }}'.replace(':id', id),
                method: 'POST',
                data: {
                    field: field,
                    value: value,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // Update display value
                        let displayValue = response.value || value;
                        if (field === 'phone') {
                            const parts = value.split('|');
                            if (parts.length === 2) {
                                displayValue = parts[0] + ' ' + parts[1];
                            }
                        }
                        container.find('.display-value').text(displayValue).show();
                        container.data('current', response.value || value);

                        // Show success message
                        show_alert('success', 'Updated successfully!');
                    } else {
                        show_alert('error', response.message || 'Update failed');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Update failed';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        const firstError = Object.values(errors)[0];
                        if (Array.isArray(firstError)) {
                            errorMessage = firstError[0];
                        } else {
                            errorMessage = firstError;
                        }
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    show_alert('error', errorMessage);
                },
                complete: function() {
                    // Reset button state
                    $button.data('submitting', false).prop('disabled', false).text('Save');
                    container.removeClass('editing');
                    container.find('.edit-form').remove();
                    container.find('.edit-btn').show();
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
            if (field === 'dob') {
                const today = new Date().toISOString().split('T')[0];
                const value = (currentValue && currentValue !== '-') ? currentValue : '';
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

            const inputType = 'text';
            const displayValue = currentValue === '-' ? '' : currentValue;
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

            if (codeOptionsEl) {
                try {
                    codeOptions = JSON.parse(codeOptionsEl.textContent);
                } catch (e) {
                    console.error('Error parsing country codes:', e);
                }
            }

            const codeOptionsHtml = Object.entries(codeOptions).map(([code, country]) =>
                `<option value="${code}" ${code === currentCode ? 'selected' : ''}>${country} (${code})</option>`
            ).join('');

            return `
                <div class="edit-form">
                    <div class="row g-1">
                        <div class="col-4">
                            <select name="code" class="form-select form-select-sm">
                                <option value="">Select Code</option>
                                ${codeOptionsHtml}
                            </select>
                        </div>
                        <div class="col-8">
                            <input type="tel" name="phone" value="${currentPhone || ''}" class="form-control form-control-sm" placeholder="Phone Number">
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

            if (field === 'app') {
                options = `
                    <option value="">Select App</option>
                    <option value="Provided" ${currentValue === 'Provided' ? 'selected' : ''}>Provided</option>
                    <option value="Ad cancel" ${currentValue === 'Ad cancel' ? 'selected' : ''}>Ad cancel</option>
                `;
            } else if (field === 'group') {
                options = `
                    <option value="">Select Group</option>
                    <option value="Assigned" ${currentValue === 'Assigned' ? 'selected' : ''}>Assigned</option>
                `;
            } else if (field === 'interview') {
                options = `
                    <option value="">Select Interview</option>
                    <option value="Failed" ${currentValue === 'Failed' ? 'selected' : ''}>Failed</option>
                    <option value="Passed" ${currentValue === 'Passed' ? 'selected' : ''}>Passed</option>
                    <option value="Ad cancel" ${currentValue === 'Ad cancel' ? 'selected' : ''}>Ad cancel</option>
                `;
            } else if (field === 'howmany_interview') {
                options = `
                    <option value="">Select Count</option>
                    <option value="0" ${currentValue === '0' ? 'selected' : ''}>0</option>
                    <option value="1" ${currentValue === '1' ? 'selected' : ''}>1</option>
                    <option value="2" ${currentValue === '2' ? 'selected' : ''}>2</option>
                    <option value="3" ${currentValue === '3' ? 'selected' : ''}>3</option>
                    <option value="4" ${currentValue === '4' ? 'selected' : ''}>4</option>
                    <option value="5" ${currentValue === '5' ? 'selected' : ''}>5</option>
                    <option value="6" ${currentValue === '6' ? 'selected' : ''}>6</option>
                    <option value="7" ${currentValue === '7' ? 'selected' : ''}>7</option>
                    <option value="8" ${currentValue === '8' ? 'selected' : ''}>8</option>
                    <option value="9" ${currentValue === '9' ? 'selected' : ''}>9</option>
                    <option value="10" ${currentValue === '10' ? 'selected' : ''}>10</option>
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

        function createAdmissionBatchField(batchId, currentValue) {
            return `
                <div class="edit-form">
                    <select class="form-select form-select-sm" data-batch-id="${batchId}">
                        <option value="">Select Admission Batch</option>
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function loadAdmissionBatchesForEdit($select, batchId, currentValue) {
            if (!batchId) {
                $select.html('<option value="">No batch selected</option>');
                return;
            }

            $.get(`/api/admission-batches/by-batch/${batchId}`).done(function(list) {
                let options = '<option value="">Select Admission Batch</option>';
                list.forEach(function(item) {
                    const selected = String(currentValue) === String(item.id) ? 'selected' : '';
                    options += `<option value="${item.id}" ${selected}>${item.title}</option>`;
                });
                $select.html(options);
            }).fail(function() {
                $select.html('<option value="">Error loading admission batches</option>');
            });
        }

        // Handle ID card generation form submission
        $(document).off('submit', '.id-card-generate-form').on('submit', '.id-card-generate-form', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            const $form = $(this);
            const $button = $form.find('button[type="submit"]');
            const originalText = $button.text();
            const loadingText = $button.data('loading-text') || 'Generating...';

            $button.prop('disabled', true).text(loadingText);

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                success: function(response) {
                    if (response.success) {
                        show_alert('success', response.message || 'ID Card generated successfully!');
                        // Reload the page to show updated buttons
                        setTimeout(() => {
                            window.reloadHotelManagementTable();
                        }, 1000);
                    } else {
                        show_alert('error', response.message || 'Failed to generate ID Card');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Failed to generate ID Card';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    show_alert('error', errorMessage);
                },
                complete: function() {
                    $button.prop('disabled', false).text(originalText);
                }
            });
        });
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
                        window.reloadHotelManagementTable();
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
                        window.reloadHotelManagementTable();
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
                if ($.fn.DataTable.isDataTable('#hotelManagementTable')) {
                    const dt = $('#hotelManagementTable').DataTable();
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
