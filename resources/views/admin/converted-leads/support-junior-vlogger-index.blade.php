@extends('layouts.mantis')

@section('title', 'Junior Vlogger - Course Support List')

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
    .inline-edit .edit-form { display: none; }
    .inline-edit.editing .edit-form { display: block; }
    .inline-edit.editing .display-value { display: none !important; }
    .inline-edit.editing .edit-btn { display: none !important; }
    .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    #supportJvTable thead th,
    #supportJvTable tbody td {
        white-space: nowrap;
    }
    #supportJvTable thead th {
        position: sticky;
        top: 0;
        background: #f8f9fa;
        z-index: 1;
        box-shadow: 0 1px 0 #dee2e6;
    }
    #supportJvTable tbody tr:hover {
        background: #fafbff;
    }
    #supportJvTable td .display-value {
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
                    <h5 class="m-b-10">Junior Vlogger - Course Support List</h5>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end align-items-center gap-3">
                    <ul class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.converted-leads.index') }}">Converted Leads</a></li>
                        <li class="breadcrumb-item">Junior Vlogger Support</li>
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
                    <a href="{{ route('admin.support-junior-vlogger-converted-leads.index') }}" class="btn btn-primary active">
                        <i class="ti ti-headphones"></i> Junior Vlogger - Course Support List
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
                <form method="GET" action="{{ route('admin.support-junior-vlogger-converted-leads.index') }}" id="filterForm">
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
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <button type="submit" class="btn btn-primary"><i class="ti ti-search"></i> Filter</button>
                            <a href="{{ route('admin.support-junior-vlogger-converted-leads.index') }}" class="btn btn-secondary"><i class="ti ti-refresh"></i> Clear</a>
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
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Junior Vlogger - Course Support List</h5>
            </div>
            <div class="card-body">
                @php
                    $canEdit = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_support_team();
                    $course = \App\Models\Course::find(25);
                @endphp
                <!-- Desktop Table View -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover data_table_basic" id="supportJvTable">
                            <thead>
                                <tr>
                                <th>SL</th>
                                <th>Academic</th>
                                <th>Support</th>
                                <th>Registration Number</th>
                                <th>Conversion Date</th>
                                <th>B2B Team</th>
                                <th>Full Name</th>
                                <th>Age</th>
                                <th>Primary Mobile</th>
                                <th>WhatsApp Number</th>
                                <th>Alternate Contact Number</th>
                                <th>Academic Batch</th>
                                <th>Batch</th>
                                <th>Class Time</th>
                                <th>Call Status</th>
                                <th>WhatsApp Group Status</th>
                                <th>B2B Partner ID</th>
                                <th>B2B Code</th>
                                <th>Remarks / Support Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($convertedLeads as $index => $lead)
                            @php
                                $jv = $lead->lead ? $lead->lead->juniorVloggerStudentDetails : null;
                                $age = $lead->dob ? \Carbon\Carbon::parse($lead->dob)->age : null;
                            @endphp
                            <tr class="{{ $lead->is_cancelled ? 'cancelled-row' : '' }}">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @include('admin.converted-leads.partials.status-badge', [
                                        'convertedLead' => $lead,
                                        'type' => 'academic',
                                        'showToggle' => \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor(),
                                        'toggleUrl' => (\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor()) ? route('admin.converted-leads.toggle-academic-verify', $lead->id) : null,
                                        'title' => 'academic',
                                        'useModal' => true
                                    ])
                                </td>
                                <td>
                                    @include('admin.converted-leads.partials.status-badge', [
                                        'convertedLead' => $lead,
                                        'type' => 'support',
                                        'showToggle' => $canEdit,
                                        'toggleUrl' => $canEdit ? route('admin.support-converted-leads.toggle-support-verify', $lead->id) : null,
                                        'title' => 'support',
                                        'useModal' => true
                                    ])
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
                                <td>{{ $lead->created_at ? $lead->created_at->format('d-m-Y') : '-' }}</td>
                                <td>{{ $lead->is_b2b == 1 && $lead->lead && $lead->lead->team ? $lead->lead->team->name : ($lead->is_b2b == 1 ? 'B2B' : 'In House') }}</td>
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
                                    @else
                                    {{ \App\Helpers\PhoneNumberHelper::display($lead->code, $lead->phone) }}
                                    @endif
                                </td>
                                <td>
                                    @if($canEdit)
                                    <div class="inline-edit" data-field="whatsapp_number" data-id="{{ $lead->id }}" data-current="{{ $jv?->whatsapp_number }}">
                                        <span class="display-value">{{ $jv && $jv->whatsapp_number ? \App\Helpers\PhoneNumberHelper::display($jv->whatsapp_code, $jv->whatsapp_number) : '-' }}</span>
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
                                    </div>
                                    @else
                                    {{ $jv && $jv->whatsapp_number ? \App\Helpers\PhoneNumberHelper::display($jv->whatsapp_code, $jv->whatsapp_number) : '-' }}
                                    @endif
                                </td>
                                <td>
                                    @if($canEdit)
                                    <div class="inline-edit" data-field="parents_number" data-id="{{ $lead->id }}" data-current="{{ $jv?->parents_number }}">
                                        <span class="display-value">{{ $jv && $jv->parents_number ? \App\Helpers\PhoneNumberHelper::display($jv->parents_code, $jv->parents_number) : '-' }}</span>
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
                                    </div>
                                    @else
                                    {{ $jv && $jv->parents_number ? \App\Helpers\PhoneNumberHelper::display($jv->parents_code, $jv->parents_number) : '-' }}
                                    @endif
                                </td>
                                <td>
                                    @if($canEdit)
                                    <div class="inline-edit" data-field="admission_batch_id" data-id="{{ $lead->id }}" data-batch-id="{{ $lead->batch_id }}" data-current-id="{{ $lead->admission_batch_id }}">
                                        <span class="display-value">{{ $lead->admissionBatch ? $lead->admissionBatch->title : 'N/A' }}</span>
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
                                    </div>
                                    @else
                                    {{ $lead->admissionBatch ? $lead->admissionBatch->title : '-' }}
                                    @endif
                                </td>
                                <td>
                                    @if($canEdit)
                                    <div class="inline-edit" data-field="batch_id" data-id="{{ $lead->id }}" data-course-id="25" data-current-id="{{ $lead->batch_id }}">
                                        <span class="display-value">{{ $lead->batch ? $lead->batch->title : 'N/A' }}</span>
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
                                    </div>
                                    @else
                                    {{ $lead->batch ? $lead->batch->title : '-' }}
                                    @endif
                                </td>
                                <td>
                                    @if($canEdit && $course && $course->needs_time)
                                    <div class="inline-edit" data-field="class_time_id" data-id="{{ $lead->id }}" data-course-id="25" data-programme-type="{{ $jv?->programme_type ?? 'online' }}" data-current-id="{{ $jv?->class_time_id }}">
                                        <span class="display-value">
                                            @if($jv && $jv->classTime)
                                            {{ \Carbon\Carbon::parse($jv->classTime->from_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($jv->classTime->to_time)->format('h:i A') }}
                                            @else - @endif
                                        </span>
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
                                    </div>
                                    @else
                                    @if($jv && $jv->classTime)
                                    {{ \Carbon\Carbon::parse($jv->classTime->from_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($jv->classTime->to_time)->format('h:i A') }}
                                    @else - @endif
                                    @endif
                                </td>
                                <td>
                                    @if($canEdit)
                                    <div class="inline-edit" data-field="call_1" data-id="{{ $lead->id }}" data-field-type="select" data-current="{{ $lead->supportDetails?->call_1 }}">
                                        <span class="display-value">{{ $lead->supportDetails?->call_1 ?? '-' }}</span>
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
                                    </div>
                                    @else
                                    {{ $lead->supportDetails?->call_1 ?? '-' }}
                                    @endif
                                </td>
                                <td>
                                    @if($canEdit)
                                    <div class="inline-edit" data-field="whatsapp_group" data-id="{{ $lead->id }}" data-field-type="select" data-current="{{ $lead->supportDetails?->whatsapp_group }}">
                                        <span class="display-value">{{ $lead->supportDetails?->whatsapp_group ?? '-' }}</span>
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
                                    </div>
                                    @else
                                    {{ $lead->supportDetails?->whatsapp_group ?? '-' }}
                                    @endif
                                </td>
                                <td>{{ $lead->lead && $lead->lead->team && $lead->lead->team->detail ? ($lead->lead->team->detail->b2b_partner_id ?? '-') : '-' }}</td>
                                <td>{{ $lead->lead && $lead->lead->team && $lead->lead->team->detail ? ($lead->lead->team->detail->b2b_code ?? '-') : '-' }}</td>
                                <td>
                                    @if($canEdit)
                                    <div class="inline-edit" data-field="support_notes" data-id="{{ $lead->id }}" data-current="{{ $lead->supportDetails?->support_notes }}">
                                        <span class="display-value">{{ $lead->supportDetails?->support_notes ?? '-' }}</span>
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
                                    </div>
                                    @else
                                    {{ $lead->supportDetails?->support_notes ?? '-' }}
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.support-converted-leads.details', $lead->id) }}" class="btn btn-sm btn-primary" title="Details"><i class="ti ti-eye"></i></a>
                                    <a href="{{ route('admin.invoices.index', $lead->id) }}" class="btn btn-sm btn-success" title="Invoice"><i class="ti ti-receipt"></i></a>
                                    @include('admin.converted-leads.partials.support-whatsapp-mail-buttons', ['convertedLead' => $lead])
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="20" class="text-center">No records found.</td>
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
                        $jv = $lead->lead ? $lead->lead->juniorVloggerStudentDetails : null;
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
                                <a href="{{ route('admin.support-converted-leads.details', $lead->id) }}" class="btn btn-sm btn-primary"><i class="ti ti-eye"></i> Details</a>
                                <a href="{{ route('admin.invoices.index', $lead->id) }}" class="btn btn-sm btn-success"><i class="ti ti-receipt"></i> Invoice</a>
                                @include('admin.converted-leads.partials.support-whatsapp-mail-buttons', ['convertedLead' => $lead])
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

@include('admin.converted-leads.partials.support-whatsapp-mail-layout-includes')

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // DataTable is automatically initialized by layout for tables with 'data_table_basic' class

    var updateUrl = '{{ route("admin.support-junior-vlogger-converted-leads.update-support-details", ":id") }}';

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

    function createInput(currentVal) {
        var v = (currentVal === '-' || !currentVal) ? '' : String(currentVal).replace(/"/g, '&quot;');
        return '<div class="edit-form"><input type="text" class="form-control form-control-sm" value="' + v + '"><div class="btn-group mt-1"><button type="button" class="btn btn-success btn-sm save-edit">Save</button><button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button></div></div>';
    }
    function createSelectCallStatus(currentVal) {
        var opts = ['Call Not Answered','Switched Off','Line Busy','Student Asks to Call Later','Lack of Interest in Conversation','Wrong Contact','Inconsistent Responses','Task Complete'];
        var html = '<option value="">--</option>';
        opts.forEach(function(o) { html += '<option value="' + o + '"' + (currentVal === o ? ' selected' : '') + '>' + o + '</option>'; });
        return '<div class="edit-form"><select class="form-select form-select-sm">' + html + '</select><div class="btn-group mt-1"><button type="button" class="btn btn-success btn-sm save-edit">Save</button><button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button></div></div>';
    }
    function createSelectWhatsappGroup(currentVal) {
        var opts = ['Sent link','Task Completed'];
        var html = '<option value="">--</option>';
        opts.forEach(function(o) { html += '<option value="' + o + '"' + (currentVal === o ? ' selected' : '') + '>' + o + '</option>'; });
        return '<div class="edit-form"><select class="form-select form-select-sm">' + html + '</select><div class="btn-group mt-1"><button type="button" class="btn btn-success btn-sm save-edit">Save</button><button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button></div></div>';
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

    $(document).on('click', '.edit-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $c = $(this).closest('.inline-edit');
        var field = $c.data('field');
        var current = $c.data('current') || '';
        $('.inline-edit').removeClass('editing').find('.edit-form').remove();
        var html = '';
        if (field === 'batch_id') {
            html = createBatchSelect();
            $c.addClass('editing').append(html);
            var courseId = $c.data('course-id') || 25;
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
            var courseId = $c.data('course-id') || 25;
            var programmeType = $c.data('programme-type') || 'online';
            var currentId = $c.data('current-id');
            $.get('/api/class-times/by-course/' + courseId + '?class_type=' + programmeType).done(function(r) {
                var opts = '<option value="">Select</option>';
                if (r && r.length) r.forEach(function(t) {
                    opts += '<option value="' + t.id + '"' + (String(currentId) === String(t.id) ? ' selected' : '') + '>' + (t.from_time + ' - ' + t.to_time) + '</option>';
                });
                $c.find('select').html(opts).focus();
            });
        } else if (field === 'call_1') {
            html = createSelectCallStatus(current);
            $c.addClass('editing').append(html);
            $c.find('select').focus();
        } else if (field === 'whatsapp_group') {
            html = createSelectWhatsappGroup(current);
            $c.addClass('editing').append(html);
            $c.find('select').focus();
        } else {
            html = createInput(current);
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
        $.ajax({
            url: updateUrl.replace(':id', id),
            method: 'POST',
            data: { field: field, value: value, _token: $('meta[name="csrf-token"]').attr('content') },
            success: function(res) {
                if (res.success) {
                    $c.find('.display-value').text(res.value || value || 'N/A');
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
@endpush

