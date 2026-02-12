<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\SubscriptionPurchase;

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
     * Show subscription payment page
     */
    public function showPayment($plan)
    {
        if (!in_array($plan, ['standard', 'premium'])) {
            return redirect()->route('subscription.page')->with('error', 'Invalid subscription plan.');
        }

        return view('checkout.subscription', compact('plan'));
    }

    /**
     * Process subscription payment
     */
    public function processPayment(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $plan = $request->input('plan');
        $paymentMethod = $request->input('payment_method');
        $discountAmount = $request->input('discount_amount', 0);
        $finalAmount = $request->input('final_amount');

        if (!in_array($plan, ['standard', 'premium'])) {
            return redirect()->route('subscription.page')->with('error', 'Invalid subscription plan.');
        }

        // Calculate base amount
        $baseAmount = $plan === 'standard' ? 50000 : 150000;

        // Create subscription purchase record
        $subscriptionPurchase = SubscriptionPurchase::create([
            'purchase_code' => SubscriptionPurchase::generatePurchaseCode(),
            'user_id' => $user->id,
            'plan' => $plan,
            'amount' => $baseAmount,
            'discount_amount' => $discountAmount,
            'final_amount' => $finalAmount,
            'status' => 'pending',
            'payment_method' => $paymentMethod,
            'payment_provider' => 'whatsapp', // Default to WhatsApp like course purchases
            'notes' => 'Menunggu konfirmasi pembayaran via WhatsApp',
        ]);

        // Log activity if method exists
        if (method_exists($user, 'logActivity')) {
            $user->logActivity('subscription_purchase', 'User purchased ' . ucfirst($plan) . ' subscription - Purchase Code: ' . $subscriptionPurchase->purchase_code);
        }

        return redirect()->route('subscription.page')->with('success', 'Invoice pembayaran telah dikirim ke email Anda. Silakan cek email untuk melakukan pembayaran dan konfirmasi. Tunggu notifikasi bahwa pembayaran telah disetujui oleh admin.');
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
