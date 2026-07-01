@extends('layouts.mantis')

@section('title', 'Telecaller Report')

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
    transition: transform .15s ease;
}
.tr-stat-card:hover { transform: translateY(-2px); }
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
    width: 38px; height: 38px;
    border-radius: 10px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.tr-modal-header {
    background: linear-gradient(135deg, #4f46e5 0%, #0ea5e9 100%);
}
.tr-action-btn {
    border-radius: 8px;
    font-size: .8rem;
    padding: .35rem .65rem;
    white-space: nowrap;
}
#telecallerReportTable thead th {
    background: #f8fafc;
    font-size: .8rem;
    text-transform: uppercase;
    letter-spacing: .03em;
    color: #475569;
    border-bottom: 2px solid #e2e8f0;
}
#telecallerReportTable tbody tr:hover { background: #f8fafc; }
@media print {
    body * { visibility: hidden; }
    .printable-report, .printable-report * { visibility: visible; }
    .printable-report { position: absolute; left: 0; top: 0; width: 100%; }
    .no-print { display: none !important; }
}
</style>
@endpush

@section('content')
@php
    use App\Helpers\DateRangeHelper;
    $callTotals = $reportSummary['call_grand_totals'] ?? [];
    $activeDateRange = $dateRange ?? DateRangeHelper::defaultPreset();
    $isCustomRange = $activeDateRange === DateRangeHelper::PRESET_CUSTOM;
    $periodLabel = DateRangeHelper::displayPeriod([
        'date_range' => $activeDateRange,
        'start_date' => $fromDate,
        'end_date' => $toDate,
    ]);
@endphp

<div class="page-header no-print">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="page-header-title">
                    <h5 class="m-b-10">Telecaller Report</h5>
                    <p class="text-muted mb-0">Lead counts by assignment date · Call metrics from Call Tracker logs</p>
                </div>
            </div>
            <div class="col-md-4">
                <ul class="breadcrumb d-flex justify-content-end mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reports.leads') }}">Reports</a></li>
                    <li class="breadcrumb-item">Telecaller</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="tr-hero no-print">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h5 class="mb-1"><i class="ti ti-chart-bar me-2"></i>Performance Overview</h5>
            <small class="opacity-90">{{ $periodLabel }}</small>
        </div>
        <a href="{{ route('admin.reports.leads') }}" class="btn btn-light btn-sm">
            <i class="ti ti-arrow-left me-1"></i>Back to Reports
        </a>
    </div>
</div>

<!-- Filters -->
<div class="row mb-3 no-print">
    <div class="col-12">
        <div class="card tr-filter-card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.telecaller') }}" id="reportFilterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Date Range</label>
                            <select class="form-select" name="date_range" id="report_date_range">
                                @foreach(DateRangeHelper::options() as $value => $label)
                                    <option value="{{ $value }}" {{ $activeDateRange === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 report-custom-dates" style="{{ $isCustomRange ? '' : 'display:none;' }}">
                            <label class="form-label fw-semibold">From</label>
                            <input type="date" class="form-control" name="date_from" id="date_from" value="{{ $fromDate }}">
                        </div>
                        <div class="col-md-2 report-custom-dates" style="{{ $isCustomRange ? '' : 'display:none;' }}">
                            <label class="form-label fw-semibold">To</label>
                            <input type="date" class="form-control" name="date_to" id="date_to" value="{{ $toDate }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Telecaller</label>
                            <select class="form-select" name="telecaller_id" id="telecaller_id">
                                <option value="">All Telecallers</option>
                                @foreach($telecallers as $telecaller)
                                    <option value="{{ $telecaller->id }}" {{ (string) $telecallerId === (string) $telecaller->id ? 'selected' : '' }}>
                                        {{ $telecaller->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-auto">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-filter me-1"></i>Generate
                            </button>
                            <a href="{{ route('admin.reports.telecaller') }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                        <div class="col-md-auto ms-auto">
                            <div class="btn-group">
                                <a href="{{ route('admin.reports.telecaller.excel', request()->query()) }}" class="btn btn-success btn-sm">
                                    <i class="ti ti-file-excel"></i> Excel
                                </a>
                                <a href="{{ route('admin.reports.telecaller.pdf', request()->query()) }}" class="btn btn-danger btn-sm">
                                    <i class="ti ti-file-pdf"></i> PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="printable-report">
<div class="row g-3 mb-4">
    <div class="col-12">
        <small class="text-muted"><i class="ti ti-calendar me-1"></i>Lead metrics: leads created <strong>{{ DateRangeHelper::formatDisplay($fromDate) }}</strong> to <strong>{{ DateRangeHelper::formatDisplay($toDate) }}</strong></small>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="tr-stat-card tr-stat-primary">
            <div class="stat-label">Total Leads</div>
            <div class="stat-value">{{ number_format($reportSummary['total_leads'] ?? 0) }}</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="tr-stat-card tr-stat-success">
            <div class="stat-label">Active Leads</div>
            <div class="stat-value">{{ number_format($reportSummary['active_leads'] ?? 0) }}</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="tr-stat-card tr-stat-info">
            <div class="stat-label">Converted · {{ $reportSummary['conversion_rate'] ?? 0 }}%</div>
            <div class="stat-value">{{ number_format($reportSummary['converted_leads'] ?? 0) }}</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="tr-stat-card tr-stat-warning">
            <div class="stat-label">Total Calls <small class="opacity-75">(Call Tracker)</small></div>
            <div class="stat-value">{{ number_format($callTotals['total_calls'] ?? 0) }}</div>
        </div>
    </div>
</div>

<div class="d-flex align-items-center gap-2 mb-2">
    <span class="badge bg-light text-dark border"><i class="ti ti-database me-1"></i>Source: call_app_logs</span>
    <small class="text-muted">Filtered by call date (started_at)</small>
</div>

<div class="row g-2 mb-4">
    @foreach([
        ['label' => 'Connected (unique)', 'val' => $callTotals['connected_calls'] ?? 0],
        ['label' => 'Incoming', 'val' => $callTotals['incoming_calls'] ?? 0],
        ['label' => 'Outgoing', 'val' => $callTotals['outgoing_calls'] ?? 0],
        ['label' => 'Not Picked', 'val' => $callTotals['not_picked_calls'] ?? 0],
        ['label' => 'Missed', 'val' => $callTotals['missed_calls'] ?? 0],
        ['label' => 'Rejected', 'val' => $callTotals['rejected_calls'] ?? 0],
    ] as $chip)
    <div class="col-lg-2 col-md-4 col-6">
        <div class="tr-mini-stat">
            <div class="val">{{ number_format($chip['val']) }}</div>
            <div class="lbl">{{ $chip['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card tr-table-card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="mb-0"><i class="ti ti-users me-2 text-primary"></i>Telecaller Performance</h5>
                    <small class="text-muted">{{ $periodLabel }}@if($telecallerId) · {{ $telecallers->where('id', $telecallerId)->first()->name ?? 'Selected telecaller' }}@endif</small>
                </div>
                <span class="badge bg-light-primary text-primary border border-primary">{{ $reports['telecaller']->count() }} telecallers</span>
            </div>
            <div class="card-body p-0">
                @if($reports['telecaller']->count() > 0)
                <div class="table-responsive">
                    <table id="telecallerReportTable" class="table table-hover align-middle mb-0" style="min-width: 1500px;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Telecaller</th>
                                <th>Team</th>
                                <th class="text-end">Leads</th>
                                <th class="text-end">Active</th>
                                <th class="text-end">Converted</th>
                                <th class="text-end">Conv.%</th>
                                <th class="text-end" title="From call_app_logs">Calls</th>
                                <th class="text-end" title="Unique contacts from call_app_logs">Connected</th>
                                <th class="text-end" title="Incoming calls from call_app_logs">In</th>
                                <th class="text-end" title="Outgoing calls from call_app_logs">Out</th>
                                <th class="text-end">Talk</th>
                                <th class="text-center no-print">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports['telecaller'] as $index => $telecaller)
                            <tr>
                                <td class="text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="tr-telecaller-avatar"><i class="ti ti-user"></i></div>
                                        <div>
                                            <div class="fw-semibold">{{ $telecaller->name }}</div>
                                            <small class="text-muted">{{ $telecaller->phone ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-light-info text-info border">{{ $telecaller->team_name ?? 'No Team' }}</span></td>
                                <td class="text-end fw-bold">{{ number_format($telecaller->total_leads) }}</td>
                                <td class="text-end">{{ number_format($telecaller->active_leads) }}</td>
                                <td class="text-end text-success fw-semibold">{{ number_format($telecaller->converted_leads) }}</td>
                                <td class="text-end">{{ $telecaller->conversion_rate }}%</td>
                                <td class="text-end">{{ number_format($telecaller->total_calls) }}</td>
                                <td class="text-end">{{ number_format($telecaller->connected_calls) }}</td>
                                <td class="text-end">{{ number_format($telecaller->incoming_calls) }}</td>
                                <td class="text-end">{{ number_format($telecaller->outgoing_calls) }}</td>
                                <td class="text-end">{{ \App\Models\CallAppLog::formatDuration((int) $telecaller->total_duration_seconds) }}</td>
                                <td class="text-center no-print">
                                    <button type="button"
                                        class="btn btn-primary tr-action-btn js-open-call-analytics"
                                        data-telecaller-id="{{ $telecaller->id }}"
                                        data-telecaller-name="{{ $telecaller->name }}"
                                        title="View call analytics">
                                        <i class="ti ti-chart-dots me-1"></i>Call Analytics
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td colspan="3" class="text-end">Grand Total</td>
                                <td class="text-end">{{ number_format($reportSummary['total_leads'] ?? 0) }}</td>
                                <td class="text-end">{{ number_format($reportSummary['active_leads'] ?? 0) }}</td>
                                <td class="text-end">{{ number_format($reportSummary['converted_leads'] ?? 0) }}</td>
                                <td class="text-end">{{ $reportSummary['conversion_rate'] ?? 0 }}%</td>
                                <td class="text-end">{{ number_format($callTotals['total_calls'] ?? 0) }}</td>
                                <td class="text-end">{{ number_format($callTotals['connected_calls'] ?? 0) }}</td>
                                <td class="text-end">{{ number_format($callTotals['incoming_calls'] ?? 0) }}</td>
                                <td class="text-end">{{ number_format($callTotals['outgoing_calls'] ?? 0) }}</td>
                                <td class="text-end">{{ \App\Models\CallAppLog::formatDuration((int) ($callTotals['total_duration_seconds'] ?? 0)) }}</td>
                                <td class="no-print"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-5">
                    <i class="ti ti-chart-bar-off f-48 mb-3 d-block"></i>
                    <p class="mb-0">No data for the selected period.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($telecallerId)
<div class="row">
    <div class="col-12">
        <div class="card tr-table-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="ti ti-list me-2"></i>Leads — {{ $telecallers->where('id', $telecallerId)->first()->name ?? 'Selected' }}</h5>
                <small class="text-muted d-block mt-1">Created {{ DateRangeHelper::formatDisplay($fromDate) }} to {{ DateRangeHelper::formatDisplay($toDate) }}</small>
            </div>
            <div class="card-body">
                @if($leads->count() > 0)
                <div class="table-responsive">
                    <table id="leadsDataTable" class="table table-hover">
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
                @if($leads->hasPages())<div class="mt-3">{{ $leads->appends(request()->query())->links() }}</div>@endif
                @else
                <p class="text-muted text-center py-4 mb-0">No leads found.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
</div>

@include('admin.reports.partials.telecaller-call-analytics-modal')
@endsection

@push('scripts')
<script>
(function ($) {
    const buildAnalyticsUrl = function (id) {
        return @json(url('/admin/reports/telecaller')) + '/' + id + '/call-analytics';
    };
    const reportFromDate = @json($fromDate);
    const reportToDate = @json($toDate);
    const reportDateRange = @json($activeDateRange);

    function toggleReportCustomDates() {
        const isCustom = $('#report_date_range').val() === 'custom';
        $('.report-custom-dates').toggle(isCustom);
        $('#date_from, #date_to').prop('disabled', !isCustom);
    }

    function toggleModalCustomDates() {
        const $form = $('#tcCallAnalyticsFilterForm');
        const isCustom = $form.find('.js-call-analytics-date-range').val() === 'custom';
        $form.find('.js-call-analytics-custom-dates').toggle(isCustom);
        $form.find('.js-call-analytics-start-date, .js-call-analytics-end-date').prop('disabled', !isCustom);
    }

    function callTypeBadge(type) {
        const map = {
            incoming: 'success',
            outgoing: 'primary',
            missed: 'warning',
            rejected: 'danger',
            not_picked: 'secondary',
            unknown: 'light text-dark'
        };
        const raw = (type || '').toLowerCase().replace(/\s+/g, '_');
        const cls = map[raw] || 'secondary';
        return '<span class="badge bg-' + cls + '">' + (type || '—') + '</span>';
    }

    function renderStats(stats) {
        const items = [
            { label: 'Total Calls', val: stats.total_calls, icon: 'ti-phone-call', cls: 'icon-primary' },
            { label: 'Connected (unique)', val: stats.connected_calls, icon: 'ti-phone-check', cls: 'icon-success' },
            { label: 'Incoming', val: stats.incoming_calls, icon: 'ti-phone-incoming', cls: 'icon-info' },
            { label: 'Outgoing', val: stats.outgoing_calls, icon: 'ti-phone-outgoing', cls: 'icon-primary' },
            { label: 'Not Picked', val: stats.not_picked_calls, icon: 'ti-phone-off', cls: 'icon-muted' },
            { label: 'Missed', val: stats.missed_calls, icon: 'ti-phone-x', cls: 'icon-warning' },
            { label: 'Rejected', val: stats.rejected_calls, icon: 'ti-ban', cls: 'icon-warning' },
            { label: 'Talk Time', val: stats.talk_time, icon: 'ti-clock', cls: 'icon-info', raw: true },
        ];

        let html = '';
        items.forEach(function (item) {
            const display = item.raw ? item.val : Number(item.val || 0).toLocaleString();
            html += '<div class="col-6 col-md-3 col-lg-3">' +
                '<div class="card ca-stat-card h-100"><div class="card-body py-3">' +
                '<div class="d-flex align-items-center justify-content-between">' +
                '<div><h6 class="mb-1 f-w-400 text-muted small">' + item.label + '</h6>' +
                '<h5 class="mb-0">' + display + '</h5></div>' +
                '<div class="stat-icon ' + item.cls + '"><i class="ti ' + item.icon + '"></i></div>' +
                '</div></div></div></div>';
        });
        $('#tcCallAnalyticsStats').html(html);
    }

    function renderCalls(calls) {
        $('#tcCallAnalyticsCallsCount').text((calls.length || 0) + ' calls');
        if (!calls.length) {
            $('#tcCallAnalyticsCallsBody').html('<tr><td colspan="7" class="text-center text-muted py-4">No calls found for this period.</td></tr>');
            return;
        }
        let html = '';
        calls.forEach(function (c) {
            const rec = c.recording_uploaded
                ? '<span class="badge bg-success">Yes</span>'
                : (c.has_recording ? '<span class="badge bg-warning text-dark">Pending</span>' : '<span class="text-muted">—</span>');
            html += '<tr>' +
                '<td class="small">' + c.started_at + '</td>' +
                '<td>' + $('<div>').text(c.contact_name).html() + '</td>' +
                '<td class="fw-medium">' + $('<div>').text(c.phone_number).html() + '</td>' +
                '<td>' + callTypeBadge(c.call_type) + '</td>' +
                '<td>' + c.duration + '</td>' +
                '<td class="small text-muted">' + $('<div>').text(c.remarks).html() + '</td>' +
                '<td class="text-center">' + rec + '</td></tr>';
        });
        $('#tcCallAnalyticsCallsBody').html(html);
    }

    function loadCallAnalytics() {
        const telecallerId = $('#tcCallAnalyticsTelecallerId').val();
        if (!telecallerId) return;

        const params = {
            date_range: $('#tcCallAnalyticsDateRange').val(),
            start_date: $('#tcCallAnalyticsStartDate').val(),
            end_date: $('#tcCallAnalyticsEndDate').val(),
            date_from: $('#tcCallAnalyticsStartDate').val(),
            date_to: $('#tcCallAnalyticsEndDate').val(),
        };

        $('#tcCallAnalyticsLoading').show();
        $('#tcCallAnalyticsContent').css('opacity', '0.45');

        $.get(buildAnalyticsUrl(telecallerId), params)
            .done(function (res) {
                if (!res.status) {
                    alert(res.message || 'Failed to load analytics.');
                    return;
                }
                $('#tcCallAnalyticsPeriodBadge').text(res.period.label);
                $('#tcCallAnalyticsFullReportLink').attr('href', res.full_report_url);
                renderStats(res.stats);
                renderCalls(res.calls);
            })
            .fail(function (xhr) {
                const msg = xhr.responseJSON?.message || 'Failed to load call analytics.';
                alert(msg);
            })
            .always(function () {
                $('#tcCallAnalyticsLoading').hide();
                $('#tcCallAnalyticsContent').css('opacity', '1');
            });
    }

    $(document).on('change', '#report_date_range', toggleReportCustomDates);

    $('#reportFilterForm').on('submit', function () {
        toggleReportCustomDates();
    });

    $(document).on('change', '#tcCallAnalyticsDateRange', toggleModalCustomDates);

    $('#tcCallAnalyticsFilterForm').on('submit', function (e) {
        e.preventDefault();
        loadCallAnalytics();
    });

    $(document).on('click', '.js-open-call-analytics', function () {
        const id = $(this).data('telecaller-id');
        const name = $(this).data('telecaller-name');
        $('#tcCallAnalyticsTelecallerId').val(id);
        $('#tcCallAnalyticsSubtitle').text(name);
        $('#telecallerCallAnalyticsModalLabel').html('<i class="ti ti-chart-dots me-2"></i>Call Analytics — ' + $('<div>').text(name).html());

        $('#tcCallAnalyticsDateRange').val(reportDateRange);
        $('#tcCallAnalyticsStartDate').val(reportFromDate);
        $('#tcCallAnalyticsEndDate').val(reportToDate);
        toggleModalCustomDates();

        const modal = new bootstrap.Modal(document.getElementById('telecallerCallAnalyticsModal'));
        modal.show();
        loadCallAnalytics();
    });

    $(function () {
        toggleReportCustomDates();

        if ($.fn.DataTable.isDataTable('#telecallerReportTable')) {
            $('#telecallerReportTable').DataTable().destroy();
        }
        if ($('#telecallerReportTable').length) {
            $('#telecallerReportTable').DataTable({
                scrollX: true,
                pageLength: 25,
                order: [[3, 'desc']],
                columnDefs: [
                    { orderable: false, targets: [0, 12] }
                ],
            });
        }

        if ($('#leadsDataTable').length && !$.fn.DataTable.isDataTable('#leadsDataTable')) {
            $('#leadsDataTable').DataTable({ pageLength: 25, order: [[8, 'desc']] });
        }
    });
})(jQuery);
</script>
@endpush
