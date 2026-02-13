<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\Notification;
use App\Models\Purchase;
use App\Http\Controllers\CertificateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class EnrollmentController extends Controller
{
    /**
     * Enroll student ke course (simulasi pembelian)
     */
    public function enroll(Request $request, $classId)
    {
        $user = auth()->user();
        
        if (!$user || !$user->isStudent()) {
            return redirect()->back()->with('error', 'Hanya student yang bisa enroll ke course.');
        }

        $class = ClassModel::where('id', $classId)
            ->where('is_published', true)
            ->firstOrFail();

        // Check if already enrolled
        $alreadyEnrolled = DB::table('class_student')
            ->where('class_id', $class->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyEnrolled) {
            return redirect()->route('course.detail', $class->id)
                ->with('info', 'Anda sudah terdaftar di course ini.');
        }

        // Simulasi pembelian - langsung enroll tanpa payment gateway
        DB::table('class_student')->insert([
            'class_id' => $class->id,
            'user_id' => $user->id,
            'enrolled_at' => now(),
            'progress' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Remove from cart after successful enrollment
        $cart = Session::get('cart', []);
        $cart = array_values(array_filter($cart, function($id) use ($classId) {
            return $id != $classId;
        }));
        Session::put('cart', $cart);

        // Create purchase record untuk tracking invoice
        Purchase::create([
            'purchase_code' => Purchase::generatePurchaseCode(),
            'user_id' => $user->id,
            'class_id' => $class->id,
            'amount' => $class->price ?? 0,
            'status' => 'success',
            'payment_method' => 'enrollment',
            'payment_provider' => 'system',
            'paid_at' => now(),
        ]);

        // Notifikasi ke teacher bahwa ada siswa yang membeli kelasnya (jika teacher mengaktifkan notifikasi)
        if ($class->teacher && $class->teacher->wantsNotification('student_enrolled')) {
            Notification::create([
                'user_id' => $class->teacher->id,
                'type' => 'student_enrolled',
                'title' => 'Siswa Baru Mendaftar',
                'message' => "Siswa '{$user->name}' telah mendaftar ke course '{$class->name}' Anda.",
                'notifiable_type' => ClassModel::class,
                'notifiable_id' => $class->id,
            ]);
        }

        $user->logActivity('purchased', "Membeli kelas: {$class->name}");

        return redirect()->route('course.detail', $class->id)
            ->with('success', 'Berhasil mendaftar ke course! Selamat belajar.');
    }

    /**
     * Mark module as complete
     */
    public function markComplete(Request $request, $classId, $moduleId)
    {
        $user = auth()->user();
        
        if (!$user || !$user->isStudent()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Check enrollment OR subscription
        $isEnrolled = DB::table('class_student')
            ->where('class_id', $classId)
            ->where('user_id', $user->id)
            ->exists();

        // Check subscription access if not enrolled
        $hasSubscriptionAccess = false;
        if (!$isEnrolled) {
            $course = ClassModel::find($classId);
            if ($course && $user->canAccessViaPlanTier($course->price_tier ?? 'standard')) {
                $hasSubscriptionAccess = true;
                
                // Auto-enroll subscription user ke class_student untuk tracking progress
                $existingEnrollment = DB::table('class_student')
                    ->where('class_id', $classId)
                    ->where('user_id', $user->id)
                    ->first();
                
                if (!$existingEnrollment) {
                    DB::table('class_student')->insert([
                        'class_id' => $classId,
                        'user_id' => $user->id,
                        'enrolled_at' => now(),
                        'progress' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $isEnrolled = true;
                } else {
                    $isEnrolled = true;
                }
            }
        }

        if (!$isEnrolled && !$hasSubscriptionAccess) {
            return response()->json(['success' => false, 'message' => 'Anda belum terdaftar di course ini atau tidak memiliki akses subscription yang sesuai.'], 403);
        }

        // Check if already completed
        $alreadyCompleted = DB::table('module_completions')
            ->where('module_id', $moduleId)
            ->where('user_id', $user->id)
            ->exists();

        if (!$alreadyCompleted) {
            // Mark module as complete
            DB::table('module_completions')->insert([
                'module_id' => $moduleId,
                'user_id' => $user->id,
                'class_id' => $classId,
                'completed_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Calculate progress - only count approved and published modules
        $totalModules = DB::table('modules')
            ->join('chapters', 'modules.chapter_id', '=', 'chapters.id')
            ->where('chapters.class_id', $classId)
            ->where('modules.is_published', true)
            ->where('modules.approval_status', 'approved')
            ->count();

        // Count only completed modules that are approved and published
        $completedModules = DB::table('module_completions')
            ->join('modules', 'module_completions.module_id', '=', 'modules.id')
            ->join('chapters', 'modules.chapter_id', '=', 'chapters.id')
            ->where('module_completions.class_id', $classId)
            ->where('module_completions.user_id', $user->id)
            ->where('modules.is_published', true)
            ->where('modules.approval_status', 'approved')
            ->count();

        $progress = $totalModules > 0 ? round(($completedModules / $totalModules) * 100, 2) : 0;

        // Update progress in class_student (untuk enrolled users dan subscription users)
        if ($isEnrolled) {
            DB::table('class_student')
                ->where('class_id', $classId)
                ->where('user_id', $user->id)
                ->update(['progress' => $progress, 'updated_at' => now()]);
        }

        // Check if course is completed (hanya untuk enrolled users, subscription users tidak perlu sertifikat)
        $isCourseCompleted = $progress >= 100 && $isEnrolled;
        if ($isCourseCompleted) {
            DB::table('class_student')
                ->where('class_id', $classId)
                ->where('user_id', $user->id)
                ->update(['completed_at' => now()]);

            // Auto-generate certificate (hanya untuk enrolled users)
            $certificateController = new CertificateController();
            $certificate = $certificateController->generateCertificate($user->id, $classId);

            // Notifikasi ke teacher bahwa siswa telah menyelesaikan kelasnya (jika teacher mengaktifkan notifikasi)
            $class = ClassModel::find($classId);
            if ($class && $class->teacher && $class->teacher->wantsNotification('course_completed')) {
                Notification::create([
                    'user_id' => $class->teacher->id,
                    'type' => 'course_completed',
                    'title' => 'Siswa Menyelesaikan Course',
                    'message' => "Siswa '{$user->name}' telah menyelesaikan course '{$class->name}' Anda dengan progress 100%.",
                    'notifiable_type' => ClassModel::class,
                    'notifiable_id' => $class->id,
                ]);
            }
        }
        
        // Return success response
        $response = [
            'success' => true, 
            'message' => 'Module berhasil ditandai sebagai selesai!',
            'progress' => $progress,
            'completed' => $completedModules,
            'total' => $totalModules,
            'courseCompleted' => $isCourseCompleted
        ];
        
        // Add course completion message if course is completed
        if ($isCourseCompleted) {
            $response['message'] = 'Selamat! Anda telah menyelesaikan course ini!';
        }
        
        return response()->json($response);
    }

    /**
     * Mark all modules in course as complete
     */
    public function completeAllModules(Request $request, $classId)
    {
        $user = auth()->user();
        
        if (!$user || !$user->isStudent()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Check enrollment OR subscription
        $isEnrolled = DB::table('class_student')
            ->where('class_id', $classId)
            ->where('user_id', $user->id)
            ->exists();

        // Check subscription access if not enrolled
        $hasSubscriptionAccess = false;
        if (!$isEnrolled) {
            $course = ClassModel::find($classId);
            if ($course && $user->canAccessViaPlanTier($course->price_tier ?? 'standard')) {
                $hasSubscriptionAccess = true;
                
                // Auto-enroll subscription user ke class_student untuk tracking progress
                $existingEnrollment = DB::table('class_student')
                    ->where('class_id', $classId)
                    ->where('user_id', $user->id)
                    ->first();
                
                if (!$existingEnrollment) {
                    DB::table('class_student')->insert([
                        'class_id' => $classId,
                        'user_id' => $user->id,
                        'enrolled_at' => now(),
                        'progress' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $isEnrolled = true;
                } else {
                    $isEnrolled = true;
                }
            }
        }

        if (!$isEnrolled && !$hasSubscriptionAccess) {
            return response()->json(['success' => false, 'message' => 'Anda belum terdaftar di course ini atau tidak memiliki akses subscription yang sesuai.'], 403);
        }

        // Get all approved and published modules in this course
        $modules = DB::table('modules')
            ->join('chapters', 'modules.chapter_id', '=', 'chapters.id')
            ->where('chapters.class_id', $classId)
            ->where('modules.is_published', true)
            ->where('modules.approval_status', 'approved')
            ->pluck('modules.id');

        // Mark all modules as complete
        foreach ($modules as $moduleId) {
            $alreadyCompleted = DB::table('module_completions')
                ->where('module_id', $moduleId)
                ->where('user_id', $user->id)
                ->exists();

            if (!$alreadyCompleted) {
                DB::table('module_completions')->insert([
                    'module_id' => $moduleId,
                    'user_id' => $user->id,
                    'class_id' => $classId,
                    'completed_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Calculate progress
        $totalModules = $modules->count();
        $completedModules = $totalModules; // Semua module sudah completed

        $progress = 100; // Course completion

        // Update progress in class_student (untuk enrolled users dan subscription users)
        if ($isEnrolled) {
            DB::table('class_student')
                ->where('class_id', $classId)
                ->where('user_id', $user->id)
                ->update(['progress' => $progress, 'completed_at' => now(), 'updated_at' => now()]);

            // Auto-generate certificate (hanya untuk enrolled users)
            $certificateController = new CertificateController();
            $certificate = $certificateController->generateCertificate($user->id, $classId);
        }

        // Notifikasi ke teacher (hanya untuk enrolled users)
        if ($isEnrolled) {
            $class = ClassModel::find($classId);
            if ($class && $class->teacher && $class->teacher->wantsNotification('course_completed')) {
                Notification::create([
                    'user_id' => $class->teacher->id,
                    'type' => 'course_completed',
                    'title' => 'Siswa Menyelesaikan Course',
                    'message' => "Siswa '{$user->name}' telah menyelesaikan course '{$class->name}' Anda dengan progress 100%.",
                    'notifiable_type' => ClassModel::class,
                    'notifiable_id' => $class->id,
                ]);
            }
        }

        // Return success response
        return response()->json([
            'success' => true, 
            'message' => 'Selamat! Anda telah menyelesaikan semua module dalam course ini!',
            'progress' => $progress,
            'completed' => $completedModules,
            'total' => $totalModules,
            'courseCompleted' => true
        ]);
    }
}