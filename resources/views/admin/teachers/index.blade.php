@extends('layouts.admin')

@section('title', 'Teachers Management')

@section('content')
<div class="page-title">
    <div>
        <h1>Teachers Management</h1>
        <p style="color: #828282; margin: 5px 0 0 0; font-size: 14px;">Daftar guru dan aksi ban/unban, view detail</p>
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

<div class="card-content teachers-card">
    <div class="card-content-title">
        <span>All Teachers</span>
    </div>

    <div class="table-responsive teachers-table-wrap">
        <table class="table table-sm teachers-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Joined Date</th>
                    <th>Total Courses</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($teachers as $teacher)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="teacher-name-cell">
                                <div class="teacher-avatar">
                                    <i class="fas fa-chalkboard-user"></i>
                                </div>
                                <div>
                                    <div class="teacher-name">{{ $teacher->name }}</div>
                                    <small class="teacher-id">ID: {{ $teacher->id }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="teacher-email">{{ $teacher->email }}</td>
                        <td class="teacher-joined">{{ $teacher->created_at ? $teacher->created_at->format('Y-m-d') : '—' }}</td>
                        <td class="teacher-courses">{{ $teacher->classes_count ?? 0 }}</td>
                        <td>
                            @php $isBanned = $teacher->isBanned(); @endphp
                            <span class="badge teacher-status {{ $isBanned ? 'status-banned' : 'status-active' }}">
                                {{ $isBanned ? 'Banned' : 'Active' }}
                            </span>
                        </td>
                        <td>
                            <div class="teacher-actions">
                                <a href="{{ route('admin.teachers.show', $teacher->id) }}" class="btn-teacher btn-view" title="View Detail">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                @if($isBanned)
                                    <form action="{{ route('admin.teachers.toggleBan', $teacher->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin unban guru ini?');">
                                        @csrf
                                        <button type="submit" class="btn-teacher btn-unban" title="Unban">
                                            <i class="fas fa-check"></i> Unban
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.teachers.toggleBan', $teacher->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin ban guru ini? Guru tidak bisa login hingga di-unban.');">
                                        @csrf
                                        <button type="submit" class="btn-teacher btn-ban" title="Ban">
                                            <i class="fas fa-ban"></i> Ban
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center teachers-empty">
                            <div class="teachers-empty-inner">
                                <i class="fas fa-chalkboard-user"></i>
                                <span>Belum ada guru terdaftar</span>
                                <p class="text-muted small">Guru terdaftar melalui halaman registrasi</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
.teachers-card { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.card-content-title { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; }
.card-content-title span { font-size: 16px; font-weight: 600; color: #333; }
.btn-add-teacher { background: #2F80ED !important; border: none; padding: 8px 16px; font-size: 13px; border-radius: 6px; color: white !important; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: opacity 0.2s; }
.btn-add-teacher:hover { opacity: 0.9; color: white !important; }

.teachers-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
.teachers-table { font-size: 13px; border-collapse: separate; border-spacing: 0; min-width: 700px; }
.teachers-table th { border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
.teachers-table td { padding: 16px 8px; vertical-align: middle; color: #333; border-bottom: 1px solid #f8f9fa; }
.teacher-name-cell { display: flex; align-items: center; gap: 12px; }
.teacher-avatar { width: 40px; height: 40px; background: #e3f2fd; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.teacher-avatar i { color: #1976d2; font-size: 16px; }
.teacher-name { font-weight: 600; color: #333; margin-bottom: 2px; }
.teacher-id { color: #828282; font-size: 11px; }
.teacher-email { color: #828282; }
.teacher-joined { color: #828282; }
.teacher-courses { font-weight: 500; }

.teacher-status { padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 500; }
.teacher-status.status-active { background: #d4edda; color: #155724; }
.teacher-status.status-banned { background: #f8d7da; color: #721c24; }

.teacher-actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
.btn-teacher { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; font-size: 12px; border-radius: 6px; border: none; cursor: pointer; text-decoration: none; transition: opacity 0.2s; font-family: inherit; }
.btn-teacher:hover { opacity: 0.9; }
.btn-view { background: #e3f2fd; color: #1976d2; }
.btn-view:hover { color: #1976d2; }
.btn-ban { background: #fff3e0; color: #f57c00; }
.btn-unban { background: #e8f5e9; color: #2e7d32; }

.teachers-empty { padding: 40px 16px !important; color: #828282; }
.teachers-empty-inner { display: flex; flex-direction: column; align-items: center; gap: 12px; }
.teachers-empty-inner i { font-size: 48px; color: #e0e0e0; }

@media (max-width: 768px) {
    .teachers-card { padding: 16px; }
    .card-content-title { flex-direction: column; align-items: stretch; }
    .card-content-title > div { display: flex; justify-content: stretch; }
    .btn-add-teacher { justify-content: center; }
    .teachers-table { font-size: 12px; min-width: 600px; }
    .teachers-table th, .teachers-table td { padding: 12px 6px; }
    .teacher-avatar { width: 36px; height: 36px; }
    .teacher-avatar i { font-size: 14px; }
    .teacher-actions { flex-direction: column; align-items: flex-start; }
    .btn-teacher { width: 100%; justify-content: center; }
}
@media (max-width: 480px) {
    .teachers-table { min-width: 540px; }
}
</style>
@endsection
