<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - REKA</title>
    
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #E2E8F0 0%, #8FAACC 100%);
            overflow-x: hidden;
        }

        .sidebar {
            width: 250px;
            flex-shrink: 0;
            background: linear-gradient(180deg, #FFFFFF 0%, #F0F2F5 100%);
            border-right: 1px solid #e0e0e0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            position: fixed;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
            box-shadow: 2px 0 5px rgba(0,0,0,0.05);
        }

        .sidebar.minimized {
            width: 80px;
            align-items: center;
        }

        .sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            position: relative;
        }

        .sidebar.minimized .sidebar-header {
            justify-content: center;
        }

        .sidebar-logo {
            text-align: center;
            flex-grow: 1;
            padding-left: 20px; /* Offset for toggler */
        }

        .sidebar.minimized .sidebar-logo {
            padding-left: 0;
        }

        .sidebar-logo img {
            max-width: 120px;
            height: auto;
            transition: all 0.3s ease;
        }

        .sidebar.minimized .sidebar-logo img {
            max-width: 40px;
        }

        .sidebar-toggler {
            background: none;
            border: none;
            font-size: 20px;
            color: #64748b;
            cursor: pointer;
            padding: 5px;
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
        }

        .sidebar.minimized .sidebar-toggler {
            right: 15px;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li {
            margin-bottom: 10px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 15px;
            border-radius: 10px;
            color: #475569;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: #e0f2fe;
            color: #3b82f6;
        }

        .sidebar.minimized .sidebar-menu a {
            justify-content: center;
            padding: 12px;
        }

        .sidebar.minimized .sidebar-menu a span {
            display: none;
        }

        .sidebar.minimized .sidebar-menu a {
            gap: 0;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }

        .sidebar.minimized + .main-content {
            margin-left: 80px;
        }

        .topbar {
            background: transparent;
            box-shadow: none;
            position: sticky;
            top: 0;
            z-index: 900;
            padding: 15px 0px 15px 25px; /* Top: 15px, Right: 25px, Left: 0px, Bottom: 15px */
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
            width: 100%;
        }

        .topbar.scrolled {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px 25px;
            position: fixed;
            left: 250px; /* Default untuk sidebar normal */
            right: 0;
            width: calc(100vw - 250px); /* Default untuk sidebar normal */
            z-index: 1001;
            transition: all 0.3s ease;
        }

        /* Responsive untuk sidebar minimized */
        .sidebar.minimized ~ .main-content .topbar.scrolled {
            left: 80px; /* Menempel ke sidebar minimized */
            width: calc(100vw - 80px); /* Lebar area untuk sidebar minimized */
        }

        /* Sembunyikan logo saat sidebar minimized */
        .sidebar.minimized .sidebar-logo {
            display: none;
        }

        /* Tampilkan hanya hamburger saat minimized */
        .sidebar.minimized .sidebar-header {
            justify-content: center;
            padding: 10px 0;
        }

        .topbar-search {
            flex: 1;
            max-width: 350px;
        }

        .topbar-search input {
            width: 100%;
            padding: 10px 15px;
            border: none;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 8px;
            font-size: 13px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .topbar.scrolled .topbar-search input {
            background: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .topbar-icon-btn {
            background: rgba(255, 255, 255, 0.5);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            color: #64748b;
            font-size: 18px;
            position: relative;
            transition: all 0.3s;
            text-decoration: none;
        }

        .topbar-icon-btn:hover {
            background: white;
            color: #2F80ED;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .topbar.scrolled .topbar-icon-btn {
            background: rgba(0, 0, 0, 0.1);
            color: #475569;
        }

        .topbar.scrolled .topbar-icon-btn:hover {
            background: white;
            color: #2F80ED;
        }

        .notification-dot {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 8px;
            height: 8px;
            background: #EB5757;
            border-radius: 50%;
            border: 1px solid white;
        }

        .user-dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 30px;
            background: rgba(255, 255, 255, 0.5);
            transition: background 0.3s;
        }

        .user-dropdown-toggle:hover {
            background: rgba(255, 255, 255, 0.9);
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .user-info {
            text-align: right;
        }

        .user-name {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            line-height: 1.2;
        }

        .user-role {
            font-size: 11px;
            color: #64748b;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 10px;
            margin-top: 10px !important;
        }

        .dropdown-item {
            border-radius: 8px;
            padding: 8px 15px;
            font-size: 14px;
            color: #475569;
        }

        .dropdown-item:hover {
            background-color: #F1F5F9;
            color: #2F80ED;
        }

        .dropdown-item.text-danger:hover {
            background-color: #FEF2F2;
            color: #DC2626;
        }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .topbar-user img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .page-title {
            margin-bottom: 25px;
        }

        .page-title h1 {
            font-size: 28px;
            font-weight: 700;
            color: #333333;
            margin: 0;
        }

        .page-title p {
            color: #828282;
            margin: 5px 0 0 0;
            font-size: 14px;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        }

        .stat-card-icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .stat-card-label {
            font-size: 13px;
            color: #828282;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .stat-card-value {
            font-size: 28px;
            font-weight: 700;
            color: #333333;
        }

        .stat-card-change {
            font-size: 12px;
            margin-top: 10px;
            font-weight: 600;
        }

        .stat-card-change.positive {
            color: #27AE60;
        }

        .stat-card-change.negative {
            color: #EB5757;
        }

        .icon-teacher {
            background: #2F80ED;
            color: white;
        }

        .icon-student {
            background: #27AE60;
            color: white;
        }

        .icon-course {
            background: #EB5757;
            color: white;
        }

        .card-content {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 25px;
        }

        .card-content-title {
            font-size: 18px;
            font-weight: 700;
            color: #333333;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                transition: width 0.3s ease;
            }

            .sidebar.show {
                width: 200px;
            }

            .main-content {
                margin-left: 0;
            }

            .topbar {
                flex-direction: column;
                gap: 15px;
            }

            .topbar-search {
                max-width: 100%;
            }
        }
    </style>

    @yield('styles')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <img src="{{ asset('images/logo.png') }}" alt="REKA MERSIF Logo">
            </div>
            <button class="sidebar-toggler" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="@if(request()->routeIs('admin.dashboard')) active @endif">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.courses.index') }}" class="@if(request()->routeIs('admin.courses*')) active @endif">
                    <i class="fas fa-book"></i>
                    <span>Course</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.teachers.index') }}" class="@if(request()->routeIs('admin.teachers*')) active @endif">
                    <i class="fas fa-chalkboard-user"></i>
                    <span>Teachers</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.students.index') }}" class="@if(request()->routeIs('admin.students*')) active @endif">
                    <i class="fas fa-users"></i>
                    <span>Students</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.admins.index') }}" class="@if(request()->routeIs('admin.admins*')) active @endif">
                    <i class="fas fa-user-shield"></i>
                    <span>Admin Management</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.settings.index') }}" class="@if(request()->routeIs('admin.settings*')) active @endif">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <div class="topbar-search">
                <input type="text" placeholder="Search...">
            </div>
            <div class="topbar-right">
                <a href="#" class="topbar-icon-btn">
                    <i class="fas fa-comment-dots"></i>
                </a>

                <a href="#" class="topbar-icon-btn">
                    <i class="fas fa-bell"></i>
                    <span class="notification-dot"></span>
                </a>

                <div class="dropdown">
                    <a href="#" class="user-dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-info d-none d-md-block">
                            <span class="user-name">{{ auth()->user()->name ?? 'Admin User' }}</span>
                            <span class="user-role">Administrator</span>
                        </div>
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name ?? 'Admin' }}&background=2F80ED&color=fff" alt="User" class="user-avatar">
                    </a>
                    
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-user me-2"></i> My Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-cog me-2"></i> Account Settings
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-bell me-2"></i> Notifications
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-shield-alt me-2"></i> Security
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('minimized');
        }

        // Scroll detection for topbar
        let lastScrollTop = 0;
        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const topbar = document.querySelector('.topbar');
            
            if (scrollTop > 50) {
                topbar.classList.add('scrolled');
            } else {
                topbar.classList.remove('scrolled');
            }
            
            lastScrollTop = scrollTop;
        });
    </script>
    
    @yield('scripts')
</body>
</html>