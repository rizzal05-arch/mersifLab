<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeacherApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherApplicationController extends Controller
{
    /**
     * Display a listing of teacher applications.
     */
    public function index(Request $request)
    {
        $applications = TeacherApplication::with(['user', 'reviewer'])
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.teacher-applications.index', compact('applications'));
    }

    /**
     * Display the specified teacher application.
     */
    public function show(TeacherApplication $teacherApplication)
    {
        $teacherApplication->load(['user', 'reviewer']);
        return view('admin.teacher-applications.show', compact('teacherApplication'));
    }

    /**
     * Approve the teacher application.
     */
    public function approve(Request $request, TeacherApplication $teacherApplication)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        // Update application status
        $teacherApplication->update([
            'status' => 'approved',
            'admin_notes' => $request->admin_notes,
            'reviewed_at' => now(),
            'reviewed_by' => Auth::id(),
        ]);

        // Update user role to teacher
        $teacherApplication->user->update([
            'role' => 'teacher',
        ]);

        return redirect()->route('admin.teacher-applications.index')
            ->with('success', 'Teacher application approved successfully. User has been upgraded to teacher role.');
    }

    /**
     * Reject the teacher application.
     */
    public function reject(Request $request, TeacherApplication $teacherApplication)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ]);

        // Update application status
        $teacherApplication->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
            'reviewed_at' => now(),
            'reviewed_by' => Auth::id(),
        ]);

        return redirect()->route('admin.teacher-applications.index')
            ->with('success', 'Teacher application rejected successfully.');
    }

    /**
     * Delete the teacher application.
     */
    public function destroy(TeacherApplication $teacherApplication)
    {
        // Delete associated files
        $files = [
            $teacherApplication->ktp_file,
            $teacherApplication->teaching_certificate_file,
            $teacherApplication->institution_id_file,
            $teacherApplication->portfolio_file,
        ];

        foreach ($files as $file) {
            if ($file && \Storage::disk('public')->exists($file)) {
                \Storage::disk('public')->delete($file);
            }
        }

        // Delete the application
        $teacherApplication->delete();

        return redirect()->route('admin.teacher-applications.index')
            ->with('success', 'Teacher application deleted successfully.');
    }

    /**
     * Download application file.
     */
    public function downloadFile(TeacherApplication $teacherApplication, $fileType)
    {
        $fileField = match($fileType) {
            'ktp' => 'ktp_file',
            'certificate' => 'teaching_certificate_file',
            'institution' => 'institution_id_file',
            'portfolio' => 'portfolio_file',
            default => null,
        };

        if (!$fileField || !$teacherApplication->$fileField) {
            return back()->with('error', 'File not found.');
        }

        $filePath = $teacherApplication->$fileField;
        $fileName = basename($filePath);

        return \Storage::disk('public')->download($filePath, $fileName);
    }
}
