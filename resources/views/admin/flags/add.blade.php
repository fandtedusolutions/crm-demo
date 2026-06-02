<div class="container p-2">
    <form id="flagAddForm" action="{{ route('admin.flags.submit') }}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="color">Color <span class="text-danger">*</span></label>
                    <input type="color" class="form-control form-control-color w-100" name="color" id="color" value="#0d6efd" required>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="title">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" id="title" placeholder="Enter flag title" required>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="description">Description <span class="text-danger">*</span></label>
                    <textarea name="description" class="form-control" id="description" rows="4" placeholder="Enter description" required></textarea>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success">Submit</button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    $('#flagAddForm').off('submit.flagAdd').on('submit.flagAdd', function(e) {
        e.preventDefault();

        const form = $(this);
        if (form.data('submitting')) {
            return;
        }
        form.data('submitting', true);
        const formData = new FormData(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();

        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="ti ti-loader-2 spin"></i> Submitting...');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function() {
                $('#small_modal').modal('hide');
                toast_success('Flag created successfully!');
                setTimeout(() => {
                    window.location.href = '{{ route("admin.flags.index") }}';
                }, 1000);
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while creating the flag.';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                }

                toast_danger(errorMessage);
                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);
                form.data('submitting', false);
            }
        });
    });
});
</script>
