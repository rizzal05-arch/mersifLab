@php
    $currentRoute = Route::currentRouteName();
    use Illuminate\Support\Facades\Storage;
@endphp

<div class="profile-sidebar">
    <!-- Profile Avatar -->
    <div class="profile-avatar-section text-center">
        <div class="profile-avatar-wrapper position-relative mx-auto">
            <div class="profile-avatar mx-auto" id="profileAvatar">
                @if(Auth::user()->avatar)
                    <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="avatar-image">
                @else
                    <span class="avatar-letter">{{ strtoupper(substr(Auth::user()->email ?? 'T', 0, 1)) }}</span>
                @endif
            </div>
            <label for="avatarUpload" class="avatar-upload-btn" title="Upload Foto Profil">
                <i class="fas fa-camera"></i>
                <input type="file" id="avatarUpload" name="avatar" accept="image/*" style="display: none;">
            </label>
        </div>
        <h5 class="profile-name mt-3">{{ Auth::user()->name ?? 'Teacher' }}</h5>
        <p class="profile-email">{{ Auth::user()->email ?? 'teacher@gmail.com' }}</p>
        <span class="profile-role-badge {{ Auth::user()->isTeacher() ? 'badge-teacher' : 'badge-student' }}">
            <i class="fas {{ Auth::user()->isTeacher() ? 'fa-chalkboard-teacher' : 'fa-user-graduate' }} me-1"></i>
            {{ Auth::user()->isTeacher() ? 'Teacher' : 'Student' }}
        </span>
    </div>
    
    <!-- Navigation Menu -->
    <nav class="profile-nav mt-4">
        <a href="{{ route('teacher.profile') }}" class="profile-nav-item {{ $currentRoute === 'teacher.profile' ? 'active' : '' }}">
            <i class="fas fa-user me-2"></i> My Profile
        </a>
        <a href="{{ route('teacher.courses') }}" class="profile-nav-item {{ $currentRoute === 'teacher.courses' ? 'active' : '' }}">
            <i class="fas fa-book me-2"></i> My Courses
        </a>
        <a href="{{ route('teacher.manage.content') }}" class="profile-nav-item {{ $currentRoute === 'teacher.manage.content' ? 'active' : '' }}">
            <i class="fas fa-folder-open me-2"></i> Manage Content
        </a>
        <a href="{{ route('teacher.statistics') }}" class="profile-nav-item {{ $currentRoute === 'teacher.statistics' ? 'active' : '' }}">
            <i class="fas fa-chart-bar me-2"></i> Statistics
        </a>
        <a href="{{ route('teacher.purchase.history') }}" class="profile-nav-item {{ $currentRoute === 'teacher.purchase.history' ? 'active' : '' }}">
            <i class="fas fa-wallet me-2"></i> Financial Management
        </a>
        <a href="{{ route('teacher.notification-preferences') }}" class="profile-nav-item {{ $currentRoute === 'teacher.notification-preferences' ? 'active' : '' }}">
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

<style>
.profile-avatar-wrapper {
    width: 120px;
    height: 120px;
    display: inline-block;
}

.profile-avatar {
    width: 120px !important;
    height: 120px !important;
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

.avatar-letter {
    color: #fff;
    font-size: 2rem;
    font-weight: 700;
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

.avatar-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.profile-name {
    font-size: 1.2rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 8px;
}

.profile-email {
    font-size: 0.85rem;
    color: #6b7280;
    margin-bottom: 12px;
}

/* Profile Role Badge Styles */
.profile-role-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
    margin-top: 4px;
}

.profile-role-badge i {
    font-size: 0.85rem;
}

/* Student Badge - Blue Theme */
.badge-student {
    background: linear-gradient(135deg, #1A76D1 0%, #4A9EE0 100%);
    color: #ffffff;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.badge-student:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(26, 118, 209, 0.4);
}

/* Teacher Badge - Purple/Violet Theme */
.badge-teacher {
    background: linear-gradient(135deg, #8B5CF6 0%, #6D28D9 100%);
    color: #ffffff;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.badge-teacher:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.4);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const avatarUpload = document.getElementById('avatarUpload');
    const profileAvatar = document.getElementById('profileAvatar');
    
    if (avatarUpload && profileAvatar) {
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