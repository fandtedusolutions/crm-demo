<!-- Select2 is now loaded globally -->
<style>
    /* Ensure Select2 styling is applied */
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        min-height: 38px;
        padding: 0.375rem 0.75rem;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #0d6efd;
        border: 1px solid #0d6efd;
        border-radius: 0.25rem;
        color: #fff;
        padding: 0.25rem 0.5rem;
        margin: 0.125rem;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #fff;
        margin-right: 0.25rem;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #fff;
    }

    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #86b7fe;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .select2-dropdown {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #0d6efd;
        color: #fff;
    }
</style>

<div class="p-3">
    <form action="{{ route('leads.bulk-upload.submit') }}" method="post" enctype="multipart/form-data" id="bulkUploadForm">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="excel_file">Select Excel File <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="file" class="form-control" id="excel_file" name="excel_file" accept=".xlsx,.xls" required />
                        <a href="{{ route('leads.bulk-upload.template') }}" class="btn btn-outline-info" type="button">
                            <i class="ti ti-download"></i> Download Template
                        </a>
                    </div>
                    <small class="text-muted">Supported formats: .xlsx, .xls (Max size: 2MB)</small>
                    <div id="excel_file_error" class="text-danger mt-1" style="display: none;"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="lead_source_id">Lead Source <span class="text-danger">*</span></label>
                    <select class="form-select" name="lead_source_id" id="lead_source_id" required>
                        <option value="">Select Lead Source</option>
                        @foreach($leadSources as $source)
                        <option value="{{ $source->id }}">{{ $source->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="lead_status_id">Lead Status <span class="text-danger">*</span></label>
                    <select class="form-select" name="lead_status_id" id="lead_status_id" required>
                        <option value="">Select Lead Status</option>
                        @foreach($leadStatuses as $status)
                        <option value="{{ $status->id }}" {{ $status->id == 1 ? 'selected' : '' }}>{{ $status->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="course_id">Course <span class="text-danger">*</span></label>
                    <select class="form-select" name="course_id" id="course_id" required>
                        <option value="">Select Course</option>
                        @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="team_id">Team <span class="text-danger">*</span></label>
                    <select class="form-select" name="team_id" id="team_id" required>
                        <option value="">Select Team</option>
                        @foreach($teams as $team)
                        <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                    <div id="team_id_error" class="text-danger mt-1" style="display: none;"></div>
                </div>
            </div>


            <div class="col-md-12">
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="assign_to_all" name="assign_to_all" value="1">
                        <label class="form-check-label" for="assign_to_all">
                            <strong>Assign to all Telecallers</strong> - Leads will be assigned to all team telecallers equally
                        </label>
                    </div>
                </div>
            </div>

            <div class="col-md-12" id="telecaller-selection">
                <div class="mb-3">
                    <label class="form-label" for="telecallers">Assign to Telecallers <span class="text-danger">*</span></label>
                    <select class="form-select select2-multiple" name="telecallers[]" id="telecaller" multiple>
                        <option value="">Select Team First</option>
                    </select>

                    <small class="text-muted">Select a team first to see available telecallers</small>
                    <div id="telecallers_error" class="text-danger mt-1" style="display: none;"></div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="alert alert-info">
                    <h6>Excel Format Guide:</h6>
                    <p class="mb-2"><strong>Required columns:</strong> Name, Phone, Place, Remarks</p>
                    <p class="mb-2"><strong>Column order:</strong></p>
                    <ul class="mb-2">
                        <li><strong>Column A:</strong> Name (Required)</li>
                        <li><strong>Column B:</strong> Phone (Required) - Use international format with country code</li>
                        <li><strong>Column C:</strong> Place (Optional)</li>
                        <li><strong>Column D:</strong> Remarks (Optional)</li>
                    </ul>
                    <p class="mb-2"><strong>Phone Format:</strong> Use international format with country code:</p>
                    <ul class="mb-2">
                        <li><code>+91 9876543210</code> (India)</li>
                        <li><code>+1 5551234567</code> (US/Canada)</li>
                        <li><code>+44 7700123456</code> (UK)</li>
                        <li><code>+86 13800138000</code> (China)</li>
                    </ul>
                    <p class="mb-2"><strong>Template:</strong> Click "Download Template" above to get the correct Excel format with sample data.</p>
                    <p class="mb-0"><strong>Note:</strong> Duplicate phone numbers (same code + phone) will be automatically skipped. Place and Remarks fields are optional.</p>
                </div>
            </div>

            <!-- Error Alert -->
            <div id="bulk-upload-error" class="alert alert-danger" style="display: none;">
                <h6><i class="ti ti-alert-circle"></i> Upload Error</h6>
                <div id="bulk-upload-error-message"></div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success" id="bulkUploadSubmitBtn">Upload & Process</button>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        // Initialize Select2 for telecaller dropdown
        function initializeTelecallerSelect2() {
            const telecallerSelect = $('#telecaller');

            // Destroy existing Select2 if any
            if (telecallerSelect.hasClass('select2-hidden-accessible')) {
                telecallerSelect.select2('destroy');
            }

            // Initialize Select2
            try {
                telecallerSelect.select2({
                    placeholder: 'Select telecallers...',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: telecallerSelect.parent()
                });
                return true;
            } catch (error) {
                return false;
            }
        }

        // Try to initialize Select2
        let attempts = 0;
        const maxAttempts = 3;

        function tryInitializeSelect2() {
            attempts++;

            if (typeof $.fn.select2 !== 'undefined') {
                if (initializeTelecallerSelect2()) {
                    return;
                }
            }

            if (attempts < maxAttempts) {
                setTimeout(tryInitializeSelect2, 200 * attempts);
            }
        }

        // Start initialization
        tryInitializeSelect2();

        // Handle team selection to load telecallers
        $('#team_id').on('change', function() {
            const teamId = $(this).val();
            const telecallerSelect = $('#telecaller');

            if (teamId) {
                // Load telecallers from specific team
                $.get('{{ route("leads.telecallers-by-team") }}', {
                        team_id: teamId
                    })
                    .done(function(data) {
                        telecallerSelect.empty();
                        telecallerSelect.append('<option value="">Select Telecallers</option>');

                        if (data.telecallers && data.telecallers.length > 0) {
                            $.each(data.telecallers, function(index, telecaller) {
                                telecallerSelect.append(
                                    $('<option></option>').val(telecaller.id).text(telecaller.name)
                                );
                            });
                        } else {
                            telecallerSelect.append('<option value="">No telecallers found in this team</option>');
                        }

                        // Trigger Select2 update if available
                        if (typeof $.fn.select2 !== 'undefined' && telecallerSelect.hasClass('select2-hidden-accessible')) {
                            telecallerSelect.trigger('change');
                        } else if (typeof $.fn.select2 !== 'undefined') {
                            // Reinitialize Select2 if it's not already initialized
                            telecallerSelect.select2({
                                placeholder: 'Select telecallers...',
                                allowClear: true,
                                width: '100%'
                            });
                        }
                    })
                    .fail(function() {
                        telecallerSelect.empty();
                        telecallerSelect.append('<option value="">Error loading telecallers</option>');
                        console.error('Failed to load telecallers');
                    });
            } else {
                telecallerSelect.empty();
                telecallerSelect.append('<option value="">Select Team First</option>');
                // Trigger Select2 update
                if (typeof $.fn.select2 !== 'undefined' && telecallerSelect.hasClass('select2-hidden-accessible')) {
                    telecallerSelect.trigger('change');
                }
            }
        });

        // Handle assign to all checkbox
        $('#assign_to_all').on('change', function() {
            const isChecked = $(this).is(':checked');
            const telecallerSelection = $('#telecaller-selection');
            const teamId = $('#team_id').val();

            if (isChecked) {
                telecallerSelection.hide();
                $('#telecaller').prop('required', false);

                // Show message about team requirement
                if (!teamId) {
                    toast_warning('Please select a team first to assign leads to all telecallers in that team.');
                } else {
                    // Load telecallers for the team to show how many will be assigned
                    $.get('{{ route("leads.telecallers-by-team") }}', {
                            team_id: teamId
                        })
                        .done(function(data) {
                            if (data.telecallers && data.telecallers.length > 0) {
                                toast_success(`Will assign leads to ${data.telecallers.length} telecaller(s) in the selected team.`);
                            } else {
                                toast_warning('No telecallers found in the selected team. Please select a different team.');
                            }
                        });
                }
            } else {
                telecallerSelection.show();
                $('#telecaller').prop('required', true);
            }
        });

        // Handle team selection change when assign to all is checked
        $('#team_id').on('change', function() {
            const teamId = $(this).val();
            const assignToAll = $('#assign_to_all').is(':checked');

            if (assignToAll && teamId) {
                // Load telecallers for the team to show how many will be assigned
                $.get('{{ route("leads.telecallers-by-team") }}', {
                        team_id: teamId
                    })
                    .done(function(data) {
                        if (data.telecallers && data.telecallers.length > 0) {
                            toast_success(`Will assign leads to ${data.telecallers.length} telecaller(s) in the selected team.`);
                        } else {
                            toast_warning('No telecallers found in the selected team. Please select a different team.');
                        }
                    });
            }
        });

        // File size validation
        $('#excel_file').on('change', function() {
            const file = this.files[0];
            const errorDiv = $('#excel_file_error');

            if (file) {
                const fileSize = file.size / 1024 / 1024; // Convert to MB
                if (fileSize > 2) {
                    errorDiv.text('File size must be less than 2MB. Current file size: ' + fileSize.toFixed(2) + 'MB').show();
                    this.value = '';
                    return false;
                } else {
                    errorDiv.hide();
                }
            } else {
                errorDiv.hide();
            }
        });

        // Clear errors when form values change
        $('#team_id, #telecaller, #assign_to_all').on('change', function() {
            $('.text-danger').hide();
            $('#bulk-upload-error').hide();
        });

        // Form submission with loading state (single request only)
        $(document).off('submit.bulkUpload', '#bulkUploadForm');
        $(document).on('submit.bulkUpload', '#bulkUploadForm', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            if (window.__bulkUploadSubmitting) {
                return false;
            }

            // Check file size before submission
            const fileInput = $('#excel_file')[0];
            const errorDiv = $('#excel_file_error');

            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const fileSize = file.size / 1024 / 1024; // Convert to MB
                if (fileSize > 2) {
                    errorDiv.text('File size must be less than 2MB. Current file size: ' + fileSize.toFixed(2) + 'MB').show();
                    return false;
                }
            }

            // Check team selection
            const teamId = $('#team_id').val();
            if (!teamId) {
                toast_error('Please select a team.');
                $('#team_id').focus();
                return false;
            }

            // Check telecaller assignment
            const assignToAll = $('#assign_to_all').is(':checked');
            const selectedTelecallers = $('#telecaller').val();

            if (!assignToAll && (!selectedTelecallers || selectedTelecallers.length === 0)) {
                toast_error('Please select at least one telecaller or choose "Assign to all telecallers in team".');
                return false;
            }

            const form = $(this);
            const submitBtn = $('#bulkUploadSubmitBtn');
            const originalText = submitBtn.html();
            const formData = new FormData(this);

            window.__bulkUploadSubmitting = true;
            submitBtn.prop('disabled', true);
            submitBtn.html('<i class="ti ti-loader-2"></i> Processing...');

            if (window.__bulkUploadXhr && typeof window.__bulkUploadXhr.abort === 'function') {
                window.__bulkUploadXhr.abort();
            }

            window.__bulkUploadXhr = $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#ajax_modal').modal('hide');

                    if (response.message) {
                        toast_success(response.message);
                    } else {
                        toast_success('Leads uploaded successfully!');
                    }

                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                },
                error: function(xhr) {
                    if (xhr.statusText === 'abort') {
                        return;
                    }

                    let errorMessage = 'An error occurred while uploading leads.';
                    let errorDetails = '';

                    $('.text-danger').hide();
                    $('#bulk-upload-error').hide();

                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        if (xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            const errorList = Object.values(errors).flat();

                            Object.keys(errors).forEach(field => {
                                const fieldErrorDiv = $('#' + field + '_error');
                                if (fieldErrorDiv.length) {
                                    fieldErrorDiv.html(errors[field].join('<br>')).show();
                                }
                            });

                            if (errorList.length > 0) {
                                errorDetails = '<br><br><strong>Details:</strong><br>' + errorList.join('<br>');
                            }
                        }
                    }

                    $('#bulk-upload-error-message').html(errorMessage + errorDetails);
                    $('#bulk-upload-error').show();
                    toast_error(errorMessage + errorDetails);

                    window.__bulkUploadSubmitting = false;
                    submitBtn.prop('disabled', false);
                    submitBtn.html(originalText);
                },
                complete: function() {
                    window.__bulkUploadXhr = null;
                }
            });

            return false;
        });

        $('#ajax_modal').off('hidden.bs.modal.bulkUpload').on('hidden.bs.modal.bulkUpload', function() {
            window.__bulkUploadSubmitting = false;
            if (window.__bulkUploadXhr && typeof window.__bulkUploadXhr.abort === 'function') {
                window.__bulkUploadXhr.abort();
            }
            window.__bulkUploadXhr = null;
        });
    });
</script>