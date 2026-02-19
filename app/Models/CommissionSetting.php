<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'commission_type',
        'platform_percentage',
        'teacher_percentage',
        'min_amount',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'platform_percentage' => 'decimal:2',
        'teacher_percentage' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the teacher that owns the commission setting.
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get active commission settings
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get global commission settings (teacher_id is null)
     */
    public function scopeGlobal($query)
    {
        return $query->whereNull('teacher_id');
    }

    /**
     * Get teacher-specific commission settings
     */
    public function scopeForTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    /**
     * Calculate commission for a given amount
     */
    public function calculateCommission($amount)
    {
        $platformCommission = ($amount * $this->platform_percentage) / 100;
        $teacherEarning = ($amount * $this->teacher_percentage) / 100;
        
        return [
            'platform_commission' => $platformCommission,
            'teacher_earning' => $teacherEarning,
            'total_amount' => $amount,
        ];
    }

    /**
     * Get default commission settings
     */
    public static function getDefault()
    {
        return self::global()->active()->first() ?? new self([
            'commission_type' => 'per_course',
            'platform_percentage' => 20.00,
            'teacher_percentage' => 80.00,
            'min_amount' => 0,
            'is_active' => true,
        ]);
    }

    /**
     * Get commission for a specific teacher
     */
    public static function getForTeacher($teacherId)
    {
        return self::forTeacher($teacherId)->active()->first() ?? self::getDefault();
    }
}
