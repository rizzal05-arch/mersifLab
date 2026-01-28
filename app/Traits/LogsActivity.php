<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    /**
     * Boot the trait and register model event listeners
     */
    protected static function bootLogsActivity()
    {
        // Log when model is created
        static::created(function ($model) {
            // Only log if user is authenticated (skip system/seed operations)
            if (auth()->check()) {
                $model->logActivity('created', $model->getActivityDescription('created'));
            }
        });

        // Log when model is updated
        static::updated(function ($model) {
            // Only log if user is authenticated and there are actual changes
            if (auth()->check() && $model->isDirty()) {
                $changes = $model->getChanges();
                // Skip if only timestamps changed
                $significantChanges = array_filter($changes, function ($key) {
                    return !in_array($key, ['updated_at', 'created_at']);
                }, ARRAY_FILTER_USE_KEY);
                
                if (!empty($significantChanges)) {
                    $description = $model->getActivityDescription('updated', $significantChanges);
                    $model->logActivity('updated', $description, $significantChanges);
                }
            }
        });

        // Log when model is deleted (use deleting event to capture data before deletion)
        static::deleting(function ($model) {
            // Log before deletion (so we still have access to model data)
            if (auth()->check()) {
                $description = $model->getActivityDescription('deleted');
                // Store model data before deletion
                $modelData = [
                    'id' => $model->id,
                    'name' => $model->name ?? $model->title ?? $model->email ?? null,
                ];
                
                ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'deleted',
                    'description' => $description,
                    'subject_type' => get_class($model),
                    'subject_id' => $model->id,
                    'properties' => $modelData,
                ]);
            }
        });
    }

    /**
     * Log an activity for this model
     */
    public function logActivity(string $action, string $description, array $properties = []): void
    {
        // Prevent logging if no user is authenticated (system operations)
        if (!auth()->check()) {
            return;
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
            'subject_type' => get_class($this),
            'subject_id' => $this->id ?? null,
            'properties' => !empty($properties) ? $properties : null,
        ]);
    }

    /**
     * Get activity description based on action
     */
    protected function getActivityDescription(string $action, array $changes = []): string
    {
        $modelName = class_basename($this);
        
        // Get a display name for the model (try to use name, title, or id)
        $displayName = $this->getDisplayName();
        
        switch ($action) {
            case 'created':
                return "Created {$modelName}: {$displayName}";
            
            case 'updated':
                $changeSummary = $this->getChangeSummary($changes);
                return "Updated {$modelName}: {$displayName}" . ($changeSummary ? " ({$changeSummary})" : '');
            
            case 'deleted':
                return "Deleted {$modelName}: {$displayName}";
            
            default:
                return "{$action} {$modelName}: {$displayName}";
        }
    }

    /**
     * Get display name for the model
     */
    protected function getDisplayName(): string
    {
        // Try common name fields
        if (isset($this->name)) {
            return $this->name;
        }
        if (isset($this->title)) {
            return $this->title;
        }
        if (isset($this->email)) {
            return $this->email;
        }
        
        // Fallback to ID
        return "#{$this->id}";
    }

    /**
     * Get summary of changes for description
     */
    protected function getChangeSummary(array $changes): string
    {
        if (empty($changes)) {
            return '';
        }

        // Remove timestamps from changes
        $filteredChanges = array_filter($changes, function ($key) {
            return !in_array($key, ['updated_at', 'created_at']);
        }, ARRAY_FILTER_USE_KEY);

        if (empty($filteredChanges)) {
            return '';
        }

        // Get first few changed fields
        $changedFields = array_slice(array_keys($filteredChanges), 0, 3);
        return implode(', ', $changedFields);
    }
}
