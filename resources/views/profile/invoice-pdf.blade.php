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
                <div class="invoice-code">{{ $purchase->purchase_code }}</div>
            </div>
            <div class="invoice-status success">
                @if($purchase->status === 'success')
                    Success
                @elseif($purchase->status === 'pending')
                    Pending
                @elseif($purchase->status === 'expired')
                    Expired
                @else
                    Cancelled
                @endif
            </div>
        </div>

        <!-- Transaction Time -->
        <div class="invoice-time">
            <div class="time-item">
                <div class="label">Waktu Transaksi</div>
                <div class="value">{{ $purchase->created_at->format('d M Y') }} pukul {{ $purchase->created_at->format('H.i') }} WIB</div>
            </div>
            <div class="time-item">
                <div class="label">Payment Time</div>
                <div class="value">
                    @if($purchase->paid_at)
                        {{ $purchase->paid_at->format('d M Y') }} at {{ $purchase->paid_at->format('H.i') }} WIB
                    @else
                        Not yet paid
                    @endif
                </div>
            </div>
        </div>

        <!-- Details -->
        <div class="invoice-details">
            <div class="detail-section">
                <div class="detail-item">
                    <div class="label">Payment Method</div>
                    <div class="value">{{ $purchase->payment_method ?? 'Not specified' }}</div>
                </div>
                <div class="detail-item">
                    <div class="label">Total Payment</div>
                    <div class="value bold">Rp{{ number_format($purchase->amount, 0, ',', '.') }}</div>
                </div>
                @if($purchase->payment_provider)
                <div class="detail-item">
                    <div class="label">Payment Provider</div>
                    <div class="value">{{ $purchase->payment_provider }}</div>
                </div>
                @endif
            </div>

            <div class="detail-section">
                <div class="detail-item">
                    <div class="label">Purchase ID</div>
                    <div class="value">{{ $purchase->purchase_code }}</div>
                </div>
                <div class="detail-item">
                    <div class="label">Product Name</div>
                    <div class="value">{{ $purchase->course->name ?? 'Course not found' }}</div>
                </div>
            </div>
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
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-end">Total Payment:</th>
                        <th class="text-end">Rp{{ number_format($purchase->amount, 0, ',', '.') }}</th>
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
