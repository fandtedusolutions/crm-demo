@once
@push('scripts')
<script>
(function() {
    let watiSendUrl = null;

    $(document).on('click', '.js-send-wati-whatsapp', function(e) {
        e.preventDefault();

        const btn = $(this);
        if (btn.prop('disabled')) {
            return;
        }

        watiSendUrl = btn.data('url');

        $('#watiWhatsappRecipient').text(btn.data('recipient') || btn.data('name') || 'student');
        $('#watiWhatsappTemplate').text(btn.data('template') || 'support_desk');
        $('#watiWhatsappModal').modal('show');
    });

    $('#confirmWatiWhatsappBtn').on('click', function() {
        if (!watiSendUrl) {
            return;
        }

        const $btn = $(this);
        const originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="ti ti-loader-2 spin"></i> Sending...');

        $.ajax({
            url: watiSendUrl,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if (res.success) {
                    $('#watiWhatsappModal').modal('hide');
                    if (typeof toast_success === 'function') {
                        toast_success(res.message || 'WhatsApp template sent.');
                    } else if (typeof show_alert === 'function') {
                        show_alert('success', res.message || 'WhatsApp template sent.');
                    }
                } else if (typeof toast_danger === 'function') {
                    toast_danger(res.error || 'Failed to send WhatsApp message.');
                } else if (typeof show_alert === 'function') {
                    show_alert('error', res.error || 'Failed to send WhatsApp message.');
                }
            },
            error: function(xhr) {
                let msg = 'Failed to send WhatsApp message.';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    msg = xhr.responseJSON.error;
                }
                if (typeof toast_danger === 'function') {
                    toast_danger(msg);
                } else if (typeof show_alert === 'function') {
                    show_alert('error', msg);
                }
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalHtml);
            }
        });
    });

    $('#watiWhatsappModal').on('hidden.bs.modal', function() {
        watiSendUrl = null;
    });
})();
</script>
@endpush
@endonce
