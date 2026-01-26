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

    .rating-input {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
        gap: 5px;
    }

    .rating-input input[type="radio"] {
        display: none;
    }

    .rating-input .star-label {
        font-size: 2rem;
        color: #ddd;
        cursor: pointer;
        transition: color 0.2s;
    }

    .rating-input input[type="radio"]:checked ~ .star-label {
        color: #ffc107;
    }

    .rating-input .star-label:hover,
    .rating-input .star-label:hover ~ .star-label {
        color: #ffc107;
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
            @if($course->image)
            <div class="col-lg-4 mb-4 mb-lg-0">
                @php
                    $imagePath = 'storage/' . $course->image;
                    $imageExists = file_exists(public_path($imagePath)) || file_exists(storage_path('app/public/' . $course->image));
                @endphp
                @if($imageExists)
                    <img src="{{ asset($imagePath) }}" 
                         alt="{{ $course->name }}" 
                         class="img-fluid rounded shadow" 
                         style="max-height: 300px; width: 100%; object-fit: cover;">
                @else
                    <div style="max-height: 300px; width: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white;">
                        <i class="fas fa-image fa-3x"></i>
                    </div>
                @endif
            </div>
            @endif
            <div class="{{ $course->image ? 'col-lg-8' : 'col-lg-12' }}">
                <h1 class="display-5 fw-bold mb-3">{{ $course->name }}</h1>
                <p class="lead mb-4">{{ $course->description ?? 'No description available' }}</p>
                
                <div class="d-flex flex-wrap gap-4 mb-3">
                    <div>
                        <i class="fas fa-star text-warning me-1"></i>
                        <strong>{{ number_format($ratingStats['average'] ?? 0, 1) }}</strong> 
                        <span class="small">({{ number_format($ratingStats['total'] ?? 0) }} ratings)</span>
                    </div>
                    <div>
                        <i class="fas fa-users me-1"></i>
                        <strong>{{ $course->students_count ?? 0 }} students</strong>
                    </div>
                    <div>
                        <i class="far fa-clock me-1"></i>
                        <strong>{{ $course->formatted_total_duration }}</strong>
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
                @if($course->what_youll_learn)
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="fw-bold mb-4">What you'll learn</h3>
                        <div class="row">
                            @php
                                $learningPoints = array_filter(explode("\n", $course->what_youll_learn));
                                $halfCount = ceil(count($learningPoints) / 2);
                                $firstHalf = array_slice($learningPoints, 0, $halfCount);
                                $secondHalf = array_slice($learningPoints, $halfCount);
                            @endphp
                            <div class="col-md-6">
                                @foreach($firstHalf as $point)
                                    @if(trim($point))
                                    <div class="learning-item">
                                        <i class="fas fa-check-circle"></i>
                                        <span>{{ trim($point) }}</span>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                            <div class="col-md-6">
                                @foreach($secondHalf as $point)
                                    @if(trim($point))
                                    <div class="learning-item">
                                        <i class="fas fa-check-circle"></i>
                                        <span>{{ trim($point) }}</span>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Course Content Section -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="fw-bold mb-2">Course content</h3>
                        <p class="text-muted mb-4">{{ $course->chapters_count ?? 0 }} chapters · {{ $course->formatted_total_duration }}</p>

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
                                                <small class="text-muted d-block">{{ $chapter->formatted_total_duration }}</small>
                                                
                                                @if($chapter->modules->count() > 0)
                                                    <div class="mt-2">
                                                        @foreach($chapter->modules as $mod)
                                                            @if($isEnrolled || auth()->check() && (auth()->user()->isTeacher() || auth()->user()->isAdmin()))
                                                                <a href="{{ route('module.show', [$course->id, $chapter->id, $mod->id]) }}" 
                                                                   class="d-block text-decoration-none text-primary mb-1 small">
                                                                    <i class="fas {{ $mod->type == 'video' ? 'fa-play-circle' : ($mod->type == 'document' ? 'fa-file-pdf' : 'fa-align-left') }} me-1"></i>
                                                                    {{ $mod->title }}
                                                                    @if($mod->estimated_duration > 0)
                                                                        <span class="badge bg-secondary ms-2">{{ $mod->estimated_duration }} menit</span>
                                                                    @endif
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
                                        <span class="text-muted">{{ $chapter->formatted_total_duration }}</span>
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
                @if($course->requirement)
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="fw-bold mb-4">Requirements</h3>
                        <ul class="list-unstyled">
                            @foreach(explode("\n", $course->requirement) as $req)
                                @if(trim($req))
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ trim($req) }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif

                <!-- Description Section -->
                @if($course->description)
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="fw-bold mb-4">Description</h3>
                        <p>{{ $course->description }}</p>
                    </div>
                </div>
                @endif

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

                <!-- Rating Form (for enrolled students) -->
                @if($isEnrolled && auth()->check() && auth()->user()->isStudent())
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="fw-bold mb-3">Berikan Rating</h3>
                        @if($userReview)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Anda sudah memberikan rating untuk course ini. Anda dapat mengubah rating Anda di bawah ini.
                            </div>
                        @endif
                        <form action="{{ route('course.rating.submit', $course->id) }}" method="POST" id="ratingForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Rating</label>
                                <div class="rating-input">
                                    @for($i = 5; $i >= 1; $i--)
                                    <input type="radio" name="rating" id="rating{{ $i }}" value="{{ $i }}" 
                                           {{ $userReview && $userReview->rating == $i ? 'checked' : '' }} required>
                                    <label for="rating{{ $i }}" class="star-label">
                                        <i class="fas fa-star"></i>
                                    </label>
                                    @endfor
                                </div>
                                @error('rating')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="comment" class="form-label">Comment (Optional)</label>
                                <textarea class="form-control" id="comment" name="comment" rows="3" 
                                          placeholder="Share your experience about this course...">{{ old('comment', $userReview->comment ?? '') }}</textarea>
                                @error('comment')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary" id="submitRatingBtn">
                                <i class="fas fa-star me-2"></i><span id="submitRatingText">{{ $userReview ? 'Update Rating' : 'Submit Rating' }}</span>
                            </button>
                        </form>
                    </div>
                </div>
                @endif

                <!-- Student Reviews Section -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="fw-bold mb-4">Student Reviews</h3>
                        
                        @if($ratingStats['total'] > 0)
                        <div class="row mb-4">
                            <div class="col-md-4 text-center">
                                <h2 class="fw-bold mb-2">{{ number_format($ratingStats['average'], 1) }}</h2>
                                <div class="mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= round($ratingStats['average']) ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                </div>
                                <small class="text-muted">Course Rating</small>
                                <p class="small text-muted mt-2">{{ $ratingStats['total'] }} {{ $ratingStats['total'] == 1 ? 'review' : 'reviews' }}</p>
                            </div>
                            <div class="col-md-8">
                                @for($i = 5; $i >= 1; $i--)
                                <div class="mb-2">
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="me-2" style="width: 60px;">{{ $i }} <i class="fas fa-star text-warning"></i></span>
                                        <div class="rating-bar flex-grow-1">
                                            <div class="rating-fill" style="width: {{ $ratingStats['distribution'][$i]['percentage'] ?? 0 }}%;"></div>
                                        </div>
                                        <span class="ms-2 small">{{ $ratingStats['distribution'][$i]['percentage'] ?? 0 }}%</span>
                                    </div>
                                </div>
                                @endfor
                            </div>
                        </div>
                        @else
                        <div class="text-center py-4">
                            <i class="fas fa-star fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada rating untuk course ini</p>
                        </div>
                        @endif

                        @if($reviews->count() > 0)
                            @foreach($reviews as $review)
                            <div class="review-item">
                                <div class="d-flex align-items-start mb-2">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-size: 14px;">
                                        {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <strong>{{ $review->user->name ?? 'Anonymous' }}</strong>
                                                <div class="mb-1">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                    @endfor
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                                        </div>
                                        @if($review->comment)
                                            <p class="mb-0">{{ $review->comment }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada review untuk course ini</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar Right -->
            <div class="col-lg-4">
                @if(!$isEnrolled)
                    <!-- Purchase Card for Not Enrolled -->
                    <div class="course-purchase-card">
                        <div class="text-center mb-3">
                            <h3 class="fw-bold mb-0">Rp{{ number_format($course->price ?? 0, 0, ',', '.') }}</h3>
                            @if($course->price && $course->price > 0)
                                @php
                                    $originalPrice = $course->price * 1.1; // 10% discount
                                @endphp
                                <span class="badge bg-danger">10% OFF</span>
                            @endif
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
                                    <form action="{{ route('cart.add') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="course_id" value="{{ $course->id }}">
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

@section('scripts')
<script>
    // Handle rating form submission with AJAX
    document.getElementById('ratingForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const formData = new FormData(form);
        const submitBtn = document.getElementById('submitRatingBtn');
        const submitText = document.getElementById('submitRatingText');
        const originalText = submitText.textContent;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || formData.get('_token');
        
        // Show loading state
        submitBtn.disabled = true;
        submitText.textContent = 'Mengirim...';
        
        // Send AJAX request
        fetch('{{ route("course.rating.submit", $course->id) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'Terjadi kesalahan saat mengirim rating');
                });
            }
            return response.json();
        })
        .then(data => {
            // Show thank you popup
            Swal.fire({
                icon: 'success',
                title: 'Terima Kasih!',
                html: `
                    <div style="text-align: center;">
                        <i class="fas fa-heart" style="font-size: 3rem; color: #ff6b6b; margin-bottom: 1rem; animation: heartbeat 1.5s ease-in-out infinite;"></i>
                        <p style="font-size: 1.1rem; margin-bottom: 0.5rem;">Terima kasih sudah memberikan rating!</p>
                        <p style="color: #6c757d; font-size: 0.9rem;">Feedback Anda sangat berarti bagi kami dan instruktur.</p>
                    </div>
                `,
                confirmButtonText: 'Saya Senang!',
                confirmButtonColor: '#667eea',
                timer: 5000,
                timerProgressBar: true,
                showCloseButton: true,
                allowOutsideClick: true,
                allowEscapeKey: true,
                customClass: {
                    popup: 'animated-popup',
                    backdrop: 'swal2-backdrop-smooth'
                },
                showClass: {
                    popup: 'swal2-show-smooth',
                    backdrop: 'swal2-backdrop-show-smooth'
                },
                hideClass: {
                    popup: 'swal2-hide-smooth',
                    backdrop: 'swal2-backdrop-hide-smooth'
                }
            }).then(() => {
                // Reload page to show updated rating
                window.location.reload();
            });
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: error.message || 'Terjadi kesalahan saat mengirim rating. Silakan coba lagi.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc3545'
            });
            
            // Reset button state
            submitBtn.disabled = false;
            submitText.textContent = originalText;
        });
    });

    // Show thank you popup after successful rating submission (for non-AJAX fallback)
    @if(session('success') && str_contains(session('success'), 'Rating'))
        Swal.fire({
            icon: 'success',
            title: 'Terima Kasih!',
            html: `
                <div style="text-align: center;">
                    <i class="fas fa-heart" style="font-size: 3rem; color: #ff6b6b; margin-bottom: 1rem; animation: heartbeat 1.5s ease-in-out infinite;"></i>
                    <p style="font-size: 1.1rem; margin-bottom: 0.5rem;">Terima kasih sudah memberikan rating!</p>
                    <p style="color: #6c757d; font-size: 0.9rem;">Feedback Anda sangat berarti bagi kami dan instruktur.</p>
                </div>
            `,
            confirmButtonText: 'Saya Senang!',
            confirmButtonColor: '#667eea',
            timer: 5000,
            timerProgressBar: true,
            showCloseButton: true,
            allowOutsideClick: true,
            allowEscapeKey: true,
            customClass: {
                popup: 'animated-popup',
                backdrop: 'swal2-backdrop-smooth'
            },
            showClass: {
                popup: 'swal2-show-smooth',
                backdrop: 'swal2-backdrop-show-smooth'
            },
            hideClass: {
                popup: 'swal2-hide-smooth',
                backdrop: 'swal2-backdrop-hide-smooth'
            }
        });
    @endif

    // Show error popup if any
    @if(session('error'))
        Swal.fire({
            icon: 'warning',
            title: 'Akses Dibatasi',
            html: `
                <div style="text-align: center;">
                    <i class="fas fa-lock" style="font-size: 3rem; color: #ffc107; margin-bottom: 1rem;"></i>
                    <p style="font-size: 1.1rem; margin-bottom: 0.5rem; color: #333;">{{ session("error") }}</p>
                </div>
            `,
            confirmButtonText: 'Mengerti',
            confirmButtonColor: '#667eea',
            allowOutsideClick: false,
            allowEscapeKey: true,
            customClass: {
                popup: 'animated-popup',
                backdrop: 'swal2-backdrop-smooth'
            },
            showClass: {
                popup: 'swal2-show-smooth',
                backdrop: 'swal2-backdrop-show-smooth'
            },
            hideClass: {
                popup: 'swal2-hide-smooth',
                backdrop: 'swal2-backdrop-hide-smooth'
            }
        }).then(() => {
            // Stay on current page or redirect to courses list
            @if(request()->is('course/*'))
                // Already on course detail page, just close popup
            @else
                window.location.href = '{{ route("courses") }}';
            @endif
        });
    @endif
</script>
<style>
    /* Smooth animations for popup */
    .animated-popup {
        animation: slideInDown 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    
    @keyframes slideInDown {
        from {
            transform: translateY(-30px) scale(0.95);
            opacity: 0;
        }
        to {
            transform: translateY(0) scale(1);
            opacity: 1;
        }
    }
    
    /* Heartbeat animation for heart icon */
    @keyframes heartbeat {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
    }
    
    /* Smooth backdrop show animation */
    .swal2-backdrop-show-smooth {
        animation: fadeInBackdrop 0.25s ease-out !important;
    }
    
    /* Smooth backdrop hide animation */
    .swal2-backdrop-hide-smooth {
        animation: fadeOutBackdrop 0.3s ease-out !important;
    }
    
    @keyframes fadeInBackdrop {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    
    @keyframes fadeOutBackdrop {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
        }
    }
    
    /* Smooth popup show animation */
    .swal2-show-smooth {
        animation: slideInDownSmooth 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
    }
    
    @keyframes slideInDownSmooth {
        from {
            transform: translateY(-30px) scale(0.95);
            opacity: 0;
        }
        to {
            transform: translateY(0) scale(1);
            opacity: 1;
        }
    }
    
    /* Smooth popup hide animation */
    .swal2-hide-smooth {
        animation: fadeOutUpSmooth 0.3s cubic-bezier(0.55, 0.055, 0.675, 0.19) !important;
    }
    
    @keyframes fadeOutUpSmooth {
        from {
            transform: translateY(0) scale(1);
            opacity: 1;
        }
        to {
            transform: translateY(-15px) scale(0.96);
            opacity: 0;
        }
    }
    
    /* Override default SweetAlert animations for smoother transitions */
    .swal2-popup.swal2-hide {
        animation: fadeOutUpSmooth 0.3s cubic-bezier(0.55, 0.055, 0.675, 0.19) !important;
    }
    
    /* Ensure smooth container transitions */
    .swal2-container {
        transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    
    .swal2-container.swal2-backdrop-show {
        animation: fadeInBackdrop 0.25s ease-out !important;
    }
    
    .swal2-container.swal2-backdrop-hide {
        animation: fadeOutBackdrop 0.3s ease-out !important;
    }
</style>
@endsection
