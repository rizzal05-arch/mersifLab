<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'new_course',
        'new_chapter',
        'new_module',
        'module_approved',
        'student_enrolled',
        'course_rated',
        'course_completed',
        'announcements',
        'promotions',
        'course_recommendations',
        'learning_stats',
    ];

    protected $casts = [
        'new_course' => 'boolean',
        'new_chapter' => 'boolean',
        'new_module' => 'boolean',
        'module_approved' => 'boolean',
        'student_enrolled' => 'boolean',
        'course_rated' => 'boolean',
        'course_completed' => 'boolean',
        'announcements' => 'boolean',
        'promotions' => 'boolean',
        'course_recommendations' => 'boolean',
        'learning_stats' => 'boolean',
    ];

    /**
     * Get user yang memiliki preference ini
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if user wants to receive notification of specific type
     */
    public function wantsNotification(string $type): bool
    {
        // Map notification types to preference columns
        $typeMap = [
            'new_course' => 'new_course',
            'new_chapter' => 'new_chapter',
            'new_module' => 'new_module',
            'module_approved' => 'module_approved',
            'student_enrolled' => 'student_enrolled',
            'course_rated' => 'course_rated',
            'course_completed' => 'course_completed',
            'announcement' => 'announcements',
            'promotion' => 'promotions',
            'course_recommendation' => 'course_recommendations',
            'learning_stat' => 'learning_stats',
        ];

        $preferenceKey = $typeMap[$type] ?? null;
        
        if (!$preferenceKey) {
            // Default to true if type not found
            return true;
        }

        return (bool) ($this->$preferenceKey ?? true);
    }
}
