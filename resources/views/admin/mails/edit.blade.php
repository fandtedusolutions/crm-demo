<div class="container p-2">
    <form id="mailEditForm" action="{{ route('admin.mails.update', $edit_data->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label" for="mail_course_id">Course <span class="text-danger">*</span></label>
                    <select name="course_id" class="form-control" id="mail_course_id" required>
                        <option value="">Select Course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ (int) $edit_data->course_id === (int) $course->id ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label" for="mail_batch_id">Batch <span class="text-danger">*</span></label>
                    <select name="batch_id" class="form-control" id="mail_batch_id" required>
                        <option value="">Select Batch</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label" for="mail_admission_batch_id">Admission Batch <span class="text-danger">*</span></label>
                    <select name="admission_batch_id" class="form-control" id="mail_admission_batch_id" required>
                        <option value="">Select Admission Batch</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="content">Content <span class="text-danger">*</span></label>
                    <textarea name="content" class="form-control" id="content" rows="8" placeholder="Enter mail content" required>{{ $edit_data->content }}</textarea>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success">Update</button>
        </div>
    </form>
</div>

@include('admin.mails._form-scripts')

<script>
$(document).ready(function() {
    initMailCascadeForm({
        courseId: '{{ $edit_data->course_id }}',
        batchId: '{{ $edit_data->batch_id }}',
        admissionBatchId: '{{ $edit_data->admission_batch_id ?? 'all' }}'
    });

    $('#mailEditForm').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const formData = new FormData(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();

        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="ti ti-loader-2 spin"></i> Updating...');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-HTTP-Method-Override': 'PUT'
            },
            success: function() {
                $('#large_modal').modal('hide');
                toast_success('Mail updated successfully!');
                setTimeout(() => {
                    window.location.href = '{{ route("admin.mails.index") }}';
                }, 1000);
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while updating the mail.';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                }

                toast_danger(errorMessage);
                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);
            }
        });
    });
});
</script>
