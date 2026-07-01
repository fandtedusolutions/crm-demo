@php
    $statusType = $type ?? 'academic';
    $isAcademic = $statusType === 'academic';
    $flagField = $isAcademic ? 'is_academic_verified' : 'is_support_verified';
    $isVerified = (bool) ($convertedLead->{$flagField} ?? false);
    $showToggle = $showToggle ?? false;
    $toggleUrl = $toggleUrl ?? null;
    $title = $title ?? ($isAcademic ? 'academic' : 'support');
    $badgeClass = $isVerified ? 'bg-success' : 'bg-secondary';
    $buttonClass = $isVerified ? 'btn-outline-danger' : 'btn-outline-success';
    $iconClass = $isVerified ? 'ti-x' : 'ti-check';
    $buttonLabel = $isVerified ? 'Unverify' : 'Verify';
    $useModal = $useModal ?? false;
    $compact = $compact ?? false;
@endphp

<span class="{{ $compact ? 'verify-status-compact d-inline-flex align-items-center gap-1' : '' }}">
<span class="badge {{ $badgeClass }}{{ $showToggle && !$compact ? ' me-1' : '' }}{{ $compact ? ' verify-badge-compact' : '' }}">
    {{ $compact ? ($isVerified ? 'Verified' : 'Pending') : ($isVerified ? 'Verified' : 'Not Verified') }}
</span>

@if($showToggle && $toggleUrl)
    <button type="button"
        class="btn btn-sm {{ $buttonClass }} toggle-{{ $statusType }}-verify-btn{{ $compact ? ' verify-toggle-compact' : '' }}"
        @if($useModal) data-use-modal="1" @endif
        data-id="{{ $convertedLead->id }}"
        data-name="{{ $convertedLead->name }}"
        data-verified="{{ $isVerified ? 1 : 0 }}"
        data-url="{{ $toggleUrl }}"
        title="{{ $buttonLabel }} {{ $title }}">
        <i class="ti {{ $iconClass }}{{ $compact ? ' f-14' : '' }}"></i>
    </button>
@endif
</span>

@once
    @push('scripts')
    <script>
        (function($){
            if (!$) { return; }
            function handleStatusToggle($btn, type) {
                const url = $btn.data('url');
                if (!url) { return; }
                const name = $btn.data('name') || 'this record';
                const isVerified = String($btn.data('verified')) === '1';
                const actionText = isVerified ? 'unverify' : 'verify';
                const label = type === 'academic' ? 'academic' : 'support';
                if (!window.confirm(`Are you sure you want to ${actionText} ${label} status for ${name}?`)) {
                    return;
                }

                const csrfToken = $('meta[name="csrf-token"]').attr('content');
                if (!csrfToken) {
                    console.error('CSRF token missing');
                    return;
                }

                $btn.prop('disabled', true).addClass('disabled');

                $.post(url, {_token: csrfToken})
                    .done(function(res) {
                        const success = res && (res.success || res.status === 'success');
                        const message = (res && (res.message || res.status_message)) || 'Status updated successfully.';
                        if (typeof window.show_alert === 'function') {
                            window.show_alert(success ? 'success' : 'error', message);
                        } else {
                            window.alert(message);
                        }
                        if (success) {
                            setTimeout(function(){ window.location.reload(); }, 400);
                        }
                    })
                    .fail(function(xhr) {
                        let message = 'Failed to update status.';
                        if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        if (typeof window.show_alert === 'function') {
                            window.show_alert('error', message);
                        } else {
                            window.alert(message);
                        }
                    })
                    .always(function() {
                        $btn.prop('disabled', false).removeClass('disabled');
                    });
            }

            $(document).on('click', '.toggle-academic-verify-btn:not([data-use-modal="1"])', function(e){
                e.preventDefault();
                handleStatusToggle($(this), 'academic');
            });

            $(document).on('click', '.toggle-support-verify-btn:not([data-use-modal="1"])', function(e){
                e.preventDefault();
                handleStatusToggle($(this), 'support');
            });
        })(window.jQuery);
    </script>
    @endpush
@endonce
