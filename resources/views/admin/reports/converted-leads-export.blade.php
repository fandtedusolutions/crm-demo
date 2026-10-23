@extends('layouts.mantis')

@section('title', 'Converted Leads Export')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-7">
                <div class="page-header-title">
                    <h5 class="m-b-10 d-flex align-items-center gap-2">
                        <i class="ti ti-file-spreadsheet text-success fs-2"></i>
                        <span>Converted Leads Export</span>
                    </h5>
                    <p class="m-b-0 text-muted">
                        Export the complete converted leads details from the converted-leads page directly into an Excel (.xlsx) file on the server.
                    </p>
                </div>
            </div>
            <div class="col-md-5">
                <ul class="breadcrumb d-flex justify-content-md-end mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.converted-leads.index') }}">Converted Leads</a></li>
                    <li class="breadcrumb-item active">Export & Download</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@php
    $key = $export['key'];
    $status = $export['status'];
    $isComplete = ($status['status'] ?? '') === 'completed';
    $isRunning = in_array(($status['status'] ?? ''), ['queued', 'processing'], true);
    $isFailed = ($status['status'] ?? '') === 'failed';
    $percent = (int) ($status['percent'] ?? 0);
@endphp

<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-11">
        <!-- Main Export Card -->
        <div class="card shadow-sm border-0 mb-4" data-export-card="{{ $key }}"
             data-status-url="{{ route('admin.background-exports.status', $key) }}"
             data-start-url="{{ route('admin.background-exports.start', $key) }}">
            
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="p-2 rounded bg-light-primary text-primary">
                        <i class="ti ti-database-export fs-3"></i>
                    </span>
                    <div>
                        <h5 class="mb-0 fw-bold">Converted Leads Excel File</h5>
                        <small class="text-muted">Includes all fields, student details, academic & support verifications, and finance information</small>
                    </div>
                </div>
                <div>
                    <span class="badge export-status-badge {{ $isComplete ? 'bg-success' : ($isRunning ? 'bg-primary' : ($isFailed ? 'bg-danger' : 'bg-secondary')) }} px-3 py-2 fs-6">
                        {{ ucfirst($status['status'] ?? 'Idle') }}
                    </span>
                </div>
            </div>

            <div class="card-body p-4">
                <!-- Status & Progress Section -->
                <div class="mb-4 p-3 bg-light rounded-3 border">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="spinner-border spinner-border-sm text-primary export-spinner {{ $isRunning ? '' : 'd-none' }}" role="status"></span>
                            <span class="export-message fw-medium text-dark">{{ $status['message'] ?? 'Ready to export' }}</span>
                        </div>
                        <span class="export-percent fw-bold fs-5 text-primary">{{ $percent }}%</span>
                    </div>

                    <div class="progress" style="height: 22px; border-radius: 11px;">
                        <div class="progress-bar export-bar fw-semibold {{ $isComplete ? 'bg-success' : ($isFailed ? 'bg-danger' : 'bg-primary') }} {{ $isRunning ? 'progress-bar-striped progress-bar-animated' : '' }}"
                             role="progressbar"
                             style="width: {{ $percent }}%;"
                             aria-valuenow="{{ $percent }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                            {{ $percent }}%
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-2 small text-muted">
                        <span class="export-counts fw-semibold">
                            @if(($status['total'] ?? 0) > 0)
                                Processed: {{ number_format($status['processed'] ?? 0) }} / {{ number_format($status['total']) }} leads
                            @endif
                        </span>
                        <span class="export-updated">
                            @if(!empty($status['updated_at']))
                                Last updated: {{ $status['updated_at'] }}
                            @endif
                        </span>
                    </div>

                    <div class="alert alert-danger mt-2 py-2 px-3 export-error {{ empty($status['error']) ? 'd-none' : '' }}">
                        <i class="ti ti-alert-circle me-1"></i>
                        <span class="export-error-text">{{ $status['error'] ?? '' }}</span>
                    </div>
                </div>

                <!-- Action Buttons & Server File Meta -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pt-2">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <a href="{{ route('admin.background-exports.download', $key) }}"
                           class="btn btn-success btn-lg export-download px-4 {{ empty($status['file_ready']) ? 'd-none' : '' }}"
                           id="downloadExcelBtn">
                            <i class="ti ti-download me-2 fs-4 align-middle"></i>
                            <span class="align-middle fw-semibold">Download Converted Leads Excel</span>
                        </a>

                        <button type="button" class="btn btn-outline-primary btn-lg export-start px-3" {{ $isRunning ? 'disabled' : '' }}>
                            <i class="ti ti-player-play me-1 align-middle"></i>
                            <span class="export-start-label align-middle">{{ $isRunning ? 'Generating…' : 'Generate Excel from Web' }}</span>
                        </button>
                    </div>

                    <div class="export-file-meta text-muted small text-md-end {{ empty($status['file_ready']) ? 'd-none' : '' }}">
                        <div class="fw-semibold text-dark">
                            <i class="ti ti-circle-check text-success me-1"></i> File ready on server
                        </div>
                        <div class="export-file-details">
                            @php
                                $metaParts = [];
                                if (!empty($status['file_size_label'])) { $metaParts[] = $status['file_size_label']; }
                                if (!empty($status['finished_at'])) { $metaParts[] = 'Completed: ' . $status['finished_at']; }
                            @endphp
                            {{ implode(' · ', $metaParts) }}
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Server Command Section -->
                <div class="bg-light-secondary p-3 rounded-3 border">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="ti ti-terminal fs-5 text-primary"></i>
                            <span>Run via Server CMD / Terminal</span>
                        </h6>
                        <button type="button" class="btn btn-sm btn-outline-secondary copy-cmd-btn" data-clipboard="{{ $export['command'] }}">
                            <i class="ti ti-copy me-1"></i> <span class="copy-label">Copy Command</span>
                        </button>
                    </div>
                    <p class="text-muted small mb-2">
                        You can run this task directly in your server terminal or command prompt. The workbook is generated row-by-row with minimal memory usage, and this page will update in real-time as it processes:
                    </p>
                    <div class="bg-dark text-white p-2 rounded font-monospace small user-select-all d-flex align-items-center justify-content-between">
                        <code>{{ $export['command'] }}</code>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const card = document.querySelector('[data-export-card]');
    if (!card) return;

    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    const token = tokenMeta ? tokenMeta.getAttribute('content') : '';
    const statusUrl = card.dataset.statusUrl;
    const startUrl = card.dataset.startUrl;

    const bar = card.querySelector('.export-bar');
    const percentEl = card.querySelector('.export-percent');
    const messageEl = card.querySelector('.export-message');
    const countsEl = card.querySelector('.export-counts');
    const updatedEl = card.querySelector('.export-updated');
    const spinnerEl = card.querySelector('.export-spinner');
    const startBtn = card.querySelector('.export-start');
    const startLabel = card.querySelector('.export-start-label');
    const downloadBtn = card.querySelector('.export-download');
    const fileMeta = card.querySelector('.export-file-meta');
    const fileDetails = card.querySelector('.export-file-details');
    const errorBox = card.querySelector('.export-error');
    const errorText = card.querySelector('.export-error-text');
    const badge = card.querySelector('.export-status-badge');

    let pollTimer = null;

    function renderStatus(status) {
        const percent = Math.max(0, Math.min(100, parseInt(status.percent || 0, 10)));
        const state = status.status || 'idle';
        const isRunning = state === 'queued' || state === 'processing';
        const isComplete = state === 'completed';
        const isFailed = state === 'failed';

        // Percentage & progress bar
        percentEl.textContent = percent + '%';
        bar.style.width = percent + '%';
        bar.setAttribute('aria-valuenow', String(percent));
        bar.textContent = percent + '%';

        bar.classList.toggle('progress-bar-striped', isRunning);
        bar.classList.toggle('progress-bar-animated', isRunning);
        bar.classList.toggle('bg-success', isComplete);
        bar.classList.toggle('bg-danger', isFailed);
        bar.classList.toggle('bg-primary', isRunning);

        // Badge
        badge.textContent = state.charAt(0).toUpperCase() + state.slice(1);
        badge.className = 'badge export-status-badge px-3 py-2 fs-6 ' +
            (isComplete ? 'bg-success' : (isRunning ? 'bg-primary' : (isFailed ? 'bg-danger' : 'bg-secondary')));

        // Message & Spinner
        messageEl.textContent = status.message || (isComplete ? 'Export completed successfully.' : 'Ready');
        spinnerEl.classList.toggle('d-none', !isRunning);

        // Counts
        if (status.total > 0) {
            countsEl.textContent = 'Processed: ' + Number(status.processed || 0).toLocaleString() + ' / ' + Number(status.total).toLocaleString() + ' leads';
        } else {
            countsEl.textContent = '';
        }

        if (status.updated_at) {
            updatedEl.textContent = 'Last updated: ' + status.updated_at;
        }

        // Error
        if (status.error) {
            errorText.textContent = status.error;
            errorBox.classList.remove('d-none');
        } else {
            errorBox.classList.add('d-none');
        }

        // Start button state
        startBtn.disabled = isRunning;
        startLabel.textContent = isRunning ? 'Generating…' : 'Generate Excel from Web';

        // Download button & file meta
        if (status.file_ready) {
            downloadBtn.classList.remove('d-none');
            fileMeta.classList.remove('d-none');
            const bits = [];
            if (status.file_size_label) bits.push(status.file_size_label);
            if (status.finished_at) bits.push('Completed: ' + status.finished_at);
            fileDetails.textContent = bits.join(' · ');
        }

        // Polling management
        if (isRunning) {
            startPolling();
        } else {
            stopPolling();
        }
    }

    function checkStatus() {
        fetch(statusUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => renderStatus(data))
        .catch(err => console.error('Status check error:', err));
    }

    function startPolling() {
        if (!pollTimer) {
            pollTimer = setInterval(checkStatus, 2000);
        }
    }

    function stopPolling() {
        if (pollTimer) {
            clearInterval(pollTimer);
            pollTimer = null;
        }
    }

    // Trigger start from web
    startBtn.addEventListener('click', function () {
        startBtn.disabled = true;
        startLabel.textContent = 'Starting…';

        fetch(startUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            renderStatus(data);
            startPolling();
        })
        .catch(err => {
            console.error('Start error:', err);
            startBtn.disabled = false;
            startLabel.textContent = 'Generate Excel from Web';
        });
    });

    // Copy command button
    const copyBtn = document.querySelector('.copy-cmd-btn');
    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            const cmd = this.dataset.clipboard;
            navigator.clipboard.writeText(cmd).then(() => {
                const label = this.querySelector('.copy-label');
                const orig = label.textContent;
                label.textContent = 'Copied!';
                setTimeout(() => { label.textContent = orig; }, 2000);
            });
        });
    }

    // Initial poll check if already running
    @if($isRunning)
        startPolling();
    @endif
});
</script>
@endpush
