<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Public route
Route::get('/', function () {
    return view('home');
});

// Guest & Auth Routes
Route::get('/courses', [CourseController::class, 'index'])->name('courses');
Route::get('/about', [AboutController::class, 'index'])->name('about');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::get('/verify', [AuthController::class, 'showVerify'])->name('verify');
Route::post('/verify', [AuthController::class, 'verify'])->name('verify.post');
Route::post('/verify/resend', [AuthController::class, 'resend'])->name('verify.resend');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (Student)
Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
});

// Profile Routes (Protected - harus login)
Route::middleware(['auth'])->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    
    // My Courses
    Route::get('/my-courses', [ProfileController::class, 'myCourses'])->name('my-courses');
    
    // Purchase History
    Route::get('/purchase-history', [ProfileController::class, 'purchaseHistory'])->name('purchase-history');
    Route::get('/invoice/{id}', [ProfileController::class, 'invoice'])->name('invoice');
    
    // Notification Preferences
    Route::get('/notification-preferences', [ProfileController::class, 'notificationPreferences'])->name('notification-preferences');
    Route::put('/notification-preferences/update', [ProfileController::class, 'updateNotificationPreferences'])->name('notification-preferences.update');
    
    // Cart & Notifications
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
});

// // Auth routes
// Route::middleware('guest')->group(function () {
//     Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
//     Route::post('/login', [AuthController::class, 'login']);
    
//     Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
//     Route::post('/register', [AuthController::class, 'register']);
    
//     // Google OAuth routes
//     Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
//     Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);
// });

// // Protected routes
// Route::middleware(['auth'])->group(function () {
//     Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

//     // Course routes
//     Route::get('/courses/{id}', function ($id) {
//         $course = \App\Models\Course::findOrFail($id);
//         $materi = $course->materi;
//         return view('course.show', ['course' => $course, 'materi' => $materi]);
//     });

//     Route::middleware(['admin'])->group(function () {
//         Route::get('/admin/materi', [MateriController::class, 'index']);
//         Route::get('/admin/materi/create', [MateriController::class, 'create']);
//         Route::post('/admin/materi', [MateriController::class, 'store']);
//         Route::delete('/admin/materi/{id}', [MateriController::class, 'destroy']);
        
//         // Subscription management
//         Route::get('/admin/users', [\App\Http\Controllers\AdminController::class, 'users']);
//         Route::post('/admin/users/{id}/subscribe', [\App\Http\Controllers\AdminController::class, 'subscribe']);
//         Route::post('/admin/users/{id}/unsubscribe', [\App\Http\Controllers\AdminController::class, 'unsubscribe']);
//     });

//     Route::get('/materi/{id}', [MateriController::class, 'show']);
//     Route::get('/materi/{id}/download', [MateriController::class, 'download'])->name('materi.download');
// });

// // Logout
// Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');