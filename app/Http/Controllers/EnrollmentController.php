<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // Check enrollment
        $isEnrolled = DB::table('class_student')
            ->where('class_id', $classId)
            ->where('user_id', $user->id)
            ->exists();

        if (!$isEnrolled) {
            return response()->json(['success' => false, 'message' => 'Anda belum terdaftar di course ini.'], 403);
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

        // Calculate progress
        $totalModules = DB::table('modules')
            ->join('chapters', 'modules.chapter_id', '=', 'chapters.id')
            ->where('chapters.class_id', $classId)
            ->where('modules.is_published', true)
            ->count();

        $completedModules = DB::table('module_completions')
            ->where('class_id', $classId)
            ->where('user_id', $user->id)
            ->count();

        $progress = $totalModules > 0 ? round(($completedModules / $totalModules) * 100, 2) : 0;

        // Update progress in class_student
        DB::table('class_student')
            ->where('class_id', $classId)
            ->where('user_id', $user->id)
            ->update(['progress' => $progress, 'updated_at' => now()]);

        // Check if course is completed
        $isCourseCompleted = $progress >= 100;
        if ($isCourseCompleted) {
            DB::table('class_student')
                ->where('class_id', $classId)
                ->where('user_id', $user->id)
                ->update(['completed_at' => now()]);
        }
        
        return response()->json([
            'success' => true, 
            'message' => 'Module marked as complete',
            'progress' => $progress,
            'completed' => $completedModules,
            'total' => $totalModules
        ]);
    }
}
