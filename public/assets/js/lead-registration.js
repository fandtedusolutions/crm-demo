/*
 * Lead Registration Forms — UI enhancements (shared by all course forms).
 *  1. Step caption under the stepper ("Step 2 of 4 · Communication Details").
 *  2. Upload step: Required / Optional / Uploaded badge on every document card
 *     and a "x of y required documents uploaded" progress summary.
 * Markup and form logic are not changed; this only reads state and adds UI.
 */
(function () {
    'use strict';

    function initStepCaption() {
        var indicator = document.querySelector('.step-indicator');
        if (!indicator || indicator.dataset.lrReady === '1') {
            return;
        }
        indicator.dataset.lrReady = '1';

        var steps = indicator.querySelectorAll('.step');
        var total = steps.length;
        if (!total) {
            return;
        }

        var caption = document.createElement('div');
        caption.className = 'lr-step-caption';
        caption.setAttribute('aria-live', 'polite');
        indicator.insertAdjacentElement('afterend', caption);

        function sectionName(index) {
            var heading = document.querySelector('#formStep' + (index + 1) + ' > h4');
            return heading ? heading.textContent.trim() : '';
        }

        function render() {
            var current = -1;
            steps.forEach(function (el, i) {
                if (el.classList.contains('active')) {
                    current = i;
                }
            });
            if (current < 0) {
                return;
            }

            var count = document.createElement('span');
            count.className = 'lr-step-count';
            count.textContent = 'Step ' + (current + 1) + ' of ' + total;

            var name = document.createElement('span');
            name.className = 'lr-step-name';
            name.textContent = sectionName(current);

            caption.replaceChildren(count, name);
        }

        if (window.MutationObserver) {
            new MutationObserver(render).observe(indicator, {
                attributes: true,
                attributeFilter: ['class'],
                subtree: true
            });
        }

        render();
    }

    function initUploadStep() {
        var step = document.getElementById('formStep4');
        if (!step) {
            return;
        }

        // Each document card = a form-group wrapping a file input and an upload area.
        var groups = Array.prototype.filter.call(
            step.querySelectorAll('.form-group'),
            function (g) { return g.querySelector('input[type="file"]') && g.querySelector('.file-upload-area'); }
        );
        groups.forEach(function (g) {
            g.classList.add('lr-upload-group');
            var area = g.querySelector('.file-upload-area');
            var badge = document.createElement('span');
            badge.className = 'lr-badge';
            area.appendChild(badge);
        });

        // Summary bar under the step heading.
        var heading = step.querySelector(':scope > h4');
        var summary = document.createElement('div');
        summary.className = 'lr-upload-summary';
        summary.innerHTML =
            '<div class="lr-upload-summary-text"></div>' +
            '<div class="lr-meter" role="progressbar" aria-valuemin="0" aria-valuemax="100"><span></span></div>';
        if (heading) {
            heading.insertAdjacentElement('afterend', summary);
        } else {
            step.insertBefore(summary, step.firstChild);
        }
        var textEl = summary.querySelector('.lr-upload-summary-text');
        var meter = summary.querySelector('.lr-meter');
        var meterBar = meter.querySelector('span');

        function isVisible(el) {
            return el.offsetParent !== null;
        }

        function refresh() {
            var requiredTotal = 0;
            var requiredDone = 0;
            var visibleCols = [];

            groups.forEach(function (g) {
                var input = g.querySelector('input[type="file"]');
                var badge = g.querySelector('.lr-badge');
                var col = g.parentElement;
                var visible = isVisible(g);
                var uploaded = input.files && input.files.length > 0;
                var required = input.hasAttribute('required');

                if (col) {
                    col.classList.remove('lr-span-full');
                }
                if (!visible) {
                    badge.style.display = 'none';
                    return;
                }
                badge.style.display = '';
                if (col) {
                    visibleCols.push(col);
                }

                if (uploaded) {
                    badge.className = 'lr-badge lr-badge--done';
                    badge.textContent = 'Uploaded';
                } else if (required) {
                    badge.className = 'lr-badge lr-badge--required';
                    badge.textContent = 'Required';
                } else {
                    badge.className = 'lr-badge lr-badge--optional';
                    badge.textContent = 'Optional';
                }

                if (required) {
                    requiredTotal++;
                    if (uploaded) {
                        requiredDone++;
                    }
                }
            });

            // An odd card out at the end would leave a hole; let it span the row.
            if (visibleCols.length % 2 === 1) {
                visibleCols[visibleCols.length - 1].classList.add('lr-span-full');
            }

            if (requiredTotal === 0) {
                summary.style.display = 'none';
                return;
            }
            summary.style.display = '';

            var percent = Math.round((requiredDone / requiredTotal) * 100);
            textEl.innerHTML = '<strong>' + requiredDone + ' of ' + requiredTotal +
                '</strong> required documents uploaded';
            meterBar.style.width = percent + '%';
            meter.setAttribute('aria-valuenow', String(percent));
            summary.classList.toggle('is-complete', requiredDone === requiredTotal);
        }

        // Files change, placement category toggles sections, and step buttons
        // re-show/hide content. Run after the form's own handlers.
        document.addEventListener('change', function () { setTimeout(refresh, 0); });
        document.addEventListener('click', function () { setTimeout(refresh, 0); });

        // Re-check whenever step 4 becomes visible.
        if (window.MutationObserver) {
            new MutationObserver(function () { refresh(); }).observe(step, {
                attributes: true,
                attributeFilter: ['class', 'style']
            });
        }

        refresh();
    }

    function init() {
        initStepCaption();
        initUploadStep();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
