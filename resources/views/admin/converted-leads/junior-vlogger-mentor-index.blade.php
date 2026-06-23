@extends('layouts.mantis')

@section('title', 'CreateX AI Converted Mentor List')

@section('content')
@php
$feeStatusOptions = ['Paid' => 'Paid', 'Pending' => 'Pending', 'Partially Paid' => 'Partially Paid', 'Overdue' => 'Overdue', 'On Hold' => 'On Hold', 'Cancelled' => 'Cancelled'];
$canEdit = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_hod() || \App\Helpers\RoleHelper::is_mentor();
@endphp
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
    .inline-edit .edit-form { display: none; }
    .inline-edit.editing .edit-form { display: block; }
    .inline-edit.editing .display-value { display: none !important; }
    .inline-edit.editing .edit-btn { display: none !important; }
    .inline-edit .edit-form input, .inline-edit .edit-form select { min-width: 120px; }
    .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    #jvMentorTable thead th,
    #jvMentorTable tbody td {
        white-space: nowrap;
    }
    #jvMentorTable thead th {
        position: sticky;
        top: 0;
        background: #f8f9fa;
        z-index: 1;
        box-shadow: 0 1px 0 #dee2e6;
    }
    #jvMentorTable tbody tr:hover {
        background: #fafbff;
    }
    #jvMentorTable td .display-value {
        display: inline-block;
        max-width: 220px;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">CreateX AI Converted Mentor List</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.converted-leads.index') }}">Converted Leads</a></li>
                    <li class="breadcrumb-item">CreateX AI Mentor</li>
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
                    <a href="{{ route('admin.junior-vlogger-mentor-converted-leads.index') }}" class="btn btn-primary active">
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
                        <i class="ti ti-headphones"></i> CreateX AI â€“ Course Support List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<!-- [ Support List ] end -->

<!-- [ Filter ] start -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.junior-vlogger-mentor-converted-leads.index') }}" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Name, Phone, Register No">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="batch_id" class="form-label">Batch</label>
                            <select class="form-select" id="batch_id" name="batch_id">
                                <option value="">All</option>
                                @foreach($batches as $b)
                                <option value="{{ $b->id }}" {{ request('batch_id') == $b->id ? 'selected' : '' }}>{{ $b->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="admission_batch_id" class="form-label">Admission Batch</label>
                            <select class="form-select" id="admission_batch_id" name="admission_batch_id" data-selected="{{ request('admission_batch_id') }}">
                                <option value="">All</option>
                            </select>
                        </div>
                        @include('admin.converted-leads.partials.mentor-flag-filter-field')
                        
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="is_b2b" class="form-label">B2B / In House</label>
                            <select class="form-select" id="is_b2b" name="is_b2b">
                                <option value="">All</option>
                                <option value="b2b" {{ request('is_b2b') === 'b2b' ? 'selected' : '' }}>B2B</option>
                                <option value="in_house" {{ request('is_b2b') === 'in_house' ? 'selected' : '' }}>In House</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <button type="submit" class="btn btn-primary"><i class="ti ti-search"></i> Filter</button>
                            <a href="{{ route('admin.junior-vlogger-mentor-converted-leads.index') }}" class="btn btn-secondary"><i class="ti ti-x"></i> Clear</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- [ Filter ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-12">
        <div class="card" data-mentor-update-url="{{ route('admin.junior-vlogger-mentor-converted-leads.update-mentor-details', ['id' => '__ID__']) }}">
            <div class="card-header">
                <h5 class="mb-0">CreateX AI Converted Mentor List</h5>
            </div>
            <div class="card-body">
                <!-- Desktop Table View -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover data_table_basic" id="jvMentorTable">
                            <thead>
                                <tr>
                                <th>SL</th>
                                <th>Conversion Date</th>
                                <th>Total Class Days</th>
                                <th>B2B Team</th>
                                <th>Batch</th>
                                <th>Time</th>
                                <th>Reg. Number</th>
                                <th>Flag</th>
                                    <th>Call Time</th>
                                    <th>Full Name</th>
                                <th>Age</th>
                                <th>Primary Mobile</th>
                                <th>WhatsApp Number</th>
                                <th>Medium of Study</th>
                                <th>Previous Qualification</th>
                                <th>Technology Perf.</th>
                                <th>Reg. & First Term Fee Status</th>
                                <th>First Term Start Date</th>
                                <th>1st Term Trainer</th>
                                <th>1st Task 1 Date</th>
                                <th>1st Task 2 Date</th>
                                <th>1st No. of Days</th>
                                <th>1st Completion Date</th>
                                <th>Second Term Fee Status</th>
                                <th>Second Term Start Date</th>
                                <th>2nd Term Trainer</th>
                                <th>2nd Task 1 Date</th>
                                <th>2nd Task 2 Date</th>
                                <th>2nd No. of Days</th>
                                <th>2nd Completion Date</th>
                                <th>Third Term Fee Status</th>
                                <th>Third Term Start Date</th>
                                <th>3rd Term Trainer</th>
                                <th>Project 1 Date</th>
                                <th>Project 2 Date</th>
                                <th>Project 3 Date</th>
                                <th>3rd No. of Days</th>
                                <th>3rd Completion Date</th>
                                <th>Certificate Issued Date</th>
                                <th>B2B Partner ID</th>
                                <th>B2B Code</th>
                                <th>Feedback / Notes</th>
                                <th>Actions</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($convertedLeads as $index => $lead)
                            @php
                                $jvLead = $lead->lead ? $lead->lead->juniorVloggerStudentDetails : null;
                                $md = $lead->mentorDetails;
                                $age = $lead->dob ? \Carbon\Carbon::parse($lead->dob)->age : null;
                                $fmt = function($d) { return $d ? \Carbon\Carbon::parse($d)->format('d-m-Y') : '-'; };
                                $fmtYmd = function($d) { return $d ? \Carbon\Carbon::parse($d)->format('Y-m-d') : ''; };
                            @endphp
                            <tr class="{{ $lead->is_cancelled ? 'cancelled-row' : '' }}">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $lead->created_at ? $lead->created_at->format('d-m-Y') : '-' }}</td>
                                <td>
                                    @if($canEdit)
                                    <div class="inline-edit" data-field="total_class_days" data-id="{{ $lead->id }}" data-current="{{ $md?->total_class_days ?? '' }}">
                                        <span class="display-value">{{ $md?->total_class_days ?? '-' }}</span>
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
                                    </div>
                                    @else
                                    {{ $md?->total_class_days ?? '-' }}
                                    @endif
                                </td>
                                <td>{{ $lead->is_b2b == 1 && $lead->lead && $lead->lead->team ? $lead->lead->team->name : ($lead->is_b2b == 1 ? 'B2B' : 'In House') }}</td>
                                <td>
                                    @if($canEdit)
                                    <div class="inline-edit" data-field="batch_id" data-id="{{ $lead->id }}" data-course-id="{{ $lead->course_id }}" data-current-id="{{ $lead->batch_id }}">
                                        <span class="display-value">{{ $lead->batch ? $lead->batch->title : 'N/A' }}</span>
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
                                    </div>
                                    @else
                                    {{ $lead->batch ? $lead->batch->title : '-' }}
                                    @endif
                                </td>
                                <td>
                                    @if($canEdit && $course && $course->needs_time)
                                    <div class="inline-edit" data-field="class_time_id" data-id="{{ $lead->id }}" data-course-id="{{ $lead->course_id }}" data-programme-type="{{ $jvLead?->programme_type }}" data-current-id="{{ $jvLead?->class_time_id }}">
                                        <span class="display-value">
                                            @if($jvLead && $jvLead->classTime)
                                            {{ \Carbon\Carbon::parse($jvLead->classTime->from_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($jvLead->classTime->to_time)->format('h:i A') }}
                                            @else - @endif
                                        </span>
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
                                    </div>
                                    @else
                                    @if($jvLead && $jvLead->classTime)
                                    {{ \Carbon\Carbon::parse($jvLead->classTime->from_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($jvLead->classTime->to_time)->format('h:i A') }}
                                    @else - @endif
                                    @endif
                                </td>
                                <td>
                                    @if($canEdit)
                                    <div class="inline-edit" data-field="register_number" data-id="{{ $lead->id }}" data-current="{{ $lead->register_number }}">
                                        <span class="display-value">{{ $lead->register_number ?? '-' }}</span>
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
                                    </div>
                                    @else
                                    {{ $lead->register_number ?? '-' }}
                                    @endif
                                </td>
                                @include('admin.converted-leads.partials.inline-mentor-flag-cell', ['convertedLead' => $lead])
                                @include('admin.converted-leads.partials.inline-call-time-cell', ['convertedLead' => $lead])
                                <td>
                                    @if($canEdit)
                                    <div class="inline-edit" data-field="name" data-id="{{ $lead->id }}" data-current="{{ $lead->name }}">
                                        <span class="display-value">{{ $lead->name }}</span>
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
                                    </div>
                                    @else
                                    {{ $lead->name }}
                                    @endif
                                </td>
                                <td>{{ $age !== null ? $age : '-' }}</td>
                                <td>
                                    @if($canEdit)
                                    <div class="inline-edit" data-field="phone" data-id="{{ $lead->id }}" data-current="{{ $lead->phone }}">
                                        <span class="display-value">{{ \App\Helpers\PhoneNumberHelper::display($lead->code, $lead->phone) }}</span>
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
                                    </div>
                                    <div class="d-none inline-code-value" data-field="code" data-id="{{ $lead->id }}" data-current="{{ $lead->code }}"></div>
                                    @else
                                    {{ \App\Helpers\PhoneNumberHelper::display($lead->code, $lead->phone) }}
                                    @endif
                                </td>
                                <td>
                                    @if($canEdit)
                                    <div class="inline-edit" data-field="whatsapp_number" data-id="{{ $lead->id }}" data-current="{{ $jvLead?->whatsapp_number }}">
                                        <span class="display-value">{{ $jvLead && $jvLead->whatsapp_number ? \App\Helpers\PhoneNumberHelper::display($jvLead->whatsapp_code, $jvLead->whatsapp_number) : '-' }}</span>
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
                                    </div>
                                    @else
                                    {{ $jvLead && $jvLead->whatsapp_number ? \App\Helpers\PhoneNumberHelper::display($jvLead->whatsapp_code, $jvLead->whatsapp_number) : '-' }}
                                    @endif
                                </td>
                                <td>
                                    @if($canEdit)
                                    <div class="inline-edit" data-field="medium_of_study" data-id="{{ $lead->id }}" data-current="{{ $jvLead?->medium_of_study }}">
                                        <span class="display-value">{{ $jvLead && $jvLead->medium_of_study ? ucfirst(str_replace('_', ' ', $jvLead->medium_of_study)) : '-' }}</span>
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
                                    </div>
                                    @else
                                    {{ $jvLead && $jvLead->medium_of_study ? ucfirst(str_replace('_', ' ', $jvLead->medium_of_study)) : '-' }}
                                    @endif
                                </td>
                                <td>
                                    @if($canEdit)
                                    <div class="inline-edit" data-field="previous_qualification" data-id="{{ $lead->id }}" data-current="{{ $jvLead?->previous_qualification }}">
                                        <span class="display-value">{{ $jvLead && $jvLead->previous_qualification ? ucfirst(str_replace('_', ' ', $jvLead->previous_qualification)) : '-' }}</span>
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
                                    </div>
                                    @else
                                    {{ $jvLead && $jvLead->previous_qualification ? ucfirst(str_replace('_', ' ', $jvLead->previous_qualification)) : '-' }}
                                    @endif
                                </td>
                                <td>
                                    @if($canEdit)
                                    <div class="inline-edit" data-field="technology_performance_category" data-id="{{ $lead->id }}" data-current="{{ $jvLead?->technology_performance_category }}">
                                        <span class="display-value">{{ $jvLead && $jvLead->technology_performance_category ? ucfirst(str_replace('_', ' ', $jvLead->technology_performance_category)) : '-' }}</span>
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
                                    </div>
                                    @else
                                    {{ $jvLead && $jvLead->technology_performance_category ? ucfirst(str_replace('_', ' ', $jvLead->technology_performance_category)) : '-' }}
                                    @endif
                                </td>
                                {{-- First term --}}
                                <td>
                                    @if($canEdit)
                                    <div class="inline-edit" data-field="first_term_fee_status" data-id="{{ $lead->id }}" data-field-type="select" data-options='@json($feeStatusOptions)' data-current="{{ $md?->first_term_fee_status }}">
                                        <span class="display-value">{{ $md?->first_term_fee_status ?? '-' }}</span>
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
                                    </div>
                                    @else
                                    {{ $md?->first_term_fee_status ?? '-' }}
                                    @endif
                                </td>
                                <td>
                                    @if($canEdit)
                                    <div class="inline-edit" data-field="first_term_start_date" data-id="{{ $lead->id }}" data-current="{{ $fmtYmd($md?->first_term_start_date) }}">
                                        <span class="display-value">{{ $fmt($md?->first_term_start_date) }}</span>
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
                                    </div>
                                    @else
                                    {{ $fmt($md?->first_term_start_date) }}
                                    @endif
                                </td>
                                <td>
                                    @if($canEdit)
                                    <div class="inline-edit" data-field="first_term_trainer_name_phone" data-id="{{ $lead->id }}" data-current="{{ $md?->first_term_trainer_name_phone }}">
                                        <span class="display-value">{{ $md?->first_term_trainer_name_phone ?? '-' }}</span>
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
                                    </div>
                                    @else
                                    {{ $md?->first_term_trainer_name_phone ?? '-' }}
                                    @endif
                                </td>
                                <td>
                                    @if($canEdit)
                                    <div class="inline-edit" data-field="first_term_task_1_date" data-id="{{ $lead->id }}" data-current="{{ $fmtYmd($md?->first_term_task_1_date) }}">
                                        <span class="display-value">{{ $fmt($md?->first_term_task_1_date) }}</span>
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
                                    </div>
                                    @else {{ $fmt($md?->first_term_task_1_date) }} @endif
                                </td>
                                <td>
                                    @if($canEdit)
                                    <div class="inline-edit" data-field="first_term_task_2_date" data-id="{{ $lead->id }}" data-current="{{ $fmtYmd($md?->first_term_task_2_date) }}">
                                        <span class="display-value">{{ $fmt($md?->first_term_task_2_date) }}</span>
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
                                    </div>
                                    @else {{ $fmt($md?->first_term_task_2_date) }} @endif
                                </td>
                                <td>
                                    @if($canEdit)
                                    <div class="inline-edit" data-field="first_term_number_of_days" data-id="{{ $lead->id }}" data-current="{{ $md?->first_term_number_of_days }}">
                                        <span class="display-value">{{ $md?->first_term_number_of_days ?? '-' }}</span>
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
                                    </div>
                                    @else {{ $md?->first_term_number_of_days ?? '-' }} @endif
                                </td>
                                <td>
                                    @if($canEdit)
                                    <div class="inline-edit" data-field="first_term_completion_date" data-id="{{ $lead->id }}" data-current="{{ $fmtYmd($md?->first_term_completion_date) }}">
                                        <span class="display-value">{{ $fmt($md?->first_term_completion_date) }}</span>
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
                                    </div>
                                    @else {{ $fmt($md?->first_term_completion_date) }} @endif
                                </td>
                                {{-- Second term --}}
                                <td>
                                    @if($canEdit)
                                    <div class="inline-edit" data-field="second_term_fee_status" data-id="{{ $lead->id }}" data-field-type="select" data-options='@json($feeStatusOptions)' data-current="{{ $md?->second_term_fee_status }}">
                                        <span class="display-value">{{ $md?->second_term_fee_status ?? '-' }}</span>
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
                                    </div>
                                    @else {{ $md?->second_term_fee_status ?? '-' }} @endif
                                </td>
                                <td>
                                    @if($canEdit)
                                    <div class="inline-edit" data-field="second_term_start_date" data-id="{{ $lead->id }}" data-current="{{ $fmtYmd($md?->second_term_start_date) }}">
                                        <span class="display-value">{{ $fmt($md?->second_term_start_date) }}</span>
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
                                    </div>
                                    @else {{ $fmt($md?->second_term_start_date) }} @endif
                                </td>
                                <td>
                                    @if($canEdit)
                                    <div class="inline-edit" data-field="second_term_trainer_name_phone" data-id="{{ $lead->id }}" data-current="{{ $md?->second_term_trainer_name_phone }}">
                                        <span class="display-value">{{ $md?->second_term_trainer_name_phone ?? '-' }}</span>
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
                                    </div>
                                    @else {{ $md?->second_term_trainer_name_phone ?? '-' }} @endif
                                </td>
                                <td>@if($canEdit)<div class="inline-edit" data-field="second_term_task_1_date" data-id="{{ $lead->id }}" data-current="{{ $fmtYmd($md?->second_term_task_1_date) }}"><span class="display-value">{{ $fmt($md?->second_term_task_1_date) }}</span><button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button></div>@else{{ $fmt($md?->second_term_task_1_date) }}@endif</td>
                                <td>@if($canEdit)<div class="inline-edit" data-field="second_term_task_2_date" data-id="{{ $lead->id }}" data-current="{{ $fmtYmd($md?->second_term_task_2_date) }}"><span class="display-value">{{ $fmt($md?->second_term_task_2_date) }}</span><button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button></div>@else{{ $fmt($md?->second_term_task_2_date) }}@endif</td>
                                <td>@if($canEdit)<div class="inline-edit" data-field="second_term_number_of_days" data-id="{{ $lead->id }}" data-current="{{ $md?->second_term_number_of_days }}"><span class="display-value">{{ $md?->second_term_number_of_days ?? '-' }}</span><button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button></div>@else{{ $md?->second_term_number_of_days ?? '-' }}@endif</td>
                                <td>@if($canEdit)<div class="inline-edit" data-field="second_term_completion_date" data-id="{{ $lead->id }}" data-current="{{ $fmtYmd($md?->second_term_completion_date) }}"><span class="display-value">{{ $fmt($md?->second_term_completion_date) }}</span><button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button></div>@else{{ $fmt($md?->second_term_completion_date) }}@endif</td>
                                {{-- Third term --}}
                                <td>
                                    @if($canEdit)
                                    <div class="inline-edit" data-field="third_term_fee_status" data-id="{{ $lead->id }}" data-field-type="select" data-options='@json($feeStatusOptions)' data-current="{{ $md?->third_term_fee_status }}">
                                        <span class="display-value">{{ $md?->third_term_fee_status ?? '-' }}</span>
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
                                    </div>
                                    @else {{ $md?->third_term_fee_status ?? '-' }} @endif
                                </td>
                                <td>@if($canEdit)<div class="inline-edit" data-field="third_term_start_date" data-id="{{ $lead->id }}" data-current="{{ $fmtYmd($md?->third_term_start_date) }}"><span class="display-value">{{ $fmt($md?->third_term_start_date) }}</span><button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button></div>@else{{ $fmt($md?->third_term_start_date) }}@endif</td>
                                <td>@if($canEdit)<div class="inline-edit" data-field="third_term_trainer_name_phone" data-id="{{ $lead->id }}" data-current="{{ $md?->third_term_trainer_name_phone }}"><span class="display-value">{{ $md?->third_term_trainer_name_phone ?? '-' }}</span><button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button></div>@else{{ $md?->third_term_trainer_name_phone ?? '-' }}@endif</td>
                                <td>@if($canEdit)<div class="inline-edit" data-field="third_term_project_1_date" data-id="{{ $lead->id }}" data-current="{{ $fmtYmd($md?->third_term_project_1_date) }}"><span class="display-value">{{ $fmt($md?->third_term_project_1_date) }}</span><button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button></div>@else{{ $fmt($md?->third_term_project_1_date) }}@endif</td>
                                <td>@if($canEdit)<div class="inline-edit" data-field="third_term_project_2_date" data-id="{{ $lead->id }}" data-current="{{ $fmtYmd($md?->third_term_project_2_date) }}"><span class="display-value">{{ $fmt($md?->third_term_project_2_date) }}</span><button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button></div>@else{{ $fmt($md?->third_term_project_2_date) }}@endif</td>
                                <td>@if($canEdit)<div class="inline-edit" data-field="third_term_project_3_date" data-id="{{ $lead->id }}" data-current="{{ $fmtYmd($md?->third_term_project_3_date) }}"><span class="display-value">{{ $fmt($md?->third_term_project_3_date) }}</span><button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button></div>@else{{ $fmt($md?->third_term_project_3_date) }}@endif</td>
                                <td>@if($canEdit)<div class="inline-edit" data-field="third_term_number_of_days" data-id="{{ $lead->id }}" data-current="{{ $md?->third_term_number_of_days }}"><span class="display-value">{{ $md?->third_term_number_of_days ?? '-' }}</span><button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button></div>@else{{ $md?->third_term_number_of_days ?? '-' }}@endif</td>
                                <td>@if($canEdit)<div class="inline-edit" data-field="third_term_completion_date" data-id="{{ $lead->id }}" data-current="{{ $fmtYmd($md?->third_term_completion_date) }}"><span class="display-value">{{ $fmt($md?->third_term_completion_date) }}</span><button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button></div>@else{{ $fmt($md?->third_term_completion_date) }}@endif</td>
                                <td>
                                    @if($canEdit)
                                    @php $certDate = $md?->certificate_issued_date ?? $lead->studentDetails?->certificate_issued_date; @endphp
                                    <div class="inline-edit" data-field="certificate_issued_date" data-id="{{ $lead->id }}" data-current="{{ $fmtYmd($certDate) }}">
                                        <span class="display-value">{{ $fmt($certDate) }}</span>
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
                                    </div>
                                    @else
                                    {{ $fmt($md?->certificate_issued_date ?? $lead->studentDetails?->certificate_issued_date) }}
                                    @endif
                                </td>
                                <td>{{ $lead->lead && $lead->lead->team && $lead->lead->team->detail ? ($lead->lead->team->detail->b2b_partner_id ?? '-') : '-' }}</td>
                                <td>{{ $lead->lead && $lead->lead->team && $lead->lead->team->detail ? ($lead->lead->team->detail->b2b_code ?? '-') : '-' }}</td>
                                <td>
                                    @if($canEdit)
                                    <div class="inline-edit" data-field="jv_feedback_notes" data-id="{{ $lead->id }}" data-current="{{ $md?->jv_feedback_notes }}">
                                        <span class="display-value">{{ $md?->jv_feedback_notes ?? '-' }}</span>
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
                                    </div>
                                    @else
                                    {{ $md?->jv_feedback_notes ?? '-' }}
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.converted-leads.show', $lead->id) }}" class="btn btn-sm btn-outline-primary" title="View"><i class="ti ti-eye"></i></a>
                                    <a href="{{ route('admin.invoices.index', $lead->id) }}" class="btn btn-sm btn-success" title="Invoice"><i class="ti ti-receipt"></i></a>
                                </td>
                                <td>
                                    @if($lead->mentorDetails?->is_placement_passed)
                                        <span class="badge bg-success">Placement Passed</span>
                                        @if($lead->mentorDetails?->is_placement_passed_at)
                                            <br><small class="text-muted">{{ $lead->mentorDetails->is_placement_passed_at->format('d-m-Y h:i A') }}</small>
                                        @endif
                                        @if($lead->mentorDetails?->placement_resume)
                                            <br><a href="{{ asset('storage/' . $lead->mentorDetails->placement_resume) }}" target="_blank" class="btn btn-sm btn-link p-0 small"><i class="ti ti-file-text"></i> View Resume</a>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                                <br><a href="javascript:void(0);" class="btn btn-sm {{ $lead->mentorDetails->is_resume_verified ? 'btn-success' : 'btn-outline-success' }} px-2 py-0"
                                                    onclick="show_small_modal('{{ route('admin.converted-leads.verify-resume-modal', $lead->id) }}', 'Resume Verification')"
                                                    title="Resume Verification">
                                                    <i class="ti ti-circle-check"></i> {{ $lead->mentorDetails->is_resume_verified ? 'Resume Verified' : 'Verify Resume' }}@if($lead->mentorDetails->is_resume_verified && $lead->mentorDetails->resume_verified_at) ({{ $lead->mentorDetails->resume_verified_at->format('d M Y') }})@endif
                                                </a>
                                                <br><a href="javascript:void(0);" class="btn btn-sm btn-outline-primary px-2 py-0"
                                                    onclick="show_small_modal('{{ route('admin.converted-leads.move-to-placement', $lead->id) }}', 'Change Resume')"
                                                    title="Change Resume">
                                                    <i class="ti ti-upload"></i> Change Resume
                                                </a>
                                            @endif
                                        @endif
                                    @else
                                        <a href="javascript:void(0);" class="btn btn-outline-primary btn-sm px-2"
                                            onclick="show_small_modal('{{ route('admin.converted-leads.move-to-placement', $lead->id) }}', 'Move to Placement')"
                                            title="Move to Placement">
                                            <i class="ti ti-user-check"></i> Move to Placement
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="53" class="text-center">No records found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                </div>

                <!-- Mobile Card View -->
                <div class="d-lg-none">
                    @forelse($convertedLeads as $index => $lead)
                    @php
                        $age = $lead->dob ? \Carbon\Carbon::parse($lead->dob)->age : null;
                    @endphp
                    <div class="card mb-3 {{ $lead->is_cancelled ? 'cancelled-card' : '' }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-0">{{ $lead->name }}</h6>
                                @if($lead->is_cancelled)<span class="badge bg-danger">Cancelled</span>@endif
                            </div>
                            <div class="row g-2 mb-2 small">
                                <div class="col-6"><span class="text-muted">Reg. No</span><br>{{ $lead->register_number ?? '-' }}</div>
                                <div class="col-6"><span class="text-muted">Batch</span><br>{{ $lead->batch ? $lead->batch->title : '-' }}</div>
                                <div class="col-6"><span class="text-muted">Phone</span><br>{{ \App\Helpers\PhoneNumberHelper::display($lead->code, $lead->phone) }}</div>
                                <div class="col-6"><span class="text-muted">Conversion</span><br>{{ $lead->created_at ? $lead->created_at->format('d-m-Y') : '-' }}</div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.converted-leads.show', $lead->id) }}" class="btn btn-sm btn-outline-primary"><i class="ti ti-eye"></i> View</a>
                                <a href="{{ route('admin.invoices.index', $lead->id) }}" class="btn btn-sm btn-success"><i class="ti ti-receipt"></i> Invoice</a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">No records found.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
@endsection

@push('scripts')
@include('admin.converted-leads.partials.placement-modal-reopen-script')
<script type="application/json" id="country-codes-json">@json($country_codes)</script>
<script>
$(document).ready(function() {
    // DataTable is automatically initialized by layout for tables with 'data_table_basic' class

    var updateUrlBase = '{{ route("admin.junior-vlogger-mentor-converted-leads.update-mentor-details", ":id") }}';

    function loadAdmissionBatches(batchId, selectedId) {
        var $sel = $('#admission_batch_id');
        $sel.html('<option value="">Loading...</option>');
        if (!batchId) { $sel.html('<option value="">All</option>'); return; }
        $.get('/api/admission-batches/by-batch/' + batchId).done(function(list) {
            var opts = '<option value="">All</option>';
            if (list && list.length) {
                list.forEach(function(i) {
                    opts += '<option value="' + i.id + '"' + (String(selectedId) === String(i.id) ? ' selected' : '') + '>' + i.title + '</option>';
                });
            }
            $sel.html(opts);
        }).fail(function() { $sel.html('<option value="">All</option>'); });
    }
    loadAdmissionBatches($('#batch_id').val(), $('#admission_batch_id').data('selected'));
    $('#batch_id').on('change', function() { loadAdmissionBatches($(this).val(), ''); });

    function createInput(currentVal, field) {
        // jQuery .data() parses numeric data-current as a number; .replace() requires a string.
        var s = (currentVal === undefined || currentVal === null) ? '' : String(currentVal);
        var v = (s === '-' || s === '') ? '' : s;
        var inputType = field === 'call_time' ? 'time' : 'text';
        return '<div class="edit-form"><input type=\"' + inputType + '\" class="form-control form-control-sm" value="' + v.replace(/"/g, '&quot;') + '"><div class="btn-group mt-1"><button type="button" class="btn btn-success btn-sm save-edit">Save</button><button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button></div></div>';
    }
    function createDateInput(currentVal) {
        var s = (currentVal === undefined || currentVal === null) ? '' : String(currentVal);
        var v = (s && s !== '-') ? s : '';
        return '<div class="edit-form"><input type="date" class="form-control form-control-sm" value="' + v + '"><div class="btn-group mt-1"><button type="button" class="btn btn-success btn-sm save-edit">Save</button><button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button></div></div>';
    }
    function createSelect(options, currentVal) {
        var opts = '<option value="">--</option>';
        if (typeof options === 'string') options = JSON.parse(options);
        for (var k in options) {
            opts += '<option value="' + k + '"' + (String(currentVal) === String(k) ? ' selected' : '') + '>' + options[k] + '</option>';
        }
        return '<div class="edit-form"><select class="form-select form-select-sm">' + opts + '</select><div class="btn-group mt-1"><button type="button" class="btn btn-success btn-sm save-edit">Save</button><button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button></div></div>';
    }
    function createBatchSelect() {
        return '<div class="edit-form"><select class="form-select form-select-sm"><option value="">Loading...</option></select><div class="btn-group mt-1"><button type="button" class="btn btn-success btn-sm save-edit">Save</button><button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button></div></div>';
    }
    function createAdmissionBatchSelect() {
        return '<div class="edit-form"><select class="form-select form-select-sm"><option value="">Loading...</option></select><div class="btn-group mt-1"><button type="button" class="btn btn-success btn-sm save-edit">Save</button><button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button></div></div>';
    }
    function createClassTimeSelect() {
        return '<div class="edit-form"><select class="form-select form-select-sm"><option value="">Loading...</option></select><div class="btn-group mt-1"><button type="button" class="btn btn-success btn-sm save-edit">Save</button><button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button></div></div>';
    }

    var dateFields = ['first_term_start_date','first_term_task_1_date','first_term_task_2_date','first_term_completion_date','second_term_start_date','second_term_task_1_date','second_term_task_2_date','second_term_completion_date','third_term_start_date','third_term_project_1_date','third_term_project_2_date','third_term_project_3_date','third_term_completion_date','certificate_issued_date'];

    $(document).on('click', '.edit-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $c = $(this).closest('.inline-edit');
        var field = $c.data('field');
        var rawCurrent = $c.data('current');
        var current = (rawCurrent === undefined || rawCurrent === null) ? '' : rawCurrent;
        $('.inline-edit').removeClass('editing').find('.edit-form').remove();
        var html = '';
        if (field === 'batch_id') {
            html = createBatchSelect();
            $c.addClass('editing').append(html);
            var courseId = $c.data('course-id');
            var currentId = $c.data('current-id');
            $.get('/api/batches/by-course/' + courseId).done(function(r) {
                var opts = '<option value="">Select</option>';
                if (r.success && r.batches) {
                    r.batches.forEach(function(b) {
                        opts += '<option value="' + b.id + '"' + (String(currentId) === String(b.id) ? ' selected' : '') + '>' + b.title + '</option>';
                    });
                }
                $c.find('select').html(opts).focus();
            });
        } else if (field === 'admission_batch_id') {
            html = createAdmissionBatchSelect();
            $c.addClass('editing').append(html);
            var batchId = $c.data('batch-id');
            var currentId = $c.data('current-id');
            $.get('/api/admission-batches/by-batch/' + batchId).done(function(list) {
                var opts = '<option value="">Select</option>';
                if (list && list.length) list.forEach(function(i) {
                    opts += '<option value="' + i.id + '"' + (String(currentId) === String(i.id) ? ' selected' : '') + '>' + i.title + '</option>';
                });
                $c.find('select').html(opts).focus();
            });
        } else if (field === 'class_time_id') {
            html = createClassTimeSelect();
            $c.addClass('editing').append(html);
            var courseId = $c.data('course-id');
            var programmeType = $c.data('programme-type') || 'online';
            var currentId = $c.data('current-id');
            $.get('/api/class-times/by-course/' + courseId + '?class_type=' + (programmeType || 'online')).done(function(r) {
                var opts = '<option value="">Select</option>';
                if (r && r.length) r.forEach(function(t) {
                    opts += '<option value="' + t.id + '"' + (String(currentId) === String(t.id) ? ' selected' : '') + '>' + (t.from_time + ' - ' + t.to_time) + '</option>';
                });
                $c.find('select').html(opts).focus();
            });
        } else if ($c.data('field-type') === 'select' && $c.data('options')) {
            html = createSelect($c.data('options'), current);
            $c.addClass('editing').append(html);
            $c.find('select').focus();
        } else if (dateFields.indexOf(field) !== -1) {
            html = createDateInput(current);
            $c.addClass('editing').append(html);
            $c.find('input').focus();
        } else {
            html = createInput(current, field);
            $c.addClass('editing').append(html);
            $c.find('input').focus();
        }
    });

    $(document).on('click', '.save-edit', function(e) {
        e.preventDefault();
        var $c = $(this).closest('.inline-edit');
        var field = $c.data('field');
        var id = $c.data('id');
        var value = $c.find('input, select').val() || '';
        var $btn = $(this);
        if ($btn.data('busy')) return;
        $btn.data('busy', true).prop('disabled', true).html('<i class="ti ti-loader-2 spin"></i>');
        var data = { field: field, value: value, _token: $('meta[name="csrf-token"]').attr('content') };
        if (field === 'phone') {
            var codeSel = $c.closest('tr').find('.inline-code-value');
            if (codeSel.length) data.code = $c.find('select[name="code"]').val();
        }
        $.ajax({
            url: updateUrlBase.replace(':id', id),
            method: 'POST',
            data: data,
            success: function(res) {
                if (res.success) {
                    $c.find('.display-value').text(res.value || 'N/A');
                    $c.data('current', value);
                    if (field === 'batch_id' || field === 'admission_batch_id' || field === 'class_time_id') $c.data('current-id', value);
                    if (typeof toast_success === 'function') toast_success(res.message);
                } else {
                    if (typeof toast_error === 'function') toast_error(res.error || 'Update failed');
                }
            },
            error: function(xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.error) || 'Update failed';
                if (typeof toast_error === 'function') toast_error(msg);
            },
            complete: function() {
                $btn.data('busy', false).prop('disabled', false).html('Save');
                $c.removeClass('editing').find('.edit-form').remove();
            }
        });
    });

    $(document).on('click', '.cancel-edit', function(e) {
        e.preventDefault();
        $(this).closest('.inline-edit').removeClass('editing').find('.edit-form').remove();
    });
});
</script>
@include('admin.converted-leads.partials.mentor-flag-inline-scripts')
@endpush