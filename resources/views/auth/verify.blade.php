@extends('layouts.app')

@section('title', 'Verification Page')

@section('content')
<section class="auth-section py-5">
    <div class="container">
        <div class="row align-items-center">
            <!-- Left Side - Illustration -->
            <div class="col-lg-6 d-none d-lg-block">
                <div class="auth-illustration">
                    <img src="{{ asset('images/illustration.png') }}" alt="Verification Illustration" class="img-fluid">
                </div>
            </div>
            
            <!-- Right Side - Verification Form -->
            <div class="col-lg-6">
                <div class="auth-card">
                    <h2 class="auth-title text-center mb-4">Check your inbox</h2>
                    
                    <p class="text-center text-muted mb-4">
                        Enter the 6-digit code we sent to <strong>{{ session('email', 'contoh@gmail.com') }}</strong> to finish your sign up.
                    </p>
                    
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <form action="{{ route('verify.post') }}" method="POST" id="verifyForm">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="verification_code" class="form-label">Verification Code<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                                <input type="text" class="form-control" id="verification_code" name="verification_code" 
                                       placeholder="6-digit code" maxlength="6" pattern="[0-9]{6}" required 
                                       autocomplete="off">
                            </div>
                            <small class="text-muted">Enter the 6-digit verification code</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3">Continue</button>
                        
                        <div class="text-center">
                            <p class="text-muted mb-2">Didn't receive the code?</p>
                            <form action="{{ route('verify.resend') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-link text-primary fw-bold p-0" id="resendBtn">
                                    Resend code in <span id="countdown">15</span>s
                                </button>
                            </form>
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
    // Auto-focus on verification code input
    document.getElementById('verification_code').focus();
    
    // Only allow numbers in verification code
    document.getElementById('verification_code').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    
    // Countdown timer for resend button
    let countdown = 15;
    const resendBtn = document.getElementById('resendBtn');
    const countdownSpan = document.getElementById('countdown');
    
    // Disable resend button initially
    resendBtn.disabled = true;
    
    const timer = setInterval(function() {
        countdown--;
        countdownSpan.textContent = countdown;
        
        if (countdown <= 0) {
            clearInterval(timer);
            resendBtn.disabled = false;
            resendBtn.innerHTML = 'Resend code';
        }
    }, 1000);
    
    // Form validation
    document.getElementById('verifyForm').addEventListener('submit', function(e) {
        const code = document.getElementById('verification_code').value;
        
        if (code.length !== 6) {
            e.preventDefault();
            alert('Please enter a 6-digit verification code');
        }
    });
</script>
@endsection