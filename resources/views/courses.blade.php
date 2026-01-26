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
                            <div class="course-card-small">
                                <div class="course-image-small">
                                    @if($course->image)
                                        <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->name }}">
                                    @else
                                        <div class="course-placeholder">
                                            <i class="fas fa-book fa-3x text-white"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="course-body">
                                    <h6 class="course-title-small">{{ $course->name }}</h6>
                                    <div class="instructor-info">
                                        <div class="instructor-avatar">
                                            @if($course->teacher && $course->teacher->name)
                                                {{ strtoupper(substr($course->teacher->name, 0, 1)) }}
                                            @else
                                                <i class="fas fa-user"></i>
                                            @endif
                                        </div>
                                        <span class="instructor-name">{{ $course->teacher->name ?? 'Teacher' }}</span>
                                    </div>
                                    <div class="course-stats">
                                        <div class="rating-small">
                                            <i class="fas fa-star"></i>
                                            <span>5.0</span>
                                            <span class="count">(0)</span>
                                        </div>
                                        <div class="duration-small">
                                            <i class="far fa-folder"></i>
                                            <span>{{ $course->chapters_count ?? 0 }} chapters</span>
                                        </div>
                                        @if(isset($course->formatted_total_duration))
                                        <div class="duration-small">
                                            <i class="fas fa-clock"></i>
                                            <span>{{ $course->formatted_total_duration }}</span>
                                        </div>
                                        @endif
                                    </div>
                                    <p class="course-price-small">Rp{{ number_format($course->price, 0, ',', '.') }}</p>
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

            <div class="featured-card">
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <div class="featured-image">
                            <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400&h=300&fit=crop" alt="Instructor">
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="featured-content">
                            <h3 class="featured-title">Belajar Full Stack Web Development dari Dasar hingga Membangun Aplikasi Siap Pakai</h3>
                            <p class="featured-description">
                                Pelajari HTML, CSS, JavaScript, dan framework terkini secara mendalam untuk membangun aplikasi web yang modern dan responsif.
                            </p>
                            <div class="featured-rating">
                                <div class="stars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <span class="rating-text">5.0 (324)</span>
                            </div>
                            <p class="featured-price">Rp350,000</p>
                        </div>
                    </div>
                </div>
            </div>
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
                                    @foreach(\App\Models\ClassModel::CATEGORIES as $key => $label)
                                    <label class="filter-option">
                                        <input type="radio" name="category" value="{{ $key }}" {{ request('category') === $key ? 'checked' : '' }}>
                                        <span>{{ $label }}</span>
                                    </label>
                                    @endforeach
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
                                                    {{ \App\Models\ClassModel::CATEGORIES[$course->category] ?? 'Uncategorized' }}
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
                                                <div class="rating">
                                                    <i class="fas fa-star"></i>
                                                    <span>5.0</span>
                                                    <span class="count">(0)</span>
                                                </div>

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