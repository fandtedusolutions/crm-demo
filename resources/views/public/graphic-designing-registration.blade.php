<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Diploma in Graphic Designing Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .wizard-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 15px;
        }
        .wizard-header {
            background: linear-gradient(225deg, #abb7ed 0%, #787879 100%);
            color: white;
            padding: 40px 30px;
            border-radius: 15px 15px 0 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .wizard-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        .wizard-header h2 {
            position: relative;
            z-index: 1;
            margin-bottom: 10px;
            font-weight: 700;
        }
        .wizard-header p {
            position: relative;
            z-index: 1;
            opacity: 0.9;
        }
        .wizard-body {
            background: white;
            padding: 50px 40px;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            position: relative;
        }
        .wizard-body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2, #667eea);
            background-size: 200% 100%;
            animation: shimmer 3s ease-in-out infinite;
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            font-weight: bold;
            color: #6c757d;
        }
        .step.active {
            background: #667eea;
            color: white;
        }
        .step.completed {
            background: #28a745;
            color: white;
        }
        .step-line {
            width: 50px;
            height: 2px;
            background: #e9ecef;
            margin-top: 19px;
        }
        .step-line.completed {
            background: #28a745;
        }
        .form-step {
            display: none;
        }
        .form-step.active {
            display: block;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        .required {
            color: #dc3545;
        }
        .btn-wizard {
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 25px;
        }
        .file-upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .file-upload-area:hover {
            border-color: #667eea;
            background-color: #f8f9ff;
        }
        .file-upload-area.dragover {
            border-color: #667eea;
            background-color: #f8f9ff;
        }
        .file-preview {
            margin-top: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
            display: none;
        }
        .progress-bar {
            height: 4px;
            background: #667eea;
            border-radius: 2px;
            transition: width 0.3s ease;
        }
        .alert-custom {
            border-radius: 10px;
            border: none;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .wizard-container {
                padding: 10px;
            }
            .wizard-header {
                padding: 30px 20px;
            }
            .wizard-body {
                padding: 30px 20px;
            }
            .step-indicator {
                flex-wrap: wrap;
                gap: 10px;
            }
            .step-line {
                display: none;
            }
        }
        
        /* Enhanced Form Styles */
        .form-control {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .form-control.is-invalid {
            border-color: #dc3545;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        /* Pre-filled data styling */
        .pre-filled-info {
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            border: 1px solid #bbdefb;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .pre-filled-info h6 {
            color: #1976d2;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }
        .info-item i {
            color: #667eea;
            margin-right: 10px;
            width: 16px;
        }
        
        /* Enhanced file upload */
        .file-upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 12px;
            padding: 40px 20px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            background: #fafbfc;
        }
        .file-upload-area:hover {
            border-color: #667eea;
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%);
            transform: translateY(-2px);
        }
        .file-upload-area.dragover {
            border-color: #667eea;
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%);
            transform: scale(1.02);
        }
        
        /* Enhanced buttons */
        .btn-wizard {
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 25px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .btn-wizard:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .btn-wizard:active {
            transform: translateY(0);
        }
        
        /* Loading animation */
        .btn-wizard.loading {
            pointer-events: none;
        }
        .btn-wizard.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* NIOS Logo Styles */
        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .nios-logo {
            max-height: 100px;
            max-width: 200px;
            object-fit: contain;
            /* filter: brightness(0) invert(1); */
            opacity: 0.9;
            transition: all 0.3s ease;
        }
        .nios-logo:hover {
            opacity: 1;
            transform: scale(1.05);
        }
        
        /* Responsive logo */
        @media (max-width: 768px) {
            .nios-logo {
                max-height: 60px;
                max-width: 150px;
            }
        }
    </style>
</head>
<body style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; padding: 20px 0;">
    <div class="wizard-container">
        <div class="wizard-header">
            <div class="logo-container mb-3">
                <img src="{{ asset('storage/skill-park-logo.png') }}" alt="Skill Park Logo" class="nios-logo">
            </div>
            <h2><i class="fas fa-graduation-cap me-2"></i>Diploma in Graphic Designing Registration</h2>
            <p class="mb-0">Complete your registration in a few simple steps</p>
        </div>
        
        <div class="wizard-body">
            <!-- Progress Bar -->
            <div class="progress mb-4" style="height: 4px;">
                <div class="progress-bar" id="progressBar" style="width: 25%"></div>
            </div>
            
            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step active" id="step1">1</div>
                <div class="step-line"></div>
                <div class="step" id="step2">2</div>
                <div class="step-line"></div>
                <div class="step" id="step3">3</div>
                <div class="step-line"></div>
                <div class="step" id="step4">4</div>
            </div>
            
            <!-- Alert Messages -->
            <div id="alertContainer"></div>
            
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
            
            <!-- Form -->
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
                                <div class="mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gender" id="gender_male" value="male" required>
                                        <label class="form-check-label" for="gender_male">Male</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gender" id="gender_female" value="female" required>
                                        <label class="form-check-label" for="gender_female">Female</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Are you employed? <span class="required">*</span></label>
                                <div class="mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_employed" id="employed_yes" value="1" required>
                                        <label class="form-check-label" for="employed_yes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_employed" id="employed_no" value="0" required>
                                        <label class="form-check-label" for="employed_no">No</label>
                                    </div>
                                </div>
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
                                        <select class="form-control" name="father_contact_code" required>
                                            @foreach($countryCodes as $code => $country)
                                                <option value="{{ $code }}">{{ $code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-8">
                                        <input type="tel" class="form-control" name="father_contact_number" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Mother's Contact No. <span class="required">*</span></label>
                                <div class="row">
                                    <div class="col-4">
                                        <select class="form-control" name="mother_contact_code" required>
                                            @foreach($countryCodes as $code => $country)
                                                <option value="{{ $code }}">{{ $code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-8">
                                        <input type="tel" class="form-control" name="mother_contact_number" required>
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
                                <textarea class="form-control" name="street" rows="3" required></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Locality <span class="required">*</span></label>
                                <input type="text" class="form-control" name="locality" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">District <span class="required">*</span></label>
                                <input type="text" class="form-control" name="district" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">State <span class="required">*</span></label>
                                <input type="text" class="form-control" name="state" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Post Office <span class="required">*</span></label>
                                <input type="text" class="form-control" name="post_office" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Pin Code <span class="required">*</span></label>
                                <input type="text" class="form-control" name="pin_code" pattern="[0-9]{6}" maxlength="6" inputmode="numeric" required>
                                <small class="form-text text-muted">Enter 6-digit pin code</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Step 3: Programme Details -->
                <div class="form-step" id="formStep3">
                    <h4 class="mb-4"><i class="fas fa-graduation-cap me-2"></i>Programme Details</h4>
                    
                    <div class="row">
                        @include('public.partials.admin-course-type-field')
                        <div class="col-md-6">
                            @if($course && ($course->is_online || $course->is_offline))
                            <div class="form-group">
                                <label class="form-label">{{ isset($courseTypes) && $courseTypes->count() > 0 ? 'Mode of Study' : 'Course Type' }} <span class="required">*</span></label>
                                <select class="form-control" name="programme_type" id="programme_type" required>
                                    <option value="">Select Course Type</option>
                                    @if($course->is_online)
                                        <option value="online">Online</option>
                                    @endif
                                    @if($course->is_offline)
                                        <option value="offline">Offline</option>
                                    @endif
                                </select>
                            </div>
                            @else
                            <div class="form-group">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Not Have Active Course Type
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            @if($course && $course->is_offline && $offlinePlaces && $offlinePlaces->count() > 0)
                            <div class="form-group" id="location_group" style="display: none;">
                                <label class="form-label">Choose Please <span class="required">*</span></label>
                                <select class="form-control" name="location" id="location">
                                    <option value="">Select Location</option>
                                    @foreach($offlinePlaces as $place)
                                        <option value="{{ $place->name }}">{{ $place->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            @if($course && $course->needs_time)
                            <div class="form-group" id="class_time_group" style="display: none;">
                                <label class="form-label">Class Time <span class="required">*</span></label>
                                <select class="form-control" name="class_time_id" id="class_time_id">
                                    <option value="">Select Class Time</option>
                                </select>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Step 4: Upload Documents -->
                <div class="form-step" id="formStep4">
                    <h4 class="mb-4"><i class="fas fa-file-upload me-2"></i>Upload Documents</h4>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Recent Passport Size Photograph <span class="required">*</span></label>
                                <div class="file-upload-area" onclick="document.getElementById('passport_photo').click()">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <p class="mb-0">Click to upload or drag & drop</p>
                                    <small class="text-muted">JPG, PNG (Max 2MB)</small>
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
                                    <small class="text-muted">PDF, JPG, PNG (Max 2MB)</small>
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
                                    <small class="text-muted">PDF, JPG, PNG (Max 2MB)</small>
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
                                    <small class="text-muted">JPG, PNG (Max 2MB)</small>
                                </div>
                                <input type="file" id="signature" name="signature" accept=".jpg,.jpeg,.png" required style="display: none;">
                                <div class="file-preview" id="signature_preview"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Secondary (10th) Certificate <span class="required">*</span></label>
                                <div class="file-upload-area" onclick="document.getElementById('sslc_certificate').click()">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <p class="mb-0">Click to upload or drag & drop</p>
                                    <small class="text-muted">PDF, JPG, PNG (Max 2MB)</small>
                                </div>
                                <input type="file" id="sslc_certificate" name="sslc_certificate" accept=".pdf,.jpg,.jpeg,.png" required style="display: none;">
                                <div class="file-preview" id="sslc_certificate_preview"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Senior Secondary (12th) Certificate <span class="required">*</span></label>
                                <div class="file-upload-area" onclick="document.getElementById('plus_two_certificate').click()">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <p class="mb-0">Click to upload or drag & drop</p>
                                    <small class="text-muted">PDF, JPG, PNG (Max 2MB)</small>
                                </div>
                                <input type="file" id="plus_two_certificate" name="plus_two_certificate" accept=".pdf,.jpg,.jpeg,.png" required style="display: none;">
                                <div class="file-preview" id="plus_two_certificate_preview"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Graduation Certificate</label>
                                <div class="file-upload-area" onclick="document.getElementById('graduation_certificate').click()">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <p class="mb-0">Click to upload or drag & drop</p>
                                    <small class="text-muted">PDF, JPG, PNG (Max 2MB)</small>
                                </div>
                                <input type="file" id="graduation_certificate" name="graduation_certificate" accept=".pdf,.jpg,.jpeg,.png" style="display: none;">
                                <div class="file-preview" id="graduation_certificate_preview"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Post-Graduation Certificate</label>
                                <div class="file-upload-area" onclick="document.getElementById('post_graduation_certificate').click()">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <p class="mb-0">Click to upload or drag & drop</p>
                                    <small class="text-muted">PDF, JPG, PNG (Max 2MB)</small>
                                </div>
                                <input type="file" id="post_graduation_certificate" name="post_graduation_certificate" accept=".pdf,.jpg,.jpeg,.png" style="display: none;">
                                <div class="file-preview" id="post_graduation_certificate_preview"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Other Relevant Documents</label>
                                <div class="file-upload-area" onclick="document.getElementById('other_relevant_documents').click()">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <p class="mb-0">Click to upload or drag & drop</p>
                                    <small class="text-muted">PDF, JPG, PNG (Max 2MB)</small>
                                </div>
                                <input type="file" id="other_relevant_documents" name="other_relevant_documents" accept=".pdf,.jpg,.jpeg,.png" style="display: none;">
                                <div class="file-preview" id="other_relevant_documents_preview"></div>
                            </div>
                        </div>
                    </div>
                </div>

                @include('public.partials.terms-and-conditions')
                    <!-- Navigation Buttons -->
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-outline-secondary btn-wizard" id="prevBtn" onclick="changeStep(-1)" style="display: none;">
                            <i class="fas fa-arrow-left me-2"></i>Previous
                        </button>
                        <div class="ms-auto">
                            <button type="button" class="btn btn-primary btn-wizard" id="nextBtn" onclick="changeStep(1)" style="display: inline-block;">
                                Next<i class="fas fa-arrow-right ms-2"></i>
                            </button>
                            <button type="submit" class="btn btn-success btn-wizard" id="submitBtn" style="display: none;">
                                <i class="fas fa-check me-2"></i>Submit Registration
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentStep = 1;
        const totalSteps = 4;
        const STORAGE_KEY = 'graphic_designing_form_data';

        // Load saved data on page load
        function loadSavedData() {
            const savedData = localStorage.getItem(STORAGE_KEY);
            if (savedData) {
                try {
                    const data = JSON.parse(savedData);
                    
                    // Fill form fields with saved data
                    Object.keys(data).forEach(key => {
                        const value = data[key];
                        
                        // Handle radio buttons
                        const radioButtons = document.querySelectorAll(`[name="${key}"][type="radio"]`);
                        if (radioButtons.length > 0) {
                            radioButtons.forEach(radio => {
                                if (radio.value === String(value)) {
                                    radio.checked = true;
                                }
                            });
                            return;
                        }
                        
                        // Handle checkboxes
                        const checkbox = document.querySelector(`[name="${key}"][type="checkbox"]`);
                        if (checkbox) {
                            checkbox.checked = value === '1' || value === 1 || value === true;
                            return;
                        }
                        
                        // Handle regular inputs and selects
                        const element = document.querySelector(`[name="${key}"]`);
                        if (element) {
                            if (element.type === 'file') {
                                // Skip file inputs as they can't be restored
                                return;
                            }
                            element.value = value;
                        }
                    });
                    
                    // After loading data, trigger programme type change to show/hide location
                    const programmeTypeSelect = document.getElementById('programme_type');
                    if (programmeTypeSelect && programmeTypeSelect.value) {
                        // Trigger change event to show/hide location field
                        programmeTypeSelect.dispatchEvent(new Event('change'));
                    }
                } catch (e) {
                    console.error('Error loading saved data:', e);
                }
            }
        }

        // Save form data to localStorage
        function saveFormData() {
            const form = document.getElementById('registrationForm');
            const formData = new FormData(form);
            const data = {};
            
            // Get all form elements
            const formElements = form.elements;
            
            for (let i = 0; i < formElements.length; i++) {
                const element = formElements[i];
                const name = element.name;
                
                if (!name || name === 'lead_id' || name === '_token') {
                    continue;
                }
                
                // Handle radio buttons - only save the checked one
                if (element.type === 'radio') {
                    if (element.checked) {
                        data[name] = element.value;
                    }
                }
                // Handle checkboxes
                else if (element.type === 'checkbox') {
                    data[name] = element.checked ? element.value : '0';
                }
                // Handle file inputs - skip them
                else if (element.type === 'file') {
                    continue;
                }
                // Handle other inputs (text, select, textarea, etc.)
                else {
                    data[name] = element.value;
                }
            }
            
            localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
        }

        // Clear saved data
        function clearSavedData() {
            localStorage.removeItem(STORAGE_KEY);
        }

        // Auto-save form data on input change
        function setupAutoSave() {
            const form = document.getElementById('registrationForm');
            
            // Save on input, change, and textarea events
            form.addEventListener('input', saveFormData);
            form.addEventListener('change', saveFormData);
            
            // Save when moving between steps
            document.getElementById('nextBtn').addEventListener('click', saveFormData);
            document.getElementById('prevBtn').addEventListener('click', saveFormData);
        }

        // Load saved data when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Setup programme type change handler first (before loading data)
            setupProgrammeTypeHandler();
            
            // Setup file upload handlers
            setupFileUploadHandlers();
            
            // Load saved data after handlers are set up
            loadSavedData();
            
            // Setup auto-save
            setupAutoSave();
            
            // Add input validation for pin code
            const pinCodeInput = document.querySelector('input[name="pin_code"]');
            if (pinCodeInput) {
                pinCodeInput.addEventListener('input', function(e) {
                    // Remove any non-numeric characters
                    e.target.value = e.target.value.replace(/[^0-9]/g, '');
                });
            }
        });
        
        // Function to handle programme type changes
        function setupProgrammeTypeHandler() {
            const programmeTypeSelect = document.getElementById('programme_type');
            const locationGroup = document.getElementById('location_group');
            const locationSelect = document.getElementById('location');
            const classTimeSelect = document.getElementById('class_time_id');
            const courseId = {{ $course ? $course->id : 'null' }};
            
            if (programmeTypeSelect) {
                programmeTypeSelect.addEventListener('change', function() {
                    const programmeType = this.value;
                    
                    // Show/hide location field
                    if (locationGroup && locationSelect) {
                        if (programmeType === 'offline') {
                            locationGroup.style.display = 'block';
                            locationSelect.setAttribute('required', 'required');
                        } else {
                            locationGroup.style.display = 'none';
                            locationSelect.removeAttribute('required');
                            locationSelect.value = '';
                        }
                    }
                    
                    // Fetch and filter class times by class_type
                    if (classTimeSelect && courseId) {
                        fetchClassTimes(courseId, programmeType);
                    }
                });
            }
        }
        
        // Function to fetch class times filtered by class_type
        function fetchClassTimes(courseId, classType) {
            const classTimeSelect = document.getElementById('class_time_id');
            const classTimeGroup = document.getElementById('class_time_group');
            if (!classTimeSelect || !classTimeGroup) return;
            
            // Clear existing options except the first one
            classTimeSelect.innerHTML = '<option value="">Select Class Time</option>';
            
            if (!classType) {
                classTimeGroup.style.display = 'none';
                classTimeSelect.removeAttribute('required');
                return;
            }
            
            // Fetch class times from API
            fetch(`/api/class-times/by-course/${courseId}?class_type=${classType}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        classTimeGroup.style.display = 'block';
                        classTimeSelect.setAttribute('required', 'required');
                        data.forEach(classTime => {
                            const option = document.createElement('option');
                            option.value = classTime.id;
                            const fromTime = new Date('2000-01-01 ' + classTime.from_time).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
                            const toTime = new Date('2000-01-01 ' + classTime.to_time).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
                            option.textContent = `${fromTime} - ${toTime}`;
                            classTimeSelect.appendChild(option);
                        });
                    } else {
                        classTimeGroup.style.display = 'none';
                        classTimeSelect.removeAttribute('required');
                    }
                })
                .catch(error => {
                    console.error('Error fetching class times:', error);
                    classTimeGroup.style.display = 'none';
                    classTimeSelect.removeAttribute('required');
                });
        }
        
        // Function to setup file upload handlers for all file inputs
        function setupFileUploadHandlers() {
            const fileInputs = [
                'passport_photo', 'adhar_front', 'adhar_back', 'signature',
                'sslc_certificate', 'plus_two_certificate', 'graduation_certificate',
                'post_graduation_certificate', 'other_relevant_documents'
            ];
            
            fileInputs.forEach(inputId => {
                const input = document.getElementById(inputId);
                if (input) {
                    input.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            const preview = document.getElementById(inputId + '_preview');
                            if (preview) {
                                preview.innerHTML = '<div class="alert alert-success mt-2"><i class="fas fa-check-circle me-2"></i>' + file.name + ' (' + (file.size / 1024).toFixed(2) + ' KB)</div>';
                            }
                        }
                    });
                }
            });
        }
        
        
        function changeStep(direction) {
            const currentStepElement = document.getElementById(`formStep${currentStep}`);
            const nextStep = currentStep + direction;
            
            if (direction > 0) {
                // Validate current step before proceeding
                if (!validateStep(currentStep)) {
                    return;
                }
            }
            
            if (nextStep >= 1 && nextStep <= totalSteps) {
                // Hide current step
                currentStepElement.classList.remove('active');
                document.getElementById(`step${currentStep}`).classList.remove('active');
                
                // Show next step
                currentStep = nextStep;
                document.getElementById(`formStep${currentStep}`).classList.add('active');
                document.getElementById(`step${currentStep}`).classList.add('active');
                
                // Update progress bar
                const progress = (currentStep / totalSteps) * 100;
                document.getElementById('progressBar').style.width = progress + '%';
                
                // Update buttons - hide all first, then show appropriate ones
                const prevBtn = document.getElementById('prevBtn');
                const nextBtn = document.getElementById('nextBtn');
                const submitBtn = document.getElementById('submitBtn');
                
                // Hide all buttons first
                if (prevBtn) prevBtn.style.display = 'none';
                if (nextBtn) nextBtn.style.display = 'none';
                if (submitBtn) submitBtn.style.display = 'none';
                
                // Show appropriate buttons based on current step
                if (currentStep === 1) {
                    // Step 1: Only show Next button
                    if (nextBtn) nextBtn.style.display = 'inline-block';
                } else if (currentStep === 2) {
                    // Step 2: Show Previous and Next buttons
                    if (prevBtn) prevBtn.style.display = 'inline-block';
                    if (nextBtn) nextBtn.style.display = 'inline-block';
                } else if (currentStep === 3) {
                    // Step 3: Show Previous and Next buttons
                    if (prevBtn) prevBtn.style.display = 'inline-block';
                    if (nextBtn) nextBtn.style.display = 'inline-block';
                } else if (currentStep === 4) {
                    // Step 4: Show Previous and Submit buttons
                    if (prevBtn) prevBtn.style.display = 'inline-block';
                    if (submitBtn) submitBtn.style.display = 'inline-block';
                }
            if (typeof window.applyTermsCheckboxVisibility === 'function') window.applyTermsCheckboxVisibility();
                
                // Mark previous steps as completed
                for (let i = 1; i < currentStep; i++) {
                    document.getElementById(`step${i}`).classList.add('completed');
                    document.querySelectorAll('.step-line')[i-1].classList.add('completed');
                }
            }
        }
        
        function validateStep(step) {
            const stepElement = document.getElementById(`formStep${step}`);
            const requiredFields = stepElement.querySelectorAll('[required]');
            let isValid = true;
            
            // Check file inputs (they should always be required in step 4)
            if (step === 4) {
                const fileFieldNames = {
                    'passport_photo': 'Passport photo',
                    'adhar_front': 'Aadhar front',
                    'adhar_back': 'Aadhar back',
                    'signature': 'Signature',
                    'birth_certificate': 'Birth certificate',
                    'sslc_certificate': 'SSLC certificate'
                };
                
                const requiredFileFields = ['passport_photo', 'adhar_front', 'adhar_back', 'signature'];
                
                // Add SSLC certificate if class is plustwo
                const classSelect = document.querySelector('select[name="class"]');
                if (classSelect && classSelect.value === 'plustwo') {
                    requiredFileFields.push('sslc_certificate');
                }
                
                for (let fieldName of requiredFileFields) {
                    const field = document.getElementById(fieldName);
                    if (field && (!field.files || field.files.length === 0)) {
                        const fieldLabel = fileFieldNames[fieldName] || fieldName;
                        showAlert(`Please upload the required file: ${fieldLabel}`, 'warning');
                        isValid = false;
                    }
                }
            }
            
            requiredFields.forEach(field => {
                if (field.type === 'file') {
                    // Skip file validation here as it's handled above
                    return;
                } else {
                    // Check text/select fields
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                }
            });
            
            if (!isValid) {
                showAlert('Please fill in all required fields.', 'danger');
            }
            
            return isValid;
        }
        
        function showAlert(message, type) {
            const alertContainer = document.getElementById('alertContainer');
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show alert-custom" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            alertContainer.innerHTML = alertHtml;
            
            // Auto dismiss after 10 seconds
            setTimeout(() => {
                const alert = alertContainer.querySelector('.alert');
                if (alert) {
                    alert.remove();
                }
            }, 10000);
        }
        
        // File upload handling
        document.querySelectorAll('input[type="file"]').forEach(input => {
            const uploadArea = input.previousElementSibling;
            
            // Handle file selection
            input.addEventListener('change', function() {
                handleFileSelect(this);
            });
            
            // Handle drag and drop
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('dragover');
            });
            
            uploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
            });
            
            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    input.files = files;
                    handleFileSelect(input);
                }
            });
        });
        
        function handleFileSelect(input) {
            const file = input.files[0];
            const preview = document.getElementById(input.name + '_preview');
            
            if (file) {
                // Validate file size (2MB limit)
                const maxSize = 2 * 1024 * 1024; // 2MB in bytes
                if (file.size > maxSize) {
                    showAlert(`File "${file.name}" is too large. Please select a file smaller than 2MB.`, 'danger');
                    input.value = '';
                    return;
                }
                
                // Validate file type
                const allowedTypes = input.accept.split(',').map(type => type.trim());
                const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
                const mimeType = file.type;
                
                // Check if file extension or MIME type is allowed
                const isExtensionAllowed = allowedTypes.includes(fileExtension);
                const isMimeTypeAllowed = allowedTypes.some(type => {
                    if (type === '.pdf') return mimeType === 'application/pdf';
                    if (type === '.jpg' || type === '.jpeg') return mimeType === 'image/jpeg';
                    if (type === '.png') return mimeType === 'image/png';
                    return false;
                });
                
                if (!isExtensionAllowed && !isMimeTypeAllowed) {
                    showAlert(`File type not allowed for "${file.name}". Please select a valid file type.`, 'danger');
                    input.value = '';
                    return;
                }
                
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                preview.innerHTML = `
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file me-2 text-success"></i>
                            <div>
                                <div class="fw-bold">${file.name}</div>
                                <small class="text-muted">${fileSize} MB</small>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile('${input.name}')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        }
        
        function removeFile(inputName) {
            const input = document.querySelector(`input[name="${inputName}"]`);
            const preview = document.getElementById(inputName + '_preview');
            
            input.value = '';
            preview.style.display = 'none';
        }
        
        // Form submission
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!validateStep(currentStep)) {
                return;
            }
            
            const formData = new FormData(this);
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
            submitBtn.disabled = true;
            submitBtn.classList.add('loading');
            
                    fetch('{{ route("public.lead.graphic-designing.register.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear saved form data on successful submission
                    clearSavedData();
                    showAlert(data.message, 'success');
                    
                    // Redirect to success page after a short delay
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
                showAlert('An error occurred while submitting the form. Please try again.', 'danger');
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                submitBtn.classList.remove('loading');
            });
        });
    </script>
</body>
</html>


