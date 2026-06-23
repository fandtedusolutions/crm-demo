@extends('layouts.mantis')

@section('title', 'EduMaster Converted Leads')

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
                    <h5 class="m-b-10">EduMaster Converted Leads Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.converted-leads.index') }}">Converted Leads</a></li>
                    <li class="breadcrumb-item">EduMaster</li>
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
                        <i class="ti ti-school"></i> National Institute of Open Schooling Converted Leads
                    </a>
                    <a href="{{ route('admin.bosse-converted-leads.index') }}" class="btn btn-outline-warning">
                        <i class="ti ti-school-2"></i> Board of Open Schooling and Skill Education Converted Leads
                    </a>
                    <a href="{{ route('admin.ugpg-converted-leads.index') }}" class="btn btn-outline-warning">
                        <i class="ti ti-graduation"></i> UG/PG Converted Leads
                    </a>
                    <a href="{{ route('admin.edumaster-converted-leads.index') }}" class="btn btn-warning active">
                        <i class="ti ti-graduation"></i> EduMaster Converted Leads
                    </a>
                    <a href="{{ route('admin.hotel-management-converted-leads.index') }}" class="btn btn-outline-info">
                        <i class="ti ti-building"></i> Hotel Management Converted Leads
                    </a>
                    <a href="{{ route('admin.gmvss-converted-leads.index') }}" class="btn btn-outline-info">
                        <i class="ti ti-certificate"></i> Grameen Mukt Vidhyalayi Shiksha Sansthan Converted Leads
                    </a>
                    <a href="{{ route('admin.digital-marketing-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-marketing"></i> AI Integrated Digital Marketing Converted Leads
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
                        <i class="ti ti-palette"></i> Diploma in Graphic Designing Converted Leads
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
                        <i class="ti ti-video"></i> CreateX AI Converted Leads
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
                        <i class="ti ti-user-star"></i> Board of Open Schooling and Skill Education Converted Mentor List
                    </a>
                    <a href="{{ route('admin.mentor-nios-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> National Institute of Open Schooling Converted Mentor List
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
                        <i class="ti ti-user-star"></i> Grameen Mukt Vidhyalayi Shiksha Sansthan Mentor List
                    </a>
                    <a href="{{ route('admin.digital-marketing-mentor-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> AI Integrated Digital Marketing Mentor List
                    </a>
                    <a href="{{ route('admin.data-science-mentor-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Data Science Course Mentor List
                    </a>
                    <a href="{{ route('admin.graphic-designing-mentor-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Diploma in Graphic Designing Mentor List
                    </a>
                    <a href="{{ route('admin.machine-learning-mentor-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Machine Learning Mentor List
                    </a>
                    <a href="{{ route('admin.medical-coding-mentor-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Certificate Course in Medical Coding Mentor List
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
                        <i class="ti ti-user-star"></i> CreateX AI Converted Mentor List
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
                        <i class="ti ti-headphones"></i> Board of Open Schooling and Skill Education Converted Support List
                    </a>
                    <a href="{{ route('admin.support-nios-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> National Institute of Open Schooling Converted Support List
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
                        <i class="ti ti-headphones"></i> Grameen Mukt Vidhyalayi Shiksha Sansthan Converted Support List
                    </a>
                    <a href="{{ route('admin.support-digital-marketing-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> AI Integrated Digital Marketing Converted Support List
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
                        <i class="ti ti-headphones"></i> Diploma in Graphic Designing Converted Support List
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
                        <i class="ti ti-headphones"></i> CreateX AI - Course Support List
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
                <form method="GET" action="{{ route('admin.edumaster-converted-leads.index') }}" id="filterForm">
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
                                <option value="UG" {{ request('course_type') === 'UG' ? 'selected' : '' }}>UG</option>
                                <option value="PG" {{ request('course_type') === 'PG' ? 'selected' : '' }}>PG</option>
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
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-search"></i> Filter
                                </button>
                                <a href="{{ route('admin.edumaster-converted-leads.index') }}" class="btn btn-secondary">
                                    <i class="ti ti-x"></i> Clear
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
                <h5 class="mb-0">EduMaster Converted Leads List</h5>
            </div>
            <div class="card-body">
                <!-- Desktop Table View -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover" id="edumasterTable">
                            <thead>
                                <tr>
                                    <th>SL No</th>
                                    <th>Academic</th>
                                    <th>Support</th>
                                    <th>Converted Date</th>
                                    <th>Register Number</th>
                                    <th>Course Flag</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>DOB</th>
                                    <th>Phone</th>
                                    <th>WhatsApp Number</th>
                                    @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                    <th>Parent Phone</th>
                                    @endif
                                    <th>Email</th>
                                    <th>Batch</th>
                                    <th>Admission Batch</th>
                                    <th>Selected Courses</th>
                                    <th>Board/University</th>
                                    <th>Course Type</th>
                                    <th>Course Name</th>
                                    <th>SSLC Back Year</th>
                                    <th>Plus Two Back Year</th>
                                    <th>Degree Back Year</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($convertedLeads as $index => $convertedLead)
                                <tr class="{{ $convertedLead->is_cancelled ? 'cancelled-row' : '' }}">
                                    @php
                                    $canToggleAcademic = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor();
                                    $canToggleSupport = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_support_team();
                                    $selectedCourses = $convertedLead->leadDetail?->selected_courses ? json_decode($convertedLead->leadDetail->selected_courses, true) : [];
                                    $hasUG = in_array('UG', $selectedCourses);
                                    $hasPG = in_array('PG', $selectedCourses);
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
                                        <div class="inline-edit" data-field="dob" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->leadDetail?->date_of_birth ? $convertedLead->leadDetail->date_of_birth->format('Y-m-d') : ($convertedLead->dob ?: '') }}">
                                            @php
                                            $dobDisplay = '-';
                                            if ($convertedLead->leadDetail && $convertedLead->leadDetail->date_of_birth) {
                                            $dobDisplay = $convertedLead->leadDetail->date_of_birth->format('d-m-Y');
                                            } elseif ($convertedLead->dob) {
                                            $dobDisplay = strtotime($convertedLead->dob) ? date('d-m-Y', strtotime($convertedLead->dob)) : $convertedLead->dob;
                                            }
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
                                        <div class="d-none inline-code-value" data-field="code" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->code }}"></div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="whatsapp_number" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->leadDetail?->whatsapp_number }}">
                                            <span class="display-value">{{ $convertedLead->leadDetail ? \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->whatsapp_code, $convertedLead->leadDetail->whatsapp_number) : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                        <div class="d-none inline-code-value" data-field="whatsapp_code" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->leadDetail?->whatsapp_code }}"></div>
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
                                        <div class="inline-edit" data-field="selected_courses" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->leadDetail?->selected_courses }}">
                                            <span class="display-value">
                                                @if(!empty($selectedCourses))
                                                    {{ implode(', ', $selectedCourses) }}
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
                                    <td>
                                        <div class="inline-edit" data-field="university_id" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->leadDetail?->university_id }}">
                                            <span class="display-value">{{ $convertedLead->leadDetail?->university?->title ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="course_type" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->leadDetail?->course_type }}">
                                            <span class="display-value">{{ $convertedLead->leadDetail?->course_type ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($hasUG || $hasPG)
                                        <div class="inline-edit" data-field="edumaster_course_name" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->leadDetail?->edumaster_course_name }}">
                                            <span class="display-value">{{ $convertedLead->leadDetail?->edumaster_course_name ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="sslc_back_year" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->leadDetail?->sslc_back_year }}">
                                            <span class="display-value">{{ $convertedLead->leadDetail?->sslc_back_year ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="plustwo_back_year" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->leadDetail?->plustwo_back_year }}">
                                            <span class="display-value">{{ $convertedLead->leadDetail?->plustwo_back_year ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($hasUG || $hasPG)
                                        <div class="inline-edit" data-field="degree_back_year" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->leadDetail?->degree_back_year }}">
                                            <span class="display-value">{{ $convertedLead->leadDetail?->degree_back_year ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
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
                                    <td colspan="20" class="text-center">No EduMaster converted leads found</td>
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
@include('admin.converted-leads.partials.course-flag-inline-scripts')
@endsection

<script id="country-codes-json" type="application/json">
    {
        {!! json_encode($country_codes ?? [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
    }
</script>

@php
    $showEdumasterParentPhone = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor();

    $edumasterConvertedLeadsColumns = [
        ['data' => 'index', 'name' => 'index', 'orderable' => false, 'searchable' => false],
        ['data' => 'academic', 'name' => 'academic', 'orderable' => false, 'searchable' => false],
        ['data' => 'support', 'name' => 'support', 'orderable' => false, 'searchable' => false],
        ['data' => 'converted_date', 'name' => 'converted_date', 'orderable' => false, 'searchable' => false],
        ['data' => 'register_number', 'name' => 'register_number', 'orderable' => false, 'searchable' => false],
    ['data' => 'course_flag', 'name' => 'course_flag', 'orderable' => false, 'searchable' => false],
        ['data' => 'name_col', 'name' => 'name_col', 'orderable' => false, 'searchable' => false],
        ['data' => 'type', 'name' => 'type', 'orderable' => false, 'searchable' => false],
        ['data' => 'dob', 'name' => 'dob', 'orderable' => false, 'searchable' => false],
        ['data' => 'phone', 'name' => 'phone', 'orderable' => false, 'searchable' => false],
        ['data' => 'whatsapp', 'name' => 'whatsapp', 'orderable' => false, 'searchable' => false],
    ];

    if ($showEdumasterParentPhone) {
        $edumasterConvertedLeadsColumns[] = ['data' => 'parent_phone', 'name' => 'parent_phone', 'orderable' => false, 'searchable' => false];
    }

    $edumasterConvertedLeadsColumns = array_merge($edumasterConvertedLeadsColumns, [
        ['data' => 'email', 'name' => 'email', 'orderable' => false, 'searchable' => false],
        ['data' => 'batch', 'name' => 'batch', 'orderable' => false, 'searchable' => false],
        ['data' => 'admission_batch', 'name' => 'admission_batch', 'orderable' => false, 'searchable' => false],
        ['data' => 'selected_courses', 'name' => 'selected_courses', 'orderable' => false, 'searchable' => false],
        ['data' => 'board_university', 'name' => 'board_university', 'orderable' => false, 'searchable' => false],
        ['data' => 'course_type', 'name' => 'course_type', 'orderable' => false, 'searchable' => false],
        ['data' => 'course_name', 'name' => 'course_name', 'orderable' => false, 'searchable' => false],
        ['data' => 'sslc_back_year', 'name' => 'sslc_back_year', 'orderable' => false, 'searchable' => false],
        ['data' => 'plustwo_back_year', 'name' => 'plustwo_back_year', 'orderable' => false, 'searchable' => false],
        ['data' => 'degree_back_year', 'name' => 'degree_back_year', 'orderable' => false, 'searchable' => false],
        ['data' => 'actions', 'name' => 'actions', 'orderable' => false, 'searchable' => false],
    ]);
@endphp

<div id="edumasterConvertedLeadsConfig" data-data-url="{{ route('admin.edumaster-converted-leads.data') }}" style="display:none"></div>
<script type="application/json" id="edumasterConvertedLeadsColumnsData">{!! json_encode($edumasterConvertedLeadsColumns) !!}</script>

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

    #edumasterTable thead th,
    #edumasterTable tbody td {
        white-space: nowrap;
    }

    #edumasterTable thead th {
        position: sticky;
        top: 0;
        z-index: 5;
        background: #fff;
        box-shadow: inset 0 -1px 0 #e9ecef;
    }

    #edumasterTable tbody tr:hover {
        background: #fafbff;
    }

    #edumasterTable td .display-value {
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
<script>
    $(document).ready(function() {
        const configEl = document.getElementById('edumasterConvertedLeadsConfig');
        const edumasterDataUrl = configEl ? configEl.dataset.dataUrl : '';
        const columnsEl = document.getElementById('edumasterConvertedLeadsColumnsData');
        const edumasterConvertedLeadsColumns = columnsEl ? JSON.parse(columnsEl.textContent || '[]') : [];

        function getEdumasterFilterParams() {
            return {
                filter_search: ($('#search').val() || '').trim(),
                university_id: $('#university_id').val() || '',
                course_type: $('#course_type').val() || '',
                course_flag_id: $('#course_flag_id').val() || '',

                date_from: $('#date_from').val() || '',
                date_to: $('#date_to').val() || ''
            };
        }

        window.reloadEdumasterTable = function() {
            if ($.fn.DataTable.isDataTable('#edumasterTable')) {
                $('#edumasterTable').DataTable().ajax.reload(null, false);
            }
        };

        if ($.fn.DataTable.isDataTable('#edumasterTable')) {
            $('#edumasterTable').DataTable().destroy();
        }

        $('#edumasterTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: edumasterDataUrl,
                type: 'GET',
                data: function(d) {
                    $.extend(d, getEdumasterFilterParams());
                },
                dataSrc: function(json) {
                    return (json && json.data) ? json.data : [];
                },
                error: function() {
                    if (typeof showToast === 'function') {
                        showToast('Error loading EduMaster converted leads.', 'error');
                    }
                }
            },
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [],
            ordering: false,
            dom: 'Bfrtip',
            scrollX: true,
            autoWidth: false,
            columns: edumasterConvertedLeadsColumns
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
            const universityId = container.data('university-id');

            if (container.hasClass('editing')) {
                return;
            }

            $('.inline-edit.editing').not(container).each(function() {
                $(this).removeClass('editing');
                $(this).find('.edit-form').remove();
            });

            let editForm = '';

            if (field === 'phone' || field === 'whatsapp_number') {
                const currentCode = container.siblings('.inline-code-value').data('current') || '';
                const codeField = field === 'phone' ? 'code' : 'whatsapp_code';
                editForm = createPhoneField(currentCode, currentValue, codeField);
            } else if (field === 'batch_id') {
                editForm = createBatchSelect(currentValue);
            } else if (field === 'admission_batch_id') {
                const batchId = container.data('batch-id') || '';
                editForm = createAdmissionBatchSelect(batchId, currentValue);
            } else if (field === 'course_type') {
                editForm = createCourseTypeSelect(currentValue);
            } else if (field === 'university_id') {
                editForm = createUniversitySelect(currentValue);
            } else if (field === 'selected_courses') {
                editForm = createSelectedCoursesField(currentValue);
            } else if (field === 'sslc_back_year' || field === 'plustwo_back_year' || field === 'degree_back_year') {
                editForm = createYearField(field, currentValue);
            } else if (field === 'dob') {
                editForm = createDateField(field, currentValue);
            } else {
                editForm = createInputField(field, currentValue);
            }

            container.addClass('editing');
            container.append(editForm);

            // Handle dependent dropdowns
            if (field === 'admission_batch_id') {
                const batchId = container.data('batch-id') || '';
                const currentId = container.data('current-id') || '';
                const $select = container.find('.admission-batch-select');
                loadAdmissionBatchesForEdit($select, batchId, currentId);
            } else if (field === 'batch_id') {
                // When batch changes, update admission batch dropdown if it exists in the same row
                const $batchSelect = container.find('.batch-select');
                $batchSelect.on('change', function() {
                    const newBatchId = $(this).val();
                    const $row = container.closest('tr');
                    const $admissionContainer = $row.find('[data-field="admission_batch_id"]');
                    if ($admissionContainer.length && $admissionContainer.hasClass('editing')) {
                        const $admissionSelect = $admissionContainer.find('.admission-batch-select');
                        loadAdmissionBatchesForEdit($admissionSelect, newBatchId, '');
                    }
                });
            }

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
            let extra = {};

            if (field === 'phone' || field === 'whatsapp_number') {
                value = container.find('input[type="text"]').val();
                const codeField = field === 'phone' ? 'code' : 'whatsapp_code';
                const codeVal = container.find('select[name="' + codeField + '"]').val();
                extra[codeField] = codeVal;
            } else if (field === 'selected_courses') {
                const selected = [];
                container.find('input[type="checkbox"]:checked').each(function() {
                    selected.push($(this).val());
                });
                value = JSON.stringify(selected);
            } else {
                value = container.find('input, select').val();
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
                        let displayValue = response.value || value;

                        // Special handling for DOB field
                        if (field === 'dob' && displayValue) {
                            try {
                                const date = new Date(displayValue);
                                if (!isNaN(date.getTime())) {
                                    displayValue = date.toLocaleDateString('en-GB'); // d/m/Y format
                                }
                            } catch (e) {
                                // Keep original value if conversion fails
                            }
                        }

                        // Special handling for selected_courses
                        if (field === 'selected_courses') {
                            try {
                                const courses = JSON.parse(displayValue);
                                displayValue = courses.join(', ');
                            } catch (e) {
                                // Keep original value if conversion fails
                            }
                        }

                        container.find('.display-value').text(displayValue);
                        container.data('current', response.value || value);

                        if (field === 'phone') {
                            const codeVal = extra.code || '';
                            container.siblings('.inline-code-value').data('current', codeVal);
                        } else if (field === 'whatsapp_number') {
                            const codeVal = extra.whatsapp_code || '';
                            container.siblings('.inline-code-value[data-field="whatsapp_code"]').data('current', codeVal);
                        } else if (field === 'batch_id') {
                            // Update batch_id in data attribute and refresh admission batch if needed
                            container.data('batch-id', value);
                            container.data('current-id', value);
                            const $row = container.closest('tr');
                            const $admissionContainer = $row.find('[data-field="admission_batch_id"]');
                            if ($admissionContainer.length) {
                                $admissionContainer.data('batch-id', value);
                                // Clear admission batch if batch changed
                                if ($admissionContainer.data('current-id')) {
                                    $admissionContainer.find('.display-value').text('N/A');
                                    $admissionContainer.data('current-id', '');
                                }
                                // If admission batch is currently being edited, reload its options
                                if ($admissionContainer.hasClass('editing')) {
                                    const $admissionSelect = $admissionContainer.find('.admission-batch-select');
                                    loadAdmissionBatchesForEdit($admissionSelect, value, '');
                                }
                            }
                        } else if (field === 'admission_batch_id') {
                            container.data('current-id', value);
                        } else if (field === 'university_id') {
                            // Reload university course options when university changes
                            const $universityCourseContainer = container.closest('tr').find('[data-field="university_course_id"]');
                            if ($universityCourseContainer.length) {
                                $universityCourseContainer.data('university-id', value);
                                $universityCourseContainer.data('current', ''); // Reset course selection
                                $universityCourseContainer.find('.display-value').text('-');
                            }
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
        });

        // Helper functions for creating form elements
        function createInputField(field, currentValue) {
            const displayValue = currentValue === '-' ? '' : currentValue;
            return `
                <div class="edit-form">
                    <input type="text" value="${displayValue}" class="form-control form-control-sm" autocomplete="off">
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createDateField(field, currentValue) {
            let value = '';
            if (currentValue && currentValue !== '-') {
                if (currentValue.match(/^\d{4}-\d{2}-\d{2}$/)) {
                    value = currentValue;
                } else if (currentValue.includes('-')) {
                    const parts = currentValue.split('-');
                    if (parts.length === 3) {
                        if (parts[0].length <= 2 && parts[1].length <= 2 && parts[2].length === 4) {
                            const day = parts[0].padStart(2, '0');
                            const month = parts[1].padStart(2, '0');
                            const year = parts[2];
                            value = `${year}-${month}-${day}`;
                        }
                    }
                }
            }
            const today = new Date().toISOString().split('T')[0];
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

        function createYearField(field, currentValue) {
            const displayValue = currentValue === '-' ? '' : currentValue;
            const currentYear = new Date().getFullYear();
            return `
                <div class="edit-form">
                    <input type="number" min="2018" max="${currentYear}" value="${displayValue}" class="form-control form-control-sm" placeholder="Year">
                    <div class="btn-group mt-1">
                        <button class="btn btn-success btn-sm save-edit">Save</button>
                        <button class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createCourseTypeSelect(currentValue) {
            return `
                <div class="edit-form">
                    <select class="form-select form-select-sm">
                        <option value="">Select</option>
                        <option value="UG" ${currentValue === 'UG' ? 'selected' : ''}>UG</option>
                        <option value="PG" ${currentValue === 'PG' ? 'selected' : ''}>PG</option>
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createUniversitySelect(currentValue) {
            let options = '<option value="">Select University</option>';
            @foreach($universities as $university)
            const selected{{ $university->id }} = String(currentValue) === '{{ $university->id }}' ? 'selected' : '';
            options += `<option value="{{ $university->id }}" ${selected{{ $university->id }}}>{{ $university->title }}</option>`;
            @endforeach
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

        function createBatchSelect(currentValue) {
            let options = '<option value="">Select Batch</option>';
            @foreach($batches as $batch)
            const batchSelected{{ $batch->id }} = String(currentValue) === '{{ $batch->id }}' ? 'selected' : '';
            options += `<option value="{{ $batch->id }}" ${batchSelected{{ $batch->id }}}>{{ $batch->title }}</option>`;
            @endforeach
            return `
                <div class="edit-form">
                    <select class="form-select form-select-sm batch-select">
                        ${options}
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createAdmissionBatchSelect(batchId, currentValue) {
            let options = '<option value="">Select Admission Batch</option>';
            if (batchId) {
                // Load admission batches dynamically
                return `
                    <div class="edit-form">
                        <select class="form-select form-select-sm admission-batch-select">
                            <option value="">Loading...</option>
                        </select>
                        <div class="btn-group mt-1">
                            <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                            <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                        </div>
                    </div>
                `;
            } else {
                return `
                    <div class="edit-form">
                        <select class="form-select form-select-sm admission-batch-select">
                            <option value="">Please select a batch first</option>
                        </select>
                        <div class="btn-group mt-1">
                            <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                            <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                        </div>
                    </div>
                `;
            }
        }

        function loadAdmissionBatchesForEdit($select, batchId, selectedId) {
            if (!batchId) {
                $select.html('<option value="">Please select a batch first</option>');
                return;
            }
            $select.html('<option value="">Loading...</option>');
            $.get(`/api/admission-batches/by-batch/${batchId}`).done(function(list) {
                let opts = '<option value="">Select Admission Batch</option>';
                list.forEach(function(i) {
                    const sel = String(selectedId) === String(i.id) ? 'selected' : '';
                    opts += `<option value="${i.id}" ${sel}>${i.title}</option>`;
                });
                $select.html(opts);
            }).fail(function() {
                $select.html('<option value="">Error loading admission batches</option>');
            });
        }

        function createSelectedCoursesField(currentValue) {
            let courses = [];
            try {
                if (currentValue && currentValue !== '-') {
                    courses = JSON.parse(currentValue);
                }
            } catch (e) {
                // If not JSON, try to parse as comma-separated
                if (currentValue && currentValue !== '-') {
                    courses = currentValue.split(',').map(c => c.trim());
                }
            }
            
            return `
                <div class="edit-form">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="SSLC" id="course_sslc" ${courses.includes('SSLC') ? 'checked' : ''}>
                        <label class="form-check-label" for="course_sslc">SSLC</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Plus two" id="course_plustwo" ${courses.includes('Plus two') ? 'checked' : ''}>
                        <label class="form-check-label" for="course_plustwo">Plus two</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="UG" id="course_ug" ${courses.includes('UG') ? 'checked' : ''}>
                        <label class="form-check-label" for="course_ug">UG</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="PG" id="course_pg" ${courses.includes('PG') ? 'checked' : ''}>
                        <label class="form-check-label" for="course_pg">PG</label>
                    </div>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createPhoneField(currentCode, currentPhone, codeFieldName) {
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
            const safePhone = (currentPhone && currentPhone !== 'N/A' && currentPhone !== '-') ? currentPhone : '';
            return `
                <div class="edit-form">
                    <div class="row g-1">
                        <div class="col-5">
                            <select name="${codeFieldName}" class="form-select form-select-sm">${buildOptions(currentCode)}</select>
                        </div>
                        <div class="col-7">
                            <input type="text" value="${safePhone}" class="form-control form-control-sm" placeholder="Phone">
                        </div>
                    </div>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
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
                        window.reloadEdumasterTable();
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
                        window.reloadEdumasterTable();
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
                if ($.fn.DataTable.isDataTable('#edumasterTable')) {
                    const dt = $('#edumasterTable').DataTable();
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

