@extends('layouts.admin')

@section('title', 'Teacher Detail - ' . ($teacher->name ?? 'N/A'))

@section('content')
@php
    $isBanned = $teacher->isBanned();
    $totalChapters = $courses ? $courses->sum(fn($c) => $c->chapters ? $c->chapters->count() : 0) : 0;
    $totalModules = $courses ? $courses->sum(fn($c) => $c->chapters ? $c->chapters->sum(fn($ch) => $ch->modules ? $ch->modules->count() : 0) : 0) : 0;
@endphp

<div class="page-title">
    <div>
        <h1>Teacher Detail</h1>
        <p style="color: #828282; margin: 5px 0 0 0; font-size: 14px;">Informasi guru, courses/chapters/modules, rating & aktivitas</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 8px;">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Teacher Header -->
<div class="card-content mb-4 teacher-header-card">
    <div class="teacher-header-inner">
        <div class="teacher-header-main">
            <div class="teacher-avatar-lg">
                <i class="fas fa-chalkboard-user"></i>
            </div>
            <div>
                <h2 class="teacher-title">{{ $teacher->name }}</h2>
                <div class="teacher-meta">
                    <span><strong>Email:</strong> {{ $teacher->email }}</span>
                    <span class="teacher-status-badge {{ $isBanned ? 'status-banned' : 'status-active' }}">{{ $isBanned ? 'Banned' : 'Active' }}</span>
                </div>
                <div class="teacher-actions-header">
                    @if($isBanned)
                        <form action="{{ route('admin.teachers.toggleBan', $teacher->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin unban guru ini?');">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-unban"><i class="fas fa-check"></i> Unban</button>
                        </form>
                    @else
                        <form action="{{ route('admin.teachers.toggleBan', $teacher->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin ban guru ini?');">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-ban"><i class="fas fa-ban"></i> Ban</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Teacher Details -->
<div class="card-content mb-4">
    <h3 class="panel-title">Teacher Details</h3>
    <div class="detail-grid">
        <div class="detail-item">
            <span class="detail-label">Bergabung</span>
            <strong>{{ $teacher->created_at ? $teacher->created_at->format('d M Y') : '—' }}</strong>
        </div>
        <div class="detail-item">
            <span class="detail-label">Total Courses</span>
            <strong>{{ $courses ? $courses->count() : 0 }}</strong>
        </div>
        <div class="detail-item">
            <span class="detail-label">Total Chapters</span>
            <strong>{{ $totalChapters }}</strong>
        </div>
        <div class="detail-item">
            <span class="detail-label">Total Modules</span>
            <strong>{{ $totalModules }}</strong>
        </div>
        <div class="detail-item">
            <span class="detail-label">Total Enrollments</span>
            <strong>{{ $totalEnrollments }}</strong>
        </div>
        <div class="detail-item">
            <span class="detail-label">Unique Students</span>
            <strong>{{ $uniqueStudents }}</strong>
        </div>
        @if($teacher->phone || $teacher->telephone)
            <div class="detail-item detail-full">
                <span class="detail-label">Telepon</span>
                <span>{{ $teacher->phone ?? $teacher->telephone ?? '—' }}</span>
            </div>
        @endif
        @if($teacher->bio || $teacher->biography)
            <div class="detail-item detail-full">
                <span class="detail-label">Bio</span>
                <p class="detail-bio">{{ $teacher->bio ?? $teacher->biography }}</p>
            </div>
        @endif
    </div>
</div>

<!-- Courses / Chapters / Modules -->
<div class="card-content mb-4">
    <h3 class="panel-title">Courses, Chapters & Modules</h3>
    @if($courses && $courses->count() > 0)
        @foreach($courses as $course)
            <div class="course-block">
                <div class="course-block-header">
                    <div class="course-block-title">
                        <i class="fas fa-book"></i>
                        <span>{{ $course->name }}</span>
                        <span class="course-meta-sm">{{ $course->chapters ? $course->chapters->count() : 0 }} chapters · {{ $course->chapters ? $course->chapters->sum(fn($ch) => $ch->modules ? $ch->modules->count() : 0) : 0 }} modules</span>
                    </div>
                    <a href="{{ route('admin.courses.moderation', $course->id) }}" class="btn-moderation" title="Moderation">
                        <i class="fas fa-cog"></i> Moderation
                    </a>
                </div>

                @if($course->chapters && $course->chapters->count() > 0)
                    <div class="accordion teacher-curriculum" id="courseAccordion{{ $course->id }}">
                        @foreach($course->chapters as $chapter)
                            <div class="chapter-item">
                                <div class="chapter-header" data-bs-toggle="collapse" data-bs-target="#chapterCollapse{{ $chapter->id }}" aria-expanded="false" aria-controls="chapterCollapse{{ $chapter->id }}">
                                    <div class="chapter-title">
                                        <i class="fas fa-folder"></i>
                                        <span>{{ $chapter->title ?? 'Untitled Section' }}</span>
                                    </div>
                                    <i class="fas fa-chevron-down chapter-chevron"></i>
                                </div>
                                <div id="chapterCollapse{{ $chapter->id }}" class="accordion-collapse collapse" data-bs-parent="#courseAccordion{{ $course->id }}">
                                    <div class="accordion-body">
                                        @if($chapter->description)
                                            <p class="chapter-desc">{{ $chapter->description }}</p>
                                        @endif
                                        @if($chapter->modules && $chapter->modules->count() > 0)
                                            <div class="modules-table-wrap">
                                                <table class="modules-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Module</th>
                                                            <th>Type</th>
                                                            <th>Status</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($chapter->modules as $module)
                                                            <tr>
                                                                <td>
                                                                    <i class="fas fa-file-alt"></i>
                                                                    <span>{{ $module->title ?? 'Untitled Module' }}</span>
                                                                </td>
                                                                <td><span class="badge badge-type">{{ ucfirst($module->type ?? 'document') }}</span></td>
                                                                <td>
                                                                    <span class="badge {{ $module->is_published ? 'badge-pub' : 'badge-hidden' }}">{{ $module->is_published ? 'Published' : 'Hidden' }}</span>
                                                                    <small>Views: {{ $module->view_count ?? 0 }}</small>
                                                                </td>
                                                                <td>
                                                                    <a href="{{ route('module.show', ['classId' => $course->id, 'chapterId' => $chapter->id, 'moduleId' => $module->id]) }}" class="btn-open-module" target="_blank" title="Open">
                                                                        <i class="fas fa-external-link-alt"></i> Open
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <p class="text-muted text-center py-3">No modules in this section</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center py-3">No chapters in this course</p>
                @endif
            </div>
        @endforeach
    @else
        <div class="empty-state">
            <i class="fas fa-book-open"></i>
            <p>Guru ini belum membuat course</p>
        </div>
    @endif
</div>

<!-- Rating & Reviews (dari kelas yang dibuat teacher) -->
<div class="card-content mb-4">
    <h3 class="panel-title">Rating & Reviews</h3>
    <p class="rating-subtitle">Dari rating dan review kelas yang dibuat guru ini</p>

    @php
        $stats = $ratingStats ?? ['total' => 0, 'avg' => 0, 'distribution' => [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0]];
        $reviewsList = $reviews ?? collect();
    @endphp

    @if($stats['total'] > 0)
        <div class="rating-summary">
            <div class="rating-avg-block">
                <span class="rating-avg-num">{{ number_format($stats['avg'], 1) }}</span>
                <div class="rating-stars-inline">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star {{ $i <= round($stats['avg']) ? 'filled' : '' }}"></i>
                    @endfor
                </div>
                <span class="rating-total-text">{{ $stats['total'] }} review</span>
            </div>
            <div class="rating-distribution">
                @foreach([5, 4, 3, 2, 1] as $stars)
                    @php
                        $n = $stats['distribution'][$stars] ?? 0;
                        $pct = $stats['total'] > 0 ? round(($n / $stats['total']) * 100) : 0;
                    @endphp
                    <div class="rating-row">
                        <span class="rating-row-stars">{{ $stars }} <i class="fas fa-star"></i></span>
                        <div class="rating-bar-wrap">
                            <div class="rating-bar-fill" style="width: {{ $pct }}%;"></div>
                        </div>
                        <span class="rating-row-count">{{ $n }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        @if($reviewsList->isNotEmpty())
            <h4 class="reviews-list-title">Ulasan terbaru</h4>
            <div class="reviews-list">
                @foreach($reviewsList as $rev)
                    <div class="review-item">
                        <div class="review-item-header">
                            <span class="review-course">{{ $rev->classModel->name ?? 'Kelas' }}</span>
                            <span class="review-date">{{ $rev->created_at?->format('d M Y') }}</span>
                        </div>
                        <div class="review-item-meta">
                            <span class="review-user">{{ $rev->user->name ?? 'Student' }}</span>
                            <span class="review-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= ($rev->rating ?? 0) ? 'filled' : '' }}"></i>
                                @endfor
                            </span>
                        </div>
                        @if(!empty($rev->comment))
                            <p class="review-comment">{{ $rev->comment }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    @else
        <div class="rating-placeholder">
            <i class="fas fa-star"></i>
            <p>Belum ada rating atau review untuk kelas yang dibuat guru ini.</p>
        </div>
    @endif
</div>

<!-- Aktivitas Terkini -->
@if($activities && $activities->count() > 0)
    <div class="card-content mb-4">
        <h3 class="panel-title">Aktivitas Terkini</h3>
        <ul class="activity-list">
            @foreach($activities as $log)
                <li>
                    <span class="activity-action">{{ $log->action }}</span>
                    <span class="activity-desc">{{ $log->description }}</span>
                    <small class="activity-time">{{ $log->created_at?->diffForHumans() }}</small>
                </li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Back -->
<div class="teacher-footer-actions">
    <a href="{{ route('admin.teachers.index') }}" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Teachers</a>
    <a href="{{ route('admin.dashboard') }}" class="btn-dashboard"><i class="fas fa-home"></i> Dashboard</a>
</div>

<style>
.teacher-header-card, .card-content { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.teacher-header-inner { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px; }
.teacher-header-main { display: flex; align-items: flex-start; gap: 20px; flex-wrap: wrap; }
.teacher-avatar-lg { width: 64px; height: 64px; background: #e3f2fd; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.teacher-avatar-lg i { color: #1976d2; font-size: 28px; }
.teacher-title { font-size: 24px; font-weight: 700; color: #333; margin: 0 0 8px 0; }
.teacher-meta { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; margin-bottom: 12px; }
.teacher-meta span { color: #666; font-size: 14px; }
.teacher-status-badge { padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; }
.teacher-status-badge.status-active { background: #d4edda; color: #155724; }
.teacher-status-badge.status-banned { background: #f8d7da; color: #721c24; }
.teacher-actions-header .btn { padding: 6px 14px; font-size: 13px; border-radius: 6px; border: none; }
.teacher-actions-header .btn-ban { background: #fff3e0; color: #f57c00; }
.teacher-actions-header .btn-unban { background: #e8f5e9; color: #2e7d32; }

.panel-title { font-size: 18px; font-weight: 700; color: #333; margin-bottom: 20px; }
.detail-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px; }
.detail-item { }
.detail-label { display: block; color: #828282; font-size: 13px; margin-bottom: 4px; }
.detail-item strong { color: #333; font-size: 16px; }
.detail-full { grid-column: 1 / -1; }
.detail-bio { color: #333; font-size: 14px; line-height: 1.6; margin: 8px 0 0 0; }

.course-block { border: 1px solid #e0e0e0; border-radius: 8px; padding: 16px; margin-bottom: 16px; background: #fafafa; }
.course-block:last-child { margin-bottom: 0; }
.course-block-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 12px; }
.course-block-title { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.course-block-title i { color: #2F80ED; }
.course-block-title span { font-weight: 600; color: #333; }
.course-meta-sm { font-weight: normal; color: #828282; font-size: 12px; }
.btn-moderation { background: #e3f2fd; color: #1976d2; border: 1px solid #90caf9; padding: 6px 12px; font-size: 12px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; }
.btn-moderation:hover { background: #1976d2; color: white; border-color: #1976d2; }

.teacher-curriculum { border: none; }
.chapter-item { border: 1px solid #e8e8e8; border-radius: 8px; padding: 14px; margin-bottom: 12px; background: white; }
.chapter-header { display: flex; align-items: center; justify-content: space-between; cursor: pointer; }
.chapter-title { display: flex; align-items: center; gap: 10px; }
.chapter-title i { color: #2F80ED; font-size: 16px; }
.chapter-title span { font-size: 14px; color: #333; }
.chapter-chevron { color: #666; font-size: 14px; transition: transform 0.2s; }
.chapter-item .accordion-collapse.show + .chapter-header .chapter-chevron { transform: rotate(180deg); }
.chapter-item .accordion-body { padding: 16px 0 0 0; background: transparent; }
.chapter-desc { font-size: 13px; color: #666; margin-bottom: 16px; }
.modules-table-wrap { overflow-x: auto; }
.modules-table { width: 100%; font-size: 13px; min-width: 500px; border-collapse: collapse; }
.modules-table th { padding: 10px 8px; color: #828282; font-size: 11px; text-transform: uppercase; text-align: left; border-bottom: 1px solid #eee; }
.modules-table td { padding: 12px 8px; border-bottom: 1px solid #f5f5f5; vertical-align: middle; }
.modules-table td i { color: #2F80ED; margin-right: 8px; }
.badge-type { background: #e3f2fd; color: #1976d2; font-size: 11px; padding: 4px 8px; border-radius: 4px; }
.badge-pub { background: #e8f5e9; color: #27AE60; font-size: 11px; padding: 4px 8px; border-radius: 4px; }
.badge-hidden { background: #fce4ec; color: #c2185b; font-size: 11px; padding: 4px 8px; border-radius: 4px; }
.modules-table td small { display: block; color: #828282; font-size: 10px; margin-top: 2px; }
.btn-open-module { background: transparent; color: #2F80ED; border: 1px solid #90caf9; padding: 6px 10px; font-size: 11px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
.btn-open-module:hover { background: #e3f2fd; color: #1976d2; }

.rating-placeholder { text-align: center; padding: 32px; background: #f8f9fa; border-radius: 8px; color: #828282; }
.rating-placeholder i { font-size: 40px; color: #e0e0e0; margin-bottom: 12px; display: block; }
.rating-placeholder p { margin: 0; font-size: 14px; }

.activity-list { list-style: none; padding: 0; margin: 0; }
.activity-list li { padding: 12px 0; border-bottom: 1px solid #f0f0f0; display: flex; flex-wrap: wrap; gap: 8px; align-items: baseline; }
.activity-list li:last-child { border-bottom: none; }
.activity-action { font-weight: 600; color: #333; font-size: 13px; }
.activity-desc { color: #666; font-size: 13px; flex: 1; min-width: 0; }
.activity-time { color: #999; font-size: 11px; }

.empty-state { text-align: center; padding: 40px; color: #828282; }
.empty-state i { font-size: 48px; color: #e0e0e0; display: block; margin-bottom: 12px; }

.teacher-footer-actions { display: flex; justify-content: flex-end; gap: 12px; flex-wrap: wrap; margin-top: 24px; margin-bottom: 24px; }
.teacher-footer-actions a { padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 500; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; }
.btn-back { background: #6c757d; color: white; }
.btn-back:hover { color: white; opacity: 0.9; }
.btn-dashboard { background: #2F80ED; color: white; }
.btn-dashboard:hover { color: white; opacity: 0.9; }

@media (max-width: 768px) {
    .teacher-header-card, .card-content { padding: 16px; }
    .teacher-header-main { flex-direction: column; align-items: flex-start; }
    .teacher-title { font-size: 20px; }
    .detail-grid { grid-template-columns: repeat(2, 1fr); gap: 16px; }
    .course-block-header { flex-direction: column; align-items: flex-start; }
    .course-block { padding: 12px; }
    .chapter-item { padding: 12px; }
    .modules-table { font-size: 12px; min-width: 450px; }
}
@media (max-width: 480px) {
    .detail-grid { grid-template-columns: 1fr; }
    .teacher-footer-actions { flex-direction: column; }
    .teacher-footer-actions a { justify-content: center; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.teacher-curriculum .chapter-item').forEach(function(item) {
        var header = item.querySelector('.chapter-header');
        var collapse = item.querySelector('.accordion-collapse');
        var chevron = header ? header.querySelector('.chapter-chevron') : null;
        if (!collapse || !chevron) return;
        collapse.addEventListener('shown.bs.collapse', function() { chevron.style.transform = 'rotate(180deg)'; });
        collapse.addEventListener('hidden.bs.collapse', function() { chevron.style.transform = 'rotate(0deg)'; });
    });
});
</script>
@endsection
