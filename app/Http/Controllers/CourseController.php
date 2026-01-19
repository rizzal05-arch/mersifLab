<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::all();
        return view('profile.my-courses', compact('courses'));
    }

    public function detail($id)
    {
        $course = Course::findOrFail($id);
        $materials = $course->materi ?? collect();
        return view('course.detail', compact('course', 'materials'));
    }
}