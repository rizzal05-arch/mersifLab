<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $invoice->title }} - {{ $invoice->invoice_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 450px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .receipt-wrapper {
            background-color: #ffffff;
            border: 2px solid #1A76D1;
            border-radius: 12px;
            padding: 0;
            margin: 0 auto;
            box-shadow: 0 4px 20px rgba(26, 118, 209, 0.15);
            overflow: hidden;
        }
        .receipt-header {
            text-align: center;
            background: linear-gradient(135deg, #1A76D1 0%, #4A9EE0 100%);
            color: white;
            padding: 25px 20px;
            border-bottom: 3px solid #1565c0;
        }
        .receipt-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            color: white;
            letter-spacing: 1px;
        }
        .receipt-header .subtitle {
            margin: 8px 0 0 0;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.95);
            text-transform: uppercase;
            font-weight: 500;
            letter-spacing: 0.5px;
        }
        .receipt-content {
            padding: 25px 20px;
        }
        .receipt-line {
            border-top: 2px dashed #1A76D1;
            margin: 20px 0;
            opacity: 0.3;
        }
        .receipt-section {
            margin: 20px 0;
        }
        .receipt-row {
            display: flex;
            align-items: center;
            padding: 12px 0;
            font-size: 16px;
            line-height: 1.8;
        }
        .receipt-label {
            font-weight: 600;
            color: #333;
            width: 140px;
            min-width: 140px;
            display: inline-block;
        }
        .receipt-value {
            color: #1A76D1;
            font-weight: 600;
            flex: 1;
            text-align: left;
        }
        .section-title {
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #1A76D1;
            color: #1A76D1;
            letter-spacing: 0.5px;
        }
        .item-row {
            display: flex;
            align-items: center;
            padding: 12px 0;
            font-size: 16px;
            border-bottom: 1px dotted #e0e0e0;
            line-height: 1.8;
        }
        .item-row:last-child {
            border-bottom: none;
        }
        .item-name {
            width: 140px;
            min-width: 140px;
            color: #333;
            font-weight: 500;
        }
        .item-price {
            flex: 1;
            font-weight: 600;
            color: #1A76D1;
        }
        .summary-section {
            margin-top: 20px;
            padding-top: 15px;
        }
        .summary-row {
            display: flex;
            align-items: center;
            padding: 12px 0;
            font-size: 16px;
            line-height: 1.8;
        }
        .summary-row span:first-child {
            color: #666;
            font-weight: 500;
            width: 120px;
            min-width: 120px;
        }
        .summary-row span:last-child {
            color: #333;
            font-weight: 600;
            flex: 1;
        }
        .total-row {
            border: 2px solid #1A76D1;
            padding: 15px;
            margin-top: 15px;
            background: linear-gradient(135deg, rgba(26, 118, 209, 0.1) 0%, rgba(74, 158, 224, 0.1) 100%);
            border-radius: 8px;
        }
        .total-row span:first-child {
            color: #1A76D1;
            font-weight: 700;
            font-size: 18px;
            width: 120px;
            min-width: 120px;
        }
        .total-row span:last-child {
            color: #1A76D1;
            font-weight: 700;
            font-size: 18px;
            flex: 1;
        }
        .qris-section {
            background: linear-gradient(135deg, #1A76D1 0%, #4A9EE0 100%);
            border: 2px solid #1565c0;
            border-radius: 12px;
            padding: 25px 20px;
            margin: 25px 0;
            text-align: center;
            box-shadow: 0 4px 15px rgba(26, 118, 209, 0.2);
        }
        .qris-title {
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 8px 0;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .qris-subtitle {
            font-size: 13px;
            margin: 0 0 20px 0;
            color: rgba(255, 255, 255, 0.95);
            font-weight: 500;
        }
        .qris-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin: 0 auto 20px;
            display: inline-block;
            max-width: 280px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .qris-image {
            max-width: 240px;
            width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        .qris-scan-text {
            font-size: 13px;
            margin-top: 15px;
            font-weight: 600;
            color: white;
            letter-spacing: 0.5px;
        }
        .warning-box {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 10px;
            padding: 18px;
            margin: 25px 0;
            font-size: 13px;
        }
        .warning-box p {
            margin: 0;
            color: #856404;
            line-height: 1.7;
            font-weight: 500;
        }
        .warning-box strong {
            color: #856404;
            font-weight: 700;
        }
        .whatsapp-button {
            display: block;
            background: #25d366;
            color: white;
            padding: 16px 32px;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 16px;
            margin: 20px auto;
            text-align: center;
            width: fit-content;
            border: 2px solid #128c7e;
            box-shadow: 0 4px 15px rgb(215, 245, 226);
            transition: all 0.3s ease;
        }
        .whatsapp-button:hover {
            background: #128c7e;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgb(233, 246, 237);
        }
        .receipt-footer {
            text-align: center;
            padding: 20px;
            border-top: 2px dashed #1A76D1;
            font-size: 13px;
            color: #666;
            background: #f8f9fa;
        }
        .receipt-footer p {
            margin: 8px 0;
            font-weight: 500;
        }
        .receipt-footer strong {
            color: #1A76D1;
            font-weight: 700;
        }
        .cut-line {
            text-align: center;
            margin: 20px 0;
            color: #999;
            font-size: 11px;
        }
        .cut-line::before,
        .cut-line::after {
            content: "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━";
            display: block;
            letter-spacing: 1px;
            opacity: 0.5;
        }
        .footer-brand {
            font-size: 11px;
            color: #999;
            margin-top: 15px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="receipt-wrapper">
        <!-- Header -->
        <div class="receipt-header">
            <h1>MERSIFLAB</h1>
            <p class="subtitle">Digital Payment Receipt</p>
        </div>

        <!-- Content -->
        <div class="receipt-content">
            <!-- Invoice Information -->
            <div class="receipt-section">
                <div class="receipt-row">
                    <span class="receipt-label">No. Invoice</span>
                    <span class="receipt-value">{{ $invoice->invoice_number }}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Tanggal</span>
                    <span class="receipt-value">{{ $invoice->created_at->format('d M Y') }}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Waktu</span>
                    <span class="receipt-value">{{ $invoice->created_at->format('H:i') }} WIB</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Jatuh Tempo</span>
                    <span class="receipt-value">{{ $invoice->due_date->format('d M H:i') }}</span>
                </div>
            </div>

            <div class="receipt-line"></div>

            <!-- Purchase Details -->
            <div class="receipt-section">
                <div class="section-title">Detail Pembelian</div>
                @php
                    $itemLabel = $invoice->type === 'subscription' ? 'Subscription' : 'Nama Course';
                @endphp
                @if(isset($items) && count($items) > 0)
                    @foreach($items as $index => $item)
                        @if($index > 0)
                            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px dotted #e0e0e0;"></div>
                        @endif
                        @php
                            $itemName = $item['name'] ?? $item['title'] ?? 'Item';
                            // Remove "Subscription:" prefix if type is subscription
                            if ($invoice->type === 'subscription' && strpos($itemName, 'Subscription:') === 0) {
                                $itemName = trim(str_replace('Subscription:', '', $itemName));
                            }
                        @endphp
                        <div class="item-row">
                            <span class="item-name">{{ $itemLabel }}</span>
                            <span class="item-price">{{ $itemName }}</span>
                        </div>
                        <div class="item-row">
                            <span class="item-name">Harga</span>
                            <span class="item-price">Rp {{ number_format($item['price'] ?? $item['amount'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                @else
                    @php
                        $itemDescription = $invoice->item_description;
                        // Remove "Subscription:" prefix if type is subscription
                        if ($invoice->type === 'subscription' && strpos($itemDescription, 'Subscription:') === 0) {
                            $itemDescription = trim(str_replace('Subscription:', '', $itemDescription));
                        }
                    @endphp
                    <div class="item-row">
                        <span class="item-name">{{ $itemLabel }}</span>
                        <span class="item-price">{{ $itemDescription }}</span>
                    </div>
                    <div class="item-row">
                        <span class="item-name">Harga</span>
                        <span class="item-price">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</span>
                    </div>
                @endif
            </div>

            <div class="receipt-line"></div>

            <!-- Summary -->
            <div class="summary-section">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>{{ $invoice->formatted_amount }}</span>
                </div>
                @if($invoice->discount_amount > 0)
                    <div class="summary-row">
                        <span>Diskon</span>
                        <span>-{{ $invoice->formatted_discount_amount }}</span>
                    </div>
                @endif
                <div class="summary-row total-row">
                    <span>TOTAL</span>
                    <span>{{ $invoice->formatted_total_amount }}</span>
                </div>
            </div>

            <div class="receipt-line"></div>

            <!-- QRIS Payment Section -->
            <div class="qris-section">
                <div class="qris-title">QRIS Payment</div>
                <div class="qris-subtitle">Scan untuk pembayaran instant</div>
                <div class="qris-container">
                    @if(isset($qrisImagePath) && $qrisImagePath && file_exists($qrisImagePath))
                        @if(isset($message))
                            <img src="{{ $message->embed($qrisImagePath) }}" alt="QRIS Payment" class="qris-image">
                        @elseif(isset($qrisImageBase64) && !empty($qrisImageBase64))
                            <img src="{{ $qrisImageBase64 }}" alt="QRIS Payment" class="qris-image">
                        @else
                            <div style="background: #f8f9fa; border: 1px dashed #dee2e6; padding: 40px; text-align: center; min-height: 180px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                <p style="margin: 0; color: #6c757d; font-size: 13px;">QRIS image not available</p>
                            </div>
                        @endif
                    @elseif(isset($qrisImageBase64) && !empty($qrisImageBase64))
                        <img src="{{ $qrisImageBase64 }}" alt="QRIS Payment" class="qris-image">
                    @else
                        <div style="background: #f8f9fa; border: 1px dashed #dee2e6; padding: 40px; text-align: center; min-height: 180px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                            <p style="margin: 0; color: #6c757d; font-size: 13px;">QRIS image not available</p>
                        </div>
                    @endif
                </div>
                <div class="qris-scan-text">SCAN HERE - Konfirmasi setelah pembayaran</div>
            </div>

            <!-- Warning Box -->
            <div class="warning-box">
                <p>
                    <strong>PENTING:</strong> Setelah pembayaran, segera konfirmasi via WhatsApp untuk aktivasi cepat. Jangan lupa kirim bukti pembayaran!
                </p>
            </div>

            <!-- WhatsApp Button -->
            <div style="text-align: center;">
                <a href="{{ $whatsappUrl }}" class="whatsapp-button" target="_blank">
                    Konfirmasi via WhatsApp
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="receipt-footer">
            <p><strong>Terima kasih telah berbelanja di MersifLab!</strong></p>
            <p>Selamat belajar!</p>
            <div class="cut-line"></div>
            <p class="footer-brand">MERSIFLAB INVOICE SYSTEM</p>
        </div>
    </div>
</body>
</html>
