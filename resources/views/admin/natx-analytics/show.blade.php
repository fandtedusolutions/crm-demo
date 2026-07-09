@extends('layouts.mantis')

@section('title', 'NatX Call Details')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">NatX Call Details</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.natx-analytics.index') }}">NatX Analytics</a></li>
                    <li class="breadcrumb-item">Details</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Call Information</h5>
                <a href="{{ route('admin.natx-analytics.index') }}" class="btn btn-light btn-sm">
                    <i class="ti ti-arrow-left"></i> Back
                </a>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted small">User</label>
                        <div class="fw-semibold">{{ $call->user?->name ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Phone Number</label>
                        <div class="fw-semibold">{{ $call->phone_number }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Contact Name</label>
                        <div>{{ $call->contact_name ?: '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Call Type</label>
                        <div>@include('admin.natx-analytics.partials.call-type-badge', ['call' => $call])</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Remarks</label>
                        <div>{{ $call->remarks ?: '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Duration</label>
                        <div>{{ $call->formatted_duration }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Call Date/Time</label>
                        <div>{{ $call->display_started_at?->format('d-m-Y h:i A') }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">End Date/Time</label>
                        <div>{{ $call->display_ended_at?->format('d-m-Y h:i A') ?: '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Device Call ID</label>
                        <div><code>{{ $call->device_call_id }}</code></div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Device ID</label>
                        <div>{{ $call->device_id }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">App Version</label>
                        <div>{{ $call->app_version ?: '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Synced At</label>
                        <div>{{ $call->created_at?->format('d-m-Y h:i A') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recording</h5>
            </div>
            <div class="card-body">
                @if($call->recording)
                    <div class="mb-2">
                        <label class="text-muted small">File Name</label>
                        <div>{{ $call->recording->file_name }}</div>
                    </div>
                    <div class="mb-2">
                        <label class="text-muted small">File Size</label>
                        <div>{{ $call->recording->formatted_file_size }}</div>
                    </div>
                    <div class="mb-2">
                        <label class="text-muted small">Duration</label>
                        <div>{{ \App\Models\NatXAppLog::formatDuration((int) $call->recording->duration_seconds) }}</div>
                    </div>
                    <audio controls preload="metadata" class="w-100 mb-3">
                        <source src="{{ $call->recording->stream_url }}" type="{{ $call->recording->playbackMimeType() }}">
                        <source src="{{ $call->recording->stream_url }}" type="audio/mp4">
                        <source src="{{ $call->recording->stream_url }}" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                    <a href="{{ route('admin.natx-analytics.recording.download', $call->id) }}" class="btn btn-outline-primary btn-sm w-100">
                        <i class="ti ti-download"></i> Download Recording
                    </a>
                @elseif($call->has_recording)
                    <div class="alert alert-warning mb-0">
                        Recording expected but not uploaded yet.
                    </div>
                @else
                    <div class="text-muted">No recording for this call.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
