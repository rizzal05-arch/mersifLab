<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\ClassReview;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Show all published courses (classes) in a grid
     */
    public function index(Request $request)
    {
        // Get popular courses (latest published with most modules) - tidak terpengaruh filter
        $popularCourses = ClassModel::published()
            ->with('teacher')
            ->withCount(['chapters', 'modules', 'reviews'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // Build query for filtered courses
        $query = ClassModel::published()
            ->with('teacher')
            ->withCount(['chapters', 'modules', 'reviews']);

        // Filter by category
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Filter by rating (average rating from reviews)
        if ($request->filled('rating') && $request->rating !== 'all') {
            $minRating = (float) $request->rating;
            // Filter courses where average rating >= minRating using subquery
            $query->whereRaw('(SELECT COALESCE(AVG(rating), 0) FROM class_reviews WHERE class_reviews.class_id = classes.id) >= ?', [$minRating]);
        }

        // Filter by price range
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Filter by level (if exists in database)
        // Note: Level filter might not be in database yet, so we'll skip it for now
        // if ($request->filled('level') && $request->level !== 'all') {
        //     $query->where('level', $request->level);
        // }

        // Get all courses with pagination
        $courses = $query->orderBy('created_at', 'desc')
            ->paginate(12)
            ->appends($request->query());

        // Get popular instructors (teachers with most published courses and students)
        $popularInstructors = \App\Models\User::where('role', 'teacher')
            ->where('is_banned', false)
            ->withCount([
                'classes' => function($query) {
                    $query->where('is_published', true);
                }
            ])
            ->having('classes_count', '>', 0)
            ->orderBy('classes_count', 'desc')
            ->take(10)
            ->get()
            ->map(function($teacher) {
                // Calculate total students across all published courses
                $totalStudents = DB::table('class_student')
                    ->join('classes', 'class_student.class_id', '=', 'classes.id')
                    ->join('users', 'class_student.user_id', '=', 'users.id')
                    ->where('classes.teacher_id', $teacher->id)
                    ->where('classes.is_published', true)
                    ->where('users.role', 'student')
                    ->distinct('class_student.user_id')
                    ->count('class_student.user_id');
                
                $teacher->total_students = $totalStudents;
                return $teacher;
            })
            ->sortByDesc(function($teacher) {
                // Sort by total students first, then by courses count
                return ($teacher->total_students * 1000) + $teacher->classes_count;
            })
            ->take(6)
            ->values();

        return view('courses', compact('courses', 'popularCourses', 'popularInstructors'));
    }

    /**
     * Show course detail page
     */
    public function detail($id)
    {
        // 1. Pastikan user sudah login (sudah dijamin oleh middleware auth)
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Anda harus login terlebih dahulu untuk mengakses course ini.')
                ->with('redirect', request()->fullUrl());
        }

        // 2. Pastikan user memiliki role yang valid (student, teacher, atau admin)
        if (!in_array($user->role, ['student', 'teacher', 'admin'])) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $isTeacherOrAdmin = $user->isTeacher() || $user->isAdmin();
        
        // 3. Load course
        $course = ClassModel::with(['teacher', 'chapters' => function($query) use ($isTeacherOrAdmin, $user) {
                // Teacher & Admin bisa lihat semua chapter, Student hanya yang published
                if (!$isTeacherOrAdmin) {
                    $query->where('is_published', true);
                } elseif ($user->isTeacher() && !$user->isAdmin()) {
                    // Teacher can see chapters of their own course even if not published
                    $query->whereHas('class', function($q) use ($user) {
                        $q->where('teacher_id', $user->id);
                    });
                }
                // Load modules (filtering for access is done below)
                $query->with(['modules' => function($q) use ($isTeacherOrAdmin, $user) {
                    if (!$isTeacherOrAdmin) {
                        $q->where('is_published', true)->approved();
                    } elseif ($user->isTeacher() && !$user->isAdmin()) {
                        // Teacher bisa lihat semua modul course mereka
                    } else {
                        // Admin bisa lihat semua
                    }
                    $q->orderBy('order');
                }])->orderBy('order');
            }])
            ->withCount(['chapters', 'modules'])
            ->findOrFail($id);
        
        // 4. Check enrollment untuk student
        $isEnrolled = false;
        $progress = 0;
        $userReview = null;
        
        if ($user->isStudent()) {
            $isEnrolled = DB::table('class_student')
                ->where('class_id', $course->id)
                ->where('user_id', $user->id)
                ->exists();
            
            // 5. Student HARUS sudah enroll untuk bisa akses course detail
            if (!$isEnrolled) {
                return redirect()->route('courses')
                    ->with('error', 'Anda harus membeli/enroll ke course ini terlebih dahulu untuk mengakses kontennya.');
            }
            
            if ($isEnrolled) {
                $enrollment = DB::table('class_student')
                    ->where('class_id', $course->id)
                    ->where('user_id', $user->id)
                    ->first();
                $progress = $enrollment->progress ?? 0;
                
                // Get user's review if exists
                $userReview = ClassReview::where('class_id', $course->id)
                    ->where('user_id', $user->id)
                    ->first();
            }
        }
        
        // 6. Check authorization: Teacher hanya bisa akses course mereka sendiri (kecuali admin)
        if ($isTeacherOrAdmin) {
            if (!$user->isAdmin() && $course->teacher_id !== $user->id) {
                abort(403, 'Unauthorized. This course does not belong to you.');
            }
        }
        
        // 7. Check if course is suspended and user is not the owner/admin
        if (!$course->is_published && !$user->isAdmin() && $course->teacher_id !== $user->id) {
            abort(403, 'This course has been suspended and is not available.');
        }

        // Hitung students count secara manual untuk menghindari masalah dengan where clause
        $course->students_count = DB::table('class_student')
            ->join('users', 'class_student.user_id', '=', 'users.id')
            ->where('class_student.class_id', $course->id)
            ->where('users.role', 'student')
            ->count();

        // Check if user is enrolled (for authenticated students)
        $isEnrolled = false;
        $progress = 0;
        $userReview = null;
        if (auth()->check() && auth()->user()->isStudent()) {
            $isEnrolled = $course->isEnrolledBy(auth()->user());
            if ($isEnrolled) {
                $enrollment = DB::table('class_student')
                    ->where('class_id', $course->id)
                    ->where('user_id', auth()->id())
                    ->first();
                $progress = $enrollment->progress ?? 0;
                
                // Get user's review if exists
                $userReview = ClassReview::where('class_id', $course->id)
                    ->where('user_id', auth()->id())
                    ->first();
            }
        }

        // Get reviews and rating stats
        $reviews = ClassReview::where('class_id', $course->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $ratingStats = [
            'total' => ClassReview::where('class_id', $course->id)->count(),
            'average' => ClassReview::where('class_id', $course->id)->avg('rating') ?? 0,
            'distribution' => []
        ];

        // Calculate rating distribution
        for ($i = 5; $i >= 1; $i--) {
            $count = ClassReview::where('class_id', $course->id)
                ->where('rating', $i)
                ->count();
            $ratingStats['distribution'][$i] = [
                'count' => $count,
                'percentage' => $ratingStats['total'] > 0 ? round(($count / $ratingStats['total']) * 100, 1) : 0
            ];
        }

        return view('course-detail', compact('course', 'isEnrolled', 'progress', 'userReview', 'reviews', 'ratingStats'));
    }

    /**
     * Submit rating and review for a course
     */
    public function submitRating(Request $request, $id)
    {
        $user = auth()->user();
        
        if (!$user || !$user->isStudent()) {
            return redirect()->back()->with('error', 'Hanya student yang bisa memberikan rating.');
        }

        $course = ClassModel::findOrFail($id);

        // Check if user is enrolled
        if (!$course->isEnrolledBy($user)) {
            return redirect()->back()->with('error', 'Anda harus enroll course ini terlebih dahulu untuk memberikan rating.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Update or create review
        $review = ClassReview::updateOrCreate(
            [
                'class_id' => $course->id,
                'user_id' => $user->id,
            ],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]
        );

        // Notifikasi ke teacher bahwa siswa memberikan rating terhadap kelasnya (jika teacher mengaktifkan notifikasi)
        if ($course->teacher && $course->teacher->wantsNotification('course_rated')) {
            $ratingText = $validated['rating'] . ' bintang';
            $commentText = !empty($validated['comment']) ? " dengan komentar: \"{$validated['comment']}\"" : '';
            
            Notification::create([
                'user_id' => $course->teacher->id,
                'type' => 'course_rated',
                'title' => 'Course Mendapat Rating Baru',
                'message' => "Siswa '{$user->name}' memberikan rating {$ratingText} untuk course '{$course->name}' Anda{$commentText}.",
                'notifiable_type' => ClassModel::class,
                'notifiable_id' => $course->id,
            ]);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Rating berhasil disimpan.'
            ]);
        }
        
        return redirect()->back()->with('success', 'Rating berhasil disimpan.');
    }
}