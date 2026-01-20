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
                                <div class="course-card">
                                    <div class="row align-items-center">
                                        <div class="col-md-3">
                                            <div class="course-thumbnail" style="background-color: #007bff; height: 150px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                                <i class="fas fa-book" style="font-size: 3rem; color: white;"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h5 class="course-title">{{ $course->name ?? 'Untitled Course' }}</h5>
                                            <p class="course-meta">
                                                <i class="fas fa-users me-1"></i> 
                                                @if(isset($course->students_count))
                                                    {{ $course->students_count }} Students
                                                @else
                                                    0 Students
                                                @endif
                                            </p>
                                            <p class="text-muted small">
                                                <i class="fas fa-calendar me-1"></i>
                                                Created: {{ $course->created_at ? $course->created_at->format('M d, Y') : 'N/A' }}
                                            </p>
                                        </div>
                                        <div class="col-md-3 text-md-end mt-3 mt-md-0">
                                            <a href="{{ route('teacher.courses') }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i>View
                                            </a>
                                            <a href="{{ route('teacher.classes.edit', $course->id ?? '#') }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-info" role="alert">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>No courses yet.</strong> Create your first course to get started.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
