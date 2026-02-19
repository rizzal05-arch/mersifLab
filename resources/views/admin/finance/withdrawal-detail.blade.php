@extends('layouts.admin')

@section('title', 'Detail Penarikan - ' . $withdrawal->withdrawal_code)

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <i class="fas fa-money-bill-wave me-2"></i>Detail Penarikan Dana
                </h2>
                <a href="{{ route('admin.finance.teacher', $withdrawal->teacher_id) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Withdrawal Info -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>Informasi Penarikan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <strong>Kode Penarikan:</strong>
                        </div>
                        <div class="col-sm-8">
                            <code class="text-danger">{{ $withdrawal->withdrawal_code }}</code>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <strong>Guru:</strong>
                        </div>
                        <div class="col-sm-8">
                            <a href="{{ route('admin.finance.teacher', $withdrawal->teacher_id) }}">
                                {{ $withdrawal->teacher->name }}
                            </a>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <strong>Jumlah:</strong>
                        </div>
                        <div class="col-sm-8">
                            <h4 class="text-success mb-0">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</h4>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <strong>Status:</strong>
                        </div>
                        <div class="col-sm-8">
                            <span class="badge bg-{{ $withdrawal->status_badge }} fs-6">
                                {{ $withdrawal->status_label }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <strong>Tanggal Permintaan:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ $withdrawal->requested_at->format('d M Y H:i') }}
                        </div>
                    </div>

                    @if($withdrawal->processed_at)
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <strong>Tanggal Diproses:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ $withdrawal->processed_at->format('d M Y H:i') }}
                        </div>
                    </div>
                    @endif

                    @if($withdrawal->notes)
                    <div class="row">
                        <div class="col-sm-4">
                            <strong>Catatan Guru:</strong>
                        </div>
                        <div class="col-sm-8">
                            <p class="mb-0 text-muted">{{ $withdrawal->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Bank Info -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-university me-2"></i>Informasi Rekening Bank
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <strong>Nama Bank:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ $withdrawal->bank_name }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <strong>Nomor Rekening:</strong>
                        </div>
                        <div class="col-sm-8">
                            <h5 class="font-monospace text-danger mb-0">{{ $withdrawal->bank_account_number }}</h5>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4">
                            <strong>Atas Nama:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ $withdrawal->bank_account_name }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approval Form -->
    @if($withdrawal->status === 'pending')
    <div class="row">
        <div class="col-12">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-gavel me-2"></i>Form Persetujuan Penarikan
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.finance.withdrawal.process', $withdrawal->id) }}" method="POST" enctype="multipart/form-data" id="approvalForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Status</strong></label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="statusApproved" value="approved" required>
                                            <label class="form-check-label" for="statusApproved">
                                                <i class="fas fa-check-circle text-success me-2"></i>Setujui
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="statusRejected" value="rejected">
                                            <label class="form-check-label" for="statusRejected">
                                                <i class="fas fa-times-circle text-danger me-2"></i>Tolak
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6"></div>
                        </div>

                        <!-- Conditional fields berdasarkan status -->
                        <div id="approvedFields" style="display:none;">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-image me-2"></i>Bukti Transfer (JPG, PNG, PDF - Max 5MB)
                                        </label>
                                        <input type="file" class="form-control" name="transfer_proof" accept=".jpg,.jpeg,.png,.pdf">
                                        <small class="text-muted">Upload bukti screenshot transfer ke rekening guru</small>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-sticky-note me-2"></i>Catatan Persetujuan
                                        </label>
                                        <textarea class="form-control" name="approval_notes" placeholder="Contoh: Transfer via BRI mobile banking" rows="2"></textarea>
                                        <small class="text-muted">Catatan untuk guru (opsional)</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="rejectedFields" style="display:none;">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-ban me-2"></i>Alasan Penolakan
                                </label>
                                <textarea class="form-control" name="admin_notes" placeholder="Jelaskan alasan penolakan..." rows="3" required></textarea>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                                <i class="fas fa-save me-2"></i>Proses Penarikan
                            </button>
                            <a href="{{ route('admin.finance.teacher', $withdrawal->teacher_id) }}" class="btn btn-secondary">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- Already Processed Info -->
    <div class="row">
        <div class="col-12">
            <div class="card border-{{ $withdrawal->status === 'rejected' ? 'danger' : 'success' }}">
                <div class="card-header bg-{{ $withdrawal->status === 'rejected' ? 'danger' : 'success' }} text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informasi Pemrosesan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>Status:</strong>
                        </div>
                        <div class="col-sm-9">
                            <span class="badge bg-{{ $withdrawal->status_badge }} fs-6">
                                {{ $withdrawal->status_label }}
                            </span>
                        </div>
                    </div>

                    @if($withdrawal->approval_notes)
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>Catatan Persetujuan:</strong>
                        </div>
                        <div class="col-sm-9">
                            {{ $withdrawal->approval_notes }}
                        </div>
                    </div>
                    @endif

                    @if($withdrawal->admin_notes)
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>Catatan Admin:</strong>
                        </div>
                        <div class="col-sm-9">
                            {{ $withdrawal->admin_notes }}
                        </div>
                    </div>
                    @endif

                    @if($withdrawal->transfer_proof)
                    <div class="row">
                        <div class="col-sm-3">
                            <strong>Bukti Transfer:</strong>
                        </div>
                        <div class="col-sm-9">
                            <a href="{{ asset('storage/' . $withdrawal->transfer_proof) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download me-1"></i>Lihat Bukti
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@if($withdrawal->status === 'pending')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusApproved = document.getElementById('statusApproved');
    const statusRejected = document.getElementById('statusRejected');
    const approvedFields = document.getElementById('approvedFields');
    const rejectedFields = document.getElementById('rejectedFields');
    const submitBtn = document.getElementById('submitBtn');
    const adminNotesField = document.querySelector('[name="admin_notes"]');

    function updateFields() {
        if (statusApproved.checked) {
            approvedFields.style.display = 'block';
            rejectedFields.style.display = 'none';
            adminNotesField.removeAttribute('required');
            submitBtn.disabled = false;
        } else if (statusRejected.checked) {
            approvedFields.style.display = 'none';
            rejectedFields.style.display = 'block';
            adminNotesField.setAttribute('required', 'required');
            submitBtn.disabled = false;
        } else {
            approvedFields.style.display = 'none';
            rejectedFields.style.display = 'none';
            submitBtn.disabled = true;
        }
    }

    statusApproved.addEventListener('change', updateFields);
    statusRejected.addEventListener('change', updateFields);

    // Form validation
    document.getElementById('approvalForm').addEventListener('submit', function(e) {
        if (statusRejected.checked && !adminNotesField.value.trim()) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Kesalahan',
                text: 'Alasan penolakan harus diisi'
            });
        }
    });
});
</script>
@endif
@endsection
