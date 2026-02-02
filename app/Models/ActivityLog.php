<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'description',
        'subject_type',
        'subject_id',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * Get user yang melakukan aktivitas
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subject of the activity (polymorphic)
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get action icon class based on action type
     */
    public function getActionIconAttribute(): string
    {
        return match($this->action) {
            'created' => 'fas fa-plus-circle text-success',
            'updated' => 'fas fa-edit text-primary',
            'deleted' => 'fas fa-trash text-danger',
            'login' => 'fas fa-sign-in-alt text-info',
            'google_login' => 'fab fa-google text-danger',
            'logout' => 'fas fa-sign-out-alt text-secondary',
            'viewed' => 'fas fa-eye text-warning',
            'accessed' => 'fas fa-key text-info',
            'approved' => 'fas fa-check-circle text-success',
            'rejected' => 'fas fa-times-circle text-danger',
            'banned' => 'fas fa-ban text-danger',
            'unbanned' => 'fas fa-user-check text-success',
            'published' => 'fas fa-globe text-success',
            'unpublished' => 'fas fa-eye-slash text-warning',
            'enrolled' => 'fas fa-user-plus text-primary',
            'purchased' => 'fas fa-shopping-cart text-success',
            default => 'fas fa-circle text-secondary',
        };
    }

    /**
     * Get formatted activity description with user name
     */
    public function getFormattedDescriptionAttribute(): string
    {
        $userName = $this->user ? $this->user->name : 'Unknown User';
        
        // Replace user_login with actual user name
        $description = str_replace('user_login', $userName, $this->description);
        
        // Add more descriptive prefixes for better readability
        return match($this->action) {
            'created' => "Created {$description}",
            'updated' => "Updated {$description}",
            'deleted' => "Deleted {$description}",
            'login' => "Logged in to the system",
            'google_login' => "Logged in via Google",
            'logout' => "Logged out from the system",
            'viewed' => "Viewed {$description}",
            'accessed' => "Accessed {$description}",
            'approved' => "Approved {$description}",
            'rejected' => "Rejected {$description}",
            'banned' => "Banned {$description}",
            'unbanned' => "Unbanned {$description}",
            'published' => "Published {$description}",
            'unpublished' => "Unpublished {$description}",
            'enrolled' => "Enrolled in {$description}",
            'purchased' => "Purchased {$description}",
            default => $description,
        };
    }
}
