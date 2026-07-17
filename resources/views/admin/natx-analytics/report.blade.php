@extends('layouts.mantis')

@section('title', 'NatX Analytics - User Report')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/call-analytics.css') }}">
@endpush

@section('content')
@php
    use App\Helpers\DateRangeHelper;

    $reportMetricUrl = function (string $metric, ?int $userId = null) use ($queryParams, $filters) {
        $params = array_merge($queryParams, array_filter([
            'user_id' => $userId ?? ($filters['user_id'] ?? null),
            'metric' => $metric,
        ], fn ($value) => $value !== null && $value !== ''));
        return route('admin.natx-analytics.report', $params);
    };

    $userReportUrl = function (int $userId) use ($queryParams) {
        return route('admin.natx-analytics.report', array_merge(
            $queryParams,
            ['user_id' => $userId, 'metric' => 'total']
        ));
    };

    $isActiveMetric = fn (string $metric, ?int $userId = null) => ($filters['metric'] ?? null) === $metric
        && (string) ($filters['user_id'] ?? '') === (string) ($userId ?? $filters['user_id'] ?? '');

    $workStatusTime = function (int $userId, ?string $reportDate, string $slot) use ($workStatusMap) {
        if (empty($reportDate)) {
            return '-';
        }

        $normalizedDate = \Carbon\Carbon::parse($reportDate)->format('Y-m-d');
        $entries = ($workStatusMap ?? collect())->get($userId . '|' . $normalizedDate);
        if (!$entries) {
            return '-';
        }

        $entry = $entries->firstWhere('slot', $slot);

        return $entry ? $entry->completionTimeDisplay() : '-';
    };

    $summaryColumnCount = 14 + (!empty($showWorkStatus) ? 4 : 0);
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

@include('admin.natx-analytics.partials.nav-tabs', ['activeTab' => 'report'])

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
                        @include('admin.natx-analytics.partials.date-range-filter')
                        <div class="col-md-2">
                            <label class="form-label">Role</label>
                            <select name="role_id" class="form-select form-select-sm js-analytics-role-filter">
                                <option value="">All Roles</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ (string)($filters['role_id'] ?? '') === (string)$role->id ? 'selected' : '' }}>
                                        {{ $role->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 js-analytics-team-filter-container" style="display: none;">
                            <label class="form-label">Team</label>
                            <select name="team_id" class="form-select form-select-sm js-analytics-team-filter">
                                <option value="">All Teams</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}" {{ (string)($filters['team_id'] ?? '') === (string)$team->id ? 'selected' : '' }}>
                                        {{ $team->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">User</label>
                            <select name="user_id" class="form-select form-select-sm js-analytics-user-filter">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" data-role-id="{{ $user->role_id }}" data-team-id="{{ $user->team_id }}" {{ (string)($filters['user_id'] ?? '') === (string)$user->id ? 'selected' : '' }}>
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
                    <span class="badge bg-light text-dark border">{{ $rows->count() }} {{ $rows->count() === 1 ? 'row' : 'rows' }}</span>
                    <span class="badge bg-light-primary border border-primary ca-period-badge">
                        {{ DateRangeHelper::displayPeriod($filters) }}
                    </span>
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm no-print" onclick="window.print()">
                    <i class="ti ti-printer me-1"></i> Print
                </button>
            </div>
            <div class="card-body">
                <div class="ca-table-scroll">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            @php
                                $callMetricColumns = 11;
                            @endphp
                            @if(!empty($showWorkStatus))
                            <tr>
                                <th rowspan="2">#</th>
                                <th rowspan="2">Date</th>
                                <th rowspan="2">User</th>
                                <th colspan="{{ $callMetricColumns }}" class="text-center border-start">Call Activity</th>
                                <th colspan="3" class="text-center border-start">Work Status</th>
                                <th rowspan="2" class="text-center no-print border-start"></th>
                            </tr>
                            @endif
                            <tr>
                                @if(empty($showWorkStatus))
                                <th>#</th>
                                <th>User</th>
                                @endif
                                <th class="text-center {{ !empty($showWorkStatus) ? 'border-start' : '' }}">Total</th>
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
                                @if(!empty($showWorkStatus))
                                    <th class="text-center border-start text-uppercase">Morning</th>
                                    <th class="text-center text-uppercase">Afternoon</th>
                                    <th class="text-center text-uppercase">Evening</th>
                                @endif
                                @if(empty($showWorkStatus))
                                <th class="text-center no-print"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $index => $row)
                                @php
                                    $user = $userMap->get($row->user_id);
                                    $reportDate = $row->report_date ?? null;
                                @endphp
                                <tr>
                                    <td class="text-muted">{{ $index + 1 }}</td>
                                    @if(!empty($showWorkStatus))
                                        <td class="text-nowrap fw-medium">{{ $reportDate ? \App\Helpers\DateRangeHelper::formatDisplay($reportDate) : '-' }}</td>
                                    @endif
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
                                    @if(!empty($showWorkStatus))
                                        @php
                                            $morningTime = $workStatusTime((int) $row->user_id, $reportDate, 'morning');
                                            $afternoonTime = $workStatusTime((int) $row->user_id, $reportDate, 'afternoon');
                                            $eveningTime = $workStatusTime((int) $row->user_id, $reportDate, 'evening');
                                        @endphp
                                        <td class="text-center">
                                            @if($morningTime !== '-')
                                                <span class="text-success fw-medium">{{ $morningTime }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($afternoonTime !== '-')
                                                <span class="text-success fw-medium">{{ $afternoonTime }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($eveningTime !== '-')
                                                <span class="text-success fw-medium">{{ $eveningTime }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    @endif
                                    <td class="text-center no-print">
                                        <a href="{{ $userReportUrl($row->user_id) }}" class="btn btn-outline-primary btn-sm ca-btn-icon" title="View all calls">
                                            <i class="ti ti-list"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $summaryColumnCount }}">
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
                                <td colspan="{{ !empty($showWorkStatus) ? 3 : 2 }}"><strong>Grand Total</strong></td>
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
                                @if(!empty($showWorkStatus))
                                    <td colspan="3"></td>
                                @endif
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
<script>
    $(function () {
        const $roleSelect = $('.js-analytics-role-filter');
        const $teamSelect = $('.js-analytics-team-filter');
        const $teamContainer = $('.js-analytics-team-filter-container');
        const $userSelect = $('.js-analytics-user-filter');

        function filterUsersAndTeams() {
            const selectedRole = $roleSelect.val();

            // Toggle Team filter visibility (only for role_id = 3)
            if (selectedRole === '3') {
                $teamContainer.show().find('select').prop('disabled', false);
            } else {
                $teamContainer.hide().find('select').prop('disabled', true).val('');
            }

            const currentSelectedRole = $roleSelect.val();
            const currentSelectedTeam = $teamSelect.is(':visible') ? $teamSelect.val() : '';

            $userSelect.find('option').each(function () {
                const $option = $(this);
                const userRole = $option.attr('data-role-id');
                const userTeam = $option.attr('data-team-id');

                let match = true;
                if (currentSelectedRole && userRole !== currentSelectedRole) {
                    match = false;
                }
                if (currentSelectedRole === '3' && currentSelectedTeam && userTeam !== currentSelectedTeam) {
                    match = false;
                }

                if (match || !$option.val()) {
                    $option.show().prop('disabled', false);
                } else {
                    $option.hide().prop('disabled', true);
                }
            });

            const $selectedOption = $userSelect.find('option:selected');
            if ($selectedOption.is(':disabled')) {
                $userSelect.val('');
            }
        }

        $roleSelect.on('change', function () {
            $teamSelect.val('');
            filterUsersAndTeams();
        });
        $teamSelect.on('change', filterUsersAndTeams);

        filterUsersAndTeams();
    });
</script>
@endpush
