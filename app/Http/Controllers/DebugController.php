<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\User;
use Illuminate\Http\Request;

class DebugController extends Controller
{
    public function test()
    {
        // Test 1: Total classes
        $total = ClassModel::count();
        
        // Test 2: Published classes
        $published = ClassModel::where('is_published', true)->count();
        
        // Test 3: Get all classes
        $allClasses = ClassModel::with('teacher')->get();
        
        // Test 4: Test categories
        $aiCourses = ClassModel::publishedByCategory('ai')->with('teacher')->get();
        
        // Test 5: Trending courses
        $trending = ClassModel::published()
            ->with(['teacher', 'chapters.modules'])
            ->withCount(['chapters', 'modules'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // Test 6: Check enrolled courses (for student)
        $enrolledCourses = collect();
        if (auth()->check() && auth()->user()->isStudent()) {
            $enrolledCourses = ClassModel::published()
                ->with('teacher')
                ->withCount('modules')
                ->take(3)
                ->get();
        }

        return response()->json([
            'user' => auth()->user() ? [
                'id' => auth()->user()->id,
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'role' => auth()->user()->role,
            ] : null,
            'total_classes' => $total,
            'published_classes' => $published,
            'all_classes' => $allClasses->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'category' => $c->category,
                'is_published' => $c->is_published,
                'teacher' => $c->teacher ? $c->teacher->name : null,
            ]),
            'ai_courses' => $aiCourses->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'category' => $c->category,
            ]),
            'trending_courses' => $trending->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'category' => $c->category,
                'chapters_count' => $c->chapters_count,
                'modules_count' => $c->modules_count,
            ]),
            'enrolled_courses' => $enrolledCourses->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'category' => $c->category,
                'modules_count' => $c->modules_count,
                'teacher' => $c->teacher ? $c->teacher->name : null,
            ]),
        ]);
    }

    /**
     * Show email verification debug page
     */
    public function showVerifyEmailDebug()
    {
        if (app()->environment('production')) {
            abort(404);
        }
        
        return view('debug.verify-email');
    }

    /**
     * Verify email for debugging (dev only)
     */
    public function verifyEmailDebug(Request $request)
    {
        if (app()->environment('production')) {
            abort(404);
        }

        $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User dengan email tersebut tidak ditemukan.']);
        }

        // Verify the email
        $user->update([
            'email_verified_at' => now(),
            'email_verification_token' => null,
            'email_verification_sent_at' => null,
        ]);

        return back()->with('success', "✅ Email '{$user->email}' berhasil diverifikasi! Sekarang Anda bisa login.");
    }
}
