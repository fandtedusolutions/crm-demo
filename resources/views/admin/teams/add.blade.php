<div class="container p-2">
    <form id="teamAddForm" action="{{ route('admin.teams.submit') }}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="name">Team Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" id="name" placeholder="Enter Team Name" required>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="description">Description</label>
                    <textarea name="description" class="form-control" id="description" rows="3" placeholder="Enter Description"></textarea>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="marketing_team" id="marketing_team" value="1">
                        <label class="form-check-label" for="marketing_team">
                            Marketing Team
                        </label>
                        <small class="form-text text-muted d-block">Marketing teams will be excluded from telecaller assignments and lead management.</small>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_b2b" id="is_b2b" value="1">
                        <label class="form-check-label" for="is_b2b">
                            <i class="ti ti-building me-1"></i>B2B Team
                        </label>
                        <small class="form-text text-muted d-block">B2B teams handle business-to-business leads.</small>
                    </div>
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
    $('#teamAddForm').off('submit.teamAdd').on('submit.teamAdd', function(e) {
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
                toast_success('Team created successfully!');
                setTimeout(() => {
                    window.location.href = '{{ route("admin.teams.index") }}';
                }, 1000);
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while creating the team.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join('<br>');
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
