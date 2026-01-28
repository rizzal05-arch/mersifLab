<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'REKA LMS') - MersifLab</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ ($siteFaviconUrl ?? asset('images/favicon.png')) . '?v=' . time() }}">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <!-- AI Assistant CSS -->
    <link rel="stylesheet" href="{{ asset('css/ai-assistant.css') }}">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    @yield('styles')
</head>
<body>
    @include('layouts.header')
    
    <main>
        @yield('content')
    </main>
    
    @include('layouts.footer')
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom JS -->
    <script src="{{ asset('js/custom.js') }}"></script>
    
    <!-- AI Assistant JS -->
    <script src="{{ asset('js/ai-assistant.js') }}"></script>
    
    <!-- Global Error Handler -->
    <script>
        // Show error popup for module approval errors
        @if(session('error') && str_contains(session('error'), 'belum disetujui'))
            Swal.fire({
                icon: 'warning',
                title: 'Akses Dibatasi',
                html: `
                    <div style="text-align: center;">
                        <i class="fas fa-lock" style="font-size: 3rem; color: #ffc107; margin-bottom: 1rem;"></i>
                        <p style="font-size: 1.1rem; margin-bottom: 0.5rem; color: #333;">{{ session("error") }}</p>
                    </div>
                `,
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#667eea',
                allowOutsideClick: false,
                allowEscapeKey: true,
                customClass: {
                    popup: 'animated-popup',
                    backdrop: 'swal2-backdrop-smooth'
                },
                showClass: {
                    popup: 'swal2-show-smooth',
                    backdrop: 'swal2-backdrop-show-smooth'
                },
                hideClass: {
                    popup: 'swal2-hide-smooth',
                    backdrop: 'swal2-backdrop-hide-smooth'
                }
            });
        @elseif(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ session("error") }}',
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc3545',
                customClass: {
                    popup: 'animated-popup',
                    backdrop: 'swal2-backdrop-smooth'
                },
                showClass: {
                    popup: 'swal2-show-smooth',
                    backdrop: 'swal2-backdrop-show-smooth'
                },
                hideClass: {
                    popup: 'swal2-hide-smooth',
                    backdrop: 'swal2-backdrop-hide-smooth'
                }
            });
        @endif
    </script>
    
    <style>
        /* Smooth animations for popup */
        .animated-popup {
            animation: slideInDown 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        
        @keyframes slideInDown {
            from {
                transform: translateY(-30px) scale(0.95);
                opacity: 0;
            }
            to {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }
        
        /* Smooth backdrop show animation */
        .swal2-backdrop-show-smooth {
            animation: fadeInBackdrop 0.25s ease-out !important;
        }
        
        /* Smooth backdrop hide animation */
        .swal2-backdrop-hide-smooth {
            animation: fadeOutBackdrop 0.3s ease-out !important;
        }
        
        @keyframes fadeInBackdrop {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        @keyframes fadeOutBackdrop {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
            }
        }
        
        /* Smooth popup show animation */
        .swal2-show-smooth {
            animation: slideInDownSmooth 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
        }
        
        @keyframes slideInDownSmooth {
            from {
                transform: translateY(-30px) scale(0.95);
                opacity: 0;
            }
            to {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }
        
        /* Smooth popup hide animation */
        .swal2-hide-smooth {
            animation: fadeOutUpSmooth 0.3s cubic-bezier(0.55, 0.055, 0.675, 0.19) !important;
        }
        
        @keyframes fadeOutUpSmooth {
            from {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
            to {
                transform: translateY(-15px) scale(0.96);
                opacity: 0;
            }
        }
        
        /* Override default SweetAlert animations for smoother transitions */
        .swal2-popup.swal2-hide {
            animation: fadeOutUpSmooth 0.3s cubic-bezier(0.55, 0.055, 0.675, 0.19) !important;
        }
        
        /* Ensure smooth container transitions */
        .swal2-container {
            transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        
        .swal2-container.swal2-backdrop-show {
            animation: fadeInBackdrop 0.25s ease-out !important;
        }
        
        .swal2-container.swal2-backdrop-hide {
            animation: fadeOutBackdrop 0.3s ease-out !important;
        }
    </style>
    
    @yield('scripts')
</body>
</html>