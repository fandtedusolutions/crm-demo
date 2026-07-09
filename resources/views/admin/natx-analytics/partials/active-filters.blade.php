@php
    use App\Helpers\DateRangeHelper;

    $chips = [];
    $dateLabel = DateRangeHelper::options()[$filters['date_range'] ?? DateRangeHelper::defaultPreset()] ?? 'Custom';
    $start = DateRangeHelper::formatDisplay($filters['start_date'] ?? null);
    $end = DateRangeHelper::formatDisplay($filters['end_date'] ?? null);
    $chips[] = ['label' => 'Period', 'value' => $dateLabel . ' (' . $start . ' - ' . $end . ')'];

    if (!empty($filters['user_id']) && isset($users)) {
        $selectedUser = $users->firstWhere('id', (int) $filters['user_id']);
        if ($selectedUser) {
            $chips[] = ['label' => 'User', 'value' => $selectedUser->name];
        }
    }

    if (!empty($filters['call_type'])) {
        $chips[] = ['label' => 'Type', 'value' => $filters['call_type'] === 'not_picked' ? 'Not Picked' : ucfirst($filters['call_type'])];
    }

    if (!empty($filters['search'])) {
        $chips[] = ['label' => 'Search', 'value' => $filters['search']];
    }

    $defaultDateRange = $defaultDateRange ?? DateRangeHelper::defaultPreset();
    $isDefaultDate = ($filters['date_range'] ?? $defaultDateRange) === $defaultDateRange;
    $hasExtraFilters = !empty($filters['user_id']) || !empty($filters['call_type']) || !empty($filters['search']) || !$isDefaultDate;
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
