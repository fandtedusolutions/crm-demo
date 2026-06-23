<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Faculty Application Form - Online Teaching</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            min-height: 100vh;
            padding: 40px 20px;
            position: relative;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 50%, rgba(120, 119, 198, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(72, 149, 239, 0.1) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }
        
        .container {
            position: relative;
            z-index: 1;
        }
        
        .form-container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.3),
                0 0 0 1px rgba(255, 255, 255, 0.1);
            padding: 0;
            max-width: 1000px;
            margin: 0 auto;
            overflow: hidden;
        }
        
        .form-header {
            background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%);
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .form-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
        }
        
        .form-header h2 {
            color: white;
            font-weight: 700;
            font-size: 28px;
            margin: 0;
            position: relative;
            z-index: 1;
        }
        
        .form-header p {
            color: rgba(255, 255, 255, 0.9);
            margin: 10px 0 0 0;
            font-size: 15px;
            position: relative;
            z-index: 1;
        }
        
        .form-body {
            padding: 40px;
        }
        
        .section-title {
            color: #2c3e50;
            font-weight: 600;
            font-size: 20px;
            margin-bottom: 25px;
            padding-bottom: 12px;
            border-bottom: 3px solid #4A90E2;
            position: relative;
            display: inline-block;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 60px;
            height: 3px;
            background: #357ABD;
        }
        
        .section-wrapper {
            margin-bottom: 40px;
        }
        
        .subsection-title {
            color: #4A90E2;
            font-weight: 600;
            font-size: 16px;
            margin: 30px 0 20px 0;
            padding-left: 12px;
            border-left: 4px solid #4A90E2;
        }
        
        .form-label {
            color: #2c3e50;
            font-weight: 500;
            font-size: 14px;
            margin-bottom: 8px;
            display: block;
        }
        
        .required-mark {
            color: #e74c3c;
            margin-left: 3px;
        }
        
        .form-control, .form-select {
            border: 2px solid #e0e6ed;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #4A90E2;
            box-shadow: 0 0 0 4px rgba(74, 144, 226, 0.1);
            background: white;
            outline: none;
        }
        
        .form-control:hover, .form-select:hover {
            border-color: #4A90E2;
            background: white;
        }
        
        .file-upload-wrapper {
            position: relative;
        }
        
        .existing-file-badge {
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
            box-shadow: 0 2px 8px rgba(39, 174, 96, 0.2);
        }
        
        .existing-file-badge .checkmark {
            font-weight: bold;
            font-size: 16px;
        }
        
        .btn-view-file {
            background: white;
            color: #4A90E2;
            border: 2px solid #4A90E2;
            padding: 6px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .btn-view-file:hover {
            background: #4A90E2;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(74, 144, 226, 0.3);
        }
        
        .file-help-text {
            color: #7f8c8d;
            font-size: 12px;
            margin-top: 6px;
            font-style: italic;
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            padding: 16px 20px;
            margin-bottom: 30px;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
        }
        
        .alert-danger ul {
            margin: 10px 0 0 20px;
        }
        
        .submit-section {
            text-align: center;
            margin-top: 50px;
            padding-top: 30px;
            border-top: 2px solid #e0e6ed;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%);
            color: white;
            border: none;
            padding: 16px 60px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(74, 144, 226, 0.4);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(74, 144, 226, 0.5);
        }
        
        .btn-submit:active {
            transform: translateY(-1px);
        }
        
        .row {
            margin-bottom: 0;
        }
        
        .mb-3 {
            margin-bottom: 20px !important;
        }
        
        @media (max-width: 768px) {
            .form-header h2 {
                font-size: 22px;
            }
            
            .form-body {
                padding: 30px 20px;
            }
            
            .btn-submit {
                padding: 14px 40px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <h2>Online Teaching Faculty Application Form</h2>
                <p>Please fill in all the required information to complete your application</p>
            </div>
            
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-body">
                <form action="{{ route('public.faculty.submit', $faculty->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="section-wrapper">
                        <h5 class="section-title">A. Personal Details</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name <span class="required-mark">*</span></label>
                                <input type="text" class="form-control" name="full_name" value="{{ old('full_name', $faculty->full_name) }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Date of Birth <span class="required-mark">*</span></label>
                                <input type="date" class="form-control" name="date_of_birth" value="{{ old('date_of_birth', $faculty->date_of_birth ? $faculty->date_of_birth->format('Y-m-d') : '') }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Gender <span class="required-mark">*</span></label>
                                <select class="form-select" name="gender" required>
                                    <option value="">Select</option>
                                    <option value="Male" {{ old('gender', $faculty->gender) === 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender', $faculty->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Primary Mobile Number <span class="required-mark">*</span></label>
                                <input type="text" class="form-control" name="primary_mobile_number" value="{{ old('primary_mobile_number', $faculty->primary_mobile_number) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Alternate Contact Number</label>
                                <input type="text" class="form-control" name="alternate_contact_number" value="{{ old('alternate_contact_number', $faculty->alternate_contact_number) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Official Email Address <span class="required-mark">*</span></label>
                                <input type="email" class="form-control" name="official_email_address" value="{{ old('official_email_address', $faculty->official_email_address) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Father's Name <span class="required-mark">*</span></label>
                                <input type="text" class="form-control" name="father_name" value="{{ old('father_name', $faculty->father_name) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mother's Name <span class="required-mark">*</span></label>
                                <input type="text" class="form-control" name="mother_name" value="{{ old('mother_name', $faculty->mother_name) }}" required>
                            </div>
                        </div>

                        <h6 class="subsection-title">Residential Address</h6>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">House Name / Flat No. <span class="required-mark">*</span></label>
                        <input type="text" class="form-control" name="address_house_name_flat_no" value="{{ old('address_house_name_flat_no', $faculty->address_house_name_flat_no) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Area / Locality <span class="required-mark">*</span></label>
                        <input type="text" class="form-control" name="address_area_locality" value="{{ old('address_area_locality', $faculty->address_area_locality) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Village / Town / City <span class="required-mark">*</span></label>
                        <input type="text" class="form-control" name="address_city" value="{{ old('address_city', $faculty->address_city) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">District <span class="required-mark">*</span></label>
                        <input type="text" class="form-control" name="address_district" value="{{ old('address_district', $faculty->address_district) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">State <span class="required-mark">*</span></label>
                        <input type="text" class="form-control" name="address_state" value="{{ old('address_state', $faculty->address_state) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">PIN Code <span class="required-mark">*</span></label>
                        <input type="text" class="form-control" name="address_pin_code" value="{{ old('address_pin_code', $faculty->address_pin_code) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Highest Educational Qualification <span class="required-mark">*</span></label>
                        <input type="text" class="form-control" name="highest_educational_qualification" value="{{ old('highest_educational_qualification', $faculty->highest_educational_qualification) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Additional Certifications / Professional Credentials</label>
                        <input type="text" class="form-control" name="additional_certifications" value="{{ old('additional_certifications', $faculty->additional_certifications) }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Teaching Experience <span class="required-mark">*</span></label>
                        <select class="form-select" name="teaching_experience" required>
                            <option value="">Select</option>
                            <option value="Yes" {{ old('teaching_experience', $faculty->teaching_experience ? 'Yes' : 'No') === 'Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ old('teaching_experience', $faculty->teaching_experience ? 'Yes' : 'No') === 'No' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Department Name <span class="required-mark">*</span></label>
                        <select class="form-select" name="department_name" required>
                            <option value="">Select</option>
                            <option value="E-School" {{ old('department_name', $faculty->department_name) === 'E-School' ? 'selected' : '' }}>E-School</option>
                            <option value="EduThanzeel" {{ old('department_name', $faculty->department_name) === 'EduThanzeel' ? 'selected' : '' }}>EduThanzeel</option>
                            <option value="Diploma in Graphic Designing" {{ old('department_name', $faculty->department_name) === 'Diploma in Graphic Designing' ? 'selected' : '' }}>Diploma in Graphic Designing</option>
                            <option value="AI Integrated Digital Marketing" {{ old('department_name', $faculty->department_name) === 'AI Integrated Digital Marketing' ? 'selected' : '' }}>AI Integrated Digital Marketing</option>
                            <option value="Data Science" {{ old('department_name', $faculty->department_name) === 'Data Science' ? 'selected' : '' }}>Data Science</option>
                            <option value="Machine Learning" {{ old('department_name', $faculty->department_name) === 'Machine Learning' ? 'selected' : '' }}>Machine Learning</option>
                        </select>
                    </div>
                    </div>

                    <div class="section-wrapper">
                        <h5 class="section-title">B. Document Submission (Uploads)</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Updated Resume / CV <span class="required-mark">*</span></label>
                                @if($faculty->document_resume_cv)
                                    <div class="existing-file-badge">
                                        <span class="checkmark">✓</span>
                                        <span>File already uploaded</span>
                                        <a href="{{ asset('storage/' . $faculty->document_resume_cv) }}" target="_blank" class="btn-view-file">View</a>
                                    </div>
                                @endif
                                <input type="file" class="form-control" name="document_resume_cv" {{ $faculty->document_resume_cv ? '' : 'required' }}>
                                @if($faculty->document_resume_cv)
                                    <small class="file-help-text">Upload a new file to replace the existing one</small>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">10th Certificate <span class="required-mark">*</span></label>
                                @if($faculty->document_10th_certificate)
                                    <div class="existing-file-badge">
                                        <span class="checkmark">✓</span>
                                        <span>File already uploaded</span>
                                        <a href="{{ asset('storage/' . $faculty->document_10th_certificate) }}" target="_blank" class="btn-view-file">View</a>
                                    </div>
                                @endif
                                <input type="file" class="form-control" name="document_10th_certificate" {{ $faculty->document_10th_certificate ? '' : 'required' }}>
                                @if($faculty->document_10th_certificate)
                                    <small class="file-help-text">Upload a new file to replace the existing one</small>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Educational Qualification Certificates <span class="required-mark">*</span></label>
                                @if($faculty->document_educational_qualification_certificates)
                                    <div class="existing-file-badge">
                                        <span class="checkmark">✓</span>
                                        <span>File already uploaded</span>
                                        <a href="{{ asset('storage/' . $faculty->document_educational_qualification_certificates) }}" target="_blank" class="btn-view-file">View</a>
                                    </div>
                                @endif
                                <input type="file" class="form-control" name="document_educational_qualification_certificates" {{ $faculty->document_educational_qualification_certificates ? '' : 'required' }}>
                                @if($faculty->document_educational_qualification_certificates)
                                    <small class="file-help-text">Upload a new file to replace the existing one</small>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Aadhaar Card (Front Side) <span class="required-mark">*</span></label>
                                @if($faculty->document_aadhaar_front)
                                    <div class="existing-file-badge">
                                        <span class="checkmark">✓</span>
                                        <span>File already uploaded</span>
                                        <a href="{{ asset('storage/' . $faculty->document_aadhaar_front) }}" target="_blank" class="btn-view-file">View</a>
                                    </div>
                                @endif
                                <input type="file" class="form-control" name="document_aadhaar_front" {{ $faculty->document_aadhaar_front ? '' : 'required' }}>
                                @if($faculty->document_aadhaar_front)
                                    <small class="file-help-text">Upload a new file to replace the existing one</small>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Aadhaar Card (Back Side) <span class="required-mark">*</span></label>
                                @if($faculty->document_aadhaar_back)
                                    <div class="existing-file-badge">
                                        <span class="checkmark">✓</span>
                                        <span>File already uploaded</span>
                                        <a href="{{ asset('storage/' . $faculty->document_aadhaar_back) }}" target="_blank" class="btn-view-file">View</a>
                                    </div>
                                @endif
                                <input type="file" class="form-control" name="document_aadhaar_back" {{ $faculty->document_aadhaar_back ? '' : 'required' }}>
                                @if($faculty->document_aadhaar_back)
                                    <small class="file-help-text">Upload a new file to replace the existing one</small>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Other Supporting Document – 1</label>
                                @if($faculty->document_other_1)
                                    <div class="existing-file-badge">
                                        <span class="checkmark">✓</span>
                                        <span>File already uploaded</span>
                                        <a href="{{ asset('storage/' . $faculty->document_other_1) }}" target="_blank" class="btn-view-file">View</a>
                                    </div>
                                @endif
                                <input type="file" class="form-control" name="document_other_1">
                                @if($faculty->document_other_1)
                                    <small class="file-help-text">Upload a new file to replace the existing one</small>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Other Supporting Document – 2</label>
                                @if($faculty->document_other_2)
                                    <div class="existing-file-badge">
                                        <span class="checkmark">✓</span>
                                        <span>File already uploaded</span>
                                        <a href="{{ asset('storage/' . $faculty->document_other_2) }}" target="_blank" class="btn-view-file">View</a>
                                    </div>
                                @endif
                                <input type="file" class="form-control" name="document_other_2">
                                @if($faculty->document_other_2)
                                    <small class="file-help-text">Upload a new file to replace the existing one</small>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="submit-section">
                        <button type="submit" class="btn-submit">
                            Submit Application
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
