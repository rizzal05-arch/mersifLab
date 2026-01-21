<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\DB;

/**
 * Class Model (Kursus/Pembelajaran)
 * 
 * Relasi:
 * - Teacher: BelongsTo User (1 teacher)
 * - Chapters: HasMany Chapter (banyak chapters)
 * - Modules: HasMany Module (through chapters)
 */
class ClassModel extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'teacher_id',
        'name',
        'description',
        'category',
        'is_published',
        'order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    const CATEGORIES = [
        'ai' => 'Artificial Intelligence (AI)',
        'development' => 'Development',
        'marketing' => 'Marketing',
        'design' => 'Design',
        'photography' => 'Photography & Video',
    ];

    /**
     * Get teacher yang punya class ini
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get chapters dalam class ini
     */
    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class, 'class_id')
            ->orderBy('order');
    }

    /**
     * Get all modules through chapters using hasManyThrough
     */
    public function modules(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(Module::class, Chapter::class, 'class_id', 'chapter_id');
    }

    /**
     * Scope: hanya published classes
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope: classes dari teacher tertentu
     */
    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    /**
     * Scope: filter by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: published by category (student view)
     */
    public function scopePublishedByCategory($query, $category)
    {
        return $query->where('is_published', true)
            ->where('category', $category);
    }

    /**
     * Check apakah class bisa di-edit oleh user
     */
    public function canBeEditedBy(User $user): bool
    {
        return $user->isAdmin() || ($user->isTeacher() && $this->teacher_id === $user->id);
    }

    /**
     * Get total modules dalam class
     */
    public function getTotalModulesAttribute()
    {
        return $this->chapters->sum(fn($chapter) => $chapter->modules->count());
    }

    /**
     * Get students yang enroll di class ini (many to many)
     */
    public function students(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'class_student', 'class_id', 'user_id')
            ->withPivot('enrolled_at', 'progress', 'completed_at')
            ->withTimestamps();
    }

    /**
     * Get students yang enroll (untuk withCount) - hanya student role
     */
    public function enrolledStudents(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'class_student', 'class_id', 'user_id')
            ->where('users.role', 'student');
    }

    /**
     * Check apakah student sudah enroll
     */
    public function isEnrolledBy(User $user): bool
    {
        if (!$user || !$user->isStudent()) {
            return false;
        }
        return DB::table('class_student')
            ->where('class_id', $this->id)
            ->where('user_id', $user->id)
            ->exists();
    }
}
