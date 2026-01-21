@extends('layouts.app')

@section('title', $course->name)

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/welcome.css') }}">
<style>
    .course-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 3rem 0;
        margin-bottom: 2rem;
    }

    .course-purchase-card {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 1.5rem;
        position: sticky;
        top: 20px;
    }

    .course-progress-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 8px;
        padding: 1.5rem;
        position: sticky;
        top: 20px;
    }

    .module-item {
        padding: 1rem;
        border-left: 3px solid #667eea;
        background: #f8f9fa;
        margin-bottom: 0.5rem;
        border-radius: 4px;
    }

    .learning-item {
        display: flex;
        align-items: start;
        margin-bottom: 0.75rem;
    }

    .learning-item i {
        color: #28a745;
        margin-right: 0.75rem;
        margin-top: 0.25rem;
    }

    .review-item {
        border-bottom: 1px solid #e0e0e0;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
    }

    .rating-bar {
        height: 8px;
        background: #e0e0e0;
        border-radius: 4px;
        overflow: hidden;
    }

    .rating-fill {
        height: 100%;
        background: #ffc107;
    }
</style>
@endsection

@section('content')
<!-- Back Link -->
<div class="container mt-3">
    <a href="{{ route('courses') }}" class="text-decoration-none">
        <i class="fas fa-arrow-left me-2"></i>Back to Course
    </a>
</div>

<!-- Course Hero Section -->
<section class="course-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold mb-3">{{ $course->name }}</h1>
                <p class="lead mb-4">{{ $course->description ?? 'No description available' }}</p>
                
                <div class="d-flex flex-wrap gap-4 mb-3">
                    <div>
                        <i class="fas fa-star text-warning me-1"></i>
                        <strong>4.9</strong> <span class="small">(10,224 ratings)</span>
                    </div>
                    <div>
                        <i class="fas fa-users me-1"></i>
                        <strong>{{ $course->students_count ?? 0 }} students</strong>
                    </div>
                    <div>
                        <i class="far fa-clock me-1"></i>
                        <strong>{{ $course->chapters_count ?? 0 }} hours</strong>
                    </div>
                    <div>
                        <small>Last updated {{ $course->updated_at ? $course->updated_at->format('F Y') : 'N/A' }}</small>
                    </div>
                </div>

                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <div class="rounded-circle bg-white text-primary d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fas fa-user-tie"></i>
                        </div>
                    </div>
                    <div>
                        <small class="d-block">Created By</small>
                        <strong>{{ $course->teacher->name ?? 'Unknown Teacher' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Main Content Left -->
            <div class="col-lg-8">
                @if($isEnrolled)
                    <!-- Enrolled View: Progress Card -->
                    <div class="course-progress-card mb-4">
                        <h5 class="mb-3">Your Progress</h5>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Progress</span>
                                <span><strong>{{ number_format($progress, 0) }}%</strong></span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-white" role="progressbar" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            @php
                                $firstChapter = $course->chapters->first();
                                $firstModule = $firstChapter ? $firstChapter->modules->first() : null;
                            @endphp
                            @if($firstModule)
                                <a href="{{ route('module.show', [$course->id, $firstChapter->id, $firstModule->id]) }}" class="btn btn-light btn-lg">
                                    @if($progress == 0)
                                        <i class="fas fa-play me-2"></i>Start Learning
                                    @else
                                        <i class="fas fa-check me-2"></i>Continue Course
                                    @endif
                                </a>
                            @else
                                <button class="btn btn-light btn-lg" disabled>
                                    <i class="fas fa-info-circle me-2"></i>No modules available
                                </button>
                            @endif
                        </div>
                        <div class="mt-3">
                            <small>This course includes:</small>
                            <ul class="list-unstyled mt-2 mb-0">
                                <li><i class="fas fa-check me-2"></i>Articles & demo lessons</li>
                                <li><i class="fas fa-check me-2"></i>Downloadable resources</li>
                                <li><i class="fas fa-check me-2"></i>Full lifetime access</li>
                                <li><i class="fas fa-check me-2"></i>Certificate of completion</li>
                            </ul>
                        </div>
                    </div>
                @else
                    <!-- Not Enrolled View: Purchase Card (will be shown in sidebar) -->
                @endif

                <!-- What you'll learn Section -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="fw-bold mb-4">What you'll learn</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="learning-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Build modern responsive web applications with React</span>
                                </div>
                                <div class="learning-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Implement state management with Redux and Context API</span>
                                </div>
                                <div class="learning-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Handle routing with React Router</span>
                                </div>
                                <div class="learning-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Implement authentication and authorization</span>
                                </div>
                                <div class="learning-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Write clean, maintainable code with TypeScript</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="learning-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Master React Hooks including useState, useEffect, useContext, and custom hooks</span>
                                </div>
                                <div class="learning-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Create reusable components following best practices</span>
                                </div>
                                <div class="learning-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Work with APIs and asynchronous data fetching</span>
                                </div>
                                <div class="learning-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Deploy React applications to production</span>
                                </div>
                                <div class="learning-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Test React components with Jest and React Testing Library</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Course Content Section -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="fw-bold mb-2">Course content</h3>
                        <p class="text-muted mb-4">{{ $course->chapters_count ?? 0 }} chapters · {{ $course->modules_count ?? 0 }} hours</p>

                        @if($course->chapters->count() > 0)
                            <div class="list-group">
                                @foreach($course->chapters as $chapter)
                                <div class="list-group-item border-0 p-3 mb-2" style="background: #f8f9fa; border-radius: 8px;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center flex-grow-1">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-size: 14px;">
                                                <i class="fas fa-info"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">{{ $chapter->title }}</h6>
                                                <small class="text-muted">{{ $chapter->modules->count() }} modules</small>
                                                
                                                @if($chapter->modules->count() > 0)
                                                    <div class="mt-2">
                                                        @foreach($chapter->modules as $mod)
                                                            @if($isEnrolled || auth()->check() && (auth()->user()->isTeacher() || auth()->user()->isAdmin()))
                                                                <a href="{{ route('module.show', [$course->id, $chapter->id, $mod->id]) }}" 
                                                                   class="d-block text-decoration-none text-primary mb-1 small">
                                                                    <i class="fas {{ $mod->type == 'video' ? 'fa-play-circle' : ($mod->type == 'document' ? 'fa-file-pdf' : 'fa-align-left') }} me-1"></i>
                                                                    {{ $mod->title }}
                                                                </a>
                                                            @else
                                                                <div class="d-block text-muted mb-1 small">
                                                                    <i class="fas {{ $mod->type == 'video' ? 'fa-play-circle' : ($mod->type == 'document' ? 'fa-file-pdf' : 'fa-align-left') }} me-1"></i>
                                                                    {{ $mod->title }}
                                                                    <span class="badge bg-secondary ms-2">Locked</span>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <span class="text-muted">{{ $chapter->modules->count() * 24 }} min</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                No chapters available yet.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Requirements Section -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="fw-bold mb-4">Requirements</h3>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Basic knowledge of HTML, CSS, and Javascript</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Familiarity with ES6+ Javascript features</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>A computer with internet connection</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Code editor (VS Code recommended)</li>
                        </ul>
                    </div>
                </div>

                <!-- Description Section -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="fw-bold mb-4">Description</h3>
                        <p>{{ $course->description ?? 'No description available for this course.' }}</p>
                        <p>Learn React.js from scratch and build real-world applications. This comprehensive course covers everything from React fundamentals to advanced concepts like Redux, React Router, Hooks, Context API, and more.</p>
                        <p>You'll build hands-on projects throughout the course, giving you practical experience and confidence to work on professional React projects.</p>
                    </div>
                </div>

                <!-- Instructors Section -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="fw-bold mb-4">Instructors</h3>
                        <div class="d-flex align-items-start">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; font-size: 24px;">
                                {{ strtoupper(substr($course->teacher->name ?? 'T', 0, 1)) }}
                            </div>
                            <div>
                                <h5 class="mb-1">{{ $course->teacher->name ?? 'Unknown Teacher' }}</h5>
                                <p class="text-muted mb-0">Full-stack developer with 10+ years of experience. Passionate about teaching and helping students achieve their goals.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Student Reviews Section -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="fw-bold mb-4">Student Reviews</h3>
                        
                        <div class="row mb-4">
                            <div class="col-md-4 text-center">
                                <h2 class="fw-bold mb-2">4.9</h2>
                                <div class="mb-2">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                </div>
                                <small class="text-muted">Course Rating</small>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-2">
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="me-2" style="width: 60px;">5 <i class="fas fa-star text-warning"></i></span>
                                        <div class="rating-bar flex-grow-1">
                                            <div class="rating-fill" style="width: 70%;"></div>
                                        </div>
                                        <span class="ms-2 small">70%</span>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="me-2" style="width: 60px;">4 <i class="fas fa-star text-warning"></i></span>
                                        <div class="rating-bar flex-grow-1">
                                            <div class="rating-fill" style="width: 20%;"></div>
                                        </div>
                                        <span class="ms-2 small">20%</span>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="me-2" style="width: 60px;">3 <i class="fas fa-star text-warning"></i></span>
                                        <div class="rating-bar flex-grow-1">
                                            <div class="rating-fill" style="width: 5%;"></div>
                                        </div>
                                        <span class="ms-2 small">5%</span>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="me-2" style="width: 60px;">2 <i class="fas fa-star text-warning"></i></span>
                                        <div class="rating-bar flex-grow-1">
                                            <div class="rating-fill" style="width: 3%;"></div>
                                        </div>
                                        <span class="ms-2 small">3%</span>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="me-2" style="width: 60px;">1 <i class="fas fa-star text-warning"></i></span>
                                        <div class="rating-bar flex-grow-1">
                                            <div class="rating-fill" style="width: 2%;"></div>
                                        </div>
                                        <span class="ms-2 small">2%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="review-item">
                            <div class="d-flex align-items-start mb-2">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-size: 14px;">
                                    MJ
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <strong>Michael Johnson</strong>
                                            <div class="mb-1">
                                                <i class="fas fa-star text-warning"></i>
                                                <i class="fas fa-star text-warning"></i>
                                                <i class="fas fa-star text-warning"></i>
                                                <i class="fas fa-star text-warning"></i>
                                                <i class="fas fa-star text-warning"></i>
                                            </div>
                                        </div>
                                        <small class="text-muted">3 weeks ago</small>
                                    </div>
                                    <p class="mb-0">Excellent course! The instructor explains everything clearly and the projects are very practical. I went from knowing nothing about React to building my own applications.</p>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <button class="btn btn-primary">Load More Reviews</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Right -->
            <div class="col-lg-4">
                @if(!$isEnrolled)
                    <!-- Purchase Card for Not Enrolled -->
                    <div class="course-purchase-card">
                        <div class="text-center mb-3">
                            <h3 class="fw-bold mb-0">Rp150,000</h3>
                            <span class="badge bg-danger">10% OFF</span>
                        </div>
                        
                        <div class="d-grid gap-2 mb-3">
                            @auth
                                @if(auth()->user()->isStudent())
                                    <form action="{{ route('course.enroll', $course->id) }}" method="POST" class="mb-2">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-lg w-100">
                                            <i class="fas fa-credit-card me-2"></i>Buy Now (Simulasi)
                                        </button>
                                    </form>
                                    <form action="{{ route('cart.add', $course->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-lg w-100">
                                            <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                                        <i class="fas fa-sign-in-alt me-2"></i>Login to Purchase
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login to Purchase
                                </a>
                            @endauth
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">This course includes:</small>
                            <ul class="list-unstyled mt-2 mb-0">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>8 hours on-demand video</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>2 downloadable resources</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Full lifetime access</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Certificate of completion</li>
                            </ul>
                        </div>

                        <div class="text-center">
                            <a href="{{ route('courses') }}" class="text-decoration-none">
                                <i class="fas fa-arrow-left me-1"></i>Back to Courses
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

@endsection
