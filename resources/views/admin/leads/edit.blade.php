@extends('layouts.mantis')

@section('title', 'Edit Lead')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Edit Lead</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('leads.index') }}">Leads</a></li>
                    <li class="breadcrumb-item">Edit</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Lead Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('leads.update', $lead->id) }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group mb-3">
                                <label for="title" class="form-label">Name</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" placeholder="Enter name" value="{{ old('title', $lead->title) }}" />
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                                <div class="row">
                                    <div class="col-4">
                                        <select class="form-select @error('code') is-invalid @enderror" name="code">
                                            <option value="">Select Code</option>
                                            @foreach($country_codes as $code => $country)
                                                <option value="{{ $code }}" {{ old('code', $lead->code) == $code ? 'selected' : '' }}>{{ $code }} - {{ $country }}</option>
                                            @endforeach
                                        </select>
                                        @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-8">
                                        <input type="number" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Enter phone no" value="{{ old('phone', $lead->phone) }}" required maxlength="15" />
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3">
                            <div class="form-group mb-3">
                                <label for="gender" class="form-label">Gender</label>
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
                        
                        <div class="col-lg-3">
                            <div class="form-group mb-3">
                                <label for="age" class="form-label">Age</label>
                                <input type="number" class="form-control @error('age') is-invalid @enderror" placeholder="Enter Age" id="age" name="age" value="{{ old('age', $lead->age) }}" max="999" />
                                @error('age')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-lg-12">
                            <div class="form-group mb-3">
                                <label for="place" class="form-label">Place</label>
                                <input type="text" class="form-control @error('place') is-invalid @enderror" placeholder="Enter Place" id="place" name="place" value="{{ old('place', $lead->place) }}" />
                                @error('place')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="whatsapp" class="form-label">Whatsapp number</label>
                                <div class="row">
                                    <div class="col-4">
                                        <select class="form-select" name="whatsapp_code">
                                            <option value="">Select Code</option>
                                            @foreach($country_codes as $code => $country)
                                                <option value="{{ $code }}" {{ old('whatsapp_code', $lead->whatsapp_code) == $code ? 'selected' : '' }}>{{ $code }} - {{ $country }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-8">
                                        <input type="number" name="whatsapp" id="whatsapp" class="form-control @error('whatsapp') is-invalid @enderror" placeholder="Enter Whatsapp number" value="{{ old('whatsapp', $lead->whatsapp) }}" maxlength="15" />
                                        @error('whatsapp')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-12">
                            <div class="form-group mb-3">
                                <label for="email" class="form-label">Email ID</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Enter email" value="{{ old('email', $lead->email) }}" />
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-lg-12">
                            <div class="form-group mb-3">
                                <label for="qualification" class="form-label">Qualification</label>
                                <input type="text" class="form-control @error('qualification') is-invalid @enderror" id="qualification" name="qualification" placeholder="Enter Qualification" value="{{ old('qualification', $lead->qualification) }}" />
                                @error('qualification')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="lead_status_id" class="form-label">Lead status</label>
                                <select class="form-select @error('lead_status_id') is-invalid @enderror" name="lead_status_id" id="lead_status_id">
                                    @foreach($leadStatuses as $status)
                                        <option value="{{ $status->id }}" {{ old('lead_status_id', $lead->lead_status_id, 1) == $status->id ? 'selected' : '' }}>{{ $status->title }}</option>
                                    @endforeach
                                </select>
                                @error('lead_status_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="lead_source_id" class="form-label">Lead Source</label>
                                <select class="form-select @error('lead_source_id') is-invalid @enderror" name="lead_source_id" id="lead_source_id">
                                    <option value="">Select Source</option>
                                    @foreach($leadSources as $source)
                                        <option value="{{ $source->id }}" {{ old('lead_source_id', $lead->lead_source_id) == $source->id ? 'selected' : '' }}>{{ $source->title }}</option>
                                    @endforeach
                                </select>
                                @error('lead_source_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="country_id" class="form-label">Country</label>
                                <select class="form-select @error('country_id') is-invalid @enderror" name="country_id" id="country_id">
                                    <option value="">Select Country</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}" {{ old('country_id', $lead->country_id) == $country->id ? 'selected' : '' }}>{{ $country->title }}</option>
                                    @endforeach
                                </select>
                                @error('country_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="course_id" class="form-label">Course Interested</label>
                                <select class="form-select @error('course_id') is-invalid @enderror" name="course_id" id="course_id">
                                    <option value="">Select Course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ old('course_id', $lead->course_id) == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_old" id="is_old" value="1" {{ old('is_old', $lead->is_old) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_old">
                                        Is Old
                                    </label>
                                    <small class="form-text text-muted d-block">Mark as an old student. Addon course options will appear on the registration form.</small>
                                </div>
                            </div>
                        </div>

                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin())
                        <div class="col-lg-12">
                            <div class="form-group mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_b2b" id="is_b2b" value="1" {{ old('is_b2b', $lead->is_b2b) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_b2b">
                                        <i class="ti ti-building me-1"></i>B2B Lead
                                    </label>
                                    <small class="form-text text-muted d-block">Mark this as a business-to-business lead. This will filter teams and telecallers to show only B2B options.</small>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="team_id" class="form-label">Team <span class="text-danger">*</span></label>
                                <select class="form-select @error('team_id') is-invalid @enderror" name="team_id" id="team_id" required>
                                    <option value="">Select Team</option>
                                    @foreach($teams as $team)
                                        <option value="{{ $team->id }}" data-is-b2b="{{ $team->is_b2b ? '1' : '0' }}" {{ old('team_id', $lead->team_id) == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                                    @endforeach
                                </select>
                                @error('team_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-lg-12">
                            <div class="form-group mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror" name="address" id="address" placeholder="Enter Address" value="{{ old('address', $lead->address) }}" />
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="telecaller_id" class="form-label">Telecaller <span class="text-danger">*</span></label>
                                <select class="form-select @error('telecaller_id') is-invalid @enderror" name="telecaller_id" id="telecaller_id" required>
                                    <option value="">Select Team First</option>
                                    @if($lead->telecaller_id)
                                        <option value="{{ $lead->telecaller_id }}" data-is-b2b="{{ $lead->telecaller->is_b2b ?? '0' }}" selected>{{ $lead->telecaller->name ?? 'Selected Telecaller' }}</option>
                                    @endif
                                </select>
                                @error('telecaller_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="followup_date" class="form-label">Follow Up Date</label>
                                <input type="date" class="form-control @error('followup_date') is-invalid @enderror" id="followup_date" name="followup_date" value="{{ old('followup_date', $lead->followup_date) }}" />
                                @error('followup_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="add_date" class="form-label">Add Date</label>
                                <input type="date" class="form-control @error('add_date') is-invalid @enderror" id="add_date" name="add_date" value="{{ old('add_date', $lead->add_date ?? date('Y-m-d')) }}" />
                                @error('add_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="add_time" class="form-label">Add Time</label>
                                <input type="time" class="form-control @error('add_time') is-invalid @enderror" id="add_time" name="add_time" value="{{ old('add_time', $lead->add_time ?? date('H:i')) }}" />
                                @error('add_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-lg-12">
                            <div class="form-group mb-3">
                                <label for="remarks" class="form-label">Remarks</label>
                                <textarea class="form-control @error('remarks') is-invalid @enderror" name="remarks" id="remarks" placeholder="Enter Remarks" rows="3">{{ old('remarks', $lead->remarks) }}</textarea>
                                @error('remarks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-group text-end">
                                <button class="btn btn-primary" type="submit">
                                    <i class="ti ti-device-floppy"></i> Update Lead
                                </button>
                                <a href="{{ route('leads.index') }}" class="btn btn-secondary ms-2">
                                    <i class="ti ti-x"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

@push('scripts')
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

    // Initialize B2B filter
    $('#is_b2b').trigger('change');
    
    // Handle team selection change
    $('#team_id').on('change', function() {
        const teamId = $(this).val();
        const telecallerSelect = $('#telecaller_id');
        const isB2BChecked = $('#is_b2b').is(':checked');
        
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
                            const selected = telecaller.id == {{ $lead->telecaller_id ?? 'null' }} ? 'selected' : '';
                            telecallerSelect.append(
                                '<option value="' + telecaller.id + '" ' + selected + '>' + telecaller.name + '</option>'
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
    
    // If there's a team_id value, trigger the change event
    @if($lead->team_id)
        $('#team_id').trigger('change');
    @endif
});
</script>
@endpush
@endsection
