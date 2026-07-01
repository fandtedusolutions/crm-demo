@extends('layouts.mantis')

@section('title', 'Telecaller Report — ' . $user->name)

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/call-analytics.css') }}">
<style>
.tr-hero {
    background: linear-gradient(135deg, #4f46e5 0%, #0ea5e9 100%);
    border-radius: 12px;
    color: #fff;
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.25rem;
    box-shadow: 0 8px 24px rgba(79, 70, 229, 0.25);
}
.tr-hero h5 { color: #fff; font-weight: 600; }
.tr-filter-card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(15, 23, 42, 0.06);
}
.tr-stat-card {
    border: none;
    border-radius: 12px;
    padding: 1.1rem 1.25rem;
    height: 100%;
    color: #fff;
    box-shadow: 0 4px 14px rgba(0,0,0,0.08);
}
.tr-stat-card .stat-value { font-size: 1.75rem; font-weight: 700; line-height: 1.2; }
.tr-stat-card .stat-label { opacity: .9; font-size: .85rem; margin-bottom: .25rem; }
.tr-stat-primary { background: linear-gradient(135deg, #3b82f6, #2563eb); }
.tr-stat-success { background: linear-gradient(135deg, #22c55e, #16a34a); }
.tr-stat-info { background: linear-gradient(135deg, #06b6d4, #0891b2); }
.tr-stat-warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
.tr-mini-stat {
    border-radius: 10px;
    padding: .75rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    text-align: center;
    height: 100%;
}
.tr-mini-stat .val { font-weight: 700; font-size: 1.1rem; color: #0f172a; }
.tr-mini-stat .lbl { font-size: .75rem; color: #64748b; }
.tr-table-card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(15, 23, 42, 0.06);
    overflow: hidden;
}
.tr-table-card .card-header {
    background: #fff;
    border-bottom: 1px solid #e2e8f0;
    padding: 1rem 1.25rem;
}
.tr-telecaller-avatar {
    width: 48px; height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    font-size: 1.25rem;
}
.tr-breakdown-bar {
    height: 6px;
    border-radius: 3px;
    background: #e2e8f0;
    overflow: hidden;
}
.tr-breakdown-bar .fill { height: 100%; border-radius: 3px; }
</style>
@endpush

@section('content')
@php
    use App\Helpers\DateRangeHelper;
    $activeDateRange = $dateRange ?? DateRangeHelper::defaultPreset();
    $isCustomRange = $activeDateRange === DateRangeHelper::PRESET_CUSTOM;
    $periodLabel = DateRangeHelper::displayPeriod([
        'date_range' => $activeDateRange,
        'start_date' => $fromDate,
        'end_date' => $toDate,
    ]);
    $backUrl = route('admin.reports.telecaller', $filterQueryParams);
    $totalLeadsForPct = max($leadSummary['total_leads'], 1);
@endphp

<div class="page-header no-print">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="page-header-title">
                    <h5 class="m-b-10">Telecaller Detail Report</h5>
                    <p class="text-muted mb-0">Lead &amp; call performance for a single telecaller</p>
                </div>
            </div>
            <div class="col-md-4">
                <ul class="breadcrumb d-flex justify-content-end mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reports.leads') }}">Reports</a></li>
                    <li class="breadcrumb-item"><a href="{{ $backUrl }}">Telecaller</a></li>
                    <li class="breadcrumb-item">{{ $user->name }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="tr-hero no-print">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="tr-telecaller-avatar"><i class="ti ti-user"></i></div>
            <div>
                <h5 class="mb-1">{{ $user->name }}</h5>
                <small class="opacity-90">
                    @if($user->team)
                        <i class="ti ti-users me-1"></i>{{ $user->team->name }}
                    @endif
                    @if($user->phone)
                        <span class="mx-2">·</span><i class="ti ti-phone me-1"></i>{{ $user->phone }}
                    @endif
                </small>
                <div class="mt-1"><small class="opacity-75"><i class="ti ti-calendar me-1"></i>{{ $periodLabel }}</small></div>
            </div>
        </div>
        <a href="{{ $backUrl }}" class="btn btn-light btn-sm">
            <i class="ti ti-arrow-left me-1"></i>Back to Telecaller Report
        </a>
    </div>
</div>

<!-- Filters -->
<div class="row mb-3 no-print">
    <div class="col-12">
        <div class="card tr-filter-card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.telecaller.detail', $user->id) }}" id="detailFilterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Date Range</label>
                            <select class="form-select" name="date_range" id="detail_date_range">
                                @foreach(DateRangeHelper::options() as $value => $label)
                                    <option value="{{ $value }}" {{ $activeDateRange === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 detail-custom-dates" style="{{ $isCustomRange ? '' : 'display:none;' }}">
                            <label class="form-label fw-semibold">From</label>
                            <input type="date" class="form-control" name="date_from" id="detail_date_from" value="{{ $fromDate }}">
                        </div>
                        <div class="col-md-2 detail-custom-dates" style="{{ $isCustomRange ? '' : 'display:none;' }}">
                            <label class="form-label fw-semibold">To</label>
                            <input type="date" class="form-control" name="date_to" id="detail_date_to" value="{{ $toDate }}">
                        </div>
                        <div class="col-md-auto">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-filter me-1"></i>Apply
                            </button>
                            <a href="{{ route('admin.reports.telecaller.detail', $user->id) }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Lead summary -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <small class="text-muted"><i class="ti ti-calendar me-1"></i>Lead metrics: created <strong>{{ DateRangeHelper::formatDisplay($fromDate) }}</strong> to <strong>{{ DateRangeHelper::formatDisplay($toDate) }}</strong></small>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="tr-stat-card tr-stat-primary">
            <div class="stat-label">Total Leads</div>
            <div class="stat-value">{{ number_format($leadSummary['total_leads']) }}</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="tr-stat-card tr-stat-success">
            <div class="stat-label">Active Leads</div>
            <div class="stat-value">{{ number_format($leadSummary['active_leads']) }}</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="tr-stat-card tr-stat-info">
            <div class="stat-label">Converted · {{ $leadSummary['conversion_rate'] }}%</div>
            <div class="stat-value">{{ number_format($leadSummary['converted_leads']) }}</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="tr-stat-card tr-stat-warning">
            <div class="stat-label">Total Calls <small class="opacity-75">(Call Tracker)</small></div>
            <div class="stat-value">{{ number_format($callStats['total_calls'] ?? 0) }}</div>
        </div>
    </div>
</div>

<!-- Status & Source breakdown -->
<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card tr-table-card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="ti ti-chart-pie me-2 text-primary"></i>Status Breakdown</h5>
                <small class="text-muted">Leads by status · {{ $periodLabel }}</small>
            </div>
            <div class="card-body">
                @if($statusBreakdown->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="statusBreakdownTable">
                        <thead class="table-light">
                            <tr>
                                <th>Status</th>
                                <th class="text-end" style="width:80px;">Count</th>
                                <th style="width:120px;">Share</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($statusBreakdown as $row)
                            @php $pct = round(($row->count / $totalLeadsForPct) * 100, 1); @endphp
                            <tr>
                                <td>
                                    <span class="badge {{ \App\Helpers\StatusHelper::getLeadStatusColorClass($row->id) }}">{{ $row->title }}</span>
                                </td>
                                <td class="text-end fw-semibold">{{ number_format($row->count) }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="tr-breakdown-bar flex-grow-1">
                                            <div class="fill bg-primary" style="width:{{ $pct }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ $pct }}%</small>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center py-4 mb-0">No leads in this period.</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card tr-table-card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="ti ti-source-code me-2 text-info"></i>Source Breakdown</h5>
                <small class="text-muted">Leads by source · {{ $periodLabel }}</small>
            </div>
            <div class="card-body">
                @if($sourceBreakdown->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="sourceBreakdownTable">
                        <thead class="table-light">
                            <tr>
                                <th>Source</th>
                                <th class="text-end" style="width:80px;">Count</th>
                                <th style="width:120px;">Share</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sourceBreakdown as $row)
                            @php $pct = round(($row->count / $totalLeadsForPct) * 100, 1); @endphp
                            <tr>
                                <td class="fw-medium">{{ $row->title }}</td>
                                <td class="text-end fw-semibold">{{ number_format($row->count) }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="tr-breakdown-bar flex-grow-1">
                                            <div class="fill bg-info" style="width:{{ $pct }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ $pct }}%</small>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center py-4 mb-0">No leads in this period.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Call breakdown -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card tr-table-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="ti ti-phone-call me-2 text-warning"></i>Call Breakdown</h5>
                <small class="text-muted d-block mt-1">From call_app_logs · filtered by call date · {{ $periodLabel }}</small>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    @foreach([
                        ['label' => 'Total Calls', 'val' => $callStats['total_calls'] ?? 0],
                        ['label' => 'Connected (unique)', 'val' => $callStats['connected_calls'] ?? 0],
                        ['label' => 'Total Attended', 'val' => $callStats['attended_calls'] ?? 0],
                        ['label' => 'Incoming', 'val' => $callStats['incoming_calls'] ?? 0],
                        ['label' => 'Outgoing', 'val' => $callStats['outgoing_calls'] ?? 0],
                        ['label' => 'Not Picked', 'val' => $callStats['not_picked_calls'] ?? 0],
                        ['label' => 'Missed', 'val' => $callStats['missed_calls'] ?? 0],
                        ['label' => 'Rejected', 'val' => $callStats['rejected_calls'] ?? 0],
                        ['label' => 'Talk Time', 'val' => $callStats['talk_time'] ?? '0s', 'raw' => true],
                    ] as $chip)
                    <div class="col-lg-2 col-md-4 col-6">
                        <div class="tr-mini-stat">
                            <div class="val">{{ !empty($chip['raw']) ? $chip['val'] : number_format($chip['val']) }}</div>
                            <div class="lbl">{{ $chip['label'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Call logs -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card tr-table-card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="mb-0"><i class="ti ti-list me-2"></i>Call Logs</h5>
                    <small class="text-muted">{{ $periodLabel }}</small>
                </div>
                <span class="badge bg-light text-dark border">{{ $calls->total() }} calls</span>
            </div>
            <div class="card-body p-0">
                @if($calls->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="callLogsTable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Date &amp; Time</th>
                                <th>Contact</th>
                                <th>Phone</th>
                                <th>Type</th>
                                <th>Duration</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($calls as $index => $call)
                            <tr>
                                <td class="text-muted">{{ $calls->firstItem() + $index }}</td>
                                <td class="small">{{ $call->display_started_at?->format('d-m-Y h:i A') ?? '—' }}</td>
                                <td>{{ $call->contact_name ?: '—' }}</td>
                                <td class="fw-medium">{{ $call->phone_number }}</td>
                                <td>
                                    @if($call->call_type)
                                        <span class="badge bg-light text-dark border">{{ ucfirst(str_replace('_', ' ', $call->call_type)) }}</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ \App\Models\CallAppLog::formatDuration((int) $call->duration_seconds) }}</td>
                                <td class="small text-muted">{{ $call->remarks ?: '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($calls->hasPages())
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 p-3 border-top">
                    <small class="text-muted mb-0">
                        Showing {{ $calls->firstItem() }} to {{ $calls->lastItem() }} of {{ $calls->total() }} calls
                    </small>
                    {{ $calls->links('pagination::bootstrap-5') }}
                </div>
                @endif
                @else
                <p class="text-muted text-center py-4 mb-0">No calls found for this period.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Leads list -->
<div class="row">
    <div class="col-12">
        <div class="card tr-table-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="ti ti-users me-2"></i>Leads</h5>
                <small class="text-muted d-block mt-1">Created {{ DateRangeHelper::formatDisplay($fromDate) }} to {{ DateRangeHelper::formatDisplay($toDate) }}</small>
            </div>
            <div class="card-body">
                @if($leads->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="telecallerDetailLeadsTable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th><th>Name</th><th>Type</th><th>Phone</th><th>Email</th>
                                <th>Status</th><th>Source</th><th>Converted</th><th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leads as $index => $lead)
                            <tr>
                                <td>{{ $leads->firstItem() + $index }}</td>
                                <td class="fw-medium">{{ $lead->title }}</td>
                                <td>@if($lead->is_b2b)<span class="badge bg-info">B2B</span>@else<span class="badge bg-secondary">In House</span>@endif</td>
                                <td>{{ $lead->phone }}</td>
                                <td>{{ $lead->email ?: '—' }}</td>
                                <td><span class="badge {{ \App\Helpers\StatusHelper::getLeadStatusColorClass($lead->leadStatus->id) }}">{{ $lead->leadStatus->title ?? 'N/A' }}</span></td>
                                <td>{{ $lead->leadSource->title ?? 'N/A' }}</td>
                                <td>@if($lead->is_converted)<span class="badge bg-success">Yes</span>@else<span class="badge bg-light text-dark">No</span>@endif</td>
                                <td>{{ $lead->created_at->format('M d, Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($leads->hasPages())
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3 px-1">
                    <small class="text-muted mb-0">
                        Showing {{ $leads->firstItem() }} to {{ $leads->lastItem() }} of {{ $leads->total() }} leads
                    </small>
                    {{ $leads->links('pagination::bootstrap-5') }}
                </div>
                @endif
                @else
                <p class="text-muted text-center py-4 mb-0">No leads found.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function ($) {
    function toggleDetailCustomDates() {
        const isCustom = $('#detail_date_range').val() === 'custom';
        $('.detail-custom-dates').toggle(isCustom);
        $('#detail_date_from, #detail_date_to').prop('disabled', !isCustom);
    }

    $(document).on('change', '#detail_date_range', toggleDetailCustomDates);

    $(function () {
        toggleDetailCustomDates();
    });
})(jQuery);
</script>
@endpush
