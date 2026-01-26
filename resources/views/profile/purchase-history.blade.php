@extends('layouts.app')

@section('title', 'Purchase History')

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
                    <!-- Profile Avatar -->
                    <div class="profile-avatar-section text-center">
                        <div class="profile-avatar mx-auto">
                            <span class="avatar-letter">{{ strtoupper(substr(Auth::user()->name ?? Auth::user()->email ?? 'S', 0, 1)) }}</span>
                        </div>
                        <h5 class="profile-name mt-3">{{ Auth::user()->name ?? 'Student' }}</h5>
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
                    <div class="profile-header">
                        <h2 class="profile-title">Purchase History</h2>
                        <p class="profile-subtitle">View and manage your past transactions</p>
                    </div>
                    
                    <!-- Purchase List -->
                    <div class="purchase-list">
                        @if(isset($purchases) && $purchases->count() > 0)
                            @foreach($purchases as $purchase)
                            <div class="purchase-card">
                                <div class="purchase-header">
                                    <span class="purchase-id">{{ $purchase->transaction_id ?? 'ML-' . $purchase->id }}</span>
                                    @if($purchase->status == 'completed')
                                        <span class="badge bg-success">Success</span>
                                    @elseif($purchase->status == 'pending')
                                        <span class="badge bg-warning text-dark">Waiting for Payment</span>
                                    @else
                                        <span class="badge bg-danger">{{ ucfirst($purchase->status) }}</span>
                                    @endif
                                </div>
                                <h5 class="purchase-course-title">{{ $purchase->course->name ?? 'Course Name' }}</h5>
                                <div class="purchase-details">
                                    <p class="mb-1">
                                        <i class="far fa-calendar me-2"></i>
                                        <strong>{{ $purchase->status == 'completed' ? 'Paid:' : 'Created:' }}</strong> 
                                        {{ $purchase->created_at->format('d M Y, H:i') }}
                                    </p>
                                    <p class="mb-1">
                                        <i class="fas fa-user me-2"></i>
                                        <strong>Buyer:</strong> {{ $purchase->user->name ?? 'User' }}
                                    </p>
                                    <p class="mb-0">
                                        <i class="fas fa-credit-card me-2"></i>
                                        <strong>Method:</strong> {{ $purchase->payment_method ?? 'Not specified' }}
                                    </p>
                                </div>
                                <div class="purchase-footer">
                                    <div class="purchase-price">Rp{{ number_format($purchase->amount ?? 0, 0, ',', '.') }}</div>
                                    @if(isset($purchase->id))
                                        <a href="{{ route('invoice', $purchase->id) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-file-invoice me-1"></i>Invoice
                                        </a>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="empty-state text-center">
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