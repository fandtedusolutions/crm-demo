@php
    $records = $detail['records'];
    $isContacts = $detail['type'] === 'contacts';
@endphp

<div class="card mt-4 border-primary" id="reportDetailSection">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="mb-0">{{ $detail['label'] }}</h5>
            <small class="text-muted">
                @if($activeTelecaller)
                    Telecaller: <strong>{{ $activeTelecaller->name }}</strong>
                @else
                    All telecallers
                @endif
                &middot; {{ $filters['start_date'] }} to {{ $filters['end_date'] }}
            </small>
        </div>
        <a href="{{ route('admin.call-analytics.report', request()->only(['start_date', 'end_date', 'user_id', 'user_name', 'role_id', 'role_title'])) }}"
           class="btn btn-outline-secondary btn-sm no-print">
            <i class="ti ti-x"></i> Close List
        </a>
    </div>
    <div class="card-body">
        @if($isContacts)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Phone</th>
                            <th>Contact Name</th>
                            @if(empty($filters['telecaller_id']))
                                <th>Last Telecaller</th>
                            @endif
                            <th class="text-center">Call Count</th>
                            <th class="text-center">Talk Time</th>
                            <th>Last Called</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $index => $contact)
                            <tr>
                                <td>{{ $records->firstItem() + $index }}</td>
                                <td class="fw-semibold">{{ $contact->phone_number }}</td>
                                <td>{{ $contact->contact_name ?: '-' }}</td>
                                @if(empty($filters['telecaller_id']))
                                    <td>{{ $detail['telecaller_names'][$contact->last_telecaller_id] ?? 'N/A' }}</td>
                                @endif
                                <td class="text-center">{{ number_format($contact->call_count) }}</td>
                                <td class="text-center">{{ \App\Models\CallAppLog::formatDuration((int) $contact->total_duration_seconds) }}</td>
                                <td>
                                    @if($contact->last_called_at)
                                        <div>{{ \Carbon\Carbon::parse($contact->last_called_at)->format('d-m-Y') }}</div>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($contact->last_called_at)->format('h:i A') }}</small>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ empty($filters['telecaller_id']) ? 7 : 6 }}" class="text-center text-muted py-4">
                                    No connected contacts found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th class="no-print">Actions</th>
                            <th>Telecaller</th>
                            <th>Phone</th>
                            <th>Contact</th>
                            <th>Type</th>
                            <th>Remarks</th>
                            <th>Duration</th>
                            <th>Call Date/Time</th>
                            <th>Recording</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $index => $call)
                            <tr>
                                <td>{{ $records->firstItem() + $index }}</td>
                                <td class="no-print">
                                    <a href="{{ route('admin.call-analytics.show', $call->id) }}" class="btn btn-outline-primary btn-sm" title="View">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $call->telecaller?->name ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $call->telecaller?->email }}</small>
                                </td>
                                <td>{{ $call->phone_number }}</td>
                                <td>{{ $call->contact_name ?: '-' }}</td>
                                <td>
                                    @php
                                        $badgeClass = match($call->call_type) {
                                            'incoming' => 'bg-success',
                                            'outgoing' => 'bg-primary',
                                            'not_picked' => 'bg-info text-dark',
                                            'missed' => 'bg-warning text-dark',
                                            'rejected' => 'bg-danger',
                                            default => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $call->call_type_label }}</span>
                                </td>
                                <td>
                                    @if($call->remarks)
                                        <span class="badge bg-warning text-dark">{{ $call->remarks }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $call->formatted_duration }}</td>
                                <td>
                                    <div>{{ $call->started_at?->format('d-m-Y') }}</div>
                                    <small class="text-muted">{{ $call->started_at?->format('h:i A') }}</small>
                                </td>
                                <td>
                                    @include('admin.call-analytics.partials.recording-cell', ['call' => $call])
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">No calls found for this filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        @if($records->hasPages())
            <div class="mt-3 no-print">
                {{ $records->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const section = document.getElementById('reportDetailSection');
        if (section) {
            section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
</script>
@endpush
