@php
    $currentRoute = Route::currentRouteName();
    use Illuminate\Support\Facades\Storage;
@endphp

<!-- Teacher Sidebar - Using Admin Structure -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <img src="{{ asset('images/logo.png') }}" alt="REKA MERSIF Logo">
        </div>
        <button class="sidebar-toggler" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Profile Section -->
    <div class="sidebar-profile">
        <div class="profile-avatar-wrapper">
            @if(Auth::user()->avatar)
                <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="profile-avatar">
            @else
                <div class="profile-avatar profile-avatar-placeholder">
                    {{ strtoupper(substr(Auth::user()->email ?? 'T', 0, 1)) }}
                </div>
            @endif
        </div>
        <div class="profile-info">
            <h6 class="profile-name">{{ Auth::user()->name ?? 'Teacher' }}</h6>
            <p class="profile-email">{{ Auth::user()->email ?? 'teacher@gmail.com' }}</p>
            <span class="profile-role-badge">
                <i class="fas fa-chalkboard-teacher me-1"></i>Teacher
            </span>
        </div>
    </div>

    <ul class="sidebar-menu">
        <li>
            <a href="{{ route('teacher.dashboard') }}" class="@if($currentRoute === 'teacher.dashboard') active @endif">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="{{ route('teacher.profile') }}" class="@if($currentRoute === 'teacher.profile') active @endif">
                <i class="fas fa-user"></i>
                <span>My Profile</span>
            </a>
        </li>
        <li>
            <a href="{{ route('teacher.courses') }}" class="@if($currentRoute === 'teacher.courses') active @endif">
                <i class="fas fa-book"></i>
                <span>My Courses</span>
            </a>
        </li>
        <li>
            <a href="{{ route('teacher.manage.content') }}" class="@if($currentRoute === 'teacher.manage.content') active @endif">
                <i class="fas fa-folder-open"></i>
                <span>Manage Content</span>
            </a>
        </li>
        <li>
            <a href="{{ route('teacher.statistics') }}" class="@if($currentRoute === 'teacher.statistics') active @endif">
                <i class="fas fa-chart-bar"></i>
                <span>Statistics</span>
            </a>
        </li>
        <li>
            <a href="{{ route('teacher.finance.management') }}" class="@if($currentRoute === 'teacher.finance.management') active @endif">
                <i class="fas fa-wallet"></i>
                <span>Finance Management</span>
            </a>
        </li>
        <li>
            <a href="{{ route('teacher.notifications') }}" class="@if($currentRoute === 'teacher.notifications' || $currentRoute === 'teacher.notification-preferences') active @endif">
                <i class="fas fa-bell"></i>
                <span>Notifications</span>
                @php
                    $unreadNotificationsCount = \App\Models\Notification::where('user_id', Auth::id())->where('is_read', false)->count();
                @endphp
                @if($unreadNotificationsCount > 0)
                    <span class="badge bg-danger ms-2" style="font-size: 10px;">{{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}</span>
                @endif
            </a>
        </li>
        <li>
            <a href="{{ route('teacher.settings') }}" class="@if($currentRoute === 'teacher.settings') active @endif">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </li>
    </ul>

    <!-- Logout -->
    <div class="sidebar-logout">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-logout">
                <i class="fas fa-sign-out-alt me-2"></i>Logout
            </button>
        </form>
    </div>
</div>

<style>
/* Teacher Sidebar Profile Section - Match Admin Style */
.sidebar-profile {
    padding: 20px;
    border-bottom: 1px solid #e5e7eb;
    text-align: center;
    background: linear-gradient(180deg, #FFFFFF 0%, #F0F2F5 100%);
}

.profile-avatar-wrapper {
    width: 60px;
    height: 60px;
    margin: 0 auto 12px;
    position: relative;
}

.profile-avatar {
    width: 60px !important;
    height: 60px !important;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #e5e7eb;
}

.profile-avatar-placeholder {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    font-weight: bold;
    border: 3px solid #e5e7eb;
}

.profile-info {
    color: #374151;
}

.profile-name {
    font-size: 0.9rem;
    font-weight: 600;
    margin: 0 0 4px 0;
    color: #374151;
}

.profile-email {
    font-size: 0.75rem;
    margin: 0 0 8px 0;
    opacity: 0.8;
    color: #6b7280;
}

.profile-role-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    background: linear-gradient(135deg, #8B5CF6 0%, #6D28D9 100%);
    color: white;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
}

/* Logout Button */
.sidebar-logout {
    padding: 20px;
    border-top: 1px solid #e5e7eb;
}

.btn-logout {
    width: 100%;
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #dc3545;
    padding: 10px;
    border-radius: 6px;
    font-size: 0.85rem;
    transition: all 0.3s ease;
}

.btn-logout:hover {
    background: #fee2e2;
    color: #dc3545;
    border-color: #fca5a5;
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