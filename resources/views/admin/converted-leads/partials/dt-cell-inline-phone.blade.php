@php
    $canEdit = $canEdit ?? false;
    $field = $field ?? 'phone';
    $codeField = $codeField ?? 'code';
    $number = $number ?? '';
    $code = $code ?? '';
    $display = $number
        ? \App\Helpers\PhoneNumberHelper::display($code, $number)
        : 'N/A';
@endphp
@if($canEdit)
    <div
        class="inline-edit"
        data-field="{{ $field }}"
        data-id="{{ $convertedLead->id }}"
        data-current="{{ e($number) }}"
        data-code="{{ e($code) }}"
        data-code-field="{{ $codeField }}"
    >
        <span class="display-value">{{ $number ? $display : 'N/A' }}</span>
        <button type="button" class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
            <i class="ti ti-edit"></i>
        </button>
    </div>
@else
    @if($number)
        {{ $display }}
    @else
        <span class="text-muted">N/A</span>
    @endif
@endif
