@php
    $canEditFaculty = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant();
    $facultyName = $convertedLead->faculty ? $convertedLead->faculty->name : 'N/A';
@endphp
<div class="inline-edit" data-field="faculty_id" data-id="{{ $convertedLead->id }}" data-current-id="{{ $convertedLead->faculty_id ?? '' }}">
    <span class="display-value">{{ $facultyName }}</span>
    @if($canEditFaculty)
    <button type="button" class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Assign Faculty">
        <i class="ti ti-edit"></i>
    </button>
    @endif
</div>
