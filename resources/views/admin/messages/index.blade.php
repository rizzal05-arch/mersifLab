@extends('layouts.admin')

@section('title', 'Messages')

@section('content')
<div class="page-title">
    <h1 class="d-flex flex-column flex-md-row align-items-md-center gap-2">
        Messages
        @if($unreadCount > 0)
            <span class="badge bg-danger">{{ $unreadCount }} Unread</span>
        @endif
    </h1>
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
                    <small class="text-muted">{{ $message->created_at->format('d M') }}</small>
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
                    <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
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
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($messages as $message)
                    <tr>
                        <td>
                            @if(!$message->is_read)
                                <span class="badge bg-primary">New</span>
                            @else
                                <span class="badge bg-secondary">Read</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $message->name }}</strong>
                        </td>
                        <td>
                            <a href="mailto:{{ $message->email }}">{{ $message->email }}</a>
                        </td>
                        <td>
                            <span class="text-truncate d-inline-block" style="max-width: 300px;" title="{{ $message->message }}">
                                {{ Str::limit($message->message, 50) }}
                            </span>
                        </td>
                        <td>
                            <small class="text-muted">{{ $message->created_at->format('d M Y H:i') }}</small>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.messages.show', $message) }}" class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="fas fa-inbox" style="font-size: 32px; opacity: 0.5;"></i>
                            <p>No messages yet</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if($messages->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $messages->links() }}
    </div>
@endif

<style>
    .card-content {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .table {
        margin-bottom: 0;
    }

    .table tbody tr {
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .badge {
        font-size: 11px;
        padding: 4px 8px;
    }

    .btn-group {
        gap: 4px;
        display: flex;
    }

    .btn-sm {
        padding: 4px 8px;
        font-size: 12px;
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
    @media (max-width: 767.98px) {
        .page-title h1 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .message-card .text-truncate {
            max-width: 100%;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }

        .badge {
            font-size: 10px;
            padding: 3px 6px;
        }
    }

    /* Ensure table doesn't break on small screens */
    @media (max-width: 576px) {
        .table-responsive {
            font-size: 0.875rem;
        }

        .table th,
        .table td {
            padding: 0.5rem;
            vertical-align: middle;
        }

        .btn-group {
            flex-direction: column;
            gap: 2px;
        }

        .btn-group .btn {
            width: 100%;
        }
    }
</style>
@endsection
