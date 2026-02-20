@extends('layouts.app')

@section('title', 'Purchase History')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/profile.css') }}">
<style>
.countdown-timer {
    display: inline-flex;
    align-items: center;
    background: rgba(255, 193, 7, 0.1);
    border: 1px solid rgba(255, 193, 7, 0.3);
    border-radius: 12px;
    padding: 4px 8px;
    font-size: 11px;
    transition: all 0.3s ease;
}

.countdown-timer:hover {
    background: rgba(255, 193, 7, 0.2);
    transform: translateY(-1px);
}

.countdown-timer .fa-clock {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.6; }
    100% { opacity: 1; }
}

.countdown-timer.urgent {
    background: rgba(220, 53, 69, 0.1);
    border-color: rgba(220, 53, 69, 0.3);
}

.countdown-timer.urgent .fa-clock {
    color: #dc3545;
    animation: pulse 1s infinite;
}

.course-list {
    background: rgba(0, 123, 255, 0.05);
    border: 1px solid rgba(0, 123, 255, 0.2);
    border-radius: 8px;
    padding: 12px;
    margin-top: 8px;
}

.course-item {
    padding: 4px 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}

.course-item:last-child {
    border-bottom: none;
    margin-bottom: 0 !important;
}

.course-item .badge {
    font-size: 0.75rem;
    min-width: 20px;
    text-align: center;
}
</style>
@endsection

@section('content')
<section class="profile-section py-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                @include('profile.partials.sidebar')
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
                        @if(isset($allTransactions) && $allTransactions->count() > 0)
                            @foreach($allTransactions as $transaction)
                                <div class="purchase-card">
                                    <div class="purchase-header">
                                        <span class="purchase-id">{{ $transaction['purchase_code'] }}</span>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <span class="badge bg-{{ $transaction['status_badge'] }}">
                                                @if($transaction['status'] === 'success')
                                                    Success
                                                @elseif($transaction['status'] === 'pending')
                                                    Waiting for Payment
                                                @elseif($transaction['status'] === 'expired')
                                                    Expired
                                                @else
                                                    Cancelled
                                                @endif
                                            </span>
                                            
                                            <!-- Countdown Timer for Pending Invoices -->
                                            @if($transaction['status'] === 'pending' && isset($transaction['due_date']))
                                                <div class="countdown-timer" data-due-date="{{ $transaction['due_date']->format('Y-m-d H:i:s') }}">
                                                    <small class="text-muted" style="font-size: 11px;">
                                                        <i class="fas fa-clock me-1"></i>
                                                        <span class="countdown-text">Calculating...</span>
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @if($transaction['type'] === 'subscription')
                                        <h5 class="purchase-course-title">
                                            <i class="fas fa-crown me-2"></i>
                                            Paket Berlangganan {{ ucfirst($transaction['plan']) }}
                                        </h5>
                                    @else
                                        @if(isset($transaction['courses']) && count($transaction['courses']) > 1)
                                            <!-- Multiple courses in one invoice -->
                                            <h5 class="purchase-course-title">
                                                <i class="fas fa-graduation-cap me-2"></i>
                                                Multiple Courses ({{ count($transaction['courses']) }} items)
                                            </h5>
                                            <div class="course-list mt-2">
                                                @foreach($transaction['courses'] as $index => $course)
                                                    <div class="course-item d-flex align-items-center mb-2">
                                                        <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                                        <span>{{ $course->name ?? 'Course tidak ditemukan' }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <!-- Single course -->
                                            <h5 class="purchase-course-title">{{ $transaction['course']->name ?? 'Course tidak ditemukan' }}</h5>
                                        @endif
                                    @endif
                                    
                                    <div class="purchase-details">
                                        @if($transaction['paid_at'])
                                            <p class="mb-1">
                                                <i class="far fa-calendar me-2"></i>
                                                <strong>Dibayarkan:</strong> 
                                                @if($transaction['paid_at'] instanceof \Carbon\Carbon)
                                                    {{ $transaction['paid_at']->format('d M Y, H:i') }}
                                                @else
                                                    {{ \Carbon\Carbon::parse($transaction['paid_at'])->format('d M Y, H:i') }}
                                                @endif
                                            </p>
                                        @else
                                            <p class="mb-1">
                                                <i class="far fa-calendar me-2"></i>
                                                <strong>Dibuat:</strong> 
                                                @if($transaction['created_at'] instanceof \Carbon\Carbon)
                                                    {{ $transaction['created_at']->format('d M Y, H:i') }}
                                                @else
                                                    {{ \Carbon\Carbon::parse($transaction['created_at'])->format('d M Y, H:i') }}
                                                @endif
                                            </p>
                                        @endif
                                        
                                        @if($transaction['type'] === 'subscription' && isset($transaction['expires_at']) && $transaction['expires_at'])
                                            <p class="mb-1">
                                                <i class="fas fa-clock me-2"></i>
                                                <strong>Berlaku hingga:</strong> 
                                                @if($transaction['expires_at'] instanceof \Carbon\Carbon)
                                                    {{ $transaction['expires_at']->format('d M Y, H:i') }}
                                                @else
                                                    {{ \Carbon\Carbon::parse($transaction['expires_at'])->format('d M Y, H:i') }}
                                                @endif
                                            </p>
                                        @endif
                                        
                                        @if($transaction['payment_provider'])
                                            <p class="mb-1">
                                                <i class="fas fa-university me-2"></i>
                                                <strong>{{ $transaction['payment_provider'] }} -</strong>
                                            </p>
                                        @endif
                                        
                                        @if($transaction['payment_method'])
                                            <p class="mb-0">
                                                <i class="fas fa-credit-card me-2"></i>
                                                <strong>Metode:</strong> {{ $transaction['payment_method'] }}
                                            </p>
                                        @endif
                                    </div>
                                    
                                    <div class="purchase-footer">
                                        <div class="purchase-price">Rp{{ number_format($transaction['amount'], 0, ',', '.') }}</div>
                                        <div class="purchase-actions">
                                            @if($transaction['type'] === 'course')
                                                <a href="{{ route('invoice', ['id' => $transaction['id'], 'type' => 'course']) }}" class="btn btn-sm btn-outline-primary me-2">
                                                    <i class="fas fa-eye me-1"></i> View Invoice
                                                </a>
                                                <a href="{{ route('invoice.download', ['id' => $transaction['id'], 'type' => 'course']) }}" class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-download me-1"></i> Download
                                                </a>
                                            @elseif($transaction['type'] === 'subscription')
                                                <a href="{{ route('invoice', ['id' => $transaction['id'], 'type' => 'subscription']) }}" class="btn btn-sm btn-outline-primary me-2">
                                                    <i class="fas fa-eye me-1"></i> View Invoice
                                                </a>
                                                <a href="{{ route('invoice.download', ['id' => $transaction['id'], 'type' => 'subscription']) }}" class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-download me-1"></i> Download
                                                </a>
                                            @endif
                                        </div>
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
                                    Start Shopping
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Countdown Timer for Pending Invoices
document.addEventListener('DOMContentLoaded', function() {
    const countdownTimers = document.querySelectorAll('.countdown-timer');
    
    countdownTimers.forEach(timer => {
        const dueDate = new Date(timer.getAttribute('data-due-date'));
        const countdownText = timer.querySelector('.countdown-text');
        
        function updateCountdown() {
            const now = new Date();
            const difference = dueDate - now;
            
            if (difference <= 0) {
                countdownText.innerHTML = '<span style="color: #dc3545; font-weight: bold;">Expired</span>';
                timer.classList.add('urgent');
                // Reload page after 2 seconds to show updated status
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
                return;
            }
            
            const hours = Math.floor(difference / (1000 * 60 * 60));
            const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((difference % (1000 * 60)) / 1000);
            
            // Format countdown
            let countdownStr = '';
            if (hours > 0) {
                countdownStr += `${hours}j `;
            }
            if (minutes > 0 || hours > 0) {
                countdownStr += `${minutes}m `;
            }
            countdownStr += `${seconds}s`;
            
            // Add warning color and urgent class if less than 1 hour
            const totalHours = hours + (minutes / 60);
            let colorClass = '';
            
            if (totalHours < 1) {
                colorClass = 'style="color: #dc3545; font-weight: bold;"';
                timer.classList.add('urgent');
            } else if (totalHours < 6) {
                colorClass = 'style="color: #f57c00; font-weight: bold;"';
                timer.classList.remove('urgent');
            } else {
                timer.classList.remove('urgent');
            }
            
            countdownText.innerHTML = `<span ${colorClass}>${countdownStr}</span>`;
        }
        
        // Update immediately
        updateCountdown();
        
        // Update every second
        setInterval(updateCountdown, 1000);
    });
});
</script>
@endsection