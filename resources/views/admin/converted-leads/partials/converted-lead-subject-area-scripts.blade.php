@once
@push('styles')
<style>
    .converted-lead-subject-area-field .subject-area-edit-form {
        min-width: 280px;
        max-width: 360px;
    }

    .converted-lead-subject-area-field .select2-container {
        width: 100% !important;
    }
</style>
@endpush
@push('scripts')
<script>
(function() {
    function getCurrentIds(container) {
        const raw = container.attr('data-current-ids');
        if (!raw || String(raw).trim() === '') {
            return [];
        }
        return String(raw).split(',').map(function(id) {
            return String(id).trim();
        }).filter(Boolean);
    }

    function normalizeSelectedIds(value) {
        if (value == null || value === '') {
            return [];
        }
        return Array.isArray(value) ? value.map(String) : [String(value)];
    }

    function destroySubjectAreaSelect2($select) {
        if ($select.length && $select.hasClass('select2-hidden-accessible') && $.fn.select2) {
            $select.select2('destroy');
        }
    }

    function createSubjectAreaEditForm() {
        return `
            <div class="edit-form subject-area-edit-form">
                <select class="form-select form-select-sm subject-area-select-edit" multiple>
                    <option value="">Loading...</option>
                </select>
                <div class="btn-group mt-1">
                    <button type="button" class="btn btn-success btn-sm subject-area-save-edit">Save</button>
                    <button type="button" class="btn btn-secondary btn-sm subject-area-cancel-edit">Cancel</button>
                </div>
            </div>
        `;
    }

    function loadSubjectAreasIntoSelect($select, currentIds, $parent) {
        $.get('/api/subject-areas').done(function(subjectAreas) {
            let options = '';
            const selectedSet = new Set(currentIds.map(String));
            subjectAreas.forEach(function(area) {
                const selected = selectedSet.has(String(area.id)) ? 'selected' : '';
                options += `<option value="${area.id}" ${selected}>${area.title}</option>`;
            });
            $select.html(options);
            destroySubjectAreaSelect2($select);
            if ($.fn.select2) {
                $select.select2({
                    dropdownParent: $parent,
                    width: '100%',
                    placeholder: 'Select subject areas',
                    allowClear: true
                });
                $select.val(currentIds.map(String)).trigger('change');
            }
        }).fail(function() {
            $select.html('<option value="">Error loading subject areas</option>');
        });
    }

    function closeSubjectAreaEdit(container) {
        destroySubjectAreaSelect2(container.find('.subject-area-select-edit'));
        container.removeClass('editing');
        container.find('.edit-form').remove();
    }

    function buildSavePayload(field, ids, token) {
        const payload = {
            field: field,
            _token: token
        };
        ids.forEach(function(id, index) {
            payload['value[' + index + ']'] = id;
        });
        return payload;
    }

    $(document).on('click', '.converted-lead-subject-area-field .edit-btn', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        const container = $(this).closest('.converted-lead-subject-area-field');
        if (container.hasClass('editing')) {
            return;
        }

        $('.converted-lead-subject-area-field.editing').not(container).each(function() {
            closeSubjectAreaEdit($(this));
        });

        const currentIds = getCurrentIds(container);
        container.addClass('editing');
        container.append(createSubjectAreaEditForm());
        const $form = container.find('.subject-area-edit-form');
        loadSubjectAreasIntoSelect(container.find('.subject-area-select-edit'), currentIds, $form);
    });

    $(document).on('click', '.subject-area-cancel-edit', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        closeSubjectAreaEdit($(this).closest('.converted-lead-subject-area-field'));
    });

    $(document).on('click', '.subject-area-save-edit', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        const container = $(this).closest('.converted-lead-subject-area-field');
        const id = container.data('id');
        const value = normalizeSelectedIds(container.find('.subject-area-select-edit').val());
        const btn = $(this);

        if (btn.data('busy')) return;
        btn.data('busy', true).prop('disabled', true).html('<i class="ti ti-loader-2 spin"></i>');

        $.ajax({
            url: `/admin/converted-leads/${id}/inline-update`,
            method: 'POST',
            data: buildSavePayload('subject_area_ids', value, $('meta[name="csrf-token"]').attr('content')),
            success: function(response) {
                if (response.success) {
                    const displayValue = response.value || 'N/A';
                    container.find('.display-value').html(
                        displayValue === 'N/A'
                            ? '<span class="text-muted">N/A</span>'
                            : `<span class="converted-lead-subject-areas-display">${$('<div>').text(displayValue).html()}</span>`
                    );
                    const ids = response.subject_area_ids || value || [];
                    container.attr('data-current-ids', ids.join(','));
                    if (typeof toast_success === 'function') {
                        toast_success(response.message || 'Updated successfully');
                    }
                } else if (typeof toast_error === 'function') {
                    toast_error(response.error || 'Update failed');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Update failed';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join(', ');
                }
                if (typeof toast_error === 'function') {
                    toast_error(errorMessage);
                }
            },
            complete: function() {
                btn.data('busy', false).prop('disabled', false).html('Save');
                closeSubjectAreaEdit(container);
            }
        });
    });
})();
</script>
@endpush
@endonce
