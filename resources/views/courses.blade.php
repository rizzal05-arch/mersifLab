@extends('layouts.app')

@section('title', 'Explore Courses')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/courses.css') }}">
@endsection

@section('content')
<div class="courses-page">
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Explore Courses</h1>
            <p class="page-subtitle">Discover and learn from our wide range of courses</p>
        </div>

        <!-- Most Popular Courses Section -->
        <section class="popular-section mb-5">
            <div class="section-header">
                <h2 class="section-title">Most Popular Courses</h2>
            </div>

            <div class="row g-3">
                @if(isset($popularCourses) && $popularCourses->count() > 0)
                    @foreach($popularCourses->take(3) as $course)
                    <div class="col-lg-4 col-md-6">
                        <a href="{{ route('course.detail', $course->id) }}" class="text-decoration-none">
                            <div class="popular-course-card">
                                <div class="popular-course-image">
                                    @if($course->image)
                                        <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->name }}">
                                    @else
                                        <div class="popular-course-placeholder">
                                            <i class="fas fa-book fa-3x text-white"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="popular-course-body">
                                    <h6 class="popular-course-title">{{ $course->name }}</h6>
                                    <div class="popular-instructor-info">
                                        <div class="popular-instructor-avatar">
                                            @if($course->teacher && !empty($course->teacher->avatar))
                                                <img src="{{ \Illuminate\Support\Facades\Storage::url($course->teacher->avatar) }}" alt="{{ $course->teacher->name ?? 'Teacher' }}">
                                            @elseif($course->teacher && $course->teacher->name)
                                                {{ strtoupper(substr($course->teacher->name, 0, 1)) }}
                                            @else
                                                <i class="fas fa-user"></i>
                                            @endif
                                        </div>
                                        <span class="popular-instructor-name">{{ $course->teacher->name ?? 'Teacher' }}</span>
                                    </div>
                                    <div class="popular-course-stats">
                                        @php
                                            $avgRating = $course->average_rating ?? 0;
                                            $reviewsCount = $course->reviews_count ?? 0;
                                        @endphp
                                        @if($reviewsCount > 0)
                                        <div class="popular-rating">
                                            <i class="fas fa-star"></i>
                                            <span>{{ number_format($avgRating, 1) }}</span>
                                            <span class="popular-count">({{ $reviewsCount }})</span>
                                        </div>
                                        @else
                                        <div class="popular-rating text-muted">
                                            <i class="far fa-star"></i>
                                            <span>-</span>
                                            <span class="popular-count">(0)</span>
                                        </div>
                                        @endif
                                        <div class="popular-chapters">
                                            <i class="far fa-folder"></i>
                                            <span>{{ $course->chapters_count ?? 0 }} chapters</span>
                                        </div>
                                        @if(isset($course->formatted_total_duration))
                                        <div class="popular-duration">
                                            <i class="fas fa-clock"></i>
                                            <span>{{ $course->formatted_total_duration }}</span>
                                        </div>
                                        @endif
                                    </div>
                                    @php
                                        $pcPrice = $course->discounted_price ?? $course->price ?? 0;
                                    @endphp
                                    <p class="popular-course-price">
                                        @if($course->has_discount && $course->discount)
                                            <span class="text-muted text-decoration-line-through">Rp{{ number_format($course->price ?? 0, 0, ',', '.') }}</span>
                                            <span class="ms-2 text-primary fw-bold">Rp{{ number_format($pcPrice, 0, ',', '.') }}</span>
                                        @else
                                            Rp{{ number_format($pcPrice, 0, ',', '.') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="text-center py-4">
                            <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada popular courses</p>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <!-- Featured Content Section - IMPROVED -->
        <section class="featured-section mb-5">
            <div class="section-header">
                <h2 class="section-title">Featured Contents</h2>
                <p class="section-subtitle">Many learners enjoyed this night course for its engaging content.</p>
            </div>

            @if(isset($featuredCourses) && $featuredCourses->count() > 0)
                @php $f = $featuredCourses->first(); @endphp
                <div class="featured-card-enhanced">
                    <div class="row align-items-center g-4">
                        <div class="col-md-6">
                            <div class="featured-image-enhanced">
                                @if($f->image)
                                    <img src="{{ asset('storage/' . $f->image) }}" alt="{{ $f->name }}">
                                @else
                                    <div class="featured-placeholder-enhanced">
                                        <i class="fas fa-book fa-4x"></i>
                                    </div>
                                @endif
                                <div class="featured-badge">
                                    <i class="fas fa-crown me-2"></i>Featured
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="featured-content-enhanced">
                                @if(isset($f->category_name))
                                <span class="featured-category-badge">
                                    <i class="fas fa-tag me-1"></i>{{ $f->category_name }}
                                </span>
                                @endif
                                <h3 class="featured-title-enhanced">{{ $f->name }}</h3>
                                <p class="featured-description-enhanced">
                                    {{ Str::limit($f->description ?? 'Discover amazing content in this featured course.', 200) }}
                                </p>
                                
                                <!-- Instructor Info -->
                                <div class="featured-instructor-info">
                                    <div class="featured-instructor-avatar">
                                        @if($f->teacher && !empty($f->teacher->avatar))
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($f->teacher->avatar) }}" alt="{{ $f->teacher->name ?? 'Teacher' }}">
                                        @elseif($f->teacher && $f->teacher->name)
                                            {{ strtoupper(substr($f->teacher->name, 0, 1)) }}
                                        @else
                                            <i class="fas fa-user"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="featured-instructor-label">Instructor</p>
                                        <p class="featured-instructor-name">{{ $f->teacher->name ?? 'Teacher' }}</p>
                                    </div>
                                </div>

                                <!-- Stats Row -->
                                <div class="featured-stats-row">
                                    @php $avg = $f->average_rating ?? 0; $cnt = $f->reviews_count ?? 0; @endphp
                                    <div class="featured-stat-item">
                                        <div class="stars-row">
                                            @for($i=1;$i<=5;$i++)
                                                @if($i <= floor($avg))
                                                    <i class="fas fa-star"></i>
                                                @elseif($i - 0.5 <= $avg)
                                                    <i class="fas fa-star-half-alt"></i>
                                                @else
                                                    <i class="far fa-star"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="stat-text">{{ number_format($avg,1) }} ({{ $cnt }} reviews)</span>
                                    </div>
                                    <div class="featured-stat-item">
                                        <i class="far fa-folder"></i>
                                        <span>{{ $f->chapters_count ?? 0 }} Chapters</span>
                                    </div>
                                    @if(isset($f->formatted_total_duration))
                                    <div class="featured-stat-item">
                                        <i class="fas fa-clock"></i>
                                        <span>{{ $f->formatted_total_duration }}</span>
                                    </div>
                                    @endif
                                </div>

                                <div class="featured-footer">
                                    <div class="featured-price-enhanced">Rp{{ number_format($f->price ?? 0, 0, ',', '.') }}</div>
                                    <a href="{{ route('course.detail', $f->id) }}" class="btn-featured">
                                        View Course <i class="fas fa-arrow-right ms-2"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-4 text-muted">No featured courses at the moment.</div>
            @endif
        </section>

        <!-- Popular Instructors Section - IMPROVED -->
        <section class="instructors-section mb-5">
            <div class="section-header">
                <h2 class="section-title">Popular Instructors</h2>
            </div>

            <div class="instructors-carousel">
                <button class="carousel-btn prev" id="instructorsPrev">
                    <i class="fas fa-chevron-left"></i>
                </button>
                
                <div class="instructors-wrapper" id="instructorsWrapper">
                    <div class="instructors-track">
                        @if(isset($popularInstructors) && $popularInstructors->count() > 0)
                            @foreach($popularInstructors as $instructor)
                            <div class="instructor-card-enhanced">
                                <div class="instructor-avatar-enhanced">
                                    @if($instructor->avatar)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($instructor->avatar) }}" alt="{{ $instructor->name }}" class="instructor-avatar-img">
                                    @elseif($instructor->name)
                                        <span class="instructor-avatar-letter">{{ strtoupper(substr($instructor->name, 0, 1)) }}</span>
                                    @else
                                        <i class="fas fa-user"></i>
                                    @endif
                                </div>
                                <h6 class="instructor-name-enhanced">{{ $instructor->name ?? 'Teacher' }}</h6>
                                <p class="instructor-title">Professional Instructor</p>
                                <div class="instructor-stats-enhanced">
                                    <div class="stat-item-enhanced">
                                        <i class="fas fa-users"></i>
                                        <div>
                                            <p class="stat-value-enhanced">{{ number_format($instructor->total_students ?? 0) }}</p>
                                            <p class="stat-label-enhanced">Students</p>
                                        </div>
                                    </div>
                                    <div class="stat-divider"></div>
                                    <div class="stat-item-enhanced">
                                        <i class="fas fa-book"></i>
                                        <div>
                                            <p class="stat-value-enhanced">{{ $instructor->classes_count ?? 0 }}</p>
                                            <p class="stat-label-enhanced">Courses</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="col-12">
                                <div class="text-center py-4">
                                    <i class="fas fa-chalkboard-teacher fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Belum ada popular instructors</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <button class="carousel-btn next" id="instructorsNext">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </section>

        <!-- All Courses Section with Filters -->
        <section id="all-courses" class="all-courses-section">
            <div class="section-header">
                <h2 class="section-title">All Courses</h2>
            </div>

            <div class="row">
                <!-- Filters Sidebar - STICKY -->
                <div class="col-lg-3 filters-sticky">
                    <form method="GET" action="{{ route('courses') }}#all-courses" id="filterForm">
                        <div class="filters-card">
                            <h5 class="filters-title">Filters</h5>

                            <!-- Category Filter -->
                            <div class="filter-group">
                                <h6 class="filter-label">Category</h6>
                                <div class="filter-options">
                                    <label class="filter-option">
                                        <input type="radio" name="category" value="all" {{ !request('category') || request('category') === 'all' ? 'checked' : '' }}>
                                        <span>All</span>
                                    </label>
                                    @if(isset($categories) && $categories->count() > 0)
                                        @foreach($categories as $category)
                                        <label class="filter-option">
                                            <input type="radio" name="category" value="{{ $category->slug }}" {{ request('category') === $category->slug ? 'checked' : '' }}>
                                            <span>{{ $category->name }}</span>
                                        </label>
                                        @endforeach
                                    @else
                                        @foreach(\App\Models\ClassModel::CATEGORIES as $key => $label)
                                        <label class="filter-option">
                                            <input type="radio" name="category" value="{{ $key }}" {{ request('category') === $key ? 'checked' : '' }}>
                                            <span>{{ $label }}</span>
                                        </label>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <!-- Rating Filter -->
                            <div class="filter-group">
                                <h6 class="filter-label">Rating</h6>
                                <div class="filter-options">
                                    <label class="filter-option">
                                        <input type="radio" name="rating" value="all" {{ !request('rating') || request('rating') === 'all' ? 'checked' : '' }}>
                                        <span>All</span>
                                    </label>
                                    <label class="filter-option">
                                        <input type="radio" name="rating" value="5" {{ request('rating') === '5' ? 'checked' : '' }}>
                                        <div class="rating-stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </div>
                                        <span>5.0+</span>
                                    </label>
                                    <label class="filter-option">
                                        <input type="radio" name="rating" value="4" {{ request('rating') === '4' ? 'checked' : '' }}>
                                        <div class="rating-stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                        </div>
                                        <span>4.0+</span>
                                    </label>
                                    <label class="filter-option">
                                        <input type="radio" name="rating" value="3" {{ request('rating') === '3' ? 'checked' : '' }}>
                                        <div class="rating-stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                        </div>
                                        <span>3.0+</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Price Range Filter -->
                            <div class="filter-group">
                                <h6 class="filter-label">Price Range</h6>

                                <div class="price-range-inputs">
                                    <div class="price-input">
                                        <label>Min Price</label>
                                        <input type="number" name="price_min" id="priceMin" value="{{ request('price_min', 0) }}" min="0" max="5000000">
                                    </div>
                                    <div class="price-input">
                                        <label>Max Price</label>
                                        <input type="number" name="price_max" id="priceMax" value="{{ request('price_max', 5000000) }}" min="0" max="5000000">
                                    </div>
                                </div>

                                <div class="price-slider-container">
                                    <input type="range" class="price-slider" id="priceSliderMin" min="0" max="5000000" step="50000" value="{{ request('price_min', 0) }}">
                                    <input type="range" class="price-slider" id="priceSliderMax" min="0" max="5000000" step="50000" value="{{ request('price_max', 5000000) }}">
                                </div>
                                
                                <div class="price-range-display">
                                    <span id="priceRangeText">Rp{{ number_format(request('price_min', 0), 0, ',', '.') }} - Rp{{ number_format(request('price_max', 5000000), 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <!-- Apply Filters Button -->
                            <button type="submit" class="btn apply-filters-btn">
                                <i class="fas fa-filter me-2"></i>Apply Filters
                            </button>
                            
                            <!-- Clear Filters Button -->
                            @if(request()->hasAny(['category', 'rating', 'price_min', 'price_max']))
                            <a href="{{ route('courses') }}#all-courses" class="btn btn-outline-secondary w-100 mt-2 clear-filters-btn">
                                <i class="fas fa-times me-2"></i>Clear Filters
                            </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Courses Grid -->
                <div class="col-lg-9">
                    @if(isset($courses) && $courses->count() > 0)
                        <div class="row g-3">
                            @foreach($courses as $course)
                            <div class="col-lg-6 col-md-6">
                                <a href="{{ route('course.detail', $course->id) }}" class="text-decoration-none">
                                    <div class="course-card">
                                        <div class="course-image">
                                            @if($course->image)
                                                <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->name }}">
                                            @else
                                                <div class="course-placeholder">
                                                    <i class="fas fa-book fa-4x text-white"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="course-body">
                                            <!-- Category Badge -->
                                            @if(isset($course->category))
                                            <div class="course-badge-wrapper">
                                                <span class="badge course-category-badge">
                                                    {{ $course->category_name }}
                                                </span>
                                            </div>
                                            @endif

                                            <!-- Title -->
                                            <h6 class="course-title">{{ $course->name }}</h6>

                                            <!-- Description (pindah ke bawah judul) -->
                                            @if($course->description)
                                            <p class="course-description">
                                                {{ Str::limit($course->description, 70) }}
                                            </p>
                                            @endif

                                            <!-- Instructor -->
                                            <div class="instructor-info-inline">
                                                <div class="instructor-avatar-sm">
                                                    @if($course->teacher && !empty($course->teacher->avatar))
                                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($course->teacher->avatar) }}" alt="{{ $course->teacher->name ?? 'Teacher' }}">
                                                    @elseif($course->teacher && $course->teacher->name)
                                                        {{ strtoupper(substr($course->teacher->name, 0, 1)) }}
                                                    @else
                                                        <i class="fas fa-user"></i>
                                                    @endif
                                                </div>
                                                <span class="instructor-name">{{ $course->teacher->name ?? 'Teacher' }}</span>
                                            </div>

                                            <!-- Footer Info -->
                                            <div class="course-footer">
                                                @php
                                                    $avgRating = $course->average_rating ?? 0;
                                                    $reviewsCount = $course->reviews_count ?? 0;
                                                @endphp
                                                @if($reviewsCount > 0)
                                                <div class="rating">
                                                    <i class="fas fa-star"></i>
                                                    <span>{{ number_format($avgRating, 1) }}</span>
                                                    <span class="count">({{ $reviewsCount }})</span>
                                                </div>
                                                @else
                                                <div class="rating text-muted">
                                                    <i class="far fa-star"></i>
                                                    <span>-</span>
                                                    <span class="count">(0)</span>
                                                </div>
                                                @endif

                                                <div class="capters">
                                                    <i class="far fa-folder"></i>
                                                    <span>{{ $course->chapters_count ?? 0 }} chapters</span>
                                                </div>

                                                @if(isset($course->formatted_total_duration))
                                                <div class="duration">
                                                    <i class="fas fa-clock"></i>
                                                    <span>{{ $course->formatted_total_duration }}</span>
                                                </div>
                                                @endif
                                            </div>

                                            <!-- Price -->
                                            <p class="course-price">
                                                Rp{{ number_format($course->price, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            @endforeach
                        </div>

                        <!-- Pagination - INLINE CUSTOM -->
                        @if($courses->hasPages())
                        <nav class="pagination-nav" role="navigation" aria-label="Pagination Navigation">
                            <ul class="pagination">
                                {{-- Previous Page Link --}}
                                @if ($courses->onFirstPage())
                                    <li class="page-item disabled" aria-disabled="true">
                                        <span class="page-link">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="15 18 9 12 15 6"></polyline>
                                            </svg>
                                            <span class="d-none d-sm-inline ms-1">Previous</span>
                                        </span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $courses->appends(request()->query())->previousPageUrl() . '#all-courses' }}" rel="prev">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="15 18 9 12 15 6"></polyline>
                                            </svg>
                                            <span class="d-none d-sm-inline ms-1">Previous</span>
                                        </a>
                                    </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach ($courses->getUrlRange(1, $courses->lastPage()) as $page => $url)
                                        @if ($page == $courses->currentPage())
                                        <li class="page-item active" aria-current="page">
                                            <span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $courses->appends(request()->query())->url($page) . '#all-courses' }}">{{ $page }}</a>
                                        </li>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
                                @if ($courses->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $courses->appends(request()->query())->nextPageUrl() . '#all-courses' }}" rel="next">
                                            <span class="d-none d-sm-inline me-1">Next</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="9 18 15 12 9 6"></polyline>
                                            </svg>
                                        </a>
                                    </li>
                                @else
                                    <li class="page-item disabled" aria-disabled="true">
                                        <span class="page-link">
                                            <span class="d-none d-sm-inline me-1">Next</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="9 18 15 12 9 6"></polyline>
                                            </svg>
                                        </span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                        @endif
                    @else
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
            </div>
        </section>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Instructors Carousel
    const instructorsWrapper = document.getElementById('instructorsWrapper');
    const instructorsTrack = instructorsWrapper?.querySelector('.instructors-track');
    const prevBtn = document.getElementById('instructorsPrev');
    const nextBtn = document.getElementById('instructorsNext');

    if (instructorsTrack && prevBtn && nextBtn) {
        const cardWidth = 220;
        let currentPosition = 0;
        const maxScroll = instructorsTrack.scrollWidth - instructorsWrapper.offsetWidth;

        prevBtn.addEventListener('click', () => {
            currentPosition = Math.max(0, currentPosition - cardWidth);
            instructorsTrack.style.transform = `translateX(-${currentPosition}px)`;
            updateButtons();
        });

        nextBtn.addEventListener('click', () => {
            currentPosition = Math.min(maxScroll, currentPosition + cardWidth);
            instructorsTrack.style.transform = `translateX(-${currentPosition}px)`;
            updateButtons();
        });

        function updateButtons() {
            prevBtn.disabled = currentPosition === 0;
            nextBtn.disabled = currentPosition >= maxScroll - 1;
        }

        updateButtons();
    }

    // Price Range Slider
    const priceSliderMin = document.getElementById('priceSliderMin');
    const priceSliderMax = document.getElementById('priceSliderMax');
    const priceMin = document.getElementById('priceMin');
    const priceMax = document.getElementById('priceMax');
    const priceRangeText = document.getElementById('priceRangeText');

    function formatPrice(price) {
        return 'Rp' + parseInt(price).toLocaleString('id-ID');
    }

    function updatePriceDisplay() {
        const minVal = parseInt(priceMin.value);
        const maxVal = parseInt(priceMax.value);
        
        if (minVal > maxVal) {
            priceMin.value = maxVal;
        }
        
        priceRangeText.textContent = formatPrice(priceMin.value) + ' - ' + formatPrice(priceMax.value);
    }

    if (priceSliderMin && priceSliderMax && priceMin && priceMax) {
        priceSliderMin.addEventListener('input', function() {
            const minVal = parseInt(this.value);
            const maxVal = parseInt(priceSliderMax.value);
            
            if (minVal > maxVal) {
                this.value = maxVal;
            }
            
            priceMin.value = this.value;
            updatePriceDisplay();
        });

        priceSliderMax.addEventListener('input', function() {
            const minVal = parseInt(priceSliderMin.value);
            const maxVal = parseInt(this.value);
            
            if (maxVal < minVal) {
                this.value = minVal;
            }
            
            priceMax.value = this.value;
            updatePriceDisplay();
        });

        priceMin.addEventListener('input', function() {
            const minVal = parseInt(this.value);
            const maxVal = parseInt(priceMax.value);
            
            if (minVal > maxVal) {
                this.value = maxVal;
            }
            
            priceSliderMin.value = this.value;
            updatePriceDisplay();
        });

        priceMax.addEventListener('input', function() {
            const minVal = parseInt(priceMin.value);
            const maxVal = parseInt(this.value);
            
            if (maxVal < minVal) {
                this.value = minVal;
            }
            
            priceSliderMax.value = this.value;
            updatePriceDisplay();
        });

        updatePriceDisplay();
    }

    const filterInputs = document.querySelectorAll('input[name="category"], input[name="rating"]');
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Uncomment jika mau auto-submit
            // document.getElementById('filterForm').submit();
        });
    });
</script>
@endsection