<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show home page with courses by category and trending courses
     */
    public function index()
    {
        // Get published classes by category (for home page categories section)
        $coursesByCategory = [
            'ai' => ClassModel::publishedByCategory('ai')
                ->with('teacher')
                ->withCount(['chapters', 'modules'])
                ->take(4)
                ->get(),
            'development' => ClassModel::publishedByCategory('development')
                ->with('teacher')
                ->withCount(['chapters', 'modules'])
                ->take(4)
                ->get(),
            'marketing' => ClassModel::publishedByCategory('marketing')
                ->with('teacher')
                ->withCount(['chapters', 'modules'])
                ->take(4)
                ->get(),
            'design' => ClassModel::publishedByCategory('design')
                ->with('teacher')
                ->withCount(['chapters', 'modules'])
                ->take(4)
                ->get(),
            'photography' => ClassModel::publishedByCategory('photography')
                ->with('teacher')
                ->withCount(['chapters', 'modules'])
                ->take(4)
                ->get(),
        ];

        // Get trending courses (latest published courses with most modules)
        $trendingCourses = ClassModel::published()
            ->with('teacher')
            ->withCount(['chapters', 'modules'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // Get enrolled courses for authenticated user
        $enrolledCourses = collect();
        if (auth()->check() && auth()->user() && auth()->user()->isStudent()) {
            try {
                $enrolledCourses = ClassModel::published()
                    ->with(['teacher' => function($q) {
                        $q->select('id', 'name'); // Explicitly select teacher fields
                    }])
                    ->withCount('modules')
                    ->take(3)
                    ->get();
            } catch (\Exception $e) {
                // If error, just show empty collection
                $enrolledCourses = collect();
            }
        }

        return view('home', [
            'coursesByCategory' => $coursesByCategory,
            'trendingCourses' => $trendingCourses,
            'enrolledCourses' => $enrolledCourses,
        ]);
    }
}
