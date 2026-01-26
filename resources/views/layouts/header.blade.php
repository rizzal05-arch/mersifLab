<header class="header-section">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">

            <!-- Brand-->
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ $siteLogoUrl ?? asset('images/logo.png') }}" alt="REKA Logo" height="50" onerror="this.src='{{ asset('images/logo.png') }}'">
            </a>

            <!-- Hamburger -->
            <button class="navbar-toggler custom-toggler" type="button"
                    data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false"
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigation -->
            <div class="collapse navbar-collapse" id="navbarNav">

                <!-- CENTER MENU -->
                <ul class="navbar-nav mx-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                            Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('courses*') ? 'active' : '' }}" href="{{ url('/courses') }}">
                            Course
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('about*') ? 'active' : '' }}" href="{{ url('/about') }}">
                            About
                        </a>
                    </li>
                </ul>

                <!-- RIGHT ACTION -->
                <ul class="navbar-nav align-items-center">

                    <!-- GUEST -->
                    @guest
                        <li class="nav-item me-2">
                            <a class="btn btn-outline-primary {{ Request::is('login') ? 'active' : '' }}"
                               href="{{ route('login') }}">
                                Log In
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-outline-primary {{ Request::is('register') ? 'active' : '' }}"
                               href="{{ route('register') }}">
                                Sign Up
                            </a>
                        </li>
                    @endguest

                    <!-- AUTH -->
                    @auth
                        <!-- Cart -->
                        <li class="nav-item me-3 position-relative">
                            <a class="nav-link icon-link" href="{{ route('cart') }}" style="color: #000;">
                                <i class="fas fa-shopping-cart" style="font-size: 1.2rem;"></i>
                                <span class="badge-notification cart-badge" id="headerCartCount" style="background: #2196f3; color: white; font-size: 0.7rem; padding: 2px 6px; min-width: 18px;">
                                    @php
                                        $cartCount = count(session('cart', []));
                                    @endphp
                                    @if($cartCount > 0)
                                        {{ $cartCount }}
                                    @else
                                        0
                                    @endif
                                </span>
                            </a>
                        </li>

                        <!-- Notification -->
                        @auth
                        @if(auth()->user()->isTeacher())
                        <li class="nav-item me-3 dropdown position-relative">
                            @php
                                $unreadCount = \App\Models\Notification::where('user_id', auth()->id())
                                    ->where('is_read', false)
                                    ->count();
                                $recentNotifications = \App\Models\Notification::where('user_id', auth()->id())
                                    ->orderBy('created_at', 'desc')
                                    ->limit(5)
                                    ->get();
                            @endphp
                            <a class="nav-link icon-link" href="#" id="notificationDropdown"
                               role="button" data-bs-toggle="dropdown" style="color: #000;">
                                <i class="fas fa-bell" style="font-size: 1.2rem;"></i>
                                @if($unreadCount > 0)
                                    <span class="badge-notification" style="background: #dc3545; color: white; font-size: 0.7rem; padding: 2px 6px; min-width: 18px; border-radius: 10px;">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                                @endif
                            </a>

                            <div class="dropdown-menu dropdown-menu-end notification-dropdown" style="min-width: 350px; max-height: 400px; overflow-y: auto;">
                                <div class="notification-header d-flex justify-content-between align-items-center p-3 border-bottom">
                                    <h6 class="mb-0 fw-bold">Notifications</h6>
                                    <a href="{{ route('notifications') }}" class="text-primary small">View all</a>
                                </div>

                                <div class="notification-body">
                                    @if($recentNotifications->count() > 0)
                                        @foreach($recentNotifications as $notif)
                                            <a href="{{ route('notifications') }}" class="notification-item {{ !$notif->is_read ? 'unread' : '' }} d-block p-3 border-bottom text-decoration-none" style="color: inherit;">
                                                <div class="d-flex align-items-start">
                                                    <div class="me-2">
                                                        @if($notif->type === 'module_approved')
                                                            <i class="fas fa-check-circle text-success"></i>
                                                        @elseif($notif->type === 'student_enrolled')
                                                            <i class="fas fa-user-plus text-success"></i>
                                                        @elseif($notif->type === 'course_rated')
                                                            <i class="fas fa-star text-warning"></i>
                                                        @elseif($notif->type === 'course_completed')
                                                            <i class="fas fa-trophy text-primary"></i>
                                                        @else
                                                            <i class="fas fa-bell text-info"></i>
                                                        @endif
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <p class="notification-text mb-1 fw-semibold" style="font-size: 0.9rem;">{{ $notif->title }}</p>
                                                        <p class="notification-text mb-1" style="font-size: 0.85rem; color: #666;">{{ Str::limit($notif->message, 60) }}</p>
                                                        <span class="notification-time" style="font-size: 0.75rem; color: #999;">{{ $notif->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    @if(!$notif->is_read)
                                                        <span class="badge bg-warning ms-2" style="font-size: 0.6rem;">New</span>
                                                    @endif
                                                </div>
                                            </a>
                                        @endforeach
                                    @else
                                        <div class="p-3 text-center text-muted">
                                            <i class="fas fa-bell-slash mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                                            <p class="mb-0 small">No notifications</p>
                                        </div>
                                    @endif
                                </div>

                                @if($recentNotifications->count() > 0)
                                <div class="notification-footer text-center p-2 border-top">
                                    <a href="{{ route('notifications') }}" class="text-primary small">View all notifications</a>
                                </div>
                                @endif
                            </div>
                        </li>
                        @elseif(auth()->user()->isStudent() || (!auth()->user()->isTeacher() && !auth()->user()->isAdmin()))
                        <li class="nav-item me-3 dropdown position-relative">
                            @php
                                $studentUnreadCount = \App\Models\Notification::where('user_id', auth()->id())
                                    ->where('is_read', false)
                                    ->count();
                                $studentRecentNotifications = \App\Models\Notification::where('user_id', auth()->id())
                                    ->orderBy('created_at', 'desc')
                                    ->limit(5)
                                    ->get();
                            @endphp
                            <a class="nav-link icon-link" href="#" id="studentNotificationDropdown"
                               role="button" data-bs-toggle="dropdown" style="color: #000;">
                                <i class="fas fa-bell" style="font-size: 1.2rem;"></i>
                                @if($studentUnreadCount > 0)
                                    <span class="badge-notification" style="background: #dc3545; color: white; font-size: 0.7rem; padding: 2px 6px; min-width: 18px; border-radius: 10px;">{{ $studentUnreadCount > 99 ? '99+' : $studentUnreadCount }}</span>
                                @endif
                            </a>

                            <div class="dropdown-menu dropdown-menu-end notification-dropdown" style="min-width: 350px; max-height: 400px; overflow-y: auto;">
                                <div class="notification-header d-flex justify-content-between align-items-center p-3 border-bottom">
                                    <h6 class="mb-0 fw-bold">Notifications</h6>
                                    <a href="{{ route('notifications') }}" class="text-primary small">View all</a>
                                </div>

                                <div class="notification-body">
                                    @if($studentRecentNotifications->count() > 0)
                                        @foreach($studentRecentNotifications as $notif)
                                            <a href="{{ route('notifications') }}" class="notification-item {{ !$notif->is_read ? 'unread' : '' }} d-block p-3 border-bottom text-decoration-none" style="color: inherit;">
                                                <div class="d-flex align-items-start">
                                                    <div class="me-2">
                                                        @if($notif->type === 'new_course')
                                                            <i class="fas fa-book-open text-primary"></i>
                                                        @elseif($notif->type === 'new_chapter')
                                                            <i class="fas fa-layer-group text-success"></i>
                                                        @elseif($notif->type === 'new_module')
                                                            <i class="fas fa-file-alt text-info"></i>
                                                        @elseif($notif->type === 'announcement')
                                                            <i class="fas fa-bullhorn text-warning"></i>
                                                        @else
                                                            <i class="fas fa-bell text-info"></i>
                                                        @endif
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <p class="notification-text mb-1 fw-semibold" style="font-size: 0.9rem;">{{ $notif->title }}</p>
                                                        <p class="notification-text mb-1" style="font-size: 0.85rem; color: #666;">{{ Str::limit($notif->message, 60) }}</p>
                                                        <span class="notification-time" style="font-size: 0.75rem; color: #999;">{{ $notif->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    @if(!$notif->is_read)
                                                        <span class="badge bg-warning ms-2" style="font-size: 0.6rem;">New</span>
                                                    @endif
                                                </div>
                                            </a>
                                        @endforeach
                                    @else
                                        <div class="p-3 text-center text-muted">
                                            <i class="fas fa-bell-slash mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                                            <p class="mb-0 small">No notifications</p>
                                        </div>
                                    @endif
                                </div>

                                @if($studentRecentNotifications->count() > 0)
                                <div class="notification-footer text-center p-2 border-top">
                                    <a href="{{ route('notifications') }}" class="text-primary small">View all notifications</a>
                                </div>
                                @endif
                            </div>
                        </li>
                        @endif
                        @endauth

                        <!-- User Profile -->
                        <li class="nav-item">
                            @php
                                $user = Auth::user();
                                $profileRoute = 'profile';
                                if ($user) {
                                    if ($user->isTeacher()) {
                                        $profileRoute = 'teacher.profile';
                                    } elseif ($user->isStudent()) {
                                        $profileRoute = 'profile';
                                    } elseif ($user->isAdmin()) {
                                        $profileRoute = 'admin.dashboard';
                                    }
                                }
                            @endphp
                            <a class="btn btn-primary" href="{{ route($profileRoute) }}">
                                <i class="fas fa-user me-1"></i>
                                {{ $user->name ?? 'User' }}
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
</header>
