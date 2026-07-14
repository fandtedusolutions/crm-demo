@extends('layouts.mantis')

@section('title', 'Registration Form Submitted Leads')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Registration Form Submitted Leads</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Registration Form Submitted Leads</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

@if(request('search_key'))
<!-- [ Search Results Indicator ] start -->
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <div class="d-flex align-items-center">
        <i class="ti ti-search me-2"></i>
        <div class="flex-grow-1">
            <strong>Search Results:</strong> Showing leads matching "{{ request('search_key') }}"
        </div>
        <a href="{{ route('leads.registration-form-submitted') }}" class="btn btn-sm btn-outline-info">
            <i class="ti ti-x"></i> Clear Search
        </a>
    </div>
</div>
<!-- [ Search Results Indicator ] end -->
@endif

<!-- [ Filter Section ] start -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('leads.registration-form-submitted') }}" id="dateFilterForm">
                    @if(request('registration_status'))
                    <input type="hidden" name="registration_status" value="{{ request('registration_status') }}">
                    @endif
                    <div class="row g-3 align-items-end">
                        <!-- From Date -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control form-control-sm" name="date_from" id="date_from"
                                value="{{ request('date_from', '') }}">
                        </div>

                        <!-- To Date -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control form-control-sm" name="date_to" id="date_to"
                                value="{{ request('date_to', '') }}">
                        </div>

                        <!-- Status -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <label for="filter_lead_status_id" class="form-label">Status</label>
                            <select class="form-select form-select-sm" name="lead_status_id" id="filter_lead_status_id">
                                <option value="">All Statuses</option>
                                @foreach($leadStatuses as $status)
                                <option value="{{ $status->id }}" {{ request('lead_status_id') == $status->id ? 'selected' : '' }}>
                                    {{ $status->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Source -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <label for="filter_lead_source_id" class="form-label">Source</label>
                            <select class="form-select form-select-sm" name="lead_source_id" id="filter_lead_source_id">
                                <option value="">All Sources</option>
                                @foreach($leadSources as $source)
                                <option value="{{ $source->id }}" {{ request('lead_source_id') == $source->id ? 'selected' : '' }}>
                                    {{ $source->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Course -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <label for="course_id" class="form-label">Course</label>
                            <select class="form-select form-select-sm" name="course_id" id="course_id">
                                <option value="">All Courses</option>
                                @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Telecaller -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <label for="telecaller_id" class="form-label">Telecaller</label>
                            <select class="form-select form-select-sm" name="telecaller_id" id="telecaller_id">
                                <option value="">All Telecallers</option>
                                @foreach($telecallers as $telecaller)
                                <option value="{{ $telecaller->id }}" {{ request('telecaller_id') == $telecaller->id ? 'selected' : '' }}>
                                    {{ $telecaller->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Rating -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <label for="rating" class="form-label">Rating</label>
                            <select class="form-select form-select-sm" name="rating" id="rating">
                                <option value="">All Ratings</option>
                                @for($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                                    {{ $i }}/10
                                    </option>
                                    @endfor
                            </select>
                        </div>

                        <!-- Registration Status -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <label for="registration_status" class="form-label">Registration Status</label>
                            <select class="form-select form-select-sm" name="registration_status" id="registration_status">
                                <option value="">All Statuses</option>
                                <option value="approved" {{ request('registration_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('registration_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>

                        <!-- Search -->
                        <div class="col-12 col-md-6 col-lg-4">
                            <label for="search_key" class="form-label">Search</label>
                            <div class="input-group">
                                <input type="text" class="form-control form-control-sm" name="search_key" id="search_key"
                                    placeholder="Search by name, phone, or email..." value="{{ request('search_key') }}">
                                <button class="btn btn-outline-secondary btn-sm" type="submit">
                                    <i class="ti ti-search"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Filter Buttons -->
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="ti ti-filter"></i> Apply Filters
                                </button>
                                <a href="{{ route('leads.registration-form-submitted', request('registration_status') ? ['registration_status' => request('registration_status')] : []) }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="ti ti-refresh"></i> Reset
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

<!-- [ Leads Table ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-file-text me-2"></i>
                        Registration Form Submitted Leads
                        <span class="badge bg-primary ms-2">{{ $leads->count() }}</span>
                    </h5>
                </div>
            </div>
            <div class="card-body">
                <!-- Tabs for Registration Status -->
                <ul class="nav nav-tabs mb-3" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ (!request('registration_status') || request('registration_status') == 'pending') ? 'active' : '' }}"
                            href="{{ route('leads.registration-form-submitted', ['registration_status' => 'pending']) }}">
                            Pending
                            <span class="badge bg-warning ms-1">{{ $pendingCount }}</span>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ request('registration_status') == 'rejected' ? 'active' : '' }}"
                            href="{{ route('leads.registration-form-submitted', ['registration_status' => 'rejected']) }}">
                            Rejected
                            <span class="badge bg-danger ms-1">{{ $rejectedCount }}</span>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ request('registration_status') == 'approved' ? 'active' : '' }}"
                            href="{{ route('leads.registration-form-submitted', ['registration_status' => 'approved']) }}">
                            Approved
                            <span class="badge bg-success ms-1">{{ $approvedCount }}</span>
                        </a>
                    </li>
                </ul>
                @if($leads->count() > 0)
                <!-- Desktop Table View -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive" style="overflow-x: auto;">
                        <table class="table table-hover data_table_basic" id="leadsTable" style="min-width: 1700px;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Actions</th>
                                    @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_general_manager())
                                    <th>Registration Details</th>
                                    @endif
                                    <th>Name</th>
                                    <th>Profile</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Interest</th>
                                    <th>Rating</th>
                                    <th>Source</th>
                                    <th>Course</th>
                                    <th>Telecaller</th>
                                    <th>Place</th>
                                    <th>Followup Date</th>
                                    <th>Last Reason</th>
                                    <th>Remarks</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leads as $index => $lead)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    @php
                                    $canConvertLead = !$lead->is_converted
                                    && $lead->studentDetails
                                    && (strtolower($lead->studentDetails->status ?? '') === 'approved');
                                    $canTriggerConvert = $canConvertLead && !empty($hasLeadActionPermission);
                                    @endphp
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary"
                                                onclick="show_large_modal('{{ route('leads.ajax-show', $lead->id) }}', 'View Lead')"
                                                title="View Lead">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            @if(!empty($canEditLead) && $canEditLead)
                                            <a href="javascript:void(0);" class="btn btn-sm btn-outline-secondary"
                                                onclick="show_ajax_modal('{{ route('leads.ajax-edit', $lead->id) }}', 'Edit Lead')"
                                                title="Edit Lead">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <a href="javascript:void(0);" class="btn btn-sm btn-outline-success"
                                                onclick="show_ajax_modal('{{ route('leads.status-update', $lead->id) }}', 'Update Status')"
                                                title="Update Status">
                                                <i class="ti ti-arrow-up"></i>
                                            </a>
                                            @endif
                                            @if($canTriggerConvert)
                                            <a href="javascript:void(0);" class="btn btn-sm btn-outline-warning"
                                                onclick="show_ajax_modal('{{ route('leads.convert', $lead->id) }}', 'Convert Lead')"
                                                title="Convert Lead">
                                                <i class="ti ti-refresh"></i>
                                            </a>
                                            @endif
                                        </div>
                                        <br>
                                        <hr><br>
                                        <div class="btn-group" role="group">
                                            @if($lead->lead_status_id == 6)
                                            <a href="https://docs.google.com/forms/d/e/1FAIpQLSchtc8xlKUJehZNmzoKTkRvwLwk4-SGjzKSHM2UFToAhgdTlQ/viewform?usp=sf_link"
                                                target="_blank"
                                                class="btn btn-sm btn-outline-info"
                                                title="Demo Conduction Form">
                                                <i class="ti ti-file-text"></i>
                                            </a>
                                            @endif
                                            @if($lead->phone && is_telecaller())
                                            @php
                                            $currentUserId = session('user_id') ?? (\App\Helpers\AuthHelper::getCurrentUserId() ?? 0);
                                            @endphp
                                            @if($currentUserId > 0)
                                            <button class="btn btn-sm btn-outline-success voxbay-call-btn"
                                                data-lead-id="{{ $lead->id }}"
                                                data-telecaller-id="{{ $currentUserId }}"
                                                title="Call Lead">
                                                <i class="ti ti-phone"></i>
                                            </button>
                                            @endif
                                            @endif
                                            <a href="{{ route('leads.call-logs', $lead) }}"
                                                class="btn btn-sm btn-outline-info"
                                                title="View Call Logs">
                                                <i class="ti ti-phone-call"></i>
                                            </a>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin())
                                            <a href="javascript:void(0);" class="btn btn-sm btn-outline-danger"
                                                onclick="delete_modal('{{ route('leads.destroy', $lead->id) }}')"
                                                title="Delete Lead">
                                                <i class="ti ti-trash"></i>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                    @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_general_manager())
                                    <td class="text-center">
                                        @if($lead->studentDetails)
                                        <div class="d-flex flex-column gap-1">
                                            <span class="badge bg-success">Form Submitted</span>
                                            <small class="text-muted">{{ $lead->studentDetails->course->title ?? 'Unknown Course' }}</small>
                                            @php
                                            $docVerificationStatus = $lead->studentDetails->getDocumentVerificationStatus();
                                            @endphp
                                            @if($docVerificationStatus !== null)
                                            <span class="badge {{ $docVerificationStatus === 'verified' ? 'bg-success' : 'bg-warning' }}">
                                                {{ $docVerificationStatus === 'verified' ? 'Documents Verified' : 'Documents Pending' }}
                                            </span>
                                            @endif
                                            @if($lead->studentDetails->status)
                                            <span class="badge 
                                                        @if($lead->studentDetails->status == 'approved') bg-success
                                                        @elseif($lead->studentDetails->status == 'rejected') bg-danger
                                                        @else bg-warning
                                                        @endif">
                                                {{ ucfirst($lead->studentDetails->status) }}
                                            </span>
                                            @php
                                            $hasFinalStatus = in_array($lead->studentDetails->status, ['approved', 'rejected']);
                                            @endphp
                                            @if($hasFinalStatus && $lead->studentDetails->reviewed_at)
                                            <small class="text-muted">{{ ucfirst($lead->studentDetails->status) }} on {{ $lead->studentDetails->reviewed_at->format('M d, Y h:i A') }}</small>
                                            @endif
                                            @endif
                                            <a href="{{ route('leads.registration-details', $lead->id) }}"
                                                class="btn btn-sm btn-outline-primary mt-1"
                                                title="View Registration Details">
                                                <i class="ti ti-eye me-1"></i>View Details
                                            </a>
                                        </div>
                                        @else
                                        @if($lead->course_id == 1)
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('public.lead.nios.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-warning" title="Open National Institute of Open Schooling Registration Form">
                                                <i class="ti ti-external-link me-1"></i>National Institute of Open Schooling Form
                                            </a>
                                        </div>
                                        @elseif($lead->course_id == 2)
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('public.lead.bosse.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-info" title="Open Board of Open Schooling and Skill Education Registration Form">
                                                <i class="ti ti-external-link me-1"></i>Board of Open Schooling and Skill Education Form
                                            </a>
                                        </div>
                                        @elseif($lead->course_id == 3)
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('public.lead.medical-coding.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-success" title="Open Certificate Course in Medical Coding Registration Form">
                                                <i class="ti ti-external-link me-1"></i>Certificate Course in Medical Coding Form
                                            </a>
                                        </div>
                                        @elseif($lead->course_id == 4)
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('public.lead.hotel-management.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Open Hotel Management Registration Form">
                                                <i class="ti ti-external-link me-1"></i>Hotel Management Form
                                            </a>
                                        </div>
                                        @elseif($lead->course_id == 5)
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('public.lead.gmvss.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Open Grameen Mukt Vidhyalayi Shiksha Sansthan Registration Form">
                                                <i class="ti ti-external-link me-1"></i>Grameen Mukt Vidhyalayi Shiksha Sansthan Form
                                            </a>
                                        </div>
                                        @elseif($lead->course_id == 6)
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('public.lead.ai-python.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-dark" title="Open AI Python Registration Form">
                                                <i class="ti ti-external-link me-1"></i>AI Python Form
                                            </a>
                                        </div>
                                        @elseif($lead->course_id == 7)
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('public.lead.digital-marketing.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-warning" title="Open AI Integrated Digital Marketing Registration Form">
                                                <i class="ti ti-external-link me-1"></i>AI Integrated Digital Marketing Form
                                            </a>
                                        </div>
                                        @elseif($lead->course_id == 8)
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('public.lead.diploma-in-data-science.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-info" title="Open Diploma in Data Science Registration Form">
                                                <i class="ti ti-external-link me-1"></i>Diploma in Data Science Form
                                            </a>
                                        </div>
                                        @elseif($lead->course_id == 9)
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('public.lead.web-development.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-success" title="Open Web Development Registration Form">
                                                <i class="ti ti-external-link me-1"></i>Web Development Form
                                            </a>
                                        </div>
                                        @elseif($lead->course_id == 10)
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('public.lead.vibe-coding.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Open Vibe Coding Registration Form">
                                                <i class="ti ti-external-link me-1"></i>Vibe Coding Form
                                            </a>
                                        </div>
                                        @elseif($lead->course_id == 11)
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('public.lead.graphic-designing.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Open Diploma in Graphic Designing Registration Form">
                                                <i class="ti ti-external-link me-1"></i>Diploma in Graphic Designing Form
                                            </a>
                                        </div>
                                        @elseif($lead->course_id == 12)
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('public.lead.eduthanzeel.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-dark" title="Open EduThanzeel Registration Form">
                                                <i class="ti ti-external-link me-1"></i>EduThanzeel Form
                                            </a>
                                        </div>
                                        @elseif($lead->course_id == 13)
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('public.lead.e-school.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-warning" title="Open E-School Registration Form">
                                                <i class="ti ti-external-link me-1"></i>E-School Form
                                            </a>
                                        </div>
                                        @elseif($lead->course_id == 27)
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('public.lead.rpa.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-warning" title="Open RPA Registration Form">
                                                <i class="ti ti-external-link me-1"></i>RPA Form
                                            </a>
                                        </div>
                                        @elseif($lead->course_id == 29)
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('public.lead.ai-sales-marketing.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-warning" title="Open AI-Integrated Sales & Marketing Registration Form">
                                                <i class="ti ti-external-link me-1"></i>AI Sales & Marketing Form
                                            </a>
                                        </div>
                                        @elseif($lead->course_id == 30)
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('public.lead.ai-integrated-video-editing.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-warning" title="Open AI-Integrated Video Editing Registration Form">
                                                <i class="ti ti-external-link me-1"></i>AI Video Editing Form
                                            </a>
                                        </div>
                                        @elseif($lead->course_id == 31)
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('public.lead.ai-integrated-videography.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-warning" title="Open AI-Integrated Videography Registration Form">
                                                <i class="ti ti-external-link me-1"></i>AI Videography Form
                                            </a>
                                        </div>
                                        @elseif($lead->course_id == 32)
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('public.lead.ai-integrated-photography.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-warning" title="Open AI-Integrated Photography Registration Form">
                                                <i class="ti ti-external-link me-1"></i>AI Photography Form
                                            </a>
                                        </div>
                                        @elseif($lead->course_id == 33)
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('public.lead.robo-vibe.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-warning" title="Open Robo Vibe Registration Form">
                                                <i class="ti ti-external-link me-1"></i>Robo Vibe Form
                                            </a>
                                        </div>
                                        @elseif($lead->course_id == 34)
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('public.lead.prompt-engineering.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-warning" title="Open Prompt Engineering Registration Form">
                                                <i class="ti ti-external-link me-1"></i>Prompt Engineering Form
                                            </a>
                                        </div>
                                        @else
                                        <span class="text-muted">No form available</span>
                                        @endif
                                        @endif
                                    </td>
                                    @endif
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avtar avtar-s rounded-circle bg-light-primary me-2 d-flex align-items-center justify-content-center">
                                                <span class="f-14 fw-bold text-primary">{{ strtoupper(substr($lead->title, 0, 1)) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold f-14">{{ $lead->title }}</h6>
                                                <small class="text-muted f-11">#{{ $lead->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($lead->gender)
                                        <span class="badge bg-{{ $lead->gender == 'male' ? 'primary' : ($lead->gender == 'female' ? 'danger' : 'secondary') }}">
                                            {{ ucfirst($lead->gender) }}
                                        </span>
                                        @if($lead->age)
                                        <span class="badge bg-info ms-1">{{ $lead->age }} years</span>
                                        @endif
                                        @else
                                        <span class="text-muted">Not specified</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($lead->phone)
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold">{{ $lead->phone }}</span>
                                            @if($lead->whatsapp)
                                            <small class="text-success">
                                                <i class="ti ti-brand-whatsapp me-1"></i>{{ $lead->whatsapp }}
                                            </small>
                                            @endif
                                        </div>
                                        @else
                                        <span class="text-muted">Not provided</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($lead->email)
                                        <span class="text-primary">{{ $lead->email }}</span>
                                        @else
                                        <span class="text-muted">Not provided</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($lead->leadStatus)
                                        <span class="badge" style="background-color: {{ $lead->leadStatus->color ?? '#6c757d' }}; color: white;">
                                            {{ $lead->leadStatus->title }}
                                        </span>
                                        @else
                                        <span class="badge bg-secondary">No Status</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($lead->interest_status)
                                        <span class="badge bg-{{ $lead->interest_status_color }}">
                                            {{ $lead->interest_status_label }}
                                        </span>
                                        @else
                                        <span class="badge bg-secondary">Not Set</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($lead->rating)
                                        <span class="badge bg-primary">{{ $lead->rating }}/10</span>
                                        @else
                                        <span class="badge bg-secondary">Not Rated</span>
                                        @endif
                                    </td>
                                    <td>{{ $lead->leadSource->title ?? '-' }}</td>
                                    <td>{{ $lead->course->title ?? '-' }}</td>
                                    <td>{{ $lead->telecaller->name ?? 'Unassigned' }}</td>
                                    <td>{{ $lead->place ?? '-' }}</td>
                                    <td>
                                        @if($lead->followup_date)
                                        <span class="badge bg-warning">{{ $lead->followup_date->format('M d, Y') }}</span>
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td>
                                        @if($lead->leadActivities && $lead->leadActivities->count() > 0)
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-primary">{{ $lead->leadActivities->first()->reason }}</span>
                                            <small class="text-muted">{{ $lead->leadActivities->first()->created_at->format('M d, Y H:i') }}</small>
                                        </div>
                                        @else
                                        <span class="text-muted">No activity</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($lead->remarks)
                                        <span class="text-truncate d-inline-block" style="max-width: 150px;" title="{{ $lead->remarks }}">
                                            {{ $lead->remarks }}
                                        </span>
                                        @else
                                        <span class="text-muted">No remarks</span>
                                        @endif
                                    </td>
                                    <td>{{ $lead->created_at->format('M d, Y') }}</td>
                                    <td>{{ $lead->created_at->format('H:i A') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="18" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="ti ti-inbox text-muted" style="font-size: 3rem;"></i>
                                            <h6 class="mt-2 text-muted">No Registration Form Submitted Leads Found</h6>
                                            <p class="text-muted">Try adjusting your filters or search criteria.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="d-lg-none">
                    @forelse($leads as $index => $lead)
                    <div class="card mb-2">
                        <div class="card-body p-3">
                            @php
                            $canConvertLead = !$lead->is_converted
                            && $lead->studentDetails
                            && (strtolower($lead->studentDetails->status ?? '') === 'approved');
                            $canTriggerConvert = $canConvertLead && !empty($hasLeadActionPermission);
                            @endphp
                            <!-- Lead Header -->
                            <div class="d-flex align-items-start justify-content-between mb-2">
                                <div class="d-flex align-items-center flex-grow-1">
                                    <div class="avtar avtar-s rounded-circle bg-light-primary me-2 d-flex align-items-center justify-content-center">
                                        <span class="f-14 fw-bold text-primary">{{ strtoupper(substr($lead->title, 0, 1)) }}</span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fw-bold f-14">{{ $lead->title }}</h6>
                                        <small class="text-muted f-11">#{{ $lead->id }}</small>
                                    </div>
                                </div>
                                <!-- Action buttons in header -->
                                <div class="d-flex gap-1">
                                    <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary"
                                        onclick="show_large_modal('{{ route('leads.ajax-show', $lead->id) }}', 'View Lead')"
                                        title="View Lead">
                                        <i class="ti ti-eye f-12"></i>
                                    </a>
                                    @if(!empty($canEditLead) && $canEditLead)
                                    <a href="javascript:void(0);" class="btn btn-sm btn-outline-secondary"
                                        onclick="show_ajax_modal('{{ route('leads.ajax-edit', $lead->id) }}', 'Edit Lead')"
                                        title="Edit Lead">
                                        <i class="ti ti-edit f-12"></i>
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-sm btn-outline-success"
                                        onclick="show_ajax_modal('{{ route('leads.status-update', $lead->id) }}', 'Update Status')"
                                        title="Update Status">
                                        <i class="ti ti-arrow-up f-12"></i>
                                    </a>
                                    @endif
                                    @if($canTriggerConvert)
                                    <a href="javascript:void(0);" class="btn btn-sm btn-outline-warning"
                                        onclick="show_ajax_modal('{{ route('leads.convert', $lead->id) }}', 'Convert Lead')"
                                        title="Convert Lead">
                                        <i class="ti ti-refresh f-12"></i>
                                    </a>
                                    @endif
                                    @if(!empty($canEditLead) && $canEditLead)
                                    @if(\App\Helpers\RoleHelper::is_admin_or_super_admin())
                                    <a href="javascript:void(0);" class="btn btn-sm btn-outline-danger"
                                        onclick="delete_modal('{{ route('leads.destroy', $lead->id) }}')"
                                        title="Delete Lead">
                                        <i class="ti ti-trash f-12"></i>
                                    </a>
                                    @endif
                                    @endif
                                </div>
                            </div>

                            <!-- Lead Details -->
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <small class="text-muted">Phone:</small>
                                    <div class="fw-bold">{{ $lead->phone ?? 'Not provided' }}</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Email:</small>
                                    <div class="fw-bold">{{ $lead->email ?? 'Not provided' }}</div>
                                </div>
                            </div>

                            <!-- Status and Rating -->
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <small class="text-muted">Status:</small>
                                    <div>
                                        @if($lead->leadStatus)
                                        <span class="badge" style="background-color: {{ $lead->leadStatus->color ?? '#6c757d' }}; color: white;">
                                            {{ $lead->leadStatus->title }}
                                        </span>
                                        @else
                                        <span class="badge bg-secondary">No Status</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Rating:</small>
                                    <div>
                                        @if($lead->rating)
                                        <span class="badge bg-primary">{{ $lead->rating }}/10</span>
                                        @else
                                        <span class="badge bg-secondary">Not Rated</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Course and Telecaller -->
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <small class="text-muted">Course:</small>
                                    <div class="fw-bold">{{ $lead->course->title ?? '-' }}</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Telecaller:</small>
                                    <div class="fw-bold">{{ $lead->telecaller->name ?? 'Unassigned' }}</div>
                                </div>
                            </div>

                            <!-- Registration Form Status -->
                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor())
                            <div class="mb-2">
                                <small class="text-muted">Registration Status:</small>
                                <div>
                                    @if($lead->studentDetails)
                                    <div class="d-flex flex-column gap-1">
                                        <span class="badge bg-success">Form Submitted</span>
                                        @if($lead->studentDetails->status)
                                        <span class="badge 
                                                    @if($lead->studentDetails->status == 'approved') bg-success
                                                    @elseif($lead->studentDetails->status == 'rejected') bg-danger
                                                    @else bg-warning
                                                    @endif">
                                            {{ ucfirst($lead->studentDetails->status) }}
                                        </span>
                                        @php
                                        $hasFinalStatus = in_array($lead->studentDetails->status, ['approved', 'rejected']);
                                        @endphp
                                        @if($hasFinalStatus && $lead->studentDetails->reviewed_at)
                                        <small class="text-muted">{{ ucfirst($lead->studentDetails->status) }} on {{ $lead->studentDetails->reviewed_at->format('M d, Y h:i A') }}</small>
                                        @endif
                                        @endif
                                        <a href="{{ route('leads.registration-details', $lead->id) }}"
                                            class="btn btn-sm btn-outline-primary mt-1"
                                            title="View Registration Details">
                                            <i class="ti ti-eye me-1"></i>View Details
                                        </a>
                                    </div>
                                    @else
                                    <span class="text-muted">No form submitted</span>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <!-- Date and Time -->
                            <div class="row g-2">
                                <div class="col-6">
                                    <small class="text-muted">Date:</small>
                                    <div class="fw-bold">{{ $lead->created_at->format('M d, Y') }}</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Time:</small>
                                    <div class="fw-bold">{{ $lead->created_at->format('H:i A') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="ti ti-inbox text-muted" style="font-size: 3rem;"></i>
                        <h6 class="mt-2 text-muted">No Registration Form Submitted Leads Found</h6>
                        <p class="text-muted">Try adjusting your filters or search criteria.</p>
                    </div>
                    @endforelse
                </div>
                @else
                <div class="text-center py-5">
                    <i class="ti ti-inbox text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">No Registration Form Submitted Leads Found</h5>
                    <p class="text-muted">There are no leads with submitted registration forms matching your criteria.</p>
                    <a href="{{ route('leads.registration-form-submitted') }}" class="btn btn-primary">
                        <i class="ti ti-refresh me-1"></i> Refresh
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- [ Leads Table ] end -->
@endsection

@section('scripts')
<script>
    // Auto-submit form on filter change
    document.addEventListener('DOMContentLoaded', function() {
        const filterForm = document.getElementById('dateFilterForm');
        const filterInputs = filterForm.querySelectorAll('select, input[type="date"]');

        filterInputs.forEach(input => {
            input.addEventListener('change', function() {
                filterForm.submit();
            });
        });
    });
</script>
@endsection