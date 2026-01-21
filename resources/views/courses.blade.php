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
                <i class="fas fa-fire-alt"></i>
                <h2 class="section-title">Most Popular Courses</h2>
            </div>

            <div class="row g-3">
                @for($i = 0; $i < 3; $i++)
                <div class="col-lg-4 col-md-6">
                    <div class="course-card-small">
                        <div class="course-image-small">
                            <img src="https://images.unsplash.com/photo-1677442136019-21780ecad995?w=400&h=200&fit=crop" alt="Course">
                        </div>
                        <div class="course-body">
                            <h6 class="course-title-small">Body Tracking Berbasis Artificial Intelligence</h6>
                            <div class="course-meta-small">
                                <div class="instructor-info">
                                    <div class="instructor-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <span class="instructor-name">Teacher's Name</span>
                                </div>
                                <div class="course-stats">
                                    <div class="rating-small">
                                        <i class="fas fa-star"></i>
                                        <span>5.0</span>
                                        <span class="count">(324)</span>
                                    </div>
                                    <div class="duration-small">
                                        <i class="far fa-clock"></i>
                                        <span>4 hours</span>
                                    </div>
                                </div>
                            </div>
                            <p class="course-price-small">Rp100,000</p>
                        </div>
                    </div>
                </div>
                @endfor
            </div>
        </section>

        <!-- Featured Content Section -->
        <section class="featured-section mb-5">
            <div class="section-header">
                <h2 class="section-title">Featured contents</h2>
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
                        @for($i = 0; $i < 6; $i++)
                        <div class="instructor-card">
                            <div class="instructor-avatar-large">
                                <i class="fas fa-user"></i>
                            </div>
                            <h6 class="instructor-name-large">Teacher's Name</h6>
                            <div class="instructor-stats-row">
                                <div class="stat-item">
                                    <i class="fas fa-book"></i>
                                    <div>
                                        <p class="stat-label">Students</p>
                                        <p class="stat-value">40,000</p>
                                    </div>
                                </div>
                                <div class="stat-item">
                                    <i class="fas fa-graduation-cap"></i>
                                    <div>
                                        <p class="stat-label">Courses</p>
                                        <p class="stat-value">15</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endfor
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
                    <div class="filters-card">
                        <h5 class="filters-title">Filters</h5>

                        <!-- Category Filter -->
                        <div class="filter-group">
                            <h6 class="filter-label">Category</h6>
                            <div class="filter-options">
                                <label class="filter-option">
                                    <input type="checkbox" name="category" value="all" checked>
                                    <span>All</span>
                                </label>
                                <label class="filter-option">
                                    <input type="checkbox" name="category" value="development">
                                    <span>Development</span>
                                </label>
                                <label class="filter-option">
                                    <input type="checkbox" name="category" value="design">
                                    <span>Design</span>
                                </label>
                                <label class="filter-option">
                                    <input type="checkbox" name="category" value="marketing">
                                    <span>Marketing</span>
                                </label>
                                <label class="filter-option">
                                    <input type="checkbox" name="category" value="ai">
                                    <span>Artificial Intelligence (AI)</span>
                                </label>
                            </div>
                        </div>

                        <!-- Rating Filter -->
                        <div class="filter-group">
                            <h6 class="filter-label">Rating</h6>
                            <div class="filter-options">
                                <label class="filter-option">
                                    <input type="radio" name="rating" value="all" checked>
                                    <span>All</span>
                                </label>
                                <label class="filter-option">
                                    <div class="rating-stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <input type="radio" name="rating" value="5">
                                    <span>5.0+</span>
                                </label>
                                <label class="filter-option">
                                    <div class="rating-stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                    <input type="radio" name="rating" value="4">
                                    <span>4.0+</span>
                                </label>
                                <label class="filter-option">
                                    <div class="rating-stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                    <input type="radio" name="rating" value="3">
                                    <span>3.0+</span>
                                </label>
                            </div>
                        </div>

                        <!-- Level Filter -->
                        <div class="filter-group">
                            <h6 class="filter-label">Level</h6>
                            <div class="filter-options">
                                <label class="filter-option">
                                    <input type="radio" name="level" value="all" checked>
                                    <span>All Level</span>
                                </label>
                                <label class="filter-option">
                                    <input type="radio" name="level" value="beginner">
                                    <span>Beginner</span>
                                </label>
                                <label class="filter-option">
                                    <input type="radio" name="level" value="intermediate">
                                    <span>Intermediate</span>
                                </label>
                                <label class="filter-option">
                                    <input type="radio" name="level" value="advanced">
                                    <span>Advanced</span>
                                </label>
                            </div>
                        </div>

                        <!-- Price Range Filter -->
                        <div class="filter-group">
                            <h6 class="filter-label">Price Range</h6>
                            <div class="price-range-inputs">
                                <input type="number" class="form-control" placeholder="Min" value="0">
                                <span>-</span>
                                <input type="number" class="form-control" placeholder="Max" value="500000">
                            </div>
                        </div>

                        <!-- Apply Filters Button -->
                        <button class="btn btn-primary w-100 apply-filters-btn">Apply Filters</button>
                    </div>
                </div>

                <!-- Courses Grid -->
                <div class="col-lg-9">
                    <div class="row g-3">
                        @for($i = 0; $i < 4; $i++)
                        <div class="col-lg-4 col-md-6">
                            <div class="course-card">
                                <div class="course-image">
                                    <img src="https://images.unsplash.com/photo-1677442136019-21780ecad995?w=400&h=250&fit=crop" alt="Course">
                                </div>
                                <div class="course-body">
                                    <h6 class="course-title">Body Tracking Berbasis Artificial Intelligence</h6>
                                    <div class="instructor-info-inline">
                                        <div class="instructor-avatar-sm">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <span class="instructor-name">Teacher's Name</span>
                                    </div>
                                    <div class="course-footer">
                                        <div class="rating">
                                            <i class="fas fa-star"></i>
                                            <span>5.0</span>
                                            <span class="count">(324)</span>
                                        </div>
                                        <div class="duration">
                                            <i class="far fa-clock"></i>
                                            <span>4 hours</span>
                                        </div>
                                    </div>
                                    <p class="course-price">Rp100,000</p>
                                </div>
                            </div>
                        </div>
                        @endfor
                    </div>

                    <!-- Pagination -->
                    <nav class="pagination-nav">
                        <ul class="pagination">
                            <li class="page-item">
                                <a class="page-link" href="#">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                            <li class="page-item active">
                                <a class="page-link" href="#">1</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">...</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">5</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
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
        const cardWidth = 200; // Width of each instructor card + gap
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
            prevBtn.style.opacity = prevBtn.disabled ? '0.3' : '1';
            nextBtn.style.opacity = nextBtn.disabled ? '0.3' : '1';
        }

        updateButtons();
    }

    // Apply Filters
    document.querySelector('.apply-filters-btn')?.addEventListener('click', function() {
        // Collect filter values
        const categories = Array.from(document.querySelectorAll('input[name="category"]:checked'))
            .map(cb => cb.value);
        const rating = document.querySelector('input[name="rating"]:checked')?.value;
        const level = document.querySelector('input[name="level"]:checked')?.value;
        
        console.log('Filters:', { categories, rating, level });
        
        // In real implementation, this would trigger an AJAX request or page reload with filters
        alert('Filters will be applied (Backend implementation needed)');
    });

    // Course card click
    document.querySelectorAll('.course-card, .course-card-small').forEach(card => {
        card.addEventListener('click', function() {
            // Redirect to course detail page
            // window.location.href = '/course/' + courseId;
        });
    });
</script>
@endsection