<td>
    <div class="inline-edit converted-lead-subject-area-field" data-field="subject_area_ids" data-id="{{ $convertedLead->id }}" data-current-ids="{{ $convertedLead->subjectAreas?->pluck('id')->implode(',') ?? '' }}">
        <span class="display-value">@include('admin.converted-leads.partials.subject-area-display', ['convertedLead' => $convertedLead])</span>
        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
        <button type="button" class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit Subject Areas">
            <i class="ti ti-edit"></i>
        </button>
        @endif
    </div>
</td>
