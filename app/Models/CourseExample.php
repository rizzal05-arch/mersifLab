<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Course Model
 * 
 * Relasi:
 * - Teacher (1 teacher banyak courses)
 * - Students (many to many dengan users table)
 * - Materi (1 course banyak materi)
 */
class Course extends Model
{
    protected $fillable = [
        'name',
        'description',
        'teacher_id',
        'price',
        'category',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'price' => 'decimal:2',
    ];

    /**
     * Get teacher yang punya course ini
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get student yang enroll di course ini (many to many)
     * 
     * Tabel pivot: course_student
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_student', 'course_id', 'user_id')
            ->where('users.role', 'student')
            ->withTimestamps();
    }

    /**
     * Get materi yang ada di course ini
     */
    public function materi()
    {
        return $this->hasMany(Materi::class);
    }

    /**
     * Check apakah course bisa di-edit oleh user
     * 
     * @param User $user
     * @return bool
     */
    public function canBeEditedBy(User $user): bool
    {
        return $user->isAdmin() || ($user->isTeacher() && $this->teacher_id === $user->id);
    }

    /**
     * Get count student yang aktif
     */
    public function activeStudentsCount()
    {
        return $this->students()->count();
    }
}
