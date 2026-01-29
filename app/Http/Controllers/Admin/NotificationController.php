<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Module;
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
            ->paginate(20);

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

        // Redirect berdasarkan type
        if ($notification->type === 'module_pending_approval' && $notification->notifiable_type === Module::class) {
            $module = Module::find($notification->notifiable_id);
            if ($module) {
                $course = $module->chapter->class;
                return redirect()
                    ->route('admin.courses.moderation', ['id' => $course->id, 'module_id' => $module->id])
                    ->with('info', 'Scroll to the module pending approval.');
            }
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
}
