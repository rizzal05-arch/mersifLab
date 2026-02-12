<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $invoice->title }} - {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .header {
            text-align: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px 10px 0 0;
            margin-bottom: 0;
        }
        .content {
            background: white;
            padding: 30px;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .invoice-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .payment-section {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background: #fff3cd;
            border-radius: 8px;
            border: 1px solid #ffeaa7;
        }
        .qris-container {
            margin: 20px 0;
            text-align: center;
        }
        .qris-image {
            max-width: 200px;
            height: auto;
            border: 3px solid #e0e0e0;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            background: white;
            padding: 10px;
        }
        .scan-text {
            color: #666;
            font-size: 14px;
            margin-top: 10px;
            font-weight: bold;
        }
        .action-button {
            display: inline-block;
            background: #25d366;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            margin: 20px 0;
            transition: all 0.3s ease;
        }
        .action-button:hover {
            background: #128c7e;
            transform: translateY(-2px);
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: bold;
            color: #555;
        }
        .detail-value {
            color: #333;
        }
        .total-row {
            background: #667eea;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
            font-weight: bold;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎓 {{ $invoice->title }}</h1>
        <p>Invoice #{{ $invoice->invoice_number }}</p>
    </div>

    <div class="content">
        <p>🎉 Terima kasih telah melakukan pembelian di MersifLab!</p>
        <p>Halo <strong>{{ $invoice->user->name }}</strong>, berikut adalah detail invoice Anda:</p>

        <div class="invoice-details">
            <div class="detail-row">
                <span class="detail-label">🔢 Invoice Number:</span>
                <span class="detail-value">{{ $invoice->invoice_number }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">📅 Tanggal:</span>
                <span class="detail-value">{{ $invoice->created_at->format('d M Y H:i') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">⏰ Jatuh Tempo:</span>
                <span class="detail-value">{{ $invoice->due_date->format('d M Y H:i') }} (1x24 jam)</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">📚 Course:</span>
                <span class="detail-value">{{ $invoice->item_description }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">💰 Subtotal:</span>
                <span class="detail-value">{{ $invoice->formatted_amount }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">🎯 Diskon:</span>
                <span class="detail-value">{{ $invoice->formatted_discount_amount }}</span>
            </div>
            <div class="total-row">
                💳 Total: {{ $invoice->formatted_total_amount }}
            </div>
        </div>

        <div class="payment-section">
            <h3>💳 Metode Pembayaran</h3>
            <h4>📱 QRIS Payment (Instant)</h4>
            <p>Scan QR code di bawah ini untuk pembayaran langsung:</p>
            
            <div class="qris-container">
                <img src="http://localhost/images/qris-payment.jpeg" alt="QRIS Payment" class="qris-image" crossorigin="anonymous">
                <div class="scan-text">📸 SCAN HERE - Sent proof after payment, awkey?! cool</div>
            </div>
        </div>

        <div class="warning">
            ⚠️ <strong>PENTING:</strong> Setelah melakukan pembayaran QRIS, harap segera konfirmasi melalui WhatsApp untuk mempercepat proses aktivasi.<br>
            📸 Jangan lupa kirim bukti pembayaran!
        </div>

        <div style="text-align: center;">
            <a href="{{ $whatsappUrl }}" class="action-button" target="_blank">
                📱 Konfirmasi Pembayaran via WhatsApp
            </a>
        </div>

        @if($invoice->type === 'subscription' && isset($invoice->metadata['plan_features']))
            <div class="invoice-details">
                <h4>🌟 Fitur {{ ucfirst($invoice->metadata['subscription_plan']) }} Plan:</h4>
                @foreach($invoice->metadata['plan_features'] as $feature)
                    <div>✓ {{ $feature }}</div>
                @endforeach
            </div>
        @endif

        <div class="footer">
            <p>🎓 Selamat belajar di MersifLab!</p>
            <p>Jika Anda memiliki pertanyaan, jangan ragu menghubungi kami.</p>
            <p><strong>Regards,<br>Tim MersifLab</strong></p>
        </div>
    </div>
</body>
</html>
