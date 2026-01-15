<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Public route
Route::get('/', function () {
    return view('welcome');
});

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    // Google OAuth routes
    Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);
});

// Protected routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Course routes
    Route::get('/courses/{id}', function ($id) {
        $course = \App\Models\Course::findOrFail($id);
        $materi = $course->materi;
        return view('course.show', ['course' => $course, 'materi' => $materi]);
    });

    Route::middleware(['admin'])->group(function () {
        Route::get('/admin/materi', [MateriController::class, 'index']);
        Route::get('/admin/materi/create', [MateriController::class, 'create']);
        Route::post('/admin/materi', [MateriController::class, 'store']);
        Route::delete('/admin/materi/{id}', [MateriController::class, 'destroy']);
        
        // Subscription management
        Route::get('/admin/users', [\App\Http\Controllers\AdminController::class, 'users']);
        Route::post('/admin/users/{id}/subscribe', [\App\Http\Controllers\AdminController::class, 'subscribe']);
        Route::post('/admin/users/{id}/unsubscribe', [\App\Http\Controllers\AdminController::class, 'unsubscribe']);
    });

    Route::get('/materi/{id}', [MateriController::class, 'show']);
    Route::get('/materi/{id}/download', [MateriController::class, 'download'])->name('materi.download');
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');