@extends('layouts.app')

@section('title', 'Purchase History - Teacher')

@section('content')
<section class="profile-section py-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                @include('teacher.partials.sidebar')
            </div>
            
            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="profile-content">
                    <div class="profile-header mb-4">
                        <h2 class="profile-title">Purchase History</h2>
                        <p class="profile-subtitle">View your transaction history</p>
                    </div>
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <!-- Purchase Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Invoice ID</th>
                                    <th>Item</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($purchases && $purchases->count() > 0)
                                    @foreach($purchases as $purchase)
                                        <tr>
                                            <td>{{ $purchase->id ?? '#' }}</td>
                                            <td>{{ $purchase->item_name ?? 'N/A' }}</td>
                                            <td>Rp {{ number_format($purchase->amount ?? 0, 0, ',', '.') }}</td>
                                            <td>{{ $purchase->created_at ? $purchase->created_at->format('d M Y') : 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-success">Completed</span>
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-file-invoice me-1"></i>Invoice
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="fas fa-inbox" style="font-size: 2rem; color: #ccc;"></i>
                                            <p class="text-muted mt-2">No purchase history found</p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
