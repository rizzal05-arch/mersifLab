<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DebugController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AiAssistantController;
use App\Http\Controllers\Teacher\ClassController;
use App\Http\Controllers\Teacher\ChapterController;
use App\Http\Controllers\Teacher\ModuleController;
use App\Http\Controllers\Teacher\TeacherDashboardController;
use App\Http\Controllers\Teacher\TeacherProfileController;
use App\Http\Controllers\TeacherApplicationController;
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
use App\Http\Controllers\Admin\ActivityController as AdminActivityController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SubscriptionController as AdminSubscriptionController;
use App\Http\Controllers\Api\ModuleController as ApiModuleController;
use App\Http\Controllers\CertificateController;

// ============================
// PUBLIC ROUTES (No Auth)
// ============================

// Apply maintenance mode middleware to public routes
Route::middleware(['maintenance'])->group(function () {
    // Debug route
    Route::get('/debug/courses', [DebugController::class, 'test']);

    // Public & Home route
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // ============================
    // AI Assistant (Public)
    // ============================
    Route::get('/ai-assistant/check-limit', [AiAssistantController::class, 'checkLimit']);
    Route::get('/ai-assistant/history', [AiAssistantController::class, 'getHistory']);
    Route::post('/ai-assistant/chat', [AiAssistantController::class, 'chat']);

    // Guest & Auth Routes - Public Course Routes
    Route::get('/courses', [CourseController::class, 'index'])->name('courses');
    Route::get('/course/{id}', [CourseController::class, 'detail'])->name('course.detail');

    // Module API Public Routes
    Route::get('/chapters/{chapterId}/modules', [ApiModuleController::class, 'index']);
    Route::get('/modules/{id}', [ApiModuleController::class, 'show']);

    // Subscription page (public view)
    Route::get('/subscription', [SubscriptionController::class, 'show'])->name('subscription.page');
});

// Enrollment Routes (Protected)
Route::middleware(['auth', 'maintenance'])->group(function () {
    Route::post('/course/{classId}/enroll', [\App\Http\Controllers\EnrollmentController::class, 'enroll'])->name('course.enroll');
    Route::post('/course/{classId}/module/{moduleId}/complete', [\App\Http\Controllers\EnrollmentController::class, 'markComplete'])->name('module.complete');
    Route::post('/course/{classId}/complete-all', [\App\Http\Controllers\EnrollmentController::class, 'completeAllModules'])->name('course.completeAll');
    
    // Rating Routes
    Route::post('/course/{id}/rating', [CourseController::class, 'submitRating'])->name('course.rating.submit');
});

// Module Routes (Protected: view + file)
Route::middleware(['auth', 'maintenance'])->group(function () {
    Route::get('/course/{classId}/chapter/{chapterId}/module/{moduleId}', [\App\Http\Controllers\ModuleViewController::class, 'show'])->name('module.show');
    Route::get('/course/{classId}/chapter/{chapterId}/module/{moduleId}/file', [\App\Http\Controllers\ModuleViewController::class, 'serveFile'])->name('module.file');
    Route::get('/modules/{id}/download', [ApiModuleController::class, 'download']);
});

// ============================
// AUTH ROUTES (Login/Register)
// ============================

// Auth Routes - Apply maintenance mode
Route::middleware(['maintenance'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    // Registration routes with registration.enabled middleware
    Route::middleware('registration.enabled')->group(function () {
        Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    });
    Route::get('/verify', [AuthController::class, 'showVerify'])->name('verify');
    Route::post('/verify', [AuthController::class, 'verify'])->name('verify.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Message Route (Public - untuk About page) - Apply maintenance mode
Route::middleware(['maintenance'])->group(function () {
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
});

Route::middleware(['maintenance'])->group(function () {
    Route::get('/about', function () {
        return view('about');
    })->name('about');

    Route::get('/checkout', [\App\Http\Controllers\CartController::class, 'showCheckout'])->name('checkout');
});

// ============================
// STUDENT ROUTES
// ============================
Route::prefix('student')
    ->name('student.')
    ->middleware(['auth', 'role:student', 'maintenance'])
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
    ->middleware(['auth', 'role:teacher', 'maintenance'])
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
    Route::post('/profile/upload-avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.upload-avatar');
    // Subscription (instant subscribe without payment)
    Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscribe');
    
    // My Courses
    Route::get('/my-courses', [ProfileController::class, 'myCourses'])->name('my-courses');
    
    // Purchase History
    Route::get('/purchase-history', [ProfileController::class, 'purchaseHistory'])->name('purchase-history');
    Route::get('/invoice/{id}', [ProfileController::class, 'invoice'])->name('invoice');
    Route::get('/invoice/{id}/download', [ProfileController::class, 'downloadInvoice'])->name('invoice.download');
    
    // Notification Preferences
    Route::get('/notification-preferences', [ProfileController::class, 'notificationPreferences'])->name('notification-preferences');
    Route::put('/notification-preferences/update', [ProfileController::class, 'updateNotificationPreferences'])->name('notification-preferences.update');
    
    // My Certificates
    Route::get('/my-certificates', [CertificateController::class, 'index'])->name('my-certificates');
    Route::get('/certificate/{id}/preview', [CertificateController::class, 'preview'])->name('certificate.preview');
    Route::get('/certificate/{id}/download', [CertificateController::class, 'download'])->name('certificate.download');
    
    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');
    Route::post('/cart/buy-now', [CartController::class, 'buyNow'])->name('cart.buyNow');
    Route::post('/cart/prepare-checkout', [CartController::class, 'prepareCheckout'])->name('cart.prepareCheckout');
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    
    // Teacher Application Routes
    Route::get('/teacher-application', [TeacherApplicationController::class, 'create'])->name('teacher.application.create');
    Route::post('/teacher-application', [TeacherApplicationController::class, 'store'])->name('teacher.application.store');
    Route::get('/teacher-application/status', [TeacherApplicationController::class, 'show'])->name('teacher.application.status');
    Route::get('/teacher-application/preview', [TeacherApplicationController::class, 'preview'])->name('teacher.application.preview');
    Route::get('/teacher-application/edit', [TeacherApplicationController::class, 'edit'])->name('teacher.application.edit');
    Route::put('/teacher-application', [TeacherApplicationController::class, 'update'])->name('teacher.application.update');
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

// Google OAuth routes - Apply maintenance mode
Route::middleware(['maintenance'])->group(function () {
    // Google OAuth routes - callback harus didefinisikan dulu sebelum route dengan parameter
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
    Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
});

// ============================
// ADMIN ROUTES
// ============================

// Admin login routes (separate from public login) - No maintenance mode for admin login
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Admin Dashboard Routes (Protected by auth and admin middleware)
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['admin'])
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard')->middleware('activity.logger');

        // Activities (Activity Logs)
        Route::get('/activities', [AdminActivityController::class, 'index'])->name('activities.index')->middleware('activity.logger');
        Route::get('/activities/user/{userId}', [AdminActivityController::class, 'userActivities'])->name('activities.user')->middleware('activity.logger');

        // Serve module file for admin preview
        Route::get('modules/{id}/file', [AdminController::class, 'serveModuleFile'])->name('modules.file');
        
        // Categories Management
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->middleware('activity.logger');
        
        // Courses Management
        Route::resource('courses', AdminCourseController::class);
        Route::post('courses/{id}/toggle-feature', [AdminCourseController::class, 'toggleFeatured'])->name('courses.toggleFeature')->middleware('activity.logger');
        Route::get('courses/{id}/moderation', [AdminCourseController::class, 'moderation'])->name('courses.moderation');
        Route::get('courses/{id}/preview', [AdminCourseController::class, 'previewCourse'])->name('courses.preview');
        
        // Chapters Moderation
        Route::delete('chapters/{id}', [AdminController::class, 'destroyChapter'])->name('chapters.destroy');
        
        // Modules Moderation
        Route::post('modules/{id}/approve', [AdminCourseController::class, 'approveModule'])->name('modules.approve');
        Route::post('modules/{id}/reject', [AdminCourseController::class, 'rejectModule'])->name('modules.reject');
        Route::delete('modules/{id}', [AdminController::class, 'destroyModule'])->name('modules.destroy');
        Route::get('modules/{id}/preview', [AdminController::class, 'previewModule'])->name('modules.preview');
        
        // Materi Moderation (for Course model)
        Route::get('materi/{id}/preview', [AdminController::class, 'previewMateri'])->name('materi.preview');
        Route::patch('materi/{id}/suspend', [AdminController::class, 'suspendMateri'])->name('materi.suspend');
        
        // Teachers Management
        Route::resource('teachers', AdminTeacherController::class)->middleware(['ajax.handler', 'activity.logger']);
        Route::post('teachers/{id}/toggle-ban', [AdminTeacherController::class, 'toggleBan'])->name('teachers.toggleBan');
        Route::get('teachers/{teacherId}/class/{classId}/reviews', [AdminTeacherController::class, 'classReviews'])->name('admin.teachers.class.reviews')->middleware('activity.logger');
        
        // Students Management
        Route::resource('students', AdminStudentController::class)->middleware(['ajax.handler', 'activity.logger']);
        Route::post('students/{id}/toggle-ban', [AdminStudentController::class, 'toggleBan'])->name('students.toggleBan');
        Route::post('students/{id}/subscription', [AdminStudentController::class, 'updateSubscription'])->name('students.updateSubscription');
        Route::get('students/{id}/activities', [AdminStudentController::class, 'activities'])->name('students.activities')->middleware('activity.logger');
        Route::post('students/{student}/unlock-course/{purchase}', [AdminStudentController::class, 'unlockCourse'])->name('students.unlock-course');
        Route::post('students/{student}/unlock-all-courses', [AdminStudentController::class, 'unlockAllCourses'])->name('students.unlock-all-courses');
        
        // Admin Management
        Route::resource('admins', AdminManagementController::class)->middleware(['log.admin', 'ajax.handler', 'activity.logger']);
        Route::post('admins/{id}/toggle-status', [AdminManagementController::class, 'toggleStatus'])->name('admins.toggleStatus');
        
        // Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index')->middleware('activity.logger');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update')->middleware('activity.logger');
        Route::post('/settings/upload-logo', [SettingController::class, 'uploadLogo'])->name('settings.uploadLogo')->middleware('activity.logger');
        Route::post('/settings/upload-favicon', [SettingController::class, 'uploadFavicon'])->name('settings.uploadFavicon');
        
        // Messages Management
        Route::get('/messages', [AdminMessageController::class, 'index'])->name('messages.index')->middleware('activity.logger');
        Route::get('/messages/{message}', [AdminMessageController::class, 'show'])->name('messages.show')->middleware('activity.logger');
        Route::delete('/messages/{message}', [AdminMessageController::class, 'destroy'])->name('messages.destroy');
        Route::post('/messages/{message}/mark-read', [AdminMessageController::class, 'markRead'])->name('messages.mark-read');
        Route::get('/messages/unread-count', [AdminMessageController::class, 'unreadCount'])->name('messages.unreadCount');
        
        // Notifications Management
        Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/{id}', [\App\Http\Controllers\Admin\NotificationController::class, 'show'])->name('notifications.show');
        Route::post('/notifications/{id}/mark-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
        Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::post('/notifications/unlock-course/{purchaseId}', [\App\Http\Controllers\Admin\NotificationController::class, 'unlockCourse'])->name('notifications.unlock-course');
        
        // Purchases Management
        Route::get('/purchases/{id}', [\App\Http\Controllers\Admin\NotificationController::class, 'showPurchase'])->name('purchases.show');
        
        // Testimonials Management (Admin)
        Route::resource('testimonials', \App\Http\Controllers\Admin\TestimonialController::class)->middleware('activity.logger');
        Route::post('testimonials/{testimonial}/toggle-publish', [\App\Http\Controllers\Admin\TestimonialController::class, 'togglePublish'])->name('testimonials.togglePublish')->middleware('activity.logger');

        // Teacher Applications Management
        Route::get('/teacher-applications', [\App\Http\Controllers\Admin\TeacherApplicationController::class, 'index'])->name('teacher-applications.index');
        Route::get('/teacher-applications/{teacherApplication}', [\App\Http\Controllers\Admin\TeacherApplicationController::class, 'show'])->name('teacher-applications.show');
        Route::post('/teacher-applications/{teacherApplication}/approve', [\App\Http\Controllers\Admin\TeacherApplicationController::class, 'approve'])->name('teacher-applications.approve');
        Route::post('/teacher-applications/{teacherApplication}/reject', [\App\Http\Controllers\Admin\TeacherApplicationController::class, 'reject'])->name('teacher-applications.reject');
        Route::delete('/teacher-applications/{teacherApplication}', [\App\Http\Controllers\Admin\TeacherApplicationController::class, 'destroy'])->name('teacher-applications.destroy');
        Route::get('/teacher-applications/{teacherApplication}/download/{fileType}', [\App\Http\Controllers\Admin\TeacherApplicationController::class, 'downloadFile'])->name('teacher-applications.download');

        // Test Route for Storage Debugging
        Route::get('/test-storage', function() {
            try {
                $files = \Storage::disk('public')->files('teacher-applications');
                return response()->json([
                    'storage_path' => storage_path('app/public'),
                    'public_path' => public_path(),
                    'storage_link_exists' => file_exists(public_path('storage')),
                    'files_in_directory' => $files,
                    'test_file_exists' => \Storage::disk('public')->exists('teacher-applications/test.txt'),
                    'url' => \Storage::disk('public')->url('teacher-applications/test.txt')
                ]);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        });

        // Route Logout
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        // Subscriptions Monitoring
        Route::get('/subscriptions', [AdminSubscriptionController::class, 'index'])->name('subscriptions.index')->middleware('activity.logger');
    });