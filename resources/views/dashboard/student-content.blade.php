{{-- Dashboard untuk Student - Uses Shared Class Display Template --}}

<div class="student-dashboard">
    <!-- Statistics Section -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="label">Available Classes</div>
            <div class="number">{{ $classes->count() }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Total Modules</div>
            <div class="number">{{ $recentModules->count() }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Learning Progress</div>
            <div class="number">45%</div>
        </div>
    </div>

    <!-- Display Classes Using Shared Template -->
    <div class="mt-5">
        @include('shared.classes-index', ['classes' => $classes])
    </div>

    <!-- Recent Modules -->
    @if($recentModules->count() > 0)
    <div class="mt-5">
        <h2 class="section-title">Recent Content</h2>
        <div class="row">
            @foreach($recentModules->take(3) as $module)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm border-0" style="border-radius: 10px; overflow: hidden;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="card-title mb-0">{{ Str::limit($module->title ?? 'Module', 40) }}</h6>
                                <span class="badge bg-info">{{ $module->type }}</span>
                            </div>
                            <p class="card-text small text-muted">
                                {{ Str::limit($module->content ?? 'Learning module', 60) }}
                            </p>
                            <a href="{{ route('student.module.view', $module) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-play-circle me-1"></i> View
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Quick Access -->
    <div class="mt-5">
        <h2 class="section-title">Quick Access</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <div style="font-size: 2rem; margin-bottom: 10px;">📖</div>
                <a href="{{ route('student.progress') }}" style="text-decoration: none; color: inherit;">
                    <div class="label">View Progress</div>
                </a>
            </div>
            <div class="stat-card">
                <div style="font-size: 2rem; margin-bottom: 10px;">🔖</div>
                <a href="{{ route('dashboard') }}" style="text-decoration: none; color: inherit;">
                    <div class="label">My Learning</div>
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
