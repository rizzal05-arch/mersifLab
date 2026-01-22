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
                        <!-- Purchase Item 1 - Expired -->
                        <div class="purchase-card">
                            <div class="purchase-header">
                                <span class="purchase-id">ML-123456</span>
                                <span class="badge bg-danger">Expired</span>
                            </div>
                            <h5 class="purchase-course-title">Belajar Desain Grafis untuk Desain Konten Digital</h5>
                            <div class="purchase-details">
                                <p class="mb-1">
                                    <i class="far fa-calendar me-2"></i>
                                    <strong>Dibayarkan:</strong> 5 Jun 2024, 9:10
                                </p>
                                <p class="mb-1">
                                    <i class="fas fa-university me-2"></i>
                                    <strong>03payakan -</strong>
                                </p>
                                <p class="mb-0">
                                    <i class="fas fa-credit-card me-2"></i>
                                    <strong>Metode:</strong> m-banking
                                </p>
                            </div>
                            <div class="purchase-footer">
                                <div class="purchase-price">Rp400,000</div>
                                <a href="{{ route('invoice', 123456) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-file-invoice me-1"></i>Invoice
                                </a>
                            </div>
                        </div>
                        
                        <!-- Purchase Item 2 - Waiting for Payment -->
                        <div class="purchase-card">
                            <div class="purchase-header">
                                <span class="purchase-id">ML-123455</span>
                                <span class="badge bg-warning text-dark">Waiting for Payment</span>
                            </div>
                            <h5 class="purchase-course-title">Pengembangan yang Kompleks: Menghasilkan Foto Menarik dengan Teknik Fotografi Dasar</h5>
                            <div class="purchase-details">
                                <p class="mb-1">
                                    <i class="far fa-calendar me-2"></i>
                                    <strong>Dibuat:</strong> 6 Jul 2023, 9:10
                                </p>
                                <p class="mb-1">
                                    <i class="fas fa-university me-2"></i>
                                    <strong>03payakan -</strong>
                                </p>
                                <p class="mb-0">
                                    <i class="fas fa-credit-card me-2"></i>
                                    <strong>Metode:</strong> transfer bank bri
                                </p>
                            </div>
                            <div class="purchase-footer">
                                <div class="purchase-price">Rp500,000</div>
                                <a href="{{ route('invoice', 123455) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-file-invoice me-1"></i>Invoice
                                </a>
                            </div>
                        </div>
                        
                        <!-- Purchase Item 3 - Success -->
                        <div class="purchase-card">
                            <div class="purchase-header">
                                <span class="purchase-id">ML-123458</span>
                                <span class="badge bg-success">Success</span>
                            </div>
                            <h5 class="purchase-course-title">Pengembangan Robot Pintar untuk Kehidupan Nyata</h5>
                            <div class="purchase-details">
                                <p class="mb-1">
                                    <i class="far fa-calendar me-2"></i>
                                    <strong>Dibayarkan:</strong> 6 Jul 2022, 10:02
                                </p>
                                <p class="mb-1">
                                    <i class="fas fa-university me-2"></i>
                                    <strong>03payakan -</strong>
                                </p>
                                <p class="mb-0">
                                    <i class="fas fa-credit-card me-2"></i>
                                    <strong>Metode:</strong> m-banking
                                </p>
                            </div>
                            <div class="purchase-footer">
                                <div class="purchase-price">Rp400,000</div>
                                <a href="{{ route('invoice', 123458) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-file-invoice me-1"></i>Invoice
                                </a>
                            </div>
                        </div>
                        
                        <!-- Empty State (uncomment if no purchases) -->
                        <div class="empty-state text-center py-5">
                            <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">No Purchase History</h4>
                            <p class="text-muted">You haven't made any purchases yet.</p>
                            <a href="{{ route('courses') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-shopping-cart me-2"></i>Start Shopping
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection