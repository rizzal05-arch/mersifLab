@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/welcome.css') }}">
@endsection

@section('content')
<div class="container py-5">
    <!-- Welcome Message -->
    <div class="welcome-banner mb-4" style="background: linear-gradient(135deg, #1a76d1 0%, #3f8eea 100%); padding: 1.5rem 2rem; border-radius: 16px; color: #ffffff; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);">
        <div class="d-flex align-items-center gap-3">
            <div class="welcome-avatar" style="width: 50px; height: 50px; background: rgba(255, 255, 255, 0.25); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 700; color: #ffffff; border: 3px solid rgba(255, 255, 255, 0.3);">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <h5 class="mb-0" style="color: #ffffff; font-size: 1.25rem; font-weight: 400;">Welcome, <strong style="font-weight: 700;">{{ Auth::user()->name }}</strong>!</h5>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-0">Teacher Dashboard</h1>
                    <p class="text-muted">Manage your courses and content</p>
                </div>
                <div>
                    <a href="{{ route('teacher.classes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create New Course
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(isset($featuredCourses) && $featuredCourses->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-header bg-light border-0 p-3" style="border-radius: 10px 10px 0 0;">
                    <h5 class="mb-0">Featured Courses</h5>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        @foreach($featuredCourses as $fclass)
                            <div class="col-md-4 mb-3">
                                <div class="card h-100 shadow-sm border-0">
                                    <div class="card-body p-3">
                                        <h6 class="mb-2">{{ $fclass->name }}</h6>
                                        <p class="small text-muted mb-2">{{ $fclass->teacher->name ?? 'Instructor' }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">{{ $fclass->chapters_count ?? 0 }} Chapters</small>
                                            <a href="{{ auth()->user()->isTeacher() ? route('teacher.course.detail', $fclass->id) : route('student.course.detail', $fclass->id) }}" class="btn btn-sm btn-primary">View</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Statistics Section -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted small mb-1">Total Courses</p>
                            <h3 class="mb-0">{{ $totalKursus ?? 0 }}</h3>
                        </div>
                        <div style="font-size: 2rem; color: #007bff; opacity: 0.2;">
                            <i class="fas fa-book"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted small mb-1">Total Chapters</p>
                            <h3 class="mb-0">{{ $totalChapters ?? 0 }}</h3>
                        </div>
                        <div style="font-size: 2rem; color: #28a745; opacity: 0.2;">
                            <i class="fas fa-layer-group"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted small mb-1">Total Modules</p>
                            <h3 class="mb-0">{{ $totalModules ?? 0 }}</h3>
                        </div>
                        <div style="font-size: 2rem; color: #ffc107; opacity: 0.2;">
                            <i class="fas fa-cube"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted small mb-1">Total Students</p>
                            <h3 class="mb-0">{{ $totalStudents ?? 0 }}</h3>
                        </div>
                        <div style="font-size: 2rem; color: #dc3545; opacity: 0.2;">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses List Section -->
    <div class="card shadow-sm border-0" style="border-radius: 10px;">
        <div class="card-header bg-light border-0 p-4" style="border-radius: 10px 10px 0 0;">
            <h5 class="mb-0">
                <i class="fas fa-book me-2"></i>Your Courses
            </h5>
        </div>
        <div class="card-body p-4">
            @if($classes && $classes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Course Name</th>
                                <th>Chapters</th>
                                <th>Modules</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($classes as $class)
                                <tr>
                                    <td>
                                        <strong>{{ $class->name ?? 'Untitled' }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $class->chapters_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">{{ $class->modules_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $class->created_at ? $class->created_at->format('M d, Y') : 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('teacher.chapters.index', $class->id) }}" class="btn btn-outline-primary" title="View Chapters">
                                                <i class="fas fa-layer-group"></i>
                                            </a>
                                            <a href="{{ route('teacher.classes.edit', $class->id) }}" class="btn btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('teacher.classes.destroy', $class->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox" style="font-size: 2rem; color: #ccc;"></i>
                    <p class="text-muted mt-3">No courses yet. <a href="{{ route('teacher.classes.create') }}">Create your first course</a></p>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Notifications Section -->
    @if(isset($notifications) && $notifications->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-bell me-2 text-primary"></i>Recent Notifications
                            @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                                <span class="badge bg-danger ms-2">{{ $unreadNotificationsCount }} New</span>
                            @endif
                        </h5>
                        <a href="{{ route('teacher.notifications') }}" class="btn btn-sm btn-outline-primary">
                            View All <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($notifications as $notification)
                            <a href="{{ route('teacher.notifications') }}" class="list-group-item list-group-item-action {{ !$notification->is_read ? 'bg-light' : '' }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-1">
                                            @if($notification->type === 'module_approved')
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                            @elseif($notification->type === 'student_enrolled')
                                                <i class="fas fa-user-plus text-success me-2"></i>
                                            @elseif($notification->type === 'course_rated')
                                                <i class="fas fa-star text-warning me-2"></i>
                                            @elseif($notification->type === 'course_completed')
                                                <i class="fas fa-trophy text-primary me-2"></i>
                                            @else
                                                <i class="fas fa-bell text-info me-2"></i>
                                            @endif
                                            <strong class="me-2">{{ $notification->title }}</strong>
                                            @if(!$notification->is_read)
                                                <span class="badge bg-warning">New</span>
                                            @endif
                                        </div>
                                        <p class="text-muted small mb-0">{{ Str::limit($notification->message, 100) }}</p>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
