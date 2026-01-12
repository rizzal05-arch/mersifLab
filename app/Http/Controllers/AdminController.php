<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
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
