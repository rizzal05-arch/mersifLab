<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NotificationPreference;
use App\Models\Purchase;
use Barryvdh\DomPDF\Facade\Pdf;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index');
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'telephone' => 'nullable|string|max:20',
            'biography' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);
        
        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            
            // Store new avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        } else {
            // Keep existing avatar if no new file uploaded
            unset($validated['avatar']);
        }
        
        $user->update($validated);
        
        return redirect()->route('profile')->with('success', 'Profile updated successfully');
    }

    public function myCourses()
    {
        $user = auth()->user();
        
        // If user is a teacher, redirect to teacher courses
        if ($user->isTeacher()) {
            return redirect()->route('teacher.courses');
        }
        
        // Get purchased course IDs (courses yang sudah dibeli - selalu muncul di My Courses)
        $purchasedCourseIds = Purchase::where('user_id', $user->id)
            ->where('status', 'success')
            ->orderByDesc('paid_at')
            ->pluck('class_id')
            ->unique()
            ->values();

        // Get course IDs yang sudah memiliki minimal 1 completed module (untuk subscription courses)
        // Course hanya muncul di My Courses setelah user mark as complete minimal 1 module
        $coursesWithCompletedModules = \Illuminate\Support\Facades\DB::table('module_completions')
            ->where('user_id', $user->id)
            ->pluck('class_id')
            ->unique()
            ->values();

        // Combine: purchased courses (selalu muncul) + courses dengan completed modules (sudah mark as complete)
        $allCourseIds = $purchasedCourseIds->merge($coursesWithCompletedModules)->unique()->values();

        // Get courses with progress tracking
        $courses = \App\Models\ClassModel::whereIn('id', $allCourseIds)
            ->with('teacher')
            ->withCount(['chapters', 'modules'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Add progress data for each course
        foreach ($courses as $course) {
            $enrollment = \Illuminate\Support\Facades\DB::table('class_student')
                ->where('class_id', $course->id)
                ->where('user_id', $user->id)
                ->first();
            
            if ($enrollment) {
                // Count completed modules
                $completedModules = \Illuminate\Support\Facades\DB::table('module_completions')
                    ->where('class_id', $course->id)
                    ->where('user_id', $user->id)
                    ->count();
                
                // Progress hanya dihitung jika sudah ada completed modules
                if ($completedModules > 0) {
                    $course->progress = $enrollment->progress ?? 0;
                } else {
                    $course->progress = 0;
                }
                
                $course->completed_modules = $completedModules;
                $course->enrolled_at = $enrollment->enrolled_at ?? null;
                
                // Check if user has purchased this course (lifetime access) or only via subscription
                $hasPurchase = $purchasedCourseIds->contains($course->id);
                $course->has_lifetime_access = $hasPurchase;
                
                // Check if subscription is still active (if accessed via subscription)
                if (!$hasPurchase && $user->hasActiveSubscription()) {
                    $course->has_subscription_access = $user->canAccessViaPlanTier($course->price_tier ?? 'standard');
                } else {
                    $course->has_subscription_access = false;
                }
            }
        }
            
        return view('profile.my-courses', compact('courses'));
    }

    public function purchaseHistory()
    {
        $user = auth()->user();
        
        // Sync purchases dengan enrollments yang sudah ada (hanya yang bukan dari subscription)
        $this->syncPurchasesForUser($user);
        
        // Get all purchases
        // Filter: hanya tampilkan purchases yang sudah punya invoice (user sudah klik "Bayar Sekarang")
        // atau purchases dengan status 'success' (langsung dibayar tanpa checkout)
        $allPurchases = Purchase::where('user_id', $user->id)
            ->with('course.teacher')
            ->orderBy('created_at', 'desc')
            ->get()
            ->filter(function($purchase) {
                // Tampilkan jika status success (langsung dibayar)
                if ($purchase->status === 'success') {
                    return true;
                }
                
                // Untuk pending purchases, hanya tampilkan jika sudah punya invoice
                // (yang berarti user sudah klik "Bayar Sekarang")
                if ($purchase->status === 'pending') {
                    // Check if this purchase has an invoice (single purchase invoice)
                    $hasDirectInvoice = \App\Models\Invoice::where('invoiceable_id', $purchase->id)
                        ->where('invoiceable_type', Purchase::class)
                        ->exists();
                    
                    if ($hasDirectInvoice) {
                        return true;
                    }
                    
                    // Check if this purchase is included in a multiple purchases invoice
                    // (invoice dengan metadata purchase_ids yang berisi purchase ini)
                    $hasMultipleInvoice = \App\Models\Invoice::where('invoiceable_type', Purchase::class)
                        ->where('type', 'course')
                        ->whereJsonContains('metadata->purchase_ids', $purchase->id)
                        ->exists();
                    
                    return $hasMultipleInvoice;
                }
                
                // Tampilkan status lain (expired, cancelled, dll)
                return true;
            });
        
        // Filter out purchases that are from subscription
        // Purchase dari subscription biasanya:
        // 1. payment_method = 'enrollment' dan payment_provider = 'system' (dari sync)
        // 2. Atau dibuat SETELAH enrollment dan enrollment dibuat saat user punya subscription aktif
        $purchases = $allPurchases->filter(function($purchase) use ($user) {
            // Skip purchase yang jelas dari subscription sync
            if ($purchase->payment_method === 'enrollment' && $purchase->payment_provider === 'system') {
                // Check if enrollment was created when user had subscription
                $enrollment = \Illuminate\Support\Facades\DB::table('class_student')
                    ->where('user_id', $user->id)
                    ->where('class_id', $purchase->class_id)
                    ->first();
                
                if ($enrollment) {
                    $enrollmentDate = $enrollment->enrolled_at ?? $enrollment->created_at;
                    
                    // Check if purchase was created after enrollment (likely from sync)
                    if ($purchase->created_at >= $enrollmentDate) {
                        // Check if user had subscription when enrollment was created
                        $activeSubscriptionAtEnrollment = \App\Models\SubscriptionPurchase::where('user_id', $user->id)
                            ->where('status', 'success')
                            ->where(function($query) use ($enrollmentDate) {
                                $query->where(function($q) use ($enrollmentDate) {
                                    // Subscription paid/started before or at enrollment
                                    $q->where('paid_at', '<=', $enrollmentDate)
                                      ->orWhereNull('paid_at');
                                })
                                ->where(function($q) use ($enrollmentDate) {
                                    // Subscription expires after enrollment (or never expires)
                                    $q->whereNull('expires_at')
                                      ->orWhere('expires_at', '>', $enrollmentDate);
                                });
                            })
                            ->exists();
                        
                        if ($activeSubscriptionAtEnrollment) {
                            return false; // Skip purchase from subscription
                        }
                    }
                }
            }
            
            // Include all other purchases (real purchases)
            return true;
        })->values();
        
        // Get subscription purchases
        $subscriptionPurchases = \App\Models\SubscriptionPurchase::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Combine purchases and subscription purchases
        // Transform subscription purchases to match purchase structure for view
        $allTransactions = collect();
        
        // Add regular purchases
        foreach ($purchases as $purchase) {
            $allTransactions->push([
                'id' => $purchase->id,
                'type' => 'course',
                'purchase_code' => $purchase->purchase_code,
                'status' => $purchase->status,
                'status_badge' => $purchase->status_badge,
                'amount' => $purchase->amount,
                'payment_method' => $purchase->payment_method,
                'payment_provider' => $purchase->payment_provider,
                'paid_at' => $purchase->paid_at,
                'created_at' => $purchase->created_at,
                'course' => $purchase->course,
                'purchase' => $purchase,
            ]);
        }
        
        // Add subscription purchases
        foreach ($subscriptionPurchases as $subscriptionPurchase) {
            $allTransactions->push([
                'id' => $subscriptionPurchase->id,
                'type' => 'subscription',
                'purchase_code' => $subscriptionPurchase->purchase_code,
                'status' => $subscriptionPurchase->status,
                'status_badge' => $subscriptionPurchase->status_badge,
                'amount' => $subscriptionPurchase->final_amount ?? $subscriptionPurchase->amount,
                'payment_method' => $subscriptionPurchase->payment_method,
                'payment_provider' => $subscriptionPurchase->payment_provider,
                'paid_at' => $subscriptionPurchase->paid_at,
                'created_at' => $subscriptionPurchase->created_at,
                'plan' => $subscriptionPurchase->plan,
                'expires_at' => $subscriptionPurchase->expires_at,
                'subscription_purchase' => $subscriptionPurchase,
            ]);
        }
        
        // Sort by created_at descending
        $allTransactions = $allTransactions->sortByDesc('created_at')->values();
        
        return view('profile.purchase-history', compact('allTransactions'));
    }

    /**
     * Sync purchase records dengan enrollments yang sudah ada
     * Menambahkan purchase record untuk enrollment yang belum punya purchase
     * HANYA untuk enrollment yang BUKAN dari subscription
     */
    private function syncPurchasesForUser($user)
    {
        // Get all enrollments for this user
        $enrollments = \Illuminate\Support\Facades\DB::table('class_student')
            ->where('user_id', $user->id)
            ->get();

        foreach ($enrollments as $enrollment) {
            // Check if purchase already exists for this enrollment
            $existingPurchase = Purchase::where('user_id', $user->id)
                ->where('class_id', $enrollment->class_id)
                ->where('status', 'success')
                ->first();

            // If no purchase exists, check if enrollment is from subscription
            if (!$existingPurchase) {
                $enrollmentDate = $enrollment->enrolled_at ?? $enrollment->created_at;
                
                // Check if user had active subscription when enrollment was created
                $hadActiveSubscription = false;
                if ($enrollmentDate) {
                    // Get subscription purchase that was active at enrollment time
                    $activeSubscriptionAtEnrollment = \App\Models\SubscriptionPurchase::where('user_id', $user->id)
                        ->where('status', 'success')
                        ->where(function($query) use ($enrollmentDate) {
                            $query->where(function($q) use ($enrollmentDate) {
                                // Subscription paid/started before or at enrollment
                                $q->where('paid_at', '<=', $enrollmentDate)
                                  ->orWhereNull('paid_at');
                            })
                            ->where(function($q) use ($enrollmentDate) {
                                // Subscription expires after enrollment (or never expires)
                                $q->whereNull('expires_at')
                                  ->orWhere('expires_at', '>', $enrollmentDate);
                            });
                        })
                        ->exists();
                    
                    // Also check if user had subscription in users table at enrollment time
                    // We can't check historical data, so we check if enrollment was created
                    // when user currently has subscription AND there's no purchase record
                    // This means it's likely from subscription
                    if (!$activeSubscriptionAtEnrollment) {
                        // Check if enrollment date is recent and user currently has subscription
                        // and there's no purchase, it's likely from subscription
                        $daysSinceEnrollment = now()->diffInDays($enrollmentDate);
                        if ($daysSinceEnrollment <= 365 && $user->hasActiveSubscription()) {
                            // Check if there's any purchase created AFTER enrollment
                            // If no purchase exists and user has subscription, likely from subscription
                            $purchaseAfterEnrollment = Purchase::where('user_id', $user->id)
                                ->where('class_id', $enrollment->class_id)
                                ->where('created_at', '>', $enrollmentDate)
                                ->exists();
                            
                            if (!$purchaseAfterEnrollment) {
                                $hadActiveSubscription = true;
                            }
                        }
                    } else {
                        $hadActiveSubscription = true;
                    }
                }
                
                // Only create purchase if enrollment is NOT from subscription
                if (!$hadActiveSubscription) {
                    $class = \App\Models\ClassModel::find($enrollment->class_id);
                    
                    if ($class) {
                        Purchase::create([
                            'purchase_code' => Purchase::generatePurchaseCode(),
                            'user_id' => $user->id,
                            'class_id' => $enrollment->class_id,
                            'amount' => $class->price ?? 0,
                            'status' => 'success',
                            'payment_method' => 'enrollment',
                            'payment_provider' => 'system',
                            'paid_at' => $enrollmentDate ?? now(),
                            'created_at' => $enrollment->created_at ?? now(),
                            'updated_at' => $enrollment->updated_at ?? now(),
                        ]);
                    }
                }
            }
        }
    }

    public function invoice($id)
    {
        $user = auth()->user();
        
        // Try to find as course purchase first
        $purchase = Purchase::where('id', $id)
            ->with('course.teacher', 'user')
            ->first();
        
        // Try to find as subscription purchase if not found as course purchase
        $subscriptionPurchase = null;
        if (!$purchase) {
            $subscriptionPurchase = \App\Models\SubscriptionPurchase::where('id', $id)
                ->with('user')
                ->first();
        }
        
        // Check if we have either purchase or subscription purchase
        if (!$purchase && !$subscriptionPurchase) {
            abort(404, 'Invoice tidak ditemukan.');
        }
        
        // Check access permission
        if ($user->isStudent()) {
            // Student hanya bisa melihat invoice mereka sendiri
            if ($purchase && $purchase->user_id !== $user->id) {
                abort(403, 'Anda tidak memiliki akses ke invoice ini.');
            }
            if ($subscriptionPurchase && $subscriptionPurchase->user_id !== $user->id) {
                abort(403, 'Anda tidak memiliki akses ke invoice ini.');
            }
        } elseif ($user->isTeacher()) {
            // Teacher hanya bisa melihat invoice dari purchases yang terkait dengan courses mereka
            if ($purchase && $purchase->course->teacher_id !== $user->id) {
                abort(403, 'Anda tidak memiliki akses ke invoice ini.');
            }
            if ($subscriptionPurchase) {
                abort(403, 'Anda tidak memiliki akses ke invoice ini.');
            }
        } else {
            // Admin atau role lain
            abort(403, 'Anda tidak memiliki akses ke invoice ini.');
        }
        
        // Return appropriate view based on type
        if ($purchase) {
            return view('profile.invoice', compact('purchase'));
        } else {
            return view('profile.invoice-subscription', ['subscription' => $subscriptionPurchase]);
        }
    }

    public function downloadInvoice($id)
    {
        $user = auth()->user();
        
        // Try to find as course purchase first
        $purchase = Purchase::where('id', $id)
            ->with('course.teacher', 'user')
            ->first();
        
        // Try to find as subscription purchase if not found as course purchase
        $subscriptionPurchase = null;
        if (!$purchase) {
            $subscriptionPurchase = \App\Models\SubscriptionPurchase::where('id', $id)
                ->with('user')
                ->first();
        }
        
        // Check if we have either purchase or subscription purchase
        if (!$purchase && !$subscriptionPurchase) {
            abort(404, 'Invoice tidak ditemukan.');
        }
        
        // Check access permission
        if ($user->isStudent()) {
            // Student hanya bisa download invoice mereka sendiri
            if ($purchase && $purchase->user_id !== $user->id) {
                abort(403, 'Anda tidak memiliki akses ke invoice ini.');
            }
            if ($subscriptionPurchase && $subscriptionPurchase->user_id !== $user->id) {
                abort(403, 'Anda tidak memiliki akses ke invoice ini.');
            }
        } elseif ($user->isTeacher()) {
            // Teacher hanya bisa download invoice dari purchases yang terkait dengan courses mereka
            if ($purchase && $purchase->course->teacher_id !== $user->id) {
                abort(403, 'Anda tidak memiliki akses ke invoice ini.');
            }
            if ($subscriptionPurchase) {
                abort(403, 'Anda tidak memiliki akses ke invoice ini.');
            }
        } else {
            // Admin atau role lain
            abort(403, 'Anda tidak memiliki akses ke invoice ini.');
        }
        
        // Generate PDF
        if ($purchase) {
            $pdf = Pdf::loadView('profile.invoice-pdf', compact('purchase'));
            $filename = 'Invoice-' . $purchase->purchase_code . '.pdf';
        } else {
            $pdf = Pdf::loadView('profile.invoice-subscription-pdf', ['subscription' => $subscriptionPurchase]);
            $filename = 'Invoice-Subscription-' . $subscriptionPurchase->purchase_code . '.pdf';
        }
        
        // Download PDF
        return $pdf->download($filename);
    }

    public function getInvoiceByNumber($invoiceNumber)
    {
        try {
            $user = auth()->user();
            
            // Find invoice by number
            $invoice = \App\Models\Invoice::where('invoice_number', $invoiceNumber)
                ->where('user_id', $user->id)
                ->first();
            
            if (!$invoice) {
                return response()->json(['success' => false, 'message' => 'Invoice tidak ditemukan.'], 404);
            }
            
            return response()->json([
                'success' => true,
                'invoice' => [
                    'id' => $invoice->invoiceable_id, // Return purchase ID for redirect
                    'invoice_number' => $invoice->invoice_number,
                    'type' => $invoice->type,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan.'], 500);
        }
    }

    public function notificationPreferences()
    {
        $user = auth()->user();
        $preferences = $user->getNotificationPreference();
        
        return view('profile.notification-preferences', compact('preferences'));
    }

    public function updateNotificationPreferences(Request $request)
    {
        $user = auth()->user();
        
        // Define all possible notification preference fields
        $allFields = [
            'new_course',
            'new_chapter',
            'new_module',
            'module_approved',
            'student_enrolled',
            'course_rated',
            'course_completed',
            'announcements',
            'promotions',
            'course_recommendations',
            'learning_stats',
        ];

        // Build preferences data - set to true if checkbox is checked, false if unchecked
        $preferencesData = [];
        foreach ($allFields as $field) {
            // Checkbox yang checked akan mengirim value '1' (karena kita set value="1")
            // Checkbox yang unchecked tidak akan dikirim sama sekali, jadi kita set false
            $preferencesData[$field] = $request->has($field) && ($request->input($field) === '1' || $request->input($field) === 1 || $request->input($field) === true || $request->input($field) === 'on');
        }

        // Get or create notification preference
        $preference = $user->getNotificationPreference();
        $preference->update($preferencesData);

        return redirect()->route('notification-preferences')->with('success', 'Notification preferences updated successfully');
    }

    /**
     * Upload avatar via AJAX
     */
    public function uploadAvatar(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);
        
        try {
            // Delete old avatar if exists
            if ($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            
            // Store new avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar' => $avatarPath]);
            
            return response()->json([
                'success' => true,
                'message' => 'Foto profil berhasil diupload',
                'avatar_url' => \Illuminate\Support\Facades\Storage::url($avatarPath)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload foto profil: ' . $e->getMessage()
            ], 500);
        }
    }
}
