@extends('layouts.app')

@section('title', 'Teacher Profile')

@section('content')
<section class="profile-section py-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                @include('teacher.partials.sidebar')
            </div>
            
            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="profile-content">
                    <div class="profile-header">
                        <h2 class="profile-title">My Profile</h2>
                        <p class="profile-subtitle">Manage your teacher profile information</p>
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
                    
                    <form action="{{ route('teacher.profile.update') }}" method="POST" enctype="multipart/form-data">
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
                                       placeholder="teacher@example.com" value="{{ old('email', Auth::user()->email ?? '') }}" 
                                       required readonly>
                                <div class="email-lock-icon">
                                    <i class="fas fa-lock text-muted"></i>
                                </div>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>Email cannot be changed
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   placeholder="08xx-xxxx-xxxx" value="{{ old('phone', Auth::user()->phone ?? '') }}">
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address" 
                                   placeholder="Your Address" value="{{ old('address', Auth::user()->address ?? '') }}">
                        </div>
                        
                        <div class="mb-4">
                            <label for="bio" class="form-label">Biography / Bio</label>
                            <textarea class="form-control" id="bio" name="bio" rows="5" 
                                      placeholder="Tell students about yourself and your teaching experience">{{ old('bio', Auth::user()->bio ?? '') }}</textarea>
                            <small class="text-muted">This information will be visible to students</small>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@section('styles')
<style>
.email-disabled-wrapper {
    position: relative;
}

.email-disabled {
    background-color: #f8f9fa !important;
    cursor: not-allowed !important;
    border-color: #dee2e6 !important;
    padding-right: 40px !important;
    opacity: 0.7;
}

.email-disabled:focus {
    background-color: #f8f9fa !important;
    border-color: #dee2e6 !important;
    box-shadow: none !important;
    cursor: not-allowed !important;
}

.email-lock-icon {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    z-index: 10;
    color: #6c757d;
}
</style>
@endsection
