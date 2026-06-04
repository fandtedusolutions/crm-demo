<div class="container p-2">
    <form action="{{ route('admin.telecallers.update', $edit_data->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" id="name" value="{{ $edit_data->name }}" placeholder="Enter Name" required>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" id="email" value="{{ $edit_data->email }}" placeholder="Enter Email" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label" for="code">Country Code <span class="text-danger">*</span></label>
                        <select class="form-select" id="code" name="code" required>
                            <option value="">Select Country</option>
                            @foreach($country_codes as $code => $country)
                                <option value="{{ $code }}" {{ $edit_data->code == $code ? 'selected' : '' }}>{{ $code }} - {{ $country }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label" for="phone">Phone <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" id="phone" value="{{ $edit_data->phone }}" placeholder="Enter Phone" required>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="joining_date">Joining Date <span class="text-danger">*</span></label>
                    <input type="date" name="joining_date" class="form-control" id="joining_date" value="{{ $edit_data->joining_date ? $edit_data->joining_date->format('Y-m-d') : '' }}" required>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="team_id">Team</label>
                    <select class="form-select" id="team_id" name="team_id">
                        <option value="">Select Team</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}" data-is-b2b="{{ $team->is_b2b ? '1' : '0' }}" {{ $edit_data->team_id == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-12">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label" for="ext_no">Extension Number</label>
                        <input type="text" name="ext_no" class="form-control" id="ext_no" value="{{ $edit_data->ext_no }}" placeholder="Enter Extension Number">
                        <small class="form-text text-muted">Extension number for Voxbay calling system</small>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_team_lead" id="is_team_lead" value="1" {{ $edit_data->is_team_lead ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_team_lead">
                            <i class="ti ti-crown me-1"></i>Is Team Lead
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_senior_manager" id="is_senior_manager" value="1" {{ $edit_data->is_senior_manager ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_senior_manager">
                            <i class="ti ti-user-star me-1"></i>Is Senior Manager
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_b2b" id="is_b2b" value="1" {{ $edit_data->is_b2b ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_b2b">
                            <i class="ti ti-building me-1"></i>B2B Telecaller
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success float-end">Update</button>
    </form>
</div>

<script>
$(document).ready(function() {
    // Store all team options
    const allTeamOptions = $('#team_id option').clone();
    
    // Filter teams based on is_b2b checkbox
    $('#is_b2b').on('change', function() {
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
            
            // Add option if:
            // 1. is_b2b is checked and team is B2B
            // 2. is_b2b is not checked (show all teams)
            if (!isB2BChecked || teamIsB2B) {
                $('#team_id').append(option.clone());
            }
        });
        
        // Restore selection if still available
        if ($('#team_id option[value="' + currentSelectedValue + '"]').length > 0) {
            $('#team_id').val(currentSelectedValue);
        } else {
            $('#team_id').val('');
        }
    });
});
</script>
