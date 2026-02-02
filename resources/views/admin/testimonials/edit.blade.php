@extends('layouts.admin')

@section('title', 'Edit Testimonial - Admin')

@section('content')
<div class="page-title">
    <h1>Edit Testimonial</h1>
</div>

<div class="card-content">
    @if($errors->any())
        <div class="alert alert-danger">
            <ul style="margin:0; padding-left: 18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.testimonials.update', $testimonial->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $testimonial->name) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Position (optional)</label>
            <input type="text" name="position" class="form-control" value="{{ old('position', $testimonial->position) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Content</label>
            <textarea name="content" class="form-control" rows="4" required>{{ old('content', $testimonial->content) }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Avatar (optional)</label>
            <input type="file" name="avatar" class="form-control">
            @if($testimonial->avatar || ($testimonial->admin && $testimonial->admin->avatar))
                <div class="mt-2">
                    <img src="{{ $testimonial->avatar ? asset('storage/' . $testimonial->avatar) : $testimonial->avatarUrl() }}" alt="avatar" style="max-width:80px; border-radius:8px;">
                </div>
                <small class="text-muted">Current avatar (testimonial/admin)</small>
            @endif
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="is_published" value="1" class="form-check-input" id="is_published" {{ old('is_published', $testimonial->is_published) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_published">Published</label>
        </div> 
        <button class="btn btn-primary">Update</button>
        <a href="{{ route('admin.testimonials.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
