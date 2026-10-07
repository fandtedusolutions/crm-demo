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
                $proofUpdatedAt = $proof->updated_at ?? $payment->updated_at ?? null;
                $proofVersion = $proofUpdatedAt instanceof \DateTimeInterface ? $proofUpdatedAt->getTimestamp() : null;
                $proofVersionQuery = $proofVersion ? ('?v=' . $proofVersion) : '';
                $viewUrl = (!empty($proof->id)
                    ? route('admin.payments.proofs.view', $proof->id)
                    : route('admin.payments.view', $payment->id)) . $proofVersionQuery;
                $downloadUrl = (!empty($proof->id)
                    ? route('admin.payments.proofs.download', $proof->id)
                    : route('admin.payments.download', $payment->id)) . $proofVersionQuery;
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
                            class="btn btn-outline-secondary js-update-payment-proof"
                            title="Update {{ $fileName }}"
                            data-payment-id="{{ $payment->id }}"
                            data-proof-id="{{ $proof->id ?? '' }}"
                            data-file-name="{{ $fileName }}"
                            data-transaction-id="{{ $proof->transaction_id ?? '' }}"
                            data-rejected="{{ $isRejectedPayment ? '1' : '0' }}">
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
                    class="btn btn-sm btn-outline-primary js-update-payment-proof"
                    title="Upload receipt/proof"
                    data-payment-id="{{ $payment->id }}"
                    data-proof-id="{{ $uploadTarget->id ?? '' }}"
                    data-file-name=""
                    data-transaction-id="{{ $uploadTarget->transaction_id ?? $payment->transaction_id ?? '' }}"
                    data-rejected="{{ $isRejectedPayment ? '1' : '0' }}">
                <i class="fas fa-upload"></i>
            </button>
        @endif
    </div>
@endif
