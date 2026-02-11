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
                            @php
                                // Check if discount is still valid
                                $hasValidDiscount = false;
                                if (isset($course->has_discount) && $course->has_discount) {
                                    if (isset($course->discount_ends_at)) {
                                        $hasValidDiscount = $course->discount_ends_at->isFuture();
                                    } else {
                                        $hasValidDiscount = true;
                                    }
                                }
                                
                                // Calculate price
                                $itemPrice = $hasValidDiscount && isset($course->discounted_price) 
                                    ? $course->discounted_price 
                                    : ($course->price ?? 150000);
                            @endphp

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
                                    
                                    <!-- Badge untuk discount yang masih valid -->
                                    @if($hasValidDiscount && isset($course->discount_percentage))
                                        <div class="discount-badge">
                                            <i class="fas fa-bolt"></i>
                                            {{ $course->discount_percentage }}% OFF
                                        </div>
                                    @endif
                                </div>

                                <!-- Course Info dengan desain lebih baik -->
                                <div class="cart-item-info">
                                    <!-- Course Title -->
                                    <h6 class="cart-item-title">{{ $course->name ?? 'Untitled Course' }}</h6>
                                    
                                    <!-- Course Category -->
                                    @if(isset($course->category))
                                        <div class="cart-item-category">
                                            <span class="course-category">
                                                <i class="fas fa-bookmark"></i>
                                                {{ $course->category->name ?? $course->category_name ?? 'Uncategorized' }}
                                            </span>
                                        </div>
                                    @endif

                                    <!-- Teacher Info with Avatar -->
                                    <div class="cart-item-teacher">
                                        <div class="teacher-avatar">
                                            @if(isset($course->teacher) && !empty($course->teacher->avatar))
                                                <img src="{{ \Illuminate\Support\Facades\Storage::url($course->teacher->avatar) }}" 
                                                     alt="{{ $course->teacher->name ?? 'Teacher' }}">
                                            @else
                                                <span class="avatar-placeholder">
                                                    {{ strtoupper(substr($course->teacher->name ?? 'T', 0, 1)) }}
                                                </span>
                                            @endif
                                        </div>
                                        <span class="teacher-name">
                                            {{ $course->teacher->name ?? 'Unknown Teacher' }}
                                        </span>
                                    </div>

                                    <!-- Course Stats (Rating & Students only) -->
                                    @if(isset($course->students_count) || isset($course->rating_average))
                                        <div class="cart-item-stats">
                                            @if(isset($course->rating_average))
                                                <span class="stat-item">
                                                    <i class="fas fa-star"></i>
                                                    {{ number_format($course->rating_average, 1) }}
                                                </span>
                                            @endif
                                            @if(isset($course->students_count))
                                                <span class="stat-item">
                                                    <i class="fas fa-users"></i>
                                                    {{ number_format($course->students_count) }} students
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                    
                                    <!-- Price Container -->
                                    <div class="cart-item-price-container">
                                        @if($hasValidDiscount && isset($course->price) && isset($course->discounted_price) && $course->price > $course->discounted_price)
                                            <div class="price-group">
                                                <span class="original-price">Rp{{ number_format($course->price, 0, ',', '.') }}</span>
                                                <span class="discounted-price">Rp{{ number_format($course->discounted_price, 0, ',', '.') }}</span>
                                                @if(isset($course->discount_ends_at) && $course->discount_ends_at->isFuture())
                                                    <span class="discount-timer" data-end-time="{{ $course->discount_ends_at->toIso8601String() }}">
                                                        <i class="fas fa-clock"></i>
                                                        <span class="timer-text">Calculating...</span>
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="current-price">Rp{{ number_format($itemPrice, 0, ',', '.') }}</span>
                                        @endif
                                    </div>

                                    <!-- Additional Info from Database -->
                                    <div class="cart-item-features">
                                        @if(isset($course->formatted_includes) && is_array($course->formatted_includes) && count($course->formatted_includes) > 0)
                                            @foreach(array_slice($course->formatted_includes, 0, 3) as $include)
                                                <span class="feature-badge">
                                                    <i class="{{ $include['icon'] ?? 'fas fa-check' }}"></i> 
                                                    {{ $include['text'] ?? '' }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="feature-badge">
                                                <i class="fas fa-infinity"></i> Lifetime Access
                                            </span>
                                            <span class="feature-badge">
                                                <i class="fas fa-certificate"></i> Certificate
                                            </span>
                                        @endif
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
   DISCOUNT TIMER COUNTDOWN
   =========================== */
function initDiscountTimers() {
    const timers = document.querySelectorAll('.discount-timer');
    
    timers.forEach(timerEl => {
        const endTime = new Date(timerEl.dataset.endTime).getTime();
        const textEl = timerEl.querySelector('.timer-text');
        
        function updateTimer() {
            const now = new Date().getTime();
            const distance = endTime - now;
            
            if (distance <= 0) {
                textEl.textContent = 'Expired';
                timerEl.style.display = 'none';
                // Reload page to update prices
                setTimeout(() => location.reload(), 1000);
                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            if (days > 0) {
                textEl.textContent = `${days}d ${hours}h left`;
            } else if (hours > 0) {
                textEl.textContent = `${hours}h ${minutes}m left`;
            } else {
                textEl.textContent = `${minutes}m ${seconds}s left`;
            }
        }
        
        updateTimer();
        setInterval(updateTimer, 1000);
    });
}

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
        return; // nothing selected, silently return (no confirmation popup)
    }

    // Add selected courses to form and submit immediately
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

    document.getElementById('checkoutForm').submit();
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
    initDiscountTimers();
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

    // No PPN: total is subtotal (before promo)
    let total = subtotal;

    // Apply discount if promo is applied
    if (promoApplied) {
        const discountValue = total * (discountAmount / 100);
        total -= discountValue;

        document.getElementById('discount').textContent = '-Rp' + formatNumber(discountValue);
        document.getElementById('discountRow').style.display = 'flex';
    }

    document.getElementById('subtotal').textContent = 'Rp' + formatNumber(subtotal);
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