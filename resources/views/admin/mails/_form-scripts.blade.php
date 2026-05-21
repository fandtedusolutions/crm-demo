<script>
function initMailCascadeForm(options) {
    const $course = $('#mail_course_id');
    const $batch = $('#mail_batch_id');
    const $admission = $('#mail_admission_batch_id');

    function loadBatches(courseId, selectedId, done) {
        $batch.html('<option value="">Loading...</option>');
        $admission.html('<option value="">Select Admission Batch</option>').prop('disabled', true);

        if (!courseId) {
            $batch.html('<option value="">Select Batch</option>').prop('disabled', true);
            if (typeof done === 'function') done();
            return;
        }

        $.get(`/api/batches/by-course/${courseId}`).done(function(response) {
            let opts = '<option value="">Select Batch</option>';
            if (response.success && response.batches) {
                response.batches.forEach(function(b) {
                    const sel = String(selectedId) === String(b.id) ? 'selected' : '';
                    opts += `<option value="${b.id}" ${sel}>${b.title}</option>`;
                });
            }
            $batch.html(opts).prop('disabled', false);
        }).fail(function() {
            $batch.html('<option value="">Select Batch</option>');
        }).always(function() {
            if (typeof done === 'function') done();
        });
    }

    function loadAdmissionBatches(batchId, selectedId, done) {
        $admission.html('<option value="">Loading...</option>');

        if (!batchId) {
            $admission.html('<option value="">Select Admission Batch</option>').prop('disabled', true);
            if (typeof done === 'function') done();
            return;
        }

        $.get(`/api/admission-batches/by-batch/${batchId}`).done(function(list) {
            let opts = '<option value="">Select Admission Batch</option>';
            const allSelected = String(selectedId) === 'all';
            opts += `<option value="all" ${allSelected ? 'selected' : ''}>All Admission Batches</option>`;
            list.forEach(function(i) {
                const sel = String(selectedId) === String(i.id) ? 'selected' : '';
                opts += `<option value="${i.id}" ${sel}>${i.title}</option>`;
            });
            $admission.html(opts).prop('disabled', false);
        }).fail(function() {
            $admission.html('<option value="">Select Admission Batch</option>');
        }).always(function() {
            if (typeof done === 'function') done();
        });
    }

    $course.on('change', function() {
        loadBatches($(this).val(), '');
    });

    $batch.on('change', function() {
        loadAdmissionBatches($(this).val(), '');
    });

    if (options && options.courseId) {
        loadBatches(options.courseId, options.batchId || '', function() {
            if (options.batchId) {
                loadAdmissionBatches(options.batchId, options.admissionBatchId || '');
            }
        });
    } else {
        $batch.prop('disabled', !$course.val());
        $admission.prop('disabled', true);
    }
}
</script>
