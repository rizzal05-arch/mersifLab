<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
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
                // Simulasi harga - bisa diambil dari database jika ada field price
                $total += 150000; // Default price
            }
        }

        return view('cart.index', compact('courses', 'total'));
    }

    /**
     * Add course to cart
     */
    public function add(Request $request, $courseId)
    {
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
    public function remove(Request $request, $courseId)
    {
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
     * Checkout - enroll all courses in cart
     */
    public function checkout(Request $request)
    {
        $user = auth()->user();
        
        if (!$user || !$user->isStudent()) {
            return redirect()->route('cart')->with('error', 'Hanya student yang bisa checkout.');
        }

        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Keranjang kosong.');
        }

        $enrolledCount = 0;
        $alreadyEnrolled = 0;

        foreach ($cart as $courseId) {
            // Check if already enrolled
            $isEnrolled = DB::table('class_student')
                ->where('class_id', $courseId)
                ->where('user_id', $user->id)
                ->exists();

            if (!$isEnrolled) {
                DB::table('class_student')->insert([
                    'class_id' => $courseId,
                    'user_id' => $user->id,
                    'enrolled_at' => now(),
                    'progress' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $enrolledCount++;
            } else {
                $alreadyEnrolled++;
            }
        }

        // Clear cart
        Session::forget('cart');

        $message = "Berhasil mendaftar ke {$enrolledCount} course!";
        if ($alreadyEnrolled > 0) {
            $message .= " ({$alreadyEnrolled} course sudah terdaftar sebelumnya)";
        }

        return redirect()->route('my-courses')->with('success', $message);
    }
}
