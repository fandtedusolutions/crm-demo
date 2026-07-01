<div class="row g-3 mb-3">
    <div class="col-6 col-md-4 col-lg">
        <div class="card ca-stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="mb-2 f-w-400 text-muted">Total Calls</h6>
                        <h4 class="mb-0">{{ number_format($stats['total_calls']) }}</h4>
                    </div>
                    <div class="stat-icon icon-primary">
                        <i class="ti ti-phone-call"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <div class="card ca-stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="mb-2 f-w-400 text-muted">Connected</h6>
                        <h4 class="mb-0 text-success">{{ number_format($stats['connected_calls']) }}</h4>
                    </div>
                    <div class="stat-icon icon-success">
                        <i class="ti ti-phone-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <div class="card ca-stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="mb-2 f-w-400 text-muted">Talk Time</h6>
                        <h4 class="mb-0">{{ \App\Models\CallAppLog::formatDuration($stats['total_duration_seconds']) }}</h4>
                    </div>
                    <div class="stat-icon icon-info">
                        <i class="ti ti-clock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <div class="card ca-stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="mb-2 f-w-400 text-muted">With Recording</h6>
                        <h4 class="mb-0">{{ number_format($stats['with_recording']) }}</h4>
                    </div>
                    <div class="stat-icon icon-warning">
                        <i class="ti ti-microphone"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <div class="card ca-stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="mb-2 f-w-400 text-muted">Uploaded</h6>
                        <h4 class="mb-0">{{ number_format($stats['recordings_uploaded']) }}</h4>
                    </div>
                    <div class="stat-icon icon-muted">
                        <i class="ti ti-cloud-upload"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
