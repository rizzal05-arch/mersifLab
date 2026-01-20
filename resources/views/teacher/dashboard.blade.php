@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')
<div class="container py-5">
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
</div>
@endsection
