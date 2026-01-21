@extends('layouts.app')

@section('title', 'All Courses')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/welcome.css') }}">
<style>
    .courses-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 2rem;
        padding: 2rem 0;
    }

    .course-card-wrapper {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .course-card-wrapper .course-card {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .course-card .course-content {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
</style>
@endsection

@section('content')
<section class="py-5">
    <div class="container">
        <!-- Header -->
        <div class="mb-5">
            <h1 class="fw-bold mb-2">Browse All Courses</h1>
            <p class="text-muted">Find the perfect course to advance your skills and career.</p>
        </div>

        <!-- Courses Grid -->
        @if($courses->count() > 0)
            <div class="row g-4">
                @foreach($courses as $course)
                <div class="col-lg-4 col-md-6">
                    <div class="course-card-wrapper">
                        <div class="course-card">
                            <!-- Course Image -->
                            <div class="course-image">
                                <div class="course-placeholder">
                                    <i class="fas fa-book fa-3x"></i>
                                </div>
                            </div>

                            <!-- Course Content -->
                            <div class="course-content">
                                <h6 class="course-title">{{ $course->name }}</h6>
                                
                                <p class="course-instructor">
                                    <i class="fas fa-user-tie me-1"></i>
                                    {{ $course->teacher->name ?? "Teacher" }}
                                </p>

                                @if($course->description)
                                <p class="course-description text-muted small">
                                    {{ Str::limit($course->description, 80) }}
                                </p>
                                @endif

                                <!-- Rating & Meta -->
                                <div class="course-meta">
                                    <div class="rating">
                                        <span class="rating-score">5.0</span>
                                        <div class="stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </div>
                                        <span class="rating-count">(0)</span>
                                    </div>
                                    <div class="duration">
                                        <i class="far fa-folder me-1"></i>
                                        {{ $course->chapters_count ?? 0 }} chapters
                                    </div>
                                </div>

                                <!-- Category Badge -->
                                <div class="mt-2">
                                    <span class="badge bg-primary">
                                        {{ \App\Models\ClassModel::CATEGORIES[$course->category] ?? 'Uncategorized' }}
                                    </span>
                                </div>

                                <!-- Price -->
                                <div class="course-price mt-3">
                                    Rp100,000
                                </div>
                            </div>
                        </div>

                        <!-- Enroll Button -->
                        <a href="{{ route('course.detail', $course->id) }}" class="btn btn-primary btn-sm w-100 mt-3">
                            View Course
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-5">
                {{ $courses->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-5">
                <i class="fas fa-book-open fa-5x text-muted mb-3"></i>
                <h5>No Courses Available</h5>
                <p class="text-muted">Check back soon for new courses.</p>
                <a href="{{ route('home') }}" class="btn btn-primary mt-3">
                    Go Back to Home
                </a>
            </div>
        @endif
    </div>
</section>
@endsection
