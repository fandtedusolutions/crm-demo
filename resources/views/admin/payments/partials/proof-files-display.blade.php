@php
    $allProofs = $payment->getDisplayProofs();
    $displayProofs = $allProofs->filter(fn ($proof) => !empty($proof->file_upload));
    $uploadTarget = $allProofs->first(fn ($proof) => empty($proof->file_upload));
    $isRejectedPayment = $payment->status === 'Rejected';
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
                @if(!empty($canUpdateProof))
                    <button type="button"
                            class="btn btn-outline-secondary"
                            title="Update {{ $fileName }}"
                            onclick="showUpdateProofModal({{ $payment->id }}, {{ !empty($proof->id) ? (int) $proof->id : 'null' }}, @json($fileName), @json($proof->transaction_id ?? ''), {{ $isRejectedPayment ? 'true' : 'false' }})">
                        <i class="fas fa-pen"></i>
                    </button>
                @endif
            </div>
        @endforeach
    </div>
@else
    <div class="d-flex flex-column gap-1 align-items-start">
        <span class="text-muted">
            <i class="fas fa-file-slash me-1"></i>No file
        </span>
        @if(!empty($canUpdateProof))
            <button type="button"
                    class="btn btn-sm btn-outline-primary"
                    title="Upload receipt/proof"
                    onclick="showUpdateProofModal({{ $payment->id }}, {{ $uploadTarget && !empty($uploadTarget->id) ? (int) $uploadTarget->id : 'null' }}, '', @json($uploadTarget->transaction_id ?? $payment->transaction_id ?? ''), {{ $isRejectedPayment ? 'true' : 'false' }})">
                <i class="fas fa-upload"></i>
            </button>
        @endif
    </div>
@endif
