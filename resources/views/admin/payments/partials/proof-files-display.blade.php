@php
    $displayProofs = $payment->getDisplayProofs()->filter(fn ($proof) => !empty($proof->file_upload));
@endphp

@if($displayProofs->isNotEmpty())
    <div class="d-flex flex-column gap-1">
        @foreach($displayProofs as $proof)
            @php
                $viewUrl = !empty($proof->id)
                    ? route('admin.payments.proofs.view', $proof->id)
                    : route('admin.payments.view', $payment->id);
                $downloadUrl = !empty($proof->id)
                    ? route('admin.payments.proofs.download', $proof->id)
                    : route('admin.payments.download', $payment->id);
                $fileName = basename($proof->file_upload);
            @endphp
            <div class="btn-group btn-group-sm" role="group" aria-label="Receipt/Proof {{ $loop->iteration }}">
                <a href="{{ $downloadUrl }}" class="btn btn-outline-primary" title="Download {{ $fileName }}">
                    <i class="fas fa-download"></i>
                </a>
                <a href="{{ $viewUrl }}" class="btn btn-primary" title="View {{ $fileName }}" target="_blank">
                    <i class="fas fa-file-alt"></i>
                </a>
            </div>
        @endforeach
    </div>
@else
    <span class="text-muted">
        <i class="fas fa-file-slash me-1"></i>No file
    </span>
@endif
