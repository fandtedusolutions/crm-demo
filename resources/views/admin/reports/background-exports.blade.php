@extends('layouts.mantis')

@section('title', 'Excel Exports')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Excel Exports</h5>
                    <p class="m-b-0 text-muted">Background jobs save the workbooks on this server. You can leave this page and come back to download.</p>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Excel Exports</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    @foreach($exports as $key => $export)
        @php $status = $export['status']; @endphp
        <div class="col-xl-6 mb-3">
            <div class="card h-100" data-export-card="{{ $key }}">
                <div class="card-header py-3">
                    <h5 class="mb-0">{{ $export['title'] }}</h5>
                </div>
                <div class="card-body d-flex flex-column">
                    <p class="text-muted">{{ $export['description'] }}</p>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="export-message">{{ $status['message'] }}</span>
                            <span class="export-percent fw-semibold">{{ (int) $status['percent'] }}%</span>
                        </div>
                        <div class="progress" style="height: 18px;">
                            <div class="progress-bar export-bar {{ ($status['status'] ?? '') === 'failed' ? 'bg-danger' : '' }}"
                                role="progressbar"
                                style="width: {{ (int) $status['percent'] }}%;"
                                aria-valuenow="{{ (int) $status['percent'] }}"
                                aria-valuemin="0"
                                aria-valuemax="100">
                                {{ (int) $status['percent'] }}%
                            </div>
                        </div>
                        <small class="text-muted export-counts d-block mt-1">
                            @if(($status['total'] ?? 0) > 0)
                                {{ number_format($status['processed'] ?? 0) }} / {{ number_format($status['total']) }}
                            @endif
                        </small>
                        <small class="text-danger export-error d-block mt-1">{{ $status['error'] }}</small>
                    </div>

                    <div class="mt-auto">
                        <button type="button" class="btn btn-primary export-start">
                            <i class="ti ti-player-play me-1"></i>
                            <span class="export-start-label">Generate Excel</span>
                        </button>
                        <a href="{{ route('admin.background-exports.download', $key) }}"
                            class="btn btn-success export-download {{ empty($status['file_ready']) ? 'd-none' : '' }}">
                            <i class="ti ti-download me-1"></i> Download
                        </a>
                        <div class="export-file-meta text-muted small mt-2 {{ empty($status['file_ready']) ? 'd-none' : '' }}">
                            Saved on the server
                            @if(!empty($status['file_size_label']))
                                · {{ $status['file_size_label'] }}
                            @endif
                            @if(!empty($status['finished_at']))
                                · {{ $status['finished_at'] }}
                            @endif
                        </div>
                        <div class="mt-3">
                            <label class="form-label mb-1">Server command</label>
                            <code class="d-block user-select-all">{{ $export['command'] }}</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const cards = document.querySelectorAll('[data-export-card]');
    const timers = {};

    function applyStatus(card, status) {
        const percent = Math.max(0, Math.min(100, parseInt(status.percent || 0, 10)));
        const running = status.status === 'queued' || status.status === 'processing';
        const bar = card.querySelector('.export-bar');
        const startButton = card.querySelector('.export-start');
        const startLabel = card.querySelector('.export-start-label');
        const download = card.querySelector('.export-download');
        const meta = card.querySelector('.export-file-meta');

        card.querySelector('.export-message').textContent = status.message || '';
        card.querySelector('.export-percent').textContent = percent + '%';
        bar.style.width = percent + '%';
        bar.setAttribute('aria-valuenow', String(percent));
        bar.textContent = percent + '%';
        bar.classList.toggle('progress-bar-striped', running);
        bar.classList.toggle('progress-bar-animated', running);
        bar.classList.toggle('bg-danger', status.status === 'failed');
        bar.classList.toggle('bg-success', status.status === 'completed');

        const counts = card.querySelector('.export-counts');
        counts.textContent = status.total > 0
            ? Number(status.processed || 0).toLocaleString() + ' / ' + Number(status.total).toLocaleString()
            : '';

        card.querySelector('.export-error').textContent = status.error || '';
        startButton.disabled = running;
        startLabel.textContent = running ? 'Generating…' : 'Generate Excel';

        if (status.file_ready) {
            download.classList.remove('d-none');
            const bits = ['Saved on the server'];
            if (status.file_size_label) {
                bits.push(status.file_size_label);
            }
            if (status.finished_at) {
                bits.push(status.finished_at);
            }
            meta.textContent = bits.join(' · ');
            meta.classList.remove('d-none');
        }

        if (running) {
            watch(card);
        } else if (timers[card.dataset.exportCard]) {
            clearInterval(timers[card.dataset.exportCard]);
            delete timers[card.dataset.exportCard];
        }
    }

    function watch(card) {
        const key = card.dataset.exportCard;
        if (timers[key]) {
            return;
        }
        timers[key] = setInterval(function () {
            fetch(card.dataset.statusUrl, { headers: { 'Accept': 'application/json' } })
                .then(function (response) { return response.json(); })
                .then(function (status) { applyStatus(card, status); })
                .catch(function () {});
        }, 1500);
    }

    cards.forEach(function (card) {
        const key = card.dataset.exportCard;
        card.dataset.statusUrl = @json(url('/admin/background-exports')) + '/' + key + '/status';
        card.dataset.startUrl = @json(url('/admin/background-exports')) + '/' + key + '/start';

        const initial = @json(collect($exports)->map(fn ($export) => $export['status']));
        if (initial[key]) {
            applyStatus(card, initial[key]);
        }

        card.querySelector('.export-start').addEventListener('click', function () {
            const button = this;
            button.disabled = true;
            fetch(card.dataset.startUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                }
            })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('Unable to start the export.');
                    }
                    return response.json();
                })
                .then(function (status) { applyStatus(card, status); })
                .catch(function (error) {
                    button.disabled = false;
                    card.querySelector('.export-error').textContent = error.message;
                });
        });
    });
});
</script>
@endpush
