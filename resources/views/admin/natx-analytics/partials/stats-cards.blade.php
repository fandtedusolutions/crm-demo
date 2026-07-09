<div class="row g-3 mb-3">
    <div class="col-6 col-md-4 col-lg">
        <div class="card ca-stat-card h-100">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Total Calls</h6>
                <h4 class="mb-0">{{ number_format($stats['total_calls']) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <div class="card ca-stat-card h-100">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Connected <span class="fw-normal">(unique)</span></h6>
                <h4 class="mb-0 text-success">{{ number_format($stats['connected_calls']) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <div class="card ca-stat-card h-100">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Total Attended</h6>
                <h4 class="mb-0 text-primary">{{ number_format($stats['attended_calls'] ?? 0) }}</h4>
                <small class="text-muted">Incoming + outgoing</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <div class="card ca-stat-card h-100">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Talk Time</h6>
                <h4 class="mb-0">{{ \App\Models\NatXAppLog::formatDuration($stats['total_duration_seconds']) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <div class="card ca-stat-card h-100">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">With Recording</h6>
                <h4 class="mb-0">{{ number_format($stats['with_recording']) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <div class="card ca-stat-card h-100">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Uploaded</h6>
                <h4 class="mb-0">{{ number_format($stats['recordings_uploaded']) }}</h4>
            </div>
        </div>
    </div>
</div>
