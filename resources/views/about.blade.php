@extends('layouts.app')

@section('title', 'About Us')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/about.css') }}">
@endsection

@section('content')
<div class="container">
    <!-- Hero Section -->
    <section class="about-hero">
        <h1>MersifLab</h1>
        <p>Empowering learners with accessible and high-quality education</p>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="row g-3">
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number">50,000+</div>
                    <div class="stat-label">Active Students</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="stat-number">100+</div>
                    <div class="stat-label">Total Courses</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Expert Instructors</div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-school"></i>
                    </div>
                    <div class="stat-number">20+</div>
                    <div class="stat-label">Schools Reached</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Story Section -->
    <section class="story-section">
        <div class="story-card">
            <h2>Our Story</h2>
            <p>
                MersifLab was founded in 2019 with a simple yet powerful vision: to make high-quality education 
                accessible to everyone, regardless of their location or background. What started as a small platform with 
                just 10 courses has grown into a global learning community serving over 50,000 students worldwide.
            </p>
            <p>
                Our founders, a team of educators and technology enthusiasts, recognized that traditional education 
                systems weren't meeting the needs of modern learners. They set out to create a platform that combines 
                expert instruction, cutting-edge technology, and flexible learning options to deliver an exceptional 
                educational experience.
            </p>
            <p class="mb-0">
                Today, MersifLab offers over 500 courses taught by 200+ expert instructors across various fields including 
                technology, business, design, and more. We're proud to have helped thousands of students achieve their 
                learning goals and advance their careers.
            </p>
        </div>
    </section>
</div>

<!-- Our Values Section -->
<section class="values-section">
    <div class="container">
        <h2>Our Values</h2>
        <div class="row g-3">
            <div class="col-lg-6">
                <div class="value-card">
                    <div class="value-icon mission">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h5>Mission-Driven</h5>
                    <p>Our mission is to make quality education accessible to everyone, everywhere.</p>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="value-card">
                    <div class="value-icon student">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h5>Student-Focused</h5>
                    <p>We put our students first, ensuring the best learning experience possible.</p>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="value-card">
                    <div class="value-icon innovation">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h5>Innovation</h5>
                    <p>We constantly innovate to bring you the latest in educational technology.</p>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="value-card">
                    <div class="value-icon growth">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h5>Growth</h5>
                    <p>We believe in continuous improvement for both students and instructors.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Meet Our Team Section -->
<section class="team-section">
    <div class="container">
        <h2>Meet Our Team</h2>
        <div class="row g-3">
            <div class="col-lg-3 col-md-6">
                <div class="team-card">
                    <div class="team-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <h5>Name</h5>
                    <p>CEO & Founder</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="team-card">
                    <div class="team-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <h5>Name</h5>
                    <p>Head of Content</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="team-card">
                    <div class="team-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <h5>Name</h5>
                    <p>Lead Designer</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="team-card">
                    <div class="team-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <h5>Name</h5>
                    <p>Head of Finance</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Send Message Section -->
<section class="contact-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="contact-card">
                    <h2>
                        <i class="fas fa-paper-plane"></i>
                        Send Message
                    </h2>

                    <form id="contactForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Name<span>*</span></label>
                                    <div class="input-icon">
                                        <i class="fas fa-user"></i>
                                        <input type="text" 
                                               class="form-control" 
                                               id="contactName"
                                               placeholder="Your Full Name"
                                               required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Email<span>*</span></label>
                                    <div class="input-icon">
                                        <i class="fas fa-envelope"></i>
                                        <input type="email" 
                                               class="form-control" 
                                               id="contactEmail"
                                               placeholder="contoh@gmail.com"
                                               required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Pesan<span>*</span></label>
                                    <textarea class="form-control" 
                                              id="contactMessage"
                                              placeholder="Write your message details here..."
                                              required></textarea>
                                </div>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn-send">
                                    Kirim Pesan
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const contactForm = document.getElementById('contactForm');
        
        if (contactForm) {
            contactForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = {
                    name: document.getElementById('contactName').value,
                    email: document.getElementById('contactEmail').value,
                    message: document.getElementById('contactMessage').value
                };
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                
                // Disable submit button
                const submitBtn = contactForm.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
                
                fetch('/messages', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw data;
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Tampilkan success alert
                        const alertHtml = `
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle"></i> ${data.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `;
                        contactForm.insertAdjacentHTML('beforebegin', alertHtml);
                        
                        // Reset form
                        contactForm.reset();
                    } else {
                        throw data;
                    }
                })
                .catch(error => {
                    let errorMsg = 'An error occurred. Please try again.';
                    
                    if (error.errors) {
                        // Handle validation errors
                        errorMsg = Object.values(error.errors).flat().join('<br>');
                    } else if (error.message) {
                        errorMsg = error.message;
                    }
                    
                    const alertHtml = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> ${errorMsg}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                    contactForm.insertAdjacentHTML('beforebegin', alertHtml);
                    console.error('Error:', error);
                })
                .finally(() => {
                    // Re-enable submit button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Kirim Pesan <i class="fas fa-arrow-right"></i>';
                });
            });
        }
    });
</script>
@endsection