<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice Subscription - {{ $subscription->purchase_code }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .invoice-wrapper {
            background-color: #ffffff;
            border: 2px solid #1A76D1;
            border-radius: 12px;
            padding: 30px;
            margin: 0 auto;
            box-shadow: 0 4px 20px rgba(26, 118, 209, 0.15);
        }
        .invoice-header {
            text-align: center;
            background: linear-gradient(135deg, #1A76D1 0%, #4A9EE0 100%);
            color: white;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .invoice-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .invoice-header .subtitle {
            margin: 8px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            border-bottom: 2px solid #1A76D1;
            padding-bottom: 20px;
        }
        .invoice-info-left, .invoice-info-right {
            flex: 1;
        }
        .invoice-info h3 {
            color: #1A76D1;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .invoice-info p {
            margin: 5px 0;
            font-size: 14px;
        }
        .invoice-details {
            margin-bottom: 30px;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .invoice-table th,
        .invoice-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .invoice-table th {
            background-color: #1A76D1;
            color: white;
            font-weight: 600;
        }
        .invoice-table .text-end {
            text-align: right;
        }
        .invoice-total {
            text-align: right;
            margin-top: 20px;
        }
        .invoice-total p {
            margin: 5px 0;
            font-size: 16px;
        }
        .invoice-total .total-amount {
            font-size: 20px;
            font-weight: 700;
            color: #1A76D1;
        }
        .qris-section {
            background: linear-gradient(135deg, #1A76D1 0%, #4A9EE0 100%);
            border: 2px solid #1565c0;
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
            text-align: center;
            color: white;
        }
        .qris-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .qris-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin: 20px auto;
            display: inline-block;
            max-width: 280px;
        }
        .qris-image {
            max-width: 240px;
            width: 100%;
            height: auto;
        }
        .invoice-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #1A76D1;
            font-size: 14px;
            color: #666;
        }
        .status-pending {
            background-color: #f59e0b;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-success {
            background-color: #22c55e;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="invoice-wrapper">
        <!-- Header -->
        <div class="invoice-header">
            <h1>MERSIFLAB</h1>
            <p class="subtitle">Invoice Subscription</p>
        </div>

        <!-- Invoice Information -->
        <div class="invoice-info">
            <div class="invoice-info-left">
                <h3>Invoice Information</h3>
                <p><strong>Invoice Number:</strong> {{ $subscription->purchase_code }}</p>
                <p><strong>Date:</strong> {{ $subscription->created_at->format('d M Y') }}</p>
                <p><strong>Time:</strong> {{ $subscription->created_at->format('H:i') }} WIB</p>
                <p><strong>Status:</strong> 
                    <span class="status-{{ $subscription->status }}">
                        {{ ucfirst($subscription->status) }}
                    </span>
                </p>
            </div>
            <div class="invoice-info-right">
                <h3>Payment Information</h3>
                <p><strong>Payment Method:</strong> {{ $subscription->payment_method ?? 'N/A' }}</p>
                @if($subscription->paid_at)
                    <p><strong>Paid At:</strong> {{ $subscription->paid_at->format('d M Y H:i') }} WIB</p>
                @else
                    <p><strong>Paid At:</strong> Not paid yet</p>
                @endif
            </div>
        </div>

        <!-- Subscription Details -->
        <div class="invoice-details">
            <h3>Subscription Details</h3>
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Subscription Plan</th>
                        <th>Duration</th>
                        <th class="text-end">Price</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>
                            <strong>Subscription Package {{ ucfirst($subscription->plan) }}</strong>
                            <br>
                            <small>ID: {{ $subscription->purchase_code }}</small>
                        </td>
                        <td>
                            @if($subscription->expires_at)
                                1 month
                            @else
                                Unlimited
                            @endif
                        </td>
                        <td class="text-end">Rp{{ number_format($subscription->final_amount ?? $subscription->amount, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Total -->
            <div class="invoice-total">
                <p><strong>Subtotal:</strong> Rp{{ number_format($subscription->final_amount ?? $subscription->amount, 0, ',', '.') }}</p>
                <p><strong>Total:</strong> <span class="total-amount">Rp{{ number_format($subscription->final_amount ?? $subscription->amount, 0, ',', '.') }}</span></p>
            </div>
        </div>

        @if($subscription->expires_at)
        <!-- Validity Period -->
        <div class="invoice-details">
            <h3>Validity Period</h3>
            <div class="invoice-info">
                <div class="invoice-info-left">
                    <p><strong>Valid From:</strong> {{ $subscription->created_at->format('d M Y') }}</p>
                </div>
                <div class="invoice-info-right">
                    <p><strong>Valid Until:</strong> {{ $subscription->expires_at->format('d M Y') }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- QRIS Payment Section (for all invoices) -->
        <div class="qris-section">
            <div class="qris-title">QRIS Payment</div>
            <div class="qris-subtitle">
                @if($subscription->status === 'success')
                    Thank you, your payment has been received
                @else
                    Scan to pay instantly
                @endif
            </div>
            <div class="qris-container">
                @if(file_exists(public_path(config('app.payment.qris_image_path'))))
                    <img src="{{ asset(config('app.payment.qris_image_path')) }}" alt="QRIS Payment" class="qris-image">
                @else
                    <div style="background: #f8f9fa; border: 1px dashed #dee2e6; padding: 40px; text-align: center; color: #6c757d;">
                        QRIS not available
                    </div>
                @endif
            </div>
            <p>
                @if($subscription->status === 'success')
                    CONTACT ADMIN - Thank you for your payment
                @else
                    SCAN HERE - Confirm after payment
                @endif
            </p>
        </div>

        <!-- Warning/Success Box -->
        <div style="background: {{ $subscription->status === 'success' ? '#d4edda' : '#fff3cd' }}; border: 2px solid {{ $subscription->status === 'success' ? '#c3e6cb' : '#ffc107' }}; border-radius: 10px; padding: 18px; margin: 25px 0; font-size: 13px;">
            <p style="margin: 0; color: {{ $subscription->status === 'success' ? '#155724' : '#856404' }}; line-height: 1.7; font-weight: 500;">
                @if($subscription->status === 'success')
                    <strong>PEMBAYARAN BERHASIL:</strong> Terima kasih atas pembayaran Anda! Subscription sudah aktif dan dapat digunakan. Jika ada pertanyaan, jangan ragu menghubungi kami.
                @else
                    <strong>PENTING:</strong> Setelah pembayaran, segera konfirmasi via WhatsApp untuk aktivasi cepat. Jangan lupa kirim bukti pembayaran!
                @endif
            </p>
        </div>

        <!-- WhatsApp Button -->
        <div style="text-align: center;">
                @if($subscription->status === 'success')
                    <a href="https://wa.me/{{ config('app.payment.whatsapp_number') }}?text={{ urlencode('Hello MersifLab, I have a question about the payment for subscription ' . $subscription->purchase_code . ' which was successful for Rp' . number_format($subscription->final_amount ?? $subscription->amount, 0, ',', '.') . '. Thank you!') }}" 
                       style="display: block; background: #25d366; color: white; padding: 16px 32px; text-decoration: none; border-radius: 10px; font-weight: 700; font-size: 16px; margin: 20px auto; text-align: center; width: fit-content; border: 2px solid #128c7e; box-shadow: 0 4px 15px rgb(215, 245, 226); transition: all 0.3s ease;" 
                       target="_blank">
                    Contact Admin
                </a>
            @else
                <a href="https://wa.me/{{ config('app.payment.whatsapp_number') }}?text={{ urlencode('Hello MersifLab, I want to confirm payment for subscription ' . $subscription->purchase_code . ' for Rp' . number_format($subscription->final_amount ?? $subscription->amount, 0, ',', '.')) }}" 
                   style="display: block; background: #25d366; color: white; padding: 16px 32px; text-decoration: none; border-radius: 10px; font-weight: 700; font-size: 16px; margin: 20px auto; text-align: center; width: fit-content; border: 2px solid #128c7e; box-shadow: 0 4px 15px rgb(215, 245, 226); transition: all 0.3s ease;" 
                   target="_blank">
                    Confirm via WhatsApp
                </a>
            @endif
        </div>

        <!-- Footer -->
        <div class="invoice-footer">
            <p><strong>Terima kasih telah berlangganan di MersifLab!</strong></p>
            <p>Invoice ini sah sebagai bukti pembayaran.</p>
            <p>Untuk bantuan, hubungi: WhatsApp {{ config('app.payment.whatsapp_number') }}</p>
        </div>
    </div>
</body>
</html>
