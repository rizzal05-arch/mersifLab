<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\ClassReview;
use App\Models\ClassModel;
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
            ->get()
            ->map(function ($teacher) {
                return [
                    'id' => $teacher->id,
                    'name' => $teacher->name,
                    'email' => $teacher->email,
                    'phone' => $teacher->phone,
                    'address' => $teacher->address,
                    'bio' => $teacher->bio,
                    'biography' => $teacher->biography,
                    'is_banned' => $teacher->is_banned,
                    'created_at' => $teacher->created_at,
                    'classes_count' => $teacher->classes_count,
                    'last_login_at' => $teacher->last_login_at,
                    'is_online' => $teacher->is_online,
                ];
            });

        return view('admin.teachers.index', compact('teachers'));
    }


    /**
     * Show the form for creating a new resource.
     * Admin tidak bisa create teacher - hanya bisa monitor.
     */
    public function create()
    {
        return redirect()->route('admin.teachers.index')->with('info', 'Admin cannot add teachers manually. Teachers register through the registration page.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return redirect()->route('admin.teachers.index')->with('info', 'Admin cannot add teachers manually. Teachers register through the registration page.');
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

        // Rating & reviews keseluruhan dari semua kelas teacher ini
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

        // Rating & reviews per kelas (hanya untuk tampilan admin)
        $ratingPerClass = collect();
        foreach ($courses ?? [] as $course) {
            $classId = $course->id;
            $classReviews = ClassReview::where('class_id', $classId)
                ->with(['user' => fn ($q) => $q->select('id', 'name')])
                ->orderBy('created_at', 'desc')
                ->get();
            $total = $classReviews->count();
            $avg = $total > 0 ? round($classReviews->avg('rating'), 1) : 0;
            $distribution = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
            foreach ($classReviews as $rev) {
                $r = (int) $rev->rating;
                if (isset($distribution[$r])) {
                    $distribution[$r]++;
                }
            }
            $ratingPerClass->push([
                'course' => $course,
                'total' => $total,
                'avg' => $avg,
                'distribution' => $distribution,
                'reviews' => $classReviews->take(10),
            ]);
        }

        return view('admin.teachers.show', compact(
            'teacher',
            'courses',
            'totalEnrollments',
            'uniqueStudents',
            'activities',
            'reviews',
            'ratingStats',
            'ratingPerClass'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     * Admin tidak bisa edit teacher - hanya bisa view, ban/unban, delete.
     */
    public function edit(string $teacher)
    {
        return redirect()->route('admin.teachers.show', $teacher)->with('info', 'Admin cannot edit teacher profile. Use View to see details.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $teacher)
    {
        return redirect()->route('admin.teachers.show', $teacher)->with('info', 'Admin cannot edit teacher profile.');
    }

    /**
     * Remove the specified resource from storage.
     * Admin bisa delete teacher (dengan konfirmasi).
     */
    public function destroy(string $teacher)
    {
        $teacher = User::where('role', 'teacher')->findOrFail($teacher);
        $teacherName = $teacher->name;
        
        // Hapus semua classes milik teacher (cascade akan handle chapters/modules)
        $teacher->classes()->delete();
        
        // Hapus teacher
        $teacher->delete();
        
        return redirect()->route('admin.teachers.index')
            ->with('success', "Teacher '{$teacherName}' dan semua course-nya telah dihapus.");
    }

    /**
     * Display all reviews for a specific class created by a teacher.
     */
    public function classReviews(string $teacherId, string $classId)
    {
        $teacher = User::where('role', 'teacher')->findOrFail($teacherId);
        $class = ClassModel::where('teacher_id', $teacherId)->findOrFail($classId);
        
        // Get all reviews for this class with pagination
        $reviews = ClassReview::where('class_id', $classId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // Calculate rating statistics
        $totalReviews = $reviews->total();
        $averageRating = ClassReview::where('class_id', $classId)->avg('rating') ?? 0;
        $ratingDistribution = [
            5 => ClassReview::where('class_id', $classId)->where('rating', 5)->count(),
            4 => ClassReview::where('class_id', $classId)->where('rating', 4)->count(),
            3 => ClassReview::where('class_id', $classId)->where('rating', 3)->count(),
            2 => ClassReview::where('class_id', $classId)->where('rating', 2)->count(),
            1 => ClassReview::where('class_id', $classId)->where('rating', 1)->count(),
        ];

        return view('admin.teachers.class-reviews', compact(
            'teacher',
            'class',
            'reviews',
            'totalReviews',
            'averageRating',
            'ratingDistribution'
        ));
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
            ->with('success', "Teacher {$teacher->name} has been {$status}.");
    }
}
