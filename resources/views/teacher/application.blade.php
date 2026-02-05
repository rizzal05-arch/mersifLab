@extends('layouts.app')

@section('title', 'Teacher Application')

@section('content')
<section class="teacher-application-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="application-card">
                    <div class="application-header text-center mb-4">
                        <i class="fas fa-chalkboard-teacher fa-3x text-primary mb-3"></i>
                        <h2 class="application-title">Become a Teacher</h2>
                        <p class="application-subtitle">Share your knowledge and help others learn</p>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
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

                    <form action="{{ route('teacher.application.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Personal Information -->
                        <div class="application-section">
                            <h4 class="section-title"><i class="fas fa-user me-2"></i>Personal Information</h4>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="full_name" class="form-label">Full Name<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="full_name" name="full_name" 
                                               placeholder="Enter your full name" value="{{ old('full_name', Auth::user()->name) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email<span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               placeholder="your@email.com" value="{{ old('email', Auth::user()->email) }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number<span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control" id="phone" name="phone" 
                                               placeholder="08xx-xxxx-xxxx" value="{{ old('phone', Auth::user()->phone) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address<span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="address" name="address" rows="3" 
                                                  placeholder="Enter your full address" required>{{ old('address', Auth::user()->address ?? '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Teaching Experience -->
                        <div class="application-section">
                            <h4 class="section-title"><i class="fas fa-graduation-cap me-2"></i>Teaching Experience</h4>
                            
                            <div class="mb-3">
                                <label for="teaching_experience" class="form-label">Teaching Experience<span class="text-danger">*</span></label>
                                <textarea class="form-control" id="teaching_experience" name="teaching_experience" rows="5" 
                                          placeholder="Describe your teaching experience, subjects you teach, years of experience, etc." required>{{ old('teaching_experience') }}</textarea>
                                <small class="text-muted">Please provide detailed information about your teaching background</small>
                            </div>
                        </div>

                        <!-- Required Documents -->
                        <div class="application-section">
                            <h4 class="section-title"><i class="fas fa-file-alt me-2"></i>Required Documents</h4>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="ktp_file" class="form-label">KTP/ID Card<span class="text-danger">*</span></label>
                                        <input type="file" class="form-control" id="ktp_file" name="ktp_file" 
                                               accept=".pdf,.jpg,.jpeg,.png" required>
                                        <small class="text-muted">PDF, JPG, PNG (Max: 2MB)</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="teaching_certificate_file" class="form-label">Teaching Certificate<span class="text-danger">*</span></label>
                                        <input type="file" class="form-control" id="teaching_certificate_file" name="teaching_certificate_file" 
                                               accept=".pdf,.jpg,.jpeg,.png" required>
                                        <small class="text-muted">PDF, JPG, PNG (Max: 2MB)</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="institution_id_file" class="form-label">Institution ID Card<span class="text-danger">*</span></label>
                                        <input type="file" class="form-control" id="institution_id_file" name="institution_id_file" 
                                               accept=".pdf,.jpg,.jpeg,.png" required>
                                        <small class="text-muted">PDF, JPG, PNG (Max: 2MB)</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="portfolio_file" class="form-label">Portfolio<span class="text-danger">*</span></label>
                                        <input type="file" class="form-control" id="portfolio_file" name="portfolio_file" 
                                               accept=".pdf,.zip,.doc,.docx" required>
                                        <small class="text-muted">PDF, ZIP, DOC, DOCX (Max: 5MB)</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Terms and Submit -->
                        <div class="application-section">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                                    <label class="form-check-label" for="terms">
                                        I certify that all information provided is true and accurate. I understand that false information may result in rejection of this application.
                                    </label>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="fas fa-paper-plane me-2"></i>Submit Application
                                </button>
                                <a href="{{ route('profile') }}" class="btn btn-outline-secondary btn-lg px-5 ms-2">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Profile
                                </a>
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
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
}

.application-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    padding: 40px;
    margin-top: 20px;
}

.application-header i {
    color: #667eea;
}

.application-title {
    color: #333;
    font-weight: 600;
    margin-bottom: 10px;
}

.application-subtitle {
    color: #666;
    font-size: 1.1rem;
}

.application-section {
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #eee;
}

.application-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.section-title {
    color: #333;
    font-weight: 600;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #667eea;
    display: inline-block;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

.form-label {
    font-weight: 600;
    color: #555;
}

.text-danger {
    color: #dc3545 !important;
}
</style>
@endsection
