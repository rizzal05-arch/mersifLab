<!-- filepath: resources/views/admin/auth/login.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - MersifLab</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            /* Gradient background sesuai request */
            background: linear-gradient(-120deg, #D9D9D9 0%, #203B72 100%);
            min-height: 100vh;
        }
        
        /* Animasi floating untuk gambar */
        .float-img {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        
        .btn-continue {
            background-color: #1A76D1;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .btn-continue:hover {
            background-color: #155ab5;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(26, 118, 209, 0.4);
        }
        
        .input-field {
            border: 1px solid #D1D5DB;
            background-color: #ffffff; /* Pastikan background input putih */
            border-radius: 8px;
            padding: 12px 12px 12px 45px; /* Left padding for icon */
            transition: all 0.3s ease;
            font-size: 14px;
        }
        
        .input-field:focus {
            outline: none;
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .icon-input {
            color: #9CA3AF;
            z-index: 10;
        }
        
        .toggle-password {
            cursor: pointer;
            color: #9CA3AF;
            transition: color 0.3s ease;
            z-index: 10;
        }
        
        .toggle-password:hover {
            color: #1A76D1;
        }

        /* Modal Popup Styling */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 50;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background-color: white;
            border-radius: 12px;
            padding: 32px;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            animation: slideIn 0.3s ease;
            position: relative;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            color: #1F2937;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            color: #6B7280;
            cursor: pointer;
            padding: 0;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s ease;
        }

        .modal-close:hover {
            color: #1F2937;
        }

        .modal-body {
            color: #4B5563;
            line-height: 1.6;
            text-align: center;
        }

        .modal-body a {
            color: #1A76D1;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .modal-body a:hover {
            color: #155ab5;
            text-decoration: underline;
        }
    </style>
</head>
<body class="flex items-center justify-center p-6">
    
    <div class="w-full max-w-7xl grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
            
        <div class="hidden md:flex justify-center items-center">
            <img src="{{ asset('images/adminlogin.png') }}" 
                 alt="Login Illustration" 
                 class="w-full max-w-lg object-contain float-img drop-shadow-xl">
        </div>
        
        <div class="w-full max-w-md mx-auto">
            
            <div class="flex justify-end mb-8">
                <img src="{{ $siteLogoUrl ?? asset('images/logo.png') }}" alt="Logo" class="h-10" onerror="this.src='{{ asset('images/logo.png') }}'">
            </div>
            
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                    Admin Login
                </h1>
                <p class="text-gray-600">Please enter your credentials to log in</p>
            </div>
            
            <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-5">
                @csrf
                
                <!-- Tampilkan pesan error dari session -->
                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Email Field -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="fas fa-envelope icon-input absolute left-3 top-1/2 transform -translate-y-1/2"></i>
                        <input 
                            type="email" 
                            id="username" 
                            name="username" 
                            class="w-full input-field @error('username') border-red-500 @enderror" 
                            placeholder="admin@example.com"
                            value="{{ old('username') }}"
                            required
                        >
                    </div>
                    @error('username')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="fas fa-key icon-input absolute left-3 top-1/2 transform -translate-y-1/2"></i>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="w-full input-field pr-10 @error('password') border-red-500 @enderror" 
                            placeholder="••••••••"
                            required
                        >
                        <i class="fas fa-eye toggle-password absolute right-3 top-1/2 transform -translate-y-1/2 cursor-pointer" id="togglePassword"></i>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember & Forgot Password -->
                <div class="flex items-center justify-between pt-2">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="remember" value="1" class="w-4 h-4 rounded border-gray-300 focus:ring-blue-500 accent-blue-500" {{ old('remember') ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-600">Remember Me</span>
                    </label>
                    <button type="button" id="forgetPasswordBtn" class="text-red-500 font-semibold text-sm hover:underline">
                        Forget Password?
                    </button>
                </div>

                <!-- Continue Button -->
                <button 
                    type="submit" 
                    class="btn-continue w-full py-3 text-white font-medium text-center mt-8"
                >
                    Login
                </button>

            </form>
            
        </div>
            
    </div>

    <!-- Modal Popup -->
    <div class="modal-overlay" id="forgetPasswordModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Administrator Notice</h2>
                <button class="modal-close" id="closeModalBtn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>Only administrators can log in. Contact the administrator to request access.</p>
                <p class="mt-4">
                    <a href="#" class="text-blue-600 font-bold hover:underline">Contact Here</a>
                </p>
            </div>
        </div>
    </div>
    
    <script>
        const togglePasswordBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const forgetPasswordBtn = document.getElementById('forgetPasswordBtn');
        const forgetPasswordModal = document.getElementById('forgetPasswordModal');
        const closeModalBtn = document.getElementById('closeModalBtn');
        
        togglePasswordBtn.addEventListener('click', function() {
            // Toggle input type
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle icon class
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        // Open modal when clicking "Forget Password?"
        forgetPasswordBtn.addEventListener('click', function(e) {
            e.preventDefault();
            forgetPasswordModal.classList.add('active');
        });

        // Close modal saat klik tombol close
        closeModalBtn.addEventListener('click', function() {
            forgetPasswordModal.classList.remove('active');
        });

        // Close modal when clicking on overlay (outside modal content)
        forgetPasswordModal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    </script>
    
    <script>
        // Prevent back button after logout
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                // Page was loaded from cache (back button)
                window.location.reload();
            }
        });
        
        // Clear history state to prevent back navigation
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>