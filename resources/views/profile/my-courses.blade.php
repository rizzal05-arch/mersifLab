@extends('layouts.app')

@section('title', 'My Courses')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/profile.css') }}">
@endsection

@section('content')
<section class="profile-section py-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                @include('profile.partials.sidebar')
            </div>
            
            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="profile-content">
                    <div class="profile-header">
                        <h2 class="profile-title">My Courses</h2>
                        <p class="profile-subtitle">Access and continue your enrolled courses</p>
                    </div>
                    
                    <!-- Course List -->
                    <div class="courses-list">
                        @if(isset($courses) && $courses->count() > 0)
                            @foreach($courses as $course)
                            <div class="course-card">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <div class="course-thumbnail">
                                            @if($course->image)
                                                <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->name }}">
                                            @else
                                                <i class="fas fa-book" style="font-size: 3rem; color: white;"></i>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-3 mt-md-0">
                                        <h5 class="course-title">{{ $course->name ?? 'Untitled Course' }}</h5>
                                        <p class="course-meta mb-2">
                                            <i class="fas fa-chalkboard-teacher me-1"></i> 
                                            {{ $course->teacher->name ?? 'Teacher' }}
                                        </p>
                                        @if($course->description)
                                        <p class="text-muted small mb-2">{{ Str::limit($course->description, 100) }}</p>
                                        @endif
                                        @php
                                            $enrollment = \Illuminate\Support\Facades\DB::table('class_student')
                                                ->where('class_id', $course->id)
                                                ->where('user_id', auth()->id())
                                                ->first();
                                            $progress = $enrollment->progress ?? 0;
                                            $completedModules = \Illuminate\Support\Facades\DB::table('module_completions')
                                                ->where('class_id', $course->id)
                                                ->where('user_id', auth()->id())
                                                ->count();
                                        @endphp
                                        <div class="progress-section">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="progress-label">Your Progress</span>
                                                <span class="progress-percentage">{{ number_format($progress, 1) }}%</span>
                                            </div>
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <small class="text-muted mt-1 d-block">
                                                <i class="fas fa-book-open me-1"></i>
                                                {{ $completedModules }} of {{ $course->modules_count ?? 0 }} modules completed
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-md-end mt-3 mt-md-0">
                                        <a href="{{ route('course.detail', $course->id) }}" class="btn btn-primary w-100">
                                            <i class="fas fa-play me-2"></i>Start Learning
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="empty-state text-center">
                                <i class="fas fa-book-open fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">No Courses Yet</h4>
                                <p class="text-muted">You haven't enrolled in any courses yet.</p>
                                <a href="{{ route('courses') }}" class="btn btn-primary mt-3">
                                    Browse Courses
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