@extends('layouts.mantis')

@section('title', 'EduMaster Faculty Converted List')

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
                    <h5 class="m-b-10">EduMaster Faculty Converted List</h5>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end align-items-center gap-3">
                    <ul class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.converted-leads.index') }}">Converted Leads</a></li>
                        <li class="breadcrumb-item">EduMaster Faculty Converted List</li>
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
                        <i class="ti ti-school"></i> National Institute of Open Schooling Converted Leads
                    </a>
                    <a href="{{ route('admin.bosse-converted-leads.index') }}" class="btn btn-outline-warning">
                        <i class="ti ti-school-2"></i> Board of Open Schooling and Skill Education Converted Leads
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
                    <a href="{{ route('admin.junior-vlogger-faculty-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> CreateX AI Converted Faculty List
                    </a>
                    <a href="{{ route('admin.support-junior-vlogger-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> CreateX AI â€“ Course Support List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<!-- [ Course Filter Buttons ] end -->

@include('admin.converted-leads.partials.mentor-list-nav', ['activeMentorRoute' => $activeMentorRoute ?? null])

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
                        <i class="ti ti-headphones"></i> CreateX AI â€“ Course Support List
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
                <form method="GET" action="{{ route('admin.faculty-edumaster-converted-leads.index') }}" id="filterForm">
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
                            <select class="form-select" id="admission_batch_id" name="admission_batch_id">
                                <option value="">All Admission Batches</option>
                                @foreach($admissionBatches as $admissionBatch)
                                <option value="{{ $admissionBatch->id }}" {{ request('admission_batch_id') == $admissionBatch->id ? 'selected' : '' }}>
                                    {{ $admissionBatch->title }}
                                </option>
                                @endforeach
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
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-search"></i> Filter
                            </button>
                            <a href="{{ route('admin.faculty-edumaster-converted-leads.index') }}" class="btn btn-secondary">
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
        <div class="card" data-mentor-update-url="{{ route('admin.faculty-edumaster-converted-leads.update-mentor-details', ['id' => '__ID__']) }}">
            <div class="card-header">
                <h5>EduMaster Faculty Converted List</h5>
            </div>
            <div class="card-body">
                <!-- Desktop Table View -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover data_table_basic" id="mentorEduMasterTable">
                            <thead>
                                <tr>
                                    <th>SL No</th>
                                    <th>Academic Support</th>
                                    <th>Conversion Date</th>
                                    <th>Registration Number</th>
                                    <th>Course Flag</th>
                                    <th>Call Time</th>
                                    <th>Student Name</th>
                                    <th>Type</th>
                                    <th>DOB</th>
                                    <th>Phone Number</th>
                                    <th>WhatsApp Number</th>
                                    @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                    <th>Parent Phone</th>
                                    @endif
                                    <th>Email ID</th>
                                    <th>Batch</th>
                                    <th>Admission Batch</th>
                                    <th>Selected Course(s)</th>
                                    <!-- SSLC Back Year -->
                                    <th>SSLC Back Year</th>
                                    <th>SSLC Enrollment Number</th>
                                    <th>SSLC Registration Link</th>
                                    <th>SSLC Online Result Publication Date</th>
                                    <th>SSLC Certificate Publication Date</th>
                                    <th>SSLC Certificate Issued Date</th>
                                    <th>SSLC Certificate Distribution Date</th>
                                    <th>SSLC Courier Tracking Number</th>
                                    <th>SSLC Remarks</th>
                                    <!-- Plus Two Back Year -->
                                    <th>Plus Two Back Year</th>
                                    <th>Plus Two Subject</th>
                                    <th>Plus Two Enrollment Number</th>
                                    <th>Plus Two Registration Link</th>
                                    <th>Plus Two Online Result Publication Date</th>
                                    <th>Plus Two Certificate Publication Date</th>
                                    <th>Plus Two Certificate Issued Date</th>
                                    <th>Plus Two Certificate Distribution Date</th>
                                    <th>Plus Two Courier Tracking Number</th>
                                    <th>Plus Two Remarks</th>
                                    <!-- Degree Back Year -->
                                    <th>Degree Board/University</th>
                                    <th>Degree Course Type</th>
                                    <th>Degree Course Name</th>
                                    <th>Degree Back Year</th>
                                    <th>Degree Registration Start Date</th>
                                    <th>Degree Registration Form Summary Distribution Date</th>
                                    <th>Degree Registration Form Summary Submission Date</th>
                                    <th>Degree ID Card Issued Date</th>
                                    <th>Degree First Year Result Date</th>
                                    <th>Degree Second Year Result Date</th>
                                    <th>Degree Third Year Result Date</th>
                                    <th>Degree Online Result Publication Date</th>
                                    <th>Degree Certificate Publication Date</th>
                                    <th>Degree Certificate Issued Date</th>
                                    <th>Degree Certificate Distribution Date</th>
                                    <th>Degree Courier Tracking Number</th>
                                    <th>Degree Remarks</th>
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
                                    $selectedCourses = [];
                                    if ($convertedLead->leadDetail && $convertedLead->leadDetail->selected_courses) {
                                        try {
                                            $selectedCourses = json_decode($convertedLead->leadDetail->selected_courses, true);
                                            if (!is_array($selectedCourses)) {
                                                $selectedCourses = [];
                                            }
                                        } catch (\Exception $e) {
                                            $selectedCourses = [];
                                        }
                                    }
                                    @endphp
                                    <td>{{ ($convertedLeads->currentPage() - 1) * $convertedLeads->perPage() + $index + 1 }}</td>
                                    <td>
                                        @if($academicVerifiedAt)
                                        <span class="badge bg-success">Verified</span><br>
                                        <small class="text-muted">{{ $academicVerifiedAt }}</small>
                                        @else
                                        <span class="badge bg-warning">Not Verified</span>
                                        @endif
                                    </td>
                                    <td>{{ $convertedLead->created_at->format('d-m-Y') }}</td>
                                    <td>{{ $convertedLead->register_number ?? '-' }}</td>
                                    @include('admin.converted-leads.partials.inline-course-flag-cell', ['convertedLead' => $convertedLead])
                                    @include('admin.converted-leads.partials.inline-call-time-cell', ['convertedLead' => $convertedLead])
                                    <td>
                                        {{ $convertedLead->name }}
                                        @if($convertedLead->is_cancelled)
                                        <div>
                                            <span class="badge bg-danger ms-2">Cancelled</span>
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
                                    <td>{{ $convertedLead->batch ? $convertedLead->batch->title : '-' }}</td>
                                    <td>{{ $convertedLead->admissionBatch ? $convertedLead->admissionBatch->title : '-' }}</td>
                                    <td>
                                        @if(!empty($selectedCourses))
                                            {{ implode(', ', $selectedCourses) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <!-- SSLC Back Year Fields -->
                                    <td>{{ $convertedLead->leadDetail?->sslc_back_year ?? '-' }}</td>
                                    <td>
                                        <div class="inline-edit" data-field="sslc_enrollment_number" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->sslc_enrollment_number }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->sslc_enrollment_number ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="sslc_registration_link_id" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->sslc_registration_link_id }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->sslcRegistrationLink?->title ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="sslc_online_result_publication_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->sslc_online_result_publication_date ? $convertedLead->mentorDetails->sslc_online_result_publication_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->sslc_online_result_publication_date ? $convertedLead->mentorDetails->sslc_online_result_publication_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="sslc_certificate_publication_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->sslc_certificate_publication_date ? $convertedLead->mentorDetails->sslc_certificate_publication_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->sslc_certificate_publication_date ? $convertedLead->mentorDetails->sslc_certificate_publication_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="sslc_certificate_issued_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->sslc_certificate_issued_date ? $convertedLead->mentorDetails->sslc_certificate_issued_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->sslc_certificate_issued_date ? $convertedLead->mentorDetails->sslc_certificate_issued_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="sslc_certificate_distribution_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->sslc_certificate_distribution_date ? $convertedLead->mentorDetails->sslc_certificate_distribution_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->sslc_certificate_distribution_date ? $convertedLead->mentorDetails->sslc_certificate_distribution_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="sslc_courier_tracking_number" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->sslc_courier_tracking_number }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->sslc_courier_tracking_number ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="sslc_remarks" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->sslc_remarks }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->sslc_remarks ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <!-- Plus Two Back Year Fields -->
                                    <td>{{ $convertedLead->leadDetail?->plustwo_back_year ?? '-' }}</td>
                                    <td>
                                        <div class="inline-edit" data-field="plustwo_subject_no" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->plustwo_subject_no }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->plustwo_subject_no ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="plustwo_enrollment_number" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->plustwo_enrollment_number }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->plustwo_enrollment_number ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="plustwo_registration_link_id" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->plustwo_registration_link_id }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->plustwoRegistrationLink?->title ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="plustwo_online_result_publication_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->plustwo_online_result_publication_date ? $convertedLead->mentorDetails->plustwo_online_result_publication_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->plustwo_online_result_publication_date ? $convertedLead->mentorDetails->plustwo_online_result_publication_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="plustwo_certificate_publication_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->plustwo_certificate_publication_date ? $convertedLead->mentorDetails->plustwo_certificate_publication_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->plustwo_certificate_publication_date ? $convertedLead->mentorDetails->plustwo_certificate_publication_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="plustwo_certificate_issued_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->plustwo_certificate_issued_date ? $convertedLead->mentorDetails->plustwo_certificate_issued_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->plustwo_certificate_issued_date ? $convertedLead->mentorDetails->plustwo_certificate_issued_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="plustwo_certificate_distribution_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->plustwo_certificate_distribution_date ? $convertedLead->mentorDetails->plustwo_certificate_distribution_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->plustwo_certificate_distribution_date ? $convertedLead->mentorDetails->plustwo_certificate_distribution_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="plustwo_courier_tracking_number" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->plustwo_courier_tracking_number }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->plustwo_courier_tracking_number ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="plustwo_remarks" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->plustwo_remarks }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->plustwo_remarks ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <!-- Degree Back Year Fields -->
                                    <td>{{ $convertedLead->leadDetail?->university?->title ?? '-' }}</td>
                                    <td>{{ $convertedLead->leadDetail?->course_type ?? '-' }}</td>
                                    <td>{{ $convertedLead->leadDetail?->edumaster_course_name ?? '-' }}</td>
                                    <td>{{ $convertedLead->leadDetail?->back_year ?? '-' }}</td>
                                    <td>
                                        <div class="inline-edit" data-field="degree_registration_start_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->degree_registration_start_date ? $convertedLead->mentorDetails->degree_registration_start_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->degree_registration_start_date ? $convertedLead->mentorDetails->degree_registration_start_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="degree_registration_form_summary_distribution_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->degree_registration_form_summary_distribution_date ? $convertedLead->mentorDetails->degree_registration_form_summary_distribution_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->degree_registration_form_summary_distribution_date ? $convertedLead->mentorDetails->degree_registration_form_summary_distribution_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="degree_registration_form_summary_submission_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->degree_registration_form_summary_submission_date ? $convertedLead->mentorDetails->degree_registration_form_summary_submission_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->degree_registration_form_summary_submission_date ? $convertedLead->mentorDetails->degree_registration_form_summary_submission_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="degree_id_card_issued_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->degree_id_card_issued_date ? $convertedLead->mentorDetails->degree_id_card_issued_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->degree_id_card_issued_date ? $convertedLead->mentorDetails->degree_id_card_issued_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="degree_first_year_result_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->degree_first_year_result_date ? $convertedLead->mentorDetails->degree_first_year_result_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->degree_first_year_result_date ? $convertedLead->mentorDetails->degree_first_year_result_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="degree_second_year_result_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->degree_second_year_result_date ? $convertedLead->mentorDetails->degree_second_year_result_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->degree_second_year_result_date ? $convertedLead->mentorDetails->degree_second_year_result_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="degree_third_year_result_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->degree_third_year_result_date ? $convertedLead->mentorDetails->degree_third_year_result_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->degree_third_year_result_date ? $convertedLead->mentorDetails->degree_third_year_result_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="degree_online_result_publication_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->degree_online_result_publication_date ? $convertedLead->mentorDetails->degree_online_result_publication_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->degree_online_result_publication_date ? $convertedLead->mentorDetails->degree_online_result_publication_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="degree_certificate_publication_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->degree_certificate_publication_date ? $convertedLead->mentorDetails->degree_certificate_publication_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->degree_certificate_publication_date ? $convertedLead->mentorDetails->degree_certificate_publication_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="degree_certificate_issued_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->degree_certificate_issued_date ? $convertedLead->mentorDetails->degree_certificate_issued_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->degree_certificate_issued_date ? $convertedLead->mentorDetails->degree_certificate_issued_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="degree_certificate_distribution_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->degree_certificate_distribution_date ? $convertedLead->mentorDetails->degree_certificate_distribution_date->format('Y-m-d') : '' }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->degree_certificate_distribution_date ? $convertedLead->mentorDetails->degree_certificate_distribution_date->format('d-m-Y') : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="degree_courier_tracking_number" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->degree_courier_tracking_number }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->degree_courier_tracking_number ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="degree_remarks" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->degree_remarks }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->degree_remarks ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
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
                                    <td colspan="51" class="text-center">No converted leads found for mentoring</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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

    #mentorEduMasterTable thead th,
    #mentorEduMasterTable tbody td {
        white-space: nowrap;
    }

    #mentorEduMasterTable thead th {
        position: sticky;
        top: 0;
        z-index: 5;
        background: #fff;
        box-shadow: inset 0 -1px 0 #e9ecef;
    }

    #mentorEduMasterTable tbody tr:hover {
        background: #fafbff;
    }

    #mentorEduMasterTable td .display-value {
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
    const registrationLinks = @json($registrationLinks);
    
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
            const dateFields = [
                'sslc_online_result_publication_date',
                'sslc_certificate_publication_date',
                'sslc_certificate_issued_date',
                'sslc_certificate_distribution_date',
                'plustwo_online_result_publication_date',
                'plustwo_certificate_publication_date',
                'plustwo_certificate_issued_date',
                'plustwo_certificate_distribution_date',
                'degree_registration_start_date',
                'degree_registration_form_summary_distribution_date',
                'degree_registration_form_summary_submission_date',
                'degree_id_card_issued_date',
                'degree_first_year_result_date',
                'degree_second_year_result_date',
                'degree_third_year_result_date',
                'degree_online_result_publication_date',
                'degree_certificate_publication_date',
                'degree_certificate_issued_date',
                'degree_certificate_distribution_date'
            ];
            
            if (dateFields.includes(field)) {
                editForm = createDateField(field, currentValue);
            } else if (['sslc_registration_link_id', 'plustwo_registration_link_id'].includes(field)) {
                editForm = createRegistrationLinkField(field, currentValue);
            } else if (['sslc_remarks', 'plustwo_remarks', 'degree_remarks'].includes(field)) {
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
                url: `/admin/faculty-edumaster-converted-leads/${id}/update-mentor-details`,
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

        function createRegistrationLinkField(field, currentValue) {
            let options = '<option value="">Select Registration Link</option>';
            registrationLinks.forEach(function(link) {
                const selected = String(currentValue) === String(link.id) ? 'selected' : '';
                options += `<option value="${link.id}" ${selected}>${link.title}</option>`;
            });
            
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
@include('admin.converted-leads.partials.course-flag-inline-scripts', ['courseUpdateUrl' => route('admin.faculty-edumaster-converted-leads.update-mentor-details', ['id' => '__ID__'])])
@endpush