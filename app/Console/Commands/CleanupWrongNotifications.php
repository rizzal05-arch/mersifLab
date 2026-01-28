<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\ClassModel;
use App\Models\User;
use Illuminate\Console\Command;

class CleanupWrongNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:cleanup-wrong-course-suspended';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup course_suspended notifications that were incorrectly sent to admin instead of teacher';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup of wrong course_suspended notifications...');

        // Find all course_suspended notifications
        $wrongNotifications = Notification::where('type', 'course_suspended')
            ->orWhere('type', 'course_activated')
            ->get();

        $deletedCount = 0;
        $fixedCount = 0;

        foreach ($wrongNotifications as $notification) {
            $user = $notification->user;
            
            // If notification was sent to admin (wrong recipient)
            if ($user && $user->role === 'admin') {
                // Try to find the correct teacher from the notifiable (course/class)
                $notifiable = $notification->notifiable;
                
                if ($notifiable) {
                    $teacher = null;
                    
                    // If notifiable is ClassModel
                    if ($notifiable instanceof ClassModel) {
                        $teacher = $notifiable->teacher;
                    }
                    // If notifiable is Course (might not have teacher, skip)
                    elseif (get_class($notifiable) === 'App\Models\Course') {
                        // Course model doesn't have teacher, try to find via ClassModel
                        $classModel = ClassModel::find($notifiable->id);
                        if ($classModel) {
                            $teacher = $classModel->teacher;
                        }
                    }
                    
                    if ($teacher) {
                        // Check if correct notification already exists for teacher
                        $existingNotification = Notification::where('user_id', $teacher->id)
                            ->where('type', $notification->type)
                            ->where('notifiable_type', $notification->notifiable_type)
                            ->where('notifiable_id', $notification->notifiable_id)
                            ->first();
                        
                        if (!$existingNotification) {
                            // Create correct notification for teacher
                            Notification::create([
                                'user_id' => $teacher->id,
                                'type' => $notification->type,
                                'title' => $notification->title,
                                'message' => $notification->message,
                                'notifiable_type' => $notification->notifiable_type,
                                'notifiable_id' => $notification->notifiable_id,
                                'is_read' => false,
                            ]);
                            $fixedCount++;
                        }
                    }
                    
                    // Delete the wrong notification (sent to admin)
                    $notification->delete();
                    $deletedCount++;
                } else {
                    // If notifiable not found, just delete the wrong notification
                    $notification->delete();
                    $deletedCount++;
                }
            }
        }

        $this->info("Cleanup completed!");
        $this->info("Deleted {$deletedCount} wrong notifications (sent to admin)");
        $this->info("Created {$fixedCount} correct notifications (sent to teacher)");

        return Command::SUCCESS;
    }
}
