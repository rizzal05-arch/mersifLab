<?php

namespace App\Http\Controllers;

use App\Models\TeacherApplication;
use App\Models\Notification;
use App\Models\User;
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
        
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:1000',
            'ktp_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'teaching_certificate_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'institution_id_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
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
        $application = TeacherApplication::create([
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

        // Notify all admins about the new teacher application
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'new_teacher_application',
                'title' => 'Permohonan Guru Baru',
                'message' => "Pengguna {$user->name} telah mengajukan permohonan menjadi guru.",
                'notifiable_type' => TeacherApplication::class,
                'notifiable_id' => $application->id,
                'is_read' => false,
            ]);
        }

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
}
