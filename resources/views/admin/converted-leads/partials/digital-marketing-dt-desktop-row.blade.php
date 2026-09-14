@php
    $canToggleAcademic = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor();
    $canToggleSupport = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_support_team();
@endphp
<tr class="{{ $convertedLead->is_cancelled ? 'cancelled-row' : '' }}">
    <td>{{ $displayIndex }}</td>
    <td>@include('admin.converted-leads.partials.status-badge', ['convertedLead' => $convertedLead, 'type' => 'academic', 'showToggle' => $canToggleAcademic, 'toggleUrl' => $canToggleAcademic ? route('admin.converted-leads.toggle-academic-verify', $convertedLead->id) : null, 'title' => 'academic', 'useModal' => true])</td>
    <td>@include('admin.converted-leads.partials.status-badge', ['convertedLead' => $convertedLead, 'type' => 'support', 'showToggle' => $canToggleSupport, 'toggleUrl' => $canToggleSupport ? route('admin.support-converted-leads.toggle-support-verify', $convertedLead->id) : null, 'title' => 'support', 'useModal' => true])</td>
    <td>{{ optional($convertedLead->created_at)->format('d-m-Y') }}</td>
    <td>
        <div class="inline-edit" data-field="register_number" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->register_number }}">
            @if($convertedLead->register_number)
                <span class="badge bg-success"><span class="display-value">{{ $convertedLead->register_number }}</span></span>
            @else
                <span class="display-value text-muted">Not Set</span>
            @endif
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                <button type="button" class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                    <i class="ti ti-edit"></i>
                </button>
            @endif
        </div>
    </td>
    @include('admin.converted-leads.partials.inline-course-flag-cell', ['convertedLead' => $convertedLead])
    <td>
        {{ $convertedLead->name }}
        @if($convertedLead->is_cancelled)
            <span class="badge bg-danger ms-2">Cancelled</span>
        @endif
    </td>
    <td>{{ $convertedLead->is_b2b == 1 ? ('B2B' . ($convertedLead->lead?->team?->name ? ' (' . $convertedLead->lead->team->name . ')' : '')) : 'In House' }}</td>
    <td>{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</td>
    <td>{{ $convertedLead->leadDetail && $convertedLead->leadDetail->whatsapp_number ? \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->whatsapp_code, $convertedLead->leadDetail->whatsapp_number) : 'N/A' }}</td>
    @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
        <td>{{ $convertedLead->leadDetail && $convertedLead->leadDetail->parents_number ? \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->parents_code, $convertedLead->leadDetail->parents_number) : 'N/A' }}</td>
    @endif
    <td>{{ $convertedLead->leadDetail?->programme_type ? ucfirst($convertedLead->leadDetail->programme_type) : '-' }}</td>
    <td>{{ $convertedLead->leadDetail?->location ?? '-' }}</td>
    <td>
        @if($convertedLead->leadDetail?->classTime)
            {{ \Carbon\Carbon::parse($convertedLead->leadDetail->classTime->from_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($convertedLead->leadDetail->classTime->to_time)->format('h:i A') }}
        @else
            -
        @endif
    </td>
    <td>{{ $convertedLead->batch?->title ?? 'N/A' }}</td>
    <td>{{ $convertedLead->admissionBatch?->title ?? 'N/A' }}</td>
    @include('admin.converted-leads.partials.inline-finance-approval-cell', ['convertedLead' => $convertedLead])
    @include('admin.converted-leads.partials.inline-faculty-cell', ['convertedLead' => $convertedLead])
    <td>{{ $convertedLead->studentDetails?->internship_id ?? 'N/A' }}</td>
    <td>{{ $convertedLead->email ?? '-' }}</td>
    <td>{{ $convertedLead->studentDetails?->call_status ?? '-' }}</td>
    <td>{{ $convertedLead->studentDetails?->class_information ?? '-' }}</td>
    <td>{{ $convertedLead->studentDetails?->orientation_class_status ?? '-' }}</td>
    <td>{{ $convertedLead->studentDetails?->class_starting_date ? \Carbon\Carbon::parse($convertedLead->studentDetails->class_starting_date)->format('d-m-Y') : '-' }}</td>
    <td>{{ $convertedLead->studentDetails?->class_ending_date ? \Carbon\Carbon::parse($convertedLead->studentDetails->class_ending_date)->format('d-m-Y') : '-' }}</td>
    <td>{{ $convertedLead->studentDetails?->whatsapp_group_status ?? '-' }}</td>
    <td>{{ $convertedLead->studentDetails?->class_status ?? '-' }}</td>
    <td>{{ $convertedLead->studentDetails?->complete_cancel_date ? \Carbon\Carbon::parse($convertedLead->studentDetails->complete_cancel_date)->format('d-m-Y') : '-' }}</td>
    <td>{{ $convertedLead->studentDetails?->remarks ?? '-' }}</td>
    <td>
        <div class="" role="group">
            <a href="{{ route('admin.converted-leads.show', $convertedLead->id) }}" class="btn btn-sm btn-outline-primary" title="View Details"><i class="ti ti-eye"></i></a>
            <a href="{{ route('admin.invoices.index', $convertedLead->id) }}" class="btn btn-sm btn-success" title="View Invoice"><i class="ti ti-receipt"></i></a>
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_support_team())
                <button type="button" class="btn btn-sm btn-info update-register-btn" title="Update Register Number"
                    data-url="{{ route('admin.converted-leads.update-register-number-modal', $convertedLead->id) }}"
                    data-title="Update Register Number">
                    <i class="ti ti-edit"></i>
                </button>
            @endif
            @if($hasIdCard)
                <a href="{{ route('admin.converted-leads.id-card-view', $convertedLead->id) }}" class="btn btn-sm btn-success" title="View ID Card" target="_blank"><i class="ti ti-id"></i></a>
            @else
                <form class="d-inline id-card-generate-form" action="{{ route('admin.converted-leads.id-card-generate', $convertedLead->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-warning" title="Generate ID Card"><i class="ti ti-id"></i></button>
                </form>
            @endif
        </div>
    </td>
</tr>

