<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Dummy data for now
        $students = [
            [
                'id' => 1,
                'name' => 'Ahmad Student',
                'email' => 'student1@example.com',
                'enrolled_courses' => 3,
                'joined_date' => '2024-01-10',
                'status' => 'Active'
            ],
            [
                'id' => 2,
                'name' => 'Siti Belajar',
                'email' => 'student2@example.com',
                'enrolled_courses' => 5,
                'joined_date' => '2024-02-15',
                'status' => 'Active'
            ],
            [
                'id' => 3,
                'name' => 'Rudi Pelajar',
                'email' => 'student3@example.com',
                'enrolled_courses' => 2,
                'joined_date' => '2024-03-05',
                'status' => 'Active'
            ]
        ];

        return view('admin.students.index', compact('students'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.students.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Implementation for storing student
        return redirect()->route('admin.students.index')->with('success', 'Student created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('admin.students.show', compact('id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('admin.students.edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Implementation for updating student
        return redirect()->route('admin.students.index')->with('success', 'Student updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Implementation for deleting student
        return redirect()->route('admin.students.index')->with('success', 'Student deleted successfully');
    }
}
