@extends('layouts.app')

@section('title', 'My Courses - Teacher')

@section('content')
<section class="profile-section py-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                @include('teacher.partials.sidebar')
            </div>
            
            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="profile-content">
                    <div class="profile-header mb-4">
                        <h2 class="profile-title">My Courses</h2>
                        <p class="profile-subtitle">View and manage your courses</p>
                    </div>
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <!-- Courses List -->
                    <div class="courses-list">
                        @if($courses && $courses->count() > 0)
                            @foreach($courses as $course)
                                <div class="course-card mb-3 p-3 border rounded">
                                    <div class="row align-items-center">
                                        <div class="col-md-3">
                                            <div class="course-thumbnail" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: 150px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                                <i class="fas fa-book" style="font-size: 3rem; color: white;"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h5 class="course-title mb-2">{{ $course->name ?? 'Untitled Course' }}</h5>
                                            @if($course->description)
                                            <p class="text-muted small mb-2">{{ Str::limit($course->description, 100) }}</p>
                                            @endif
                                            <div class="course-meta mb-2">
                                                <span class="badge bg-{{ $course->is_published ? 'success' : 'warning' }} me-2">
                                                    {{ $course->is_published ? 'Published' : 'Draft' }}
                                                </span>
                                                <span class="badge bg-info me-2">
                                                    <i class="fas fa-folder me-1"></i>{{ $course->chapters_count ?? 0 }} Chapters
                                                </span>
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-book me-1"></i>{{ $course->modules_count ?? 0 }} Modules
                                                </span>
                                            </div>
                                            <p class="text-muted small mb-0">
                                                <i class="fas fa-calendar me-1"></i>
                                                Created: {{ $course->created_at ? $course->created_at->format('M d, Y') : 'N/A' }}
                                            </p>
                                        </div>
                                        <div class="col-md-3 text-md-end mt-3 mt-md-0">
                                            <div class="d-flex flex-column gap-2">
                                                <a href="{{ route('course.detail', $course->id) }}" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye me-1"></i>View
                                                </a>
                                                <a href="{{ route('teacher.classes.edit', $course->id) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-info" role="alert">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>No courses yet.</strong> Create your first course to get started.
                                <a href="{{ route('teacher.manage.content') }}" class="btn btn-primary btn-sm mt-2">
                                    <i class="fas fa-plus me-1"></i>Create Course
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
