<span>{{ $convertedLead->name }}</span>
@include('admin.converted-leads.partials.addon-course-listing-badge', ['convertedLead' => $convertedLead])
@if($convertedLead->is_cancelled)
<div>
    <span class="badge bg-danger ms-2">Cancelled</span>
    @if($convertedLead->cancelledBy)
        <br><small class="text-muted ms-2">By: {{ $convertedLead->cancelledBy->name }}
        @if($convertedLead->cancelled_at)
            ({{ $convertedLead->cancelled_at->format('d-m-Y h:i A') }})
        @endif
        </small>
    @endif
</div>
@endif
