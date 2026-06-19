@extends('layouts.mantis')

@section('title', 'Call Analytics - Telecaller Report')

@push('styles')
<style>
    .card-body .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    #callAnalyticsReportTable {
        width: max-content !important;
        min-width: 100%;
        margin-bottom: 0;
        border-collapse: collapse;
    }

    #callAnalyticsReportTable thead th {
        background: #f8f9fa;
        font-weight: 600;
        font-size: 12px;
        padding: 10px 8px;
        vertical-align: middle;
        white-space: nowrap;
        border-bottom: 2px solid #dee2e6;
    }

    #callAnalyticsReportTable tbody td {
        font-size: 12px;
        padding: 8px;
        vertical-align: middle;
        border-bottom: 1px solid #eee;
    }

    #callAnalyticsReportTable tbody tr:hover {
        background: #f8f9ff;
    }

    .report-metric-link {
        color: inherit;
        text-decoration: none;
        display: inline-block;
        padding: 2px 6px;
        border-radius: 4px;
        transition: background-color .15s ease;
    }

    .report-metric-link:hover {
        background: rgba(13, 110, 253, 0.12);
        color: #0d6efd;
        text-decoration: underline;
    }

    .report-metric-link.is-active {
        background: rgba(13, 110, 253, 0.18);
        color: #0d6efd;
        font-weight: 700;
    }

    @media print {
        .no-print { display: none !important; }
    }
</style>
@endpush

@section('content')
@php
    use App\Helpers\DateRangeHelper;

    $reportMetricUrl = function (string $metric, ?int $telecallerId = null) use ($filters) {
        $params = array_merge(
            DateRangeHelper::queryParams($filters),
            request()->only(['user_id', 'user_name', 'role_id', 'role_title']),
            array_filter([
                'telecaller_id' => $telecallerId ?? ($filters['telecaller_id'] ?? null),
                'metric' => $metric,
            ], fn ($value) => $value !== null && $value !== '')
        );

        return route('admin.call-analytics.report', $params);
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

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="mb-0">Telecaller-wise Summary</h5>
                    <small class="text-muted">Aggregated call activity from Call Tracker app</small>
                </div>
                <div class="d-flex gap-2 no-print">
                    <a href="{{ route('admin.call-analytics.index', request()->query()) }}" class="btn btn-outline-primary btn-sm">
                        <i class="ti ti-list"></i> Call Logs
                    </a>
                    <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
                        <i class="ti ti-printer"></i> Print
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="card border-0 shadow-sm mb-4 no-print">
                    <div class="card-body bg-light">
                        <form method="GET" action="{{ route('admin.call-analytics.report') }}">
                            @if(!empty($filters['metric']))
                                <input type="hidden" name="metric" value="{{ $filters['metric'] }}">
                            @endif
                            <div class="row g-3 align-items-end">
                                @include('admin.call-analytics.partials.date-range-filter')
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Telecaller</label>
                                    <select name="telecaller_id" class="form-select form-select-sm">
                                        <option value="">All Telecallers</option>
                                        @foreach($telecallers as $telecaller)
                                            <option value="{{ $telecaller->id }}" {{ (string) $filters['telecaller_id'] === (string) $telecaller->id ? 'selected' : '' }}>
                                                {{ $telecaller->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="ti ti-search"></i> Filter
                                        </button>
                                        <a href="{{ route('admin.call-analytics.report') }}" class="btn btn-outline-secondary btn-sm">
                                            <i class="ti ti-refresh"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="callAnalyticsReportTable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Telecaller</th>
                                <th class="text-center">Total Calls</th>
                                <th class="text-center">Connected Calls</th>
                                <th class="text-center">Incoming</th>
                                <th class="text-center">Outgoing</th>
                                <th class="text-center">Not Picked</th>
                                <th class="text-center">Missed</th>
                                <th class="text-center">Rejected</th>
                                <th class="text-center">Talk Time</th>
                                <th class="text-center">With Recording</th>
                                <th class="text-center">Uploaded</th>
                                <th class="text-center no-print">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $index => $row)
                                @php $telecaller = $telecallerMap->get($row->telecaller_id); @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $telecaller?->name ?? 'Unknown' }}</div>
                                        <small class="text-muted">{{ $telecaller?->email }}</small>
                                    </td>
                                    <td class="text-center fw-semibold">
                                        <a href="{{ $reportMetricUrl('total', $row->telecaller_id) }}"
                                           class="report-metric-link {{ $isActiveMetric('total', $row->telecaller_id) ? 'is-active' : '' }}">
                                            {{ number_format($row->total_calls) }}
                                        </a>
                                    </td>
                                    <td class="text-center fw-semibold text-primary">
                                        <a href="{{ $reportMetricUrl('connected', $row->telecaller_id) }}"
                                           class="report-metric-link {{ $isActiveMetric('connected', $row->telecaller_id) ? 'is-active' : '' }}">
                                            {{ number_format($row->connected_calls) }}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ $reportMetricUrl('incoming', $row->telecaller_id) }}"
                                           class="report-metric-link {{ $isActiveMetric('incoming', $row->telecaller_id) ? 'is-active' : '' }}">
                                            {{ number_format($row->incoming_calls) }}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ $reportMetricUrl('outgoing', $row->telecaller_id) }}"
                                           class="report-metric-link {{ $isActiveMetric('outgoing', $row->telecaller_id) ? 'is-active' : '' }}">
                                            {{ number_format($row->outgoing_calls) }}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ $reportMetricUrl('not_picked', $row->telecaller_id) }}"
                                           class="report-metric-link {{ $isActiveMetric('not_picked', $row->telecaller_id) ? 'is-active' : '' }}">
                                            {{ number_format($row->not_picked_calls) }}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ $reportMetricUrl('missed', $row->telecaller_id) }}"
                                           class="report-metric-link {{ $isActiveMetric('missed', $row->telecaller_id) ? 'is-active' : '' }}">
                                            {{ number_format($row->missed_calls) }}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ $reportMetricUrl('rejected', $row->telecaller_id) }}"
                                           class="report-metric-link {{ $isActiveMetric('rejected', $row->telecaller_id) ? 'is-active' : '' }}">
                                            {{ number_format($row->rejected_calls) }}
                                        </a>
                                    </td>
                                    <td class="text-center">{{ \App\Models\CallAppLog::formatDuration((int) $row->total_duration_seconds) }}</td>
                                    <td class="text-center">{{ number_format($row->with_recording) }}</td>
                                    <td class="text-center">{{ number_format($row->recordings_uploaded) }}</td>
                                    <td class="text-center no-print">
                                        <a href="{{ route('admin.call-analytics.index', array_merge(request()->query(), ['telecaller_id' => $row->telecaller_id])) }}"
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="ti ti-list"></i> View Calls
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center text-muted py-4">No report data found for the selected filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($rows->isNotEmpty())
                        <tfoot class="table-light fw-semibold">
                            <tr>
                                <td colspan="2">Grand Total</td>
                                <td class="text-center">
                                    <a href="{{ $reportMetricUrl('total') }}"
                                       class="report-metric-link {{ $isActiveMetric('total') ? 'is-active' : '' }}">
                                        {{ number_format($grandTotals['total_calls']) }}
                                    </a>
                                </td>
                                <td class="text-center text-primary">
                                    <a href="{{ $reportMetricUrl('connected') }}"
                                       class="report-metric-link {{ $isActiveMetric('connected') ? 'is-active' : '' }}">
                                        {{ number_format($grandTotals['connected_calls']) }}
                                    </a>
                                </td>
                                <td class="text-center">
                                    <a href="{{ $reportMetricUrl('incoming') }}"
                                       class="report-metric-link {{ $isActiveMetric('incoming') ? 'is-active' : '' }}">
                                        {{ number_format($grandTotals['incoming_calls']) }}
                                    </a>
                                </td>
                                <td class="text-center">
                                    <a href="{{ $reportMetricUrl('outgoing') }}"
                                       class="report-metric-link {{ $isActiveMetric('outgoing') ? 'is-active' : '' }}">
                                        {{ number_format($grandTotals['outgoing_calls']) }}
                                    </a>
                                </td>
                                <td class="text-center">
                                    <a href="{{ $reportMetricUrl('not_picked') }}"
                                       class="report-metric-link {{ $isActiveMetric('not_picked') ? 'is-active' : '' }}">
                                        {{ number_format($grandTotals['not_picked_calls']) }}
                                    </a>
                                </td>
                                <td class="text-center">
                                    <a href="{{ $reportMetricUrl('missed') }}"
                                       class="report-metric-link {{ $isActiveMetric('missed') ? 'is-active' : '' }}">
                                        {{ number_format($grandTotals['missed_calls']) }}
                                    </a>
                                </td>
                                <td class="text-center">
                                    <a href="{{ $reportMetricUrl('rejected') }}"
                                       class="report-metric-link {{ $isActiveMetric('rejected') ? 'is-active' : '' }}">
                                        {{ number_format($grandTotals['rejected_calls']) }}
                                    </a>
                                </td>
                                <td class="text-center">{{ \App\Models\CallAppLog::formatDuration($grandTotals['total_duration_seconds']) }}</td>
                                <td class="text-center">{{ number_format($grandTotals['with_recording']) }}</td>
                                <td class="text-center">{{ number_format($grandTotals['recordings_uploaded']) }}</td>
                                <td class="no-print"></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>

                @if(!empty($detail))
                    @include('admin.call-analytics.partials.report-detail')
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function pauseOtherRecordings(activeId) {
        document.querySelectorAll('.call-recording-player').forEach(function (player) {
            if (player.id !== activeId) {
                player.style.display = 'none';
                const audio = player.querySelector('audio');
                if (audio) {
                    audio.pause();
                    audio.currentTime = 0;
                }
            }
        });
    }

    $(document).on('click', '.js-toggle-recording', function () {
        const targetId = $(this).data('target');
        const player = document.getElementById(targetId);
        if (!player) {
            return;
        }

        const isHidden = player.style.display === 'none' || player.style.display === '';
        pauseOtherRecordings(targetId);

        if (isHidden) {
            player.style.display = 'block';
            const audio = player.querySelector('audio');
            if (audio) {
                audio.play().catch(function () {});
            }
        } else {
            player.style.display = 'none';
            const audio = player.querySelector('audio');
            if (audio) {
                audio.pause();
            }
        }
    });
</script>
@endpush
