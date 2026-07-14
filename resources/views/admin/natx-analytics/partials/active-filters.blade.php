@php
    use App\Helpers\DateRangeHelper;

    $chips = [];

    if (($filters['date_range'] ?? DateRangeHelper::natxDefaultPreset()) !== DateRangeHelper::PRESET_ALL) {
        $chips[] = ['label' => 'Period', 'value' => DateRangeHelper::displayPeriod($filters)];
    }

    if (!empty($filters['call_type'])) {
        $chips[] = ['label' => 'Type', 'value' => $filters['call_type'] === 'not_picked' ? 'Not Picked' : ucfirst($filters['call_type'])];
    }

    if (!empty($filters['search'])) {
        $chips[] = ['label' => 'Search', 'value' => $filters['search']];
    }

    if (!empty($filters['role_id']) && isset($roles)) {
        $activeRole = $roles->firstWhere('id', $filters['role_id']);
        if ($activeRole) {
            $chips[] = ['label' => 'Role', 'value' => $activeRole->title];
        }
    }

    if (!empty($filters['role_id']) && (int)$filters['role_id'] === 3 && !empty($filters['team_id']) && isset($teams)) {
        $activeTeam = $teams->firstWhere('id', $filters['team_id']);
        if ($activeTeam) {
            $chips[] = ['label' => 'Team', 'value' => $activeTeam->name];
        }
    }

    if (!empty($filters['user_id']) && isset($users)) {
        $activeUser = $users->firstWhere('id', $filters['user_id']);
        if ($activeUser) {
            $chips[] = ['label' => 'User', 'value' => $activeUser->name];
        }
    }

    $hasExtraFilters = !empty($chips);
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
