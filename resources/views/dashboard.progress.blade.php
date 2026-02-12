@extends('layouts.app')

@section('title', 'Learning Progress')

@section('content')
<div class="progress-page">
    <!-- Progress Header -->
    <div class="progress-header">
        <div class="header-content">
            <h1 class="page-title">
                <i class="fas fa-chart-line"></i>
                My Learning Progress
            </h1>
            <p class="page-subtitle">Track your learning journey and achievements</p>
        </div>
        
        @php
            $user = auth()->user();
            $isSubscriber = $user->is_subscriber;
            $subscriptionPlan = strtolower($user->subscription_plan ?? 'none');
            $isExpired = $user->subscription_expires_at && $user->subscription_expires_at->isPast();
        @endphp
        
        <!-- Subscription Status -->
        <div class="subscription-compact">
            @if($isSubscriber && $subscriptionPlan !== 'none')
                @if($subscriptionPlan === 'premium')
                    <div class="compact-badge premium">
                        <i class="fas fa-crown"></i>
                        Premium
                        <i class="fas fa-crown" style="color: gold; margin-left: 6px; font-size: 0.8em;" title="Premium - Gold Crown"></i>
                    </div>
                @else
                    <div class="compact-badge standard">
                        <i class="fas fa-crown"></i>
                        Standard
                        <i class="fas fa-crown" style="color: silver; margin-left: 6px; font-size: 0.8em;" title="Standard - Silver Crown"></i>
                    </div>
                @endif
                @if($isExpired)
                    <span class="expired-compact">Expired</span>
                @endif
            @else
                <div class="compact-badge free">
                    <i class="fas fa-user"></i>
                    Free
                </div>
            @endif
        </div>
    </div>

    <!-- Progress Stats -->
    <div class="progress-stats">
        <div class="stat-card total">
            <div class="stat-icon">
                <i class="fas fa-eye"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $viewedModules->count() }}</h3>
                <p>Modules Viewed</p>
            </div>
        </div>
        
        <div class="stat-card completed">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $completedModules = \App\Models\ModuleCompletion::where('user_id', $user->id)->count() }}</h3>
                <p>Modules Completed</p>
            </div>
        </div>
        
        <div class="stat-card progress">
            <div class="stat-icon">
                <i class="fas fa-percentage"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $viewedModules->count() > 0 ? round(($completedModules / $viewedModules->count()) * 100, 1) : 0 }}%</h3>
                <p>Completion Rate</p>
            </div>
        </div>
    </div>

    <!-- Viewed Modules List -->
    <div class="modules-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-book-open"></i>
                Recently Viewed Modules
            </h2>
            <p class="section-subtitle">Continue learning from where you left off</p>
        </div>
        
        @if($viewedModules->count() > 0)
            <div class="modules-grid">
                @foreach($viewedModules as $module)
                    <div class="module-card">
                        <div class="module-header">
                            <div class="module-type">
                                @if($module->type === 'video')
                                    <i class="fas fa-video"></i>
                                    Video
                                @elseif($module->type === 'text')
                                    <i class="fas fa-file-alt"></i>
                                    Text
                                @elseif($module->type === 'document')
                                    <i class="fas fa-file-pdf"></i>
                                    Document
                                @else
                                    <i class="fas fa-file"></i>
                                    File
                                @endif
                            </div>
                            <div class="module-status">
                                @php
                                    $isCompleted = \App\Models\ModuleCompletion::where('user_id', $user->id)
                                        ->where('module_id', $module->id)
                                        ->exists();
                                @endphp
                                @if($isCompleted)
                                    <span class="status-badge completed">
                                        <i class="fas fa-check"></i>
                                        Completed
                                    </span>
                                @else
                                    <span class="status-badge in-progress">
                                        <i class="fas fa-play"></i>
                                        In Progress
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="module-content">
                            <h3 class="module-title">{{ $module->title }}</h3>
                            <p class="module-description">{{ Str::limit($module->description ?? '', 100) }}</p>
                            @if($module->chapter && $module->chapter->class)
                                <p class="module-course">
                                    <i class="fas fa-graduation-cap"></i>
                                    {{ $module->chapter->class->name }}
                                </p>
                            @endif
                        </div>
                        <div class="module-actions">
                            @if($module->chapter && $module->chapter->class)
                                <a href="{{ route('module.show', [$module->chapter->class_id, $module->chapter_id, $module->id]) }}" 
                                   class="btn btn-primary">
                                    <i class="fas fa-play"></i>
                                    Continue
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-book-open"></i>
                <h3>No modules viewed yet</h3>
                <p>Start exploring courses to track your learning progress.</p>
                <a href="{{ route('courses') }}" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    Browse Courses
                </a>
            </div>
        @endif
    </div>
</div>

<style>
.progress-page {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
    flex-wrap: wrap;
    gap: 20px;
}

.header-content h1 {
    font-size: 2rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.page-subtitle {
    color: #6b7280;
    font-size: 1.1rem;
    margin: 0;
}

.subscription-compact {
    display: flex;
    align-items: center;
    gap: 12px;
}

.compact-badge {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    color: white;
    display: flex;
    align-items: center;
    gap: 6px;
}

.compact-badge.premium {
    background: linear-gradient(135deg, #6a1b9a, #8e24aa);
    box-shadow: 0 2px 8px rgba(106, 27, 154, 0.3);
}

.compact-badge.standard {
    background: linear-gradient(135deg, #2e7d32, #43a047);
    box-shadow: 0 2px 8px rgba(46, 125, 50, 0.3);
}

.compact-badge.free {
    background: linear-gradient(135deg, #64748b, #475569);
    box-shadow: 0 2px 8px rgba(100, 116, 139, 0.3);
}

.expired-compact {
    background: #ff9800;
    color: white;
    padding: 4px 8px;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 500;
}

.progress-stats {
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
    transition: transform 0.2s ease;
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
    color: white;
}

.stat-card.total .stat-icon {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
}

.stat-card.completed .stat-icon {
    background: linear-gradient(135deg, #10b981, #059669);
}

.stat-card.progress .stat-icon {
    background: linear-gradient(135deg, #f59e0b, #d97706);
}

.stat-info h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0 0 4px 0;
}

.stat-info p {
    color: #6b7280;
    margin: 0;
    font-size: 0.9rem;
}

.modules-section {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.section-header {
    margin-bottom: 24px;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.section-subtitle {
    color: #6b7280;
    font-size: 0.95rem;
    margin: 0;
}

.modules-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 16px;
}

.module-card {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 20px;
    transition: transform 0.2s ease;
}

.module-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.module-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.module-type {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.85rem;
    color: #6b7280;
    font-weight: 500;
}

.module-status {
    display: flex;
    align-items: center;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 4px;
}

.status-badge.completed {
    background: #d4edda;
    color: #155724;
}

.status-badge.in-progress {
    background: #fff3cd;
    color: #856404;
}

.module-content {
    margin-bottom: 16px;
}

.module-title {
    font-size: 1rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 8px 0;
    line-height: 1.4;
}

.module-description {
    color: #6b7280;
    font-size: 0.9rem;
    line-height: 1.5;
    margin: 0 0 12px 0;
}

.module-course {
    color: #3b82f6;
    font-size: 0.85rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 6px;
}

.module-actions {
    display: flex;
    justify-content: flex-end;
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

.empty-state {
    text-align: center;
    padding: 60px 20px;
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
    .progress-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }
    
    .progress-stats {
        grid-template-columns: 1fr;
    }
    
    .modules-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection
