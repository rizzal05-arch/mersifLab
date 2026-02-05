@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<section class="profile-section py-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="profile-sidebar">
                    <!-- Profile Avatar -->
                    <div class="profile-avatar-section text-center">
                        <div class="profile-avatar mx-auto">
                            <span class="avatar-letter">{{ strtoupper(substr(Auth::user()->email ?? 'S', 0, 1)) }}</span>
                        </div>
                        <h5 class="profile-name mt-3">Student</h5>
                        <p class="profile-email">{{ Auth::user()->email ?? 'student@gmail.com' }}</p>
                    </div>
                    
                    <!-- Navigation Menu -->
                    <nav class="profile-nav mt-4">
                        <a href="{{ route('profile') }}" class="profile-nav-item active">
                            <i class="fas fa-user me-2"></i> My Profile
                        </a>
                        <a href="{{ route('my-courses') }}" class="profile-nav-item">
                            <i class="fas fa-book me-2"></i> My Courses
                        </a>
                        <a href="{{ route('purchase-history') }}" class="profile-nav-item">
                            <i class="fas fa-history me-2"></i> Purchase History
                        </a>
                        <a href="{{ route('notification-preferences') }}" class="profile-nav-item">
                            <i class="fas fa-bell me-2"></i> Notification Preferences
                        </a>
                    </nav>
                    
                    <!-- Logout Button -->
                    <form action="{{ route('logout') }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout Account
                        </button>
                    </form>
                </div>
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
                            <label for="full_name" class="form-label">Full Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="full_name" name="full_name" 
                                   placeholder="Your Full Name" value="{{ old('full_name', Auth::user()->full_name ?? '') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email<span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="student@gmail.com" value="{{ old('email', Auth::user()->email ?? '') }}" required readonly>
                            <small class="text-muted">Email cannot be changed</small>
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
                    
                    @if(Auth::user()->isStudent() && !Auth::user()->hasPendingTeacherApplication())
                    <div class="mt-4 text-center">
                        <hr>
                        <p class="text-muted mb-3">Want to share your knowledge and help others learn?</p>
                        <a href="{{ route('teacher.application.create') }}" class="btn btn-outline-primary">
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
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection