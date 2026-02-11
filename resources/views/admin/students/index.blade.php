@extends('layouts.admin')

@section('title', 'Students Management')

@section('content')
<div class="page-title" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
    <div>
        <h1>Students Management</h1>
    </div>
    <div style="max-width: 350px; width: 100%; margin-top: 0;">
        <input type="text" id="studentSearch" placeholder="Search students..." style="width: 100%; padding: 10px 15px; border: none; background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); border-radius: 20px; font-size: 13px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); transition: all 0.3s ease; outline: none;" onfocus="this.style.background='white'; this.style.boxShadow='0 2px 8px rgba(0, 0, 0, 0.1)';" onblur="this.style.background='rgba(255, 255, 255, 0.8)'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.05)';">
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
                    <th>Pending Courses</th>
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
                                    @if(!empty($student['avatar_url']))
                                        <img src="{{ $student['avatar_url'] }}" alt="{{ $student['name'] }}" class="student-avatar-img" onerror="this.onerror=null; this.style.display='none'; var n=this.nextElementSibling; if(n) n.classList.add('student-avatar-fallback-visible');">
                                        <i class="fas fa-user-graduate student-avatar-fallback"></i>
                                    @else
                                        <i class="fas fa-user-graduate"></i>
                                    @endif
                                </div>
                                <div>
                                    <div class="student-name">{{ $student['name'] }}</div>
                                    <small class="student-id">ID: {{ $student['id'] }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="student-email">{{ $student['email'] }}</td>
                        @php
                    // Get pending courses count for each student
                    $pendingCourses = \App\Models\Purchase::where('user_id', $student['id'])
                        ->where('status', 'pending')
                        ->count();
                @endphp
                        <td class="student-joined">{{ $student['created_at'] ? \Carbon\Carbon::parse($student['created_at'])->format('d M Y, H:i') : '—' }}</td>
                        <td class="student-courses">{{ $student['enrolled_classes_count'] ?? 0 }}</td>
                        <td class="student-pending">
                            @if($pendingCourses > 0)
                                <span class="badge" style="background: #ff9800; color: white; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 500;">
                                    {{ $pendingCourses }} pending
                                </span>
                                @if($pendingCourses > 0)
                                    <form action="{{ route('admin.students.unlock-all-courses', $student['id']) }}" method="POST" style="display: inline; margin-left: 8px;">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to unlock all pending courses for this student?')">
                                            <i class="fas fa-unlock"></i> Unlock All
                                        </button>
                                    </form>
                                @endif
                            @else
                                <span class="badge" style="background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 500;">
                                    0 pending
                                </span>
                            @endif
                        </td>
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
                            </div>
                        </td>
                        <td style="padding: 16px 8px; vertical-align: middle;">
                            <div style="display: flex; gap: 6px; align-items: center; flex-wrap: wrap;">
                                <!-- View Button (Text Link) -->
                                <a href="{{ route('admin.students.show', $student['id']) }}" 
                                   style="color: #1976d2; text-decoration: none; font-size: 12px; font-weight: 500; padding: 4px 8px; border-radius: 4px; transition: background 0.2s;"
                                   onmouseover="this.style.background='#e3f2fd'" 
                                   onmouseout="this.style.background='transparent'"
                                   title="View Detail">
                                    <i class="fas fa-eye me-1"></i>View
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
    // Refresh status every 60 seconds (1 minute) for real-time updates
    refreshInterval = setInterval(refreshStudentStatus, 60000);
    
    // Stop refresh when page is not visible
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            clearInterval(refreshInterval);
        } else {
            refreshInterval = setInterval(refreshStudentStatus, 60000);
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

    // Search functionality for students
    const searchInput = document.getElementById('studentSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase().trim();
            const tableRows = document.querySelectorAll('.students-table tbody tr');
            
            tableRows.forEach(row => {
                const name = row.querySelector('.student-name')?.textContent.toLowerCase() || '';
                const email = row.querySelector('.student-email')?.textContent.toLowerCase() || '';
                const id = row.querySelector('.student-id')?.textContent.toLowerCase() || '';
                const courses = row.querySelector('.student-courses')?.textContent.toLowerCase() || '';
                
                const text = name + ' ' + email + ' ' + id + ' ' + courses;
                
                if (searchTerm === '' || text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Check if all rows are hidden
            const visibleRows = Array.from(tableRows).filter(row => {
                const emptyCell = row.querySelector('.students-empty');
                return !emptyCell && row.style.display !== 'none';
            });
            const emptyRow = document.querySelector('tr .students-empty');
            
            if (visibleRows.length === 0 && searchTerm !== '') {
                // Show no results
                if (emptyRow) {
                    emptyRow.closest('tr').style.display = '';
                    const span = emptyRow.querySelector('.students-empty-inner span');
                    if (span) {
                        span.textContent = `No students found for "${searchTerm}"`;
                    }
                }
            } else if (emptyRow && searchTerm === '') {
                // Restore original empty message
                const span = emptyRow.querySelector('.students-empty-inner span');
                if (span && span.textContent.includes('No students found')) {
                    span.textContent = 'No students registered yet';
                }
            }
        });
    }
});
</script>

<style>
.students-card { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.card-content-title { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; }
.card-content-title span { font-size: 16px; font-weight: 700 !important; color: #333; }
.students-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
.students-table { font-size: 13px; border-collapse: separate; border-spacing: 0; min-width: 700px; }
.students-table th { border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
.students-table td { padding: 16px 8px; vertical-align: middle; color: #333; border-bottom: 1px solid #f8f9fa; }
.student-name-cell { display: flex; align-items: center; gap: 12px; }
.student-avatar { width: 40px; height: 40px; background: #e8f5e9; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden; }
.student-avatar i { color: #2e7d32; font-size: 16px; }
.student-avatar .student-avatar-img { width: 100%; height: 100%; object-fit: cover; }
.student-avatar .student-avatar-fallback { display: none; color: #2e7d32; font-size: 16px; }
.student-avatar .student-avatar-fallback.student-avatar-fallback-visible { display: flex !important; }
.student-name { font-weight: 600; color: #333; margin-bottom: 2px; font-size: 13px; }
.student-id { color: #828282; font-size: 11px; }
.student-email { color: #828282; font-size: 13px; }
.student-joined { color: #828282; font-size: 13px; }
.student-courses { font-weight: 500; color: #333; font-size: 13px; }
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
    .page-title { flex-direction: column !important; gap: 15px; }
    .page-title > div:last-child { max-width: 100% !important; width: 100% !important; }
}
</style>
@endsection
