@extends('layouts.app')

@section('title', 'Notifications')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/notifications.css') }}">
@endsection

@section('content')
<div class="notifications-page">
    <div class="container">
        <!-- Page Header -->
        <div class="notifications-header">
            <div class="header-content">
                <div class="header-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="header-text">
                    <h1 class="page-title">Notifications Center</h1>
                    <p class="page-subtitle">Stay updated with your latest activities</p>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="notification-stats">
            <div class="stat-card">
                <div class="stat-icon unread">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $unreadCount ?? 0 }}</h3>
                    <p>Unread</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon total">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ isset($notifications) ? $notifications->total() : 0 }}</h3>
                    <p>Total Notifications</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon today">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-info">
                    @php
                        $todayCount = isset($notifications) ? $notifications->filter(function($n) {
                            return $n->created_at->isToday();
                        })->count() : 0;
                    @endphp
                    <h3>{{ $todayCount }}</h3>
                    <p>Today</p>
                </div>
            </div>
        </div>

        <!-- Controls Section -->
        <div class="notifications-controls">
            <div class="controls-left">
                <h2 class="section-title">All Notifications</h2>
                <p class="unread-count">
                    <span class="count-badge">{{ $unreadCount ?? 0 }}</span>
                    unread notification{{ ($unreadCount ?? 0) != 1 ? 's' : '' }}
                </p>
            </div>
            @if(($unreadCount ?? 0) > 0)
            <form action="{{ route('notifications.mark-all-read') }}" method="POST" style="display: inline;" id="markAllReadForm">
                @csrf
                <button type="submit" class="mark-all-btn" id="markAllRead">
                    <i class="fas fa-check-double"></i>
                    Mark all as read
                </button>
            </form>
            @endif
        </div>

        <!-- Notifications List -->
        <div class="notifications-list">
            @if(isset($notifications) && $notifications->count() > 0)
                @foreach($notifications as $notification)
                <div class="notification-card {{ !$notification->is_read ? 'unread' : '' }}" data-notification-id="{{ $notification->id }}">
                    <div class="notification-left">
                        <div class="notification-icon-wrapper 
                            @if($notification->type === 'new_course') course
                            @elseif($notification->type === 'new_chapter' || $notification->type === 'new_module') update
                            @elseif($notification->type === 'announcement') welcome
                            @elseif($notification->type === 'module_approved') success
                            @elseif($notification->type === 'student_enrolled') info
                            @elseif($notification->type === 'course_rated') star
                            @elseif($notification->type === 'course_completed') trophy
                            @else payment
                            @endif">
                            <div class="notification-icon">
                                @if($notification->type === 'new_course')
                                    <i class="fas fa-book-open"></i>
                                @elseif($notification->type === 'new_chapter')
                                    <i class="fas fa-layer-group"></i>
                                @elseif($notification->type === 'new_module')
                                    <i class="fas fa-file-alt"></i>
                                @elseif($notification->type === 'announcement')
                                    <i class="fas fa-bullhorn"></i>
                                @elseif($notification->type === 'module_approved')
                                    <i class="fas fa-check-circle"></i>
                                @elseif($notification->type === 'student_enrolled')
                                    <i class="fas fa-user-plus"></i>
                                @elseif($notification->type === 'course_rated')
                                    <i class="fas fa-star"></i>
                                @elseif($notification->type === 'course_completed')
                                    <i class="fas fa-trophy"></i>
                                @else
                                    <i class="fas fa-bell"></i>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="notification-content">
                        <div class="notification-main">
                            <div class="notification-header-row">
                                <h5 class="notification-title">{{ $notification->title ?? 'Notification' }}</h5>
                                @if(!$notification->is_read)
                                    <span class="unread-indicator">
                                        <span class="pulse"></span>
                                    </span>
                                @endif
                            </div>
                            <p class="notification-description">
                                {{ $notification->message ?? 'N/A' }}
                            </p>
                            <div class="notification-footer">
                                <span class="notification-time">
                                    <i class="far fa-clock"></i>
                                    {{ $notification->created_at ? $notification->created_at->diffForHumans() : 'Recently' }}
                                </span>
                                @if(!$notification->is_read)
                                <form action="{{ route('notifications.read', $notification->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn-mark-read">
                                        <i class="fas fa-check"></i>
                                        Mark as read
                                    </button>
                                </form>
                                @else
                                <span class="read-status">
                                    <i class="fas fa-check-double"></i>
                                    Read
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Pagination -->
                @if($notifications->hasPages())
                <div class="pagination-wrapper">
                    <div class="pagination-info">
                        Showing {{ $notifications->firstItem() ?? 0 }} to {{ $notifications->lastItem() ?? 0 }} of {{ $notifications->total() }} notifications
                    </div>
                    <div class="pagination-links">
                        {{ $notifications->links('pagination::bootstrap-4') }}
                    </div>
                </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-bell-slash"></i>
                    </div>
                    <h5>No Notifications Yet</h5>
                    <p>You're all caught up! Check back later for new updates.</p>
                    <a href="{{ route('home') }}" class="btn-back-home">
                        <i class="fas fa-home"></i>
                        Back to Home
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Mark all as read functionality
    document.addEventListener('DOMContentLoaded', function() {
        const markAllForm = document.getElementById('markAllReadForm');
        const markAllBtn = document.getElementById('markAllRead');
        
        if (markAllForm && markAllBtn) {
            markAllForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || formData.get('_token');
                
                // Show loading state
                const originalText = markAllBtn.innerHTML;
                markAllBtn.disabled = true;
                markAllBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                
                // Send AJAX request
                fetch('{{ route("notifications.mark-all-read") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.message || 'Network response was not ok');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    // Show success message
                    showNotification('All notifications marked as read!', 'success');
                    
                    // Reload after short delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 800);
                })
                .catch(error => {
                    console.error('Error:', error);
                    markAllBtn.disabled = false;
                    markAllBtn.innerHTML = originalText;
                    showNotification('Failed to mark all as read. Please try again.', 'error');
                });
            });
        }

        // Individual mark as read
        const markReadForms = document.querySelectorAll('form[action*="notifications.read"]');
        markReadForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const btn = this.querySelector('.btn-mark-read');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Marking...';
                }
            });
        });
    });

    // Show notification toast
    function showNotification(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `notification-toast ${type}`;
        toast.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            <span>${message}</span>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => toast.classList.add('show'), 100);
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
</script>
@endsection