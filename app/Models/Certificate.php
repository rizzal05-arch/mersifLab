<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'certificate_code',
        'issued_at',
        'file_path',
        'status',
        'revoke_reason',
        'revoked_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    /**
     * Get user yang memiliki sertifikat ini
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get course/class untuk sertifikat ini
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'course_id');
    }

    /**
     * Scope: hanya sertifikat aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Generate unique certificate code
     */
    public static function generateCertificateCode(): string
    {
        do {
            $code = 'CERT-' . strtoupper(uniqid()) . '-' . rand(1000, 9999);
        } while (self::where('certificate_code', $code)->exists());
        
        return $code;
    }

    /**
     * Check if certificate is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
