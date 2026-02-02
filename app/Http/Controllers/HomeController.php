<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\Category;
use Illuminate\Http\Request;

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

        // Get trending courses (latest published courses with most modules)
        $trendingCourses = ClassModel::published()
            ->with('teacher')
            ->withCount(['chapters', 'modules', 'reviews'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // Get enrolled courses for authenticated user (only for students)
        $enrolledCourses = collect();
        if (auth()->check() && auth()->user() && auth()->user()->isStudent()) {
            try {
                $enrolledCourseIds = \Illuminate\Support\Facades\DB::table('class_student')
                    ->where('user_id', auth()->id())
                    ->pluck('class_id');
                
                if ($enrolledCourseIds->isNotEmpty()) {
                    $enrolledCourses = ClassModel::whereIn('id', $enrolledCourseIds)
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
                            ->where('user_id', auth()->id())
                            ->first();
                        $course->progress = $enrollment->progress ?? 0;
                        $course->completed_modules = \Illuminate\Support\Facades\DB::table('module_completions')
                            ->where('class_id', $course->id)
                            ->where('user_id', auth()->id())
                            ->count();
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

        return view('home', [
            'categories' => $categories,
            'coursesByCategory' => $coursesByCategory,
            'trendingCourses' => $trendingCourses,
            'enrolledCourses' => $enrolledCourses,
            'teacherCourses' => $teacherCourses,
            'testimonials' => $testimonials,
        ]);
    }
}
