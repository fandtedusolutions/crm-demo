<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Diploma in Hospital Administration Registration</title>
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
        }
        .progress-container {
            margin-bottom: 40px;
        }
        .progress {
            height: 8px;
            border-radius: 10px;
            background: #f0f0f0;
        }
        .progress-bar {
            background: #829b99;
            border-radius: 10px;
            transition: width 0.3s ease;
        }
        .step-indicators {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .step-indicator {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
        }
        .step-indicator:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 15px;
            left: 60%;
            right: -40%;
            height: 2px;
            background: #e0e0e0;
            z-index: 1;
        }
        .step-indicator.active:not(:last-child)::after {
            background: #829b99;
        }
        .step-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #e0e0e0;
            color: #999;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
            position: relative;
            z-index: 2;
            transition: all 0.3s ease;
        }
        .step-indicator.active .step-circle {
            background: #829b99;
            color: white;
        }
        .step-indicator.completed .step-circle {
            background: #28a745;
            color: white;
        }
        .step-label {
            margin-top: 8px;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        .step-indicator.active .step-label {
            color: #829b99;
            font-weight: 600;
        }
        .form-step {
            display: none;
        }
        .form-step.active {
            display: block;
        }
        .form-group {
            margin-bottom: 25px;
        }
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            display: block;
        }
        .required {
            color: #dc3545;
        }
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #829b99;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-wizard {
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: #829b99;
            border: none;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
        }
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
        }
        .btn-secondary {
            background: #6c757d;
            border: none;
        }
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
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
            color: #1976d2;
            margin-right: 10px;
            width: 16px;
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
        .file-upload-area:hover {
            border-color: #829b99;
            background: #f8f9ff;
        }
        .file-upload-area.dragover {
            border-color: #829b99;
            background: #f0f4ff;
        }
        .file-preview {
            margin-top: 15px;
        }
        .file-preview-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 10px 15px;
            margin-bottom: 10px;
        }
        .file-preview-item .file-info {
            display: flex;
            align-items: center;
        }
        .file-preview-item .file-info i {
            color: #829b99;
            margin-right: 10px;
        }
        .file-preview-item .remove-file {
            color: #dc3545;
            cursor: pointer;
            padding: 5px;
        }
        .file-preview-item .remove-file:hover {
            background: #f8d7da;
            border-radius: 4px;
        }
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }
        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
        }
        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
        }
        .alert-info {
            background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
            color: #0c5460;
        }
        .alert-warning {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            color: #856404;
        }
        /* Skill Park Logo Styles */
        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .skill-park-logo {
            max-height: 100px;
            max-width: 200px;
            object-fit: contain;
            opacity: 0.9;
            transition: all 0.3s ease;
        }
        .skill-park-logo:hover {
            opacity: 1;
            transform: scale(1.05);
        }
        /* Responsive logo */
        @media (max-width: 768px) {
            .skill-park-logo {
                max-height: 80px;
                max-width: 150px;
            }
        }
        /* Responsive Design */
        @media (max-width: 768px) {
            .wizard-container {
                padding: 10px;
            }
            .wizard-body {
                padding: 30px 20px;
            }
            .step-indicators {
                flex-direction: column;
                gap: 15px;
            }
            .step-indicator:not(:last-child)::after {
                display: none;
            }
            .btn-wizard {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; padding: 20px 0;">
    <div class="wizard-container">
        <div class="wizard-header">
            <div class="logo-container mb-3">
                <img src="{{ asset('skill-park-logo.png') }}" alt="Skill Park Logo" class="skill-park-logo">
            </div>
            <h2><i class="fas fa-hospital me-2"></i>Diploma in Hospital Administration Registration</h2>
            <p class="mb-0">Complete your registration in 3 simple steps</p>
        </div>
        
        <div class="wizard-body">
            <!-- Progress Bar -->
            <div class="progress-container">
                <div class="progress">
                    <div class="progress-bar" id="progressBar" style="width: 33.33%"></div>
                </div>
                <div class="step-indicators">
                    <div class="step-indicator active" id="step1Indicator">
                        <div class="step-circle">1</div>
                        <div class="step-label">Personal Info</div>
                    </div>
                    <div class="step-indicator" id="step2Indicator">
                        <div class="step-circle">2</div>
                        <div class="step-label">Address Info</div>
                    </div>
                    <div class="step-indicator" id="step3Indicator">
                        <div class="step-circle">3</div>
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
                
                <!-- Step 1: Personal Information -->
                <div class="form-step active" id="formStep1">
                    <h4 class="mb-4"><i class="fas fa-user me-2"></i>Personal Information</h4>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Student Name <span class="required">*</span></label>
                                <input type="text" class="form-control" name="student_name" value="{{ $lead->title ?? '' }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Father Name <span class="required">*</span></label>
                                <input type="text" class="form-control" name="father_name" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Mother Name <span class="required">*</span></label>
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
                                <label class="form-label">Email <span class="required">*</span></label>
                                <input type="email" class="form-control" name="email" value="{{ $lead->email ?? '' }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Personal Number <span class="required">*</span></label>
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
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Parents Number <span class="required">*</span></label>
                                <div class="row">
                                    <div class="col-4">
                                        <select class="form-control" name="parents_code" required>
                                            @foreach($countryCodes as $code => $country)
                                                <option value="{{ $code }}">{{ $code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-8">
                                        <input type="tel" class="form-control" name="parents_number" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">WhatsApp Number <span class="required">*</span></label>
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
                    
                    <!-- Batch Selection -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Batch <span class="required">*</span></label>
                                <select class="form-control" name="batch_id" required>
                                    <option value="">Select Batch</option>
                                    @foreach($batches as $batch)
                                        <option value="{{ $batch->id }}">{{ $batch->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                </div>
                
                <!-- Step 2: Address Information -->
                <div class="form-step" id="formStep2">
                    <h4 class="mb-4"><i class="fas fa-map-marker-alt me-2"></i>Address Information</h4>
                    <div class="form-group">
                        <label class="form-label">Street <span class="required">*</span></label>
                        <textarea class="form-control" name="street" rows="2" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Locality <span class="required">*</span></label>
                                <input type="text" class="form-control" name="locality" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Post Office <span class="required">*</span></label>
                                <input type="text" class="form-control" name="post_office" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">District <span class="required">*</span></label>
                                <input type="text" class="form-control" name="district" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">State <span class="required">*</span></label>
                                <input type="text" class="form-control" name="state" required>
                            </div>
                        </div>
                    </div>
                            <div class="form-group">
                                <label class="form-label">Pin Code <span class="required">*</span></label>
                                <input type="text" class="form-control" name="pin_code" pattern="[0-9]{6}" maxlength="6" inputmode="numeric" required>
                                <small class="form-text text-muted">Enter 6-digit pin code</small>
                            </div>
                            
                </div>
                
                <!-- Step 3: Document Uploads & Message -->
                <div class="form-step" id="formStep3">
                    <h4 class="mb-4"><i class="fas fa-upload me-2"></i>Document Uploads</h4>
                    <p class="text-muted mb-4">Please upload clear scans or photos of the required documents. Max file size: 2MB.</p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">SSLC Certificate <span class="required">*</span></label>
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
                                <label class="form-label">Plus Two Certificate</label>
                                <div class="file-upload-area" onclick="document.getElementById('plustwo_certificate').click()">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <p class="mb-0">Click to upload or drag & drop</p>
                                    <small class="text-muted">PDF, JPG, PNG (Max 2MB) - Optional</small>
                                </div>
                                <input type="file" id="plustwo_certificate" name="plustwo_certificate" accept=".pdf,.jpg,.jpeg,.png" style="display: none;">
                                <div class="file-preview" id="plustwo_certificate_preview"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Passport Photo <span class="required">*</span></label>
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
                                <label class="form-label">Aadhar Front <span class="required">*</span></label>
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
                                <label class="form-label">Aadhar Back <span class="required">*</span></label>
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
                        <button type="button" class="btn btn-primary btn-wizard" id="nextBtn" onclick="changeStep(1)" style="display: inline-block;">
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
         const totalSteps = 3;
         const STORAGE_KEY = 'hospital_admin_form_data';

         // Load saved data on page load
         function loadSavedData() {
             const savedData = localStorage.getItem(STORAGE_KEY);
             if (savedData) {
                 try {
                     const data = JSON.parse(savedData);
                     
                     // Fill form fields with saved data
                     Object.keys(data).forEach(key => {
                         const element = document.querySelector(`[name="${key}"]`);
                         if (element) {
                             if (element.type === 'file') {
                                 // Skip file inputs as they can't be restored
                                 return;
                             }
                             element.value = data[key];
                         }
                     });
                 } catch (e) {
                     console.error('Error loading saved data:', e);
                 }
             }
         }

         // Save form data to localStorage
         function saveFormData() {
             const formData = new FormData(document.getElementById('registrationForm'));
             const data = {};
             
             for (let [key, value] of formData.entries()) {
                 if (key !== 'lead_id' && key !== '_token') {
                     data[key] = value;
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
            loadSavedData();
            setupAutoSave();
            updateStepDisplay(); // Initialize step display and buttons
            
            // Add input validation for pin code
            const pinCodeInput = document.querySelector('input[name="pin_code"]');
            if (pinCodeInput) {
                pinCodeInput.addEventListener('input', function(e) {
                    // Remove any non-numeric characters
                    e.target.value = e.target.value.replace(/[^0-9]/g, '');
                });
            }
        });

        // Step navigation - using onclick handlers instead of addEventListener to avoid conflicts

        function updateStepDisplay() {
            // Hide all steps
            document.querySelectorAll('.form-step').forEach(step => {
                step.classList.remove('active');
            });
            
            // Show current step
            document.getElementById(`formStep${currentStep}`).classList.add('active');
            
            // Update progress bar
            const progress = (currentStep / totalSteps) * 100;
            document.getElementById('progressBar').style.width = progress + '%';
            
            // Update step indicators
            document.querySelectorAll('.step-indicator').forEach((indicator, index) => {
                indicator.classList.remove('active', 'completed');
                if (index + 1 < currentStep) {
                    indicator.classList.add('completed');
                } else if (index + 1 === currentStep) {
                    indicator.classList.add('active');
                }
            });
            
            // Update navigation buttons based on current step
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
                // Step 3: Show Previous and Submit buttons
                if (prevBtn) prevBtn.style.display = 'inline-block';
                if (submitBtn) submitBtn.style.display = 'inline-block';
            }
            if (typeof window.applyTermsCheckboxVisibility === 'function') window.applyTermsCheckboxVisibility();
        }

        function changeStep(direction) {
            const nextStep = currentStep + direction;
            
            if (direction > 0) {
                // Validate current step before proceeding
                if (!validateCurrentStep()) {
                    return;
                }
            }
            
            if (nextStep >= 1 && nextStep <= totalSteps) {
                currentStep = nextStep;
                updateStepDisplay();
            }
        }

         function validateCurrentStep() {
             const currentStepElement = document.getElementById(`formStep${currentStep}`);
             
             // Get all required fields, including those that might have had required attribute temporarily removed
             const requiredFields = currentStepElement.querySelectorAll('[required]');
             const fileInputs = currentStepElement.querySelectorAll('input[type="file"]');
             
            // Check file inputs (they should always be required in step 3)
            if (currentStep === 3) {
                const fileFieldNames = {
                    'sslc_certificate': 'SSLC certificate',
                    'plustwo_certificate': 'Plus Two certificate',
                    'passport_photo': 'Passport photo',
                    'adhar_front': 'Aadhar front',
                    'adhar_back': 'Aadhar back',
                    'signature': 'Signature'
                };
                
                const requiredFileFields = ['sslc_certificate', 'passport_photo', 'adhar_front', 'adhar_back', 'signature'];
                for (let fieldName of requiredFileFields) {
                    const field = document.getElementById(fieldName);
                    if (field && (!field.files || field.files.length === 0)) {
                        const fieldLabel = fileFieldNames[fieldName] || fieldName;
                        showAlert(`Please upload the required file: ${fieldLabel}`, 'warning');
                        return false;
                    }
                }
            }
             
             // Check other required fields
             for (let field of requiredFields) {
                 if (field.type === 'file') {
                     // Skip file validation here as it's handled above
                     continue;
                 } else {
                     // Check text/select fields
                     if (!field.value.trim()) {
                         field.focus();
                         showAlert('Please fill in all required fields.', 'warning');
                         return false;
                     }
                 }
             }
             
             return true;
         }

        // File upload handling
        function setupFileUpload(inputId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(inputId + '_preview');
            
            input.addEventListener('change', function(e) {
                handleFileUpload(e, preview, inputId);
            });
            
            // Drag and drop
            const uploadArea = input.previousElementSibling;
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

        function handleFileUpload(event, preview, inputId) {
            const file = event.target.files[0];
            if (file) {
                // Validate file size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    showAlert('File size must be less than 2MB.', 'danger');
                    return;
                }
                
                // Validate file type
                const allowedTypes = inputId === 'passport_photo' || inputId === 'signature' 
                    ? ['image/jpeg', 'image/jpg', 'image/png']
                    : ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
                
                if (!allowedTypes.includes(file.type)) {
                    showAlert('Invalid file type. Please upload a valid file.', 'danger');
                    return;
                }
                
                // Show preview
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
        }

        function removeFile(inputId) {
            document.getElementById(inputId).value = '';
            document.getElementById(inputId + '_preview').innerHTML = '';
        }

        // Setup file uploads
        setupFileUpload('sslc_certificate');
        setupFileUpload('plustwo_certificate');
        setupFileUpload('passport_photo');
        setupFileUpload('adhar_front');
        setupFileUpload('adhar_back');
        setupFileUpload('signature');

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
            
            fetch('{{ route("public.lead.hospital-admin.register.store") }}', {
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

        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const form = document.getElementById('registrationForm');
            form.insertBefore(alertDiv, form.firstChild);
            
            // Auto dismiss after 10 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 10000);
        }
    </script>
</body>
</html>


