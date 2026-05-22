@once
@push('scripts')
<script>
window.CRM_TINYMCE_BASE = @json(asset('assets/mantis/js/plugins/tinymce'));
(function() {
    function loadTinyMceScript(callback) {
        if (typeof tinymce !== 'undefined') {
            callback();
            return;
        }

        const existing = document.querySelector('script[data-crm-tinymce]');
        if (existing) {
            if (existing.getAttribute('data-loaded') === '1') {
                callback();
            } else {
                existing.addEventListener('load', callback, { once: true });
            }
            return;
        }

        const script = document.createElement('script');
        script.src = CRM_TINYMCE_BASE + '/tinymce.min.js';
        script.setAttribute('data-crm-tinymce', '1');
        script.onload = function() {
            script.setAttribute('data-loaded', '1');
            callback();
        };
        script.onerror = function() {
            console.error('Failed to load TinyMCE from', script.src);
        };
        document.head.appendChild(script);
    }

    window.initCrmTinyMCE = function(selector, options) {
        options = options || {};

        loadTinyMceScript(function() {
            function run(attempt) {
                attempt = attempt || 0;
                const field = document.querySelector(selector);
                if (!field) {
                    return;
                }

                const isVisible = field.offsetParent !== null
                    || field.getClientRects().length > 0
                    || field.closest('.modal.show');

                if (! isVisible && attempt < 40) {
                    window.setTimeout(function() {
                        run(attempt + 1);
                    }, 50);
                    return;
                }

                const editorId = field.id || selector.replace('#', '');
                if (tinymce.get(editorId)) {
                    tinymce.remove('#' + editorId);
                }

                const config = Object.assign({
                    selector: selector,
                    base_url: CRM_TINYMCE_BASE,
                    suffix: '.min',
                    height: options.height || 320,
                    menubar: false,
                    branding: false,
                    promotion: false,
                    license_key: 'gpl',
                    plugins: 'lists link autolink table code fullscreen',
                    toolbar: 'undo redo | bold italic underline | bullist numlist | link table | removeformat | code',
                    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
                    setup: function(editor) {
                        editor.on('change keyup', function() {
                            editor.save();
                        });
                    }
                }, options.config || {});

                const done = typeof options.onReady === 'function' ? options.onReady : null;
                const initPromise = tinymce.init(config);

                if (initPromise && typeof initPromise.then === 'function') {
                    initPromise.then(function() {
                        if (done) {
                            done(tinymce.get(editorId));
                        }
                    }).catch(function(err) {
                        console.error('TinyMCE init failed for', selector, err);
                    });
                } else if (done) {
                    window.setTimeout(function() {
                        done(tinymce.get(editorId));
                    }, 200);
                }
            }

            run(0);
        });
    };

    window.destroyCrmTinyMCE = function(selector) {
        if (typeof tinymce === 'undefined') {
            return;
        }

        const field = document.querySelector(selector);
        if (!field) {
            return;
        }

        const editorId = field.id || selector.replace('#', '');
        if (tinymce.get(editorId)) {
            tinymce.remove('#' + editorId);
        }
    };

    window.saveCrmTinyMCE = function(selector) {
        if (typeof tinymce === 'undefined') {
            return;
        }

        const field = document.querySelector(selector);
        if (!field) {
            return;
        }

        const editorId = field.id || selector.replace('#', '');
        const editor = tinymce.get(editorId);
        if (editor) {
            editor.save();
        }
    };
})();
</script>
@endpush
@endonce
