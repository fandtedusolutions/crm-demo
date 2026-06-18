@extends('layouts.mantis')

@section('title', 'Call Analytics - Telecaller Report')

@push('styles')
<style>
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
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Telecaller</th>
                                <th class="text-center">Total Calls</th>
                                <th class="text-center">Incoming</th>
                                <th class="text-center">Outgoing</th>
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
                                    <td class="text-center fw-semibold">{{ number_format($row->total_calls) }}</td>
                                    <td class="text-center">{{ number_format($row->incoming_calls) }}</td>
                                    <td class="text-center">{{ number_format($row->outgoing_calls) }}</td>
                                    <td class="text-center">{{ number_format($row->missed_calls) }}</td>
                                    <td class="text-center">{{ number_format($row->rejected_calls) }}</td>
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
                                    <td colspan="11" class="text-center text-muted py-4">No report data found for the selected filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($rows->isNotEmpty())
                        <tfoot class="table-light fw-semibold">
                            <tr>
                                <td colspan="2">Grand Total</td>
                                <td class="text-center">{{ number_format($grandTotals['total_calls']) }}</td>
                                <td class="text-center">{{ number_format($grandTotals['incoming_calls']) }}</td>
                                <td class="text-center">{{ number_format($grandTotals['outgoing_calls']) }}</td>
                                <td class="text-center">{{ number_format($grandTotals['missed_calls']) }}</td>
                                <td class="text-center">{{ number_format($grandTotals['rejected_calls']) }}</td>
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
@endsection
