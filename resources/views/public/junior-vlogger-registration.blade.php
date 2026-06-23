<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CreateX AI – Lead Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .wizard-container { max-width: 900px; margin: 0 auto; padding: 15px; }
        .wizard-header {
            background: linear-gradient(225deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            border-radius: 15px 15px 0 0;
            text-align: center;
        }
        .wizard-header h2 { margin-bottom: 10px; font-weight: 700; }
        .wizard-body {
            background: white;
            padding: 50px 40px;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        }
        .progress { height: 8px; border-radius: 10px; background: #f0f0f0; }
        .progress-bar { background: linear-gradient(90deg, #667eea, #764ba2); border-radius: 10px; transition: width 0.3s ease; }
        .step-indicators { display: flex; justify-content: space-between; margin-top: 20px; }
        .step-indicator { display: flex; flex-direction: column; align-items: center; flex: 1; position: relative; }
        .step-indicator:not(:last-child)::after {
            content: ''; position: absolute; top: 15px; left: 60%; right: -40%; height: 2px; background: #e0e0e0; z-index: 1;
        }
        .step-indicator.active:not(:last-child)::after { background: #667eea; }
        .step-circle {
            width: 30px; height: 30px; border-radius: 50%; background: #e0e0e0; color: #999;
            display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px; position: relative; z-index: 2;
        }
        .step-indicator.active .step-circle { background: #667eea; color: white; }
        .step-indicator.completed .step-circle { background: #28a745; color: white; }
        .step-label { margin-top: 8px; font-size: 12px; color: #666; text-align: center; }
        .step-indicator.active .step-label { color: #667eea; font-weight: 600; }
        .form-step { display: none; }
        .form-step.active { display: block; }
        .form-group { margin-bottom: 25px; }
        .form-label { font-weight: 600; color: #333; margin-bottom: 8px; display: block; }
        .required { color: #dc3545; }
        .form-control { border: 2px solid #e9ecef; border-radius: 8px; padding: 12px 15px; font-size: 14px; }
        .form-control:focus { border-color: #667eea; box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25); }
        .section-title { font-size: 1.1rem; color: #495057; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e9ecef; }
        .btn-wizard { padding: 12px 30px; border-radius: 8px; font-weight: 600; }
        .btn-primary { background: #667eea; border: none; }
        .btn-primary:hover { background: #5a67d8; transform: translateY(-2px); }
        .btn-success { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border: none; }
        .btn-success:hover { transform: translateY(-2px); }
        .btn-secondary { background: #6c757d; border: none; }
        .pre-filled-info {
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            border: 1px solid #bbdefb;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .file-upload-area {
            border: 2px dashed #ddd;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #fafafa;
        }
        .file-upload-area:hover { border-color: #667eea; background: #f8f9ff; }
        .file-upload-area.dragover { border-color: #667eea; background: #f0f4ff; }
        .file-preview-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 10px 15px;
            margin-top: 10px;
        }
        .age-display { font-weight: 600; color: #495057; }
        .loading { opacity: 0.7; pointer-events: none; }
        .alert { border-radius: 10px; margin-bottom: 20px; }
        @media (max-width: 768px) {
            .wizard-body { padding: 30px 20px; }
            .step-indicators { flex-direction: column; gap: 15px; }
            .step-indicator:not(:last-child)::after { display: none; }
        }
    </style>
</head>
<body style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; padding: 20px 0;">
    <div class="wizard-container">
        <div class="wizard-header">
            <h2><i class="fas fa-video me-2"></i>CreateX AI – Lead Registration</h2>
            <p class="mb-0">Course registration in 4 simple steps</p>
        </div>

        <div class="wizard-body">
            <div class="progress-container mb-4">
                <div class="progress">
                    <div class="progress-bar" id="progressBar" style="width: 25%"></div>
                </div>
                <div class="step-indicators">
                    <div class="step-indicator active" id="step1Indicator">
                        <div class="step-circle">1</div>
                        <div class="step-label">Profile</div>
                    </div>
                    <div class="step-indicator" id="step2Indicator">
                        <div class="step-circle">2</div>
                        <div class="step-label">Contact</div>
                    </div>
                    <div class="step-indicator" id="step3Indicator">
                        <div class="step-circle">3</div>
                        <div class="step-label">Academic</div>
                    </div>
                    <div class="step-indicator" id="step4Indicator">
                        <div class="step-circle">4</div>
                        <div class="step-label">Documents</div>
                    </div>
                </div>
            </div>

            @if($lead)
            <div class="pre-filled-info">
                <h6><i class="fas fa-info-circle me-2"></i>Pre-filled from lead</h6>
                <div class="row">
                    <div class="col-md-4"><strong>Name:</strong> {{ $lead->title }}</div>
                    @if($lead->email)<div class="col-md-4"><strong>Email:</strong> {{ $lead->email }}</div>@endif
                    <div class="col-md-4"><strong>Phone:</strong> {{ \App\Helpers\PhoneNumberHelper::display($lead->code ?? '', $lead->phone ?? '') }}</div>
                </div>
            </div>
            @endif

            <form id="registrationForm" enctype="multipart/form-data" novalidate>
                @csrf
                <input type="hidden" name="lead_id" value="{{ $lead ? $lead->id : '' }}">

                <!-- Step 1: Student Master Profile -->
                <div class="form-step active" id="formStep1">
                    <h5 class="section-title"><i class="fas fa-user me-2"></i>1. Student Master Profile</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Full Name <span class="required">*</span></label>
                                <input type="text" class="form-control" name="student_name" value="{{ $lead ? $lead->title : '' }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Gender <span class="required">*</span></label>
                                <select class="form-control" name="gender" required>
                                    <option value="">Select</option>
                                    <option value="male" {{ ($lead && $lead->gender == 'male') ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ ($lead && $lead->gender == 'female') ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ ($lead && $lead->gender == 'other') ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Date of Birth <span class="required">*</span></label>
                                <input type="date" class="form-control" name="date_of_birth" id="date_of_birth"
                                       min="{{ date('Y-m-d', strtotime('-100 years')) }}" max="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Age</label>
                                <p class="age-display mt-2 mb-0" id="ageDisplay">—</p>
                                <small class="text-muted">Auto-calculated from date of birth</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Contact Information -->
                <div class="form-step" id="formStep2">
                    <h5 class="section-title"><i class="fas fa-address-book me-2"></i>2. Contact Information</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Primary Mobile Number <span class="required">*</span></label>
                                <div class="row g-1">
                                    <div class="col-4">
                                        <select class="form-control" name="personal_code" required>
                                            @foreach($countryCodes as $code => $country)
                                                <option value="{{ $code }}" {{ ($lead && $lead->code === $code) ? 'selected' : '' }}>{{ $code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-8">
                                        <input type="tel" class="form-control" name="personal_number" value="{{ $lead ? $lead->phone : '' }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">WhatsApp Number <span class="required">*</span></label>
                                <div class="row g-1">
                                    <div class="col-4">
                                        <select class="form-control" name="whatsapp_code" required>
                                            @foreach($countryCodes as $code => $country)
                                                <option value="{{ $code }}" {{ ($lead && $lead->whatsapp_code === $code) ? 'selected' : '' }}>{{ $code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-8">
                                        <input type="tel" class="form-control" name="whatsapp_number" value="{{ $lead ? $lead->whatsapp : '' }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Alternate Contact Number</label>
                                <div class="row g-1">
                                    <div class="col-4">
                                        <select class="form-control" name="parents_code">
                                            @foreach($countryCodes as $code => $country)
                                                <option value="{{ $code }}">{{ $code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-8">
                                        <input type="tel" class="form-control" name="parents_number" placeholder="Optional">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Email ID <span class="required">*</span></label>
                                <input type="email" class="form-control" name="email" value="{{ $lead ? $lead->email : '' }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Academic Background -->
                <div class="form-step" id="formStep3">
                    <h5 class="section-title"><i class="fas fa-graduation-cap me-2"></i>3. Academic Background</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Medium of Study <span class="required">*</span></label>
                                <select class="form-control" name="medium_of_study" required>
                                    <option value="">Select</option>
                                    <option value="english">English</option>
                                    <option value="malayalam">Malayalam</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Previous Qualification <span class="required">*</span></label>
                                <select class="form-control" name="previous_qualification" required>
                                    <option value="">Select</option>
                                    <option value="plus_two">Plus Two</option>
                                    <option value="sslc">SSLC</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Technology Performance Category <span class="required">*</span></label>
                                <select class="form-control" name="technology_performance_category" required>
                                    <option value="">Select</option>
                                    <option value="excellent">Excellent</option>
                                    <option value="average">Average</option>
                                    <option value="needs_support">Needs Support</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Required Documents -->
                <div class="form-step" id="formStep4">
                    <h5 class="section-title"><i class="fas fa-file-alt me-2"></i>4. Required Documents</h5>
                    <p class="text-muted mb-4">Upload clear scans or photos. Max 2MB per file.</p>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Passport-size Photo</label>
                                <div class="file-upload-area" onclick="document.getElementById('passport_photo').click()">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <p class="mb-0">Click or drag & drop</p>
                                    <small class="text-muted">JPG, PNG</small>
                                </div>
                                <input type="file" id="passport_photo" name="passport_photo" accept=".jpg,.jpeg,.png" style="display: none;">
                                <div class="file-preview" id="passport_photo_preview"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Aadhaar Card</label>
                                <div class="file-upload-area" onclick="document.getElementById('adhar_front').click()">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <p class="mb-0">Click or drag & drop</p>
                                    <small class="text-muted">PDF, JPG, PNG</small>
                                </div>
                                <input type="file" id="adhar_front" name="adhar_front" accept=".pdf,.jpg,.jpeg,.png" style="display: none;">
                                <div class="file-preview" id="adhar_front_preview"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">SSLC Certificate</label>
                                <div class="file-upload-area" onclick="document.getElementById('sslc_certificate').click()">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <p class="mb-0">Click or drag & drop</p>
                                    <small class="text-muted">PDF, JPG, PNG</small>
                                </div>
                                <input type="file" id="sslc_certificate" name="sslc_certificate" accept=".pdf,.jpg,.jpeg,.png" style="display: none;">
                                <div class="file-preview" id="sslc_certificate_preview"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Message (optional)</label>
                        <textarea class="form-control" name="message" rows="2" placeholder="Any additional details..."></textarea>
                    </div>
                </div>

                @include('public.partials.terms-and-conditions')
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-secondary btn-wizard" id="prevBtn" onclick="changeStep(-1)" style="display: none;">
                        <i class="fas fa-arrow-left me-2"></i>Previous
                    </button>
                    <div class="ms-auto">
                        <button type="button" class="btn btn-primary btn-wizard" id="nextBtn" onclick="changeStep(1)">
                            Next <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                        <button type="submit" class="btn btn-success btn-wizard" id="submitBtn" style="display: none;">
                            <i class="fas fa-check me-2"></i>Submit
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Success popup modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-body text-center py-5 px-4">
                    <div class="mb-3">
                        <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-check text-success fa-3x"></i>
                        </div>
                    </div>
                    <h5 class="modal-title text-success mb-2" id="successModalLabel">Success!</h5>
                    <p class="text-muted mb-4" id="successModalMessage">Registration submitted successfully! We will review your application and get back to you soon.</p>
                    <button type="button" class="btn btn-success px-4" id="successModalOkBtn">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentStep = 1;
        const totalSteps = 4;

        function calcAge(dobStr) {
            if (!dobStr) return null;
            var dob = new Date(dobStr);
            var today = new Date();
            var age = today.getFullYear() - dob.getFullYear();
            var m = today.getMonth() - dob.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
            return age;
        }

        function updateAgeDisplay() {
            var dob = document.getElementById('date_of_birth').value;
            var age = calcAge(dob);
            document.getElementById('ageDisplay').textContent = age !== null ? age + ' years' : '—';
        }

        document.getElementById('date_of_birth').addEventListener('change', updateAgeDisplay);
        document.getElementById('date_of_birth').addEventListener('input', updateAgeDisplay);

        function updateStepDisplay() {
            document.querySelectorAll('.form-step').forEach(function(s) { s.classList.remove('active'); });
            document.getElementById('formStep' + currentStep).classList.add('active');
            document.getElementById('progressBar').style.width = (currentStep / totalSteps) * 100 + '%';
            [1,2,3,4].forEach(function(i) {
                var ind = document.getElementById('step' + i + 'Indicator');
                ind.classList.remove('active', 'completed');
                if (i < currentStep) ind.classList.add('completed');
                else if (i === currentStep) ind.classList.add('active');
            });
            document.getElementById('prevBtn').style.display = currentStep === 1 ? 'none' : 'inline-block';
            document.getElementById('nextBtn').style.display = currentStep === totalSteps ? 'none' : 'inline-block';
            document.getElementById('submitBtn').style.display = currentStep === totalSteps ? 'inline-block' : 'none';
            if (typeof window.applyTermsCheckboxVisibility === 'function') window.applyTermsCheckboxVisibility();
        }

        function validateCurrentStep() {
            var step = document.getElementById('formStep' + currentStep);
            var required = step.querySelectorAll('[required]');
            for (var i = 0; i < required.length; i++) {
                var el = required[i];
                if (el.type === 'file') {
                    if (!el.files || el.files.length === 0) {
                        alert('Please upload all required documents.');
                        return false;
                    }
                } else if (!el.value.trim()) {
                    el.focus();
                    alert('Please fill in all required fields.');
                    return false;
                }
            }
            return true;
        }

        function changeStep(direction) {
            if (direction > 0 && !validateCurrentStep()) return;
            var next = currentStep + direction;
            if (next >= 1 && next <= totalSteps) {
                currentStep = next;
                updateStepDisplay();
            }
        }

        function setupFileUpload(inputId) {
            var input = document.getElementById(inputId);
            var preview = document.getElementById(inputId + '_preview');
            if (!input || !preview) return;
            input.addEventListener('change', function() {
                var file = this.files[0];
                if (file) {
                    if (file.size > 2 * 1024 * 1024) {
                        alert('File size must be less than 2MB.');
                        this.value = '';
                        return;
                    }
                    preview.innerHTML = '<div class="file-preview-item"><div class="file-info"><i class="fas fa-file"></i> ' + file.name + '</div><span class="remove-file" onclick="document.getElementById(\'' + inputId + '\').value=\'\'; document.getElementById(\'' + inputId + '_preview\').innerHTML=\'\'"><i class="fas fa-times text-danger"></i></span></div>';
                }
            });
        }
        setupFileUpload('passport_photo');
        setupFileUpload('adhar_front');
        setupFileUpload('sslc_certificate');

        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            if (!validateCurrentStep()) return;
            var formData = new FormData(this);
            var btn = document.getElementById('submitBtn');
            var orig = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
            btn.disabled = true;
            fetch('{{ route("public.lead.junior-vlogger.store") }}', {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    var modal = new bootstrap.Modal(document.getElementById('successModal'));
                    document.getElementById('successModalMessage').textContent = data.message || 'Registration submitted successfully! We will review your application and get back to you soon.';
                    var redirectUrl = data.redirect || '';
                    document.getElementById('successModalOkBtn').onclick = function() {
                        modal.hide();
                        if (redirectUrl) window.location.href = redirectUrl;
                    };
                    modal.show();
                } else {
                    alert(data.message || 'Something went wrong.');
                }
            })
            .catch(function() { alert('Network error. Please try again.'); })
            .finally(function() {
                btn.innerHTML = orig;
                btn.disabled = false;
            });
        });

        updateStepDisplay();
    </script>
</body>
</html>
