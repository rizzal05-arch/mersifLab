@extends('layouts.app')

@section('title', 'Subscription Payment')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/cart-checkout.css') }}">
@endsection

@section('content')
<div class="checkout-page">
    <div class="container">
        <div class="checkout-wrapper">
            <div class="checkout-left">
                <h5 class="summary-title">Ringkasan Subscription</h5>
            </div>
            <div class="checkout-content-wrapper">
                <div class="checkout-left-content">
                    <div class="product-list">
                        @php
                            $planName = ucfirst($plan);
                            $planPrice = $plan === 'standard' ? 50000 : 150000;
                            $planDescription = $plan === 'standard' 
                                ? 'Get access to all standard classes' 
                                : 'Get access to all standard to premium classes';
                        @endphp
                        <div class="product-card">
                            <div class="product-image-placeholder" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <i class="fas fa-crown" style="font-size: 2rem; color: #FFD700;"></i>
                            </div>
                            <div class="product-details">
                                <h6 class="product-title">{{ $planName }} Plan Subscription</h6>
                                <p class="product-description">{{ $planDescription }}</p>

                                <div class="product-meta">
                                    <div class="product-meta-item">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span>1 Bulan</span>
                                    </div>
                                    <div class="product-meta-item">
                                        <i class="fas fa-infinity"></i>
                                        <span>Akses Unlimited</span>
                                    </div>
                                    @if($plan === 'premium')
                                        <div class="product-meta-item">
                                            <i class="fas fa-star"></i>
                                            <span>Premium Certificate</span>
                                        </div>
                                        <div class="product-meta-item">
                                            <i class="fas fa-download"></i>
                                            <span>Download Materials</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="product-section">
                                    <h6 class="product-section-title">
                                        <i class="fas fa-gift"></i>
                                        Yang Termasuk
                                    </h6>
                                    <div class="product-includes">
                                        @if($plan === 'standard')
                                            <div class="product-include-item">
                                                <i class="fas fa-check-circle text-success"></i>
                                                <span>Akses ke semua course standard</span>
                                            </div>
                                            <div class="product-include-item">
                                                <i class="fas fa-check-circle text-success"></i>
                                                <span>Sertifikat basic</span>
                                            </div>
                                            <div class="product-include-item">
                                                <i class="fas fa-check-circle text-success"></i>
                                                <span>Email support</span>
                                            </div>
                                            <div class="product-include-item">
                                                <i class="fas fa-check-circle text-success"></i>
                                                <span>Validitas 1 bulan</span>
                                            </div>
                                        @else
                                            <div class="product-include-item">
                                                <i class="fas fa-check-circle text-success"></i>
                                                <span>Akses ke semua course (standard + premium)</span>
                                            </div>
                                            <div class="product-include-item">
                                                <i class="fas fa-check-circle text-success"></i>
                                                <span>Sertifikat premium</span>
                                            </div>
                                            <div class="product-include-item">
                                                <i class="fas fa-check-circle text-success"></i>
                                                <span>Priority support</span>
                                            </div>
                                            <div class="product-include-item">
                                                <i class="fas fa-check-circle text-success"></i>
                                                <span>Download materials</span>
                                            </div>
                                            <div class="product-include-item">
                                                <i class="fas fa-check-circle text-success"></i>
                                                <span>Validitas 1 bulan</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="product-price-section">
                                    <div class="product-price">Rp{{ number_format($planPrice, 0, ',', '.') }}<span style="font-size: 0.8em; color: #666; margin-left: 4px;">/month</span></div>
                                </div>
                            </div>
                        </div>
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
                        $total = $plan === 'standard' ? 50000 : 150000;
                    @endphp
                    <div class="price-list">
                        <div class="price-row">
                            <span>Subtotal</span>
                            <span id="subtotalCheckout">Rp{{ number_format($total, 0, ',', '.') }}</span>
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
                    
                    <!-- Bayar Sekarang Button -->
                    <button id="bayarSekarangBtn" class="btn-bayar-sekarang" onclick="showPaymentConfirmation()" disabled>
                        Bayar Sekarang
                    </button>
                    
                    <!-- Hidden form for submission -->
                    <form action="{{ route('subscription.process.payment') }}" method="POST" id="paymentForm" style="display: none;">
                        @csrf
                        <input type="hidden" name="plan" value="{{ $plan }}">
                        <input type="hidden" name="payment_method" id="selected_payment_method">
                        <input type="hidden" name="discount_amount" value="0">
                        <input type="hidden" name="final_amount" value="{{ $total }}">
                    </form>
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
                <!-- QRIS -->
                <div class="payment-category">
                    <h4>QRIS</h4>
                    <div class="payment-options">
                        <div class="payment-option payment-option-active" onclick="selectPayment('QRIS', 'qris')">
                            <img src="{{ asset('images/payment/qris.png') }}" alt="QRIS" onerror="this.src='https://via.placeholder.com/40x40/1A76D1/FFFFFF?text=QRIS'">
                            <span>QRIS</span>
                        </div>
                    </div>
                </div>

                <!-- Virtual Account -->
                <div class="payment-category">
                    <h4>Virtual Account</h4>
                    <div class="payment-options">
                        <div class="payment-option payment-option-disabled">
                            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                <img src="https://via.placeholder.com/40x40/0066CC/FFFFFF?text=BCA" alt="BCA Virtual Account">
                                <span>BCA Virtual Account</span>
                            </div>
                            <span class="payment-strip">—</span>
                        </div>
                        <div class="payment-option payment-option-disabled">
                            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                <img src="https://via.placeholder.com/40x40/FF6B35/FFFFFF?text=BNI" alt="BNI Virtual Account">
                                <span>BNI Virtual Account</span>
                            </div>
                            <span class="payment-strip">—</span>
                        </div>
                        <div class="payment-option payment-option-disabled">
                            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                <img src="https://via.placeholder.com/40x40/0047AB/FFFFFF?text=BRI" alt="BRI Virtual Account">
                                <span>BRI Virtual Account</span>
                            </div>
                            <span class="payment-strip">—</span>
                        </div>
                        <div class="payment-option payment-option-disabled">
                            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                <img src="https://via.placeholder.com/40x40/003D7A/FFFFFF?text=MANDIRI" alt="Mandiri Virtual Account">
                                <span>Mandiri Virtual Account</span>
                            </div>
                            <span class="payment-strip">—</span>
                        </div>
                    </div>
                </div>

                <!-- E-Wallet -->
                <div class="payment-category">
                    <h4>E-Wallet</h4>
                    <div class="payment-options">
                        <div class="payment-option payment-option-disabled">
                            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                <img src="https://via.placeholder.com/40x40/00AA13/FFFFFF?text=GO" alt="GoPay">
                                <span>GoPay</span>
                            </div>
                            <span class="payment-strip">—</span>
                        </div>
                        <div class="payment-option payment-option-disabled">
                            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                <img src="https://via.placeholder.com/40x40/6B46C1/FFFFFF?text=OVO" alt="OVO">
                                <span>OVO</span>
                            </div>
                            <span class="payment-strip">—</span>
                        </div>
                        <div class="payment-option payment-option-disabled">
                            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                <img src="https://via.placeholder.com/40x40/0084FF/FFFFFF?text=DANA" alt="DANA">
                                <span>DANA</span>
                            </div>
                            <span class="payment-strip">—</span>
                        </div>
                        <div class="payment-option payment-option-disabled">
                            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                <img src="https://via.placeholder.com/40x40/FF4444/FFFFFF?text=SP" alt="ShopeePay">
                                <span>ShopeePay</span>
                            </div>
                            <span class="payment-strip">—</span>
                        </div>
                    </div>
                </div>

                <!-- Retail Outlet -->
                <div class="payment-category">
                    <h4>Retail Outlet</h4>
                    <div class="payment-options">
                        <div class="payment-option payment-option-disabled">
                            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                <img src="https://via.placeholder.com/40x40/ED1C24/FFFFFF?text=ALFA" alt="Alfamart">
                                <span>Alfamart</span>
                            </div>
                            <span class="payment-strip">—</span>
                        </div>
                        <div class="payment-option payment-option-disabled">
                            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                <img src="https://via.placeholder.com/40x40/00529B/FFFFFF?text=INDO" alt="Indomaret">
                                <span>Indomaret</span>
                            </div>
                            <span class="payment-strip">—</span>
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
            'qris': '{{ asset("images/payment/qris.png") }}',
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
        
        iconImg.onerror = function() {
            this.src = 'https://via.placeholder.com/24x24/1A76D1/FFFFFF?text=QRIS';
        };
        
        iconImg.src = iconMap[code] || 'https://via.placeholder.com/24x24/CCCCCC/FFFFFF?text=?';
        iconImg.alt = name;
        
        // Update hidden input
        document.getElementById('selected_payment_method').value = code;
        
        // Enable Bayar Sekarang button
        document.getElementById('bayarSekarangBtn').disabled = false;
        
        // Close modal
        closePaymentModal();
    }

    // Payment Confirmation Functions
    function showPaymentConfirmation() {
        // Check if payment method is selected
        if (!selectedPaymentMethod) {
            alert('Silakan pilih metode pembayaran terlebih dahulu.');
            return;
        }
        
        // Disable button to prevent double submission
        const btn = document.getElementById('bayarSekarangBtn');
        btn.disabled = true;
        btn.textContent = 'Memproses...';
        
        // Get payment method from hidden input or selectedPaymentMethod
        const paymentMethodInput = document.getElementById('selected_payment_method');
        const paymentMethod = paymentMethodInput ? paymentMethodInput.value : selectedPaymentMethod.code;
        const paymentProvider = selectedPaymentMethod.code;
        
        // Submit form
        const form = document.getElementById('paymentForm');
        const formData = new FormData(form);
        
        // Send payment to server
        fetch('{{ route('subscription.process.payment') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => {
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                // If redirect response, parse as text and show success
                return response.text().then(() => ({ success: true }));
            }
        })
        .then(data => {
            if (data && data.success) {
                // Show confirmation modal
                document.getElementById('paymentConfirmationModal').style.display = 'flex';
                document.body.style.overflow = 'hidden';
            } else {
                alert(data?.message || 'Terjadi kesalahan saat memproses pembayaran.');
                btn.disabled = false;
                btn.textContent = 'Bayar Sekarang';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.');
            btn.disabled = false;
            btn.textContent = 'Bayar Sekarang';
        });
    }

    function closePaymentConfirmation() {
        document.getElementById('paymentConfirmationModal').style.display = 'none';
        document.body.style.overflow = 'auto';
        
        // Redirect to subscription page
        window.location.href = '{{ route("subscription.page") }}';
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

</script>
@endsection
