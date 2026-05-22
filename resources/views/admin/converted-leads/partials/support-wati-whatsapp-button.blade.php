@php
    use App\Support\ConvertedLeadWhatsAppSupport;

    $recipient = ConvertedLeadWhatsAppSupport::resolveRecipient($convertedLead);
    $templateName = config('wati.template_name', 'support_desk');
    $canSend = $recipient
        && config('wati.enabled')
        && filled(config('wati.api_endpoint'))
        && filled(config('wati.api_token'))
        && filled(config('wati.channel_phone_number'));
@endphp
@if($canSend)
<button type="button"
    class="btn btn-sm btn-success js-send-wati-whatsapp"
    title="Send WhatsApp template {{ $templateName }} via Wati"
    data-url="{{ route('admin.support-bosse-converted-leads.send-whatsapp', $convertedLead->id) }}"
    data-name="{{ $convertedLead->name }}"
    data-recipient="{{ $recipient['display'] }} ({{ $recipient['source'] }})"
    data-template="{{ $templateName }}">
    <i class="ti ti-brand-whatsapp"></i>
</button>
@else
<button type="button"
    class="btn btn-sm btn-success"
    disabled
    title="{{ $recipient ? 'Configure WATI_* in .env (including WATI_CHANNEL_PHONE_NUMBER)' : 'No WhatsApp or phone number available' }}">
    <i class="ti ti-brand-whatsapp"></i>
</button>
@endif
