<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DebugController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\MessageController;
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
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\TeacherController as AdminTeacherController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\MessageController as AdminMessageController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Api\ModuleController as ApiModuleController;

// ============================
// PUBLIC ROUTES (No Auth)
// ============================

// Apply maintenance mode middleware to public routes
Route::middleware(['maintenance'])->group(function () {
    // Debug route
    Route::get('/debug/courses', [DebugController::class, 'test']);

    // Public & Home route
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Guest & Auth Routes - Public Course Routes
    Route::get('/courses', [CourseController::class, 'index'])->name('courses');
    Route::get('/course/{id}', [CourseController::class, 'detail'])->name('course.detail');

    // Module Viewing Routes (Public - untuk preview)
    Route::get('/course/{classId}/chapter/{chapterId}/module/{moduleId}', [\App\Http\Controllers\ModuleViewController::class, 'show'])->name('module.show');
    Route::get('/course/{classId}/chapter/{chapterId}/module/{moduleId}/file', [\App\Http\Controllers\ModuleViewController::class, 'serveFile'])->name('module.file');

    // Module API Public Routes
    Route::get('/chapters/{chapterId}/modules', [ApiModuleController::class, 'index']);
    Route::get('/modules/{id}', [ApiModuleController::class, 'show']);
    Route::get('/modules/{id}/download', [ApiModuleController::class, 'download']);
});

// Enrollment Routes (Protected)
Route::middleware(['auth', 'maintenance'])->group(function () {
    Route::post('/course/{classId}/enroll', [\App\Http\Controllers\EnrollmentController::class, 'enroll'])->name('course.enroll');
    Route::post('/course/{classId}/module/{moduleId}/complete', [\App\Http\Controllers\EnrollmentController::class, 'markComplete'])->name('module.complete');
    
    // Rating Routes
    Route::post('/course/{id}/rating', [CourseController::class, 'submitRating'])->name('course.rating.submit');
});

// ============================
// AUTH ROUTES (Login/Register)
// ============================

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->middleware('registration.enabled')->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('registration.enabled')->name('register.post');
Route::get('/verify', [AuthController::class, 'showVerify'])->name('verify');
Route::post('/verify', [AuthController::class, 'verify'])->name('verify.post');
Route::post('/verify/resend', [AuthController::class, 'resend'])->name('verify.resend');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Message Route (Public - untuk About page)
Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/checkout', function () {
    return view('profile.invoice');
})->name('checkout');

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
        Route::get('/classes/{class}/chapters/{chapter}', [ChapterController::class, 'show'])->name('chapters.show');
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
        Route::get('/statistics', [TeacherProfileController::class, 'statistics'])->name('statistics');
        Route::get('/purchase-history', [TeacherProfileController::class, 'purchaseHistory'])->name('purchase.history');
        Route::get('/notifications', [TeacherProfileController::class, 'notifications'])->name('notifications');
        Route::patch('/notifications/{id}/mark-read', [TeacherProfileController::class, 'markNotificationAsRead'])->name('notifications.mark-read');
        Route::get('/notification-preferences', [TeacherProfileController::class, 'notificationPreferences'])->name('notification-preferences');
        Route::put('/notification-preferences/update', [TeacherProfileController::class, 'updateNotificationPreferences'])->name('notification-preferences.update');
        
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
    Route::get('/invoice/{id}/download', [ProfileController::class, 'downloadInvoice'])->name('invoice.download');
    
    // Notification Preferences
    Route::get('/notification-preferences', [ProfileController::class, 'notificationPreferences'])->name('notification-preferences');
    Route::put('/notification-preferences/update', [ProfileController::class, 'updateNotificationPreferences'])->name('notification-preferences.update');
    
    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
});

// ============================
// INSTRUCTOR PROTECTED ROUTES
// ============================

// Module API Protected Routes (Instructor)
Route::middleware(['auth'])
    ->prefix('instructor')
    ->group(function () {
        // Module CRUD operations
        Route::post('/chapters/{chapterId}/modules', [ApiModuleController::class, 'store']);
        Route::put('/modules/{id}', [ApiModuleController::class, 'update']);
        Route::delete('/modules/{id}', [ApiModuleController::class, 'destroy']);
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
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Admin Dashboard Routes (Protected by auth and admin middleware)
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['admin'])
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Courses Management
        Route::resource('courses', AdminCourseController::class);
        Route::get('courses/{id}/moderation', [AdminCourseController::class, 'moderation'])->name('courses.moderation');
        Route::patch('courses/{id}/toggle-status', [AdminController::class, 'toggleStatus'])->name('courses.toggle-status');
        
        // Chapters Moderation
        Route::patch('chapters/{id}/toggle-status', [AdminController::class, 'toggleChapterStatus'])->name('chapters.toggle-status');
        Route::delete('chapters/{id}', [AdminController::class, 'destroyChapter'])->name('chapters.destroy');
        
        // Modules Moderation
        Route::patch('modules/{id}/toggle-status', [AdminController::class, 'toggleModuleStatus'])->name('modules.toggle-status');
        Route::post('modules/{id}/approve', [AdminCourseController::class, 'approveModule'])->name('modules.approve');
        Route::post('modules/{id}/reject', [AdminCourseController::class, 'rejectModule'])->name('modules.reject');
        Route::delete('modules/{id}', [AdminController::class, 'destroyModule'])->name('modules.destroy');
        Route::get('modules/{id}/preview', [AdminController::class, 'previewModule'])->name('modules.preview');
        
        // Materi Moderation (for Course model)
        Route::get('materi/{id}/preview', [AdminController::class, 'previewMateri'])->name('materi.preview');
        Route::patch('materi/{id}/suspend', [AdminController::class, 'suspendMateri'])->name('materi.suspend');
        
        // Teachers Management
        Route::resource('teachers', AdminTeacherController::class);
        Route::post('teachers/{id}/toggle-ban', [AdminTeacherController::class, 'toggleBan'])->name('teachers.toggleBan');
        
        // Students Management
        Route::resource('students', AdminStudentController::class);
        Route::post('students/{id}/toggle-ban', [AdminStudentController::class, 'toggleBan'])->name('students.toggleBan');
        
        // Admin Management
        Route::resource('admins', AdminManagementController::class);
        
        // Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/upload-logo', [SettingController::class, 'uploadLogo'])->name('settings.uploadLogo');
        Route::post('/settings/upload-favicon', [SettingController::class, 'uploadFavicon'])->name('settings.uploadFavicon');
        
        // Messages Management
        Route::get('/messages', [AdminMessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/{message}', [AdminMessageController::class, 'show'])->name('messages.show');
        Route::delete('/messages/{message}', [AdminMessageController::class, 'destroy'])->name('messages.destroy');
        Route::post('/messages/{message}/mark-read', [AdminMessageController::class, 'markRead'])->name('messages.mark-read');
        Route::get('/messages/unread-count', [AdminMessageController::class, 'unreadCount'])->name('messages.unreadCount');
        
        // Notifications Management
        Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/{id}', [\App\Http\Controllers\Admin\NotificationController::class, 'show'])->name('notifications.show');
        Route::post('/notifications/{id}/mark-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
        Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        
        // Route Logout
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    });