@extends('layouts.mantis')

@section('title', 'NatX Analytics')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/call-analytics.css') }}">
@endpush

@section('content')
@php
    use App\Helpers\DateRangeHelper;
    $tabQuery = \Illuminate\Support\Arr::except($queryParams, ['user_id', 'call_type', 'search', 'metric']);
@endphp

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">NatX Analytics</h5>
                    <p class="m-b-0 text-muted">Call activity synced from the NatX mobile app</p>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">NatX Analytics</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@include('admin.natx-analytics.partials.nav-tabs', ['activeTab' => 'index', 'tabQuery' => $tabQuery])

<div class="row mb-3 no-print">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3">
                <h5 class="mb-0"><i class="ti ti-filter me-2"></i>Search &amp; Filters</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.natx-analytics.index') }}">
                    <div class="row g-3 align-items-end">
                        @include('admin.call-analytics.partials.date-range-filter')
                        <div class="col-md-2">
                            <label class="form-label">User</label>
                            <select name="user_id" class="form-select form-select-sm">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ (string) $filters['user_id'] === (string) $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
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
                            <a href="{{ route('admin.natx-analytics.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="ti ti-refresh me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('admin.natx-analytics.partials.active-filters', [
    'filters' => $filters,
    'users' => $users,
    'defaultDateRange' => $defaultDateRange ?? \App\Helpers\DateRangeHelper::PRESET_THIS_MONTH,
])

@include('admin.natx-analytics.partials.stats-cards')

<div class="row">
    <div class="col-12">
        <div class="card ca-table-card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h5 class="mb-0">NatX Call Logs</h5>
                    <span class="badge bg-light-primary border border-primary ca-period-badge">
                        {{ DateRangeHelper::displayPeriod($filters) }}
                    </span>
                    <span class="badge bg-light text-dark border">{{ $calls->total() }} {{ $calls->total() === 1 ? 'record' : 'records' }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="ca-table-scroll">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th></th>
                                <th>User</th>
                                <th>Phone</th>
                                <th>Contact</th>
                                <th>Type</th>
                                <th>Remarks</th>
                                <th>Duration</th>
                                <th>Call Date/Time</th>
                                <th>End Date/Time</th>
                                <th>Recording</th>
                                <th>Device</th>
                                <th>Ver.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($calls as $index => $call)
                                <tr>
                                    <td class="text-muted">{{ $calls->firstItem() + $index }}</td>
                                    <td>
                                        <a href="{{ route('admin.natx-analytics.show', $call->id) }}" class="btn btn-outline-primary btn-sm ca-btn-icon" title="View details">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $call->user?->name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $call->user?->email }}</small>
                                    </td>
                                    <td class="fw-medium">{{ $call->phone_number }}</td>
                                    <td>{{ $call->contact_name ?: '-' }}</td>
                                    <td>
                                        @include('admin.natx-analytics.partials.call-type-badge', ['call' => $call])
                                    </td>
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
                                    <td>
                                        @include('admin.natx-analytics.partials.recording-cell', ['call' => $call])
                                    </td>
                                    <td><small class="text-muted" title="{{ $call->device_id }}">{{ \Illuminate\Support\Str::limit($call->device_id, 22) }}</small></td>
                                    <td><small>{{ $call->app_version ?: '-' }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13">
                                        <div class="ca-empty-state">
                                            <i class="ti ti-phone-off"></i>
                                            <p>No NatX call logs found for the selected filters.</p>
                                            <small class="text-muted">Try widening the date range (e.g. This Month) if you expect older synced calls.</small>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="ca-pagination no-print">
                    {{ $calls->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('admin.natx-analytics.partials.recording-scripts')
@endpush
