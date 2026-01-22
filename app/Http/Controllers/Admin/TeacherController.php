<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\ClassReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    /**
     * Display a listing of teachers (role=teacher).
     */
    public function index()
    {
        $teachers = User::where('role', 'teacher')
            ->withCount('classes')
            ->orderBy('name')
            ->get();

        return view('admin.teachers.index', compact('teachers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.teachers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return redirect()->route('admin.teachers.index')->with('info', 'Fitur tambah guru coming soon. Guru terdaftar melalui registrasi.');
    }

    /**
     * Display teacher detail: info, courses/chapters/modules, rating placeholder, activity.
     */
    public function show(string $teacher)
    {
        $teacher = User::where('role', 'teacher')->findOrFail($teacher);

        $courses = $teacher->classes()
            ->withCount(['chapters', 'modules'])
            ->with(['chapters' => function ($q) {
                $q->orderBy('order');
            }, 'chapters.modules' => function ($q) {
                $q->orderBy('order');
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        // Total enrollments dan unique students di semua course guru ini
        $classIds = $teacher->classes()->pluck('id');
        $totalEnrollments = $classIds->isNotEmpty() 
            ? DB::table('class_student')->whereIn('class_id', $classIds)->count() 
            : 0;
        $uniqueStudents = $classIds->isNotEmpty()
            ? (int) DB::table('class_student')
                ->whereIn('class_id', $classIds)
                ->selectRaw('count(distinct user_id) as c')
                ->value('c')
            : 0;

        $activities = ActivityLog::where('user_id', $teacher->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Rating & reviews dari kelas yang dibuat teacher ini
        $reviews = collect();
        $ratingStats = [
            'total' => 0,
            'avg' => 0,
            'distribution' => [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0],
        ];
        if ($classIds->isNotEmpty()) {
            $reviews = ClassReview::whereIn('class_id', $classIds)
                ->with(['classModel' => fn ($q) => $q->select('id', 'name'), 'user' => fn ($q) => $q->select('id', 'name')])
                ->orderBy('created_at', 'desc')
                ->take(50)
                ->get();
            $ratingStats['total'] = ClassReview::whereIn('class_id', $classIds)->count();
            $ratingStats['avg'] = $ratingStats['total'] > 0
                ? round(ClassReview::whereIn('class_id', $classIds)->avg('rating'), 1)
                : 0;
            $dist = ClassReview::whereIn('class_id', $classIds)
                ->selectRaw('rating, count(*) as c')
                ->groupBy('rating')
                ->pluck('c', 'rating');
            foreach ([5, 4, 3, 2, 1] as $r) {
                $ratingStats['distribution'][$r] = (int) ($dist[$r] ?? 0);
            }
        }

        return view('admin.teachers.show', compact(
            'teacher',
            'courses',
            'totalEnrollments',
            'uniqueStudents',
            'activities',
            'reviews',
            'ratingStats'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $teacher)
    {
        $teacher = User::where('role', 'teacher')->findOrFail($teacher);
        return view('admin.teachers.edit', compact('teacher'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $teacher)
    {
        return redirect()->route('admin.teachers.index')->with('info', 'Fitur edit guru coming soon.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $teacher)
    {
        return redirect()->route('admin.teachers.index')->with('info', 'Fitur hapus guru coming soon.');
    }

    /**
     * Ban/Unban teacher.
     */
    public function toggleBan(string $id)
    {
        $teacher = User::where('role', 'teacher')->findOrFail($id);
        $teacher->update(['is_banned' => !$teacher->is_banned]);
        $status = $teacher->is_banned ? 'banned' : 'unbanned';

        return redirect()->back()
            ->with('success', "Teacher {$teacher->name} telah di-{$status}.");
    }
}
