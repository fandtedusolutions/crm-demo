@extends('layouts.mantis')

@section('title', 'Call Analytics - Telecaller Report')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/call-analytics.css') }}">
@endpush

@section('content')
@php
    use App\Helpers\DateRangeHelper;
    $tabQuery = \Illuminate\Support\Arr::except($queryParams, ['telecaller_id', 'metric']);

    $reportMetricUrl = function (string $metric, ?int $telecallerId = null) use ($queryParams, $filters) {
        $params = array_merge($queryParams, array_filter([
            'telecaller_id' => $telecallerId ?? ($filters['telecaller_id'] ?? null),
            'metric' => $metric,
        ], fn ($value) => $value !== null && $value !== ''));
        return route('admin.call-analytics.report', $params);
    };

    $telecallerReportUrl = function (int $telecallerId) use ($queryParams) {
        return route('admin.call-analytics.report.telecaller', array_merge(
            ['telecaller' => $telecallerId],
            \Illuminate\Support\Arr::except($queryParams, ['telecaller_id', 'metric'])
        ));
    };

    $isActiveMetric = fn (string $metric, ?int $telecallerId = null) => ($filters['metric'] ?? null) === $metric
        && (string) ($filters['telecaller_id'] ?? '') === (string) ($telecallerId ?? $filters['telecaller_id'] ?? '');
@endphp

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Telecaller Call Report</h5>
                    <p class="m-b-0 text-muted">Aggregated call activity per telecaller</p>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.call-analytics.index') }}">Call Analytics</a></li>
                    <li class="breadcrumb-item">Report</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@include('admin.call-analytics.partials.nav-tabs', ['activeTab' => 'report', 'tabQuery' => $tabQuery])

{{-- Filters --}}
<div class="row mb-3 no-print">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3">
                <h5 class="mb-0"><i class="ti ti-filter me-2"></i>Search &amp; Filters</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.call-analytics.report') }}">
                    @if(!empty($filters['metric']))
                        <input type="hidden" name="metric" value="{{ $filters['metric'] }}">
                    @endif
                    <div class="row g-3 align-items-end">
                        @include('admin.call-analytics.partials.date-range-filter')
                        <div class="col-md-3">
                            <label class="form-label">Telecaller</label>
                            <select name="telecaller_id" class="form-select form-select-sm">
                                <option value="">All Telecallers</option>
                                @foreach($telecallers as $telecaller)
                                    <option value="{{ $telecaller->id }}" {{ (string) $filters['telecaller_id'] === (string) $telecaller->id ? 'selected' : '' }}>
                                        {{ $telecaller->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-auto">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="ti ti-search me-1"></i> Apply
                            </button>
                            <a href="{{ route('admin.call-analytics.report') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="ti ti-refresh me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('admin.call-analytics.partials.active-filters', ['filters' => $filters, 'telecallers' => $telecallers])

@php $stats = $grandTotals; @endphp
@include('admin.call-analytics.partials.stats-cards')

<div class="ca-hint-banner no-print">
    <i class="ti ti-info-circle me-1"></i>
    Click any number in the table to drill down into call records. Click a telecaller name to view their full call log.
</div>

{{-- Report table --}}
<div class="row">
    <div class="col-12">
        <div class="card ca-table-card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h5 class="mb-0">Telecaller-wise Summary</h5>
                    <span class="badge bg-light-primary border border-primary ca-period-badge">
                        {{ DateRangeHelper::displayPeriod($filters) }}
                    </span>
                    <span class="badge bg-light text-dark border">{{ $rows->count() }} {{ $rows->count() === 1 ? 'telecaller' : 'telecallers' }}</span>
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm no-print" onclick="window.print()">
                    <i class="ti ti-printer me-1"></i> Print
                </button>
            </div>
            <div class="card-body">
                <div class="ca-table-scroll">
                    <table class="table table-hover align-middle mb-0" id="callAnalyticsReportTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Telecaller</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Connected <span class="text-muted fw-normal text-lowercase">(unique)</span></th>
                                <th class="text-center">Incoming</th>
                                <th class="text-center">Outgoing</th>
                                <th class="text-center">Not Picked</th>
                                <th class="text-center">Missed</th>
                                <th class="text-center">Rejected</th>
                                <th class="text-center">Talk Time</th>
                                <th class="text-center">Recording</th>
                                <th class="text-center">Uploaded</th>
                                <th class="text-center no-print"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $index => $row)
                                @php $telecaller = $telecallerMap->get($row->telecaller_id); @endphp
                                <tr>
                                    <td class="text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <a href="{{ $telecallerReportUrl($row->telecaller_id) }}" class="ca-telecaller-link">
                                            <div class="ca-telecaller-cell">
                                                <div class="avtar avtar-s rounded-circle bg-light-primary flex-shrink-0">
                                                    <i class="ti ti-user text-primary f-12"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $telecaller?->name ?? 'Unknown' }}</div>
                                                    <small class="text-muted">{{ $telecaller?->email }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ $reportMetricUrl('total', $row->telecaller_id) }}" class="report-metric-link {{ $isActiveMetric('total', $row->telecaller_id) ? 'is-active' : '' }}">{{ number_format($row->total_calls) }}</a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ $reportMetricUrl('connected', $row->telecaller_id) }}" class="report-metric-link {{ $isActiveMetric('connected', $row->telecaller_id) ? 'is-active' : '' }}">{{ number_format($row->connected_calls) }}</a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ $reportMetricUrl('incoming', $row->telecaller_id) }}" class="report-metric-link {{ $isActiveMetric('incoming', $row->telecaller_id) ? 'is-active' : '' }}">{{ number_format($row->incoming_calls) }}</a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ $reportMetricUrl('outgoing', $row->telecaller_id) }}" class="report-metric-link {{ $isActiveMetric('outgoing', $row->telecaller_id) ? 'is-active' : '' }}">{{ number_format($row->outgoing_calls) }}</a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ $reportMetricUrl('not_picked', $row->telecaller_id) }}" class="report-metric-link {{ $isActiveMetric('not_picked', $row->telecaller_id) ? 'is-active' : '' }}">{{ number_format($row->not_picked_calls) }}</a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ $reportMetricUrl('missed', $row->telecaller_id) }}" class="report-metric-link {{ $isActiveMetric('missed', $row->telecaller_id) ? 'is-active' : '' }}">{{ number_format($row->missed_calls) }}</a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ $reportMetricUrl('rejected', $row->telecaller_id) }}" class="report-metric-link {{ $isActiveMetric('rejected', $row->telecaller_id) ? 'is-active' : '' }}">{{ number_format($row->rejected_calls) }}</a>
                                    </td>
                                    <td class="text-center fw-medium">{{ \App\Models\CallAppLog::formatDuration((int) $row->total_duration_seconds) }}</td>
                                    <td class="text-center">{{ number_format($row->with_recording) }}</td>
                                    <td class="text-center">{{ number_format($row->recordings_uploaded) }}</td>
                                    <td class="text-center no-print">
                                        <a href="{{ $telecallerReportUrl($row->telecaller_id) }}" class="btn btn-outline-primary btn-sm ca-btn-icon" title="View all calls">
                                            <i class="ti ti-list"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13">
                                        <div class="ca-empty-state">
                                            <i class="ti ti-chart-bar"></i>
                                            <p>No report data found for the selected filters.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($rows->isNotEmpty())
                        <tfoot>
                            <tr>
                                <td colspan="2"><strong>Grand Total</strong></td>
                                <td class="text-center"><a href="{{ $reportMetricUrl('total') }}" class="report-metric-link {{ $isActiveMetric('total') ? 'is-active' : '' }}">{{ number_format($grandTotals['total_calls']) }}</a></td>
                                <td class="text-center"><a href="{{ $reportMetricUrl('connected') }}" class="report-metric-link {{ $isActiveMetric('connected') ? 'is-active' : '' }}">{{ number_format($grandTotals['connected_calls']) }}</a></td>
                                <td class="text-center"><a href="{{ $reportMetricUrl('incoming') }}" class="report-metric-link {{ $isActiveMetric('incoming') ? 'is-active' : '' }}">{{ number_format($grandTotals['incoming_calls']) }}</a></td>
                                <td class="text-center"><a href="{{ $reportMetricUrl('outgoing') }}" class="report-metric-link {{ $isActiveMetric('outgoing') ? 'is-active' : '' }}">{{ number_format($grandTotals['outgoing_calls']) }}</a></td>
                                <td class="text-center"><a href="{{ $reportMetricUrl('not_picked') }}" class="report-metric-link {{ $isActiveMetric('not_picked') ? 'is-active' : '' }}">{{ number_format($grandTotals['not_picked_calls']) }}</a></td>
                                <td class="text-center"><a href="{{ $reportMetricUrl('missed') }}" class="report-metric-link {{ $isActiveMetric('missed') ? 'is-active' : '' }}">{{ number_format($grandTotals['missed_calls']) }}</a></td>
                                <td class="text-center"><a href="{{ $reportMetricUrl('rejected') }}" class="report-metric-link {{ $isActiveMetric('rejected') ? 'is-active' : '' }}">{{ number_format($grandTotals['rejected_calls']) }}</a></td>
                                <td class="text-center">{{ \App\Models\CallAppLog::formatDuration($grandTotals['total_duration_seconds']) }}</td>
                                <td class="text-center">{{ number_format($grandTotals['with_recording']) }}</td>
                                <td class="text-center">{{ number_format($grandTotals['recordings_uploaded']) }}</td>
                                <td class="no-print"></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@if(!empty($detail))
    @include('admin.call-analytics.partials.report-detail')
@endif
@endsection

@push('scripts')
@include('admin.call-analytics.partials.recording-scripts')
@endpush
