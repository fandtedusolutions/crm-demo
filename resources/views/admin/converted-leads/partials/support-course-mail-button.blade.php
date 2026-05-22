@php
    $canSendCourseMail = filled($convertedLead->email)
        && $convertedLead->course_id
        && $convertedLead->batch_id;
    $mailModalTitle = 'Send Mail — ' . $convertedLead->name;
@endphp
@if($canSendCourseMail)
<a href="javascript:void(0);"
    class="btn btn-sm btn-primary"
    title="Send course mail to {{ $convertedLead->email }}"
    onclick="show_large_modal('{{ route('admin.support-converted-leads.send-course-mail', $convertedLead->id) }}', {{ json_encode($mailModalTitle) }})">
    <i class="ti ti-mail"></i>
</a>
@else
<button type="button"
    class="btn btn-sm btn-primary"
    disabled
    title="{{ filled($convertedLead->email) ? 'Course and batch are required' : 'No email on file' }}">
    <i class="ti ti-mail"></i>
</button>
@endif
