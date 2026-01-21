<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Dummy data for now
        $courses = [
            [
                'id' => 1,
                'title' => 'Introduction to Laravel',
                'instructor' => 'Dr. Budi Pengajar',
                'price' => 'Rp 500.000',
                'status' => 'Active',
                'students' => 45
            ],
            [
                'id' => 2,
                'title' => 'Advanced PHP',
                'instructor' => 'Ibu Ratna Instruktur',
                'price' => 'Rp 750.000',
                'status' => 'Active',
                'students' => 32
            ],
            [
                'id' => 3,
                'title' => 'Web Development Fundamentals',
                'instructor' => 'Dr. Budi Pengajar',
                'price' => 'Rp 400.000',
                'status' => 'Inactive',
                'students' => 28
            ]
        ];

        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.courses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Implementation for storing course
        return redirect()->route('admin.courses.index')->with('success', 'Course created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('admin.courses.show', compact('id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('admin.courses.edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Implementation for updating course
        return redirect()->route('admin.courses.index')->with('success', 'Course updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Implementation for deleting course
        return redirect()->route('admin.courses.index')->with('success', 'Course deleted successfully');
    }
}
