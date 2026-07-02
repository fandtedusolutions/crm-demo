@php
    $row = $convertedLead ?? $lead ?? null;
    $appValue = ($context ?? 'mentor') === 'support'
        ? ($row?->supportDetails?->app)
        : ($row?->mentorDetails?->app);
    $editable = $canEdit ?? false;
@endphp
<td>
    @if($editable)
        <div class="inline-edit" data-field="app" data-id="{{ $row->id }}" data-field-type="select" data-current="{{ $appValue }}">
            <span class="display-value">{{ $appValue ?? '-' }}</span>
            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit"><i class="ti ti-edit"></i></button>
        </div>
    @else
        {{ $appValue ?? '-' }}
    @endif
</td>
