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
     * Just shows the payment page, no pending purchase created yet
     */
    public function showPayment($plan)
    {
        if (!in_array($plan, ['standard', 'premium'])) {
            return redirect()->route('subscription.page')->with('error', 'Invalid subscription plan.');
        }

        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user already has active subscription
        if ($user->hasActiveSubscription()) {
            return redirect()->route('subscription.page')->with('error', 'Anda sudah memiliki subscription aktif. Tunggu hingga subscription berakhir sebelum membeli yang baru.');
        }

        // Check if there's a recent pending subscription purchase for this user (within 24 hours)
        $hasRecentPendingSubscription = SubscriptionPurchase::where('user_id', $user->id)
            ->where('status', 'pending')
            ->where('created_at', '>=', now()->subHours(24))
            ->first();

        if ($hasRecentPendingSubscription) {
            // Check if invoice already exists
            $hasInvoice = \App\Models\Invoice::where('invoiceable_id', $hasRecentPendingSubscription->id)
                ->where('invoiceable_type', SubscriptionPurchase::class)
                ->exists();

            if ($hasInvoice) {
                return redirect()->route('subscription.page')->with('error', 'Anda sudah memiliki pembelian langganan yang pending. Silakan tunggu persetujuan admin terlebih dahulu.');
            }

            // Use existing pending purchase
            \Illuminate\Support\Facades\Session::put('latest_subscription_purchase_id', $hasRecentPendingSubscription->id);
        }

        // Don't create any purchase record here - only create when user clicks "Bayar Sekarang"
        return view('checkout.subscription', compact('plan'));
    }

    /**
     * Process subscription payment - called when user clicks "Bayar Sekarang"
     * Creates invoice and sends email to user and admin
     */
    public function processPayment(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }
            return redirect()->route('login');
        }

        // Check if user already has active subscription
        if ($user->hasActiveSubscription()) {
            $errorMessage = 'Anda sudah memiliki subscription aktif. Tunggu hingga subscription berakhir sebelum membeli yang baru.';
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $errorMessage], 400);
            }
            return redirect()->route('subscription.page')->with('error', $errorMessage);
        }

        $plan = $request->input('plan');
        $paymentMethod = $request->input('payment_method');
        $paymentProvider = $request->input('payment_provider', 'manual');
        $discountAmount = $request->input('discount_amount', 0);
        $finalAmount = $request->input('final_amount');

        if (!in_array($plan, ['standard', 'premium'])) {
            $errorMessage = 'Invalid subscription plan.';
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $errorMessage], 400);
            }
            return redirect()->route('subscription.page')->with('error', $errorMessage);
        }

        // Create or get subscription purchase (only create when user actually clicks "Bayar Sekarang")
        $subscriptionPurchaseId = session('latest_subscription_purchase_id');
        
        if ($subscriptionPurchaseId) {
            $subscriptionPurchase = SubscriptionPurchase::where('id', $subscriptionPurchaseId)
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->first();
            // Ensure expires_at exists for display (1 month duration)
            if ($subscriptionPurchase && !$subscriptionPurchase->expires_at) {
                try {
                    $subscriptionPurchase->update(['expires_at' => now()->addMonth()]);
                } catch (\Exception $e) {
                    // ignore update failures
                }
            }
        } else {
            // Create new subscription purchase record
            $baseAmount = $plan === 'standard' ? 50000 : 150000;
            
            $subscriptionPurchase = SubscriptionPurchase::create([
                'purchase_code' => SubscriptionPurchase::generatePurchaseCode(),
                'user_id' => $user->id,
                'plan' => $plan,
                'amount' => $baseAmount,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
                'status' => 'pending',
                'payment_method' => $paymentMethod,
                'payment_provider' => $paymentProvider,
                'expires_at' => now()->addMonth(), // set 1 month duration for subscription
            ]);
        }

        if (!$subscriptionPurchase) {
            $errorMessage = 'Gagal membuat pembelian subscription. Silakan coba lagi.';
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $errorMessage], 400);
            }
            return redirect()->route('subscription.payment', $plan)->with('error', $errorMessage);
        }

        // Check if invoice already exists
        $existingInvoice = \App\Models\Invoice::where('invoiceable_id', $subscriptionPurchase->id)
            ->where('invoiceable_type', SubscriptionPurchase::class)
            ->exists();

        if ($existingInvoice) {
            $errorMessage = 'Invoice untuk pembelian ini sudah dibuat. Silakan cek email Anda.';
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $errorMessage], 400);
            }
            return redirect()->route('subscription.page')->with('error', $errorMessage);
        }

        // Set flag to skip auto-invoice creation (kita akan buat manual)
        \Illuminate\Support\Facades\Session::put('skip_auto_invoice', true);

        // Update payment method di subscription purchase
        $subscriptionPurchase->update([
            'payment_method' => $paymentMethod,
            'payment_provider' => $paymentProvider,
        ]);

        // Create invoice - invoice akan otomatis mengirim email via Invoice model boot method
        $features = [
            'standard' => [
                'Access all standard courses',
                'Basic certificate',
                'Email support',
                '1 month validity'
            ],
            'premium' => [
                'Access all courses (standard + premium)',
                'Premium certificate',
                'Priority support',
                'Download materials',
                '1 month validity'
            ]
        ];

        $invoice = \App\Models\Invoice::create([
            'user_id' => $user->id,
            'type' => 'subscription',
            'invoiceable_id' => $subscriptionPurchase->id,
            'invoiceable_type' => SubscriptionPurchase::class,
            'amount' => $subscriptionPurchase->amount,
            'tax_amount' => 0,
            'discount_amount' => $subscriptionPurchase->discount_amount,
            'total_amount' => $subscriptionPurchase->final_amount,
            'currency' => 'IDR',
            'status' => 'pending',
            'payment_method' => $paymentMethod,
            'payment_provider' => $paymentProvider,
            'metadata' => [
                'subscription_plan' => $plan,
                'plan_features' => $features[$plan] ?? [],
                'purchase_code' => $subscriptionPurchase->purchase_code,
            ],
        ]);

        // Remove skip flag
        \Illuminate\Support\Facades\Session::forget('skip_auto_invoice');
        
        // Clear subscription purchase session
        \Illuminate\Support\Facades\Session::forget('latest_subscription_purchase_id');

        // Log activity if method exists
        if (method_exists($user, 'logActivity')) {
            $user->logActivity('subscription_purchase', 'User purchased ' . ucfirst($plan) . ' subscription - Purchase Code: ' . $subscriptionPurchase->purchase_code);
        }

        // Check if request is AJAX/JSON
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Invoice pembayaran telah dikirim ke email Anda.',
                'invoice_number' => $invoice->invoice_number,
            ]);
        }

        return redirect()->route('subscription.page')->with('success', 'Invoice pembayaran telah dikirim ke email Anda. Silakan cek email untuk melakukan pembayaran dan konfirmasi. Tunggu notifikasi bahwa pembayaran telah disetujui oleh admin.');
    }
    
    /**
     * Subscribe user to a plan (redirects to payment checkout)
     * This ensures all subscriptions go through payment verification
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

        // Redirect to payment checkout instead of directly activating
        return redirect()->route('subscription.payment', $plan);
    }

    /**
     * Cancel pending subscription purchase
     */
    public function cancelPendingPurchase(Request $request)
    {
        if (!auth()->check() || !auth()->user()->isStudent()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $user = auth()->user();

        // Find recent pending subscription purchase without invoice
        $pendingPurchase = SubscriptionPurchase::where('user_id', $user->id)
            ->where('status', 'pending')
            ->where('created_at', '>=', now()->subHours(24)) // Only recent purchases
            ->first();

        // Check if there's an invoice for this purchase
        if ($pendingPurchase) {
            $hasInvoice = \App\Models\Invoice::where('invoiceable_id', $pendingPurchase->id)
                ->where('invoiceable_type', SubscriptionPurchase::class)
                ->exists();
            
            if ($hasInvoice) {
                return response()->json(['success' => false, 'message' => 'No cancellable pending purchase found.']);
            }
        }

        if (!$pendingPurchase) {
            return response()->json(['success' => false, 'message' => 'No cancellable pending purchase found.']);
        }

        // Delete the pending purchase
        $pendingPurchase->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pending subscription purchase cancelled successfully.'
        ]);
    }
}
