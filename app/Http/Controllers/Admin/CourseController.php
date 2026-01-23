<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Module;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ClassModel::with('teacher')
            ->withCount(['chapters', 'modules'])
            ->orderBy('created_at', 'desc');

        // Filter by category
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $courses = $query->paginate(15);
        $courses->appends($request->query());

        return view('admin.courses.index', compact('courses'));
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
            ->with('success', "Module '{$module->title}' telah disetujui dan dipublish.");
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
            ->with('success', "Module '{$module->title}' telah ditolak.");
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

        return redirect()->route('admin.courses.index')->with('success', "Course '{$courseName}' has been deleted successfully.");
    }
}
