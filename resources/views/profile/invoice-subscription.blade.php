@extends('layouts.app')

@section('title', 'Invoice Subscription')

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
                    <h4 class="invoice-title">Invoice Subscription</h4>
                    <span class="invoice-code">{{ $subscription->purchase_code }}</span>
                </div>
                <span class="invoice-status {{ $subscription->status === 'success' ? 'success' : ($subscription->status === 'pending' ? 'warning' : 'danger') }}">
                    @if($subscription->status === 'success')
                        Success
                    @elseif($subscription->status === 'pending')
                        Pending
                    @elseif($subscription->status === 'expired')
                        Expired
                    @else
                        Cancelled
                    @endif
                </span>
            </div>

            <!-- Divider -->
            <div class="invoice-divider"></div>

            <!-- Transaction Time -->
            <div class="invoice-time">
                <div>
                    <p class="label">Transaction Time</p>
                    <p class="value">{{ $subscription->created_at->format('d M Y') }} at {{ $subscription->created_at->format('H.i') }} WIB</p>
                </div>
                @if($subscription->status === 'pending')
                <div>
                    <p class="label">Payment Due</p>
                    <p class="value">
                        @if(isset($invoice) && $invoice && $invoice->due_date)
                            @if($invoice->due_date->isPast())
                                <span class="text-danger">Expired pada {{ $invoice->due_date->format('d M Y') }} pukul {{ $invoice->due_date->format('H.i') }} WIB</span>
                            @else
                                {{ $invoice->due_date->format('d M Y') }} pukul {{ $invoice->due_date->format('H.i') }} WIB
                                <br>
                                <small class="text-muted">Sisa waktu: {{ $invoice->due_date->diffForHumans() }}</small>
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
                        @if($subscription->paid_at)
                            {{ $subscription->paid_at->format('d M Y') }} pukul {{ $subscription->paid_at->format('H.i') }} WIB
                        @else
                            Not paid yet
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
                        <p class="label">Payment Method</p>
                        <p class="value">{{ $subscription->payment_method ?? 'Not specified' }}</p>
                    </div>
                    <div class="detail-item">
                        <p class="label">Total Payment</p>
                        <p class="value bold">Rp{{ number_format($subscription->final_amount ?? $subscription->amount, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="detail-right">
                    <div class="detail-item">
                        <p class="label">Purchase ID</p>
                        <p class="value mono">{{ $subscription->purchase_code }}</p>
                    </div>
                    <div class="detail-item">
                        <p class="label">Subscription Plan</p>
                        <p class="value">
                            Subscription Package {{ ucfirst($subscription->plan) }}
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
                    @if($subscription->status === 'success')
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
                    @if($subscription->status === 'success')
                        <a href="https://wa.me/{{ config('app.payment.whatsapp_number') }}?text={{ urlencode('Halo MersifLab, saya ingin bertanya tentang pembayaran subscription ' . $subscription->purchase_code . ' yang sudah berhasil sebesar Rp' . number_format($subscription->final_amount ?? $subscription->amount, 0, ',', '.') . '. Terima kasih!') }}" 
                           class="btn btn-success w-100" target="_blank">
                            <i class="fab fa-whatsapp me-2"></i>Contact Admin
                        </a>
                    @else
                        <a href="https://wa.me/{{ config('app.payment.whatsapp_number') }}?text={{ urlencode('Halo MersifLab, saya ingin konfirmasi pembayaran untuk subscription ' . $subscription->purchase_code . ' sebesar Rp' . number_format($subscription->final_amount ?? $subscription->amount, 0, ',', '.')) }}" 
                           class="btn btn-success w-100" target="_blank">
                            <i class="fab fa-whatsapp me-2"></i>Confirm Payment
                        </a>
                    @endif
                </div>
                <div class="alert alert-{{ $subscription->status === 'success' ? 'success' : 'warning' }} mt-3">
                    <i class="fas fa-{{ $subscription->status === 'success' ? 'check-circle' : 'exclamation-triangle' }} me-2"></i>
                    @if($subscription->status === 'success')
                        <strong>PAYMENT SUCCESSFUL:</strong> Thank you for your payment! The subscription is now active and ready to use. If you have any questions, please contact us.
                    @else
                        <strong>IMPORTANT:</strong> After payment, please confirm via WhatsApp for faster activation. Don't forget to send proof of payment!
                    @endif
                </div>
            </div>

            <!-- Subscription Details -->
            <div class="invoice-divider"></div>
            <div class="invoice-items">
                <h5 class="mb-3">Subscription Details</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
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
                                    <small class="text-muted">ID: {{ $subscription->purchase_code }}</small>
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
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">Total Payment:</th>
                                <th class="text-end">Rp{{ number_format($subscription->final_amount ?? $subscription->amount, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            @if($subscription->expires_at)
            <div class="invoice-divider"></div>
            <div class="invoice-time">
                <div>
                    <p class="label">Valid From</p>
                    <p class="value">{{ $subscription->created_at->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="label">Valid Until</p>
                    <p class="value">{{ $subscription->expires_at->format('d M Y') }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="invoice-actions">
            <a href="{{ route('purchase-history') }}" class="btn btn-primary">
                Purchase History
            </a>
            <a href="{{ route('invoice.download', ['id' => $subscription->id, 'type' => 'subscription']) }}" class="btn btn-primary">
                Download Invoice
            </a>
        </div>

        <div class="invoice-start">
            <a href="{{ route('my-courses') }}" class="btn btn-light-primary">
                Start Learning
            </a>
        </div>
    </div>
</div>
@endsection
