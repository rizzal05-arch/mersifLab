# Referensi Penggunaan Middleware & Authorization

## 🔐 RoleMiddleware

### Penggunaan di Routes

**1. Single Role**
```php
Route::get('/student/dashboard', [StudentDashboardController::class, 'index'])
    ->middleware('auth', 'role:student');
```

**2. Multiple Roles (OR logic)**
```php
Route::get('/manage', [ManageController::class, 'index'])
    ->middleware('auth', 'role:teacher,admin');
```

**3. Route Group**
```php
Route::prefix('student')
    ->middleware(['auth', 'role:student'])
    ->group(function () {
        Route::get('/dashboard', ...);
        Route::get('/courses', ...);
    });
```

### Flow Middleware

```
Request → auth (check login) → role:student (check role)
                                    ↓
                            Jika OK ↓ Jika role != student
                         Controller    ↓
                            ↓        Redirect + error
                         Action
```

---

## 🎯 Authorization Policies

### Setup di AppServiceProvider

```php
// app/Providers/AppServiceProvider.php

use App\Models\Course;
use App\Policies\CoursePolicy;

public function boot(): void
{
    Gate::policy(Course::class, CoursePolicy::class);
}
```

### Penggunaan di Controller

**Method 1: authorize()**
```php
public function edit(Request $request, Course $course)
{
    // Throw ForbiddenHttpException jika tidak authorized
    $this->authorize('update', $course);
    
    return view('courses.edit', compact('course'));
}
```

**Method 2: can()**
```php
if ($request->user()->can('update', $course)) {
    // Do something
}
```

**Method 3: cannot()**
```php
if ($request->user()->cannot('delete', $course)) {
    abort(403, 'Anda tidak bisa delete course ini');
}
```

### Penggunaan di Blade Template

```blade
@can('update', $course)
    <a href="{{ route('courses.edit', $course) }}">Edit</a>
@endcan

@cannot('delete', $course)
    <p>Anda tidak bisa menghapus course ini</p>
@endcannot

@if($user->can('create', App\Models\Course::class))
    <a href="{{ route('courses.create') }}">Buat Kursus Baru</a>
@endif
```

---

## 💡 Contoh Implementasi

### StudentDashboardController
```php
<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class StudentDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Hanya student yang bisa akses
     * Protected by: route middleware 'role:student'
     */
    public function index()
    {
        $courses = auth()->user()->enrolledCourses()->get();
        return view('student.dashboard', compact('courses'));
    }

    /**
     * Jika ingin check policy:
     */
    public function enrollCourse(Request $request, Course $course)
    {
        // Authorize: user bisa enroll (student only)
        $this->authorize('enroll', $course);
        
        auth()->user()->enrollCourses()->attach($course);
        return redirect()->back()->with('success', 'Berhasil mendaftar kursus');
    }
}
```

### TeacherDashboardController
```php
<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class TeacherDashboardController extends Controller
{
    /**
     * Hanya teacher yang bisa akses
     * Protected by: route middleware 'role:teacher'
     */
    public function index()
    {
        $courses = auth()->user()->ownedCourses()->get();
        return view('teacher.dashboard', compact('courses'));
    }

    /**
     * Update course - check authorization
     */
    public function updateCourse(Request $request, Course $course)
    {
        // Throws ForbiddenHttpException jika:
        // - User bukan teacher
        // - User bukan pemilik course
        $this->authorize('update', $course);
        
        $course->update($request->validated());
        return redirect()->back()->with('success', 'Course berhasil diupdate');
    }

    /**
     * Delete course - check authorization
     */
    public function deleteCourse(Request $request, Course $course)
    {
        // Throws ForbiddenHttpException jika tidak authorized
        $this->authorize('delete', $course);
        
        $course->delete();
        return redirect()->route('teacher.dashboard')->with('success', 'Course dihapus');
    }
}
```

---

## 🧪 Testing Middleware & Authorization

### Unit Test untuk Policy
```php
// tests/Unit/Policies/CoursePolicyTest.php

namespace Tests\Unit\Policies;

use App\Models\Course;
use App\Models\User;
use PHPUnit\Framework\TestCase;

class CoursePolicyTest extends TestCase
{
    public function test_student_cannot_update_course()
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = Course::factory()->create(['teacher_id' => $student->id + 1]);
        
        $this->assertFalse($student->can('update', $course));
    }

    public function test_teacher_can_update_own_course()
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);
        
        $this->assertTrue($teacher->can('update', $course));
    }
}
```

### Feature Test untuk Routes
```php
// tests/Feature/StudentDashboardTest.php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class StudentDashboardTest extends TestCase
{
    public function test_student_can_access_student_dashboard()
    {
        $student = User::factory()->create(['role' => 'student']);
        
        $response = $this->actingAs($student)
            ->get(route('student.dashboard'));
        
        $response->assertStatus(200);
    }

    public function test_teacher_cannot_access_student_dashboard()
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        
        $response = $this->actingAs($teacher)
            ->get(route('student.dashboard'));
        
        $response->assertStatus(302); // Redirect
        $response->assertRedirect(route('teacher.dashboard'));
    }

    public function test_unauthenticated_user_redirected_to_login()
    {
        $response = $this->get(route('student.dashboard'));
        
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }
}
```

---

## 📋 Checklist Implementasi

- [ ] Update `app/Http/Middleware/RoleMiddleware.php`
- [ ] Create `app/Http/Controllers/Student/StudentDashboardController.php`
- [ ] Create `app/Http/Controllers/Teacher/TeacherDashboardController.php`
- [ ] Update `routes/web.php` dengan route groups
- [ ] Update `app/Models/User.php` dengan helper methods
- [ ] Create `resources/views/dashboard.blade.php`
- [ ] Create `resources/views/dashboard/student-content.blade.php`
- [ ] Create `resources/views/dashboard/teacher-content.blade.php`
- [ ] Register middleware alias di `bootstrap/app.php`
- [ ] Run migration: `php artisan migrate`
- [ ] Run seeder: `php artisan db:seed --class=RoleUserSeeder`
- [ ] Test login dengan berbagai role
- [ ] Setup CoursePolicy untuk authorization
- [ ] Test unauthorized access

---

## ⚠️ Common Mistakes

### ❌ Mistake 1: Middleware di Constructor
```php
// WRONG
public function __construct()
{
    $this->middleware('role:student');
}
```

✅ **Correct:**
```php
// RIGHT - di route
Route::get('/dashboard', Controller::class)
    ->middleware('role:student');
```

### ❌ Mistake 2: Duplicate Middleware
```php
// WRONG
Route::middleware(['auth', 'auth'])
    ->middleware('role:student')
    ->group(...);
```

✅ **Correct:**
```php
// RIGHT
Route::middleware(['auth', 'role:student'])
    ->group(...);
```

### ❌ Mistake 3: Authorize tanpa User
```php
// WRONG
public function update(Course $course)
{
    $this->authorize('update', $course); // Apa user-nya?
}
```

✅ **Correct:**
```php
// RIGHT - Laravel otomatis inject current user
public function update(Request $request, Course $course)
{
    $this->authorize('update', $course); // Otomatis pakai auth()->user()
}
```

---

## 📚 Referensi Resmi

- [Laravel Authorization](https://laravel.com/docs/authorization)
- [Laravel Middleware](https://laravel.com/docs/middleware)
- [Laravel Policies](https://laravel.com/docs/policies)

