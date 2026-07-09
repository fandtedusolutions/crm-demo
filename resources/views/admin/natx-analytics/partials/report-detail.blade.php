@php
    $records = $detail['records'];
    $isContacts = $detail['type'] === 'contacts';
@endphp

<div class="ca-detail-panel no-print-scroll" id="natxReportDetailSection">
    <div class="ca-detail-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="mb-1 text-primary"><i class="ti ti-list-details me-1"></i>{{ $detail['label'] }}</h5>
            <small class="text-muted">
                @if(!empty($activeUser))
                    User: <strong>{{ $activeUser->name }}</strong>
                @else
                    All users
                @endif
                &middot; {{ $records->total() }} {{ $records->total() === 1 ? 'record' : 'records' }}
            </small>
        </div>
        <a href="{{ route('admin.natx-analytics.report', \Illuminate\Support\Arr::except($queryParams, ['metric'])) }}"
           class="btn btn-outline-secondary btn-sm no-print">
            <i class="ti ti-x me-1"></i> Close
        </a>
    </div>
    @if($isContacts)
    <div class="ca-table-scroll">
        <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th class="no-print"></th>
                        <th>Phone</th>
                        <th>Contact Name</th>
                        <th>Last User</th>
                        <th class="text-center">Call Count</th>
                        <th class="text-center">Talk Time</th>
                        <th>Last Called</th>
                        <th>Recording</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $index => $contact)
                        <tr>
                            <td class="text-muted">{{ $records->firstItem() + $index }}</td>
                            <td class="no-print">
                                @if($contact->last_call_id)
                                    <a href="{{ route('admin.natx-analytics.show', $contact->last_call_id) }}" class="btn btn-outline-primary btn-sm ca-btn-icon" title="View latest call">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $contact->phone_number }}</td>
                            <td>{{ $contact->contact_name ?: '-' }}</td>
                            <td>{{ $detail['user_names'][$contact->last_user_id] ?? 'N/A' }}</td>
                            <td class="text-center">{{ number_format($contact->call_count) }}</td>
                            <td class="text-center">{{ \App\Models\NatXAppLog::formatDuration((int) $contact->total_duration_seconds) }}</td>
                            <td>
                                @if($contact->last_started_at_ms)
                                    @php($lastCalledAt = \App\Models\NatXAppLog::dateTimeFromMilliseconds((int) $contact->last_started_at_ms))
                                    <div>{{ $lastCalledAt?->format('d M Y') }}</div>
                                    <small class="text-muted">{{ $lastCalledAt?->format('h:i A') }}</small>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($contact->recording_call)
                                    @include('admin.natx-analytics.partials.recording-cell', ['call' => $contact->recording_call])
                                @else
                                    <span class="badge bg-light text-dark border">None</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="ca-empty-state">
                                    <i class="ti ti-users"></i>
                                    <p>No connected contacts found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
    </div>
    @else
        @include('admin.natx-analytics.partials.full-logs-table', ['calls' => $records])
    @endif

    @if($isContacts && $records->hasPages())
        <div class="ca-pagination no-print">
            {{ $records->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const section = document.getElementById('natxReportDetailSection');
        if (section) {
            section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
</script>
@endpush
