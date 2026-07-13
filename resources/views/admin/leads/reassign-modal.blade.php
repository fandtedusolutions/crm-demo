@php
    $currentTelecallerName = $lead->telecaller->name ?? 'Unassigned';
    $currentTeamName = $lead->team->name
        ?? ($lead->telecaller->team->name ?? 'No Team');
@endphp

<form id="leadReassignForm">
    @csrf
    <div class="modal-body">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="ti ti-user me-2"></i>Lead Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label fw-semibold text-muted">Lead Name</label>
                            <p class="mb-0 fw-medium">{{ $lead->title }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label fw-semibold text-muted">Phone</label>
                            <p class="mb-0 fw-medium">{{ \App\Helpers\PhoneNumberHelper::display($lead->code, $lead->phone) }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label fw-semibold text-muted">Current Telecaller</label>
                            <p class="mb-0 fw-medium">
                                <i class="ti ti-user me-1 text-primary"></i>{{ $currentTelecallerName }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label fw-semibold text-muted">Current Team</label>
                            <p class="mb-0 fw-medium">
                                <i class="ti ti-users-group me-1 text-primary"></i>{{ $currentTeamName }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-info d-flex align-items-start gap-2 mb-3">
            <i class="ti ti-info-circle mt-1"></i>
            <div>
                Select a team first, then choose the telecaller to reassign this lead to.
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label" for="reassign_team_id">Team <span class="text-danger">*</span></label>
            <select class="form-select" name="team_id" id="reassign_team_id" required>
                <option value="">Select Team</option>
                @foreach ($teams as $team)
                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                @endforeach
            </select>
            <div class="invalid-feedback" id="team_id-error"></div>
        </div>

        <div class="mb-3">
            <label class="form-label" for="reassign_telecaller_id">Telecaller <span class="text-danger">*</span></label>
            <select class="form-select" name="telecaller_id" id="reassign_telecaller_id" required>
                <option value="">Select Team First</option>
            </select>
            <div class="invalid-feedback" id="telecaller_id-error"></div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary" id="reassignLeadBtn">
            <span class="btn-text"><i class="ti ti-exchange me-1"></i>Re-assign Lead</span>
            <span class="btn-loading" style="display: none;">
                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                Reassigning...
            </span>
        </button>
    </div>
</form>

<script>
    (function() {
        const $form = $('#leadReassignForm');
        if (!$form.length || $form.data('reassign-init')) {
            return;
        }
        $form.data('reassign-init', true);

        const $teamSelect = $('#reassign_team_id');
        const $telecallerSelect = $('#reassign_telecaller_id');
        const $submitBtn = $('#reassignLeadBtn');
        const $btnText = $submitBtn.find('.btn-text');
        const $btnLoading = $submitBtn.find('.btn-loading');

        $teamSelect.off('change.leadReassign').on('change.leadReassign', function() {
            const teamId = $(this).val();
            $telecallerSelect.html('<option value="">Loading...</option>').prop('disabled', true);

            if (!teamId) {
                $telecallerSelect.html('<option value="">Select Team First</option>').prop('disabled', false);
                return;
            }

            $.ajax({
                url: '{{ route("leads.telecallers-by-team") }}',
                type: 'GET',
                data: { team_id: teamId },
                success: function(response) {
                    $telecallerSelect.html('<option value="">Select Telecaller</option>');
                    if (response.telecallers && response.telecallers.length > 0) {
                        response.telecallers.forEach(function(telecaller) {
                            $telecallerSelect.append(
                                $('<option>', { value: telecaller.id, text: telecaller.name })
                            );
                        });
                    } else {
                        $telecallerSelect.append('<option value="">No telecallers found in this team</option>');
                    }
                    $telecallerSelect.prop('disabled', false);
                },
                error: function() {
                    $telecallerSelect.html('<option value="">Failed to load telecallers</option>').prop('disabled', false);
                }
            });
        });

        $form.off('submit.leadReassign').on('submit.leadReassign', function(e) {
            e.preventDefault();

            $form.find('.is-invalid').removeClass('is-invalid');
            $form.find('.invalid-feedback').text('');

            const teamId = $teamSelect.val();
            const telecallerId = $telecallerSelect.val();

            if (!teamId) {
                if (typeof toast_error === 'function') {
                    toast_error('Please select a team.');
                } else {
                    alert('Please select a team.');
                }
                return;
            }

            if (!telecallerId) {
                if (typeof toast_error === 'function') {
                    toast_error('Please select a telecaller.');
                } else {
                    alert('Please select a telecaller.');
                }
                return;
            }

            $submitBtn.prop('disabled', true);
            $btnText.hide();
            $btnLoading.show();

            $.ajax({
                url: '{{ route("leads.reassign.submit", $lead->id) }}',
                type: 'POST',
                data: $form.serialize(),
                success: function(response) {
                    if (response.success) {
                        if (typeof toast_success === 'function') {
                            toast_success(response.message || 'Lead reassigned successfully!');
                        }
                        $('#ajax_modal').modal('hide');
                        if ($.fn.DataTable.isDataTable('#leadsTable')) {
                            $('#leadsTable').DataTable().ajax.reload(null, false);
                        } else if ($.fn.DataTable.isDataTable('#followupLeadsAjaxTable')) {
                            $('#followupLeadsAjaxTable').DataTable().ajax.reload(null, false);
                        } else {
                            location.reload();
                        }
                    } else {
                        if (typeof toast_error === 'function') {
                            toast_error(response.message || 'Failed to reassign lead.');
                        } else {
                            alert(response.message || 'Failed to reassign lead.');
                        }
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(function(field) {
                            const $field = $form.find('[name="' + field + '"]');
                            $field.addClass('is-invalid');
                            $('#' + field + '-error').text(errors[field][0]);
                        });
                        if (typeof toast_error === 'function') {
                            toast_error(xhr.responseJSON.message || 'Please fix the validation errors.');
                        }
                    } else {
                        const message = (xhr.responseJSON && xhr.responseJSON.message)
                            ? xhr.responseJSON.message
                            : 'An error occurred while reassigning the lead.';
                        if (typeof toast_error === 'function') {
                            toast_error(message);
                        } else {
                            alert(message);
                        }
                    }
                },
                complete: function() {
                    $submitBtn.prop('disabled', false);
                    $btnLoading.hide();
                    $btnText.show();
                }
            });
        });
    })();
</script>
