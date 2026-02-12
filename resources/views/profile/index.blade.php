@extends('layouts.app')

@section('title', 'My Profile')

@php
use Illuminate\Support\Facades\Storage;
@endphp

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
                        <h2 class="profile-title">Profile</h2>
                        <p class="profile-subtitle">Add information about yourself</p>
                    </div>
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
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
                    
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   placeholder="Your Full Name" value="{{ old('name', Auth::user()->name ?? '') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email<span class="text-danger">*</span></label>
                            <div class="email-disabled-wrapper">
                                <input type="email" class="form-control email-disabled" id="email" name="email" 
                                       placeholder="your.email@example.com" value="{{ old('email', Auth::user()->email ?? '') }}" 
                                       required readonly>
                                <div class="email-lock-icon">
                                    <i class="fas fa-lock"></i>
                                </div>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>Email cannot be changed
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="telephone" class="form-label">Telephone Number</label>
                            <input type="tel" class="form-control" id="telephone" name="telephone" 
                                   placeholder="08xx-xxxx-xxxx" value="{{ old('telephone', Auth::user()->telephone ?? '') }}">
                        </div>
                        
                        <div class="mb-4">
                            <label for="biography" class="form-label">Biography</label>
                            <textarea class="form-control" id="biography" name="biography" rows="5" 
                                      placeholder="Describe yourself">{{ old('biography', Auth::user()->biography ?? '') }}</textarea>
                            <small class="text-muted">Tell us about yourself</small>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                        </div>
                    </form>

                    <div class="mt-4">
                        <hr>
                        <h5 class="mb-3">Langganan</h5>
                        @if(Auth::user()->isSubscriber())
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>Anda saat ini berlangganan dan memiliki akses penuh ke semua materi.
                            </div>
                        @else
                            <p class="text-muted">Dengan berlangganan, Anda akan mendapatkan akses penuh ke semua materi di platform ini.</p>
                            <form action="{{ url('/subscribe') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-bell me-2"></i>Langganan Sekarang
                                </button>
                            </form>
                        @endif
                    </div>
                    
                    @if(Auth::user()->isStudent() && !Auth::user()->hasPendingTeacherApplication() && !Auth::user()->hasRejectedTeacherApplication())
                    <div class="mt-4 text-center">
                        <hr>
                        <p class="text-muted mb-3">Want to share your knowledge and help others learn?</p>
                        <a href="{{ route('teacher.application.create') }}" class="btn btn-outline-primary" target="_blank" rel="noopener noreferrer">
                            <i class="fas fa-chalkboard-teacher me-2"></i>Want to become a teacher?
                        </a>
                    </div>
                    @elseif(Auth::user()->hasPendingTeacherApplication())
                    <div class="mt-4 text-center">
                        <hr>
                        <div class="alert alert-info">
                            <i class="fas fa-clock me-2"></i>
                            Your teacher application is under review. We'll notify you once there's an update.
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('teacher.application.preview') }}" class="btn btn-primary">
                                <i class="fas fa-eye me-2"></i>View Application
                            </a>
                        </div>
                    </div>
                    @elseif(Auth::user()->hasRejectedTeacherApplication())
                    <div class="mt-4 text-center">
                        <hr>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Your teacher application was rejected. You can edit and resubmit your application.
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('teacher.application.preview') }}" class="btn btn-primary me-2">
                                <i class="fas fa-eye me-2"></i>View Application
                            </a>
                            <a href="{{ route('teacher.application.edit') }}" class="btn btn-outline-warning">
                                <i class="fas fa-edit me-2"></i>Edit & Resubmit
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