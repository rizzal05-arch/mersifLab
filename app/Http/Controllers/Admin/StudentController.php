<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Purchase;
use App\Models\SubscriptionPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    /**
     * Display a listing of students (role=student).
     */
    public function index()
    {
        $students = User::where('role', 'student')
            ->withCount(['enrolledClasses', 'subscriptionPurchases' => function($query) {
                $query->where('status', 'pending');
            }])
            ->orderBy('name')
            ->get()
            ->map(function ($student) {
                $avatarUrl = null;
                if (!empty($student->avatar) && Storage::disk('public')->exists($student->avatar)) {
                    $avatarUrl = Storage::disk('public')->url($student->avatar);
                }
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                    'phone' => $student->phone,
                    'address' => $student->address,
                    'bio' => $student->bio,
                    'biography' => $student->biography,
                    'created_at' => $student->created_at,
                    'enrolled_classes_count' => $student->enrolled_classes_count,
                    'pending_subscriptions_count' => $student->subscription_purchases_count,
                    'last_login_at' => $student->last_login_at,
                    'is_online' => $student->is_online,
                    'is_subscriber' => (bool) ($student->is_subscriber ?? false),
                    'subscription_expires_at' => $student->subscription_expires_at,
                    'subscription_plan' => $student->subscription_plan,
                    'avatar' => $student->avatar,
                    'avatar_url' => $avatarUrl,
                ];
            });

        return view('admin.students.index', compact('students'));
    }

    /**
     * Display student details with purchase history
     */
    public function show($id)
    {
        $student = User::where('role', 'student')->findOrFail($id);

        // Get enrolled courses
        $enrolled = $student->enrolledClasses()
            ->with('teacher')
            ->withCount(['chapters', 'modules'])
            ->orderByPivot('enrolled_at', 'desc')
            ->get();

        // Get student's purchase history
        // Filter: hanya tampilkan purchases yang sudah punya invoice (user sudah klik "Bayar Sekarang")
        // atau purchases dengan status 'success' (langsung dibayar tanpa checkout)
        $purchases = Purchase::where('user_id', $id)
            ->with(['course.teacher'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->filter(function($purchase) {
                // Tampilkan jika status success (langsung dibayar)
                if ($purchase->status === 'success') {
                    return true;
                }
                
                // Untuk pending purchases, hanya tampilkan jika sudah punya invoice
                // (yang berarti user sudah klik "Bayar Sekarang")
                if ($purchase->status === 'pending') {
                    // Check if this purchase has an invoice (single purchase invoice)
                    $hasDirectInvoice = \App\Models\Invoice::where('invoiceable_id', $purchase->id)
                        ->where('invoiceable_type', Purchase::class)
                        ->exists();
                    
                    if ($hasDirectInvoice) {
                        return true;
                    }
                    
                    // Check if this purchase is included in a multiple purchases invoice
                    // (invoice dengan metadata purchase_ids yang berisi purchase ini)
                    $hasMultipleInvoice = \App\Models\Invoice::where('invoiceable_type', Purchase::class)
                        ->where('type', 'course')
                        ->whereJsonContains('metadata->purchase_ids', $purchase->id)
                        ->exists();
                    
                    return $hasMultipleInvoice;
                }
                
                // Tampilkan status lain (expired, cancelled, dll)
                return true;
            });

        // Get student's subscription purchases
        $subscriptionPurchases = SubscriptionPurchase::where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalModulesCompleted = (int) DB::table('module_completions')
            ->where('user_id', $student->id)
            ->count();

        $activities = ActivityLog::where('user_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $completions = DB::table('module_completions')
            ->where('user_id', $student->id)
            ->join('modules', 'module_completions.module_id', '=', 'modules.id')
            ->join('classes', 'module_completions.class_id', '=', 'classes.id')
            ->select(
                'module_completions.id',
                'module_completions.module_id',
                'module_completions.class_id',
                'module_completions.completed_at',
                'modules.title as module_title',
                'classes.name as class_name'
            )
            ->orderBy('module_completions.completed_at', 'desc')
            ->get();

        return view('admin.students.show', compact(
            'student',
            'enrolled',
            'purchases',
            'subscriptionPurchases',
            'totalModulesCompleted',
            'activities',
            'completions'
        ));
    }

    /**
     * Unlock course for student
     */
    public function unlockCourse($studentId, $purchaseId)
    {
        $purchase = Purchase::where('user_id', $studentId)->findOrFail($purchaseId);
        
        if ($purchase->status === 'success') {
            return back()->with('error', 'Course already unlocked');
        }

        $purchase->unlockCourse();

        // Mark related invoice as paid (if exists)
        $invoice = \App\Models\Invoice::where('invoiceable_id', $purchase->id)
            ->where('invoiceable_type', Purchase::class)
            ->first();
        
        if ($invoice && $invoice->status !== 'paid') {
            $invoice->markAsPaid('whatsapp', 'admin_approval');
        }

        return back()->with('success', 'Course unlocked successfully! Student has been notified.');
    }

    /**
     * Unlock all pending courses for a student
     */
    public function unlockAllCourses($studentId)
    {
        $student = User::where('role', 'student')->findOrFail($studentId);
        
        // Get all pending purchases for this student
        // Hanya ambil pending purchases yang sudah punya invoice (user sudah klik "Bayar Sekarang")
        $pendingPurchases = Purchase::where('user_id', $studentId)
            ->where('status', 'pending')
            ->get()
            ->filter(function($purchase) {
                // Check if this purchase has an invoice (single purchase invoice)
                $hasDirectInvoice = \App\Models\Invoice::where('invoiceable_id', $purchase->id)
                    ->where('invoiceable_type', Purchase::class)
                    ->exists();
                
                if ($hasDirectInvoice) {
                    return true;
                }
                
                // Check if this purchase is included in a multiple purchases invoice
                $hasMultipleInvoice = \App\Models\Invoice::where('invoiceable_type', Purchase::class)
                    ->where('type', 'course')
                    ->whereJsonContains('metadata->purchase_ids', $purchase->id)
                    ->exists();
                
                return $hasMultipleInvoice;
            });

        if ($pendingPurchases->isEmpty()) {
            return back()->with('info', 'No pending courses to unlock for this student.');
        }

        // Unlock all pending courses
        $unlockedCount = 0;
        foreach ($pendingPurchases as $purchase) {
            $purchase->unlockCourse();
            
            // Mark related invoice as paid (if exists)
            $invoice = \App\Models\Invoice::where('invoiceable_id', $purchase->id)
                ->where('invoiceable_type', Purchase::class)
                ->first();
            
            if ($invoice && $invoice->status !== 'paid') {
                $invoice->markAsPaid('whatsapp', 'admin_approval');
            }
            
            $unlockedCount++;
        }

        return back()->with('success', "Successfully unlocked {$unlockedCount} courses for {$student->name}. Student has been notified.");
    }

    /**
     * Approve subscription purchase for a student
     */
    public function approveSubscription($studentId, $subscriptionId)
    {
        $student = User::where('role', 'student')->findOrFail($studentId);
        $subscription = SubscriptionPurchase::where('user_id', $studentId)->findOrFail($subscriptionId);
        
        if ($subscription->status === 'success') {
            return back()->with('error', 'Subscription already approved.');
        }

        // Activate the subscription
        $subscription->activateSubscription();

        // Mark related invoice as paid (if exists)
        $invoice = \App\Models\Invoice::where('invoiceable_id', $subscription->id)
            ->where('invoiceable_type', SubscriptionPurchase::class)
            ->first();
        
        if ($invoice && $invoice->status !== 'paid') {
            $invoice->markAsPaid('whatsapp', 'admin_approval');
        }

        return back()->with('success', "Subscription {$subscription->formatted_plan} approved for {$student->name}. Student has been notified.");
    }

    /**
     * Reject subscription purchase for a student
     */
    public function rejectSubscription($studentId, $subscriptionId)
    {
        $student = User::where('role', 'student')->findOrFail($studentId);
        $subscription = SubscriptionPurchase::where('user_id', $studentId)->findOrFail($subscriptionId);
        
        if ($subscription->status === 'success') {
            return back()->with('error', 'Cannot reject approved subscription.');
        }

        $subscription->update([
            'status' => 'cancelled',
            'notes' => ($subscription->notes ?? '') . ' - Rejected by admin',
        ]);

        // Create notification for student
        if (\Illuminate\Support\Facades\Schema::hasTable('notifications')) {
            \App\Models\Notification::create([
                'user_id' => $student->id,
                'type' => 'subscription_rejected',
                'title' => 'Subscription Ditolak',
                'message' => "Pembelian subscription {$subscription->plan} Anda telah ditolak. Silakan hubungi admin untuk informasi lebih lanjut.",
                'notifiable_type' => SubscriptionPurchase::class,
                'notifiable_id' => $subscription->id,
                'is_read' => false,
            ]);
        }

        // Log activity
        auth()->user()->logActivity('subscription_rejected', "Rejected subscription purchase for {$student->name}: {$subscription->plan} - {$subscription->purchase_code}");

        return back()->with('success', "Subscription {$subscription->formatted_plan} rejected for {$student->name}. Student has been notified.");
    }


    /**
     * Admin tidak bisa create student.
     */
    public function create()
    {
        return redirect()->route('admin.students.index')
            ->with('info', 'Admin cannot add students manually. Students register through the registration page.');
    }

    /**
     * Store: redirect.
     */
    public function store(Request $request)
    {
        return redirect()->route('admin.students.index')
            ->with('info', 'Admin cannot add students manually. Students register through the registration page.');
    }

    /**
     * Admin tidak bisa edit student.
     */
    public function edit(string $id)
    {
        return redirect()->route('admin.students.show', $id)
            ->with('info', 'Admin tidak dapat mengedit profil student. Gunakan View untuk melihat detail.');
    }

    /**
     * Update: redirect.
     */
    public function update(Request $request, string $id)
    {
        return redirect()->route('admin.students.show', $id)
            ->with('info', 'Admin cannot edit student profile.');
    }

    /**
     * Delete student.
     */
    public function destroy(string $id)
    {
        $student = User::where('role', 'student')->findOrFail($id);
        $studentName = $student->name;
        
        $student->delete();

        auth()->user()->logActivity('student_deleted', "Menghapus student: {$studentName}");

        return redirect()->route('admin.students.index')
            ->with('success', "Student {$studentName} has been deleted successfully.");
    }

    /**
     * Display all student activities.
     */
    public function activities(string $id)
    {
        $student = User::where('role', 'student')->findOrFail($id);

        // Get all activity logs
        $activities = ActivityLog::where('user_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        // Get enrollments
        $enrollments = DB::table('class_user')
            ->where('user_id', $student->id)
            ->join('classes', 'class_user.class_id', '=', 'classes.id')
            ->select(
                'class_user.enrolled_at',
                'classes.name as class_name',
                'classes.id as class_id'
            )
            ->orderBy('class_user.enrolled_at', 'desc')
            ->get();

        // Get module completions
        $completions = DB::table('module_completions')
            ->where('user_id', $student->id)
            ->join('modules', 'module_completions.module_id', '=', 'modules.id')
            ->join('classes', 'module_completions.class_id', '=', 'classes.id')
            ->select(
                'module_completions.completed_at',
                'modules.title as module_title',
                'classes.name as class_name',
                'module_completions.module_id',
                'module_completions.class_id'
            )
            ->orderBy('module_completions.completed_at', 'desc')
            ->get();

        // Combine all activities
        $allActivities = collect();

        // Add activity logs
        foreach ($activities as $activity) {
            $allActivities->push([
                'type' => 'activity',
                'action' => $activity->action,
                'description' => $activity->description,
                'created_at' => $activity->created_at,
                'time_ago' => $activity->created_at->diffForHumans(),
            ]);
        }

        // Add enrollments
        foreach ($enrollments as $enrollment) {
            $allActivities->push([
                'type' => 'enrollment',
                'action' => 'Enrolled in Class',
                'description' => $enrollment->class_name,
                'created_at' => $enrollment->enrolled_at,
                'time_ago' => \Carbon\Carbon::parse($enrollment->enrolled_at)->diffForHumans(),
            ]);
        }

        // Add completions
        foreach ($completions as $completion) {
            $allActivities->push([
                'type' => 'completion',
                'action' => 'Completed Module',
                'description' => $completion->module_title . ' in ' . $completion->class_name,
                'created_at' => $completion->completed_at,
                'time_ago' => \Carbon\Carbon::parse($completion->completed_at)->diffForHumans(),
            ]);
        }

        // Sort all activities by date
        $allActivities = $allActivities->sortByDesc('created_at')->values();

        return view('admin.students.activities', compact('student', 'allActivities'));
    }

    /**
     * Toggle ban status for a student (admin action).
     */
    public function toggleBan(string $id)
    {
        $student = User::where('role', 'student')->findOrFail($id);
        $student->is_banned = !(bool) ($student->is_banned ?? false);
        $student->save();

        $status = $student->is_banned ? 'dibanned' : 'diaktifkan';
        auth()->user()->logActivity($student->is_banned ? 'student_banned' : 'student_unbanned', ($student->is_banned ? 'Mem-ban student: ' : 'Membatalkan ban student: ') . $student->name . ' (' . $student->email . ')');

        return redirect()->back()->with('success', "Student {$student->name} has been {$status} successfully.");
    }

    /**
     * Update subscription info for a student (admin action).
     */
    public function updateSubscription(Request $request, string $id)
    {
        $data = $request->validate([
            'plan' => 'nullable|string|in:standard,medium,premium,none',
            'expires_at' => 'nullable|date',
        ]);

        $student = User::where('role', 'student')->findOrFail($id);

        $plan = $data['plan'] ?? 'none';

        if ($plan === 'none') {
            $student->is_subscriber = false;
            $student->subscription_plan = null;
            $student->subscription_expires_at = null;
        } else {
            $student->is_subscriber = true;
            $student->subscription_plan = $plan;
            $student->subscription_expires_at = $data['expires_at'] ?? null;
        }

        $student->save();

        auth()->user()->logActivity('admin_update_subscription', sprintf('Updated subscription for user %s (%s): plan=%s, expires=%s', $student->name, $student->email, $student->subscription_plan ?? 'none', $student->subscription_expires_at ?? 'null'));

        return redirect()->back()->with('success', 'Subscription updated for ' . $student->name);
    }
}
