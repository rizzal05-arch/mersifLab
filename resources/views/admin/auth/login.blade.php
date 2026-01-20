<!-- filepath: resources/views/admin/auth/login.blade.php -->
<!DOCTYPE html>
<html lang="id">
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
            padding: 12px 12px 12px 45px; /* Padding kiri disesuaikan untuk ikon */
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
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-10">
            </div>
            
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                    Admin Login
                </h1>
                <p class="text-gray-600">Masukkan kredensial Anda untuk melanjutkan</p>
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
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 focus:ring-blue-500 accent-blue-500">
                        <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                    </label>
                    <a href="#" class="text-red-500 font-semibold text-sm hover:underline">
                        Lupa Password?
                    </a>
                </div>

                <!-- Continue Button -->
                <button 
                    type="submit" 
                    class="btn-continue w-full py-3 text-white font-medium text-center mt-8"
                >
                    Lanjutkan
                </button>

            </form>
            
            <p class="text-center text-gray-600 text-sm mt-8">
                Belum punya akun? <a href="#" class="text-blue-600 font-bold hover:underline">Daftar di sini</a>
            </p>
            
        </div>
            
    </div>
    
    <script>
        const togglePasswordBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        
        togglePasswordBtn.addEventListener('click', function() {
            // Toggle tipe input
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle icon class
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
    
</body>
</html>