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
            @endphp
            <a href="{{ $viewUrl }}" class="btn btn-outline-primary btn-sm" title="View Receipt/Proof" target="_blank">
                <i class="ti ti-file-invoice"></i>
                @if($displayProofs->count() > 1)
                    <span class="ms-1">{{ $loop->iteration }}</span>
                @endif
            </a>
        @endforeach
    </div>
@endif
