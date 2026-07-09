@extends('layouts.mantis')

@section('title', 'NatX Analytics - ' . $reportUser->name)

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
                    <p class="m-b-0 text-muted">Full call log and recording details for {{ $reportUser->name }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.natx-analytics.index') }}">NatX Analytics</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.natx-analytics.report', DateRangeHelper::queryParams($filters)) }}">Report</a></li>
                    <li class="breadcrumb-item">{{ $reportUser->name }}</li>
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
        <h5 class="mb-1">{{ $reportUser->name }}</h5>
        <div class="profile-meta">
            <i class="ti ti-mail f-12 me-1"></i>{{ $reportUser->email }}
            @if($reportUser->phone)
                <span class="mx-2">|</span>
                <i class="ti ti-phone f-12 me-1"></i>{{ $reportUser->phone }}
            @endif
            <span class="mx-2">|</span>
            <span class="text-muted">User ID: {{ $reportUser->id }}</span>
        </div>
    </div>
    <div class="d-flex gap-2 flex-shrink-0">
        <a href="{{ route('admin.natx-analytics.report', DateRangeHelper::queryParams($filters)) }}" class="btn btn-outline-secondary btn-sm">
            <i class="ti ti-arrow-left me-1"></i> Back to Report
        </a>
    </div>
</div>

<div class="row mb-3 no-print">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3">
                <h5 class="mb-0"><i class="ti ti-filter me-2"></i>Search &amp; Filters</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.natx-analytics.report.user', $reportUser->id) }}">
                    <div class="row g-3 align-items-end">
                        @include('admin.natx-analytics.partials.date-range-filter')
                        <div class="col-md-2">
                            <label class="form-label">Call Type</label>
                            <select name="call_type" class="form-select form-select-sm">
                                <option value="">All Types</option>
                                @foreach(['incoming', 'outgoing', 'not_picked', 'missed', 'rejected', 'unknown'] as $type)
                                    <option value="{{ $type }}" {{ ($filters['call_type'] ?? '') === $type ? 'selected' : '' }}>{{ $type === 'not_picked' ? 'Not Picked' : ucfirst($type) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control form-control-sm" value="{{ $filters['search'] ?? '' }}" placeholder="Phone, contact, or device_call_id">
                        </div>
                        <div class="col-md-auto">
                            <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-search me-1"></i> Apply</button>
                            <a href="{{ route('admin.natx-analytics.report.user', $reportUser->id) }}" class="btn btn-outline-secondary btn-sm"><i class="ti ti-refresh me-1"></i> Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('admin.natx-analytics.partials.active-filters')

@include('admin.natx-analytics.partials.stats-cards')

<div class="row">
    <div class="col-12">
        <div class="card ca-table-card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h5 class="mb-0">Call Logs &amp; Recordings</h5>
                    <span class="badge bg-light-primary border border-primary ca-period-badge">
                        {{ DateRangeHelper::displayPeriod($filters) }}
                    </span>
                    <span class="badge bg-light text-dark border">{{ $calls->total() }} {{ $calls->total() === 1 ? 'record' : 'records' }}</span>
                </div>
            </div>
            <div class="card-body">
                @include('admin.natx-analytics.partials.full-logs-table', ['calls' => $calls])
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('admin.natx-analytics.partials.recording-scripts')
@endpush
