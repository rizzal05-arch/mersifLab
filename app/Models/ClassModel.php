<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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
        'is_published',
        'order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
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
}
