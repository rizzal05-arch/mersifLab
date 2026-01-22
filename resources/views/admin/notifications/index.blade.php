@extends('layouts.admin')

@section('title', 'Notifications')

@section('content')
<div class="page-title">
    <div>
        <h1>Notifications</h1>
        <p style="color: #828282; margin: 5px 0 0 0; font-size: 14px;">Notifikasi sistem dan permintaan approval</p>
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
                    @if($notif->type === 'module_pending_approval')
                        <i class="fas fa-file-alt" style="color: #ff9800;"></i>
                    @elseif($notif->type === 'module_approved')
                        <i class="fas fa-check-circle" style="color: #27AE60;"></i>
                    @elseif($notif->type === 'module_rejected')
                        <i class="fas fa-times-circle" style="color: #e53935;"></i>
                    @else
                        <i class="fas fa-bell" style="color: #2F80ED;"></i>
                    @endif
                </div>
                <div class="notification-content-admin">
                    <div class="notification-header-admin">
                        <h6 class="notification-title-admin">{{ $notif->title }}</h6>
                        <small class="notification-time-admin">{{ $notif->created_at->diffForHumans() }}</small>
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
                <p>Tidak ada notifikasi</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $notifications->links() }}
        </div>
    @endif
</div>

<style>
.notifications-list-admin { }
.notification-item-admin { display: flex; gap: 16px; padding: 16px; border-bottom: 1px solid #f0f0f0; transition: background 0.2s; }
.notification-item-admin:hover { background: #f8f9fa; }
.notification-item-admin.unread { background: #fff9e6; border-left: 3px solid #ff9800; }
.notification-icon-admin { width: 40px; height: 40px; background: #f0f0f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.notification-content-admin { flex: 1; }
.notification-header-admin { display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px; }
.notification-title-admin { font-size: 14px; font-weight: 600; color: #333; margin: 0; }
.notification-time-admin { color: #999; font-size: 12px; }
.notification-message-admin { font-size: 13px; color: #666; margin: 0 0 12px 0; line-height: 1.5; }
.notification-actions-admin { display: flex; gap: 8px; }
.btn-notif-link { background: #e3f2fd; color: #1976d2; border: 1px solid #90caf9; padding: 6px 12px; font-size: 12px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
.btn-notif-link:hover { background: #1976d2; color: white; }
.btn-notif-mark { background: #f0f0f0; color: #666; border: none; padding: 6px 12px; font-size: 12px; border-radius: 6px; cursor: pointer; }
.btn-notif-mark:hover { background: #e0e0e0; }
</style>
@endsection
