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
                    <span class="invoice-code">{{ $purchase->purchase_code }}</span>
                </div>
                <span class="invoice-status {{ $purchase->status === 'success' ? 'success' : ($purchase->status === 'pending' ? 'warning' : 'danger') }}">
                    @if($purchase->status === 'success')
                        Success
                    @elseif($purchase->status === 'pending')
                        Pending
                    @elseif($purchase->status === 'expired')
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
                    <p class="label">Waktu Transaksi</p>
                    <p class="value">{{ $purchase->created_at->format('d M Y') }} pukul {{ $purchase->created_at->format('H.i') }} WIB</p>
                </div>
                <div>
                    <p class="label">Waktu Pembayaran</p>
                    <p class="value">
                        @if($purchase->paid_at)
                            {{ $purchase->paid_at->format('d M Y') }} pukul {{ $purchase->paid_at->format('H.i') }} WIB
                        @else
                            Belum dibayar
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
                        <p class="label">Metode Pembayaran</p>
                        <p class="value">{{ $purchase->payment_method ?? 'Tidak ditentukan' }}</p>
                    </div>
                    <div class="detail-item">
                        <p class="label">Total Pembayaran</p>
                        <p class="value bold">Rp{{ number_format($purchase->amount, 0, ',', '.') }}</p>
                    </div>
                    @if($purchase->payment_provider)
                    <div class="detail-item">
                        <p class="label">Payment Provider</p>
                        <p class="value">{{ $purchase->payment_provider }}</p>
                    </div>
                    @endif
                </div>

                <div class="detail-right">
                    <div class="detail-item">
                        <p class="label">Purchase ID</p>
                        <p class="value mono">{{ $purchase->purchase_code }}</p>
                    </div>
                    <div class="detail-item">
                        <p class="label">Product Name</p>
                        <p class="value">
                            {{ $purchase->course->name ?? 'Course tidak ditemukan' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Single Purchase Details -->
            <div class="invoice-divider"></div>
            <div class="invoice-items">
                <h5 class="mb-3">Detail Pembelian</h5>
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
                            <tr>
                                <td>1</td>
                                <td>
                                    <strong>{{ $purchase->course->name ?? 'Course tidak ditemukan' }}</strong>
                                    <br>
                                    <small class="text-muted">ID: {{ $purchase->purchase_code }}</small>
                                </td>
                                <td>{{ $purchase->course->teacher->name ?? '-' }}</td>
                                <td>{{ $purchase->course->category ? ucfirst($purchase->course->category) : '-' }}</td>
                                <td class="text-end">Rp{{ number_format($purchase->amount, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="4" class="text-end">Total Pembayaran:</th>
                                <th class="text-end">Rp{{ number_format($purchase->amount, 0, ',', '.') }}</th>
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
            <a href="{{ route('invoice.download', $purchase->id) }}" class="btn btn-primary">
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