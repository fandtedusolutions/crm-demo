@php
    $canManageCancelFlag = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor();
    $canSendToLms = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor();
    $cancelBtnClass = $convertedLead->is_cancelled ? 'btn-danger' : 'btn-outline-danger';
    $cancelBtnTitle = $convertedLead->is_cancelled ? 'Update cancellation confirmation' : 'Confirm cancellation';
    $courseChanged = (bool) ($convertedLead->is_course_changed ?? false);
    $alreadySharedToLms = (bool) ($convertedLead->is_shared_to_lms ?? false);
    $sendToLmsBtnClass = $alreadySharedToLms ? 'btn-success' : 'btn-primary';
    $sendToLmsTitle = $alreadySharedToLms ? 'Already sent to LMS' : 'Send to LMS';
    $actionButtons = [];
@endphp

@php ob_start(); @endphp
<a href="{{ route('admin.converted-leads.show', $convertedLead->id) }}" class="btn btn-sm btn-outline-primary" title="View Details">
    <i class="ti ti-eye"></i>
</a>
@php $actionButtons[] = trim(ob_get_clean()); @endphp

@php ob_start(); @endphp
<a href="{{ route('admin.invoices.index', $convertedLead->id) }}" class="btn btn-sm btn-success" title="View Invoice">
    <i class="ti ti-receipt"></i>
</a>
@php $actionButtons[] = trim(ob_get_clean()); @endphp

@if($canManageCancelFlag)
@php ob_start(); @endphp
<button type="button" class="btn btn-sm {{ $cancelBtnClass }} js-cancel-flag" title="{{ $cancelBtnTitle }}"
    data-cancel-url="{{ route('admin.converted-leads.cancel-flag', $convertedLead->id) }}"
    data-modal-title="Cancellation Confirmation">
    <i class="ti ti-ban"></i>
</button>
@php $actionButtons[] = trim(ob_get_clean()); @endphp
@endif

@if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_support_team())
@php ob_start(); @endphp
<button type="button" class="btn btn-sm btn-info update-register-btn" title="Update Register Number"
    data-url="{{ route('admin.converted-leads.update-register-number-modal', $convertedLead->id) }}"
    data-title="Update Register Number">
    <i class="ti ti-edit"></i>
</button>
@php $actionButtons[] = trim(ob_get_clean()); @endphp

@if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
@php ob_start(); @endphp
<button type="button" class="btn btn-sm {{ $courseChanged ? 'btn-success' : 'btn-danger' }} js-change-course-modal"
    title="Change Course"
    data-modal-url="{{ route('admin.converted-leads.change-course-modal', $convertedLead->id) }}"
    data-modal-title="Change Course">
    <i class="ti ti-exchange"></i>
</button>
@php $actionButtons[] = trim(ob_get_clean()); @endphp
@endif

@if($convertedLead->register_number)
    @if($hasIdCard)
    @php ob_start(); @endphp
    <a href="{{ route('admin.converted-leads.id-card-view', $convertedLead->id) }}" class="btn btn-sm btn-success" title="View ID Card" target="_blank">
        <i class="ti ti-id"></i>
    </a>
    @php $actionButtons[] = trim(ob_get_clean()); @endphp
    @else
    @php ob_start(); @endphp
    <form action="{{ route('admin.converted-leads.id-card-generate', $convertedLead->id) }}" method="post" style="display:inline-block" class="id-card-generate-form">
        @csrf
        <button type="submit" class="btn btn-sm btn-warning" title="Generate ID Card" data-loading-text="Generating...">
            <i class="ti ti-id"></i>
        </button>
    </form>
    @php $actionButtons[] = trim(ob_get_clean()); @endphp
    @endif
@endif
@endif

@if($canSendToLms)
@php ob_start(); @endphp
<button type="button" class="btn btn-sm {{ $sendToLmsBtnClass }} js-send-to-lms-modal" title="{{ $sendToLmsTitle }}"
    data-modal-url="{{ route('admin.converted-leads.send-to-lms-modal', $convertedLead->id) }}"
    data-modal-title="Send to LMS">
    <i class="ti ti-send"></i>
</button>
@php $actionButtons[] = trim(ob_get_clean()); @endphp
@endif

@if(\App\Helpers\RoleHelper::is_super_admin())
@php ob_start(); @endphp
<button type="button" class="btn btn-sm btn-outline-danger" title="Delete Converted Lead"
    onclick="delete_modal({{ json_encode(route('admin.converted-leads.destroy', $convertedLead->id)) }}, {{ json_encode('Delete converted lead "'.$convertedLead->name.'"? All related invoices, payments, support, placement, and student records will be permanently removed. This cannot be undone.') }})">
    <i class="ti ti-trash"></i>
</button>
@php $actionButtons[] = trim(ob_get_clean()); @endphp
@endif

@php
    $actionButtons = $actionButtons ?? [];
    $buttonChunks = array_chunk($actionButtons, 3);
@endphp
<div class="converted-lead-actions" role="group" style="min-width: 120px;">
@foreach($buttonChunks as $chunkIndex => $chunk)
    @if($chunkIndex > 0)
    <br>
    <hr class="my-1">
    @endif
    <div class="d-inline-flex flex-wrap gap-1">
        {!! implode('', $chunk) !!}
    </div>
@endforeach
</div>
