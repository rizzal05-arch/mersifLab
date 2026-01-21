<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Dummy data for now
        $teachers = [
            [
                'id' => 1,
                'name' => 'Dr. Budi Pengajar',
                'email' => 'teacher1@example.com',
                'joined_date' => '2024-01-15',
                'total_courses' => 5,
                'status' => 'Active'
            ],
            [
                'id' => 2,
                'name' => 'Ibu Ratna Instruktur',
                'email' => 'teacher2@example.com',
                'joined_date' => '2024-02-20',
                'total_courses' => 3,
                'status' => 'Active'
            ],
            [
                'id' => 3,
                'name' => 'Ahmad Guru',
                'email' => 'teacher3@example.com',
                'joined_date' => '2024-03-10',
                'total_courses' => 7,
                'status' => 'Banned'
            ]
        ];

        return view('admin.teachers.index', compact('teachers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.teachers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Implementation for storing teacher
        return redirect()->route('admin.teachers.index')->with('success', 'Teacher created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('admin.teachers.show', compact('id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('admin.teachers.edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Implementation for updating teacher
        return redirect()->route('admin.teachers.index')->with('success', 'Teacher updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Implementation for deleting teacher
        return redirect()->route('admin.teachers.index')->with('success', 'Teacher deleted successfully');
    }

    /**
     * Ban/Unban teacher
     */
    public function toggleBan(string $id)
    {
        // Implementation for banning/unbanning teacher
        return redirect()->route('admin.teachers.index')->with('success', 'Teacher status updated successfully');
    }
}
