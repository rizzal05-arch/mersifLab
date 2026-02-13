@extends('layouts.app')

@section('title', 'My Certificates')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/profile.css') }}">
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
                        <div class="certificates-grid">
                            @foreach($certificates as $certificate)
                                <div class="certificate-card">
                                    <div class="certificate-badge">
                                        <i class="fas fa-award"></i>
                                        <span>Certified</span>
                                    </div>
                                    <div class="certificate-icon-wrapper">
                                        <div class="certificate-icon">
                                            <i class="fas fa-certificate"></i>
                                        </div>
                                    </div>
                                    <div class="certificate-body">
                                        <h5 class="certificate-title">{{ $certificate->course->name }}</h5>
                                        <div class="certificate-meta">
                                            <div class="meta-item">
                                                <i class="fas fa-calendar-alt"></i>
                                                <span>{{ $certificate->issued_at->format('d M Y') }}</span>
                                            </div>
                                            <div class="certificate-code-wrapper">
                                                <div class="certificate-code">
                                                    <i class="fas fa-fingerprint"></i>
                                                    <span>{{ $certificate->certificate_code }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="certificate-actions">
                                        <a href="{{ route('certificate.preview', $certificate->id) }}" 
                                           class="btn btn-outline-primary btn-certificate" 
                                           target="_blank">
                                            <i class="fas fa-eye"></i>
                                            <span>Preview</span>
                                        </a>
                                        <a href="{{ route('certificate.download', $certificate->id) }}" 
                                           class="btn btn-primary btn-certificate">
                                            <i class="fas fa-download"></i>
                                            <span>Download</span>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $certificates->links() }}
                        </div>
                    @else
                        <div class="empty-state text-center">
                            <div class="empty-icon">
                                <i class="fas fa-certificate"></i>
                            </div>
                            <h4>No Certificates Yet</h4>
                            <p>Complete courses to earn your certificates!</p>
                            <a href="{{ route('courses') }}" class="btn btn-primary mt-3">
                                Browse Courses
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection