<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ClassModel;
use App\Models\Chapter;
use App\Models\Module;
use App\Models\Course;
use App\Models\Materi;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        // Hitung total kelas, chapter, dan modul
        $totalKelas = ClassModel::count();
        $totalChapter = Chapter::count();
        $totalModul = Module::count();
        $totalUsers = User::count();
        
        $activeSubscribers = User::where('is_subscriber', true)
            ->where(function ($query) {
                $query->whereNull('subscription_expires_at')
                    ->orWhere('subscription_expires_at', '>', now());
            })
            ->count();
        
        // Ambil semua kelas dengan relasi teacher dan counts
        // Include category, price (default 0), dan modules count untuk Content Info
        $classes = ClassModel::with('teacher')
            ->withCount(['chapters', 'modules'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Get unique categories for filter dropdown
        $categories = ClassModel::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category')
            ->mapWithKeys(function ($category) {
                return [$category => ClassModel::CATEGORIES[$category] ?? ucfirst($category)];
            });

        return view('admin.dashboard', compact(
            'totalKelas', 
            'totalChapter', 
            'totalModul', 
            'totalUsers', 
            'activeSubscribers', 
            'classes',
            'categories'
        ));
    }

    /**
     * Show all users with subscription status
     */
    public function users()
    {
        $users = User::all();
        return view('admin.users.index', ['users' => $users]);
    }

    /**
     * Subscribe user to access materials
     */
    public function subscribe(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'days' => 'required|integer|min:1|max:365'
        ]);

        $days = (int)$request->days;

        $user->update([
            'is_subscriber' => true,
            'subscription_expires_at' => now()->addDays($days)
        ]);

        return redirect('/admin/users')->with('success', "User {$user->name} telah diaktifkan sebagai subscriber selama {$days} hari");
    }

    /**
     * Unsubscribe user
     */
    public function unsubscribe($id)
    {
        $user = User::findOrFail($id);
        
        $user->update([
            'is_subscriber' => false,
            'subscription_expires_at' => null
        ]);

        return redirect('/admin/users')->with('success', "Langganan user {$user->name} telah dihapus");
    }

    /**
     * Toggle course status (Active/Inactive)
     * When suspending, simulate sending notification to teacher
     * Handles both ClassModel and Course model
     */
    public function toggleStatus(Request $request, $id)
    {
        // Try Course model first (new structure)
        $course = Course::find($id);
        
        if ($course) {
            // Toggle status for Course model
            $wasActive = $course->status === 'active';
            $course->status = $course->status === 'active' ? 'inactive' : 'active';
            $course->save();

            // Send notification to teacher (not admin) about course status change
            // Note: Course model might not have teacher relationship, so we check ClassModel instead
            // If Course model is used, we need to find the associated ClassModel
            $classModel = ClassModel::where('id', $id)->first();
            
            if ($classModel && $classModel->teacher) {
                if ($course->status === 'inactive' && $wasActive) {
                    // Course suspended - send notification to teacher (NOT admin)
                    Notification::create([
                        'user_id' => $classModel->teacher_id,
                        'type' => 'course_suspended',
                        'title' => 'Course Dinonaktifkan',
                        'message' => "Course Anda '{$course->title}' telah dinonaktifkan oleh admin. Course ini tidak akan terlihat oleh student dan teacher lainnya.",
                        'notifiable_type' => Course::class,
                        'notifiable_id' => $course->id,
                    ]);
                } elseif ($course->status === 'active' && !$wasActive) {
                    // Course activated - send notification to teacher (NOT admin)
                    Notification::create([
                        'user_id' => $classModel->teacher_id,
                        'type' => 'course_activated',
                        'title' => 'Course Diaktifkan',
                        'message' => "Course Anda '{$course->title}' telah diaktifkan kembali oleh admin.",
                        'notifiable_type' => Course::class,
                        'notifiable_id' => $course->id,
                    ]);
                }
            }

            $status = $course->status === 'active' ? 'activated' : 'suspended';
            return redirect()->route('admin.dashboard')->with('success', "Course '{$course->title}' has been {$status}.");
        }

        // Fallback to ClassModel (old structure)
        $class = ClassModel::findOrFail($id);
        
        // Toggle is_published status
        $wasPublished = $class->is_published;
        $class->is_published = !$class->is_published;
        $class->save();

        // Send notification to teacher
        if ($class->teacher) {
            if (!$class->is_published && $wasPublished) {
                // Course suspended
                Notification::create([
                    'user_id' => $class->teacher_id,
                    'type' => 'course_suspended',
                    'title' => 'Course Dinonaktifkan',
                    'message' => "Course Anda '{$class->name}' telah dinonaktifkan oleh admin. Course ini tidak akan terlihat oleh student dan teacher lainnya.",
                    'notifiable_type' => ClassModel::class,
                    'notifiable_id' => $class->id,
                ]);
            } elseif ($class->is_published && !$wasPublished) {
                // Course activated
                Notification::create([
                    'user_id' => $class->teacher_id,
                    'type' => 'course_activated',
                    'title' => 'Course Diaktifkan',
                    'message' => "Course Anda '{$class->name}' telah diaktifkan kembali oleh admin.",
                    'notifiable_type' => ClassModel::class,
                    'notifiable_id' => $class->id,
                ]);
            }
        }

        $status = $class->is_published ? 'activated' : 'suspended';
        
        // Redirect to previous page or dashboard
        $redirectTo = $request->input('redirect_to', route('admin.dashboard'));
        return redirect($redirectTo)->with('success', "Course '{$class->name}' has been {$status}.");
    }

    /**
     * Soft delete course
     */
    public function destroyCourse($id)
    {
        $class = ClassModel::findOrFail($id);
        $className = $class->name;
        $teacherId = $class->teacher_id;
        
        // Send notification to teacher before deleting
        if ($teacherId) {
            Notification::create([
                'user_id' => $teacherId,
                'type' => 'course_deleted',
                'title' => 'Course Dihapus',
                'message' => "Course Anda '{$className}' telah dihapus oleh admin. Course ini tidak akan muncul lagi di sistem.",
                'notifiable_type' => ClassModel::class,
                'notifiable_id' => $class->id,
            ]);
        }
        
        // Soft delete (if using soft deletes) or hard delete
        $class->delete();

        return redirect()->route('admin.dashboard')->with('success', "Course '{$className}' has been deleted successfully.");
    }

    /**
     * Toggle chapter status (Active/Inactive)
     */
    public function toggleChapterStatus($id)
    {
        $chapter = Chapter::findOrFail($id);
        $wasPublished = $chapter->is_published;
        
        // Toggle is_published status
        $chapter->is_published = !$chapter->is_published;
        $chapter->save();

        // Send notification to teacher
        if ($chapter->teacher()) {
            $teacher = $chapter->class->teacher;
            if ($teacher) {
                if (!$chapter->is_published && $wasPublished) {
                    // Chapter suspended
                    Notification::create([
                        'user_id' => $teacher->id,
                        'type' => 'chapter_suspended',
                        'title' => 'Chapter Dinonaktifkan',
                        'message' => "Chapter '{$chapter->title}' dalam course '{$chapter->class->name}' telah dinonaktifkan oleh admin.",
                        'notifiable_type' => Chapter::class,
                        'notifiable_id' => $chapter->id,
                    ]);
                } elseif ($chapter->is_published && !$wasPublished) {
                    // Chapter activated
                    Notification::create([
                        'user_id' => $teacher->id,
                        'type' => 'chapter_activated',
                        'title' => 'Chapter Diaktifkan',
                        'message' => "Chapter '{$chapter->title}' dalam course '{$chapter->class->name}' telah diaktifkan kembali oleh admin.",
                        'notifiable_type' => Chapter::class,
                        'notifiable_id' => $chapter->id,
                    ]);
                }
            }
        }

        $status = $chapter->is_published ? 'activated' : 'suspended';
        return redirect()->back()->with('success', "Chapter '{$chapter->title}' has been {$status}.");
    }

    /**
     * Delete chapter
     */
    public function destroyChapter($id)
    {
        $chapter = Chapter::findOrFail($id);
        $chapterTitle = $chapter->title;
        $courseId = $chapter->class_id;
        $course = $chapter->class;
        
        // Send notification to teacher
        if ($course && $course->teacher) {
            Notification::create([
                'user_id' => $course->teacher_id,
                'type' => 'chapter_deleted',
                'title' => 'Chapter Dihapus',
                'message' => "Chapter '{$chapterTitle}' dalam course '{$course->name}' telah dihapus oleh admin.",
                'notifiable_type' => Chapter::class,
                'notifiable_id' => $chapter->id,
            ]);
        }
        
        $chapter->delete();

        return redirect()->back()->with('success', "Chapter '{$chapterTitle}' has been deleted successfully.");
    }

    /**
     * Toggle module status (Active/Inactive)
     */
    public function toggleModuleStatus($id)
    {
        $module = Module::findOrFail($id);
        
        // Toggle is_published status
        $module->is_published = !$module->is_published;
        $module->save();

        $status = $module->is_published ? 'activated' : 'suspended';
        return redirect()->back()->with('success', "Module '{$module->title}' has been {$status}.");
    }

    /**
     * Delete module
     */
    public function destroyModule($id)
    {
        $module = Module::findOrFail($id);
        $moduleTitle = $module->title;
        $courseId = $module->chapter->class_id;
        
        // Delete file if exists
        if ($module->file_path && Storage::disk('private')->exists($module->file_path)) {
            Storage::disk('private')->delete($module->file_path);
        }
        
        $module->delete();

        return redirect()->route('admin.courses.show', $courseId)->with('success', "Module '{$moduleTitle}' has been deleted successfully.");
    }

    /**
     * Preview module file (PDF/Video/Text) - Admin Preview Mode
     * This is a READ-ONLY preview for Admin to check module content without student features
     */
    public function previewModule($id)
    {
        $user = auth()->user();
        
        // Ensure only Admin can access this
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Unauthorized. Only administrators can access this preview.');
        }

        $module = Module::with(['chapter.class'])->findOrFail($id);
        
        // Admin can preview any module regardless of approval status
        // No need to check enrollment, progress, or student role
        
        return view('admin.courses.preview-module', compact('module'));
    }

    /**
     * Preview Materi file (PDF/Video) for Course model
     */
    public function previewMateri($id)
    {
        $materi = Materi::findOrFail($id);
        
        if (!$materi->file_path || !file_exists(storage_path('app/private/' . $materi->file_path))) {
            abort(404, 'File not found');
        }

        $filePath = storage_path('app/private/' . $materi->file_path);
        $mimeType = $materi->type === 'pdf' ? 'application/pdf' : 'video/mp4';

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . ($materi->title ?? 'file') . '"',
        ]);
    }

    /**
     * Suspend/Hide Materi content
     */
    public function suspendMateri($id)
    {
        $materi = Materi::findOrFail($id);
        
        // For now, we'll delete the materi (soft delete if needed)
        // In a real scenario, you might want to add an 'is_suspended' or 'is_hidden' field
        $materiTitle = $materi->title;
        $courseId = $materi->course_id;
        
        // Delete file if exists
        if ($materi->file_path && file_exists(storage_path('app/private/' . $materi->file_path))) {
            unlink(storage_path('app/private/' . $materi->file_path));
        }
        
        $materi->delete();

        return redirect()->route('admin.courses.show', $courseId)->with('success', "Content '{$materiTitle}' has been suspended/hidden successfully.");
    }
}
