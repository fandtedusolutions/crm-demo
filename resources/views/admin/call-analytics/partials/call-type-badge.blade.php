@php
    $badgeClass = match($call->call_type) {
        'incoming' => 'bg-success',
        'outgoing' => 'bg-primary',
        'not_picked' => 'bg-info text-dark',
        'missed' => 'bg-warning text-dark',
        'rejected' => 'bg-danger',
        default => 'bg-secondary',
    };
@endphp
<span class="badge ca-call-badge {{ $badgeClass }}">{{ $call->call_type_label }}</span>
