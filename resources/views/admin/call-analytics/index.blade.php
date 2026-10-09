@extends('layouts.mantis')

@section('title', 'Call Analytics')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/call-analytics.css') }}">
<style>
    #callAnalyticsTable th:nth-child(1),  #callAnalyticsTable td:nth-child(1)  { min-width: 45px; }
    #callAnalyticsTable th:nth-child(2),  #callAnalyticsTable td:nth-child(2)  { min-width: 55px; }
    #callAnalyticsTable th:nth-child(3),  #callAnalyticsTable td:nth-child(3)  { min-width: 160px; }
    #callAnalyticsTable th:nth-child(4),  #callAnalyticsTable td:nth-child(4)  { min-width: 130px; }
    #callAnalyticsTable th:nth-child(5),  #callAnalyticsTable td:nth-child(5)  { min-width: 120px; }
    #callAnalyticsTable th:nth-child(6),  #callAnalyticsTable td:nth-child(6)  { min-width: 90px; }
    #callAnalyticsTable th:nth-child(7),  #callAnalyticsTable td:nth-child(7)  { min-width: 100px; }
    #callAnalyticsTable th:nth-child(8),  #callAnalyticsTable td:nth-child(8)  { min-width: 75px; }
    #callAnalyticsTable th:nth-child(9),  #callAnalyticsTable td:nth-child(9)  { min-width: 105px; }
    #callAnalyticsTable th:nth-child(10), #callAnalyticsTable td:nth-child(10) { min-width: 105px; }
    #callAnalyticsTable th:nth-child(11), #callAnalyticsTable td:nth-child(11) { min-width: 260px; }
    #callAnalyticsTable th:nth-child(12), #callAnalyticsTable td:nth-child(12) { min-width: 150px; }
    #callAnalyticsTable th:nth-child(13), #callAnalyticsTable td:nth-child(13) { min-width: 70px; }
</style>
@endpush

@section('content')
@php
    use App\Helpers\DateRangeHelper;
    $tabQuery = \Illuminate\Support\Arr::except($queryParams, ['telecaller_id', 'call_type', 'search', 'metric']);
@endphp

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Call Analytics</h5>
                    <p class="m-b-0 text-muted">Call activity synced from Call Tracker mobile app</p>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Call Analytics</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@include('admin.call-analytics.partials.nav-tabs', ['activeTab' => 'index', 'tabQuery' => $tabQuery])

{{-- Filters --}}
<div class="row mb-3 no-print">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3">
                <h5 class="mb-0"><i class="ti ti-filter me-2"></i>Search &amp; Filters</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.call-analytics.index') }}">
                    <div class="row g-3 align-items-end">
                        @include('admin.call-analytics.partials.date-range-filter')
                        <div class="col-md-2">
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
                        <div class="col-md-2">
                            <label class="form-label">Call Type</label>
                            <select name="call_type" class="form-select form-select-sm">
                                <option value="">All Types</option>
                                @foreach(['incoming', 'outgoing', 'not_picked', 'missed', 'rejected', 'unknown'] as $type)
                                    <option value="{{ $type }}" {{ $filters['call_type'] === $type ? 'selected' : '' }}>{{ $type === 'not_picked' ? 'Not Picked' : ucfirst($type) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control form-control-sm" value="{{ $filters['search'] ?? '' }}" placeholder="Phone or contact name">
                        </div>
                        <div class="col-md-auto">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="ti ti-search me-1"></i> Apply
                            </button>
                            <a href="{{ route('admin.call-analytics.index') }}" class="btn btn-outline-secondary btn-sm">
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

@include('admin.call-analytics.partials.stats-cards')

{{-- Call logs table --}}
<div class="row">
    <div class="col-12">
        <div class="card ca-table-card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h5 class="mb-0">Call Logs</h5>
                    <span class="badge bg-light-primary border border-primary ca-period-badge">
                        {{ DateRangeHelper::displayPeriod($filters) }}
                    </span>
                    <span class="badge bg-light text-dark border">{{ $calls->total() }} {{ $calls->total() === 1 ? 'record' : 'records' }}</span>
                </div>
                <div class="d-flex gap-2 no-print">
                    <a href="{{ route('admin.call-analytics.export', $queryParams) }}" class="btn btn-outline-success btn-sm">
                        <i class="ti ti-file-spreadsheet me-1"></i> Export Excel
                    </a>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                        <i class="ti ti-printer me-1"></i> Print
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="ca-table-scroll">
                    <table class="table table-hover mb-0" id="callAnalyticsTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th></th>
                                <th>Telecaller</th>
                                <th>Phone</th>
                                <th>Contact</th>
                                <th>Type</th>
                                <th>Remarks</th>
                                <th>Duration</th>
                                <th>Call Date/Time</th>
                                <th>End Date/Time</th>
                                <th>Recording</th>
                                <th>Device ID</th>
                                <th>Ver.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($calls as $index => $call)
                                <tr>
                                    <td class="text-muted">{{ $calls->firstItem() + $index }}</td>
                                    <td>
                                        <a href="{{ route('admin.call-analytics.show', $call->id) }}" class="btn btn-outline-primary btn-sm ca-btn-icon" title="View details">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="ca-telecaller-cell">
                                            <div class="avtar avtar-s rounded-circle bg-light-primary flex-shrink-0">
                                                <i class="ti ti-user text-primary f-12"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $call->telecaller?->name ?? 'N/A' }}</div>
                                                <small class="text-muted">{{ $call->telecaller?->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fw-medium">{{ $call->phone_number }}</td>
                                    <td>{{ $call->contact_name ?: '-' }}</td>
                                    <td>@include('admin.call-analytics.partials.call-type-badge', ['call' => $call])</td>
                                    <td>
                                        @if($call->remarks)
                                            <span class="badge bg-warning text-dark ca-call-badge">{{ $call->remarks }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $call->formatted_duration }}</td>
                                    <td data-order="{{ $call->started_at_ms }}">
                                        <div>{{ $call->display_started_at?->format('d M Y') }}</div>
                                        <small class="text-muted">{{ $call->display_started_at?->format('h:i A') }}</small>
                                    </td>
                                    <td data-order="{{ $call->end_at_ms ?? 0 }}">
                                        @if($call->display_ended_at)
                                            <div>{{ $call->display_ended_at->format('d M Y') }}</div>
                                            <small class="text-muted">{{ $call->display_ended_at->format('h:i A') }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>@include('admin.call-analytics.partials.recording-cell', ['call' => $call])</td>
                                    <td><small class="text-muted" title="{{ $call->device_id }}">{{ \Illuminate\Support\Str::limit($call->device_id, 22) }}</small></td>
                                    <td><small>{{ $call->app_version ?: '-' }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13">
                                        <div class="ca-empty-state">
                                            <i class="ti ti-phone-off"></i>
                                            <p>No call logs found for the selected filters.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($calls->hasPages())
                <div class="ca-pagination no-print">
                    {{ $calls->links('pagination::bootstrap-5') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('admin.call-analytics.partials.recording-scripts')
<script>
    $(function () {
        if ($.fn.DataTable && $('#callAnalyticsTable tbody tr').length > 0 && !$('#callAnalyticsTable tbody tr td[colspan]').length) {
            $('#callAnalyticsTable').DataTable({
                paging: false, searching: false, ordering: true, info: false,
                order: [[8, 'desc']],
                columnDefs: [{ orderable: false, targets: [0, 1, 10] }]
            });
        }
    });
</script>
@endpush
