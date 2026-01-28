<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

/**
 * Module Model (Konten Pembelajaran)
 * 
 * Types: text, document (PDF), video
 * 
 * Relasi:
 * - Chapter: BelongsTo Chapter (1 chapter)
 */
class Module extends Model
{
    protected $fillable = [
        'chapter_id',
        'title',
        'type',
        'content',
        'file_path',
        'file_name',
        'video_url',
        'duration',
        'order',
        'is_published',
        'view_count',
        'mime_type',
        'estimated_duration',
        'file_size',
        'approval_status',
        'admin_feedback',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'duration' => 'integer',
        'file_size' => 'integer',
        'view_count' => 'integer',
        'estimated_duration' => 'integer',
    ];

    const APPROVAL_PENDING = 'pending_approval';
    const APPROVAL_APPROVED = 'approved';
    const APPROVAL_REJECTED = 'rejected';

    const TYPE_TEXT = 'text';
    const TYPE_DOCUMENT = 'document';
    const TYPE_VIDEO = 'video';

    const ALLOWED_TYPES = [self::TYPE_TEXT, self::TYPE_DOCUMENT, self::TYPE_VIDEO];

    /**
     * Boot method untuk auto-calculate duration
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($module) {
            $module->updateChapterDuration();
        });
        
        static::updated(function ($module) {
            // Hanya update jika estimated_duration berubah atau module baru dibuat
            if ($module->isDirty('estimated_duration') || $module->wasRecentlyCreated) {
                $module->updateChapterDuration();
            }
        });
        
        static::deleted(function ($module) {
            $module->updateChapterDuration();
        });
    }

    /**
     * Update chapter total duration
     */
    protected function updateChapterDuration()
    {
        if (!$this->chapter_id) {
            return $this->estimated_duration ?? 0;
        }
        
        $chapter = \App\Models\Chapter::find($this->chapter_id);
        if ($chapter) {
            $chapter->recalculateTotalDuration();
        }
        
        return $this->estimated_duration ?? 0;
    }

    /**
     * Get chapter yang punya module ini
     */
    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class, 'chapter_id');
    }

    /**
     * Get class dari chapter
     */
    public function class()
    {
        return $this->chapter->class();
    }

    /**
     * Get teacher dari class
     */
    public function teacher()
    {
        return $this->class->teacher();
    }

    /**
     * Scope: hanya published modules
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope: hanya module yang sudah disetujui admin (bisa ditayang & diakses)
     */
    public function scopeApproved($query)
    {
        return $query->where('approval_status', self::APPROVAL_APPROVED);
    }

    /**
     * Cek apakah module sudah disetujui dan boleh diakses/ditayangkan.
     */
    public function isApproved(): bool
    {
        return ($this->approval_status ?? '') === self::APPROVAL_APPROVED;
    }

    /**
     * Scope: filter by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Check apakah module bisa di-edit oleh user
     */
    public function canBeEditedBy(User $user): bool
    {
        return $this->chapter->canBeEditedBy($user);
    }

    /**
     * Check apakah module bisa di-view oleh user
     */
    public function canBeViewedBy(User $user): bool
    {
        // Admin & teacher (pemilik) selalu bisa view
        if ($user->isAdmin() || $this->chapter->canBeEditedBy($user)) {
            return true;
        }

        // Student hanya bisa view published modules
        if ($user->isStudent() && $this->is_published) {
            return true;
        }

        return false;
    }

    /**
     * Format file size ke readable format
     */
    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) return null;

        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = $this->file_size;
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Get module type label
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            self::TYPE_TEXT => 'Text',
            self::TYPE_DOCUMENT => 'Document (PDF)',
            self::TYPE_VIDEO => 'Video',
            default => 'Unknown',
        };
    }

    /**
     * Get module icon
     */
    public function getTypeIconAttribute()
    {
        return match($this->type) {
            self::TYPE_TEXT => '📝',
            self::TYPE_DOCUMENT => '📄',
            self::TYPE_VIDEO => '🎥',
            default => '📦',
        };
    }

    /**
     * Increment view count
     */
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    /**
     * Get file icon class based on file extension or type
     * 
     * @return string FontAwesome icon class with color
     */
    public function getFileIconAttribute(): string
    {
        // Check file extension from file_path or file_name
        $extension = null;
        if ($this->file_path) {
            $extension = strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));
        } elseif ($this->file_name) {
            $extension = strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));
        }

        // If we have video_url, it's a YouTube video
        if ($this->type === self::TYPE_VIDEO && $this->video_url) {
            return 'fas fa-play-circle text-danger';
        }

        // Check by extension first (more accurate)
        if ($extension) {
            // PDF files
            if (in_array($extension, ['pdf'])) {
                return 'fas fa-file-pdf text-danger';
            }
            // Word documents
            if (in_array($extension, ['doc', 'docx'])) {
                return 'fas fa-file-word text-primary';
            }
            // Excel files
            if (in_array($extension, ['xls', 'xlsx'])) {
                return 'fas fa-file-excel text-success';
            }
            // Video files
            if (in_array($extension, ['mp4', 'avi', 'mov', 'mkv', 'wmv', 'flv', 'webm'])) {
                return 'fas fa-play-circle text-danger';
            }
        }

        // Fallback to type if no extension found
        switch ($this->type) {
            case self::TYPE_TEXT:
                return 'fas fa-file-alt text-secondary';
            case self::TYPE_DOCUMENT:
                return 'fas fa-file-pdf text-danger';
            case self::TYPE_VIDEO:
                return 'fas fa-video text-info';
            default:
                return 'fas fa-file-alt text-secondary';
        }
    }
}

