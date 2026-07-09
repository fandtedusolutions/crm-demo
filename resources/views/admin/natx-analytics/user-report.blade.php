@extends('layouts.mantis')

@section('title', 'NatX Analytics - ' . $user->name)

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/call-analytics.css') }}">
@endpush

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">NatX User Detail</h5>
                    <p class="m-b-0 text-muted">Full call log and recording details for {{ $user->name }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.natx-analytics.index') }}">NatX Analytics</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.natx-analytics.report') }}">Report</a></li>
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
        <a href="{{ route('admin.natx-analytics.report') }}" class="btn btn-outline-secondary btn-sm">
            <i class="ti ti-arrow-left me-1"></i> Back to Report
        </a>
    </div>
</div>

@include('admin.natx-analytics.partials.stats-cards')

<div class="row">
    <div class="col-12">
        <div class="card ca-table-card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h5 class="mb-0">Call Logs &amp; Recordings</h5>
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
