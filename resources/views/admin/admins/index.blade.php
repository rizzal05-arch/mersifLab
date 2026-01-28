@extends('layouts.admin')

@section('title', 'Admin Management')

@section('content')
<div class="page-title" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
    <div>
        <h1>Admin Management</h1>
    </div>
    <div style="max-width: 350px; width: 100%; margin-top: 0;">
        <input type="text" id="adminSearch" placeholder="Search admins..." style="width: 100%; padding: 10px 15px; border: none; background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); border-radius: 20px; font-size: 13px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); transition: all 0.3s ease; outline: none;" onfocus="this.style.background='white'; this.style.boxShadow='0 2px 8px rgba(0, 0, 0, 0.1)';" onblur="this.style.background='rgba(255, 255, 255, 0.8)'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.05)';">
    </div>
</div>

<div class="card-content">
    <div class="card-content-title">
        All Administrators
        <div>
            <a href="{{ route('admin.admins.create') }}" class="btn btn-primary" style="background: #2F80ED; border: none; padding: 8px 16px; font-size: 13px; border-radius: 6px; color: white; text-decoration: none;">
                <i class="fas fa-plus"></i> Create New Admin
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-sm" style="font-size: 13px; border-collapse: separate; border-spacing: 0;">
            <thead>
                <tr>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">#</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Email</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Username</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Password</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Role</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Status</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Created By</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Last Login</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($admins as $admin)
                    <tr style="border-bottom: 1px solid #f8f9fa;">
                        <td style="padding: 16px 8px; vertical-align: middle; color: #333; font-size: 13px;">{{ $loop->iteration }}</td>
                        <td style="padding: 16px 8px; vertical-align: middle; color: #828282; font-size: 13px;">{{ $admin['email'] }}</td>
                        <td style="padding: 16px 8px; vertical-align: middle; color: #333; font-weight: 600; font-size: 13px;">{{ $admin['username'] }}</td>
                        <td style="padding: 16px 8px; vertical-align: middle;">
                            <span style="font-family: monospace; color: #828282; font-size: 12px;">{{ $admin['password'] }}</span>
                        </td>
                        <td style="padding: 16px 8px; vertical-align: middle;">
                            <span class="badge" style="padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 500; @if($admin['role'] == 'Super Admin') background: #f3e5f5; color: #4a148c; @else background: #e3f2fd; color: #1565c0; @endif">
                                {{ $admin['role'] }}
                            </span>
                        </td>
                        <td style="padding: 16px 8px; vertical-align: middle;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                @if($admin['is_online'])
                                    <span class="badge" style="background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 500;">
                                        <i class="fas fa-circle" style="font-size: 8px; margin-right: 4px;"></i> Online
                                    </span>
                                @else
                                    <span class="badge" style="background: #f8d7da; color: #721c24; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 500;">
                                        <i class="fas fa-circle" style="font-size: 8px; margin-right: 4px;"></i> Offline
                                    </span>
                                @endif
                                @if($admin['is_active'])
                                    <span class="badge" style="background: #e3f2fd; color: #1565c0; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 500;">
                                        Active
                                    </span>
                                @else
                                    <span class="badge" style="background: #fff3e0; color: #f57c00; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 500;">
                                        Inactive
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td style="padding: 16px 8px; vertical-align: middle; color: #828282; font-size: 13px;">{{ $admin['created_by'] }}</td>
                        <td style="padding: 16px 8px; vertical-align: middle; color: #828282; font-size: 13px;">
                            @if($admin['last_login'] !== 'Never')
                                <span>{{ $admin['last_login'] }}</span>
                            @else
                                <span style="color: #999;">Never</span>
                            @endif
                        </td>
                        <td style="padding: 16px 8px; vertical-align: middle;">
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                <a href="{{ route('admin.admins.show', $admin['id']) }}" class="btn btn-sm" style="background: #e3f2fd; color: #1976d2; border: none; padding: 4px 8px; font-size: 11px; border-radius: 4px; text-decoration: none;">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                @if(auth()->user()->email !== $admin['email'])
                                    <form action="{{ route('admin.admins.toggleStatus', $admin['id']) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm" style="background: {{ $admin['is_active'] ? '#fff3e0' : '#e8f5e8' }}; color: {{ $admin['is_active'] ? '#f57c00' : '#2e7d32' }}; border: none; padding: 4px 8px; font-size: 11px; border-radius: 4px;" title="{{ $admin['is_active'] ? 'Deactivate Admin' : 'Activate Admin' }}">
                                            <i class="fas fa-{{ $admin['is_active'] ? 'pause' : 'play' }}"></i> {{ $admin['is_active'] ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.admins.destroy', $admin['id']) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm" style="background: #ffebee; color: #d32f2f; border: none; padding: 4px 8px; font-size: 11px; border-radius: 4px;" onclick="return confirm('Are you sure you want to delete this admin? This action cannot be undone.')">
                                            <i class="fas fa-trash"></i> Remove
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center" style="padding: 40px; color: #828282;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 10px;">
                                <i class="fas fa-user-shield" style="font-size: 48px; color: #e0e0e0;"></i>
                                <span style="font-size: 14px;">No admins found</span>
                                <a href="{{ route('admin.admins.create') }}" class="btn btn-primary" style="background: #2F80ED; border: none; padding: 8px 16px; font-size: 13px; border-radius: 6px; color: white; text-decoration: none;">
                                    <i class="fas fa-plus"></i> Create First Admin
                                </a>
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

function refreshAdminStatus() {
    fetch('{{ route("admin.admins.index") }}', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html'
        }
    })
    .then(response => response.text())
    .then(html => {
        // Parse the HTML to extract admin data
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const adminRows = doc.querySelectorAll('tbody tr');
        
        // Update status badges for each admin
        adminRows.forEach((row, index) => {
            const currentRow = document.querySelectorAll('tbody tr')[index];
            if (currentRow) {
                const statusCell = row.querySelector('td:nth-child(6)'); // Status column
                const currentStatusCell = currentRow.querySelector('td:nth-child(6)');
                if (statusCell && currentStatusCell) {
                    currentStatusCell.innerHTML = statusCell.innerHTML;
                }
                
                const lastLoginCell = row.querySelector('td:nth-child(8)'); // Last Login column
                const currentLastLoginCell = currentRow.querySelector('td:nth-child(8)');
                if (lastLoginCell && currentLastLoginCell) {
                    currentLastLoginCell.innerHTML = lastLoginCell.innerHTML;
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
    refreshInterval = setInterval(refreshAdminStatus, 30000);
    
    // Stop refresh when page is not visible
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            clearInterval(refreshInterval);
        } else {
            refreshInterval = setInterval(refreshAdminStatus, 30000);
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

    // Search functionality for admins
    const searchInput = document.getElementById('adminSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase().trim();
            const tableRows = document.querySelectorAll('tbody tr');
            
            tableRows.forEach(row => {
                // Skip empty row
                if (row.querySelector('td[colspan]')) return;
                
                const email = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                const username = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() || '';
                const role = row.querySelector('td:nth-child(5)')?.textContent.toLowerCase() || '';
                const createdBy = row.querySelector('td:nth-child(7)')?.textContent.toLowerCase() || '';
                
                const text = email + ' ' + username + ' ' + role + ' ' + createdBy;
                
                if (searchTerm === '' || text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Check if all rows are hidden
            const visibleRows = Array.from(tableRows).filter(row => {
                if (row.querySelector('td[colspan]')) return false;
                return row.style.display !== 'none';
            });
            const emptyRow = document.querySelector('tbody tr td[colspan]');
            
            if (visibleRows.length === 0 && searchTerm !== '' && emptyRow) {
                emptyRow.closest('tr').style.display = '';
                const span = emptyRow.querySelector('span');
                if (span) {
                    span.textContent = `No admins found for "${searchTerm}"`;
                }
            } else if (emptyRow && searchTerm === '') {
                // Restore original empty message if exists
                const span = emptyRow.querySelector('span');
                if (span && !span.textContent.includes('No admins found')) {
                    span.textContent = 'No admins found';
                }
            }
        });
    }
});

// Add loading state for form submissions
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                submitBtn.disabled = true;
                
                // Re-enable after 5 seconds as fallback
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 5000);
            }
        });
    });
});
</script>

<style>
@media (max-width: 768px) {
    .page-title { flex-direction: column !important; gap: 15px; }
    .page-title > div:last-child { max-width: 100% !important; width: 100% !important; }
}
</style>
@endsection
