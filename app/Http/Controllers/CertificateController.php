<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    /**
     * Display user's certificates
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isStudent()) {
            abort(403, 'Only students can view certificates.');
        }

        $certificates = Certificate::with('course')
            ->where('user_id', $user->id)
            ->active()
            ->orderBy('issued_at', 'desc')
            ->paginate(12);

        return view('profile.my-certificates', compact('certificates'));
    }

    /**
     * Preview certificate
     */
    public function preview(Request $request, $id)
    {
        $user = Auth::user();
        
        $certificate = Certificate::with(['user', 'course'])
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->active()
            ->firstOrFail();

        // Generate certificate HTML for preview
        return view('certificates.preview', compact('certificate'));
    }

    /**
     * Download certificate as PDF
     */
    public function download(Request $request, $id)
    {
        $user = Auth::user();
        
        $certificate = Certificate::with(['user', 'course'])
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->active()
            ->firstOrFail();

        // Generate PDF
        $pdf = PDF::loadView('certificates.pdf', compact('certificate'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'serif',
            ]);

        $filename = 'certificate-' . $certificate->certificate_code . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Auto-generate certificate when course is completed (100% progress)
     * This method can be called from course completion logic
     */
    public function generateCertificate($userId, $courseId)
    {
        // Check if certificate already exists
        $existing = Certificate::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->active()
            ->first();

        if ($existing) {
            \Log::info("Certificate already exists for user {$userId}, course {$courseId}: {$existing->certificate_code}");
            return $existing;
        }

        // Check if user has completed the course (100% progress)
        $enrollment = DB::table('class_student')
            ->where('user_id', $userId)
            ->where('class_id', $courseId)
            ->where('progress', 100)
            ->first();

        if (!$enrollment) {
            \Log::warning("Cannot generate certificate: User {$userId} has not completed course {$courseId}");
            return null; // User hasn't completed the course
        }

        try {
            // Create new certificate
            $certificate = Certificate::create([
                'user_id' => $userId,
                'course_id' => $courseId,
                'certificate_code' => Certificate::generateCertificateCode(),
                'issued_at' => now(),
                'status' => 'active',
            ]);

            \Log::info("Certificate generated successfully: {$certificate->certificate_code} for user {$userId}, course {$courseId}");
            return $certificate;
        } catch (\Exception $e) {
            \Log::error("Failed to generate certificate for user {$userId}, course {$courseId}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Check and generate certificates for completed courses
     * This can be called periodically or on course completion
     */
    public function checkAndGenerateCertificates()
    {
        // Find users who have completed courses but don't have certificates
        $completedEnrollments = DB::table('class_student')
            ->where('progress', 100)
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('certificates')
                    ->whereRaw('certificates.user_id = class_student.user_id')
                    ->whereRaw('certificates.course_id = class_student.class_id')
                    ->where('certificates.status', 'active');
            })
            ->get();

        foreach ($completedEnrollments as $enrollment) {
            $this->generateCertificate($enrollment->user_id, $enrollment->class_id);
        }

        return $completedEnrollments->count() . ' certificates generated.';
    }

    // ============================
    // ADMIN METHODS
    // ============================

    /**
     * Display all certificates (admin)
     */
    public function adminIndex(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $certificates = Certificate::with(['user', 'course'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.certificates.index', compact('certificates'));
    }

    /**
     * Show certificate details (admin)
     */
    public function adminShow(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $certificate = Certificate::with(['user', 'course'])
            ->findOrFail($id);

        return view('admin.certificates.show', compact('certificate'));
    }

    /**
     * Preview certificate (admin)
     */
    public function adminPreview(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $certificate = Certificate::with(['user', 'course'])
            ->findOrFail($id);

        return view('certificates.preview', compact('certificate'));
    }

    /**
     * Download certificate (admin)
     */
    public function adminDownload(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $certificate = Certificate::with(['user', 'course'])
            ->findOrFail($id);

        // Generate PDF
        $pdf = PDF::loadView('certificates.pdf', compact('certificate'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'serif',
            ]);

        $filename = 'certificate-' . $certificate->certificate_code . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Revoke certificate (admin)
     */
    public function adminRevoke(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $certificate = Certificate::findOrFail($id);
        
        $certificate->update([
            'status' => 'revoked',
            'revoke_reason' => $request->input('reason', 'Revoked by administrator'),
            'revoked_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Certificate revoked successfully.');
    }

    /**
     * Reactivate certificate (admin)
     */
    public function adminReactivate(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $certificate = Certificate::findOrFail($id);
        
        $certificate->update([
            'status' => 'active',
            'revoke_reason' => null,
            'revoked_at' => null,
        ]);

        return redirect()->back()->with('success', 'Certificate reactivated successfully.');
    }

    /**
     * Regenerate certificate code (admin)
     */
    public function adminRegenerateCode(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $certificate = Certificate::findOrFail($id);
        
        $certificate->update([
            'certificate_code' => Certificate::generateCertificateCode(),
        ]);

        return redirect()->back()->with('success', 'Certificate code regenerated successfully.');
    }

    /**
     * Generate certificate for user (admin)
     */
    public function adminGenerateForUser(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $userId = $request->input('user_id');
        $courseId = $request->input('course_id');

        $certificate = $this->generateCertificate($userId, $courseId);

        if ($certificate) {
            return redirect()->back()->with('success', 'Certificate generated successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to generate certificate. User may not have completed the course.');
        }
    }

    /**
     * Bulk revoke certificates (admin)
     */
    public function adminBulkRevoke(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $certificateIds = $request->input('certificate_ids', []);
        $reason = $request->input('reason', 'Bulk revoked by administrator');

        Certificate::whereIn('id', $certificateIds)
            ->update([
                'status' => 'revoked',
                'revoke_reason' => $reason,
                'revoked_at' => now(),
            ]);

        return redirect()->back()->with('success', count($certificateIds) . ' certificates revoked successfully.');
    }
}
