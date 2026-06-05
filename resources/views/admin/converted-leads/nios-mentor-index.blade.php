@extends('layouts.mantis')

@section('title', 'NIOS Converted Mentor List')

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
                    <h5 class="m-b-10">NIOS Converted Mentor List</h5>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end align-items-center gap-3">
                    <ul class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.converted-leads.index') }}">Converted Leads</a></li>
                        <li class="breadcrumb-item">NIOS Converted Mentor List</li>
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
                    <a href="{{ route('admin.junior-vlogger-mentor-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Junior Vlogger Converted Mentor List
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
                    <a href="{{ route('admin.mentor-nios-converted-leads.index') }}" class="btn btn-outline-primary active">
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
                <form method="GET" action="{{ route('admin.mentor-nios-converted-leads.index') }}" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search"
                                value="{{ request('search') }}" placeholder="Name, Phone, Email, Enroll Number">
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
                                @if(request('batch_id'))
                                @php
                                $admissionBatches = \App\Models\AdmissionBatch::where('batch_id', request('batch_id'))->get();
                                @endphp
                                @foreach($admissionBatches as $admissionBatch)
                                <option value="{{ $admissionBatch->id }}" {{ request('admission_batch_id') == $admissionBatch->id ? 'selected' : '' }}>
                                    {{ $admissionBatch->title }}
                                </option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="subject_id" class="form-label">Subject</label>
                            <select class="form-select" id="subject_id" name="subject_id">
                                <option value="">All Subjects</option>
                                @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="registration_status" class="form-label">Registration Status</label>
                            <select class="form-select" id="registration_status" name="registration_status">
                                <option value="">All</option>
                                <option value="Paid" {{ request('registration_status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                                <option value="Not Paid" {{ request('registration_status') == 'Not Paid' ? 'selected' : '' }}>Not Paid</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="student_status" class="form-label">Student Status</label>
                            <select class="form-select" id="student_status" name="student_status">
                                <option value="">All</option>
                                <option value="Low Level" {{ request('student_status') == 'Low Level' ? 'selected' : '' }}>Low Level</option>
                                <option value="Below Medium" {{ request('student_status') == 'Below Medium' ? 'selected' : '' }}>Below Medium</option>
                                <option value="Medium Level" {{ request('student_status') == 'Medium Level' ? 'selected' : '' }}>Medium Level</option>
                                <option value="Advanced Level" {{ request('student_status') == 'Advanced Level' ? 'selected' : '' }}>Advanced Level</option>
                            </select>
                        </div>
                        @include('admin.converted-leads.partials.mentor-flag-filter-field')
                        
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="date_from" class="form-label">Date From</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="date_to" class="form-label">Date To</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-search"></i> <span class="d-none d-sm-inline">Filter</span>
                                </button>
                                <a href="{{ route('admin.mentor-nios-converted-leads.index') }}" class="btn btn-secondary">
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
        <div class="card" data-mentor-update-url="{{ route('admin.mentor-nios-converted-leads.update-mentor-details', ['id' => '__ID__']) }}">
            <div class="card-header">
                <h5 class="mb-0">NIOS Converted Mentor List</h5>
            </div>
            <div class="card-body">
                <!-- Desktop Table View -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover data_table_basic" id="niosMentorTable">
                            <thead>
                                <tr>
                                    <th>SL No</th>
                                    <th>Converted Date</th>
                                    <th>Academic Verified At</th>
                                    <th>Support Verified At</th>
                                    <th>Registration Number</th>
                                    <th>Flag</th>
                                    <th>Call Time</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>DOB</th>
                                    <th>REG. FEE</th>
                                    <th>EXAM FEE</th>
                                    <th>Phone</th>
                                    <th>WhatsApp</th>
                                    @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                    <th>Parent Phone</th>
                                    @endif
                                    <th>Subject</th>
                                    <th>Batch</th>
                                    <th>Admission Batch</th>
                                    <th>Technology Side</th>
                                    <th>Student Status</th>
                                    <th>CALL - 1</th>
                                    <th>APP</th>
                                    <th>WhatsApp Group</th>
                                    <th>Telegram Group</th>
                                    <th>Problems</th>
                                    <th>Call - 2</th>
                                    <th>Mentor Live 1</th>
                                    <th>FIRST LIVE</th>
                                    <th>FIRST EXAM</th>
                                    <th>CALL - 3</th>
                                    <th>Mentor Live 2</th>
                                    <th>CALL - 4</th>
                                    <th>SECOND LIVE</th>
                                    <th>Second Exam</th>
                                    <th>Call - 5</th>
                                    <th>Mentor Live 3</th>
                                    <th>Assignment</th>
                                    <th>EXAM FEES</th>
                                    <th>CALL - 6</th>
                                    <th>PCP CLASS</th>
                                    <th>CALL - 7</th>
                                    <th>Practical Record</th>
                                    <th>CALL - 8</th>
                                    <th>Mentor Live 4</th>
                                    <th>Model Exam Live</th>
                                    <th>Model Exam</th>
                                    <th>I D CARD</th>
                                    <th>Practical Hall Ticket</th>
                                    <th>CALL - 9</th>
                                    <th>Particle Exam</th>
                                    <th>Theory Hall Ticket</th>
                                    <th>Call - 10</th>
                                    <th>Subject -1</th>
                                    <th>Subject -2</th>
                                    <th>Subject -3</th>
                                    <th>Subject -4</th>
                                    <th>Subject -5</th>
                                    <th>Subject -6</th>
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
                                    <td>{{ $convertedLead->status ?? 'N/A' }}</td>
                                    <td>{{ $convertedLead->exam_fee ?? 'N/A' }}</td>
                                    <td>{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</td>
                                    <td>
                                        @if($convertedLead->leadDetail && $convertedLead->leadDetail->whatsapp_number)
                                            {{ \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->whatsapp_code, $convertedLead->leadDetail->whatsapp_number) }}
                                        @else
                                            <span class="text-muted">-</span>
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
                                    <td>
                                        <div class="inline-edit" data-field="subject_id" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->subject_id }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->subject?->title ?? $convertedLead->subject?->title ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $convertedLead->batch ? $convertedLead->batch->title : 'N/A' }}</td>
                                    <td>{{ $convertedLead->admissionBatch ? $convertedLead->admissionBatch->title : 'N/A' }}</td>
                                    <td>
                                        <div class="inline-edit" data-field="technology_side" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->technology_side }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->technology_side ?? '-' }}</span>
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
                                    <!-- Continue with all the other fields... -->
                                    <td>
                                        <div class="inline-edit" data-field="call_1" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->call_1 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->call_1 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="app" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->app }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->app ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="whatsapp_group" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->whatsapp_group }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->whatsapp_group ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="telegram_group" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->telegram_group }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->telegram_group ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="problems" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->problems }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->problems ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <!-- Continue with remaining fields... -->
                                    <td>
                                        <div class="inline-edit" data-field="call_2" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->call_2 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->call_2 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="mentor_live_1" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->mentor_live_1 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->mentor_live_1 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="first_live" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->first_live }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->first_live ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="first_exam" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->first_exam }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->first_exam ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="call_3" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->call_3 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->call_3 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="mentor_live_2" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->mentor_live_2 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->mentor_live_2 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="call_4" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->call_4 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->call_4 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="second_live" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->second_live }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->second_live ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="second_exam" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->second_exam }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->second_exam ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="call_5" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->call_5 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->call_5 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="mentor_live_3" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->mentor_live_3 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->mentor_live_3 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="assignment" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->assignment }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->assignment ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <div class="inline-edit" data-field="exam_fees" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->exam_fees }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->exam_fees ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="call_6" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->call_6 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->call_6 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="pcp_class" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->pcp_class }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->pcp_class ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="call_7" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->call_7 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->call_7 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="practical_record" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->practical_record }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->practical_record ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="call_8" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->call_8 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->call_8 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="mentor_live_4" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->mentor_live_4 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->mentor_live_4 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="model_exam_live" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->model_exam_live }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->model_exam_live ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="model_exam" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->model_exam }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->model_exam ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="id_card" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->id_card }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->id_card ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="practical_hall_ticket" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->practical_hall_ticket }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->practical_hall_ticket ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="call_9" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->call_9 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->call_9 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="particle_exam" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->particle_exam }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->particle_exam ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="theory_hall_ticket" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->theory_hall_ticket }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->theory_hall_ticket ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="call_10" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->call_10 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->call_10 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="exam_subject_1" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->exam_subject_1 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->exam_subject_1 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="exam_subject_2" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->exam_subject_2 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->exam_subject_2 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="exam_subject_3" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->exam_subject_3 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->exam_subject_3 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="exam_subject_4" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->exam_subject_4 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->exam_subject_4 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="exam_subject_5" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->exam_subject_5 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->exam_subject_5 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="exam_subject_6" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->exam_subject_6 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->exam_subject_6 ?? '-' }}</span>
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
                                    <td colspan="51" class="text-center">No converted leads found for NIOS mentoring</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="d-lg-none">
                    <div class="row" id="mobileCards">
                        @forelse($convertedLeads as $index => $convertedLead)
                        <div class="col-12 mb-3">
                            <div class="card {{ $convertedLead->is_cancelled ? 'cancelled-card' : '' }}">
                                <div class="card-header">
                                    <h6 class="mb-0">{{ $convertedLead->name }}</h6>
                                    @if($convertedLead->is_cancelled)
                                    <span class="badge bg-danger ms-2">Cancelled</span>
                                    @endif
                                </div>
                                <div class="card-body">
                                    @php
                                    $academicVerifiedAtMobile = $convertedLead->academic_verified_at
                                    ? $convertedLead->academic_verified_at->copy()->timezone($appTimezone)->format('d-m-Y h:i A')
                                    : null;
                                    $supportVerifiedAtMobile = $convertedLead->support_verified_at
                                    ? $convertedLead->support_verified_at->copy()->timezone($appTimezone)->format('d-m-Y h:i A')
                                    : null;
                                    @endphp
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
                                            <small class="text-muted d-block">REG. FEE</small>
                                            <span class="fw-medium">{{ $convertedLead->reg_fee ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">EXAM FEE</small>
                                            <span class="fw-medium">{{ $convertedLead->exam_fee ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Technology Side</small>
                                            <span class="fw-medium">{{ $convertedLead->mentorDetails?->technology_side ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Student Status</small>
                                            <span class="fw-medium">{{ $convertedLead->mentorDetails?->student_status ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Subject</small>
                                            <span class="fw-medium">{{ $convertedLead->mentorDetails?->subject?->title ?? $convertedLead->subject?->title ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Batch</small>
                                            <span class="fw-medium">{{ $convertedLead->batch ? $convertedLead->batch->title : 'N/A' }}</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Academic Verified At</small>
                                            <span class="fw-medium">{{ $academicVerifiedAtMobile ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Support Verified At</small>
                                            <span class="fw-medium">{{ $supportVerifiedAtMobile ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="text-center py-4">
                                <p class="text-muted">No converted leads found for NIOS mentoring</p>
                            </div>
                        </div>
                        @endforelse
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
</style>
@endpush

@push('scripts')
@include('admin.converted-leads.partials.placement-modal-reopen-script')
<script>
    $(document).ready(function() {
        // Dependent filters: admission batches by batch
        function loadAdmissionBatchesByBatch(batchId, selectedId) {
            const $admission = $('#admission_batch_id');
            $admission.html('<option value="">Loading...</option>');
            if (!batchId) {
                $admission.html('<option value="">All Admission Batches</option>');
                return;
            }
            $.get(`/api/admission-batches/by-batch/${batchId}`).done(function(list) {
                let opts = '<option value=\"\">All Admission Batches</option>';
                list.forEach(function(i) {
                    const sel = String(selectedId) === String(i.id) ? 'selected' : '';
                    opts += `<option value="${i.id}" ${sel}>${i.title}</option>`;
                });
                $admission.html(opts);
            }).fail(function() {
                $admission.html('<option value="">All Admission Batches</option>');
            });
        }

        loadAdmissionBatchesByBatch($('#batch_id').val(), $('#admission_batch_id').data('selected'));
        $('#batch_id').on('change', function() {
            loadAdmissionBatchesByBatch($(this).val(), '');
        });

        // Inline editing aligned with BOSSE mentor page
        $(document).on('click', '.edit-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const container = $(this).closest('.inline-edit');
            const field = container.data('field');
            const id = container.data('id');
            const currentValue = container.data('current') !== undefined ? String(container.data('current')).trim() : container.find('.display-value').text().trim();

            if (container.hasClass('editing')) return;

            $('.inline-edit.editing').not(container).each(function() {
                $(this).removeClass('editing');
                $(this).find('.edit-form').remove();
            });

            let editForm = '';
            if (field === 'subject_id') {
                editForm = createSubjectField(field, currentValue);
            } else if (field === 'problems') {
                editForm = createTextareaField(field, currentValue);
            } else if (['registration_status', 'technology_side', 'student_status', 'call_1', 'call_2', 'call_3', 'call_4', 'call_5', 'call_6', 'call_7', 'call_8', 'call_9', 'call_10', 'app', 'whatsapp_group', 'telegram_group', 'mentor_live_1', 'mentor_live_2', 'mentor_live_3', 'mentor_live_4', 'mentor_live_5', 'first_live', 'first_exam', 'second_live', 'second_exam', 'model_exam_live', 'model_exam', 'assignment', 'exam_fees', 'pcp_class', 'id_card', 'practical_hall_ticket', 'particle_exam', 'theory_hall_ticket', 'practical_record', 'admit_card', 'exam_subject_1', 'exam_subject_2', 'exam_subject_3', 'exam_subject_4', 'exam_subject_5', 'exam_subject_6'].includes(field)) {
                editForm = createSelectField(field, currentValue);
            } else {
                editForm = createInputField(field, currentValue);
            }

            container.addClass('editing');
            container.append(editForm);

            if (field === 'subject_id') {
                const $select = container.find('select');
                loadSubjectsForEdit($select, currentValue);
            }

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
                url: `/admin/mentor-nios-converted-leads/${id}/update-mentor-details`,
                method: 'POST',
                data: {
                    field: field,
                    value: value,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        container.find('.display-value').text(response.value || value || '-');
                        container.data('current', response.value || value);
                        if (typeof toast_success === 'function') toast_success(response.message || 'Updated successfully');
                    } else {
                        if (typeof toast_error === 'function') toast_error(response.error || 'Update failed');
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
                    if (typeof toast_error === 'function') toast_error(errorMessage);
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

        // Helpers
        function createInputField(field, currentValue) {
            const displayValue = currentValue === '-' ? '' : currentValue;
            const inputType = field === 'call_time' ? 'time' : 'text';
            return `
            <div class=\"edit-form\">
                <input type=\"${inputType}\" value=\"${displayValue}\" class=\"form-control form-control-sm\" autocomplete=\"off\" autocapitalize=\"off\" spellcheck=\"false\">
                <div class=\"btn-group mt-1\">
                    <button type=\"button\" class=\"btn btn-success btn-sm save-edit\">Save</button>
                    <button type=\"button\" class=\"btn btn-secondary btn-sm cancel-edit\">Cancel</button>
                </div>
            </div>
        `;
        }

        function createTextareaField(field, currentValue) {
            const displayValue = currentValue === '-' ? '' : currentValue;
            return `
            <div class=\"edit-form\">
                <textarea rows=\"3\" class=\"form-control form-control-sm\" autocomplete=\"off\" autocapitalize=\"off\" spellcheck=\"false\">${displayValue}</textarea>
                <div class=\"btn-group mt-1\">
                    <button type=\"button\" class=\"btn btn-success btn-sm save-edit\">Save</button>
                    <button type=\"button\" class=\"btn btn-secondary btn-sm cancel-edit\">Cancel</button>
                </div>
            </div>
        `;
        }

        function createSubjectField(field, currentValue) {
            return `
            <div class=\"edit-form\">
                <select class=\"form-select form-select-sm\">
                    <option value=\"\">Select Subject</option>
                </select>
                <div class=\"btn-group mt-1\">
                    <button type=\"button\" class=\"btn btn-success btn-sm save-edit\">Save</button>
                    <button type=\"button\" class=\"btn btn-secondary btn-sm cancel-edit\">Cancel</button>
                </div>
            </div>
        `;
        }

        function loadSubjectsForEdit($select, currentValue) {
            $.get('/api/subjects/by-course/1').done(function(list) {
                let options = '<option value=\"\">Select Subject</option>';
                list.forEach(function(item) {
                    const selected = String(currentValue) === String(item.id) ? 'selected' : '';
                    options += `<option value="${item.id}" ${selected}>${item.title}</option>`;
                });
                $select.html(options);
            }).fail(function() {
                $select.html('<option value="">Error loading subjects</option>');
            });
        }

        function createSelectField(field, currentValue) {
            let options = '';

            if (field === 'registration_status') {
                options = `
                <option value=\"\">Select Registration Status</option>
                <option value=\"Paid\" ${currentValue === 'Paid' ? 'selected' : ''}>Paid</option>
                <option value=\"Not Paid\" ${currentValue === 'Not Paid' ? 'selected' : ''}>Not Paid</option>
            `;
            } else if (field === 'technology_side') {
                options = `
                <option value=\"\">Select Technology Side</option>
                <option value=\"No Knowledge\" ${currentValue === 'No Knowledge' ? 'selected' : ''}>No Knowledge</option>
                <option value=\"Limited Knowledge\" ${currentValue === 'Limited Knowledge' ? 'selected' : ''}>Limited Knowledge</option>
                <option value=\"Moderate Knowledge\" ${currentValue === 'Moderate Knowledge' ? 'selected' : ''}>Moderate Knowledge</option>
                <option value=\"High Knowledge\" ${currentValue === 'High Knowledge' ? 'selected' : ''}>High Knowledge</option>
            `;
            } else if (field === 'student_status') {
                options = `
                <option value=\"\">Select Student Status</option>
                <option value=\"Low Level\" ${currentValue === 'Low Level' ? 'selected' : ''}>Low Level</option>
                <option value=\"Below Medium\" ${currentValue === 'Below Medium' ? 'selected' : ''}>Below Medium</option>
                <option value=\"Medium Level\" ${currentValue === 'Medium Level' ? 'selected' : ''}>Medium Level</option>
                <option value=\"Advanced Level\" ${currentValue === 'Advanced Level' ? 'selected' : ''}>Advanced Level</option>
            `;
            } else if (['call_1', 'call_2', 'call_3', 'call_4', 'call_5', 'call_6', 'call_7', 'call_8', 'call_9', 'call_10'].includes(field)) {
                options = `
                <option value=\"\">Select Call Status</option>
                <option value=\"Call Not Answered\" ${currentValue === 'Call Not Answered' ? 'selected' : ''}>Call Not Answered</option>
                <option value=\"Switched Off\" ${currentValue === 'Switched Off' ? 'selected' : ''}>Switched Off</option>
                <option value=\"Line Busy\" ${currentValue === 'Line Busy' ? 'selected' : ''}>Line Busy</option>
                <option value=\"Student Asks to Call Later\" ${currentValue === 'Student Asks to Call Later' ? 'selected' : ''}>Student Asks to Call Later</option>
                <option value=\"Lack of Interest in Conversation\" ${currentValue === 'Lack of Interest in Conversation' ? 'selected' : ''}>Lack of Interest in Conversation</option>
                <option value=\"Wrong Contact\" ${currentValue === 'Wrong Contact' ? 'selected' : ''}>Wrong Contact</option>
                <option value=\"Inconsistent Responses\" ${currentValue === 'Inconsistent Responses' ? 'selected' : ''}>Inconsistent Responses</option>
                <option value=\"Task Complete\" ${currentValue === 'Task Complete' ? 'selected' : ''}>Task Complete</option>
            `;
            } else if (field === 'app') {
                options = `
                <option value=\"\">Select APP Status</option>
                <option value=\"Provided app\" ${currentValue === 'Provided app' ? 'selected' : ''}>Provided app</option>
                <option value=\"OTP Problem\" ${currentValue === 'OTP Problem' ? 'selected' : ''}>OTP Problem</option>
                <option value=\"Task Completed\" ${currentValue === 'Task Completed' ? 'selected' : ''}>Task Completed</option>
                <option value=\"Not Respond\" ${currentValue === 'Not Respond' ? 'selected' : ''}>Not Respond</option>
            `;
            } else if (field === 'whatsapp_group') {
                options = `
                <option value=\"\">Select WhatsApp Group Status</option>
                <option value=\"Sent link\" ${currentValue === 'Sent link' ? 'selected' : ''}>Sent link</option>
                <option value=\"Task Completed\" ${currentValue === 'Task Completed' ? 'selected' : ''}>Task Completed</option>
                <option value=\"Not Responding\" ${currentValue === 'Not Responding' ? 'selected' : ''}>Not Responding</option>
                <option value=\"Task Complete\" ${currentValue === 'Task Complete' ? 'selected' : ''}>Task Complete</option>
            `;
            } else if (field === 'telegram_group') {
                options = `
                <option value=\"\">Select Telegram Group Status</option>
                <option value=\"Sent link\" ${currentValue === 'Sent link' ? 'selected' : ''}>Sent link</option>
                <option value=\"task complete\" ${currentValue === 'task complete' ? 'selected' : ''}>Task complete</option>
            `;
            } else if (field === 'first_live' || field === 'second_live' || field === 'model_exam_live') {
                options = `
                <option value=\"\">Select Live Status</option>
                <option value=\"Not Respond\" ${currentValue === 'Not Respond' ? 'selected' : ''}>Not Respond</option>
                <option value=\"1 subject attend\" ${currentValue === '1 subject attend' ? 'selected' : ''}>1 subject attend</option>
                <option value=\"2 subject attend\" ${currentValue === '2 subject attend' ? 'selected' : ''}>2 subject attend</option>
                <option value=\"3 subject attend\" ${currentValue === '3 subject attend' ? 'selected' : ''}>3 subject attend</option>
                <option value=\"4 subject attend\" ${currentValue === '4 subject attend' ? 'selected' : ''}>4 subject attend</option>
                <option value=\"5 subject attend\" ${currentValue === '5 subject attend' ? 'selected' : ''}>5 subject attend</option>
                <option value=\"6 subject attend\" ${currentValue === '6 subject attend' ? 'selected' : ''}>6 subject attend</option>
                <option value=\"Task complete\" ${currentValue === 'Task complete' ? 'selected' : ''}>Task complete</option>
            `;
            } else if (field === 'first_exam' || field === 'second_exam' || field === 'model_exam' || field === 'assignment') {
                options = `
                <option value=\"\">Select Exam Status</option>
                <option value=\"not respond\" ${currentValue === 'not respond' ? 'selected' : ''}>not respond</option>
                <option value=\"1 subject attend\" ${currentValue === '1 subject attend' ? 'selected' : ''}>1 subject attend</option>
                <option value=\"2 subject attend\" ${currentValue === '2 subject attend' ? 'selected' : ''}>2 subject attend</option>
                <option value=\"3 subject attend\" ${currentValue === '3 subject attend' ? 'selected' : ''}>3 subject attend</option>
                <option value=\"4 subject attend\" ${currentValue === '4 subject attend' ? 'selected' : ''}>4 subject attend</option>
                <option value=\"5 subject attend\" ${currentValue === '5 subject attend' ? 'selected' : ''}>5 subject attend</option>
                <option value=\"6 subject attend\" ${currentValue === '6 subject attend' ? 'selected' : ''}>6 subject attend</option>
                <option value=\"task complete\" ${currentValue === 'task complete' ? 'selected' : ''}>task complete</option>
            `;
            } else if (field === 'exam_fees' || field === 'pcp_class') {
                options = `
                <option value=\"\">Select Status</option>
                <option value=\"Not Respond\" ${currentValue === 'Not Respond' ? 'selected' : ''}>Not Respond</option>
                <option value=\"Task Complete\" ${currentValue === 'Task Complete' ? 'selected' : ''}>Task Complete</option>
            `;
            } else if (['mentor_live_1', 'mentor_live_2', 'mentor_live_3', 'mentor_live_4', 'mentor_live_5'].includes(field)) {
                options = `
                <option value=\"\">Select Mentor Live Status</option>
                <option value=\"Not Respond\" ${currentValue === 'Not Respond' ? 'selected' : ''}>Not Respond</option>
                <option value=\"Task Complete\" ${currentValue === 'Task Complete' ? 'selected' : ''}>Task Complete</option>
            `;
            } else if (['first_exam', 'second_exam', 'model_exam', 'particle_exam'].includes(field)) {
                options = `
                <option value=\"\">Select Exam Status</option>
                <option value=\"Did not log in on time\" ${currentValue === 'Did not log in on time' ? 'selected' : ''}>Did not log in on time</option>
                <option value=\"missed the exam\" ${currentValue === 'missed the exam' ? 'selected' : ''}>missed the exam</option>
                <option value=\"technical issue\" ${currentValue === 'technical issue' ? 'selected' : ''}>technical issue</option>
                <option value=\"task complete\" ${currentValue === 'task complete' ? 'selected' : ''}>task complete</option>
            `;
            } else if (field === 'practical_record') {
                options = `
                <option value=\"\">Select Practical Record Status</option>
                <option value=\"Not Respond\" ${currentValue === 'Not Respond' ? 'selected' : ''}>Not Respond</option>
                <option value=\"1 Subject Attend\" ${currentValue === '1 Subject Attend' ? 'selected' : ''}>1 Subject Attend</option>
                <option value=\"2 Subject Attend\" ${currentValue === '2 Subject Attend' ? 'selected' : ''}>2 Subject Attend</option>
                <option value=\"3 Subject Attend\" ${currentValue === '3 Subject Attend' ? 'selected' : ''}>3 Subject Attend</option>
                <option value=\"4 Subject Attend\" ${currentValue === '4 Subject Attend' ? 'selected' : ''}>4 Subject Attend</option>
                <option value=\"5 Subject Attend\" ${currentValue === '5 Subject Attend' ? 'selected' : ''}>5 Subject Attend</option>
                <option value=\"6 Subject Attend\" ${currentValue === '6 Subject Attend' ? 'selected' : ''}>6 Subject Attend</option>
                <option value=\"Task Complete\" ${currentValue === 'Task Complete' ? 'selected' : ''}>Task Complete</option>
            `;
            } else if (['id_card', 'practical_hall_ticket', 'theory_hall_ticket', 'admit_card'].includes(field)) {
                options = `
                <option value=\"\">Select Status</option>
                <option value=\"Did Not\" ${currentValue === 'Did Not' ? 'selected' : ''}>Did Not</option>
                <option value=\"Task Complete\" ${currentValue === 'Task Complete' ? 'selected' : ''}>Task Complete</option>
            `;
            } else if (['exam_subject_1', 'exam_subject_2', 'exam_subject_3', 'exam_subject_4', 'exam_subject_5', 'exam_subject_6'].includes(field)) {
                options = `
                <option value=\"\">Select Exam Subject Status</option>
                <option value=\"Did not log in on time\" ${currentValue === 'Did not log in on time' ? 'selected' : ''}>Did not log in on time</option>
                <option value=\"missed the exam\" ${currentValue === 'missed the exam' ? 'selected' : ''}>missed the exam</option>
                <option value=\"technical issue\" ${currentValue === 'technical issue' ? 'selected' : ''}>technical issue</option>
                <option value=\"task complete\" ${currentValue === 'task complete' ? 'selected' : ''}>task complete</option>
            `;
            }

            return `
            <div class=\"edit-form\">
                <select class=\"form-select form-select-sm\">${options}</select>
                <div class=\"btn-group mt-1\">
                    <button type=\"button\" class=\"btn btn-success btn-sm save-edit\">Save</button>
                    <button type=\"button\" class=\"btn btn-secondary btn-sm cancel-edit\">Cancel</button>
                </div>
            </div>
        `;
        }
    });
</script>
@include('admin.converted-leads.partials.mentor-flag-inline-scripts')
@endpush