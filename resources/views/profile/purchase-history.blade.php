@extends('layouts.app')

@section('title', 'Purchase History')

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
                            <a href="{{ route('teacher.statistics') }}" class="profile-nav-item">
                                <i class="fas fa-chart-bar me-2"></i> Statistics
                            </a>
                            <a href="{{ route('teacher.purchase.history') }}" class="profile-nav-item active">
                                <i class="fas fa-history me-2"></i> Purchase History
                            </a>
                            <a href="{{ route('teacher.notifications') }}" class="profile-nav-item">
                                <i class="fas fa-bell me-2"></i> Notifications
                            </a>
                        @else
                            <a href="{{ route('profile') }}" class="profile-nav-item">
                                <i class="fas fa-user me-2"></i> My Profile
                            </a>
                            <a href="{{ route('my-courses') }}" class="profile-nav-item">
                                <i class="fas fa-book me-2"></i> My Courses
                            </a>
                            <a href="{{ route('purchase-history') }}" class="profile-nav-item active">
                                <i class="fas fa-history me-2"></i> Purchase History
                            </a>
                            <a href="{{ route('notification-preferences') }}" class="profile-nav-item">
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
                        <h2 class="profile-title">Purchase History</h2>
                        <p class="profile-subtitle">View and manage your past transactions</p>
                    </div>
                    
                    <!-- Purchase List -->
                    <div class="purchase-list">
                        @if($purchases->count() > 0)
                            @foreach($purchases as $purchase)
                                <div class="purchase-card">
                                    <div class="purchase-header">
                                        <span class="purchase-id">{{ $purchase->purchase_code }}</span>
                                        <span class="badge bg-{{ $purchase->status_badge }}">
                                            @if($purchase->status === 'success')
                                                Success
                                            @elseif($purchase->status === 'pending')
                                                Waiting for Payment
                                            @elseif($purchase->status === 'expired')
                                                Expired
                                            @else
                                                Cancelled
                                            @endif
                                        </span>
                                    </div>
                                    <h5 class="purchase-course-title">{{ $purchase->course->name ?? 'Course tidak ditemukan' }}</h5>
                                    <div class="purchase-details">
                                        @if($purchase->paid_at)
                                            <p class="mb-1">
                                                <i class="far fa-calendar me-2"></i>
                                                <strong>Dibayarkan:</strong> {{ $purchase->paid_at->format('d M Y, H:i') }}
                                            </p>
                                        @else
                                            <p class="mb-1">
                                                <i class="far fa-calendar me-2"></i>
                                                <strong>Dibuat:</strong> {{ $purchase->created_at->format('d M Y, H:i') }}
                                            </p>
                                        @endif
                                        @if($purchase->payment_provider)
                                            <p class="mb-1">
                                                <i class="fas fa-university me-2"></i>
                                                <strong>{{ $purchase->payment_provider }} -</strong>
                                            </p>
                                        @endif
                                        @if($purchase->payment_method)
                                            <p class="mb-0">
                                                <i class="fas fa-credit-card me-2"></i>
                                                <strong>Metode:</strong> {{ $purchase->payment_method }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="purchase-footer">
                                        <div class="purchase-price">Rp{{ number_format($purchase->amount, 0, ',', '.') }}</div>
                                        <a href="{{ route('invoice', $purchase->id) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-file-invoice me-1"></i>Invoice
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <!-- Empty State -->
                            <div class="empty-state text-center py-5">
                                <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">No Purchase History</h4>
                                <p class="text-muted">You haven't made any purchases yet.</p>
                                <a href="{{ route('courses') }}" class="btn btn-primary mt-3">
                                    <i class="fas fa-shopping-cart me-2"></i>Start Shopping
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection