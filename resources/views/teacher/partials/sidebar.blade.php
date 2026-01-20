@php
    $currentRoute = Route::currentRouteName();
@endphp

<div class="profile-sidebar">
    <!-- Profile Avatar -->
    <div class="profile-avatar-section text-center">
        <div class="profile-avatar mx-auto">
            <span class="avatar-letter">{{ strtoupper(substr(Auth::user()->email ?? 'T', 0, 1)) }}</span>
        </div>
        <h5 class="profile-name mt-3">{{ Auth::user()->name ?? 'Teacher' }}</h5>
        <p class="profile-email">{{ Auth::user()->email ?? 'teacher@gmail.com' }}</p>
        <p class="badge bg-info">Teacher</p>
    </div>
    
    <!-- Navigation Menu -->
    <nav class="profile-nav mt-4">
        <a href="{{ route('teacher.profile') }}" class="profile-nav-item {{ $currentRoute === 'teacher.profile' ? 'active' : '' }}">
            <i class="fas fa-user me-2"></i> My Profile
        </a>
        <a href="{{ route('teacher.courses') }}" class="profile-nav-item {{ $currentRoute === 'teacher.courses' ? 'active' : '' }}">
            <i class="fas fa-book me-2"></i> My Courses
        </a>
        <a href="{{ route('teacher.manage.content') }}" class="profile-nav-item">
            <i class="fas fa-folder-open me-2"></i> Manage Content
        </a>
        <a href="{{ route('teacher.purchase.history') }}" class="profile-nav-item {{ $currentRoute === 'teacher.purchase.history' ? 'active' : '' }}">
            <i class="fas fa-history me-2"></i> Purchase History
        </a>
        <a href="{{ route('teacher.notifications') }}" class="profile-nav-item {{ $currentRoute === 'teacher.notifications' ? 'active' : '' }}">
            <i class="fas fa-bell me-2"></i> Notifications
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
