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
                                            @if($course->teacher && $course->teacher->name)
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
                                    <p class="popular-course-price">Rp{{ number_format($course->price, 0, ',', '.') }}</p>
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

        <!-- Featured Content Section -->
        <section class="featured-section mb-5">
            <div class="section-header">
                <h2 class="section-title">Featured Contents</h2>
                <p class="section-subtitle">Many learners enjoyed this night course for its engaging content.</p>
            </div>

            @if(isset($featuredCourses) && $featuredCourses->count() > 0)
                @php $f = $featuredCourses->first(); @endphp
                <div class="featured-card">
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <div class="featured-image">
                                @if($f->image)
                                    <img src="{{ asset('storage/' . $f->image) }}" alt="{{ $f->name }}">
                                @else
                                    <div class="featured-placeholder" style="height:300px; display:flex; align-items:center; justify-content:center; background:#f8f9fa; border-radius:8px;">
                                        <i class="fas fa-book fa-3x text-muted"></i>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="featured-content">
                                <h3 class="featured-title">{{ $f->name }}</h3>
                                <p class="featured-description">
                                    {{ Str::limit($f->description ?? '', 200) }}
                                </p>
                                <div class="featured-rating mb-2">
                                    @php $avg = $f->average_rating ?? 0; $cnt = $f->reviews_count ?? 0; @endphp
                                    <div class="stars me-2">
                                        @for($i=1;$i<=5;$i++)
                                            @if($i <= floor($avg))
                                                <i class="fas fa-star text-warning"></i>
                                            @elseif($i - 0.5 <= $avg)
                                                <i class="fas fa-star-half-alt text-warning"></i>
                                            @else
                                                <i class="far fa-star text-warning"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="rating-text fw-bold">{{ number_format($avg,1) }} ({{ $cnt }})</span>
                                </div>
                                <p class="featured-price">Rp{{ number_format($f->price ?? 0, 0, ',', '.') }}</p>
                                <a href="{{ route('course.detail', $f->id) }}" class="btn btn-primary">View Course</a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-4 text-muted">No featured courses at the moment.</div>
            @endif
        </section>

        <!-- Popular Instructors Section -->
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
                            <div class="instructor-card">
                                <div class="instructor-avatar-large">
                                    @if($instructor->name)
                                        {{ strtoupper(substr($instructor->name, 0, 1)) }}
                                    @else
                                        <i class="fas fa-user"></i>
                                    @endif
                                </div>
                                <h6 class="instructor-name-large">{{ $instructor->name ?? 'Teacher' }}</h6>
                                <div class="instructor-stats-row">
                                    <div class="stat-item">
                                        <p class="stat-label">Students</p>
                                        <p class="stat-value">{{ number_format($instructor->total_students ?? 0) }}</p>
                                    </div>
                                    <div class="stat-item">
                                        <p class="stat-label">Courses</p>
                                        <p class="stat-value">{{ $instructor->classes_count ?? 0 }}</p>
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
        <section class="all-courses-section">
            <div class="section-header">
                <h2 class="section-title">All Courses</h2>
            </div>

            <div class="row">
                <!-- Filters Sidebar -->
                <div class="col-lg-3">
                    <form method="GET" action="{{ route('courses') }}" id="filterForm">
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
                            <a href="{{ route('courses') }}" class="btn btn-outline-secondary w-100 mt-2 clear-filters-btn">
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
                                                    @if($course->teacher && $course->teacher->name)
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

                        <!-- Pagination -->
                        <nav class="pagination-nav mt-4">
                            {{ $courses->links() }}
                        </nav>
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
        const cardWidth = 190; // Width of each instructor card + gap
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
        
        // Ensure min is not greater than max
        if (minVal > maxVal) {
            priceMin.value = maxVal;
        }
        
        priceRangeText.textContent = formatPrice(priceMin.value) + ' - ' + formatPrice(priceMax.value);
    }

    // Sync slider with input
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

        // Sync input with slider
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

        // Initialize display
        updatePriceDisplay();
    }

    // Auto-submit form when filter changes (optional)
    const filterInputs = document.querySelectorAll('input[name="category"], input[name="rating"]');
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Uncomment if you want auto-submit on filter change
            // document.getElementById('filterForm').submit();
        });
    });
</script>
@endsection