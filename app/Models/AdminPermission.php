<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminPermission extends Model
{
    protected $fillable = [
        'user_id',
        'permission',
        'granted',
        'granted_by',
    ];

    protected $casts = [
        'granted' => 'boolean',
    ];

    /**
     * Get the admin user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who granted this permission
     */
    public function grantedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by');
    }

    /**
     * Available permission types
     */
    public static function getAvailablePermissions(): array
    {
        return [
            'manage_courses' => 'Manage Courses',
            'manage_teachers' => 'Manage Teachers',
            'manage_students' => 'Manage Students',
            'manage_admins' => 'Manage Admins',
            'view_analytics' => 'View Analytics',
            'manage_settings' => 'Manage Settings',
            'manage_messages' => 'Manage Messages',
            'manage_notifications' => 'Manage Notifications',
            'moderate_content' => 'Moderate Content',
            'view_reports' => 'View Reports',
        ];
    }

    /**
     * Check if user has specific permission
     */
    public static function hasPermission(int $userId, string $permission): bool
    {
        // Super admins (first admin) have all permissions
        $firstAdmin = User::where('role', 'admin')->oldest()->first();
        if ($firstAdmin && $firstAdmin->id === $userId) {
            return true;
        }

        return self::where('user_id', $userId)
            ->where('permission', $permission)
            ->where('granted', true)
            ->exists();
    }

    /**
     * Grant permission to user
     */
    public static function grantPermission(int $userId, string $permission, int $grantedBy): void
    {
        self::updateOrCreate(
            ['user_id' => $userId, 'permission' => $permission],
            ['granted' => true, 'granted_by' => $grantedBy]
        );
    }

    /**
     * Revoke permission from user
     */
    public static function revokePermission(int $userId, string $permission): void
    {
        self::where('user_id', $userId)
            ->where('permission', $permission)
            ->update(['granted' => false]);
    }

    /**
     * Get user permissions
     */
    public static function getUserPermissions(int $userId): array
    {
        return self::where('user_id', $userId)
            ->where('granted', true)
            ->pluck('permission')
            ->toArray();
    }
}
