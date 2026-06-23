<div class="container p-2">
    <form id="classTimeEditForm" action="{{ route('admin.class-times.update', $edit_data->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="course_id">Course <span class="text-danger">*</span></label>
                    <select name="course_id" class="form-select" id="course_id" required>
                        <option value="">Select Course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ $edit_data->course_id == $course->id ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Only courses with "Needs Time" enabled are shown</small>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="class_type">Class Type <span class="text-danger">*</span></label>
                    <select name="class_type" class="form-select" id="class_type" required>
                        <option value="">Select Class Type</option>
                        <option value="online" {{ $edit_data->class_type == 'online' ? 'selected' : '' }}>Online</option>
                        <option value="offline" {{ $edit_data->class_type == 'offline' ? 'selected' : '' }}>Offline</option>
                    </select>
                    <small class="form-text text-muted">Select whether this class time is for online or offline</small>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="from_time">From Time <span class="text-danger">*</span></label>
                    <input type="time" name="from_time" class="form-control" id="from_time" 
                           value="{{ $edit_data->from_time ? date('H:i', strtotime($edit_data->from_time)) : '' }}" required>
                    <small class="form-text text-muted">Select start time</small>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="to_time">To Time <span class="text-danger">*</span></label>
                    <input type="time" name="to_time" class="form-control" id="to_time" 
                           value="{{ $edit_data->to_time ? date('H:i', strtotime($edit_data->to_time)) : '' }}" required>
                    <small class="form-text text-muted">Select end time (must be after from time)</small>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ $edit_data->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        Active
                    </label>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success">Update</button>
        </div>
    </form>
</div>

<script>
(function() {
    function initClassTimeEditForm() {
        const form = $('#classTimeEditForm');
        if (!form.length) {
            return;
        }

        form.off('submit.classTimeEdit').on('submit.classTimeEdit', function(e) {
            e.preventDefault();

            if (form.data('submitting')) {
                return;
            }

            form.data('submitting', true);

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
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-HTTP-Method-Override': 'PUT'
                },
                success: function(response) {
                    $('#small_modal').modal('hide');
                    toast_success(response.message || 'Class time updated successfully!');
                    setTimeout(function() {
                        window.location.href = '{{ route("admin.class-times.index") }}';
                    }, 1000);
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred while updating the class time.';

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
    }

    initClassTimeEditForm();
})();
</script>

