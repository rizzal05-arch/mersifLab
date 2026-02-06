@extends('layouts.app')

@section('title', 'Edit Teacher Application')

@section('content')
<section class="teacher-application-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="application-card">
                    <div class="application-header text-center mb-4">
                        <i class="fas fa-edit fa-3x text-primary mb-3"></i>
                        <h2 class="application-title">Edit Your Application</h2>
                        <p class="application-subtitle">Update your teacher application information</p>
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
                            <h5 class="mb-2"><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:</h5>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Status Info -->
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Current Status:</strong> 
                        @if($application->isPending())
                            <span class="badge bg-warning">Pending Review</span>
                        @elseif($application->isRejected())
                            <span class="badge bg-danger">Rejected</span> - Please address the admin notes and resubmit
                        @else
                            <span class="badge bg-success">Approved</span>
                        @endif
                    </div>

                    @if($application->isRejected() && $application->admin_notes)
                        <div class="alert alert-light border border-danger mb-4">
                            <h6 class="text-danger mb-2"><i class="fas fa-comment-dots me-2"></i>Admin Notes:</h6>
                            <p class="mb-0">{{ $application->admin_notes }}</p>
                        </div>
                    @endif

                    <form action="{{ route('teacher.application.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Personal Information -->
                        <div class="application-section">
                            <h4 class="section-title"><i class="fas fa-user me-2"></i>Personal Information</h4>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="full_name" class="form-label">Full Name<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('full_name') is-invalid @enderror" 
                                               id="full_name" name="full_name" 
                                               placeholder="Enter your full name" 
                                               pattern="[A-Z][a-zA-Z\s\'-]*"
                                               title="Nama harus dimulai dengan huruf kapital"
                                               oninput="this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1).toLowerCase()"
                                               value="{{ old('full_name', $application->full_name) }}" required>
                                        @error('full_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email<span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" 
                                               placeholder="your@email.com" 
                                               value="{{ old('email', $application->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number<span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" name="phone" 
                                               placeholder="08xx-xxxx-xxxx" 
                                               inputmode="numeric"
                                               oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                               title="Nomor telepon hanya boleh mengandung angka"
                                               value="{{ old('phone', $application->phone) }}" required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address<span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                                  id="address" name="address" rows="3" 
                                                  placeholder="Enter your full address" required>{{ old('address', $application->address) }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Teaching Experience -->
                        <div class="application-section">
                            <h4 class="section-title"><i class="fas fa-graduation-cap me-2"></i>Teaching Experience</h4>
                            
                            <div class="mb-3">
                                <label for="teaching_experience" class="form-label">Teaching Experience<span class="text-danger">*</span></label>
                                <textarea class="form-control @error('teaching_experience') is-invalid @enderror" 
                                          id="teaching_experience" name="teaching_experience" rows="5" 
                                          placeholder="Describe your teaching experience, subjects you teach, years of experience, etc." required>{{ old('teaching_experience', $application->teaching_experience) }}</textarea>
                                <small class="text-muted">Please provide detailed information about your teaching background</small>
                                @error('teaching_experience')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Required Documents -->
                        <div class="application-section">
                            <h4 class="section-title"><i class="fas fa-file-alt me-2"></i>Required Documents</h4>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="ktp_file" class="form-label">
                                            KTP/ID Card
                                            @if($application->ktp_file)
                                                <span class="badge bg-success">Uploaded</span>
                                            @else
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>
                                        <input type="file" class="form-control @error('ktp_file') is-invalid @enderror" 
                                               id="ktp_file" name="ktp_file" 
                                               accept=".pdf,.jpg,.jpeg,.png">
                                        <small class="text-muted">PDF, JPG, PNG (Max: 10MB). Leave empty to keep the current file.</small>
                                        @if($application->ktp_file)
                                            <div class="mt-2">
                                                <a href="{{ asset('storage/' . $application->ktp_file) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="fas fa-eye me-1"></i>View Current File
                                                </a>
                                            </div>
                                        @endif
                                        @error('ktp_file')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="teaching_certificate_file" class="form-label">
                                            Teaching Certificate
                                            @if($application->teaching_certificate_file)
                                                <span class="badge bg-success">Uploaded</span>
                                            @else
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>
                                        <input type="file" class="form-control @error('teaching_certificate_file') is-invalid @enderror" 
                                               id="teaching_certificate_file" name="teaching_certificate_file" 
                                               accept=".pdf,.jpg,.jpeg,.png">
                                        <small class="text-muted">PDF, JPG, PNG (Max: 10MB). Leave empty to keep the current file.</small>
                                        @if($application->teaching_certificate_file)
                                            <div class="mt-2">
                                                <a href="{{ asset('storage/' . $application->teaching_certificate_file) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="fas fa-eye me-1"></i>View Current File
                                                </a>
                                            </div>
                                        @endif
                                        @error('teaching_certificate_file')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="institution_id_file" class="form-label">
                                            Institution ID Card
                                            @if($application->institution_id_file)
                                                <span class="badge bg-success">Uploaded</span>
                                            @else
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>
                                        <input type="file" class="form-control @error('institution_id_file') is-invalid @enderror" 
                                               id="institution_id_file" name="institution_id_file" 
                                               accept=".pdf,.jpg,.jpeg,.png">
                                        <small class="text-muted">PDF, JPG, PNG (Max: 10MB). Leave empty to keep the current file.</small>
                                        @if($application->institution_id_file)
                                            <div class="mt-2">
                                                <a href="{{ asset('storage/' . $application->institution_id_file) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="fas fa-eye me-1"></i>View Current File
                                                </a>
                                            </div>
                                        @endif
                                        @error('institution_id_file')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="portfolio_file" class="form-label">
                                            Portfolio
                                            @if($application->portfolio_file)
                                                <span class="badge bg-success">Uploaded</span>
                                            @else
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>
                                        <input type="file" class="form-control @error('portfolio_file') is-invalid @enderror" 
                                               id="portfolio_file" name="portfolio_file" 
                                               accept=".pdf,.zip,.doc,.docx">
                                        <small class="text-muted">PDF, ZIP, DOC, DOCX (Max: 10MB). Leave empty to keep the current file.</small>
                                        @if($application->portfolio_file)
                                            <div class="mt-2">
                                                <a href="{{ asset('storage/' . $application->portfolio_file) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="fas fa-eye me-1"></i>View Current File
                                                </a>
                                            </div>
                                        @endif
                                        @error('portfolio_file')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Portfolio Link -->
                        <div class="application-section">
                            <h4 class="section-title"><i class="fas fa-link me-2"></i>Portfolio Link (Optional)</h4>
                            
                            <div class="mb-3">
                                <label for="portfolio_link" class="form-label">Portfolio Website/Link</label>
                                <input type="url" class="form-control @error('portfolio_link') is-invalid @enderror" 
                                       id="portfolio_link" name="portfolio_link" 
                                       placeholder="https://example.com/portfolio" 
                                       value="{{ old('portfolio_link', $application->portfolio_link ?? '') }}">
                                <small class="text-muted">Enter the URL to your portfolio website or online profile (optional)</small>
                                @error('portfolio_link')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Section -->
                        <div class="application-section border-0">
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="fas fa-save me-2"></i>Save Changes
                                </button>
                                <a href="{{ route('teacher.application.preview') }}" class="btn btn-outline-secondary btn-lg px-5 ms-2">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Preview
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

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Full Name auto-capitalize
    const fullNameInput = document.getElementById('full_name');
    if (fullNameInput) {
        fullNameInput.addEventListener('input', function() {
            if (this.value.length > 0) {
                this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
            }
        });
    }

    // Phone Number - only digits
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }
});
</script>
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

.application-section.border-0 {
    border-bottom: none !important;
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

.form-control.is-invalid:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.form-label {
    font-weight: 600;
    color: #555;
}

.text-danger {
    color: #dc3545 !important;
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

.btn-outline-secondary:hover {
    transform: translateY(-2px);
}

.badge {
    font-size: 0.8rem;
    padding: 0.4rem 0.6rem;
}

.btn-outline-primary {
    color: #667eea;
    border-color: #667eea;
}

.btn-outline-primary:hover {
    background-color: #667eea;
    border-color: #667eea;
}

.alert-info {
    background-color: #e7f3ff;
    border-color: #b3d9ff;
    color: #004085;
}

@media (max-width: 768px) {
    .application-card {
        padding: 20px;
    }
}
</style>
@endsection
