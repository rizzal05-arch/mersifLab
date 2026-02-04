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
                        <td style="padding: 16px 8px; vertical-align: middle;">
                            <div style="display: flex; gap: 6px; align-items: center; flex-wrap: wrap;">
                                <!-- Edit Button (Text Link) -->
                                <a href="{{ route('admin.categories.edit', $category) }}" 
                                   style="color: #1976d2; text-decoration: none; font-size: 12px; font-weight: 500; padding: 4px 8px; border-radius: 4px; transition: background 0.2s;"
                                   onmouseover="this.style.background='#e3f2fd'" 
                                   onmouseout="this.style.background='transparent'"
                                   title="Edit Category">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
                                <!-- Delete Button -->
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" style="display: inline;" class="delete-category-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm delete-category-btn" 
                                            style="background: #ffebee; color: #c62828; border: none; padding: 4px 8px; font-size: 11px; border-radius: 4px; cursor: pointer; transition: opacity 0.2s;"
                                            onmouseover="this.style.opacity='0.8'" 
                                            onmouseout="this.style.opacity='1'"
                                            title="Delete Category"
                                            onclick="return confirm('Hapus kategori ini? Pastikan tidak ada course yang menggunakan kategori ini.');">
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
