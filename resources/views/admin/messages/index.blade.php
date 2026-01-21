@extends('layouts.admin')

@section('title', 'Messages')

@section('content')
<div class="page-title">
    <h1>
        <i class="fas fa-envelope"></i> Messages
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

<div class="card-content">
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

    <!-- Pagination -->
    @if($messages->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $messages->links() }}
        </div>
    @endif
</div>

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
</style>
@endsection
