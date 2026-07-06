<!-- Status Update Modal Content -->
<form id="statusChangeForm">
    @csrf
    @php
    $previousReason = optional($lead->leadActivities->firstWhere('reason', '!=', null))->reason;
    $followupStatusIds = [2, 7, 8, 9];
    @endphp
    <div class="modal-body">
        <!-- Lead Information Card -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="ti ti-user me-2"></i>Lead Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-2">
                            <label class="form-label fw-semibold text-muted">Lead Name</label>
                            <p class="mb-0 fw-medium">{{ $lead->title }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-2">
                            <label class="form-label fw-semibold text-muted">Phone</label>
                            <p class="mb-0 fw-medium">{{ \App\Helpers\PhoneNumberHelper::display($lead->code, $lead->phone) }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-2">
                            <label class="form-label fw-semibold text-muted">Current Status</label>
                            <p class="mb-0">
                                <span class="badge {{ \App\Helpers\StatusHelper::getLeadStatusColorClass($lead->leadStatus->id) }}">
                                    {{ $lead->leadStatus->title }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label" for="lead_status_id">New Lead Status <span class="text-danger">*</span></label>
            <select class="form-select" name="lead_status_id" id="lead_status_id" required>
                <option value="">Select New Status</option>
                @foreach($leadStatuses as $status)
                <option value="{{ $status->id }}" {{ $status->id == $lead->lead_status_id ? 'selected' : '' }}>
                    {{ $status->title }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label" for="course_id">Course</label>
            <select class="form-select" name="course_id" id="course_id">
                <option value="">Select Course</option>
                @isset($courses)
                @foreach($courses as $course)
                <option value="{{ $course->id }}" {{ (string)$course->id === (string)$lead->course_id ? 'selected' : '' }}>
                    {{ $course->title }}
                </option>
                @endforeach
                @endisset
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label" for="rating">Lead Rating (1 - 10) <span class="text-danger">*</span></label>
            <input type="number" class="form-control" name="rating" id="rating"
                min="1" max="10" required placeholder="Enter rating between 1 and 10"
                value="{{ old('rating', $lead->rating) }}">
        </div>

        <!-- Followup Date Section - Only shown when followup-required statuses are selected -->
        <div class="mb-3" id="followupDateSection" style="display: none;">
            <div class="alert alert-warning d-flex align-items-center">
                <i class="ti ti-calendar me-2"></i>
                <div class="flex-grow-1">
                    <strong>Followup Required:</strong> Please set a followup date for this lead.
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label class="form-label" for="followup_date">Followup Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="followup_date" id="followup_date"
                        min="{{ date('Y-m-d') }}"
                        data-saved-date="{{ optional($lead->followup_date)->format('Y-m-d') }}"
                        value="{{ optional($lead->followup_date)->format('Y-m-d') }}">
                    <small class="text-muted" id="followupDateHint" style="display: none;"></small>
                </div>
            </div>
        </div>

        <!-- Demo Booking Button - Only shown when status 6 is selected -->
        <div class="mb-3" id="demoBookingSection" style="display: none;">
            <div class="alert alert-info d-flex align-items-center">
                <i class="ti ti-info-circle me-2"></i>
                <div class="flex-grow-1">
                    <strong>Demo Conduction Required:</strong> Please complete the demo conduction form before updating the status.
                </div>
            </div>
            <div class="text-center">
                <a href="https://docs.google.com/forms/d/e/1FAIpQLSchtc8xlKUJehZNmzoKTkRvwLwk4-SGjzKSHM2UFToAhgdTlQ/viewform?usp=sf_link"
                    target="_blank"
                    class="btn btn-warning"
                    id="demoBookingBtn"
                    title="Open Demo Conduction Form">
                    <i class="ti ti-file-text me-2"></i>Complete Demo Conduction Form
                </a>
                <div class="mt-2">
                    <small class="text-muted">Click the button above to open the demo conduction form in a new tab. After clicking this button, you can update the status.</small>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label" for="reason">Reason for Status Change <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="reason" id="reason" required placeholder="Enter reason for status change..." value="{{ old('reason', $previousReason) }}" />
        </div>

        <div class="mb-3">
            <label class="form-label" for="remarks">Remarks <span class="text-danger">*</span></label>
            <textarea class="form-control" name="remarks" id="remarks" rows="3" required placeholder="Enter remarks for this status change...">{{ $lead->remarks }}</textarea>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="date">Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="date" id="date" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="time">Time <span class="text-danger">*</span></label>
                    <input type="time" class="form-control" name="time" id="time" value="{{ date('H:i') }}" required>
                </div>
            </div>
        </div>

        <!-- Activity History Section -->
        @if($lead->leadActivities && $lead->leadActivities->count() > 0)
        <div class="mb-3">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="ti ti-history me-2"></i>Recent Activity History</h6>
                </div>
                <div class="card-body" style="max-height: 250px; overflow-y: auto;">
                    @foreach($lead->leadActivities->take(5) as $activity)
                    <div class="d-flex align-items-start mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="flex-shrink-0 me-3">
                            <div class="avtar avtar-s rounded-circle bg-light-info d-flex align-items-center justify-content-center">
                                @if($activity->leadStatus)
                                <i class="ti ti-arrow-right f-12"></i>
                                @elseif($activity->activity_type == 'bulk_upload')
                                <i class="ti ti-upload f-12"></i>
                                @else
                                <i class="ti ti-clock f-12"></i>
                                @endif
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div>
                                    <h6 class="mb-1 f-14 fw-semibold">
                                        @if($activity->leadStatus)
                                        Status Changed to: {{ $activity->leadStatus->title }}
                                        @elseif($activity->activity_type)
                                        {{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }}
                                        @else
                                        Activity
                                        @endif
                                    </h6>
                                    @if($activity->reason)
                                    <p class="mb-1 f-13"><strong>Reason:</strong> <span class="badge bg-info">{{ $activity->formatted_reason }}</span></p>
                                    @endif
                                    @if($activity->description)
                                    <p class="mb-1 text-muted f-13">{{ $activity->description }}</p>
                                    @endif
                                    @if(in_array($activity->lead_status_id, $followupStatusIds) && $activity->followup_date)
                                    <p class="mb-1 f-13"><strong>Followup Date:</strong> <span class="badge bg-warning">{{ $activity->followup_date->format('d M Y') }}</span></p>
                                    @endif
                                    @if($activity->remarks)
                                    <div class="mb-1 f-13 text-dark" style="white-space: pre-wrap; word-wrap: break-word;">{{ $activity->remarks }}</div>
                                    @endif
                                </div>
                                <small class="text-muted f-12">
                                    {{ $activity->created_at->format('M d, h:i A') }}
                                </small>
                            </div>
                            @if($activity->createdBy)
                            <small class="text-muted f-12">
                                <i class="ti ti-user me-1"></i>By: {{ $activity->createdBy->name }}
                            </small>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary" id="updateStatusBtn">Update Status</button>
    </div>
</form>

<script>
    $(document).ready(function() {
        let formCompleted = false;

        // Debug: Check if elements exist
        console.log('Followup section exists:', $('#followupDateSection').length > 0);
        console.log('Followup input exists:', $('#followup_date').length > 0);
        console.log('Status select exists:', $('#lead_status_id').length > 0);

        const followupStatuses = @json(array_map('strval', $followupStatusIds));

        const followupInput = $('#followup_date');
        const followupDateSection = $('#followupDateSection');
        const demoBookingSection = $('#demoBookingSection');
        const updateStatusBtn = $('#updateStatusBtn');

        const todayStr = new Date().toISOString().split('T')[0];

        function updateFollowupUI(selectedStatus) {
            const demoBookingSection = $('#demoBookingSection');
            const followupDateSection = $('#followupDateSection');
            const followupInput = $('#followup_date');
            const updateStatusBtn = $('#updateStatusBtn');

            console.log('Status changed to:', selectedStatus);
            console.log('Followup section element:', followupDateSection);
            console.log('Followup section length:', followupDateSection.length);

            if (selectedStatus == '6') {
                // Show demo booking section
                demoBookingSection.show();
                followupDateSection.hide();
                // Disable update button until form is completed
                updateStatusBtn.prop('disabled', true);
                followupInput.prop('required', false); // remove required
                updateStatusBtn.html('Complete Demo Booking First');
                formCompleted = false;
            } else if (followupStatuses.includes(selectedStatus)) {
                // Show followup date section
                console.log('Showing followup section for followup-required status');
                followupDateSection.css('display', 'block');
                followupDateSection.show();
                console.log('Followup section visible:', followupDateSection.is(':visible'));
                console.log('Followup section display style:', followupDateSection.css('display'));
                demoBookingSection.hide();
                followupInput.prop('disabled', false);
                followupInput.prop('required', true);
                followupInput.attr('required', 'required');
                followupInput.attr('min', todayStr);

                const savedFollowupDate = followupInput.data('saved-date') || followupInput.attr('value') || '';
                if (!savedFollowupDate || savedFollowupDate < todayStr) {
                    followupInput.val(todayStr);
                    if (savedFollowupDate && savedFollowupDate < todayStr) {
                        const parts = savedFollowupDate.split('-');
                        const savedDisplay = parts.length === 3
                            ? `${parts[2]}-${parts[1]}-${parts[0]}`
                            : savedFollowupDate;
                        $('#followupDateHint')
                            .text(`Previous follow-up was ${savedDisplay}. Confirm the new date and click Update Status to save.`)
                            .show();
                    } else {
                        $('#followupDateHint').hide();
                    }
                } else {
                    followupInput.val(savedFollowupDate);
                    $('#followupDateHint').hide();
                }
                // Enable update button
                updateStatusBtn.prop('disabled', false);
                updateStatusBtn.html('Update Status');
                formCompleted = true;
            } else {
                // Hide both sections
                demoBookingSection.hide();
                followupDateSection.hide();
                // Enable update button
                followupInput.prop('required', false); // remove required
                followupInput.removeAttr('required'); // ensure required attribute is removed
                followupInput.prop('disabled', true);
                followupInput.removeAttr('min');
                followupInput.val('');
                $('#followupDateHint').hide();
                updateStatusBtn.prop('disabled', false);
                updateStatusBtn.html('Update Status');
                formCompleted = true;
            }
        }

        // Initialize form state - ensure followup_date is not required initially
        followupInput.prop('required', false).prop('disabled', true).removeAttr('min');

        // Handle status selection change
        $('#lead_status_id').on('change', function() {
            const selectedStatus = $(this).val();
            updateFollowupUI(selectedStatus);
        });

        // Handle demo booking form button click
        $('#demoBookingBtn').on('click', function() {
            formCompleted = true;
            const updateStatusBtn = $('#updateStatusBtn');

            // Enable update button
            updateStatusBtn.prop('disabled', false);
            updateStatusBtn.html('Update Status');

            // Show a brief success message
            if (typeof toast_success === 'function') {
                toast_success('Demo conduction form opened. You can now update the status.');
            }
        });

        // Handle form submission
        $('#statusChangeForm').on('submit', function(e) {
            e.preventDefault();

            // Check if status 6 is selected and form is not completed
            const selectedStatus = $('#lead_status_id').val();
            if (selectedStatus == '6' && !formCompleted) {
                if (typeof toast_error === 'function') {
                    toast_error('Please click the "Complete Demo Conduction Form" button before updating the status.');
                } else {
                    toast_error('Please click the "Complete Demo Conduction Form" button before updating the status.');
                }
                return;
            }

            // Check if status 2 is selected and followup date is not provided
            if (followupStatuses.includes(selectedStatus)) {
                const followupDate = $('#followup_date').val();
                if (!followupDate) {
                    if (typeof toast_error === 'function') {
                        toast_error('Please select a followup date for this status.');
                    } else {
                        alert('Please select a followup date for this status.');
                    }
                    return;
                }
            }

            // Check if reason and remarks are provided
            const reason = $('#reason').val().trim();
            const remarks = $('#remarks').val().trim();

            if (!reason) {
                if (typeof toast_error === 'function') {
                    toast_error('Please enter a reason for the status change.');
                } else {
                    alert('Please enter a reason for the status change.');
                }
                return;
            }

            if (!remarks) {
                if (typeof toast_error === 'function') {
                    toast_error('Please enter remarks for the status change.');
                } else {
                    alert('Please enter remarks for the status change.');
                }
                return;
            }

            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();

            submitBtn.prop('disabled', true);
            submitBtn.html('<i class="ti ti-loader-2"></i> Updating...');

            $.ajax({
                url: '{{ route("leads.status-update-submit", $lead->id) }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        if (typeof toast_success === 'function') {
                            toast_success(response.message);
                        } else {
                            alert(response.message);
                        }

                        // Close modal
                        $('#ajax_modal').modal('hide');

                        if ($.fn.DataTable.isDataTable('#followupLeadsAjaxTable')) {
                            $('#followupLeadsAjaxTable').DataTable().ajax.reload(null, false);
                        } else {
                            location.reload();
                        }
                    } else {
                        // Show error message
                        if (typeof toast_error === 'function') {
                            toast_error(response.message);
                        } else {
                            alert(response.message);
                        }
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred while updating the status.';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMessage = errors.join(', ');
                    }

                    if (typeof toast_error === 'function') {
                        toast_error(errorMessage);
                    } else {
                        alert(errorMessage);
                    }
                },
                complete: function() {
                    submitBtn.prop('disabled', false);
                    submitBtn.html(originalText);
                }
            });
        });

        // Initialize form state on page load
        updateFollowupUI($('#lead_status_id').val());
        // Rating input validation
        $('#rating').on('input', function() {
            let value = parseInt($(this).val(), 10);

            if (value > 10) {
                $(this).val(10);
                if (typeof toast_error === 'function') {
                    toast_error('Rating cannot be more than 10.');
                }
            } else if (value < 1 && value !== '') {
                $(this).val(1);
                if (typeof toast_error === 'function') {
                    toast_error('Rating cannot be less than 1.');
                }
            }
        });

        // Debug function - can be called from browser console
        window.testFollowupSection = function() {
            console.log('Testing followup section...');
            const section = $('#followupDateSection');
            section.css('display', 'block');
            section.show();
            console.log('Followup section should now be visible');
            console.log('Section display style:', section.css('display'));
            console.log('Section is visible:', section.is(':visible'));
        };
    });
</script>