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
        'status',
        'admin_feedback',
        'price',
        'total_sales',
        'image',
        'what_youll_learn',
        'requirement',
        'total_duration',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Recalculate total duration when class is created or updated
        static::created(function ($class) {
            $class->recalculateTotalDuration();
        });

        static::updated(function ($class) {
            // Skip jika hanya total_duration yang berubah (untuk menghindari loop)
            if ($class->isDirty('total_duration') && $class->getDirty() === ['total_duration']) {
                return;
            }
            $class->recalculateTotalDuration();
        });
    }

    protected $casts = [
        'is_published' => 'boolean',
        'price' => 'decimal:2',
        'total_sales' => 'integer',
        'total_duration' => 'integer',
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
     * Get reviews (rating & comment) untuk class ini
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(ClassReview::class, 'class_id');
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

    /**
     * Recalculate total duration from all chapters
     */
    public function recalculateTotalDuration()
    {
        if (!$this->id) {
            return 0;
        }
        
        $total = (int) (DB::table('chapters')
            ->where('class_id', $this->id)
            ->sum('total_duration') ?? 0);
        
        // Update tanpa trigger event untuk menghindari infinite loop
        if ($this->total_duration != $total) {
            DB::table('classes')
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
     * Get total duration in minutes (calculated from all modules)
     */
    public function getTotalDurationMinutesAttribute()
    {
        if (!$this->id) {
            return 0;
        }
        
        $total = 0;
        
        // Priority 1: Use loaded chapters->modules (most accurate, already filtered by controller)
        if ($this->relationLoaded('chapters') && $this->chapters->count() > 0) {
            foreach ($this->chapters as $chapter) {
                if ($chapter->relationLoaded('modules')) {
                    foreach ($chapter->modules as $module) {
                        $duration = (int) ($module->estimated_duration ?? 0);
                        $total += $duration;
                    }
                }
            }
            // Return total from chapters->modules if we have chapters loaded
            return $total;
        }
        
        // Priority 2: Use loaded modules relationship (already filtered by controller)
        if ($this->relationLoaded('modules') && $this->modules->count() > 0) {
            foreach ($this->modules as $module) {
                $duration = (int) ($module->estimated_duration ?? 0);
                $total += $duration;
            }
            return $total;
        }
        
        // Priority 3: Fallback to direct query - calculate all modules for accurate total duration
        // Note: Controller now loads all modules, so this should rarely be used
        return (int) ($this->modules()->sum('estimated_duration') ?? 0);
    }

    /**
     * Get formatted total duration
     */
    public function getFormattedTotalDurationAttribute()
    {
        $minutes = $this->total_duration_minutes;
        
        if ($minutes <= 0) {
            return '0 menit';
        }
        
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
     * Get average rating from reviews
     */
    public function getAverageRatingAttribute()
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    /**
     * Get total reviews count
     */
    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }
}
