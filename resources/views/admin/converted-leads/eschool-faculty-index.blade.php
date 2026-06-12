@extends('layouts.mantis')

@section('title', 'E-School Converted Faculty List')

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
                    <h5 class="m-b-10">E-School Converted Faculty List</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.converted-leads.index') }}">Converted Leads</a></li>
                    <li class="breadcrumb-item">E-School Faculty</li>
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
                    <a href="{{ route('admin.junior-vlogger-faculty-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Junior Vlogger Converted Faculty List
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
                    <a href="{{ route('admin.faculty-eschool-converted-leads.index') }}" class="btn btn-outline-primary active">
                        <i class="ti ti-user-star"></i> E-School Converted Faculty List
                    </a>
                    <a href="{{ route('admin.faculty-eduthanzeel-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Eduthanzeel Converted Faculty List
                    </a>
                    <a href="{{ route('admin.gmvss-faculty-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> GMVSS Faculty List
                    </a>
                                                            <a href="{{ route('admin.digital-marketing-faculty-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Digital Marketing Faculty List
                    </a><a href="{{ route('admin.data-science-faculty-converted-leads.index') }}" class="btn btn-outline-primary">
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
                    <a href="{{ route('admin.junior-vlogger-faculty-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Junior Vlogger Converted Faculty List
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
                <form method="GET" action="{{ route('admin.faculty-eschool-converted-leads.index') }}" id="filterForm">
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
                                @foreach($admission_batches as $admission_batch)
                                <option value="{{ $admission_batch->id }}" {{ request('admission_batch_id') == $admission_batch->id ? 'selected' : '' }}>
                                    {{ $admission_batch->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="sub_course_id" class="form-label">Sub Course</label>
                            <select class="form-select" id="sub_course_id" name="sub_course_id">
                                <option value="">All Sub Courses</option>
                                @foreach($sub_courses as $sub_course)
                                <option value="{{ $sub_course->id }}" {{ request('sub_course_id') == $sub_course->id ? 'selected' : '' }}>
                                    {{ $sub_course->title }}
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
                            <a href="{{ route('admin.faculty-eschool-converted-leads.index') }}" class="btn btn-secondary">
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
        <div class="card" data-mentor-update-url="{{ route('admin.faculty-eschool-converted-leads.update-mentor-details', ['id' => '__ID__']) }}">
            <div class="card-header">
                <h5>E-School Converted Faculty List</h5>
            </div>
            <div class="card-body">
                <!-- Desktop Table View -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover data_table_basic" id="eschoolMentorTable">
                        <thead>
                            <tr>
                                <th>SL No</th>
                                @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                <th>Support Verified</th>
                                @endif
                                <th>Converted Date</th>
                                <th>Academic Verified At</th>
                                <th>Support Verified At</th>
                                <th>Registration No</th>
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
                                <th>Subcourse</th>
                                <th>Call 1</th>
                                <th>WhatsApp Group</th>
                                <th>Screening Date</th>
                                <th>Screening Officer</th>
                                <th>Class Time</th>
                                <th>Tutor Name</th>
                                <th>Tutor Phone Number</th>
                                <th>Class Status</th>
                                <th>First PA</th>
                                <th>First PA Mark</th>
                                <th>Feedback Call 1</th>
                                <th>First PA Remarks</th>
                                <th>Second PA</th>
                                <th>Second PA Mark</th>
                                <th>Feedback Call 2</th>
                                <th>Second PA Remarks</th>
                                <th>Third PA</th>
                                <th>Third PA Mark</th>
                                <th>Feedback Call 3</th>
                                <th>Third PA Remarks</th>
                                <th>Certification Exam</th>
                                <th>Certification Exam Mark</th>
                                <th>Course Completion Feedback</th>
                                <th>Certificate Collection</th>
                                <th>Continuing Studies?</th>
                                <th>Reason</th>
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
                                @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                <td>
                                    @php $isSupportVerified = (bool) ($convertedLead->is_support_verified ?? false); @endphp
                                    <span class="badge {{ $isSupportVerified ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $isSupportVerified ? 'Verified' : 'Not Verified' }}
                                    </span>
                                    @if($isSupportVerified && $convertedLead->support_verified_at)
                                    <br><small class="text-muted">{{ \Carbon\Carbon::parse($convertedLead->support_verified_at)->format('d-m-Y') }}</small>
                                    @endif
                                </td>
                                @endif
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
                                        <span class="display-value">{{ $convertedLead->register_number ?: '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_faculty())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                @include('admin.converted-leads.partials.inline-course-flag-cell', ['convertedLead' => $convertedLead])
                                    @include('admin.converted-leads.partials.inline-call-time-cell', ['convertedLead' => $convertedLead])
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
                                    <div class="inline-edit" data-field="phone" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->phone }}">
                                        <span class="display-value">{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) ?: '-' }}</span>
                                        <span class="inline-code-value d-none" data-current="{{ $convertedLead->code }}"></span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
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
                                <td>{{ $convertedLead->batch?->title ?: '-' }}</td>
                                <td>{{ $convertedLead->admissionBatch?->title ?: '-' }}</td>
                                <td>{{ $convertedLead->subCourse?->title ?: '-' }}</td>
                                <td>
                                    <div class="inline-edit" data-field="call_1" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->call_1 }}">
                                        <span class="display-value">{{ $convertedLead->mentorDetails?->call_1 ?: '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="whatsapp_group" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->whatsapp_group }}">
                                        <span class="display-value">{{ $convertedLead->mentorDetails?->whatsapp_group ?: '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="screening_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->screening_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->screening_date)->format('Y-m-d') : '' }}">
                                        <span class="display-value">{{ $convertedLead->mentorDetails?->screening_date ? \Carbon\Carbon::parse($convertedLead->mentorDetails->screening_date)->format('d-m-Y') : '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="screening_officer" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->screening_officer }}">
                                        <span class="display-value">{{ $convertedLead->mentorDetails?->screening_officer ?: '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="class_time" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->class_time ? \Carbon\Carbon::parse($convertedLead->mentorDetails->class_time)->format('H:i') : '' }}">
                                        <span class="display-value">{{ $convertedLead->mentorDetails?->class_time ? \Carbon\Carbon::parse($convertedLead->mentorDetails->class_time)->format('h:i A') : '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="tutor_id" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->teacher_id }}">
                                        <span class="display-value">{{ $convertedLead->studentDetails?->teacher?->name ?: '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span>{{ $convertedLead->studentDetails?->teacher ? \App\Helpers\PhoneNumberHelper::display($convertedLead->studentDetails->teacher->code, $convertedLead->studentDetails->teacher->phone) : ($convertedLead->mentorDetails?->tutor_phone_number ?: '-') }}</span>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="class_status" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->class_status }}">
                                        <span class="display-value">{{ $convertedLead->mentorDetails?->class_status ?: '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="first_pa" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->first_pa }}">
                                        <span class="display-value">{{ $convertedLead->mentorDetails?->first_pa ?: '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="first_pa_mark" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->first_pa_mark }}">
                                        <span class="display-value">{{ $convertedLead->mentorDetails?->first_pa_mark ?: '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="feedback_call_1" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->feedback_call_1 }}">
                                        <span class="display-value">{{ $convertedLead->mentorDetails?->feedback_call_1 ?: '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="first_pa_remarks" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->first_pa_remarks }}">
                                        <span class="display-value">{{ $convertedLead->mentorDetails?->first_pa_remarks ?: '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="second_pa" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->second_pa }}">
                                        <span class="display-value">{{ $convertedLead->mentorDetails?->second_pa ?: '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="second_pa_mark" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->second_pa_mark }}">
                                        <span class="display-value">{{ $convertedLead->mentorDetails?->second_pa_mark ?: '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="feedback_call_2" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->feedback_call_2 }}">
                                        <span class="display-value">{{ $convertedLead->mentorDetails?->feedback_call_2 ?: '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="second_pa_remarks" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->second_pa_remarks }}">
                                        <span class="display-value">{{ $convertedLead->mentorDetails?->second_pa_remarks ?: '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="third_pa" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->third_pa }}">
                                        <span class="display-value">{{ $convertedLead->mentorDetails?->third_pa ?: '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="third_pa_mark" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->third_pa_mark }}">
                                        <span class="display-value">{{ $convertedLead->mentorDetails?->third_pa_mark ?: '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="feedback_call_3" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->feedback_call_3 }}">
                                        <span class="display-value">{{ $convertedLead->mentorDetails?->feedback_call_3 ?: '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="third_pa_remarks" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->third_pa_remarks }}">
                                        <span class="display-value">{{ $convertedLead->mentorDetails?->third_pa_remarks ?: '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="certification_exam" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->certification_exam }}">
                                        <span class="display-value">{{ $convertedLead->mentorDetails?->certification_exam ?: '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="certification_exam_mark" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->certification_exam_mark }}">
                                        <span class="display-value">{{ $convertedLead->mentorDetails?->certification_exam_mark ?: '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="course_completion_feedback" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->course_completion_feedback }}">
                                        <span class="display-value">{{ ucfirst($convertedLead->mentorDetails?->course_completion_feedback ?: '-') }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="certificate_collection" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->certificate_collection }}">
                                        <span class="display-value">{{ $convertedLead->mentorDetails?->certificate_collection ?: '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="continuing_studies" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->continuing_studies }}">
                                        <span class="display-value">{{ ucfirst($convertedLead->mentorDetails?->continuing_studies ?: '-') }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_faculty())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="reason" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->reason }}">
                                        <span class="display-value">{{ $convertedLead->mentorDetails?->reason ?: '-' }}</span>
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
                                <td colspan="35" class="text-center">No E-School converted leads found for mentoring</td>
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

<!-- Country Codes JSON for JavaScript -->
<script type="application/json" id="country-codes-json">
{!! json_encode($country_codes) !!}
</script>
@endsection

@push('styles')
<style>
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
    box-shadow: 0 0 0 2px rgba(115,102,255,0.15);
}

    .inline-edit .edit-form {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 4px;
    }

.inline-edit .edit-form .btn-group {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin: 0;
}

.inline-edit .edit-form .btn {
    padding: 2px 8px;
    font-size: 11px;
}

    /* Select2 sizing for inline tutor edit */
    .inline-edit .select2-container {
        width: 240px !important;
    }

    .inline-edit .select2-selection--single {
        height: 34px;
        padding: 4px 8px;
        border: 1px solid #ced4da;
        border-radius: 4px;
    }

    .inline-edit .select2-selection__rendered {
        line-height: 24px;
    }

    .inline-edit .select2-selection__arrow {
        height: 32px;
        right: 8px;
    }

#eschoolMentorTable thead th,
#eschoolMentorTable tbody td {
    white-space: nowrap;
}

#eschoolMentorTable thead th {
    position: sticky;
    top: 0;
    z-index: 5;
    background: #fff;
    box-shadow: inset 0 -1px 0 #e9ecef;
}

#eschoolMentorTable tbody tr:hover {
    background: #fafbff;
}

#eschoolMentorTable td .display-value {
    display: inline-block;
    max-width: 220px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    vertical-align: middle;
}
</style>
@endpush

@php
    $eschoolMentorTeacherOptions = isset($teachers)
        ? $teachers->map(function ($teacher) {
            return ['id' => $teacher->id, 'name' => $teacher->name];
        })->values()
        : collect();
@endphp

@push('scripts')
@include('admin.converted-leads.partials.placement-modal-reopen-script')
<script>
    const eschoolMentorTeachers = <?php echo $eschoolMentorTeacherOptions->toJson(); ?>;

    $(document).ready(function() {
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
            
            if (field === 'phone') {
                const currentCode = container.find('.inline-code-value').data('current') || '';
                editForm = createPhoneField(currentCode, currentValue);
            } else if (['call_1', 'whatsapp_group', 'telegram_group', 'class_status', 'first_pa', 'second_pa', 'third_pa', 'certification_exam', 'course_completion_feedback', 'certificate_collection', 'continuing_studies'].includes(field)) {
                editForm = createSelectField(field, currentValue);
            } else if (field === 'screening_date') {
                editForm = createDateField(field, currentValue);
            } else if (field === 'class_time') {
                editForm = createTimeField(field, currentValue);
            } else if (field === 'tutor_id') {
                editForm = createTutorField(field, currentValue);
            } else {
                editForm = createInputField(field, currentValue);
            }
            
            container.addClass('editing');
            container.append(editForm);
            
            // Load tutor options if it's a tutor field
            if (field === 'tutor_id') {
                const $select = container.find('.edit-form select');
                loadTutorOptions($select, currentValue);
                if ($.fn.select2) {
                    $select.select2({
                        dropdownParent: container.find('.edit-form'),
                        width: '100%',
                        placeholder: 'Select Tutor',
                        allowClear: true
                    }).val(String(currentValue || '')).trigger('change');
                }
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
            if (field === 'phone') {
                value = container.find('input[type="text"]').val();
            } else {
                value = container.find('input, select').val();
            }
            let extra = {};
            if (field === 'phone') {
                const codeVal = container.find('select[name="code"]').val();
                extra = { code: codeVal };
            }
            
            const btn = $(this);
            if (btn.data('busy')) return;
            btn.data('busy', true);
            btn.prop('disabled', true).html('<i class="ti ti-loader-2 spin"></i>');
            
            // Use converted-leads endpoint for register number and phone updates, faculty endpoint for other fields
            const isConvertedLeadField = ['phone', 'register_number'].includes(field);
            const updateUrl = isConvertedLeadField
                ? `/admin/converted-leads/${id}/inline-update`
                : `/admin/faculty-eschool-converted-leads/${id}/update-mentor-details`;
            
            $.ajax({
                url: updateUrl,
                method: 'POST',
                data: $.extend({
                    field: field,
                    value: value,
                    _token: $('meta[name="csrf-token"]').attr('content')
                }, extra),
                success: function(response) {
                    if (response.success) {
                        if (field === 'tutor_id') {
                            // Update tutor name display - response.value should be the teacher NAME
                            const teacherName = response.value ? String(response.value).trim() : '-';
                            container.find('.display-value').text(teacherName);
                            // Store the teacher_id in data-current for future edits
                            container.data('current', value || '');
                            
                            // Always update tutor phone number display - response.tutor_phone should be the formatted phone
                            const tutorPhoneCell = container.closest('tr').find('td').eq(14); // Tutor Phone Number is 15th column (0-indexed: 14)
                            if (tutorPhoneCell.length) {
                                const phoneDisplay = response.tutor_phone ? String(response.tutor_phone).trim() : '-';
                                tutorPhoneCell.find('span').text(phoneDisplay);
                            }
                        } else if (field === 'class_time') {
                            // For class_time, use the formatted response value
                            const displayTime = response.value || '-';
                            container.find('.display-value').text(displayTime);
                            // Store the raw time value (H:i format) in data-current for future edits
                            container.data('current', value);
                        } else {
                            const displayText = response.value || value || '-';
                            container.find('.display-value').text(displayText);

                            let currentForEditing = response.value || value;
                            if (field === 'phone') {
                                const codeVal = extra.code || '';
                                container.find('.inline-code-value').data('current', codeVal);
                                currentForEditing = value;
                            }
                            if (field === 'register_number' && (!value || value === '-' || value === 'N/A')) {
                                currentForEditing = '';
                            }
                            container.data('current', currentForEditing);
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
            container.find('.display-value').show();
            container.find('.edit-btn').show();
        });

        // Helper functions for creating form elements
        function createInputField(field, currentValue) {
            const displayValue = currentValue === '-' ? '' : currentValue;
            const inputType = field === 'call_time' ? 'time' : 'text';
            return `
                <div class="edit-form">
                    <input type="${inputType}" value="${displayValue}" class="form-control form-control-sm" autocomplete="off">
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
                // Convert d-m-Y to Y-m-d for date input
                if (currentValue.includes('-')) {
                    const parts = currentValue.split('-');
                    if (parts.length === 3) {
                        value = parts[2] + '-' + parts[1] + '-' + parts[0];
                    }
                } else {
                    value = currentValue;
                }
            }
            return `
                <div class="edit-form">
                    <input type="date" value="${value}" class="form-control form-control-sm">
                    <div class="btn-group mt-1">
                        <button class="btn btn-success btn-sm save-edit">Save</button>
                        <button class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createTimeField(field, currentValue) {
            let value = '';
            if (currentValue && currentValue !== '-') {
                // Convert h:i A to H:i for time input
                if (currentValue.includes('AM') || currentValue.includes('PM')) {
                    const time12 = currentValue.replace(/\s*(AM|PM)\s*/i, '');
                    const parts = time12.split(':');
                    if (parts.length === 2) {
                        let hours = parseInt(parts[0]);
                        const minutes = parts[1];
                        if (currentValue.toUpperCase().includes('PM') && hours !== 12) hours += 12;
                        if (currentValue.toUpperCase().includes('AM') && hours === 12) hours = 0;
                        value = String(hours).padStart(2, '0') + ':' + minutes;
                    }
                } else {
                    value = currentValue;
                }
            }
            return `
                <div class="edit-form">
                    <input type="time" value="${value}" class="form-control form-control-sm">
                    <div class="btn-group mt-1">
                        <button class="btn btn-success btn-sm save-edit">Save</button>
                        <button class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createSelectField(field, currentValue) {
            let options = '';
            
            if (field === 'call_1') {
                options = `
                    <option value="">Select</option>
                    <option value="Call Not Answered" ${currentValue === 'Call Not Answered' ? 'selected' : ''}>Call Not Answered</option>
                    <option value="Switched Off" ${currentValue === 'Switched Off' ? 'selected' : ''}>Switched Off</option>
                    <option value="Line Busy" ${currentValue === 'Line Busy' ? 'selected' : ''}>Line Busy</option>
                    <option value="Student Asks to Call Later" ${currentValue === 'Student Asks to Call Later' ? 'selected' : ''}>Student Asks to Call Later</option>
                    <option value="Lack of Interest in Conversation" ${currentValue === 'Lack of Interest in Conversation' ? 'selected' : ''}>Lack of Interest in Conversation</option>
                    <option value="Wrong Contact" ${currentValue === 'Wrong Contact' ? 'selected' : ''}>Wrong Contact</option>
                    <option value="Inconsistent Responses" ${currentValue === 'Inconsistent Responses' ? 'selected' : ''}>Inconsistent Responses</option>
                    <option value="Task Complete" ${currentValue === 'Task Complete' ? 'selected' : ''}>Task Complete</option>
                `;
            } else if (field === 'whatsapp_group') {
                options = `
                    <option value="">Select</option>
                    <option value="Sent link" ${currentValue === 'Sent link' ? 'selected' : ''}>Sent link</option>
                    <option value="Task Completed" ${currentValue === 'Task Completed' ? 'selected' : ''}>Task Completed</option>
                    <option value="Not Responding" ${currentValue === 'Not Responding' ? 'selected' : ''}>Not Responding</option>
                    <option value="Task Complete" ${currentValue === 'Task Complete' ? 'selected' : ''}>Task Complete</option>
                `;
            } else if (field === 'telegram_group') {
                options = `
                    <option value="">Select</option>
                    <option value="Sent link" ${currentValue === 'Sent link' ? 'selected' : ''}>Sent link</option>
                    <option value="task complete" ${currentValue === 'task complete' ? 'selected' : ''}>Task complete</option>
                `;
            } else if (field === 'class_status') {
                options = `
                    <option value="">Select</option>
                    <option value="Active" ${currentValue === 'Active' ? 'selected' : ''}>Active</option>
                    <option value="In Progress" ${currentValue === 'In Progress' ? 'selected' : ''}>In Progress</option>
                    <option value="Inactive" ${currentValue === 'Inactive' ? 'selected' : ''}>Inactive</option>
                    <option value="Dropped Out" ${currentValue === 'Dropped Out' ? 'selected' : ''}>Dropped Out</option>
                    <option value="Completed" ${currentValue === 'Completed' ? 'selected' : ''}>Completed</option>
                    <option value="Rejoining" ${currentValue === 'Rejoining' ? 'selected' : ''}>Rejoining</option>
                `;
            } else if (['first_pa', 'second_pa', 'third_pa', 'certification_exam'].includes(field)) {
                options = `
                    <option value="">Select</option>
                    <option value="Pending" ${currentValue === 'Pending' ? 'selected' : ''}>Pending</option>
                    <option value="Not Written" ${currentValue === 'Not Written' ? 'selected' : ''}>Not Written</option>
                    <option value="Completed" ${currentValue === 'Completed' ? 'selected' : ''}>Completed</option>
                `;
            } else if (field === 'course_completion_feedback') {
                options = `
                    <option value="">Select</option>
                    <option value="yes" ${currentValue === 'yes' ? 'selected' : ''}>Yes</option>
                    <option value="no" ${currentValue === 'no' ? 'selected' : ''}>No</option>
                `;
            } else if (field === 'certificate_collection') {
                options = `
                    <option value="">Select</option>
                    <option value="Pending" ${currentValue === 'Pending' ? 'selected' : ''}>Pending</option>
                    <option value="Collected" ${currentValue === 'Collected' ? 'selected' : ''}>Collected</option>
                    <option value="Not Required" ${currentValue === 'Not Required' ? 'selected' : ''}>Not Required</option>
                `;
            } else if (field === 'continuing_studies') {
                options = `
                    <option value="">Select</option>
                    <option value="yes" ${currentValue === 'yes' ? 'selected' : ''}>Yes</option>
                    <option value="no" ${currentValue === 'no' ? 'selected' : ''}>No</option>
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

        function createTutorField(field, currentValue) {
            return `
                <div class="edit-form">
                    <select class="form-select form-select-sm">
                        <option value="">Select Tutor</option>
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function loadTutorOptions($select, currentValue) {
            let options = '<option value="">Select Tutor</option>';
            eschoolMentorTeachers.forEach(function(teacher) {
                const isSelected = String(currentValue ?? '') === String(teacher.id) ? 'selected' : '';
                options += `<option value="${teacher.id}" ${isSelected}>${teacher.name}</option>`;
            });
            $select.html(options);
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
            const safePhone = (currentPhone && currentPhone !== 'N/A' && currentPhone !== '-') ? currentPhone : '';
            return `
                <div class="edit-form">
                    <div class="row g-1">
                        <div class="col-5">
                            <select name="code" class="form-select form-select-sm">${buildOptions(currentCode)}</select>
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
</script>
@include('admin.converted-leads.partials.course-flag-inline-scripts', ['courseUpdateUrl' => route('admin.faculty-eschool-converted-leads.update-mentor-details', ['id' => '__ID__'])])
@endpush