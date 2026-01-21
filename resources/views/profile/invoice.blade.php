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
                    <span class="invoice-code">ML-123456</span>
                </div>
                <span class="invoice-status success">Success</span>
            </div>

            <!-- Divider -->
            <div class="invoice-divider"></div>

            <!-- Transaction Time -->
            <div class="invoice-time">
                <div>
                    <p class="label">Waktu Transaksi</p>
                    <p class="value">2 Mei 2023 pukul 19.41 WIB</p>
                </div>
                <div>
                    <p class="label">Waktu Pembayaran</p>
                    <p class="value">2 Mei 2023 pukul 19.41 WIB</p>
                </div>
            </div>

            <!-- Divider -->
            <div class="invoice-divider"></div>

            <!-- Details -->
            <div class="invoice-details">
                <div class="detail-left">
                    <div class="detail-item">
                        <p class="label">Metode Pembayaran</p>
                        <p class="value">Transfer Bank BRI</p>
                    </div>
                    <div class="detail-item">
                        <p class="label">Total Pembayaran</p>
                        <p class="value bold">Rp100.000</p>
                    </div>
                    <div class="detail-item">
                        <p class="label">Kode Unik</p>
                        <p class="value">Rp987</p>
                    </div>
                </div>

                <div class="detail-right">
                    <div class="detail-item">
                        <p class="label">ID Produk</p>
                        <p class="value mono">zGgyeWtC6KQo2uAWetBR</p>
                    </div>
                    <div class="detail-item">
                        <p class="label">Nama Produk</p>
                        <p class="value">
                            Belajar Desain Grafis untuk Desain Konten Digital
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="invoice-actions">
            <a href="{{ route('purchase-history') }}" class="btn btn-primary">
                Purchase History
            </a>
            <a href="#" class="btn btn-primary">
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