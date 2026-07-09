@extends('layouts.mantis')

@section('title', 'NatX Analytics')

@section('content')
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

<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-1">Total Calls</p>
                <h4 class="mb-0">{{ number_format($stats['total_calls']) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-1">Synced Users</p>
                <h4 class="mb-0">{{ number_format($stats['unique_users']) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-1">With Recording</p>
                <h4 class="mb-0">{{ number_format($stats['with_recording']) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-1">Recordings Uploaded</p>
                <h4 class="mb-0">{{ number_format($stats['recordings_uploaded']) }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3">
                <h5 class="mb-0"><i class="ti ti-filter me-2"></i>Search &amp; Filters</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.natx-analytics.index') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Call Type</label>
                            <select name="call_type" class="form-select form-select-sm">
                                <option value="">All Types</option>
                                @foreach(['incoming', 'outgoing', 'not_picked', 'missed', 'rejected', 'unknown'] as $type)
                                    <option value="{{ $type }}" {{ request('call_type') === $type ? 'selected' : '' }}>
                                        {{ $type === 'not_picked' ? 'Not Picked' : ucfirst($type) }}
                                        @if($callTypeCounts->has($type))
                                            ({{ $callTypeCounts[$type] }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="Phone or contact name">
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

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">NatX Call Logs</h5>
                <span class="badge bg-light text-dark border">{{ $calls->total() }} {{ $calls->total() === 1 ? 'record' : 'records' }}</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Phone</th>
                                <th>Contact</th>
                                <th>Type</th>
                                <th>Duration</th>
                                <th>Started At</th>
                                <th>Recording</th>
                                <th>Device</th>
                                <th>Ver.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($calls as $index => $call)
                                @php
                                    $seconds = (int) $call->duration_seconds;
                                    $duration = $seconds > 0
                                        ? sprintf('%d:%02d:%02d', intdiv($seconds, 3600), intdiv($seconds % 3600, 60), $seconds % 60)
                                        : '0:00:00';
                                @endphp
                                <tr>
                                    <td class="text-muted">{{ $calls->firstItem() + $index }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $call->user?->name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $call->user?->email }}</small>
                                    </td>
                                    <td class="fw-medium">{{ $call->phone_number }}</td>
                                    <td>{{ $call->contact_name ?: '-' }}</td>
                                    <td>
                                        <span class="badge bg-light-primary text-primary">
                                            {{ $call->call_type === 'not_picked' ? 'Not Picked' : ucfirst(str_replace('_', ' ', $call->call_type ?? 'unknown')) }}
                                        </span>
                                    </td>
                                    <td>{{ $duration }}</td>
                                    <td>
                                        <div>{{ $call->display_started_at?->format('d M Y') ?? '-' }}</div>
                                        <small class="text-muted">{{ $call->display_started_at?->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        @if($call->recording_uploaded)
                                            <span class="badge bg-success">Uploaded</span>
                                        @elseif($call->has_recording)
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @else
                                            <span class="text-muted">No</span>
                                        @endif
                                    </td>
                                    <td><small class="text-muted">{{ $call->device_id }}</small></td>
                                    <td>{{ $call->app_version ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">No NatX call logs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $calls->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
