@extends('layouts.admin')

@section('title', 'Notifications')

@section('content')
<div class="page-title" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
    <div>
        <h1>Notifications</h1>
        <p style="color: #828282; margin: 5px 0 0 0; font-size: 14px;">System notifications and approval requests</p>
    </div>
    <div style="max-width: 350px; width: 100%; margin-top: 0;">
        <input type="text" id="notificationSearch" placeholder="Search notifications..." style="width: 100%; padding: 10px 15px; border: none; background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); border-radius: 20px; font-size: 13px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); transition: all 0.3s ease; outline: none;" onfocus="this.style.background='white'; this.style.boxShadow='0 2px 8px rgba(0, 0, 0, 0.1)';" onblur="this.style.background='rgba(255, 255, 255, 0.8)'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.05)';">
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 8px;">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert" style="background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; border-radius: 8px;">
        {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card-content">
    <div class="card-content-title">
        <span>All Notifications</span>
        @if($notifications->where('is_read', false)->count() > 0)
            <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm" style="background: #e3f2fd; color: #1976d2; border: none; padding: 6px 12px; font-size: 12px; border-radius: 6px;">
                    <i class="fas fa-check-double"></i> Mark All as Read
                </button>
            </form>
        @endif
    </div>

    <div class="notifications-list-admin">
        @forelse($notifications as $notif)
            <div class="notification-item-admin {{ !$notif->is_read ? 'unread' : '' }}">
                <div class="notification-icon-admin">
                    @if($notif->type === 'teacher_application')
                        <i class="fas fa-user-plus" style="color: #17a2b8;"></i>
                    @elseif($notif->type === 'course_approval_request' || $notif->type === 'course_reapproval_request')
                        <i class="fas fa-graduation-cap" style="color: #8b5cf6;"></i>
                    @elseif($notif->type === 'module_pending_approval')
                        <i class="fas fa-file-alt" style="color: #ff9800;"></i>
                    @elseif($notif->type === 'module_approved')
                        <i class="fas fa-check-circle" style="color: #27AE60;"></i>
                    @elseif($notif->type === 'module_rejected')
                        <i class="fas fa-times-circle" style="color: #e53935;"></i>
                    @elseif($notif->type === 'new_purchase')
                        <i class="fas fa-shopping-cart" style="color: #4CAF50;"></i>
                    @else
                        <i class="fas fa-bell" style="color: #2F80ED;"></i>
                    @endif
                </div>
                <div class="notification-content-admin">
                    <div class="notification-header-admin">
                        <h6 class="notification-title-admin">{{ $notif->title }}</h6>
                        <small class="notification-time-admin">{{ $notif->created_at->format('d M Y, H:i') }}</small>
                    </div>
                    <p class="notification-message-admin">{{ $notif->message }}</p>
                    <div class="notification-actions-admin">
                        <a href="{{ route('admin.notifications.show', $notif->id) }}" class="btn-notif-link">
                            <i class="fas fa-eye"></i> View
                        </a>
                        @if(!$notif->is_read)
                            <form action="{{ route('admin.notifications.mark-read', $notif->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn-notif-mark">Mark as Read</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center" style="padding: 40px; color: #828282;">
                <i class="fas fa-bell-slash" style="font-size: 48px; color: #e0e0e0; margin-bottom: 12px;"></i>
                <p>No notifications</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $notifications->links('pagination::admin') }}
        </div>
    @endif
</div>

<script>
// Search functionality for notifications
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('notificationSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase().trim();
            const notificationItems = document.querySelectorAll('.notification-item-admin');
            
            notificationItems.forEach(item => {
                const title = item.querySelector('.notification-title-admin')?.textContent.toLowerCase() || '';
                const message = item.querySelector('.notification-message-admin')?.textContent.toLowerCase() || '';
                const time = item.querySelector('.notification-time-admin')?.textContent.toLowerCase() || '';
                
                // Get notification type from icon
                const icon = item.querySelector('.notification-icon-admin i');
                let type = '';
                if (icon) {
                    if (icon.classList.contains('fa-file-alt')) type = 'pending approval';
                    else if (icon.classList.contains('fa-check-circle')) type = 'approved';
                    else if (icon.classList.contains('fa-times-circle')) type = 'rejected';
                    else type = 'notification';
                }
                
                const text = title + ' ' + message + ' ' + time + ' ' + type;
                
                if (searchTerm === '' || text.includes(searchTerm)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Check if all items are hidden
            const visibleItems = Array.from(notificationItems).filter(item => item.style.display !== 'none');
            const originalEmptyState = document.querySelector('.notifications-list-admin .text-center');
            const listContainer = document.querySelector('.notifications-list-admin');
            
            if (visibleItems.length === 0 && searchTerm !== '') {
                // Hide original empty state if exists
                if (originalEmptyState && originalEmptyState.querySelector('p')?.textContent.includes('No notifications')) {
                    originalEmptyState.style.display = 'none';
                }
                
                // Show no results message
                let noResults = listContainer.querySelector('.text-center.search-no-results');
                if (!noResults) {
                    noResults = document.createElement('div');
                    noResults.className = 'text-center search-no-results';
                    noResults.style.cssText = 'padding: 40px; color: #828282;';
                    listContainer.appendChild(noResults);
                }
                noResults.innerHTML = `
                    <i class="fas fa-search" style="font-size: 48px; color: #e0e0e0; margin-bottom: 12px;"></i>
                    <p>No notifications found for "${searchTerm}"</p>
                `;
                noResults.style.display = 'block';
            } else {
                // Remove no results message if exists
                const noResults = listContainer.querySelector('.search-no-results');
                if (noResults) {
                    noResults.remove();
                }
                
                // Show original empty state if no search term and no items
                if (searchTerm === '' && originalEmptyState && notificationItems.length === 0) {
                    originalEmptyState.style.display = 'block';
                }
            }
        });
    }
});
</script>

<style>
.notifications-list-admin { }
.notification-item-admin { display: flex; gap: 16px; padding: 16px; border-bottom: 1px solid #f0f0f0; transition: background 0.2s; }
.notification-item-admin:hover { background: #f8f9fa; }
.notification-item-admin.unread { background: #fff9e6; border-left: 3px solid #ff9800; }
.notification-icon-admin { width: 40px; height: 40px; background: #f0f0f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.notification-content-admin { flex: 1; }
.notification-header-admin { display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px; }
.notification-title-admin { font-size: 13px; font-weight: 600; color: #333; margin: 0; }
.notification-time-admin { color: #828282; font-size: 12px; }
.notification-message-admin { font-size: 13px; color: #666; margin: 0 0 12px 0; line-height: 1.5; }
.notification-actions-admin { display: flex; gap: 8px; }
.btn-notif-link { background: #e3f2fd; color: #1976d2; border: 1px solid #90caf9; padding: 6px 12px; font-size: 12px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
.btn-notif-link:hover { background: #1976d2; color: white; }
.btn-notif-mark { background: #f0f0f0; color: #666; border: none; padding: 6px 12px; font-size: 12px; border-radius: 6px; cursor: pointer; }
.btn-notif-mark:hover { background: #e0e0e0; }

/* Admin Pagination Styles */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.pagination li {
    display: inline-block;
}

.pagination .page-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    padding: 0 12px;
    margin: 0 2px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    background: white;
    color: #6c757d;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.pagination .page-link:hover {
    background: #2F80ED;
    border-color: #2F80ED;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(47, 128, 237, 0.2);
}

.pagination .page-item.active .page-link {
    background: #2F80ED;
    border-color: #2F80ED;
    color: white;
    font-weight: 600;
    box-shadow: 0 4px 8px rgba(47, 128, 237, 0.3);
}

.pagination .page-item.disabled .page-link {
    background: #f8f9fa;
    border-color: #e9ecef;
    color: #adb5bd;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.pagination .page-item.disabled .page-link:hover {
    background: #f8f9fa;
    border-color: #e9ecef;
    color: #adb5bd;
    transform: none;
    box-shadow: none;
}

/* Pagination icons */
.pagination .page-link i {
    font-size: 12px;
}

/* Responsive pagination */
@media (max-width: 768px) {
    .pagination {
        flex-wrap: wrap;
        gap: 4px;
    }
    
    .pagination .page-link {
        min-width: 36px;
        height: 36px;
        font-size: 13px;
        padding: 0 10px;
    }
}

@media (max-width: 768px) {
    .page-title { flex-direction: column !important; gap: 15px; }
    .page-title > div:last-child { max-width: 100% !important; width: 100% !important; }
}
</style>
@endsection
