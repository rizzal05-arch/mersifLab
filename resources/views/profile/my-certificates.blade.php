@extends('layouts.app')

@section('title', 'My Certificates')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/profile.css') }}">
<style>
.btn-xs {
    font-size: 0.7rem !important;
    padding: 0.25rem 0.5rem !important;
    border-radius: 0.2rem !important;
    line-height: 1.2 !important;
}

.btn-xs i {
    font-size: 0.65rem !important;
}

.certificate-actions {
    display: flex;
    gap: 0.25rem;
}
</style>
@endsection

@section('content')
<section class="profile-section py-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                @include('profile.partials.sidebar')
            </div>
            
            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="profile-content">
                    <div class="profile-header">
                        <h2 class="profile-title">My Certificates</h2>
                        <p class="profile-subtitle">View and download your course completion certificates</p>
                    </div>
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if($certificates->count() > 0)
                        <div class="row">
                            @foreach($certificates as $certificate)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="certificate-card">
                                        <div class="certificate-icon">
                                            <i class="fas fa-certificate"></i>
                                        </div>
                                        <div class="certificate-info">
                                            <h6 class="certificate-title">{{ $certificate->course->name }}</h6>
                                            <p class="certificate-meta">
                                                <i class="fas fa-calendar-alt me-1"></i>
                                                {{ $certificate->issued_at->format('d M Y') }}
                                            </p>
                                            <p class="certificate-code">
                                                <i class="fas fa-hashtag me-1"></i>
                                                {{ $certificate->certificate_code }}
                                            </p>
                                        </div>
                                        <div class="certificate-actions">
                                            <a href="{{ route('certificate.preview', $certificate->id) }}" 
                                               class="btn btn-outline-primary btn-xs me-1" 
                                               target="_blank">
                                                <i class="fas fa-eye"></i> Preview
                                            </a>
                                            <a href="{{ route('certificate.download', $certificate->id) }}" 
                                               class="btn btn-primary btn-xs">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $certificates->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-certificate fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">No Certificates Yet</h4>
                                <p class="text-muted">Complete courses to earn your certificates!</p>
                                <a href="{{ route('courses') }}" class="btn btn-primary mt-3">
                                    <i class="fas fa-book me-2"></i>Browse Courses
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
.certificate-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.certificate-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

.certificate-icon {
    text-align: center;
    margin-bottom: 1rem;
}

.certificate-icon i {
    font-size: 3rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.certificate-info {
    flex-grow: 1;
    margin-bottom: 1rem;
}

.certificate-title {
    font-weight: 600;
    color: #333;
    margin-bottom: 0.5rem;
    font-size: 1rem;
    line-height: 1.4;
}

.certificate-meta {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.certificate-code {
    font-size: 0.8rem;
    color: #8b5cf6;
    font-weight: 500;
    margin: 0;
}

.certificate-actions {
    display: flex;
    gap: 0.5rem;
}

.empty-state {
    max-width: 400px;
    margin: 0 auto;
}

@media (max-width: 768px) {
    .certificate-actions {
        flex-direction: column;
    }
    
    .certificate-actions .btn {
        width: 100%;
        text-align: center;
    }
}
</style>
@endpush
