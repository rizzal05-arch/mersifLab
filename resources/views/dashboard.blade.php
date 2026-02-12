@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<div class="dashboard-page">
    <!-- Student Header -->
    <div class="dashboard-header">
        <div class="welcome-section">
            <h1 class="welcome-title">
    Welcome back, {{ auth()->user()->name }}! 👋
    @if($isSubscriber)
        @if($subscriptionPlan === 'premium')
            <i class="fas fa-crown" style="color: gold; margin-left: 12px; font-size: 1.2em;" title="Premium Subscriber - Gold Crown"></i>
        @else
            <i class="fas fa-crown" style="color: silver; margin-left: 12px; font-size: 1.2em;" title="Standard Subscriber - Silver Crown"></i>
        @endif
    @endif
</h1>
            <p class="welcome-subtitle">Continue your learning journey</p>
        </div>
        
        @php
            $user = auth()->user();
            $isSubscriber = $user->is_subscriber;
            $subscriptionPlan = strtolower($user->subscription_plan ?? 'none');
            $isExpired = $user->subscription_expires_at && $user->subscription_expires_at->isPast();
        @endphp
        
        <!-- Subscription Status Card -->
        <div class="subscription-card">
            @if($isSubscriber && $subscriptionPlan !== 'none')
                @if($subscriptionPlan === 'premium')
                    <!-- Premium Subscriber -->
                    <div class="subscription-status premium">
                        <div class="subscription-header">
                            <i class="fas fa-crown" style="font-size: 1.5rem;"></i>
                            <span class="subscription-title">
                                Premium Subscriber
                                @if($subscriptionPlan === 'premium')
                                    <i class="fas fa-crown" style="color: gold; margin-left: 8px; font-size: 0.9em;" title="Premium - Gold Crown"></i>
                                @else
                                    <i class="fas fa-crown" style="color: silver; margin-left: 8px; font-size: 0.9em;" title="Standard - Silver Crown"></i>
                                @endif
                            </span>
                            @if($isExpired)
                                <span class="expired-badge">Expired</span>
                            @endif
                        </div>
                        <div class="subscription-details">
                            <p class="access-info">
                                <i class="fas fa-infinity"></i>
                                Access to ALL courses (Standard + Premium)
                            </p>
                            <p class="expiry-info">
                                <i class="fas fa-calendar"></i>
                                Expires: {{ $user->subscription_expires_at ? $user->subscription_expires_at->format('F j, Y') : 'Unlimited' }}
                            </p>
                        </div>
                    </div>
                @else
                    <!-- Standard Subscriber -->
                    <div class="subscription-status standard">
                        <div class="subscription-header">
                            <i class="fas fa-crown" style="font-size: 1.5rem;"></i>
                            <span class="subscription-title">
                                Standard Subscriber
                                <i class="fas fa-crown" style="color: silver; margin-left: 8px; font-size: 0.9em;" title="Standard - Silver Crown"></i>
                            </span>
                            @if($isExpired)
                                <span class="expired-badge">Expired</span>
                            @endif
                        </div>
                        <div class="subscription-details">
                            <p class="access-info">
                                <i class="fas fa-graduation-cap"></i>
                                Access to Standard courses only
                            </p>
                            <p class="expiry-info">
                                <i class="fas fa-calendar"></i>
                                Expires: {{ $user->subscription_expires_at ? $user->subscription_expires_at->format('F j, Y') : 'Unlimited' }}
                            </p>
                        </div>
                    </div>
                @endif
            @else
                <!-- Free User -->
                <div class="subscription-status free">
                    <div class="subscription-header">
                        <i class="fas fa-user"></i>
                        <span class="subscription-title">Free User</span>
                    </div>
                    <div class="subscription-details">
                        <p class="access-info">
                            <i class="fas fa-lock"></i>
                            No subscription access
                        </p>
                        <p class="upgrade-info">
                            <i class="fas fa-rocket"></i>
                            <a href="{{ route('subscription.page') }}" class="upgrade-link">Upgrade to Premium</a>
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="stats-grid">
        <div class="stat-card enrolled">
            <div class="stat-icon">
                <i class="fas fa-book-open"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $classes->count() }}</h3>
                <p>Enrolled Courses</p>
            </div>
        </div>
        
        <div class="stat-card progress">
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $recentModules->count() }}</h3>
                <p>Recent Modules</p>
            </div>
        </div>
        
        <div class="stat-card featured">
            <div class="stat-icon">
                <i class="fas fa-fire"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $featuredCourses->count() }}</h3>
                <p>Featured Courses</p>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="dashboard-content">
        <!-- My Courses -->
        <div class="content-section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-graduation-cap"></i>
                    My Courses
                </h2>
                <a href="{{ route('courses') }}" class="view-all-link">View All Courses →</a>
            </div>
            
            @if($classes->count() > 0)
                <div class="courses-grid">
                    @foreach($classes->take(6) as $course)
                        <div class="course-card">
                            <div class="course-image">
                                @if($course->image)
                                    <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->name }}">
                                @else
                                    <div class="course-placeholder">
                                        <i class="fas fa-book"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="course-content">
                                <h3 class="course-title">{{ $course->name }}</h3>
                                <p class="course-instructor">
                                    <i class="fas fa-user-tie"></i>
                                    {{ $course->teacher->name ?? 'Instructor' }}
                                </p>
                                <div class="course-meta">
                                    <span class="course-modules">
                                        <i class="fas fa-layer-group"></i>
                                        {{ $course->modules_count ?? 0 }} modules
                                    </span>
                                    <span class="course-chapters">
                                        <i class="fas fa-list"></i>
                                        {{ $course->chapters_count ?? 0 }} chapters
                                    </span>
                                </div>
                                <div class="course-actions">
                                    @php
                                        $isEnrolled = DB::table('class_student')
                                            ->where('class_id', $course->id)
                                            ->where('user_id', $user->id)
                                            ->exists();
                                    @endphp
                                    
                                    @if($isEnrolled)
                                        <a href="{{ route('student.course.detail', $course->id) }}" class="btn btn-primary">
                                            <i class="fas fa-play"></i>
                                            Continue Learning
                                        </a>
                                    @else
                                        <a href="{{ route('course.detail', $course->id) }}" class="btn btn-outline">
                                            <i class="fas fa-info-circle"></i>
                                            View Details
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-book-open"></i>
                    <h3>No courses enrolled yet</h3>
                    <p>Start your learning journey by exploring our course catalog.</p>
                    <a href="{{ route('courses') }}" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Browse Courses
                    </a>
                </div>
            @endif
        </div>

        <!-- Featured Courses -->
        <div class="content-section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-fire"></i>
                    Featured Courses
                </h2>
                <a href="{{ route('courses') }}" class="view-all-link">View All →</a>
            </div>
            
            @if($featuredCourses->count() > 0)
                <div class="featured-list">
                    @foreach($featuredCourses->take(3) as $course)
                        <div class="featured-item">
                            <div class="featured-content">
                                <div class="featured-image">
                                    @if($course->image)
                                        <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->name }}">
                                    @else
                                        <div class="featured-placeholder">
                                            <i class="fas fa-book"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="featured-info">
                                    <h4 class="featured-title">{{ $course->name }}</h4>
                                    <p class="featured-instructor">{{ $course->teacher->name ?? 'Instructor' }}</p>
                                    <div class="featured-stats">
                                        <span class="student-count">
                                            <i class="fas fa-users"></i>
                                            {{ $course->student_count ?? 0 }} students
                                        </span>
                                        <span class="module-count">
                                            <i class="fas fa-layer-group"></i>
                                            {{ $course->modules_count ?? 0 }} modules
                                        </span>
                                    </div>
                                    <a href="{{ route('course.detail', $course->id) }}" class="featured-link">
                                        View Course
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.dashboard-page {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.dashboard-header {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 30px;
    margin-bottom: 40px;
    align-items: start;
}

.welcome-section h1 {
    font-size: 2rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 8px;
}

.welcome-subtitle {
    color: #6b7280;
    font-size: 1.1rem;
    margin: 0;
}

.subscription-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.subscription-status {
    border-radius: 12px;
    padding: 20px;
    color: white;
}

.subscription-status.premium {
    background: linear-gradient(135deg, #6a1b9a, #8e24aa);
    box-shadow: 0 4px 12px rgba(106, 27, 154, 0.3);
}

.subscription-status.standard {
    background: linear-gradient(135deg, #2e7d32, #43a047);
    box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
}

.subscription-status.free {
    background: linear-gradient(135deg, #64748b, #475569);
    box-shadow: 0 4px 12px rgba(100, 116, 139, 0.3);
}

.subscription-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
}

.subscription-header i {
    font-size: 1.5rem;
}

.subscription-title {
    font-size: 1.25rem;
    font-weight: 600;
}

.expired-badge {
    background: #ff9800;
    color: white;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
    margin-left: 12px;
}

.subscription-details p {
    margin: 8px 0;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.95rem;
}

.upgrade-info a {
    color: #6a1b9a;
    text-decoration: none;
    font-weight: 600;
}

.upgrade-info a:hover {
    text-decoration: underline;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    gap: 16px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.stat-card.enrolled .stat-icon {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
}

.stat-card.progress .stat-icon {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.stat-card.featured .stat-icon {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}

.stat-content h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0 0 4px 0;
}

.stat-content p {
    color: #6b7280;
    margin: 0;
    font-size: 0.9rem;
}

.dashboard-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px;
}

.content-section {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.section-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 20px;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1a1a1a;
    display: flex;
    align-items: center;
    gap: 8px;
}

.view-all-link {
    color: #3b82f6;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.9rem;
}

.view-all-link:hover {
    text-decoration: underline;
}

.courses-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 16px;
}

.course-card {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.course-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.course-image {
    height: 160px;
    position: relative;
    overflow: hidden;
}

.course-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.course-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    font-size: 2rem;
}

.course-content {
    padding: 16px;
}

.course-title {
    font-size: 1rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 8px 0;
    line-height: 1.4;
}

.course-instructor {
    color: #6b7280;
    font-size: 0.85rem;
    margin: 0 0 12px 0;
    display: flex;
    align-items: center;
    gap: 6px;
}

.course-meta {
    display: flex;
    gap: 16px;
    margin-bottom: 16px;
}

.course-meta span {
    font-size: 0.8rem;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 4px;
}

.course-actions {
    display: flex;
    gap: 8px;
}

.btn {
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
}

.btn-outline {
    background: white;
    color: #3b82f6;
    border: 1px solid #3b82f6;
}

.btn-outline:hover {
    background: #3b82f6;
    color: white;
}

.featured-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.featured-item {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 16px;
    transition: transform 0.2s ease;
}

.featured-item:hover {
    transform: translateY(-1px);
}

.featured-content {
    display: flex;
    gap: 16px;
}

.featured-image {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    overflow: hidden;
    flex-shrink: 0;
}

.featured-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.featured-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    font-size: 1.5rem;
}

.featured-info {
    flex: 1;
}

.featured-title {
    font-size: 1rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 8px 0;
}

.featured-instructor {
    color: #6b7280;
    font-size: 0.85rem;
    margin: 0 0 12px 0;
}

.featured-stats {
    display: flex;
    gap: 16px;
    margin-bottom: 12px;
}

.featured-stats span {
    font-size: 0.8rem;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 4px;
}

.featured-link {
    color: #3b82f6;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.85rem;
}

.featured-link:hover {
    text-decoration: underline;
}

.empty-state {
    text-align: center;
    padding: 40px;
    color: #6b7280;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 16px;
    color: #d1d5db;
}

.empty-state h3 {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0 0 8px 0;
}

.empty-state p {
    margin: 0 0 20px 0;
    font-size: 0.95rem;
}

@media (max-width: 768px) {
    .dashboard-header {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .dashboard-content {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .courses-grid {
        grid-template-columns: 1fr;
    }
    
    .featured-content {
        flex-direction: column;
    }
}
</style>
@endsection
