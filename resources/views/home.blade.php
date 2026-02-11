@extends('layouts.app')

@section('title', 'Home')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/welcome.css') }}">
@endsection

@section('content')

<!-- Continue Learning Section (Only for Authenticated Users, but not for Teachers) -->
@auth
@if(!Auth::user()->isTeacher())
<section class="py-5 continue-learning-section">
    <div class="container">
        <!-- Welcome Message -->
        <div class="welcome-banner">
            <div class="d-flex align-items-center gap-3">
                <div class="welcome-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <h5 class="mb-0">Welcome, <strong>{{ Auth::user()->name }}</strong>!</h5>
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
                                @if($course->image)
                                    <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <div class="learning-placeholder">
                                        <i class="fas fa-book-reader fa-2x"></i>
                                    </div>
                                @endif
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
@endif

<!-- Welcome Banner for Teachers -->
@if(Auth::check() && Auth::user()->isTeacher())
<!-- My Courses Section for Teachers -->
<section class="py-5 teacher-courses-section">
    <div class="container">
        <!-- Welcome Message -->
        <div class="welcome-banner">
            <div class="d-flex align-items-center gap-3">
                <div class="welcome-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <h5 class="mb-0">Welcome, <strong>{{ Auth::user()->name }}</strong>!</h5>
            </div>
        </div>

        <!-- My Courses Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="continue-learning-title mb-0">My Courses</h2>
            <a href="{{ route('teacher.courses') }}" class="view-all-btn">
                View All →
            </a>
        </div>

        <!-- My Courses Cards -->
        <div class="row g-4">
            @if(isset($teacherCourses) && $teacherCourses->count() > 0)
                @foreach($teacherCourses as $course)
                <div class="col-lg-4 col-md-6">
                    <a href="{{ route('teacher.course.detail', $course->id) }}" class="text-decoration-none">
                        <div class="learning-card">
                            <!-- Course Image -->
                            <div class="learning-image">
                                @if($course->image)
                                    <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <div class="learning-placeholder">
                                        <i class="fas fa-book fa-2x"></i>
                                    </div>
                                @endif
                            </div>

                            <!-- Course Info -->
                            <div class="learning-content">
                                <h6 class="learning-title">{{ $course->name ?? 'Untitled Course' }}</h6>
                                <p class="learning-instructor">
                                    {{ $course->chapters_count ?? 0 }} Chapters
                                </p>

                                <!-- Course Stats -->
                                <div class="learning-progress">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="progress-label">Modules</span>
                                        <span class="progress-percentage">{{ $course->modules_count ?? 0 }}</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" 
                                             role="progressbar" 
                                             style="width: 100%; background-color: #28a745;"
                                             aria-valuenow="100" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <p class="progress-status mt-2">
                                        <i class="far fa-calendar me-1"></i>
                                        Created {{ $course->created_at ? $course->created_at->format('M d, Y') : 'N/A' }}
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
                        <i class="fas fa-plus-circle fa-4x mb-3"></i>
                        <h5>Start Creating Courses</h5>
                        <p class="text-muted mb-4">You haven't created any courses yet. Start creating your first course today!</p>
                        <a href="{{ route('teacher.classes.create') }}" class="btn btn-primary">
                            Create Course
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
@endif
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

<!-- Courses Section - MODIFIED VERSION -->
<section class="courses-section py-5">
    <div class="container">
        <!-- Header -->
        <div class="mb-4">
            <h2 class="fw-bold mb-2">Skills to transform your career and life</h2>
            <p class="text-muted">From critical skills to technical topics, MersifLab supports your professional development.</p>
        </div>

        <!-- ===== PREMIUM CATEGORY DROPDOWN (Universal Design for All Screen Sizes) ===== -->
        <div class="course-category-wrapper">
            <label class="course-category-label">
                <i class="fas fa-filter"></i>
                Filter by Category
            </label>
            <div class="course-category-select-wrapper">
                <select class="course-category-select" id="courseCategorySelect">
                    @foreach($categories as $index => $category)
                    <option value="{{ $category->slug }}" {{ $index === 0 ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="tab-content" id="courseTabContent">
            @foreach($categories as $index => $category)
            <!-- {{ $category->name }} Tab -->
            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="{{ $category->slug }}" role="tabpanel">
                <div class="row g-4">
                    @if(isset($coursesByCategory[$category->slug]) && $coursesByCategory[$category->slug]->count() > 0)
                        @foreach($coursesByCategory[$category->slug] as $course)
                        <div class="col-md-3">
                            <a href="{{ route('course.detail', $course->id) }}" class="text-decoration-none" style="display: block; height: 100%;">
                            <div class="course-card" style="height: 100%;">
                                <!-- Course Image -->
                                <div class="course-image" style="position: relative;">
                                    @if($course->image)
                                        <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->name }}">
                                    @else
                                        <div class="course-placeholder">
                                            @php
                                                $iconMap = [
                                                    'ai' => 'fa-brain',
                                                    'development' => 'fa-code',
                                                    'marketing' => 'fa-chart-line',
                                                    'design' => 'fa-palette',
                                                    'photography' => 'fa-camera',
                                                ];
                                                $icon = $iconMap[$category->slug] ?? 'fa-book-open';
                                            @endphp
                                            <i class="fas {{ $icon }} fa-3x"></i>
                                        </div>
                                    @endif
                                    <!-- Badges (top-left) -->
                                    <div style="position: absolute; top: 12px; left: 12px; display: flex; gap: 8px; align-items: center; z-index: 3;">
                                        <span class="badge" style="background: #e3f2fd; color: #1976d2; font-size: 12px; padding: 6px 10px; border-radius: 12px; font-weight:600;">{{ $category->name }}</span>
                                        @php $tier = $course->price_tier ?? null; @endphp
                                        @if($tier)
                                            <span class="badge" style="background: {{ $tier === 'standard' ? '#e8f5e9' : '#f3e8ff' }}; color: {{ $tier === 'standard' ? '#2e7d32' : '#6a1b9a' }}; padding:6px 10px; border-radius:12px; font-size:12px; font-weight:600;">{{ ucfirst($tier) }}</span>
                                        @endif
                                    </div>
                                    <!-- Popularity Indicator -->
                                    @if(($course->enrollments_count ?? 0) > 50)
                                    <div style="position: absolute; top: 12px; right: 12px; background: linear-gradient(135deg, #fbbf24, #f59e0b); color: #fff; padding: 6px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; z-index: 3; box-shadow: 0 4px 12px rgba(251, 191, 36, 0.3); display: flex; align-items: center; gap: 4px;">
                                        <i class="fas fa-fire"></i> Popular
                                    </div>
                                    @endif
                                </div>

                                <!-- Course Content -->
                                <div class="course-content">
                                    <h6 class="course-title">{{ $course->name }}</h6>
                                    <p class="course-instructor">
                                        <span class="course-instructor-avatar">
                                            @if($course->teacher && !empty($course->teacher->avatar))
                                                <img src="{{ \Illuminate\Support\Facades\Storage::url($course->teacher->avatar) }}" alt="{{ $course->teacher->name ?? 'Teacher' }}">
                                            @elseif($course->teacher && $course->teacher->name)
                                                {{ strtoupper(substr($course->teacher->name, 0, 1)) }}
                                            @else
                                                <i class="fas fa-user"></i>
                                            @endif
                                        </span>
                                        <span class="course-instructor-name">{{ $course->teacher->name ?? "Teacher" }}</span>
                                    </p>
                                    
                                    <!-- Rating & Duration -->
                                    <div class="course-meta">
                                        @php
                                            $avgRating = $course->average_rating ?? 0;
                                            $reviewsCount = $course->reviews_count ?? 0;
                                            // Get actual duration from database or estimate
                                            $totalMinutes = $course->total_duration ?? (($course->chapters_count ?? 1) * 120) + (($course->modules_count ?? 0) * 30);
                                            $hours = intdiv($totalMinutes, 60);
                                            $minutes = $totalMinutes % 60;
                                            $durationText = $hours > 0 ? ($hours . 'h ' . ($minutes > 0 ? $minutes . 'm' : '')) : $minutes . 'm';
                                        @endphp
                                        <div class="rating">
                                            <span class="rating-score">
                                                @if($reviewsCount > 0)
                                                    <i class="fas fa-star" style="color: #fbbf24;"></i> {{ number_format($avgRating, 1) }}
                                                @else
                                                    <span class="text-muted">No ratings</span>
                                                @endif
                                            </span>
                                            @if($reviewsCount > 0)
                                            <span class="rating-count">({{ $reviewsCount }})</span>
                                            @endif
                                        </div>
                                        <div class="duration">
                                            <i class="far fa-clock me-1"></i>
                                            {{ $durationText }}
                                        </div>
                                    </div>

                                    <!-- Price -->
                                    <div class="course-price">
                                        @php 
                                            $hp = $course->discounted_price ?? $course->price ?? 100000;
                                            $now = \Carbon\Carbon::now();
                                            $isDiscountActive = $course->has_discount && $course->discount && 
                                                              (!$course->discount_starts_at || $now->greaterThanOrEqualTo($course->discount_starts_at)) && 
                                                              (!$course->discount_ends_at || $now->lessThanOrEqualTo($course->discount_ends_at));
                                        @endphp
                                        @if($isDiscountActive)
                                            <span class="course-price-original">Rp{{ number_format($course->price ?? 100000, 0, ',', '.') }}</span>
                                            <span class="course-price-current">Rp{{ number_format($hp, 0, ',', '.') }}</span>
                                        @else
                                            <span class="course-price-current">Rp{{ number_format($course->price ?? 100000, 0, ',', '.') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            </a>
                        </div>
                        @endforeach
                    @else
                        <div class="col-12">
                            <div class="text-center py-5">
                                @php
                                    // Default icon based on category slug
                                    $iconMap = [
                                        'ai' => 'fa-brain',
                                        'development' => 'fa-code',
                                        'marketing' => 'fa-chart-line',
                                        'design' => 'fa-palette',
                                        'photography' => 'fa-camera',
                                    ];
                                    $icon = $iconMap[$category->slug] ?? 'fa-book-open';
                                @endphp
                                <i class="fas {{ $icon }} fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No courses yet for {{ $category->name }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Show All Link -->
                <div class="mt-4">
                    <a href="{{ route('courses') }}#all-courses" class="show-all-link">
                        Show All Courses →
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('courseCategorySelect');
    
    if (categorySelect) {
        categorySelect.addEventListener('change', function(e) {
            const selectedCategory = this.value;
            
            // Hide all tab panes
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('show', 'active');
            });
            
            // Show selected tab pane
            const selectedPane = document.getElementById(selectedCategory);
            if (selectedPane) {
                selectedPane.classList.add('show', 'active');
            }
        });
    }
});
</script>

<!-- Partnership Section -->
<section class="py-5 partnership-section">
    <div class="container">
        <p class="text-center text-muted mb-4">Trusted by over 10 schools and millions of learners</p>
        
        <div class="partners-wrapper">
            <div class="partners-marquee">
                <div class="partner-logo">
                    <img src="{{ asset('images/partners/smkn2solo.png') }}" alt="SMK Negeri 2 Surakarta">
                </div>
                <div class="partner-logo">
                    <img src="{{ asset('images/partners/smkn5solo.png') }}" alt="SMK Negeri 5 Surakarta">
                </div>
                <div class="partner-logo">
                    <img src="{{ asset('images/partners/smkn1kra.png') }}" alt="SMK Negeri 1 Karanganyar">
                </div>
                <div class="partner-logo">
                    <img src="{{ asset('images/partners/smkn2klt.png') }}" alt="SMK Negeri 2 Klaten">
                </div>
                <div class="partner-logo">
                    <img src="{{ asset('images/partners/smkn4skh.png') }}" alt="SMK Negeri 4 Sukoharjo">
                </div>
                <!-- Duplicate for seamless loop -->
                <div class="partner-logo">
                    <img src="{{ asset('images/partners/smkn2solo.png') }}" alt="SMK Negeri 2 Surakarta">
                </div>
                <div class="partner-logo">
                    <img src="{{ asset('images/partners/smkn5solo.png') }}" alt="SMK Negeri 5 Surakarta">
                </div>
                <div class="partner-logo">
                    <img src="{{ asset('images/partners/smkn1kra.png') }}" alt="SMK Negeri 1 Karanganyar">
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
            @if(isset($testimonials) && $testimonials->isNotEmpty())
                @foreach($testimonials as $t)
                    <div class="col-lg-4 col-md-6">
                        <div class="testimonial-card">
                            <div class="quote-icon">
                                <i class="fas fa-quote-left"></i>
                            </div>

                            <p class="testimonial-text">{{ $t->content }}</p>

                            <div class="testimonial-author">
                                <img src="{{ $t->avatar ? asset('storage/' . $t->avatar) : $t->avatarUrl() }}"
                                     alt="{{ $t->name }}" 
                                     class="author-avatar"
                                     onerror="this.src='{{ $t->avatarUrl() }}'">
                                <div class="author-info">
                                    <h6 class="author-name">{{ $t->name }}</h6>
                                    @if($t->position)
                                        <p class="author-position">{{ $t->position }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <!-- fallback static testimonials -->
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
            @endif
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
                                @if($course->image)
                                    <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=400&h=250&fit=crop" alt="{{ $course->name }}">
                                @endif
                            </div>
                            
                                <div class="trending-content">
                                <h6 class="trending-course-title">{{ $course->name }}</h6>
                                @php $tier = $course->price_tier ?? null; @endphp
                                @if($tier)
                                    <div style="margin-top:6px;">
                                        <span class="badge" style="background: {{ $tier === 'standard' ? '#e8f5e9' : '#f3e8ff' }}; color: {{ $tier === 'standard' ? '#2e7d32' : '#6a1b9a' }}; padding:4px 8px; border-radius:10px; font-size:12px; font-weight:600;">{{ ucfirst($tier) }}</span>
                                    </div>
                                @endif
                                <p class="trending-instructor">
                                    <span class="trending-instructor-avatar">
                                        @if($course->teacher && !empty($course->teacher->avatar))
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($course->teacher->avatar) }}" alt="{{ $course->teacher->name ?? 'Teacher' }}">
                                        @elseif($course->teacher && $course->teacher->name)
                                            {{ strtoupper(substr($course->teacher->name, 0, 1)) }}
                                        @else
                                            <i class="fas fa-user"></i>
                                        @endif
                                    </span>
                                    <span class="trending-instructor-name">{{ $course->teacher->name ?? "Teacher" }}</span>
                                </p>
                                
                                <div class="trending-meta">
                                    @php
                                        $avgRating = $course->average_rating ?? 0;
                                        $reviewsCount = $course->reviews_count ?? 0;
                                    @endphp
                                    @if($reviewsCount > 0)
                                    <div class="trending-rating">
                                        <span class="rating-score">{{ number_format($avgRating, 1) }}</span>
                                        <div class="stars">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= floor($avgRating))
                                                    <i class="fas fa-star"></i>
                                                @elseif($i - 0.5 <= $avgRating)
                                                    <i class="fas fa-star-half-alt"></i>
                                                @else
                                                    <i class="far fa-star"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="rating-count">({{ $reviewsCount }})</span>
                                    </div>
                                    @else
                                    <div class="trending-rating">
                                        <span class="rating-score text-muted">-</span>
                                        <div class="stars">
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                        </div>
                                        <span class="rating-count text-muted">(0)</span>
                                    </div>
                                    @endif
                                    <div class="trending-duration">
                                        <i class="far fa-clock"></i>
                                        <span>{{ $course->modules_count ?? 0 }} modules</span>
                                    </div>
                                </div>

                                <div class="trending-price">
                                    @php 
                                        $trendingPrice = $course->discounted_price ?? $course->price ?? 0;
                                        $now = \Carbon\Carbon::now();
                                        $isDiscountActive = $course->has_discount && $course->discount && 
                                                          (!$course->discount_starts_at || $now->greaterThanOrEqualTo($course->discount_starts_at)) && 
                                                          (!$course->discount_ends_at || $now->lessThanOrEqualTo($course->discount_ends_at));
                                    @endphp
                                    @if($isDiscountActive)
                                        <span class="text-muted text-decoration-line-through" style="font-size: 0.9rem; font-weight: 500;">Rp{{ number_format($course->price ?? 0, 0, ',', '.') }}</span>
                                        Rp{{ number_format($trendingPrice, 0, ',', '.') }}
                                    @else
                                        Rp{{ number_format($course->price ?? 0, 0, ',', '.') }}
                                    @endif
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
                            <p class="text-muted">No trending courses yet</p>
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
                    <span>How to register and start learning?</span>
                    <i class="faq-icon fas fa-chevron-down"></i>
                </button>
                <div id="faq1" class="faq-answer collapse show" data-bs-parent="#faqAccordion">
                    <div class="faq-answer-content">
                        Please click the "Get Started" button, create an account, and choose the course you want to take. You can start learning immediately after registration.
                    </div>
                </div>
            </div>

            <!-- FAQ Item 2 -->
            <div class="faq-item">
                <button class="faq-question collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false">
                    <span>Is there a certificate after completing the course??</span>
                    <i class="faq-icon fas fa-chevron-down"></i>
                </button>
                <div id="faq2" class="faq-answer collapse" data-bs-parent="#faqAccordion">
                    <div class="faq-answer-content">
                        Yes, we provide a digital certificate after you complete all materials and quizzes in the course.
                    </div>
                </div>
            </div>

            <!-- FAQ Item 3 -->
            <div class="faq-item">
                <button class="faq-question collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false">
                    <span>How long is access to course materials?</span>
                    <i class="faq-icon fas fa-chevron-down"></i>
                </button>
                <div id="faq3" class="faq-answer collapse" data-bs-parent="#faqAccordion">
                    <div class="faq-answer-content">
                        Access to course materials is lifetime. You can learn anytime at your own pace.
                    </div>
                </div>
            </div>

            <!-- FAQ Item 4 -->
            <div class="faq-item">
                <button class="faq-question collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false">
                    <span>What if I have difficulty with the material?</span>
                    <i class="faq-icon fas fa-chevron-down"></i>
                </button>
                <div id="faq4" class="faq-answer collapse" data-bs-parent="#faqAccordion">
                    <div class="faq-answer-content">
                        You can contact our support team via chat or email. Instructors are also ready to help answer your questions.
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