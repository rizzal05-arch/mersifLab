@extends('layouts.app')

@section('title', 'Register Page')

@section('content')
<section class="auth-section py-5">
    <div class="container">
        <div class="row align-items-center">
            <!-- Left Side - Illustration -->
            <div class="col-lg-6 d-none d-lg-block">
                <div class="auth-illustration">
                    <img src="{{ asset('images/illustration.png') }}" alt="Register Illustration" class="img-fluid">
                </div>
            </div>
            
            <!-- Right Side - Register Form -->
            <div class="col-lg-6">
                <div class="auth-card">
                    <h2 class="auth-title text-center mb-4">Sign up with email</h2>
                    
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs auth-tabs mb-4" role="tablist">
                        <li class="nav-item flex-fill" role="presentation">
                            <button class="nav-link active w-100" id="student-tab" data-bs-toggle="tab" data-bs-target="#student" type="button" role="tab">
                                Student
                            </button>
                        </li>
                        <li class="nav-item flex-fill" role="presentation">
                            <button class="nav-link w-100" id="teacher-tab" data-bs-toggle="tab" data-bs-target="#teacher" type="button" role="tab">
                                Teacher
                            </button>
                        </li>
                    </ul>
                    
                    <!-- Tab Content -->
                    <div class="tab-content">
                        <!-- Student Register -->
                        <div class="tab-pane fade show active" id="student" role="tabpanel">
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
                            
                            <form action="{{ route('register.post') }}" method="POST" id="registerForm">
                                @csrf
                                <input type="hidden" name="role" value="student">
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               placeholder="Enter your full name" value="{{ old('name') }}" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               placeholder="student@gmail.com" value="{{ old('email') }}" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" id="password" name="password" 
                                               placeholder="Create a password" required minlength="8">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">Minimum 8 characters</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm Password<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" 
                                               placeholder="Confirm your password" required minlength="8">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                                    <label class="form-check-label" for="terms">
                                        I agree to the <a href="{{ url('/syarat-ketentuan') }}" target="_blank">Terms & Conditions</a> and <a href="{{ url('/privacy-policy') }}" target="_blank">Privacy Policy</a>
                                    </label>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100 mb-3">Continue</button>
                                
                                <div class="divider mb-3">
                                    <span>Or sign up with</span>
                                </div>
                                
                                <button type="button" class="btn btn-outline-secondary w-100 google-btn mb-3" onclick="window.location.href='{{ route('auth.google', ['role' => 'student']) }}'">
                                    <img src="https://www.google.com/favicon.ico" alt="Google" width="20" class="me-2">
                                    Sign up with Google
                                </button>
                                
                                <p class="text-center mb-0">
                                    Already Have an Account? <a href="{{ route('login') }}" class="text-primary fw-bold">Log In</a>
                                </p>
                            </form>
                        </div>
                        
                        <!-- Teacher Register -->
                        <div class="tab-pane fade" id="teacher" role="tabpanel">
                            <form action="{{ route('register.post') }}" method="POST">
                                @csrf
                                <input type="hidden" name="role" value="teacher">
                                
                                <div class="mb-3">
                                    <label for="teacher-name" class="form-label">Full Name<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control" id="teacher-name" name="name" 
                                               placeholder="Enter your full name" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="teacher-email" class="form-label">Email<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" id="teacher-email" name="email" 
                                               placeholder="teacher@gmail.com" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="teacher-password" class="form-label">Password<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" id="teacher-password" name="password" 
                                               placeholder="Create a password" required minlength="8">
                                        <button class="btn btn-outline-secondary" type="button" id="toggleTeacherPassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">Minimum 8 characters</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="teacher-password-confirm" class="form-label">Confirm Password<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" id="teacher-password-confirm" name="password_confirmation" 
                                               placeholder="Confirm your password" required minlength="8">
                                        <button class="btn btn-outline-secondary" type="button" id="toggleTeacherPasswordConfirm">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                                    <label class="form-check-label" for="terms">
                                        I agree to the <a href="{{ url('/syarat-ketentuan') }}" target="_blank">Terms & Conditions</a> and <a href="{{ url('/privacy-policy') }}" target="_blank">Privacy Policy</a>
                                    </label>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100 mb-3">Continue</button>
                                
                                <div class="divider mb-3">
                                    <span>Or sign up with</span>
                                </div>
                                
                                <button type="button" class="btn btn-outline-secondary w-100 google-btn mb-3" onclick="window.location.href='{{ route('auth.google', ['role' => 'teacher']) }}'">
                                    <img src="https://www.google.com/favicon.ico" alt="Google" width="20" class="me-2">
                                    Sign up with Google
                                </button>

                                <p class="text-center mb-0">
                                    Already Have an Account? <a href="{{ route('login') }}" class="text-primary fw-bold">Log In</a>
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    // Toggle password visibility for all password fields
    const toggleButtons = [
        { btnId: 'togglePassword', inputId: 'password' },
        { btnId: 'togglePasswordConfirm', inputId: 'password_confirmation' },
        { btnId: 'toggleTeacherPassword', inputId: 'teacher-password' },
        { btnId: 'toggleTeacherPasswordConfirm', inputId: 'teacher-password-confirm' }
    ];
    
    toggleButtons.forEach(({ btnId, inputId }) => {
        const btn = document.getElementById(btnId);
        if (btn) {
            btn.addEventListener('click', function() {
                const input = document.getElementById(inputId);
                const icon = this.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        }
    });
    
    // Password confirmation validation
    const form = document.getElementById('registerForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirmation').value;
            
            if (password !== passwordConfirm) {
                e.preventDefault();
                alert('Password and Confirm Password do not match!');
            }
        });
    }
</script>
@endsection