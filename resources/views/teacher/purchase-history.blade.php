@extends('layouts.app')

@section('title', 'Financial Management - Teacher')

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
                        <h2 class="profile-title">Financial Management</h2>
                        <p class="profile-subtitle">Manage your income and financial transactions</p>
                    </div>
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <!-- Financial Summary Cards -->
                    @php
                        $totalRevenue = $purchases ? $purchases->sum('amount') : 0;
                        $totalTransactions = $purchases ? $purchases->count() : 0;
                        $successTransactions = $purchases ? $purchases->where('status', 'success')->count() : 0;
                        $pendingTransactions = $purchases ? $purchases->where('status', 'pending')->count() : 0;
                        $uniqueStudents = $purchases ? $purchases->where('status', 'success')->pluck('user_id')->unique()->count() : 0;
                        $totalCourses = $courses ? $courses->count() : 0;
                    @endphp
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="financial-card financial-card-primary">
                                <div class="financial-card-icon">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <div class="financial-card-content">
                                    <span class="financial-card-label">Total Revenue</span>
                                    <h3 class="financial-card-value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="financial-card financial-card-secondary">
                                <div class="financial-card-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="financial-card-content">
                                    <span class="financial-card-label">Total Student Buyers</span>
                                    <h3 class="financial-card-value">{{ $uniqueStudents }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="financial-card financial-card-success">
                                <div class="financial-card-icon">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="financial-card-content">
                                    <span class="financial-card-label">Total Course</span>
                                    <h3 class="financial-card-value">{{ $totalCourses }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="financial-card financial-card-warning">
                                <div class="financial-card-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="financial-card-content">
                                    <span class="financial-card-label">Successful Transactions</span>
                                    <h3 class="financial-card-value">{{ $successTransactions }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Student List Section -->
                    <div class="mt-5 mb-4">
                        <h4 class="section-title mb-4">
                            <i class="fas fa-users me-2"></i> Student Buyer List
                        </h4>

                        @if($purchases && $purchases->count() > 0)
                            <div class="students-grid">
                                @foreach($purchases->where('status', 'success') as $purchase)
                                    <div class="student-card">
                                        <div class="student-card-header">
                                            <div class="student-avatar">
                                                @if(isset($purchase->user->avatar) && $purchase->user->avatar)
                                                    <img src="{{ asset('storage/' . $purchase->user->avatar) }}" alt="{{ $purchase->user->name }}">
                                                @else
                                                    <span class="avatar-initial">{{ strtoupper(substr($purchase->user->name ?? 'S', 0, 2)) }}</span>
                                                @endif
                                            </div>
                                            <div class="student-info">
                                                <h6 class="student-name">{{ $purchase->user->name ?? 'Student' }}</h6>
                                                <small class="student-email">{{ $purchase->user->email ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                        <div class="student-card-body">
                                            <div class="student-detail">
                                                <span class="detail-label">Course:</span>
                                                <span class="detail-value">{{ $purchase->course->name ?? 'Course' }}</span>
                                            </div>
                                            <div class="student-detail">
                                                <span class="detail-label">Tanggal Pembelian:</span>
                                                <span class="detail-value">{{ $purchase->created_at ? $purchase->created_at->format('d M Y') : 'N/A' }}</span>
                                            </div>
                                            <div class="student-detail">
                                                <span class="detail-label">Amount:</span>
                                                <span class="detail-value price">Rp {{ number_format($purchase->amount ?? 0, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="student-detail">
                                                <span class="detail-label">Status:</span>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Verified
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state-box">
                                <i class="fas fa-inbox"></i>
                                <p>No students have bought your course yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .financial-card {
        border-radius: 12px;
        padding: 24px;
        color: white;
        display: flex;
        align-items: center;
        gap: 20px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border: none;
    }

    .financial-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .financial-card-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .financial-card-secondary {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .financial-card-success {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .financial-card-warning {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }

    .financial-card-icon {
        font-size: 2.5rem;
        opacity: 0.9;
        min-width: 60px;
        text-align: center;
    }

    .financial-card-content {
        flex: 1;
    }

    .financial-card-label {
        display: block;
        font-size: 13px;
        opacity: 0.9;
        font-weight: 500;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .financial-card-value {
        font-size: 28px;
        font-weight: bold;
        margin: 0;
        word-break: break-word;
    }

    @media (max-width: 768px) {
        .financial-card {
            padding: 16px;
            gap: 12px;
        }

        .financial-card-icon {
            font-size: 2rem;
            min-width: 50px;
        }

        .financial-card-value {
            font-size: 22px;
        }
    }

    .section-title {
        font-size: 20px;
        font-weight: 600;
        color: #333;
        border-bottom: 3px solid #667eea;
        padding-bottom: 12px;
        display: inline-block;
    }

    .students-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 24px;
    }

    .student-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: all 0.3s ease;
        border: 1px solid #f0f0f0;
    }

    .student-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
        border-color: #667eea;
    }

    .student-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        border-bottom: 1px solid #e0e0e0;
    }

    .student-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        overflow: hidden;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        flex-shrink: 0;
    }

    .student-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-initial {
        font-size: 20px;
    }

    .student-info {
        flex: 1;
        min-width: 0;
    }

    .student-name {
        margin: 0;
        font-size: 15px;
        font-weight: 600;
        color: #333;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .student-email {
        display: block;
        color: #999;
        font-size: 12px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .student-card-body {
        padding: 16px;
    }

    .student-detail {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        font-size: 13px;
    }

    .student-detail:last-child {
        margin-bottom: 0;
    }

    .detail-label {
        color: #666;
        font-weight: 500;
    }

    .detail-value {
        color: #333;
        font-weight: 600;
        text-align: right;
        flex: 1;
        margin-left: 8px;
    }

    .detail-value.price {
        color: #28a745;
        font-size: 14px;
    }

    .empty-state-box {
        text-align: center;
        padding: 48px 24px;
        background: #f8f9fa;
        border-radius: 12px;
        border: 2px dashed #ddd;
    }

    .empty-state-box i {
        font-size: 48px;
        color: #ccc;
        margin-bottom: 16px;
        display: block;
    }

    .empty-state-box p {
        color: #999;
        font-size: 16px;
        margin: 0;
    }

    @media (max-width: 768px) {
        .students-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .student-detail {
            flex-direction: column;
            align-items: flex-start;
        }

        .detail-value {
            text-align: left;
            margin-left: 0;
            margin-top: 4px;
        }
    }
</style>
@endsection
