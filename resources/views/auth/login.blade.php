@extends('layouts.app')

@section('title', 'Login Page')

@section('content')
<section class="auth-section py-5">
    <div class="container">
        <div class="row align-items-center">
            <!-- Left Side - Illustration -->
            <div class="col-lg-6 d-none d-lg-block">
                <div class="auth-illustration">
                    <img src="{{ asset('images/illustration.png') }}" alt="Login Illustration" class="img-fluid">
                </div>
            </div>
            
            <!-- Right Side - Login Form -->
            <div class="col-lg-6">
                <div class="auth-card">
                    <h2 class="auth-title text-center mb-4">Log in to continue exploring your courses</h2>
                    
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
                        <!-- Student Login -->
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
                            
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif
                            
                            <form action="{{ route('login.post') }}" method="POST" id="loginForm">
                                @csrf
                                <input type="hidden" name="role" value="student">
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               placeholder="ptreka@gmail.com" value="{{ old('email') }}" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" id="password" name="password" 
                                               placeholder="Enter your password" required>
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">Remember me</label>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100 mb-3">Continue</button>
                                
                                <div class="divider mb-3">
                                    <span>Or log in with</span>
                                </div>
                                
                                <button type="button" class="btn btn-outline-secondary w-100 google-btn mb-3" onclick="window.location.href='{{ route('auth.google') }}'">
                                    <img src="https://www.google.com/favicon.ico" alt="Google" width="20" class="me-2">
                                    Log in with Google
                                </button>
                                
                                <p class="text-center mb-0">
                                    Don't Have an Account? <a href="{{ route('register') }}" class="text-primary fw-bold">Sign Up</a>
                                </p>
                            </form>
                        </div>
                        
                        <!-- Teacher Login -->
                        <div class="tab-pane fade" id="teacher" role="tabpanel">
                            <form action="{{ route('login.post') }}" method="POST">
                                @csrf
                                <input type="hidden" name="role" value="teacher">
                                
                                <div class="mb-3">
                                    <label for="teacher-email" class="form-label">Email<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" id="teacher-email" name="email" 
                                               placeholder="ptreka@gmail.com" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="teacher-password" class="form-label">Password<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" id="teacher-password" name="password" 
                                               placeholder="Enter your password" required>
                                        <button class="btn btn-outline-secondary" type="button" id="toggleTeacherPassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">Remember me</label>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100 mb-3">Continue</button>
                                
                                <div class="divider mb-3">
                                    <span>Or log in with</span>
                                </div>
                                
                                <button type="button" class="btn btn-outline-secondary w-100 google-btn mb-3" onclick="window.location.href='{{ route('auth.google') }}'">
                                    <img src="https://www.google.com/favicon.ico" alt="Google" width="20" class="me-2">
                                    Log in with Google
                                </button>

                                <p class="text-center mb-0">
                                    Don't Have an Account? <a href="{{ route('register') }}" class="text-primary fw-bold">Sign Up</a>
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
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const password = document.getElementById('password');
        const icon = this.querySelector('i');
        
        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            password.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
    
    // Toggle teacher password visibility
    document.getElementById('toggleTeacherPassword')?.addEventListener('click', function() {
        const password = document.getElementById('teacher-password');
        const icon = this.querySelector('i');
        
        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            password.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
</script>
@endsection