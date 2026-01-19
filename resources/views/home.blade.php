@extends('layouts.app')

@section('title', 'Home')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/welcome.css') }}">
@endsection

@section('content')

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
<section class="py-5 bg-light">
    <div class="container">
        <h4 class="fw-bold mb-3">Skills to transform your career and life</h4>

        <div class="row g-4">
            @foreach([1,2,3,4] as $course)
            <div class="col-md-3">
                <div class="border rounded-4 overflow-hidden h-100">
                    <img src="{{ asset("assets/img/course$course.jpg") }}"
                         class="w-100"
                         style="height:160px; object-fit:cover">

                    <div class="p-3">
                        <h6 class="fw-semibold mb-1">Course Title</h6>
                        <p class="small text-muted mb-2">Teacher's Name</p>
                        <p class="fw-bold mb-0">
                            Rp{{ number_format(100000 * $course,0,',','.') }}
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-5">
    <div class="container">
        <h4 class="fw-bold mb-4">
            Join others transforming their lives through learning
        </h4>

        <div class="row g-4">
            @foreach([1,2,3] as $t)
            <div class="col-md-4">
                <div class="border rounded-4 p-4 h-100">
                    <p class="fst-italic small">
                        “Course ini sangat membantu dan materinya mudah dipahami.”
                    </p>
                    <div class="d-flex align-items-center mt-3">
                        <img src="{{ asset('assets/img/avatar.png') }}"
                             width="40"
                             class="rounded-circle me-2">
                        <div>
                            <strong class="small d-block">Nama User</strong>
                            <span class="small text-muted">Professional</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5">
    <div class="container">
        <h4 class="fw-bold mb-4">Frequently Asked Question (FAQ)</h4>

        <div class="accordion" id="faqAccordion">
            @for($i=1;$i<=4;$i++)
            <div class="accordion-item mb-3">
                <h2 class="accordion-header">
                    <button class="accordion-button {{ $i != 1 ? 'collapsed' : '' }}"
                            data-bs-toggle="collapse"
                            data-bs-target="#faq{{ $i }}">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit
                    </button>
                </h2>
                <div id="faq{{ $i }}"
                     class="accordion-collapse collapse {{ $i == 1 ? 'show' : '' }}">
                    <div class="accordion-body">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit,
                        sed do eiusmod tempor incididunt ut labore.
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</section>
@endsection