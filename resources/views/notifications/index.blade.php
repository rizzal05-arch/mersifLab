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
            <h1 class="page-title">Notifications</h1>
        </div>

        <!-- Unread Count & Mark All -->
        <div class="notifications-controls">
            <p class="unread-count">3 unread notifications</p>
            <button class="mark-all-btn" id="markAllRead">
                <i class="fas fa-check"></i>
                Mark all as read
            </button>
        </div>

        <!-- Notifications List -->
        <div class="notifications-list">
            <!-- Notification Item 1 - Unread -->
            <div class="notification-card unread">
                <div class="notification-icon payment">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-header-row">
                        <h5 class="notification-title">Payment Successful</h5>
                        <div class="notification-meta">
                            <span class="notification-time">1 min ago</span>
                            <span class="unread-badge"></span>
                        </div>
                    </div>
                    <p class="notification-description">
                        Your payment for the course Full Stack Web Development was successful. You can now access the course and start learning anytime.
                    </p>
                </div>
            </div>

            <!-- Notification Item 2 - Unread -->
            <div class="notification-card unread">
                <div class="notification-icon course">
                    <i class="fas fa-book-open"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-header-row">
                        <h5 class="notification-title">New Course Available</h5>
                        <div class="notification-meta">
                            <span class="notification-time">1 hour ago</span>
                            <span class="unread-badge"></span>
                        </div>
                    </div>
                    <p class="notification-description">
                        A new course Introduction to Photography is now available. Explore the course and enhance your creative knowledge.
                    </p>
                </div>
            </div>

            <!-- Notification Item 3 - Unread -->
            <div class="notification-card unread">
                <div class="notification-icon failed">
                    <i class="fas fa-exclamation"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-header-row">
                        <h5 class="notification-title">Payment Failed</h5>
                        <div class="notification-meta">
                            <span class="notification-time">2 days ago</span>
                            <span class="unread-badge"></span>
                        </div>
                    </div>
                    <p class="notification-description">
                        Your payment for the course JavaScript Basics could not be processed. Please try again using a different payment method.
                    </p>
                </div>
            </div>

            <!-- Notification Item 4 - Read -->
            <div class="notification-card">
                <div class="notification-icon update">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-header-row">
                        <h5 class="notification-title">Course Content Updated</h5>
                        <div class="notification-meta">
                            <span class="notification-time">Sun, 18 Jan 2026 18:00</span>
                        </div>
                    </div>
                    <p class="notification-description">
                        New materials have been added to the course Digital Marketing Fundamentals. Check the latest updates and continue learning.
                    </p>
                </div>
            </div>

            <!-- Notification Item 5 - Read -->
            <div class="notification-card">
                <div class="notification-icon welcome">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-header-row">
                        <h5 class="notification-title">Welcome to MersifLab</h5>
                        <div class="notification-meta">
                            <span class="notification-time">Sat, 17 Jan 2026 18:00</span>
                        </div>
                    </div>
                    <p class="notification-description">
                        Your account has been successfully created. Start exploring courses and begin your learning journey today.
                    </p>
                </div>
            </div>

            <!-- Empty State (Hidden by default, shown when no notifications) -->
            <div class="empty-state" style="display: none;">
                <i class="fas fa-bell-slash"></i>
                <h5>No Notifications</h5>
                <p>You don't have any notifications at the moment.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Mark all as read functionality
    document.getElementById('markAllRead')?.addEventListener('click', function() {
        const unreadCards = document.querySelectorAll('.notification-card.unread');
        const unreadCount = document.querySelector('.unread-count');
        const markAllBtn = document.querySelector('.mark-all-btn');
        
        // Remove unread class and badges
        unreadCards.forEach(card => {
            card.classList.remove('unread');
            const badge = card.querySelector('.unread-badge');
            if (badge) {
                badge.remove();
            }
        });
        
        // Update unread count
        if (unreadCount) {
            unreadCount.textContent = '0 unread notifications';
        }
        
        // Disable button after marking all
        if (markAllBtn) {
            markAllBtn.disabled = true;
            markAllBtn.style.opacity = '0.5';
            markAllBtn.style.cursor = 'not-allowed';
        }
        
        // Optional: Send AJAX request to backend to mark as read
        /*
        fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('All notifications marked as read');
        });
        */
    });

    // Mark individual notification as read on click
    document.querySelectorAll('.notification-card').forEach(card => {
        card.addEventListener('click', function() {
            if (this.classList.contains('unread')) {
                this.classList.remove('unread');
                const badge = this.querySelector('.unread-badge');
                if (badge) {
                    badge.remove();
                }
                
                // Update unread count
                const unreadCards = document.querySelectorAll('.notification-card.unread');
                const unreadCount = document.querySelector('.unread-count');
                if (unreadCount) {
                    unreadCount.textContent = `${unreadCards.length} unread notification${unreadCards.length !== 1 ? 's' : ''}`;
                }
                
                // Optional: Send AJAX request to backend
                // const notificationId = this.dataset.notificationId;
                // markNotificationAsRead(notificationId);
            }
        });
    });
</script>
@endsection