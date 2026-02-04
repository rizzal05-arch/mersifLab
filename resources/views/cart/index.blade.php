@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/cart.css') }}">
@endsection

@section('content')
<div class="cart-page">
    <div class="container">
        <!-- Page Header dengan animasi -->
        <div class="cart-header">
            <div class="header-content">
                <i class="fas fa-shopping-cart header-icon"></i>
                <h1 class="page-title">Shopping Cart</h1>
                <p class="page-subtitle">Review your selected courses before checkout</p>
            </div>
        </div>

        @if(count($courses) > 0)
            <div class="row g-4">
                <!-- Cart Items Section -->
                <div class="col-lg-8">
                    <!-- Cart Count Box dengan design lebih menarik -->
                    <div class="cart-count-box">
                        <div class="count-content">
                            <i class="fas fa-shopping-bag me-2"></i>
                            <span class="cart-count-text">
                                {{ count($courses) }} Course{{ count($courses) > 1 ? 's' : '' }} in Cart
                            </span>
                        </div>
                        <div class="select-all-container">
                            <input type="checkbox" id="selectAll" class="select-all-checkbox" checked>
                            <label for="selectAll" class="select-all-label">Select All</label>
                        </div>
                    </div>

                    <!-- Cart Items List -->
                    <div class="cart-items-list">
                        @foreach($courses as $course)
                            <div class="cart-item" data-course-id="{{ $course->id }}">
                                <!-- Checkbox -->
                                <div class="cart-item-checkbox">
                                    <input type="checkbox" class="cart-checkbox" id="course-{{ $course->id }}" checked>
                                    <label for="course-{{ $course->id }}" class="checkbox-label"></label>
                                </div>

                                <!-- Course Image - Multiple field support -->
                                <div class="cart-item-image">
                                    @php
                                        // Check berbagai kemungkinan nama field untuk gambar
                                        $courseImage = $course->image 
                                                    ?? $course->thumbnail 
                                                    ?? $course->cover_image 
                                                    ?? $course->picture 
                                                    ?? null;
                                    @endphp

                                    @if($courseImage)
                                        <img src="{{ asset('storage/' . $courseImage) }}" 
                                             alt="{{ $course->name }}"
                                             style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        <div class="course-placeholder">
                                            <i class="fas fa-graduation-cap"></i>
                                        </div>
                                    @endif
                                    
                                    <!-- Badge untuk discount -->
                                    @if(isset($course->has_discount) && $course->has_discount && isset($course->discount))
                                        <div class="discount-badge">
                                            {{ $course->discount }}% OFF
                                        </div>
                                    @endif
                                </div>

                                <!-- Course Info dengan desain lebih baik -->
                                <div class="cart-item-info">
                                    <h6 class="cart-item-title">{{ $course->name ?? 'Untitled Course' }}</h6>
                                    
                                    <div class="cart-item-meta">
                                        <span class="instructor-name">
                                            <i class="fas fa-user-tie me-1"></i>
                                            {{ $course->teacher->name ?? 'Unknown Teacher' }}
                                        </span>
                                    </div>

                                    @php
                                        $itemPrice = $course->discounted_price ?? $course->price ?? 150000;
                                    @endphp
                                    
                                    <div class="cart-item-price-container">
                                        @if(isset($course->has_discount) && $course->has_discount && isset($course->discount))
                                            <span class="original-price">Rp{{ number_format($course->price ?? 150000, 0, ',', '.') }}</span>
                                            <span class="discounted-price">Rp{{ number_format($itemPrice, 0, ',', '.') }}</span>
                                        @else
                                            <span class="current-price">Rp{{ number_format($itemPrice, 0, ',', '.') }}</span>
                                        @endif
                                    </div>

                                    <!-- Additional Info -->
                                    <div class="cart-item-features">
                                        <span class="feature-badge">
                                            <i class="fas fa-infinity"></i> Lifetime Access
                                        </span>
                                        <span class="feature-badge">
                                            <i class="fas fa-certificate"></i> Certificate
                                        </span>
                                    </div>
                                </div>

                                <!-- Delete Button dengan design lebih baik -->
                                <div class="cart-item-actions">
                                    <form action="{{ route('cart.remove') }}" method="POST" class="cart-item-delete-form">
                                        @csrf
                                        <input type="hidden" name="course_id" value="{{ $course->id }}">
                                        <button type="button"
                                                class="cart-item-delete"
                                                onclick="confirmDelete({{ $course->id }}, '{{ addslashes($course->name) }}')"
                                                title="Remove from cart">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Order Summary Sidebar - TETAP PAKAI INCLUDE -->
                <div class="col-lg-4">
                    @include('cart.sidebar', ['courses' => $courses, 'total' => $total])
                </div>
            </div>
        @else
            <!-- Empty Cart State dengan design lebih menarik -->
            <div class="empty-cart">
                <div class="empty-cart-animation">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h5>Your cart is empty</h5>
                <p>Looks like you haven't added any courses yet.<br>Start exploring our amazing courses!</p>
                <a href="{{ route('courses') }}" class="btn-browse">
                    <i class="fas fa-compass me-2"></i>
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
        showConfirmButton: false,
        toast: true,
        position: 'top-end',
        background: '#d4edda',
        color: '#155724'
    });
@endif

@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Oops!',
        text: '{{ session('error') }}',
        confirmButtonColor: '#dc3545'
    });
@endif

@if(session('info'))
    Swal.fire({
        icon: 'info',
        title: 'Information',
        text: '{{ session('info') }}',
        confirmButtonColor: '#17a2b8'
    });
@endif

@if(session('warning'))
    Swal.fire({
        icon: 'warning',
        title: 'Warning!',
        text: '{{ session('warning') }}',
        confirmButtonColor: '#ffc107'
    });
@endif

/* ===========================
   PROMO CODE
   =========================== */
let promoApplied = false;
let discountAmount = 0;

function applyPromo() {
    const promoCode = document.getElementById('promoCode').value.trim();
    const promoMessage = document.getElementById('promoMessage');

    if (promoCode === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Oops!',
            text: 'Please enter a promo code',
            confirmButtonColor: '#1976d2'
        });
        return;
    }

    // Simulasi pengecekan promo code
    const validPromoCodes = {
        'WELCOME10': 10,
        'SAVE20': 20,
        'SPECIAL50': 50
    };

    if (validPromoCodes[promoCode.toUpperCase()]) {
        const discount = validPromoCodes[promoCode.toUpperCase()];
        promoApplied = true;
        discountAmount = discount;
        
        promoMessage.innerHTML = `<i class="fas fa-check-circle"></i> Promo applied! ${discount}% discount`;
        promoMessage.className = 'promo-message success';
        
        updateTotal();
        
        Swal.fire({
            icon: 'success',
            title: 'Promo Applied!',
            text: `You got ${discount}% discount with code "${promoCode}"`,
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });

        // Disable input dan button
        document.getElementById('promoCode').disabled = true;
        document.querySelector('.btn-apply').disabled = true;
    } else {
        promoMessage.innerHTML = `<i class="fas fa-times-circle"></i> Invalid promo code`;
        promoMessage.className = 'promo-message error';
        
        Swal.fire({
            icon: 'error',
            title: 'Invalid Code',
            text: 'The promo code you entered is not valid',
            confirmButtonColor: '#dc3545'
        });
    }
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
function confirmDelete(courseId, courseName) {
    Swal.fire({
        title: 'Remove Course?',
        html: `Are you sure you want to remove<br><strong>${courseName}</strong><br>from your cart?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash me-2"></i>Yes, Remove',
        cancelButtonText: '<i class="fas fa-times me-2"></i>Cancel',
        reverseButtons: true,
        focusCancel: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Tampilkan loading
            Swal.fire({
                title: 'Removing...',
                text: 'Please wait',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Submit form
            const form = document.querySelector(`form input[value="${courseId}"]`).closest('form');
            form.submit();
        }
    });
}

/* ===========================
   CHECKOUT CONFIRMATION
   =========================== */
function confirmCheckout() {
    const checkedCount = document.querySelectorAll('.cart-checkbox:checked').length;
    
    if (checkedCount === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No Course Selected',
            text: 'Please select at least one course to checkout',
            confirmButtonColor: '#1976d2'
        });
        return;
    }

    const total = document.getElementById('total').textContent;
    
    Swal.fire({
        title: 'Confirm Checkout',
        html: `
            <div style="text-align: left; padding: 10px;">
                <p><strong>Selected Courses:</strong> ${checkedCount}</p>
                <p><strong>Total Amount:</strong> ${total}</p>
                <hr>
                <p style="font-size: 0.9em; color: #666;">
                    <i class="fas fa-info-circle"></i> You will be redirected to payment page
                </p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-credit-card me-2"></i>Proceed to Payment',
        cancelButtonText: '<i class="fas fa-arrow-left me-2"></i>Continue Shopping',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Tampilkan loading
            Swal.fire({
                title: 'Processing...',
                text: 'Preparing your checkout',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Add selected courses to form
            const selectedCoursesContainer = document.getElementById('selectedCoursesContainer');
            selectedCoursesContainer.innerHTML = ''; // Clear previous
            
            document.querySelectorAll('.cart-checkbox:checked').forEach((checkbox) => {
                const courseId = checkbox.id.replace('course-', '');
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'course_ids[]';
                input.value = courseId;
                selectedCoursesContainer.appendChild(input);
            });

            // Submit form
            document.getElementById('checkoutForm').submit();
        }
    });
}

/* ===========================
   CHECKBOX & TOTAL CALC
   =========================== */
document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = document.querySelectorAll('.cart-checkbox');
    const selectAllCheckbox = document.getElementById('selectAll');

    // Select All functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
            });
            updateTotal();
        });
    }

    // Individual checkbox change
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            updateTotal();
            
            // Update select all checkbox
            if (selectAllCheckbox) {
                const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
                selectAllCheckbox.checked = allChecked;
            }
        });
    });

    updateTotal();
});

function updateTotal() {
    const checkboxes = document.querySelectorAll('.cart-checkbox:checked');
    let subtotal = 0;

    // Hitung subtotal dari course yang dipilih
    checkboxes.forEach(checkbox => {
        const cartItem = checkbox.closest('.cart-item');
        const priceElement = cartItem.querySelector('.discounted-price, .current-price');
        if (priceElement) {
            const priceText = priceElement.textContent.replace(/[^0-9]/g, '');
            subtotal += parseInt(priceText);
        }
    });

    const ppn = subtotal * 0.11;
    let total = subtotal + ppn;

    // Apply discount if promo is applied
    if (promoApplied) {
        const discountValue = total * (discountAmount / 100);
        total -= discountValue;
        
        document.getElementById('discount').textContent = '-Rp' + formatNumber(discountValue);
        document.getElementById('discountRow').style.display = 'flex';
    }

    document.getElementById('subtotal').textContent = 'Rp' + formatNumber(subtotal);
    document.getElementById('ppn').textContent = 'Rp' + formatNumber(ppn);
    document.getElementById('total').textContent = 'Rp' + formatNumber(total);
}

function formatNumber(num) {
    return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

/* ===========================
   INIT
   =========================== */
updateCartCount();

// Add smooth scroll animation
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});
</script>
@endsection