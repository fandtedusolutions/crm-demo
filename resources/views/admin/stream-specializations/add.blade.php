<div class="container p-2">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="streamSpecializationAddForm" action="{{ route('admin.stream-specializations.submit') }}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="course_id">Course <span class="text-danger">*</span></label>
                    <select name="course_id" class="form-control" id="course_id" required>
                        <option value="">Select Course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" id="course_id-error"></div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="title">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" id="title" placeholder="Enter stream / specialization title" required>
                    <div class="invalid-feedback" id="title-error"></div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <div class="form-check mt-2">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                        <label class="form-check-label" for="is_active">Active</label>
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
$(document).ready(function() {
    $('#streamSpecializationAddForm').off('submit.streamSpecializationAdd').on('submit.streamSpecializationAdd', function(e) {
        e.preventDefault();

        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        const form = $(this);
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
            success: function(response) {
                if (response.success) {
                    $('#small_modal').modal('hide');
                    toast_success(response.message);
                    setTimeout(() => {
                        window.location.href = '{{ route("admin.stream-specializations.index") }}';
                    }, 1000);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    $.each(errors, function(field, messages) {
                        const input = $('[name="' + field + '"]');
                        input.addClass('is-invalid');
                        $('#' + field + '-error').text(messages[0]);
                    });
                } else {
                    toast_danger(xhr.responseJSON?.message || 'An error occurred while creating the stream / specialization.');
                }

                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);
            }
        });
    });
});
</script>
