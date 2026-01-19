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
<section class="py-5 bg-light">
    <div class="container">
        <h4 class="fw-bold mb-3">Skills to transform your career and life</h4>

        <div class="row g-4">
            @if(isset($courses) && $courses->count() > 0)
                @foreach($courses as $course)
                <div class="col-md-3">
                    <a href="{{ route('course.detail', $course->id) }}" class="text-decoration-none">
                        <div class="border rounded-4 overflow-hidden h-100 shadow-sm">
                            <div class="bg-primary" style="height:160px; display:flex; align-items:center; justify-content:center; color:white; font-size:2rem;">
                                <i class="fas fa-book"></i>
                            </div>

                            <div class="p-3">
                                <h6 class="fw-semibold mb-1 text-dark">{{ $course->title }}</h6>
                                <p class="small text-muted mb-2">Course</p>
                                <p class="fw-bold mb-0 text-primary">
                                    Rp{{ number_format(100000, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            @else
                <div class="col-12">
                    <p class="text-center text-muted">Belum ada kursus tersedia</p>
                </div>
            @endif
        </div>

        <div class="mt-4 text-center">
            <a href="{{ route('courses') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-right me-2"></i>Browse All Courses
            </a>
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
                    <div class="mb-3">
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                    </div>
                    <p class="fst-italic small">
                        "Course ini sangat membantu dan materinya mudah dipahami. Instrukturnya juga sangat responsif."
                    </p>
                    <div class="d-flex align-items-center mt-3">
                        <div class="rounded-circle bg-primary text-white me-2" style="width:40px; height:40px; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <strong class="small d-block">Student {{ $t }}</strong>
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
<section class="py-5 bg-light">
    <div class="container">
        <h4 class="fw-bold mb-4">Frequently Asked Question (FAQ)</h4>

        <div class="accordion" id="faqAccordion">
            @for($i=1;$i<=4;$i++)
            <div class="accordion-item mb-3">
                <h2 class="accordion-header">
                    <button class="accordion-button {{ $i != 1 ? 'collapsed' : '' }}"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#faq{{ $i }}">
                        @if($i==1)
                            Bagaimana cara mendaftar dan memulai belajar?
                        @elseif($i==2)
                            Apakah ada sertifikat setelah menyelesaikan kursus?
                        @elseif($i==3)
                            Berapa lama akses ke materi kursus?
                        @else
                            Bagaimana jika saya kesulitan dengan materi?
                        @endif
                    </button>
                </h2>
                <div id="faq{{ $i }}"
                     class="accordion-collapse collapse {{ $i == 1 ? 'show' : '' }}"
                     data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        @if($i==1)
                            Silakan klik tombol "Get Started", buat akun, dan pilih kursus yang ingin Anda ikuti. Anda bisa langsung mulai belajar setelah mendaftar.
                        @elseif($i==2)
                            Ya, kami menyediakan sertifikat digital setelah Anda menyelesaikan semua materi dan kuis di kursus.
                        @elseif($i==3)
                            Akses ke materi kursus berlaku seumur hidup. Anda bisa belajar kapan saja sesuai kecepatan Anda sendiri.
                        @else
                            Anda bisa menghubungi tim support kami melalui chat atau email. Instruktur juga siap membantu menjawab pertanyaan Anda.
                        @endif
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</section>

<!-- CTA Section -->
@guest
<section class="py-5" style="background:#1f7ae0">
    <div class="container text-center text-white">
        <h2 class="fw-bold mb-3">Ready to start learning?</h2>
        <p class="mb-4">Join thousands of students learning new skills today</p>
        <a href="{{ route('register') }}" class="btn btn-light btn-lg">
            <i class="fas fa-user-plus me-2"></i>Register Now
        </a>
    </div>
</section>
@endguest

@endsection