@extends('layouts.mantis')

@section('title', 'D2D SKILL PARK - Marketing Form')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">D2D SKILL PARK</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.marketing.index') }}">Marketing</a></li>
                    <li class="breadcrumb-item">D2D Form</li>
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
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">D2D SKILL PARK</h5>
                    <a href="{{ route('admin.marketing.marketing-leads') }}" class="btn btn-secondary btn-sm">
                        <i class="ti ti-arrow-left"></i> Back to Marketing Leads
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <p class="text-muted">
                        A door-to-door marketing campaign for Skill Park is a community-based outreach initiative designed to promote the institute's training programs and skill development courses directly to potential students and families. In this campaign, Skill Park representatives visit homes, schools, and local areas to create awareness about various courses, government-certified programs, and career opportunities offered by the institution. The team explains the benefits of skill-based education, distributes brochures, collects lead information, and answers queries face-to-face. This personal interaction helps build trust within the community and ensures that even those with limited digital access learn about Skill Park's offerings. The campaign aims to increase enrollments, strengthen community relationships, and spread the message of empowering youth through practical skills and career-oriented training.
                    </p>
                </div>

                <form action="{{ route('admin.marketing.d2d-submit') }}" method="post" data-form-action="{{ route('admin.marketing.d2d-submit') }}" data-redirect-url="{{ route('admin.marketing.d2d-form') }}">
                    @csrf
                    <div class="row">
                        <!-- BDE Name - Only show if user is not marketing -->
                        @if(!$isMarketing)
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="bde_id" class="form-label">BDE Name <span class="text-danger">*</span></label>
                                <select class="form-select @error('bde_id') is-invalid @enderror" name="bde_id" id="bde_id" required>
                                    <option value="">Select BDE</option>
                                    @foreach($marketingUsers as $user)
                                        <option value="{{ $user->id }}" {{ old('bde_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('bde_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @else
                        <!-- Hidden field to store marketing user ID -->
                        <input type="hidden" name="marketing_bde_id" value="{{ $currentUser->id }}">
                        @endif

                        <!-- Date Of Visit -->
                        <div class="{{ $isMarketing ? 'col-md-12' : 'col-md-6' }}">
                            <div class="form-group mb-3">
                                <label for="date_of_visit" class="form-label">Date Of Visit <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date_of_visit') is-invalid @enderror" id="date_of_visit" name="date_of_visit" value="{{ old('date_of_visit', date('Y-m-d')) }}" required />
                                @error('date_of_visit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Location / Area Covered -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="location" class="form-label">Location / Area Covered <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" placeholder="Enter Location / Area Covered" value="{{ old('location') }}" required />
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- House Number -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="house_number" class="form-label">House Number</label>
                                <input type="text" class="form-control @error('house_number') is-invalid @enderror" id="house_number" name="house_number" placeholder="Enter House Number" value="{{ old('house_number') }}" />
                                @error('house_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- LEAD INFORMATION Section -->
                        <div class="col-12">
                            <hr class="my-4">
                            <h6 class="mb-3">LEAD INFORMATION</h6>
                            <div class="alert alert-info">
                                <p class="mb-0">
                                    <strong>Lead information</strong> refers to the collection of details about a potential customer who has shown interest in a product or service. It typically includes the lead's name, contact number, email address, and location, along with the source from which the lead was generated, such as social media, referrals, or door-to-door campaigns. Additionally, it records the lead's specific interest or inquiry, current status (like new, contacted, or converted), and any planned follow-up dates. Notes or remarks may also be added to capture extra details from conversations or interactions. This information helps businesses track, manage, and nurture potential customers effectively, ensuring that each lead is followed up and guided smoothly through the sales process.
                                </p>
                            </div>
                        </div>

                        <!-- Lead Name -->
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="lead_name" class="form-label">Lead Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('lead_name') is-invalid @enderror" id="lead_name" name="lead_name" placeholder="Enter Lead Name" value="{{ old('lead_name') }}" required />
                                @error('lead_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Code and Phone -->
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="code" class="form-label">Country Code <span class="text-danger">*</span></label>
                                <select class="form-select @error('code') is-invalid @enderror" id="code" name="code" required>
                                    <option value="">Select Country</option>
                                    @foreach($country_codes as $code => $country)
                                        <option value="{{ $code }}" {{ old('code') == $code ? 'selected' : '' }}>{{ $code }} - {{ $country }}</option>
                                    @endforeach
                                </select>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group mb-3">
                                <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="number" name="phone" class="form-control @error('phone') is-invalid @enderror" id="phone" placeholder="Enter Phone" value="{{ old('phone') }}" required maxlength="15" />
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="phone-duplicate-warning" class="invalid-feedback d-none"></div>
                            </div>
                        </div>

                        <!-- Whatsapp Code and Number -->
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="whatsapp_code" class="form-label">WhatsApp Country Code</label>
                                <select class="form-select @error('whatsapp_code') is-invalid @enderror" id="whatsapp_code" name="whatsapp_code">
                                    <option value="">Select Country</option>
                                    @foreach($country_codes as $code => $country)
                                        <option value="{{ $code }}" {{ old('whatsapp_code') == $code ? 'selected' : '' }}>{{ $code }} - {{ $country }}</option>
                                    @endforeach
                                </select>
                                @error('whatsapp_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group mb-3">
                                <label for="whatsapp" class="form-label">WhatsApp Number</label>
                                <input type="number" name="whatsapp" class="form-control @error('whatsapp') is-invalid @enderror" id="whatsapp" placeholder="Enter WhatsApp Number" value="{{ old('whatsapp') }}" maxlength="15" />
                                @error('whatsapp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror" name="address" id="address" placeholder="Enter Address" value="{{ old('address') }}" />
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Lead Category -->
                        <div class="col-12">
                            <hr class="my-4">
                            <h6 class="mb-3">Lead Category</h6>
                            <div class="alert alert-info">
                                <p class="mb-0">
                                    <strong>Lead category</strong> refers to the classification of potential customers based on their level of interest and likelihood of conversion. This helps businesses or marketing teams prioritize their efforts and plan suitable follow-up actions. For Skill Park, leads can be categorized as hot, warm, cold, or not interested. Hot leads are those who are highly interested and ready to enroll, while warm leads show potential but may need more information or time to decide. Cold leads have been contacted but currently show little interest, and not interested leads are those who have declined or are not suitable for the offered programs. Categorizing leads in this way enables Skill Park to manage outreach efficiently, focus on the most promising prospects, and improve overall conversion rates.
                                </p>
                            </div>
                        </div>

                        <!-- Lead Type -->
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="lead_type" class="form-label">Lead Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('lead_type') is-invalid @enderror" name="lead_type" id="lead_type" required>
                                    <option value="">Select Lead Type</option>
                                    <option value="Student" {{ old('lead_type') == 'Student' ? 'selected' : '' }}>Student</option>
                                    <option value="Parent" {{ old('lead_type') == 'Parent' ? 'selected' : '' }}>Parent</option>
                                    <option value="Working Professional" {{ old('lead_type') == 'Working Professional' ? 'selected' : '' }}>Working Professional</option>
                                    <option value="Institution Representative" {{ old('lead_type') == 'Institution Representative' ? 'selected' : '' }}>Institution Representative</option>
                                    <option value="Others" {{ old('lead_type') == 'Others' ? 'selected' : '' }}>Others</option>
                                </select>
                                @error('lead_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Interested Courses -->
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label class="form-label">Interested Courses</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="interested_courses[]" value="SSLC" id="course_sslc" {{ in_array('SSLC', old('interested_courses', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="course_sslc">SSLC</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="interested_courses[]" value="Plus one Plus Two" id="course_plus" {{ in_array('Plus one Plus Two', old('interested_courses', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="course_plus">Plus one Plus Two</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="interested_courses[]" value="Degree" id="course_degree" {{ in_array('Degree', old('interested_courses', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="course_degree">Degree</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="interested_courses[]" value="Ai Python" id="course_ai_python" {{ in_array('Ai Python', old('interested_courses', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="course_ai_python">Ai Python</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="interested_courses[]" value="AI Integrated Digital Marketing" id="course_digital_marketing" {{ in_array('AI Integrated Digital Marketing', old('interested_courses', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="course_digital_marketing">AI Integrated Digital Marketing</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="interested_courses[]" value="Diploma in Graphic Designing" id="course_graphic_designing" {{ in_array('Diploma in Graphic Designing', old('interested_courses', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="course_graphic_designing">Diploma in Graphic Designing</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="interested_courses[]" value="Certificate Course in Medical Coding" id="course_medical_coding" {{ in_array('Certificate Course in Medical Coding', old('interested_courses', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="course_medical_coding">Certificate Course in Medical Coding</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="interested_courses[]" value="Diploma in Hospital Administration" id="course_hospital_admin" {{ in_array('Diploma in Hospital Administration', old('interested_courses', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="course_hospital_admin">Diploma in Hospital Administration</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="interested_courses[]" value="Hotel Management" id="course_hotel_management" {{ in_array('Hotel Management', old('interested_courses', [])) ? 'checked' : '' }}>
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
                                <textarea class="form-control @error('remarks') is-invalid @enderror" name="remarks" id="remarks" placeholder="Enter Remarks / Notes" rows="3">{{ old('remarks') }}</textarea>
                                @error('remarks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-12">
                            <div class="form-group text-end">
                                <button class="btn btn-primary" type="submit" id="submitBtn">
                                    <i class="ti ti-device-floppy"></i> <span id="submitBtnText">Submit</span>
                                </button>
                                <a href="{{ route('admin.marketing.index') }}" class="btn btn-secondary ms-2">
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
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form[method="post"]');
        if (!form) {
            console.error('Form not found');
            return;
        }
        
        const formAction = form.getAttribute('data-form-action') || form.action;
        const formRedirectUrl = form.getAttribute('data-redirect-url') || window.location.href;
        const submitBtn = document.getElementById('submitBtn');
        const submitBtnText = document.getElementById('submitBtnText');
        let isSubmitting = false;

        if (!submitBtn) {
            console.error('Submit button not found');
            return;
        }

        // Get CSRF token from meta tag or form
        function getCsrfToken() {
            const metaToken = document.querySelector('meta[name="csrf-token"]');
            if (metaToken) {
                return metaToken.getAttribute('content');
            }
            const formToken = form.querySelector('input[name="_token"]');
            return formToken ? formToken.value : '';
        }

        // Duplicate phone check
        const codeSelect = document.getElementById('code');
        const phoneInput = document.getElementById('phone');
        const phoneWarning = document.getElementById('phone-duplicate-warning');
        let duplicateCheckTimeout = null;
        let isDuplicate = false;

        function checkDuplicatePhone() {
            const code = codeSelect ? codeSelect.value : '';
            const phone = phoneInput ? phoneInput.value : '';

            // Clear previous timeout
            if (duplicateCheckTimeout) {
                clearTimeout(duplicateCheckTimeout);
            }

            // Hide warning initially
            if (phoneWarning) {
                phoneWarning.classList.add('d-none');
                phoneWarning.textContent = '';
            }

            // Only check if both code and phone are filled
            if (!code || !phone || phone.length < 5) {
                isDuplicate = false;
                return;
            }

            // Debounce the check (wait 500ms after user stops typing)
            duplicateCheckTimeout = setTimeout(function() {
                const csrfToken = getCsrfToken();
                
                fetch('{{ route("admin.marketing.check-duplicate-phone") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        code: code,
                        phone: phone
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        isDuplicate = true;
                        if (phoneWarning && phoneInput) {
                            phoneWarning.textContent = data.message + ' (Lead: ' + (data.lead ? data.lead.name + ' - ' + data.lead.date_of_visit : 'N/A') + ')';
                            phoneWarning.classList.remove('d-none');
                            phoneInput.classList.add('is-invalid');
                        }
                    } else {
                        isDuplicate = false;
                        if (phoneWarning && phoneInput) {
                            phoneWarning.classList.add('d-none');
                            phoneInput.classList.remove('is-invalid');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error checking duplicate:', error);
                    isDuplicate = false;
                });
            }, 500);
        }

        // Add event listeners for duplicate checking
        if (codeSelect) {
            codeSelect.addEventListener('change', checkDuplicatePhone);
        }
        if (phoneInput) {
            phoneInput.addEventListener('input', checkDuplicatePhone);
            phoneInput.addEventListener('blur', checkDuplicatePhone);
        }

        if (form && submitBtn && formAction) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validate phone and code are filled
                const code = codeSelect ? codeSelect.value : '';
                const phone = phoneInput ? phoneInput.value.trim() : '';
                
                if (!code || !phone) {
                    alert('Please fill in both Country Code and Phone number.');
                    if (!code && codeSelect) {
                        codeSelect.focus();
                    } else if (!phone && phoneInput) {
                        phoneInput.focus();
                    }
                    return false;
                }

                // Check for duplicate before submitting - prevent submission if duplicate exists
                if (isDuplicate) {
                    alert('This phone number (' + code + ' ' + phone + ') already exists in the system. Please use a different phone number or check the existing lead.');
                    if (phoneInput) {
                        phoneInput.focus();
                        phoneInput.select();
                    }
                    return false;
                }

                // Perform a final duplicate check before submission
                const csrfToken = getCsrfToken();
                
                // First check for duplicate, then proceed with submission
                fetch('{{ route("admin.marketing.check-duplicate-phone") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        code: code,
                        phone: phone
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        isDuplicate = true;
                        if (phoneWarning && phoneInput) {
                            phoneWarning.textContent = data.message + (data.lead ? ' (Lead: ' + data.lead.name + ' - ' + data.lead.date_of_visit + ')' : '');
                            phoneWarning.classList.remove('d-none');
                            phoneInput.classList.add('is-invalid');
                        }
                        alert('This phone number (' + code + ' ' + phone + ') already exists in the system. Please use a different phone number.');
                        if (phoneInput) {
                            phoneInput.focus();
                            phoneInput.select();
                        }
                        throw new Error('Duplicate phone number');
                    }
                    
                    // If no duplicate, proceed with form submission
                    isDuplicate = false;
                    
                    // Prevent multiple submissions
                    if (isSubmitting) {
                        return false;
                    }

                    // Mark as submitting
                    isSubmitting = true;
                    
                    // Disable submit button
                    submitBtn.disabled = true;
                    submitBtn.classList.add('disabled');
                    submitBtnText.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Submitting...';
                    
                    // Disable all form inputs to prevent changes
                    const formInputs = form.querySelectorAll('input, select, textarea, button');
                    formInputs.forEach(input => {
                        if (input !== submitBtn && input.type !== 'hidden') {
                            input.disabled = true;
                        }
                    });

                    // Prepare form data
                    const formData = new FormData(form);
                    
                    // Add CSRF token to form data if not already present
                    if (!formData.has('_token')) {
                        formData.append('_token', csrfToken);
                    }

                    // Submit via AJAX
                    return fetch(formAction, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json, text/html, */*'
                        },
                        credentials: 'same-origin',
                        redirect: 'follow'
                    });
                })
                .then(response => {
                    // Check if response is a redirect (302, 301, etc.)
                    if (response.redirected || response.status === 302 || response.status === 301) {
                        // Follow the redirect
                        window.location.href = response.url || formRedirectUrl;
                        return;
                    }
                    
                    // Check for 419 CSRF token mismatch
                    if (response.status === 419) {
                        // Reload page to get fresh CSRF token
                        alert('Session expired. Please try again.');
                        window.location.reload();
                        return;
                    }
                    
                    // Check for other errors
                    if (!response.ok) {
                        throw new Error('Server error: ' + response.status);
                    }
                    
                    return response.text();
                })
                .then(data => {
                    if (!data) {
                        // If no data, redirect to form page
                        window.location.href = formRedirectUrl;
                        return;
                    }
                    
                    // Check if response contains error indicators
                    if (data.includes('419') || data.includes('Page Expired') || data.includes('CSRF token mismatch')) {
                        alert('Session expired. Please refresh the page and try again.');
                        window.location.reload();
                        return;
                    }
                    
                    // If we get here, redirect to form page (success)
                    window.location.href = formRedirectUrl;
                })
                .catch(error => {
                    console.error('Error:', error);
                    
                    // Don't show generic error for duplicate phone (already handled)
                    if (error.message === 'Duplicate phone number') {
                        return; // Error already shown to user
                    }
                    
                    isSubmitting = false;
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('disabled');
                    submitBtnText.innerHTML = 'Submit';
                    
                    // Re-enable form inputs
                    const formInputs = form.querySelectorAll('input, select, textarea, button');
                    formInputs.forEach(input => {
                        input.disabled = false;
                    });

                    // Show error message
                    alert('An error occurred while submitting the form. Please try again.\n\nIf this problem persists, please refresh the page and try again.');
                });
            });

            // Re-enable form if user navigates back (using browser back button)
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    isSubmitting = false;
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('disabled');
                    submitBtnText.innerHTML = 'Submit';
                    
                    const formInputs = form.querySelectorAll('input, select, textarea, button');
                    formInputs.forEach(input => {
                        input.disabled = false;
                    });
                }
            });
        }
    });
</script>
@endpush

