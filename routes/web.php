<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Teacher\ClassController;
use App\Http\Controllers\Teacher\ChapterController;
use App\Http\Controllers\Teacher\ModuleController;
use App\Http\Controllers\Teacher\TeacherDashboardController;
use App\Http\Controllers\Teacher\TeacherProfileController;
use App\Http\Controllers\StudentDashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\TeacherController as AdminTeacherController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\SettingController;

// ============================
// PUBLIC ROUTES (No Auth)
// ============================

// Public & Home route
Route::get('/', function () {
    $courses = \App\Models\Course::all();
    return view('home', compact('courses'));
})->name('home');

// Guest & Auth Routes
Route::get('/courses', [CourseController::class, 'index'])->name('courses');
Route::get('/courses/{id}', [CourseController::class, 'detail'])->name('course.detail');

// ============================
// AUTH ROUTES (Login/Register)
// ============================

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::get('/verify', [AuthController::class, 'showVerify'])->name('verify');
Route::post('/verify', [AuthController::class, 'verify'])->name('verify.post');
Route::post('/verify/resend', [AuthController::class, 'resend'])->name('verify.resend');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ============================
// STUDENT ROUTES
// ============================
Route::prefix('student')
    ->name('student.')
    ->middleware(['auth', 'role:student'])
    ->group(function () {
        // Student Dashboard
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        Route::get('/course/{id}', [StudentDashboardController::class, 'courseDetail'])->name('course.detail');
        Route::get('/progress', [StudentDashboardController::class, 'progress'])->name('progress');
    });

// ============================
// TEACHER ROUTES
// ============================
Route::prefix('teacher')
    ->name('teacher.')
    ->middleware(['auth', 'role:teacher'])
    ->group(function () {
        // Teacher Dashboard
        Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
        Route::get('/course/{id}', [TeacherDashboardController::class, 'courseDetail'])->name('course.detail');
        Route::get('/analytics', [TeacherDashboardController::class, 'analytics'])->name('analytics');
        Route::get('/materi-management', [TeacherDashboardController::class, 'materiManagement'])->name('materi.management');

        // Content Management
        Route::get('/manage-content', [ClassController::class, 'manageContent'])->name('manage.content');
        
        // Classes
        Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
        Route::get('/classes/create', [ClassController::class, 'create'])->name('classes.create');
        Route::post('/classes', [ClassController::class, 'store'])->name('classes.store');
        Route::get('/classes/{class}/edit', [ClassController::class, 'edit'])->name('classes.edit');
        Route::put('/classes/{class}', [ClassController::class, 'update'])->name('classes.update');
        Route::delete('/classes/{class}', [ClassController::class, 'destroy'])->name('classes.destroy');
        
        // Chapters
        Route::get('/classes/{class}/chapters', [ChapterController::class, 'index'])->name('chapters.index');
        Route::get('/classes/{class}/chapters/create', [ChapterController::class, 'create'])->name('chapters.create');
        Route::post('/classes/{class}/chapters', [ChapterController::class, 'store'])->name('chapters.store');
        Route::get('/classes/{class}/chapters/{chapter}/edit', [ChapterController::class, 'edit'])->name('chapters.edit');
        Route::put('/classes/{class}/chapters/{chapter}', [ChapterController::class, 'update'])->name('chapters.update');
        Route::delete('/classes/{class}/chapters/{chapter}', [ChapterController::class, 'destroy'])->name('chapters.destroy');
        Route::post('/chapters/reorder', [ChapterController::class, 'reorder'])->name('chapters.reorder');
        
        // Modules
        Route::get('/chapters/{chapter}/modules/create', [ModuleController::class, 'create'])->name('modules.create');
        Route::get('/chapters/{chapter}/modules/create/text', [ModuleController::class, 'createText'])->name('modules.create.text');
        Route::get('/chapters/{chapter}/modules/create/document', [ModuleController::class, 'createDocument'])->name('modules.create.document');
        Route::get('/chapters/{chapter}/modules/create/video', [ModuleController::class, 'createVideo'])->name('modules.create.video');
        
        Route::post('/chapters/{chapter}/modules/text', [ModuleController::class, 'storeText'])->name('modules.store.text');
        Route::post('/chapters/{chapter}/modules/document', [ModuleController::class, 'storeDocument'])->name('modules.store.document');
        Route::post('/chapters/{chapter}/modules/video', [ModuleController::class, 'storeVideo'])->name('modules.store.video');
        
        Route::get('/chapters/{chapter}/modules/{module}/edit', [ModuleController::class, 'edit'])->name('modules.edit');
        Route::put('/chapters/{chapter}/modules/{module}', [ModuleController::class, 'update'])->name('modules.update');
        Route::delete('/chapters/{chapter}/modules/{module}', [ModuleController::class, 'destroy'])->name('modules.destroy');
        Route::post('/modules/reorder', [ModuleController::class, 'reorder'])->name('modules.reorder');
        
        // Teacher Profile Routes
        Route::get('/profile', [TeacherProfileController::class, 'profile'])->name('profile');
        Route::put('/profile/update', [TeacherProfileController::class, 'updateProfile'])->name('profile.update');
        Route::get('/my-courses', [TeacherProfileController::class, 'myCourses'])->name('courses');
        Route::get('/purchase-history', [TeacherProfileController::class, 'purchaseHistory'])->name('purchase.history');
        Route::get('/notifications', [TeacherProfileController::class, 'notifications'])->name('notifications');
        
    });


// ============================
// SHARED AUTHENTICATED ROUTES
// ============================

// Protected Routes (Authenticated users)
Route::middleware(['auth'])->group(function () {
    // Backward compatibility (redirect to role-specific dashboard)
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->isTeacher()) {
            return redirect()->route('teacher.dashboard');
        } elseif ($user->isStudent()) {
            return redirect()->route('student.dashboard');
        }
        return redirect()->route('home');
    })->name('dashboard');
    
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

// ============================
// GOOGLE OAUTH ROUTES
// ============================

// Google OAuth routes - callback harus didefinisikan dulu sebelum route dengan parameter
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
Route::get('/auth/google/{role?}', [GoogleAuthController::class, 'redirect'])->name('auth.google');

// ============================
// ADMIN ROUTES
// ============================

// Admin login routes (separate from public login)
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');

// Admin Dashboard Routes (Protected by auth and admin middleware)
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // Courses Management
        Route::resource('courses', AdminCourseController::class);
        
        // Teachers Management
        Route::resource('teachers', AdminTeacherController::class);
        Route::post('teachers/{id}/toggle-ban', [AdminTeacherController::class, 'toggleBan'])->name('teachers.toggleBan');
        
        // Students Management
        Route::resource('students', AdminStudentController::class);
        
        // Admin Management
        Route::resource('admins', AdminManagementController::class);
        
        // Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/upload-logo', [SettingController::class, 'uploadLogo'])->name('settings.uploadLogo');
        
        // Route Logout
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    });