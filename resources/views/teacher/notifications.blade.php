@extends('layouts.app')

@section('title', 'Notifications - Teacher')

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
                    <div class="profile-header mb-4">
                        <h2 class="profile-title">Notifications</h2>
                        <p class="profile-subtitle">Stay updated with your latest notifications</p>
                    </div>
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <!-- Notifications List -->
                    <div class="notifications-list">
                        @if($notifications && $notifications->count() > 0)
                            @foreach($notifications as $notification)
                                <div class="notification-card card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="card-title mb-2">
                                                    @if($notification->type === 'student_enrolled')
                                                        <i class="fas fa-user-plus text-success me-2"></i>New Student Enrollment
                                                    @elseif($notification->type === 'course_update')
                                                        <i class="fas fa-bell text-info me-2"></i>Course Update
                                                    @else
                                                        <i class="fas fa-envelope text-primary me-2"></i>{{ $notification->title ?? 'Notification' }}
                                                    @endif
                                                </h6>
                                                <p class="card-text text-muted small">{{ $notification->message ?? $notification->description ?? 'N/A' }}</p>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ $notification->created_at ? $notification->created_at->diffForHumans() : 'Recently' }}
                                                </small>
                                            </div>
                                            <div>
                                                @if(!$notification->read_at)
                                                    <span class="badge bg-warning">New</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-info text-center" role="alert">
                                <i class="fas fa-bell-slash" style="font-size: 2rem; display: block; margin-bottom: 1rem; opacity: 0.5;"></i>
                                <strong>No notifications</strong>
                                <p class="text-muted mt-2">You're all caught up! Check back later for updates.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
