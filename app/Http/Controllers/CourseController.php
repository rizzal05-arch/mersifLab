<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Show all published courses (classes) in a grid
     */
    public function index()
    {
        // Get popular courses (latest published with most modules)
        $popularCourses = ClassModel::published()
            ->with('teacher')
            ->withCount(['chapters', 'modules'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // Get all courses with pagination
        $courses = ClassModel::published()
            ->with('teacher')
            ->withCount(['chapters', 'modules'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('courses', compact('courses', 'popularCourses'));
    }

    /**
     * Show course detail page
     */
    public function detail($id)
    {
        $course = ClassModel::where('id', $id)
            ->where('is_published', true)
            ->with(['teacher', 'chapters' => function($query) {
                $query->where('is_published', true)
                    ->with(['modules' => function($q) {
                        $q->where('is_published', true)->orderBy('order');
                    }])
                    ->orderBy('order');
            }])
            ->withCount(['chapters', 'modules'])
            ->firstOrFail();

        return view('course-detail', compact('course'));
    }
}