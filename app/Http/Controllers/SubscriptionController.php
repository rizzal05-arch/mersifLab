<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Show subscription plans page
     */
    public function show()
    {
        return view('subscription.index');
    }
    /**
     * Instantly mark current user as subscriber (no payment)
     */
    public function subscribe(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $plan = $request->input('plan');

        $user->update([
            'is_subscriber' => true,
            // null means open/unlimited in current logic
            'subscription_expires_at' => null,
            'subscription_plan' => $plan ? strtolower($plan) : 'standard',
        ]);

        // Optionally log activity if method exists
        if (method_exists($user, 'logActivity')) {
            $user->logActivity('subscribe', 'User subscribed (instant, no payment) - plan: ' . ($plan ?? 'standard'));
        }

        return redirect()->route('subscription.page')->with('success', 'Berhasil berlangganan sebagai ' . ($plan ?? 'Standard') . ' — Anda sekarang mendapatkan akses penuh ke semua materi.');
    }
}
