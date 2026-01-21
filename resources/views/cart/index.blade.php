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

        <div class="row g-4">
            <!-- Cart Items Section -->
            <div class="col-lg-7">
                <!-- Cart Count -->
                <div class="cart-count-box">
                    <p class="cart-count-text">3 Courses in Cart</p>
                </div>

                <!-- Cart Items List -->
                <div class="cart-items-list">
                    <!-- Cart Item 1 -->
                    <div class="cart-item">
                        <div class="cart-item-checkbox">
                            <input type="checkbox" class="form-check-input cart-checkbox" id="item1" checked>
                        </div>
                        <div class="cart-item-image">
                            <img src="https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=300&h=200&fit=crop" alt="Course">
                        </div>
                        <div class="cart-item-info">
                            <h6 class="cart-item-title">Menghasilkan Foto Menarik dengan Teknik Fotografi Dasar</h6>
                            <p class="cart-item-instructor">Teacher's name</p>
                            <p class="cart-item-price">Rp100,000</p>
                        </div>
                        <button class="cart-item-delete">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>

                    <!-- Cart Item 2 -->
                    <div class="cart-item">
                        <div class="cart-item-checkbox">
                            <input type="checkbox" class="form-check-input cart-checkbox" id="item2" checked>
                        </div>
                        <div class="cart-item-image">
                            <img src="https://images.unsplash.com/photo-1561070791-2526d30994b5?w=300&h=200&fit=crop" alt="Course">
                        </div>
                        <div class="cart-item-info">
                            <h6 class="cart-item-title">Belajar Desain Grafis untuk Desain Konten Digital</h6>
                            <p class="cart-item-instructor">Teacher's name</p>
                            <p class="cart-item-price">Rp400,000</p>
                        </div>
                        <button class="cart-item-delete">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>

                    <!-- Cart Item 3 -->
                    <div class="cart-item">
                        <div class="cart-item-checkbox">
                            <input type="checkbox" class="form-check-input cart-checkbox" id="item3">
                        </div>
                        <div class="cart-item-image">
                            <img src="https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=300&h=200&fit=crop" alt="Course">
                        </div>
                        <div class="cart-item-info">
                            <h6 class="cart-item-title">Pengembangan Robot Pintar untuk Kehidupan Nyata</h6>
                            <p class="cart-item-instructor">Teacher's name</p>
                            <p class="cart-item-price">Rp100,000</p>
                        </div>
                        <button class="cart-item-delete">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>

                <!-- Empty Cart State (Hidden by default) -->
                <div class="empty-cart" style="display: none;">
                    <i class="fas fa-shopping-cart"></i>
                    <h5>Your cart is empty</h5>
                    <p>Start adding courses to your cart!</p>
                    <a href="{{ route('courses') }}" class="btn btn-primary">Browse Courses</a>
                </div>
            </div>

            <!-- Order Summary Section -->
            <div class="col-lg-5">
                <div class="order-summary-card">
                    <h5 class="summary-title">Order Summary (<span id="selectedCount">2</span> Product)</h5>

                    <!-- Promo Code -->
                    <div class="promo-section">
                        <label class="promo-label">Promo Code</label>
                        <div class="promo-input-group">
                            <input type="text" class="form-control promo-input" placeholder="Enter Code">
                            <button class="btn btn-apply">Apply</button>
                        </div>
                    </div>

                    <!-- Price Details -->
                    <div class="price-details">
                        <div class="price-row">
                            <span class="price-label">Subtotal</span>
                            <span class="price-value" id="subtotal">Rp200,000</span>
                        </div>
                        <div class="price-row">
                            <span class="price-label">PPN (11%)</span>
                            <span class="price-value" id="ppn">Rp22,000</span>
                        </div>
                        <div class="price-divider"></div>
                        <div class="price-row total-row">
                            <span class="price-label-total">Total</span>
                            <span class="price-value-total" id="total">Rp222,000</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <button class="btn btn-payment">
                            Pilih Metode Pembayaran
                        </button>
                        <button class="btn btn-checkout">
                            Checkout
                        </button>
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
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Calculate totals
    function calculateTotal() {
        const checkboxes = document.querySelectorAll('.cart-checkbox:checked');
        let subtotal = 0;
        
        checkboxes.forEach(checkbox => {
            const item = checkbox.closest('.cart-item');
            const priceText = item.querySelector('.cart-item-price').textContent;
            const price = parseInt(priceText.replace(/\D/g, ''));
            subtotal += price;
        });
        
        const ppn = Math.round(subtotal * 0.11);
        const total = subtotal + ppn;
        
        // Update UI
        document.getElementById('subtotal').textContent = formatCurrency(subtotal);
        document.getElementById('ppn').textContent = formatCurrency(ppn);
        document.getElementById('total').textContent = formatCurrency(total);
        document.getElementById('selectedCount').textContent = checkboxes.length;
    }
    
    // Format currency
    function formatCurrency(amount) {
        return 'Rp' + amount.toLocaleString('id-ID');
    }
    
    // Update total when checkbox changes
    document.querySelectorAll('.cart-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', calculateTotal);
    });
    
    // Delete item
    document.querySelectorAll('.cart-item-delete').forEach(button => {
        button.addEventListener('click', function () {
            const item = this.closest('.cart-item');

            Swal.fire({
                title: 'Remove course?',
                text: 'This course will be removed from your cart.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, remove it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {

                    item.style.opacity = '0';
                    item.style.transform = 'translateX(20px)';

                    setTimeout(() => {
                        item.remove();
                        calculateTotal();
                        updateCartCount();

                        const remainingItems = document.querySelectorAll('.cart-item').length;
                        if (remainingItems === 0) {
                            document.querySelector('.cart-items-list').style.display = 'none';
                            document.querySelector('.empty-cart').style.display = 'block';
                            document.querySelector('.order-summary-card').style.opacity = '0.5';
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Removed!',
                            text: 'Course has been removed from your cart.',
                            timer: 1500,
                            showConfirmButton: false
                        });

                    }, 300);
                }
            });
        });
    });
    
    // Update cart count
    function updateCartCount() {
        const totalItems = document.querySelectorAll('.cart-item').length;
        const countText = document.querySelector('.cart-count-text');
        if (countText) {
            countText.textContent = `${totalItems} Course${totalItems !== 1 ? 's' : ''} in Cart`;
        }
    }
    
    // Apply promo code
    document.querySelector('.btn-apply')?.addEventListener('click', function() {
        const promoInput = document.querySelector('.promo-input');
        const promoCode = promoInput.value.trim();
        
        if (promoCode) {
            // Simulate promo code validation
            alert('Promo code validation will be implemented by backend');
            // In real implementation, send AJAX request to validate promo code
        } else {
            alert('Please enter a promo code');
        }
    });
    
    // Checkout button
    document.querySelector('.btn-checkout')?.addEventListener('click', function() {
        const selectedItems = document.querySelectorAll('.cart-checkbox:checked').length;
        
        if (selectedItems === 0) {
            alert('Please select at least one course to checkout');
            return;
        }
        
        // Proceed to checkout
        window.location.href = '{{ route("checkout") }}';
    });
    
    // Payment method button
    document.querySelector('.btn-payment')?.addEventListener('click', function() {
        const selectedItems = document.querySelectorAll('.cart-checkbox:checked').length;
        
        if (selectedItems === 0) {
            alert('Please select at least one course');
            return;
        }
        
        // Show payment methods modal or redirect
        alert('Payment method selection will be implemented');
    });
    
    // Initial calculation
    calculateTotal();
</script>
@endsection