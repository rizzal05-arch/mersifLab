<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Chapter Model (Bab dalam Kelas)
 * 
 * Relasi:
 * - Class: BelongsTo ClassModel (1 class)
 * - Modules: HasMany Module (banyak modules)
 */
class Chapter extends Model
{
    protected $fillable = [
        'class_id',
        'title',
        'description',
        'is_published',
        'order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    /**
     * Get class yang punya chapter ini
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * Get modules dalam chapter ini
     */
    public function modules(): HasMany
    {
        return $this->hasMany(Module::class, 'chapter_id')
            ->orderBy('order');
    }

    /**
     * Get teacher dari class
     */
    public function teacher()
    {
        return $this->class->teacher();
    }

    /**
     * Scope: hanya published chapters
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Check apakah chapter bisa di-edit oleh user
     */
    public function canBeEditedBy(User $user): bool
    {
        return $this->class->canBeEditedBy($user);
    }

    /**
     * Get total modules dalam chapter
     */
    public function getTotalModulesAttribute()
    {
        return $this->modules->count();
    }
}
