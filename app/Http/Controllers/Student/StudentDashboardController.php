<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Materi;
use Illuminate\Http\Request;

class StudentDashboardController extends Controller
{
    /**
     * Display student dashboard
     * 
     * Logic spesifik untuk student:
     * - Tampilkan kursus yang sudah dibeli/diikuti
     * - Progress pembelajaran
     * - Materi yang tersedia
     * - Rekomendasi kursus
     */
    public function index()
    {
        $user = auth()->user();
        
        // Ambil kursus yang diikuti student
        $enrolledCourses = Course::all(); // Sesuaikan query dengan relasi enrollment Anda
        $totalCourses = $enrolledCourses->count();
        
        // Ambil materi yang dipelajari
        $materiList = Materi::all();
        $totalMateri = $materiList->count();

        $data = [
            'user' => $user,
            'courses' => $enrolledCourses,
            'totalKursus' => $totalCourses,
            'totalMateri' => $totalMateri,
            'role' => 'student',
        ];

        return view('dashboard', $data);
    }

    /**
     * Get course detail untuk student
     */
    public function courseDetail($id)
    {
        $course = Course::findOrFail($id);
        
        return view('student.course-detail', [
            'course' => $course,
            'role' => 'student',
        ]);
    }

    /**
     * Get student progress
     */
    public function progress()
    {
        $user = auth()->user();
        
        return view('student.progress', [
            'user' => $user,
            'role' => 'student',
        ]);
    }
}
