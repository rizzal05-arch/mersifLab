<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\Module;
use Illuminate\Http\Request;

class StudentDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:student');
    }

    public function index()
    {
        $student = auth()->user();
        
        // Get published classes only (read-only data)
        $classes = ClassModel::where('is_published', true)
            ->withCount(['chapters', 'modules'])
            ->with('teacher')
            ->get();
        
        $recentModules = Module::where('is_published', true)
            ->latest()
            ->limit(6)
            ->get();
        
        return view('student.dashboard', compact('classes', 'recentModules'));
    }

    public function courseDetail($id)
    {
        $class = ClassModel::where('id', $id)
            ->where('is_published', true)
            ->firstOrFail();
        
        $chapters = $class->chapters()
            ->where('is_published', true)
            ->with(['modules' => function ($query) {
                $query->where('is_published', true);
            }])
            ->get();
        
        return view('student.course-detail', compact('class', 'chapters'));
    }

    public function progress()
    {
        $student = auth()->user();
        
        // Get progress dari modules yang sudah dilihat
        $viewedModules = Module::where('is_published', true)
            ->where('view_count', '>', 0)
            ->get();
        
        return view('student.progress', compact('viewedModules'));
    }
}
