@php
    use App\Models\NatXAppLog;

    $formatMs = function ($ms) {
        $dt = NatXAppLog::dateTimeFromMilliseconds($ms ? (int) $ms : null);
        return $dt ? $dt->format('d-m-Y h:i A') : '-';
    };
@endphp

<div class="ca-table-scroll">
    <table class="table table-hover table-sm mb-0 align-middle" style="min-width: 2200px;">
        <thead class="table-light">
            <tr>
                <th rowspan="2">#</th>
                <th rowspan="2"></th>
                <th colspan="20" class="text-center border-start">natx_app_logs</th>
                <th colspan="10" class="text-center border-start">natx_app_recordings</th>
            </tr>
            <tr>
                <th class="border-start">Log ID</th>
                <th>User</th>
                <th>user_id</th>
                <th>device_call_id</th>
                <th>device_id</th>
                <th>phone_number</th>
                <th>contact_name</th>
                <th>call_type</th>
                <th>remarks</th>
                <th>duration_seconds</th>
                <th>started_at_ms</th>
                <th>started_at</th>
                <th>end_at_ms</th>
                <th>ended_at</th>
                <th>has_recording</th>
                <th>recording_uploaded</th>
                <th>recording_duration_seconds</th>
                <th>recording_file_name</th>
                <th>app_version</th>
                <th>created_at</th>
                <th>updated_at</th>
                <th class="border-start">Rec ID</th>
                <th>file_name</th>
                <th>file_path</th>
                <th>mime_type</th>
                <th>file_size_bytes</th>
                <th>rec duration_seconds</th>
                <th>recorded_at_ms</th>
                <th>rec created_at</th>
                <th>rec updated_at</th>
                <th>Audio</th>
            </tr>
        </thead>
        <tbody>
            @forelse($calls as $index => $call)
                @php $rec = $call->recording; @endphp
                <tr>
                    <td class="text-muted">{{ $calls->firstItem() + $index }}</td>
                    <td>
                        <a href="{{ route('admin.natx-analytics.show', $call->id) }}" class="btn btn-outline-primary btn-sm ca-btn-icon" title="View">
                            <i class="ti ti-eye"></i>
                        </a>
                    </td>
                    <td class="border-start">{{ $call->id }}</td>
                    <td>
                        <div class="fw-semibold">{{ $call->user?->name ?? 'N/A' }}</div>
                        <small class="text-muted">{{ $call->user?->email }}</small>
                    </td>
                    <td>{{ $call->user_id }}</td>
                    <td><small>{{ $call->device_call_id }}</small></td>
                    <td><small title="{{ $call->device_id }}">{{ \Illuminate\Support\Str::limit($call->device_id, 18) }}</small></td>
                    <td class="fw-medium">{{ $call->phone_number }}</td>
                    <td>{{ $call->contact_name ?: '-' }}</td>
                    <td>@include('admin.natx-analytics.partials.call-type-badge', ['call' => $call])</td>
                    <td>{{ $call->remarks ?: '-' }}</td>
                    <td>{{ $call->duration_seconds }}</td>
                    <td><small>{{ $call->started_at_ms }}</small></td>
                    <td>
                        <div>{{ $call->display_started_at?->format('d-m-Y') }}</div>
                        <small class="text-muted">{{ $call->display_started_at?->format('h:i A') }}</small>
                    </td>
                    <td><small>{{ $call->end_at_ms ?? '-' }}</small></td>
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
                    <td><small>{{ $call->recording_file_name ?: '-' }}</small></td>
                    <td>{{ $call->app_version ?: '-' }}</td>
                    <td><small>{{ $call->created_at?->format('d-m-Y h:i A') }}</small></td>
                    <td><small>{{ $call->updated_at?->format('d-m-Y h:i A') }}</small></td>
                    <td class="border-start">{{ $rec?->id ?? '-' }}</td>
                    <td><small>{{ $rec?->file_name ?: '-' }}</small></td>
                    <td><small title="{{ $rec?->file_path }}">{{ $rec?->file_path ? \Illuminate\Support\Str::limit($rec->file_path, 28) : '-' }}</small></td>
                    <td><small>{{ $rec?->mime_type ?: '-' }}</small></td>
                    <td>{{ $rec?->file_size_bytes ?? '-' }}</td>
                    <td>{{ $rec?->duration_seconds ?? '-' }}</td>
                    <td>
                        @if($rec?->recorded_at_ms)
                            <small>{{ $rec->recorded_at_ms }}</small>
                            <div class="text-muted">{{ $formatMs($rec->recorded_at_ms) }}</div>
                        @else
                            -
                        @endif
                    </td>
                    <td><small>{{ $rec?->created_at?->format('d-m-Y h:i A') ?: '-' }}</small></td>
                    <td><small>{{ $rec?->updated_at?->format('d-m-Y h:i A') ?: '-' }}</small></td>
                    <td>
                        @if($rec && $call->recording_uploaded)
                            @include('admin.natx-analytics.partials.recording-cell', ['call' => $call])
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="32">
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
