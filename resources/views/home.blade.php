@extends('layouts.app')

@section('title', 'Home')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/welcome.css') }}">
@endsection

@section('content')

<!-- Continue Learning Section (Only for Authenticated Users) -->
@auth
<section class="py-5 continue-learning-section">
    <div class="container">
        <!-- Welcome Message -->
        <div class="welcome-banner">
            <div class="d-flex align-items-center gap-3">
                <div class="welcome-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <h5 class="mb-0">Welcome back, <strong>{{ Auth::user()->name }}</strong>!</h5>
            </div>
        </div>

        <!-- Continue Learning Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="continue-learning-title mb-0">Continue Learning</h2>
            <a href="{{ route('my-courses') }}" class="view-all-btn">
                View All →
            </a>
        </div>

        <!-- Learning Progress Cards -->
        <div class="row g-4">
            @if(isset($enrolledCourses) && $enrolledCourses->count() > 0)
                @foreach($enrolledCourses->take(3) as $course)
                <div class="col-lg-4 col-md-6">
                    <a href="{{ route('course.detail', $course->id) }}" class="text-decoration-none">
                        <div class="learning-card">
                            <!-- Course Image -->
                            <div class="learning-image">
                                <div class="learning-placeholder">
                                    <i class="fas fa-book-reader fa-2x"></i>
                                </div>
                            </div>

                            <!-- Course Info -->
                            <div class="learning-content">
                                <h6 class="learning-title">{{ $course->name ?? 'Untitled Course' }}</h6>
                                <p class="learning-instructor">
                                    {{ ($course->teacher?->name) ?? "Teacher" }}
                                </p>

                                <!-- Progress Bar -->
                                <div class="learning-progress">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="progress-label">Progress</span>
                                        <span class="progress-percentage">{{ number_format($course->progress ?? 0, 1) }}%</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" 
                                             role="progressbar" 
                                             style="width: {{ $course->progress ?? 0 }}%"
                                             aria-valuenow="{{ $course->progress ?? 0 }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <p class="progress-status mt-2">
                                        <i class="far fa-clock me-1"></i>
                                        {{ $course->completed_modules ?? 0 }} of {{ $course->modules_count ?? 0 }} lessons completed
                                    </p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            @else
                <!-- Empty State -->
                <div class="col-12">
                    <div class="empty-learning-state">
                        <i class="fas fa-graduation-cap fa-4x mb-3"></i>
                        <h5>Start Your Learning Journey</h5>
                        <p class="text-muted mb-4">You haven't enrolled in any courses yet. Explore our courses and start learning today!</p>
                        <a href="{{ route('courses') }}" class="btn btn-primary">
                            Browse Courses
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
@endauth

<!-- Hero Section -->
<section class="hero-section py-5" style="background:#1f7ae0">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="bg-white text-dark p-4 rounded-4 shadow-sm" style="max-width:420px">
                    <h4 class="fw-bold">This big sale ends today</h4>
                    <p class="mb-0">
                        But your big year is just beginning.<br>
                        Pick up the courses from <strong>Rp109,000</strong> for your 2026.
                    </p>
                    @auth
                        <a href="{{ route('my-courses') }}" class="btn btn-primary btn-sm mt-3">
                            <i class="fas fa-book me-2"></i>My Courses
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-sm mt-3">
                            <i class="fas fa-sign-in-alt me-2"></i>Get Started
                        </a>
                    @endauth
                </div>
            </div>

            <div class="col-lg-6 text-center">
                <img src="{{ asset('assets/img/hero.png') }}"
                     class="img-fluid rounded-4"
                     alt="Hero Image">
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section py-5">
    <div class="container text-center">
        <h3 class="fw-bold mb-2">Learn Today, Grow for Tomorrow</h3>

        <div class="row g-4 mt-4">
            <div class="col-md-4">
                <div class="feature-card text-center">
                    <i class="fas fa-users fa-2x text-primary mb-3"></i>
                    <p class="fw-semibold mb-0">
                        Over 1.5 Million Learners<br>
                        Learning Together
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-card text-center">
                    <i class="fas fa-certificate fa-2x text-primary mb-3"></i>
                    <p class="fw-semibold mb-0">
                        Learn through hands-on<br>
                        materials and certificates
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-card text-center">
                    <i class="fas fa-star fa-2x text-primary mb-3"></i>
                    <p class="fw-semibold mb-0">
                        Trusted by learners with<br>
                        high ratings
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Courses Section -->
<section class="courses-section py-5">
    <div class="container">
        <!-- Header -->
        <div class="mb-4">
            <h2 class="fw-bold mb-2">Skills to transform your career and life</h2>
            <p class="text-muted">From critical skills to technical topics, MersifLab supports your professional development.</p>
        </div>

        <!-- Category Tabs -->
        <ul class="nav nav-pills mb-4 course-tabs" id="courseTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="ai-tab" data-bs-toggle="pill" 
                        data-bs-target="#ai" type="button" role="tab">
                    Artificial Intelligence (AI)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="development-tab" data-bs-toggle="pill" 
                        data-bs-target="#development" type="button" role="tab">
                    Development
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="marketing-tab" data-bs-toggle="pill" 
                        data-bs-target="#marketing" type="button" role="tab">
                    Marketing
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="design-tab" data-bs-toggle="pill" 
                        data-bs-target="#design" type="button" role="tab">
                    Design
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="photo-tab" data-bs-toggle="pill" 
                        data-bs-target="#photo" type="button" role="tab">
                    Photography & Video
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="courseTabContent">
            <!-- AI Tab -->
            <div class="tab-pane fade show active" id="ai" role="tabpanel">
                <div class="row g-4">
                    @if(isset($coursesByCategory['ai']) && $coursesByCategory['ai']->count() > 0)
                        @foreach($coursesByCategory['ai'] as $course)
                        <div class="col-md-3">
                            <div class="course-card">
                                <!-- Course Image -->
                                <div class="course-image">
                                    <div class="course-placeholder">
                                        <i class="fas fa-brain fa-3x"></i>
                                    </div>
                                </div>

                                <!-- Course Content -->
                                <div class="course-content">
                                    <h6 class="course-title">{{ $course->name }}</h6>
                                    <p class="course-instructor">
                                        <i class="fas fa-user-tie me-1"></i>
                                        {{ $course->teacher->name ?? "Teacher" }}
                                    </p>
                                    
                                    <!-- Rating & Duration -->
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
                                            <i class="far fa-clock me-1"></i>
                                            {{ $course->chapters_count ?? 0 }} chapters
                                        </div>
                                    </div>

                                    <!-- Price -->
                                    <div class="course-price">
                                        Rp100,000
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada kursus tersedia</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Show All Link -->
                <div class="mt-4">
                    <a href="{{ route('courses') }}" class="show-all-link">
                        Show all Artificial Intelligence courses →
                    </a>
                </div>
            </div>

            <!-- Development Tab -->
            <div class="tab-pane fade" id="development" role="tabpanel">
                <div class="row g-4">
                    @if(isset($coursesByCategory['development']) && $coursesByCategory['development']->count() > 0)
                        @foreach($coursesByCategory['development'] as $course)
                        <div class="col-md-3">
                            <div class="course-card">
                                <div class="course-image">
                                    <div class="course-placeholder">
                                        <i class="fas fa-code fa-3x"></i>
                                    </div>
                                </div>
                                <div class="course-content">
                                    <h6 class="course-title">{{ $course->name }}</h6>
                                    <p class="course-instructor">
                                        <i class="fas fa-user-tie me-1"></i>
                                        {{ $course->teacher->name ?? "Teacher" }}
                                    </p>
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
                                            <i class="far fa-clock me-1"></i>
                                            {{ $course->chapters_count ?? 0 }} chapters
                                        </div>
                                    </div>
                                    <div class="course-price">Rp100,000</div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-code fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada kursus Development</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Show All Link -->
                <div class="mt-4">
                    <a href="{{ route('courses') }}" class="show-all-link">
                        Show all Development courses →
                    </a>
                </div>
            </div>

            <!-- Marketing Tab -->
            <div class="tab-pane fade" id="marketing" role="tabpanel">
                <div class="row g-4">
                    @if(isset($coursesByCategory['marketing']) && $coursesByCategory['marketing']->count() > 0)
                        @foreach($coursesByCategory['marketing'] as $course)
                        <div class="col-md-3">
                            <div class="course-card">
                                <div class="course-image">
                                    <div class="course-placeholder">
                                        <i class="fas fa-chart-line fa-3x"></i>
                                    </div>
                                </div>
                                <div class="course-content">
                                    <h6 class="course-title">{{ $course->name }}</h6>
                                    <p class="course-instructor">
                                        <i class="fas fa-user-tie me-1"></i>
                                        {{ $course->teacher->name ?? "Teacher" }}
                                    </p>
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
                                            <i class="far fa-clock me-1"></i>
                                            {{ $course->chapters_count ?? 0 }} chapters
                                        </div>
                                    </div>
                                    <div class="course-price">Rp100,000</div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada kursus Marketing</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Show All Link -->
                <div class="mt-4">
                    <a href="{{ route('courses') }}" class="show-all-link">
                        Show all Marketing courses →
                    </a>
                </div>
            </div>

            <!-- Design Tab -->
            <div class="tab-pane fade" id="design" role="tabpanel">
                <div class="row g-4">
                    @if(isset($coursesByCategory['design']) && $coursesByCategory['design']->count() > 0)
                        @foreach($coursesByCategory['design'] as $course)
                        <div class="col-md-3">
                            <div class="course-card">
                                <div class="course-image">
                                    <div class="course-placeholder">
                                        <i class="fas fa-palette fa-3x"></i>
                                    </div>
                                </div>
                                <div class="course-content">
                                    <h6 class="course-title">{{ $course->name }}</h6>
                                    <p class="course-instructor">
                                        <i class="fas fa-user-tie me-1"></i>
                                        {{ $course->teacher->name ?? "Teacher" }}
                                    </p>
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
                                            <i class="far fa-clock me-1"></i>
                                            {{ $course->chapters_count ?? 0 }} chapters
                                        </div>
                                    </div>
                                    <div class="course-price">Rp100,000</div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-palette fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada kursus Design</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Show All Link -->
                <div class="mt-4">
                    <a href="{{ route('courses') }}" class="show-all-link">
                        Show all Design courses →
                    </a>
                </div>
            </div>

            <!-- Photography Tab -->
            <div class="tab-pane fade" id="photo" role="tabpanel">
                <div class="row g-4">
                    @if(isset($coursesByCategory['photography']) && $coursesByCategory['photography']->count() > 0)
                        @foreach($coursesByCategory['photography'] as $course)
                        <div class="col-md-3">
                            <div class="course-card">
                                <div class="course-image">
                                    <div class="course-placeholder">
                                        <i class="fas fa-camera fa-3x"></i>
                                    </div>
                                </div>
                                <div class="course-content">
                                    <h6 class="course-title">{{ $course->name }}</h6>
                                    <p class="course-instructor">
                                        <i class="fas fa-user-tie me-1"></i>
                                        {{ $course->teacher->name ?? "Teacher" }}
                                    </p>
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
                                            <i class="far fa-clock me-1"></i>
                                            {{ $course->chapters_count ?? 0 }} chapters
                                        </div>
                                    </div>
                                    <div class="course-price">Rp100,000</div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada kursus Photography</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Show All Link -->
                            <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Photography & Video courses coming soon</p>
                        </div>
                    </div>
                </div>

                <!-- Show All Link -->
                <div class="mt-4">
                    <a href="{{ route('courses') }}" class="show-all-link">
                        Show all Photography & Video courses →
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Partnership Section -->
<section class="py-5 partnership-section">
    <div class="container">
        <p class="text-center text-muted mb-4">Trusted by over 10 schools and millions of learners</p>
        
        <div class="partners-wrapper">
            <div class="partners-marquee">
                <div class="partner-logo">
                    <img src="{{ asset('images/partners/visa.png') }}" alt="Visa">
                </div>
                <div class="partner-logo">
                    <img src="{{ asset('images/partners/figma.png') }}" alt="Figma">
                </div>
                <div class="partner-logo">
                    <img src="{{ asset('images/partners/github.png') }}" alt="GitHub">
                </div>
                <div class="partner-logo">
                    <img src="{{ asset('images/partners/spotify.png') }}" alt="Spotify">
                </div>
                <div class="partner-logo">
                    <img src="{{ asset('images/partners/youtube.png') }}" alt="YouTube">
                </div>
                <!-- Duplicate for seamless loop -->
                <div class="partner-logo">
                    <img src="{{ asset('images/partners/visa.png') }}" alt="Visa">
                </div>
                <div class="partner-logo">
                    <img src="{{ asset('images/partners/figma.png') }}" alt="Figma">
                </div>
                <div class="partner-logo">
                    <img src="{{ asset('images/partners/github.png') }}" alt="GitHub">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-5 testimonial-section">
    <div class="container">
        <h2 class="testimonial-heading text-center mb-5">
            Join others transforming their lives through learning
        </h2>

        <div class="row g-4">
            <!-- Testimonial 1 -->
            <div class="col-lg-4 col-md-6">
                <div class="testimonial-card">
                    <div class="quote-icon">
                        <i class="fas fa-quote-left"></i>
                    </div>
                    
                    <p class="testimonial-text">
                        Course ini cukup membantu menambah wawasan, meskipun beberapa bagian bisa dijelaskan lebih detail.
                    </p>
                    
                    <div class="testimonial-author">
                        <img src="{{ asset('images/avatar/user1.jpg') }}" 
                             alt="Tubagus Mukti" 
                             class="author-avatar"
                             onerror="this.src='https://ui-avatars.com/api/?name=Tubagus+Mukti&background=667eea&color=fff'">
                        <div class="author-info">
                            <h6 class="author-name">Tubagus Mukti</h6>
                            <p class="author-position">Technical Co-Founder, CTO at Drivensiional</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Testimonial 2 -->
            <div class="col-lg-4 col-md-6">
                <div class="testimonial-card">
                    <div class="quote-icon">
                        <i class="fas fa-quote-left"></i>
                    </div>
                    
                    <p class="testimonial-text">
                        Penyampaian materi runtut dan tidak membosankan, sehingga nyaman diikuti sampai selesai.
                    </p>
                    
                    <div class="testimonial-author">
                        <img src="{{ asset('images/avatar/user2.jpg') }}" 
                             alt="Rara Rawra" 
                             class="author-avatar"
                             onerror="this.src='https://ui-avatars.com/api/?name=Rara+Rawra&background=f093fb&color=fff'">
                        <div class="author-info">
                            <h6 class="author-name">Rara Rawra</h6>
                            <p class="author-position">Product Account Manager at Amazon Web Service</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Testimonial 3 -->
            <div class="col-lg-4 col-md-6">
                <div class="testimonial-card">
                    <div class="quote-icon">
                        <i class="fas fa-quote-left"></i>
                    </div>
                    
                    <p class="testimonial-text">
                        Kontennya informatif dan terstruktur, walaupun durasi beberapa materi terasa agak panjang.
                    </p>
                    
                    <div class="testimonial-author">
                        <img src="{{ asset('images/avatar/user3.jpg') }}" 
                             alt="Hamadafah Syahrani" 
                             class="author-avatar"
                             onerror="this.src='https://ui-avatars.com/api/?name=Hamadafah+Syahrani&background=4facfe&color=fff'">
                        <div class="author-info">
                            <h6 class="author-name">Hamadafah Syahrani</h6>
                            <p class="author-position">Head of Capability Development, North America at Publicis Sapient</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Trending Courses Section -->
<section class="py-5 trending-section">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="trending-title mb-0">Trending courses</h2>
            <div class="trending-nav">
                <button class="trending-nav-btn prev" id="trendingPrev">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="trending-nav-btn next" id="trendingNext">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>

        <div class="trending-carousel-wrapper">
            <div class="trending-carousel" id="trendingCarousel">
                <!-- Course 1 -->
                @if(isset($trendingCourses) && $trendingCourses->count() > 0)
                    @foreach($trendingCourses as $course)
                    <div class="trending-card">
                        <a href="{{ route('course.detail', $course->id) }}" class="text-decoration-none">
                            <div class="trending-image">
                                <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=400&h=250&fit=crop" alt="{{ $course->name }}">
                            </div>
                            
                            <div class="trending-content">
                                <h6 class="trending-course-title">{{ $course->name }}</h6>
                                <p class="trending-instructor">
                                    {{ $course->teacher->name ?? "Teacher" }}
                                </p>
                                
                                <div class="trending-meta">
                                    <div class="trending-rating">
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
                                    <div class="trending-duration">
                                        <i class="far fa-clock"></i>
                                        <span>{{ $course->modules_count ?? 0 }} modules</span>
                                    </div>
                                </div>

                                <div class="trending-price">
                                    Rp100,000
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                @else
                    <!-- Empty State -->
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada trending courses</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Carousel JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.getElementById('trendingCarousel');
    const prevBtn = document.getElementById('trendingPrev');
    const nextBtn = document.getElementById('trendingNext');
    
    if (carousel && prevBtn && nextBtn) {
        const cardWidth = carousel.querySelector('.trending-card').offsetWidth;
        const gap = 24; // 1.5rem = 24px
        const scrollAmount = cardWidth + gap;
        
        prevBtn.addEventListener('click', () => {
            carousel.scrollBy({
                left: -scrollAmount,
                behavior: 'smooth'
            });
        });
        
        nextBtn.addEventListener('click', () => {
            carousel.scrollBy({
                left: scrollAmount,
                behavior: 'smooth'
            });
        });
        
        // Update button states based on scroll position
        carousel.addEventListener('scroll', () => {
            const maxScroll = carousel.scrollWidth - carousel.clientWidth;
            
            prevBtn.disabled = carousel.scrollLeft <= 0;
            nextBtn.disabled = carousel.scrollLeft >= maxScroll - 1;
            
            prevBtn.style.opacity = prevBtn.disabled ? '0.3' : '1';
            nextBtn.style.opacity = nextBtn.disabled ? '0.3' : '1';
        });
        
        // Initial state
        prevBtn.style.opacity = '0.3';
        prevBtn.disabled = true;
    }
});
</script>

<!-- FAQ Section -->
<section class="py-5 faq-section">
    <div class="container">
        <h2 class="faq-title mb-4">Frequently Asked Question (FAQ)</h2>

        <div class="faq-accordion" id="faqAccordion">
            <!-- FAQ Item 1 -->
            <div class="faq-item">
                <button class="faq-question active" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true">
                    <span>Bagaimana cara mendaftar dan memulai belajar?</span>
                    <i class="faq-icon fas fa-chevron-down"></i>
                </button>
                <div id="faq1" class="faq-answer collapse show" data-bs-parent="#faqAccordion">
                    <div class="faq-answer-content">
                        Silakan klik tombol "Get Started", buat akun, dan pilih kursus yang ingin Anda ikuti. Anda bisa langsung mulai belajar setelah mendaftar.
                    </div>
                </div>
            </div>

            <!-- FAQ Item 2 -->
            <div class="faq-item">
                <button class="faq-question collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false">
                    <span>Apakah ada sertifikat setelah menyelesaikan kursus?</span>
                    <i class="faq-icon fas fa-chevron-down"></i>
                </button>
                <div id="faq2" class="faq-answer collapse" data-bs-parent="#faqAccordion">
                    <div class="faq-answer-content">
                        Ya, kami menyediakan sertifikat digital setelah Anda menyelesaikan semua materi dan kuis di kursus.
                    </div>
                </div>
            </div>

            <!-- FAQ Item 3 -->
            <div class="faq-item">
                <button class="faq-question collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false">
                    <span>Berapa lama akses ke materi kursus?</span>
                    <i class="faq-icon fas fa-chevron-down"></i>
                </button>
                <div id="faq3" class="faq-answer collapse" data-bs-parent="#faqAccordion">
                    <div class="faq-answer-content">
                        Akses ke materi kursus berlaku seumur hidup. Anda bisa belajar kapan saja sesuai kecepatan Anda sendiri.
                    </div>
                </div>
            </div>

            <!-- FAQ Item 4 -->
            <div class="faq-item">
                <button class="faq-question collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false">
                    <span>Bagaimana jika saya kesulitan dengan materi?</span>
                    <i class="faq-icon fas fa-chevron-down"></i>
                </button>
                <div id="faq4" class="faq-answer collapse" data-bs-parent="#faqAccordion">
                    <div class="faq-answer-content">
                        Anda bisa menghubungi tim support kami melalui chat atau email. Instruktur juga siap membantu menjawab pertanyaan Anda.
                    </div>
                </div>
            </div>

            <!-- FAQ Item 5 -->
            <div class="faq-item">
                <button class="faq-question collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5" aria-expanded="false">
                    <span>Apakah bisa belajar secara offline?</span>
                    <i class="faq-icon fas fa-chevron-down"></i>
                </button>
                <div id="faq5" class="faq-answer collapse" data-bs-parent="#faqAccordion">
                    <div class="faq-answer-content">
                        Saat ini semua materi hanya tersedia secara online. Namun, Anda bisa download materi pendukung seperti PDF untuk dibaca offline.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ JavaScript for Icon Rotation -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const faqButtons = document.querySelectorAll('.faq-question');
    
    faqButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            faqButtons.forEach(btn => {
                if (btn !== this) {
                    btn.classList.remove('active');
                }
            });
            
            // Toggle active class on clicked button
            this.classList.toggle('active');
        });
    });
});
</script>

<!-- CTA Section -->
@guest
<section class="cta-section py-4">
    <div class="container">
        <div class="cta-card text-center mx-auto">
            <h2 class="fw-bold mb-2">
                Ready to start learning?
            </h2>
            <p class="mb-3">
                Join thousands of students building new skills today.
            </p>
            <a href="{{ route('register') }}" class="btn btn-cta px-4 py-2">
                <i class="fas fa-user-plus me-2"></i>Register Now
            </a>
        </div>
    </div>
</section>
@endguest

@endsection