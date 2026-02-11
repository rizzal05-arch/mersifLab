<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

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
     * Subscribe user to a plan (instantly, no payment)
     */
    public function subscribe(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $plan = $request->input('plan');
        if (!in_array($plan, ['standard', 'premium'])) {
            return redirect()->route('subscription.page')->with('error', 'Invalid subscription plan.');
        }

        // Set subscription to 1 month from now
        $expiresAt = Carbon::now()->addMonth();

        $user->update([
            'is_subscriber' => true,
            'subscription_expires_at' => $expiresAt,
            'subscription_plan' => strtolower($plan),
        ]);

        // Optionally log activity if method exists
        if (method_exists($user, 'logActivity')) {
            $user->logActivity('subscribe', 'User subscribed to ' . ucfirst($plan) . ' plan - expires: ' . $expiresAt->format('Y-m-d'));
        }

        return redirect()->route('subscription.page')->with('success', 'Successfully subscribed to ' . ucfirst($plan) . ' plan! You now have full access to all courses in this tier.');
    }
}
