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
        
        // For students, show only enrolled courses
        $enrolledCourseIds = \Illuminate\Support\Facades\DB::table('class_student')
            ->where('user_id', $user->id)
            ->pluck('class_id');
        
        $courses = \App\Models\ClassModel::whereIn('id', $enrolledCourseIds)
            ->with('teacher')
            ->withCount(['chapters', 'modules'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // If no enrolled courses, redirect to courses page
        if ($courses->isEmpty()) {
            return redirect()->route('courses')
                ->with('info', 'Anda belum berlangganan course apapun. Silakan pilih course yang ingin Anda ikuti.');
        }
            
        return view('profile.my-courses', compact('courses'));
    }

    public function purchaseHistory()
    {
        $user = auth()->user();
        
        // Sync purchases dengan enrollments yang sudah ada
        $this->syncPurchasesForUser($user);
        
        $purchases = Purchase::where('user_id', $user->id)
            ->with('course.teacher')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('profile.purchase-history', compact('purchases'));
    }

    /**
     * Sync purchase records dengan enrollments yang sudah ada
     * Menambahkan purchase record untuk enrollment yang belum punya purchase
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
                ->first();

            // If no purchase exists, create one
            if (!$existingPurchase) {
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
                        'paid_at' => $enrollment->enrolled_at ?? $enrollment->created_at ?? now(),
                        'created_at' => $enrollment->created_at ?? now(),
                        'updated_at' => $enrollment->updated_at ?? now(),
                    ]);
                }
            }
        }
    }

    public function invoice($id)
    {
        $user = auth()->user();
        
        // Get purchase by ID
        // Student: hanya bisa melihat invoice mereka sendiri
        // Teacher: bisa melihat invoice dari purchases yang terkait dengan courses mereka
        $purchase = Purchase::where('id', $id)
            ->with('course.teacher', 'user')
            ->firstOrFail();
        
        // Check access permission
        if ($user->isStudent()) {
            // Student hanya bisa melihat invoice mereka sendiri
            if ($purchase->user_id !== $user->id) {
                abort(403, 'Anda tidak memiliki akses ke invoice ini.');
            }
        } elseif ($user->isTeacher()) {
            // Teacher bisa melihat invoice dari purchases yang terkait dengan courses mereka
            if ($purchase->course->teacher_id !== $user->id) {
                abort(403, 'Anda tidak memiliki akses ke invoice ini.');
            }
        } else {
            // Admin atau role lain
            abort(403, 'Anda tidak memiliki akses ke invoice ini.');
        }
        
        return view('profile.invoice', compact('purchase'));
    }

    public function downloadInvoice($id)
    {
        $user = auth()->user();
        
        // Get purchase by ID
        // Student: hanya bisa download invoice mereka sendiri
        // Teacher: bisa download invoice dari purchases yang terkait dengan courses mereka
        $purchase = Purchase::where('id', $id)
            ->with('course.teacher', 'user')
            ->firstOrFail();
        
        // Check access permission
        if ($user->isStudent()) {
            // Student hanya bisa download invoice mereka sendiri
            if ($purchase->user_id !== $user->id) {
                abort(403, 'Anda tidak memiliki akses ke invoice ini.');
            }
        } elseif ($user->isTeacher()) {
            // Teacher bisa download invoice dari purchases yang terkait dengan courses mereka
            if ($purchase->course->teacher_id !== $user->id) {
                abort(403, 'Anda tidak memiliki akses ke invoice ini.');
            }
        } else {
            // Admin atau role lain
            abort(403, 'Anda tidak memiliki akses ke invoice ini.');
        }
        
        // Generate PDF
        $pdf = Pdf::loadView('profile.invoice-pdf', compact('purchase'));
        
        // Set filename
        $filename = 'Invoice-' . $purchase->purchase_code . '.pdf';
        
        // Download PDF
        return $pdf->download($filename);
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
        
        $validated = $request->validate([
            'new_course' => 'nullable|boolean',
            'new_chapter' => 'nullable|boolean',
            'new_module' => 'nullable|boolean',
            'module_approved' => 'nullable|boolean',
            'student_enrolled' => 'nullable|boolean',
            'course_rated' => 'nullable|boolean',
            'course_completed' => 'nullable|boolean',
            'announcements' => 'nullable|boolean',
            'promotions' => 'nullable|boolean',
            'course_recommendations' => 'nullable|boolean',
            'learning_stats' => 'nullable|boolean',
        ]);

        // Convert checkbox values to boolean
        $preferencesData = [];
        foreach ($validated as $key => $value) {
            $preferencesData[$key] = $request->has($key) ? true : false;
        }

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
