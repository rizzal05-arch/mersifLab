@extends('layouts.admin')

@section('title', 'Detail Penarikan - ' . $withdrawal->withdrawal_code)

@section('content')
<div class="page-title" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
    <div>
        <h1>Detail Penarikan - {{ $withdrawal->withdrawal_code }}</h1>
    </div>
    <div>
        <a href="{{ route('admin.finance.teacher', $withdrawal->teacher_id) }}" class="btn btn-light" style="font-size: 13px; color: #2F80ED; border: 1px solid #e0e0e0; padding: 8px 16px; border-radius: 6px; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='white'">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>
</div>

<div class="row">
    <!-- Withdrawal Info -->
    <div class="col-lg-6">
        <div class="card-content">
            <div class="card-content-title">
                <span>
                    <i class="fas fa-file-alt me-2"></i>Informasi Penarikan
                </span>
            </div>
            <div class="table-responsive">
                <table class="table table-sm" style="font-size: 13px; border-collapse: separate; border-spacing: 0;">
                    <tbody>
                        <tr style="border-bottom: 1px solid #f8f9fa;">
                            <td style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; width: 140px;">Kode Penarikan</td>
                            <td style="border: none; padding: 12px 8px; vertical-align: middle;">
                                <code style="background: #f8d7da; color: #721c24; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500;">{{ $withdrawal->withdrawal_code }}</code>
                            </td>
                        </tr>
                        <tr style="border-bottom: 1px solid #f8f9fa;">
                            <td style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Guru</td>
                            <td style="border: none; padding: 12px 8px; vertical-align: middle;">
                                <a href="{{ route('admin.finance.teacher', $withdrawal->teacher_id) }}" style="color: #1976d2; text-decoration: none; font-size: 13px; font-weight: 500;">
                                    {{ $withdrawal->teacher->name }}
                                </a>
                            </td>
                        </tr>
                        <tr style="border-bottom: 1px solid #f8f9fa;">
                            <td style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Jumlah</td>
                            <td style="border: none; padding: 12px 8px; vertical-align: middle; color: #333; font-weight: 500; font-size: 14px;">
                                <strong>Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                        <tr style="border-bottom: 1px solid #f8f9fa;">
                            <td style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Status</td>
                            <td style="border: none; padding: 12px 8px; vertical-align: middle;">
                                <span class="badge" style="background: {{ $withdrawal->status == 'pending' ? '#fff3cd' : ($withdrawal->status == 'approved' ? '#d4edda' : '#f8d7da') }}; color: {{ $withdrawal->status == 'pending' ? '#856404' : ($withdrawal->status == 'approved' ? '#155724' : '#721c24') }}; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 500;">
                                    {{ $withdrawal->status_label }}
                                </span>
                            </td>
                        </tr>
                        <tr style="border-bottom: 1px solid #f8f9fa;">
                            <td style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Tanggal Permintaan</td>
                            <td style="border: none; padding: 12px 8px; vertical-align: middle; color: #333; font-weight: 500; font-size: 13px;">
                                {{ $withdrawal->requested_at->format('d M Y H:i') }}
                            </td>
                        </tr>
                        @if($withdrawal->processed_at)
                        <tr style="border-bottom: 1px solid #f8f9fa;">
                            <td style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Tanggal Diproses</td>
                            <td style="border: none; padding: 12px 8px; vertical-align: middle; color: #333; font-weight: 500; font-size: 13px;">
                                {{ $withdrawal->processed_at->format('d M Y H:i') }}
                            </td>
                        </tr>
                        @endif
                        @if($withdrawal->notes)
                        <tr style="border-bottom: 1px solid #f8f9fa;">
                            <td style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Catatan Guru</td>
                            <td style="border: none; padding: 12px 8px; vertical-align: middle; color: #333; font-weight: 500; font-size: 13px;">
                                {{ $withdrawal->notes }}
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

        <!-- Bank Info -->
    <div class="col-lg-6">
        <div class="card-content">
            <div class="card-content-title">
                <span>
                    <i class="fas fa-university me-2"></i>Informasi Rekening Bank
                </span>
            </div>
            <div class="table-responsive">
                <table class="table table-sm" style="font-size: 13px; border-collapse: separate; border-spacing: 0;">
                    <tbody>
                        <tr style="border-bottom: 1px solid #f8f9fa;">
                            <td style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; width: 140px;">Nama Bank</td>
                            <td style="border: none; padding: 12px 8px; vertical-align: middle; color: #333; font-weight: 500; font-size: 13px;">
                                {{ $withdrawal->bank_name }}
                            </td>
                        </tr>
                        <tr style="border-bottom: 1px solid #f8f9fa;">
                            <td style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Nomor Rekening</td>
                            <td style="border: none; padding: 12px 8px; vertical-align: middle; color: #333; font-weight: 500; font-size: 13px;">
                                <h5 class="font-monospace text-danger mb-0" style="font-size: 14px;">{{ $withdrawal->bank_account_number }}</h5>
                            </td>
                        </tr>
                        <tr style="border-bottom: 1px solid #f8f9fa;">
                            <td style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Atas Nama</td>
                            <td style="border: none; padding: 12px 8px; vertical-align: middle; color: #333; font-weight: 500; font-size: 13px;">
                                {{ $withdrawal->bank_account_name }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>

    <!-- Approval Form -->
    @if($withdrawal->status === 'pending')
    <div class="row">
        <div class="col-12">
            <div class="card-content" style="border: 2px solid #ffc107;">
                <div class="card-content-title" style="background: #ffc107; color: #333; padding: 15px 25px; margin: -25px -25px 20px -25px; border-radius: 8px 8px 0 0;">
                    <span>
                        <i class="fas fa-gavel me-2"></i>Form Persetujuan Penarikan
                    </span>
                </div>
                <form action="{{ route('admin.finance.withdrawal.process', $withdrawal->id) }}" method="POST" enctype="multipart/form-data" id="approvalForm">
                    @csrf
                    
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label class="form-label" style="font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">
                                    <i class="fas fa-flag me-2"></i>Status
                                </label>
                                <div>
                                    <div class="form-check form-check-inline me-3">
                                        <input class="form-check-input" type="radio" name="status" id="statusApproved" value="approved" required>
                                        <label class="form-check-label" for="statusApproved" style="font-size: 13px; color: #27AE60; font-weight: 500;">
                                            <i class="fas fa-check-circle me-2"></i>Setujui
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="status" id="statusRejected" value="rejected">
                                        <label class="form-check-label" for="statusRejected" style="font-size: 13px; color: #dc3545; font-weight: 500;">
                                            <i class="fas fa-times-circle me-2"></i>Tolak
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
                                <div class="mb-4">
                                    <label class="form-label" style="font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">
                                        <i class="fas fa-image me-2"></i>Bukti Transfer (JPG, PNG, PDF - Max 5MB)
                                    </label>
                                    <input type="file" class="form-control" name="transfer_proof" accept=".jpg,.jpeg,.png,.pdf" style="font-size: 14px; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;">
                                    <small class="text-muted" style="font-size: 12px;">Upload bukti screenshot transfer ke rekening guru</small>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label class="form-label" style="font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">
                                        <i class="fas fa-sticky-note me-2"></i>Catatan Persetujuan
                                    </label>
                                    <textarea class="form-control" name="approval_notes" placeholder="Contoh: Transfer via BRI mobile banking" rows="2" style="font-size: 14px; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; resize: vertical;"></textarea>
                                    <small class="text-muted" style="font-size: 12px;">Catatan untuk guru (opsional)</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="rejectedFields" style="display:none;">
                        <div class="mb-4">
                            <label class="form-label" style="font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">
                                <i class="fas fa-ban me-2"></i>Alasan Penolakan
                            </label>
                            <textarea class="form-control" name="admin_notes" placeholder="Jelaskan alasan penolakan..." rows="3" required style="font-size: 14px; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; resize: vertical;"></textarea>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" id="submitBtn" disabled style="padding: 10px 20px; border-radius: 6px; font-weight: 500; background: #2F80ED; border: none;">
                            <i class="fas fa-save me-2"></i>Proses Penarikan
                        </button>
                        <a href="{{ route('admin.finance.teacher', $withdrawal->teacher_id) }}" class="btn btn-secondary" style="padding: 10px 20px; border-radius: 6px; font-weight: 500;">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @else
    <!-- Already Processed Info -->
    <div class="row">
        <div class="col-12">
            <div class="card-content" style="border: 2px solid {{ $withdrawal->status === 'rejected' ? '#dc3545' : '#28a745' }};">
                <div class="card-content-title" style="background: {{ $withdrawal->status === 'rejected' ? '#dc3545' : '#28a745' }}; color: white; padding: 15px 25px; margin: -25px -25px 20px -25px; border-radius: 8px 8px 0 0;">
                    <span>
                        <i class="fas fa-info-circle me-2"></i>Informasi Pemrosesan
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm" style="font-size: 13px; border-collapse: separate; border-spacing: 0;">
                        <tbody>
                            <tr style="border-bottom: 1px solid #f8f9fa;">
                                <td style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; width: 140px;">Status</td>
                                <td style="border: none; padding: 12px 8px; vertical-align: middle;">
                                    <span class="badge" style="background: {{ $withdrawal->status == 'approved' ? '#d4edda' : '#f8d7da' }}; color: {{ $withdrawal->status == 'approved' ? '#155724' : '#721c24' }}; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 500;">
                                        {{ $withdrawal->status_label }}
                                    </span>
                                </td>
                            </tr>
                            @if($withdrawal->approval_notes)
                            <tr style="border-bottom: 1px solid #f8f9fa;">
                                <td style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Catatan Persetujuan</td>
                                <td style="border: none; padding: 12px 8px; vertical-align: middle; color: #333; font-weight: 500; font-size: 13px;">
                                    {{ $withdrawal->approval_notes }}
                                </td>
                            </tr>
                            @endif
                            @if($withdrawal->admin_notes)
                            <tr style="border-bottom: 1px solid #f8f9fa;">
                                <td style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Catatan Admin</td>
                                <td style="border: none; padding: 12px 8px; vertical-align: middle; color: #333; font-weight: 500; font-size: 13px;">
                                    {{ $withdrawal->admin_notes }}
                                </td>
                            </tr>
                            @endif
                            @if($withdrawal->transfer_proof)
                            <tr style="border-bottom: 1px solid #f8f9fa;">
                                <td style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Bukti Transfer</td>
                                <td style="border: none; padding: 12px 8px; vertical-align: middle;">
                                    <a href="{{ asset('storage/' . $withdrawal->transfer_proof) }}" target="_blank" class="btn btn-sm" style="color: #1976d2; text-decoration: none; font-size: 12px; font-weight: 500; padding: 4px 8px; border-radius: 4px; transition: background 0.2s;" onmouseover="this.style.background='#e3f2fd'" onmouseout="this.style.background='transparent'">
                                        <i class="fas fa-download me-1"></i>Lihat Bukti
                                    </a>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@endsection

<style>
/* Withdrawal Detail Consistent Styles */
.card-content {
    background: white;
    border-radius: 10px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    margin-bottom: 25px;
}

.card-content-title {
    font-size: 18px;
    font-weight: 700;
    color: #333333;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.table {
    font-size: 13px;
    border-collapse: separate;
    border-spacing: 0;
}

.table td {
    vertical-align: middle;
}

.badge {
    border-radius: 20px;
    padding: 0.5rem 1rem;
    font-weight: 500;
    font-size: 0.8rem;
}

.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
}

.btn-primary {
    background: #2F80ED;
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(47, 128, 237, 0.4);
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(108, 117, 125, 0.4);
}

.form-control {
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    padding: 12px;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

.form-control:focus {
    border-color: #2F80ED;
    box-shadow: 0 0 0 0.2rem rgba(47, 128, 237, 0.25);
}

.form-label {
    font-size: 14px;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
}

.form-check-input:checked + .form-check-label {
    color: #2F80ED;
    font-weight: 600;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .card-content {
        margin-bottom: 1rem;
    }
    
    .table {
        font-size: 0.875rem;
    }
    
    .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
}
</style>

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
