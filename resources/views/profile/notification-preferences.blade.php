@extends('layouts.app')

@section('title', 'Notification Preferences')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/profile.css') }}">
@endsection

@section('content')
<section class="profile-section py-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="profile-sidebar">
                    <div class="profile-avatar-section text-center">
                        <div class="profile-avatar mx-auto">
                            <span class="avatar-letter">{{ strtoupper(substr(Auth::user()->name ?? Auth::user()->email ?? 'S', 0, 1)) }}</span>
                        </div>
                        <h5 class="profile-name mt-3">{{ Auth::user()->name ?? 'Student' }}</h5>
                        <p class="profile-email">{{ Auth::user()->email ?? 'student@gmail.com' }}</p>
                    </div>
                    
                    <nav class="profile-nav mt-4">
                        @if(auth()->user()->isTeacher())
                            <a href="{{ route('teacher.profile') }}" class="profile-nav-item">
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
                            <a href="{{ route('teacher.notifications') }}" class="profile-nav-item active">
                                <i class="fas fa-bell me-2"></i> Notifications
                            </a>
                        @else
                            <a href="{{ route('profile') }}" class="profile-nav-item">
                                <i class="fas fa-user me-2"></i> My Profile
                            </a>
                            <a href="{{ route('my-courses') }}" class="profile-nav-item">
                                <i class="fas fa-book me-2"></i> My Courses
                            </a>
                            <a href="{{ route('purchase-history') }}" class="profile-nav-item">
                                <i class="fas fa-history me-2"></i> Purchase History
                            </a>
                            <a href="{{ route('notification-preferences') }}" class="profile-nav-item active">
                                <i class="fas fa-bell me-2"></i> Notification Preferences
                            </a>
                        @endif
                    </nav>
                    
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
                        <h2 class="profile-title">Notification Preferences</h2>
                        <p class="profile-subtitle">Manage the types of notifications you receive</p>
                    </div>
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <form action="{{ auth()->user()->isTeacher() ? route('teacher.notification-preferences.update') : route('notification-preferences.update') }}" method="POST" id="notificationForm">
                        @csrf
                        @method('PUT')
                        
                        @php
                            $pref = $preferences ?? auth()->user()->getNotificationPreference();
                        @endphp
                        
                        <!-- Course & Content Updates -->
                        <div class="notification-group">
                            <div class="notification-group-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h5>Course & Content Updates</h5>
                                    <small class="text-muted">Get notified about new courses and content</small>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input toggle-all" type="checkbox" id="toggleCourseUpdates" data-group="course-updates" 
                                        {{ ($pref->new_course && $pref->new_chapter && $pref->new_module) ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="notification-group-body">
                                <div class="notification-item">
                                    <div class="form-check">
                                        <input class="form-check-input notification-checkbox" type="checkbox" name="new_course" id="new_course" value="1"
                                            data-group="course-updates" {{ $pref->new_course ? 'checked' : '' }}>
                                        <label class="form-check-label" for="new_course">New courses available</label>
                                    </div>
                                </div>
                                <div class="notification-item">
                                    <div class="form-check">
                                        <input class="form-check-input notification-checkbox" type="checkbox" name="new_chapter" id="new_chapter" value="1"
                                            data-group="course-updates" {{ $pref->new_chapter ? 'checked' : '' }}>
                                        <label class="form-check-label" for="new_chapter">New chapters in enrolled courses</label>
                                    </div>
                                </div>
                                <div class="notification-item">
                                    <div class="form-check">
                                        <input class="form-check-input notification-checkbox" type="checkbox" name="new_module" id="new_module" value="1"
                                            data-group="course-updates" {{ $pref->new_module ? 'checked' : '' }}>
                                        <label class="form-check-label" for="new_module">New modules in enrolled courses</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        @if(auth()->user()->isTeacher())
                        <!-- Teacher Notifications -->
                        <div class="notification-group">
                            <div class="notification-group-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h5>Teaching Updates</h5>
                                    <small class="text-muted">Get notified about your courses and students</small>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input toggle-all" type="checkbox" id="toggleTeaching" data-group="teaching" 
                                        {{ ($pref->module_approved && $pref->student_enrolled && $pref->course_rated && $pref->course_completed) ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="notification-group-body">
                                <div class="notification-item">
                                    <div class="form-check">
                                        <input class="form-check-input notification-checkbox" type="checkbox" name="module_approved" id="module_approved" value="1"
                                            data-group="teaching" {{ $pref->module_approved ? 'checked' : '' }}>
                                        <label class="form-check-label" for="module_approved">Module approved by admin</label>
                                    </div>
                                </div>
                                <div class="notification-item">
                                    <div class="form-check">
                                        <input class="form-check-input notification-checkbox" type="checkbox" name="student_enrolled" id="student_enrolled" value="1"
                                            data-group="teaching" {{ $pref->student_enrolled ? 'checked' : '' }}>
                                        <label class="form-check-label" for="student_enrolled">New student enrolled in your course</label>
                                    </div>
                                </div>
                                <div class="notification-item">
                                    <div class="form-check">
                                        <input class="form-check-input notification-checkbox" type="checkbox" name="course_rated" id="course_rated" value="1"
                                            data-group="teaching" {{ $pref->course_rated ? 'checked' : '' }}>
                                        <label class="form-check-label" for="course_rated">Course received a rating</label>
                                    </div>
                                </div>
                                <div class="notification-item">
                                    <div class="form-check">
                                        <input class="form-check-input notification-checkbox" type="checkbox" name="course_completed" id="course_completed" value="1"
                                            data-group="teaching" {{ $pref->course_completed ? 'checked' : '' }}>
                                        <label class="form-check-label" for="course_completed">Student completed your course</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Announcements & Promotions -->
                        <div class="notification-group">
                            <div class="notification-group-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h5>Announcements & Promotions</h5>
                                    <small class="text-muted">Get notified about special offers and updates</small>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input toggle-all" type="checkbox" id="toggleAnnouncements" data-group="announcements" 
                                        {{ ($pref->announcements && $pref->promotions) ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="notification-group-body">
                                <div class="notification-item">
                                    <div class="form-check">
                                        <input class="form-check-input notification-checkbox" type="checkbox" name="announcements" id="announcements" value="1"
                                            data-group="announcements" {{ $pref->announcements ? 'checked' : '' }}>
                                        <label class="form-check-label" for="announcements">Platform announcements</label>
                                    </div>
                                </div>
                                <div class="notification-item">
                                    <div class="form-check">
                                        <input class="form-check-input notification-checkbox" type="checkbox" name="promotions" id="promotions" value="1"
                                            data-group="announcements" {{ $pref->promotions ? 'checked' : '' }}>
                                        <label class="form-check-label" for="promotions">Special offers and promotions</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Learning Updates -->
                        <div class="notification-group">
                            <div class="notification-group-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h5>Learning Updates</h5>
                                    <small class="text-muted">Get personalized learning recommendations</small>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input toggle-all" type="checkbox" id="toggleLearning" data-group="learning" 
                                        {{ ($pref->course_recommendations && $pref->learning_stats) ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="notification-group-body">
                                <div class="notification-item">
                                    <div class="form-check">
                                        <input class="form-check-input notification-checkbox" type="checkbox" name="course_recommendations" id="course_recommendations" value="1"
                                            data-group="learning" {{ $pref->course_recommendations ? 'checked' : '' }}>
                                        <label class="form-check-label" for="course_recommendations">Course recommendations</label>
                                    </div>
                                </div>
                                <div class="notification-item">
                                    <div class="form-check">
                                        <input class="form-check-input notification-checkbox" type="checkbox" name="learning_stats" id="learning_stats" value="1"
                                            data-group="learning" {{ $pref->learning_stats ? 'checked' : '' }}>
                                        <label class="form-check-label" for="learning_stats">Learning statistics and progress</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Info Text -->
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>Note: Changes will be applied immediately. You'll still receive important transactional notifications related to your account.</small>
                        </div>
                        
                        <!-- Save Button -->
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Save Preferences
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    // Toggle all checkboxes in a group
    document.querySelectorAll('.toggle-all').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const group = this.dataset.group;
            const checkboxes = document.querySelectorAll(`.notification-checkbox[data-group="${group}"]`);
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    });
    
    // Update toggle switch when individual checkboxes change
    document.querySelectorAll('.notification-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const group = this.dataset.group;
            const groupCheckboxes = document.querySelectorAll(`.notification-checkbox[data-group="${group}"]`);
            const toggle = document.querySelector(`.toggle-all[data-group="${group}"]`);
            
            const allChecked = Array.from(groupCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(groupCheckboxes).some(cb => cb.checked);
            
            if (allChecked) {
                toggle.checked = true;
                toggle.indeterminate = false;
            } else if (someChecked) {
                toggle.indeterminate = true;
            } else {
                toggle.checked = false;
                toggle.indeterminate = false;
            }
        });
    });
</script>
@endsection