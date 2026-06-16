@php
    $col = $col ?? 6;
    $type = $type ?? 'text';
    $displayValue = ($displayValue === '' || $displayValue === null) ? 'N/A' : $displayValue;
    $rawValue = $rawValue ?? '';
    $code = $code ?? '';
    $codeField = $codeField ?? '';
    $courseId = $courseId ?? '';
    $currentId = $currentId ?? '';
    $canEdit = $canEdit ?? false;
    $options = $options ?? [];
    $optionsJson = !empty($options)
        ? htmlspecialchars(json_encode($options, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8')
        : '';
    if ($field === 'second_language' && $rawValue) {
        $rawValue = strtolower((string) $rawValue);
    }
@endphp
<div class="col-md-{{ $col }}">
    <label class="form-label text-muted">{{ $label }}</label>
    @if($canEdit)
        <div
            class="inline-edit-show-cl"
            data-field="{{ $field }}"
            data-id="{{ $convertedLeadId }}"
            data-type="{{ $type }}"
            data-current="{{ e($rawValue) }}"
            @if($optionsJson) data-options-json='{{ $optionsJson }}' @endif
            @if($codeField) data-code-field="{{ $codeField }}" data-current-code="{{ e($code) }}" @endif
            @if($courseId) data-course-id="{{ $courseId }}" @endif
            @if($currentId !== '' && $currentId !== null) data-current-id="{{ $currentId }}" @endif
        >
            <div class="fw-bold mb-0 d-flex align-items-center gap-1 flex-wrap">
                <span class="display-value">{{ $displayValue }}</span>
                <button type="button" class="btn btn-sm btn-link p-0 edit-btn-show-cl" title="Edit {{ $label }}">
                    <i class="ti ti-pencil"></i>
                </button>
            </div>
        </div>
    @else
        <p class="fw-bold mb-0">{{ $displayValue }}</p>
    @endif
</div>
