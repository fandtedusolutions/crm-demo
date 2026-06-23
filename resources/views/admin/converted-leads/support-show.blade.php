@extends('layouts.mantis')

@section('title', 'Support - Converted Lead Details')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Support - Converted Lead Details</h5>
                    <p class="mb-0 text-muted">{{ $convertedLead->course?->title ?? '-' }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end align-items-center gap-2">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#feedbackModal">
                        <i class="ti ti-message-plus"></i> Feedback Form
                    </button>
                    @if(isset($backUrl))
                    <a href="{{ $backUrl }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left"></i> Back to Support List
                    </a>
                    @elseif(($convertedLead->course_id ?? null) === 2)
                    <a href="{{ route('admin.support-bosse-converted-leads.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left"></i> Back to Board of Open Schooling and Skill Education Support
                    </a>
                    @elseif(($convertedLead->course_id ?? null) === 1)
                    <a href="{{ route('admin.support-nios-converted-leads.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left"></i> Back to National Institute of Open Schooling Support
                    </a>
                    @else
                    <a href="{{ route('admin.converted-leads.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left"></i> Back to Converted Leads
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-lg-4">
                        <h6 class="text-muted mb-2"><i class="ti ti-user me-1"></i> Student</h6>
                        <div class="mb-1"><strong>Name:</strong> {{ $convertedLead->name }}</div>
                        <div class="mb-1"><strong>Phone:</strong> {{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</div>
                        <div class="mb-1"><strong>WhatsApp:</strong> 
                            @if($convertedLead->leadDetail && $convertedLead->leadDetail->whatsapp_number)
                                {{ \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->whatsapp_code, $convertedLead->leadDetail->whatsapp_number) }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </div>
                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                        <div class="mb-1"><strong>Parent Phone:</strong> 
                            @if($convertedLead->leadDetail && $convertedLead->leadDetail->parents_number)
                                {{ \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->parents_code, $convertedLead->leadDetail->parents_number) }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </div>
                        @endif
                        <div class="mb-1"><strong>Email:</strong> {{ $convertedLead->email ?? '-' }}</div>
                        <div class="mb-1"><strong>DOB:</strong> {{ $convertedLead->dob ? \Carbon\Carbon::parse($convertedLead->dob)->format('d-m-Y') : '-' }}</div>
                        <div class="mb-1"><strong>Type:</strong> {{ $convertedLead->is_b2b == 1 ? ('B2B' . ($convertedLead->lead?->team?->name ? ' (' . $convertedLead->lead->team->name . ')' : '')) : 'In House' }}</div>
                    </div>
                    <div class="col-lg-4">
                        <h6 class="text-muted mb-2"><i class="ti ti-book me-1"></i> Course</h6>
                        <div class="mb-1"><strong>Course:</strong> <span class="badge bg-light text-dark border">{{ $convertedLead->course?->title ?? '-' }}</span></div>
                        <div class="mb-1"><strong>Subject:</strong> {{ $convertedLead->subject?->title ?? '-' }}</div>
                        <div class="mb-1"><strong>Batch:</strong> {{ $convertedLead->batch?->title ?? '-' }}</div>
                        <div class="mb-1"><strong>Admission Batch:</strong> {{ $convertedLead->admissionBatch?->title ?? '-' }}</div>
                    </div>
                    <div class="col-lg-4">
                        <h6 class="text-muted mb-2"><i class="ti ti-id me-1"></i> Registration</h6>
                        <div class="mb-1"><strong>Register No:</strong> {{ $convertedLead->register_number ?? '-' }}</div>
                        <div class="mb-1"><strong>Application No:</strong> {{ $convertedLead->studentDetails?->application_number ?? '-' }}</div>
                        <div class="mb-1"><strong>Converted At:</strong> {{ $convertedLead->created_at?->format('d-m-Y') }}</div>
                        @if($convertedLead->supportDetails?->last_feedback)
                        <div class="mb-1"><strong>Last Feedback:</strong> <span class="badge bg-info">{{ $convertedLead->supportDetails?->last_feedback?->format('d-m-Y H:i') }}</span></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="text-muted mb-2"><i class="ti ti-headphones me-1"></i> Support Status</h6>
                        
                        <div class="mb-1"><strong>APP:</strong> {{ $convertedLead->supportDetails?->app ?? '-' }}</div>
                        <div class="mb-1"><strong>WhatsApp Group:</strong> {{ $convertedLead->supportDetails?->whatsapp_group ?? '-' }}</div>
                        <div class="mb-1"><strong>Telegram Group:</strong> {{ $convertedLead->supportDetails?->telegram_group ?? '-' }}</div>
                    </div>
                    <div class="col-lg-6">
                        <h6 class="text-muted mb-2"><i class="ti ti-phone me-1"></i> Contacts & Issues</h6>
                        <div class="mb-1"><strong>Call - 1:</strong> {{ $convertedLead->supportDetails?->call_1 ?? '-' }}</div>
                        <div class="mb-1"><strong>Problems:</strong> {{ $convertedLead->supportDetails?->problems ?? '-' }}</div>
                        @if($convertedLead->supportDetails?->support_notes)
                        <div class="mb-1"><strong>Notes:</strong> {{ $convertedLead->supportDetails?->support_notes }}</div>
                        @endif
                        @if($convertedLead->supportDetails?->support_status)
                        <div class="mb-1"><strong>Status:</strong> <span class="badge bg-primary">{{ $convertedLead->supportDetails?->support_status }}</span></div>
                        @endif
                        @if($convertedLead->supportDetails?->support_priority)
                        <div class="mb-1"><strong>Priority:</strong> <span class="badge bg-warning text-dark">{{ $convertedLead->supportDetails?->support_priority }}</span></div>
                        @endif
                        @if($convertedLead->supportDetails?->last_support_contact)
                        <div class="mb-1"><strong>Last Contact:</strong> {{ $convertedLead->supportDetails?->last_support_contact?->format('d-m-Y H:i') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Feedback History Timeline -->
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h6 class="mb-0"><i class="ti ti-history me-1"></i> Feedback History Timeline</h6>
            </div>
            <div class="card-body">
                @if($convertedLead->supportFeedbackHistory->count() > 0)
                <div class="timeline">
                    @foreach($convertedLead->supportFeedbackHistory as $feedback)
                    <div class="timeline-item">
                        <div class="timeline-marker">
                            @switch($feedback->feedback_type)
                                @case('call')
                                    <i class="ti ti-phone text-primary"></i>
                                    @break
                                @case('issue')
                                    <i class="ti ti-alert-triangle text-warning"></i>
                                    @break
                                @case('resolution')
                                    <i class="ti ti-check text-success"></i>
                                    @break
                                @case('complaint')
                                    <i class="ti ti-exclamation-mark text-danger"></i>
                                    @break
                                @default
                                    <i class="ti ti-message text-info"></i>
                            @endswitch
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <h6 class="mb-0">{{ ucfirst(str_replace('_', ' ', $feedback->feedback_type)) }}</h6>
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    @if($feedback->priority)
                                    <span class="badge bg-{{ $feedback->priority === 'urgent' ? 'danger' : ($feedback->priority === 'high' ? 'warning' : ($feedback->priority === 'medium' ? 'info' : 'secondary')) }}">
                                        {{ ucfirst($feedback->priority) }}
                                    </span>
                                    @endif
                                    @if($feedback->feedback_status)
                                    <span class="badge bg-{{ $feedback->feedback_status === 'resolved' ? 'success' : ($feedback->feedback_status === 'pending' ? 'warning' : 'primary') }}">
                                        {{ ucfirst(str_replace('_', ' ', $feedback->feedback_status)) }}
                                    </span>
                                    @endif
                                    <small class="text-muted">{{ $feedback->created_at->format('d M Y, H:i') }}</small>
                                </div>
                            </div>
                            <div class="timeline-body">
                                <div class="feedback-content">
                                    @php
                                        $lines = explode("\n", $feedback->feedback_content);
                                    @endphp
                                    @foreach($lines as $line)
                                        @php
                                            $line = trim($line);
                                            if ($line === '') { continue; }
                                            $parts = explode(':', $line, 2);
                                            $question = trim($parts[0] ?? '');
                                            $answer = trim($parts[1] ?? '');
                                        @endphp
                                        <div class="feedback-line">
                                            <span class="feedback-line-question">{{ $question }}@if($answer!==''): @endif</span>
                                            @if($answer!=='')<span class="feedback-line-answer">{{ $answer }}</span>@endif
                                        </div>
                                    @endforeach
                                </div>
                                @if($feedback->notes)
                                <div class="alert alert-light p-2 mb-2">
                                    <small><strong>Notes:</strong> {{ $feedback->notes }}</small>
                                </div>
                                @endif
                                @if($feedback->follow_up_date)
                                <div class="text-muted">
                                    <small><i class="ti ti-calendar me-1"></i> Follow up: {{ $feedback->follow_up_date->format('d M Y') }}</small>
                                </div>
                                @endif
                            </div>
                            <div class="timeline-footer">
                                <small class="text-muted">
                                    <i class="ti ti-user me-1"></i> {{ $feedback->createdBy?->name ?? 'Unknown User' }}
                                </small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <i class="ti ti-message-off text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">No feedback history available</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Uploaded Documents Section -->
    @if($convertedLead->leadDetail)
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h6 class="mb-0"><i class="ti ti-file-upload me-1"></i> Uploaded Documents</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
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
                                $exists = $path ? \Illuminate\Support\Facades\Storage::disk('public')->exists($path) : false;
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
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- SSLC Certificates Section -->
    @if($convertedLead->leadDetail)
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h6 class="mb-0"><i class="ti ti-file-certificate me-1"></i> SSLC Certificates</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @php
                        $doc = $convertedLead->leadDetail;
                    @endphp
                    
                    <!-- SSLC Certificates from sslc_certificates table -->
                    @if($doc->sslcCertificates && $doc->sslcCertificates->count() > 0)
                        @foreach($doc->sslcCertificates as $index => $certificate)
                        <div class="col-md-3">
                            <label class="form-label text-muted">SSLC Certificate {{ $index + 1 }}</label>
                            @php
                                $certPath = $certificate->certificate_path ?? null;
                                $certExists = $certPath ? \Illuminate\Support\Facades\Storage::disk('public')->exists($certPath) : false;
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
                    
                    <!-- Legacy SSLC Certificate from leads_details table -->
                    @if($doc->sslc_certificate)
                        <div class="col-md-3">
                            <label class="form-label text-muted">SSLC Certificate (Legacy)</label>
                            @php
                                $legacyPath = $doc->sslc_certificate ?? null;
                                $legacyExists = $legacyPath ? \Illuminate\Support\Facades\Storage::disk('public')->exists($legacyPath) : false;
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
                    
                    @if((!$doc->sslcCertificates || $doc->sslcCertificates->count() == 0) && !$doc->sslc_certificate)
                        <div class="col-12">
                            <div class="text-center text-muted py-4">
                                <i class="ti ti-file-alert f-48 mb-2"></i>
                                <p class="mb-0">No SSLC certificates found</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Feedback Modal -->
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="feedbackModalLabel">
                    <i class="ti ti-message-plus me-1"></i> Submit Feedback
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="feedbackForm">
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-12">
                            <h6 class="mb-2">വിദ്യാർത്ഥിയുടെ അടിസ്ഥാന വിവരങ്ങൾ (Basic Details)</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">പേര് (Name)</label>
                                    <input type="text" class="form-control" value="{{ $convertedLead->name }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">കോഴ്‌സ് നാമം (Course)</label>
                                    <input type="text" class="form-control" value="{{ $convertedLead->course?->title ?? '-' }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">ബാച്ച് (Batch)</label>
                                    <input type="text" class="form-control" value="{{ $convertedLead->batch?->title ?? '-' }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">REGISTRATION Number</label>
                                    <input type="text" class="form-control" value="{{ $convertedLead->register_number ?? '-' }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mode</label>
                                    <div class="d-flex gap-3 align-items-center">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="mode" id="mode_online" value="Online">
                                            <label class="form-check-label" for="mode_online">Online</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="mode" id="mode_offline" value="Offline">
                                            <label class="form-check-label" for="mode_offline">Offline</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">പ്രവേശന ബാച്ച് (Admission Batch)</label>
                                    <input type="text" class="form-control" value="{{ $convertedLead->admissionBatch?->title ?? '-' }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">മെന്റർ നാമം (Mentor Name)</label>
                                    <input type="text" class="form-control" value="{{ $convertedLead->admissionBatch?->mentor?->name ?? '-' }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">ബന്ധപ്പെടാനുള്ള നമ്പർ (Phone)</label>
                                    <input type="text" class="form-control" value="{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">ഇമെയിൽ ഐഡി (Email)</label>
                                    <input type="text" class="form-control" value="{{ $convertedLead->email ?? '-' }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="col-12"><hr></div>

                        <div class="col-12">
                            <h6 class="mb-2">A. ക്ലാസ് & അക്കാദമിക് പെർഫോമൻസ്</h6>
                            <div class="mb-3">
                                <label class="form-label">1) ക്ലാസുകളുടെ ഗുണമേന്മ</label>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach(['വളരെ നല്ലത്','നല്ലത്','ശരാശരി','മെച്ചപ്പെടുത്തണം'] as $opt)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="q1_quality" value="{{ $opt }}">
                                        <label class="form-check-label">{{ $opt }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">2) ക്ലാസുകൾ സമയത്ത്?</label>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach(['എല്ലായ്പ്പോഴും','ചിലപ്പോൾ','അപൂർവ്വമായി','ഒരിക്കലും ഇല്ല'] as $opt)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="q2_on_time" value="{{ $opt }}">
                                        <label class="form-check-label">{{ $opt }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">3) പഠനരീതി മനസ്സിലാക്കാൻ എളുപ്പമാണോ?</label>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach(['അതെ, പൂർണമായും','ഭാഗികമായി','ചിലപ്പോൾ ബുദ്ധിമുട്ട്','മനസ്സിലാക്കാൻ ബദ്ധിമുട്ടാണ്'] as $opt)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="q3_method" value="{{ $opt }}">
                                        <label class="form-check-label">{{ $opt }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">4) മെന്റർ വിശദീകരണം</label>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach(['അതെ','ഭാഗികമായി','ചിലപ്പോൾ','ഇല്ല'] as $opt)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="q4_explain" value="{{ $opt }}">
                                        <label class="form-check-label">{{ $opt }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">5) സംശയങ്ങൾ ചോദിക്കാൻ അവസരം</label>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach(['ലഭിച്ചു','ചിലപ്പോൾ മാത്രം','ഇല്ല'] as $opt)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="q5_doubts" value="{{ $opt }}">
                                        <label class="form-check-label">{{ $opt }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="col-12"><hr></div>

                        <div class="col-12">
                            <h6 class="mb-2">B. മെന്റർ & കമ്യൂണിക്കേഷൻ</h6>
                            <div class="mb-3">
                                <label class="form-label">6) മെന്റർ ഫോൺ ബന്ധം</label>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach(['രണ്ടോ അതിലധികം തവണ','ഒരു തവണ','ഇല്ല'] as $opt)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="q6_call" value="{{ $opt }}">
                                        <label class="form-check-label">{{ $opt }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">7) മെന്ററുടെ സമീപനം</label>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach(['വളരെ സൗഹൃദപരവും പ്രൊഫഷണലും','സാധാരണ','മെച്ചപ്പെടുത്തണം'] as $opt)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="q7_approach" value="{{ $opt }}">
                                        <label class="form-check-label">{{ $opt }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">8) സംശയ പരിഹാരം</label>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach(['പൂർണമായും','ഭാഗികമായി','സഹായം ആവശ്യമുണ്ട്'] as $opt)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="q8_help" value="{{ $opt }}">
                                        <label class="form-check-label">{{ $opt }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">9) അറിയിപ്പുകൾ ലഭ്യമാകുന്നത്</label>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach(['എല്ലായ്പ്പോഴും','ചിലപ്പോൾ','അപൂർവ്വമായി'] as $opt)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="q9_notifications" value="{{ $opt }}">
                                        <label class="form-check-label">{{ $opt }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">10) അക്കാദമിക് മാർഗനിർദ്ദേശം</label>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach(['സ്ഥിരമായി','ചിലപ്പോൾ','ഇല്ല'] as $opt)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="q10_guidance" value="{{ $opt }}">
                                        <label class="form-check-label">{{ $opt }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="col-12"><hr></div>

                        <div class="col-12">
                            <h6 class="mb-2">C. ആപ്പ്, ലൈവ് ക്ലാസ് & ടെക്നിക്കൽ സപ്പോർട്ട്</h6>
                            <div class="mb-3">
                                <label class="form-label">11) ലൈവ് ക്ലാസുകളിൽ പങ്കെടുക്കാൻ എളുപ്പമാണോ?</label>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach(['അതെ, എളുപ്പമാണ്','ചിലപ്പോൾ ബുദ്ധിമുട്ട്','എപ്പോഴും ബുദ്ധിമുട്ട്'] as $opt)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="q11_live_easy" value="{{ $opt }}">
                                        <label class="form-check-label">{{ $opt }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">12) മൊബൈൽ ആപ്പ് വഴി ക്ലാസ് കാണാറുണ്ടോ?</label>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach(['എല്ലായ്പ്പോഴും','ചിലപ്പോൾ','ഒരിക്കലും ഇല്ല'] as $opt)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="q12_mobile_app" value="{{ $opt }}">
                                        <label class="form-check-label">{{ $opt }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">13) ടെക്നിക്കൽ പ്രശ്നങ്ങൾ നേരിട്ടാൽ സപ്പോർട്ട് ടീം പ്രതികരിക്കുന്നുണ്ടോ?</label>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach(['വേഗത്തിൽ','വൈക്കി','പ്രതികരണം ഇല്ല'] as $opt)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="q13_support_response" value="{{ $opt }}">
                                        <label class="form-check-label">{{ $opt }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">14) ആപ്പ് ഉപയോഗിക്കുന്നതിൽ ബുദ്ധിമുട്ടുണ്ടോ?</label>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach(['ഇല്ല','ചിലപ്പോൾ','സ്ഥിരമായി'] as $opt)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="q14_app_difficulty" value="{{ $opt }}">
                                        <label class="form-check-label">{{ $opt }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">15) ലൈവ് ക്ലാസുകളുടെ ശബ്ദവും വീഡിയോയുടെയും ഗുണമേന്മ എങ്ങനെ തോന്നുന്നു?</label>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach(['മികച്ചത്','ശരാശരി','മെച്ചപ്പെടുത്തണം'] as $opt)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="q15_av_quality" value="{{ $opt }}">
                                        <label class="form-check-label">{{ $opt }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="col-12"><hr></div>

                        <div class="col-12">
                            <h6 class="mb-2">D. അക്കാദമിക് പ്രകടനം & സംതൃപ്തി</h6>
                            <div class="mb-3">
                                <label class="form-label">16) നിങ്ങളുടെ പഠനത്തിൽ മെന്ററിന്റെ സഹായം എത്രമാത്രം ഗുണം ചെയ്തു?</label>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach(['വളരെ ഗുണം ചെയ്തു','കുറച്ച് ഗുണം ചെയ്തു','മാറ്റമൊന്നുമില്ല'] as $opt)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="q16_benefit" value="{{ $opt }}">
                                        <label class="form-check-label">{{ $opt }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">17) ഈ മാസം നിങ്ങളുടെ അക്കാദമിക് പ്രകടനം എങ്ങനെ തോന്നുന്നു?</label>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach(['വളരെ മികച്ചത്','നല്ലത്','ശരാശരി','മെച്ചപ്പെടുത്തണം'] as $opt)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="q17_performance" value="{{ $opt }}">
                                        <label class="form-check-label">{{ $opt }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">18) കോഴ്‌സ് അനുഭവം ആകെ എങ്ങനെ തോന്നുന്നു?</label>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach(['വളരെ സംതൃപ്തം','സംതൃപ്തം','ശരാശരി','അസംതൃപ്തം'] as $opt)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="q18_overall" value="{{ $opt }}">
                                        <label class="form-check-label">{{ $opt }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">19) ഭാവിയിൽ നിങ്ങൾ ഈ സ്ഥാപനത്തെ മറ്റുള്ളവർക്ക് ശുപാർശ ചെയ്യുമോ?</label>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach(['തീർച്ചയായും ചെയ്യും','ചെയ്യും','ഉറപ്പില്ല','ഇല്ല'] as $opt)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="q19_recommend" value="{{ $opt }}">
                                        <label class="form-check-label">{{ $opt }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Student Comments</label>
                            <textarea class="form-control" name="student_comments" rows="2" placeholder="Comments / Suggestions"></textarea>
                        </div>

                        <div class="col-12"><hr></div>

                        <div class="col-12">
                            <h6 class="mb-2">Support Team Section</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Collected By</label>
                                    <input type="text" class="form-control" name="collected_by" placeholder="Enter name" value="">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Month & Year</label>
                                    <input type="text" class="form-control" value="{{ now()->format('F Y') }}" readonly>
                                    <input type="hidden" name="month_year_month" value="{{ (int) now()->format('n') }}">
                                    <input type="hidden" name="month_year_year" value="{{ (int) now()->format('Y') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Date & Time</label>
                                    <input type="text" class="form-control" value="{{ now()->format('d-m-Y H:i') }}" readonly>
                                    <input type="hidden" name="date_day" value="{{ (int) now()->format('j') }}">
                                    <input type="hidden" name="time_hm" value="{{ now()->format('H:i') }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-12"><hr></div>

                        <input type="hidden" id="feedback_type" name="feedback_type" value="general">
                        <input type="hidden" id="priority" name="priority" value="">
                        <input type="hidden" id="feedback_status" name="feedback_status" value="">
                        <input type="hidden" id="follow_up_date" name="follow_up_date" value="">
                        <input type="hidden" id="feedback_content" name="feedback_content" required>
                        <div class="col-12">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Any additional notes..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitFeedbackBtn">
                        <i class="ti ti-send me-1"></i> Submit Feedback
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
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
    margin-bottom: 20px;
    display: grid;
    grid-template-columns: 40px 1fr;
    column-gap: 12px;
}

.timeline-marker {
    width: 32px;
    height: 32px;
    background: #fff;
    border: 2px solid #e9ecef;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}

.timeline-content {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 12px 14px;
    border-left: 4px solid #0d6efd;
}

.timeline-header {
    margin-bottom: 8px;
}

.timeline-body {
    margin-bottom: 8px;
}

.timeline-footer {
    border-top: 1px solid #e9ecef;
    padding-top: 8px;
}

.feedback-content {
    margin-bottom: 8px;
}

.feedback-line {
    margin-bottom: 4px;
    padding: 2px 0;
    line-height: 1.4;
}

.feedback-line:last-child {
    margin-bottom: 0;
}

.feedback-line-question {
    font-weight: 600;
    color: #212529;
    display: inline;
    word-break: break-word;
}

.feedback-line-answer {
    color: #495057;
    white-space: pre-wrap;
    word-break: break-word;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const feedbackForm = document.getElementById('feedbackForm');
    const submitBtn = document.getElementById('submitFeedbackBtn');
    
    feedbackForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="ti ti-loader-2 spin me-1"></i> Submitting...';
        
        // Build feedback content from answers (Malayalam form)
        const answers = [];
        const getVal = (name) => {
            const el = feedbackForm.querySelector(`[name="${name}"]:checked`);
            return el ? el.value : '';
        };
        const txt = (name) => (feedbackForm.querySelector(`[name="${name}"]`)?.value || '');

        answers.push('വിദ്യാർത്ഥിയുടെ അടിസ്ഥാന വിവരങ്ങൾ');
        answers.push(`പേര്: {{ $convertedLead->name }}`);
        answers.push(`കോഴ്‌സ്: {{ $convertedLead->course?->title ?? '-' }}`);
        answers.push(`ബാച്ച്: {{ $convertedLead->batch?->title ?? '-' }}`);
        answers.push(`REG NO: {{ $convertedLead->register_number ?? '-' }}`);
        answers.push(`Mode: ${getVal('mode')}`);
        answers.push(`Admission Batch: {{ $convertedLead->admissionBatch?->title ?? '-' }}`);
        answers.push(`Teacher: {{ $convertedLead->academicAssistant?->name ?? '-' }}`);
        answers.push(`Phone: {{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}`);
        answers.push(`Email: {{ $convertedLead->email ?? '-' }}`);

        // A
        answers.push('\nA. ക്ലാസ് & അക്കാദമിക് പെർഫോമൻസ്');
        answers.push(`1) ക്ലാസുകളുടെ ഗുണമേന്മ: ${getVal('q1_quality')}`);
        answers.push(`2) ക്ലാസുകൾ സമയത്ത്: ${getVal('q2_on_time')}`);
        answers.push(`3) പഠനരീതി മനസ്സിലാക്കാൻ എളുപ്പമാണോ: ${getVal('q3_method')}`);
        answers.push(`4) മെന്റർ വിശദീകരണം: ${getVal('q4_explain')}`);
        answers.push(`5) സംശയങ്ങൾ ചോദിക്കാൻ അവസരം: ${getVal('q5_doubts')}`);

        // B
        answers.push('\nB. മെന്റർ & കമ്യൂണിക്കേഷൻ');
        answers.push(`6) മെന്റർ ഫോൺ ബന്ധം: ${getVal('q6_call')}`);
        answers.push(`7) മെന്ററുടെ സമീപനം: ${getVal('q7_approach')}`);
        answers.push(`8) സംശയ പരിഹാരം: ${getVal('q8_help')}`);
        answers.push(`9) അറിയിപ്പുകൾ ലഭ്യമാകുന്നത്: ${getVal('q9_notifications')}`);
        answers.push(`10) അക്കാദമിക് മാർഗനിർദ്ദേശം: ${getVal('q10_guidance')}`);

        // C & D
        answers.push('\nC. ആപ്പ്, ലൈവ് ക്ലാസ് & ടെക്നിക്കൽ സപ്പോർട്ട്');
        answers.push(`11) ലൈവ് ക്ലാസുകളിൽ പങ്കെടുക്കാൻ എളുപ്പമാണോ: ${getVal('q11_live_easy')}`);
        answers.push(`12) മൊബൈൽ ആപ്പ് വഴി ക്ലാസ് കാണാറുണ്ടോ: ${getVal('q12_mobile_app')}`);
        answers.push(`13) ടെക്നിക്കൽ പ്രശ്നങ്ങൾ നേരിട്ടാൽ സപ്പോർട്ട് ടീം പ്രതികരിക്കുന്നുണ്ടോ: ${getVal('q13_support_response')}`);
        answers.push(`14) ആപ്പ് ഉപയോഗിക്കുന്നതിൽ ബുദ്ധിമുട്ടുണ്ടോ: ${getVal('q14_app_difficulty')}`);
        answers.push(`15) ലൈവ് ക്ലാസുകളുടെ ശബ്ദവും വീഡിയോയുടെയും ഗുണമേന്മ എങ്ങനെ തോന്നുന്നു: ${getVal('q15_av_quality')}`);
        answers.push('\nD. അക്കാദമിക് പ്രകടനം & സംതൃപ്തി');
        answers.push(`16) നിങ്ങളുടെ പഠനത്തിൽ മെന്ററിന്റെ സഹായം എത്രമാത്രം ഗുണം ചെയ്തു: ${getVal('q16_benefit')}`);
        answers.push(`17) ഈ മാസം നിങ്ങളുടെ അക്കാദമിക് പ്രകടനം എങ്ങനെ തോന്നുന്നു: ${getVal('q17_performance')}`);
        answers.push(`18) കോഴ്‌സ് അനുഭവം ആകെ എങ്ങനെ തോന്നുന്നു: ${getVal('q18_overall')}`);
        answers.push(`19) ഭാവിയിൽ നിങ്ങൾ ഈ സ്ഥാപനത്തെ മറ്റുള്ളവർക്ക് ശുപാർശ ചെയ്യുമോ: ${getVal('q19_recommend')}`);

        if (txt('student_comments')) {
            answers.push(`\nStudent Comments: ${txt('student_comments')}`);
        }
        answers.push(`\nCollected By: ${txt('collected_by')}`);
        
        // Get month/year/day/time from hidden inputs (not selects)
        const monthVal = feedbackForm.querySelector('[name="month_year_month"]')?.value || '';
        const yearVal = feedbackForm.querySelector('[name="month_year_year"]')?.value || '';
        const dayVal = feedbackForm.querySelector('[name="date_day"]')?.value || '';
        const timeVal = feedbackForm.querySelector('[name="time_hm"]')?.value || '';
        
        // Convert month number to month name
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                           'July', 'August', 'September', 'October', 'November', 'December'];
        const monthName = monthVal ? monthNames[parseInt(monthVal) - 1] || monthVal : '';
        
        answers.push(`Month & Year: ${monthName} ${yearVal}`);
        answers.push(`Date & Time: ${dayVal}-${('0'+monthVal).slice(-2)}-${yearVal} ${timeVal}`);

        const feedbackContent = answers.join('\n');
        
        // Check if content exceeds max length (10000 chars)
        if (feedbackContent.length > 10000) {
            alert('Feedback content is too long. Please reduce the content.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="ti ti-send me-1"></i> Submit Feedback';
            return;
        }

        document.getElementById('feedback_content').value = feedbackContent;

        const formData = new FormData(feedbackForm);
        
        fetch('{{ route("admin.support-converted-leads.feedback", $convertedLead->id) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Show success message
                if (typeof toast_success === 'function') {
                    toast_success(data.message || 'Feedback submitted successfully!');
                } else {
                    alert(data.message || 'Feedback submitted successfully!');
                }
                
                // Close modal
                const modalElement = document.getElementById('feedbackModal');
                if (modalElement) {
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                modal.hide();
                    }
                }
                
                // Reset form
                feedbackForm.reset();
                
                // Reload page to show new feedback in timeline
                setTimeout(() => {
                location.reload();
                }, 500);
            } else {
                const errorMsg = data.error || data.message || 'Failed to submit feedback';
                if (typeof toast_error === 'function') {
                    toast_error(errorMsg);
                } else {
                    alert('Error: ' + errorMsg);
                }
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="ti ti-send me-1"></i> Submit Feedback';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            let errorMsg = 'An error occurred while submitting feedback';
            if (error.errors) {
                errorMsg = Object.values(error.errors).flat().join(', ');
            } else if (error.error) {
                errorMsg = error.error;
            } else if (error.message) {
                errorMsg = error.message;
            }
            if (typeof toast_error === 'function') {
                toast_error(errorMsg);
            } else {
                alert(errorMsg);
            }
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="ti ti-send me-1"></i> Submit Feedback';
        });
    });
});
</script>
@endsection


