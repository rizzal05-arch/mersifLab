@extends('layouts.admin')

@section('title', 'Messages')

@section('content')
<div class="page-title" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
    <div>
        <h1 class="d-flex flex-column flex-md-row align-items-md-center gap-2">
            Messages
            @if($unreadCount > 0)
                <span class="badge bg-danger">{{ $unreadCount }} Unread</span>
            @endif
        </h1>
    </div>
    <div style="max-width: 350px; width: 100%; margin-top: 0;">
        <input type="text" id="messageSearch" placeholder="Search messages..." style="width: 100%; padding: 10px 15px; border: none; background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); border-radius: 20px; font-size: 13px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); transition: all 0.3s ease; outline: none;" onfocus="this.style.background='white'; this.style.boxShadow='0 2px 8px rgba(0, 0, 0, 0.1)';" onblur="this.style.background='rgba(255, 255, 255, 0.8)'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.05)';">
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Mobile Card View -->
<div class="d-md-none">
    @forelse($messages as $message)
        <div class="card mb-3 message-card">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex align-items-center gap-2">
                        @if(!$message->is_read)
                            <span class="badge bg-primary">New</span>
                        @else
                            <span class="badge bg-secondary">Read</span>
                        @endif
                        <strong class="text-truncate">{{ $message->name }}</strong>
                    </div>
                    <small class="text-muted">{{ $message->created_at->format('d M Y, H:i') }}</small>
                </div>
                
                <div class="mb-2">
                    <small class="text-muted d-block">{{ $message->email }}</small>
                    <p class="mb-2 text-truncate" title="{{ $message->message }}">
                        {{ Str::limit($message->message, 80) }}
                    </p>
                </div>
                
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.messages.show', $message) }}" class="btn btn-sm btn-outline-primary flex-fill">
                        <i class="fas fa-eye me-1"></i> View
                    </a>
                    <a href="https://mail.google.com/mail/?view=cm&fs=1&to={{ urlencode($message->email) }}" target="_blank" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-reply me-1"></i> Reply
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-5">
            <i class="fas fa-inbox" style="font-size: 48px; opacity: 0.3;"></i>
            <p class="text-muted mt-3">No messages yet</p>
        </div>
    @endforelse
</div>

<!-- Desktop Table View -->
<div class="d-none d-md-block">
    <div class="card-content messages-card">
        <div class="card-content-title">
            <span>All Messages</span>
        </div>

        <div class="table-responsive messages-table-wrap">
            <table class="table table-sm messages-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Status</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($messages as $message)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                @if(!$message->is_read)
                                    <span class="badge message-status status-new">New</span>
                                @else
                                    <span class="badge message-status status-read">Read</span>
                                @endif
                            </td>
                            <td>
                                <div class="message-name-cell">
                                    <div class="message-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <div class="message-name" style="font-weight: 600;">{{ $message->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="message-email">
                                <a href="mailto:{{ $message->email }}" style="color: #666; text-decoration: none;">{{ $message->email }}</a>
                            </td>
                            <td class="message-date">{{ $message->created_at->format('d M Y H:i') }}</td>
                            <td style="padding: 16px 8px; vertical-align: middle;">
                                <div style="display: flex; gap: 6px; align-items: center; flex-wrap: wrap;">
                                    <!-- View Button (Text Link) -->
                                    <a href="{{ route('admin.messages.show', $message) }}" 
                                       style="color: #1976d2; text-decoration: none; font-size: 12px; font-weight: 500; padding: 4px 8px; border-radius: 4px; transition: background 0.2s;"
                                       onmouseover="this.style.background='#e3f2fd'" 
                                       onmouseout="this.style.background='transparent'"
                                       title="View Message">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                    <!-- Reply Button (External Link) -->
                                    <a href="https://mail.google.com/mail/?view=cm&fs=1&to={{ urlencode($message->email) }}" target="_blank"
                                       style="color: #2e7d32; text-decoration: none; font-size: 12px; font-weight: 500; padding: 4px 8px; border-radius: 4px; transition: background 0.2s;"
                                       onmouseover="this.style.background='#e8f5e8'" 
                                       onmouseout="this.style.background='transparent'"
                                       title="Reply via Gmail">
                                        <i class="fas fa-reply me-1"></i>Reply
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center messages-empty">
                                <div class="messages-empty-inner">
                                    <i class="fas fa-inbox"></i>
                                    <span>No messages yet</span>
                                    <p class="text-muted small">Messages will appear here when users contact you</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
@if($messages->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $messages->links() }}
    </div>
@endif

<script>
// Add hover effects for better UX
document.addEventListener('DOMContentLoaded', function() {
    const tableRows = document.querySelectorAll('.messages-table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
        });
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });

    // Search functionality for messages
    const searchInput = document.getElementById('messageSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase().trim();
            
            // Search in desktop table
            const desktopRows = document.querySelectorAll('.messages-table tbody tr');
            desktopRows.forEach(row => {
                const emptyCell = row.querySelector('.messages-empty');
                if (emptyCell) return; // Skip empty row
                
                const name = row.querySelector('.message-name')?.textContent.toLowerCase() || '';
                const email = row.querySelector('.message-email')?.textContent.toLowerCase() || '';
                const date = row.querySelector('.message-date')?.textContent.toLowerCase() || '';
                const status = row.querySelector('.message-status')?.textContent.toLowerCase() || '';
                
                const text = name + ' ' + email + ' ' + date + ' ' + status;
                
                if (searchTerm === '' || text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Search in mobile cards
            const mobileCards = document.querySelectorAll('.message-card');
            mobileCards.forEach(card => {
                const name = card.querySelector('strong')?.textContent.toLowerCase() || '';
                const email = card.querySelector('small.text-muted')?.textContent.toLowerCase() || '';
                const date = card.querySelector('small.text-muted:last-of-type')?.textContent.toLowerCase() || '';
                const status = card.querySelector('.badge')?.textContent.toLowerCase() || '';
                const message = card.querySelector('p')?.textContent.toLowerCase() || '';
                
                const text = name + ' ' + email + ' ' + date + ' ' + status + ' ' + message;
                
                if (searchTerm === '' || text.includes(searchTerm)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Check if all rows are hidden (desktop)
            const visibleDesktopRows = Array.from(desktopRows).filter(row => {
                const emptyCell = row.querySelector('.messages-empty');
                return !emptyCell && row.style.display !== 'none';
            });
            const emptyRow = document.querySelector('tr .messages-empty');
            
            if (visibleDesktopRows.length === 0 && searchTerm !== '') {
                // Show no results
                if (emptyRow) {
                    emptyRow.closest('tr').style.display = '';
                    const span = emptyRow.querySelector('.messages-empty-inner span');
                    if (span) {
                        span.textContent = `No messages found for "${searchTerm}"`;
                    }
                }
            } else if (emptyRow && searchTerm === '') {
                // Restore original empty message
                const span = emptyRow.querySelector('.messages-empty-inner span');
                if (span && span.textContent.includes('No messages found')) {
                    span.textContent = 'No messages yet';
                }
            }
            
            // Check if all cards are hidden (mobile)
            const visibleMobileCards = Array.from(mobileCards).filter(card => card.style.display !== 'none');
            const mobileEmptyState = document.querySelector('.d-md-none .text-center');
            
            if (visibleMobileCards.length === 0 && searchTerm !== '' && mobileCards.length > 0) {
                // Show no results for mobile
                if (!mobileEmptyState || !mobileEmptyState.querySelector('p')?.textContent.includes('No messages found')) {
                    const noResults = document.createElement('div');
                    noResults.className = 'text-center py-5 search-no-results-mobile';
                    noResults.innerHTML = `
                        <i class="fas fa-search" style="font-size: 48px; opacity: 0.3;"></i>
                        <p class="text-muted mt-3">No messages found for "${searchTerm}"</p>
                    `;
                    const mobileContainer = document.querySelector('.d-md-none');
                    if (mobileContainer && !mobileContainer.querySelector('.search-no-results-mobile')) {
                        mobileContainer.appendChild(noResults);
                    }
                } else if (mobileEmptyState) {
                    mobileEmptyState.querySelector('p').textContent = `No messages found for "${searchTerm}"`;
                    mobileEmptyState.style.display = 'block';
                }
            } else {
                // Remove no results message if exists
                const noResults = document.querySelector('.search-no-results-mobile');
                if (noResults) {
                    noResults.remove();
                }
                // Show original empty state if no search term
                if (searchTerm === '' && mobileEmptyState && mobileCards.length === 0) {
                    mobileEmptyState.style.display = 'block';
                }
            }
        });
    }
});
</script>

<style>
    .messages-card { 
        background: white; 
        border-radius: 12px; 
        padding: 24px; 
        box-shadow: 0 2px 8px rgba(0,0,0,0.08); 
    }
    
    .card-content-title { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        flex-wrap: wrap; 
        gap: 12px; 
        margin-bottom: 20px; 
    }
    
    .card-content-title span { 
        font-size: 16px; 
        font-weight: 700 !important; 
        color: #333; 
    }
    
    .messages-table-wrap { 
        overflow-x: auto; 
        -webkit-overflow-scrolling: touch; 
    }
    
    .messages-table { 
        font-size: 13px; 
        border-collapse: separate; 
        border-spacing: 0; 
        min-width: 700px; 
    }
    
    .messages-table th { 
        border: none; 
        padding: 12px 8px; 
        color: #828282; 
        font-weight: 600; 
        font-size: 12px; 
        text-transform: uppercase; 
        letter-spacing: 0.5px; 
    }
    
    .messages-table td { 
        padding: 16px 8px; 
        vertical-align: middle; 
        color: #333; 
        border-bottom: 1px solid #f8f9fa; 
    }
    
    .message-name-cell { 
        display: flex; 
        align-items: center; 
        gap: 12px; 
    }
    
    .message-avatar { 
        width: 40px; 
        height: 40px; 
        background: #e3f2fd; 
        border-radius: 50%; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        flex-shrink: 0; 
    }
    
    .message-avatar i { 
        color: #1976d2; 
        font-size: 16px; 
    }
    
    .message-name { 
        font-weight: 600 !important; 
        color: #333; 
        margin-bottom: 2px; 
        font-size: 13px;
    }
    
    .message-email { 
        color: #828282; 
        font-size: 13px; 
    }
    
    .message-date { 
        color: #828282; 
        font-size: 13px; 
    }
    
    .message-status { 
        padding: 4px 10px; 
        border-radius: 12px; 
        font-size: 11px; 
        font-weight: 500; 
    }
    
    .message-status.status-new { 
        background: #e3f2fd; 
        color: #1976d2; 
    }
    
    .message-status.status-read { 
        background: #f5f5f5; 
        color: #666; 
    }
    
    .message-actions { 
        display: flex; 
        gap: 8px; 
        align-items: center; 
        flex-wrap: wrap; 
    }
    
    .btn-message { 
        display: inline-flex; 
        align-items: center; 
        gap: 6px; 
        padding: 6px 12px; 
        font-size: 12px; 
        border-radius: 6px; 
        border: none; 
        cursor: pointer; 
        text-decoration: none; 
        transition: opacity 0.2s; 
        font-family: inherit; 
    }
    
    .btn-message:hover { 
        opacity: 0.9; 
    }
    
    .btn-view { 
        background: #e3f2fd; 
        color: #1976d2; 
    }
    
    .btn-view:hover { 
        color: #1976d2; 
    }
    
    .btn-reply { 
        background: #e8f5e9; 
        color: #2e7d32; 
    }
    
    .btn-reply:hover { 
        color: #2e7d32; 
    }
    
    .messages-empty { 
        padding: 40px 16px !important; 
        color: #828282; 
    }
    
    .messages-empty-inner { 
        display: flex; 
        flex-direction: column; 
        align-items: center; 
        gap: 12px; 
    }
    
    .messages-empty-inner i { 
        font-size: 48px; 
        color: #e0e0e0; 
    }
    
    .messages-empty-inner span { 
        font-size: 16px; 
        color: #666; 
        font-weight: 500; 
    }

    /* Mobile Card Styles */
    .message-card {
        border: 1px solid #e9ecef;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .message-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .message-card .card-body {
        padding: 1rem;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .messages-card { 
            padding: 16px; 
        }
        
        .card-content-title { 
            flex-direction: column; 
            align-items: stretch; 
        }
        
        .messages-table { 
            font-size: 12px; 
            min-width: 600px; 
        }
        
        .messages-table th, 
        .messages-table td { 
            padding: 12px 6px; 
        }
        
        .message-avatar { 
            width: 36px; 
            height: 36px; 
        }
        
        .message-avatar i { 
            font-size: 14px; 
        }
        
        .message-actions { 
            flex-direction: column; 
            align-items: flex-start; 
        }
        
        .btn-message { 
            width: 100%; 
            justify-content: center; 
        }
        
        .page-title { 
            flex-direction: column !important; 
            gap: 15px; 
        }
        
        .page-title > div:last-child { 
            max-width: 100% !important; 
            width: 100% !important; 
        }
        
        .page-title h1 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .message-card .text-truncate {
            max-width: 100%;
        }
    }
    
    @media (max-width: 480px) {
        .messages-table { 
            min-width: 540px; 
        }
    }
</style>
@endsection
