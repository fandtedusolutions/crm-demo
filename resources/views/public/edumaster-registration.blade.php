<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>EduMaster Course Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .wizard-container { max-width: 900px; margin: 0 auto; padding: 15px; }
        .wizard-header { background: linear-gradient(225deg, #abb7ed 0%, #787879 100%); color: white; padding: 40px 30px; border-radius: 15px 15px 0 0; text-align: center; position: relative; overflow: hidden; }
        .wizard-header h2 { position: relative; z-index: 1; margin-bottom: 10px; font-weight: 700; }
        .wizard-header p { position: relative; z-index: 1; opacity: 0.9; }
        .wizard-body { background: white; padding: 50px 40px; border-radius: 0 0 15px 15px; box-shadow: 0 20px 60px rgba(0,0,0,0.1); }
        .progress-container { margin-bottom: 40px; }
        .progress { height: 8px; border-radius: 10px; background: #f0f0f0; }
        .progress-bar { background: #829b99; border-radius: 10px; transition: width 0.3s ease; }
        .step-indicators { display: flex; justify-content: space-between; margin-top: 20px; }
        .step-indicator { display: flex; flex-direction: column; align-items: center; flex: 1; position: relative; }
        .step-indicator:not(:last-child)::after { content: ''; position: absolute; top: 15px; left: 60%; right: -40%; height: 2px; background: #e0e0e0; z-index: 1; }
        .step-indicator.active:not(:last-child)::after { background: #829b99; }
        .step-circle { width: 30px; height: 30px; border-radius: 50%; background: #e0e0e0; color: #999; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px; position: relative; z-index: 2; transition: all 0.3s ease; }
        .step-indicator.active .step-circle { background: #829b99; color: white; }
        .step-indicator.completed .step-circle { background: #28a745; color: white; }
        .step-label { margin-top: 8px; font-size: 12px; color: #666; text-align: center; }
        .step-indicator.active .step-label { color: #829b99; font-weight: 600; }
        .form-step { display: none; }
        .form-step.active { display: block; }
        .form-group { margin-bottom: 25px; }
        .form-label { font-weight: 600; color: #333; margin-bottom: 8px; display: block; }
        .required { color: #dc3545; }
        .form-control { border: 2px solid #e9ecef; border-radius: 8px; padding: 12px 15px; font-size: 14px; transition: all 0.3s ease; }
        .form-control:focus { border-color: #829b99; box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25); }
        .btn-wizard { padding: 12px 30px; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; }
        .btn-primary { background: #829b99; border: none; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3); }
        .btn-success { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border: none; }
        .btn-success:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3); }
        .btn-secondary { background: #6c757d; border: none; }
        .btn-secondary:hover { background: #5a6268; transform: translateY(-2px); }
        .pre-filled-info { background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%); border: 1px solid #bbdefb; border-radius: 10px; padding: 20px; margin-bottom: 30px; }
        .pre-filled-info h6 { color: #1976d2; font-weight: 600; margin-bottom: 15px; }
        .info-item { display: flex; align-items: center; margin-bottom: 8px; }
        .info-item i { color: #1976d2; margin-right: 10px; width: 16px; }
        .file-upload-area { border: 2px dashed #ddd; border-radius: 10px; padding: 30px; text-align: center; cursor: pointer; transition: all 0.3s ease; background: #fafafa; }
        .file-upload-area:hover { border-color: #829b99; background: #f8f9ff; }
        .file-upload-area.dragover { border-color: #829b99; background: #f0f4ff; }
        .file-preview { margin-top: 15px; }
        .file-preview-item { display: flex; align-items: center; justify-content: space-between; background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; padding: 10px 15px; margin-bottom: 10px; }
        .file-preview-item .file-info { display: flex; align-items: center; }
        .file-preview-item .file-info i { color: #829b99; margin-right: 10px; }
        .file-preview-item .remove-file { color: #dc3545; cursor: pointer; padding: 5px; }
        .file-preview-item .remove-file:hover { background: #f8d7da; border-radius: 4px; }
        .loading { opacity: 0.7; pointer-events: none; }
        .alert { border-radius: 10px; border: none; padding: 15px 20px; margin-bottom: 20px; }
        .alert-success { background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); color: #155724; }
        .alert-danger { background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); color: #721c24; }
        .alert-info { background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%); color: #0c5460; }
        .alert-warning { background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); color: #856404; }
        .logo-container { display: flex; justify-content: center; align-items: center; }
        .skill-park-logo { max-height: 100px; max-width: 200px; object-fit: contain; opacity: 0.9; transition: all 0.3s ease; }
        .skill-park-logo:hover { opacity: 1; transform: scale(1.05); }
        .form-control.is-invalid { border-color: #dc3545; }
        .checkbox-group { display: flex; flex-wrap: wrap; gap: 15px; margin-top: 10px; }
        .checkbox-item { display: flex; align-items: center; }
        .checkbox-item input[type="checkbox"] { width: 20px; height: 20px; margin-right: 8px; cursor: pointer; }
        .checkbox-item label { margin: 0; cursor: pointer; font-weight: 500; }
        @media (max-width: 768px) {
            .wizard-container { padding: 10px; }
            .wizard-body { padding: 30px 20px; }
            .step-indicators { flex-direction: column; gap: 15px; }
            .step-indicator:not(:last-child)::after { display: none; }
            .btn-wizard { width: 100%; margin-bottom: 10px; }
            .skill-park-logo { max-height: 80px; max-width: 150px; }
        }
    </style>
</head>
<body style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; padding: 20px 0;">
    <div class="wizard-container">
        <div class="wizard-header">
            <div class="logo-container mb-3">
                <img src="{{ asset('skill-park-logo.png') }}" alt="Skill Park Logo" class="skill-park-logo">
            </div>
            <h2><i class="fas fa-graduation-cap me-2"></i>EduMaster Course Registration</h2>
            <p class="mb-0">Complete your registration in 4 simple steps</p>
        </div>
        
        <div class="wizard-body">
            <!-- Progress Bar -->
            <div class="progress-container">
                <div class="progress">
                    <div class="progress-bar" id="progressBar" style="width: 25%"></div>
                </div>
                <div class="step-indicators">
                    <div class="step-indicator active" id="step1Indicator">
                        <div class="step-circle">1</div>
                        <div class="step-label">Personal Details</div>
                    </div>
                    <div class="step-indicator" id="step2Indicator">
                        <div class="step-circle">2</div>
                        <div class="step-label">Communication</div>
                    </div>
                    <div class="step-indicator" id="step3Indicator">
                        <div class="step-circle">3</div>
                        <div class="step-label">Programme</div>
                    </div>
                    <div class="step-indicator" id="step4Indicator">
                        <div class="step-circle">4</div>
                        <div class="step-label">Documents</div>
                    </div>
                </div>
            </div>

            <!-- Pre-filled Information -->
            @if($lead)
            <div class="pre-filled-info">
                <h6><i class="fas fa-info-circle me-2"></i>Pre-filled Information</h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-item">
                            <i class="fas fa-user"></i>
                            <span><strong>Name:</strong> {{ $lead->title }}</span>
                        </div>
                    </div>
                    @if($lead->email)
                    <div class="col-md-4">
                        <div class="info-item">
                            <i class="fas fa-envelope"></i>
                            <span><strong>Email:</strong> {{ $lead->email }}</span>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-4">
                        <div class="info-item">
                            <i class="fas fa-phone"></i>
                            <span><strong>Phone:</strong> {{ \App\Helpers\PhoneNumberHelper::display($lead->code, $lead->phone) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <form id="registrationForm" enctype="multipart/form-data" novalidate>
                @csrf
                <input type="hidden" name="lead_id" value="{{ $lead->id ?? '' }}">
                
                <!-- Step 1: Personal Details -->
                <div class="form-step active" id="formStep1">
                    <h4 class="mb-4"><i class="fas fa-user me-2"></i>Personal Details</h4>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Candidate Name <span class="required">*</span></label>
                                <input type="text" class="form-control" name="student_name" value="{{ $lead->title ?? '' }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Father's Name <span class="required">*</span></label>
                                <input type="text" class="form-control" name="father_name" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Mother's Name <span class="required">*</span></label>
                                <input type="text" class="form-control" name="mother_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Date of Birth <span class="required">*</span></label>
                                <input type="date" class="form-control" name="date_of_birth" min="{{ date('Y-m-d', strtotime('-100 years')) }}" max="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Gender <span class="required">*</span></label>
                                <select class="form-control" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Are you employed? <span class="required">*</span></label>
                                <select class="form-control" name="is_employed" required>
                                    <option value="">Select</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                </div>
                
                <!-- Step 2: Communication Details -->
                <div class="form-step" id="formStep2">
                    <h4 class="mb-4"><i class="fas fa-phone me-2"></i>Communication Details</h4>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Candidate Contact No. <span class="required">*</span></label>
                                <div class="row">
                                    <div class="col-4">
                                        <select class="form-control" name="personal_code" required>
                                            @foreach($countryCodes as $code => $country)
                                                <option value="{{ $code }}" {{ ($lead && $lead->code == $code) ? 'selected' : '' }}>{{ $code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-8">
                                        <input type="tel" class="form-control" name="personal_number" value="{{ $lead->phone ?? '' }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Candidate WhatsApp No. <span class="required">*</span></label>
                                <div class="row">
                                    <div class="col-4">
                                        <select class="form-control" name="whatsapp_code" required>
                                            @foreach($countryCodes as $code => $country)
                                                <option value="{{ $code }}">{{ $code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-8">
                                        <input type="tel" class="form-control" name="whatsapp_number" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Father's Contact No. <span class="required">*</span></label>
                                <div class="row">
                                    <div class="col-4">
                                        <select class="form-control" name="father_code" required>
                                            @foreach($countryCodes as $code => $country)
                                                <option value="{{ $code }}">{{ $code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-8">
                                        <input type="tel" class="form-control" name="father_number" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Mother's Contact No. <span class="required">*</span></label>
                                <div class="row">
                                    <div class="col-4">
                                        <select class="form-control" name="mother_code" required>
                                            @foreach($countryCodes as $code => $country)
                                                <option value="{{ $code }}">{{ $code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-8">
                                        <input type="tel" class="form-control" name="mother_number" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Email Address <span class="required">*</span></label>
                                <input type="email" class="form-control" name="email" value="{{ $lead->email ?? '' }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Residential Address <span class="required">*</span></label>
                                <textarea class="form-control" name="residential_address" rows="2" required></textarea>
                            </div>
                        </div>
                    </div>
                    
                </div>
                
                <!-- Step 3: Programme Details -->
                <div class="form-step" id="formStep3">
                    <h4 class="mb-4"><i class="fas fa-graduation-cap me-2"></i>Programme Details</h4>
                    @include('public.partials.addon-course-field', ['addonParentCourseId' => 23])
                    
                    <!-- Select Course Checkboxes -->
                    <div class="form-group">
                        <label class="form-label">Select Course <span class="required">*</span></label>
                        <div class="checkbox-group">
                            <div class="checkbox-item">
                                <input type="checkbox" id="course_sslc" name="selected_courses[]" value="SSLC">
                                <label for="course_sslc">SSLC</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="course_plustwo" name="selected_courses[]" value="Plus two">
                                <label for="course_plustwo">Plus two</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="course_ug" name="selected_courses[]" value="UG">
                                <label for="course_ug">UG</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="course_pg" name="selected_courses[]" value="PG">
                                <label for="course_pg">PG</label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- SSLC Back Year -->
                    <div class="row" id="sslc_back_year_row" style="display: none;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">SSLC Back Year <span class="required">*</span></label>
                                <select class="form-control" name="sslc_back_year" id="sslc_back_year">
                                    <option value="">Select Back Year</option>
                                    @for($year = 2018; $year <= date('Y'); $year++)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Plus Two Back Year -->
                    <div class="row" id="plustwo_back_year_row" style="display: none;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Plus Two Back Year <span class="required">*</span></label>
                                <select class="form-control" name="plustwo_back_year" id="plustwo_back_year">
                                    <option value="">Select Back Year</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Plus Two Subject <span class="required">*</span></label>
                                <input type="text" class="form-control" name="plustwo_subject" id="plustwo_subject" placeholder="Enter Plus Two Subject">
                            </div>
                        </div>
                    </div>
                    
                    <!-- University and Course Type (for UG/PG) -->
                    <div class="row" id="university_row" style="display: none;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Board/University <span class="required">*</span></label>
                                <select class="form-control" name="university_id" id="university_id">
                                    <option value="">Select Board/University</option>
                                    @foreach($universities as $university)
                                        <option value="{{ $university->id }}">{{ $university->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Course Type <span class="required">*</span></label>
                                <select class="form-control" name="course_type" id="course_type">
                                    <option value="">Select Course Type</option>
                                    <option value="UG">UG (Under Graduate)</option>
                                    <option value="PG">PG (Post Graduate)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Degree / University Back Year (separate from Plus Two back year) -->
                    <div class="row" id="degree_back_year_row" style="display: none;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Degree Back Year <span class="required">*</span></label>
                                <select class="form-control" name="degree_back_year" id="degree_back_year">
                                    <option value="">Select Back Year</option>
                                    @for($year = 2018; $year <= date('Y'); $year++)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Course Name (for UG/PG) -->
                    <div class="row" id="course_name_row" style="display: none;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Course Name <span class="required">*</span></label>
                                <input type="text" class="form-control" name="edumaster_course_name" id="edumaster_course_name" placeholder="Enter course name">
                            </div>
                        </div>
                    </div>
                    
                </div>
                
                <!-- Step 4: Upload Documents -->
                <div class="form-step" id="formStep4">
                    <h4 class="mb-4"><i class="fas fa-upload me-2"></i>Upload Documents</h4>
                    <p class="text-muted mb-4">Please upload clear scans or photos of the required documents. Max file size: 1MB.</p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Recent Passport Size Photograph <span class="required">*</span></label>
                                <div class="file-upload-area" onclick="document.getElementById('passport_photo').click()">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <p class="mb-0">Click to upload or drag & drop</p>
                                    <small class="text-muted">JPG, PNG (Max 1MB)</small>
                                </div>
                                <input type="file" id="passport_photo" name="passport_photo" accept=".jpg,.jpeg,.png" required style="display: none;">
                                <div class="file-preview" id="passport_photo_preview"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Aadhar Card (Front) <span class="required">*</span></label>
                                <div class="file-upload-area" onclick="document.getElementById('adhar_front').click()">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <p class="mb-0">Click to upload or drag & drop</p>
                                    <small class="text-muted">PDF, JPG, PNG (Max 1MB)</small>
                                </div>
                                <input type="file" id="adhar_front" name="adhar_front" accept=".pdf,.jpg,.jpeg,.png" required style="display: none;">
                                <div class="file-preview" id="adhar_front_preview"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Aadhar Card (Back) <span class="required">*</span></label>
                                <div class="file-upload-area" onclick="document.getElementById('adhar_back').click()">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <p class="mb-0">Click to upload or drag & drop</p>
                                    <small class="text-muted">PDF, JPG, PNG (Max 1MB)</small>
                                </div>
                                <input type="file" id="adhar_back" name="adhar_back" accept=".pdf,.jpg,.jpeg,.png" required style="display: none;">
                                <div class="file-preview" id="adhar_back_preview"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Signature <span class="required">*</span></label>
                                <div class="file-upload-area" onclick="document.getElementById('signature').click()">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <p class="mb-0">Click to upload or drag & drop</p>
                                    <small class="text-muted">JPG, PNG (Max 1MB)</small>
                                </div>
                                <input type="file" id="signature" name="signature" accept=".jpg,.jpeg,.png" required style="display: none;">
                                <div class="file-preview" id="signature_preview"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Secondary (10th) Certificate - only if Plus Two is checked -->
                    <div class="row" id="sslc_certificate_row" style="display: none;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Secondary (10th) Certificate <span class="required">*</span></label>
                                <div class="file-upload-area" onclick="document.getElementById('sslc_certificate').click()">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <p class="mb-0">Click to upload or drag & drop</p>
                                    <small class="text-muted">PDF, JPG, PNG (Max 1MB)</small>
                                </div>
                                <input type="file" id="sslc_certificate" name="sslc_certificate" accept=".pdf,.jpg,.jpeg,.png" style="display: none;">
                                <div class="file-preview" id="sslc_certificate_preview"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Senior Secondary (12th) Certificate - only if UG is checked -->
                    <div class="row" id="plustwo_certificate_row" style="display: none;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Senior Secondary (12th) Certificate <span class="required">*</span></label>
                                <div class="file-upload-area" onclick="document.getElementById('plustwo_certificate').click()">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <p class="mb-0">Click to upload or drag & drop</p>
                                    <small class="text-muted">PDF, JPG, PNG (Max 1MB)</small>
                                </div>
                                <input type="file" id="plustwo_certificate" name="plustwo_certificate" accept=".pdf,.jpg,.jpeg,.png" style="display: none;">
                                <div class="file-preview" id="plustwo_certificate_preview"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Graduation Certificate - only if PG is checked -->
                    <div class="row" id="ug_certificate_row" style="display: none;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Graduation Certificate <span class="required">*</span></label>
                                <div class="file-upload-area" onclick="document.getElementById('ug_certificate').click()">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <p class="mb-0">Click to upload or drag & drop</p>
                                    <small class="text-muted">PDF, JPG, PNG (Max 1MB)</small>
                                </div>
                                <input type="file" id="ug_certificate" name="ug_certificate" accept=".pdf,.jpg,.jpeg,.png" style="display: none;">
                                <div class="file-preview" id="ug_certificate_preview"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Other Documents</label>
                                <div class="file-upload-area" onclick="document.getElementById('other_documents').click()">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <p class="mb-0">Click to upload or drag & drop</p>
                                    <small class="text-muted">PDF, JPG, PNG (Max 1MB)</small>
                                </div>
                                <input type="file" id="other_documents" name="other_documents[]" accept=".pdf,.jpg,.jpeg,.png" multiple style="display: none;">
                                <div class="file-preview" id="other_documents_preview"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Message</label>
                        <textarea class="form-control" name="message" rows="3" placeholder="Enter any message or additional details..."></textarea>
                    </div>
                </div>

                @include('public.partials.terms-and-conditions')
                <!-- Navigation Buttons -->
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-outline-secondary btn-wizard" id="prevBtn" onclick="changeStep(-1)" style="display: none;">
                        <i class="fas fa-arrow-left me-2"></i>Previous
                    </button>
                    <div class="ms-auto">
                        <button type="button" class="btn btn-primary btn-wizard" id="nextBtn" style="display: inline-block;">
                            Next<i class="fas fa-arrow-right ms-2"></i>
                        </button>
                        <button type="submit" class="btn btn-success btn-wizard" id="submitBtn" style="display: none;">
                            <i class="fas fa-check me-2"></i>Submit Registration
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentStep = 1;
        const totalSteps = 4;
        const STORAGE_KEY = 'edumaster_form_data';

        // Handle course checkbox changes
        function handleCourseSelection() {
            const sslcChecked = document.getElementById('course_sslc').checked;
            const plustwoChecked = document.getElementById('course_plustwo').checked;
            const ugChecked = document.getElementById('course_ug').checked;
            const pgChecked = document.getElementById('course_pg').checked;
            const courseTypeEl = document.getElementById('course_type');
            
            // Show/hide SSLC back year
            const sslcRow = document.getElementById('sslc_back_year_row');
            const sslcBackYear = document.getElementById('sslc_back_year');
            if (sslcChecked) {
                sslcRow.style.display = 'block';
                sslcBackYear.required = true;
            } else {
                sslcRow.style.display = 'none';
                sslcBackYear.required = false;
                sslcBackYear.value = '';
            }
            
            // Show/hide Plus Two back year and subject (only if Plus Two is checked)
            const plustwoRow = document.getElementById('plustwo_back_year_row');
            const plustwoBackYear = document.getElementById('plustwo_back_year');
            const plustwoSubject = document.getElementById('plustwo_subject');
            if (plustwoChecked) {
                plustwoRow.style.display = 'block';
                plustwoBackYear.required = true;
                if (plustwoSubject) {
                    plustwoSubject.required = true;
                }
                updatePlusTwoBackYear(); // populate options whenever Plus Two is selected (this also calls updateDegreeBackYear)
            } else {
                plustwoRow.style.display = 'none';
                plustwoBackYear.required = false;
                plustwoBackYear.value = '';
                if (plustwoSubject) {
                    plustwoSubject.required = false;
                    plustwoSubject.value = '';
                }
                // Update Degree Back Year when Plus Two is unchecked
                updateDegreeBackYear();
            }
            
            // Show/hide university fields (for UG/PG)
            const universityRow = document.getElementById('university_row');
            const courseNameRow = document.getElementById('course_name_row');
            const courseNameInput = document.getElementById('edumaster_course_name');
            const degreeBackYearRow = document.getElementById('degree_back_year_row');
            const degreeBackYearSelect = document.getElementById('degree_back_year');

            // If only one of UG/PG is selected, auto-set course type to match
            if (courseTypeEl) {
                if (ugChecked && !pgChecked) {
                    courseTypeEl.value = 'UG';
                } else if (pgChecked && !ugChecked) {
                    courseTypeEl.value = 'PG';
                }
            }

            // If a course is unchecked, clear mismatched course type
            if (!ugChecked && courseTypeEl && courseTypeEl.value === 'UG') {
                courseTypeEl.value = '';
            }
            if (!pgChecked && courseTypeEl && courseTypeEl.value === 'PG') {
                courseTypeEl.value = '';
            }

            if (ugChecked || pgChecked) {
                universityRow.style.display = 'block';
                courseNameRow.style.display = 'block';
                document.getElementById('university_id').required = true;
                document.getElementById('course_type').required = true;
                courseNameInput.required = true;
                // Degree Back Year shows only when a Course Type is selected
                if (degreeBackYearRow && degreeBackYearSelect) {
                    if (courseTypeEl && courseTypeEl.value) {
                        degreeBackYearRow.style.display = 'block';
                        degreeBackYearSelect.required = true;
                        updateDegreeBackYear();
                    } else {
                        degreeBackYearRow.style.display = 'none';
                        degreeBackYearSelect.required = false;
                        degreeBackYearSelect.value = '';
                    }
                }
            } else {
                universityRow.style.display = 'none';
                courseNameRow.style.display = 'none';
                document.getElementById('university_id').required = false;
                document.getElementById('course_type').required = false;
                courseNameInput.required = false;
                document.getElementById('university_id').value = '';
                document.getElementById('course_type').value = '';
                courseNameInput.value = '';
                if (degreeBackYearRow && degreeBackYearSelect) {
                    degreeBackYearRow.style.display = 'none';
                    degreeBackYearSelect.required = false;
                    degreeBackYearSelect.value = '';
                }
            }
            
            // Show/hide document upload fields
            const sslcCertRow = document.getElementById('sslc_certificate_row');
            const sslcCertInput = document.getElementById('sslc_certificate');
            if (plustwoChecked) {
                sslcCertRow.style.display = 'block';
                sslcCertInput.required = true;
            } else {
                sslcCertRow.style.display = 'none';
                sslcCertInput.required = false;
                sslcCertInput.value = '';
                document.getElementById('sslc_certificate_preview').innerHTML = '';
            }
            
            const plustwoCertRow = document.getElementById('plustwo_certificate_row');
            const plustwoCertInput = document.getElementById('plustwo_certificate');
            if (ugChecked) {
                plustwoCertRow.style.display = 'block';
                plustwoCertInput.required = true;
            } else {
                plustwoCertRow.style.display = 'none';
                plustwoCertInput.required = false;
                plustwoCertInput.value = '';
                document.getElementById('plustwo_certificate_preview').innerHTML = '';
            }
            
            const ugCertRow = document.getElementById('ug_certificate_row');
            const ugCertInput = document.getElementById('ug_certificate');
            if (pgChecked) {
                ugCertRow.style.display = 'block';
                ugCertInput.required = true;
            } else {
                ugCertRow.style.display = 'none';
                ugCertInput.required = false;
                ugCertInput.value = '';
                document.getElementById('ug_certificate_preview').innerHTML = '';
            }
            
            // After handling course selection, update degree back year visibility based on university
            if (ugChecked || pgChecked) {
                handleUniversityChange();
            }
        }
        
        // Update Plus Two back year based on SSLC back year
        function updatePlusTwoBackYear() {
            const sslcBackYear = document.getElementById('sslc_back_year').value;
            const plustwoBackYear = document.getElementById('plustwo_back_year');
            const sslcChecked = document.getElementById('course_sslc').checked;
            const previousValue = plustwoBackYear.value;
            
            // If SSLC is checked and has a back year, calculate Plus Two as 2 years after SSLC
            if (sslcChecked && sslcBackYear) {
                const minYear = parseInt(sslcBackYear) + 2;
                const maxYear = new Date().getFullYear();
                
                plustwoBackYear.innerHTML = '<option value="">Select Back Year</option>';
                for (let year = minYear; year <= maxYear; year++) {
                    const option = document.createElement('option');
                    option.value = year;
                    option.textContent = year;
                    plustwoBackYear.appendChild(option);
                }
            } else {
                // If SSLC is not checked, show all years from 2018
                const minYear = 2018;
                const maxYear = new Date().getFullYear();
                
                plustwoBackYear.innerHTML = '<option value="">Select Back Year</option>';
                for (let year = minYear; year <= maxYear; year++) {
                    const option = document.createElement('option');
                    option.value = year;
                    option.textContent = year;
                    plustwoBackYear.appendChild(option);
                }
            }
            
            // Preserve selection (avoid resetting when unrelated fields change)
            if (previousValue && Array.from(plustwoBackYear.options).some(o => o.value === previousValue)) {
                plustwoBackYear.value = previousValue;
            }
        
            // Update Degree Back Year when Plus Two Back Year changes
            updateDegreeBackYear();
        }
        
        // Update Degree Back Year based on Plus Two Back Year (2 years after)
        function updateDegreeBackYear() {
            const plustwoBackYear = document.getElementById('plustwo_back_year').value;
            const degreeBackYearSelect = document.getElementById('degree_back_year');
            const ugChecked = document.getElementById('course_ug').checked;
            const pgChecked = document.getElementById('course_pg').checked;
            const previousValue = degreeBackYearSelect ? degreeBackYearSelect.value : '';
            
            if (!degreeBackYearSelect) return;
            
            // Only update when UG/PG is checked
            if (ugChecked || pgChecked) {
                const maxYear = new Date().getFullYear();
                const minYear = plustwoBackYear ? (parseInt(plustwoBackYear) + 2) : 2018;
                
                degreeBackYearSelect.innerHTML = '<option value="">Select Back Year</option>';
                for (let year = minYear; year <= maxYear; year++) {
                    const option = document.createElement('option');
                    option.value = year;
                    option.textContent = year;
                    degreeBackYearSelect.appendChild(option);
                }
            }
            
            // Preserve selection (avoid losing it when options rebuild)
            if (previousValue && Array.from(degreeBackYearSelect.options).some(o => o.value === previousValue)) {
                degreeBackYearSelect.value = previousValue;
            }
        }
        
        // Handle University selection change
        function handleUniversityChange() {
            const universityId = document.getElementById('university_id').value;
            const ugChecked = document.getElementById('course_ug').checked;
            const pgChecked = document.getElementById('course_pg').checked;
            
            // Degree Back Year is controlled by UG/PG selection; on university change we just keep options in sync
            const degreeBackYearRow = document.getElementById('degree_back_year_row');
            const degreeBackYearSelect = document.getElementById('degree_back_year');
            
            if (degreeBackYearRow && degreeBackYearSelect) {
                if (ugChecked || pgChecked) {
                    updateDegreeBackYear(); // Keep options based on Plus Two Back Year
                }
            }
        }

        function loadSavedData() {
            const savedData = localStorage.getItem(STORAGE_KEY);
            if (savedData) {
                try {
                    const data = JSON.parse(savedData);
                    Object.keys(data).forEach(key => {
                        if (key === 'selected_courses') {
                            // Handle checkboxes
                            const checkboxes = document.querySelectorAll('input[name="selected_courses[]"]');
                            checkboxes.forEach(cb => {
                                if (data.selected_courses && data.selected_courses.includes(cb.value)) {
                                    cb.checked = true;
                                }
                            });
                            handleCourseSelection();
                        } else {
                            const element = document.querySelector(`[name="${key}"]`);
                            if (element && element.type !== 'file') {
                                element.value = data[key];
                            }
                        }
                    });
                    
                    // Handle course selection after loading
                    if (data.selected_courses) {
                        handleCourseSelection();
                    }
                    
                    // Update Degree Back Year if Plus Two Back Year is loaded
                    if (data.plustwo_back_year) {
                        setTimeout(() => {
                            updateDegreeBackYear();
                        }, 100);
                    }
                } catch (e) {
                    console.error('Error loading saved data:', e);
                }
            }
        }

        function saveFormData() {
            const formData = new FormData(document.getElementById('registrationForm'));
            const data = {};
            
            // Handle checkboxes
            const selectedCourses = [];
            document.querySelectorAll('input[name="selected_courses[]"]:checked').forEach(cb => {
                selectedCourses.push(cb.value);
            });
            data.selected_courses = selectedCourses;
            
            for (let [key, value] of formData.entries()) {
                if (key !== 'lead_id' && key !== '_token' && key !== 'selected_courses[]') {
                    data[key] = value;
                }
            }
            localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
        }

        function clearSavedData() {
            localStorage.removeItem(STORAGE_KEY);
        }

        function setupAutoSave() {
            const form = document.getElementById('registrationForm');
            form.addEventListener('input', saveFormData);
            form.addEventListener('change', saveFormData);
            document.getElementById('nextBtn').addEventListener('click', saveFormData);
            document.getElementById('prevBtn').addEventListener('click', saveFormData);
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadSavedData();
            setupAutoSave();
            updateStepDisplay();
            // Ensure initial state (shows/hides and populates dropdowns)
            handleCourseSelection();
            
            // Add event listeners for course checkboxes
            document.querySelectorAll('input[name="selected_courses[]"]').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    handleCourseSelection();
                    saveFormData();
                });
            });
            
            // Add event listener for SSLC back year change
            document.getElementById('sslc_back_year').addEventListener('change', function() {
                // Only update Plus Two back year if Plus Two is checked
                if (document.getElementById('course_plustwo').checked) {
                updatePlusTwoBackYear();
                }
                saveFormData();
            });
            
            // Add event listener for SSLC checkbox change
            document.getElementById('course_sslc').addEventListener('change', function() {
                // If SSLC is unchecked, update Plus Two back year options
                if (document.getElementById('course_plustwo').checked) {
                    updatePlusTwoBackYear();
                }
            });
            
            // Add event listener for Plus Two back year change to update Degree Back Year
            document.getElementById('plustwo_back_year').addEventListener('change', function() {
                updateDegreeBackYear();
                saveFormData();
            });
            
            // Add change event listener to Course Type select
            document.getElementById('course_type').addEventListener('change', function() {
                handleCourseSelection();
                saveFormData();
                this.classList.remove('is-invalid');
            });
            
            // Add change event listener to University select
            document.getElementById('university_id').addEventListener('change', function() {
                handleUniversityChange();
                saveFormData();
                this.classList.remove('is-invalid');
            });
            
            // Add click event listener to next button
            document.getElementById('nextBtn').addEventListener('click', function(e) {
                e.preventDefault();
                if (!validateCurrentStep()) {
                    return false;
                }
                changeStep(1);
            });
        });

        function updateStepDisplay() {
            document.querySelectorAll('.form-step').forEach(step => step.classList.remove('active'));
            document.getElementById(`formStep${currentStep}`).classList.add('active');
            
            const progress = (currentStep / totalSteps) * 100;
            document.getElementById('progressBar').style.width = progress + '%';
            
            document.querySelectorAll('.step-indicator').forEach((indicator, index) => {
                indicator.classList.remove('active', 'completed');
                if (index + 1 < currentStep) {
                    indicator.classList.add('completed');
                } else if (index + 1 === currentStep) {
                    indicator.classList.add('active');
                }
            });
            
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const submitBtn = document.getElementById('submitBtn');
            
            [prevBtn, nextBtn, submitBtn].forEach(btn => btn.style.display = 'none');
            
            if (currentStep === 1) {
                nextBtn.style.display = 'inline-block';
            } else if (currentStep === 2) {
                prevBtn.style.display = 'inline-block';
                nextBtn.style.display = 'inline-block';
            } else if (currentStep === 3) {
                prevBtn.style.display = 'inline-block';
                nextBtn.style.display = 'inline-block';
            } else if (currentStep === 4) {
                prevBtn.style.display = 'inline-block';
                submitBtn.style.display = 'inline-block';
            }
            if (typeof window.applyTermsCheckboxVisibility === 'function') window.applyTermsCheckboxVisibility();
        }

        function changeStep(direction) {
            const nextStep = currentStep + direction;
            
            if (direction > 0) {
                if (!validateCurrentStep()) {
                    return false;
                }
            }
            
            if (nextStep >= 1 && nextStep <= totalSteps) {
                currentStep = nextStep;
                updateStepDisplay();
            }
        }

        function validateCurrentStep() {
            const currentStepElement = document.getElementById(`formStep${currentStep}`);
            const requiredFields = currentStepElement.querySelectorAll('[required]');
            
            // Validate all required fields in current step
            for (let field of requiredFields) {
                if (field.type === 'checkbox') {
                    // For checkboxes, check if at least one is checked
                    const checkboxes = currentStepElement.querySelectorAll('input[type="checkbox"][required]');
                    let atLeastOneChecked = false;
                    checkboxes.forEach(cb => {
                        if (cb.checked) atLeastOneChecked = true;
                    });
                    if (!atLeastOneChecked) {
                        showAlert('Please select at least one course.', 'warning');
                        return false;
                    }
                } else if (!field.value || field.value.trim() === '') {
                    field.classList.add('is-invalid');
                    showAlert(`Please fill in the required field: ${field.name}`, 'warning');
                    return false;
                } else {
                    field.classList.remove('is-invalid');
                }
            }
            
            // Special validation for step 3 (Programme Details)
            if (currentStep === 3) {
                // Check if at least one course is selected
                const selectedCourses = document.querySelectorAll('input[name="selected_courses[]"]:checked');
                if (selectedCourses.length === 0) {
                    showAlert('Please select at least one course.', 'warning');
                    return false;
                }
                
                // Validate SSLC back year if SSLC is selected
                const sslcChecked = document.getElementById('course_sslc').checked;
                if (sslcChecked) {
                    const sslcBackYear = document.getElementById('sslc_back_year').value;
                    if (!sslcBackYear) {
                        document.getElementById('sslc_back_year').classList.add('is-invalid');
                        showAlert('Please select SSLC back year.', 'warning');
                        return false;
                    }
                }
                
                // Validate Plus Two back year if Plus Two is selected
                const plustwoChecked = document.getElementById('course_plustwo').checked;
                if (plustwoChecked) {
                    const plustwoBackYear = document.getElementById('plustwo_back_year').value;
                    if (!plustwoBackYear) {
                        document.getElementById('plustwo_back_year').classList.add('is-invalid');
                        showAlert('Please select Plus Two back year.', 'warning');
                        return false;
                    }
                }
                
                // Validate university fields if UG or PG is selected
                const ugChecked = document.getElementById('course_ug').checked;
                const pgChecked = document.getElementById('course_pg').checked;
                if (ugChecked || pgChecked) {
                    const universityId = document.getElementById('university_id').value;
                    const courseType = document.getElementById('course_type').value;
                    const courseName = document.getElementById('edumaster_course_name').value;
                    const degreeBackYearSelect = document.getElementById('degree_back_year');
                    
                    if (!universityId) {
                        document.getElementById('university_id').classList.add('is-invalid');
                        showAlert('Please select a Board/University.', 'warning');
                        return false;
                    }
                    
                    if (!courseType) {
                        document.getElementById('course_type').classList.add('is-invalid');
                        showAlert('Please select a Course Type.', 'warning');
                        return false;
                    }
                    
                    if (!courseName || courseName.trim() === '') {
                        document.getElementById('edumaster_course_name').classList.add('is-invalid');
                        showAlert('Please enter a Course Name.', 'warning');
                        return false;
                    }
                    
                    if (universityId == '1' && degreeBackYearSelect) {
                        if (!degreeBackYearSelect.value || degreeBackYearSelect.value.trim() === '') {
                            degreeBackYearSelect.classList.add('is-invalid');
                            showAlert('Please select a Degree Back Year.', 'warning');
                            return false;
                        } else {
                            degreeBackYearSelect.classList.remove('is-invalid');
                        }
                    }
                }
            }
            
            // Special validation for step 4 (Documents)
            if (currentStep === 4) {
                const plustwoChecked = document.getElementById('course_plustwo').checked;
                const ugChecked = document.getElementById('course_ug').checked;
                const pgChecked = document.getElementById('course_pg').checked;
                
                // Check required files
                const requiredFiles = ['passport_photo', 'adhar_front', 'adhar_back', 'signature'];
                
                if (plustwoChecked) {
                    requiredFiles.push('sslc_certificate');
                }
                
                if (ugChecked) {
                    requiredFiles.push('plustwo_certificate');
                }
                
                if (pgChecked) {
                    requiredFiles.push('ug_certificate');
                }
                
                for (let fieldName of requiredFiles) {
                    const field = document.getElementById(fieldName);
                    if (field && (!field.files || field.files.length === 0)) {
                        showAlert(`Please upload the required file: ${fieldName}`, 'warning');
                        return false;
                    }
                }
            }
            
            return true;
        }

        function setupFileUpload(inputId) {
            const input = document.getElementById(inputId);
            if (!input) return;
            
            const preview = document.getElementById(inputId + '_preview');
            if (!preview) return;
            
            input.addEventListener('change', function(e) {
                handleFileUpload(e, preview, inputId);
            });
            
            const uploadArea = input.previousElementSibling;
            if (uploadArea) {
                uploadArea.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    uploadArea.classList.add('dragover');
                });
                
                uploadArea.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    uploadArea.classList.remove('dragover');
                });
                
                uploadArea.addEventListener('drop', function(e) {
                    e.preventDefault();
                    uploadArea.classList.remove('dragover');
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        input.files = files;
                        handleFileUpload({ target: { files: files } }, preview, inputId);
                    }
                });
            }
        }

        function handleFileUpload(event, preview, inputId) {
            const file = event.target.files[0] || (event.target.files && event.target.files[0]);
            if (!file) return;
            
            if (file.size > 1 * 1024 * 1024) {
                showAlert('File size must be less than 1MB.', 'danger');
                return;
            }
            
            const allowedTypes = inputId === 'passport_photo' || inputId === 'signature' 
                ? ['image/jpeg', 'image/jpg', 'image/png']
                : ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
            
            if (!allowedTypes.includes(file.type)) {
                showAlert('Invalid file type. Please upload a valid file.', 'danger');
                return;
            }
            
            const fileInfo = document.createElement('div');
            fileInfo.className = 'file-preview-item';
            fileInfo.innerHTML = `
                <div class="file-info">
                    <i class="fas fa-file-${file.type.includes('image') ? 'image' : 'pdf'}"></i>
                    <span>${file.name}</span>
                </div>
                <div class="remove-file" onclick="removeFile('${inputId}')">
                    <i class="fas fa-times"></i>
                </div>
            `;
            
            preview.innerHTML = '';
            preview.appendChild(fileInfo);
        }

        function removeFile(inputId) {
            const input = document.getElementById(inputId);
            if (input) {
                input.value = '';
            }
            const preview = document.getElementById(inputId + '_preview');
            if (preview) {
                preview.innerHTML = '';
            }
        }

        // Setup file uploads
        setupFileUpload('sslc_certificate');
        setupFileUpload('plustwo_certificate');
        setupFileUpload('ug_certificate');
        setupFileUpload('passport_photo');
        setupFileUpload('adhar_front');
        setupFileUpload('adhar_back');
        setupFileUpload('signature');
        setupFileUpload('other_documents');

        // Form submission
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!validateCurrentStep()) {
                return;
            }
            
            const formData = new FormData(this);
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
            submitBtn.disabled = true;
            submitBtn.classList.add('loading');
            
            fetch('{{ route("public.lead.edumaster.register.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(async response => {
                const isJson = response.headers.get('content-type')?.includes('application/json');
                const data = isJson ? await response.json() : null;
                if (!response.ok) {
                    if (response.status === 422 && data && data.errors) {
                        const firstError = Object.values(data.errors)[0]?.[0] || 'Validation failed.';
                        throw new Error(firstError);
                    }
                    throw new Error(data?.message || 'Request failed.');
                }
                return data;
            })
            .then(data => {
                if (data.success) {
                    clearSavedData();
                    showAlert(data.message, 'success');
                    setTimeout(() => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        }
                    }, 2000);
                } else {
                    showAlert(data.message || 'An error occurred. Please try again.', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert(error.message || 'An error occurred while submitting the form. Please try again.', 'danger');
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                submitBtn.classList.remove('loading');
            });
        });

        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const form = document.getElementById('registrationForm');
            form.insertBefore(alertDiv, form.firstChild);
            
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 10000);
        }
    </script>
</body>
</html>
