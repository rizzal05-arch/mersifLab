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

    .chapter-accordion {
        margin-top: 2rem;
    }

    .module-item {
        padding: 1rem;
        border-left: 3px solid #667eea;
        background: #f8f9fa;
        margin-bottom: 0.5rem;
        border-radius: 4px;
    }

    .module-icon {
        margin-right: 0.5rem;
        color: #667eea;
    }
</style>
@endsection

@section('content')
<!-- Course Hero Section -->
<section class="course-hero">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold">{{ $course->name }}</h1>
                <p class="lead mt-3">{{ $course->description }}</p>
                <div class="mt-4">
                    <p class="mb-2">
                        <i class="fas fa-user-tie me-2"></i>
                        <strong>Instructor:</strong> {{ $course->teacher->name ?? 'Unknown Teacher' }}
                    </p>
                    <p class="mb-2">
                        <i class="fas fa-folder me-2"></i>
                        <strong>Chapters:</strong> {{ $course->chapters_count ?? 0 }}
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-book me-2"></i>
                        <strong>Modules:</strong> {{ $course->modules_count ?? 0 }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Course Content Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-4">Course Content</h3>

                @if($course->chapters->count() > 0)
                    <div class="accordion chapter-accordion" id="courseAccordion">
                        @foreach($course->chapters as $chapter)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#chapter{{ $loop->index }}">
                                    <strong>{{ $loop->iteration }}. {{ $chapter->title }}</strong>
                                    <span class="badge bg-secondary ms-2">
                                        {{ $chapter->modules->count() }} modules
                                    </span>
                                </button>
                            </h2>
                            <div id="chapter{{ $loop->index }}" 
                                 class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" 
                                 data-bs-parent="#courseAccordion">
                                <div class="accordion-body p-0">
                                    @if($chapter->modules->count() > 0)
                                        @foreach($chapter->modules as $module)
                                        <div class="module-item">
                                            <div class="d-flex align-items-start">
                                                <span class="module-icon">
                                                    @if($module->type === 'text')
                                                        <i class="fas fa-align-left"></i>
                                                    @elseif($module->type === 'document')
                                                        <i class="fas fa-file-pdf"></i>
                                                    @elseif($module->type === 'video')
                                                        <i class="fas fa-play-circle"></i>
                                                    @else
                                                        <i class="fas fa-file"></i>
                                                    @endif
                                                </span>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">{{ $module->title }}</h6>
                                                    <small class="text-muted">
                                                        Type: <strong>{{ ucfirst($module->type) }}</strong>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    @else
                                        <div class="p-3 text-muted text-center">
                                            No modules in this chapter yet.
                                        </div>
                                    @endif
                                </div>
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

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-4">Course Information</h5>

                        <div class="mb-3">
                            <label class="text-muted small">Instructor</label>
                            <p class="mb-0">{{ $course->teacher->name ?? 'Unknown' }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted small">Category</label>
                            <p class="mb-0">
                                <span class="badge bg-primary">
                                    {{ \App\Models\ClassModel::CATEGORIES[$course->category] ?? 'Uncategorized' }}
                                </span>
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted small">Total Chapters</label>
                            <p class="mb-0">{{ $course->chapters_count ?? 0 }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="text-muted small">Total Modules</label>
                            <p class="mb-0">{{ $course->modules_count ?? 0 }}</p>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <h6 class="fw-bold">Price</h6>
                            <h4 class="text-primary fw-bold">Rp100,000</h4>
                        </div>

                        @auth
                            @if(auth()->user()->isStudent())
                            <button class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-shopping-cart me-2"></i>Enroll Now
                            </button>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-sign-in-alt me-2"></i>Login to Enroll
                            </a>
                        @endauth

                        <a href="{{ route('courses') }}" class="btn btn-outline-secondary w-100">
                            Back to Courses
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
