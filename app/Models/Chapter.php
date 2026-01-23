<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

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
        'total_duration',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'total_duration' => 'integer',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Update class duration when chapter is created, updated, or deleted
        static::created(function ($chapter) {
            $chapter->updateClassDuration();
        });

        static::updated(function ($chapter) {
            $chapter->updateClassDuration();
        });

        static::deleted(function ($chapter) {
            $chapter->updateClassDuration();
        });
    }

    /**
     * Recalculate total duration from all modules
     */
    public function recalculateTotalDuration()
    {
        if (!$this->id) {
            return 0;
        }
        
        $total = (int) (DB::table('modules')
            ->where('chapter_id', $this->id)
            ->sum('estimated_duration') ?? 0);
        
        // Update tanpa trigger event untuk menghindari infinite loop
        if ($this->total_duration != $total) {
            DB::table('chapters')
                ->where('id', $this->id)
                ->update(['total_duration' => $total]);
            
            // Update attribute tanpa trigger event
            $this->setAttribute('total_duration', $total);
            
            // Refresh model untuk memastikan data ter-update
            $this->refresh();
        }
        
        return $total;
    }

    /**
     * Update class total duration
     */
    public function updateClassDuration()
    {
        $class = ClassModel::find($this->class_id);
        if ($class) {
            $class->recalculateTotalDuration();
        }
        
        return $this->total_duration;
    }

    /**
     * Get formatted total duration
     */
    public function getFormattedTotalDurationAttribute()
    {
        $minutes = $this->total_duration ?? 0;
        
        if ($minutes < 60) {
            return $minutes . ' menit';
        }
        
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        if ($remainingMinutes == 0) {
            return $hours . ' jam';
        }
        
        return $hours . ' jam ' . $remainingMinutes . ' menit';
    }

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
