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
            ->get();

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
     * Admin hanya bisa ban, tidak delete student.
     */
    public function destroy(string $id)
    {
        return redirect()->route('admin.students.index')
            ->with('info', 'Admin hanya dapat menonaktifkan (ban) akun student. Gunakan Ban untuk menonaktifkan.');
    }

    /**
     * Ban/Unban student.
     */
    public function toggleBan(string $id)
    {
        $student = User::where('role', 'student')->findOrFail($id);
        $student->update(['is_banned' => !$student->is_banned]);
        $status = $student->is_banned ? 'banned' : 'unbanned';

        return redirect()->back()
            ->with('success', "Student {$student->name} telah di-{$status}.");
    }
}
