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
            <p class="unread-count">{{ $unreadCount ?? 0 }} unread notification{{ ($unreadCount ?? 0) != 1 ? 's' : '' }}</p>
            @if(($unreadCount ?? 0) > 0)
            <form action="{{ route('notifications.mark-all-read') }}" method="POST" style="display: inline;" id="markAllReadForm">
                @csrf
                <button type="submit" class="mark-all-btn" id="markAllRead">
                    <i class="fas fa-check"></i>
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
                    <div class="notification-icon 
                        @if($notification->type === 'new_course') course
                        @elseif($notification->type === 'new_chapter' || $notification->type === 'new_module') update
                        @elseif($notification->type === 'announcement') welcome
                        @else payment
                        @endif">
                        @if($notification->type === 'new_course')
                            <i class="fas fa-book-open"></i>
                        @elseif($notification->type === 'new_chapter')
                            <i class="fas fa-layer-group"></i>
                        @elseif($notification->type === 'new_module')
                            <i class="fas fa-file-alt"></i>
                        @elseif($notification->type === 'announcement')
                            <i class="fas fa-bullhorn"></i>
                        @else
                            <i class="fas fa-bell"></i>
                        @endif
                    </div>
                    <div class="notification-content">
                        <div class="notification-header-row">
                            <h5 class="notification-title">{{ $notification->title ?? 'Notification' }}</h5>
                            <div class="notification-meta">
                                <span class="notification-time">{{ $notification->created_at ? $notification->created_at->diffForHumans() : 'Recently' }}</span>
                                @if(!$notification->is_read)
                                    <span class="unread-badge"></span>
                                @endif
                            </div>
                        </div>
                        <p class="notification-description">
                            {{ $notification->message ?? 'N/A' }}
                        </p>
                        @if(!$notification->is_read)
                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST" style="display: inline; margin-top: 10px;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-check me-1"></i>Mark as read
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $notifications->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="empty-state">
                    <i class="fas fa-bell-slash"></i>
                    <h5>No Notifications</h5>
                    <p>You don't have any notifications at the moment.</p>
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
                e.preventDefault(); // Prevent default form submission
                
                const formData = new FormData(this);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || formData.get('_token');
                
                // Show loading state
                const originalText = markAllBtn.innerHTML;
                markAllBtn.disabled = true;
                markAllBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                
                // Send AJAX request to backend
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
                    // Remove unread class and badges from all notifications
                    const unreadCards = document.querySelectorAll('.notification-card.unread');
                    unreadCards.forEach(card => {
                        card.classList.remove('unread');
                        const badge = card.querySelector('.unread-badge');
                        if (badge) {
                            badge.remove();
                        }
                        
                        // Remove mark as read button
                        const markReadBtn = card.querySelector('form[action*="notifications.read"]');
                        if (markReadBtn) {
                            markReadBtn.remove();
                        }
                    });
                    
                    // Update unread count
                    const unreadCount = document.querySelector('.unread-count');
                    if (unreadCount) {
                        unreadCount.textContent = '0 unread notifications';
                    }
                    
                    // Hide mark all button
                    if (markAllForm) {
                        markAllForm.style.display = 'none';
                    }
                    
                    // Show success message
                    if (data.message) {
                        // Reload page to ensure data is synced
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    markAllBtn.disabled = false;
                    markAllBtn.innerHTML = originalText;
                    alert('Failed to mark all notifications as read. Please try again.');
                });
            });
        }
    });

    // Mark individual notification as read - handled by form submit
    // No need for click handler as form submission handles it
</script>
@endsection