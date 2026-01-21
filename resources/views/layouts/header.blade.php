<header class="header-section">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">

            <!-- Brand-->
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ asset('images/logo.png') }}" alt="REKA Logo" height="50">
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
                            Courses
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
                            <a class="nav-link icon-link" href="{{ route('cart') }}">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="badge-notification cart-badge">3</span>
                            </a>
                        </li>

                        <!-- Notification -->
                        <li class="nav-item me-3 dropdown position-relative">
                            <a class="nav-link icon-link" href="#" id="notificationDropdown"
                               role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-bell"></i>
                                <span class="badge-notification">5</span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-end notification-dropdown">
                                <div class="notification-header d-flex justify-content-between">
                                    <h6 class="mb-0">Notifications</h6>
                                    <a href="#" class="mark-read">Mark all</a>
                                </div>

                                <div class="notification-body">
                                    <a href="#" class="notification-item unread">
                                        <p class="notification-text">New course available</p>
                                        <span class="notification-time">2 hours ago</span>
                                    </a>
                                    <a href="#" class="notification-item">
                                        <p class="notification-text">Payment success</p>
                                        <span class="notification-time">1 day ago</span>
                                    </a>
                                </div>

                                <div class="notification-footer text-center">
                                    <a href="{{ route('notifications') }}">View all</a>
                                </div>
                            </div>
                        </li>

                        <!-- User -->
                        <li class="nav-item dropdown">
                            <a class="btn btn-primary dropdown-toggle" href="#"
                               id="userDropdown" role="button"
                               data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i>
                                {{ Auth::user()->name ?? 'Student' }}
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile') }}">
                                        <i class="fas fa-user me-2"></i> Profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('my-courses') }}">
                                        <i class="fas fa-book me-2"></i> My Courses
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
</header>
