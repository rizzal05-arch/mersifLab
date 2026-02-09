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
                    <a href="#" class="btn btn-teal choose-payment">Pilih Metode Pembayaran  &nbsp; ›</a>

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
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    let promoAppliedCheckout = false;
    let discountPctCheckout = 0;

    function formatNumber(num) {
        return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

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
