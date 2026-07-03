@extends('layouts.mantis')

@section('title', 'Registration Details - ' . $lead->title)

@section('content')
<!-- Country codes JSON for JavaScript -->
<script type="application/json" id="country-codes-json">{{ json_encode($country_codes) }}</script>

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">
                        <i class="ti ti-user-check me-2 text-primary"></i>Registration Details
                    </h5>
                    <p class="m-b-0 text-muted">{{ $lead->title }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('leads.registration-form-submitted') }}">Registration Form Submitted Leads</a></li>
                    <li class="breadcrumb-item active">Registration Details</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

@if(isset($error))
<!-- [ Error Alert ] start -->
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <div class="d-flex align-items-center">
        <i class="ti ti-alert-circle me-2"></i>
        <div class="flex-grow-1">
            <strong>Error:</strong> {{ $error }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<!-- [ Error Alert ] end -->
@endif

@if($studentDetail)

<!-- Action Buttons -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-2">
                        <div>
                            <h6 class="mb-1">Registration Status</h6>
                            <span class="badge bg-{{ $studentDetail->status === 'approved' ? 'success' : ($studentDetail->status === 'rejected' ? 'danger' : 'warning') }} fs-6">
                                {{ ucfirst($studentDetail->status ?? 'pending') }}
                            </span>
                            @if($studentDetail->reviewed_at && in_array($studentDetail->status, ['approved', 'rejected']))
                            <small class="text-muted d-block mt-1">
                                {{ ucfirst($studentDetail->status) }} on {{ $studentDetail->reviewed_at->format('M d, Y h:i A') }}
                            </small>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex flex-column flex-sm-row gap-2 w-100 w-md-auto justify-content-end">
                        <a href="{{ route('leads.registration-form-submitted') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-arrow-left me-2"></i>Back to Registration Form Submitted Leads
                        </a>
                        @if(\App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant()) {{-- Only admission counsellor, admin, and academic assistant can approve/reject --}}
                        <div class="d-flex flex-column flex-sm-row gap-2">
                            @if($studentDetail->status !== 'approved')
                            <button class="btn btn-success" onclick="show_small_modal('{{ route('leads.approve-modal', $lead->id) }}', 'Approve Registration')">
                                <i class="ti ti-check me-2"></i>Approve
                            </button>
                            @endif
                            @if($studentDetail->status !== 'rejected')
                            <button class="btn btn-danger" onclick="show_small_modal('{{ route('leads.reject-modal', $lead->id) }}', 'Reject Registration')">
                                <i class="ti ti-x me-2"></i>Reject
                            </button>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row registration-details-container">
    <!-- Lead Information Card -->
    <div class="col-12 col-lg-4 mb-4 mb-lg-0">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-gradient-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="ti ti-user me-2"></i>Lead Information
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="avtar avtar-xl rounded-circle bg-light-primary mx-auto mb-3">
                        <span class="f-24 fw-bold text-primary">{{ strtoupper(substr($lead->title, 0, 1)) }}</span>
                    </div>
                    <h5 class="mb-1">{{ $lead->title }}</h5>
                    <p class="text-muted mb-0">{{ $lead->leadSource->title ?? 'N/A' }}</p>
                </div>

                <div class="row g-3">
                    <div class="col-12">
                        <div class="d-flex align-items-center p-2 bg-light rounded">
                            <i class="ti ti-phone text-primary me-3"></i>
                            <div class="flex-grow-1">
                                <small class="text-muted d-block">Phone</small>
                                <span class="fw-medium">{{ \App\Helpers\PhoneNumberHelper::display($lead->code, $lead->phone) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center p-2 bg-light rounded">
                            <i class="ti ti-mail text-primary me-3"></i>
                            <div class="flex-grow-1">
                                <small class="text-muted d-block">Email</small>
                                <span class="fw-medium">{{ $lead->email ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center p-2 bg-light rounded">
                            <i class="ti ti-book text-primary me-3"></i>
                            <div class="flex-grow-1">
                                <small class="text-muted d-block">Course</small>
                                <span class="fw-medium">{{ $lead->course->title ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center p-2 bg-light rounded">
                            <i class="ti ti-user text-primary me-3"></i>
                            <div class="flex-grow-1">
                                <small class="text-muted d-block">Telecaller</small>
                                <span class="fw-medium">{{ $lead->telecaller->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center p-2 bg-light rounded">
                            <i class="ti ti-calendar text-primary me-3"></i>
                            <div class="flex-grow-1">
                                <small class="text-muted d-block">Created</small>
                                <span class="fw-medium">{{ $lead->created_at->format('M d, Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Registration Details Card -->
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-gradient-success text-white">
                <h5 class="card-title mb-0">
                    <i class="ti ti-file-text me-2"></i>Registration Details
                </h5>
            </div>
            <div class="card-body">
                <!-- Navigation Tabs -->
                <ul class="nav nav-tabs nav-fill mb-4 responsive-tabs" id="registrationTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab">
                            <i class="ti ti-user me-1 me-md-2"></i><span class="d-none d-sm-inline">Personal Info</span><span class="d-sm-none">Personal</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab">
                            <i class="ti ti-phone me-1 me-md-2"></i><span class="d-none d-sm-inline">Contact Info</span><span class="d-sm-none">Contact</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="address-tab" data-bs-toggle="tab" data-bs-target="#address" type="button" role="tab">
                            <i class="ti ti-map-pin me-1 me-md-2"></i><span class="d-none d-sm-inline">Address</span><span class="d-sm-none">Address</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="course-tab" data-bs-toggle="tab" data-bs-target="#course" type="button" role="tab">
                            <i class="ti ti-book me-1 me-md-2"></i><span class="d-none d-sm-inline">Course Info</span><span class="d-sm-none">Course</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab">
                            <i class="ti ti-file me-1 me-md-2"></i><span class="d-none d-sm-inline">Documents</span><span class="d-sm-none">Docs</span>
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="registrationTabsContent">
                    <!-- Personal Information Tab -->
                    <div class="tab-pane fade show active" id="personal" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-user text-primary"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Student Name</label>
                                        <p class="info-value" data-field="student_name" data-lead-detail-id="{{ $studentDetail->id }}">
                                            {{ $studentDetail->student_name }}
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-field" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-user-check text-success"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Father Name</label>
                                        <p class="info-value" data-field="father_name" data-lead-detail-id="{{ $studentDetail->id }}">
                                            {{ $studentDetail->father_name }}
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-field" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-user-heart text-info"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Mother Name</label>
                                        <p class="info-value" data-field="mother_name" data-lead-detail-id="{{ $studentDetail->id }}">
                                            {{ $studentDetail->mother_name }}
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-field" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-calendar text-warning"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Date of Birth</label>
                                        <p class="info-value" data-field="date_of_birth" data-lead-detail-id="{{ $studentDetail->id }}" data-value="{{ $studentDetail->date_of_birth ? $studentDetail->date_of_birth->format('Y-m-d') : '' }}">
                                            {{ $studentDetail->date_of_birth ? $studentDetail->date_of_birth->format('M d, Y') : 'N/A' }}
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-field" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @if($studentDetail->gender)
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-gender-bigender text-info"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Gender</label>
                                        <p class="info-value" data-field="gender" data-lead-detail-id="{{ $studentDetail->id }}" data-value="{{ $studentDetail->gender }}">
                                            {{ ucfirst($studentDetail->gender) }}
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-field" data-field-type="select" data-options='{"male":"Male","female":"Female"}' title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if(isset($studentDetail->is_employed))
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-briefcase text-success"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Are you employed?</label>
                                        <p class="info-value" data-field="is_employed" data-lead-detail-id="{{ $studentDetail->id }}" data-value="{{ $studentDetail->is_employed ? '1' : '0' }}">
                                            {{ $studentDetail->is_employed ? 'Yes' : 'No' }}
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-field" data-field-type="select" data-options='{"1":"Yes","0":"No"}' title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Contact Information Tab -->
                    <div class="tab-pane fade" id="contact" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-mail text-primary"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Email</label>
                                        <p class="info-value" data-field="email" data-lead-detail-id="{{ $studentDetail->id }}">
                                            {{ $studentDetail->email }}
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-field" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-phone text-success"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Personal Phone</label>
                                        <p class="info-value" data-field="phone" data-lead-detail-id="{{ $studentDetail->id }}" data-phone-code="{{ $studentDetail->personal_code }}" data-phone-number="{{ $studentDetail->personal_number }}">
                                            {{ \App\Helpers\PhoneNumberHelper::display($studentDetail->personal_code, $studentDetail->personal_number) }}
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-phone-field" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-phone-call text-info"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Parents Phone</label>
                                        <p class="info-value" data-field="parents_phone" data-lead-detail-id="{{ $studentDetail->id }}" data-phone-code="{{ $studentDetail->parents_code }}" data-phone-number="{{ $studentDetail->parents_number }}">
                                            {{ \App\Helpers\PhoneNumberHelper::display($studentDetail->parents_code, $studentDetail->parents_number) }}
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-phone-field" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-brand-whatsapp text-success"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">WhatsApp</label>
                                        <p class="info-value" data-field="whatsapp" data-lead-detail-id="{{ $studentDetail->id }}" data-phone-code="{{ $studentDetail->whatsapp_code }}" data-phone-number="{{ $studentDetail->whatsapp_number }}">
                                            {{ \App\Helpers\PhoneNumberHelper::display($studentDetail->whatsapp_code, $studentDetail->whatsapp_number) }}
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-phone-field" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @if($studentDetail->father_contact_number)
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-phone-call text-primary"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Father's Contact No.</label>
                                        <p class="info-value" data-field="father_contact" data-lead-detail-id="{{ $studentDetail->id }}" data-phone-code="{{ $studentDetail->father_contact_code }}" data-phone-number="{{ $studentDetail->father_contact_number }}">
                                            {{ \App\Helpers\PhoneNumberHelper::display($studentDetail->father_contact_code, $studentDetail->father_contact_number) }}
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-phone-field" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if($studentDetail->mother_contact_number)
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-phone-call text-info"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Mother's Contact No.</label>
                                        <p class="info-value" data-field="mother_contact" data-lead-detail-id="{{ $studentDetail->id }}" data-phone-code="{{ $studentDetail->mother_contact_code }}" data-phone-number="{{ $studentDetail->mother_contact_number }}">
                                            {{ \App\Helpers\PhoneNumberHelper::display($studentDetail->mother_contact_code, $studentDetail->mother_contact_number) }}
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-phone-field" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Address Information Tab -->
                    <div class="tab-pane fade" id="address" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-map-pin text-primary"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Street Address</label>
                                        <p class="info-value" data-field="street" data-lead-detail-id="{{ $studentDetail->id }}">
                                            {{ $studentDetail->street }}
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-field" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-building text-success"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Locality</label>
                                        <p class="info-value" data-field="locality" data-lead-detail-id="{{ $studentDetail->id }}">
                                            {{ $studentDetail->locality }}
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-field" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-mailbox text-info"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Post Office</label>
                                        <p class="info-value" data-field="post_office" data-lead-detail-id="{{ $studentDetail->id }}">
                                            {{ $studentDetail->post_office }}
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-field" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-map text-warning"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">District</label>
                                        <p class="info-value" data-field="district" data-lead-detail-id="{{ $studentDetail->id }}">
                                            {{ $studentDetail->district }}
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-field" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-flag text-danger"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">State</label>
                                        <p class="info-value" data-field="state" data-lead-detail-id="{{ $studentDetail->id }}">
                                            {{ $studentDetail->state }}
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-field" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-hash text-secondary"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Pin Code</label>
                                        <p class="info-value" data-field="pin_code" data-lead-detail-id="{{ $studentDetail->id }}">
                                            {{ $studentDetail->pin_code }}
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-field" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Course Information Tab -->
                    <div class="tab-pane fade" id="course" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-book text-primary"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Course</label>
                                        <p class="info-value">{{ $studentDetail->course->title ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                            @if(in_array($lead->course_id, [1, 2]))
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-bookmark text-success"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Subject</label>
                                        <p class="info-value" id="subject-value">
                                            {{ $studentDetail->subject->title ?? 'N/A' }}
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-field" data-field="subject_id" data-lead-detail-id="{{ $studentDetail->id }}" data-course-id="{{ $studentDetail->course_id }}" data-current-id="{{ $studentDetail->subject_id }}" title="Edit"><i class="ti ti-edit"></i></button>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-users text-info"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Batch</label>
                                        <p class="info-value" id="batch-value">
                                            {{ $studentDetail->batch->title ?? 'N/A' }}
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-field" data-field="batch_id" data-lead-detail-id="{{ $studentDetail->id }}" data-course-id="{{ $studentDetail->course_id }}" data-current-id="{{ $studentDetail->batch_id }}" title="Edit"><i class="ti ti-edit"></i></button>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @if($lead->course_id == 23 || $lead->course_id == '23')
                            {{-- Course Type --}}
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-certificate text-primary"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Course Type</label>
                                        <p class="info-value" data-field="course_type" data-lead-detail-id="{{ $studentDetail->id }}" data-value="{{ isset($studentDetail->course_type) ? $studentDetail->course_type : '' }}">
                                            {{ isset($studentDetail->course_type) && $studentDetail->course_type ? $studentDetail->course_type : 'N/A' }}
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-field" data-field="course_type" data-lead-detail-id="{{ $studentDetail->id }}" data-field-type="select" data-options='{"UG":"UG","PG":"PG"}' title="Edit"><i class="ti ti-edit"></i></button>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            {{-- EduMaster Course Name --}}
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-book-2 text-success"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">EduMaster Course Name</label>
                                        <p class="info-value" data-field="edumaster_course_name" data-lead-detail-id="{{ $studentDetail->id }}" data-value="{{ isset($studentDetail->edumaster_course_name) ? e($studentDetail->edumaster_course_name) : '' }}">
                                            {{ isset($studentDetail->edumaster_course_name) && $studentDetail->edumaster_course_name ? $studentDetail->edumaster_course_name : 'N/A' }}
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-field" data-field="edumaster_course_name" data-lead-detail-id="{{ $studentDetail->id }}" title="Edit"><i class="ti ti-edit"></i></button>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            {{-- Selected Courses --}}
                            @php
                            $selectedCoursesDisplay = 'N/A';
                            if (isset($studentDetail->selected_courses) && $studentDetail->selected_courses) {
                            $selected = is_string($studentDetail->selected_courses) ? json_decode($studentDetail->selected_courses, true) : $studentDetail->selected_courses;
                            $selectedCoursesDisplay = is_array($selected) ? implode(', ', $selected) : ($studentDetail->selected_courses ?? 'N/A');
                            if (empty($selectedCoursesDisplay)) {
                            $selectedCoursesDisplay = 'N/A';
                            }
                            }
                            @endphp
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-list-check text-info"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Selected Courses</label>
                                        <p class="info-value" data-field="selected_courses" data-lead-detail-id="{{ $studentDetail->id }}" data-value="{{ e($selectedCoursesDisplay) }}">
                                            {{ $selectedCoursesDisplay }}
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-field" data-field="selected_courses" data-lead-detail-id="{{ $studentDetail->id }}" title="Edit"><i class="ti ti-edit"></i></button>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            {{-- SSLC Back Year --}}
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-calendar-event text-warning"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">SSLC Back Year</label>
                                        <p class="info-value" data-field="sslc_back_year" data-lead-detail-id="{{ $studentDetail->id }}" data-value="{{ isset($studentDetail->sslc_back_year) ? $studentDetail->sslc_back_year : '' }}">
                                            {{ isset($studentDetail->sslc_back_year) && $studentDetail->sslc_back_year ? $studentDetail->sslc_back_year : 'N/A' }}
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-field" data-field="sslc_back_year" data-lead-detail-id="{{ $studentDetail->id }}" title="Edit"><i class="ti ti-edit"></i></button>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            {{-- Plus Two Back Year --}}
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-calendar-event text-warning"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Plus Two Back Year</label>
                                        <p class="info-value" data-field="plustwo_back_year" data-lead-detail-id="{{ $studentDetail->id }}" data-value="{{ isset($studentDetail->plustwo_back_year) ? $studentDetail->plustwo_back_year : '' }}">
                                            {{ isset($studentDetail->plustwo_back_year) && $studentDetail->plustwo_back_year ? $studentDetail->plustwo_back_year : 'N/A' }}
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-field" data-field="plustwo_back_year" data-lead-detail-id="{{ $studentDetail->id }}" title="Edit"><i class="ti ti-edit"></i></button>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            {{-- Plus Two Subject --}}
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-book text-info"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Plus Two Subject</label>
                                        <p class="info-value" data-field="plustwo_subject" data-lead-detail-id="{{ $studentDetail->id }}" data-value="{{ isset($studentDetail->plustwo_subject) ? e($studentDetail->plustwo_subject) : '' }}">
                                            {{ isset($studentDetail->plustwo_subject) && $studentDetail->plustwo_subject ? $studentDetail->plustwo_subject : 'N/A' }}
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-field" data-field="plustwo_subject" data-lead-detail-id="{{ $studentDetail->id }}" title="Edit"><i class="ti ti-edit"></i></button>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            {{-- Back Year --}}
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-calendar-event text-warning"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Back Year</label>
                                        <p class="info-value" data-field="back_year" data-lead-detail-id="{{ $studentDetail->id }}" data-value="{{ isset($studentDetail->back_year) ? $studentDetail->back_year : '' }}">
                                            {{ isset($studentDetail->back_year) && $studentDetail->back_year ? $studentDetail->back_year : 'N/A' }}
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-field" data-field="back_year" data-lead-detail-id="{{ $studentDetail->id }}" title="Edit"><i class="ti ti-edit"></i></button>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            {{-- Degree Back Year --}}
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-calendar-event text-warning"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Degree Back Year</label>
                                        <p class="info-value" data-field="degree_back_year" data-lead-detail-id="{{ $studentDetail->id }}" data-value="{{ isset($studentDetail->degree_back_year) ? $studentDetail->degree_back_year : '' }}">
                                            {{ isset($studentDetail->degree_back_year) && $studentDetail->degree_back_year ? $studentDetail->degree_back_year : 'N/A' }}
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-field" data-field="degree_back_year" data-lead-detail-id="{{ $studentDetail->id }}" title="Edit"><i class="ti ti-edit"></i></button>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if(isset($hasSubCourses) && $hasSubCourses)
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-book-2 text-secondary"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Sub Course</label>
                                        <p class="info-value" id="sub-course-value">
                                            {{ $studentDetail->subCourse->title ?? 'N/A' }}
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-field" data-field="sub_course_id" data-lead-detail-id="{{ $studentDetail->id }}" data-course-id="{{ $studentDetail->course_id }}" data-current-id="{{ $studentDetail->sub_course_id ?? '' }}" title="Edit"><i class="ti ti-edit"></i></button>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if($studentDetail->ug_pg_selection)
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-graduation-cap text-warning"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">UG/PG Selection</label>
                                        <p class="info-value">{{ ucfirst($studentDetail->ug_pg_selection) }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if($lead->course_id == 16)
                            @if($studentDetail->class)
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-school text-success"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Course Type</label>
                                        <p class="info-value" data-field="class" data-lead-detail-id="{{ $studentDetail->id }}" data-value="{{ strtolower($studentDetail->class) }}">
                                            @php
                                            $classValue = strtolower($studentDetail->class);
                                            $displayValue = ($classValue === 'sslc') ? 'SSLC' : (($classValue === 'plustwo') ? 'Plus Two' : ucfirst($studentDetail->class));
                                            @endphp
                                            {{ $displayValue }}
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-field" data-field-type="select" data-options='{"sslc":"SSLC","plustwo":"Plus Two"}' title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-calendar-event text-danger"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Pass Year</label>
                                        <p class="info-value" data-field="passed_year" data-lead-detail-id="{{ $studentDetail->id }}">
                                            {{ $studentDetail->passed_year ?? 'N/A' }}
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-field" title="Edit"><i class="ti ti-edit"></i></button>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if($studentDetail->programme_type)
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-device-desktop text-primary"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Course Type</label>
                                        <p class="info-value" data-field="programme_type" data-lead-detail-id="{{ $studentDetail->id }}" data-value="{{ $studentDetail->programme_type }}">
                                            {{ ucfirst($studentDetail->programme_type) }}
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-field" data-field-type="select" data-options='{"online":"Online","offline":"Offline"}' title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if($studentDetail->programme_type === 'offline')
                            <div class="col-md-6 location-field-container">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-map-pin text-warning"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Location</label>
                                        <p class="info-value" data-field="location" data-lead-detail-id="{{ $studentDetail->id }}" data-value="{{ $studentDetail->location ?? '' }}">
                                            {{ $studentDetail->location ?? 'N/A' }}
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-field" data-field-type="select" data-options='{!! json_encode($offlinePlaceOptions ?? []) !!}' title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if($studentDetail->classTime || (isset($classTimes) && $classTimes->count() > 0))
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-clock text-info"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Class Time</label>
                                        <p class="info-value" data-field="class_time_id" data-lead-detail-id="{{ $studentDetail->id }}" data-course-id="{{ $studentDetail->course_id }}" data-current-id="{{ $studentDetail->class_time_id }}">
                                            @if($studentDetail->classTime)
                                            @php
                                            $fromTime = $studentDetail->classTime->from_time
                                            ? date('h:i A', strtotime($studentDetail->classTime->from_time))
                                            : '-';
                                            $toTime = $studentDetail->classTime->to_time
                                            ? date('h:i A', strtotime($studentDetail->classTime->to_time))
                                            : '-';
                                            @endphp
                                            {{ $fromTime }} - {{ $toTime }}
                                            @else
                                            N/A
                                            @endif
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                                            <button class="btn btn-sm btn-outline-primary ms-2 edit-class-time-field" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Documents Tab -->
                    <div class="tab-pane fade" id="documents" role="tabpanel">
                        <div class="row g-3">
                            @if($studentDetail->sslcCertificates && $studentDetail->sslcCertificates->count() > 0)
                            @foreach($studentDetail->sslcCertificates as $index => $certificate)
                            <div class="col-12 col-md-6">
                                <div class="document-card">
                                    <div class="document-icon">
                                        <i class="ti ti-file-certificate text-primary"></i>
                                    </div>
                                    <div class="document-content">
                                        <div class="document-info">
                                            <label class="document-label">SSLC Certificate {{ $index + 1 }}</label>
                                            <div class="verification-status">
                                                <span class="badge bg-{{ $certificate->verification_status === 'verified' ? 'success' : 'warning' }}" data-document-type="sslc_certificate_{{ $certificate->id }}">
                                                    {{ ucfirst($certificate->verification_status) }}
                                                </span>
                                                @if($certificate->verified_at)
                                                <small class="text-muted d-block">
                                                    Verified by: {{ $certificate->verifiedBy->name ?? 'Unknown' }}<br>
                                                    Date: {{ $certificate->verified_at->format('M d, Y h:i A') }}
                                                </small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="document-actions">
                                            <a href="{{ Storage::url($certificate->certificate_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="ti ti-eye me-1"></i>View
                                            </a>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                                            <button class="btn btn-sm btn-success" onclick="openSSLCVerificationModal({{ $certificate->id }}, '{{ $certificate->verification_status }}')">
                                                <i class="ti ti-check me-1"></i>Verify
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="removeSSLCertificate({{ $certificate->id }})">
                                                <i class="ti ti-trash me-1"></i>Remove
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @elseif($studentDetail->sslc_certificate)
                            <!-- Fallback for old single SSLC certificate -->
                            <div class="col-12 col-md-6">
                                <div class="document-card">
                                    <div class="document-icon">
                                        <i class="ti ti-file-certificate text-primary"></i>
                                    </div>
                                    <div class="document-content">
                                        <div class="document-info">
                                            <label class="document-label">SSLC Certificate (Legacy)</label>
                                            <div class="verification-status">
                                                <span class="badge bg-{{ $studentDetail->sslc_verification_status === 'verified' ? 'success' : 'warning' }}" data-document-type="sslc_certificate">
                                                    {{ ucfirst($studentDetail->sslc_verification_status ?? 'pending') }}
                                                </span>
                                                @if($studentDetail->sslc_verified_at)
                                                <small class="text-muted d-block">
                                                    Verified by: {{ $studentDetail->sslcVerifiedBy->name ?? 'Unknown' }}<br>
                                                    Date: {{ $studentDetail->sslc_verified_at->format('M d, Y h:i A') }}
                                                </small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="document-actions">
                                            <a href="{{ Storage::url($studentDetail->sslc_certificate) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="ti ti-eye me-1"></i>View
                                            </a>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                                            <button class="btn btn-sm btn-success" onclick="openVerificationModal('sslc_certificate', '{{ $studentDetail->sslc_verification_status }}')">
                                                <i class="ti ti-check me-1"></i>Verify
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Add SSLC Certificate Button - Always show if user has permission -->
                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                            <div class="col-12">
                                <div class="text-center">
                                    <button class="btn btn-outline-primary" onclick="openAddSSLCModal()">
                                        <i class="ti ti-plus me-2"></i>Add SSLC Certificate
                                    </button>
                                </div>
                            </div>
                            @endif

                            @if($studentDetail->plustwo_certificate || $studentDetail->plus_two_certificate)
                            <div class="col-12 col-md-6">
                                <div class="document-card">
                                    <div class="document-icon">
                                        <i class="ti ti-file-certificate text-success"></i>
                                    </div>
                                    <div class="document-content">
                                        <div class="document-info">
                                            <label class="document-label">Plus Two Certificate</label>
                                            <div class="verification-status">
                                                @php
                                                $certificateField = $studentDetail->plustwo_certificate ? 'plustwo' : 'plus_two';
                                                $verificationStatus = $studentDetail->{$certificateField . '_verification_status'} ?? 'pending';
                                                $verifiedAt = $studentDetail->{$certificateField . '_verified_at'};
                                                $verifiedBy = $studentDetail->{ucfirst($certificateField) . 'VerifiedBy'} ?? null;
                                                @endphp
                                                <span class="badge bg-{{ $verificationStatus === 'verified' ? 'success' : 'warning' }}" data-document-type="{{ $certificateField }}_certificate">
                                                    {{ ucfirst($verificationStatus) }}
                                                </span>
                                                @if($verifiedAt)
                                                <small class="text-muted d-block">
                                                    Verified by: {{ $verifiedBy->name ?? 'Unknown' }}<br>
                                                    Date: {{ $verifiedAt->format('M d, Y h:i A') }}
                                                </small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="document-actions">
                                            <a href="{{ Storage::url($studentDetail->plustwo_certificate ?? $studentDetail->plus_two_certificate) }}" target="_blank" class="btn btn-sm btn-outline-success">
                                                <i class="ti ti-eye me-1"></i>View
                                            </a>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                                            <button class="btn btn-sm btn-success" onclick="openVerificationModal('{{ $certificateField }}_certificate', '{{ $verificationStatus }}')">
                                                <i class="ti ti-check me-1"></i>Verify
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($studentDetail->ug_certificate)
                            <div class="col-12 col-md-6">
                                <div class="document-card">
                                    <div class="document-icon">
                                        <i class="ti ti-file-certificate text-info"></i>
                                    </div>
                                    <div class="document-content">
                                        <div class="document-info">
                                            <label class="document-label">UG Certificate</label>
                                            <div class="verification-status">
                                                <span class="badge bg-{{ $studentDetail->ug_verification_status === 'verified' ? 'success' : 'warning' }}" data-document-type="ug_certificate">
                                                    {{ ucfirst($studentDetail->ug_verification_status ?? 'pending') }}
                                                </span>
                                                @if($studentDetail->ug_verified_at)
                                                <small class="text-muted d-block">
                                                    Verified by: {{ $studentDetail->ugVerifiedBy->name ?? 'Unknown' }}<br>
                                                    Date: {{ $studentDetail->ug_verified_at->format('M d, Y h:i A') }}
                                                </small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="document-actions">
                                            <a href="{{ Storage::url($studentDetail->ug_certificate) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                                <i class="ti ti-eye me-1"></i>View
                                            </a>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                                            <button class="btn btn-sm btn-success" onclick="openVerificationModal('ug_certificate', '{{ $studentDetail->ug_verification_status }}')">
                                                <i class="ti ti-check me-1"></i>Verify
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($studentDetail->birth_certificate)
                            <div class="col-12 col-md-6">
                                <div class="document-card">
                                    <div class="document-icon">
                                        <i class="ti ti-file-certificate text-info"></i>
                                    </div>
                                    <div class="document-content">
                                        <div class="document-info">
                                            <label class="document-label">Birth Certificate</label>
                                            <div class="verification-status">
                                                @php($birthStatus = $studentDetail->birth_certificate_verification_status ?? 'pending')
                                                <span class="badge bg-{{ $birthStatus === 'verified' ? 'success' : 'warning' }}" data-document-type="birth_certificate">
                                                    {{ ucfirst($birthStatus) }}
                                                </span>
                                                @if(!empty($studentDetail->birth_certificate_verified_at))
                                                <small class="text-muted d-block">
                                                    Verified by: {{ $studentDetail->birthCertificateVerifiedBy->name ?? 'Unknown' }}<br>
                                                    Date: {{ $studentDetail->birth_certificate_verified_at->format('M d, Y h:i A') }}
                                                </small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="document-actions">
                                            <a href="{{ Storage::url($studentDetail->birth_certificate) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                                <i class="ti ti-eye me-1"></i>View
                                            </a>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                                            <button class="btn btn-sm btn-success" onclick="openVerificationModal('birth_certificate', '{{ $studentDetail->birth_certificate_verification_status }}')">
                                                <i class="ti ti-check me-1"></i>Verify
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="col-12 col-md-6">
                                <div class="document-card">
                                    <div class="document-icon">
                                        <i class="ti ti-photo text-warning"></i>
                                    </div>
                                    <div class="document-content">
                                        <div class="document-info">
                                            <label class="document-label">Passport Photo</label>
                                            <div class="verification-status">
                                                <span class="badge bg-{{ $studentDetail->passport_photo_verification_status === 'verified' ? 'success' : 'warning' }}" data-document-type="passport_photo">
                                                    {{ ucfirst($studentDetail->passport_photo_verification_status ?? 'pending') }}
                                                </span>
                                                @if($studentDetail->passport_photo_verified_at)
                                                <small class="text-muted d-block">
                                                    Verified by: {{ $studentDetail->passportPhotoVerifiedBy->name ?? 'Unknown' }}<br>
                                                    Date: {{ $studentDetail->passport_photo_verified_at->format('M d, Y h:i A') }}
                                                </small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="document-actions">
                                            <a href="{{ Storage::url($studentDetail->passport_photo) }}" target="_blank" class="btn btn-sm btn-outline-warning">
                                                <i class="ti ti-eye me-1"></i>View
                                            </a>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                                            <button class="btn btn-sm btn-success" onclick="openVerificationModal('passport_photo', '{{ $studentDetail->passport_photo_verification_status }}')">
                                                <i class="ti ti-check me-1"></i>Verify
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Show Aadhaar Front and Back for all courses if uploaded --}}
                            @if($studentDetail->adhar_front)
                            <div class="col-12 col-md-6">
                                <div class="document-card">
                                    <div class="document-icon">
                                        <i class="ti ti-id text-danger"></i>
                                    </div>
                                    <div class="document-content">
                                        <div class="document-info">
                                            <label class="document-label">Aadhar Front</label>
                                            <div class="verification-status">
                                                <span class="badge bg-{{ $studentDetail->adhar_front_verification_status === 'verified' ? 'success' : 'warning' }}" data-document-type="adhar_front">
                                                    {{ ucfirst($studentDetail->adhar_front_verification_status ?? 'pending') }}
                                                </span>
                                                @if($studentDetail->adhar_front_verified_at)
                                                <small class="text-muted d-block">
                                                    Verified by: {{ $studentDetail->adharFrontVerifiedBy->name ?? 'Unknown' }}<br>
                                                    Date: {{ $studentDetail->adhar_front_verified_at->format('M d, Y h:i A') }}
                                                </small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="document-actions">
                                            <a href="{{ Storage::url($studentDetail->adhar_front) }}" target="_blank" class="btn btn-sm btn-outline-danger">
                                                <i class="ti ti-eye me-1"></i>View
                                            </a>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                                            <button class="btn btn-sm btn-success" onclick="openVerificationModal('adhar_front', '{{ $studentDetail->adhar_front_verification_status }}')">
                                                <i class="ti ti-check me-1"></i>Verify
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($studentDetail->adhar_back)
                            <div class="col-12 col-md-6">
                                <div class="document-card">
                                    <div class="document-icon">
                                        <i class="ti ti-id text-secondary"></i>
                                    </div>
                                    <div class="document-content">
                                        <div class="document-info">
                                            <label class="document-label">Aadhar Back</label>
                                            <div class="verification-status">
                                                <span class="badge bg-{{ $studentDetail->adhar_back_verification_status === 'verified' ? 'success' : 'warning' }}" data-document-type="adhar_back">
                                                    {{ ucfirst($studentDetail->adhar_back_verification_status ?? 'pending') }}
                                                </span>
                                                @if($studentDetail->adhar_back_verified_at)
                                                <small class="text-muted d-block">
                                                    Verified by: {{ $studentDetail->adharBackVerifiedBy->name ?? 'Unknown' }}<br>
                                                    Date: {{ $studentDetail->adhar_back_verified_at->format('M d, Y h:i A') }}
                                                </small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="document-actions">
                                            <a href="{{ Storage::url($studentDetail->adhar_back) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                <i class="ti ti-eye me-1"></i>View
                                            </a>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                                            <button class="btn btn-sm btn-success" onclick="openVerificationModal('adhar_back', '{{ $studentDetail->adhar_back_verification_status }}')">
                                                <i class="ti ti-check me-1"></i>Verify
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- Show signature for all courses if uploaded --}}
                            @if($studentDetail->signature)
                            <div class="col-12 col-md-6">
                                <div class="document-card">
                                    <div class="document-icon">
                                        <i class="ti ti-signature text-dark"></i>
                                    </div>
                                    <div class="document-content">
                                        <div class="document-info">
                                            <label class="document-label">Signature</label>
                                            <div class="verification-status">
                                                <span class="badge bg-{{ $studentDetail->signature_verification_status === 'verified' ? 'success' : 'warning' }}" data-document-type="signature">
                                                    {{ ucfirst($studentDetail->signature_verification_status ?? 'pending') }}
                                                </span>
                                                @if($studentDetail->signature_verified_at)
                                                <small class="text-muted d-block">
                                                    Verified by: {{ $studentDetail->signatureVerifiedBy->name ?? 'Unknown' }}<br>
                                                    Date: {{ $studentDetail->signature_verified_at->format('M d, Y h:i A') }}
                                                </small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="document-actions">
                                            <a href="{{ Storage::url($studentDetail->signature) }}" target="_blank" class="btn btn-sm btn-outline-dark">
                                                <i class="ti ti-eye me-1"></i>View
                                            </a>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                                            <button class="btn btn-sm btn-success" onclick="openVerificationModal('signature', '{{ $studentDetail->signature_verification_status }}')">
                                                <i class="ti ti-check me-1"></i>Verify
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- Show Other Document for all courses --}}
                            @if($studentDetail->other_document)
                            <div class="col-12 col-md-6">
                                <div class="document-card">
                                    <div class="document-icon">
                                        <i class="ti ti-file text-info"></i>
                                    </div>
                                    <div class="document-content">
                                        <div class="document-info">
                                            <label class="document-label">Other Document</label>
                                            <div class="verification-status">
                                                <span class="badge bg-{{ $studentDetail->other_document_verification_status === 'verified' ? 'success' : 'warning' }}" data-document-type="other_document">
                                                    {{ ucfirst($studentDetail->other_document_verification_status ?? 'pending') }}
                                                </span>
                                                @if($studentDetail->other_document_verified_at)
                                                <small class="text-muted d-block">
                                                    Verified by: {{ $studentDetail->otherDocumentVerifiedBy->name ?? 'Unknown' }}<br>
                                                    Date: {{ $studentDetail->other_document_verified_at->format('M d, Y h:i A') }}
                                                </small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="document-actions">
                                            <a href="{{ Storage::url($studentDetail->other_document) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                                <i class="ti ti-eye me-1"></i>View
                                            </a>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                                            <button class="btn btn-sm btn-success" onclick="openVerificationModal('other_document', '{{ $studentDetail->other_document_verification_status ?? 'pending' }}')">
                                                <i class="ti ti-check me-1"></i>Verify
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                            {{-- Show upload option if other_document is missing (for all courses) --}}
                            <div class="col-12 col-md-6">
                                <div class="document-card border-dashed">
                                    <div class="document-icon">
                                        <i class="ti ti-file-upload text-muted"></i>
                                    </div>
                                    <div class="document-content">
                                        <div class="document-info">
                                            <label class="document-label">Other Document</label>
                                            <div class="verification-status">
                                                <span class="badge bg-secondary">Not Uploaded</span>
                                            </div>
                                        </div>
                                        <div class="document-actions">
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                                            <button class="btn btn-sm btn-primary w-100 w-md-auto" onclick="openUploadOtherDocumentModal()">
                                                <i class="ti ti-upload me-1"></i><span class="d-none d-md-inline">Upload </span>Document
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- Show Post Graduation Certificate if uploaded --}}
                            @if($studentDetail->post_graduation_certificate)
                            <div class="col-12 col-md-6">
                                <div class="document-card">
                                    <div class="document-icon">
                                        <i class="ti ti-file-certificate text-success"></i>
                                    </div>
                                    <div class="document-content">
                                        <div class="document-info">
                                            <label class="document-label">Post-Graduation Certificate</label>
                                            <div class="verification-status">
                                                <span class="badge bg-{{ $studentDetail->post_graduation_certificate_verification_status === 'verified' ? 'success' : 'warning' }}" data-document-type="post_graduation_certificate">
                                                    {{ ucfirst($studentDetail->post_graduation_certificate_verification_status ?? 'pending') }}
                                                </span>
                                                @if($studentDetail->post_graduation_certificate_verified_at)
                                                <small class="text-muted d-block">
                                                    Verified by: {{ $studentDetail->postGraduationCertificateVerifiedBy->name ?? 'Unknown' }}<br>
                                                    Date: {{ $studentDetail->post_graduation_certificate_verified_at->format('M d, Y h:i A') }}
                                                </small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="document-actions">
                                            <a href="{{ Storage::url($studentDetail->post_graduation_certificate) }}" target="_blank" class="btn btn-sm btn-outline-success">
                                                <i class="ti ti-eye me-1"></i>View
                                            </a>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_telecaller() || \App\Helpers\RoleHelper::is_senior_manager() || \App\Helpers\RoleHelper::is_general_manager())
                                            <button class="btn btn-sm btn-success" onclick="openVerificationModal('post_graduation_certificate', '{{ $studentDetail->post_graduation_certificate_verification_status ?? 'pending' }}')">
                                                <i class="ti ti-check me-1"></i>Verify
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if($studentDetail->message)
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="ti ti-message me-2"></i>Additional Message
                            </h6>
                            <p class="mb-0">{{ $studentDetail->message }}</p>
                        </div>
                    </div>
                </div>
                @endif

                @if($studentDetail->status === 'rejected' && $studentDetail->admin_remarks)
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="alert alert-danger">
                            <h6 class="alert-heading">
                                <i class="ti ti-alert-circle me-2"></i>Rejection Remark
                            </h6>
                            <p class="mb-0">{{ $studentDetail->admin_remarks }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Document Verification Modal -->
<div class="modal fade" id="verificationModal" tabindex="-1" aria-labelledby="verificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verificationModalLabel">Document Verification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="verificationForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="lead_detail_id" name="lead_detail_id" value="{{ $studentDetail->id }}">
                    <input type="hidden" id="document_type" name="document_type">

                    <div class="mb-3">
                        <label for="verification_status" class="form-label">Verification Status</label>
                        <select class="form-select" id="verification_status" name="verification_status" required>
                            <option value="pending">Pending</option>
                            <option value="verified">Verified</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="need_to_change_document">
                            <label class="form-check-label" for="need_to_change_document">
                                Need to change document
                            </label>
                        </div>
                    </div>

                    <div class="mb-3" id="file_upload_section" style="display: none;">
                        <label for="new_file" class="form-label">Upload New File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="new_file" name="new_file" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Upload a new file (Max 1MB)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Verification</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Upload Other Document Modal -->
<div class="modal fade" id="uploadOtherDocumentModal" tabindex="-1" aria-labelledby="uploadOtherDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadOtherDocumentModalLabel">
                    <i class="ti ti-upload me-2"></i>Upload Other Document
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="uploadOtherDocumentForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="lead_detail_id" value="{{ $studentDetail->id }}">
                    <input type="hidden" name="document_type" value="other_document">
                    <input type="hidden" name="verification_status" value="pending">
                    <input type="hidden" name="need_to_change_document" value="1">

                    <div class="mb-3">
                        <label for="other_document_file" class="form-label">Upload Other Document <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="other_document_file" name="new_file" accept=".pdf,.jpg,.jpeg,.png" required>
                        <small class="text-muted d-block mt-1">PDF, JPG, PNG (Max 2MB)</small>
                        <div id="other_document_file_preview" class="mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-upload me-1"></i>Upload Document
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SSLC Certificate Verification Modal -->
<div class="modal fade" id="sslcVerificationModal" tabindex="-1" aria-labelledby="sslcVerificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sslcVerificationModalLabel">SSLC Certificate Verification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="sslcVerificationForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="sslc_certificate_id" name="sslc_certificate_id">
                    <input type="hidden" id="lead_detail_id" name="lead_detail_id" value="{{ $studentDetail->id }}">

                    <div class="mb-3">
                        <label for="sslc_verification_status" class="form-label">Verification Status</label>
                        <select class="form-select" id="sslc_verification_status" name="verification_status" required>
                            <option value="pending">Pending</option>
                            <option value="verified">Verified</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="sslc_verification_notes" class="form-label">Verification Notes</label>
                        <textarea class="form-control" id="sslc_verification_notes" name="verification_notes" rows="3" placeholder="Enter verification notes..."></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="sslc_need_to_change_document">
                            <label class="form-check-label" for="sslc_need_to_change_document">
                                Need to change document
                            </label>
                        </div>
                    </div>

                    <div id="sslc_file_upload_section" style="display: none;">
                        <div class="mb-3">
                            <label for="sslc_new_file" class="form-label">Upload New Document</label>
                            <input type="file" class="form-control" id="sslc_new_file" name="new_file" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text">PDF, JPG, PNG files only (Max 2MB)</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Verification</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add SSLC Certificate Modal -->
<div class="modal fade" id="addSSLCModal" tabindex="-1" aria-labelledby="addSSLCModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSSLCModalLabel">Add SSLC Certificate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addSSLCForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="add_lead_detail_id" name="lead_detail_id" value="{{ $studentDetail->id }}">

                    <div class="mb-3">
                        <label for="add_sslc_certificates" class="form-label">SSLC Certificates <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="add_sslc_certificates" name="certificates[]" accept=".pdf,.jpg,.jpeg,.png" multiple required>
                        <div class="form-text">PDF, JPG, PNG files only (Max 2MB each) - You can upload multiple files</div>
                    </div>

                    <div class="file-preview" id="add_sslc_preview"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Certificate(s)</button>
                </div>
            </form>
        </div>
    </div>
</div>

@else
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="ti ti-file-x f-48 text-muted mb-3"></i>
                <h5 class="text-muted">No Registration Details Found</h5>
                <p class="text-muted">This lead has not submitted any registration form yet.</p>
                <a href="{{ route('leads.registration-form-submitted') }}" class="btn btn-primary">
                    <i class="ti ti-arrow-left me-2"></i>Back to Registration Form Submitted Leads
                </a>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('styles')
<style>
    .info-card {
        display: flex;
        align-items: center;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 10px;
        border-left: 4px solid #007bff;
        transition: all 0.3s ease;
    }

    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .info-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: rgba(0, 123, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        flex-shrink: 0;
    }

    .info-icon i {
        font-size: 1.5rem;
    }

    .info-content {
        flex-grow: 1;
    }

    .info-label {
        font-size: 0.875rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
        font-weight: 500;
    }

    .info-value {
        font-size: 1rem;
        color: #212529;
        margin-bottom: 0;
        font-weight: 600;
    }

    .document-card {
        display: flex;
        align-items: center;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 10px;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
        width: 100%;
        box-sizing: border-box;
    }

    .document-card.border-dashed {
        border: 2px dashed #dee2e6;
        background: #f8f9fa;
    }

    .document-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border-color: #007bff;
    }

    .document-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: rgba(0, 123, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        flex-shrink: 0;
    }

    .document-icon i {
        font-size: 1.5rem;
    }

    .document-content {
        flex-grow: 1;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        min-width: 0;
        /* Prevents flex items from overflowing */
    }

    .document-info {
        flex-grow: 1;
        min-width: 0;
        /* Allows text to wrap */
    }

    .document-label {
        font-size: 0.875rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
        font-weight: 500;
        word-wrap: break-word;
    }

    .verification-status {
        margin-bottom: 0.5rem;
    }

    .document-actions {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        flex-shrink: 0;
        min-width: fit-content;
    }

    .document-actions .btn {
        white-space: nowrap;
    }

    .registration-details-container .nav-tabs .nav-link {
        border: none;
        border-radius: 10px 10px 0 0;
        margin-right: 0.25rem;
        background: #f8f9fa;
        color: #6c757d;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .registration-details-container .nav-tabs .nav-link:hover {
        background: #e9ecef;
        color: #495057;
    }

    .registration-details-container .nav-tabs .nav-link.active {
        background: #007bff;
        color: white;
        border-color: #007bff;
    }

    .tab-content {
        background: white;
        border-radius: 0 10px 10px 10px;
        padding: 1.5rem;
        min-height: 400px;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
    }

    .card {
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
    }

    /* Mobile Responsive Styles */
    @media (max-width: 768px) {

        .info-card,
        .document-card {
            flex-direction: column;
            text-align: center;
            padding: 0.75rem;
            width: 100%;
            max-width: 100%;
        }

        .info-icon,
        .document-icon {
            margin-right: 0;
            margin-bottom: 0.75rem;
            width: 40px;
            height: 40px;
        }

        .info-icon i,
        .document-icon i {
            font-size: 1.25rem;
        }

        .document-content {
            flex-direction: column;
            gap: 0.75rem;
            align-items: stretch;
            width: 100%;
        }

        .document-info {
            width: 100%;
            text-align: center;
        }

        .document-actions {
            flex-direction: row;
            justify-content: center;
            gap: 0.5rem;
            flex-wrap: wrap;
            width: 100%;
        }

        .document-actions .btn {
            flex: 1 1 auto;
            min-width: 0;
            max-width: 100%;
            white-space: normal;
            word-wrap: break-word;
        }

        .responsive-tabs {
            flex-wrap: wrap;
        }

        .responsive-tabs .nav-link {
            margin-bottom: 0.25rem;
            font-size: 0.875rem;
            padding: 0.5rem 0.75rem;
        }

        .tab-content {
            padding: 1rem;
            min-height: 300px;
        }

        .card-body {
            padding: 1rem;
        }

        .page-header .row {
            flex-direction: column;
        }

        .page-header .col-md-6:last-child {
            margin-top: 1rem;
        }

        .breadcrumb {
            justify-content: flex-start !important;
        }
    }

    /* Upload modal responsive styles */
    @media (max-width: 768px) {
        #uploadOtherDocumentModal .modal-dialog {
            margin: 0.5rem;
            max-width: calc(100% - 1rem);
        }

        #uploadOtherDocumentModal .modal-footer {
            flex-direction: column;
            gap: 0.5rem;
        }

        #uploadOtherDocumentModal .modal-footer .btn {
            width: 100%;
            margin: 0;
        }
    }

    /* Spinning loader animation */
    .spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    @media (max-width: 576px) {

        .info-card,
        .document-card {
            padding: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .responsive-tabs .nav-link {
            font-size: 0.8rem;
            padding: 0.4rem 0.6rem;
        }

        .btn {
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
        }

        .btn-sm {
            font-size: 0.75rem;
            padding: 0.375rem 0.625rem;
            white-space: normal;
            word-wrap: break-word;
        }

        .modal-dialog {
            margin: 0.5rem;
        }

        .document-content {
            gap: 0.5rem;
        }

        .document-actions {
            flex-direction: column;
            width: 100%;
            gap: 0.5rem;
        }

        .document-actions .btn {
            width: 100%;
            max-width: 100%;
            white-space: normal;
            word-wrap: break-word;
        }

        /* Ensure document cards don't overflow */
        .col-12.col-md-6 {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
    }

    /* Checkbox group styling for selected courses */
    .checkbox-group {
        padding: 0.5rem;
        background: #f8f9fa;
        border-radius: 0.375rem;
        border: 1px solid #dee2e6;
    }

    .checkbox-group .form-check {
        margin-bottom: 0.25rem;
    }

    .checkbox-group .form-check:last-child {
        margin-bottom: 0;
    }

    .checkbox-group .form-check-label {
        margin-left: 0.5rem;
        font-weight: 500;
    }

    .edit-form {
        width: 100%;
    }
</style>
@endpush

@push('scripts')
<script>
    const offlinePlaceOptions = {!! json_encode($offlinePlaceOptions ?? []) !!};

    function buildLocationSelectHtml(field, currentValue) {
        let optionsHtml = '<option value="">Select Location</option>';
        Object.keys(offlinePlaceOptions).forEach(function(name) {
            const selected = currentValue === name ? 'selected' : '';
            optionsHtml += `<option value="${name}" ${selected}>${name}</option>`;
        });
        return `<select name="${field}" class="form-select form-select-sm">${optionsHtml}</select>`;
    }
</script>
<script>
    // Handle Upload Other Document form submission
    document.addEventListener('DOMContentLoaded', function() {
        const uploadOtherDocumentForm = document.getElementById('uploadOtherDocumentForm');
        if (uploadOtherDocumentForm) {
            // File preview handler
            const fileInput = document.getElementById('other_document_file');
            const previewDiv = document.getElementById('other_document_file_preview');

            if (fileInput && previewDiv) {
                fileInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        // Validate file size (2MB)
                        if (file.size > 2 * 1024 * 1024) {
                            toast_error('File size must not exceed 2MB.');
                            e.target.value = '';
                            previewDiv.innerHTML = '';
                            return;
                        }

                        // Validate file type
                        const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
                        if (!allowedTypes.includes(file.type)) {
                            toast_error('Invalid file type. Please upload PDF, JPG, or PNG files only.');
                            e.target.value = '';
                            previewDiv.innerHTML = '';
                            return;
                        }

                        // Show file preview
                        const fileSize = (file.size / 1024 / 1024).toFixed(2);
                        previewDiv.innerHTML = `
                        <div class="alert alert-info mb-0">
                            <i class="ti ti-file me-2"></i>
                            <strong>Selected:</strong> ${file.name} (${fileSize} MB)
                        </div>
                    `;
                    } else {
                        previewDiv.innerHTML = '';
                    }
                });
            }

            // Form submission handler
            uploadOtherDocumentForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const fileInput = document.getElementById('other_document_file');
                if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                    toast_error('Please select a file to upload.');
                    return;
                }

                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                if (!submitBtn) {
                    toast_error('Submit button not found.');
                    return;
                }

                const originalText = submitBtn.innerHTML;

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="ti ti-loader-2 spin me-1"></i> Uploading...';

                fetch('{{ route("leads.update-document-verification") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            toast_success(data.message || 'Document uploaded successfully!');
                            const modalElement = document.getElementById('uploadOtherDocumentModal');
                            if (modalElement) {
                                const modal = bootstrap.Modal.getInstance(modalElement);
                                if (modal) {
                                    modal.hide();
                                }
                            }
                            // Reload page to show uploaded document
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            toast_error(data.message || 'Failed to upload document. Please try again.');
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        toast_error('An error occurred while uploading the document. Please try again.');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    });
            });

            // Reset form when modal is hidden
            const modalElement = document.getElementById('uploadOtherDocumentModal');
            if (modalElement) {
                modalElement.addEventListener('hidden.bs.modal', function() {
                    uploadOtherDocumentForm.reset();
                    const previewDiv = document.getElementById('other_document_file_preview');
                    if (previewDiv) {
                        previewDiv.innerHTML = '';
                    }
                });
            }
        }
    });

    // Handle tab navigation on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Try URL parameter first, then localStorage
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab') || localStorage.getItem('activeTab');

        if (tabParam) {
            // Wait a bit for Bootstrap to be fully loaded
            setTimeout(() => {
                // Try to find tab by data-bs-target first, then by href
                let tabElement = document.querySelector(`[data-bs-target="#${tabParam}"]`);
                if (!tabElement) {
                    tabElement = document.querySelector(`[href="#${tabParam}"]`);
                }

                if (tabElement) {
                    // Remove active class from all tabs
                    document.querySelectorAll('.registration-details-container .nav-link').forEach(tab => {
                        tab.classList.remove('active');
                    });
                    document.querySelectorAll('.registration-details-container .tab-pane').forEach(pane => {
                        pane.classList.remove('active', 'show');
                    });

                    // Activate the target tab
                    tabElement.classList.add('active');

                    // Get the target pane
                    let targetPane = document.querySelector(`#${tabParam}`);
                    if (!targetPane) {
                        // Fallback: try to get from data-bs-target or href
                        const target = tabElement.getAttribute('data-bs-target') || tabElement.getAttribute('href');
                        if (target) {
                            targetPane = document.querySelector(target);
                        }
                    }

                    if (targetPane) {
                        targetPane.classList.add('active', 'show');
                    }
                }
            }, 100);
        }

        // Store active tab when user clicks on tabs
        document.querySelectorAll('.registration-details-container .nav-link[data-bs-toggle="tab"]').forEach(tab => {
            tab.addEventListener('click', function() {
                let tabId = 'personal';

                // Check if it has data-bs-target attribute (Bootstrap 5)
                const target = this.getAttribute('data-bs-target');
                if (target) {
                    tabId = target.substring(1); // Remove the # symbol
                } else {
                    // Fallback to href attribute if present
                    const href = this.getAttribute('href');
                    if (href) {
                        tabId = href.substring(1);
                    }
                }

                localStorage.setItem('activeTab', tabId);
            });
        });
    });

    function updateStatus(status) {
        if (confirm(`Are you sure you want to ${status} this registration?`)) {
            // Add your status update logic here
            console.log('Updating status to:', status);
            // You can implement AJAX call to update the status
        }
    }

    function openUploadOtherDocumentModal() {
        const fileInput = document.getElementById('other_document_file');
        if (fileInput) {
            fileInput.value = '';
        }

        const modalElement = document.getElementById('uploadOtherDocumentModal');
        if (modalElement) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        } else {
            console.error('Upload modal not found');
            toast_error('Upload modal could not be opened. Please refresh the page.');
        }
    }

    function openVerificationModal(documentType, currentStatus) {
        document.getElementById('document_type').value = documentType;
        document.getElementById('verification_status').value = currentStatus || 'pending';
        document.getElementById('need_to_change_document').checked = false;
        document.getElementById('new_file').value = '';
        document.getElementById('file_upload_section').style.display = 'none';

        const modal = new bootstrap.Modal(document.getElementById('verificationModal'));
        modal.show();
    }

    function openSSLCVerificationModal(certificateId, currentStatus) {
        document.getElementById('sslc_certificate_id').value = certificateId;
        document.getElementById('sslc_verification_status').value = currentStatus || 'pending';
        document.getElementById('sslc_need_to_change_document').checked = false;
        document.getElementById('sslc_new_file').value = '';
        document.getElementById('sslc_file_upload_section').style.display = 'none';

        const modal = new bootstrap.Modal(document.getElementById('sslcVerificationModal'));
        modal.show();
    }

    // Handle checkbox change
    document.getElementById('need_to_change_document').addEventListener('change', function() {
        const fileUploadSection = document.getElementById('file_upload_section');
        const newFileInput = document.getElementById('new_file');

        if (this.checked) {
            fileUploadSection.style.display = 'block';
            newFileInput.required = true;
        } else {
            fileUploadSection.style.display = 'none';
            newFileInput.required = false;
            newFileInput.value = '';
        }
    });

    // Handle SSLC checkbox change
    document.getElementById('sslc_need_to_change_document').addEventListener('change', function() {
        const fileUploadSection = document.getElementById('sslc_file_upload_section');
        const newFileInput = document.getElementById('sslc_new_file');

        if (this.checked) {
            fileUploadSection.style.display = 'block';
            newFileInput.required = true;
        } else {
            fileUploadSection.style.display = 'none';
            newFileInput.required = false;
            newFileInput.value = '';
        }
    });

    // Handle verification form submission
    document.getElementById('verificationForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const needToChangeDocument = document.getElementById('need_to_change_document').checked;
        const newFile = document.getElementById('new_file');
        const submitBtn = this.querySelector('button[type="submit"]');

        // Validate file upload requirement
        if (needToChangeDocument && !newFile.files.length) {
            toast_error('Please upload a new file when "Need to change document" is checked.');
            return;
        }

        // Show loading state
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="ti ti-loader-2"></i> Updating...';

        const formData = new FormData(this);

        // Ensure need_to_change_document is sent as 1 or 0
        const needToChangeDoc = document.getElementById('need_to_change_document').checked;
        formData.set('need_to_change_document', needToChangeDoc ? '1' : '0');

        // Debug: Log form data
        console.log('Form data being sent:');
        for (let [key, value] of formData.entries()) {
            console.log(key, value);
        }

        fetch('{{ route("leads.update-document-verification") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    // Show success toast
                    toast_success(data.message);

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('verificationModal'));
                    modal.hide();

                    // Reload page to show updated verification status and refresh approve functionality
                    setTimeout(() => {
                        // Get current active tab and store it
                        const activeTab = document.querySelector('.registration-details-container .nav-link.active');
                        let activeTabId = 'personal';

                        if (activeTab) {
                            // Check if it has data-bs-target attribute (Bootstrap 5)
                            const target = activeTab.getAttribute('data-bs-target');
                            if (target) {
                                activeTabId = target.substring(1); // Remove the # symbol
                            } else {
                                // Fallback to href attribute if present
                                const href = activeTab.getAttribute('href');
                                if (href) {
                                    activeTabId = href.substring(1);
                                }
                            }
                        }

                        localStorage.setItem('activeTab', activeTabId);

                        // Reload the page
                        window.location.reload();
                    }, 1000);
                } else {
                    toast_error(data.message);
                }

                // Reset button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            })
            .catch(error => {
                console.error('Error:', error);
                toast_error('An error occurred while updating verification.');

                // Reset button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
    });

    // Handle SSLC verification form submission
    document.getElementById('sslcVerificationForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const needToChangeDocument = document.getElementById('sslc_need_to_change_document').checked;
        const newFileInput = document.getElementById('sslc_new_file');

        if (needToChangeDocument && (!newFileInput.files || newFileInput.files.length === 0)) {
            toast_error('Please select a new file to upload.');
            return;
        }

        const formData = new FormData(this);

        // Ensure need_to_change_document is sent as 1 or 0
        const needToChangeDoc = document.getElementById('sslc_need_to_change_document').checked;
        formData.set('need_to_change_document', needToChangeDoc ? '1' : '0');

        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';

        fetch('{{ route("leads.verify-sslc-certificate") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toast_success(data.message);

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('sslcVerificationModal'));
                    modal.hide();

                    // Reload page to show updated verification status
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    toast_error(data.message);
                }

                // Reset button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            })
            .catch(error => {
                console.error('Error:', error);
                toast_error('An error occurred while updating verification.');

                // Reset button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
    });

    function updateVerificationStatus(updatedData) {
        // Get the document type that was updated
        const documentType = document.getElementById('document_type').value;
        const verificationStatus = document.getElementById('verification_status').value;

        // Find all verification status badges for this document type
        const statusBadges = document.querySelectorAll(`[data-document-type="${documentType}"]`);

        statusBadges.forEach(badge => {
            // Update the badge text and class
            if (verificationStatus === 'verified') {
                badge.textContent = 'Verified';
                badge.className = 'badge bg-success';
            } else {
                badge.textContent = 'Pending';
                badge.className = 'badge bg-warning';
            }
        });

        // Update verification buttons to show current status
        const verifyButtons = document.querySelectorAll(`button[onclick*="${documentType}"]`);
        verifyButtons.forEach(button => {
            const onclickAttr = button.getAttribute('onclick');
            const newOnclick = onclickAttr.replace(/'.*?'/, `'${verificationStatus}'`);
            button.setAttribute('onclick', newOnclick);
        });

        console.log(`Updated verification status for ${documentType} to ${verificationStatus}`);
    }

    // Inline editing functionality
    let inlineEditInitialized = false;

    document.addEventListener('DOMContentLoaded', function() {
        if (!inlineEditInitialized) {
            inlineEditInitialized = true;

            // Handle regular field editing
            document.addEventListener('click', function(e) {
                if (e.target.closest('.edit-field')) {
                    e.preventDefault();
                    const button = e.target.closest('.edit-field');
                    const infoValue = button.closest('.info-value');
                    const field = button.dataset.field || infoValue.dataset.field;
                    const leadDetailId = button.dataset.leadDetailId || infoValue.dataset.leadDetailId;
                    const courseId = button.dataset.courseId || infoValue.dataset.courseId;
                    const currentId = button.dataset.currentId || infoValue.dataset.currentId || '';
                    // Get the value from data attribute or text content
                    let currentValue = infoValue.dataset.value || infoValue.textContent.trim().replace(/\s*Edit\s*$/, '').trim();

                    // Debug logging
                    console.log('Field:', field);
                    console.log('Current value:', currentValue);
                    console.log('Course ID:', courseId);
                    console.log('Current ID:', currentId);

                    // Create edit form
                    if (['subject_id', 'batch_id', 'sub_course_id'].includes(field)) {
                        createCourseDependentEditForm(field, courseId, currentId, leadDetailId, infoValue);
                    } else if (field === 'class_time_id') {
                        // Handle class time field separately (will be handled by edit-class-time-field handler)
                        return;
                    } else {
                        // Check if field has field-type attribute for select fields
                        const fieldType = button.dataset.fieldType;
                        if (fieldType === 'select') {
                            const options = JSON.parse(button.dataset.options || '{}');
                            let optionsHtml = '';
                            Object.entries(options).forEach(([val, label]) => {
                                optionsHtml += `<option value="${val}" ${currentValue == val ? 'selected' : ''}>${label}</option>`;
                            });
                            const editForm = `
                            <div class="edit-form">
                                <select name="${field}" class="form-select form-select-sm">
                                    ${optionsHtml}
                                </select>
                                <div class="btn-group mt-1">
                                    <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                                    <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                                </div>
                            </div>
                        `;
                            infoValue.innerHTML = editForm;
                        } else {
                            const editForm = createEditForm(field, currentValue, leadDetailId);
                            infoValue.innerHTML = editForm;
                        }

                        // Focus on input
                        const input = infoValue.querySelector('input, textarea, select');
                        if (input) input.focus();
                    }
                }
            });

            // Handle phone field editing
            document.addEventListener('click', function(e) {
                if (e.target.closest('.edit-phone-field')) {
                    e.preventDefault();
                    const button = e.target.closest('.edit-phone-field');
                    const infoValue = button.closest('.info-value');
                    const field = infoValue.dataset.field;
                    const leadDetailId = infoValue.dataset.leadDetailId;
                    const phoneCode = infoValue.dataset.phoneCode;
                    const phoneNumber = infoValue.dataset.phoneNumber;

                    // Create phone edit form
                    const editForm = createPhoneEditForm(field, phoneCode, phoneNumber, leadDetailId);
                    infoValue.innerHTML = editForm;

                    // Focus on phone number input
                    const phoneInput = infoValue.querySelector('input[name="phone_number"]');
                    if (phoneInput) phoneInput.focus();
                }
            });

            // Handle class time field editing
            document.addEventListener('click', function(e) {
                if (e.target.closest('.edit-class-time-field')) {
                    e.preventDefault();
                    const button = e.target.closest('.edit-class-time-field');
                    const infoValue = button.closest('.info-value');
                    const field = infoValue.dataset.field;
                    const leadDetailId = infoValue.dataset.leadDetailId;
                    const courseId = infoValue.dataset.courseId;
                    const currentId = infoValue.dataset.currentId || '';

                    // Check if course needs time
                    fetch(`/api/courses/${courseId}/needs-time`)
                        .then(response => response.json())
                        .then(data => {
                            if (!data.needs_time) {
                                toast_error('This course does not require class time.');
                                return;
                            }

                            // Fetch class times for the course
                            fetch(`/api/class-times/by-course/${courseId}`)
                                .then(response => response.json())
                                .then(classTimes => {
                                    let options = '<option value="">Select Class Time</option>';
                                    if (classTimes && classTimes.length > 0) {
                                        classTimes.forEach(ct => {
                                            const fromTime = new Date('1970-01-01T' + ct.from_time).toLocaleTimeString('en-US', {
                                                hour: '2-digit',
                                                minute: '2-digit',
                                                hour12: true
                                            });
                                            const toTime = new Date('1970-01-01T' + ct.to_time).toLocaleTimeString('en-US', {
                                                hour: '2-digit',
                                                minute: '2-digit',
                                                hour12: true
                                            });
                                            options += `<option value="${ct.id}" ${ct.id == currentId ? 'selected' : ''}>${fromTime} - ${toTime}</option>`;
                                        });
                                    }

                                    const editForm = `
                                    <div class="edit-form">
                                        <select name="${field}" class="form-select form-select-sm">
                                            ${options}
                                        </select>
                                        <div class="btn-group mt-1">
                                            <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                                            <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                                        </div>
                                    </div>
                                `;
                                    infoValue.innerHTML = editForm;

                                    // Focus on select
                                    const select = infoValue.querySelector('select');
                                    if (select) select.focus();
                                })
                                .catch(error => {
                                    console.error('Error fetching class times:', error);
                                    toast_error('Error loading class times. Please try again.');
                                });
                        })
                        .catch(error => {
                            console.error('Error checking course needs_time:', error);
                            toast_error('Error checking course settings. Please try again.');
                        });
                }
            });

            // Handle save and cancel buttons
            document.addEventListener('click', function(e) {
                if (e.target.closest('.save-edit')) {
                    e.preventDefault();
                    const button = e.target.closest('.save-edit');
                    const infoValue = button.closest('.info-value');
                    const field = infoValue.dataset.field;
                    const leadDetailId = infoValue.dataset.leadDetailId;

                    let value = '';
                    if (field.includes('phone') || field === 'whatsapp') {
                        const code = infoValue.querySelector('select[name="code"]').value;
                        const number = infoValue.querySelector('input[name="phone_number"]').value;
                        value = code + '|' + number;
                    } else if (field === 'selected_courses') {
                        // Handle checkboxes for selected_courses
                        const checkboxes = infoValue.querySelectorAll('input[name="selected_courses[]"]:checked');
                        const selectedValues = Array.from(checkboxes).map(cb => cb.value);
                        value = selectedValues.join(', ');
                    } else {
                        const input = infoValue.querySelector('input, textarea, select');
                        value = input ? input.value : '';
                    }

                    // Store field name in infoValue for save function
                    if (!infoValue.dataset.field) {
                        infoValue.dataset.field = field;
                    }
                    if (!infoValue.dataset.leadDetailId) {
                        infoValue.dataset.leadDetailId = leadDetailId;
                    }

                    saveFieldEdit(field, value, leadDetailId, infoValue);
                }

                if (e.target.closest('.cancel-edit')) {
                    e.preventDefault();
                    const button = e.target.closest('.cancel-edit');
                    const infoValue = button.closest('.info-value');
                    const field = infoValue.dataset.field;
                    const leadDetailId = infoValue.dataset.leadDetailId;

                    // Reload the field with original value
                    location.reload();
                }
            });
        }
    });

    function createEditForm(field, currentValue, leadDetailId) {
        let inputHtml = '';

        if (field === 'date_of_birth') {
            // Use the date value directly (should be in Y-m-d format from data attribute)
            let dateValue = currentValue || '';
            console.log('Processing date_of_birth field');
            console.log('Current value for date:', currentValue);
            console.log('Date value for input:', dateValue);

            inputHtml = `<input type="date" name="${field}" value="${dateValue}" class="form-control form-control-sm">`;
        } else if (field === 'gender') {
            inputHtml = `
            <select name="${field}" class="form-select form-select-sm">
                <option value="male" ${currentValue === 'male' ? 'selected' : ''}>Male</option>
                <option value="female" ${currentValue === 'female' ? 'selected' : ''}>Female</option>
            </select>
        `;
        } else if (field === 'is_employed') {
            inputHtml = `
            <select name="${field}" class="form-select form-select-sm">
                <option value="1" ${currentValue === '1' || currentValue === 1 ? 'selected' : ''}>Yes</option>
                <option value="0" ${currentValue === '0' || currentValue === 0 ? 'selected' : ''}>No</option>
            </select>
        `;
        } else if (field === 'programme_type') {
            inputHtml = `
            <select name="${field}" class="form-select form-select-sm">
                <option value="online" ${currentValue === 'online' ? 'selected' : ''}>Online</option>
                <option value="offline" ${currentValue === 'offline' ? 'selected' : ''}>Offline</option>
            </select>
        `;
        } else if (field === 'location') {
            inputHtml = buildLocationSelectHtml(field, currentValue);
        } else if (field === 'message') {
            inputHtml = `<textarea name="${field}" class="form-control form-control-sm" rows="3">${currentValue}</textarea>`;
        } else if (field === 'selected_courses') {
            // Parse current value - could be comma-separated string or array
            let selectedArray = [];
            if (currentValue && currentValue !== 'N/A') {
                if (typeof currentValue === 'string') {
                    selectedArray = currentValue.split(',').map(s => s.trim()).filter(s => s);
                } else if (Array.isArray(currentValue)) {
                    selectedArray = currentValue;
                }
            }

            inputHtml = `
            <div class="checkbox-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selected_courses[]" value="SSLC" id="edit_course_sslc" ${selectedArray.includes('SSLC') ? 'checked' : ''}>
                    <label class="form-check-label" for="edit_course_sslc">SSLC</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selected_courses[]" value="Plus two" id="edit_course_plustwo" ${selectedArray.includes('Plus two') ? 'checked' : ''}>
                    <label class="form-check-label" for="edit_course_plustwo">Plus two</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selected_courses[]" value="UG" id="edit_course_ug" ${selectedArray.includes('UG') ? 'checked' : ''}>
                    <label class="form-check-label" for="edit_course_ug">UG</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selected_courses[]" value="PG" id="edit_course_pg" ${selectedArray.includes('PG') ? 'checked' : ''}>
                    <label class="form-check-label" for="edit_course_pg">PG</label>
                </div>
            </div>
        `;
        } else if (['sslc_back_year', 'plustwo_back_year', 'back_year', 'degree_back_year'].includes(field)) {
            // Create year dropdown from 2018 to current year
            const currentYear = new Date().getFullYear();
            let yearOptions = '<option value="">Select Back Year</option>';
            for (let year = 2018; year <= currentYear; year++) {
                const selected = currentValue == year ? 'selected' : '';
                yearOptions += `<option value="${year}" ${selected}>${year}</option>`;
            }
            inputHtml = `<select name="${field}" class="form-select form-select-sm">${yearOptions}</select>`;
        } else if (field === 'edumaster_course_name') {
            // Remove "N/A" from input value for EduMaster Course Name
            const inputValue = (currentValue && currentValue !== 'N/A') ? currentValue : '';
            inputHtml = `<input type="text" name="${field}" value="${inputValue}" class="form-control form-control-sm" placeholder="Enter course name">`;
        } else if (field === 'plustwo_subject') {
            // Remove "N/A" from input value for Plus Two Subject
            const inputValue = (currentValue && currentValue !== 'N/A') ? currentValue : '';
            inputHtml = `<input type="text" name="${field}" value="${inputValue}" class="form-control form-control-sm" placeholder="Enter Plus Two Subject">`;
        } else {
            inputHtml = `<input type="text" name="${field}" value="${currentValue}" class="form-control form-control-sm">`;
        }

        return `
        <div class="edit-form">
            ${inputHtml}
            <div class="btn-group mt-1">
                <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
            </div>
        </div>
    `;
    }

    // Function to create edit form for course-dependent fields (subject, batch, sub-course)
    function createCourseDependentEditForm(field, courseId, currentId, leadDetailId, infoValue) {
        if (!courseId) {
            toast_error('Course ID is required');
            return;
        }

        // Show loading state
        infoValue.innerHTML = '<select class="form-select form-select-sm"><option>Loading...</option></select>';

        let apiUrl = '';
        if (field === 'subject_id') {
            apiUrl = `/api/subjects/by-course/${courseId}`;
        } else if (field === 'batch_id') {
            apiUrl = `/api/batches/by-course/${courseId}`;
        } else if (field === 'sub_course_id') {
            apiUrl = `/api/sub-courses/by-course/${courseId}`;
        }

        if (!apiUrl) {
            toast_error('Invalid field');
            return;
        }

        fetch(apiUrl)
            .then(response => response.json())
            .then(data => {
                let options = '<option value="">Select ' + field.replace('_id', '').replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) + '</option>';

                // Handle different response formats
                let items = [];
                if (field === 'subject_id') {
                    items = data.subjects || data || [];
                } else if (field === 'batch_id') {
                    items = data.batches || data || [];
                } else if (field === 'sub_course_id') {
                    items = data.sub_courses || data || [];
                } else if (Array.isArray(data)) {
                    items = data;
                }

                items.forEach(item => {
                    const selected = String(currentId) === String(item.id) ? 'selected' : '';
                    options += `<option value="${item.id}" ${selected}>${item.title}</option>`;
                });

                infoValue.innerHTML = `
                <div class="edit-form">
                    <select name="${field}" class="form-select form-select-sm" data-field="${field}" data-lead-detail-id="${leadDetailId}">
                        ${options}
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;

                // Store field, leadDetailId, and courseId in infoValue for save function
                infoValue.dataset.field = field;
                infoValue.dataset.leadDetailId = leadDetailId;
                infoValue.dataset.courseId = courseId;

                // Focus on select
                const select = infoValue.querySelector('select');
                if (select) select.focus();
            })
            .catch(error => {
                console.error('Error loading options:', error);
                toast_error('Error loading options');
                location.reload();
            });
    }

    function createPhoneEditForm(field, phoneCode, phoneNumber, leadDetailId) {
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
            `<option value="${code}" ${code === phoneCode ? 'selected' : ''}>${code} - ${country}</option>`
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
                    <input type="tel" name="phone_number" value="${phoneNumber || ''}" class="form-control form-control-sm" placeholder="Phone Number">
                </div>
            </div>
            <div class="btn-group mt-1">
                <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
            </div>
        </div>
    `;
    }

    let isSubmitting = false;

    function saveFieldEdit(field, value, leadDetailId, infoValue) {
        if (isSubmitting) {
            return;
        }

        isSubmitting = true;
        const formData = new FormData();
        formData.append('lead_detail_id', leadDetailId);
        formData.append('field', field);
        formData.append('value', value);

        fetch('{{ route("leads.update-registration-details") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the display value
                    if (field.includes('phone') || field === 'whatsapp' || field === 'father_contact' || field === 'mother_contact') {
                        const [code, number] = value.split('|');
                        const displayValue = code && number ? `${code} ${number}` : 'N/A';
                        // Update data attributes for phone fields
                        if (infoValue.dataset) {
                            if (field === 'father_contact') {
                                infoValue.dataset.phoneCode = code;
                                infoValue.dataset.phoneNumber = number;
                            } else if (field === 'mother_contact') {
                                infoValue.dataset.phoneCode = code;
                                infoValue.dataset.phoneNumber = number;
                            }
                        }
                        infoValue.innerHTML = `${displayValue} <button class="btn btn-sm btn-outline-primary ms-2 edit-phone-field" title="Edit"><i class="ti ti-edit"></i></button>`;
                    } else if (field === 'class_time_id') {
                        const displayValue = data.new_value || 'N/A';
                        infoValue.innerHTML = `${displayValue} <button class="btn btn-sm btn-outline-primary ms-2 edit-class-time-field" title="Edit"><i class="ti ti-edit"></i></button>`;
                    } else {
                        const displayValue = data.new_value || value;
                        const fieldName = infoValue.dataset.field || field;
                        const leadDetailIdValue = infoValue.dataset.leadDetailId || leadDetailId;
                        const courseId = infoValue.dataset.courseId || '';
                        // Get the updated ID from the response if available, otherwise use the value we just sent
                        const updatedId = data.updated_id || value || '';

                        // Rebuild the edit button with proper data attributes
                        let editButton = '';
                        if (['subject_id', 'batch_id', 'sub_course_id'].includes(fieldName)) {
                            editButton = `<button class="btn btn-sm btn-outline-primary ms-2 edit-field" data-field="${fieldName}" data-lead-detail-id="${leadDetailIdValue}" data-course-id="${courseId}" data-current-id="${updatedId}" title="Edit"><i class="ti ti-edit"></i></button>`;
                        } else if (fieldName === 'class_time_id') {
                            editButton = `<button class="btn btn-sm btn-outline-primary ms-2 edit-class-time-field" title="Edit"><i class="ti ti-edit"></i></button>`;
                        } else if (['gender', 'is_employed', 'programme_type', 'location', 'class', 'course_type'].includes(fieldName)) {
                            // For select fields, preserve data attributes
                            const dataValue = data.new_value || value;
                            let optionsAttr = '';
                            if (fieldName === 'gender') {
                                optionsAttr = `data-options='{"male":"Male","female":"Female"}'`;
                            } else if (fieldName === 'is_employed') {
                                optionsAttr = `data-options='{"1":"Yes","0":"No"}'`;
                            } else if (fieldName === 'programme_type') {
                                optionsAttr = `data-options='{"online":"Online","offline":"Offline"}'`;
                            } else if (fieldName === 'location') {
                                optionsAttr = `data-options='${JSON.stringify(offlinePlaceOptions).replace(/'/g, '&#39;')}'`;
                            } else if (fieldName === 'class') {
                                optionsAttr = `data-options='{"sslc":"SSLC","plustwo":"Plus Two"}'`;
                            } else if (fieldName === 'course_type') {
                                optionsAttr = `data-options='{"UG":"UG","PG":"PG"}'`;
                            }
                            editButton = `<button class="btn btn-sm btn-outline-primary ms-2 edit-field" data-field="${fieldName}" data-lead-detail-id="${leadDetailIdValue}" data-field-type="select" ${optionsAttr} title="Edit"><i class="ti ti-edit"></i></button>`;
                            if (infoValue.dataset) {
                                // For class field, store the lowercase value
                                if (fieldName === 'class') {
                                    infoValue.dataset.value = value.toLowerCase();
                                } else {
                                    infoValue.dataset.value = dataValue;
                                }
                            }
                        } else {
                            editButton = `<button class="btn btn-sm btn-outline-primary ms-2 edit-field" data-field="${fieldName}" data-lead-detail-id="${leadDetailIdValue}" title="Edit"><i class="ti ti-edit"></i></button>`;
                        }

                        infoValue.innerHTML = `${displayValue} ${editButton}`;
                        if (infoValue.dataset) {
                            infoValue.dataset.value = displayValue;
                        }
                    }

                    // Handle location field visibility based on programme_type
                    if (field === 'programme_type') {
                        const locationContainer = document.querySelector('.location-field-container');
                        if (data.hide_location) {
                            // Hide location field if programme_type is changed to online
                            if (locationContainer) {
                                locationContainer.style.display = 'none';
                            }
                        } else if (data.show_location) {
                            // Show location field if programme_type is changed to offline
                            // If container doesn't exist, we need to create it
                            if (!locationContainer) {
                                // Find the programme_type field container to insert location field after it
                                const programmeTypeContainer = infoValue.closest('.col-md-6');
                                if (programmeTypeContainer) {
                                    const leadDetailId = infoValue.dataset.leadDetailId;
                                    // Check if user has permission to edit (we'll show edit button if they can edit other fields)
                                    const canEdit = infoValue.querySelector('.edit-field') !== null;
                                    const editButtonHtml = canEdit ?
                                        `<button class="btn btn-sm btn-outline-primary ms-2 edit-field" data-field-type="select" data-options='${JSON.stringify(offlinePlaceOptions).replace(/'/g, '&#39;')}' title="Edit"><i class="ti ti-edit"></i></button>` : '';

                                    const locationHtml = `
                                <div class="col-md-6 location-field-container">
                                    <div class="info-card">
                                        <div class="info-icon">
                                            <i class="ti ti-map-pin text-warning"></i>
                                        </div>
                                        <div class="info-content">
                                            <label class="info-label">Location</label>
                                            <p class="info-value" data-field="location" data-lead-detail-id="${leadDetailId}" data-value="">
                                                N/A
                                                ${editButtonHtml}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            `;
                                    programmeTypeContainer.insertAdjacentHTML('afterend', locationHtml);
                                }
                            } else {
                                // Container exists, just show it
                                locationContainer.style.display = '';
                            }
                        }
                    }

                    toast_success(data.message);
                } else {
                    toast_error(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toast_error('An error occurred while updating the field.');
            })
            .finally(() => {
                isSubmitting = false;
            });
    }

    // SSLC Certificate Management
    function removeSSLCertificate(certificateId) {
        if (confirm('Are you sure you want to remove this SSLC certificate?')) {
            const formData = new FormData();
            formData.append('certificate_id', certificateId);

            fetch('{{ route("leads.remove-sslc-certificate") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toast_success(data.message);
                        // Reload page to show updated certificates
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        toast_error(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toast_error('An error occurred while removing the certificate.');
                });
        }
    }

    function openAddSSLCModal() {
        const modal = new bootstrap.Modal(document.getElementById('addSSLCModal'));
        modal.show();
    }

    // Handle Add SSLC Certificate form submission
    let addSSLCInitialized = false;

    document.addEventListener('DOMContentLoaded', function() {
        if (!addSSLCInitialized) {
            addSSLCInitialized = true;

            const addSSLCForm = document.getElementById('addSSLCForm');
            if (addSSLCForm) {
                addSSLCForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    if (isSubmitting) {
                        return;
                    }

                    isSubmitting = true;
                    const formData = new FormData(this);
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;

                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="ti ti-loader-2"></i> Adding...';

                    fetch('{{ route("leads.add-sslc-certificate") }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                toast_success(data.message);
                                const modal = bootstrap.Modal.getInstance(document.getElementById('addSSLCModal'));
                                modal.hide();
                                // Reload page to show new certificates
                                setTimeout(() => location.reload(), 1000);
                            } else {
                                toast_error(data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            toast_error('An error occurred while adding the certificate.');
                        })
                        .finally(() => {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                            isSubmitting = false;
                        });
                });
            }
        }
    });

    // Handle file preview for Add SSLC modal
    let filePreviewInitialized = false;

    document.addEventListener('DOMContentLoaded', function() {
        if (!filePreviewInitialized) {
            filePreviewInitialized = true;

            const fileInput = document.getElementById('add_sslc_certificates');
            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    const files = e.target.files;
                    const preview = document.getElementById('add_sslc_preview');

                    if (files && files.length > 0) {
                        let html = `<div class="mt-2"><strong>${files.length} file(s) selected:</strong></div>`;

                        Array.from(files).forEach((file, index) => {
                            const fileSize = (file.size / 1024 / 1024).toFixed(2);
                            html += `
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-file me-2 text-success"></i>
                                    <div>
                                        <div class="fw-bold">${file.name}</div>
                                        <small class="text-muted">${fileSize} MB</small>
                                    </div>
                                </div>
                            </div>
                        `;
                        });

                        preview.innerHTML = html;
                        preview.style.display = 'block';
                    } else {
                        preview.style.display = 'none';
                        preview.innerHTML = '';
                    }
                });
            }
        }
    });
</script>
@endpush