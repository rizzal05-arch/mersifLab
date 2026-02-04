@extends('layouts.admin')

@section('title', 'Testimonials - Admin')

@section('content')
<div class="page-title">
    <h1>Testimonials</h1>
    <p style="color: #828282; margin: 5px 0 0 0; font-size: 14px;">Manage homepage testimonials shown to visitors.</p>
</div>

<div class="card-content mb-4">
    <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary">+ Add Testimonial</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card-content">
    <table class="table table-sm">
        <thead>
            <tr>
                <th>#</th>
                <th>Avatar</th>
                <th>Name</th>
                <th>Position</th>
                <th>Published</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($testimonials as $t)
                <tr>
                    <td>{{ $loop->iteration + (($testimonials->currentPage()-1) * $testimonials->perPage()) }}</td>
                    <td style="width:70px;">
                        @if($t->avatar || ($t->admin && $t->admin->avatar))
                            <img src="{{ $t->avatar ? asset('storage/' . $t->avatar) : $t->avatarUrl() }}" alt="avatar" style="width:48px; height:48px; object-fit:cover; border-radius:8px;">
                        @else
                            <div style="width:48px; height:48px; border-radius:8px; background:#f1f5f9; display:flex; align-items:center; justify-content:center; color:#64748b; font-weight:600;">{{ strtoupper(substr($t->name,0,2)) }}</div>
                        @endif
                    </td>
                    <td>{{ $t->name }}</td>
                    <td>{{ $t->position }}</td>
                    <td>{{ $t->is_published ? 'Yes' : 'No' }}</td>
                    <td style="padding: 16px 8px; vertical-align: middle;">
                        <div style="display: flex; gap: 6px; align-items: center; flex-wrap: wrap;">
                            <!-- Edit Button (Text Link) -->
                            <a href="{{ route('admin.testimonials.edit', $t->id) }}" 
                               style="color: #1976d2; text-decoration: none; font-size: 12px; font-weight: 500; padding: 4px 8px; border-radius: 4px; transition: background 0.2s;"
                               onmouseover="this.style.background='#e3f2fd'" 
                               onmouseout="this.style.background='transparent'"
                               title="Edit Testimonial">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <!-- Toggle Publish Button -->
                            <form action="{{ route('admin.testimonials.togglePublish', $t->id) }}" method="POST" style="display: inline;" class="toggle-publish-form">
                                @csrf
                                <button type="submit" class="btn btn-sm toggle-publish-btn" 
                                        style="background: {{ $t->is_published ? '#fff3e0' : '#e8f5e8' }}; color: {{ $t->is_published ? '#f57c00' : '#2e7d32' }}; border: none; padding: 4px 8px; font-size: 11px; border-radius: 4px; cursor: pointer; transition: opacity 0.2s;"
                                        onmouseover="this.style.opacity='0.8'" 
                                        onmouseout="this.style.opacity='1'"
                                        title="{{ $t->is_published ? 'Unpublish Testimonial' : 'Publish Testimonial' }}">
                                    <i class="fas fa-{{ $t->is_published ? 'eye-slash' : 'eye' }}"></i> {{ $t->is_published ? 'Unpublish' : 'Publish' }}
                                </button>
                            </form>
                            <!-- Delete Button -->
                            <form action="{{ route('admin.testimonials.destroy', $t->id) }}" method="POST" style="display: inline;" class="delete-testimonial-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm delete-testimonial-btn" 
                                        style="background: #ffebee; color: #c62828; border: none; padding: 4px 8px; font-size: 11px; border-radius: 4px; cursor: pointer; transition: opacity 0.2s;"
                                        onmouseover="this.style.opacity='0.8'" 
                                        onmouseout="this.style.opacity='1'"
                                        title="Delete Testimonial"
                                        onclick="return confirm('Are you sure you want to delete this testimonial? This action cannot be undone.')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No testimonials yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $testimonials->links() }}
    </div>
</div>
@endsection
