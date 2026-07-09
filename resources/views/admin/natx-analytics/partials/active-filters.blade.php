@php
    use App\Helpers\DateRangeHelper;

    $chips = [];

    if (($filters['date_range'] ?? DateRangeHelper::natxDefaultPreset()) !== DateRangeHelper::PRESET_ALL) {
        $chips[] = ['label' => 'Period', 'value' => DateRangeHelper::displayPeriod($filters)];
    }

    if (!empty($activeRole ?? null)) {
        $chips[] = ['label' => 'Role', 'value' => $activeRole->title];
    } elseif (!empty($filters['role_id']) && !empty($roles ?? null)) {
        $role = $roles->firstWhere('id', (int) $filters['role_id']);
        if ($role) {
            $chips[] = ['label' => 'Role', 'value' => $role->title];
        }
    }

    if (!empty($activeUser ?? null)) {
        $chips[] = ['label' => 'User', 'value' => $activeUser->name];
    } elseif (!empty($filters['user_id']) && !empty($users ?? null)) {
        $user = $users->firstWhere('id', (int) $filters['user_id']);
        if ($user) {
            $chips[] = ['label' => 'User', 'value' => $user->name];
        }
    }

    if (!empty($filters['call_type'])) {
        $chips[] = ['label' => 'Type', 'value' => $filters['call_type'] === 'not_picked' ? 'Not Picked' : ucfirst($filters['call_type'])];
    }

    if (!empty($filters['search'])) {
        $chips[] = ['label' => 'Search', 'value' => $filters['search']];
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
