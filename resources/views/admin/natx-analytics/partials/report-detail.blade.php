@php
    $records = $detail['records'];
    $isContacts = $detail['type'] === 'contacts';
@endphp

<div class="ca-detail-panel no-print-scroll" id="natxReportDetailSection">
    <div class="ca-detail-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="mb-1 text-primary"><i class="ti ti-list-details me-1"></i>{{ $detail['label'] }}</h5>
            <small class="text-muted">
                All users
                &middot; {{ $records->total() }} {{ $records->total() === 1 ? 'record' : 'records' }}
            </small>
        </div>
        <a href="{{ route('admin.natx-analytics.report', \Illuminate\Support\Arr::except($queryParams, ['metric'])) }}"
           class="btn btn-outline-secondary btn-sm no-print">
            <i class="ti ti-x me-1"></i> Close
        </a>
    </div>
    <div class="ca-table-scroll">
        @if($isContacts)
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
        @else
            <table class="table table-hover align-middle mb-0" style="min-width: 1800px;">
                <thead>
                    <tr>
                        <th colspan="14" class="text-center table-light">natx_app_logs + natx_app_recordings</th>
                    </tr>
                    <tr>
                        <th>#</th>
                        <th class="no-print"></th>
                        <th>Log ID</th>
                        <th>User</th>
                        <th>Phone</th>
                        <th>Contact</th>
                        <th>Type</th>
                        <th>Remarks</th>
                        <th>Duration</th>
                        <th>Call Date/Time</th>
                        <th>has_recording</th>
                        <th>recording_uploaded</th>
                        <th>Rec file</th>
                        <th>Recording</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $index => $call)
                        <tr>
                            <td class="text-muted">{{ $records->firstItem() + $index }}</td>
                            <td class="no-print">
                                <a href="{{ route('admin.natx-analytics.show', $call->id) }}" class="btn btn-outline-primary btn-sm ca-btn-icon" title="View">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </td>
                            <td>{{ $call->id }}</td>
                            <td>
                                <div class="fw-semibold">{{ $call->user?->name ?? 'N/A' }}</div>
                                <small class="text-muted">ID {{ $call->user_id }}</small>
                            </td>
                            <td class="fw-medium">{{ $call->phone_number }}</td>
                            <td>{{ $call->contact_name ?: '-' }}</td>
                            <td>@include('admin.natx-analytics.partials.call-type-badge', ['call' => $call])</td>
                            <td>{{ $call->remarks ?: '-' }}</td>
                            <td>{{ $call->formatted_duration }}</td>
                            <td>
                                <div>{{ $call->display_started_at?->format('d M Y') }}</div>
                                <small class="text-muted">{{ $call->display_started_at?->format('h:i A') }}</small>
                            </td>
                            <td>{{ $call->has_recording ? 'Yes' : 'No' }}</td>
                            <td>{{ $call->recording_uploaded ? 'Yes' : 'No' }}</td>
                            <td><small>{{ $call->recording?->file_name ?: '-' }}</small></td>
                            <td>@include('admin.natx-analytics.partials.recording-cell', ['call' => $call])</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14">
                                <div class="ca-empty-state">
                                    <i class="ti ti-phone-off"></i>
                                    <p>No calls found for this filter.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif
    </div>

    @if($records->hasPages())
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
