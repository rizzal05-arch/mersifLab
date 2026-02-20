@extends('layouts.app')

@section('title', 'Subscription')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/subs.css') }}">
@endsection

@section('content')
<section class="subscription-section">
    <div class="container">
        <!-- Header -->
        <div class="subscription-header">
            <h2>Subscription Plans</h2>
            <p>Choose a subscription plan that suits your needs. Currently, subscription features are available directly without payment (simulation).</p>
        </div>

        <!-- Success Alert -->
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        <!-- Active Subscription Alert -->
        @auth
            @php
                $user = auth()->user();
                if ($user && $user->hasActiveSubscription()) {
                    $activePlan = ucfirst($user->subscription_plan);
                    $expiryDate = $user->subscription_expires_at ? $user->subscription_expires_at->format('d M Y H:i') : 'Tidak diketahui';
                }
            @endphp
            @if(isset($activePlan))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <div style="display: flex; align-items: flex-start; gap: 12px;">
                    <i class="fas fa-info-circle" style="color: #1976d2; font-size: 20px; margin-top: 2px;"></i>
                    <div>
                        <strong>Subscription Aktif</strong><br>
                        Anda saat ini memiliki subscription <strong>{{ $activePlan }}</strong>.<br>
                        <small>Berakhir: {{ $expiryDate }} WIB</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
        @endauth

        <!-- Plans Container -->
        <div class="plans-container">
            <!-- Standard Plan -->
            <div class="plan-card">
                <div class="plan-header">
                    <h5 class="plan-name">Standard</h5>
                    <div class="plan-price">
                        <span class="price-amount">Rp 50.000</span>
                        <span class="price-period">/month</span>
                    </div>
                </div>

                <ul class="plan-features">
                    <li>Get access to all standard courses</li>
                    <li>Get unlimited AI Assistant</li>
                </ul>

                <div class="plan-action">
                    @auth
                        @php
                            $user = auth()->user();
                            $isSubscribedStandard = $user->is_subscriber && $user->subscription_plan === 'standard' && $user->subscription_expires_at && $user->subscription_expires_at > now();
                            $isPremium = $user->is_subscriber && $user->subscription_plan === 'premium' && $user->subscription_expires_at && $user->subscription_expires_at > now();
                            
                            // Check for pending subscription purchases
                            $pendingStandardPurchase = \App\Models\SubscriptionPurchase::where('user_id', $user->id)
                                ->where('plan', 'standard')
                                ->where('status', 'pending')
                                ->first();
                            
                            $pendingPremiumPurchase = \App\Models\SubscriptionPurchase::where('user_id', $user->id)
                                ->where('plan', 'premium')
                                ->where('status', 'pending')
                                ->first();
                        @endphp
                        
                        @if($isSubscribedStandard)
                            <button class="btn-status btn-active" disabled>
                                <i class="fas fa-check-circle"></i> Active Plan
                            </button>
                            <div class="plan-info">
                                <p class="expiry-date">Expires: {{ $user->subscription_expires_at->format('d M Y') }}</p>
                            </div>
                        @elseif($isPremium)
                            <button class="btn-status btn-downgrade" disabled>
                                <i class="fas fa-arrow-down"></i> Downgrade from Premium
                            </button>
                            <div class="plan-info">
                                <p class="expiry-date">Expires: {{ $user->subscription_expires_at->format('d M Y') }}</p>
                            </div>
                        @elseif($user->hasActiveSubscription())
                            <button class="btn-status btn-disabled" disabled>
                                <i class="fas fa-lock"></i> Already Have Active Subscription
                            </button>
                            <div class="plan-info">
                                <p class="expiry-date">Current plan expires: {{ $user->subscription_expires_at->format('d M Y') }}</p>
                            </div>
                        @elseif($pendingStandardPurchase)
                            <button class="btn-status btn-pending" disabled>
                                <i class="fas fa-clock"></i> Pending Payment
                            </button>
                            <div class="plan-info">
                                <p class="purchase-code">{{ $pendingStandardPurchase->purchase_code }}</p>
                                <p class="pending-note">Menunggu konfirmasi pembayaran</p>
                            </div>
                        @elseif($pendingPremiumPurchase)
                            <button class="btn-status btn-disabled" disabled>
                                <i class="fas fa-lock"></i> Premium Subscription Pending
                            </button>
                            <div class="plan-info">
                                <p class="pending-note">You have pending Premium subscription</p>
                            </div>
                        @else
                            <a href="{{ route('subscription.payment', 'standard') }}" class="btn-subscribe btn-subscribe-primary">
                                Subscribe Standard
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn-login">
                            Login to Subscribe
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Premium Plan -->
            <div class="plan-card premium">
                <div class="plan-header">
                    <h5 class="plan-name">Premium</h5>
                    <div class="plan-price">
                        <span class="price-amount">Rp 150.000</span>
                        <span class="price-period">/month</span>
                    </div>
                </div>

                <ul class="plan-features">
                    <li>Get access to all standard to premium courses</li>
                    <li>Get unlimited smarter AI assistant (can upload files to ask questions)</li>
                </ul>

                <div class="plan-action">
                    @auth
                        @php
                            $user = auth()->user();
                            $isSubscribedPremium = $user->is_subscriber && $user->subscription_plan === 'premium' && $user->subscription_expires_at && $user->subscription_expires_at > now();
                            
                            $pendingPremiumPurchase = \App\Models\SubscriptionPurchase::where('user_id', $user->id)
                                ->where('plan', 'premium')
                                ->where('status', 'pending')
                                ->first();
                            
                            $pendingStandardPurchase = \App\Models\SubscriptionPurchase::where('user_id', $user->id)
                                ->where('plan', 'standard')
                                ->where('status', 'pending')
                                ->first();
                        @endphp
                        
                        @if($isSubscribedPremium)
                            <button class="btn-status btn-active" disabled>
                                <i class="fas fa-check-circle"></i> Active Plan
                            </button>
                            <div class="plan-info">
                                <p class="expiry-date">Expires: {{ $user->subscription_expires_at->format('d M Y') }}</p>
                            </div>
                        @elseif($user->hasActiveSubscription())
                            <button class="btn-status btn-disabled" disabled>
                                <i class="fas fa-lock"></i> Already Have Active Subscription
                            </button>
                            <div class="plan-info">
                                <p class="expiry-date">Current plan expires: {{ $user->subscription_expires_at->format('d M Y') }}</p>
                            </div>
                        @elseif($pendingPremiumPurchase)
                            <button class="btn-status btn-pending" disabled>
                                <i class="fas fa-clock"></i> Pending Payment
                            </button>
                            <div class="plan-info">
                                <p class="purchase-code">{{ $pendingPremiumPurchase->purchase_code }}</p>
                                <p class="pending-note">Menunggu konfirmasi pembayaran</p>
                            </div>
                        @elseif($pendingStandardPurchase)
                            <button class="btn-status btn-disabled" disabled>
                                <i class="fas fa-lock"></i> Standard Subscription Pending
                            </button>
                            <div class="plan-info">
                                <p class="pending-note">You have pending Standard subscription</p>
                            </div>
                        @else
                            <a href="{{ route('subscription.payment', 'premium') }}" class="btn-subscribe btn-subscribe-primary">
                                Subscribe Premium
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn-login">
                            Login to Subscribe
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</section>
@endsection