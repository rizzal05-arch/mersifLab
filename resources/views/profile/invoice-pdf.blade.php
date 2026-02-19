<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $purchase->purchase_code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            background: #fff;
        }
        
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 5px;
        }
        
        .invoice-code {
            font-size: 14px;
            color: #666;
        }
        
        .invoice-status {
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .invoice-status.success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .invoice-time {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .time-item {
            flex: 1;
        }
        
        .label {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        
        .value {
            font-size: 13px;
            color: #333;
            font-weight: 500;
        }
        
        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .detail-section {
            flex: 1;
        }
        
        .detail-item {
            margin-bottom: 15px;
        }
        
        .detail-item .value.bold {
            font-weight: bold;
            font-size: 16px;
            color: #1a1a1a;
        }
        
        .invoice-items {
            margin-top: 30px;
        }
        
        .items-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #1a1a1a;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table thead {
            background-color: #f8f9fa;
        }
        
        table th {
            padding: 12px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
            color: #666;
            text-transform: uppercase;
            border-bottom: 2px solid #e0e0e0;
        }
        
        table th.text-end {
            text-align: right;
        }
        
        table td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 12px;
        }
        
        table td.text-end {
            text-align: right;
        }
        
        table tfoot {
            background-color: #f8f9fa;
        }
        
        table tfoot th {
            padding: 15px 12px;
            font-size: 14px;
            font-weight: bold;
            color: #1a1a1a;
        }
        
        .course-name {
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 3px;
        }
        
        .course-id {
            font-size: 10px;
            color: #999;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <div>
                <div class="invoice-title">Invoice</div>
                <div class="invoice-code">
                    @if(isset($invoice))
                        {{ $invoice->invoice_number }}
                    @else
                        {{ $purchase->purchase_code }}
                    @endif
                </div>
            </div>
            <div class="invoice-status @if(isset($invoice)) @if($invoice->status === 'paid') success @elseif($invoice->status === 'pending') warning @else danger @endif @else @if($purchase->status === 'success') success @elseif($purchase->status === 'pending') warning @else danger @endif @endif">
                @if(isset($invoice))
                    @if($invoice->status === 'paid')
                        Success
                    @elseif($invoice->status === 'pending')
                        Pending
                    @elseif($invoice->status === 'expired')
                        Expired
                    @else
                        Cancelled
                    @endif
                @else
                    @if($purchase->status === 'success')
                        Success
                    @elseif($purchase->status === 'pending')
                        Pending
                    @elseif($purchase->status === 'expired')
                        Expired
                    @else
                        Cancelled
                    @endif
                @endif
            </div>
        </div>

        <!-- Transaction Details -->
        <div class="transaction-details">
            <div class="detail-item">
                <div class="detail-label">Transaction Date</div>
                <div class="detail-value">
                    @if(isset($invoice))
                        {{ $invoice->created_at->format('M d, Y') }} {{ $invoice->created_at->format('H:i') }} WIB
                    @else
                        {{ $purchase->created_at->format('M d, Y') }} {{ $purchase->created_at->format('H:i') }} WIB
                    @endif
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Payment Date</div>
                <div class="detail-value">
                    @if(isset($invoice))
                        @if($invoice->paid_at)
                            {{ $invoice->paid_at->format('M d, Y') }} {{ $invoice->paid_at->format('H:i') }} WIB
                        @else
                            Not paid yet
                        @endif
                    @else
                        @if($purchase->paid_at)
                            {{ $purchase->paid_at->format('M d, Y') }} {{ $purchase->paid_at->format('H:i') }} WIB
                        @else
                            Not paid yet
                        @endif
                    @endif
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Due Date</div>
                <div class="detail-value">
                    @if(isset($invoice) && $invoice->due_date)
                        @if($invoice->due_date->isPast())
                            <span style="color: #dc3545;">Expired on {{ $invoice->due_date->format('M d, Y') }} {{ $invoice->due_date->format('H:i') }} WIB</span>
                        @else
                            {{ $invoice->due_date->format('M d, Y') }} {{ $invoice->due_date->format('H:i') }} WIB
                        @endif
                    @else
                        -
                    @endif
                </div>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="payment-info">
            <div class="info-left">
                <div class="info-item">
                    <div class="info-label">Payment Method</div>
                    <div class="info-value">
                        @if(isset($invoice))
                            {{ $invoice->payment_method ?? 'Not specified' }}
                        @else
                            {{ $purchase->payment_method ?? 'Not specified' }}
                        @endif
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Total Payment</div>
                    <div class="info-value">
                        @if(isset($invoice))
                            {{ $invoice->formatted_total_amount }}
                        @else
                            Rp{{ number_format($purchase->amount, 0, ',', '.') }}
                        @endif
                    </div>
                </div>
            </div>
            <div class="info-right">
                <div class="info-item">
                    <div class="info-label">Purchase ID</div>
                    <div class="info-value">
                        @if(isset($invoice) && isset($invoice->metadata['purchase_codes']) && count($invoice->metadata['purchase_codes']) > 1)
                            Multiple: {{ implode(', ', array_slice($invoice->metadata['purchase_codes'], 0, 2)) }}{{ count($invoice->metadata['purchase_codes']) > 2 ? '...' : '' }}
                        @else
                            {{ $purchase->purchase_code }}
                        @endif
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Product Name</div>
                    <div class="info-value">
                        @if(isset($invoice) && isset($invoice->metadata['items']) && count($invoice->metadata['items']) > 1)
                            Multiple Courses ({{ count($invoice->metadata['items']) }} items)
                        @elseif(isset($invoice) && $invoice->invoiceItems && $invoice->invoiceItems->count() > 1)
                            Multiple Courses ({{ $invoice->invoiceItems->count() }} items)
                        @else
                            {{ $purchase->course->name ?? 'Course not found' }}
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- QRIS Payment Section (for all invoices) -->
        <div class="qris-section">
            <div class="qris-title">QRIS Payment</div>
            <div class="qris-subtitle">
                @if($purchase->status === 'success')
                    Terima kasih, pembayaran Anda telah diterima
                @else
                    Scan untuk pembayaran instant
                @endif
            </div>
            <div class="qris-container">
                @if(file_exists(public_path(config('app.payment.qris_image_path'))))
                    <img src="{{ asset(config('app.payment.qris_image_path')) }}" alt="QRIS Payment" class="qris-image">
                @else
                    <div style="background: #f8f9fa; border: 1px dashed #dee2e6; padding: 40px; text-align: center; min-height: 180px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                        <p style="margin: 0; color: #6c757d; font-size: 13px;">QRIS image not available</p>
                    </div>
                @endif
            </div>
            <div class="qris-scan-text">
                @if($purchase->status === 'success')
                    HUBUNGI ADMIN - Terima kasih atas pembayaran Anda
                @else
                    SCAN HERE - Konfirmasi setelah pembayaran
                @endif
            </div>
        </div>

        <!-- Warning/Success Box -->
        <div class="alert-box alert-{{ $purchase->status === 'success' ? 'success' : 'warning' }}">
            <p>
                @if($purchase->status === 'success')
                    <strong>PEMBAYARAN BERHASIL:</strong> Terima kasih atas pembayaran Anda! Course sudah aktif dan dapat diakses. Jika ada pertanyaan, jangan ragu menghubungi kami.
                @else
                    <strong>PENTING:</strong> Setelah pembayaran, segera konfirmasi via WhatsApp untuk aktivasi cepat. Jangan lupa kirim bukti pembayaran!
                @endif
            </p>
        </div>

        <!-- WhatsApp Button -->
        <div style="text-align: center;">
            @if($purchase->status === 'success')
                <a href="https://wa.me/{{ config('app.payment.whatsapp_number') }}?text={{ urlencode('Halo MersifLab, saya ingin bertanya tentang pembayaran invoice ' . $purchase->purchase_code . ' yang sudah berhasil sebesar Rp' . number_format($purchase->amount, 0, ',', '.') . '. Terima kasih!') }}" 
                   class="whatsapp-button" target="_blank">
                    Hubungi Admin
                </a>
            @else
                <a href="https://wa.me/{{ config('app.payment.whatsapp_number') }}?text={{ urlencode('Halo MersifLab, saya ingin konfirmasi pembayaran untuk invoice ' . $purchase->purchase_code . ' sebesar Rp' . number_format($purchase->amount, 0, ',', '.')) }}" 
                   class="whatsapp-button" target="_blank">
                    Konfirmasi via WhatsApp
                </a>
            @endif
        </div>

        <!-- Purchase Details -->
        <div class="invoice-items">
            <div class="items-title">Purchase Details</div>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Course Name</th>
                        <th>Teacher</th>
                        <th>Category</th>
                        <th class="text-end">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($invoice) && $invoice->invoiceItems && $invoice->invoiceItems->count() > 0)
                        @foreach($invoice->invoiceItems as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="course-name">{{ $item->item_name }}</div>
                                    @if($item->item_description)
                                        <div class="course-id">{{ $item->item_description }}</div>
                                    @endif
                                    @if(isset($item->metadata['purchase_code']))
                                        <div class="course-id">ID: {{ $item->metadata['purchase_code'] }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if($item->course)
                                        {{ $item->course->teacher->name ?? '-' }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($item->course)
                                        {{ $item->course->category ? ucfirst($item->course->category) : '-' }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-end">{{ $item->formatted_amount }}</td>
                            </tr>
                        @endforeach
                    @elseif(isset($invoice) && isset($invoice->metadata['items']) && is_array($invoice->metadata['items']))
                        @foreach($invoice->metadata['items'] as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="course-name">{{ $item['name'] ?? $item['title'] ?? 'Course Item' }}</div>
                                    @if(isset($item['description']))
                                        <div class="course-id">{{ $item['description'] }}</div>
                                    @endif
                                    @if(isset($item['purchase_code']))
                                        <div class="course-id">ID: {{ $item['purchase_code'] }}</div>
                                    @endif
                                </td>
                                <td>-</td>
                                <td>-</td>
                                <td class="text-end">Rp{{ number_format($item['amount'] ?? $item['price'] ?? 0, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @else
                        <!-- Single purchase fallback -->
                        <tr>
                            <td>1</td>
                            <td>
                                <div class="course-name">{{ $purchase->course->name ?? 'Course not found' }}</div>
                                <div class="course-id">ID: {{ $purchase->purchase_code }}</div>
                            </td>
                            <td>{{ $purchase->course->teacher->name ?? '-' }}</td>
                            <td>{{ $purchase->course->category ? ucfirst($purchase->course->category) : '-' }}</td>
                            <td class="text-end">Rp{{ number_format($purchase->amount, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-end">Total Payment:</th>
                        <th class="text-end">
                            @if(isset($invoice))
                                {{ $invoice->formatted_total_amount }}
                            @else
                                Rp{{ number_format($purchase->amount, 0, ',', '.') }}
                            @endif
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This invoice has been automatically generated by the MersifLab system</p>
            <p>Terima kasih atas pembelian Anda!</p>
        </div>
    </div>
</body>
</html>
