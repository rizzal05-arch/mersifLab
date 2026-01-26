@extends('layouts.admin')

@section('title', 'Students Management')

@section('content')
<div class="page-title">
    <div>
        <h1>Students Management</h1>
        <p style="color: #828282; margin: 5px 0 0 0; font-size: 14px;">List of students and view details</p>
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

<div class="card-content students-card">
    <div class="card-content-title">
        <span>All Students</span>
    </div>

    <div class="table-responsive students-table-wrap">
        <table class="table table-sm students-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Joined Date</th>
                    <th>Enrolled Courses</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="student-name-cell">
                                <div class="student-avatar">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                                <div>
                                    <div class="student-name">{{ $student['name'] }}</div>
                                    <small class="student-id">ID: {{ $student['id'] }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="student-email">{{ $student['email'] }}</td>
                        <td class="student-joined">{{ $student['created_at'] ? \Carbon\Carbon::parse($student['created_at'])->format('Y-m-d') : '—' }}</td>
                        <td class="student-courses">{{ $student['enrolled_classes_count'] ?? 0 }}</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                @if($student['is_online'])
                                    <span class="badge" style="background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 500;">
                                        <i class="fas fa-circle" style="font-size: 8px; margin-right: 4px;"></i> Online
                                    </span>
                                @else
                                    <span class="badge" style="background: #f8d7da; color: #721c24; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 500;">
                                        <i class="fas fa-circle" style="font-size: 8px; margin-right: 4px;"></i> Offline
                                    </span>
                                @endif
                                @php $isBanned = $student['is_banned']; @endphp
                                <span class="badge student-status {{ $isBanned ? 'status-banned' : 'status-active' }}">
                                    {{ $isBanned ? 'Banned' : 'Active' }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="student-actions">
                                <a href="{{ route('admin.students.show', $student['id']) }}" class="btn-student btn-view" title="View Detail">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center students-empty">
                            <div class="students-empty-inner">
                                <i class="fas fa-user-graduate"></i>
                                <span>No students registered yet</span>
                                <p class="text-muted small">Students register through the registration page</p>
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

function refreshStudentStatus() {
    fetch('{{ route("admin.students.index") }}', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html'
        }
    })
    .then(response => response.text())
    .then(html => {
        // Parse the HTML to extract student data
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const studentRows = doc.querySelectorAll('tbody tr');
        
        // Update status badges for each student
        studentRows.forEach((row, index) => {
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
    // Refresh status every 30 seconds
    refreshInterval = setInterval(refreshStudentStatus, 30000);
    
    // Stop refresh when page is not visible
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            clearInterval(refreshInterval);
        } else {
            refreshInterval = setInterval(refreshStudentStatus, 30000);
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
});
</script>

<style>
.students-card { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.students-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
.students-table { font-size: 13px; border-collapse: separate; border-spacing: 0; min-width: 700px; }
.students-table th { border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
.students-table td { padding: 16px 8px; vertical-align: middle; color: #333; border-bottom: 1px solid #f8f9fa; }
.student-name-cell { display: flex; align-items: center; gap: 12px; }
.student-avatar { width: 40px; height: 40px; background: #e8f5e9; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.student-avatar i { color: #2e7d32; font-size: 16px; }
.student-name { font-weight: 600; color: #333; margin-bottom: 2px; }
.student-id { color: #828282; font-size: 11px; }
.student-email { color: #666; font-size: 14px; }
.student-joined { color: #666; font-size: 14px; }
.student-courses { color: #666; font-size: 14px; font-weight: 500; }
.student-status { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
.student-status.status-active { background: #d4edda; color: #155724; }
.student-status.status-banned { background: #f8d7da; color: #721c24; }
.student-actions { display: flex; gap: 6px; }
.btn-student { padding: 6px 10px; font-size: 11px; border-radius: 4px; border: none; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; transition: all 0.2s; }
.btn-student:hover { transform: translateY(-1px); box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.btn-view { background: #e3f2fd; color: #1976d2; }
.btn-view:hover { background: #bbdefb; }
.students-empty { padding: 60px 20px; }
.students-empty-inner { display: flex; flex-direction: column; align-items: center; gap: 15px; }
.students-empty-inner i { font-size: 48px; color: #e0e0e0; }
.students-empty-inner span { font-size: 16px; color: #666; font-weight: 500; }

@media (max-width: 768px) {
    .students-card { padding: 16px; }
    .students-table { font-size: 12px; min-width: 600px; }
    .students-table th, .students-table td { padding: 12px 6px; }
    .student-avatar { width: 36px; height: 36px; }
    .btn-student { width: 100%; justify-content: center; }
}
</style>
@endsection
