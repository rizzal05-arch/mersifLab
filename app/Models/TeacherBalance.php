<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'balance',
        'total_earnings',
        'total_withdrawn',
        'pending_earnings',
        'last_updated',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'total_withdrawn' => 'decimal:2',
        'pending_earnings' => 'decimal:2',
        'last_updated' => 'datetime',
    ];

    /**
     * Get the teacher that owns the balance.
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Update balance with new earnings
     */
    public function addEarnings($amount)
    {
        $this->balance += $amount;
        $this->total_earnings += $amount;
        $this->last_updated = now();
        $this->save();
    }

    /**
     * Process withdrawal
     */
    public function processWithdrawal($amount)
    {
        if ($this->balance >= $amount) {
            $this->balance -= $amount;
            $this->total_withdrawn += $amount;
            $this->last_updated = now();
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Add pending earnings
     */
    public function addPendingEarnings($amount)
    {
        $this->pending_earnings += $amount;
        $this->last_updated = now();
        $this->save();
    }

    /**
     * Approve pending earnings
     */
    public function approvePendingEarnings($amount)
    {
        if ($this->pending_earnings >= $amount) {
            $this->pending_earnings -= $amount;
            $this->balance += $amount;
            $this->total_earnings += $amount;
            $this->last_updated = now();
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Get formatted balance
     */
    public function getFormattedBalanceAttribute()
    {
        return 'Rp ' . number_format($this->balance, 0, ',', '.');
    }

    /**
     * Get formatted total earnings
     */
    public function getFormattedTotalEarningsAttribute()
    {
        return 'Rp ' . number_format($this->total_earnings, 0, ',', '.');
    }

    /**
     * Get formatted total withdrawn
     */
    public function getFormattedTotalWithdrawnAttribute()
    {
        return 'Rp ' . number_format($this->total_withdrawn, 0, ',', '.');
    }

    /**
     * Get formatted pending earnings
     */
    public function getFormattedPendingEarningsAttribute()
    {
        return 'Rp ' . number_format($this->pending_earnings, 0, ',', '.');
    }

    /**
     * Scope to get teachers with available balance
     */
    public function scopeWithAvailableBalance($query)
    {
        return $query->where('balance', '>', 0);
    }

    /**
     * Scope to get teachers with pending earnings
     */
    public function scopeWithPendingEarnings($query)
    {
        return $query->where('pending_earnings', '>', 0);
    }
}
