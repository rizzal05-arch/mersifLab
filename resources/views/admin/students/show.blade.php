@extends('layouts.admin')

@section('title', 'Student Detail - ' . ($student->name ?? 'N/A'))

@section('content')
@php
    $isBanned = $student->isBanned();
    $enrolled = $enrolled ?? collect();
    $totalModulesCompleted = $totalModulesCompleted ?? 0;
    $activities = $activities ?? collect();
    $completions = $completions ?? collect();
    $activityItems = collect();
    foreach ($activities as $log) {
        $activityItems->push((object)['type' => 'log', 'action' => $log->action ?? '', 'desc' => $log->description ?? '', 'at' => $log->created_at]);
    }
    foreach ($enrolled as $c) {
        $enrolledAt = $c->pivot->enrolled_at ?? null;
        if ($enrolledAt) {
            $activityItems->push((object)['type' => 'enroll', 'action' => 'Mendaftar kelas', 'desc' => $c->name, 'at' => \Carbon\Carbon::parse($enrolledAt)]);
        }
    }
    foreach ($completions as $comp) {
        $activityItems->push((object)['type' => 'complete', 'action' => 'Menyelesaikan modul', 'desc' => ($comp->module_title ?? 'Module') . ' di ' . ($comp->class_name ?? 'Kelas'), 'at' => \Carbon\Carbon::parse($comp->completed_at)]);
    }
    $activityItems = $activityItems->sortByDesc('at')->values()->take(30);
@endphp

<div class="page-title">
    <div>
        <h1>Student Detail</h1>
        <p style="color: #828282; margin: 5px 0 0 0; font-size: 14px;">Informasi student, kelas yang dibeli, progress & aktivitas</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 8px;">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert" style="background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; border-radius: 8px;">
        {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Student Header -->
<div class="card-content mb-4 student-header-card">
    <div class="student-header-inner">
        <div class="student-header-main">
            <div class="student-avatar-lg">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div>
                <h2 class="student-title">{{ $student->name }}</h2>
                <div class="student-meta">
                    <span><strong>Email:</strong> {{ $student->email }}</span>
                    <span class="student-status-badge {{ $isBanned ? 'status-banned' : 'status-active' }}">{{ $isBanned ? 'Banned' : 'Active' }}</span>
                </div>
                <div class="student-actions-header">
                    @if($isBanned)
                        <form action="{{ route('admin.students.toggleBan', $student->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin unban student ini?');">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-unban"><i class="fas fa-check"></i> Unban</button>
                        </form>
                    @else
                        <form action="{{ route('admin.students.toggleBan', $student->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin ban student ini? Student tidak bisa login hingga di-unban.');">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-ban"><i class="fas fa-ban"></i> Ban</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Student Details -->
<div class="card-content mb-4">
    <h3 class="panel-title">Student Details</h3>
    <div class="detail-grid">
        <div class="detail-item">
            <span class="detail-label">Bergabung</span>
            <strong>{{ $student->created_at ? $student->created_at->format('d M Y') : '—' }}</strong>
        </div>
        <div class="detail-item">
            <span class="detail-label">Kelas yang dibeli</span>
            <strong>{{ $enrolled->count() }}</strong>
        </div>
        <div class="detail-item">
            <span class="detail-label">Modul diselesaikan</span>
            <strong>{{ $totalModulesCompleted }}</strong>
        </div>
        @if($student->phone || $student->telephone)
            <div class="detail-item detail-full">
                <span class="detail-label">Telepon</span>
                <span>{{ $student->phone ?? $student->telephone ?? '—' }}</span>
            </div>
        @endif
        @if($student->bio || $student->biography)
            <div class="detail-item detail-full">
                <span class="detail-label">Bio</span>
                <p class="detail-bio">{{ $student->bio ?? $student->biography }}</p>
            </div>
        @endif
    </div>
</div>

<!-- Kelas yang dibeli -->
<div class="card-content mb-4">
    <h3 class="panel-title">Kelas yang dibeli</h3>
    @if($enrolled->count() > 0)
        <div class="enrolled-list">
            @foreach($enrolled as $c)
                @php
                    $pivot = $c->pivot;
                    $progress = (float) ($pivot->progress ?? 0);
                    $enrolledAt = $pivot->enrolled_at ?? null;
                    $completedAt = $pivot->completed_at ?? null;
                @endphp
                <div class="enrolled-item">
                    <div class="enrolled-main">
                        <div class="enrolled-icon"><i class="fas fa-book"></i></div>
                        <div>
                            <div class="enrolled-name">{{ $c->name }}</div>
                            <div class="enrolled-meta">
                                <span>Instructor: {{ $c->teacher->name ?? 'N/A' }}</span>
                                <span>Daftar: {{ $enrolledAt ? \Carbon\Carbon::parse($enrolledAt)->format('d M Y') : '—' }}</span>
                                @if($completedAt)
                                    <span class="text-success"><i class="fas fa-check-circle"></i> Selesai {{ \Carbon\Carbon::parse($completedAt)->format('d M Y') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="enrolled-progress-wrap">
                        <div class="enrolled-progress-bar"><div class="enrolled-progress-fill" style="width: {{ min(100, max(0, (float)$progress)) }}%;"></div></div>
                        <span class="enrolled-progress-pct">{{ number_format((float)$progress, 1) }}%</span>
                    </div>
                    <a href="{{ route('admin.courses.moderation', $c->id) }}" class="btn-enrolled-link" title="Lihat di Course Moderation"><i class="fas fa-external-link-alt"></i></a>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-shopping-cart"></i>
            <p>Student ini belum membeli/mendaftar kelas</p>
        </div>
    @endif
</div>

<!-- Aktivitas -->
<div class="card-content mb-4">
    <h3 class="panel-title">Aktivitas</h3>
    @if($activityItems->count() > 0)
        <ul class="activity-list">
            @foreach($activityItems as $a)
                <li>
                    <span class="activity-action">{{ $a->action }}</span>
                    <span class="activity-desc">{{ $a->desc }}</span>
                    <small class="activity-time">{{ $a->at->diffForHumans() }}</small>
                </li>
            @endforeach
        </ul>
    @else
        <div class="empty-state">
            <i class="fas fa-history"></i>
            <p>Belum ada aktivitas tercatat</p>
        </div>
    @endif
</div>

<!-- Back -->
<div class="student-footer-actions">
    <a href="{{ route('admin.students.index') }}" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Students</a>
    <a href="{{ route('admin.dashboard') }}" class="btn-dashboard"><i class="fas fa-home"></i> Dashboard</a>
</div>

<style>
.student-header-card, .card-content { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.student-header-inner { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px; }
.student-header-main { display: flex; align-items: flex-start; gap: 20px; flex-wrap: wrap; }
.student-avatar-lg { width: 64px; height: 64px; background: #e8f5e9; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.student-avatar-lg i { color: #2e7d32; font-size: 28px; }
.student-title { font-size: 24px; font-weight: 700; color: #333; margin: 0 0 8px 0; }
.student-meta { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; margin-bottom: 12px; }
.student-meta span { color: #666; font-size: 14px; }
.student-status-badge { padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; }
.student-status-badge.status-active { background: #d4edda; color: #155724; }
.student-status-badge.status-banned { background: #f8d7da; color: #721c24; }
.student-actions-header .btn { padding: 6px 14px; font-size: 13px; border-radius: 6px; border: none; }
.student-actions-header .btn-ban { background: #fff3e0; color: #f57c00; }
.student-actions-header .btn-unban { background: #e8f5e9; color: #2e7d32; }

.panel-title { font-size: 18px; font-weight: 700; color: #333; margin-bottom: 20px; }
.detail-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px; }
.detail-label { display: block; color: #828282; font-size: 13px; margin-bottom: 4px; }
.detail-item strong { color: #333; font-size: 16px; }
.detail-full { grid-column: 1 / -1; }
.detail-bio { color: #333; font-size: 14px; line-height: 1.6; margin: 8px 0 0 0; }

.enrolled-list { display: flex; flex-direction: column; gap: 12px; }
.enrolled-item { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; padding: 16px; border: 1px solid #e8e8e8; border-radius: 8px; background: #fafafa; }
.enrolled-main { display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0; }
.enrolled-icon { width: 44px; height: 44px; background: #e8f5e9; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #2e7d32; }
.enrolled-name { font-weight: 600; color: #333; margin-bottom: 4px; }
.enrolled-meta { font-size: 12px; color: #666; display: flex; flex-wrap: wrap; gap: 12px; }
.enrolled-progress-wrap { display: flex; align-items: center; gap: 10px; min-width: 120px; }
.enrolled-progress-bar { flex: 1; height: 8px; background: #e0e0e0; border-radius: 4px; overflow: hidden; }
.enrolled-progress-fill { height: 100%; background: #2e7d32; border-radius: 4px; transition: width 0.3s; }
.enrolled-progress-pct { font-size: 12px; font-weight: 600; color: #333; white-space: nowrap; }
.btn-enrolled-link { background: #e3f2fd; color: #1976d2; border: 1px solid #90caf9; padding: 8px 12px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; transition: all 0.2s; }
.btn-enrolled-link:hover { background: #1976d2; color: white; border-color: #1976d2; }

.activity-list { list-style: none; padding: 0; margin: 0; }
.activity-list li { padding: 12px 0; border-bottom: 1px solid #f0f0f0; display: flex; flex-wrap: wrap; gap: 8px; align-items: baseline; }
.activity-list li:last-child { border-bottom: none; }
.activity-action { font-weight: 600; color: #333; font-size: 13px; }
.activity-desc { color: #666; font-size: 13px; flex: 1; min-width: 0; }
.activity-time { color: #999; font-size: 11px; }

.empty-state { text-align: center; padding: 40px; color: #828282; }
.empty-state i { font-size: 48px; color: #e0e0e0; display: block; margin-bottom: 12px; }

.student-footer-actions { display: flex; justify-content: flex-end; gap: 12px; flex-wrap: wrap; margin-top: 24px; margin-bottom: 24px; }
.student-footer-actions a { padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 500; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; }
.btn-back { background: #6c757d; color: white; }
.btn-back:hover { color: white; opacity: 0.9; }
.btn-dashboard { background: #2F80ED; color: white; }
.btn-dashboard:hover { color: white; opacity: 0.9; }

@media (max-width: 768px) {
    .student-header-card, .card-content { padding: 16px; }
    .student-header-main { flex-direction: column; align-items: flex-start; }
    .student-title { font-size: 20px; }
    .detail-grid { grid-template-columns: repeat(2, 1fr); gap: 16px; }
    .enrolled-item { flex-direction: column; align-items: flex-start; }
    .enrolled-progress-wrap { width: 100%; }
}
@media (max-width: 480px) {
    .detail-grid { grid-template-columns: 1fr; }
    .student-footer-actions { flex-direction: column; }
    .student-footer-actions a { justify-content: center; }
}
</style>
@endsection
