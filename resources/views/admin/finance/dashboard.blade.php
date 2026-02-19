@extends('layouts.admin')

@section('title', 'Financial Dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4 page-title">
                <i class="fas fa-chart-line me-2"></i>Financial Dashboard
            </h2>
        </div>
    </div>

    <!-- Financial Overview Cards -->
    <div class="row mb-4 fade-in-up">
        <div class="col-md-3 mb-3">
            <div class="card finance-overview-card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title">Total Revenue</h5>
                            <h3 class="mb-0">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card finance-overview-card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title">Teacher Payouts</h5>
                            <h3 class="mb-0">Rp {{ number_format($totalTeacherPayouts, 0, ',', '.') }}</h3>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-hand-holding-usd fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card finance-overview-card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title">Platform Commission</h5>
                            <h3 class="mb-0">Rp {{ number_format($platformCommission, 0, ',', '.') }}</h3>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-percentage fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card finance-overview-card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title">Pending Withdrawals</h5>
                            <h3 class="mb-0">{{ $pendingWithdrawals }}</h3>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Teacher Statistics -->
        <div class="col-lg-8">
            <div class="card fade-in-up">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-chalkboard-teacher me-2"></i>Teacher Statistics
                    </h5>
                    <button class="btn btn-light btn-sm" onclick="exportReport('summary')">
                        <i class="fas fa-download me-1"></i>Export
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Teacher</th>
                                    <th>Total Sales</th>
                                    <th>Total Earnings</th>
                                    <th>Current Balance</th>
                                    <th>Total Withdrawn</th>
                                    <th>Pending Earnings</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($teacherStats as $teacher)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                {{ strtoupper(substr($teacher['name'], 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $teacher['name'] }}</div>
                                                <small class="text-muted">{{ $teacher['email'] }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Rp {{ number_format($teacher['total_sales'], 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($teacher['total_earnings'], 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $teacher['current_balance'] > 0 ? 'success' : 'secondary' }}">
                                            Rp {{ number_format($teacher['current_balance'], 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>Rp {{ number_format($teacher['total_withdrawn'], 0, ',', '.') }}</td>
                                    <td>
                                        @if($teacher['pending_earnings'] > 0)
                                            <span class="badge bg-warning">
                                                Rp {{ number_format($teacher['pending_earnings'], 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.finance.teacher', $teacher['id']) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($teacher['pending_earnings'] > 0)
                                                <button class="btn btn-outline-success btn-sm" onclick="approveEarnings({{ $teacher['id'] }}, {{ $teacher['pending_earnings'] }})">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-lg-4">
            <!-- Recent Transactions -->
            <div class="card mb-4 fade-in-up">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-shopping-cart me-2"></i>Recent Transactions
                    </h6>
                </div>
                <div class="card-body">
                    @if($recentTransactions->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentTransactions as $transaction)
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold">{{ $transaction->user->name }}</div>
                                        <small class="text-muted">{{ $transaction->course->name }}</small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-success">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</div>
                                        <small class="text-muted">{{ $transaction->created_at->format('d M') }}</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center">No recent transactions</p>
                    @endif
                </div>
            </div>

            <!-- Recent Withdrawals -->
            <div class="card fade-in-up">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-money-bill-wave me-2"></i>Recent Withdrawals
                    </h6>
                </div>
                <div class="card-body">
                    @if($recentWithdrawals->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentWithdrawals as $withdrawal)
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold">{{ $withdrawal->teacher->name }}</div>
                                        <small class="text-muted">{{ $withdrawal->bank_name }}</small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</div>
                                        <span class="badge bg-{{ $withdrawal->status == 'pending' ? 'warning' : ($withdrawal->status == 'approved' ? 'success' : 'danger') }}">
                                            {{ ucfirst($withdrawal->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center">No recent withdrawals</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Earnings Modal -->
<div class="modal fade" id="approveEarningsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i>Approve Pending Earnings
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="approveEarningsForm" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="approveTeacherId" name="teacher_id">
                    <div class="mb-3">
                        <label class="form-label">Amount to Approve</label>
                        <input type="number" class="form-control" id="approveAmount" name="amount" readonly>
                        <small class="text-muted">Maximum: Rp <span id="maxAmount">0</span></small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Add notes for this approval..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Approve Earnings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Enhanced Finance Dashboard Styles */
.finance-overview-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    transition: all 0.3s ease;
    overflow: hidden;
    position: relative;
}

.finance-overview-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.1) 100%);
}

.finance-overview-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.finance-overview-card .card-body {
    padding: 1.5rem;
}

.finance-overview-card .card-title {
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    opacity: 0.9;
}

.finance-overview-card h3 {
    font-size: 1.75rem;
    font-weight: 700;
    margin-top: 0.5rem;
}

.finance-overview-card .fa-2x {
    opacity: 0.8;
    transition: all 0.3s ease;
}

.finance-overview-card:hover .fa-2x {
    transform: scale(1.1);
    opacity: 1;
}

/* Enhanced table styles */
.table {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.table thead th {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    border: none;
    padding: 1rem;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}

.table tbody tr {
    transition: all 0.2s ease;
}

.table tbody tr:hover {
    background-color: #f8f9ff;
    transform: scale(1.01);
}

.table tbody td {
    padding: 1rem;
    vertical-align: middle;
    border-color: #f0f0f0;
}

/* Enhanced card headers */
.card-header {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    border: none;
    border-radius: 10px 10px 0 0 !important;
    padding: 1.25rem;
}

.card-header h5 {
    margin: 0;
    font-weight: 600;
}

/* Enhanced buttons */
.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
}

.btn-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.4);
}

.btn-outline-primary {
    border: 2px solid #007bff;
    color: #007bff;
    background: transparent;
}

.btn-outline-primary:hover {
    background: #007bff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.4);
}

.btn-outline-success {
    border: 2px solid #28a745;
    color: #28a745;
    background: transparent;
}

.btn-outline-success:hover {
    background: #28a745;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
}

/* Enhanced badges */
.badge {
    border-radius: 20px;
    padding: 0.5rem 1rem;
    font-weight: 500;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Enhanced avatar */
.avatar-sm {
    font-size: 12px;
    font-weight: bold;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: 2px solid white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* Enhanced modal */
.modal-content {
    border: none;
    border-radius: 15px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
}

.modal-header {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    border: none;
    border-radius: 15px 15px 0 0;
}

/* Enhanced list group */
.list-group-item {
    border: none;
    border-bottom: 1px solid #f0f0f0;
    transition: all 0.2s ease;
}

.list-group-item:hover {
    background-color: #f8f9ff;
    transform: translateX(5px);
}

/* Enhanced page title */
.page-title {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 700;
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in-up {
    animation: fadeInUp 0.6s ease-out;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .finance-overview-card {
        margin-bottom: 1rem;
    }
    
    .table {
        font-size: 0.875rem;
    }
    
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
}
</style>

<script>
function approveEarnings(teacherId, maxAmount) {
    document.getElementById('approveTeacherId').value = teacherId;
    document.getElementById('approveAmount').value = maxAmount;
    document.getElementById('maxAmount').textContent = new Intl.NumberFormat('id-ID').format(maxAmount);
    document.getElementById('approveEarningsForm').action = `/admin/finance/teacher/${teacherId}/approve-earnings`;
    new bootstrap.Modal(document.getElementById('approveEarningsModal')).show();
}

function exportReport(type) {
    const startDate = prompt('Start date (YYYY-MM-DD):', new Date().toISOString().split('T')[0]);
    const endDate = prompt('End date (YYYY-MM-DD):', new Date().toISOString().split('T')[0]);
    
    if (startDate && endDate) {
        window.open(`/admin/finance/export?type=${type}&start_date=${startDate}&end_date=${endDate}`, '_blank');
    }
}
</script>
@endsection
