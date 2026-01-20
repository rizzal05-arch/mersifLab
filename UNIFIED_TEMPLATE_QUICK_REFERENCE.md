# 🎯 Unified Template Quick Reference

## The Concept

**One template, two dashboards, permission-aware rendering**

```
shared/classes-index.blade.php
        ↓
        ├─→ Student Dashboard (read-only)
        └─→ Teacher Dashboard (with CRUD)
```

---

## How It Works

### 1. Controllers Load Different Data

**StudentDashboardController:**
```php
$classes = ClassModel::where('is_published', true)->get();
return view('student.dashboard', ['classes' => $classes]);
```

**TeacherDashboardController:**
```php
$classes = ClassModel::where('teacher_id', $user->id)->get();
return view('dashboard.teacher-content', ['classes' => $classes]);
```

### 2. Both Include Same Template

**student-content.blade.php:**
```blade
@include('shared.classes-index', ['classes' => $classes])
```

**teacher-content.blade.php:**
```blade
@include('shared.classes-index', ['classes' => $classes])
```

### 3. Template Checks Permissions

```blade
@can('updateClass', $class)
    {{-- Show edit button --}}
    <button>Edit</button>
@else
    {{-- Show view button --}}
    <button>View</button>
@endcan
```

---

## File Locations

```
resources/views/
├── shared/
│   └── classes-index.blade.php          ← SHARED TEMPLATE
├── dashboard/
│   ├── student-content.blade.php        ← Includes shared template
│   └── teacher-content.blade.php        ← Includes shared template

app/Http/Controllers/
├── StudentDashboardController.php        ← Load data only
└── Teacher/
    └── TeacherDashboardController.php    ← Load data + permissions
```

---

## Template Logic

### Permission Checks

```blade
{{-- Check if user can update class --}}
@can('updateClass', $class)
    <button>Edit Class</button>
@endcan

{{-- Check user role --}}
@if(auth()->user()->isTeacher())
    <span>Teacher-only content</span>
@endif

{{-- Chain checks --}}
@can('updateClass', $class)
    <button>Edit</button>
@elsecan('viewClass', $class)
    <button>View</button>
@endcan
```

---

## What Gets Rendered

### For Students

```
Available Classes
═════════════════════════════

📚 PHP Basics
Taught by: John Doe
5 chapters • 24 modules
[Published]

[View Class]  ← Only button shown
```

### For Teachers

```
My Classes                    [+ Create New Class]
═════════════════════════════

📚 PHP Basics
5 chapters • 24 modules
[Draft]

[⋮ Menu]  ← Can edit/delete
[Manage Content]
```

---

## Key Directives

| Directive | Purpose | Policy |
|-----------|---------|--------|
| `@can('createClass')` | Show create button | Checks role |
| `@can('updateClass', $class)` | Show edit menu | Checks ownership |
| `@can('deleteClass', $class)` | Show delete form | Checks ownership |
| `@can('viewClass', $class)` | Show view button | Checks published |

---

## Controller Comparison

| Aspect | Student | Teacher |
|--------|---------|---------|
| **Query** | `is_published=true` | `teacher_id=user.id` |
| **Data** | Published only | Full access |
| **Permissions** | Checked in template | Provided to template |
| **Buttons** | View only | Edit/Delete |
| **Statistics** | Basic | Full |

---

## Data Flow Diagram

```
┌──────────────────────────┐
│  StudentDashboardController
│  - Get published classes
└──────────────┬───────────┘
               │
        ┌──────▼──────┐
        │ $classes    │
        │ (published) │
        └──────┬──────┘
               │
        ┌──────▼──────────────────────┐
        │ student-content.blade.php   │
        │ @include('shared/...')      │
        └──────┬──────────────────────┘
               │
        ┌──────▼──────────────────────┐
        │ classes-index.blade.php     │
        │ @can('updateClass')?        │
        │ NO → Show View button       │
        └─────────────────────────────┘

┌──────────────────────────┐
│  TeacherDashboardController
│  - Get teacher's classes
└──────────────┬───────────┘
               │
        ┌──────▼──────┐
        │ $classes    │
        │ (all owned) │
        └──────┬──────┘
               │
        ┌──────▼──────────────────────┐
        │ teacher-content.blade.php   │
        │ @include('shared/...')      │
        └──────┬──────────────────────┘
               │
        ┌──────▼──────────────────────┐
        │ classes-index.blade.php     │
        │ @can('updateClass')?        │
        │ YES → Show Edit/Delete      │
        └─────────────────────────────┘
```

---

## Features

✅ Single Blade template used by both dashboards  
✅ No code duplication  
✅ Permission-driven UI (via @can)  
✅ Role-aware messaging  
✅ Responsive cards  
✅ Empty state handling  
✅ Hover effects  
✅ Status badges  
✅ Chapter/Module counts  
✅ Teacher info display

---

## Common Use Cases

### Show Create Button (Teachers Only)
```blade
@can('createClass')
    <a href="{{ route('teacher.classes.create') }}" class="btn btn-primary">
        + Create Class
    </a>
@endcan
```

### Show Edit Menu (Owners Only)
```blade
@can('updateClass', $class)
    <div class="dropdown">
        <button>⋮</button>
        <ul>
            <li>Edit</li>
            <li>Delete</li>
        </ul>
    </div>
@endcan
```

### Conditional Text
```blade
<h2>
    @if(auth()->user()->isTeacher())
        My Classes
    @else
        Available Classes
    @endif
</h2>
```

### Action Links
```blade
@can('updateClass', $class)
    <a href="...manage-content">Manage Content</a>
@elsecan('viewClass', $class)
    <a href="...view">View Class</a>
@endcan
```

---

## Testing

### Test 1: Student Dashboard
```
✓ Login as student
✓ Go to dashboard
✓ Verify: No "Create" button
✓ Verify: No "Edit" dropdown
✓ Verify: "View Class" button visible
```

### Test 2: Teacher Dashboard
```
✓ Login as teacher
✓ Go to dashboard
✓ Verify: "Create" button visible
✓ Verify: "Edit" dropdown visible
✓ Verify: Can click manage content
```

### Test 3: Admin Access
```
✓ Login as admin
✓ Go to any dashboard
✓ Verify: Full access (admin bypass)
```

### Test 4: Unauthorized Access
```
✓ Login as student
✓ Try: /teacher/classes/1/edit
✓ Verify: 403 Forbidden
```

---

## Summary Table

| Aspect | Implementation |
|--------|-----------------|
| **Template** | `shared/classes-index.blade.php` |
| **Student View** | `dashboard/student-content.blade.php` |
| **Teacher View** | `dashboard/teacher-content.blade.php` |
| **Student Controller** | `StudentDashboardController` |
| **Teacher Controller** | `Teacher\TeacherDashboardController` |
| **Permissions** | `ContentPolicy` + `@can` directives |
| **Data Filter** | `is_published` (student), `teacher_id` (teacher) |
| **UI Logic** | Permission checks in template |

---

## Key Benefits

🎯 **DRY** - One template, two dashboards  
🎯 **Maintainable** - Update once = update both  
🎯 **Secure** - Permissions checked in template  
🎯 **Scalable** - Easy to add more roles  
🎯 **Consistent** - Identical UI for both  
🎯 **Clean** - Separation of concerns  

---

**Status: ✅ Complete & Working**
