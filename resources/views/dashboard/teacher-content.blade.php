{{-- Dashboard para Teacher - Uses Shared Class Display Template --}}

<div class="teacher-dashboard container-fluid">
    <!-- Statistics Section -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="label">Active Classes</div>
            <div class="number">{{ $totalKursus ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Total Modules</div>
            <div class="number">{{ $totalModules ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Registered Students</div>
            <div class="number">{{ $totalStudents ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Rating</div>
            <div class="number">4.8⭐</div>
        </div>
    </div>

    <!-- Display Classes Using Shared Template with CRUD Permissions -->
    <div class="mt-5">
        @include('shared.classes-index', ['classes' => $classes ?? []])
    </div>

    <!-- Analytics and Management -->
    <div class="mt-5">
        <h2 class="section-title">Management</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <div style="font-size: 2rem; margin-bottom: 10px;">📊</div>
                <a href="{{ route('teacher.analytics') }}" style="text-decoration: none; color: inherit;">
                    <div class="label">View Analytics</div>
                </a>
            </div>
            <div class="stat-card">
                <div style="font-size: 2rem; margin-bottom: 10px;">📄</div>
                <a href="{{ route('teacher.materi.management') }}" style="text-decoration: none; color: inherit;">
                    <div class="label">Manage Content</div>
                </a>
            </div>
            <div class="stat-card">
                <div style="font-size: 2rem; margin-bottom: 10px;">👥</div>
                <a href="{{ route('my-courses') }}" style="text-decoration: none; color: inherit;">
                    <div class="label">My Students</div>
                </a>
            </div>
            <div class="stat-card">
                <div style="font-size: 2rem; margin-bottom: 10px;">⚙️</div>
                <a href="{{ route('profile') }}" style="text-decoration: none; color: inherit;">
                    <div class="label">Profile Settings</div>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="mt-5">
        <h2 class="section-title">Recent Activity</h2>
        <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">
            <ul style="list-style: none; padding: 0; margin: 0;">
                <li style="padding: 1rem 0; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #333; font-weight: 500;">New student enrolled in Web Development Class</span>
                    <small style="color: #999;">2 hours ago</small>
                </li>
                <li style="padding: 1rem 0; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #333; font-weight: 500;">3 students completed Module 5 Quiz</span>
                    <small style="color: #999;">5 hours ago</small>
                </li>
                <li style="padding: 1rem 0; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #333; font-weight: 500;">New review from student: ⭐⭐⭐⭐⭐</span>
                    <small style="color: #999;">1 day ago</small>
                </li>
                <li style="padding: 1rem 0; display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #333; font-weight: 500;">Certificates awarded to 5 students</span>
                    <small style="color: #999;">2 days ago</small>
                </li>
            </ul>
        </div>
    </div>
</div>
