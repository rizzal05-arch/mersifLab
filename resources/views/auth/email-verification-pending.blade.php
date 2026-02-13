@extends('layouts.app')

@section('title', 'Email Verification')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/verify.css') }}">
@endsection

@section('content')
<section class="verification-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">
                <div class="verification-card">
                    <!-- Icon -->
                    <div class="verification-icon">
                        <i class="fas fa-envelope-circle-check"></i>
                    </div>
                    
                    <!-- Title -->
                    <h2 class="verification-title">Check Your Email</h2>
                    
                    <!-- Subtitle -->
                    <p class="verification-subtitle">
                        We have sent a verification link to<br>
                        <strong>{{ session('email', 'email@gmail.com') }}</strong>
                    </p>
                    
                    <!-- Error Alert -->
                    @if($errors->any())
                        <div class="alert-custom alert-danger-custom">
                            <i class="fas fa-exclamation-circle"></i>
                            <div class="flex-grow-1">
                                <ul class="alert-list">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <!-- Success Alert -->
                    @if(session('success'))
                        <div class="alert-custom alert-success-custom">
                            <i class="fas fa-check-circle"></i>
                            <div class="flex-grow-1">
                                {{ session('success') }}
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <!-- Error Alert (session) -->
                    @if(session('error'))
                        <div class="alert-custom alert-danger-custom">
                            <i class="fas fa-exclamation-circle"></i>
                            <div class="flex-grow-1">
                                {{ session('error') }}
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <!-- Instructions -->
                    <div class="instructions-box">
                        <div class="box-title">
                            <i class="fas fa-info-circle"></i>
                            <span>Next Steps:</span>
                        </div>
                        <ol class="instructions-list">
                            <li>Open your email inbox</li>
                            <li>Look for an email from MersifLab</li>
                            <li>Click the "Verify Email" button</li>
                            <li>Your account will be activated immediately</li>
                        </ol>
                    </div>
                    
                    <!-- Divider -->
                    <div class="divider-custom"></div>
                    
                    <!-- Help Section -->
                    <div class="help-section">
                        <p>Haven't received the email?</p>
                        
                        <!-- Resend Form -->
                        <form action="{{ route('verify.resend') }}" method="POST">
                            @csrf
                            <input type="hidden" name="email" value="{{ session('email') }}">
                            <button type="submit" class="btn-resend">
                                <i class="fas fa-redo"></i>
                                <span>Resend Verification Email</span>
                            </button>
                        </form>
                    </div>
                    
                    <!-- Text Divider -->
                    <div class="text-divider">
                        <span>atau</span>
                    </div>
                    
                    <!-- Back Link -->
                    <div class="back-link">
                        <p>
                            <a href="{{ route('login') }}">
                                <i class="fas fa-arrow-left me-1"></i>
                                Back to Login
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection