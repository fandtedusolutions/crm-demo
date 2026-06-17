@php
    $displayProofs = $payment->getDisplayProofs();
@endphp

@if($displayProofs->isNotEmpty())
    <div class="d-flex flex-column gap-1">
        @foreach($displayProofs as $proof)
            @if(!empty($proof->transaction_id))
                <code class="{{ $loop->first ? '' : 'mt-1' }}">{{ $proof->transaction_id }}</code>
            @endif
        @endforeach
    </div>
@else
    <span class="text-muted">N/A</span>
@endif
