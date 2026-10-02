@php
    $canInlineEditName = \App\Helpers\RoleHelper::is_admin_or_super_admin()
        || \App\Helpers\RoleHelper::is_finance()
        || \App\Helpers\RoleHelper::is_admission_counsellor()
        || \App\Helpers\RoleHelper::is_academic_assistant()
        || \App\Helpers\RoleHelper::is_general_manager()
        || \App\Helpers\RoleHelper::is_senior_manager()
        || \App\Helpers\RoleHelper::is_team_lead();
@endphp
<div class="d-flex align-items-center">
    <div class="avtar avtar-s rounded-circle bg-light-success me-2 d-flex align-items-center justify-content-center">
        <span class="f-16 fw-bold text-success js-name-initial">{{ strtoupper(substr($convertedLead->name, 0, 1)) }}</span>
    </div>
    <div>
        <div class="inline-edit" data-field="name" data-id="{{ $convertedLead->id }}" data-current="{{ e($convertedLead->name) }}">
            <h6 class="mb-0 d-inline"><span class="display-value">{{ $convertedLead->name }}</span></h6>
            @if($canInlineEditName)
            <button type="button" class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit Name">
                <i class="ti ti-edit"></i>
            </button>
            @endif
        </div>
        <small class="text-muted">ID: {{ $convertedLead->lead_id }}</small>
        @include('admin.converted-leads.partials.addon-course-listing-badge', ['convertedLead' => $convertedLead])
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
