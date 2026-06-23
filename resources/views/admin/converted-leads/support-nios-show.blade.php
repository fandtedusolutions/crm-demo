@extends('layouts.mantis')

@section('title', 'National Institute of Open Schooling Support - Converted Lead Details')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">National Institute of Open Schooling Support - Converted Lead Details</h5>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end align-items-center gap-2">
                    <a href="{{ route('admin.support-nios-converted-leads.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left"></i> Back to Support List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-4">
                        <h6 class="text-muted">Student</h6>
                        <div><strong>Name:</strong> {{ $convertedLead->name }}</div>
                        <div><strong>Phone:</strong> {{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</div>
                        <div><strong>WhatsApp:</strong> 
                            @if($convertedLead->leadDetail && $convertedLead->leadDetail->whatsapp_number)
                                {{ \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->whatsapp_code, $convertedLead->leadDetail->whatsapp_number) }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </div>
                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                        <div><strong>Parent Phone:</strong> 
                            @if($convertedLead->leadDetail && $convertedLead->leadDetail->parents_number)
                                {{ \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->parents_code, $convertedLead->leadDetail->parents_number) }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </div>
                        @endif
                        <div><strong>Email:</strong> {{ $convertedLead->email ?? '-' }}</div>
                        <div><strong>DOB:</strong> {{ $convertedLead->dob ? \Carbon\Carbon::parse($convertedLead->dob)->format('d-m-Y') : '-' }}</div>
                        <div><strong>Type:</strong> {{ $convertedLead->is_b2b == 1 ? ('B2B' . ($convertedLead->lead?->team?->name ? ' (' . $convertedLead->lead->team->name . ')' : '')) : 'In House' }}</div>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted">Course</h6>
                        <div><strong>Course:</strong> {{ $convertedLead->course?->title ?? 'National Institute of Open Schooling' }}</div>
                        <div><strong>Subject:</strong> {{ $convertedLead->subject?->title ?? '-' }}</div>
                        <div><strong>Batch:</strong> {{ $convertedLead->batch?->title ?? '-' }}</div>
                        <div><strong>Admission Batch:</strong> {{ $convertedLead->admissionBatch?->title ?? '-' }}</div>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted">Registration</h6>
                        <div><strong>Register No:</strong> {{ $convertedLead->register_number ?? '-' }}</div>
                        <div><strong>Application No:</strong> {{ $convertedLead->studentDetails?->application_number ?? '-' }}</div>
                        <div><strong>Converted At:</strong> {{ $convertedLead->created_at?->format('d-m-Y') }}</div>
                    </div>
                </div>

                <hr>

                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Support Status</h6>
                        
                        <div><strong>APP:</strong> {{ $convertedLead->supportDetails?->app ?? '-' }}</div>
                        <div><strong>WhatsApp Group:</strong> {{ $convertedLead->supportDetails?->whatsapp_group ?? '-' }}</div>
                        <div><strong>Telegram Group:</strong> {{ $convertedLead->supportDetails?->telegram_group ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Contacts</h6>
                        <div><strong>Call - 1:</strong> {{ $convertedLead->supportDetails?->call_1 ?? '-' }}</div>
                        <div><strong>Problems:</strong> {{ $convertedLead->supportDetails?->problems ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Uploaded Documents Section -->
    @if($convertedLead->leadDetail)
    <div class="col-12">
        <div class="card">
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
        <div class="card">
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
@endsection


