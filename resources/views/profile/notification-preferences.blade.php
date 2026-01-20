@extends('layouts.app')

@section('title', 'Notification Preferences')

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
                        <h2 class="profile-title">Notification Preferences</h2>
                        <p class="profile-subtitle">Manage the types of communications you receive</p>
                    </div>
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <form action="{{ route('notification-preferences.update') }}" method="POST" id="notificationForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Updates and Offerings -->
                        <div class="notification-group">
                            <div class="notification-group-header">
                                <div>
                                    <h5 class="mb-1">Updates and offerings</h5>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input toggle-all" type="checkbox" id="toggleUpdates" data-group="updates" checked>
                                </div>
                            </div>
                            <div class="notification-group-body">
                                <div class="notification-item">
                                    <div class="form-check">
                                        <input class="form-check-input notification-checkbox" type="checkbox" name="product_launches" id="product_launches" data-group="updates" checked>
                                        <label class="form-check-label" for="product_launches">
                                            Product launches and announcements
                                        </label>
                                    </div>
                                </div>
                                <div class="notification-item">
                                    <div class="form-check">
                                        <input class="form-check-input notification-checkbox" type="checkbox" name="offers_promotions" id="offers_promotions" data-group="updates" checked>
                                        <label class="form-check-label" for="offers_promotions">
                                            Offers and promotions
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Your Learning -->
                        <div class="notification-group">
                            <div class="notification-group-header">
                                <div>
                                    <h5 class="mb-1">Your learning</h5>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input toggle-all" type="checkbox" id="toggleLearning" data-group="learning" checked>
                                </div>
                            </div>
                            <div class="notification-group-body">
                                <div class="notification-item">
                                    <div class="form-check">
                                        <input class="form-check-input notification-checkbox" type="checkbox" name="course_recommendations" id="course_recommendations" data-group="learning" checked>
                                        <label class="form-check-label" for="course_recommendations">
                                            Course recommendations
                                        </label>
                                    </div>
                                </div>
                                <div class="notification-item">
                                    <div class="form-check">
                                        <input class="form-check-input notification-checkbox" type="checkbox" name="instructor_notifications" id="instructor_notifications" data-group="learning" checked>
                                        <label class="form-check-label" for="instructor_notifications">
                                            Notifications from instructors
                                        </label>
                                    </div>
                                </div>
                                <div class="notification-item">
                                    <div class="form-check">
                                        <input class="form-check-input notification-checkbox" type="checkbox" name="learning_stats" id="learning_stats" data-group="learning" checked>
                                        <label class="form-check-label" for="learning_stats">
                                            Learning stats
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Info Text -->
                        <div class="alert alert-info mt-4" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>Note: It may take a few hours for changes to be reflected in your preferences. You'll still receive transactional emails related to your account and purchases if you unsubscribe.</small>
                        </div>
                        
                        <!-- Save Button -->
                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Save
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