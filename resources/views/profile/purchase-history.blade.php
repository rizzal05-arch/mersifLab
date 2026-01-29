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
                        @if(isset($purchases) && $purchases->count() > 0)
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