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
                        @elseif($pendingStandardPurchase)
                            <button class="btn-status btn-pending" disabled>
                                <i class="fas fa-clock"></i> Pending Payment
                            </button>
                            <div class="plan-info">
                                <p class="purchase-code">{{ $pendingStandardPurchase->purchase_code }}</p>
                                <p class="pending-note">Menunggu konfirmasi pembayaran</p>
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
                        @endphp
                        
                        @if($isSubscribedPremium)
                            <button class="btn-status btn-active" disabled>
                                <i class="fas fa-check-circle"></i> Active Plan
                            </button>
                            <div class="plan-info">
                                <p class="expiry-date">Expires: {{ $user->subscription_expires_at->format('d M Y') }}</p>
                            </div>
                        @elseif($pendingPremiumPurchase)
                            <button class="btn-status btn-pending" disabled>
                                <i class="fas fa-clock"></i> Pending Payment
                            </button>
                            <div class="plan-info">
                                <p class="purchase-code">{{ $pendingPremiumPurchase->purchase_code }}</p>
                                <p class="pending-note">Menunggu konfirmasi pembayaran</p>
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