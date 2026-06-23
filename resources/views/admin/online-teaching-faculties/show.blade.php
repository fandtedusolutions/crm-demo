@extends('layouts.mantis')

@section('title', 'View Online Teaching Faculty')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Online Teaching Faculty - View</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.online-teaching-faculties.index') }}">Online Teaching Faculty</a></li>
                    <li class="breadcrumb-item">View</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<div
    id="jsOnlineTeachingFacultyShowConfig"
    data-upload-url="{{ route('admin.online-teaching-faculties.upload-document', $faculty->id) }}"
    data-inline-url="{{ route('admin.online-teaching-faculties.inline-update', $faculty->id) }}"
    data-faculty-id="{{ $faculty->id }}"
    style="display: none;"
></div>

<!-- Header Card -->
<div class="row">
    <div class="col-12">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h4 class="text-white mb-1">{{ $faculty->full_name }}</h4>
                        <p class="mb-0 opacity-75">
                            <i class="ti ti-id"></i> Faculty ID: {{ $faculty->faculty_id ?? 'Not Assigned' }}
                            @if($faculty->department_name)
                                <span class="ms-3"><i class="ti ti-building"></i> {{ $faculty->department_name }}</span>
                            @endif
                        </p>
                    </div>
                    <a href="{{ route('admin.online-teaching-faculties.index') }}" class="btn btn-light btn-sm">
                        <i class="ti ti-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Personal Details Card -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="ti ti-user me-2"></i>A. Personal Details</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="text-muted small mb-1">Full Name</label>
                        {!! renderInlineEdit($faculty, 'full_name', 'text') !!}
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small mb-1">Date of Birth</label>
                        {!! renderInlineEdit($faculty, 'date_of_birth', 'date') !!}
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small mb-1">Gender</label>
                        {!! renderInlineEdit($faculty, 'gender', 'select', [''=>'N/A', 'Male'=>'Male', 'Female'=>'Female']) !!}
                    </div>

                    <div class="col-md-4">
                        <label class="text-muted small mb-1">Primary Mobile Number</label>
                        {!! renderInlineEdit($faculty, 'primary_mobile_number', 'text') !!}
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small mb-1">Alternate Contact Number</label>
                        {!! renderInlineEdit($faculty, 'alternate_contact_number', 'text') !!}
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small mb-1">Official Email Address</label>
                        {!! renderInlineEdit($faculty, 'official_email_address', 'text') !!}
                    </div>

                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Father's Name</label>
                        {!! renderInlineEdit($faculty, 'father_name', 'text') !!}
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Mother's Name</label>
                        {!! renderInlineEdit($faculty, 'mother_name', 'text') !!}
                    </div>

                    <div class="col-12"><hr class="my-2"></div>
                    <div class="col-12">
                        <h6 class="mb-2 text-muted"><i class="ti ti-map-pin me-2"></i>Residential Address</h6>
                    </div>

                    <div class="col-md-6">
                        <label class="text-muted small mb-1">House Name / Flat No.</label>
                        {!! renderInlineEdit($faculty, 'address_house_name_flat_no', 'text') !!}
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Area / Locality</label>
                        {!! renderInlineEdit($faculty, 'address_area_locality', 'text') !!}
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small mb-1">Village / Town / City</label>
                        {!! renderInlineEdit($faculty, 'address_city', 'text') !!}
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small mb-1">District</label>
                        {!! renderInlineEdit($faculty, 'address_district', 'text') !!}
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small mb-1">State</label>
                        {!! renderInlineEdit($faculty, 'address_state', 'text') !!}
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small mb-1">PIN Code</label>
                        {!! renderInlineEdit($faculty, 'address_pin_code', 'text') !!}
                    </div>

                    <div class="col-12"><hr class="my-2"></div>

                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Highest Educational Qualification</label>
                        {!! renderInlineEdit($faculty, 'highest_educational_qualification', 'text') !!}
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Additional Certifications</label>
                        {!! renderInlineEdit($faculty, 'additional_certifications', 'text') !!}
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Teaching Experience</label>
                        {!! renderInlineEdit($faculty, 'teaching_experience', 'select', [''=>'N/A','1'=>'Yes','0'=>'No'], $faculty->teaching_experience === null ? '' : ($faculty->teaching_experience ? '1' : '0')) !!}
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Department Name</label>
                        {!! renderInlineEdit($faculty, 'department_name', 'select', [''=>'N/A','E-School'=>'E-School','EduThanzeel'=>'EduThanzeel','Diploma in Graphic Designing'=>'Diploma in Graphic Designing','AI Integrated Digital Marketing'=>'AI Integrated Digital Marketing','Data Science'=>'Data Science','Machine Learning'=>'Machine Learning']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Documents Card -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="ti ti-file-upload me-2"></i>B. Document Submission</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 30%;">Document</th>
                                <th style="width: 25%;">Current File</th>
                                <th style="width: 45%;">Upload / Replace</th>
                            </tr>
                        </thead>
                        <tbody>
                            @include('admin.online-teaching-faculties.partials.doc-row', ['label' => 'Updated Resume / CV', 'field' => 'document_resume_cv', 'value' => $faculty->document_resume_cv])
                            @include('admin.online-teaching-faculties.partials.doc-row', ['label' => '10th certificate', 'field' => 'document_10th_certificate', 'value' => $faculty->document_10th_certificate])
                            @include('admin.online-teaching-faculties.partials.doc-row', ['label' => 'Educational Qualification Certificates', 'field' => 'document_educational_qualification_certificates', 'value' => $faculty->document_educational_qualification_certificates])
                            @include('admin.online-teaching-faculties.partials.doc-row', ['label' => 'Aadhaar Card (Front Side)', 'field' => 'document_aadhaar_front', 'value' => $faculty->document_aadhaar_front])
                            @include('admin.online-teaching-faculties.partials.doc-row', ['label' => 'Aadhaar Card (Back Side)', 'field' => 'document_aadhaar_back', 'value' => $faculty->document_aadhaar_back])
                            @include('admin.online-teaching-faculties.partials.doc-row', ['label' => 'Other Supporting Document – 1', 'field' => 'document_other_1', 'value' => $faculty->document_other_1])
                            @include('admin.online-teaching-faculties.partials.doc-row', ['label' => 'Other Supporting Document – 2', 'field' => 'document_other_2', 'value' => $faculty->document_other_2])
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- HOD Review Card -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="ti ti-clipboard-check me-2"></i>C. HOD Review & Academic Hiring Tracking</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="text-muted small mb-1">Faculty ID</label>
                        {!! renderInlineEdit($faculty, 'faculty_id', 'text') !!}
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small mb-1">Class Level</label>
                        {!! renderInlineEdit($faculty, 'class_level', 'select', [''=>'N/A','Basic'=>'Basic','LP (Lower Primary)'=>'LP (Lower Primary)','UP (Upper Primary)'=>'UP (Upper Primary)','Secondary'=>'Secondary','Higher Secondary'=>'Higher Secondary','IT'=>'IT']) !!}
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small mb-1">Employment Type</label>
                        {!! renderInlineEdit($faculty, 'employment_type', 'select', [''=>'N/A','Full-Time'=>'Full-Time','Part-Time'=>'Part-Time']) !!}
                    </div>

                    <div class="col-md-4">
                        <label class="text-muted small mb-1">Work Schedule / Mode</label>
                        {!! renderInlineEdit($faculty, 'work_schedule_mode', 'select', [''=>'N/A','Day'=>'Day','Night'=>'Night','Full-Time'=>'Full-Time']) !!}
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small mb-1">Candidate Status</label>
                        {!! renderInlineEdit($faculty, 'candidate_status', 'select', [''=>'N/A','New'=>'New','Shortlisted'=>'Shortlisted','Demo Completed'=>'Demo Completed','Selected'=>'Selected','Rejected'=>'Rejected']) !!}
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small mb-1">Preferred Teaching Platform</label>
                        {!! renderInlineEdit($faculty, 'preferred_teaching_platform', 'select', [''=>'N/A','Google Meet'=>'Google Meet','Zoom'=>'Zoom','Both'=>'Both']) !!}
                    </div>

                    <div class="col-md-4">
                        <label class="text-muted small mb-1">Technical Readiness Confirmation</label>
                        {!! renderInlineEdit($faculty, 'technical_readiness_confirmation', 'select', [''=>'N/A','Yes'=>'Yes','No'=>'No']) !!}
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small mb-1">Demo Class Date</label>
                        {!! renderInlineEdit($faculty, 'demo_class_date', 'date') !!}
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small mb-1">Demo Conducted By</label>
                        {!! renderInlineEdit($faculty, 'demo_conducted_by', 'text') !!}
                    </div>

                    <div class="col-md-4">
                        <label class="text-muted small mb-1">Offer Letter Issued Date</label>
                        {!! renderInlineEdit($faculty, 'offer_letter_issued_date', 'date') !!}
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small mb-1">Joining Date</label>
                        {!! renderInlineEdit($faculty, 'joining_date', 'date') !!}
                    </div>
                    <div class="col-md-12">
                        <label class="text-muted small mb-1">Remarks / HOD Observations</label>
                        {!! renderInlineEdit($faculty, 'remarks', 'textarea') !!}
                    </div>

                    <div class="col-12"><hr class="my-2"></div>

                    <div class="col-12">
                        <label class="text-muted small mb-2">Offer Letter Upload</label>
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 p-3 bg-light rounded">
                            <div>
                                @if($faculty->offer_letter_upload)
                                    <a class="btn btn-outline-primary btn-sm" target="_blank" href="{{ asset('storage/'.$faculty->offer_letter_upload) }}">
                                        <i class="ti ti-download"></i> View / Download Offer Letter
                                    </a>
                                @else
                                    <span class="text-muted"><i class="ti ti-file-off"></i> No file uploaded</span>
                                @endif
                            </div>
                            <div class="d-flex gap-2 align-items-center">
                                <input type="file" class="form-control form-control-sm js-doc-file" data-field="offer_letter_upload" style="max-width: 280px;">
                                <button type="button" class="btn btn-primary btn-sm js-upload-doc" data-field="offer_letter_upload">
                                    <i class="ti ti-upload"></i> Upload
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@php
function renderInlineEdit($faculty, $field, $type = 'text', $options = [], $override = null) {
    $value = $override !== null ? $override : ($faculty->{$field} ?? '');
    
    if ($type === 'date' && $value && is_object($value)) {
        $value = $value->format('Y-m-d');
    } elseif ($type === 'select' && $field === 'teaching_experience') {
        $value = $faculty->teaching_experience === null ? '' : ($faculty->teaching_experience ? '1' : '0');
    }
    
    $displayValue = $value;
    if ($type === 'select' && !empty($options)) {
        $displayValue = $options[$value] ?? ($value === '' ? 'N/A' : $value);
    } elseif ($type === 'date') {
        $displayValue = $value ?: 'N/A';
    } else {
        $displayValue = ($value === '' || $value === null) ? 'N/A' : $value;
    }
    
    $optionsJson = !empty($options) ? htmlspecialchars(json_encode($options, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') : '';
    
    return sprintf(
        '<div class="inline-edit-show" data-field="%s" data-id="%d" data-type="%s" data-current="%s" data-options-json=\'%s\'>
            <span class="display-value d-inline-block">%s</span>
            <a href="#" class="edit-btn-show text-primary ms-2"><i class="ti ti-pencil"></i></a>
        </div>',
        htmlspecialchars($field, ENT_QUOTES),
        $faculty->id,
        htmlspecialchars($type, ENT_QUOTES),
        htmlspecialchars((string)$value, ENT_QUOTES),
        $optionsJson,
        htmlspecialchars((string)$displayValue, ENT_QUOTES)
    );
}
@endphp

@push('styles')
<style>
    .inline-edit-show {
        position: relative;
        display: block;
        padding: 8px 12px;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 4px;
        transition: all 0.2s;
    }

    .inline-edit-show:hover {
        background: #fff;
        border-color: #7367f0;
        box-shadow: 0 2px 8px rgba(115, 103, 240, 0.1);
    }

    .inline-edit-show .display-value {
        font-weight: 500;
        color: #333;
        min-height: 20px;
    }

    .inline-edit-show .edit-btn-show {
        opacity: 0.4;
        transition: opacity 0.2s;
    }

    .inline-edit-show:hover .edit-btn-show {
        opacity: 1;
    }

    .inline-edit-show.editing {
        background: #fff;
        border-color: #7367f0;
        box-shadow: 0 4px 12px rgba(115, 103, 240, 0.15);
    }

    .inline-edit-show.editing .display-value,
    .inline-edit-show.editing .edit-btn-show {
        display: none;
    }

    .inline-edit-show .edit-form-show {
        display: none;
    }

    .inline-edit-show.editing .edit-form-show {
        display: block;
    }

    .inline-edit-show .edit-form-show input,
    .inline-edit-show .edit-form-show select,
    .inline-edit-show .edit-form-show textarea {
        width: 100%;
        padding: 6px 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 13px;
    }

    .inline-edit-show .edit-form-show textarea {
        resize: vertical;
        min-height: 80px;
    }

    .inline-edit-show .edit-form-show .btn-group {
        margin-top: 8px;
        display: flex;
        gap: 8px;
    }

    .inline-edit-show .edit-form-show .btn {
        flex: 1;
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
    <script src="{{ asset('assets/js/online-teaching-faculty-show.js') }}"></script>
@endpush
@endsection
