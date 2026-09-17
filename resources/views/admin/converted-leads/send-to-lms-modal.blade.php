@php
    $alreadyShared = (bool) ($convertedLead->is_shared_to_lms ?? false);
@endphp

<form id="sendToLmsForm" method="POST" action="{{ route('admin.converted-leads.send-to-lms', $convertedLead->id) }}" data-submit-url="{{ route('admin.converted-leads.send-to-lms', $convertedLead->id) }}">
    @csrf
    <div class="row g-3">
        <div class="col-12">
            <div class="border rounded p-3 bg-light">
                <div class="mb-2">
                    <small class="text-muted d-block">CRM Course</small>
                    <strong>{{ $convertedLead->course?->title ?? 'Not Assigned' }}</strong>
                </div>
                <div>
                    <small class="text-muted d-block">Mapped LMS Course</small>
                    @if($lmsCourse)
                        <strong>{{ $lmsCourse->title }}</strong>
                        <span class="text-muted">(ID: {{ $lmsCourse->id }})</span>
                    @else
                        <strong class="text-danger">No LMS course mapped</strong>
                    @endif
                </div>
            </div>
        </div>

        @if($alreadyShared)
            <div class="col-12">
                <div class="alert alert-success mb-0">
                    <i class="ti ti-check me-1"></i>
                    Already sent to LMS
                    @if($convertedLead->shared_to_lms_at)
                        on {{ $convertedLead->shared_to_lms_at->format('d-m-Y H:i') }}
                    @endif
                    @if($convertedLead->lms_stream_id)
                        (Stream ID: {{ $convertedLead->lms_stream_id }})
                    @endif
                </div>
            </div>
        @elseif(!$lmsCourse)
            <div class="col-12">
                <div class="alert alert-warning mb-0">
                    Map this CRM course to an LMS course under Course Mapping before sending.
                </div>
            </div>
        @elseif($streamsError)
            <div class="col-12">
                <div class="alert alert-danger mb-0">{{ $streamsError }}</div>
            </div>
        @else
            <div class="col-12">
                <label for="lms_stream_id" class="form-label">LMS Stream <span class="text-danger">*</span></label>
                <select class="form-select" id="lms_stream_id" name="lms_stream_id" required>
                    <option value="">Select Stream</option>
                    @foreach($streams as $stream)
                        <option value="{{ $stream['id'] }}">{{ $stream['name'] }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback" data-error-for="lms_stream_id"></div>
                @if(count($streams) === 0)
                    <small class="text-muted">No streams found for this LMS course.</small>
                @endif
            </div>
            <div class="col-12">
                <label for="lms_notes" class="form-label">Notes <span class="text-muted">(Optional)</span></label>
                <textarea class="form-control" id="lms_notes" name="notes" rows="3" maxlength="1000" placeholder="Add optional notes for LMS"></textarea>
                <div class="invalid-feedback" data-error-for="notes"></div>
            </div>
        @endif

        <div class="col-12 text-end">
            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Close</button>
            @if(!$alreadyShared && $lmsCourse && !$streamsError && count($streams) > 0)
                <button type="submit" class="btn btn-primary" id="sendToLmsSubmitBtn">Send</button>
            @endif
        </div>
    </div>
</form>
