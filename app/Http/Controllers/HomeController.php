<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Show home page with courses by category and trending courses
     */
    public function index()
    {
        // Get active categories from database
        $categories = Category::active()->ordered()->get();
        
        // Fallback to constant categories if database is empty
        if ($categories->isEmpty()) {
            $categories = collect(ClassModel::CATEGORIES)->map(function ($name, $slug) {
                return (object) [
                    'slug' => $slug,
                    'name' => $name,
                    'id' => null,
                ];
            });
        }

        // Get published classes by category (for home page categories section)
        $coursesByCategory = [];
        foreach ($categories as $category) {
            $coursesByCategory[$category->slug] = ClassModel::publishedByCategory($category->slug)
                ->with('teacher')
                ->withCount(['chapters', 'modules', 'reviews'])
                ->take(4)
                ->get();
        }

        // Get trending courses (sorted by number of students)
        $trendingCourses = ClassModel::published()
            ->with('teacher')
            ->withCount(['chapters', 'modules', 'reviews'])
            ->leftJoin('class_student', 'classes.id', '=', 'class_student.class_id')
            ->select('classes.*', DB::raw('COUNT(DISTINCT class_student.user_id) as student_count'))
            ->groupBy('classes.id')
            ->orderByDesc(DB::raw('COUNT(DISTINCT class_student.user_id)'))
            ->take(6)
            ->get();

        // Get enrolled courses for authenticated user (only for students)
        // Include courses from purchases and subscriptions (both are in class_student table)
        $enrolledCourses = collect();
        if (auth()->check() && auth()->user() && auth()->user()->isStudent()) {
            try {
                $user = auth()->user();
                
                // Get enrolled course IDs from class_student (includes purchased and subscription courses)
                $enrolledCourseIds = \Illuminate\Support\Facades\DB::table('class_student')
                    ->where('user_id', $user->id)
                    ->pluck('class_id');
                
                if ($enrolledCourseIds->isNotEmpty()) {
                    // Get courses that have at least 1 completed module (progress tracking hanya muncul jika sudah complete minimal 1 module)
                    $coursesWithProgress = \Illuminate\Support\Facades\DB::table('module_completions')
                        ->where('user_id', $user->id)
                        ->whereIn('class_id', $enrolledCourseIds)
                        ->pluck('class_id')
                        ->unique()
                        ->values();
                    
                    // Only show courses that have progress (minimal 1 module completed)
                    if ($coursesWithProgress->isNotEmpty()) {
                        $enrolledCourses = ClassModel::whereIn('id', $coursesWithProgress)
                            ->with(['teacher' => function($q) {
                                $q->select('id', 'name'); // Explicitly select teacher fields
                            }])
                            ->withCount('modules')
                            ->orderBy('created_at', 'desc')
                            ->take(3)
                            ->get();
                        
                        // Add progress data for each enrolled course
                        foreach ($enrolledCourses as $course) {
                            $enrollment = \Illuminate\Support\Facades\DB::table('class_student')
                                ->where('class_id', $course->id)
                                ->where('user_id', $user->id)
                                ->first();
                            $course->progress = $enrollment->progress ?? 0;
                            $course->completed_modules = \Illuminate\Support\Facades\DB::table('module_completions')
                                ->where('class_id', $course->id)
                                ->where('user_id', $user->id)
                                ->count();
                        }
                    }
                }
            } catch (\Exception $e) {
                // If error, just show empty collection
                $enrolledCourses = collect();
            }
        }

        // Get teacher's courses (only for teachers)
        $teacherCourses = collect();
        if (auth()->check() && auth()->user() && auth()->user()->isTeacher()) {
            try {
                $teacherCourses = ClassModel::where('teacher_id', auth()->id())
                    ->with('teacher')
                    ->withCount(['chapters', 'modules'])
                    ->orderBy('created_at', 'desc')
                    ->take(3)
                    ->get();
            } catch (\Exception $e) {
                // If error, just show empty collection
                $teacherCourses = collect();
            }
        }

        // Testimonials for homepage (added by admin)
        $testimonials = \App\Models\Testimonial::where('is_published', true)->orderBy('created_at', 'desc')->take(3)->get();

        // Most popular courses (most students enrolled)
        $featuredCourses = ClassModel::where('is_published', true)
            ->with('teacher')
            ->withCount(['chapters', 'modules', 'reviews'])
            ->leftJoin('class_student', 'classes.id', '=', 'class_student.class_id')
            ->select('classes.*', DB::raw('COUNT(DISTINCT class_student.user_id) as student_count'))
            ->groupBy('classes.id')
            ->orderByDesc(DB::raw('COUNT(DISTINCT class_student.user_id)'))
            ->take(3)
            ->get();

        return view('home', [
            'categories' => $categories,
            'coursesByCategory' => $coursesByCategory,
            'trendingCourses' => $trendingCourses,
            'enrolledCourses' => $enrolledCourses,
            'teacherCourses' => $teacherCourses,
            'testimonials' => $testimonials,
            'featuredCourses' => $featuredCourses,
        ]);
    }
}
