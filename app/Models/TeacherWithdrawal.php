<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherWithdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'amount',
        'status',
        'notes',
        'admin_notes',
        'withdrawal_code',
        'bank_name',
        'bank_account_name',
        'bank_account_number',
        'requested_at',
        'processed_at',
        'transfer_proof',
        'approval_notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($withdrawal) {
            $withdrawal->withdrawal_code = static::generateWithdrawalCode();
            $withdrawal->requested_at = now();
        });
    }

    /**
     * Generate unique withdrawal code
     */
    public static function generateWithdrawalCode()
    {
        do {
            $code = 'WD-' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('withdrawal_code', $code)->exists());
        
        return $code;
    }

    /**
     * Get the teacher that owns the withdrawal
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'info',
            'processed' => 'success',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get status label in Indonesian
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'processed' => 'Diproses',
            'rejected' => 'Ditolak',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Scope a query to only include pending withdrawals
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include approved withdrawals
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include processed withdrawals
     */
    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }

    /**
     * Mark withdrawal as approved
     */
    public function approve($adminNotes = null)
    {
        $this->update([
            'status' => 'approved',
            'admin_notes' => $adminNotes,
        ]);
        
        // Send notification to teacher
        $this->notifyTeacherStatusChange('approved', $adminNotes);
    }

    /**
     * Mark withdrawal as processed
     */
    public function markAsProcessed($adminNotes = null)
    {
        $this->update([
            'status' => 'processed',
            'admin_notes' => $adminNotes,
            'processed_at' => now(),
        ]);
        
        // Send notification to teacher
        $this->notifyTeacherStatusChange('processed', $adminNotes);
    }

    /**
     * Mark withdrawal as rejected
     */
    public function reject($adminNotes = null)
    {
        $this->update([
            'status' => 'rejected',
            'admin_notes' => $adminNotes,
        ]);
        
        // Send notification to teacher
        $this->notifyTeacherStatusChange('rejected', $adminNotes);
    }

    /**
     * Send notification to teacher when withdrawal status changes
     */
    private function notifyTeacherStatusChange($status, $adminNotes = null)
    {
        $teacher = $this->teacher;
        if (!$teacher) return;

        $notificationData = match($status) {
            'approved' => [
                'type' => 'withdrawal_approved',
                'title' => 'Penarikan Disetujui',
                'message' => "Penarikan dana sebesar Rp " . number_format($this->amount, 0, ',', '.') . " telah disetujui. Kode: {$this->withdrawal_code}. Proses transfer akan segera dilakukan."
            ],
            'processed' => [
                'type' => 'withdrawal_processed',
                'title' => 'Penarikan Diproses',
                'message' => "Penarikan dana sebesar Rp " . number_format($this->amount, 0, ',', '.') . " telah diproses. Kode: {$this->withdrawal_code}. Dana telah ditransfer ke rekening Anda."
            ],
            'rejected' => [
                'type' => 'withdrawal_rejected',
                'title' => 'Penarikan Ditolak',
                'message' => "Penarikan dana sebesar Rp " . number_format($this->amount, 0, ',', '.') . " ditolak. Kode: {$this->withdrawal_code}" . ($adminNotes ? ". Alasan: {$adminNotes}" : "")
            ],
            default => null
        };

        if ($notificationData) {
            \App\Models\Notification::create([
                'user_id' => $teacher->id,
                'type' => $notificationData['type'],
                'title' => $notificationData['title'],
                'message' => $notificationData['message'],
            ]);
        }
    }
}
