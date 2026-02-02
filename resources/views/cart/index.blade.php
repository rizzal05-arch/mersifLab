@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/cart.css') }}">
@endsection

@section('content')
<div class="cart-page">
    <div class="container">
        <!-- Page Header -->
        <div class="cart-header">
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

        @if(count($courses) > 0)
            <div class="row g-4">
                <!-- Cart Items Section -->
                <div class="col-lg-8">
                    <!-- Cart Count Box -->
                    <div class="cart-count-box">
                        <p class="cart-count-text">
                            <i class="fas fa-shopping-cart me-2"></i>
                            {{ count($courses) }} Course{{ count($courses) > 1 ? 's' : '' }} in Cart
                        </p>
                    </div>

                    <!-- Cart Items List -->
                    <div class="cart-items-list">
                        @foreach($courses as $course)
                            <div class="cart-item" data-course-id="{{ $course->id }}">
                                <!-- Checkbox -->
                                <div class="cart-item-checkbox">
                                    <input type="checkbox" class="cart-checkbox" checked>
                                </div>

                                <!-- Course Image -->
                                <div class="cart-item-image">
                                    @if($course->thumbnail)
                                        <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->name }}">
                                    @else
                                        <div class="course-placeholder">
                                            {{ strtoupper(substr($course->name, 0, 2)) }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Course Info -->
                                <div class="cart-item-info">
                                    <h6 class="cart-item-title">{{ $course->name }}</h6>
                                    <p class="cart-item-instructor">{{ $course->teacher->name ?? 'Unknown Teacher' }}</p>
                                    @php
                                        $itemPrice = $course->discounted_price ?? $course->price ?? 150000;
                                    @endphp
                                    <p class="cart-item-price">
                                        @if($course->has_discount && $course->discount)
                                            <span class="text-muted text-decoration-line-through">Rp{{ number_format($course->price ?? 150000, 0, ',', '.') }}</span>
                                            <span class="ms-2 text-primary fw-bold">Rp{{ number_format($itemPrice, 0, ',', '.') }}</span>
                                        @else
                                            Rp{{ number_format($itemPrice, 0, ',', '.') }}
                                        @endif
                                    </p>
                                </div>

                                <!-- Delete Button -->
                                <form action="{{ route('cart.remove') }}" method="POST" class="cart-item-delete-form">
                                    @csrf
                                    <input type="hidden" name="course_id" value="{{ $course->id }}">
                                    <button type="button"
                                            class="cart-item-delete"
                                            onclick="confirmDelete({{ $course->id }})">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Order Summary Section -->
                <div class="col-lg-4">
                    <div class="order-summary-card">
                        <h5 class="summary-title">Order Summary ({{ count($courses) }} Product{{ count($courses) > 1 ? 's' : '' }})</h5>

                        <!-- Promo Code Section -->
                        <div class="promo-section">
                            <label class="promo-label">Promo Code</label>
                            <div class="promo-input-group">
                                <input type="text" class="promo-input" placeholder="Enter Code" id="promoCode">
                                <button class="btn-apply" onclick="applyPromo()">Apply</button>
                            </div>
                        </div>

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
                        <div class="action-buttons">
                            @auth
                                @if(auth()->user()->isStudent())
                                    <button class="btn-payment" onclick="alert('Metode pembayaran akan muncul di sini')">
                                        <i class="fas fa-credit-card me-2"></i>Pilih Metode Pembayaran
                                    </button>
                                    <form action="{{ route('cart.checkout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn-checkout">
                                            <i class="fas fa-shopping-bag me-2"></i>Checkout
                                        </button>
                                    </form>
                                @else
                                    <div class="alert alert-warning mb-0">
                                        <small>Hanya student yang bisa checkout</small>
                                    </div>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn-checkout text-center text-decoration-none">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login to Checkout
                                </a>
                            @endauth
                        </div>

                        <!-- Guarantee Banner -->
                        <div class="guarantee-banner">
                            <p class="guarantee-title">30-Day Money-Back Guarantee</p>
                            <p class="guarantee-subtitle">Full refund if you're not satisfied</p>
                        </div>

                        <!-- Benefits List -->
                        <div class="benefits-list">
                            <div class="benefit-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Lifetime access to courses</span>
                            </div>
                            <div class="benefit-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Certificate of completion</span>
                            </div>
                            <div class="benefit-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Access on mobile and desktop</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty Cart State -->
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h5>Your cart is empty</h5>
                <p>Start adding courses to your cart!</p>
                <a href="{{ route('courses') }}" class="btn-browse">
                    Browse Courses
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
/* ===========================
   AUTO SHOW FLASH MESSAGES
   =========================== */
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
        timer: 2000,
        showConfirmButton: false
    });
@endif

@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Failed!',
        text: '{{ session('error') }}'
    });
@endif

@if(session('info'))
    Swal.fire({
        icon: 'info',
        title: 'Informasi',
        text: '{{ session('info') }}'
    });
@endif

@if(session('warning'))
    Swal.fire({
        icon: 'warning',
        title: 'Warning!',
        text: '{{ session('warning') }}'
    });
@endif

/* ===========================
   SWEET ALERT HELPERS
   =========================== */
function showSuccess(message) {
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: message,
        timer: 1500,
        showConfirmButton: false
    });
}

function showError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Failed',
        text: message
    });
}

/* ===========================
   PROMO CODE
   =========================== */
function applyPromo() {
    const promoCode = document.getElementById('promoCode').value;

    if (promoCode.trim() === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Oops!',
            text: 'Please enter promo code'
        });
        return;
    }

    // Dummy success (nanti bisa diganti API)
    Swal.fire({
        icon: 'success',
        title: 'Promo Code Applied',
        text: `Promo "${promoCode}" has been successfully applied`,
        timer: 2000,
        showConfirmButton: false
    });
}

/* ===========================
   CART COUNT HEADER
   =========================== */
function updateCartCount() {
    fetch('{{ route("cart.count") }}')
        .then(response => response.json())
        .then(data => {
            const badge = document.querySelector('.cart-badge');
            if (badge) {
                badge.textContent = data.count;
                badge.style.display = data.count === 0 ? 'none' : 'inline-block';
            }
        })
        .catch(() => {
            console.error('Failed to update cart count');
        });
}

/* ===========================
   DELETE CART (CONFIRM)
   =========================== */
function confirmDelete(courseId) {
    Swal.fire({
        title: 'Are you sure you want to remove?',
        text: 'This course will be removed from cart',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit form
            const form = document.querySelector(`form input[value="${courseId}"]`).closest('form');
            form.submit();
        }
    });
}

/* ===========================
   CHECKBOX & TOTAL CALC
   =========================== */
document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = document.querySelectorAll('.cart-checkbox');

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateTotal);
    });

    updateTotal();
});

function updateTotal() {
    const checkedCount = document.querySelectorAll('.cart-checkbox:checked').length;
    const pricePerCourse = 150000;

    const subtotal = checkedCount * pricePerCourse;
    const ppn = subtotal * 0.11;
    const total = subtotal + ppn;

    document.getElementById('subtotal').textContent = 'Rp' + formatNumber(subtotal);
    document.getElementById('ppn').textContent = 'Rp' + formatNumber(ppn);
    document.getElementById('total').textContent = 'Rp' + formatNumber(total);
}

function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

/* ===========================
   INIT
   =========================== */
updateCartCount();
</script>
@endsection