@extends('layouts.admin')

@section('title', 'Teachers Management')

@section('content')
<div class="page-title" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
    <div>
        <h1>Teachers Management</h1>
    </div>
    <div style="max-width: 350px; width: 100%; margin-top: 0;">
        <input type="text" id="teacherSearch" placeholder="Search teachers..." style="width: 100%; padding: 10px 15px; border: none; background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); border-radius: 20px; font-size: 13px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); transition: all 0.3s ease; outline: none;" onfocus="this.style.background='white'; this.style.boxShadow='0 2px 8px rgba(0, 0, 0, 0.1)';" onblur="this.style.background='rgba(255, 255, 255, 0.8)'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.05)';">
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
                                    <div class="teacher-name" style="font-weight: 600;">{{ $teacher['name'] }}</div>
                                    <small class="teacher-id">ID: {{ $teacher['id'] }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="teacher-email">{{ $teacher['email'] }}</td>
                        <td class="teacher-joined">{{ $teacher['created_at'] ? \Carbon\Carbon::parse($teacher['created_at'])->format('d M Y, H:i') : '—' }}</td>
                        <td class="teacher-courses">{{ $teacher['classes_count'] ?? 0 }}</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                @if($teacher['is_online'])
                                    <span class="badge" style="background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 500;">
                                        <i class="fas fa-circle" style="font-size: 8px; margin-right: 4px;"></i> Online
                                    </span>
                                @else
                                    <span class="badge" style="background: #f8d7da; color: #721c24; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 500;">
                                        <i class="fas fa-circle" style="font-size: 8px; margin-right: 4px;"></i> Offline
                                    </span>
                                @endif
                                @php $isBanned = $teacher['is_banned']; @endphp
                                <span class="badge teacher-status {{ $isBanned ? 'status-banned' : 'status-active' }}">
                                    {{ $isBanned ? 'Banned' : 'Active' }}
                                </span>
                            </div>
                        </td>
                        <td style="padding: 16px 8px; vertical-align: middle;">
                            <div style="display: flex; gap: 6px; align-items: center; flex-wrap: wrap;">
                                <!-- View Button (Text Link) -->
                                <a href="{{ route('admin.teachers.show', $teacher['id']) }}" 
                                   style="color: #1976d2; text-decoration: none; font-size: 12px; font-weight: 500; padding: 4px 8px; border-radius: 4px; transition: background 0.2s;"
                                   onmouseover="this.style.background='#e3f2fd'" 
                                   onmouseout="this.style.background='transparent'"
                                   title="View Detail">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                                @if($teacher['is_banned'])
                                    <!-- Unban Button (Green) -->
                                    <form action="{{ route('admin.teachers.toggleBan', $teacher['id']) }}" method="POST" style="display: inline;" class="unban-teacher-form">
                                        @csrf
                                        <button type="submit" class="btn btn-sm unban-teacher-btn" 
                                                style="background: #e8f5e8; color: #2e7d32; border: none; padding: 4px 8px; font-size: 11px; border-radius: 4px; cursor: pointer; transition: opacity 0.2s;"
                                                onmouseover="this.style.opacity='0.8'" 
                                                onmouseout="this.style.opacity='1'"
                                                title="Unban Teacher"
                                                onclick="return confirm('Are you sure you want to unban this teacher?');">
                                            <i class="fas fa-check"></i> Unban
                                        </button>
                                    </form>
                                @else
                                    <!-- Ban Button (Red) -->
                                    <form action="{{ route('admin.teachers.toggleBan', $teacher['id']) }}" method="POST" style="display: inline;" class="ban-teacher-form">
                                        @csrf
                                        <button type="submit" class="btn btn-sm ban-teacher-btn" 
                                                style="background: #ffebee; color: #c62828; border: none; padding: 4px 8px; font-size: 11px; border-radius: 4px; cursor: pointer; transition: opacity 0.2s;"
                                                onmouseover="this.style.opacity='0.8'" 
                                                onmouseout="this.style.opacity='1'"
                                                title="Ban Teacher"
                                                onclick="return confirm('Are you sure you want to ban this teacher? The teacher will not be able to log in until unbanned.');">
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
                                <span>No teachers registered yet</span>
                                <p class="text-muted small">Teachers register through the registration page</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
// Auto-refresh for real-time status updates
let refreshInterval;

function refreshTeacherStatus() {
    fetch('{{ route("admin.teachers.index") }}', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html'
        }
    })
    .then(response => response.text())
    .then(html => {
        // Parse the HTML to extract teacher data
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const teacherRows = doc.querySelectorAll('tbody tr');
        
        // Update status badges for each teacher
        teacherRows.forEach((row, index) => {
            const currentRow = document.querySelectorAll('tbody tr')[index];
            if (currentRow) {
                const statusCell = row.querySelector('td:nth-child(6)'); // Status column
                const currentStatusCell = currentRow.querySelector('td:nth-child(6)');
                if (statusCell && currentStatusCell) {
                    currentStatusCell.innerHTML = statusCell.innerHTML;
                }
            }
        });
    })
    .catch(error => {
        console.log('Status refresh failed:', error);
    });
}

// Start auto-refresh when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Refresh status every 60 seconds (1 minute) for real-time updates
    refreshInterval = setInterval(refreshTeacherStatus, 60000);
    
    // Stop refresh when page is not visible
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            clearInterval(refreshInterval);
        } else {
            refreshInterval = setInterval(refreshTeacherStatus, 60000);
        }
    });
});

// Clean up interval when page unloads
window.addEventListener('beforeunload', function() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});

// Add hover effects for better UX
document.addEventListener('DOMContentLoaded', function() {
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
        });
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });

    // Search functionality for teachers
    const searchInput = document.getElementById('teacherSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase().trim();
            const tableRows = document.querySelectorAll('.teachers-table tbody tr');
            
            tableRows.forEach(row => {
                const name = row.querySelector('.teacher-name')?.textContent.toLowerCase() || '';
                const email = row.querySelector('.teacher-email')?.textContent.toLowerCase() || '';
                const id = row.querySelector('.teacher-id')?.textContent.toLowerCase() || '';
                const courses = row.querySelector('.teacher-courses')?.textContent.toLowerCase() || '';
                
                const text = name + ' ' + email + ' ' + id + ' ' + courses;
                
                if (searchTerm === '' || text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Check if all rows are hidden
            const visibleRows = Array.from(tableRows).filter(row => {
                const emptyCell = row.querySelector('.teachers-empty');
                return !emptyCell && row.style.display !== 'none';
            });
            const emptyRow = document.querySelector('tr .teachers-empty');
            
            if (visibleRows.length === 0 && searchTerm !== '') {
                // Show no results
                if (emptyRow) {
                    emptyRow.closest('tr').style.display = '';
                    const span = emptyRow.querySelector('.teachers-empty-inner span');
                    if (span) {
                        span.textContent = `No teachers found for "${searchTerm}"`;
                    }
                }
            } else if (emptyRow && searchTerm === '') {
                // Restore original empty message
                const span = emptyRow.querySelector('.teachers-empty-inner span');
                if (span && span.textContent.includes('No teachers found')) {
                    span.textContent = 'No teachers registered yet';
                }
            }
        });
    }
});
</script>

<style>
.teachers-card { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.card-content-title { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; }
.card-content-title span { font-size: 16px; font-weight: 700 !important; color: #333; }
.btn-add-teacher { background: #2F80ED !important; border: none; padding: 8px 16px; font-size: 13px; border-radius: 6px; color: white !important; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: opacity 0.2s; }
.btn-add-teacher:hover { opacity: 0.9; color: white !important; }

.teachers-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
.teachers-table { font-size: 13px; border-collapse: separate; border-spacing: 0; min-width: 700px; }
.teachers-table th { border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
.teachers-table td { padding: 16px 8px; vertical-align: middle; color: #333; border-bottom: 1px solid #f8f9fa; }
.teacher-name-cell { display: flex; align-items: center; gap: 12px; }
.teacher-avatar { width: 40px; height: 40px; background: #e3f2fd; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.teacher-avatar i { color: #1976d2; font-size: 16px; }
.teacher-name { font-weight: 600 !important; color: #333; margin-bottom: 2px; font-size: 13px; }
.teacher-id { color: #828282; font-size: 11px; }
.teacher-email { color: #828282; font-size: 13px; }
.teacher-joined { color: #828282; font-size: 13px; }
.teacher-courses { font-weight: 500; color: #333; font-size: 13px; }

.teacher-status { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
.teacher-status.status-active { background: #d4edda; color: #155724; }
.teacher-status.status-banned { background: #f8d7da; color: #721c24; }

.teacher-actions { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; }
.btn-teacher { display: inline-flex; align-items: center; gap: 4px; padding: 6px 10px; font-size: 11px; border-radius: 4px; border: none; cursor: pointer; text-decoration: none; transition: all 0.2s; font-family: inherit; }
.btn-teacher:hover { transform: translateY(-1px); box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
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
    .page-title { flex-direction: column !important; gap: 15px; }
    .page-title > div:last-child { max-width: 100% !important; width: 100% !important; }
}
@media (max-width: 480px) {
    .teachers-table { min-width: 540px; }
}
</style>
@endsection
