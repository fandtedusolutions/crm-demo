<div class="ca-table-scroll">
    <table class="table table-hover table-sm mb-0 align-middle">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>User</th>
                <th>Phone Number</th>
                <th>Contact</th>
                <th>Call Type</th>
                <th>Remarks</th>
                <th>Duration Seconds</th>
                <th>Started At</th>
                <th>Ended At</th>
                <th>Has Recording</th>
                <th>Recording Uploaded</th>
                <th>Recording Duration Seconds</th>
                <th>Audio</th>
                <th>Device Call ID</th>
                <th>Device ID</th>
                <th>Created At</th>
                <th>App Version</th>
            </tr>
        </thead>
        <tbody>
            @forelse($calls as $index => $call)
                <tr>
                    <td class="text-muted">{{ $calls->firstItem() + $index }}</td>
                    <td>
                        <div class="fw-semibold">{{ $call->user?->name ?? 'N/A' }}</div>
                        @if($call->user?->email)
                            <small class="text-muted">{{ $call->user->email }}</small>
                        @endif
                    </td>
                    <td class="fw-medium">{{ $call->phone_number }}</td>
                    <td>{{ $call->contact_name ?: '-' }}</td>
                    <td>@include('admin.natx-analytics.partials.call-type-badge', ['call' => $call])</td>
                    <td>{{ $call->remarks ?: '-' }}</td>
                    <td>{{ $call->duration_seconds }}</td>
                    <td>
                        @if($call->display_started_at)
                            <div>{{ $call->display_started_at->format('d-m-Y') }}</div>
                            <small class="text-muted">{{ $call->display_started_at->format('h:i A') }}</small>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($call->display_ended_at)
                            <div>{{ $call->display_ended_at->format('d-m-Y') }}</div>
                            <small class="text-muted">{{ $call->display_ended_at->format('h:i A') }}</small>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $call->has_recording ? 'Yes' : 'No' }}</td>
                    <td>{{ $call->recording_uploaded ? 'Yes' : 'No' }}</td>
                    <td>{{ $call->recording_duration_seconds ?? '-' }}</td>
                    <td>
                        @if($call->recording_uploaded && $call->recording)
                            @include('admin.natx-analytics.partials.recording-cell', ['call' => $call])
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td><small>{{ $call->device_call_id }}</small></td>
                    <td><small title="{{ $call->device_id }}">{{ \Illuminate\Support\Str::limit($call->device_id, 24) }}</small></td>
                    <td><small>{{ $call->created_at?->format('d-m-Y h:i A') ?: '-' }}</small></td>
                    <td>{{ $call->app_version ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="17">
                        <div class="ca-empty-state">
                            <i class="ti ti-phone-off"></i>
                            <p>No NatX call logs found.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($calls->hasPages())
<div class="ca-pagination no-print">
    {{ $calls->links('pagination::bootstrap-5') }}
</div>
@endif
