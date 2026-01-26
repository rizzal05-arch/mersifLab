<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\ClassModel;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ModuleViewController extends Controller
{
    /**
     * Show module dengan sidebar course navigation
     */
    public function show(Request $request, $classId, $chapterId, $moduleId)
    {
        $user = auth()->user();
        $isTeacherOrAdmin = $user && ($user->isTeacher() || $user->isAdmin());

        // Check enrollment first (student yang enrolled bisa lihat semua termasuk draft)
        $isEnrolled = false;
        $progress = 0;
        $completedModules = [];
        if ($user && $user->isStudent()) {
            $isEnrolled = DB::table('class_student')
                ->where('class_id', $classId)
                ->where('user_id', $user->id)
                ->exists();
            
            if ($isEnrolled) {
                $enrollment = DB::table('class_student')
                    ->where('class_id', $classId)
                    ->where('user_id', $user->id)
                    ->first();
                $progress = $enrollment->progress ?? 0;
                
                // Get completed modules
                $completedModules = DB::table('module_completions')
                    ->where('class_id', $classId)
                    ->where('user_id', $user->id)
                    ->pluck('module_id')
                    ->toArray();
            }
        }

        // Jika enrolled atau teacher/admin, bisa lihat class/chapter (termasuk draft)
        // Module: hanya yang sudah APPROVED yang boleh ditayang & diakses (teacher & student)
        $canViewAll = $isEnrolled || $isTeacherOrAdmin;

        // Load class - enrolled student bisa lihat meskipun belum published
        $classQuery = ClassModel::where('id', $classId);
        if (!$canViewAll) {
            $classQuery->where('is_published', true);
        }

        $class = $classQuery->with(['teacher', 'chapters' => function($query) use ($canViewAll, $user) {
            if (!$canViewAll) {
                $query->where('is_published', true);
            } elseif ($user && $user->isTeacher() && !$user->isAdmin()) {
                $query->whereHas('class', function($q) use ($user) {
                    $q->where('teacher_id', $user->id);
                });
            }
            $query->with(['modules' => function($q) use ($canViewAll) {
                // Hanya module yang sudah disetujui admin yang ditayangkan & bisa diakses (teacher & student)
                $q->approved();
                if (!$canViewAll) {
                    $q->where('is_published', true);
                }
                $q->orderBy('order');
            }])->orderBy('order');
        }])->firstOrFail();

        // Check authorization: Teacher hanya bisa akses class mereka sendiri (kecuali admin)
        if ($isTeacherOrAdmin) {
            // Admin bisa akses semua
            if (!$user->isAdmin() && $class->teacher_id !== $user->id) {
                abort(403, 'Unauthorized. This class does not belong to you.');
            }
        }

        // Check if course is suspended and user is not the owner/admin
        if (!$class->is_published && $user && !$user->isAdmin() && $class->teacher_id !== $user->id) {
            abort(403, 'This course has been suspended and is not available.');
        }

        // Student yang belum enrolled tidak bisa akses module
        if (!$isEnrolled && !$isTeacherOrAdmin && !$class->is_published) {
            abort(403, 'You must enroll in this course to access the modules.');
        }

        // Get chapter
        $chapterQuery = $class->chapters()->where('id', $chapterId);
        if (!$canViewAll) {
            $chapterQuery->where('is_published', true);
        } elseif ($user && $user->isTeacher() && !$user->isAdmin() && $class->teacher_id === $user->id) {
            // Teacher can see their own chapters even if not published
        } else {
            // For others, check if chapter is published
            if (!$canViewAll) {
                $chapterQuery->where('is_published', true);
            }
        }
        $chapter = $chapterQuery->firstOrFail();
        
        // Check if chapter is suspended and user is not the owner/admin
        if (!$chapter->is_published && $user && !$user->isAdmin() && $class->teacher_id !== $user->id) {
            abort(403, 'This chapter has been suspended and is not available.');
        }

        // Get module - hanya yang sudah approved boleh diakses (teacher & student)
        $moduleQuery = $chapter->modules()->approved()->where('id', $moduleId);
        if (!$canViewAll) {
            $moduleQuery->where('is_published', true);
        }
        $module = $moduleQuery->first();

        if (!$module) {
            // Check if module exists but not approved
            $moduleExists = $chapter->modules()->where('id', $moduleId)->first();
            if ($moduleExists && !$moduleExists->isApproved()) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'error' => true,
                        'message' => 'Modul ini belum disetujui admin sehingga tidak dapat ditayangkan atau diakses. Silakan tunggu persetujuan.'
                    ], 403);
                }
                return redirect()
                    ->route('course.detail', $classId)
                    ->with('error', 'Modul ini belum disetujui admin sehingga tidak dapat ditayangkan atau diakses. Silakan tunggu persetujuan.');
            }
            abort(404, 'Module not found.');
        }

        // Increment view count jika enrolled atau teacher/admin
        if ($isEnrolled || $isTeacherOrAdmin) {
            $module->incrementViewCount();
        }

        // Get next and previous modules (hanya yang approved untuk navigation)
        $allModules = $class->chapters->flatMap(function($ch) {
            return $ch->modules->filter(function($m) {
                return $m->isApproved();
            });
        })->values();
        $currentIndex = $allModules->search(function($m) use ($moduleId) {
            return $m->id == $moduleId;
        });
        
        $previousModule = $currentIndex !== false && $currentIndex > 0 ? $allModules[$currentIndex - 1] : null;
        $nextModule = $currentIndex !== false && $currentIndex < $allModules->count() - 1 ? $allModules[$currentIndex + 1] : null;

        return view('module.show', compact('class', 'chapter', 'module', 'isEnrolled', 'progress', 'previousModule', 'nextModule', 'completedModules'));
    }

    /**
     * Serve PDF file from private storage
     */
    public function serveFile($classId, $chapterId, $moduleId)
    {
        $user = auth()->user();
        $isTeacherOrAdmin = $user && ($user->isTeacher() || $user->isAdmin());

        // Check enrollment
        $isEnrolled = false;
        if ($user && $user->isStudent()) {
            $isEnrolled = DB::table('class_student')
                ->where('class_id', $classId)
                ->where('user_id', $user->id)
                ->exists();
        }

        $canViewAll = $isEnrolled || $isTeacherOrAdmin;

        // Load class
        $class = ClassModel::findOrFail($classId);
        
        // Check authorization: Teacher hanya bisa akses class mereka sendiri (kecuali admin)
        if ($isTeacherOrAdmin) {
            if (!$user->isAdmin() && $class->teacher_id !== $user->id) {
                abort(403, 'Unauthorized. This class does not belong to you.');
            }
        }

        // Student yang belum enrolled tidak bisa akses module
        if (!$isEnrolled && !$isTeacherOrAdmin && !$class->is_published) {
            abort(403, 'You must enroll in this course to access the modules.');
        }

        // Load module
        $module = Module::where('id', $moduleId)
            ->where('chapter_id', $chapterId)
            ->firstOrFail();

        // Check if module is approved and accessible (same logic as show method)
        if (!$module->isApproved()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Modul ini belum disetujui admin sehingga tidak dapat ditayangkan atau diakses. Silakan tunggu persetujuan.'
                ], 403);
            }
            return redirect()
                ->route('course.detail', $classId)
                ->with('error', 'Modul ini belum disetujui admin sehingga tidak dapat ditayangkan atau diakses. Silakan tunggu persetujuan.');
        }

        // Check if user can view
        if (!$canViewAll && !$module->is_published) {
            abort(403, 'Anda tidak memiliki akses ke file ini.');
        }

        // Check if file exists
        if (!$module->file_path || !Storage::disk('private')->exists($module->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $filePath = Storage::disk('private')->path($module->file_path);
        $mimeType = $module->mime_type ?? Storage::disk('private')->mimeType($module->file_path);

        // Determine content disposition based on file type
        $isVideo = str_starts_with($mimeType, 'video/');
        $contentDisposition = $isVideo 
            ? 'inline; filename="' . ($module->file_name ?? 'video.mp4') . '"'
            : 'inline; filename="' . ($module->file_name ?? 'document.pdf') . '"';

        // Set headers untuk mencegah download dan caching
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => $contentDisposition,
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Accept-Ranges' => 'bytes', // Enable range requests for video streaming
        ]);
    }
}
