@extends('layouts.app')

@section('title', 'Teacher Application')

@section('content')
<section class="teacher-application-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="application-card">
                    <div class="application-header text-center mb-5">
                        <div class="icon-wrapper mb-3">
                            <i class="fas fa-chalkboard-teacher fa-3x"></i>
                        </div>
                        <h2 class="application-title">Become a Teacher</h2>
                        <p class="application-subtitle">Share your knowledge and inspire the next generation</p>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle me-2"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <span>{{ session('error') }}</span>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Progress Steps -->
                    <div class="progress-steps mb-5">
                        <div class="step active" data-step="1">
                            <div class="step-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <span class="step-label">Personal Info</span>
                        </div>
                        <div class="step-line" data-step="1"></div>
                        <div class="step" data-step="2">
                            <div class="step-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <span class="step-label">Experience</span>
                        </div>
                        <div class="step-line" data-step="2"></div>
                        <div class="step" data-step="3">
                            <div class="step-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <span class="step-label">Documents</span>
                        </div>
                    </div>

                    <form action="{{ route('teacher.application.store') }}" method="POST" enctype="multipart/form-data" class="application-form" id="teacherApplicationForm">
                        @csrf

                        <!-- STEP 1: Personal Information -->
                        <div class="form-step active" data-step="1">
                            <div class="application-section">
                                <div class="section-header">
                                    <div class="section-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <h4 class="section-title">Personal Information</h4>
                                        <p class="section-description">Tell us about yourself</p>
                                    </div>
                                </div>
                                
                                <div class="section-content">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="full_name" class="form-label">
                                                Full Name <span class="required">*</span>
                                            </label>
                                            <div class="input-group-custom">
                                                <i class="fas fa-user input-icon"></i>
                                                <input type="text" class="form-control" id="full_name" name="full_name" 
                                                       placeholder="Enter your full name" 
                                                       value="{{ old('full_name', Auth::user()->name) }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">
                                                Email Address <span class="required">*</span>
                                            </label>
                                            <div class="input-group-custom">
                                                <i class="fas fa-envelope input-icon"></i>
                                                <input type="email" class="form-control" id="email" name="email" 
                                                       placeholder="your@email.com" 
                                                       value="{{ old('email', Auth::user()->email) }}" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="phone" class="form-label">
                                                Phone Number <span class="required">*</span>
                                            </label>
                                            <div class="input-group-custom">
                                                <i class="fas fa-phone input-icon"></i>
                                                <input type="tel" class="form-control" id="phone" name="phone" 
                                                       placeholder="08xx-xxxx-xxxx" 
                                                       value="{{ old('phone', Auth::user()->phone) }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="address" class="form-label">
                                                Address <span class="required">*</span>
                                            </label>
                                            <div class="input-group-custom">
                                                <i class="fas fa-map-marker-alt input-icon"></i>
                                                <textarea class="form-control" id="address" name="address" rows="3" 
                                                          placeholder="Enter your full address" required>{{ old('address', Auth::user()->address ?? '') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="step-navigation">
                                <a href="{{ route('profile') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Profile
                                </a>
                                <button type="button" class="btn btn-primary btn-next">
                                    Next Step <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- STEP 2: Teaching Experience -->
                        <div class="form-step" data-step="2">
                            <div class="application-section">
                                <div class="section-header">
                                    <div class="section-icon">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                    <div>
                                        <h4 class="section-title">Teaching Experience</h4>
                                        <p class="section-description">Share your teaching background and expertise</p>
                                    </div>
                                </div>
                                
                                <div class="section-content">
                                    <div class="mb-3">
                                        <label for="teaching_experience" class="form-label">
                                            Describe Your Experience <span class="required">*</span>
                                        </label>
                                        <div class="input-group-custom">
                                            <i class="fas fa-book-open input-icon"></i>
                                            <textarea class="form-control" id="teaching_experience" name="teaching_experience" rows="8" 
                                                      placeholder="Tell us about your teaching journey, subjects you've taught, years of experience, teaching methodologies, and achievements..." required>{{ old('teaching_experience') }}</textarea>
                                        </div>
                                        <small class="form-text">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Include specific details about your teaching background, certifications, and notable accomplishments
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="step-navigation">
                                <button type="button" class="btn btn-outline-secondary btn-prev">
                                    <i class="fas fa-arrow-left me-2"></i>Previous
                                </button>
                                <button type="button" class="btn btn-primary btn-next">
                                    Next Step <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- STEP 3: Required Documents -->
                        <div class="form-step" data-step="3">
                            <div class="application-section">
                                <div class="section-header">
                                    <div class="section-icon">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <div>
                                        <h4 class="section-title">Required Documents</h4>
                                        <p class="section-description">Upload your supporting documents</p>
                                    </div>
                                </div>
                                
                                <div class="section-content">
                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <div class="file-upload-box">
                                                <label for="ktp_file" class="file-label">
                                                    <i class="fas fa-id-card file-icon"></i>
                                                    <span class="file-title">KTP/ID Card <span class="required">*</span></span>
                                                    <span class="file-requirements">PDF, JPG, PNG (Max: 5MB)</span>
                                                </label>
                                                <input type="file" class="file-input" id="ktp_file" name="ktp_file" 
                                                       accept=".pdf,.jpg,.jpeg,.png" required>
                                                <div class="file-preview" id="ktp_preview"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <div class="file-upload-box">
                                                <label for="teaching_certificate_file" class="file-label">
                                                    <i class="fas fa-certificate file-icon"></i>
                                                    <span class="file-title">Teaching Certificate <span class="required">*</span></span>
                                                    <span class="file-requirements">PDF, JPG, PNG (Max: 5MB)</span>
                                                </label>
                                                <input type="file" class="file-input" id="teaching_certificate_file" name="teaching_certificate_file" 
                                                       accept=".pdf,.jpg,.jpeg,.png" required>
                                                <div class="file-preview" id="cert_preview"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <div class="file-upload-box">
                                                <label for="institution_id_file" class="file-label">
                                                    <i class="fas fa-building file-icon"></i>
                                                    <span class="file-title">Institution ID Card <span class="required">*</span></span>
                                                    <span class="file-requirements">PDF, JPG, PNG (Max: 5MB)</span>
                                                </label>
                                                <input type="file" class="file-input" id="institution_id_file" name="institution_id_file" 
                                                       accept=".pdf,.jpg,.jpeg,.png" required>
                                                <div class="file-preview" id="inst_preview"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <div class="file-upload-box">
                                                <label for="portfolio_file" class="file-label">
                                                    <i class="fas fa-briefcase file-icon"></i>
                                                    <span class="file-title">Portfolio <span class="required">*</span></span>
                                                    <span class="file-requirements">PDF, ZIP, DOC, DOCX (Max: 10MB)</span>
                                                </label>
                                                <input type="file" class="file-input" id="portfolio_file" name="portfolio_file" 
                                                       accept=".pdf,.zip,.doc,.docx" required>
                                                <div class="file-preview" id="portfolio_preview"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="terms-box mt-4">
                                        <div class="custom-checkbox">
                                            <input type="checkbox" class="checkbox-input" id="terms" name="terms" required>
                                            <label class="checkbox-label" for="terms">
                                                <i class="fas fa-shield-alt me-2"></i>
                                                I certify that all information provided is true and accurate. I understand that false information may result in rejection of this application.
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="step-navigation">
                                <button type="button" class="btn btn-outline-secondary btn-prev">
                                    <i class="fas fa-arrow-left me-2"></i>Previous
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Submit Application
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('styles')
<style>
.teacher-application-section {
    background: #D7EBFF;
    min-height: 100vh;
    padding: 3rem 0;
}

.application-card {
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    padding: 3rem;
    animation: fadeInUp 0.6s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Header Section */
.application-header {
    position: relative;
}

.icon-wrapper {
    width: 80px;
    height: 80px;
    margin: 0 auto;
    background: linear-gradient(135deg, #1A76D1 0%, #4A9EE0 100%);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 24px rgba(26, 118, 209, 0.3);
}

.icon-wrapper i {
    color: #ffffff;
}

.application-title {
    color: #1a1a1a;
    font-weight: 700;
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.application-subtitle {
    color: #6c757d;
    font-size: 1.1rem;
    font-weight: 400;
}

/* Progress Steps */
.progress-steps {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 0;
    position: relative;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    position: relative;
    z-index: 2;
    cursor: pointer;
    transition: all 0.3s ease;
}

.step-icon {
    width: 50px;
    height: 50px;
    background: #e9ecef;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-size: 1.25rem;
    transition: all 0.3s ease;
}

.step.active .step-icon {
    background: linear-gradient(135deg, #1A76D1 0%, #4A9EE0 100%);
    color: #ffffff;
    box-shadow: 0 4px 15px rgba(26, 118, 209, 0.3);
    transform: scale(1.1);
}

.step.completed .step-icon {
    background: #198754;
    color: #ffffff;
}

.step.completed .step-icon::after {
    content: '\f00c';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
}

.step.completed .step-icon i {
    display: none;
}

.step-label {
    font-size: 0.85rem;
    font-weight: 600;
    color: #6c757d;
    transition: all 0.3s ease;
}

.step.active .step-label {
    color: #1A76D1;
}

.step.completed .step-label {
    color: #198754;
}

.step-line {
    width: 100px;
    height: 2px;
    background: #e9ecef;
    position: relative;
    top: -20px;
    transition: all 0.3s ease;
}

.step-line.completed {
    background: #198754;
}

/* Form Steps */
.form-step {
    display: none;
    animation: fadeIn 0.4s ease;
}

.form-step.active {
    display: block;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Application Section */
.application-section {
    margin-bottom: 2rem;
}

.section-header {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid #f1f3f5;
}

.section-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #e7f3ff 0%, #d6ebff 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1A76D1;
    font-size: 1.25rem;
}

.section-title {
    color: #1a1a1a;
    font-weight: 700;
    font-size: 1.5rem;
    margin: 0 0 0.5rem 0;
    line-height: 1.2;
}

.section-description {
    color: #6c757d;
    font-size: 0.9rem;
    margin: 0;
    line-height: 1.4;
}

.section-content {
    padding: 1rem 0;
}

/* Form Controls */
.form-label {
    font-weight: 600;
    color: #333;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.required {
    color: #dc3545;
    margin-left: 3px;
}

.input-group-custom {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 1rem;
    top: 1rem;
    color: #6c757d;
    z-index: 3;
    pointer-events: none;
}

/* Icon alignment for single-line inputs */
.input-group-custom:not(:has(textarea)) .input-icon {
    top: 50%;
    transform: translateY(-50%);
}

.input-group-custom .form-control {
    padding-left: 2.75rem;
}

.form-control {
    padding: 0.875rem 1rem;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    transition: all 0.3s ease;
    font-size: 0.95rem;
}

.form-control:focus {
    border-color: #1A76D1;
    box-shadow: 0 0 0 0.2rem rgba(26, 118, 209, 0.15);
    outline: none;
}

.form-control::placeholder {
    color: #adb5bd;
}

textarea.form-control {
    resize: vertical;
    min-height: 100px;
}

.form-text {
    color: #6c757d;
    font-size: 0.85rem;
    display: block;
    margin-top: 0.5rem;
}

/* File Upload Box */
.file-upload-box {
    border: 2px dashed #dee2e6;
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
    background: #f8f9fa;
    position: relative;
}

.file-upload-box:hover {
    border-color: #1A76D1;
    background: #e7f3ff;
}

.file-upload-box.has-file {
    border-color: #198754;
    background: #d1f4e0;
}

.file-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    margin: 0;
}

.file-icon {
    font-size: 2rem;
    color: #1A76D1;
    margin-bottom: 0.5rem;
}

.file-title {
    font-weight: 600;
    color: #333;
    font-size: 0.95rem;
}

.file-requirements {
    font-size: 0.8rem;
    color: #6c757d;
}

.file-input {
    position: absolute;
    opacity: 0;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    cursor: pointer;
}

.file-preview {
    margin-top: 1rem;
    font-size: 0.85rem;
    color: #198754;
    font-weight: 600;
}

/* Terms Box */
.terms-box {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 1.5rem;
    border: 2px solid #e9ecef;
}

.custom-checkbox {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
}

.checkbox-input {
    width: 20px;
    height: 20px;
    margin-top: 2px;
    cursor: pointer;
    accent-color: #1A76D1;
}

.checkbox-label {
    font-size: 0.95rem;
    color: #495057;
    line-height: 1.6;
    cursor: pointer;
    margin: 0;
}

/* Step Navigation */
.step-navigation {
    display: flex;
    gap: 1rem;
    justify-content: space-between;
    align-items: center;
    padding-top: 2rem;
    margin-top: 2rem;
    border-top: 2px solid #f1f3f5;
}

.btn {
    padding: 0.875rem 2rem;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 10px;
    transition: all 0.3s ease;
    border: none;
}

.btn-primary {
    background: linear-gradient(135deg, #1A76D1 0%, #4A9EE0 100%);
    color: #fff;
    box-shadow: 0 4px 15px rgba(26, 118, 209, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(26, 118, 209, 0.4);
    background: linear-gradient(135deg, #1565c0 0%, #1A76D1 100%);
}

.btn-outline-secondary {
    border: 2px solid #6c757d;
    color: #6c757d;
    background: transparent;
}

.btn-outline-secondary:hover {
    background: #6c757d;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
}

/* Alert Styles */
.alert {
    border-radius: 12px;
    border: none;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
}

.alert-success {
    background: #d1f4e0;
    color: #155724;
}

.alert-danger {
    background: #fee;
    color: #c00;
}

/* Responsive Design */
@media (max-width: 991.98px) {
    .application-card {
        padding: 2rem;
    }

    .application-title {
        font-size: 1.75rem;
    }

    .progress-steps {
        padding: 1.5rem 0;
    }

    .step-line {
        width: 60px;
    }

    .step-navigation {
        flex-direction: column-reverse;
    }

    .step-navigation .btn {
        width: 100%;
    }
}

@media (max-width: 767.98px) {
    .teacher-application-section {
        padding: 2rem 0;
    }

    .application-card {
        padding: 1.5rem;
        border-radius: 15px;
    }

    .icon-wrapper {
        width: 60px;
        height: 60px;
    }

    .icon-wrapper i {
        font-size: 2rem;
    }

    .application-title {
        font-size: 1.5rem;
    }

    .progress-steps {
        padding: 1rem 0;
    }

    .step-icon {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }

    .step-line {
        width: 40px;
        top: -15px;
    }

    .step-label {
        font-size: 0.75rem;
    }

    .section-title {
        font-size: 1.25rem;
    }

    .file-upload-box {
        padding: 1rem;
    }

    .file-icon {
        font-size: 1.5rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    const totalSteps = 3;
    
    // File upload preview
    const fileInputs = document.querySelectorAll('.file-input');
    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const box = this.closest('.file-upload-box');
            const previewId = this.id + '_preview';
            const preview = document.getElementById(previewId);
            
            if (file) {
                box.classList.add('has-file');
                if (preview) {
                    preview.innerHTML = `<i class="fas fa-check-circle me-1"></i>${file.name}`;
                }
            } else {
                box.classList.remove('has-file');
                if (preview) {
                    preview.innerHTML = '';
                }
            }
        });
    });
    
    // Step navigation functions
    function showStep(step) {
        // Hide all steps
        document.querySelectorAll('.form-step').forEach(s => s.classList.remove('active'));
        
        // Show current step
        const currentStepElement = document.querySelector(`.form-step[data-step="${step}"]`);
        if (currentStepElement) {
            currentStepElement.classList.add('active');
        }
        
        // Update progress indicators
        document.querySelectorAll('.step').forEach((s, index) => {
            const stepNum = index + 1;
            s.classList.remove('active', 'completed');
            
            if (stepNum < step) {
                s.classList.add('completed');
            } else if (stepNum === step) {
                s.classList.add('active');
            }
        });
        
        // Update step lines
        document.querySelectorAll('.step-line').forEach((line, index) => {
            const lineStep = index + 1;
            if (lineStep < step) {
                line.classList.add('completed');
            } else {
                line.classList.remove('completed');
            }
        });
        
        // Scroll to top of form
        document.querySelector('.application-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    
    function validateStep(step) {
        const currentStepElement = document.querySelector(`.form-step[data-step="${step}"]`);
        const inputs = currentStepElement.querySelectorAll('input[required], textarea[required]');
        
        let isValid = true;
        inputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('is-invalid');
                
                // Remove invalid class after user types
                input.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                }, { once: true });
            }
        });
        
        if (!isValid) {
            // Show validation message
            const existingAlert = currentStepElement.querySelector('.validation-alert');
            if (!existingAlert) {
                const alert = document.createElement('div');
                alert.className = 'alert alert-danger validation-alert mt-3';
                alert.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>Please fill in all required fields before continuing.';
                currentStepElement.querySelector('.section-content').insertAdjacentElement('beforeend', alert);
                
                // Remove alert after 3 seconds
                setTimeout(() => alert.remove(), 3000);
            }
        }
        
        return isValid;
    }
    
    // Next button handlers
    document.querySelectorAll('.btn-next').forEach(btn => {
        btn.addEventListener('click', function() {
            if (validateStep(currentStep)) {
                if (currentStep < totalSteps) {
                    currentStep++;
                    showStep(currentStep);
                }
            }
        });
    });
    
    // Previous button handlers
    document.querySelectorAll('.btn-prev').forEach(btn => {
        btn.addEventListener('click', function() {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        });
    });
    
    // Step indicator click handlers
    document.querySelectorAll('.step').forEach((step, index) => {
        step.addEventListener('click', function() {
            const stepNum = index + 1;
            // Only allow going back to previous steps, not skipping forward
            if (stepNum <= currentStep) {
                currentStep = stepNum;
                showStep(currentStep);
            }
        });
    });
    
    // Initialize first step
    showStep(1);
});
</script>
@endsection