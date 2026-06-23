@extends('layouts.mantis')

@section('title', 'EduMaster Converted Support List')

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
                    <h5 class="m-b-10">EduMaster Converted Support List</h5>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end align-items-center gap-3">
                    <ul class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.converted-leads.index') }}">Converted Leads</a></li>
                        <li class="breadcrumb-item">EduMaster Converted Support List</li>
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
                        <i class="ti ti-school"></i> National Institute of Open Schooling Converted Leads
                    </a>
                    <a href="{{ route('admin.bosse-converted-leads.index') }}" class="btn btn-outline-warning">
                        <i class="ti ti-school-2"></i> Board of Open Schooling and Skill Education Converted Leads
                    </a>
                    <a href="{{ route('admin.ugpg-converted-leads.index') }}" class="btn btn-outline-warning">
                        <i class="ti ti-graduation"></i> UG/PG Converted Leads
                    </a>
                    <a href="{{ route('admin.edumaster-converted-leads.index') }}" class="btn btn-warning">
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
                    <a href="{{ route('admin.junior-vlogger-mentor-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> CreateX AI Converted Mentor List
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
                    <a href="{{ route('admin.mentor-edumaster-converted-leads.index') }}" class="btn btn-outline-primary active">
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
                    @if(\App\Helpers\RoleHelper::is_support_team())
                    <a href="{{ route('admin.converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-list"></i> All Converted Leads
                    </a>
                    @endif
                    <a href="{{ route('admin.support-bosse-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> Board of Open Schooling and Skill Education Converted Support List
                    </a>
                    <a href="{{ route('admin.support-nios-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> National Institute of Open Schooling Converted Support List
                    </a>
                    <a href="{{ route('admin.support-ugpg-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> UG/PG Converted Support List
                    </a>
                    <a href="{{ route('admin.support-edumaster-converted-leads.index') }}" class="btn btn-outline-primary active">
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
                <form method="GET" action="{{ route('admin.support-edumaster-converted-leads.index') }}" id="filterForm">
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
                        @include('admin.converted-leads.partials.support-flag-filter-field')
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
                            <a href="{{ route('admin.support-edumaster-converted-leads.index') }}" class="btn btn-secondary">
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
                <h5>EduMaster Converted Support List</h5>
            </div>
            <div class="card-body">
                <!-- Desktop Table View -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover data_table_basic" id="supportEdumasterTable">
                            <thead>
                                <tr>
                                    <th>SL No</th>
                                    <th>Support</th>
                                    <th>Academic Verified At</th>
                                    <th>Converted Date</th>
                                    <th>Registration Number</th>
                                    <th>Support Flag</th>
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
                                    <th>CALL - 1</th>
                                    <th>APP</th>
                                    <th>WhatsApp Group</th>
                                    <th>Telegram Group</th>
                                    <th>Problems</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($convertedLeads as $index => $convertedLead)
                                <tr class="{{ $convertedLead->is_cancelled ? 'cancelled-row' : '' }}">
                                    <td>{{ ($convertedLeads->currentPage() - 1) * $convertedLeads->perPage() + $index + 1 }}</td>
                                    <td>
                                        @php
                                        $canToggleSupport = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_support_team();
                                        @endphp
                                        @include('admin.converted-leads.partials.status-badge', [
                                        'convertedLead' => $convertedLead,
                                        'type' => 'support',
                                        'showToggle' => $canToggleSupport,
                                        'toggleUrl' => $canToggleSupport ? route('admin.support-converted-leads.toggle-support-verify', $convertedLead->id) : null,
                                        'title' => 'support',
                                        'useModal' => true
                                        ])
                                    </td>
                                    @php
                                    $academicVerifiedAt = $convertedLead->academic_verified_at
                                    ? $convertedLead->academic_verified_at->copy()->timezone($appTimezone)->format('d-m-Y h:i A')
                                    : null;
                                    $selectedCourses = $convertedLead->leadDetail?->selected_courses ? json_decode($convertedLead->leadDetail->selected_courses, true) : [];
                                    @endphp
                                    <td>
                                        @if($academicVerifiedAt)
                                        {{ $academicVerifiedAt }}
                                        @else
                                        <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>{{ $convertedLead->created_at->format('d-m-Y') }}</td>
                                    <td>{{ $convertedLead->register_number ?? '-' }}</td>
                                    @include('admin.converted-leads.partials.inline-support-flag-cell', ['convertedLead' => $convertedLead])
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
                                        @php
                                        $dobDisplay = '-';
                                        if ($convertedLead->leadDetail && $convertedLead->leadDetail->date_of_birth) {
                                            $dobDisplay = $convertedLead->leadDetail->date_of_birth->format('d-m-Y');
                                        } elseif ($convertedLead->dob) {
                                            $dobDisplay = strtotime($convertedLead->dob) ? date('d-m-Y', strtotime($convertedLead->dob)) : $convertedLead->dob;
                                        }
                                        @endphp
                                        {{ $dobDisplay }}
                                    </td>
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
                                    <td>{{ $convertedLead->batch ? $convertedLead->batch->title : 'N/A' }}</td>
                                    <td>{{ $convertedLead->admissionBatch ? $convertedLead->admissionBatch->title : 'N/A' }}</td>
                                    <td>
                                        @if(!empty($selectedCourses))
                                            {{ implode(', ', $selectedCourses) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $convertedLead->leadDetail?->university?->title ?? '-' }}</td>
                                    <td>{{ $convertedLead->leadDetail?->course_type ?? '-' }}</td>
                                    <td>{{ $convertedLead->leadDetail?->edumaster_course_name ?? '-' }}</td>
                                    <td>
                                        <div class="inline-edit" data-field="call_1" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->supportDetails?->call_1 }}">
                                            <span class="display-value">{{ $convertedLead->supportDetails?->call_1 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_support_team())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="app" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->supportDetails?->app }}">
                                            <span class="display-value">{{ $convertedLead->supportDetails?->app ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_support_team())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="whatsapp_group" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->supportDetails?->whatsapp_group }}">
                                            <span class="display-value">{{ $convertedLead->supportDetails?->whatsapp_group ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_support_team())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="telegram_group" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->supportDetails?->telegram_group }}">
                                            <span class="display-value">{{ $convertedLead->supportDetails?->telegram_group ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_support_team())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="problems" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->supportDetails?->problems }}">
                                            <span class="display-value">{{ $convertedLead->supportDetails?->problems ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_support_team())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.support-converted-leads.details', $convertedLead->id) }}" class="btn btn-sm btn-primary" title="View Details">
                                                <i class="ti ti-eye"></i> Details
                                            </a>
                                            <a href="{{ route('admin.invoices.index', $convertedLead->id) }}" class="btn btn-sm btn-success" title="View Invoice">
                                                <i class="ti ti-receipt"></i>
                                            </a>
                                            @include('admin.converted-leads.partials.support-whatsapp-mail-buttons', ['convertedLead' => $convertedLead])
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
                                    <td colspan="22" class="text-center">No converted leads found for support</td>
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
                            @php
                            $academicVerifiedAt = $convertedLead->academic_verified_at
                            ? $convertedLead->academic_verified_at->copy()->timezone($appTimezone)->format('d-m-Y h:i A')
                            : null;
                            @endphp
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
                                            <a class="dropdown-item" href="{{ route('admin.support-converted-leads.details', $convertedLead->id) }}">
                                                <i class="ti ti-eye me-2"></i>View Details
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.invoices.index', $convertedLead->id) }}">
                                                <i class="ti ti-receipt me-2"></i>View Invoice
                                            </a>
                                        </li>
                                        @include('admin.converted-leads.partials.support-whatsapp-mail-dropdown-items', ['convertedLead' => $convertedLead])
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
                                    <small class="text-muted d-block">Converted Date</small>
                                    <span class="fw-medium">{{ $convertedLead->created_at->format('d-m-Y') }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Batch</small>
                                    <span class="fw-medium">{{ $convertedLead->batch?->title ?? 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">University</small>
                                    <span class="fw-medium">{{ $convertedLead->leadDetail?->university?->title ?? 'N/A' }}</span>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block">Academic Verified At</small>
                                    @if($academicVerifiedAt)
                                    <span class="fw-medium">{{ $academicVerifiedAt }}</span>
                                    @else
                                    <span class="fw-medium text-muted">N/A</span>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <div class="text-muted">
                            <i class="ti ti-headphones f-48 mb-3 d-block"></i>
                            <h5>No converted leads found for support</h5>
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

@include('admin.converted-leads.partials.support-whatsapp-mail-layout-includes')

@include('admin.converted-leads.partials.support-flag-inline-scripts', ['supportUpdateUrl' => route('admin.support-edumaster-converted-leads.update-support-details', ['id' => '__ID__'])])
@endsection

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

    #supportEdumasterTable thead th,
    #supportEdumasterTable tbody td {
        white-space: nowrap;
    }

    #supportEdumasterTable thead th {
        position: sticky;
        top: 0;
        z-index: 5;
        background: #fff;
        box-shadow: inset 0 -1px 0 #e9ecef;
    }

    #supportEdumasterTable tbody tr:hover {
        background: #fafbff;
    }

    #supportEdumasterTable td .display-value {
        display: inline-block;
        max-width: 220px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        vertical-align: middle;
    }

    #supportEdumasterTable .btn-group .btn {
        margin-right: 4px;
    }

    #supportEdumasterTable .btn-group .btn:last-child {
        margin-right: 0;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // DataTable is automatically initialized by layout for tables with 'data_table_basic' class

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

        // On batch change → reload admission batches
        $('#batch_id').on('change', function() {
            const bid = $(this).val();
            loadAdmissionBatchesByBatch(bid, '');
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

            if (['call_1', 'app', 'whatsapp_group', 'telegram_group'].includes(field)) {
                editForm = createSelectField(field, currentValue);
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
            const value = container.find('input, select').val();

            const btn = $(this);
            if (btn.data('busy')) return;
            btn.data('busy', true);
            btn.prop('disabled', true).html('<i class="ti ti-loader-2 spin"></i>');

            $.ajax({
                url: `/admin/support-edumaster-converted-leads/${id}/update-support-details`,
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
            return `
                <div class="edit-form">
                    <input type="text" value="${displayValue}" class="form-control form-control-sm" autocomplete="off" autocapitalize="off" spellcheck="false">
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createSelectField(field, currentValue) {
            let options = '';

            if (field === 'call_1') {
                options = `
                    <option value="">Select Call Status</option>
                    <option value="Call Not Answered" ${currentValue === 'Call Not Answered' ? 'selected' : ''}>Call Not Answered</option>
                    <option value="Switched Off" ${currentValue === 'Switched Off' ? 'selected' : ''}>Switched Off</option>
                    <option value="Line Busy" ${currentValue === 'Line Busy' ? 'selected' : ''}>Line Busy</option>
                    <option value="Student Asks to Call Later" ${currentValue === 'Student Asks to Call Later' ? 'selected' : ''}>Student Asks to Call Later</option>
                    <option value="Lack of Interest in Conversation" ${currentValue === 'Lack of Interest in Conversation' ? 'selected' : ''}>Lack of Interest in Conversation</option>
                    <option value="Wrong Contact" ${currentValue === 'Wrong Contact' ? 'selected' : ''}>Wrong Contact</option>
                    <option value="Inconsistent Responses" ${currentValue === 'Inconsistent Responses' ? 'selected' : ''}>Inconsistent Responses</option>
                    <option value="Task Complete" ${currentValue === 'Task Complete' ? 'selected' : ''}>Task Complete</option>
                `;
            } else if (field === 'app') {
                options = `
                    <option value="">Select APP Status</option>
                    <option value="Provided app" ${currentValue === 'Provided app' ? 'selected' : ''}>Provided app</option>
                    <option value="OTP Problem" ${currentValue === 'OTP Problem' ? 'selected' : ''}>OTP Problem</option>
                    <option value="Task Completed" ${currentValue === 'Task Completed' ? 'selected' : ''}>Task Completed</option>
                `;
            } else if (field === 'whatsapp_group') {
                options = `
                    <option value="">Select WhatsApp Group Status</option>
                    <option value="Sent link" ${currentValue === 'Sent link' ? 'selected' : ''}>Sent link</option>
                    <option value="Task Completed" ${currentValue === 'Task Completed' ? 'selected' : ''}>Task Completed</option>
                `;
            } else if (field === 'telegram_group') {
                options = `
                    <option value="">Select Telegram Group Status</option>
                    <option value="Call not answered" ${currentValue === 'Call not answered' ? 'selected' : ''}>Call not answered</option>
                    <option value="switched off" ${currentValue === 'switched off' ? 'selected' : ''}>switched off</option>
                    <option value="line busy" ${currentValue === 'line busy' ? 'selected' : ''}>line busy</option>
                    <option value="student asks to call later" ${currentValue === 'student asks to call later' ? 'selected' : ''}>student asks to call later</option>
                    <option value="lack of interest in conversation" ${currentValue === 'lack of interest in conversation' ? 'selected' : ''}>lack of interest in conversation</option>
                    <option value="wrong contact" ${currentValue === 'wrong contact' ? 'selected' : ''}>wrong contact</option>
                    <option value="inconsistent responses" ${currentValue === 'inconsistent responses' ? 'selected' : ''}>inconsistent responses</option>
                    <option value="task complete" ${currentValue === 'task complete' ? 'selected' : ''}>task complete</option>
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

        // Support verify with confirmation modal
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
                    _token: $('meta[name="csrf-token"]').attr('content')
                })
                .done(function(res) {
                    if (res && res.success) {
                        toast_success(res.message || 'Updated');
                        $('#supportVerifyModal').modal('hide');
                        setTimeout(() => {
                            location.reload();
                        }, 600);
                    } else {
                        toast_error((res && res.message) ? res.message : 'Failed to update');
                    }
                })
                .fail(function(xhr) {
                    let msg = 'Failed to update';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    toast_error(msg);
                })
                .always(function() {
                    $confirmBtn.prop('disabled', false).removeClass('disabled').html(originalHtml);
                    supportVerifyUrl = null;
                });
        });
    });
</script>
@endpush

