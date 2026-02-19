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
                                    </div>
                                    
                                    @if($transaction['type'] === 'subscription')
                                        <h5 class="purchase-course-title">
                                            <i class="fas fa-crown me-2"></i>
                                            Paket Berlangganan {{ ucfirst($transaction['plan']) }}
                                        </h5>
                                    @else
                                        <h5 class="purchase-course-title">{{ $transaction['course']->name ?? 'Course tidak ditemukan' }}</h5>
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
                                                <a href="{{ route('invoice', $transaction['id']) }}" class="btn btn-sm btn-outline-primary me-2" target="_blank">
                                                    <i class="fas fa-eye me-1"></i> View Invoice
                                                </a>
                                                <a href="{{ route('invoice.download', $transaction['id']) }}" class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-download me-1"></i> Download
                                                </a>
                                            @elseif($transaction['type'] === 'subscription')
                                                <a href="{{ route('invoice', $transaction['id']) }}" class="btn btn-sm btn-outline-primary me-2" target="_blank">
                                                    <i class="fas fa-eye me-1"></i> View Invoice
                                                </a>
                                                <a href="{{ route('invoice.download', $transaction['id']) }}" class="btn btn-sm btn-outline-secondary">
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
@endsection