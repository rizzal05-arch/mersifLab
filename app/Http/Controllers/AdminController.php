<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ClassModel;
use App\Models\Chapter;
use App\Models\Module;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        // Hitung total kelas, chapter, dan modul
        $totalKelas = ClassModel::count();
        $totalChapter = Chapter::count();
        $totalModul = Module::count();
        $totalUsers = User::count();
        
        $activeSubscribers = User::where('is_subscriber', true)
            ->where(function ($query) {
                $query->whereNull('subscription_expires_at')
                    ->orWhere('subscription_expires_at', '>', now());
            })
            ->count();
        
        // Ambil semua kelas dengan relasi teacher dan counts
        $classes = ClassModel::with('teacher')
            ->withCount(['chapters', 'modules'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalKelas', 
            'totalChapter', 
            'totalModul', 
            'totalUsers', 
            'activeSubscribers', 
            'classes'
        ));
    }

    /**
     * Show all users with subscription status
     */
    public function users()
    {
        $users = User::all();
        return view('admin.users.index', ['users' => $users]);
    }

    /**
     * Subscribe user to access materials
     */
    public function subscribe(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'days' => 'required|integer|min:1|max:365'
        ]);

        $days = (int)$request->days;

        $user->update([
            'is_subscriber' => true,
            'subscription_expires_at' => now()->addDays($days)
        ]);

        return redirect('/admin/users')->with('success', "User {$user->name} telah diaktifkan sebagai subscriber selama {$days} hari");
    }

    /**
     * Unsubscribe user
     */
    public function unsubscribe($id)
    {
        $user = User::findOrFail($id);
        
        $user->update([
            'is_subscriber' => false,
            'subscription_expires_at' => null
        ]);

        return redirect('/admin/users')->with('success', "Langganan user {$user->name} telah dihapus");
    }
}
