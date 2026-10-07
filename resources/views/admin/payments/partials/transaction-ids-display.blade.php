@php
    $displayProofs = $payment->getDisplayProofs();
    $isRejectedPayment = $payment->status === 'Rejected';
@endphp

@if($displayProofs->isNotEmpty())
    <div class="d-flex flex-column gap-1">
        @foreach($displayProofs as $proof)
            <div class="d-flex align-items-center gap-1 {{ $loop->first ? '' : 'mt-1' }}">
                @if(!empty($proof->transaction_id))
                    <code>{{ $proof->transaction_id }}</code>
                @else
                    <span class="text-muted">N/A</span>
                @endif
                @if(!empty($canUpdateProof))
                    <button type="button"
                            class="btn btn-outline-secondary btn-sm py-0 px-1 js-update-payment-proof"
                            title="Edit transaction ID"
                            data-payment-id="{{ $payment->id }}"
                            data-proof-id="{{ $proof->id ?? '' }}"
                            data-file-name="{{ !empty($proof->file_upload) ? basename($proof->file_upload) : '' }}"
                            data-transaction-id="{{ $proof->transaction_id ?? '' }}"
                            data-rejected="{{ $isRejectedPayment ? '1' : '0' }}">
                        <i class="fas fa-pen"></i>
                    </button>
                @endif
            </div>
        @endforeach
    </div>
@else
    <div class="d-flex align-items-center gap-1">
        <span class="text-muted">N/A</span>
        @if(!empty($canUpdateProof))
            <button type="button"
                    class="btn btn-outline-secondary btn-sm py-0 px-1 js-update-payment-proof"
                    title="Edit transaction ID"
                    data-payment-id="{{ $payment->id }}"
                    data-proof-id=""
                    data-file-name=""
                    data-transaction-id="{{ $payment->transaction_id ?? '' }}"
                    data-rejected="{{ $isRejectedPayment ? '1' : '0' }}">
                <i class="fas fa-pen"></i>
            </button>
        @endif
    </div>
@endif
