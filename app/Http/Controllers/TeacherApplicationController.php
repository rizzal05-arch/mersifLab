<?php

namespace App\Http\Controllers;

use App\Models\TeacherApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TeacherApplicationController extends Controller
{
    /**
     * Show the teacher application form.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Check if user already has a pending application
        if ($user->hasPendingTeacherApplication()) {
            return redirect()->route('profile')
                ->with('info', 'You already have a pending teacher application. Please wait for the review.');
        }
        
        // Check if user has a rejected application (should edit instead of creating new)
        if ($user->hasRejectedTeacherApplication()) {
            return redirect()->route('teacher.application.edit')
                ->with('info', 'You have a rejected application. Please edit and resubmit it instead of creating a new one.');
        }
        
        // Check if user is already a teacher
        if ($user->isTeacher()) {
            return redirect()->route('profile')
                ->with('info', 'You are already a teacher.');
        }
        
        return view('teacher.application');
    }

    /**
     * Store the teacher application.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Double check if user already has a pending application
        if ($user->hasPendingTeacherApplication()) {
            return back()->with('error', 'You already have a pending teacher application.');
        }
        
        // Check if user has a rejected application (should edit instead)
        if ($user->hasRejectedTeacherApplication()) {
            return back()->with('error', 'You have a rejected application. Please edit and resubmit it instead of creating a new one.');
        }
        
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255|regex:/^[A-Z][a-zA-Z\s\'-]*$/',
            'email' => 'required|email|max:255|in:' . $user->email,
            'phone' => 'required|string|max:20|regex:/^[0-9\-\s\(\)\+]*$/',
            'address' => 'required|string|max:1000',
            'ktp_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'teaching_certificate_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'institution_id_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'teaching_experience' => 'required|string|max:2000',
            'portfolio_file' => 'required|file|mimes:pdf,zip,doc,docx|max:10240',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Upload files
        $ktpFile = $request->file('ktp_file')->store('teacher-applications/ktp', 'public');
        $teachingCertificateFile = $request->file('teaching_certificate_file')->store('teacher-applications/certificates', 'public');
        $institutionIdFile = $request->file('institution_id_file')->store('teacher-applications/institution-ids', 'public');
        $portfolioFile = $request->file('portfolio_file')->store('teacher-applications/portfolios', 'public');

        // Create application
        TeacherApplication::create([
            'user_id' => $user->id,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'ktp_file' => $ktpFile,
            'teaching_certificate_file' => $teachingCertificateFile,
            'institution_id_file' => $institutionIdFile,
            'teaching_experience' => $request->teaching_experience,
            'portfolio_file' => $portfolioFile,
            'status' => 'pending',
        ]);

        return redirect()->route('profile')
            ->with('success', 'Your teacher application has been submitted successfully. We will review it and get back to you soon.');
    }

    /**
     * Show the application status to the user.
     */
    public function show()
    {
        $user = Auth::user();
        $application = $user->teacherApplication;
        
        if (!$application) {
            return redirect()->route('teacher.application.create');
        }
        
        return view('teacher.application-status', compact('application'));
    }

    /**
     * Show the preview of the application.
     */
    public function preview()
    {
        $user = Auth::user();
        $application = $user->teacherApplication;
        
        if (!$application) {
            return redirect()->route('teacher.application.create');
        }

        // Check authorization - user can only view their own application
        if ($application->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }
        
        return view('teacher.application-preview', compact('application'));
    }

    /**
     * Show the edit form for the application.
     */
    public function edit()
    {
        $user = Auth::user();
        $application = $user->teacherApplication;
        
        if (!$application) {
            return redirect()->route('teacher.application.create');
        }

        // Check authorization - user can only edit their own application
        if ($application->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        // Only allow editing if application is rejected
        if (!in_array($application->status, ['rejected'])) {
            return redirect()->route('teacher.application.preview')
                ->with('info', 'You can only edit rejected applications.');
        }
        
        return view('teacher.application-edit', compact('application'));
    }

    /**
     * Update the teacher application.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $application = $user->teacherApplication;
        
        if (!$application) {
            return redirect()->route('teacher.application.create');
        }

        // Check authorization
        if ($application->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        // Only allow editing if application is rejected
        if (!in_array($application->status, ['rejected'])) {
            return redirect()->route('teacher.application.preview')
                ->with('error', 'You can only edit rejected applications.');
        }

        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255|regex:/^[A-Z][a-zA-Z\s\'-]*$/',
            'email' => 'required|email|max:255|in:' . $application->email,
            'phone' => 'required|string|max:20|regex:/^[0-9\-\s\(\)\+]*$/',
            'address' => 'required|string|max:1000',
            'ktp_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'teaching_certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'institution_id_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'teaching_experience' => 'required|string|max:2000',
            'portfolio_file' => 'nullable|file|mimes:pdf,zip,doc,docx|max:10240',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update files if provided
        if ($request->hasFile('ktp_file')) {
            if ($application->ktp_file) {
                Storage::disk('public')->delete($application->ktp_file);
            }
            $application->ktp_file = $request->file('ktp_file')->store('teacher-applications/ktp', 'public');
        }

        if ($request->hasFile('teaching_certificate_file')) {
            if ($application->teaching_certificate_file) {
                Storage::disk('public')->delete($application->teaching_certificate_file);
            }
            $application->teaching_certificate_file = $request->file('teaching_certificate_file')->store('teacher-applications/certificates', 'public');
        }

        if ($request->hasFile('institution_id_file')) {
            if ($application->institution_id_file) {
                Storage::disk('public')->delete($application->institution_id_file);
            }
            $application->institution_id_file = $request->file('institution_id_file')->store('teacher-applications/institution-ids', 'public');
        }

        if ($request->hasFile('portfolio_file')) {
            if ($application->portfolio_file) {
                Storage::disk('public')->delete($application->portfolio_file);
            }
            $application->portfolio_file = $request->file('portfolio_file')->store('teacher-applications/portfolios', 'public');
        }

        // Update basic information
        $application->update([
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'teaching_experience' => $request->teaching_experience,
            'status' => 'pending', // Reset to pending when updated
        ]);

        return redirect()->route('teacher.application.preview')
            ->with('success', 'Your teacher application has been updated successfully.');
    }
}
