@extends('layouts.app')

@section('title', 'My Classes')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-0">My Classes</h1>
                    <p class="text-muted">Manage all your courses and classes</p>
                </div>
                <div>
                    <a href="{{ route('teacher.classes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create New Class
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Classes List -->
    @if($classes && $classes->count() > 0)
    <div class="row">
        @foreach($classes as $class)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm border-0" style="border-radius: 10px; overflow: hidden; transition: transform 0.3s, box-shadow 0.3s;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="card-title mb-0">{{ $class->name ?? 'Untitled' }}</h5>
                        @if($class->is_published)
                            <span class="badge bg-success">Published</span>
                        @else
                            <span class="badge bg-warning">Draft</span>
                        @endif
                    </div>
                    
                    <p class="card-text text-muted small mb-3">
                        {{ Str::limit($class->description ?? 'No description', 80) }}
                    </p>

                    <div class="mb-3">
                        <small class="text-muted d-block">
                            <i class="fas fa-layer-group me-1"></i>{{ $class->chapters->count() ?? 0 }} Chapters
                        </small>
                        <small class="text-muted d-block">
                            <i class="fas fa-cube me-1"></i>{{ $class->modules->count() ?? 0 }} Modules
                        </small>
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>{{ $class->created_at ? $class->created_at->format('M d, Y') : 'N/A' }}
                        </small>
                    </div>

                    <div class="btn-group w-100" role="group">
                        <a href="{{ route('teacher.classes.edit', $class->id) }}" class="btn btn-outline-primary btn-sm" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="{{ route('teacher.chapters.index', $class->id) }}" class="btn btn-outline-info btn-sm" title="View Chapters">
                            <i class="fas fa-eye"></i>
                        </a>
                        <form action="{{ route('teacher.classes.destroy', $class->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="fas fa-inbox" style="font-size: 3rem; color: #ccc;"></i>
            <p class="text-muted mt-3">No classes yet. Create your first class to get started.</p>
            <a href="{{ route('teacher.classes.create') }}" class="btn btn-primary mt-3">
                <i class="fas fa-plus me-2"></i>Create Your First Class
            </a>
        </div>
    </div>
    @endif
</div>
@endsection
