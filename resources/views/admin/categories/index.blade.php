@extends('layouts.admin')

@section('title', 'Categories Management')

@section('content')
<div class="page-title">
    <h1>Categories Management</h1>
</div>

<div class="card-content">
    <div class="card-content-title">
        <span>All Categories ({{ $categories->count() }} total)</span>
        <div>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary" style="font-size: 13px; padding: 6px 16px;">
                <i class="fas fa-plus me-1"></i>Add Category
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Description</th>
                    <th style="width: 100px;">Status</th>
                    <th style="width: 100px;">Courses</th>
                    <th style="width: 150px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td>
                            <strong>{{ $category->name }}</strong>
                        </td>
                        <td>
                            <code style="font-size: 12px; background: #f5f5f5; padding: 2px 6px; border-radius: 3px;">{{ $category->slug }}</code>
                        </td>
                        <td>
                            <span style="color: #828282; font-size: 13px;">
                                {{ Str::limit($category->description ?? 'No description', 50) }}
                            </span>
                        </td>
                        <td>
                            @if($category->is_active)
                                <span class="badge bg-success" style="font-size: 11px;">Active</span>
                            @else
                                <span class="badge bg-secondary" style="font-size: 11px;">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <span style="color: #333; font-weight: 500;">{{ $category->classes()->count() }}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus kategori ini? Pastikan tidak ada course yang menggunakan kategori ini.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 40px; color: #828282;">
                            <i class="fas fa-inbox" style="font-size: 48px; color: #e0e0e0; margin-bottom: 10px;"></i>
                            <p style="font-size: 14px; margin: 0;">No categories found. <a href="{{ route('admin.categories.create') }}">Create your first category</a></p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
