@extends('layouts.app')

@section('title', 'Edit Chapter')

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
                    <li class="breadcrumb-item">
                        {{ $chapter->class->name }}
                    </li>
                    <li class="breadcrumb-item active">
                        Edit Chapter
                    </li>
                </ol>
            </nav>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Edit Chapter: {{ $chapter->title }}
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

                    <form action="{{ route('teacher.chapters.update', [$chapter->class, $chapter]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">Chapter Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" placeholder="Chapter title"
                                   value="{{ old('title', $chapter->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Chapter Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4"
                                      placeholder="What will students learn in this chapter?">{{ old('description', $chapter->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_published" 
                                       name="is_published" value="1" {{ old('is_published', $chapter->is_published) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_published">
                                    Publish this chapter
                                </label>
                                <small class="d-block text-muted mt-1">Students can only see published chapters and their modules</small>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-save me-2"></i>Update Chapter
                            </button>
                            <a href="{{ route('teacher.manage.content') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>

                    <hr class="my-4">

                    <!-- Modules Section -->
                    <div class="modules-section">
                        <h6 class="mb-3">
                            <i class="fas fa-layer-group me-2"></i>Modules in This Chapter
                            <a href="{{ route('teacher.modules.create', $chapter) }}" class="btn btn-sm btn-success float-end">
                                <i class="fas fa-plus me-1"></i>Add Module
                            </a>
                        </h6>

                        @if($chapter->modules()->count() > 0)
                            <div class="modules-list border rounded p-3">
                                @foreach($chapter->modules()->orderBy('order')->get() as $module)
                                    <div class="module-item d-flex justify-content-between align-items-center py-2 border-bottom" style="min-height: 60px;">
                                        <div>
                                            <h6 class="mb-1">
                                                @if($module->type === 'text')
                                                    <i class="fas fa-align-left text-primary me-2"></i>
                                                @elseif($module->type === 'document')
                                                    <i class="fas fa-file-pdf text-danger me-2"></i>
                                                @else
                                                    <i class="fas fa-video text-warning me-2"></i>
                                                @endif
                                                {{ $module->title }}
                                            </h6>
                                            <small class="text-muted">
                                                <strong>Type:</strong> {{ ucfirst($module->type) }} | 
                                                <strong>Views:</strong> {{ $module->view_count }}
                                                @if($module->is_published)
                                                    <span class="badge bg-success ms-2">Published</span>
                                                @else
                                                    <span class="badge bg-secondary ms-2">Draft</span>
                                                @endif
                                            </small>
                                        </div>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('teacher.modules.edit', [$chapter, $module]) }}" class="btn btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('teacher.modules.destroy', [$chapter, $module]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this module?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                No modules yet. <a href="{{ route('teacher.modules.create', $chapter) }}" class="alert-link">Create your first module</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="card border-danger border-2 shadow-sm mt-4">
                <div class="card-header bg-danger bg-opacity-10">
                    <h6 class="mb-0 text-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>Danger Zone
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Deleting this chapter will permanently remove it and all its modules. This action cannot be undone.
                    </p>
                    <form action="{{ route('teacher.chapters.destroy', [$chapter->class, $chapter]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you absolutely sure? This will delete the chapter and all its modules.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>Delete This Chapter
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
