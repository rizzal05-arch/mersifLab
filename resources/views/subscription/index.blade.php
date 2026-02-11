@extends('layouts.app')

@section('title', 'Subscription')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2>Subscription Plans</h2>
            <p class="text-muted">Choose a subscription plan that suits your needs. Currently, subscription features are available directly without payment (simulation).</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row g-4 justify-content-center">
            <!-- Standard Plan -->
            <div class="col-md-5">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title fw-bold mb-3">Standard</h5>
                        <p class="display-6 fw-bold text-primary">Rp 50.000<span class="fs-6 fw-normal">/month</span></p>
                        <ul class="list-unstyled text-start mb-4" style="color: #555;">
                            <li class="mb-2">• Get access to all standard classes</li>
                            <li class="mb-2">• Get AI assistant</li>
                        </ul>
                        @auth
                            @php
                                $user = auth()->user();
                                $isSubscribedStandard = $user->is_subscriber && $user->subscription_plan === 'standard' && $user->subscription_expires_at && $user->subscription_expires_at > now();
                                $isPremium = $user->is_subscriber && $user->subscription_plan === 'premium' && $user->subscription_expires_at && $user->subscription_expires_at > now();
                            @endphp
                            @if($isSubscribedStandard)
                                <button class="btn btn-success w-100 fw-bold" disabled>
                                    <i class="fas fa-check-circle"></i> Active Plan
                                </button>
                                <p class="text-muted small mt-2">Expires: {{ $user->subscription_expires_at->format('d M Y') }}</p>
                            @elseif($isPremium)
                                <button class="btn btn-secondary w-100 fw-bold" disabled>
                                    <i class="fas fa-arrow-down"></i> Downgrade from Premium
                                </button>
                            @else
                                <form action="{{ url('/subscribe') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="plan" value="standard">
                                    <button type="submit" class="btn btn-primary w-100 fw-bold">Subscribe Standard</button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-primary w-100">Login to Subscribe</a>
                        @endauth
                    </div>
                </div>
            </div>

            <!-- Premium Plan -->
            <div class="col-md-5">
                <div class="card h-100 shadow-sm border border-primary" style="border-width: 2px !important;">
                    <div class="card-body text-center">
                        <h5 class="card-title fw-bold mb-3">Premium</h5>
                        <p class="display-6 fw-bold text-primary">Rp 150.000<span class="fs-6 fw-normal">/month</span></p>
                        <ul class="list-unstyled text-start mb-4" style="color: #555;">
                            <li class="mb-2">• Get access to all standard to premium classes</li>
                            <li class="mb-2">• Get smarter AI assistant (can upload files to ask questions)</li>
                        </ul>
                        @auth
                            @php
                                $user = auth()->user();
                                $isSubscribedPremium = $user->is_subscriber && $user->subscription_plan === 'premium' && $user->subscription_expires_at && $user->subscription_expires_at > now();
                            @endphp
                            @if($isSubscribedPremium)
                                <button class="btn btn-success w-100 fw-bold" disabled>
                                    <i class="fas fa-check-circle"></i> Active Plan
                                </button>
                                <p class="text-muted small mt-2">Expires: {{ $user->subscription_expires_at->format('d M Y') }}</p>
                            @else
                                <form action="{{ url('/subscribe') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="plan" value="premium">
                                    <button type="submit" class="btn btn-primary w-100 fw-bold">Subscribe Premium</button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-primary w-100">Login to Subscribe</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
