@php
    $chips = [];

    if (!empty($filters['call_type'])) {
        $chips[] = ['label' => 'Type', 'value' => $filters['call_type'] === 'not_picked' ? 'Not Picked' : ucfirst($filters['call_type'])];
    }

    if (!empty($filters['search'])) {
        $chips[] = ['label' => 'Search', 'value' => $filters['search']];
    }

    $hasExtraFilters = !empty($filters['call_type']) || !empty($filters['search']);
@endphp

@if($hasExtraFilters)
<div class="ca-active-filters no-print mb-3">
    <span class="ca-chip-label"><i class="ti ti-filter me-1"></i> Active:</span>
    @foreach($chips as $chip)
        <span class="ca-chip">
            <span class="text-muted">{{ $chip['label'] }}:</span>
            <strong>{{ $chip['value'] }}</strong>
        </span>
    @endforeach
</div>
@endif
