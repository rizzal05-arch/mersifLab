<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invoice->title }} - {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .invoice-header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .invoice-header h1 {
            color: #007bff;
            margin: 0;
        }
        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .invoice-details .left, .invoice-details .right {
            width: 48%;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .table .text-right {
            text-align: right;
        }
        .total-section {
            text-align: right;
            margin-top: 20px;
        }
        .total-section table {
            width: 300px;
            margin-left: auto;
        }
        .payment-instructions {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .status-paid {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        @media print {
            body { background: white; }
            .invoice-container { box-shadow: none; border: none; }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <h1>INVOICE</h1>
            <p><strong>{{ $invoice->invoice_number }}</strong></p>
            <p>{{ $invoice->created_at->format('d F Y') }}</p>
        </div>

        <!-- Customer and Invoice Details -->
        <div class="invoice-details">
            <div class="left">
                <h3>Buyer Information</h3>
                <table>
                    <tr>
                        <td><strong>Nama</strong></td>
                        <td>{{ $invoice->user->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Email</strong></td>
                        <td>{{ $invoice->user->email }}</td>
                    </tr>
                    @if($invoice->user->phone)
                    <tr>
                        <td><strong>Telepon</strong></td>
                        <td>{{ $invoice->user->phone }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            <div class="right">
                <h3>Invoice Details</h3>
                <table>
                    <tr>
                        <td><strong>No. Invoice</strong></td>
                        <td>{{ $invoice->invoice_number }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal</strong></td>
                        <td>{{ $invoice->created_at->format('d F Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Jatuh Tempo</strong></td>
                        <td>{{ $invoice->due_date->format('d F Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Status</strong></td>
                            <td>
                            <span class="badge">{{ $invoice->status === 'paid' ? 'Paid' : 'Unpaid' }}</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Invoice Items -->
        <h3>Purchase Details</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Course</th>
                    <th class="text-right">Harga</th>
                    <th class="text-right">Diskon</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @if($invoice->invoiceItems && $invoice->invoiceItems->count() > 0)
                    @foreach($invoice->invoiceItems as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->item_name }}</strong>
                                @if($item->item_description)
                                    <br><small>{{ $item->item_description }}</small>
                                @endif
                                @if($item->course)
                                    <br><small>Course: {{ $item->course->name }}</small>
                                @endif
                            </td>
                            <td class="text-right">{{ $item->formatted_amount }}</td>
                            <td class="text-right">{{ $item->formatted_discount_amount }}</td>
                            <td class="text-right"><strong>{{ $item->formatted_total_amount }}</strong></td>
                        </tr>
                    @endforeach
                @elseif(isset($invoice->metadata['items']) && is_array($invoice->metadata['items']))
                    @foreach($invoice->metadata['items'] as $item)
                        <tr>
                            <td>
                                <strong>{{ $item['name'] ?? $item['title'] ?? 'Course Item' }}</strong>
                                @if(isset($item['description']))
                                    <br><small>{{ $item['description'] }}</small>
                                @endif
                                @if(isset($item['purchase_code']))
                                    <br><small>Kode: {{ $item['purchase_code'] }}</small>
                                @endif
                            </td>
                            <td class="text-right">Rp{{ number_format($item['amount'] ?? $item['price'] ?? 0, 0, ',', '.') }}</td>
                            <td class="text-right">Rp{{ number_format($item['discount_amount'] ?? 0, 0, ',', '.') }}</td>
                            <td class="text-right"><strong>Rp{{ number_format($item['total_amount'] ?? $item['amount'] ?? $item['price'] ?? 0, 0, ',', '.') }}</strong></td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td>
                            <strong>{{ $invoice->item_description }}</strong>
                            @if($invoice->type === 'subscription' && isset($invoice->metadata['plan_features']))
                                <br><small>
                                @foreach($invoice->metadata['plan_features'] as $feature)
                                    • {{ $feature }}<br>
                                @endforeach
                                </small>
                            @endif
                        </td>
                        <td class="text-right">{{ $invoice->formatted_amount }}</td>
                        <td class="text-right">{{ $invoice->formatted_discount_amount }}</td>
                        <td class="text-right"><strong>{{ $invoice->formatted_total_amount }}</strong></td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Total Section -->
        <div class="total-section">
            <table>
                <tr>
                    <td><strong>Subtotal:</strong></td>
                    <td class="text-right">{{ $invoice->formatted_amount }}</td>
                </tr>
                @if($invoice->discount_amount > 0)
                <tr>
                    <td><strong>Discount:</strong></td>
                    <td class="text-right" style="color: green;">-{{ $invoice->formatted_discount_amount }}</td>
                </tr>
                @endif
                @if($invoice->tax_amount > 0)
                <tr>
                    <td><strong>Tax:</strong></td>
                    <td class="text-right">{{ $invoice->formatted_tax_amount }}</td>
                </tr>
                @endif
                <tr style="border-top: 2px solid #007bff;">
                    <td><h4><strong>TOTAL:</strong></h4></td>
                    <td class="text-right"><h4><strong>{{ $invoice->formatted_total_amount }}</strong></h4></td>
                </tr>
            </table>
        </div>

        <!-- Payment Status -->
        @if($invoice->status === 'paid')
            <div class="status-paid">
                <h4>✓ PAYMENT SUCCESSFUL</h4>
                <p>Thank you! Your payment has been received.</p>
                @if($invoice->paid_at)
                    <small>Paid on: {{ $invoice->paid_at->format('d F Y H:i') }}</small>
                @endif
            </div>
        @elseif($invoice->status === 'pending')
            <div class="status-pending">
                <h4>⏰ AWAITING PAYMENT</h4>
                <p>Please make payment before {{ $invoice->due_date->format('d F Y') }}</p>
            </div>
        @endif

        <!-- Payment Instructions -->
        @if($invoice->status === 'pending')
            <div class="payment-instructions">
                <h4><strong>Payment Information:</strong></h4>
                <p>Transfer to the following bank account:</p>
                <table style="width: 100%; margin-bottom: 20px;">
                    <tr>
                        <td><strong>Bank:</strong></td>
                        <td>BCA</td>
                    </tr>
                    <tr>
                        <td><strong>No. Rekening:</strong></td>
                        <td>123-456-7890</td>
                    </tr>
                    <tr>
                        <td><strong>A/n:</strong></td>
                        <td>PT MersifLab Education</td>
                    </tr>
                    <tr>
                        <td><strong>Jumlah:</strong></td>
                        <td><strong>{{ $invoice->formatted_total_amount }}</strong></td>
                    </tr>
                </table>
                <p><strong>Payment Confirmation:</strong></p>
                <p>After making the transfer, please confirm the payment via WhatsApp:</p>
                <p><strong>WhatsApp: 088806658440</strong></p>
                <p><em>IMPORTANT: Keep the transfer proof for verification if needed.</em></p>
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>This invoice is valid and generated by the MersifLab system.</p>
            <p>If you have any questions, contact: support@mersiflab.com</p>
            <p>© {{ date('Y') }} MersifLab. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
