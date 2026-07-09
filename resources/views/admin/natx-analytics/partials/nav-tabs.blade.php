@php
    use App\Helpers\DateRangeHelper;

    $activeTab = $activeTab ?? 'index';
    $tabQuery = $tabQuery ?? \Illuminate\Support\Arr::only(request()->query(), ['date_range', 'start_date', 'end_date']);

    if (empty($tabQuery['date_range'])) {
        $tabQuery['date_range'] = DateRangeHelper::natxDefaultPreset();
    }
@endphp
<div class="ca-page-tabs no-print">
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'index' ? 'active' : '' }}"
               href="{{ route('admin.natx-analytics.index', $tabQuery) }}">
                <i class="ti ti-list me-1"></i> Call Logs
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'report' ? 'active' : '' }}"
               href="{{ route('admin.natx-analytics.report', $tabQuery) }}">
                <i class="ti ti-chart-bar me-1"></i> User Report
            </a>
        </li>
    </ul>
</div>
