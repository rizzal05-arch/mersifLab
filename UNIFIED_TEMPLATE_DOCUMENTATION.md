# 📋 Unified Class Display Template Documentation

## Overview

Both Student and Teacher dashboards now use the **same Blade template** (`shared/classes-index.blade.php`) to display class content. The template dynamically shows or hides CRUD buttons based on user permissions using Laravel's `@can` directive.

---

## Architecture

### Three-Component System

```
┌─────────────────────────────────────┐
│   StudentDashboardController        │
│   - Loads data only                 │
│   - No CRUD permissions             │
└──────────────┬──────────────────────┘
               │ $classes (published only)
               │
┌──────────────▼──────────────────────┐
│   shared/classes-index.blade.php    │
│   - Display logic                   │
│   - Permission checks (@can)        │
│   - Conditional CRUD buttons        │
└──────────────┬──────────────────────┘
               │
       ┌───────┴────────┐
       │                │
┌──────▼──────┐  ┌──────▼──────┐
│  Student    │  │   Teacher   │
│  View       │  │   View      │
│  (no edit)  │  │   (+ edit)  │
└─────────────┘  └─────────────┘

┌─────────────────────────────────────┐
│   TeacherDashboardController        │
│   - Loads data + permissions        │
│   - Teacher's own classes           │
└──────────────┬──────────────────────┘
               │ $classes (all owned)
               │
        (same template)
```

---

## Controllers

### StudentDashboardController

**Data Loading Only:**
```php
public function index()
{
    $student = auth()->user();
    
    // Load published classes only (read-only data)
    $classes = ClassModel::where('is_published', true)
        ->withCount(['chapters', 'modules'])
        ->with('teacher')
        ->get();
    
    $recentModules = Module::where('is_published', true)
        ->latest()
        ->limit(6)
        ->get();
    
    return view('student.dashboard', compact('classes', 'recentModules'));
}
```

**Key Points:**
- ✅ Only published classes
- ✅ No CRUD permissions
- ✅ Simple data queries
- ✅ Includes teacher info for display

---

### TeacherDashboardController

**Data + Permissions:**
```php
public function index()
{
    $user = auth()->user();
    
    // Load teacher's own classes with full permissions
    $classes = ClassModel::where('teacher_id', $user->id)
        ->withCount(['chapters', 'modules'])
        ->with('teacher')
        ->get();
    
    $totalCourses = $classes->count();
    $totalChapters = $user->classes()
        ->withCount('chapters')
        ->get()
        ->sum('chapters_count');
    $totalModules = $classes->flatMap->modules->count();
    
    // Pass data with explicit permission flag
    $data = [
        'user' => $user,
        'classes' => $classes,
        'totalKursus' => $totalCourses,
        'totalChapters' => $totalChapters,
        'totalModules' => $totalModules,
        'totalStudents' => User::where('role', 'student')->count(),
        'role' => 'teacher',
        'canCreate' => true,  // Explicit flag
    ];

    return view('dashboard.teacher-content', $data);
}
```

**Key Points:**
- ✅ Only teacher's own classes
- ✅ Includes statistics
- ✅ Full data access
- ✅ Permissions in data context

---

## Shared Template

### Location
```
resources/views/shared/classes-index.blade.php
```

### Template Structure

#### Header with Permission Check
```blade
<div style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h2>
            @if(auth()->user()->isTeacher() || auth()->user()->isAdmin())
                My Classes
            @else
                Available Classes
            @endif
        </h2>
    </div>
    @can('createClass')
        <a href="{{ route('teacher.classes.create') }}" class="btn btn-primary">
            + Create New Class
        </a>
    @endcan
</div>
```

**Output:**
- **Teachers:** See "My Classes" + Create button
- **Students:** See "Available Classes" (no button)

---

#### Class Cards with Dynamic Buttons

```blade
@foreach ($classes as $class)
    <div class="card class-card">
        <!-- Status Badge -->
        <div>
            @if($class->is_published)
                <span class="badge bg-success">Published</span>
            @else
                <span class="badge bg-warning">Draft</span>
            @endif
        </div>

        <!-- Edit Menu (Teachers Only) -->
        @can('updateClass', $class)
            <div class="dropdown">
                <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{ route('teacher.classes.edit', $class) }}">
                            Edit Class
                        </a>
                    </li>
                    <li>
                        <form action="{{ route('teacher.classes.destroy', $class) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete?')">
                                Delete Class
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        @endcan

        <!-- Conditional Action Button -->
        @can('updateClass', $class)
            {{-- Teacher: Manage Content --}}
            <a href="{{ route('teacher.chapters.index', $class) }}" class="btn btn-primary">
                <i class="fas fa-cog"></i> Manage Content
            </a>
        @elsecan('viewClass', $class)
            {{-- Student: View Content --}}
            <a href="{{ route('student.class.detail', $class) }}" class="btn btn-primary">
                <i class="fas fa-book-open"></i> View Class
            </a>
        @endcan
    </div>
@endforeach
```

**Rendered Output:**

**For Teachers:**
```
┌─────────────────────────────┐
│ My Classes          [+Create]│
├─────────────────────────────┤
│ PHP Basics       [Draft]  ⋮  │
│ 5 chapters, 24 modules      │
│ [Manage Content]            │
│ ⋮ Edit Class / Delete Class │
└─────────────────────────────┘
```

**For Students:**
```
┌─────────────────────────────┐
│ Available Classes           │
├─────────────────────────────┤
│ PHP Basics   [Published]    │
│ By: John Doe                │
│ 5 chapters, 24 modules      │
│ [View Class]                │
└─────────────────────────────┘
```

---

### Permission Logic with @can

#### Method 1: Policy Authorization
```blade
@can('updateClass', $class)
    {{-- Show edit button --}}
@endcan
```

**Behind the scenes:**
```php
// Calls ContentPolicy::updateClass($user, $class)
return $user->isAdmin() || ($user->isTeacher() && $class->teacher_id === $user->id);
```

#### Method 2: Role Check
```blade
@if(auth()->user()->isTeacher())
    {{-- Show teacher-specific content --}}
@endif
```

#### Method 3: Else-Can Chain
```blade
@can('updateClass', $class)
    {{-- Teacher: Edit/Delete --}}
@elsecan('viewClass', $class)
    {{-- Student: View Only --}}
@endcan
```

---

## Empty State Handling

Template includes role-aware empty state:

```blade
@else
    <div class="empty-state">
        <div class="empty-state-icon">📚</div>
        <h5>No Classes Found</h5>
        <p class="text-muted">
            @if(auth()->user()->isTeacher())
                You haven't created any classes yet.
            @else
                No classes available at this moment.
            @endif
        </p>
        @can('createClass')
            <a href="{{ route('teacher.classes.create') }}" class="btn btn-primary">
                Create First Class
            </a>
        @endcan
    </div>
@endif
```

---

## View Includes

### Student Dashboard

```blade
{{-- resources/views/dashboard/student-content.blade.php --}}

<div class="student-dashboard">
    <!-- Stats -->
    <div class="stats-grid">...</div>
    
    <!-- Classes using shared template -->
    <div class="mt-5">
        @include('shared.classes-index', ['classes' => $classes])
    </div>
    
    <!-- Recent Modules -->
    <div class="mt-5">...</div>
    
    <!-- Quick Access -->
    <div class="mt-5">...</div>
</div>
```

### Teacher Dashboard

```blade
{{-- resources/views/dashboard/teacher-content.blade.php --}}

<div class="teacher-dashboard">
    <!-- Stats -->
    <div class="stats-grid">...</div>
    
    <!-- Classes using same shared template -->
    <div class="mt-5">
        @include('shared.classes-index', ['classes' => $classes])
    </div>
    
    <!-- Management Section -->
    <div class="mt-5">...</div>
    
    <!-- Recent Activity -->
    <div class="mt-5">...</div>
</div>
```

---

## Data Flow

### Student Journey

```
1. StudentDashboardController::index()
   ↓
2. Query: ClassModel::where('is_published', true)
   ↓
3. Pass to: view('student.dashboard', ['classes' => ...])
   ↓
4. Include: @include('shared.classes-index')
   ↓
5. @can checks:
   - updateClass? NO (student)
   - viewClass? YES (published)
   ↓
6. Render: View buttons only
```

### Teacher Journey

```
1. TeacherDashboardController::index()
   ↓
2. Query: ClassModel::where('teacher_id', $user->id)
   ↓
3. Pass to: view('dashboard.teacher-content', ['classes' => ...])
   ↓
4. Include: @include('shared.classes-index')
   ↓
5. @can checks:
   - updateClass? YES (owner)
   ↓
6. Render: View + Edit + Delete buttons
```

---

## Key Features

✅ **Single Template, Multiple Views**
- Same HTML structure for both roles
- No code duplication
- Easier to maintain

✅ **Permission-Driven Rendering**
- Uses Laravel policies
- `@can` directives for UI logic
- Security-first approach

✅ **Contextual Display**
- Students: "Available Classes"
- Teachers: "My Classes"
- Different headings, same layout

✅ **Responsive Design**
- Cards, dropdowns, badges
- Mobile-friendly
- Bootstrap 5 styling

✅ **Dynamic Statistics**
- Chapter counts
- Module counts
- Status indicators

✅ **CRUD Operations**
- Edit dropdown (teachers only)
- Delete confirmation
- Action buttons

---

## Template Variables

| Variable | Source | Used By |
|----------|--------|---------|
| `$classes` | Controller | Card loop |
| `auth()->user()` | Global | Permission checks |
| `$class->name` | Model | Card title |
| `$class->description` | Model | Card subtitle |
| `$class->is_published` | Model | Badge display |
| `$class->chapters_count` | withCount | Stats |
| `$class->modules_count` | withCount | Stats |
| `$class->teacher->name` | with() relation | Teacher info |

---

## Styling Classes

```css
.class-card {
    transition: all 0.3s ease;
    border-radius: 10px;
}

.class-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.section-title {
    font-size: 1.75rem;
    color: #333;
    border-bottom: 3px solid #667eea;
}

.empty-state {
    padding: 60px 20px;
    background-color: #f8f9fa;
    border-radius: 10px;
}
```

---

## Routes Used

### From Shared Template

| Action | Route | Middleware |
|--------|-------|-----------|
| Create | `teacher.classes.create` | auth, role:teacher |
| Edit | `teacher.classes.edit` | auth, role:teacher |
| Delete | `teacher.classes.destroy` | auth, role:teacher |
| View | `student.class.detail` | auth, role:student |
| Chapters | `teacher.chapters.index` | auth, role:teacher |

---

## Authorization Policy Methods Used

```php
// From ContentPolicy.php

@can('createClass')              // Show create button
@can('updateClass', $class)      // Show edit/delete
@can('deleteClass', $class)      // Show delete form
@can('viewClass', $class)        // Show view button
```

---

## Benefits

### For Developers
- **DRY Principle**: One template, two views
- **Maintainability**: Update once, both dashboards update
- **Testability**: Single template to test
- **Consistency**: Identical UI structure

### For Users
- **Familiar Interface**: Both dashboards look similar
- **Clear Permissions**: Edit buttons only for owners
- **Fast Loading**: Same queries
- **Intuitive**: Role-based UI adapts automatically

---

## Implementation Checklist

- [x] Create `shared/classes-index.blade.php`
- [x] Update StudentDashboardController
- [x] Update TeacherDashboardController
- [x] Update `dashboard/student-content.blade.php`
- [x] Update `dashboard/teacher-content.blade.php`
- [x] Verify @can directives work
- [x] Test student access (no buttons)
- [x] Test teacher access (with buttons)
- [x] Test admin bypass
- [x] Verify policy calls

---

## Testing Guide

### Student View Test
```bash
# Login as student
# Go to dashboard
# Expected: Classes visible, no edit buttons
```

### Teacher View Test
```bash
# Login as teacher
# Go to dashboard
# Expected: Classes visible, edit/delete dropdowns shown
```

### Authorization Test
```bash
# Login as student
# Try: /teacher/classes/1/edit (in URL)
# Expected: 403 Forbidden
```

---

## Files Modified

1. ✅ `resources/views/shared/classes-index.blade.php` (NEW)
2. ✅ `app/Http/Controllers/StudentDashboardController.php`
3. ✅ `app/Http/Controllers/Teacher/TeacherDashboardController.php`
4. ✅ `resources/views/dashboard/student-content.blade.php`
5. ✅ `resources/views/dashboard/teacher-content.blade.php`

---

## Summary

**Unified Template Design:**
- Single `shared/classes-index.blade.php` template
- Used by both Student and Teacher dashboards
- Permission checks via `@can` directives
- Controllers load appropriate data (read-only vs full access)
- Template renders UI based on user permissions
- Clean separation of concerns
- DRY principle maintained

✅ **Production Ready**

---

**Version:** 1.0  
**Date:** January 20, 2026  
**Status:** Complete ✅
