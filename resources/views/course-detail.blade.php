@extends('layouts.app')

@section('title', $course->name)

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/course-detail.css') }}">
<style>
    .course-hero::before {
        background-image: url('{{ $course->image ? asset("storage/" . $course->image) : asset("assets/images/default-course.jpg") }}');
    }
</style>
@endsection

@section('content')
<div class="course-detail-page">
    <!-- Breadcrumb -->
    <div class="breadcrumb-section">
        <div class="container">
            <nav class="breadcrumb-nav">
                <a href="{{ route('courses') }}"><i class="fas fa-home"></i> Courses</a>
                <i class="fas fa-chevron-right"></i>
                <span>{{ Str::limit($course->name, 50) }}</span>
            </nav>
        </div>
    </div>

    <!-- Course Hero Section -->
    <section class="course-hero">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="hero-content-card">
                        <div class="course-badge">
                            <i class="fas fa-graduation-cap"></i>
                            <span>{{ $course->category->name ?? 'Programming' }}</span>
                        </div>
                        
                        <h1 class="hero-title">{{ $course->name }}</h1>
                        <p class="hero-description">{{ $course->description ?? 'Master React from basics to advanced concepts including Hooks, Context API, Redux, and modern best practices!' }}</p>
                        
                        <div class="course-stats-grid">
                            <div class="stat-card">
                                <div class="stat-icon stat-icon-rating">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-value">{{ number_format($ratingStats['average'] ?? 4.9, 1) }}</div>
                                    <div class="stat-label">Rating ({{ number_format($ratingStats['total'] ?? 10224) }})</div>
                                </div>
                            </div>
                            
                            <div class="stat-card">
                                <div class="stat-icon stat-icon-students">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-value">{{ $course->students_count ?? 430 }}</div>
                                    <div class="stat-label">Students</div>
                                </div>
                            </div>
                            
                            <div class="stat-card">
                                <div class="stat-icon stat-icon-duration">
                                    <i class="far fa-clock"></i>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-value">{{ $course->formatted_total_duration ?? '8h' }}</div>
                                    <div class="stat-label">Duration</div>
                                </div>
                            </div>
                        </div>

                        <div class="hero-footer">
                            <div class="creator-info">
                                <div class="creator-avatar">
                                    {{ strtoupper(substr($course->teacher->name ?? 'T', 0, 1)) }}
                                </div>
                                <div class="creator-details">
                                    <small>Created by</small>
                                    <strong>{{ $course->teacher->name ?? 'Teacher\'s Name' }}</strong>
                                </div>
                            </div>
                            <div class="last-updated">
                                <i class="fas fa-calendar-alt"></i>
                                Updated {{ $course->updated_at ? $course->updated_at->format('M Y') : 'Jan 2024' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    @if($isEnrolled)
                        <!-- Enrolled: Progress Card -->
                        @php
                            $firstChapter = $course->chapters->first();
                            $firstModule = $firstChapter ? $firstChapter->modules->first() : null;
                        @endphp
                        <div class="progress-card">
                            <div class="progress-badge">
                                <i class="fas fa-check-circle"></i>
                                <span>Enrolled</span>
                            </div>
                            @if($firstModule)
                                <button class="btn-start-learning" onclick="window.location.href='{{ route('module.show', [$course->id, $firstChapter->id, $firstModule->id]) }}'" aria-live="polite">
                                    <i class="fas fa-play-circle"></i>
                                    @if($progress == 0)
                                        Start Learning
                                    @elseif($progress >= 100)
                                        Learning again
                                    @else
                                        Continue Learning
                                    @endif
                                </button>
                            @else
                                <button class="btn-start-learning" disabled>
                                    <i class="fas fa-info-circle"></i>
                                    No modules available
                                </button>
                            @endif
                            <div class="progress-info">
                                <small>Your Progress</small>
                                <div class="progress-bar-wrapper">
                                    <div class="progress-bar-fill" style="width: {{ $progress }}%"></div>
                                </div>
                                <span class="progress-text">{{ number_format($progress, 0) }}% Complete</span>
                            </div>
                            <hr>
                            <div class="course-includes">
                                <small class="includes-title">This course includes:</small>
                                <ul class="includes-list">
                                    @if($course->formatted_includes && count($course->formatted_includes) > 0)
                                        @foreach($course->formatted_includes as $include)
                                            <li>
                                                <i class="{{ $include['icon'] }}"></i>
                                                {{ $include['text'] }}
                                            </li>
                                        @endforeach
                                    @else
                                        <li><i class="fas fa-video"></i> Video pembelajaran on-demand</li>
                                        <li><i class="fas fa-infinity"></i> Akses seumur hidup</li>
                                        <li><i class="fas fa-certificate"></i> Sertifikat penyelesaian</li>
                                        <li><i class="fas fa-robot"></i> Tanya AI Assistant</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    @else
                        <!-- Not Enrolled: Purchase Card -->
                        <div class="purchase-card">
                            @if($isPopular)
                            <div class="card-ribbon">Popular</div>
                            @endif
                            <div class="price-section">
                                @if($course->price && $course->price > 0)
                                    @php
                                        $original = $course->price;
                                        $current = $course->discounted_price ?? $original;
                                        $discountPct = $course->discount_percentage ?? 0;
                                    @endphp
                                    @if($course->has_discount && $course->discount && $current < $original)
                                        <div class="original-price text-muted text-decoration-line-through">RP{{ number_format($original, 0, ',', '.') }}</div>
                                        <div class="current-price text-primary fw-bold">RP{{ number_format($current, 0, ',', '.') }}</div>
                                        <div class="discount-badge">
                                            <i class="fas fa-bolt"></i> -Rp{{ number_format($course->discount, 0, ',', '.') }} ({{ $discountPct }}% OFF)
                                        </div>
                                        @if($course->discount_ends_at)
                                        <div class="discount-countdown" id="countdown-{{ $course->id }}" style="font-size: 13px; color: #d32f2f; margin-top: 8px; text-align: center; font-weight: 600;">
                                            Diskon berakhir dalam <span class="countdown-timer">--:--:--:--</span>
                                        </div>
                                        <script>
                                            (function() {
                                                const endDate = new Date('{{ $course->discount_ends_at->toIso8601String() }}').getTime();
                                                const countdownEl = document.getElementById('countdown-{{ $course->id }}');
                                                const timerEl = countdownEl?.querySelector('.countdown-timer');
                                                
                                                function updateCountdown() {
                                                    const now = new Date().getTime();
                                                    const distance = endDate - now;
                                                    
                                                    if (distance <= 0) {
                                                        timerEl.textContent = 'BERAKHIR';
                                                        return;
                                                    }
                                                    
                                                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                                                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                                                    
                                                    timerEl.textContent = 
                                                        String(days).padStart(2, '0') + ':' +
                                                        String(hours).padStart(2, '0') + ':' +
                                                        String(minutes).padStart(2, '0') + ':' +
                                                        String(seconds).padStart(2, '0');
                                                }
                                                
                                                updateCountdown();
                                                setInterval(updateCountdown, 1000);
                                            })();
                                        </script>
                                        @elseif($course->discount_starts_at)
                                        <div class="discount-duration" style="font-size: 12px; color: #666; margin-top: 8px; text-align: center;">
                                            Diskon dimulai {{ $course->discount_starts_at->format('d M Y H:i') }}
                                        </div>
                                        @endif
                                    @else
                                        <div class="current-price">RP{{ number_format($original ?? 150000, 0, ',', '.') }}</div>
                                    @endif
                                @else
                                    <div class="current-price">FREE</div>
                                    <div class="free-badge">
                                        <i class="fas fa-gift"></i> Free Course
                                    </div>
                                @endif
                            </div>
                            
                            @auth
                                @if(auth()->user()->isStudent())
                                    <form action="{{ route('cart.add') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="course_id" value="{{ $course->id }}">
                                        <button type="submit" class="btn-add-cart">
                                            <i class="fas fa-shopping-cart"></i> Add to Cart
                                        </button>
                                    </form>
                                    <form action="{{ route('course.enroll', $course->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn-buy-now">
                                            Buy Now
                                        </button>
                                    </form>
                                @elseif(auth()->user()->isTeacher())
                                    @php
                                        $firstChapter = $course->chapters->first();
                                        $firstModule = $firstChapter ? $firstChapter->modules->first() : null;
                                    @endphp
                                    @if($firstModule)
                                        <button class="btn-add-cart" onclick="window.location.href='{{ route('module.show', [$course->id, $firstChapter->id, $firstModule->id]) }}'">
                                            <i class="fas fa-eye me-1"></i> Preview
                                        </button>
                                    @else
                                        <button class="btn-add-cart" disabled>
                                            <i class="fas fa-info-circle me-1"></i> No modules available
                                        </button>
                                    @endif
                                    <button class="btn-buy-now" onclick="window.location.href='{{ route('teacher.classes.edit', $course->id) }}'">
                                        <i class="fas fa-cog me-1"></i> Manage
                                    </button>
                                @else
                                    <button class="btn-add-cart" onclick="window.location.href='{{ route('login') }}'">
                                        <i class="fas fa-sign-in-alt"></i> Login to Purchase
                                    </button>
                                @endif
                            @else
                                <button class="btn-add-cart" onclick="window.location.href='{{ route('login') }}'">
                                    <i class="fas fa-sign-in-alt"></i> Login to Purchase
                                </button>
                            @endauth

                            <hr>
                            <div class="course-includes">
                                <small class="includes-title">This course includes:</small>
                                <ul class="includes-list">
                                    @if($course->formatted_includes && count($course->formatted_includes) > 0)
                                        @foreach($course->formatted_includes as $include)
                                            <li>
                                                <i class="{{ $include['icon'] }}"></i>
                                                {{ $include['text'] }}
                                            </li>
                                        @endforeach
                                    @else
                                        <li><i class="fas fa-video"></i> Video pembelajaran on-demand</li>
                                        <li><i class="fas fa-infinity"></i> Akses seumur hidup</li>
                                        <li><i class="fas fa-certificate"></i> Sertifikat penyelesaian</li>
                                        <li><i class="fas fa-robot"></i> Tanya AI Assistant</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <!-- What you'll learn -->
            @if($course->what_youll_learn)
            <div class="content-section what-learn-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <span class="title-icon"><i class="fas fa-lightbulb"></i></span>
                        What you'll learn
                    </h2>
                    <p class="section-subtitle">Master these key skills and concepts</p>
                </div>
                <div class="learning-grid">
                    @php
                        $learningPoints = array_filter(explode("\n", $course->what_youll_learn));
                    @endphp
                    @foreach($learningPoints as $index => $point)
                        @if(trim($point))
                        <div class="learning-card" style="animation-delay: {{ $index * 0.1 }}s">
                            <div class="check-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <span>{{ trim($point) }}</span>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Course Content -->
            <div class="content-section course-content-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <span class="title-icon"><i class="fas fa-book-open"></i></span>
                        Course content
                    </h2>
                    <p class="section-subtitle">{{ $course->chapters_count ?? 0 }} chapters · {{ $course->formatted_total_duration ?? '8 hours' }} total length</p>
                </div>
                
                @if($course->chapters->count() > 0)
                    <div class="chapters-accordion">
                        @foreach($course->chapters as $index => $chapter)
                        <div class="chapter-card">
                            <div class="chapter-header-wrapper">
                                <div class="chapter-number">{{ $index + 1 }}</div>
                                <div class="chapter-content-info">
                                    <h6 class="chapter-title">{{ $chapter->title }}</h6>
                                    <div class="chapter-meta">
                                        <span><i class="fas fa-play-circle"></i> {{ $chapter->modules->count() }} lessons</span>
                                        <span><i class="far fa-clock"></i> {{ $chapter->formatted_total_duration ?? '34 min' }}</span>
                                    </div>
                                </div>
                                @if($isEnrolled)
                                <span class="access-badge access-unlocked">
                                    <i class="fas fa-unlock"></i> Unlocked
                                </span>
                                @else
                                <span class="access-badge access-locked">
                                    <i class="fas fa-lock"></i> Locked
                                </span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-folder-open"></i>
                        <p>No chapters available yet.</p>
                    </div>
                @endif
            </div>

            <!-- Requirements -->
            @if($course->requirement)
            <div class="content-section">
                <h2 class="section-title">Requirements</h2>
                <ul class="requirements-list">
                    @foreach(explode("\n", $course->requirement) as $req)
                        @if(trim($req))
                        <li>{{ trim($req) }}</li>
                        @endif
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Description -->
            @if($course->description)
            <div class="content-section">
                <h2 class="section-title">Description</h2>
                <p class="description-text">{{ $course->description }}</p>
            </div>
            @endif

            <!-- Instructors -->
            <div class="content-section">
                <h2 class="section-title">Instructors</h2>
                <div class="instructor-card">
                    <div class="instructor-avatar">
                        <span>{{ strtoupper(substr($course->teacher->name ?? 'T', 0, 1)) }}</span>
                    </div>
                    <div class="instructor-details">
                        <h5 class="instructor-name">{{ $course->teacher->name ?? 'Instructor Name' }}</h5>
                        <p class="instructor-bio">
                            @if($course->teacher->bio)
                                {{ $course->teacher->bio }}
                            @elseif($course->teacher->biography)
                                {{ $course->teacher->biography }}
                            @else
                                Passionate educator dedicated to helping students achieve their learning goals and master new skills.
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Student Reviews -->
            <div class="content-section">
                <h2 class="section-title">Student Reviews</h2>
                
                @if($ratingStats['total'] > 0)
                <div class="reviews-summary">
                    <div class="rating-overview">
                        <div class="rating-number">{{ number_format($ratingStats['average'] ?? 4.9, 1) }}</div>
                        <div class="rating-stars">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= round($ratingStats['average'] ?? 4.9) ? 'filled' : '' }}"></i>
                            @endfor
                        </div>
                        <p class="rating-label">Course Rating based on {{ number_format($ratingStats['total'] ?? 0) }} {{ $ratingStats['total'] == 1 ? 'review' : 'reviews' }}</p>
                    </div>
                    
                    <div class="rating-bars">
                        @for($i = 5; $i >= 1; $i--)
                        <div class="rating-bar-row">
                            <span class="star-label">{{ $i }} <i class="fas fa-star"></i></span>
                            <div class="rating-bar-bg">
                                <div class="rating-bar-fill" style="width: {{ $ratingStats['distribution'][$i]['percentage'] ?? (6-$i)*15 }}%"></div>
                            </div>
                            <span class="percentage">{{ $ratingStats['distribution'][$i]['percentage'] ?? (6-$i)*15 }}%</span>
                        </div>
                        @endfor
                    </div>
                </div>
                @endif

                <!-- Leave a Review (Only for Enrolled Students) -->
                @if($isEnrolled && auth()->check() && auth()->user()->isStudent())
                <div class="leave-review-box">
                    <h4 class="review-box-title">Leave a review</h4>
                    <form action="{{ route('course.rating.submit', $course->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Your Rating</label>
                            <div class="star-rating" id="starRating">
                                @for($i = 1; $i <= 5; $i++)
                                <i class="far fa-star" data-rating="{{ $i }}"></i>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" id="ratingInput" required>
                        </div>
                        <div class="form-group">
                            <label>Your Review</label>
                            <textarea name="comment" class="form-control" rows="4" placeholder="Share your experience with this course...">{{ old('comment', $userReview->comment ?? '') }}</textarea>
                        </div>
                        <button type="submit" class="btn-submit-review">
                            <i class="fas fa-paper-plane"></i> Submit Review
                        </button>
                    </form>
                </div>
                @endif

                <!-- Reviews List -->
                @if($reviews->count() > 0)
                <div class="reviews-list">
                    @foreach($reviews as $review)
                    <div class="review-card">
                        <div class="review-card-header">
                            <div class="review-card-user">
                                <div class="reviewer-avatar">
                                    @if(isset($review->user->avatar) && $review->user->avatar)
                                        <img src="{{ asset('storage/' . $review->user->avatar) }}" alt="{{ $review->user->name ?? 'User' }}" class="avatar-image">
                                    @else
                                        <span class="avatar-initial">{{ strtoupper(substr($review->user->name ?? 'M', 0, 2)) }}</span>
                                    @endif
                                </div>
                                <div class="review-user-info">
                                    <h6 class="reviewer-name">{{ $review->user->name ?? 'Anonymous User' }}</h6>
                                    <small class="review-date">{{ $review->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="review-card-rating">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $review->rating ? 'filled' : 'empty' }}"></i>
                            @endfor
                        </div>

                        @if($review->comment)
                        <div class="review-card-content">
                            <p class="review-text">{{ $review->comment }}</p>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                
                <div class="text-center mt-4">
                    <button class="btn-load-more">
                        <i class="fas fa-chevron-down"></i> Load More Reviews
                    </button>
                </div>
                @else
                <div class="empty-state" style="text-align: center; padding: 48px 24px; background: #f8f9fa; border-radius: 12px; margin-top: 24px;">
                    <i class="fas fa-comments" style="font-size: 48px; color: #e0e0e0; margin-bottom: 16px; display: block;"></i>
                    <h4 style="color: #828282; font-size: 16px; font-weight: 600; margin-bottom: 8px;">There is no review provided yet</h4>
                    <p style="color: #828282; font-size: 14px; margin: 0;">Be the first to leave a review for this course.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Star rating interaction
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('#starRating i');
        const ratingInput = document.getElementById('ratingInput');
        let selectedRating = 0;

        stars.forEach(star => {
            star.addEventListener('click', function() {
                selectedRating = parseInt(this.getAttribute('data-rating'));
                ratingInput.value = selectedRating;
                updateStars(selectedRating);
            });

            star.addEventListener('mouseover', function() {
                const hoverRating = parseInt(this.getAttribute('data-rating'));
                updateStars(hoverRating);
            });
        });

        document.getElementById('starRating')?.addEventListener('mouseleave', function() {
            updateStars(selectedRating);
        });

        function updateStars(rating) {
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.remove('far');
                    star.classList.add('fas');
                } else {
                    star.classList.remove('fas');
                    star.classList.add('far');
                }
            });
        }
    });
</script>
@endsection