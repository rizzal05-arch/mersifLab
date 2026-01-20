# Dokumentasi Struktur Role-Based Authentication (Student & Teacher)

## 📋 Daftar Isi
1. [Struktur Folder](#struktur-folder)
2. [File yang Sudah Dibuat/Diupdate](#file-yang-sudah-dibuatdiupdate)
3. [Cara Kerja](#cara-kerja)
4. [Implementasi & Testing](#implementasi--testing)
5. [Migrasi Database](#migrasi-database)
6. [Best Practices](#best-practices)
7. [Troubleshooting](#troubleshooting)

---

## 📁 Struktur Folder

```
mersifLab/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php ✅ (Updated)
│   │   │   ├── Student/
│   │   │   │   └── StudentDashboardController.php ✅ (New)
│   │   │   ├── Teacher/
│   │   │   │   └── TeacherDashboardController.php ✅ (New)
│   │   │   └── [controller lainnya...]
│   │   └── Middleware/
│   │       └── RoleMiddleware.php ✅ (Updated)
│   └── Models/
│       └── User.php ✅ (Updated dengan isStudent(), isTeacher())
├── routes/
│   └── web.php ✅ (Updated dengan struktur role-based)
├── resources/
│   └── views/
│       ├── dashboard.blade.php ✅ (New - shared view)
│       ├── dashboard/
│       │   ├── student-content.blade.php ✅ (New)
│       │   └── teacher-content.blade.php ✅ (New)
│       └── [views lainnya...]
```

---

## ✅ File yang Sudah Dibuat/Diupdate

### 1. **RoleMiddleware** (`app/Http/Middleware/RoleMiddleware.php`)
```php
// Penggunaan:
Route::middleware('role:student')->group(function () { ... })
Route::middleware('role:teacher')->group(function () { ... })
Route::middleware('role:student,teacher')->group(function () { ... })
```

**Fitur:**
- Validasi role dengan multiple roles support
- Auto-redirect ke dashboard sesuai role jika tidak punya akses
- Error message yang informatif

### 2. **StudentDashboardController** (`app/Http/Controllers/Student/StudentDashboardController.php`)
- Controller khusus untuk Student Dashboard
- Logic: menampilkan kursus yang diikuti, progress belajar
- Routes: `/student/dashboard`, `/student/course/{id}`, `/student/progress`

### 3. **TeacherDashboardController** (`app/Http/Controllers/Teacher/TeacherDashboardController.php`)
- Controller khusus untuk Teacher Dashboard
- Logic: menampilkan kursus yang diajar, analytics, management
- Routes: `/teacher/dashboard`, `/teacher/course/{id}`, `/teacher/analytics`, `/teacher/materi-management`

### 4. **AuthController** (Updated)
- Redirect logic berdasarkan role setelah login:
  - Teacher → `/teacher/dashboard`
  - Student → `/student/dashboard`
- Default role untuk registrasi: `'student'`

### 5. **User Model** (Updated)
- Helper methods:
  - `isAdmin()` - cek apakah admin
  - `isTeacher()` - cek apakah teacher
  - `isStudent()` - cek apakah student
  - `isSubscriber()` - cek apakah subscriber

### 6. **Routes** (Updated `routes/web.php`)
- Struktur:
  ```
  PUBLIC ROUTES (Home, Login, Register)
  ↓
  STUDENT ROUTES (/student/...) + middleware role:student
  ↓
  TEACHER ROUTES (/teacher/...) + middleware role:teacher
  ↓
  SHARED AUTH ROUTES (/profile, /dashboard, etc)
  ↓
  OAUTH ROUTES (Google Auth)
  ↓
  ADMIN ROUTES (/admin/...)
  ```

### 7. **Dashboard Views** (New)
- `dashboard.blade.php` - Main dashboard (shared)
- `dashboard/student-content.blade.php` - Specific content untuk student
- `dashboard/teacher-content.blade.php` - Specific content untuk teacher

---

## 🔄 Cara Kerja

### Flow: Login → Redirect → Dashboard

```
1. User Login (email & password)
   ↓
2. AuthController@login validasi credentials
   ↓
3. Session regenerate & cek role user
   ↓
4. Redirect:
   - Jika role='teacher' → route('teacher.dashboard')
   - Jika role='student' → route('student.dashboard')
   ↓
5. RoleMiddleware cek apakah user punya akses ke route
   ↓
6. Jika ✓ → Controller proses & return view
   Jika ✗ → Redirect dengan error message
```

### Middleware Chain
```
'auth' → Validate user sudah login
  ↓
'role:student' → Validate role = 'student'
  ↓
Controller Action
```

### View Logic
```
dashboard.blade.php (shared view)
  ↓
if ($role === 'student')
  ├── @include('dashboard.student-content')
  │    ├── Stats untuk student (kursus diikuti, materi, progress)
  │    ├── List kursus yang diikuti
  │    └── Quick access (Progress, My Courses, Settings)
  
  elseif ($role === 'teacher')
  ├── @include('dashboard.teacher-content')
       ├── Stats untuk teacher (kursus aktif, student terdaftar, rating)
       ├── List kursus yang diajar
       ├── Management section (Analytics, Materi, Student, Settings)
       └── Recent activity log
```

---

## 🚀 Implementasi & Testing

### 1. Pastikan Tabel Users memiliki kolom `role`

```sql
-- Jika sudah ada migration, pastikan kolom role ada:
ALTER TABLE users ADD COLUMN role VARCHAR(50) DEFAULT 'student';

-- Atau di migration baru:
Schema::table('users', function (Blueprint $table) {
    $table->string('role')->default('student')->nullable();
});
```

### 2. Jalankan Migration
```bash
php artisan migrate
```

### 3. Test Dengan Seed Data (Optional)

Buat seeder untuk test:
```php
// database/seeders/UserSeeder.php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create student user
        User::create([
            'name' => 'John Student',
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        // Create teacher user
        User::create([
            'name' => 'Jane Teacher',
            'email' => 'teacher@example.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);
    }
}
```

Run seeder:
```bash
php artisan db:seed --class=UserSeeder
```

### 4. Test Workflow

**Test Student Login:**
```
URL: http://localhost:8000/login
Email: student@example.com
Password: password
Expected: Redirect ke /student/dashboard
```

**Test Teacher Login:**
```
URL: http://localhost:8000/login
Email: teacher@example.com
Password: password
Expected: Redirect ke /teacher/dashboard
```

**Test Unauthorized Access:**
```
URL: http://localhost:8000/teacher/dashboard
Login as: student@example.com
Expected: Redirect ke /student/dashboard dengan error message
```

---

## 🗄️ Migrasi Database

### Jika kolom `role` belum ada, buat migration baru:

```bash
php artisan make:migration add_role_to_users_table
```

Edit file migration:
```php
// database/migrations/XXXX_XX_XX_XXXXXX_add_role_to_users_table.php

public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->enum('role', ['admin', 'teacher', 'student'])->default('student');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('role');
    });
}
```

Run migration:
```bash
php artisan migrate
```

---

## 📌 Best Practices

### 1. **Folder Structure**
✅ Controller terpisah per role:
```
Controllers/
├── Student/
│   └── StudentDashboardController.php
├── Teacher/
│   └── TeacherDashboardController.php
└── AuthController.php
```

### 2. **Route Organization**
✅ Route grouped by role dengan prefix:
```php
Route::prefix('student')
    ->name('student.')
    ->middleware(['auth', 'role:student'])
    ->group(function () { ... });

Route::prefix('teacher')
    ->name('teacher.')
    ->middleware(['auth', 'role:teacher'])
    ->group(function () { ... });
```

### 3. **View Naming**
✅ Gunakan shared view + partial includes:
```
views/
├── dashboard.blade.php (shared, logic based on $role)
└── dashboard/
    ├── student-content.blade.php (student specific)
    └── teacher-content.blade.php (teacher specific)
```

### 4. **Helper Methods pada Model**
✅ Gunakan helper methods untuk cek role:
```php
@if(auth()->user()->isTeacher())
    {{-- show teacher UI --}}
@endif

@if(auth()->user()->isStudent())
    {{-- show student UI --}}
@endif
```

### 5. **Middleware Usage**
✅ Apply middleware di route bukan di controller:
```php
// ✅ GOOD
Route::get('/dashboard', Controller::class)->middleware('role:student');

// ❌ AVOID
// Di controller constructor
$this->middleware('role:student');
```

### 6. **Error Handling**
✅ Redirect dengan error message:
```php
return redirect()->route('student.dashboard')
    ->with('error', 'Anda tidak memiliki akses.');
```

---

## 🔍 Troubleshooting

### Issue 1: "Class not found" Error pada Controller

**Masalah:** Controller tidak terdeteksi

**Solusi:**
```bash
# Clear cache
php artisan cache:clear
php artisan config:clear

# Regenerate autoload
composer dump-autoload
```

### Issue 2: Middleware tidak jalan

**Masalah:** Middleware 'role' tidak registered

**Solusi:** Pastikan di `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);
})
```

### Issue 3: View tidak ditemukan

**Masalah:** `View [dashboard] not found`

**Solusi:**
1. Pastikan file ada: `resources/views/dashboard.blade.php`
2. Pastikan file partial ada: `resources/views/dashboard/student-content.blade.php`
3. Check if view path correct di controller

### Issue 4: User tidak redirect setelah login

**Masalah:** Login tapi tidak ke dashboard

**Solusi:**
1. Pastikan AuthController.php sudah diupdate
2. Check `auth()->user()->role` value di database
3. Pastikan route name benar (`teacher.dashboard`, `student.dashboard`)

### Issue 5: Infinite redirect loop

**Masalah:** Redirect terus-terusan

**Solusi:**
1. Pastikan middleware tidak di-apply dua kali
2. Check route guard (gunakan 'auth' sekali saja)
3. Debug dengan `dd(auth()->user()->role)` di controller

---

## 📝 Summary Implementasi

| Komponen | File | Status | Keterangan |
|----------|------|--------|-----------|
| Middleware | `app/Http/Middleware/RoleMiddleware.php` | ✅ Updated | Support multi-role |
| Student Controller | `app/Http/Controllers/Student/StudentDashboardController.php` | ✅ Created | Logic student |
| Teacher Controller | `app/Http/Controllers/Teacher/TeacherDashboardController.php` | ✅ Created | Logic teacher |
| Auth Logic | `app/Http/Controllers/AuthController.php` | ✅ Updated | Role-based redirect |
| User Model | `app/Models/User.php` | ✅ Updated | Helper methods |
| Routes | `routes/web.php` | ✅ Updated | Organized by role |
| Dashboard View | `resources/views/dashboard.blade.php` | ✅ Created | Shared view |
| Student Content | `resources/views/dashboard/student-content.blade.php` | ✅ Created | Student specific |
| Teacher Content | `resources/views/dashboard/teacher-content.blade.php` | ✅ Created | Teacher specific |

---

## 🎯 Next Steps

1. ✅ Implementasi di aplikasi Anda
2. Test login dengan berbagai role
3. Sesuaikan `dashboard/student-content.blade.php` dan `teacher-content.blade.php` dengan logic bisnis Anda
4. Tambah relasi database (Student-Course, Teacher-Course) sesuai kebutuhan
5. Implementasi authorization policies untuk aksi create/edit/delete
6. Tambah logging untuk track user activity
7. Setup email notification untuk admin jika ada aktivitas penting

---

**Dokumentasi dibuat pada:** 20 Januari 2026
**Laravel Version:** 11.x
**PHP Version:** 8.1+

