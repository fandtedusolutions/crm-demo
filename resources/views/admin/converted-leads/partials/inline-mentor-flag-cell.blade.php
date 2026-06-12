<td>
    <div class="inline-edit mentor-flag-field" data-field="flag_id" data-id="{{ $convertedLead->id }}" data-current-id="{{ $convertedLead->flag_id }}">
        <span class="display-value">@include('admin.converted-leads.partials.flag-display', ['flag' => $convertedLead->flag])</span>
        @if(\App\Support\MentorFlagFieldSupport::canUserUpdateFlag())
        <button type="button" class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit Flag">
            <i class="ti ti-edit"></i>
        </button>
        @endif
    </div>
</td>
