@php
    $activeTab = $activeTab ?? 'index';
    $tabQuery = $tabQuery ?? [];
@endphp
<div class="ca-page-tabs no-print">
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'index' ? 'active' : '' }}"
               href="{{ route('admin.call-analytics.index', $tabQuery) }}">
                <i class="ti ti-list me-1"></i> Call Logs
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'report' ? 'active' : '' }}"
               href="{{ route('admin.call-analytics.report', $tabQuery) }}">
                <i class="ti ti-chart-bar me-1"></i> Telecaller Report
            </a>
        </li>
    </ul>
</div>
