<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google for authentication
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google callback
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Failed to login with Google');
        }

        // Check if user exists by google_id
        $user = User::where('google_id', $googleUser->id)->first();

        // If user doesn't exist, create new user
        if (!$user) {
            $user = User::where('email', $googleUser->email)->first();

            if (!$user) {
                // Create new user
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => null,
                    'role' => 'user',
                    'is_subscriber' => false,
                ]);
            } else {
                // Update existing user with google_id
                $user->update(['google_id' => $googleUser->id]);
            }
        }

        // Login user
        Auth::login($user, remember: true);

        return redirect('/');
    }
}
