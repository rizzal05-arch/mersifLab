@extends('layouts.app')

@section('title', 'My Profile')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/profile.css') }}">
<style>
.avatar-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}
.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-size: 3rem;
    font-weight: bold;
    border: 4px solid white;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.profile-avatar-wrapper {
    width: 120px;
    height: 120px;
    display: inline-block;
}

.avatar-upload-btn {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 36px;
    height: 36px;
    background: #2196f3;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    cursor: pointer;
    border: 3px solid white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
    z-index: 10;
}

.avatar-upload-btn:hover {
    background: #1976d2;
    transform: scale(1.1);
}

.avatar-upload-btn i {
    font-size: 14px;
}
</style>
@endsection

@section('content')
<section class="profile-section py-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="profile-sidebar">
                    <!-- Profile Avatar -->
                    <div class="profile-avatar-section text-center">
                        <div class="profile-avatar-wrapper position-relative mx-auto">
                            <div class="profile-avatar mx-auto" id="profileAvatar">
                                @if(Auth::user()->avatar)
                                    <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="avatar-image">
                                @else
                                    <span class="avatar-letter">{{ strtoupper(substr(Auth::user()->name ?? Auth::user()->email ?? 'S', 0, 1)) }}</span>
                                @endif
                            </div>
                            <label for="avatarUpload" class="avatar-upload-btn" title="Upload Foto Profil">
                                <i class="fas fa-camera"></i>
                                <input type="file" id="avatarUpload" name="avatar" accept="image/*" style="display: none;">
                            </label>
                        </div>
                        <h5 class="profile-name mt-3">{{ Auth::user()->name ?? 'Student' }}</h5>
                        <p class="profile-email">{{ Auth::user()->email ?? 'student@gmail.com' }}</p>
                    </div>
                    
                    <!-- Navigation Menu -->
                    <nav class="profile-nav mt-4">
                        @if(auth()->user()->isTeacher())
                            <a href="{{ route('teacher.profile') }}" class="profile-nav-item active">
                                <i class="fas fa-user me-2"></i> My Profile
                            </a>
                            <a href="{{ route('teacher.courses') }}" class="profile-nav-item">
                                <i class="fas fa-book me-2"></i> My Courses
                            </a>
                            <a href="{{ route('teacher.manage.content') }}" class="profile-nav-item">
                                <i class="fas fa-folder-open me-2"></i> Manage Content
                            </a>
                            <a href="{{ route('teacher.statistics') }}" class="profile-nav-item">
                                <i class="fas fa-chart-bar me-2"></i> Statistics
                            </a>
                            <a href="{{ route('teacher.purchase.history') }}" class="profile-nav-item">
                                <i class="fas fa-history me-2"></i> Purchase History
                            </a>
                            <a href="{{ route('teacher.notifications') }}" class="profile-nav-item">
                                <i class="fas fa-bell me-2"></i> Notifications
                            </a>
                        @else
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
                        @endif
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
                </div>
            </div>
        </div>
    </div>
</section>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const avatarUpload = document.getElementById('avatarUpload');
    const profileAvatar = document.getElementById('profileAvatar');
    
    if (avatarUpload) {
        avatarUpload.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            // Validate file
            const maxSize = 2 * 1024 * 1024; // 2MB
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            
            if (!allowedTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Format Tidak Valid',
                    text: 'Format file harus JPG, PNG, GIF, atau WEBP',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc3545'
                });
                return;
            }
            
            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Terlalu Besar',
                    text: 'Ukuran file maksimal 2MB',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc3545'
                });
                return;
            }
            
            // Show loading
            Swal.fire({
                title: 'Mengupload...',
                text: 'Mohon tunggu',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Create FormData
            const formData = new FormData();
            formData.append('avatar', file);
            formData.append('_token', '{{ csrf_token() }}');
            
            // Upload via AJAX
            fetch('{{ route("profile.upload-avatar") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    // Update avatar image
                    profileAvatar.innerHTML = `<img src="${data.avatar_url}" alt="{{ Auth::user()->name }}" class="avatar-image">`;
                    
                    // Update navbar avatar if exists
                    const navbarAvatar = document.querySelector('.navbar-avatar img');
                    if (navbarAvatar) {
                        navbarAvatar.src = data.avatar_url;
                    }
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#28a745',
                        timer: 2000
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(error => {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: 'Gagal mengupload foto profil. Silakan coba lagi.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc3545'
                });
                console.error('Error:', error);
            });
        });
    }
});
</script>
@endsection