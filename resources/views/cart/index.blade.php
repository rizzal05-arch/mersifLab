@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/cart.css') }}">
<style>
    .cart-item {
        display: flex;
        align-items: center;
        padding: 1rem;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        margin-bottom: 1rem;
        background: white;
    }

    .cart-item-image img {
        width: 120px;
        height: 80px;
        object-fit: cover;
        border-radius: 6px;
    }

    .cart-item-info {
        flex: 1;
        margin-left: 1rem;
    }

    .cart-item-title {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .cart-item-instructor {
        color: #666;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }

    .cart-item-price {
        font-weight: 600;
        color: #2196f3;
        font-size: 1.1rem;
    }

    .order-summary-card {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 1.5rem;
        position: sticky;
        top: 20px;
    }

    .price-details {
        margin: 1.5rem 0;
    }

    .price-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.75rem;
    }

    .price-divider {
        border-top: 1px solid #e0e0e0;
        margin: 1rem 0;
    }

    .total-row {
        font-size: 1.2rem;
        font-weight: 600;
    }
</style>
@endsection

@section('content')
<div class="cart-page">
    <div class="container py-5">
        <!-- Page Header -->
        <div class="mb-4">
            <h1 class="page-title">Shopping Cart</h1>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4">
            <!-- Cart Items Section -->
            <div class="col-lg-7">
                @if(count($courses) > 0)
                    <!-- Cart Count -->
                    <div class="mb-3">
                        <p class="text-muted">{{ count($courses) }} Course{{ count($courses) > 1 ? 's' : '' }} in Cart</p>
                    </div>

                    <!-- Cart Items List -->
                    <div class="cart-items-list">
                        @foreach($courses as $course)
                            <div class="cart-item">
                                <div class="cart-item-image">
                                    <div style="width: 120px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                        {{ strtoupper(substr($course->name, 0, 2)) }}
                                    </div>
                                </div>
                                <div class="cart-item-info">
                                    <h6 class="cart-item-title">{{ $course->name }}</h6>
                                    <p class="cart-item-instructor">By {{ $course->teacher->name ?? 'Unknown Teacher' }}</p>
                                    <p class="cart-item-price">Rp150,000</p>
                                </div>
                                <form action="{{ route('cart.remove', $course->id) }}" method="POST" class="ms-2">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Remove this course from cart?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>

                    <!-- Clear Cart Button -->
                    <div class="mt-3">
                        <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Clear all items from cart?')">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary">
                                <i class="fas fa-trash me-2"></i>Clear Cart
                            </button>
                        </form>
                    </div>
                @else
                    <!-- Empty Cart State -->
                    <div class="empty-cart text-center py-5">
                        <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                        <h5>Your cart is empty</h5>
                        <p class="text-muted">Start adding courses to your cart!</p>
                        <a href="{{ route('courses') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-search me-2"></i>Browse Courses
                        </a>
                    </div>
                @endif
            </div>

            <!-- Order Summary Section -->
            <div class="col-lg-5">
                @if(count($courses) > 0)
                    <div class="order-summary-card">
                        <h5 class="summary-title mb-4">Order Summary ({{ count($courses) }} Product{{ count($courses) > 1 ? 's' : '' }})</h5>

                        <!-- Price Details -->
                        <div class="price-details">
                            <div class="price-row">
                                <span class="price-label">Subtotal</span>
                                <span class="price-value" id="subtotal">Rp{{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                            <div class="price-row">
                                <span class="price-label">PPN (11%)</span>
                                <span class="price-value" id="ppn">Rp{{ number_format($total * 0.11, 0, ',', '.') }}</span>
                            </div>
                            <div class="price-divider"></div>
                            <div class="price-row total-row">
                                <span class="price-label-total">Total</span>
                                <span class="price-value-total" id="total">Rp{{ number_format($total * 1.11, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2 mt-4">
                            @auth
                                @if(auth()->user()->isStudent())
                                    <form action="{{ route('cart.checkout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-lg w-100">
                                            <i class="fas fa-credit-card me-2"></i>Checkout (Enroll All)
                                        </button>
                                    </form>
                                @else
                                    <div class="alert alert-warning">
                                        <small>Hanya student yang bisa checkout</small>
                                    </div>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login to Checkout
                                </a>
                            @endauth
                            <a href="{{ route('courses') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                            </a>
                        </div>

                        <!-- Guarantee Banner -->
                        <div class="guarantee-banner mt-4 p-3 bg-light rounded">
                            <p class="guarantee-title mb-1 fw-bold">30-Day Money-Back Guarantee</p>
                            <p class="guarantee-subtitle text-muted small mb-0">Full refund if you're not satisfied</p>
                        </div>

                        <!-- Benefits List -->
                        <div class="benefits-list mt-3">
                            <div class="benefit-item d-flex align-items-center mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span class="small">Lifetime access to courses</span>
                            </div>
                            <div class="benefit-item d-flex align-items-center mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span class="small">Certificate of completion</span>
                            </div>
                            <div class="benefit-item d-flex align-items-center mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span class="small">Access on mobile and desktop</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Update cart count in header
    function updateCartCount() {
        fetch('{{ route("cart.count") }}')
            .then(response => response.json())
            .then(data => {
                const badge = document.querySelector('.cart-badge');
                if (badge) {
                    badge.textContent = data.count;
                    if (data.count === 0) {
                        badge.style.display = 'none';
                    } else {
                        badge.style.display = 'inline-block';
                    }
                }
            });
    }

    // Update on page load
    updateCartCount();
</script>
@endsection
