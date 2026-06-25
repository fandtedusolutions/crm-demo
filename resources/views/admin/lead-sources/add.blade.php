<div class="container p-2">
    <form id="leadSourceAddForm" action="{{ route('admin.lead-sources.submit') }}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="title">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" id="title" placeholder="Enter Lead Source Title" required>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="description">Description</label>
                    <textarea name="description" class="form-control" id="description" rows="3" placeholder="Enter Description"></textarea>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                    <label class="form-check-label" for="is_active">
                        Active
                    </label>
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
(function() {
    $('#leadSourceAddForm').off('submit.leadSourceAdd').on('submit.leadSourceAdd', function(e) {
        e.preventDefault();

        const form = $(this);
        if (form.data('submitting')) {
            return false;
        }

        const formData = new FormData(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();

        form.data('submitting', true);
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
            success: function(response) {
                $('#small_modal').modal('hide');
                toast_success('Lead Source created successfully!');
                setTimeout(() => {
                    window.location.href = '{{ route("admin.lead-sources.index") }}';
                }, 1000);
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while creating the lead source.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join('<br>');
                } else if (xhr.status === 422) {
                    errorMessage = 'Validation failed. Please check your input.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error. Please try again.';
                }
                
                toast_danger(errorMessage);
                form.data('submitting', false);
                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);
            }
        });
    });
})();
</script>
