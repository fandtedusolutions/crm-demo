<div class="container p-2">
    @if(!empty($error))
        <div class="alert alert-danger mb-0">{{ $error }}</div>
        <div class="d-flex justify-content-end mt-3">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
    @else
        <p class="mb-3 text-muted small">
            To: <strong>{{ $convertedLead->email }}</strong>
            @if(!empty($context))
                <span class="d-block mt-1">{{ $context }}</span>
            @endif
        </p>

        <form id="supportCourseMailForm" action="{{ route('admin.support-converted-leads.send-course-mail.submit', $convertedLead->id) }}" method="post">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="support_course_mail_template">Mail template</label>
                <select class="form-control" id="support_course_mail_template" name="course_mail_template_id">
                    <option value="">— Select template —</option>
                    @foreach($templateOptions ?? [] as $tpl)
                        <option value="{{ $tpl['id'] }}" {{ isset($selectedTemplateId) && (int) $tpl['id'] === (int) $selectedTemplateId ? 'selected' : '' }}>
                            {{ $tpl['label'] }}
                        </option>
                    @endforeach
                </select>
                <small class="text-muted">Optional: choose a template to load its content. You can edit before sending; the template in Admin → Mail is not changed.</small>
            </div>
            <div class="mb-3">
                <label class="form-label" for="support_course_mail_subject">Subject <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="support_course_mail_subject" name="subject" value="{{ $subject ?? '' }}" required maxlength="255">
            </div>
            <div class="mb-3">
                <label class="form-label" for="support_course_mail_content">Content <span class="text-danger">*</span></label>
                <textarea class="form-control" id="support_course_mail_content" name="content" rows="10">{{ $content ?? '' }}</textarea>
            </div>
            <div class="d-flex justify-content-end gap-2">
                @if(request()->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                @else
                    <a href="{{ route('admin.support-bosse-converted-leads.index') }}" class="btn btn-secondary">Cancel</a>
                @endif
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-mail"></i> Send Mail
                </button>
            </div>
        </form>
    @endif
</div>

@if(empty($error) && (request()->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest'))
@include('admin.converted-leads.partials.send-course-mail-form-scripts')
@endif
