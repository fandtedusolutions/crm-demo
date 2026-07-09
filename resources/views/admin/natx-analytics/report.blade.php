@extends('layouts.mantis')

@section('title', 'NatX Analytics - User Report')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/call-analytics.css') }}">
@endpush

@section('content')
@php
    $reportMetricUrl = function (string $metric, ?int $userId = null) use ($queryParams) {
        $params = array_merge($queryParams, array_filter([
            'metric' => $metric,
        ], fn ($value) => $value !== null && $value !== ''));
        return route('admin.natx-analytics.report', $params);
    };

    $userReportUrl = function (int $userId) {
        return route('admin.natx-analytics.report.user', ['user' => $userId]);
    };

    $isActiveMetric = fn (string $metric) => ($filters['metric'] ?? null) === $metric;
@endphp

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">NatX User Report</h5>
                    <p class="m-b-0 text-muted">All users · aggregated call activity</p>
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

@include('admin.natx-analytics.partials.nav-tabs', ['activeTab' => 'report', 'tabQuery' => $queryParams])

@php $stats = $grandTotals; @endphp
@include('admin.natx-analytics.partials.stats-cards')

<div class="ca-hint-banner no-print">
    <i class="ti ti-info-circle me-1"></i>
    Click a number to drill down, or a user name to view their full call log with recording details.
</div>

<div class="row">
    <div class="col-12">
        <div class="card ca-table-card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h5 class="mb-0">User-wise Summary</h5>
                    <span class="badge bg-light text-dark border">All users</span>
                    <span class="badge bg-light-primary border border-primary">{{ $rows->count() }} {{ $rows->count() === 1 ? 'user' : 'users' }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="ca-table-scroll">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Connected</th>
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
                                            <div class="fw-semibold">{{ $user?->name ?? 'Unknown' }}</div>
                                            <small class="text-muted">{{ $user?->email }} · ID {{ $row->user_id }}</small>
                                        </a>
                                    </td>
                                    @foreach(['total' => 'total_calls', 'connected' => 'connected_calls', 'attended' => 'attended_calls', 'incoming' => 'incoming_calls', 'outgoing' => 'outgoing_calls', 'not_picked' => 'not_picked_calls', 'missed' => 'missed_calls', 'rejected' => 'rejected_calls'] as $metric => $field)
                                        <td class="text-center">
                                            <a href="{{ $reportMetricUrl($metric) }}" class="report-metric-link {{ $isActiveMetric($metric) ? 'is-active' : '' }}">{{ number_format($row->{$field}) }}</a>
                                        </td>
                                    @endforeach
                                    <td class="text-center fw-medium">{{ \App\Models\NatXAppLog::formatDuration((int) $row->total_duration_seconds) }}</td>
                                    <td class="text-center">{{ number_format($row->with_recording) }}</td>
                                    <td class="text-center">{{ number_format($row->recordings_uploaded) }}</td>
                                    <td class="text-center no-print">
                                        <a href="{{ $userReportUrl($row->user_id) }}" class="btn btn-outline-primary btn-sm ca-btn-icon" title="Full details">
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
                                <td colspan="2"><strong>Grand Total (all users)</strong></td>
                                @foreach(['total' => 'total_calls', 'connected' => 'connected_calls', 'attended' => 'attended_calls', 'incoming' => 'incoming_calls', 'outgoing' => 'outgoing_calls', 'not_picked' => 'not_picked_calls', 'missed' => 'missed_calls', 'rejected' => 'rejected_calls'] as $metric => $field)
                                    <td class="text-center">
                                        <a href="{{ $reportMetricUrl($metric) }}" class="report-metric-link {{ $isActiveMetric($metric) ? 'is-active' : '' }}">{{ number_format($grandTotals[$field]) }}</a>
                                    </td>
                                @endforeach
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
