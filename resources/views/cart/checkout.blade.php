@extends('layouts.app')

@section('title', 'Checkout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/cart-checkout.css') }}">
@endsection

@section('content')
<div class="checkout-page">
    <div class="container">
        <div class="checkout-wrapper">
            <div class="checkout-left">
                        <h5 class="summary-title">Ringkasan Product</h5>
                        <div class="product-list">
                            @php
                                $subtotal = 0;
                                $items = [];
                            @endphp

                            @if(isset($purchases) && $purchases->count() > 0)
                                @foreach($purchases as $p)
                                    @php
                                        $price = $p->amount;
                                        $subtotal += $price;
                                        $items[] = ['name' => $p->course->name ?? 'Course', 'price' => $price];
                                    @endphp
                                @endforeach
                            @elseif(isset($purchase))
                                @php
                                    $subtotal = $purchase->amount;
                                    $items[] = ['name' => $purchase->course->name ?? 'Course', 'price' => $purchase->amount];
                                @endphp
                            @endif

                            @foreach($items as $it)
                                <div class="product-item">
                                    <div class="product-title">{{ $it['name'] }}</div>
                                    <div class="product-price">Rp{{ number_format($it['price'], 0, ',', '.') }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

            <div class="checkout-right">
                <div class="payment-card">
                    <a href="#" class="btn btn-teal choose-payment" onclick="openPaymentModal()">Pilih Metode Pembayaran  &nbsp; ›</a>
                    
                    <!-- Selected Payment Method Display -->
                    <div id="selectedPaymentMethod" style="display: none; margin-bottom: 12px;">
                        <div class="selected-method-box">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <img id="selectedPaymentIcon" src="" alt="" style="width: 24px; height: 24px;">
                                    <span id="selectedPaymentName" style="font-weight: 500;"></span>
                                </div>
                                <button type="button" onclick="openPaymentModal()" class="btn-change-method">Ubah</button>
                            </div>
                        </div>
                    </div>

                    @php
                        $total = isset($subtotal) ? $subtotal : 0;
                    @endphp
                    <div class="promo-box" style="margin-bottom:12px">
                        <label class="promo-label">Kode Promo/Kupon</label>
                        <div style="display:flex;gap:8px">
                            <input id="promoCodeCheckout" type="text" class="promo-input" placeholder="Masukkan kode promo/kupon...">
                            <button id="applyPromoBtn" class="btn btn-outline" onclick="applyPromoCheckout()">✓</button>
                        </div>
                        <div id="promoMessageCheckout" style="margin-top:8px;font-size:0.95rem;color:#666"></div>
                    </div>

                    <div class="price-list">
                        <div class="price-row">
                            <span>Subtotal</span>
                            <span id="subtotalCheckout">Rp{{ number_format($subtotal ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="price-row" id="discountRowCheckout" style="display:none">
                            <span>Discount</span>
                            <span id="discountCheckout">-Rp0</span>
                        </div>
                        <div class="price-row">
                            <span>Biaya Transaksi</span>
                            <span>-</span>
                        </div>
                        <div class="price-row total">
                            <strong>Total</strong>
                            <strong id="totalCheckout">Rp{{ number_format($total, 0, ',', '.') }}</strong>
                        </div>
                    </div>

                    <div class="unique-note">+ kode unik</div>
                    
                    <!-- Bayar Sekarang Button -->
                    <button id="bayarSekarangBtn" class="btn-bayar-sekarang" onclick="showPaymentConfirmation()" disabled>
                        Bayar Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Payment Method Modal -->
<div id="paymentModal" class="payment-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Pilih Metode Pembayaran</h3>
            <button class="close-btn" onclick="closePaymentModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="payment-methods-grid">
                <!-- Virtual Account -->
                <div class="payment-category">
                    <h4>Virtual Account</h4>
                    <div class="payment-options">
                        <div class="payment-option" onclick="selectPayment('BCA VA', 'bca-va')">
                            <img src="https://via.placeholder.com/40x40/0066CC/FFFFFF?text=BCA" alt="BCA Virtual Account">
                            <span>BCA Virtual Account</span>
                        </div>
                        <div class="payment-option" onclick="selectPayment('BNI VA', 'bni-va')">
                            <img src="https://via.placeholder.com/40x40/FF6B35/FFFFFF?text=BNI" alt="BNI Virtual Account">
                            <span>BNI Virtual Account</span>
                        </div>
                        <div class="payment-option" onclick="selectPayment('BRI VA', 'bri-va')">
                            <img src="https://via.placeholder.com/40x40/0047AB/FFFFFF?text=BRI" alt="BRI Virtual Account">
                            <span>BRI Virtual Account</span>
                        </div>
                        <div class="payment-option" onclick="selectPayment('Mandiri VA', 'mandiri-va')">
                            <img src="https://via.placeholder.com/40x40/003D7A/FFFFFF?text=MANDIRI" alt="Mandiri Virtual Account">
                            <span>Mandiri Virtual Account</span>
                        </div>
                    </div>
                </div>

                <!-- E-Wallet -->
                <div class="payment-category">
                    <h4>E-Wallet</h4>
                    <div class="payment-options">
                        <div class="payment-option" onclick="selectPayment('GoPay', 'gopay')">
                            <img src="https://via.placeholder.com/40x40/00AA13/FFFFFF?text=GO" alt="GoPay">
                            <span>GoPay</span>
                        </div>
                        <div class="payment-option" onclick="selectPayment('OVO', 'ovo')">
                            <img src="https://via.placeholder.com/40x40/6B46C1/FFFFFF?text=OVO" alt="OVO">
                            <span>OVO</span>
                        </div>
                        <div class="payment-option" onclick="selectPayment('DANA', 'dana')">
                            <img src="https://via.placeholder.com/40x40/0084FF/FFFFFF?text=DANA" alt="DANA">
                            <span>DANA</span>
                        </div>
                        <div class="payment-option" onclick="selectPayment('ShopeePay', 'shopeepay')">
                            <img src="https://via.placeholder.com/40x40/FF4444/FFFFFF?text=SP" alt="ShopeePay">
                            <span>ShopeePay</span>
                        </div>
                    </div>
                </div>

                <!-- Retail Outlet -->
                <div class="payment-category">
                    <h4>Retail Outlet</h4>
                    <div class="payment-options">
                        <div class="payment-option" onclick="selectPayment('Alfamart', 'alfamart')">
                            <img src="https://via.placeholder.com/40x40/ED1C24/FFFFFF?text=ALFA" alt="Alfamart">
                            <span>Alfamart</span>
                        </div>
                        <div class="payment-option" onclick="selectPayment('Indomaret', 'indomaret')">
                            <img src="https://via.placeholder.com/40x40/00529B/FFFFFF?text=INDO" alt="Indomaret">
                            <span>Indomaret</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Confirmation Modal -->
<div id="paymentConfirmationModal" class="payment-confirmation-modal">
    <div class="confirmation-modal-content">
        <div class="confirmation-icon">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="#28a745"/>
            </svg>
        </div>
        <div class="confirmation-content">
            <h3>Invoice Terkirim!</h3>
            <p>Invoice pembayaran telah dikirim ke email Anda.</p>
            <p>Silakan cek email untuk melakukan pembayaran dan konfirmasi.</p>
            <p>Tunggu notifikasi bahwa pembayaran telah disetujui oleh admin.</p>
        </div>
        <div class="confirmation-actions">
            <button class="btn btn-primary" onclick="closePaymentConfirmation()">OK, Mengerti</button>
            <a href="{{ route('purchase-history') }}" class="btn btn-outline">Lihat Riwayat Pembelian</a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let promoAppliedCheckout = false;
    let discountPctCheckout = 0;
    let selectedPaymentMethod = null;

    function formatNumber(num) {
        return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Payment Modal Functions
    function openPaymentModal() {
        document.getElementById('paymentModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    function selectPayment(name, code) {
        selectedPaymentMethod = { name, code };
        
        // Update UI
        const selectedDiv = document.getElementById('selectedPaymentMethod');
        const nameSpan = document.getElementById('selectedPaymentName');
        const iconImg = document.getElementById('selectedPaymentIcon');
        
        // Hide the choose payment button
        document.querySelector('.choose-payment').style.display = 'none';
        
        // Show selected payment method
        selectedDiv.style.display = 'block';
        nameSpan.textContent = name;
        
        // Set icon based on payment method
        const iconMap = {
            'bca-va': 'https://via.placeholder.com/24x24/0066CC/FFFFFF?text=BCA',
            'bni-va': 'https://via.placeholder.com/24x24/FF6B35/FFFFFF?text=BNI',
            'bri-va': 'https://via.placeholder.com/24x24/0047AB/FFFFFF?text=BRI',
            'mandiri-va': 'https://via.placeholder.com/24x24/003D7A/FFFFFF?text=MANDIRI',
            'gopay': 'https://via.placeholder.com/24x24/00AA13/FFFFFF?text=GO',
            'ovo': 'https://via.placeholder.com/24x24/6B46C1/FFFFFF?text=OVO',
            'dana': 'https://via.placeholder.com/24x24/0084FF/FFFFFF?text=DANA',
            'shopeepay': 'https://via.placeholder.com/24x24/FF4444/FFFFFF?text=SP',
            'alfamart': 'https://via.placeholder.com/24x24/ED1C24/FFFFFF?text=ALFA',
            'indomaret': 'https://via.placeholder.com/24x24/00529B/FFFFFF?text=INDO'
        };
        
        iconImg.src = iconMap[code] || 'https://via.placeholder.com/24x24/CCCCCC/FFFFFF?text=?';
        iconImg.alt = name;
        
        // Enable Bayar Sekarang button
        document.getElementById('bayarSekarangBtn').disabled = false;
        
        // Close modal
        closePaymentModal();
        
        // Add hidden input for form submission
        let hiddenInput = document.getElementById('selected_payment_method');
        if (!hiddenInput) {
            hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.id = 'selected_payment_method';
            hiddenInput.name = 'payment_method';
            document.querySelector('.payment-card').appendChild(hiddenInput);
        }
        hiddenInput.value = code;
    }

    // Payment Confirmation Functions
    function showPaymentConfirmation() {
        // Check if payment method is selected
        if (!selectedPaymentMethod) {
            alert('Silakan pilih metode pembayaran terlebih dahulu.');
            return;
        }
        
        // Show confirmation modal
        document.getElementById('paymentConfirmationModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        // Here you would typically send the order to server
        // For demo purposes, we'll just show the modal
        console.log('Processing payment with method:', selectedPaymentMethod);
    }

    function closePaymentConfirmation() {
        document.getElementById('paymentConfirmationModal').style.display = 'none';
        document.body.style.overflow = 'auto';
        
        // Redirect to courses page
        window.location.href = '{{ route('courses') }}';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('paymentModal');
        if (event.target === modal) {
            closePaymentModal();
        }
    }

    // Escape key to close modal
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closePaymentModal();
        }
    });

    function applyPromoCheckout() {
        if (promoAppliedCheckout) return;

        const code = document.getElementById('promoCodeCheckout').value.trim();
        const msgEl = document.getElementById('promoMessageCheckout');
        if (!code) {
            msgEl.textContent = 'Please enter a promo code';
            msgEl.style.color = '#d9534f';
            return;
        }

        // Simple promo mapping (update server-side later if needed)
        const valid = {
            'WELCOME10': 10,
            'SAVE20': 20,
            'SPECIAL50': 50
        };

        const up = code.toUpperCase();
        if (!valid[up]) {
            msgEl.textContent = 'Invalid promo code';
            msgEl.style.color = '#d9534f';
            return;
        }

        discountPctCheckout = valid[up];
        promoAppliedCheckout = true;

        msgEl.textContent = `Promo applied: ${discountPctCheckout}% off`;
        msgEl.style.color = '#28a745';

        // Update totals
        const subtotalText = document.getElementById('subtotalCheckout').textContent.replace(/[^0-9]/g, '');
        const subtotal = parseInt(subtotalText || '0');
        const discountValue = Math.round(subtotal * (discountPctCheckout/100));
        const total = subtotal - discountValue;

        document.getElementById('discountCheckout').textContent = '-Rp' + formatNumber(discountValue);
        document.getElementById('discountRowCheckout').style.display = 'flex';
        document.getElementById('totalCheckout').textContent = 'Rp' + formatNumber(total);

        // disable input
        document.getElementById('promoCodeCheckout').disabled = true;
        document.getElementById('applyPromoBtn').disabled = true;
    }
</script>
@endsection
