<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    /**
     * Display a listing of students (role=student).
     */
    public function index()
    {
        $students = User::where('role', 'student')
            ->withCount(['enrolledClasses'])
            ->orderBy('name')
            ->get()
            ->map(function ($student) {
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
                    'last_login_at' => $student->last_login_at,
                    'is_online' => $student->is_online,
                ];
            });

        return view('admin.students.index', compact('students'));
    }


    /**
     * Admin tidak bisa create student.
     */
    public function create()
    {
        return redirect()->route('admin.students.index')
            ->with('info', 'Admin tidak dapat menambahkan student. Student terdaftar melalui halaman registrasi.');
    }

    /**
     * Store: redirect.
     */
    public function store(Request $request)
    {
        return redirect()->route('admin.students.index')
            ->with('info', 'Admin tidak dapat menambahkan student. Student terdaftar melalui halaman registrasi.');
    }

    /**
     * Display student detail: info, enrolled courses, progress, aktivitas.
     */
    public function show(string $id)
    {
        $student = User::where('role', 'student')->findOrFail($id);

        $enrolled = $student->enrolledClasses()
            ->with('teacher')
            ->withCount(['chapters', 'modules'])
            ->orderByPivot('enrolled_at', 'desc')
            ->get();

        $totalModulesCompleted = (int) DB::table('module_completions')
            ->where('user_id', $student->id)
            ->count();

        $activities = ActivityLog::where('user_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->take(20)
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
            ->limit(30)
            ->get();

        return view('admin.students.show', compact(
            'student',
            'enrolled',
            'totalModulesCompleted',
            'activities',
            'completions'
        ));
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
            ->with('info', 'Admin tidak dapat mengedit profil student.');
    }

    /**
     * Delete student.
     */
    public function destroy(string $id)
    {
        $student = User::where('role', 'student')->findOrFail($id);
        $studentName = $student->name;
        
        $student->delete();

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
}
