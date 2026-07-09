@extends('layouts.mantis')

@section('title', 'NatX Analytics - User Report')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/call-analytics.css') }}">
@endpush

@section('content')
@php
    $tabQuery = \Illuminate\Support\Arr::except($queryParams, ['user_id', 'metric']);

    $reportMetricUrl = function (string $metric, ?int $userId = null) use ($queryParams, $filters) {
        $params = array_merge($queryParams, array_filter([
            'user_id' => $userId ?? ($filters['user_id'] ?? null),
            'metric' => $metric,
        ], fn ($value) => $value !== null && $value !== ''));
        return route('admin.natx-analytics.report', $params);
    };

    $userReportUrl = function (int $userId) use ($queryParams) {
        return route('admin.natx-analytics.report.user', array_merge(
            ['user' => $userId],
            \Illuminate\Support\Arr::except($queryParams, ['user_id', 'metric'])
        ));
    };

    $isActiveMetric = fn (string $metric, ?int $userId = null) => ($filters['metric'] ?? null) === $metric
        && (string) ($filters['user_id'] ?? '') === (string) ($userId ?? $filters['user_id'] ?? '');
@endphp

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">NatX User Report</h5>
                    <p class="m-b-0 text-muted">Aggregated call activity per user</p>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.natx-analytics.index') }}">NatX Analytics</a></li>
                    <li class="breadcrumb-item">Report</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@include('admin.natx-analytics.partials.nav-tabs', ['activeTab' => 'report', 'tabQuery' => $tabQuery])

<div class="row mb-3 no-print">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3">
                <h5 class="mb-0"><i class="ti ti-filter me-2"></i>Search &amp; Filters</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.natx-analytics.report') }}">
                    @if(!empty($filters['metric']))
                        <input type="hidden" name="metric" value="{{ $filters['metric'] }}">
                    @endif
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">User</label>
                            <select name="user_id" class="form-select form-select-sm">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ (string) ($filters['user_id'] ?? '') === (string) $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-auto">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="ti ti-search me-1"></i> Apply
                            </button>
                            <a href="{{ route('admin.natx-analytics.report') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="ti ti-refresh me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@php $stats = $grandTotals; @endphp
@include('admin.natx-analytics.partials.stats-cards')

<div class="ca-hint-banner no-print">
    <i class="ti ti-info-circle me-1"></i>
    Click any number in the table to drill down into call records. Click a user name to view their full call log.
</div>

<div class="row">
    <div class="col-12">
        <div class="card ca-table-card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h5 class="mb-0">User-wise Summary</h5>
                    <span class="badge bg-light text-dark border">{{ $rows->count() }} {{ $rows->count() === 1 ? 'user' : 'users' }}</span>
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm no-print" onclick="window.print()">
                    <i class="ti ti-printer me-1"></i> Print
                </button>
            </div>
            <div class="card-body">
                <div class="ca-table-scroll">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Connected <span class="text-muted fw-normal text-lowercase">(unique)</span></th>
                                <th class="text-center">Attended</th>
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
                                @php $user = $userMap->get($row->user_id); @endphp
                                <tr>
                                    <td class="text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <a href="{{ $userReportUrl($row->user_id) }}" class="ca-telecaller-link">
                                            <div class="ca-telecaller-cell">
                                                <div class="avtar avtar-s rounded-circle bg-light-primary flex-shrink-0">
                                                    <i class="ti ti-user text-primary f-12"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $user?->name ?? 'Unknown' }}</div>
                                                    <small class="text-muted">{{ $user?->email }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ $reportMetricUrl('total', $row->user_id) }}" class="report-metric-link {{ $isActiveMetric('total', $row->user_id) ? 'is-active' : '' }}">{{ number_format($row->total_calls) }}</a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ $reportMetricUrl('connected', $row->user_id) }}" class="report-metric-link {{ $isActiveMetric('connected', $row->user_id) ? 'is-active' : '' }}">{{ number_format($row->connected_calls) }}</a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ $reportMetricUrl('attended', $row->user_id) }}" class="report-metric-link {{ $isActiveMetric('attended', $row->user_id) ? 'is-active' : '' }}">{{ number_format($row->attended_calls) }}</a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ $reportMetricUrl('incoming', $row->user_id) }}" class="report-metric-link {{ $isActiveMetric('incoming', $row->user_id) ? 'is-active' : '' }}">{{ number_format($row->incoming_calls) }}</a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ $reportMetricUrl('outgoing', $row->user_id) }}" class="report-metric-link {{ $isActiveMetric('outgoing', $row->user_id) ? 'is-active' : '' }}">{{ number_format($row->outgoing_calls) }}</a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ $reportMetricUrl('not_picked', $row->user_id) }}" class="report-metric-link {{ $isActiveMetric('not_picked', $row->user_id) ? 'is-active' : '' }}">{{ number_format($row->not_picked_calls) }}</a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ $reportMetricUrl('missed', $row->user_id) }}" class="report-metric-link {{ $isActiveMetric('missed', $row->user_id) ? 'is-active' : '' }}">{{ number_format($row->missed_calls) }}</a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ $reportMetricUrl('rejected', $row->user_id) }}" class="report-metric-link {{ $isActiveMetric('rejected', $row->user_id) ? 'is-active' : '' }}">{{ number_format($row->rejected_calls) }}</a>
                                    </td>
                                    <td class="text-center fw-medium">{{ \App\Models\NatXAppLog::formatDuration((int) $row->total_duration_seconds) }}</td>
                                    <td class="text-center">{{ number_format($row->with_recording) }}</td>
                                    <td class="text-center">{{ number_format($row->recordings_uploaded) }}</td>
                                    <td class="text-center no-print">
                                        <a href="{{ $userReportUrl($row->user_id) }}" class="btn btn-outline-primary btn-sm ca-btn-icon" title="View all calls">
                                            <i class="ti ti-list"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="14">
                                        <div class="ca-empty-state">
                                            <i class="ti ti-chart-bar"></i>
                                            <p>No report data found.</p>
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
                                <td class="text-center"><a href="{{ $reportMetricUrl('attended') }}" class="report-metric-link {{ $isActiveMetric('attended') ? 'is-active' : '' }}">{{ number_format($grandTotals['attended_calls']) }}</a></td>
                                <td class="text-center"><a href="{{ $reportMetricUrl('incoming') }}" class="report-metric-link {{ $isActiveMetric('incoming') ? 'is-active' : '' }}">{{ number_format($grandTotals['incoming_calls']) }}</a></td>
                                <td class="text-center"><a href="{{ $reportMetricUrl('outgoing') }}" class="report-metric-link {{ $isActiveMetric('outgoing') ? 'is-active' : '' }}">{{ number_format($grandTotals['outgoing_calls']) }}</a></td>
                                <td class="text-center"><a href="{{ $reportMetricUrl('not_picked') }}" class="report-metric-link {{ $isActiveMetric('not_picked') ? 'is-active' : '' }}">{{ number_format($grandTotals['not_picked_calls']) }}</a></td>
                                <td class="text-center"><a href="{{ $reportMetricUrl('missed') }}" class="report-metric-link {{ $isActiveMetric('missed') ? 'is-active' : '' }}">{{ number_format($grandTotals['missed_calls']) }}</a></td>
                                <td class="text-center"><a href="{{ $reportMetricUrl('rejected') }}" class="report-metric-link {{ $isActiveMetric('rejected') ? 'is-active' : '' }}">{{ number_format($grandTotals['rejected_calls']) }}</a></td>
                                <td class="text-center">{{ \App\Models\NatXAppLog::formatDuration($grandTotals['total_duration_seconds']) }}</td>
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
    @include('admin.natx-analytics.partials.report-detail')
@endif
@endsection

@push('scripts')
@include('admin.natx-analytics.partials.recording-scripts')
@endpush
