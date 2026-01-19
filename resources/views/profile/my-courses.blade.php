@extends('layouts.app')

@section('title', 'Profile Page (My Course)')

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
                        <a href="{{ route('profile') }}" class="profile-nav-item">
                            <i class="fas fa-user me-2"></i> My Profile
                        </a>
                        <a href="{{ route('my-courses') }}" class="profile-nav-item active">
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
                    <div class="profile-header mb-4">
                        <h2 class="profile-title">My Courses</h2>
                        <p class="profile-subtitle">Access and continue your enrolled courses</p>
                    </div>
                    
                    <!-- Course List -->
                    <div class="courses-list">
                        <!-- Course Item 1 -->
                        <div class="course-card">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <img src="{{ asset('images/courses/course1.jpg') }}" alt="Course Thumbnail" class="course-thumbnail">
                                </div>
                                <div class="col-md-6">
                                    <h5 class="course-title">Belajar Desain Grafis untuk Desain Konten Digital</h5>
                                    <p class="course-meta">
                                        <i class="fas fa-chalkboard-teacher me-1"></i> Teacher's Name
                                    </p>
                                    <div class="progress-section">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="progress-label">Your Progress</span>
                                            <span class="progress-percentage">100%</span>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 text-md-end mt-3 mt-md-0">
                                    <a href="{{ route('course.detail', 1) }}" class="btn btn-primary w-100">
                                        <i class="fas fa-play me-2"></i>Mulai Belajar
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Course Item 2 -->
                        <div class="course-card">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <img src="{{ asset('images/courses/course2.jpg') }}" alt="Course Thumbnail" class="course-thumbnail">
                                </div>
                                <div class="col-md-6">
                                    <h5 class="course-title">Membangun Chatbot Berbasis Artificial Intelligence</h5>
                                    <p class="course-meta">
                                        <i class="fas fa-chalkboard-teacher me-1"></i> Teacher's Name
                                    </p>
                                    <div class="progress-section">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="progress-label">Your Progress</span>
                                            <span class="progress-percentage">100%</span>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 text-md-end mt-3 mt-md-0">
                                    <a href="{{ route('course.detail', 2) }}" class="btn btn-primary w-100">
                                        <i class="fas fa-play me-2"></i>Mulai Belajar
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Course Item 3 -->
                        <div class="course-card">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <img src="{{ asset('images/courses/course3.jpg') }}" alt="Course Thumbnail" class="course-thumbnail">
                                </div>
                                <div class="col-md-6">
                                    <h5 class="course-title">Web Development Dasar untuk Kebutuhan Digital</h5>
                                    <p class="course-meta">
                                        <i class="fas fa-chalkboard-teacher me-1"></i> Teacher's Name
                                    </p>
                                    <div class="progress-section">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="progress-label">Your Progress</span>
                                            <span class="progress-percentage">100%</span>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 text-md-end mt-3 mt-md-0">
                                    <a href="{{ route('course.detail', 3) }}" class="btn btn-primary w-100">
                                        <i class="fas fa-play me-2"></i>Mulai Belajar
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Empty State (uncomment if no courses) -->
                        <!-- 
                        <div class="empty-state text-center py-5">
                            <i class="fas fa-book-open fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">No Courses Yet</h4>
                            <p class="text-muted">You haven't enrolled in any courses yet.</p>
                            <a href="{{ route('courses') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-search me-2"></i>Browse Courses
                            </a>
                        </div>
                        -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection