@extends('layouts.app')

@section('title', 'Add Chapter to ' . $class->name)

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('teacher.manage.content') }}">
                            <i class="fas fa-home me-1"></i>Manage Content
                        </a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ $class->name }}
                    </li>
                </ol>
            </nav>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-plus me-2"></i>Add Chapter to: {{ $class->name }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Please fix the errors:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('teacher.chapters.store', $class) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">Chapter Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" placeholder="e.g., Introduction to HTML"
                                   value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Chapter Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4"
                                      placeholder="What will students learn in this chapter?" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_published" 
                                       name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_published">
                                    Publish this chapter
                                </label>
                                <small class="d-block text-muted mt-1">Students can only see published chapters and their modules</small>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Create Chapter
                            </button>
                            <a href="{{ route('teacher.manage.content') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Class Info -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-info-circle text-info me-2"></i>Class Information
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">Class Name:</small>
                            <p class="mb-2"><strong>{{ $class->name }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Current Chapters:</small>
                            <p class="mb-0"><strong>{{ $class->chapters()->count() }}</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
