@extends('layouts.admin')

@section('title', 'Purchase Details')

@section('content')
<div class="page-title" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
    <div>
        <h1>Purchase Details</h1>
        <p style="color: #828282; margin: 5px 0 0 0; font-size: 14px;">View and manage student purchase</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 8px;">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 8px;">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card-content">
    <div class="card-content-title">
        <span>Purchase Information</span>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="purchase-details-section">
                <!-- Student Info -->
                <div class="detail-group">
                    <h6><i class="fas fa-user"></i> Student Information</h6>
                    <div class="detail-item">
                        <label>Name:</label>
                        <span>{{ $purchase->user->name }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Email:</label>
                        <span>{{ $purchase->user->email }}</span>
                    </div>
                </div>

                <!-- Course Info -->
                <div class="detail-group">
                    <h6><i class="fas fa-book"></i> Course Information</h6>
                    <div class="detail-item">
                        <label>Course:</label>
                        <span>{{ $purchase->course->name }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Instructor:</label>
                        <span>{{ $purchase->course->teacher->name ?? 'N/A' }}</span>
                    </div>
                </div>

                <!-- Purchase Info -->
                <div class="detail-group">
                    <h6><i class="fas fa-receipt"></i> Purchase Details</h6>
                    <div class="detail-item">
                        <label>Purchase Code:</label>
                        <span><strong>{{ $purchase->purchase_code }}</strong></span>
                    </div>
                    <div class="detail-item">
                        <label>Amount:</label>
                        <span><strong>Rp{{ number_format($purchase->amount, 0, ',', '.') }}</strong></span>
                    </div>
                    <div class="detail-item">
                        <label>Status:</label>
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
                    <div class="detail-item">
                        <label>Payment Method:</label>
                        <span>{{ $purchase->payment_method ?? 'Not specified' }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Payment Provider:</label>
                        <span>{{ $purchase->payment_provider ?? 'Not specified' }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Created:</label>
                        <span>{{ $purchase->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    @if($purchase->paid_at)
                        <div class="detail-item">
                            <label>Paid At:</label>
                            <span>{{ $purchase->paid_at->format('d M Y, H:i') }}</span>
                        </div>
                    @endif
                    @if($purchase->notes)
                        <div class="detail-item">
                            <label>Notes:</label>
                            <span>{{ $purchase->notes }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="purchase-actions">
                <h6><i class="fas fa-cog"></i> Actions</h6>
                
                @if($purchase->status === 'pending')
                    <div class="action-card">
                        <div class="action-header">
                            <i class="fas fa-lock" style="color: #ff9800;"></i>
                            <span>Course Locked</span>
                        </div>
                        <p>Student is waiting for payment confirmation via WhatsApp.</p>
                        
                        <form action="{{ route('admin.notifications.unlock-course', $purchase->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to unlock this course? This will notify the student that they can start learning.')">
                            @csrf
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-unlock"></i> Unlock Course
                            </button>
                        </form>
                    </div>
                @endif

                @if($purchase->status === 'success')
                    <div class="action-card">
                        <div class="action-header">
                            <i class="fas fa-check-circle" style="color: #27AE60;"></i>
                            <span>Course Unlocked</span>
                        </div>
                        <p>Student can access this course and start learning.</p>
                    </div>
                @endif

                <div class="action-card">
                    <h6>Quick Actions</h6>
                    <div class="btn-group-vertical">
                        <a href="{{ route('admin.students.show', $purchase->user_id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-user"></i> View Student Profile
                        </a>
                        <a href="{{ route('admin.courses.show', $purchase->course_id) }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-book"></i> View Course Details
                        </a>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $purchase->user->phone ?? '') }}?text=Hi%20{{ urlencode($purchase->user->name) }}%2C%20regarding%20your%20course%20purchase%20{{ urlencode($purchase->purchase_code) }}" 
                           target="_blank" class="btn btn-outline-success btn-sm">
                            <i class="fab fa-whatsapp"></i> Contact via WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.purchase-details-section {
    background: white;
    border-radius: 8px;
    padding: 24px;
}

.detail-group {
    margin-bottom: 32px;
}

.detail-group h6 {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 16px;
    padding-bottom: 8px;
    border-bottom: 2px solid #f0f0f0;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #f8f9fa;
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-item label {
    font-weight: 500;
    color: #6c757d;
    margin: 0;
}

.detail-item span {
    color: #2c3e50;
    font-weight: 500;
}

.purchase-actions {
    background: white;
    border-radius: 8px;
    padding: 24px;
    position: sticky;
    top: 20px;
}

.action-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 16px;
}

.action-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
    font-weight: 600;
    color: #2c3e50;
}

.action-card p {
    color: #6c757d;
    font-size: 14px;
    margin-bottom: 16px;
}

.btn-group-vertical {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.btn-block {
    width: 100%;
}
</style>
@endsection
