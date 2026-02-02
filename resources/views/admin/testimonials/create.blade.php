@extends('layouts.admin')

@section('title', 'Add Testimonial - Admin')

@section('content')
<div class="page-title">
    <h1>Add Testimonial</h1>
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

    <form action="{{ route('admin.testimonials.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Position (optional)</label>
            <input type="text" name="position" class="form-control" value="{{ old('position') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Content</label>
            <textarea name="content" class="form-control" rows="4" required>{{ old('content') }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Avatar (optional)</label>
            <input type="file" name="avatar" class="form-control">
            <small class="text-muted">If provided, this avatar will be used for the testimonial. Otherwise admin's profile avatar is used.</small>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="is_published" value="1" class="form-check-input" id="is_published" {{ old('is_published', true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_published">Published</label>
        </div>
        <button class="btn btn-primary">Save</button>
        <a href="{{ route('admin.testimonials.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
