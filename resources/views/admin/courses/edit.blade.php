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

    <form id="courseEditForm" action="{{ route('admin.courses.update', $edit_data->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="title">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" 
                           id="title" value="{{ $edit_data->title }}" 
                           placeholder="Enter Course Title"
                           @unless(is_super_admin()) readonly @endunless
                           required>
                    <div class="invalid-feedback" id="title-error"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="code">Code</label>
                    <input type="text" name="code" class="form-control" 
                           id="code" value="{{ $edit_data->code }}" 
                           placeholder="Enter Course Code">
                    <div class="invalid-feedback" id="code-error"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="amount">Amount (₹) <span class="text-danger">*</span></label>
                    <input type="number" name="amount" class="form-control" 
                           id="amount" value="{{ $edit_data->amount }}"
                           placeholder="0.00" step="0.01" min="0" required>
                    <div class="invalid-feedback" id="amount-error"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="hod_id">Select HOD</label>
                    <select class="form-select" id="hod_id" name="hod_id">
                        <option value="">Select HOD</option>
                        @foreach($hodUsers as $hod)
                            <option value="{{ $hod->id }}" 
                                    data-code="{{ $hod->code ?? '' }}" 
                                    data-phone="{{ $hod->phone ?? '' }}"
                                    {{ $edit_data->hod_id == $hod->id ? 'selected' : '' }}>
                                {{ $hod->name }} ({{ $hod->email }})
                            </option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" id="hod_id-error"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="hod_number">HOD Number</label>
                    <input type="text" name="hod_number" class="form-control" 
                           id="hod_number" value="{{ $edit_data->hod_number ?? '' }}" 
                           placeholder="Enter HOD Number or select HOD above" readonly>
                    <div class="invalid-feedback" id="hod_number-error"></div>
                    <small class="form-text text-muted">Auto-filled when HOD is selected</small>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <div class="form-check mt-4">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                               {{ $edit_data->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active
                        </label>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <div class="form-check mt-4">
                        <input type="hidden" name="needs_time" value="0">
                        <input class="form-check-input" type="checkbox" id="needs_time" name="needs_time" value="1" 
                               {{ $edit_data->needs_time ? 'checked' : '' }}>
                        <label class="form-check-label" for="needs_time">
                            Needs Time
                        </label>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <div class="form-check mt-4">
                        <input type="hidden" name="is_online" value="0">
                        <input class="form-check-input" type="checkbox" id="is_online" name="is_online" value="1" 
                               {{ $edit_data->is_online ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_online">
                            Online
                        </label>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <div class="form-check mt-4">
                        <input type="hidden" name="is_offline" value="0">
                        <input class="form-check-input" type="checkbox" id="is_offline" name="is_offline" value="1" 
                               {{ $edit_data->is_offline ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_offline">
                            Offline
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success float-end">Update</button>
    </form>

    <script>
    (function() {
        function initCourseEditForm() {
            const form = $('#courseEditForm');
            if (!form.length) {
                return;
            }

            $('#hod_id').off('change.courseEdit').on('change.courseEdit', function() {
                const selectedOption = $(this).find('option:selected');
                const code = selectedOption.data('code');
                const phone = selectedOption.data('phone');

                if (code && phone) {
                    $('#hod_number').val('+' + code + ' ' + phone);
                } else if (phone) {
                    $('#hod_number').val(phone);
                } else {
                    $('#hod_number').val('');
                }
            });

            const selectedHod = $('#hod_id option:selected');
            if (selectedHod.val() && !$('#hod_number').val()) {
                const code = selectedHod.data('code');
                const phone = selectedHod.data('phone');
                if (code && phone) {
                    $('#hod_number').val('+' + code + ' ' + phone);
                } else if (phone) {
                    $('#hod_number').val(phone);
                }
            }

            $('#hod_number').off('focus.courseEdit').on('focus.courseEdit', function() {
                $(this).prop('readonly', false);
            });

            form.off('submit.courseEdit').on('submit.courseEdit', function(e) {
                e.preventDefault();

                if (form.data('submitting')) {
                    return;
                }

                form.data('submitting', true);
                form.find('.form-control').removeClass('is-invalid');
                form.find('.invalid-feedback').text('');

                const submitBtn = form.find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true);
                submitBtn.html('<i class="ti ti-loader-2 spin"></i> Updating...');

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.success) {
                            toast_success(response.message);
                            $('#small_modal').modal('hide');
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                            return;
                        }

                        form.data('submitting', false);
                        submitBtn.prop('disabled', false);
                        submitBtn.html(originalText);
                    },
                    error: function(xhr) {
                        form.data('submitting', false);
                        submitBtn.prop('disabled', false);
                        submitBtn.html(originalText);

                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $.each(errors, function(field, messages) {
                                const input = form.find('[name="' + field + '"]');
                                input.addClass('is-invalid');
                                input.siblings('.invalid-feedback').text(messages[0]);
                            });
                        } else {
                            alert('An error occurred. Please try again.');
                        }
                    }
                });
            });
        }

        initCourseEditForm();
    })();
    </script>
</div>
