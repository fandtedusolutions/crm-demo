@if($call->recording_uploaded && $call->recording)
    <div class="call-recording-cell">
        <div class="d-flex flex-wrap gap-1">
            <button type="button"
                class="btn btn-outline-primary btn-sm js-toggle-recording"
                data-target="call-recording-{{ $call->id }}"
                title="Play recording">
                <i class="ti ti-player-play"></i> Play
            </button>
            <a href="{{ route('admin.call-analytics.recording.download', $call->id) }}"
                class="btn btn-outline-secondary btn-sm"
                title="Download recording">
                <i class="ti ti-download"></i>
            </a>
        </div>
        <div id="call-recording-{{ $call->id }}" class="call-recording-player mt-2" style="display: none;">
            <audio controls preload="none" class="w-100" style="min-width: 220px; max-width: 300px;">
                <source src="{{ $call->recording->file_url }}" type="{{ $call->recording->mime_type }}">
                Your browser does not support the audio element.
            </audio>
            <small class="text-muted d-block mt-1">{{ $call->recording->file_name }}</small>
        </div>
    </div>
@elseif($call->has_recording)
    <span class="badge bg-warning text-dark">Pending</span>
@else
    <span class="badge bg-light text-dark border">None</span>
@endif
