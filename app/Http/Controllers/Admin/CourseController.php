<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Module;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ClassModel::with('teacher')
            ->withCount(['chapters', 'modules'])
            ->withCount(['purchases' => function($query) {
                $query->where('status', 'success');
            }])
            ->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('description', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhereHas('teacher', function($teacherQuery) use ($searchTerm) {
                      $teacherQuery->where('name', 'LIKE', '%' . $searchTerm . '%')
                                   ->orWhere('email', 'LIKE', '%' . $searchTerm . '%');
                  });
            });
        }

        // Filter by category
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $courses = $query->paginate(20); // Changed from 15 to 20 per page
        $courses->appends($request->query());

        // Hanya satu course yang boleh featured (pinned) - untuk disable tombol pin di course lain
        $featuredCourseId = ClassModel::where('is_featured', true)->value('id');

        return view('admin.courses.index', compact('courses', 'featuredCourseId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.courses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Implementation for storing course
        return redirect()->route('admin.courses.index')->with('success', 'Course created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('admin.courses.show', compact('id'));
    }

    /**
     * Preview course detail - Admin Preview Mode (READ-ONLY)
     * Admin dapat melihat course detail tanpa fitur student (enroll, progress, dll)
     */
    public function previewCourse(string $id)
    {
        $user = auth()->user();
        
        // Ensure only Admin can access this
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Unauthorized. Only administrators can access this preview.');
        }

        // Load course dengan semua data (termasuk yang belum approved)
        $course = ClassModel::with(['teacher', 'chapters' => function($query) {
                $query->orderBy('order');
            }, 'chapters.modules' => function($query) {
                $query->orderBy('order');
            }])
            ->withCount(['chapters', 'modules'])
            ->findOrFail($id);

        // Hitung stats untuk preview
        $course->students_count = DB::table('class_student')
            ->join('users', 'class_student.user_id', '=', 'users.id')
            ->where('class_student.class_id', $course->id)
            ->where('users.role', 'student')
            ->count();

        // Get reviews and rating stats
        $reviews = \App\Models\ClassReview::where('class_id', $course->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $ratingStats = [
            'total' => \App\Models\ClassReview::where('class_id', $course->id)->count(),
            'average' => \App\Models\ClassReview::where('class_id', $course->id)->avg('rating') ?? 0,
            'distribution' => []
        ];

        // Calculate rating distribution
        for ($i = 5; $i >= 1; $i--) {
            $count = \App\Models\ClassReview::where('class_id', $course->id)
                ->where('rating', $i)
                ->count();
            $ratingStats['distribution'][$i] = [
                'count' => $count,
                'percentage' => $ratingStats['total'] > 0 ? round(($count / $ratingStats['total']) * 100, 1) : 0
            ];
        }

        return view('admin.courses.preview-course', compact('course', 'reviews', 'ratingStats'));
    }

    /**
     * Display course moderation page with full course hierarchy
     */
    public function moderation(string $id, Request $request)
    {
        $course = ClassModel::with(['teacher', 'chapters' => function($query) {
                $query->orderBy('order');
            }, 'chapters.modules' => function($query) {
                $query->orderBy('order');
            }])
            ->withCount(['chapters', 'modules'])
            ->findOrFail($id);

        // Jika ada module_id di query, scroll ke module tersebut
        $moduleId = $request->get('module_id');

        return view('admin.courses.moderation', compact('course', 'moduleId'));
    }

    /**
     * Approve module (set approved, publish, kirim notifikasi ke teacher)
     */
    public function approveModule(string $id, Request $request)
    {
        $module = Module::findOrFail($id);
        $course = $module->chapter->class;
        $teacher = $course->teacher;

        $validated = $request->validate([
            'admin_feedback' => 'nullable|string|max:1000',
        ]);

        $module->update([
            'approval_status' => Module::APPROVAL_APPROVED,
            'is_published' => true,
            'admin_feedback' => $validated['admin_feedback'] ?? null,
        ]);

        auth()->user()->logActivity('module_approved', "Menyetujui modul: {$module->title} di course {$course->name}");

        // Pastikan chapter dan class duration ter-update setelah approve module
        $module->refresh();
        if ($module->chapter) {
            $module->chapter->recalculateTotalDuration();
            if ($module->chapter->class) {
                $module->chapter->class->recalculateTotalDuration();
            }
        }

        // Notifikasi ke teacher (jika teacher mengaktifkan notifikasi)
        if ($teacher && $teacher->wantsNotification('module_approved')) {
            Notification::create([
                'user_id' => $teacher->id,
                'type' => 'module_approved',
                'title' => 'Module Disetujui',
                'message' => "Module '{$module->title}' di course '{$course->name}' telah disetujui dan dipublish oleh admin.",
                'notifiable_type' => Module::class,
                'notifiable_id' => $module->id,
            ]);
        }

        // Notifikasi ke semua student yang sudah enroll di course ini (jika mereka mengaktifkan notifikasi)
        if ($module->is_published) {
            $enrolledStudents = DB::table('class_student')
                ->where('class_id', $course->id)
                ->join('users', 'class_student.user_id', '=', 'users.id')
                ->where('users.role', 'student')
                ->select('users.id')
                ->get();

            foreach ($enrolledStudents as $student) {
                $user = \App\Models\User::find($student->id);
                if ($user && $user->wantsNotification('new_module')) {
                    Notification::create([
                        'user_id' => $student->id,
                        'type' => 'new_module',
                        'title' => 'Module Baru Tersedia',
                        'message' => "Module baru '{$module->title}' telah ditambahkan ke course '{$course->name}' yang Anda ikuti.",
                        'notifiable_type' => Module::class,
                        'notifiable_id' => $module->id,
                    ]);
                }
            }
        }

        return redirect()
            ->route('admin.courses.moderation', ['id' => $course->id, 'module_id' => $module->id])
            ->with('success', "Module '{$module->title}' has been approved and published.");
    }

    /**
     * Reject module (set rejected, tidak publish, kirim notifikasi ke teacher)
     */
    public function rejectModule(string $id, Request $request)
    {
        $module = Module::findOrFail($id);
        $course = $module->chapter->class;
        $teacher = $course->teacher;

        $validated = $request->validate([
            'admin_feedback' => 'required|string|max:1000',
        ]);

        $module->update([
            'approval_status' => Module::APPROVAL_REJECTED,
            'is_published' => false,
            'admin_feedback' => $validated['admin_feedback'],
        ]);

        auth()->user()->logActivity('module_rejected', "Menolak modul: {$module->title} di course {$course->name}");

        // Pastikan chapter dan class duration ter-update setelah reject module
        $module->refresh();
        if ($module->chapter) {
            $module->chapter->recalculateTotalDuration();
            if ($module->chapter->class) {
                $module->chapter->class->recalculateTotalDuration();
            }
        }

        // Notifikasi ke teacher
        if ($teacher) {
            Notification::create([
                'user_id' => $teacher->id,
                'type' => 'module_rejected',
                'title' => 'Module Ditolak',
                'message' => "Module '{$module->title}' di course '{$course->name}' ditolak oleh admin. Feedback: {$validated['admin_feedback']}",
                'notifiable_type' => Module::class,
                'notifiable_id' => $module->id,
            ]);
        }

        return redirect()
            ->route('admin.courses.moderation', ['id' => $course->id, 'module_id' => $module->id])
            ->with('success', "Module '{$module->title}' has been rejected.");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('admin.courses.edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Implementation for updating course
        return redirect()->route('admin.courses.index')->with('success', 'Course updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $course = ClassModel::findOrFail($id);
        $courseName = $course->name;
        $teacherId = $course->teacher_id;
        
        // Send notification to teacher before deleting
        if ($teacherId) {
            Notification::create([
                'user_id' => $teacherId,
                'type' => 'course_deleted',
                'title' => 'Course Dihapus',
                'message' => "Course Anda '{$courseName}' telah dihapus oleh admin. Course ini tidak akan muncul lagi di sistem.",
                'notifiable_type' => ClassModel::class,
                'notifiable_id' => $course->id,
            ]);
        }
        
        // Delete course (cascade will handle chapters and modules)
        $course->delete();

        auth()->user()->logActivity('course_deleted', "Menghapus course: {$courseName}");

        return redirect()->route('admin.courses.index')->with('success', "Course '{$courseName}' has been deleted successfully.");
    }

    /**
     * Toggle featured status for a course (pin/unpin).
     * Hanya satu course yang boleh featured; untuk pin yang lain harus unpin dulu.
     */
    public function toggleFeatured(string $id)
    {
        $course = ClassModel::findOrFail($id);

        if (!$course->is_featured) {
            // Mau pin: cek apakah sudah ada course lain yang featured
            $otherFeatured = ClassModel::where('is_featured', true)->where('id', '!=', $id)->exists();
            if ($otherFeatured) {
                return redirect()->route('admin.courses.index')
                    ->with('error', 'Hanya satu course yang dapat di-featured. Unpin course yang saat ini featured terlebih dahulu.');
            }
        }

        $course->is_featured = !$course->is_featured;
        $course->save();

        $status = $course->is_featured ? 'marked as featured' : 'removed from featured';
        auth()->user()->logActivity($course->is_featured ? 'course_featured' : 'course_unfeatured', ($course->is_featured ? 'Mem-featured course: ' : 'Menghapus featured course: ') . $course->name);

        // Notify teacher optionally
        if ($course->teacher_id) {
            Notification::create([
                'user_id' => $course->teacher_id,
                'type' => 'course_featured',
                'title' => $course->is_featured ? 'Course Featured' : 'Course Unfeatured',
                'message' => "Course '{$course->name}' telah {$status} oleh admin.",
                'notifiable_type' => ClassModel::class,
                'notifiable_id' => $course->id,
            ]);
        }

        return redirect()->route('admin.courses.index')->with('success', "Course '{$course->name}' has been {$status}.");
    }
}
