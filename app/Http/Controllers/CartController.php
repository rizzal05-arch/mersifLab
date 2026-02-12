<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Display shopping cart
     */
    public function index()
    {
        $cart = Session::get('cart', []);
        $courses = [];
        $total = 0;

        foreach ($cart as $courseId) {
            $course = ClassModel::where('id', $courseId)
                ->where('is_published', true)
                ->with('teacher')
                ->withCount(['chapters', 'modules'])
                ->first();
            
            if ($course) {
                $courses[] = $course;
                // Use discounted price if available, otherwise course price, fallback to 150000
                $priceToUse = $course->discounted_price ?? $course->price ?? 150000;
                $total += (float) $priceToUse;
            }
        }

        return view('cart.index', compact('courses', 'total'));
    }

    /**
     * Add course to cart
     */
    public function add(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:classes,id'
        ]);

        $courseId = $request->input('course_id');
        
        $course = ClassModel::where('id', $courseId)
            ->where('is_published', true)
            ->firstOrFail();

        $cart = Session::get('cart', []);

        // Check if already in cart
        if (in_array($courseId, $cart)) {
            return redirect()->back()->with('info', 'Course sudah ada di keranjang.');
        }

        // Check if already enrolled
        if (auth()->check() && auth()->user()->isStudent()) {
            $isEnrolled = DB::table('class_student')
                ->where('class_id', $courseId)
                ->where('user_id', auth()->id())
                ->exists();

            if ($isEnrolled) {
                return redirect()->back()->with('info', 'Anda sudah terdaftar di course ini.');
            }
        }

        // Add to cart
        $cart[] = $courseId;
        Session::put('cart', $cart);

        return redirect()->back()->with('success', 'Course berhasil ditambahkan ke keranjang!');
    }

    /**
     * Remove course from cart
     */
    public function remove(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:classes,id'
        ]);

        $courseId = $request->input('course_id');
        
        $cart = Session::get('cart', []);
        $cart = array_values(array_filter($cart, function($id) use ($courseId) {
            return $id != $courseId;
        }));

        Session::put('cart', $cart);

        return redirect()->route('cart')->with('success', 'Course berhasil dihapus dari keranjang.');
    }

    /**
     * Clear all items from cart
     */
    public function clear()
    {
        Session::forget('cart');
        return redirect()->route('cart')->with('success', 'Keranjang berhasil dikosongkan.');
    }

    /**
     * Get cart count (for AJAX/API)
     */
    public function count()
    {
        $cart = Session::get('cart', []);
        return response()->json(['count' => count($cart)]);
    }

    /**
     * Checkout - enroll selected courses from cart
     */
    public function checkout(Request $request)
    {
        $user = auth()->user();
        
        if (!$user || !$user->isStudent()) {
            return redirect()->route('cart')->with('error', 'Hanya student yang bisa checkout.');
        }

        // Get selected course IDs from request
        $selectedCourseIds = $request->input('course_ids', []);

        if (empty($selectedCourseIds)) {
            return redirect()->route('cart')->with('error', 'Tidak ada course yang dipilih.');
        }

        $enrolledCount = 0;
        $alreadyEnrolled = 0;

        foreach ($selectedCourseIds as $courseId) {
            // Validate course ID
            $courseId = intval($courseId);
            
            if (!ClassModel::where('id', $courseId)->where('is_published', true)->exists()) {
                continue; // Skip if course not found or not published
            }

            // Check if already enrolled
            $isEnrolled = DB::table('class_student')
                ->where('class_id', $courseId)
                ->where('user_id', $user->id)
                ->exists();

            if (!$isEnrolled) {
                // Get course details
                $course = ClassModel::find($courseId);
                
                if (!$course) {
                    continue; // Skip if course not found
                }
                
                // Enroll student
                DB::table('class_student')->insert([
                    'class_id' => $courseId,
                    'user_id' => $user->id,
                    'enrolled_at' => now(),
                    'progress' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                // Determine amount using discount if present
                $amount = $course->discounted_price ?? $course->price ?? 150000;

                // Create purchase record with actual course price
                Purchase::create([
                    'purchase_code' => Purchase::generatePurchaseCode(),
                    'user_id' => $user->id,
                    'class_id' => $courseId,
                    'amount' => $amount,
                    'status' => 'success',
                    'payment_method' => 'checkout', // Default payment method
                    'payment_provider' => 'system',
                    'paid_at' => now(),
                ]);

                $user->logActivity('purchased', "Membeli kelas: {$course->name}");
                
                $enrolledCount++;
            } else {
                $alreadyEnrolled++;
            }
        }

        // Remove only selected courses from cart
        $cart = Session::get('cart', []);
        $cart = array_values(array_filter($cart, function($id) use ($selectedCourseIds) {
            return !in_array($id, $selectedCourseIds);
        }));
        Session::put('cart', $cart);

        $message = "Berhasil mendaftar ke {$enrolledCount} course!";
        if ($alreadyEnrolled > 0) {
            $message .= " ({$alreadyEnrolled} course sudah terdaftar sebelumnya)";
        }

        return redirect()->route('my-courses')->with('success', $message);
    }

    /**
     * Buy Now: create a pending purchase for a single course and redirect to checkout page
     */
    public function buyNow(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:classes,id'
        ]);

        if (!auth()->check() || !auth()->user()->isStudent()) {
            return redirect()->route('login');
        }

        $courseId = $request->input('course_id');

        $course = ClassModel::where('id', $courseId)->where('is_published', true)->firstOrFail();

        // Check if already enrolled
        $isEnrolled = DB::table('class_student')
            ->where('class_id', $courseId)
            ->where('user_id', auth()->id())
            ->exists();

        if ($isEnrolled) {
            return redirect()->back()->with('info', 'Anda sudah terdaftar di course ini.');
        }

        $amount = $course->discounted_price ?? $course->price ?? 150000;

        // Create a pending purchase record (no enrollment yet)
        $purchase = Purchase::create([
            'purchase_code' => Purchase::generatePurchaseCode(),
            'user_id' => auth()->id(),
            'class_id' => $courseId,
            'amount' => $amount,
            'status' => 'pending',
            'payment_method' => null,
            'payment_provider' => null,
        ]);

        // Store latest purchase for checkout view
        session(['latest_purchase_id' => $purchase->id]);

        return redirect()->route('checkout');
    }

    /**
     * Show checkout/invoice page for the latest pending purchase
     */
    public function showCheckout()
    {
        // Support both single purchase (from buyNow) and multiple purchases (from cart prepareCheckout)
        $purchaseIds = session('latest_purchase_ids');

        if ($purchaseIds && is_array($purchaseIds) && count($purchaseIds) > 0) {
            $purchases = Purchase::with('course.teacher')->whereIn('id', $purchaseIds)->get();
            if ($purchases->isEmpty()) {
                return redirect()->route('cart')->with('error', 'Pembelian tidak ditemukan.');
            }
            return view('cart.checkout', ['purchases' => $purchases]);
        }

        $purchaseId = session('latest_purchase_id') ?? request('purchase_id');

        if (!$purchaseId) {
            return redirect()->route('cart')->with('error', 'Tidak ada pembelian yang ditemukan untuk checkout.');
        }

        $purchase = Purchase::with('course.teacher')->find($purchaseId);

        if (!$purchase) {
            return redirect()->route('cart')->with('error', 'Pembelian tidak ditemukan.');
        }

        return view('cart.checkout', compact('purchase'));
    }

    /**
     * Prepare checkout for multiple cart items: create pending purchases and redirect to checkout
     */
    public function prepareCheckout(Request $request)
    {
        if (!auth()->check() || !auth()->user()->isStudent()) {
            return redirect()->route('login');
        }

        $selectedCourseIds = $request->input('course_ids', Session::get('cart', []));

        if (empty($selectedCourseIds)) {
            return redirect()->route('cart')->with('error', 'Tidak ada course yang dipilih.');
        }

        // Set flag to skip auto-invoice creation for individual purchases
        // We'll create a single combined invoice instead
        Session::put('skip_auto_invoice', true);

        $purchaseIds = [];
        $purchases = [];
        $totalAmount = 0;
        $items = [];

        foreach ($selectedCourseIds as $courseId) {
            $courseId = intval($courseId);
            $course = ClassModel::where('id', $courseId)->where('is_published', true)->first();
            if (!$course) continue;

            // Skip if already enrolled
            $isEnrolled = DB::table('class_student')
                ->where('class_id', $courseId)
                ->where('user_id', auth()->id())
                ->exists();

            if ($isEnrolled) {
                continue;
            }

            $amount = $course->discounted_price ?? $course->price ?? 150000;

            $purchase = Purchase::create([
                'purchase_code' => Purchase::generatePurchaseCode(),
                'user_id' => auth()->id(),
                'class_id' => $courseId,
                'amount' => $amount,
                'status' => 'pending',
                'payment_method' => null,
                'payment_provider' => null,
            ]);

            $purchaseIds[] = $purchase->id;
            $purchases[] = $purchase;
            $totalAmount += $amount;
            
            // Prepare items for invoice metadata
            $items[] = [
                'name' => $course->name ?? 'Course Tidak Diketahui',
                'title' => $course->name ?? 'Course Tidak Diketahui',
                'price' => $amount,
                'amount' => $amount,
                'course_id' => $courseId,
                'purchase_code' => $purchase->purchase_code,
            ];
        }

        // Remove skip flag
        Session::forget('skip_auto_invoice');

        if (empty($purchaseIds)) {
            return redirect()->route('cart')->with('info', 'Tidak ada pembelian baru yang perlu diproses (mungkin semua sudah terdaftar).');
        }

        // Create single combined invoice if multiple purchases
        if (count($purchaseIds) > 1) {
            $firstPurchase = $purchases[0];
            
            \App\Models\Invoice::create([
                'user_id' => auth()->id(),
                'type' => 'course',
                'invoiceable_id' => $firstPurchase->id, // Link to first purchase as primary
                'invoiceable_type' => Purchase::class,
                'amount' => $totalAmount,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => $totalAmount,
                'currency' => 'IDR',
                'status' => 'pending',
                'payment_method' => 'bank_transfer',
                'payment_provider' => 'manual',
                'metadata' => [
                    'items' => $items,
                    'purchase_ids' => $purchaseIds,
                    'purchase_codes' => array_column($items, 'purchase_code'),
                    'is_multiple_courses' => true,
                    'course_count' => count($items),
                ],
            ]);
        }
        // If only 1 purchase, let the Purchase model create the invoice normally
        // (but we already skipped it, so create it manually)
        else {
            $purchase = $purchases[0];
            $purchase->load('course');
            
            \App\Models\Invoice::create([
                'user_id' => auth()->id(),
                'type' => 'course',
                'invoiceable_id' => $purchase->id,
                'invoiceable_type' => Purchase::class,
                'amount' => $purchase->amount,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => $purchase->amount,
                'currency' => 'IDR',
                'status' => 'pending',
                'payment_method' => 'bank_transfer',
                'payment_provider' => 'manual',
                'metadata' => [
                    'items' => $items,
                    'course_name' => $purchase->course->name ?? 'Course Tidak Diketahui',
                    'course_description' => $purchase->course->description ?? '',
                    'purchase_code' => $purchase->purchase_code,
                ],
            ]);
        }

        // Store purchase ids and redirect to checkout view
        session(['latest_purchase_ids' => $purchaseIds]);

        return redirect()->route('checkout');
    }
}
