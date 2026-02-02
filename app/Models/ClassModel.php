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
        'is_featured',
        'has_discount',
        'discount',
        'discount_starts_at',
        'discount_ends_at',
        'order',
        'status',
        'admin_feedback',
        'price',
        'total_sales',
        'image',
        'what_youll_learn',
        'requirement',
        'total_duration',
        'includes',
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
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
        'has_discount' => 'boolean',
        'discount' => 'decimal:2',
        'discount_starts_at' => 'datetime',
        'discount_ends_at' => 'datetime',
        'total_sales' => 'integer',
        'total_duration' => 'integer',
        'includes' => 'array',
    ];

    const CATEGORIES = [
        'ai' => 'Artificial Intelligence (AI)',
        'development' => 'Development',
        'marketing' => 'Marketing',
        'design' => 'Design',
        'photography' => 'Photography & Video',
    ];

    /**
     * Get formatted includes with icons
     */
    public function getFormattedIncludesAttribute()
    {
        if (!$this->includes || !is_array($this->includes)) {
            return [];
        }

        $includeOptions = [
            'video' => [
                'icon' => 'fas fa-video',
                'color' => 'text-primary',
                'text' => 'Video pembelajaran on-demand'
            ],
            'lifetime' => [
                'icon' => 'fas fa-infinity',
                'color' => 'text-success',
                'text' => 'Akses seumur hidup'
            ],
            'certificate' => [
                'icon' => 'fas fa-certificate',
                'color' => 'text-warning',
                'text' => 'Sertifikat penyelesaian'
            ],
            'ai' => [
                'icon' => 'fas fa-robot',
                'color' => 'text-primary',
                'text' => 'Tanya AI Assistant'
            ],
            'mobile' => [
                'icon' => 'fas fa-mobile-alt',
                'color' => 'text-success',
                'text' => 'Akses mobile & tablet'
            ]
        ];

        $formatted = [];
        foreach ($this->includes as $include) {
            if (isset($includeOptions[$include])) {
                $formatted[] = $includeOptions[$include];
            }
        }

        return $formatted;
    }
    public function getCategoryNameAttribute()
    {
        // First try to get from database categories
        try {
            $categories = self::getAvailableCategories();
            return $categories[$this->category] ?? 'Uncategorized';
        } catch (\Exception $e) {
            // Fallback to constant
            return self::CATEGORIES[$this->category] ?? 'Uncategorized';
        }
    }

    /**
     * Get all available categories (from database, fallback to constant)
     */
    public static function getAvailableCategories()
    {
        try {
            $dbCategories = \App\Models\Category::active()->ordered()->get();
            if ($dbCategories->count() > 0) {
                return $dbCategories->pluck('name', 'slug')->toArray();
            }
        } catch (\Exception $e) {
            // Fallback to constant if table doesn't exist yet
        }
        
        return self::CATEGORIES;
    }

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
     * Get discounted price (price after subtracting nominal discount)
     */
    public function getDiscountedPriceAttribute()
    {
        $price = $this->price ?? 0;
        if (!($this->has_discount && $this->discount && $price > 0)) {
            return $price;
        }

        // Check period: discount only active when now is between start and end (if provided)
        $now = \Carbon\Carbon::now();
        if ($this->discount_starts_at && $now->lessThan($this->discount_starts_at)) {
            // Not started yet
            return $price;
        }
        if ($this->discount_ends_at && $now->greaterThan($this->discount_ends_at)) {
            // Already ended
            return $price;
        }

        $discounted = $price - $this->discount;
        return $discounted > 0 ? round($discounted, 2) : 0.00;
    }

    /**
     * Get discount percentage (rounded integer) if available
     */
    public function getDiscountPercentageAttribute()
    {
        $price = $this->price ?? 0;
        $now = \Carbon\Carbon::now();
        if (!($this->has_discount && $this->discount && $price > 0)) {
            return 0;
        }
        // Only report percentage if currently active
        if ($this->discount_starts_at && $now->lessThan($this->discount_starts_at)) {
            return 0;
        }
        if ($this->discount_ends_at && $now->greaterThan($this->discount_ends_at)) {
            return 0;
        }

        return (int) round(($this->discount / $price) * 100);
    }

    /**
     * Get total reviews count
     */
    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }

    /**
     * Get total sales count (successful purchases only)
     */
    public function getTotalSalesCountAttribute()
    {
        return $this->purchases()->where('status', 'success')->count();
    }

    /**
     * Get purchases for this course
     */
    public function purchases(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Purchase::class, 'class_id');
    }
}
