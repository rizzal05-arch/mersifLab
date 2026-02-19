@extends('layouts.admin')

@section('title', 'Teacher Financial Management - ' . $teacher->name)

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <i class="fas fa-user-tie me-2"></i>Financial Management - {{ $teacher->name }}
                </h2>
                <a href="{{ route('admin.finance.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Balance Overview -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-0">Current Balance</h5>
                            <h3 class="mb-0">Rp {{ number_format($balance->balance, 0, ',', '.') }}</h3>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-wallet fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-0">Total Earnings</h5>
                            <h3 class="mb-0">Rp {{ number_format($balance->total_earnings, 0, ',', '.') }}</h3>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-chart-line fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-0">Total Withdrawn</h5>
                            <h3 class="mb-0">Rp {{ number_format($balance->total_withdrawn, 0, ',', '.') }}</h3>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-hand-holding-usd fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-0">Pending Earnings</h5>
                            <h3 class="mb-0">Rp {{ number_format($balance->pending_earnings, 0, ',', '.') }}</h3>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Commission Settings -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
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
            <div class="card">
                <div class="card-header">
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
                                        @if($course->commission_type == 'premium')
                                            <span class="badge bg-success">10%</span>
                                        @else
                                            <span class="badge bg-info">20%</span>
                                        @endif
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
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>Transaction History
                    </h5>
                    <button class="btn btn-outline-primary btn-sm" onclick="exportTransactions()">
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
            <div class="card">
                <div class="card-header">
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
                                    <th>Account Name</th>
                                    <th>Status</th>
                                    <th>Processed Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($withdrawals as $withdrawal)
                                <tr>
                                    <td>{{ $withdrawal->created_at->format('d M Y H:i') }}</td>
                                    <td><code>{{ $withdrawal->withdrawal_code }}</code></td>
                                    <td>Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</td>
                                    <td>{{ $withdrawal->bank_name }}</td>
                                    <td>{{ $withdrawal->bank_account_name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $withdrawal->status == 'pending' ? 'warning' : ($withdrawal->status == 'approved' ? 'success' : 'danger') }}">
                                            {{ ucfirst($withdrawal->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $withdrawal->processed_at?->format('d M Y H:i') ?? '-' }}</td>
                                    <td>
                                        @if($withdrawal->status == 'pending')
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-success btn-sm" onclick="processWithdrawal({{ $withdrawal->id }}, 'approved')">
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                                <button class="btn btn-danger btn-sm" onclick="processWithdrawal({{ $withdrawal->id }}, 'rejected')">
                                                    <i class="fas fa-times"></i> Reject
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-muted">Processed</span>
                                        @endif
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

<!-- Process Withdrawal Modal -->
<div class="modal fade" id="processWithdrawalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-gavel me-2"></i>Process Withdrawal
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="processWithdrawalForm" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="withdrawalId" name="withdrawal_id">
                    <input type="hidden" id="withdrawalStatus" name="status">
                    <div class="mb-3">
                        <label class="form-label">Action</label>
                        <input type="text" class="form-control" id="actionDisplay" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Admin Notes</label>
                        <textarea class="form-control" name="admin_notes" rows="4" placeholder="Enter reason for approval/rejection..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Process
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function processWithdrawal(withdrawalId, status) {
    document.getElementById('withdrawalId').value = withdrawalId;
    document.getElementById('withdrawalStatus').value = status;
    document.getElementById('actionDisplay').value = status === 'approved' ? 'Approve Withdrawal' : 'Reject Withdrawal';
    document.getElementById('processWithdrawalForm').action = `/admin/finance/withdrawal/${withdrawalId}/process`;
    new bootstrap.Modal(document.getElementById('processWithdrawalModal')).show();
}

function exportTransactions() {
    const startDate = prompt('Start date (YYYY-MM-DD):', new Date().toISOString().split('T')[0]);
    const endDate = prompt('End date (YYYY-MM-DD):', new Date().toISOString().split('T')[0]);
    
    if (startDate && endDate) {
        window.open(`/admin/finance/export?type=transactions&start_date=${startDate}&end_date=${endDate}`, '_blank');
    }
}
</script>
@endsection
