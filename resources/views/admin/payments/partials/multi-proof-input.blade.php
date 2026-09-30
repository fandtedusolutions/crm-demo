@php
    $inputName = $inputName ?? 'receipt_files';
    $inputId = $inputId ?? $inputName;
    $proofError = $errors->has($inputName);
    if (!$proofError) {
        foreach ($errors->keys() as $errorKey) {
            if (str_starts_with((string) $errorKey, $inputName . '.')) {
                $proofError = true;
                break;
            }
        }
    }
@endphp
<div class="js-multi-proof">
    <input type="file"
           class="form-control js-multi-proof-input @if($proofError) is-invalid @endif"
           name="{{ $inputName }}[]"
           id="{{ $inputId }}"
           accept=".pdf,.jpg,.jpeg,.png"
           multiple>
    <div class="form-text">Select one or more files, or choose again to add more. Accepted formats: PDF, JPG, JPEG, PNG (Max: 2MB each, up to 10 files).</div>
    <div class="js-multi-proof-error text-danger small mt-1"></div>
    <ul class="js-multi-proof-list list-unstyled mb-0 mt-2"></ul>
    @foreach($errors->get($inputName) as $message)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @endforeach
    @foreach($errors->get($inputName . '.*') as $message)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @endforeach
</div>
