@extends('layouts.mantis')

@section('title', 'View Converted Lead')

@section('content')
@php
    $listRoute = $listRoute ?? route('admin.converted-leads.index');
    $pdfRoute = $pdfRoute ?? route('admin.converted-leads.details-pdf', $convertedLead->id);
    $canInlineEditPersonal = \App\Helpers\RoleHelper::is_admin_or_super_admin()
        || \App\Helpers\RoleHelper::is_admission_counsellor()
        || \App\Helpers\RoleHelper::is_academic_assistant();
    $leadDetail = $convertedLead->leadDetail;
    $personalDobRaw = $convertedLead->dob
        ? (strtotime($convertedLead->dob) ? date('Y-m-d', strtotime($convertedLead->dob)) : $convertedLead->dob)
        : '';
    $personalDobDisplay = $personalDobRaw ? date('d-m-Y', strtotime($personalDobRaw)) : 'N/A';
    $leadDetailDobRaw = ($leadDetail && $leadDetail->date_of_birth)
        ? $leadDetail->date_of_birth->format('Y-m-d')
        : '';
    $leadDetailDobDisplay = ($leadDetail && $leadDetail->date_of_birth)
        ? $leadDetail->date_of_birth->format('d M Y')
        : 'N/A';
    $__fileMeta = $fileExistenceMeta ?? [];
    $fileExistsOnDisk = function (?string $p) use ($__fileMeta) {
        if (!$p) {
            return false;
        }
        if (array_key_exists($p, $__fileMeta)) {
            return (bool) $__fileMeta[$p];
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->exists($p);
    };
@endphp
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Converted Lead Details</h5>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end align-items-center gap-3">
                    <ul class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ $listRoute }}">Converted Leads</a></li>
                        <li class="breadcrumb-item">View</li>
                    </ul>
                    <a href="{{ $pdfRoute }}" target="_blank" class="btn btn-outline-primary">
                        <i class="ti ti-file-type-pdf"></i> Download PDF
                    </a>
                    <a href="{{ $listRoute }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-12">
        <div class="card {{ $convertedLead->is_cancelled ? 'cancelled-card' : '' }}">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0 d-flex align-items-center gap-2"><i class="ti ti-user-check text-primary"></i> Converted Lead Information</h5>
                <div class="d-flex align-items-center gap-2">
                    @if($convertedLead->is_cancelled)
                        <div>
                            <span class="badge bg-danger">Cancelled</span>
                            @if($convertedLead->cancelledBy)
                                <br><small class="text-muted">By: {{ $convertedLead->cancelledBy->name }}
                                @if($convertedLead->cancelled_at)
                                    ({{ $convertedLead->cancelled_at->format('d-m-Y h:i A') }})
                                @endif
                                </small>
                            @endif
                        </div>
                    @endif
                    <span class="badge bg-light-primary text-primary">ID #{{ $convertedLead->id }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Personal Information -->
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3 d-flex align-items-center gap-2"><i class="ti ti-address-book"></i> Personal Information</h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avtar avtar-s rounded-circle bg-light-success me-2" style="width: 60px; height: 60px;">
                                        <span class="text-info fw-bold js-cl-show-name-initial" style="font-size: 1.5rem;">{{ strtoupper(substr($convertedLead->name, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        @if($canInlineEditPersonal)
                                            <div class="inline-edit-show-cl d-inline-block" data-field="name" data-id="{{ $convertedLead->id }}" data-type="text" data-current="{{ e($convertedLead->name) }}">
                                                <h4 class="mb-1 d-inline js-cl-show-name-heading"><span class="display-value">{{ $convertedLead->name }}</span></h4>
                                                <button type="button" class="btn btn-sm btn-link p-0 ms-1 edit-btn-show-cl" title="Edit Name">
                                                    <i class="ti ti-pencil"></i>
                                                </button>
                                            </div>
                                        @else
                                            <h4 class="mb-1 js-cl-show-name-heading">{{ $convertedLead->name }}</h4>
                                        @endif
                                        <p class="text-muted mb-0">Converted Lead</p>
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
                            </div>
                            @include('admin.converted-leads.partials.show-inline-field', [
                                'label' => 'Phone',
                                'field' => 'phone',
                                'type' => 'phone',
                                'displayValue' => \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone),
                                'rawValue' => $convertedLead->phone ?? '',
                                'code' => $convertedLead->code ?? '',
                                'codeField' => 'code',
                                'canEdit' => $canInlineEditPersonal,
                                'convertedLeadId' => $convertedLead->id,
                                'col' => 6,
                            ])
                            @include('admin.converted-leads.partials.show-inline-field', [
                                'label' => 'Email',
                                'field' => 'email',
                                'type' => 'email',
                                'displayValue' => $convertedLead->email ?? 'N/A',
                                'rawValue' => $convertedLead->email ?? '',
                                'canEdit' => $canInlineEditPersonal,
                                'convertedLeadId' => $convertedLead->id,
                                'col' => 6,
                            ])
                            @include('admin.converted-leads.partials.show-inline-field', [
                                'label' => 'Register Number',
                                'field' => 'register_number',
                                'type' => 'text',
                                'displayValue' => $convertedLead->register_number ?? 'N/A',
                                'rawValue' => $convertedLead->register_number ?? '',
                                'canEdit' => $canInlineEditPersonal,
                                'convertedLeadId' => $convertedLead->id,
                                'col' => 6,
                            ])
                            @include('admin.converted-leads.partials.show-inline-field', [
                                'label' => 'DOB',
                                'field' => 'dob',
                                'type' => 'date',
                                'displayValue' => $personalDobDisplay,
                                'rawValue' => $personalDobRaw,
                                'canEdit' => $canInlineEditPersonal,
                                'convertedLeadId' => $convertedLead->id,
                                'col' => 6,
                            ])
                            @include('admin.converted-leads.partials.show-inline-field', [
                                'label' => 'Remarks',
                                'field' => 'remarks',
                                'type' => 'textarea',
                                'displayValue' => $convertedLead->remarks ?? 'N/A',
                                'rawValue' => $convertedLead->remarks ?? '',
                                'canEdit' => $canInlineEditPersonal,
                                'convertedLeadId' => $convertedLead->id,
                                'col' => 6,
                            ])
                            <div class="col-6">
                                <label class="form-label text-muted">Lead Type</label>
                                <p class="fw-bold">{{ $convertedLead->is_b2b == 1 ? ('B2B' . ($convertedLead->lead?->team?->name ? ' (' . $convertedLead->lead->team->name . ')' : '')) : 'In House' }}</p>
                            </div>
                            @if($convertedLead->post_sales_remarks)
                            <div class="col-12">
                                <label class="form-label text-muted">Post Sales Remarks</label>
                                <p class="fw-bold">{{ $convertedLead->post_sales_remarks }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Academic Information -->
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3 d-flex align-items-center gap-2"><i class="ti ti-school"></i> Academic Information</h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label text-muted">Course</label>
                                <p class="fw-bold">{{ $convertedLead->course ? $convertedLead->course->title : 'N/A' }}</p>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted">Batch</label>
                                <p class="fw-bold">{{ $convertedLead->batch ? $convertedLead->batch->title : 'N/A' }}</p>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted">Admission Batch</label>
                                <p class="fw-bold">{{ $convertedLead->admissionBatch ? $convertedLead->admissionBatch->title : 'N/A' }}</p>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted">Subject</label>
                                <p class="fw-bold">{{ $convertedLead->subject ? $convertedLead->subject->title : 'N/A' }}</p>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted">Academic Assistant</label>
                                <p class="fw-bold">{{ $convertedLead->academicAssistant ? $convertedLead->academicAssistant->name : 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Placement (Mentor) -->
                    @if($convertedLead->mentorDetails && ($convertedLead->mentorDetails->is_placement_passed || $convertedLead->mentorDetails->placement_resume))
                    <div class="col-12">
                        <hr>
                        <h6 class="text-primary mb-3 d-flex align-items-center gap-2"><i class="ti ti-briefcase"></i> Placement</h6>
                        <div class="row g-3">
                            @if($convertedLead->mentorDetails->is_placement_passed)
                            <div class="col-md-4">
                                <label class="form-label text-muted">Placement Passed</label>
                                <p class="fw-bold"><span class="badge bg-success">Yes</span></p>
                            </div>
                            @if($convertedLead->mentorDetails->is_placement_passed_at)
                            <div class="col-md-4">
                                <label class="form-label text-muted">Passed At</label>
                                <p class="fw-bold">{{ $convertedLead->mentorDetails->is_placement_passed_at->format('d-m-Y h:i A') }}</p>
                            </div>
                            @endif
                            @if($convertedLead->mentorDetails->placementPassedBy)
                            <div class="col-md-4">
                                <label class="form-label text-muted">Passed By</label>
                                <p class="fw-bold">{{ $convertedLead->mentorDetails->placementPassedBy->name }}</p>
                            </div>
                            @endif
                            @endif
                            @if($convertedLead->mentorDetails->placement_resume)
                            @php
                                $resumePath = $convertedLead->mentorDetails->placement_resume;
                                $resumeExists = $fileExistsOnDisk($resumePath);
                                $resumeUrl = $resumeExists ? asset('storage/' . $resumePath) : null;
                            @endphp
                            <div class="col-12">
                                <label class="form-label text-muted">Resume</label>
                                <p class="fw-bold">
                                    @if($resumeUrl)
                                        <a href="{{ $resumeUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary">
                                            <i class="ti ti-file-text"></i> View / Download Resume
                                        </a>
                                    @else
                                        <span class="text-muted">File not found</span>
                                    @endif
                                </p>
                            </div>
                            @if($convertedLead->mentorDetails->is_resume_verified)
                            <div class="col-md-4">
                                <label class="form-label text-muted">Resume Verified</label>
                                <p class="fw-bold"><span class="badge bg-success">Yes</span></p>
                            </div>
                            @if($convertedLead->mentorDetails->resume_verified_at)
                            <div class="col-md-4">
                                <label class="form-label text-muted">Resume Verified At</label>
                                <p class="fw-bold">{{ $convertedLead->mentorDetails->resume_verified_at->format('d M Y h:i A') }}</p>
                            </div>
                            @endif
                            @if($convertedLead->mentorDetails->resumeVerifiedBy)
                            <div class="col-md-4">
                                <label class="form-label text-muted">Resume Verified By</label>
                                <p class="fw-bold">{{ $convertedLead->mentorDetails->resumeVerifiedBy->name }}</p>
                            </div>
                            @endif
                            @endif
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Lead Information -->
                    @if($convertedLead->lead)
                    <div class="col-12">
                        <hr>
                        <h6 class="text-primary mb-3 d-flex align-items-center gap-2"><i class="ti ti-user"></i> Original Lead Information</h6>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label text-muted">Lead ID</label>
                                <p class="fw-bold">#{{ $convertedLead->lead->id }}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">Lead Name</label>
                                <p class="fw-bold">{{ $convertedLead->lead->title }}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">Lead Source</label>
                                <p class="fw-bold">{{ $convertedLead->lead->leadSource ? $convertedLead->lead->leadSource->title : 'N/A' }}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">Lead Status</label>
                                <p class="fw-bold">{{ $convertedLead->lead->leadStatus ? $convertedLead->lead->leadStatus->title : 'N/A' }}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">Interest Status</label>
                                <p class="fw-bold">
                                    @if($convertedLead->lead->interest_status)
                                        <span class="badge bg-{{ $convertedLead->lead->interest_status_color }}">{{ $convertedLead->lead->interest_status_label }}</span>
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">BDE Name</label>
                                <p class="fw-bold">{{ $convertedLead->lead->telecaller ? $convertedLead->lead->telecaller->name : 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Conversion Information -->
                    <div class="col-12">
                        <hr>
                        <h6 class="text-primary mb-3 d-flex align-items-center gap-2"><i class="ti ti-clipboard-check"></i> Conversion & Account Information</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label text-muted">Converted By</label>
                                <p class="fw-bold">{{ $convertedLead->createdBy ? $convertedLead->createdBy->name : 'N/A' }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted">Converted Date</label>
                                <p class="fw-bold">{{ $convertedLead->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted">Last Updated</label>
                                <p class="fw-bold">{{ $convertedLead->updated_at->format('M d, Y h:i A') }}</p>
                            </div>
                            @php
                                $statusBadge = $convertedLead->status === 'Paid' ? 'success' : ($convertedLead->status === 'Admission cancel' ? 'danger' : 'secondary');
                                $regFeeBadge = $convertedLead->studentDetails?->reg_fee === 'Received' ? 'success' : ($convertedLead->studentDetails?->reg_fee ? 'warning' : 'secondary');
                                $examFeeBadge = $convertedLead->studentDetails?->exam_fee === 'Paid' ? 'success' : ($convertedLead->studentDetails?->exam_fee === 'Pending' ? 'warning' : ($convertedLead->studentDetails?->exam_fee ? 'danger' : 'secondary'));
                                $idCardBadge = $convertedLead->studentDetails?->id_card === 'download' ? 'success' : ($convertedLead->studentDetails?->id_card === 'processing' ? 'warning' : ($convertedLead->studentDetails?->id_card ? 'secondary' : 'secondary'));
                                $tmaBadge = $convertedLead->studentDetails?->tma === 'Uploaded' ? 'success' : ($convertedLead->studentDetails?->tma ? 'secondary' : 'secondary');
                            @endphp
                            <div class="col-md-3">
                                <label class="form-label text-muted">Username</label>
                                <p class="fw-bold"><span class="badge bg-light text-dark border">{{ $convertedLead->username ?? 'N/A' }}</span></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">Password</label>
                                <p class="fw-bold"><span class="badge bg-light text-dark border">{{ $convertedLead->password ?? 'N/A' }}</span></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">Status</label>
                                <p class="fw-bold"><span class="badge bg-{{ $statusBadge }}">{{ $convertedLead->status ?? 'N/A' }}</span></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">ID Card</label>
                                <p class="fw-bold"><span class="badge bg-{{ $idCardBadge }} text-uppercase">{{ $convertedLead->studentDetails?->id_card ?? 'N/A' }}</span></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">Called Date</label>
                                <p class="fw-bold">{{ $convertedLead->called_date ? $convertedLead->called_date->format('d-m-Y') : 'N/A' }}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">Call Time</label>
                                <p class="fw-bold">{{ $convertedLead->called_time ? $convertedLead->called_time->format('h:i A') : 'N/A' }}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">REG. FEE</label>
                                <p class="fw-bold"><span class="badge bg-{{ $regFeeBadge }}">{{ $convertedLead->studentDetails?->reg_fee ?? 'N/A' }}</span></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">EXAM FEE</label>
                                <p class="fw-bold"><span class="badge bg-{{ $examFeeBadge }}">{{ $convertedLead->studentDetails?->exam_fee ?? 'N/A' }}</span></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">Ref No</label>
                                <p class="fw-bold"><span class="badge bg-light text-dark border">{{ $convertedLead->ref_no ?? 'N/A' }}</span></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">Enroll No</label>
                                <p class="fw-bold"><span class="badge bg-light text-dark border">{{ $convertedLead->studentDetails?->enroll_no ?? 'N/A' }}</span></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">TMA</label>
                                <p class="fw-bold"><span class="badge bg-{{ $tmaBadge }}">{{ $convertedLead->studentDetails?->tma ?? 'N/A' }}</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Course-Specific Information -->
                    @if($convertedLead->studentDetails)
                    <div class="col-12">
                        <hr>
                        <h6 class="text-primary mb-3 d-flex align-items-center gap-2"><i class="ti ti-school"></i> Course-Specific Information</h6>
                        <div class="row g-3">
                            @if($convertedLead->course_id == 16) {{-- GMVSS --}}
                                <div class="col-md-3">
                                    <label class="form-label text-muted">Registration Link</label>
                                    <p class="fw-bold">{{ $convertedLead->studentDetails->registrationLink?->title ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-muted">Certificate Status</label>
                                    <p class="fw-bold"><span class="badge bg-info">{{ $convertedLead->studentDetails->certificate_status ?? 'N/A' }}</span></p>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-muted">Certificate Received Date</label>
                                    <p class="fw-bold">{{ $convertedLead->studentDetails->certificate_received_date ? $convertedLead->studentDetails->certificate_received_date->format('d-m-Y') : 'N/A' }}</p>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-muted">Certificate Issued Date</label>
                                    <p class="fw-bold">{{ $convertedLead->studentDetails->certificate_issued_date ? $convertedLead->studentDetails->certificate_issued_date->format('d-m-Y') : 'N/A' }}</p>
                                </div>
                            @endif
                            
                            @if($convertedLead->studentDetails->registration_number)
                            <div class="col-md-3">
                                <label class="form-label text-muted">Registration Number</label>
                                <p class="fw-bold">{{ $convertedLead->studentDetails->registration_number }}</p>
                            </div>
                            @endif
                            
                            @if($convertedLead->studentDetails->enrollment_number)
                            <div class="col-md-3">
                                <label class="form-label text-muted">Enrollment Number</label>
                                <p class="fw-bold">{{ $convertedLead->studentDetails->enrollment_number }}</p>
                            </div>
                            @endif
                            
                            @if($convertedLead->studentDetails->converted_date)
                            <div class="col-md-3">
                                <label class="form-label text-muted">Converted Date</label>
                                <p class="fw-bold">{{ $convertedLead->studentDetails->converted_date->format('d-m-Y') }}</p>
                            </div>
                            @endif
                            
                            @if($convertedLead->studentDetails->remarks)
                            <div class="col-12">
                                <label class="form-label text-muted">Remarks</label>
                                <p class="fw-bold">{{ $convertedLead->studentDetails->remarks }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($leadDetail || $canInlineEditPersonal)
                    <div class="col-12 mt-4">
                        <hr>
                        <h6 class="text-primary mb-3">Lead Details</h6>
                        <div class="row g-3">
                            @include('admin.converted-leads.partials.show-inline-field', [
                                'label' => "Father's Name",
                                'field' => 'father_name',
                                'type' => 'text',
                                'displayValue' => $leadDetail?->father_name ?? 'N/A',
                                'rawValue' => $leadDetail?->father_name ?? '',
                                'canEdit' => $canInlineEditPersonal,
                                'convertedLeadId' => $convertedLead->id,
                                'col' => 3,
                            ])
                            @include('admin.converted-leads.partials.show-inline-field', [
                                'label' => "Mother's Name",
                                'field' => 'mother_name',
                                'type' => 'text',
                                'displayValue' => $leadDetail?->mother_name ?? 'N/A',
                                'rawValue' => $leadDetail?->mother_name ?? '',
                                'canEdit' => $canInlineEditPersonal,
                                'convertedLeadId' => $convertedLead->id,
                                'col' => 3,
                            ])
                            @include('admin.converted-leads.partials.show-inline-field', [
                                'label' => 'Date of Birth',
                                'field' => 'date_of_birth',
                                'type' => 'date',
                                'displayValue' => $leadDetailDobDisplay,
                                'rawValue' => $leadDetailDobRaw,
                                'canEdit' => $canInlineEditPersonal,
                                'convertedLeadId' => $convertedLead->id,
                                'col' => 3,
                            ])
                            @include('admin.converted-leads.partials.show-inline-field', [
                                'label' => 'Second Language',
                                'field' => 'second_language',
                                'type' => 'select',
                                'options' => ['' => 'Select Language', 'malayalam' => 'Malayalam', 'hindi' => 'Hindi'],
                                'displayValue' => $leadDetail?->second_language
                                    ? ucfirst($leadDetail->second_language)
                                    : 'N/A',
                                'rawValue' => $leadDetail?->second_language
                                    ? strtolower($leadDetail->second_language)
                                    : '',
                                'canEdit' => $canInlineEditPersonal,
                                'convertedLeadId' => $convertedLead->id,
                                'col' => 3,
                            ])
                            @include('admin.converted-leads.partials.show-inline-field', [
                                'label' => 'Personal Phone',
                                'field' => 'personal_number',
                                'type' => 'phone',
                                'displayValue' => ($leadDetail && $leadDetail->personal_number)
                                    ? \App\Helpers\PhoneNumberHelper::display($leadDetail->personal_code, $leadDetail->personal_number)
                                    : 'N/A',
                                'rawValue' => $leadDetail?->personal_number ?? '',
                                'code' => $leadDetail?->personal_code ?? '',
                                'codeField' => 'personal_code',
                                'canEdit' => $canInlineEditPersonal,
                                'convertedLeadId' => $convertedLead->id,
                                'col' => 3,
                            ])
                            @include('admin.converted-leads.partials.show-inline-field', [
                                'label' => 'WhatsApp',
                                'field' => 'whatsapp_number',
                                'type' => 'phone',
                                'displayValue' => ($leadDetail && $leadDetail->whatsapp_number)
                                    ? \App\Helpers\PhoneNumberHelper::display($leadDetail->whatsapp_code, $leadDetail->whatsapp_number)
                                    : 'N/A',
                                'rawValue' => $leadDetail?->whatsapp_number ?? '',
                                'code' => $leadDetail?->whatsapp_code ?? '',
                                'codeField' => 'whatsapp_code',
                                'canEdit' => $canInlineEditPersonal,
                                'convertedLeadId' => $convertedLead->id,
                                'col' => 3,
                            ])
                            @if($canInlineEditPersonal)
                            @include('admin.converted-leads.partials.show-inline-field', [
                                'label' => 'Parent Phone',
                                'field' => 'parents_number',
                                'type' => 'phone',
                                'displayValue' => ($leadDetail && $leadDetail->parents_number)
                                    ? \App\Helpers\PhoneNumberHelper::display($leadDetail->parents_code, $leadDetail->parents_number)
                                    : 'N/A',
                                'rawValue' => $leadDetail?->parents_number ?? '',
                                'code' => $leadDetail?->parents_code ?? '',
                                'codeField' => 'parents_code',
                                'canEdit' => true,
                                'convertedLeadId' => $convertedLead->id,
                                'col' => 3,
                            ])
                            @endif
                            @include('admin.converted-leads.partials.show-inline-field', [
                                'label' => 'Batch',
                                'field' => 'lead_detail_batch_id',
                                'type' => 'select',
                                'displayValue' => optional($leadDetail?->batch)->title ?? 'N/A',
                                'rawValue' => $leadDetail?->batch_id ?? '',
                                'currentId' => $leadDetail?->batch_id ?? '',
                                'courseId' => $convertedLead->course_id,
                                'canEdit' => $canInlineEditPersonal,
                                'convertedLeadId' => $convertedLead->id,
                                'col' => 3,
                            ])

                            @if($leadDetail)
                            <div class="col-12 d-flex justify-content-between align-items-center">
                                <h6 class="text-primary mt-2 mb-0">Uploaded Documents</h6>
                                @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editDocumentsModal">
                                        <i class="ti ti-edit me-1"></i> Edit Documents
                                    </button>
                                @endif
                            </div>
                            @php
                                $doc = $convertedLead->leadDetail;
                                $files = [
                                    'passport_photo' => [
                                        'label' => 'Passport Photo',
                                        'status_field' => 'passport_photo_verification_status',
                                        'verified_by_field' => 'passportPhotoVerifiedBy',
                                        'verified_at_field' => 'passport_photo_verified_at'
                                    ],
                                    'adhar_front' => [
                                        'label' => 'Aadhar Front',
                                        'status_field' => 'adhar_front_verification_status',
                                        'verified_by_field' => 'adharFrontVerifiedBy',
                                        'verified_at_field' => 'adhar_front_verified_at'
                                    ],
                                    'adhar_back' => [
                                        'label' => 'Aadhar Back',
                                        'status_field' => 'adhar_back_verification_status',
                                        'verified_by_field' => 'adharBackVerifiedBy',
                                        'verified_at_field' => 'adhar_back_verified_at'
                                    ],
                                    'signature' => [
                                        'label' => 'Signature',
                                        'status_field' => 'signature_verification_status',
                                        'verified_by_field' => 'signatureVerifiedBy',
                                        'verified_at_field' => 'signature_verified_at'
                                    ],
                                    'birth_certificate' => [
                                        'label' => 'Birth Certificate',
                                        'status_field' => 'birth_certificate_verification_status',
                                        'verified_by_field' => 'birthCertificateVerifiedBy',
                                        'verified_at_field' => 'birth_certificate_verified_at'
                                    ],
                                    'plustwo_certificate' => [
                                        'label' => 'Plus Two Certificate',
                                        'status_field' => 'plustwo_verification_status',
                                        'verified_by_field' => 'plustwoVerifiedBy',
                                        'verified_at_field' => 'plustwo_verified_at'
                                    ],
                                    'other_document' => [
                                        'label' => 'Other Document',
                                        'status_field' => 'other_document_verification_status',
                                        'verified_by_field' => 'otherDocumentVerifiedBy',
                                        'verified_at_field' => 'other_document_verified_at'
                                    ],
                                ];
                            @endphp
                            @foreach($files as $field => $config)
                                <div class="col-md-3">
                                    <label class="form-label text-muted">{{ $config['label'] }}</label>
                                    @php
                                        $path = $doc->$field ?? null;
                                        $exists = $fileExistsOnDisk($path);
                                        $fileUrl = $exists ? asset('storage/' . $path) : null;
                                        $isPdf = $exists ? \Illuminate\Support\Str::endsWith(strtolower($path), '.pdf') : false;
                                        $verificationStatus = $doc->{$config['status_field']} ?? 'pending';
                                        $verifiedBy = $doc->{$config['verified_by_field']} ?? null;
                                        $verifiedAt = $doc->{$config['verified_at_field']} ?? null;
                                    @endphp
                                    <div class="card p-2">
                                        @if($exists)
                                            @if($isPdf)
                                                <div class="text-center mb-2">
                                                    <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                                </div>
                                                <div class="mb-2">
                                                    <span class="badge bg-{{ $verificationStatus === 'verified' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($verificationStatus) }}
                                                    </span>
                                                    @if($verifiedAt)
                                                        <small class="text-muted d-block mt-1">
                                                            Verified by: {{ optional($verifiedBy)->name ?? 'Unknown' }}<br>
                                                            Date: {{ $verifiedAt->format('M d, Y') }}
                                                        </small>
                                                    @endif
                                                </div>
                                                <div class="d-grid gap-1">
                                                    <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-eye me-1"></i> View PDF
                                                    </a>
                                                    <a href="{{ $fileUrl }}" download class="btn btn-sm btn-outline-secondary">
                                                        <i class="ti ti-download me-1"></i> Download
                                                    </a>
                                                </div>
                                            @else
                                                <a href="{{ $fileUrl }}" target="_blank" class="d-block text-center mb-2">
                                                    <img src="{{ $fileUrl }}" alt="{{ $config['label'] }}" style="max-width: 100%; max-height: 140px; object-fit: contain;" onerror="this.onerror=null;this.src='{{ asset('assets/img/file.png') }}';">
                                                </a>
                                                <div class="mb-2">
                                                    <span class="badge bg-{{ $verificationStatus === 'verified' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($verificationStatus) }}
                                                    </span>
                                                    @if($verifiedAt)
                                                        <small class="text-muted d-block mt-1">
                                                            Verified by: {{ optional($verifiedBy)->name ?? 'Unknown' }}<br>
                                                            Date: {{ $verifiedAt->format('M d, Y') }}
                                                        </small>
                                                    @endif
                                                </div>
                                                <div class="d-grid">
                                                    <a href="{{ $fileUrl }}" download class="btn btn-sm btn-outline-secondary">
                                                        <i class="ti ti-download me-1"></i> Download
                                                    </a>
                                                </div>
                                            @endif
                                        @else
                                            <div class="text-center text-muted py-4">
                                                <i class="ti ti-file-alert f-24 d-block mb-1"></i>
                                                <small>File not found</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            
                            <!-- SSLC Certificates Section - Display all certificates from sslc_certificates table -->
                            @if($doc && ($doc->sslcCertificates && $doc->sslcCertificates->count() > 0))
                                @foreach($doc->sslcCertificates as $index => $certificate)
                                <div class="col-md-3">
                                    <label class="form-label text-muted">SSLC Certificate {{ $index + 1 }}</label>
                                    @php
                                        $certPath = $certificate->certificate_path ?? null;
                                        $certExists = $fileExistsOnDisk($certPath);
                                        $certUrl = $certExists ? asset('storage/' . $certPath) : null;
                                        $certIsPdf = $certExists ? \Illuminate\Support\Str::endsWith(strtolower($certPath), '.pdf') : false;
                                    @endphp
                                    <div class="card p-2">
                                        @if($certExists)
                                            @if($certIsPdf)
                                                <div class="text-center mb-2">
                                                    <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                                </div>
                                                <div class="mb-2">
                                                    <span class="badge bg-{{ $certificate->verification_status === 'verified' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($certificate->verification_status) }}
                                                    </span>
                                                    @if($certificate->verified_at)
                                                        <small class="text-muted d-block mt-1">
                                                            Verified by: {{ $certificate->verifiedBy->name ?? 'Unknown' }}<br>
                                                            Date: {{ $certificate->verified_at->format('M d, Y') }}
                                                        </small>
                                                    @endif
                                                </div>
                                                <div class="d-grid gap-1">
                                                    <a href="{{ $certUrl }}" target="_blank" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-eye me-1"></i> View PDF
                                                    </a>
                                                    <a href="{{ $certUrl }}" download class="btn btn-sm btn-outline-secondary">
                                                        <i class="ti ti-download me-1"></i> Download
                                                    </a>
                                                </div>
                                            @else
                                                <a href="{{ $certUrl }}" target="_blank" class="d-block text-center mb-2">
                                                    <img src="{{ $certUrl }}" alt="SSLC Certificate {{ $index + 1 }}" style="max-width: 100%; max-height: 140px; object-fit: contain;" onerror="this.onerror=null;this.src='{{ asset('assets/img/file.png') }}';">
                                                </a>
                                                <div class="mb-2">
                                                    <span class="badge bg-{{ $certificate->verification_status === 'verified' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($certificate->verification_status) }}
                                                    </span>
                                                    @if($certificate->verified_at)
                                                        <small class="text-muted d-block mt-1">
                                                            Verified by: {{ $certificate->verifiedBy->name ?? 'Unknown' }}<br>
                                                            Date: {{ $certificate->verified_at->format('M d, Y') }}
                                                        </small>
                                                    @endif
                                                </div>
                                                <div class="d-grid">
                                                    <a href="{{ $certUrl }}" download class="btn btn-sm btn-outline-secondary">
                                                        <i class="ti ti-download me-1"></i> Download
                                                    </a>
                                                </div>
                                            @endif
                                        @else
                                            <div class="text-center text-muted py-4">
                                                <i class="ti ti-file-alert f-24 d-block mb-1"></i>
                                                <small>File not found</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            @endif
                            
                            <!-- Legacy SSLC Certificate from leads_details table (if exists) -->
                            @if($doc && $doc->sslc_certificate)
                                <div class="col-md-3">
                                    <label class="form-label text-muted">SSLC Certificate (Legacy)</label>
                                    @php
                                        $legacyPath = $doc->sslc_certificate ?? null;
                                        $legacyExists = $fileExistsOnDisk($legacyPath);
                                        $legacyUrl = $legacyExists ? asset('storage/' . $legacyPath) : null;
                                        $legacyIsPdf = $legacyExists ? \Illuminate\Support\Str::endsWith(strtolower($legacyPath), '.pdf') : false;
                                    @endphp
                                    <div class="card p-2">
                                        @if($legacyExists)
                                            @if($legacyIsPdf)
                                                <div class="text-center mb-2">
                                                    <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                                </div>
                                                <div class="mb-2">
                                                    <span class="badge bg-{{ $doc->sslc_verification_status === 'verified' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($doc->sslc_verification_status ?? 'pending') }}
                                                    </span>
                                                    @if($doc->sslc_verified_at)
                                                        <small class="text-muted d-block mt-1">
                                                            Verified by: {{ optional($doc->sslcVerifiedBy)->name ?? 'Unknown' }}<br>
                                                            Date: {{ $doc->sslc_verified_at->format('M d, Y') }}
                                                        </small>
                                                    @endif
                                                </div>
                                                <div class="d-grid gap-1">
                                                    <a href="{{ $legacyUrl }}" target="_blank" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-eye me-1"></i> View PDF
                                                    </a>
                                                    <a href="{{ $legacyUrl }}" download class="btn btn-sm btn-outline-secondary">
                                                        <i class="ti ti-download me-1"></i> Download
                                                    </a>
                                                </div>
                                            @else
                                                <a href="{{ $legacyUrl }}" target="_blank" class="d-block text-center mb-2">
                                                    <img src="{{ $legacyUrl }}" alt="SSLC Certificate" style="max-width: 100%; max-height: 140px; object-fit: contain;" onerror="this.onerror=null;this.src='{{ asset('assets/img/file.png') }}';">
                                                </a>
                                                <div class="mb-2">
                                                    <span class="badge bg-{{ $doc->sslc_verification_status === 'verified' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($doc->sslc_verification_status ?? 'pending') }}
                                                    </span>
                                                    @if($doc->sslc_verified_at)
                                                        <small class="text-muted d-block mt-1">
                                                            Verified by: {{ optional($doc->sslcVerifiedBy)->name ?? 'Unknown' }}<br>
                                                            Date: {{ $doc->sslc_verified_at->format('M d, Y') }}
                                                        </small>
                                                    @endif
                                                </div>
                                                <div class="d-grid">
                                                    <a href="{{ $legacyUrl }}" download class="btn btn-sm btn-outline-secondary">
                                                        <i class="ti ti-download me-1"></i> Download
                                                    </a>
                                                </div>
                                            @endif
                                        @else
                                            <div class="text-center text-muted py-4">
                                                <i class="ti ti-file-alert f-24 d-block mb-1"></i>
                                                <small>File not found</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    <!-- Call History -->
    @if(isset($callLogs))
    <div class="col-12 mt-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Call History</h5>
                <span class="badge bg-light-primary text-primary">{{ $callLogs->count() }} record(s)</span>
            </div>
            <div class="card-body">
                @if($callLogs->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Agent</th>
                                <th>Number</th>
                                <th>Date &amp; Time</th>
                                <th>Duration</th>
                                <th>Recording</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($callLogs as $callLog)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><span class="badge bg-light-{{ $callLog->type === 'incoming' ? 'info' : ($callLog->type === 'outgoing' ? 'success' : 'warning') }} text-capitalize">{{ $callLog->type ?? 'N/A' }}</span></td>
                                <td>{!! $callLog->call_status_badge ?? '<span class="badge bg-light-secondary text-secondary">N/A</span>' !!}</td>
                                <td>
                                    <div class="fw-semibold">{{ $callLog->telecaller_name ?? 'Unknown' }}</div>
                                    <small class="text-muted">{{ $callLog->AgentNumber ?? $callLog->extensionNumber ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $callLog->destinationNumber ?? $callLog->calledNumber ?? 'N/A' }}</div>
                                    <small class="text-muted">Caller: {{ $callLog->callerNumber ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <div>{{ $callLog->date ? $callLog->date->format('d M Y') : 'N/A' }}</div>
                                    <small class="text-muted">{{ $callLog->start_time ?? 'N/A' }}</small>
                                </td>
                                <td>{{ $callLog->formatted_duration ?? 'N/A' }}</td>
                                <td>
                                    @if($callLog->recording_URL)
                                        <audio controls preload="none" style="width: 180px;">
                                            <source src="{{ $callLog->recording_URL }}" type="audio/mpeg">
                                            <source src="{{ $callLog->recording_URL }}" type="audio/wav">
                                            Your browser does not support the audio element.
                                        </audio>
                                    @else
                                        <span class="text-muted">Not available</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.call-logs.show', $callLog->id) }}" class="btn btn-sm btn-outline-primary" title="View Call">
                                        <i class="ti ti-external-link"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-4">
                    <i class="ti ti-phone-off f-36 d-block mb-2"></i>
                    <p class="mb-0">No call logs found for this student yet.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Converted Student Activities History -->
    @if(isset($convertedStudentActivities) && $convertedStudentActivities->count() > 0)
    <div class="col-12 mt-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0">Student Activities History
                    @if(!empty($convertedStudentActivitiesTruncated))
                        <small class="text-muted fw-normal">(showing the {{ (int) ($convertedStudentActivitiesLimit ?? 150) }} most recent)</small>
                    @endif
                </h5>
                <span class="badge bg-light-primary text-primary">{{ $convertedStudentActivities->count() }} record(s)</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($convertedStudentActivities as $activity)
                    <div class="col-12">
                        <div class="card border shadow-sm">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="d-flex align-items-start mb-3">
                                            <div class="avtar avtar-s rounded-circle bg-light-success me-3 d-flex align-items-center justify-content-center" style="min-width: 40px; height: 40px;">
                                                <i class="ti ti-activity f-18 text-success"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-2 fw-bold">{{ ucfirst(str_replace('_', ' ', $activity->activity_type ?? 'Status Update')) }}</h6>
                                                @if($activity->description)
                                                    <p class="mb-2 text-muted">{{ $activity->description }}</p>
                                                @endif
                                                
                                                <div class="d-flex flex-wrap gap-2 mb-2">
                                                    @if($activity->status)
                                                        <span class="badge bg-{{ $activity->status === 'paid' ? 'success' : ($activity->status === 'unpaid' ? 'warning' : ($activity->status === 'cancel' ? 'danger' : 'info')) }}">
                                                            Status: {{ ucfirst($activity->status) }}
                                                        </span>
                                                    @endif
                                                    @if($activity->paid_status)
                                                        <span class="badge bg-info">Paid: {{ $activity->paid_status }}</span>
                                                    @endif
                                                    @if($activity->call_status)
                                                        <span class="badge bg-{{ in_array($activity->call_status, ['Attended', 'Whatsapp connected']) ? 'success' : ($activity->call_status === 'RNR' ? 'warning' : 'danger') }}">
                                                            Call: {{ $activity->call_status }}
                                                        </span>
                                                    @endif
                                                    @if($activity->called_date)
                                                        <span class="badge bg-primary">
                                                            Called: {{ $activity->called_date->format('d M Y') }}
                                                        </span>
                                                    @endif
                                                    @if($activity->called_time)
                                                        <span class="badge bg-secondary">
                                                            Call Time: {{ $activity->called_time->format('h:i A') }}
                                                        </span>
                                                    @endif
                                                    @if($activity->followup_date)
                                                        <span class="badge bg-warning">
                                                            Followup: {{ $activity->followup_date->format('d M Y') }}
                                                        </span>
                                                    @endif
                                                </div>
                                                
                                                @if($activity->remark)
                                                    <div class="mt-2 p-2 bg-light rounded" style="font-size: 14px; line-height: 1.6;">
                                                        <strong class="text-muted d-block mb-1">Remarks:</strong>
                                                        {{ $activity->remark }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-md-end">
                                        <div class="mb-2">
                                            @if($activity->activity_date)
                                                <div class="fw-semibold">{{ $activity->activity_date->format('d M Y') }}</div>
                                            @endif
                                            @if($activity->activity_time)
                                                <div class="text-muted small">{{ date('h:i A', strtotime($activity->activity_time)) }}</div>
                                            @endif
                                            @if($activity->called_time)
                                                <div class="text-muted small">Call Time: {{ $activity->called_time->format('h:i A') }}</div>
                                            @endif
                                        </div>
                                        @if($activity->createdBy)
                                            <div class="text-muted small">
                                                <i class="ti ti-user me-1"></i>by {{ $activity->createdBy->name }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Lead Activities History -->
    <div class="col-12 mt-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Lead Activities History
                    @if(!empty($leadActivitiesTruncated))
                        <small class="text-muted fw-normal">(showing the {{ (int) ($leadActivitiesLimit ?? 200) }} most recent)</small>
                    @endif
                </h5>
            </div>
            <div class="card-body">
                @if($leadActivities->count() > 0)
                    <div class="timeline">
                        @foreach($leadActivities as $activity)
                        <div class="timeline-item">
                            <div class="timeline-marker">
                                <div class="avtar avtar-s rounded-circle bg-light-{{ $activity->activity_type === 'converted' ? 'success' : 'primary' }}">
                                    <i class="ti ti-{{ $activity->activity_type === 'converted' ? 'check' : 'activity' }} f-16"></i>
                                </div>
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }}</h6>
                                        <p class="mb-1 text-muted">{{ $activity->description }}</p>
                                        @if($activity->reason)
                                            <p class="mb-1"><strong>Reason:</strong> <span class="badge bg-info">{{ $activity->formatted_reason }}</span></p>
                                        @endif
                                        @if($activity->rating)
                                            <p class="mb-1"><strong>Rating:</strong> <span class="badge bg-success">{{ $activity->rating }}/10</span></p>
                                        @endif
                                        @if($activity->lead_status_id == 2 && $activity->followup_date)
                                            <p class="mb-1"><strong>Followup Date:</strong> <span class="badge bg-warning">{{ $activity->followup_date->format('d M Y') }}</span></p>
                                        @endif
                                        @if($activity->remarks)
                                            <p class="mb-1"><small class="text-info">{{ $activity->remarks }}</small></p>
                                        @endif
                                        @if($activity->leadStatus)
                                            <span class="badge bg-light-{{ \App\Helpers\StatusHelper::getLeadStatusColor($activity->leadStatus->id) }}">
                                                {{ $activity->leadStatus->title }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">{{ $activity->created_at->format('M d, Y h:i A') }}</small>
                                        @if($activity->createdBy)
                                            <p class="mb-0"><small class="text-muted">by {{ $activity->createdBy->name }}</small></p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="ti ti-activity f-48 mb-3"></i>
                        <h6>No Activities Found</h6>
                        <p class="mb-0">No activities have been recorded for this lead.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

<!-- Edit Documents Modal -->
@if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
<div class="modal fade" id="editDocumentsModal" tabindex="-1" aria-labelledby="editDocumentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDocumentsModalLabel">Edit Uploaded Documents</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editDocumentsForm" method="POST" action="{{ route('admin.converted-leads.update-documents', $convertedLead->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle me-2"></i>
                        You can update any document by uploading a new file. Leave blank to keep the existing file.
                    </div>
                    <div class="row g-3">
                        @php
                            $doc = $convertedLead->leadDetail;
                            $editFiles = [
                                'passport_photo' => ['label' => 'Passport Photo', 'field' => 'passport_photo'],
                                'adhar_front' => ['label' => 'Aadhar Front', 'field' => 'adhar_front'],
                                'adhar_back' => ['label' => 'Aadhar Back', 'field' => 'adhar_back'],
                                'signature' => ['label' => 'Signature', 'field' => 'signature'],
                                'birth_certificate' => ['label' => 'Birth Certificate', 'field' => 'birth_certificate'],
                                'plustwo_certificate' => ['label' => 'Plus Two Certificate', 'field' => 'plustwo_certificate'],
                                'ug_certificate' => ['label' => 'UG Certificate', 'field' => 'ug_certificate'],
                                'pg_certificate' => ['label' => 'PG Certificate', 'field' => 'pg_certificate'],
                                'other_document' => ['label' => 'Other Document', 'field' => 'other_document'],
                            ];
                        @endphp
                        @foreach($editFiles as $key => $fileConfig)
                            <div class="col-md-6">
                                <label class="form-label">{{ $fileConfig['label'] }}</label>
                                @php
                                    $currentFile = $doc ? $doc->{$fileConfig['field']} : null;
                                    $fileExists = $fileExistsOnDisk($currentFile);
                                    $fileUrl = $fileExists ? asset('storage/' . $currentFile) : null;
                                @endphp
                                @if($fileExists)
                                    <div class="mb-2">
                                        <small class="text-muted d-block mb-1">Current file:</small>
                                        <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-outline-info">
                                            <i class="ti ti-eye me-1"></i> View Current
                                        </a>
                                    </div>
                                @endif
                                <input type="file" class="form-control" name="{{ $fileConfig['field'] }}" accept="image/*,.pdf">
                                <small class="text-muted">Leave blank to keep existing file. Max size: 2MB</small>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-upload me-1"></i> Update Documents
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if($canInlineEditPersonal)
<div
    id="jsConvertedLeadShowConfig"
    data-inline-url="{{ route('admin.converted-leads.inline-update', $convertedLead->id) }}"
    style="display: none;"
></div>
<script id="country-codes-json" type="application/json">{!! json_encode($country_codes ?? [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
@endif

@endsection

@push('styles')
<style>
.cancelled-card {
    border: 1px solid #f5c2c7;
    background-color: #fff5f5;
}
.cancelled-card .card-header {
    background-color: #fff1f0;
    border-bottom: 1px solid #f5c2c7;
}
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    z-index: 1;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #7366ff;
    margin-left: 10px;
}

.timeline-item:last-child .timeline::before {
    display: none;
}

.inline-edit-show-cl.editing .display-value,
.inline-edit-show-cl.editing .edit-btn-show-cl {
    display: none !important;
}

.inline-edit-show-cl .edit-form-show-cl {
    display: none;
}

.inline-edit-show-cl.editing .edit-form-show-cl {
    display: block;
}

.spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endpush

@push('scripts')
@if($canInlineEditPersonal)
    <script src="{{ asset('assets/js/converted-lead-show-inline-edit.js') }}"></script>
@endif
@endpush
