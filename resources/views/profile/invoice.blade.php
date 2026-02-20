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
                <span class="invoice-status {{ isset($invoice) ? ($invoice->status === 'paid' ? 'success' : ($invoice->status === 'pending' ? 'warning' : 'danger')) : (isset($purchase) ? ($purchase->status === 'success' ? 'success' : ($purchase->status === 'pending' ? 'warning' : 'danger')) : 'danger') }}">
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
                    @elseif(isset($purchase))
                        @if($purchase->status === 'success')
                            Success
                        @elseif($purchase->status === 'pending')
                            Pending
                        @elseif($purchase->status === 'expired')
                            Expired
                        @else
                            Cancelled
                        @endif
                    @else
                        Unknown
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
                        @elseif(isset($purchase))
                            {{ $purchase->created_at->format('d M Y') }} at {{ $purchase->created_at->format('H.i') }} WIB
                        @else
                            -
                        @endif
                    </p>
                </div>
                <div>
                    <p class="label">Payment Time</p>
                    <p class="value">
                        @if(isset($invoice))
                            @if($invoice->paid_at)
                                {{ $invoice->paid_at->format('d M Y') }} at {{ $invoice->paid_at->format('H.i') }} WIB
                            @else
                                Not paid yet
                            @endif
                        @elseif(isset($purchase))
                            @if($purchase->paid_at)
                                {{ $purchase->paid_at->format('d M Y') }} at {{ $purchase->paid_at->format('H.i') }} WIB
                            @else
                                Not paid yet
                            @endif
                        @else
                            -
                        @endif
                    </p>
                </div>
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
            </div>

            <!-- Divider -->
            <div class="invoice-divider"></div>

            <!-- Details -->
            <div class="invoice-details">
                <div class="detail-left">
                    <div class="detail-item">
                        <p class="label">Payment Method</p>
                        <p class="value">
                            @if(isset($invoice))
                                {{ $invoice->payment_method ?? 'Not specified' }}
                            @elseif(isset($purchase))
                                {{ $purchase->payment_method ?? 'Not specified' }}
                            @else
                                Not specified
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
                            @elseif(isset($invoice) && $invoice->invoiceItems && $invoice->invoiceItems->count() > 1)
                                Multiple: {{ implode(', ', $invoice->invoiceItems->pluck('metadata.purchase_code')->take(2)->toArray()) }}{{ $invoice->invoiceItems->count() > 2 ? '...' : '' }}
                            @elseif(isset($purchase))
                                {{ $purchase->purchase_code }}
                            @elseif(isset($invoice) && $invoice->invoiceItems && $invoice->invoiceItems->count() === 1)
                                {{ $invoice->invoiceItems->first()->metadata['purchase_code'] ?? $invoice->invoice_number }}
                            @else
                                {{ $invoice->invoice_number ?? '-' }}
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
                            @elseif(isset($purchase))
                                {{ $purchase->course->name ?? 'Course not found' }}
                            @elseif(isset($invoice) && $invoice->invoiceItems && $invoice->invoiceItems->count() === 1)
                                {{ $invoice->invoiceItems->first()->item_name ?? 'Course not found' }}
                            @else
                                Course not found
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
                    @if(isset($invoice) && $invoice->status === 'paid')
                        Thank you, your payment has been received
                    @else
                        Scan to pay instantly
                    @endif
                </p>
                <div class="qris-container">
                    @if(file_exists(public_path(config('app.payment.qris_image_path'))))
                        <img src="{{ asset(config('app.payment.qris_image_path')) }}" alt="QRIS Payment" class="qris-image">
                    @else
                        <div class="qris-placeholder">
                            <i class="fas fa-qrcode fa-3x text-muted"></i>
                            <p class="text-muted mt-2">QRIS not available</p>
                        </div>
                    @endif
                </div>
                <div class="qris-actions">
                    @if(isset($invoice) && $invoice->status === 'paid')
                        <a href="https://wa.me/{{ config('app.payment.whatsapp_number') }}?text={{ urlencode('Halo MersifLab, saya ingin bertanya tentang pembayaran invoice ' . $invoice->invoice_number . ' yang sudah berhasil sebesar ' . $invoice->formatted_total_amount . '. Terima kasih!') }}" 
                           class="btn btn-success w-100" target="_blank">
                            <i class="fab fa-whatsapp me-2"></i>Contact Admin
                        </a>
                    @else
                        <a href="https://wa.me/{{ config('app.payment.whatsapp_number') }}?text={{ urlencode('Halo MersifLab, saya ingin konfirmasi pembayaran untuk invoice ' . (isset($invoice) ? $invoice->invoice_number : $purchase->purchase_code) . ' sebesar ' . (isset($invoice) ? $invoice->formatted_total_amount : 'Rp' . number_format($purchase->amount, 0, ',', '.'))) }}" 
                           class="btn btn-success w-100" target="_blank">
                            <i class="fab fa-whatsapp me-2"></i>Confirm Payment
                        </a>
                    @endif
                </div>
                <div class="alert alert-{{ isset($invoice) && $invoice->status === 'paid' ? 'success' : 'warning' }} mt-3">
                    <i class="fas fa-{{ isset($invoice) && $invoice->status === 'paid' ? 'check-circle' : 'exclamation-triangle' }} me-2"></i>
                    @if(isset($invoice) && $invoice->status === 'paid')
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
                                <th class="text-end">Price</th>
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
                                        <strong>{{ isset($purchase) ? ($purchase->course->name ?? 'Course not found') : ($invoice->invoiceItems->first()->item_name ?? 'Course not found') }}</strong>
                                        <br>
                                        <small class="text-muted">ID: {{ isset($purchase) ? $purchase->purchase_code : ($invoice->invoiceItems->first()->metadata['purchase_code'] ?? $invoice->invoice_number) }}</small>
                                    </td>
                                    <td>{{ isset($purchase) ? ($purchase->course->teacher->name ?? '-') : ($invoice->invoiceItems->first()->course->teacher->name ?? '-') }}</td>
                                    <td>{{ isset($purchase) ? ($purchase->course->category ? ucfirst($purchase->course->category) : '-') : ($invoice->invoiceItems->first()->course->category ? ucfirst($invoice->invoiceItems->first()->course->category) : '-') }}</td>
                                    <td class="text-end">{{ isset($purchase) ? ('Rp' . number_format($purchase->amount, 0, ',', '.')) : $invoice->invoiceItems->first()->formatted_amount }}</td>
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
            <button onclick="downloadInvoice('{{ isset($invoice) ? $invoice->id : $purchase->id }}')" class="btn btn-primary">
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
        url: '{{ route("invoice.download", ":id") }}'.replace(':id', purchaseId),
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
            a.download = 'Invoice-{{ isset($invoice) ? $invoice->invoice_number : $purchase->purchase_code }}.pdf';
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