<script>
(function() {
    const CONTENT_SELECTOR = '#support_course_mail_content';
    const courseMailTemplates = @json($templateOptions);

    function applySelectedCourseMailTemplate(templateId) {
        const tpl = courseMailTemplates.find(function(t) {
            return String(t.id) === String(templateId);
        });
        if (!tpl) {
            return;
        }
        $(CONTENT_SELECTOR).val(tpl.content || '');
    }

    $('#support_course_mail_template').on('change', function() {
        const templateId = $(this).val();
        if (!templateId) {
            $(CONTENT_SELECTOR).val('');
            return;
        }
        applySelectedCourseMailTemplate(templateId);
    });

    $('#supportCourseMailForm').on('submit', function(e) {
        e.preventDefault();

        const $form = $(this);
        const $btn = $form.find('button[type="submit"]');
        const originalHtml = $btn.html();

        $btn.prop('disabled', true).html('<i class="ti ti-loader-2 spin"></i> Sending...');

        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            dataType: 'json',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                subject: $('#support_course_mail_subject').val(),
                content: $(CONTENT_SELECTOR).val()
            },
            success: function(res) {
                if (res && res.success) {
                    $('#large_modal, #ajax_modal').modal('hide');
                    if (typeof toast_success === 'function') {
                        toast_success(res.message || 'Mail sent successfully.');
                    }
                } else if (typeof toast_danger === 'function') {
                    toast_danger((res && res.error) ? res.error : 'Failed to send mail.');
                }
            },
            error: function(xhr) {
                let msg = 'Failed to send mail.';
                if (xhr.responseJSON) {
                    msg = xhr.responseJSON.error || xhr.responseJSON.message || msg;
                }
                if (typeof toast_danger === 'function') {
                    toast_danger(msg);
                }
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalHtml);
            }
        });
    });
})();
</script>
