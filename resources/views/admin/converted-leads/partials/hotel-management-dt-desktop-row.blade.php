@php
    $canToggleAcademic = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor();
    $canToggleSupport = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_support_team();
    $canManageCancelFlag = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor();
    $showParentPhone = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor();
    $courseChanged = (bool) ($convertedLead->is_course_changed ?? false);
@endphp

<tr class="{{ $convertedLead->is_cancelled ? 'cancelled-row' : '' }}">
    <td>{{ $displayIndex }}</td>
    <td>
        @include('admin.converted-leads.partials.status-badge', [
            'convertedLead' => $convertedLead,
            'type' => 'academic',
            'showToggle' => $canToggleAcademic,
            'toggleUrl' => $canToggleAcademic ? route('admin.converted-leads.toggle-academic-verify', $convertedLead->id) : null,
            'title' => 'academic',
            'useModal' => true
        ])
    </td>
    <td>
        @include('admin.converted-leads.partials.status-badge', [
            'convertedLead' => $convertedLead,
            'type' => 'support',
            'showToggle' => $canToggleSupport,
            'toggleUrl' => $canToggleSupport ? route('admin.support-converted-leads.toggle-support-verify', $convertedLead->id) : null,
            'title' => 'support',
            'useModal' => true
        ])
    </td>
    <td>
        <div class="inline-edit" data-field="registration_number" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->registration_number }}">
            <span class="display-value">{{ $convertedLead->studentDetails?->registration_number ?? '-' }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                    <i class="ti ti-edit"></i>
                </button>
            @endif
        </div>
    </td>
    @include('admin.converted-leads.partials.inline-course-flag-cell', ['convertedLead' => $convertedLead])
    <td>{{ $convertedLead->converted_date ? \Carbon\Carbon::parse($convertedLead->converted_date)->format('d-m-Y') : '-' }}</td>
    <td>
        <div class="inline-edit" data-field="dob" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->dob }}">
            <span class="display-value">{{ $convertedLead->dob ? \Carbon\Carbon::parse($convertedLead->dob)->format('d-m-Y') : '-' }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                    <i class="ti ti-edit"></i>
                </button>
            @endif
        </div>
    </td>
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
    <td>
        <div class="inline-edit" data-field="phone" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->phone }}" data-code="{{ $convertedLead->code }}">
            <span class="display-value">
                @if($convertedLead->code && $convertedLead->phone)
                    {{ $convertedLead->code }} {{ $convertedLead->phone }}
                @else
                    {{ $convertedLead->phone ?? '-' }}
                @endif
            </span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                    <i class="ti ti-edit"></i>
                </button>
            @endif
        </div>
    </td>
    <td>
        @if($convertedLead->leadDetail && $convertedLead->leadDetail->whatsapp_number)
            {{ \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->whatsapp_code, $convertedLead->leadDetail->whatsapp_number) }}
        @else
            <span class="text-muted">N/A</span>
        @endif
    </td>
    @if($showParentPhone)
        <td>
            @if($convertedLead->leadDetail && $convertedLead->leadDetail->parents_number)
                {{ \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->parents_code, $convertedLead->leadDetail->parents_number) }}
            @else
                <span class="text-muted">N/A</span>
            @endif
        </td>
    @endif
    <td>
        <div class="inline-edit" data-field="batch_id" data-id="{{ $convertedLead->id }}" data-course-id="{{ $convertedLead->course_id }}" data-current-id="{{ $convertedLead->batch_id }}">
            <span class="display-value">{{ $convertedLead->batch ? $convertedLead->batch->title : 'N/A' }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                    <i class="ti ti-edit"></i>
                </button>
            @endif
        </div>
    </td>
    <td>
        <div class="inline-edit" data-field="admission_batch_id" data-id="{{ $convertedLead->id }}" data-batch-id="{{ $convertedLead->batch_id }}" data-current-id="{{ $convertedLead->admission_batch_id }}">
            <span class="display-value">{{ $convertedLead->admissionBatch ? $convertedLead->admissionBatch->title : 'N/A' }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                    <i class="ti ti-edit"></i>
                </button>
            @endif
        </div>
    </td>
    @include('admin.converted-leads.partials.inline-finance-approval-cell', ['convertedLead' => $convertedLead])
    @include('admin.converted-leads.partials.inline-faculty-cell', ['convertedLead' => $convertedLead])
    <td>
        <div class="inline-edit" data-field="internship_id" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->internship_id }}">
            <span class="display-value">{{ $convertedLead->studentDetails?->internship_id ?? 'N/A' }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                    <i class="ti ti-edit"></i>
                </button>
            @endif
        </div>
    </td>
    <td>
        <div class="inline-edit" data-field="app" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->app }}">
            <span class="display-value">{{ $convertedLead->studentDetails?->app ?? '-' }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                    <i class="ti ti-edit"></i>
                </button>
            @endif
        </div>
    </td>
    <td>
        <div class="inline-edit" data-field="group" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->group }}">
            <span class="display-value">{{ $convertedLead->studentDetails?->group ?? '-' }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                    <i class="ti ti-edit"></i>
                </button>
            @endif
        </div>
    </td>
    <td>
        <div class="inline-edit" data-field="interview" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->interview }}">
            <span class="display-value">{{ $convertedLead->studentDetails?->interview ?? '-' }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                    <i class="ti ti-edit"></i>
                </button>
            @endif
        </div>
    </td>
    <td>
        <div class="inline-edit" data-field="howmany_interview" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->howmany_interview }}">
            <span class="display-value">{{ $convertedLead->studentDetails?->howmany_interview ?? '0' }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                    <i class="ti ti-edit"></i>
                </button>
            @endif
        </div>
    </td>
    <td>
        <div class="inline-edit" data-field="remarks" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->remarks }}">
            <span class="display-value">{{ $convertedLead->studentDetails?->remarks ?? '-' }}</span>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                    <i class="ti ti-edit"></i>
                </button>
            @endif
        </div>
    </td>
    <td>
        <div role="group">
            <a href="{{ route('admin.converted-leads.show', $convertedLead->id) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                <i class="ti ti-eye"></i>
            </a>
            <a href="{{ route('admin.invoices.index', $convertedLead->id) }}" class="btn btn-sm btn-success" title="View Invoice">
                <i class="ti ti-receipt"></i>
            </a>
            @if($canManageCancelFlag)
                @php
                    $cancelBtnClass = $convertedLead->is_cancelled ? 'btn-danger' : 'btn-outline-danger';
                    $cancelBtnTitle = $convertedLead->is_cancelled ? 'Update cancellation confirmation' : 'Confirm cancellation';
                @endphp
                <button type="button" class="btn btn-sm {{ $cancelBtnClass }} js-cancel-flag" title="{{ $cancelBtnTitle }}"
                    data-cancel-url="{{ route('admin.converted-leads.cancel-flag', $convertedLead->id) }}"
                    data-modal-title="Cancellation Confirmation">
                    <i class="ti ti-ban"></i>
                </button>
            @endif
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_support_team())
                <button type="button" class="btn btn-sm btn-info update-register-btn" title="Update Register Number"
                    data-url="{{ route('admin.converted-leads.update-register-number-modal', $convertedLead->id) }}"
                    data-title="Update Register Number">
                    <i class="ti ti-edit"></i>
                </button>
                @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                    <button type="button" class="btn btn-sm {{ $courseChanged ? 'btn-success' : 'btn-danger' }} js-change-course-modal"
                        title="Change Course"
                        data-modal-url="{{ route('admin.converted-leads.change-course-modal', $convertedLead->id) }}"
                        data-modal-title="Change Course">
                        <i class="ti ti-exchange"></i>
                    </button>
                @endif
                @if($hasIdCard)
                    <a href="{{ route('admin.converted-leads.id-card-view', $convertedLead->id) }}" class="btn btn-sm btn-success" title="View ID Card" target="_blank">
                        <i class="ti ti-id"></i>
                    </a>
                @else
                    <form class="d-inline id-card-generate-form" action="{{ route('admin.converted-leads.id-card-generate', $convertedLead->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-warning" title="Generate ID Card" data-loading-text="Generating...">
                            <i class="ti ti-id"></i>
                        </button>
                    </form>
                @endif
            @endif
        </div>
    </td>
</tr>

