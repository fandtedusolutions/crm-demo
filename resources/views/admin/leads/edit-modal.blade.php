<div class="container p-2" data-is-telecaller="{{ $isTelecaller ? 'true' : 'false' }}">
    <form id="leadEditForm" action="{{ route('leads.update', $lead->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="title">Name</label>
                    <input type="text" name="title" class="form-control" id="title" placeholder="Enter Name" value="{{ old('title', $lead->title) }}">
                    <div class="invalid-feedback" id="title-error"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label" for="code">Country Code <span class="text-danger">*</span></label>
                        <select class="form-select" id="code" name="code" required>
                            <option value="">Select Country</option>
                            @foreach($country_codes as $code => $country)
                                <option value="{{ $code }}" {{ old('code', $lead->code) == $code ? 'selected' : '' }}>{{ $code }} - {{ $country }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" id="code-error"></div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label" for="phone">Phone <span class="text-danger">*</span></label>
                        <input type="number" name="phone" class="form-control" id="phone" placeholder="Enter Phone" value="{{ old('phone', $lead->phone) }}" required maxlength="15" />
                        <div class="invalid-feedback" id="phone-error"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="gender">Gender</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="gender" id="gender-male" value="male" {{ old('gender', $lead->gender) == 'male' ? 'checked' : '' }}>
                            <label class="form-check-label" for="gender-male">Male</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="gender" id="gender-female" value="female" {{ old('gender', $lead->gender) == 'female' ? 'checked' : '' }}>
                            <label class="form-check-label" for="gender-female">Female</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="age">Age</label>
                    <input type="number" class="form-control" id="age" name="age" placeholder="Enter Age" value="{{ old('age', $lead->age) }}" max="999" />
                    <div class="invalid-feedback" id="age-error"></div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="place">Place</label>
                    <input type="text" class="form-control" id="place" name="place" placeholder="Enter Place" value="{{ old('place', $lead->place) }}" />
                    <div class="invalid-feedback" id="place-error"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label" for="whatsapp_code">WhatsApp Code</label>
                        <select class="form-select" id="whatsapp_code" name="whatsapp_code">
                            <option value="">Select Country</option>
                            @foreach($country_codes as $code => $country)
                                <option value="{{ $code }}" {{ old('whatsapp_code', $lead->whatsapp_code) == $code ? 'selected' : '' }}>{{ $code }} - {{ $country }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" id="whatsapp_code-error"></div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label" for="whatsapp">WhatsApp Number</label>
                        <input type="number" name="whatsapp" class="form-control" id="whatsapp" placeholder="Enter WhatsApp Number" value="{{ old('whatsapp', $lead->whatsapp) }}" maxlength="15" />
                        <div class="invalid-feedback" id="whatsapp-error"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="email">Email ID</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email" value="{{ old('email', $lead->email) }}" />
                    <div class="invalid-feedback" id="email-error"></div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="qualification">Qualification</label>
                    <input type="text" class="form-control" id="qualification" name="qualification" placeholder="Enter Qualification" value="{{ old('qualification', $lead->qualification) }}" />
                    <div class="invalid-feedback" id="qualification-error"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="lead_status_id">Lead Status <span class="text-danger">*</span></label>
                    <select class="form-select" name="lead_status_id" id="lead_status_id" required>
                        <option value="">Select Lead Status</option>
                        @foreach($leadStatuses as $status)
                            <option value="{{ $status->id }}" {{ old('lead_status_id', $lead->lead_status_id) == $status->id ? 'selected' : '' }}>{{ $status->title }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" id="lead_status_id-error"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="lead_source_id">Lead Source <span class="text-danger">*</span></label>
                    <select class="form-select" name="lead_source_id" id="lead_source_id" required>
                        <option value="">Select Source</option>
                        @foreach($leadSources as $source)
                            <option value="{{ $source->id }}" {{ old('lead_source_id', $lead->lead_source_id) == $source->id ? 'selected' : '' }}>{{ $source->title }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" id="lead_source_id-error"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="country_id">Country</label>
                    <select class="form-select" name="country_id" id="country_id">
                        <option value="">Select Country</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" {{ old('country_id', $lead->country_id) == $country->id ? 'selected' : '' }}>{{ $country->title }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" id="country_id-error"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="course_id">Course Interested <span class="text-danger">*</span></label>
                    <select class="form-select" name="course_id" id="course_id" required>
                        <option value="">Select Course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id', $lead->course_id) == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" id="course_id-error"></div>
                </div>
            </div>

            @if(!$isTelecaller)
            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin())
            <div class="col-md-12">
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_b2b" id="is_b2b_edit_modal" value="1" {{ old('is_b2b', $lead->is_b2b) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_b2b_edit_modal">
                            <i class="ti ti-building me-1"></i>B2B Lead
                        </label>
                        <small class="form-text text-muted d-block">Mark this as a business-to-business lead. This will filter teams and telecallers to show only B2B options.</small>
                    </div>
                </div>
            </div>
            @endif

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="team_id">Team <span class="text-danger">*</span></label>
                    <select class="form-select" name="team_id" id="team_id" required>
                        <option value="">Select Team</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}" data-is-b2b="{{ $team->is_b2b ? '1' : '0' }}" {{ old('team_id', $lead->team_id) == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" id="team_id-error"></div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="telecaller_id">Telecaller <span class="text-danger">*</span></label>
                    <select class="form-select" name="telecaller_id" id="telecaller_id" required>
                        <option value="">Select Telecaller</option>
                        @foreach($telecallers as $telecaller)
                            <option value="{{ $telecaller->id }}" {{ old('telecaller_id', $lead->telecaller_id) == $telecaller->id ? 'selected' : '' }}>{{ $telecaller->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" id="telecaller_id-error"></div>
                </div>
            </div>
            @else
            {{-- For telecallers, show current assignment as read-only --}}
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Current Team</label>
                    <input type="text" class="form-control" value="{{ $lead->team ? $lead->team->name : 'No Team Assigned' }}" readonly>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Current Telecaller</label>
                    <input type="text" class="form-control" value="{{ $lead->telecaller ? $lead->telecaller->name : 'No Telecaller Assigned' }}" readonly>
                </div>
            </div>
            @endif

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="address">Address</label>
                    <input type="text" class="form-control" name="address" id="address" placeholder="Enter Address" value="{{ old('address', $lead->address) }}" />
                    <div class="invalid-feedback" id="address-error"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="add_date">Date</label>
                    <input type="date" class="form-control" id="add_date" name="add_date" value="{{ old('add_date', $lead->created_at ? $lead->created_at->format('Y-m-d') : '') }}" />
                    <div class="invalid-feedback" id="add_date-error"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="add_time">Add Time</label>
                    <input type="time" class="form-control" id="add_time" name="add_time" value="{{ old('add_time', $lead->created_at ? $lead->created_at->format('H:i') : '') }}" />
                    <div class="invalid-feedback" id="add_time-error"></div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="remarks">Remarks</label>
                    <textarea class="form-control" name="remarks" id="remarks" placeholder="Enter Remarks" rows="3">{{ old('remarks', $lead->remarks) }}</textarea>
                    <div class="invalid-feedback" id="remarks-error"></div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success float-end">Update Lead</button>
    </form>
</div>

<script>
(function() {
    var isTelecaller = $('.container').data('is-telecaller') === 'true';
    
    if (!isTelecaller) {
        const allTeamOptions = $('#team_id option').clone();
        
        $('#is_b2b_edit_modal').off('change.leadEdit').on('change.leadEdit', function() {
            const isB2BChecked = $(this).is(':checked');
            const currentSelectedValue = $('#team_id').val();
            
            // Clear current options except the placeholder
            $('#team_id').find('option:not(:first)').remove();
            
            // Filter and add appropriate options
            allTeamOptions.each(function() {
                const option = $(this);
                if (option.val() === '') {
                    // Skip placeholder (already exists)
                    return;
                }
                
                const teamIsB2B = option.attr('data-is-b2b') === '1';
                
                // Strict filtering:
                // 1. is_b2b is checked: Team MUST be B2B
                // 2. is_b2b is NOT checked: Team MUST NOT be B2B
                if (isB2BChecked) {
                    if (teamIsB2B) {
                        $('#team_id').append(option.clone());
                    }
                } else {
                    if (!teamIsB2B) {
                        $('#team_id').append(option.clone());
                    }
                }
            });
            
            // Restore selection if still available, otherwise clear
            if ($('#team_id option[value="' + currentSelectedValue + '"]').length > 0) {
                $('#team_id').val(currentSelectedValue);
            } else {
                $('#team_id').val('');
                // Clear telecaller since team was cleared
                $('#telecaller_id').html('<option value="">Select Team First</option>');
            }
            
            // If team is selected, reload telecallers with B2B filter
            if ($('#team_id').val()) {
                $('#team_id').trigger('change');
            }
        });

        $('#is_b2b_edit_modal').trigger('change');
        
        $('#team_id').off('change.leadEdit').on('change.leadEdit', function() {
            const teamId = $(this).val();
            const telecallerSelect = $('#telecaller_id');
            const isB2BChecked = $('#is_b2b_edit_modal').is(':checked');
            
            // Clear existing options
            telecallerSelect.html('<option value="">Loading telecallers...</option>');
            
            if (teamId) {
                // Fetch telecallers for selected team
                $.ajax({
                    url: '{{ route("leads.telecallers-by-team") }}',
                    type: 'GET',
                    data: { 
                        team_id: teamId,
                        is_b2b: isB2BChecked ? 1 : 0
                    },
                    success: function(response) {
                        telecallerSelect.html('<option value="">Select Telecaller</option>');
                        
                        if (response.telecallers && response.telecallers.length > 0) {
                            $.each(response.telecallers, function(index, telecaller) {
                                telecallerSelect.append(
                                    '<option value="' + telecaller.id + '">' + telecaller.name + '</option>'
                                );
                            });
                        } else {
                            telecallerSelect.append('<option value="">No telecallers found in this team</option>');
                        }
                    },
                    error: function() {
                        telecallerSelect.html('<option value="">Error loading telecallers</option>');
                    }
                });
            } else {
                telecallerSelect.html('<option value="">Select Team First</option>');
            }
        });
        
        // If there's a current team_id value, trigger the change event to load telecallers
        var currentTeamId = '{{ $lead->team_id }}';
        var currentTelecallerId = '{{ $lead->telecaller_id }}';
        
        if (currentTeamId) {
            $('#team_id').trigger('change');
            // Set the current telecaller as selected after loading
            setTimeout(function() {
                $('#telecaller_id').val(currentTelecallerId);
            }, 500);
        }
    }

    $('#leadEditForm').off('submit.leadEdit').on('submit.leadEdit', function(e) {
        e.preventDefault();

        if ($(this).data('submitting')) {
            return false;
        }
        
        $('.form-control, .form-select').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        const form = $(this);
        const formData = new FormData(this);

        form.data('submitting', true);
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="ti ti-loader-2"></i> Updating...');
        
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
                    toast_success(response.message);
                    $('#ajax_modal').modal('hide');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    form.data('submitting', false);
                    submitBtn.prop('disabled', false);
                    submitBtn.html(originalText);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(field, messages) {
                        var input = form.find('[name="' + field + '"]');
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(messages[0]);
                    });
                } else {
                    toast_danger('An error occurred while updating the lead. Please try again.');
                }
                
                form.data('submitting', false);
                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);
            }
        });
    });
})();
</script>
