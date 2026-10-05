<div class="container p-2">
    <form action="{{ route('leads.submit') }}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="title">Name</label>
                    <input type="text" name="title" class="form-control" id="title" placeholder="Enter Name" value="{{ old('title') }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label" for="code">Country Code <span class="text-danger">*</span></label>
                        <select class="form-select" id="code" name="code" required>
                            <option value="">Select Country</option>
                            @foreach($country_codes as $code => $country)
                                <option value="{{ $code }}" {{ old('code') == $code ? 'selected' : '' }}>{{ $code }} - {{ $country }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label" for="phone">Phone <span class="text-danger">*</span></label>
                        <input type="number" name="phone" class="form-control" id="phone" placeholder="Enter Phone" value="{{ old('phone') }}" required maxlength="15" />
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="gender">Gender</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="gender" id="gender-male" value="male" {{ old('gender') == 'male' ? 'checked' : '' }}>
                            <label class="form-check-label" for="gender-male">Male</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="gender" id="gender-female" value="female" {{ old('gender') == 'female' ? 'checked' : '' }}>
                            <label class="form-check-label" for="gender-female">Female</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="age">Age</label>
                    <input type="number" class="form-control" id="age" name="age" placeholder="Enter Age" value="{{ old('age') }}" max="999" />
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="place">Place</label>
                    <input type="text" class="form-control" id="place" name="place" placeholder="Enter Place" value="{{ old('place') }}" />
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label" for="whatsapp_code">WhatsApp Code</label>
                        <select class="form-select" id="whatsapp_code" name="whatsapp_code">
                            <option value="">Select Country</option>
                            @foreach($country_codes as $code => $country)
                                <option value="{{ $code }}" {{ old('whatsapp_code') == $code ? 'selected' : '' }}>{{ $code }} - {{ $country }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label" for="whatsapp">WhatsApp Number</label>
                        <input type="number" name="whatsapp" class="form-control" id="whatsapp" placeholder="Enter WhatsApp Number" value="{{ old('whatsapp') }}" maxlength="15" />
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="email">Email ID</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email" value="{{ old('email') }}" />
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="qualification">Qualification</label>
                    <input type="text" class="form-control" id="qualification" name="qualification" placeholder="Enter Qualification" value="{{ old('qualification') }}" />
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="lead_status_id">Lead Status <span class="text-danger">*</span></label>
                    <select class="form-select" name="lead_status_id" id="lead_status_id" required>
                        <option value="">Select Lead Status</option>
                        @foreach($leadStatuses as $status)
                            <option value="{{ $status->id }}" {{ old('lead_status_id', 1) == $status->id ? 'selected' : '' }}>{{ $status->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="lead_source_id">Lead Source <span class="text-danger">*</span></label>
                    <select class="form-select" name="lead_source_id" id="lead_source_id" required>
                        <option value="">Select Source</option>
                        @foreach($leadSources as $source)
                            <option value="{{ $source->id }}" {{ old('lead_source_id') == $source->id ? 'selected' : '' }}>{{ $source->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="country_id">Country</label>
                    <select class="form-select" name="country_id" id="country_id">
                        <option value="">Select Country</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>{{ $country->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="course_id">Course Interested <span class="text-danger">*</span></label>
                    <select class="form-select" name="course_id" id="course_id" required>
                        <option value="">Select Course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_old" id="is_old" value="1" {{ old('is_old') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_old">
                            Is Old
                        </label>
                        <small class="form-text text-muted d-block">Mark as an old student. Addon course options will appear on the registration form.</small>
                    </div>
                </div>
            </div>

            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin())
            <div class="col-md-12">
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_b2b" id="is_b2b" value="1" {{ old('is_b2b') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_b2b">
                            <i class="ti ti-building me-1"></i>B2B Lead
                        </label>
                        <small class="form-text text-muted d-block">Mark this as a business-to-business lead. This will filter teams and telecallers to show only B2B options.</small>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_postsale" id="is_postsale" value="1" {{ old('is_postsale') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_postsale">
                            <i class="ti ti-headset me-1"></i>Post-sale Lead
                        </label>
                        <small class="form-text text-muted d-block">Only post-sale telecallers, post-sale team leads, and post-sale GMs can see this lead.</small>
                    </div>
                </div>
            </div>
            @elseif(\App\Helpers\PostSaleLeadHelper::seesOnlyPostSaleRecords())
            <div class="col-md-12">
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_postsale_locked" value="1" checked disabled>
                        <input type="hidden" name="is_postsale" value="1">
                        <label class="form-check-label" for="is_postsale_locked">
                            <i class="ti ti-headset me-1"></i>Post-sale Lead
                        </label>
                        <small class="form-text text-muted d-block">New leads are saved as post-sale.</small>
                    </div>
                </div>
            </div>
            @endif

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="team_id_add">Team <span class="text-danger">*</span></label>
                    <select class="form-select" name="team_id" id="team_id_add" required>
                        <option value="">Select Team</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}" data-is-b2b="{{ $team->is_b2b ? '1' : '0' }}" {{ old('team_id') == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="telecaller_id_add">Telecaller <span class="text-danger">*</span></label>
                    <select class="form-select" name="telecaller_id" id="telecaller_id_add" required>
                        <option value="">Select Telecaller</option> 
                    </select>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="address">Address</label>
                    <input type="text" class="form-control" name="address" id="address" placeholder="Enter Address" value="{{ old('address') }}" />
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="add_date">Date</label>
                    <input type="date" class="form-control" id="add_date" name="add_date" value="" />
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="add_time">Add Time</label>
                    <input type="time" class="form-control" id="add_time" name="add_time" value="" />
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="remarks">Remarks</label>
                    <textarea class="form-control" name="remarks" id="remarks" placeholder="Enter Remarks" rows="3">{{ old('remarks') }}</textarea>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success float-end">Submit</button>
    </form>
</div>

<script>
(function() {
    // Immediate execution for AJAX loaded content
    
    // Store all team options from the unique select ID
    // We use a specific ID selector to ensure we target the correct element in this modal
    const $teamSelect = $('#team_id_add');
    const $checkbox = $('#is_b2b_add');
    const $telecallerSelect = $('#telecaller_id_add');

    // If elements aren't found (e.g. issues with rendering), stop.
    if ($teamSelect.length === 0) return;

    const allTeamOptions = $teamSelect.find('option').clone();
    
    // Function to filter teams
    function filterTeams() {
        const isB2BChecked = $checkbox.is(':checked');
        const currentSelectedValue = $teamSelect.val();
        
        // Clear current options except the placeholder
        $teamSelect.find('option:not(:first)').remove();
        
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
                    $teamSelect.append(option.clone());
                }
            } else {
                if (!teamIsB2B) {
                    $teamSelect.append(option.clone());
                }
            }
        });
        
        // Restore selection if still available and valid for the new list, otherwise clear
        if (currentSelectedValue && $teamSelect.find('option[value="' + currentSelectedValue + '"]').length > 0) {
            $teamSelect.val(currentSelectedValue);
        } else {
            $teamSelect.val('');
            // Clear telecaller since team was cleared/changed context
            $telecallerSelect.html('<option value="">Select Team First</option>');
        }
    }

    // Bind change event to checkbox
    $checkbox.off('change.addModal').on('change.addModal', function() {
        filterTeams();
    });

    // Handle team selection change to update telecallers
    $teamSelect.off('change.addModal').on('change.addModal', function() {
        const teamId = $(this).val();
        const isB2BChecked = $checkbox.length > 0 ? $checkbox.is(':checked') : false;
        
        // Clear existing options
        $telecallerSelect.html('<option value="">Loading telecallers...</option>');
        
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
                    $telecallerSelect.html('<option value="">Select Telecaller</option>');
                    
                    if (response.telecallers && response.telecallers.length > 0) {
                        $.each(response.telecallers, function(index, telecaller) {
                            $telecallerSelect.append(
                                '<option value="' + telecaller.id + '">' + telecaller.name + '</option>'
                            );
                        });
                    } else {
                        $telecallerSelect.append('<option value="">No telecallers found in this team</option>');
                    }
                },
                error: function() {
                    $telecallerSelect.html('<option value="">Error loading telecallers</option>');
                }
            });
        } else {
            $telecallerSelect.html('<option value="">Select Team First</option>');
        }
    });

    // Initialize logic
    if ($checkbox.length > 0) {
        // Trigger initial filtering
        filterTeams();
    }
    
    // If there's an old team_id value (e.g. from validation error), trigger the change event
    @if(old('team_id'))
        $teamSelect.trigger('change');
    @endif

})();
</script>
