@extends('layouts.admin')

@section('title', 'Teacher Financial Management - ' . $teacher->name)

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="page-title">
                    <i class="fas fa-user-tie me-2"></i>Financial Management - {{ $teacher->name }}
                </h2>
                <a href="{{ route('admin.finance.dashboard') }}" class="btn btn-light">
                    <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Balance Overview -->
    <div class="row mb-4 fade-in-up">
        <div class="col-md-3 mb-3">
            <div class="card finance-overview-card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title">Current Balance</h5>
                            <h3 class="mb-0">Rp {{ number_format($balance->balance, 0, ',', '.') }}</h3>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-wallet fa-2x"></i>
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
                            <h5 class="card-title">Total Earnings</h5>
                            <h3 class="mb-0">Rp {{ number_format($balance->total_earnings, 0, ',', '.') }}</h3>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-chart-line fa-2x"></i>
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
                            <h5 class="card-title">Total Withdrawn</h5>
                            <h3 class="mb-0">Rp {{ number_format($balance->total_withdrawn, 0, ',', '.') }}</h3>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-hand-holding-usd fa-2x"></i>
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
                            <h5 class="card-title">Pending Earnings</h5>
                            <h3 class="mb-0">Rp {{ number_format($balance->pending_earnings, 0, ',', '.') }}</h3>
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
        <!-- Commission Settings -->
        <div class="col-lg-4">
            <div class="card fade-in-up">
                <div class="card-header gradient-header">
                    <h5 class="mb-0">
                        <i class="fas fa-percentage me-2"></i>Commission Settings
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.finance.teacher.commission', $teacher->id) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Commission Type</label>
                            <select class="form-select" name="commission_type">
                                <option value="per_course" {{ $commissionSettings->commission_type == 'per_course' ? 'selected' : '' }}>Per Course</option>
                                <option value="fixed" {{ $commissionSettings->commission_type == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                <option value="tiered" {{ $commissionSettings->commission_type == 'tiered' ? 'selected' : '' }}>Tiered</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Platform %</label>
                                    <input type="number" class="form-control" name="platform_percentage" 
                                           value="{{ $commissionSettings->platform_percentage }}" step="0.01" min="0" max="100" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Teacher %</label>
                                    <input type="number" class="form-control" name="teacher_percentage" 
                                           value="{{ $commissionSettings->teacher_percentage }}" step="0.01" min="0" max="100" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Minimum Amount</label>
                            <input type="number" class="form-control" name="min_amount" 
                                   value="{{ $commissionSettings->min_amount }}" step="1000" min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="2">{{ $commissionSettings->notes ?? '' }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Courses -->
        <div class="col-lg-8">
            <div class="card fade-in-up">
                <div class="card-header gradient-header">
                    <h5 class="mb-0">
                        <i class="fas fa-book me-2"></i>Teacher Courses
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Course Name</th>
                                    <th>Type</th>
                                    <th>Students</th>
                                    <th>Revenue</th>
                                    <th>Commission</th>
                                    <th>Teacher Earning</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($courses as $course)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $course->name }}</div>
                                        <small class="text-muted">Created: {{ $course->created_at->format('d M Y') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $course->commission_type == 'premium' ? 'warning' : 'secondary' }}">
                                            {{ ucfirst($course->commission_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $course->purchases_count ?? 0 }}</span>
                                    </td>
                                    <td>Rp {{ number_format($course->revenue ?? 0, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge bg-info">20%</span>
                                    </td>
                                    <td class="fw-bold text-success">
                                        Rp {{ number_format($course->teacher_earning ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card fade-in-up">
                <div class="card-header d-flex justify-content-between align-items-center gradient-header">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>Transaction History
                    </h5>
                    <button class="btn btn-light btn-sm" onclick="exportTransactions()">
                        <i class="fas fa-download me-1"></i>Export
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Student</th>
                                    <th>Course</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Teacher Share</th>
                                    <th>Platform Share</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                    <td>{{ $transaction->user->name }}</td>
                                    <td>{{ $transaction->course->name }}</td>
                                    <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $transaction->status == 'success' ? 'success' : ($transaction->status == 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                    </td>
                                    <td class="text-success">Rp {{ number_format($transaction->teacher_earning ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-warning">Rp {{ number_format($transaction->platform_commission ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Withdrawal History -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card fade-in-up">
                <div class="card-header gradient-header">
                    <h5 class="mb-0">
                        <i class="fas fa-money-bill-wave me-2"></i>Withdrawal History
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Withdrawal Code</th>
                                    <th>Amount</th>
                                    <th>Bank</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($withdrawals as $withdrawal)
                                <tr>
                                    <td>{{ $withdrawal->created_at->format('d M Y H:i') }}</td>
                                    <td><code>{{ $withdrawal->withdrawal_code }}</code></td>
                                    <td><strong>Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</strong></td>
                                    <td>{{ $withdrawal->bank_name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $withdrawal->status == 'pending' ? 'warning' : ($withdrawal->status == 'approved' ? 'info' : ($withdrawal->status == 'processed' ? 'success' : 'danger')) }}">
                                            {{ $withdrawal->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.finance.withdrawal.show', $withdrawal->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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
.gradient-header {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    border: none;
    border-radius: 10px 10px 0 0 !important;
    padding: 1.25rem;
}

.gradient-header h5 {
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

.btn-light {
    background: rgba(255, 255, 255, 0.9);
    color: #007bff;
    backdrop-filter: blur(10px);
}

.btn-light:hover {
    background: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 255, 255, 0.3);
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
function exportTransactions() {
    const startDate = prompt('Start date (YYYY-MM-DD):', new Date().toISOString().split('T')[0]);
    const endDate = prompt('End date (YYYY-MM-DD):', new Date().toISOString().split('T')[0]);
    
    if (startDate && endDate) {
        window.open(`/admin/finance/export?type=transactions&start_date=${startDate}&end_date=${endDate}`, '_blank');
    }
}
</script>
@endsection
