@extends('layouts.app')

@section('title', 'Teacher Application Preview')

@section('content')
<section class="teacher-application-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="application-card">
                    <div class="application-header text-center mb-4">
                        <i class="fas fa-eye fa-3x text-primary mb-3"></i>
                        <h2 class="application-title">Application Status & Preview</h2>
                        <p class="application-subtitle">View and manage your teacher application</p>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Status Badge Section -->
                    <div class="status-section mb-4">
                        <h4 class="section-title"><i class="fas fa-clock me-2"></i>Application Status</h4>
                        <div class="status-badge-container">
                            @if($application->isPending())
                                <div class="alert alert-warning" role="alert">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-hourglass-half fa-2x me-3 text-warning"></i>
                                        <div>
                                            <h5 class="mb-1">
                                                <i class="fas fa-spinner fa-pulse"></i> Pending Review
                                            </h5>
                                            <p class="mb-0">Your application is being reviewed by our admin team. This usually takes 1-3 business days.</p>
                                        </div>
                                    </div>
                                </div>
                            @elseif($application->isApproved())
                                <div class="alert alert-success" role="alert">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle fa-2x me-3 text-success"></i>
                                        <div>
                                            <h5 class="mb-1">✓ Application Approved</h5>
                                            <p class="mb-0">Congratulations! Your teacher application has been approved. You can now create and manage courses.</p>
                                        </div>
                                    </div>
                                </div>
                                @if($application->reviewed_at)
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>Approved on {{ $application->reviewed_at->format('F d, Y \a\t h:i A') }}
                                    </small>
                                @endif
                            @elseif($application->isRejected())
                                <div class="alert alert-danger" role="alert">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-times-circle fa-2x me-3 text-danger"></i>
                                        <div>
                                            <h5 class="mb-1">✗ Application Rejected</h5>
                                            <p class="mb-0">Unfortunately, your application was not approved at this time.</p>
                                        </div>
                                    </div>
                                </div>
                                @if($application->admin_notes)
                                    <div class="alert alert-light border border-danger mt-3">
                                        <h6 class="text-danger mb-2"><i class="fas fa-comment-dots me-2"></i>Admin Notes:</h6>
                                        <p class="mb-0">{{ $application->admin_notes }}</p>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- Application Details Section -->
                    <div class="application-section">
                        <h4 class="section-title"><i class="fas fa-user me-2"></i>Personal Information</h4>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-group">
                                    <label class="detail-label">Full Name</label>
                                    <p class="detail-value">{{ $application->full_name }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-group">
                                    <label class="detail-label">Email</label>
                                    <p class="detail-value">{{ $application->email }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-group">
                                    <label class="detail-label">Phone Number</label>
                                    <p class="detail-value">{{ $application->phone }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-group">
                                    <label class="detail-label">Address</label>
                                    <p class="detail-value">{{ $application->address }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Teaching Experience Section -->
                    <div class="application-section">
                        <h4 class="section-title"><i class="fas fa-graduation-cap me-2"></i>Teaching Experience</h4>
                        <div class="detail-group">
                            <p class="detail-value" style="white-space: pre-wrap;">{{ $application->teaching_experience }}</p>
                        </div>
                    </div>

                    <!-- Documents Section -->
                    <div class="application-section">
                        <h4 class="section-title"><i class="fas fa-file-alt me-2"></i>Documents</h4>
                        
                        <div class="documents-grid">
                            <!-- KTP File -->
                            <div class="document-item">
                                <div class="document-icon">
                                    <i class="fas fa-id-card fa-2x"></i>
                                </div>
                                <div class="document-info">
                                    <h6>KTP/ID Card</h6>
                                    @if($application->ktp_file)
                                        <a href="{{ asset('storage/' . $application->ktp_file) }}" class="btn btn-sm btn-primary" target="_blank">
                                            <i class="fas fa-download me-1"></i>View
                                        </a>
                                    @else
                                        <span class="text-muted">Not uploaded</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Teaching Certificate -->
                            <div class="document-item">
                                <div class="document-icon">
                                    <i class="fas fa-certificate fa-2x text-success"></i>
                                </div>
                                <div class="document-info">
                                    <h6>Teaching Certificate</h6>
                                    @if($application->teaching_certificate_file)
                                        <a href="{{ asset('storage/' . $application->teaching_certificate_file) }}" class="btn btn-sm btn-primary" target="_blank">
                                            <i class="fas fa-download me-1"></i>View
                                        </a>
                                    @else
                                        <span class="text-muted">Not uploaded</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Institution ID -->
                            <div class="document-item">
                                <div class="document-icon">
                                    <i class="fas fa-school fa-2x text-info"></i>
                                </div>
                                <div class="document-info">
                                    <h6>Institution ID Card</h6>
                                    @if($application->institution_id_file)
                                        <a href="{{ asset('storage/' . $application->institution_id_file) }}" class="btn btn-sm btn-primary" target="_blank">
                                            <i class="fas fa-download me-1"></i>View
                                        </a>
                                    @else
                                        <span class="text-muted">Not uploaded</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Portfolio -->
                            <div class="document-item">
                                <div class="document-icon">
                                    <i class="fas fa-folder fa-2x text-warning"></i>
                                </div>
                                <div class="document-info">
                                    <h6>Portfolio</h6>
                                    @if($application->portfolio_file)
                                        <a href="{{ asset('storage/' . $application->portfolio_file) }}" class="btn btn-sm btn-primary" target="_blank">
                                            <i class="fas fa-download me-1"></i>View
                                        </a>
                                    @else
                                        <span class="text-muted">Not uploaded</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Portfolio Link -->
                        @if($application->portfolio_link)
                            <div class="mt-3">
                                <label class="detail-label">Portfolio Link</label>
                                <p class="detail-value">
                                    <a href="{{ $application->portfolio_link }}" target="_blank" class="text-primary">
                                        {{ $application->portfolio_link }}
                                        <i class="fas fa-external-link-alt ms-1"></i>
                                    </a>
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Additional Info Section -->
                    <div class="application-section">
                        <h4 class="section-title"><i class="fas fa-info-circle me-2"></i>Application Information</h4>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-group">
                                    <label class="detail-label">Submitted Date</label>
                                    <p class="detail-value">{{ $application->created_at->format('F d, Y \a\t h:i A') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-group">
                                    <label class="detail-label">Last Updated</label>
                                    <p class="detail-value">{{ $application->updated_at->format('F d, Y \a\t h:i A') }}</p>
                                </div>
                            </div>
                        </div>

                        @if($application->reviewed_by)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="detail-group">
                                        <label class="detail-label">Reviewed By</label>
                                        <p class="detail-value">{{ $application->reviewer->name ?? 'Admin' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Action Buttons Section -->
                    <div class="application-section border-0">
                        <div class="action-buttons text-center">
                            @if($application->isPending() || $application->isRejected())
                                <a href="{{ route('teacher.application.edit') }}" class="btn btn-primary btn-lg px-5">
                                    <i class="fas fa-edit me-2"></i>Edit Application
                                </a>
                            @endif
                            <a href="{{ route('profile') }}" class="btn btn-outline-secondary btn-lg px-5">
                                <i class="fas fa-arrow-left me-2"></i>Back to Profile
                            </a>
                        </div>
                    </div>
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

.detail-group {
    margin-bottom: 15px;
}

.detail-label {
    font-weight: 600;
    color: #667eea;
    font-size: 0.9rem;
    display: block;
    margin-bottom: 5px;
}

.detail-value {
    color: #333;
    margin: 0;
    padding: 10px;
    background-color: #f8f9fa;
    border-left: 3px solid #667eea;
    border-radius: 4px;
}

.status-badge-container {
    margin-bottom: 20px;
}

.status-badge-container .alert {
    border-left: 4px solid;
    border-radius: 8px;
}

.documents-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.document-item {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.document-item:hover {
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.1);
    transform: translateY(-2px);
}

.document-icon {
    color: #667eea;
    margin-bottom: 10px;
}

.document-info h6 {
    color: #333;
    font-weight: 600;
    margin-bottom: 10px;
    font-size: 0.95rem;
}

.action-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
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

@media (max-width: 768px) {
    .application-card {
        padding: 20px;
    }

    .documents-grid {
        grid-template-columns: 1fr;
    }

    .action-buttons {
        flex-direction: column;
    }

    .action-buttons .btn {
        width: 100%;
    }
}
</style>
@endsection
