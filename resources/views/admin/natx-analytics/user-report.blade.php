@extends('layouts.mantis')

@section('title', 'NatX Analytics - ' . $user->name)

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/call-analytics.css') }}">
@endpush

@section('content')
@php
    use App\Helpers\DateRangeHelper;
@endphp

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">NatX User Detail</h5>
                    <p class="m-b-0 text-muted">Individual NatX call log and performance summary</p>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.natx-analytics.index') }}">NatX Analytics</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.natx-analytics.report', DateRangeHelper::queryParams($filters)) }}">Report</a></li>
                    <li class="breadcrumb-item">{{ $user->name }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="ca-telecaller-profile no-print">
    <div class="avtar avtar-l rounded-circle bg-light-primary flex-shrink-0">
        <i class="ti ti-user text-primary"></i>
    </div>
    <div class="flex-grow-1">
        <h5 class="mb-1">{{ $user->name }}</h5>
        <div class="profile-meta">
            <i class="ti ti-mail f-12 me-1"></i>{{ $user->email }}
            @if($user->phone)
                <span class="mx-2">|</span>
                <i class="ti ti-phone f-12 me-1"></i>{{ $user->phone }}
            @endif
        </div>
    </div>
    <div class="d-flex gap-2 flex-shrink-0">
        <a href="{{ route('admin.natx-analytics.report', DateRangeHelper::queryParams($filters)) }}" class="btn btn-outline-secondary btn-sm">
            <i class="ti ti-arrow-left me-1"></i> Back to Report
        </a>
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="ti ti-printer me-1"></i> Print
        </button>
    </div>
</div>

<div class="row mb-3 no-print">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3">
                <h5 class="mb-0"><i class="ti ti-filter me-2"></i>Search &amp; Filters</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.natx-analytics.report.user', $user->id) }}">
                    <div class="row g-3 align-items-end">
                        @include('admin.call-analytics.partials.date-range-filter')
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
                            <a href="{{ route('admin.natx-analytics.report.user', array_merge(['user' => $user->id], $filterQueryParams)) }}" class="btn btn-outline-secondary btn-sm">
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
    'users' => collect([$user]),
    'defaultDateRange' => $defaultDateRange ?? \App\Helpers\DateRangeHelper::PRESET_THIS_MONTH,
])

@include('admin.natx-analytics.partials.stats-cards')

<div class="row">
    <div class="col-12">
        <div class="card ca-table-card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h5 class="mb-0">Call Logs</h5>
                    <span class="badge bg-light-primary border border-primary ca-period-badge">
                        {{ DateRangeHelper::displayPeriod($filters) }}
                    </span>
                    <span class="badge bg-light text-dark border">{{ $calls->total() }} {{ $calls->total() === 1 ? 'call' : 'calls' }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="ca-table-scroll">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th></th>
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
                                        <a href="{{ route('admin.natx-analytics.show', $call->id) }}" class="btn btn-outline-primary btn-sm ca-btn-icon" title="View">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    </td>
                                    <td class="fw-medium">{{ $call->phone_number }}</td>
                                    <td>{{ $call->contact_name ?: '-' }}</td>
                                    <td>@include('admin.natx-analytics.partials.call-type-badge', ['call' => $call])</td>
                                    <td>{{ $call->remarks ?: '-' }}</td>
                                    <td>{{ $call->formatted_duration }}</td>
                                    <td>
                                        <div>{{ $call->display_started_at?->format('d M Y') }}</div>
                                        <small class="text-muted">{{ $call->display_started_at?->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        @if($call->display_ended_at)
                                            <div>{{ $call->display_ended_at->format('d M Y') }}</div>
                                            <small class="text-muted">{{ $call->display_ended_at->format('h:i A') }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>@include('admin.natx-analytics.partials.recording-cell', ['call' => $call])</td>
                                    <td><small class="text-muted" title="{{ $call->device_id }}">{{ \Illuminate\Support\Str::limit($call->device_id, 22) }}</small></td>
                                    <td><small>{{ $call->app_version ?: '-' }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12">
                                        <div class="ca-empty-state">
                                            <i class="ti ti-phone-off"></i>
                                            <p>No call logs found for the selected filters.</p>
                                            <small class="text-muted">Try widening the date range if you expect older synced calls.</small>
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
@include('admin.natx-analytics.partials.recording-scripts')
@endpush
