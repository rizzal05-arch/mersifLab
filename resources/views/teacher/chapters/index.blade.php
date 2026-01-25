@extends('layouts.app')

@section('title', $class->name . ' - Chapters')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4>{{ $class->name }}</h4>
                    <p class="text-muted mb-0">{{ $class->description }}</p>
                    <div class="mt-2">
                        <span class="badge bg-info">
                            <i class="fas fa-layer-group"></i> {{ $chapters->count() }} chapters
                        </span>
                        <span class="badge bg-secondary">
                            <i class="fas fa-book"></i> {{ $chapters->sum(function($c) { return $c->modules->count(); }) }} modules
                        </span>
                        @if($class->is_published)
                            <span class="badge bg-success">Published</span>
                        @else
                            <span class="badge bg-secondary">Draft</span>
                        @endif
                    </div>
                </div>
                <div>
                    <a href="{{ route('teacher.chapters.create', $class) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add Chapter
                    </a>
                    <a href="{{ route('teacher.manage.content') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Manage Content
                    </a>
                </div>
            </div>

            <!-- Chapters List -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Chapters</h5>
                </div>
                <div class="card-body">
                    @if($chapters->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="50">Order</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Modules</th>
                                        <th>Status</th>
                                        <th width="200">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($chapters->sortBy('order') as $chapter)
                                        <tr>
                                            <td>
                                                <span class="badge bg-light text-dark">{{ $chapter->order }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-bookmark text-primary me-2"></i>
                                                    <strong>{{ $chapter->title }}</strong>
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ Str::limit($chapter->description, 50) }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <i class="fas fa-book"></i> {{ $chapter->modules->count() }} modules
                                                </span>
                                            </td>
                                            <td>
                                                @if($chapter->is_published)
                                                    <span class="badge bg-success">Published</span>
                                                @else
                                                    <span class="badge bg-secondary">Draft</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('teacher.chapters.show', [$class, $chapter]) }}" class="btn btn-outline-primary" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('teacher.chapters.edit', [$class, $chapter]) }}" class="btn btn-outline-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('teacher.chapters.destroy', [$class, $chapter]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this chapter?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No chapters yet</h5>
                            <p class="text-muted">Start by adding your first chapter to this class.</p>
                            <a href="{{ route('teacher.chapters.create', $class) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Chapter
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
