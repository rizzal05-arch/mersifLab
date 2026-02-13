<!-- Order Summary Sidebar - STICKY VERSION -->
<div class="order-summary-card">
    <div class="summary-header">
        <h5 class="summary-title">
            <i class="fas fa-receipt me-2"></i>Order Summary
        </h5>
        <span class="items-count">{{ count($courses) }} item{{ count($courses) > 1 ? 's' : '' }}</span>
    </div>

    <!-- Promo removed per request -->

    <!-- Price Details -->
    <div class="price-details">
        <div class="price-row">
            <span class="price-label">
                <i class="fas fa-list-ul me-1"></i>Subtotal
            </span>
            <span class="price-value" id="subtotal">Rp{{ number_format($total, 0, ',', '.') }}</span>
        </div>
        <!-- Tax row removed -->
        <div class="price-row discount-row" id="discountRow" style="display: none;">
            <span class="price-label">
                <i class="fas fa-badge-percent me-1"></i>Discount
            </span>
            <span class="price-value discount-value" id="discount">-Rp0</span>
        </div>
        <div class="price-divider"></div>
        <div class="price-row total-row">
            <span class="price-label-total">
                <i class="fas fa-wallet me-1"></i>Total Payment
            </span>
            <span class="price-value-total" id="total">Rp{{ number_format($total, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <!-- Back Button -->
        <button class="btn btn-outline-secondary mb-3" onclick="showCancelConfirmation()" style="width: 100%;">
            <i class="fas fa-arrow-left"></i> Kembali
        </button>
        
        @auth
            @if(auth()->user()->isStudent())
                <form action="{{ route('cart.prepareCheckout') }}" method="POST" id="checkoutForm">
                    @csrf
                    <!-- Hidden input untuk selected courses -->
                    <div id="selectedCoursesContainer"></div>
                    <button type="button" class="btn-checkout" onclick="confirmCheckout()">
                        <i class="fas fa-lock me-2"></i>
                        <span>Proceed to Checkout</span>
                        <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </form>
            @else
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <small>Only students can checkout</small>
                </div>
            @endif
        @else
            <a href="{{ route('login') }}" class="btn-checkout text-center text-decoration-none">
                <i class="fas fa-sign-in-alt me-2"></i>
                <span>Login to Checkout</span>
            </a>
        @endauth
    </div>

    <!-- Guarantee Banner -->
    <div class="guarantee-banner">
        <div class="guarantee-icon">
            <i class="fas fa-shield-alt"></i>
        </div>
        <div class="guarantee-content">
            <p class="guarantee-title">30-Day Money-Back Guarantee</p>
            <p class="guarantee-subtitle">Full refund if you're not satisfied</p>
        </div>
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
        <div class="benefit-item">
            <i class="fas fa-check-circle"></i>
            <span>24/7 customer support</span>
        </div>
    </div>

    <!-- Security Badge -->
    <div class="security-badge">
        <i class="fas fa-lock me-2"></i>
        <span>Secure Payment Processing</span>
    </div>
</div>

<!-- Cancel Purchase Confirmation Modal -->
<div id="cancelPurchaseModal" class="payment-confirmation-modal">
    <div class="confirmation-modal-content">
        <div class="confirmation-icon" style="background: #dc3545;">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" fill="#ffffff"/>
            </svg>
        </div>
        <div class="confirmation-content">
            <h3>Batalkan Pembelian?</h3>
            <p>Apakah Anda yakin ingin membatalkan pembelian ini?</p>
            <p>Course akan tetap tersedia di keranjang untuk pembelian kembali.</p>
        </div>
        <div class="confirmation-actions">
            <button class="btn btn-secondary" onclick="closeCancelConfirmation()">Tidak</button>
            <button class="btn btn-danger" onclick="confirmCancelPurchase()">Ya, Batalkan</button>
        </div>
    </div>
</div>