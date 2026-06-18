@extends('layouts.mantis')

@section('title', 'Call Analytics - Call Logs')

@push('styles')
<style>
    .call-stat-card {
        border: 0;
        box-shadow: 0 1px 3px rgba(0,0,0,.08);
    }
    .call-type-badge {
        font-size: 11px;
        text-transform: capitalize;
    }
    @media print {
        .no-print { display: none !important; }
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Call Analytics</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Online Teaching Faculty</li>
                    <li class="breadcrumb-item">Call Analytics</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card call-stat-card">
            <div class="card-body">
                <h6 class="text-muted mb-1">Total Calls</h6>
                <h3 class="mb-0">{{ number_format($stats['total_calls']) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card call-stat-card">
            <div class="card-body">
                <h6 class="text-muted mb-1">Total Talk Time</h6>
                <h3 class="mb-0">{{ \App\Models\CallAppLog::formatDuration($stats['total_duration_seconds']) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card call-stat-card">
            <div class="card-body">
                <h6 class="text-muted mb-1">With Recording</h6>
                <h3 class="mb-0">{{ number_format($stats['with_recording']) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card call-stat-card">
            <div class="card-body">
                <h6 class="text-muted mb-1">Uploaded Recordings</h6>
                <h3 class="mb-0">{{ number_format($stats['recordings_uploaded']) }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="mb-0">Call Logs</h5>
                    <small class="text-muted">Synced from Call Tracker mobile app</small>
                </div>
                <div class="d-flex gap-2 no-print">
                    <a href="{{ route('admin.call-analytics.report', request()->query()) }}" class="btn btn-outline-primary btn-sm">
                        <i class="ti ti-chart-bar"></i> Telecaller Report
                    </a>
                    <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
                        <i class="ti ti-printer"></i> Print
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="card border-0 shadow-sm mb-4 no-print">
                    <div class="card-body bg-light">
                        <form method="GET" action="{{ route('admin.call-analytics.index') }}">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">From Date</label>
                                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $filters['start_date'] }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">To Date</label>
                                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $filters['end_date'] }}">
                                </div>
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
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">Call Type</label>
                                    <select name="call_type" class="form-select form-select-sm">
                                        <option value="">All Types</option>
                                        @foreach(['incoming', 'outgoing', 'missed', 'rejected', 'unknown'] as $type)
                                            <option value="{{ $type }}" {{ $filters['call_type'] === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="ti ti-search"></i> Filter
                                        </button>
                                        <a href="{{ route('admin.call-analytics.index') }}" class="btn btn-outline-secondary btn-sm">
                                            <i class="ti ti-refresh"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Actions</th>
                                <th>Telecaller</th>
                                <th>Phone</th>
                                <th>Contact</th>
                                <th>Type</th>
                                <th>Duration</th>
                                <th>Call Date/Time</th>
                                <th>Recording</th>
                                <th>Device ID</th>
                                <th>App Version</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($calls as $index => $call)
                                <tr>
                                    <td>{{ $calls->firstItem() + $index }}</td>
                                    <td>
                                        <a href="{{ route('admin.call-analytics.show', $call->id) }}" class="btn btn-outline-primary btn-sm" title="View">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $call->telecaller?->name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $call->telecaller?->email }}</small>
                                    </td>
                                    <td>{{ $call->phone_number }}</td>
                                    <td>{{ $call->contact_name ?: '-' }}</td>
                                    <td>
                                        @php
                                            $badgeClass = match($call->call_type) {
                                                'incoming' => 'bg-success',
                                                'outgoing' => 'bg-primary',
                                                'missed' => 'bg-warning text-dark',
                                                'rejected' => 'bg-danger',
                                                default => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge call-type-badge {{ $badgeClass }}">{{ $call->call_type_label }}</span>
                                    </td>
                                    <td>{{ $call->formatted_duration }}</td>
                                    <td>
                                        <div>{{ $call->started_at?->format('d-m-Y') }}</div>
                                        <small class="text-muted">{{ $call->started_at?->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        @if($call->recording_uploaded && $call->recording)
                                            <span class="badge bg-success">Uploaded</span>
                                            <a href="{{ $call->recording->file_url }}" target="_blank" class="btn btn-link btn-sm p-0 ms-1">Play</a>
                                        @elseif($call->has_recording)
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @else
                                            <span class="badge bg-light text-dark">None</span>
                                        @endif
                                    </td>
                                    <td><small>{{ \Illuminate\Support\Str::limit($call->device_id, 20) }}</small></td>
                                    <td>{{ $call->app_version ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center text-muted py-4">No call logs found for the selected filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center no-print">
                    {{ $calls->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
