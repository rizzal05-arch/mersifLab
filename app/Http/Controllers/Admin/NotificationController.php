<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Module;
use App\Models\Purchase;
use App\Models\TeacherApplication;
use App\Models\ClassModel;
use App\Models\User;
use App\Models\TeacherWithdrawal;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display all notifications for admin
     */
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Mark notification as read and redirect to appropriate page
     */
    public function show($id)
    {
        $notification = Notification::where('user_id', auth()->id())->findOrFail($id);
        
        // Mark as read
        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        // Handle teacher application notifications
        if ($notification->type === 'teacher_application' && $notification->notifiable_type === TeacherApplication::class) {
            $application = TeacherApplication::find($notification->notifiable_id);
            if ($application) {
                return redirect()
                    ->route('admin.teacher-applications.show', $application->id)
                    ->with('info', 'Viewing teacher application from notification.');
            }
        }

        // Handle course approval notifications
        if (($notification->type === 'course_approval_request' || $notification->type === 'course_reapproval_request') && $notification->notifiable_type === ClassModel::class) {
            $course = ClassModel::find($notification->notifiable_id);
            if ($course) {
                return redirect()
                    ->route('admin.courses.approval', $course->id)
                    ->with('info', 'Viewing course approval request from notification.');
            }
        }

        // Handle module pending approval notifications
        if ($notification->type === 'module_pending_approval' && $notification->notifiable_type === Module::class) {
            $module = Module::find($notification->notifiable_id);
            if ($module) {
                $course = $module->chapter->class;
                return redirect()
                    ->route('admin.courses.moderation', ['id' => $course->id, 'module_id' => $module->id])
                    ->with('info', 'Scroll to the module pending approval.');
            }
        }

        // Handle new purchase notifications
        if ($notification->type === 'new_purchase' && $notification->notifiable_type === Purchase::class) {
            $purchase = Purchase::find($notification->notifiable_id);
            if ($purchase) {
                return redirect()
                    ->route('admin.students.show', $purchase->user_id)
                    ->with('info', 'Viewing student purchase details from notification.');
            }
        }

        // Handle withdrawal request notifications
        if ($notification->type === 'withdrawal_request') {
            $teacherId = null;
            
            // First try: check if notification has notifiable_id and it's a withdrawal
            if (isset($notification->notifiable_id) && $notification->notifiable_type === TeacherWithdrawal::class) {
                $withdrawal = TeacherWithdrawal::find($notification->notifiable_id);
                if ($withdrawal) {
                    $teacherId = $withdrawal->teacher_id;
                }
            }
            
            // Second try: extract teacher info from notification message
            if (!$teacherId) {
                if (preg_match('/^(.+?)\s+mengajukan\s+penarikan/', $notification->message, $matches)) {
                    $teacherName = trim($matches[1]);
                    // Find teacher by name
                    $teacher = User::where('name', $teacherName)
                        ->where('role', 'teacher')
                        ->first();
                    if ($teacher) {
                        $teacherId = $teacher->id;
                    }
                }
            }
            
            // Third try: find recent withdrawal by amount and time
            if (!$teacherId) {
                if (preg_match('/Rp\s+([\d,.]+)/', $notification->message, $matches)) {
                    $amount = (float) str_replace(['.', ','], ['', '.'], $matches[1]);
                    $withdrawal = TeacherWithdrawal::where('amount', $amount)
                        ->where('created_at', '>=', $notification->created_at->subMinutes(5))
                        ->where('created_at', '<=', $notification->created_at->addMinutes(5))
                        ->first();
                    if ($withdrawal) {
                        $teacherId = $withdrawal->teacher_id;
                    }
                }
            }
            
            // If we found the teacher, redirect to their finance management
            if ($teacherId) {
                return redirect()
                    ->route('admin.finance.teacher', $teacherId)
                    ->with('info', 'Viewing teacher finance management from withdrawal request notification.');
            }
            
            // Fallback: redirect to finance dashboard
            return redirect()
                ->route('admin.finance.dashboard')
                ->with('info', 'Viewing finance dashboard from withdrawal request notification.');
        }

        // Default redirect ke index
        return redirect()->route('admin.notifications.index');
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', auth()->id())->findOrFail($id);
        $notification->markAsRead();
        
        return back()->with('success', 'Notification marked as read');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        return back()->with('success', 'All notifications marked as read');
    }

    /**
     * Unlock course for student
     */
    public function unlockCourse($purchaseId)
    {
        $purchase = Purchase::findOrFail($purchaseId);
        
        if ($purchase->status === 'success') {
            return back()->with('error', 'Course already unlocked');
        }

        $purchase->unlockCourse();

        return back()->with('success', 'Course unlocked successfully! Student has been notified.');
    }

    /**
     * Show purchase details
     */
    public function showPurchase($id)
    {
        $purchase = Purchase::with(['user', 'course.teacher'])->findOrFail($id);
        
        return view('admin.purchases.show', compact('purchase'));
    }
}
