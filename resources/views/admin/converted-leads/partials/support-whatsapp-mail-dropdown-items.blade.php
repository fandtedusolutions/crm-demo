@php
    use App\Support\ConvertedLeadWhatsAppSupport;

    $recipient = ConvertedLeadWhatsAppSupport::resolveRecipient($convertedLead);
    $templateName = config('wati.template_name', 'support_desk');
    $watiCanSend = $recipient
        && config('wati.enabled')
        && filled(config('wati.api_endpoint'))
        && filled(config('wati.api_token'))
        && filled(config('wati.channel_phone_number'));
    $canSendCourseMail = filled($convertedLead->email)
        && $convertedLead->course_id
        && $convertedLead->batch_id;
@endphp
<li>
    <button type="button"
        class="dropdown-item js-send-wati-whatsapp {{ $watiCanSend ? '' : 'disabled' }}"
        @if($watiCanSend)
        data-url="{{ route('admin.support-converted-leads.send-whatsapp', $convertedLead->id) }}"
        data-name="{{ $convertedLead->name }}"
        data-recipient="{{ $recipient['display'] }} ({{ $recipient['source'] }})"
        data-template="{{ $templateName }}"
        @else
        disabled
        @endif>
        <i class="ti ti-brand-whatsapp me-2"></i>WhatsApp ({{ $templateName }})
    </button>
</li>
<li>
    @if($canSendCourseMail)
    <a href="javascript:void(0);"
        class="dropdown-item"
        onclick="show_large_modal('{{ route('admin.support-converted-leads.send-course-mail', $convertedLead->id) }}', {{ json_encode('Send Mail — ' . $convertedLead->name) }})">
        <i class="ti ti-mail me-2"></i>Mail
    </a>
    @else
    <span class="dropdown-item disabled text-muted"
        title="{{ filled($convertedLead->email) ? 'Course and batch are required' : 'No email on file' }}">
        <i class="ti ti-mail me-2"></i>Mail
    </span>
    @endif
</li>
