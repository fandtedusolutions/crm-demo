<div class="card mb-3 {{ $convertedLead->is_cancelled ? 'cancelled-card' : '' }}">
    <div class="card-body">
        @php
            $canToggleAcademic = \App\Helpers\RoleHelper::is_admin_or_super_admin()
                || \App\Helpers\RoleHelper::is_academic_assistant()
                || \App\Helpers\RoleHelper::is_admission_counsellor();
            $canToggleSupport = \App\Helpers\RoleHelper::is_admin_or_super_admin()
                || \App\Helpers\RoleHelper::is_support_team();
        @endphp

        <!-- Lead Header -->
        <div class="d-flex align-items-center mb-3">
            <div class="avtar avtar-s rounded-circle bg-light-success me-3 d-flex align-items-center justify-content-center">
                <span class="f-16 fw-bold text-success">{{ strtoupper(substr($convertedLead->name, 0, 1)) }}</span>
            </div>
            <div class="flex-grow-1">
                <h6 class="mb-1 fw-bold">{{ $convertedLead->name }}</h6>
                <small class="text-muted">ID: {{ $convertedLead->lead_id }}</small>
                @if($convertedLead->is_cancelled)
                    <div>
                        <span class="badge bg-danger ms-2">Cancelled</span>
                        @if($convertedLead->cancelledBy)
                            <br><small class="text-muted ms-2">
                                By: {{ $convertedLead->cancelledBy->name }}
                                @if($convertedLead->cancelled_at)
                                    ({{ $convertedLead->cancelled_at->format('d-m-Y h:i A') }})
                                @endif
                            </small>
                        @endif
                    </div>
                @endif
            </div>

            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="ti ti-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.converted-leads.show', $convertedLead->id) }}">
                            <i class="ti ti-eye me-2"></i>View Details
                        </a>
                    </li>

                    @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_finance() || \App\Helpers\RoleHelper::is_post_sales())
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.invoices.index', $convertedLead->id) }}">
                                <i class="ti ti-receipt me-2"></i>View Invoice
                            </a>
                        </li>
                    @endif

                    @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor())
                        <li>
                            <a class="dropdown-item update-register-btn" href="#"
                                data-url="{{ route('admin.converted-leads.update-register-number-modal', $convertedLead->id) }}"
                                data-title="Update Register Number">
                                <i class="ti ti-edit me-2"></i>Update Register Number
                            </a>
                        </li>

                        @if($convertedLead->register_number)
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.converted-leads.id-card-pdf', $convertedLead->id) }}" target="_blank">
                                    <i class="ti ti-id me-2"></i>Generate ID Card PDF
                                </a>
                            </li>
                        @endif
                    @endif
                </ul>
            </div>
        </div>

        <!-- Lead Details -->
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
                <small class="text-muted d-block">Email</small>
                <span class="fw-medium">{{ $convertedLead->email ?? 'N/A' }}</span>
            </div>

            <div class="col-6">
                <small class="text-muted d-block">Course</small>
                <span class="fw-medium">{{ $convertedLead->course ? $convertedLead->course->title : 'N/A' }}</span>
            </div>

            <div class="col-6">
                <small class="text-muted d-block">Academic Assistant</small>
                <span class="fw-medium">{{ $convertedLead->academicAssistant ? $convertedLead->academicAssistant->name : 'N/A' }}</span>
            </div>

            <div class="col-6">
                <small class="text-muted d-block">Register Number</small>
                @if($convertedLead->register_number)
                    <span class="badge bg-success">{{ $convertedLead->register_number }}</span>
                @else
                    <span class="text-muted">Not Set</span>
                @endif
            </div>

            <div class="col-6">
                <small class="text-muted d-block">Subject</small>
                <span class="fw-medium">{{ $convertedLead->subject?->title ?? 'N/A' }}</span>
            </div>

            <div class="col-6">
                <small class="text-muted d-block">Subject Area</small>
                <span class="fw-medium">{{ $convertedLead->subjectArea?->title ?? 'N/A' }}</span>
            </div>

            <div class="col-6">
                <small class="text-muted d-block">Academic</small>
                @include('admin.converted-leads.partials.status-badge', [
                    'convertedLead' => $convertedLead,
                    'type' => 'academic',
                    'showToggle' => $canToggleAcademic,
                    'toggleUrl' => $canToggleAcademic ? route('admin.converted-leads.toggle-academic-verify', $convertedLead->id) : null,
                ])
            </div>

            <div class="col-6">
                <small class="text-muted d-block">Support</small>
                @include('admin.converted-leads.partials.status-badge', [
                    'convertedLead' => $convertedLead,
                    'type' => 'support',
                    'showToggle' => $canToggleSupport,
                    'toggleUrl' => $canToggleSupport ? route('admin.support-converted-leads.toggle-support-verify', $convertedLead->id) : null,
                ])
            </div>

            <div class="col-6">
                <small class="text-muted d-block">Converted Date</small>
                <span class="fw-medium">{{ $convertedLead->created_at->format('d-m-Y') }}</span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.converted-leads.show', $convertedLead->id) }}" class="btn btn-sm btn-primary">
                <i class="ti ti-eye me-1"></i>View Details
            </a>

            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_finance() || \App\Helpers\RoleHelper::is_post_sales())
                <a href="{{ route('admin.invoices.index', $convertedLead->id) }}" class="btn btn-sm btn-success">
                    <i class="ti ti-receipt me-1"></i>View Invoice
                </a>
            @endif

            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor())
                <button type="button" class="btn btn-sm btn-info update-register-btn"
                    data-url="{{ route('admin.converted-leads.update-register-number-modal', $convertedLead->id) }}"
                    data-title="Update Register Number">
                    <i class="ti ti-edit me-1"></i>Update Register
                </button>

                @if($convertedLead->register_number)
                    <a href="{{ route('admin.converted-leads.id-card-pdf', $convertedLead->id) }}" class="btn btn-sm btn-warning" target="_blank">
                        <i class="ti ti-id me-1"></i>ID Card PDF
                    </a>
                @endif
            @endif
        </div>
    </div>
</div>

