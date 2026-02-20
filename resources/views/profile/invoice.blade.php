@extends('layouts.app')

@section('title', 'Invoice')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/invoice.css') }}">
@endsection

@section('content')
<div class="invoice-page">
    <div class="container">
        <div class="invoice-card">

            <!-- Header -->
            <div class="invoice-header">
                <div>
                    <h4 class="invoice-title">Invoice</h4>
                    <span class="invoice-code">
                        @if(isset($invoice))
                            {{ $invoice->invoice_number }}
                        @else
                            {{ $purchase->purchase_code }}
                        @endif
                    </span>
                </div>
                <span class="invoice-status {{ isset($invoice) ? ($invoice->status === 'paid' ? 'success' : ($invoice->status === 'pending' ? 'warning' : 'danger')) : ($purchase->status === 'success' ? 'success' : ($purchase->status === 'pending' ? 'warning' : 'danger')) }}">
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
                </span>
            </div>

            <!-- Divider -->
            <div class="invoice-divider"></div>

            <!-- Transaction Time -->
            <div class="invoice-time">
                <div>
                    <p class="label">Transaction Time</p>
                    <p class="value">
                        @if(isset($invoice))
                            {{ $invoice->created_at->format('d M Y') }} at {{ $invoice->created_at->format('H.i') }} WIB
                        @else
                            {{ $purchase->created_at->format('d M Y') }} at {{ $purchase->created_at->format('H.i') }} WIB
                        @endif
                    </p>
                </div>
                @if((isset($invoice) && $invoice && $invoice->status === 'pending') || (!isset($invoice) && $purchase->status === 'pending'))
                <div>
                    <p class="label">Payment Due</p>
                    <p class="value">
                        @if(isset($invoice) && $invoice && $invoice->due_date)
                            @if($invoice->due_date->isPast())
                                <span class="text-danger">Expired on {{ $invoice->due_date->format('d M Y') }} at {{ $invoice->due_date->format('H.i') }} WIB</span>
                            @else
                                {{ $invoice->due_date->format('d M Y') }} at {{ $invoice->due_date->format('H.i') }} WIB
                                <br>
                                <small class="text-muted">Time left: {{ $invoice->due_date->diffForHumans() }}</small>
                            @endif
                        @else
                            -
                        @endif
                    </p>
                </div>
                @else
                <div>
                    <p class="label">Payment Time</p>
                    <p class="value">
                        @if(isset($invoice))
                            @if($invoice->paid_at)
                                {{ $invoice->paid_at->format('d M Y') }} pukul {{ $invoice->paid_at->format('H.i') }} WIB
                            @else
                                Not paid yet
                            @endif
                        @else
                            @if($purchase->paid_at)
                                {{ $purchase->paid_at->format('d M Y') }} pukul {{ $purchase->paid_at->format('H.i') }} WIB
                            @else
                                Belum dibayar
                            @endif
                        @endif
                    </p>
                </div>
                @endif
            </div>

            <!-- Divider -->
            <div class="invoice-divider"></div>

            <!-- Details -->
            <div class="invoice-details">
                <div class="detail-left">
                    <div class="detail-item">
                        <p class="label">Metode Pembayaran</p>
                        <p class="value">
                            @if(isset($invoice))
                                {{ $invoice->payment_method ?? 'Tidak ditentukan' }}
                            @else
                                {{ $purchase->payment_method ?? 'Not specified' }}
                            @endif
                        </p>
                    </div>
                    <div class="detail-item">
                        <p class="label">Total Payment</p>
                        <p class="value bold">
                            @if(isset($invoice))
                                {{ $invoice->formatted_total_amount }}
                            @else
                                Rp{{ number_format($purchase->amount, 0, ',', '.') }}
                            @endif
                        </p>
                    </div>
                </div>

                <div class="detail-right">
                    <div class="detail-item">
                        <p class="label">Purchase ID</p>
                        <p class="value mono">
                            @if(isset($invoice) && isset($invoice->metadata['purchase_codes']) && count($invoice->metadata['purchase_codes']) > 1)
                                Multiple: {{ implode(', ', array_slice($invoice->metadata['purchase_codes'], 0, 2)) }}{{ count($invoice->metadata['purchase_codes']) > 2 ? '...' : '' }}
                            @else
                                {{ $purchase->purchase_code }}
                            @endif
                        </p>
                    </div>
                    <div class="detail-item">
                        <p class="label">Product Name</p>
                        <p class="value">
                            @if(isset($invoice) && isset($invoice->metadata['items']) && count($invoice->metadata['items']) > 1)
                                Multiple Courses ({{ count($invoice->metadata['items']) }} items)
                            @elseif(isset($invoice) && $invoice->invoiceItems && $invoice->invoiceItems->count() > 1)
                                Multiple Courses ({{ $invoice->invoiceItems->count() }} items)
                            @else
                                {{ $purchase->course->name ?? 'Course not found' }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- QRIS Payment Section (for all invoices) -->
            <div class="invoice-divider"></div>
            <div class="qris-section">
                <h5 class="qris-title">
                    <i class="fas fa-qrcode me-2"></i>QRIS Payment
                </h5>
                <p class="qris-subtitle">
                    @if($purchase->status === 'success')
                        Thank you, your payment has been received
                    @else
                        Scan to pay instantly
                    @endif
                </p>
                <div class="qris-container">
                    @php
                        $qrisPath = 'images/qris-payment.jpeg';
                        $qrisFullPath = public_path($qrisPath);
                        $qrisExists = file_exists($qrisFullPath);
                    @endphp
                    @if($qrisExists)
                        <img src="{{ asset($qrisPath) }}" alt="QRIS Payment" class="qris-image" style="max-width: 300px; height: auto;">
                    @else
                        <div class="qris-placeholder">
                            <i class="fas fa-qrcode fa-3x text-muted"></i>
                            <p class="text-muted mt-2">QRIS not available</p>
                        </div>
                    @endif
                </div>
                <div class="qris-actions">
                    @if($purchase->status === 'success')
                        <a href="https://wa.me/{{ config('app.payment.whatsapp_number') }}?text={{ urlencode('Halo MersifLab, saya ingin bertanya tentang pembayaran invoice ' . $purchase->purchase_code . ' yang sudah berhasil sebesar Rp' . number_format($purchase->amount, 0, ',', '.') . '. Terima kasih!') }}" 
                           class="btn btn-success w-100" target="_blank">
                            <i class="fab fa-whatsapp me-2"></i>Contact Admin
                        </a>
                    @else
                        <a href="https://wa.me/{{ config('app.payment.whatsapp_number') }}?text={{ urlencode('Halo MersifLab, saya ingin konfirmasi pembayaran untuk invoice ' . $purchase->purchase_code . ' sebesar Rp' . number_format($purchase->amount, 0, ',', '.')) }}" 
                           class="btn btn-success w-100" target="_blank">
                            <i class="fab fa-whatsapp me-2"></i>Confirm Payment
                        </a>
                    @endif
                </div>
                <div class="alert alert-{{ $purchase->status === 'success' ? 'success' : 'warning' }} mt-3">
                    <i class="fas fa-{{ $purchase->status === 'success' ? 'check-circle' : 'exclamation-triangle' }} me-2"></i>
                    @if($purchase->status === 'success')
                        <strong>PAYMENT SUCCESSFUL:</strong> Thank you for your payment! The course is now active and accessible. If you have any questions, please contact us.
                    @else
                        <strong>IMPORTANT:</strong> After payment, please confirm via WhatsApp for faster activation. Don't forget to send proof of payment!
                    @endif
                </div>
            </div>

            <!-- Invoice Items -->
            <div class="invoice-divider"></div>
            <div class="invoice-items">
                <h5 class="mb-3">Purchase Details</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Course Name</th>
                                <th>Teacher</th>
                                <th>Category</th>
                                <th class="text-end">Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($invoice) && $invoice->invoiceItems && $invoice->invoiceItems->count() > 0)
                                @foreach($invoice->invoiceItems as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $item->item_name }}</strong>
                                            @if($item->item_description)
                                                <br><small class="text-muted">{{ $item->item_description }}</small>
                                            @endif
                                            @if(isset($item->metadata['purchase_code']))
                                                <br><small class="text-muted">ID: {{ $item->metadata['purchase_code'] }}</small>
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
                                            <strong>{{ $item['name'] ?? $item['title'] ?? 'Course Item' }}</strong>
                                            @if(isset($item['description']))
                                                <br><small class="text-muted">{{ $item['description'] }}</small>
                                            @endif
                                            @if(isset($item['purchase_code']))
                                                <br><small class="text-muted">ID: {{ $item['purchase_code'] }}</small>
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
                                            <strong>{{ $purchase->course->name ?? 'Course not found' }}</strong>
                                        <br>
                                        <small class="text-muted">ID: {{ $purchase->purchase_code }}</small>
                                    </td>
                                    <td>{{ $purchase->course->teacher->name ?? '-' }}</td>
                                    <td>{{ $purchase->course->category ? ucfirst($purchase->course->category) : '-' }}</td>
                                    <td class="text-end">Rp{{ number_format($purchase->amount, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot class="table-light">
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
            </div>
        </div>

        <!-- Actions -->
        <div class="invoice-actions">
            <a href="{{ route('purchase-history') }}" class="btn btn-primary">
                Purchase History
            </a>
            <button onclick="downloadInvoice('{{ $purchase->id }}')" class="btn btn-primary">
                <i class="fas fa-download me-2"></i> Download Invoice
            </button>
        </div>

        <div class="invoice-start">
            <a href="{{ route('my-courses') }}" class="btn btn-light-primary">
                Start Learning
            </a>
        </div>
    </div>
</div>

<script>
function downloadInvoice(purchaseId) {
    $.ajax({
        url: '{{ route("invoice.download", ["id" => ":id", "type" => "course"]) }}'.replace(':id', purchaseId),
        method: 'GET',
        xhrFields: {
            responseType: 'blob'
        },
        beforeSend: function() {
            // Show loading
            Swal.fire({
                title: 'Downloading...',
                html: '<i class="fas fa-spinner fa-spin"></i> Preparing invoice PDF',
                allowOutsideClick: false,
                showConfirmButton: false
            });
        },
        success: function(data, status, xhr) {
            // Check if content-type is JSON (error response)
            if (xhr.getResponseHeader('Content-Type').includes('application/json')) {
                Swal.close();
                // Let the global AJAX error handler handle this
                return;
            }
            
            // Create download link
            const blob = new Blob([data], { type: 'application/pdf' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'Invoice-{{ $purchase->purchase_code }}.pdf';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
            
            Swal.close();
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Invoice downloaded successfully',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        },
        error: function(xhr, status, error) {
            Swal.close();
            // Let the global AJAX error handler handle this
        }
    });
}
</script>
@endsection