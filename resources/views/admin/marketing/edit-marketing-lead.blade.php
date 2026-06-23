<form id="editMarketingLeadForm" action="{{ route('admin.marketing.marketing-leads.update', $marketingLead->id) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="row">
        <!-- BDE Name - Only show if user is not marketing -->
        @if(!$isMarketing)
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="bde_id" class="form-label">BDE Name <span class="text-danger">*</span></label>
                <select class="form-select" name="bde_id" id="bde_id" required>
                    <option value="">Select BDE</option>
                    @foreach($marketingUsers as $user)
                        <option value="{{ $user->id }}" {{ $marketingLead->marketing_bde_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif

        <!-- Date Of Visit -->
        <div class="{{ $isMarketing ? 'col-md-6' : 'col-md-6' }}">
            <div class="form-group mb-3">
                <label for="date_of_visit" class="form-label">Date Of Visit <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="date_of_visit" name="date_of_visit" value="{{ $marketingLead->date_of_visit ? $marketingLead->date_of_visit->format('Y-m-d') : '' }}" required />
            </div>
        </div>

        <!-- Location / Area Covered -->
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="location" class="form-label">Location / Area Covered <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="location" name="location" placeholder="Enter Location / Area Covered" value="{{ $marketingLead->location }}" required />
            </div>
        </div>

        <!-- House Number -->
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="house_number" class="form-label">House Number</label>
                <input type="text" class="form-control" id="house_number" name="house_number" placeholder="Enter House Number" value="{{ $marketingLead->house_number }}" />
            </div>
        </div>

        <!-- Lead Name -->
        <div class="col-md-12">
            <div class="form-group mb-3">
                <label for="lead_name" class="form-label">Lead Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="lead_name" name="lead_name" placeholder="Enter Lead Name" value="{{ $marketingLead->lead_name }}" required />
            </div>
        </div>

        <!-- Code and Phone -->
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label for="code" class="form-label">Country Code <span class="text-danger">*</span></label>
                <select class="form-select" id="code" name="code" required>
                    <option value="">Select Country</option>
                    @foreach($country_codes as $code => $country)
                        <option value="{{ $code }}" {{ $marketingLead->code == $code ? 'selected' : '' }}>{{ $code }} - {{ $country }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-8">
            <div class="form-group mb-3">
                <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                <input type="number" name="phone" class="form-control" id="phone" placeholder="Enter Phone" value="{{ $marketingLead->phone }}" required maxlength="15" />
            </div>
        </div>

        <!-- Whatsapp Code and Number -->
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label for="whatsapp_code" class="form-label">WhatsApp Country Code</label>
                <select class="form-select" id="whatsapp_code" name="whatsapp_code">
                    <option value="">Select Country</option>
                    @foreach($country_codes as $code => $country)
                        <option value="{{ $code }}" {{ $marketingLead->whatsapp_code == $code ? 'selected' : '' }}>{{ $code }} - {{ $country }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-8">
            <div class="form-group mb-3">
                <label for="whatsapp" class="form-label">WhatsApp Number</label>
                <input type="number" name="whatsapp" class="form-control" id="whatsapp" placeholder="Enter WhatsApp Number" value="{{ $marketingLead->whatsapp }}" maxlength="15" />
            </div>
        </div>

        <!-- Address -->
        <div class="col-md-12">
            <div class="form-group mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" name="address" id="address" placeholder="Enter Address" value="{{ $marketingLead->address }}" />
            </div>
        </div>

        <!-- Lead Type -->
        <div class="col-md-12">
            <div class="form-group mb-3">
                <label for="lead_type" class="form-label">Lead Type <span class="text-danger">*</span></label>
                <select class="form-select" name="lead_type" id="lead_type" required>
                    <option value="">Select Lead Type</option>
                    <option value="Student" {{ $marketingLead->lead_type == 'Student' ? 'selected' : '' }}>Student</option>
                    <option value="Parent" {{ $marketingLead->lead_type == 'Parent' ? 'selected' : '' }}>Parent</option>
                    <option value="Working Professional" {{ $marketingLead->lead_type == 'Working Professional' ? 'selected' : '' }}>Working Professional</option>
                    <option value="Institution Representative" {{ $marketingLead->lead_type == 'Institution Representative' ? 'selected' : '' }}>Institution Representative</option>
                    <option value="Others" {{ $marketingLead->lead_type == 'Others' ? 'selected' : '' }}>Others</option>
                </select>
            </div>
        </div>

        <!-- Interested Courses -->
        <div class="col-md-12">
            <div class="form-group mb-3">
                <label class="form-label">Interested Courses</label>
                <div class="row">
                    @php
                        $interestedCourses = $marketingLead->interested_courses ?? [];
                    @endphp
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="interested_courses[]" value="SSLC" id="course_sslc" {{ in_array('SSLC', $interestedCourses) ? 'checked' : '' }}>
                            <label class="form-check-label" for="course_sslc">SSLC</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="interested_courses[]" value="Plus one Plus Two" id="course_plus" {{ in_array('Plus one Plus Two', $interestedCourses) ? 'checked' : '' }}>
                            <label class="form-check-label" for="course_plus">Plus one Plus Two</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="interested_courses[]" value="Degree" id="course_degree" {{ in_array('Degree', $interestedCourses) ? 'checked' : '' }}>
                            <label class="form-check-label" for="course_degree">Degree</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="interested_courses[]" value="Ai Python" id="course_ai_python" {{ in_array('Ai Python', $interestedCourses) ? 'checked' : '' }}>
                            <label class="form-check-label" for="course_ai_python">Ai Python</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="interested_courses[]" value="AI Integrated Digital Marketing" id="course_digital_marketing" {{ in_array('AI Integrated Digital Marketing', $interestedCourses) ? 'checked' : '' }}>
                            <label class="form-check-label" for="course_digital_marketing">AI Integrated Digital Marketing</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="interested_courses[]" value="Diploma in Graphic Designing" id="course_graphic_designing" {{ in_array('Diploma in Graphic Designing', $interestedCourses) ? 'checked' : '' }}>
                            <label class="form-check-label" for="course_graphic_designing">Diploma in Graphic Designing</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="interested_courses[]" value="Certificate Course in Medical Coding" id="course_medical_coding" {{ in_array('Certificate Course in Medical Coding', $interestedCourses) ? 'checked' : '' }}>
                            <label class="form-check-label" for="course_medical_coding">Certificate Course in Medical Coding</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="interested_courses[]" value="Diploma in Hospital Administration" id="course_hospital_admin" {{ in_array('Diploma in Hospital Administration', $interestedCourses) ? 'checked' : '' }}>
                            <label class="form-check-label" for="course_hospital_admin">Diploma in Hospital Administration</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="interested_courses[]" value="Hotel Management" id="course_hotel_management" {{ in_array('Hotel Management', $interestedCourses) ? 'checked' : '' }}>
                            <label class="form-check-label" for="course_hotel_management">Hotel Management</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Remarks / Notes -->
        <div class="col-md-12">
            <div class="form-group mb-3">
                <label for="remarks" class="form-label">Remarks / Notes</label>
                <textarea class="form-control" name="remarks" id="remarks" placeholder="Enter Remarks / Notes" rows="3">{{ $marketingLead->remarks }}</textarea>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="col-12">
            <div class="form-group text-end">
                <button class="btn btn-primary" type="submit">
                    <i class="ti ti-device-floppy"></i> Update
                </button>
                <button type="button" class="btn btn-secondary ms-2" data-bs-dismiss="modal">
                    <i class="ti ti-x"></i> Cancel
                </button>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {
    $('#editMarketingLeadForm').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var formData = form.serialize();
        var url = form.attr('action');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#ajax_modal').modal('hide');
                    // Pass success message as URL parameter
                    var redirectUrl = response.redirect;
                    if (response.message) {
                        var separator = redirectUrl.includes('?') ? '&' : '?';
                        redirectUrl += separator + 'success=' + encodeURIComponent(response.message);
                    }
                    window.location.href = redirectUrl;
                } else {
                    alert_modal_error(response.error || 'Something went wrong!');
                }
            },
            error: function(xhr) {
                var errorMessage = 'Something went wrong!';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                alert_modal_error(errorMessage);
            }
        });
    });
});
</script>

