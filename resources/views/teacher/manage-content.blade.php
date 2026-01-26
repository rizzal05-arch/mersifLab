@extends('layouts.app')

@section('title', 'Manage Content')

@section('content')
<section class="profile-section py-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                @include('teacher.partials.sidebar')
            </div>
            
            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="profile-content">
                    <!-- Header -->
                    <div class="profile-header mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="profile-title">Manage Content</h2>
                                <p class="profile-subtitle">Create and manage your learning content</p>
                            </div>
                            <a href="{{ route('teacher.classes.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i> New Class
                            </a>
                        </div>
                    </div>
                    
                    <!-- Alerts -->
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

                    @if($errors->any())
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <strong>Please fix the errors:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <!-- Classes List -->
                    <div class="content-management">
                        @if($classes && $classes->count() > 0)
                            <div class="classes-grid">
                                @foreach($classes as $class)
                                    <div class="class-card card border-0 shadow-sm mb-4">
                                        <div class="card-header bg-light d-flex justify-content-between align-items-start">
                                            <div>
                                                <h5 class="card-title mb-1">
                                                    {{ $class->name }}
                                                </h5>
                                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                                    <small class="text-muted">
                                                        <i class="fas fa-layer-group me-1"></i>
                                                        {{ $class->chapters->count() }} chapters · 
                                                        {{ $class->chapters->sum(function($c) { return $c->modules->count(); }) }} modules
                                                    </small>
                                                    @if($class->is_published)
                                                        <span class="badge bg-success">Published</span>
                                                    @else
                                                        <span class="badge bg-secondary">Draft</span>
                                                    @endif
                                                    <a href="{{ route('course.detail', $class->id) }}" class="btn btn-sm btn-outline-info" target="_blank" title="Preview Course">
                                                        <i class="fas fa-eye"></i> Preview
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('teacher.classes.edit', $class) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('teacher.classes.destroy', $class) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        
                                        <div class="card-body">
                                            <p class="card-text text-muted">{{ Str::limit($class->description, 100) }}</p>
                                            
                                            <!-- Chapters List -->
                                            @if($class->chapters->count() > 0)
                                                <div class="chapters-section">
                                                    <h6 class="mb-3">
                                                        <i class="fas fa-list me-2"></i>Chapters
                                                        <a href="{{ route('teacher.chapters.create', $class) }}" class="btn btn-sm btn-outline-success float-end">
                                                            <i class="fas fa-plus me-1"></i>Add
                                                        </a>
                                                    </h6>
                                                    
                                                    <div class="chapters-list">
                                                        @foreach($class->chapters->sortBy('order') as $chapter)
                                                            <div class="chapter-item card card-sm mb-2 border">
                                                                <div class="card-body p-3">
                                                                    <div class="d-flex justify-content-between align-items-start">
                                                                        <div style="flex: 1;">
                                                                            <h6 class="mb-1">
                                                                                <i class="fas fa-bookmark me-2 text-primary"></i>
                                                                                {{ $chapter->title }}
                                                                            </h6>
                                                                            <small class="text-muted">
                                                                                {{ $chapter->modules->count() }} modules
                                                                                @if($chapter->is_published)
                                                                                    <span class="badge bg-success ms-2">Published</span>
                                                                                @else
                                                                                    <span class="badge bg-secondary ms-2">Draft</span>
                                                                                @endif
                                                                            </small>
                                                                        </div>
                                                                        <div class="btn-group btn-group-sm" role="group">
                                                                            <a href="{{ route('teacher.chapters.edit', [$class, $chapter]) }}" class="btn btn-outline-primary" title="Edit">
                                                                                <i class="fas fa-edit"></i>
                                                                            </a>
                                                                            <a href="#" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#modulesModal{{ $chapter->id }}" title="Manage Modules">
                                                                                <i class="fas fa-folder-open"></i>
                                                                            </a>
                                                                            <form action="{{ route('teacher.chapters.destroy', [$class, $chapter]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this chapter? All modules will be deleted too.');">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                                                    <i class="fas fa-trash"></i>
                                                                                </button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <!-- Modules Preview -->
                                                                    @if($chapter->modules->count() > 0)
                                                                        <div class="modules-preview mt-2 pt-2 border-top">
                                                                            <small class="d-block mb-2"><strong>Modules:</strong></small>
                                                                            <div class="module-badges">
                                                                                @foreach($chapter->modules->take(3) as $mod)
                                                                                    <a href="{{ route('module.show', [$class->id, $chapter->id, $mod->id]) }}" 
                                                                                       target="_blank"
                                                                                       class="badge bg-light text-dark me-1 mb-1 text-decoration-none" 
                                                                                       title="View {{ $mod->title }}">
                                                                                        @if($mod->type === 'text')
                                                                                            <i class="fas fa-align-left"></i>
                                                                                        @elseif($mod->type === 'document')
                                                                                            <i class="fas fa-file-pdf"></i>
                                                                                        @else
                                                                                            <i class="fas fa-video"></i>
                                                                                        @endif
                                                                                        {{ $mod->title }}
                                                                                    </a>
                                                                                @endforeach
                                                                                @if($chapter->modules->count() > 3)
                                                                                    <span class="badge bg-light text-dark">+{{ $chapter->modules->count() - 3 }} more</span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    @else
                                                                        <div class="alert alert-info alert-sm mt-2 mb-0" role="alert">
                                                                            <small>No modules yet. <a href="{{ route('teacher.modules.create', $chapter) }}" class="alert-link">Add one</a></small>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @else
                                                <div class="alert alert-info alert-sm" role="alert">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    No chapters yet. 
                                                    <a href="{{ route('teacher.chapters.create', $class) }}" class="alert-link">Create your first chapter</a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info text-center py-5">
                                <i class="fas fa-inbox" style="font-size: 3rem; color: #0dcaf0; margin-bottom: 1rem;"></i>
                                <h5 class="mt-3">No Classes Yet</h5>
                                <p class="text-muted">Get started by creating your first class to organize your learning content.</p>
                                <a href="{{ route('teacher.classes.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Create First Class
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modules Management Modals -->
    @foreach($classes as $class)
        @foreach($class->chapters->sortBy('order') as $chapter)
        <!-- Modules Management Modal -->
        <div class="modal fade" id="modulesModal{{ $chapter->id }}" tabindex="-1" aria-labelledby="modulesModalLabel{{ $chapter->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modulesModalLabel{{ $chapter->id }}">
                            Manage Modules: {{ $chapter->title }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Add Module Button -->
                        <div class="mb-4">
                            <a href="{{ route('teacher.modules.create', $chapter) }}" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>Add New Module
                            </a>
                        </div>
                        
                        <!-- Modules List -->
                        @if($chapter->modules->count() > 0)
                            <div class="modules-list">
                                @foreach($chapter->modules->sortBy('order') as $module)
                                    <div class="module-row d-flex justify-content-between align-items-center p-3 border-bottom">
                                        <div>
                                            <h6 class="mb-1">
                                                @if($module->type === 'text')
                                                    <i class="fas fa-align-left text-primary"></i>
                                                @elseif($module->type === 'document')
                                                    <i class="fas fa-file-pdf text-danger"></i>
                                                @else
                                                    <i class="fas fa-video text-warning"></i>
                                                @endif
                                                {{ $module->title }}
                                            </h6>
                                            <small class="text-muted">
                                                Type: <strong>{{ ucfirst($module->type) }}</strong> 
                                                | Views: <strong>{{ $module->view_count ?? 0 }}</strong>
                                                @php
                                                    $approvalStatus = $module->approval_status ?? 'pending_approval';
                                                @endphp
                                                @if($approvalStatus === 'approved')
                                                    <span class="badge bg-success ms-2">Approved</span>
                                                @elseif($approvalStatus === 'rejected')
                                                    <span class="badge bg-danger ms-2">Rejected</span>
                                                @else
                                                    <span class="badge bg-warning ms-2">Pending Approval</span>
                                                @endif
                                                @if($module->is_published)
                                                    <span class="badge bg-info ms-2">Published</span>
                                                @else
                                                    <span class="badge bg-secondary ms-2">Draft</span>
                                                @endif
                                            </small>
                                        </div>
                                        <div class="btn-group" role="group">
                                            @if($approvalStatus === 'approved')
                                                <a href="{{ route('module.show', [$chapter->class_id, $chapter->id, $module->id]) }}" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   target="_blank"
                                                   title="View/Preview Module">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @else
                                                <span class="btn btn-sm btn-outline-secondary" 
                                                      title="Modul belum disetujui admin, tidak dapat ditayangkan atau diakses"
                                                      style="cursor: not-allowed; opacity: 0.6;">
                                                    <i class="fas fa-eye-slash"></i>
                                                </span>
                                            @endif
                                            <a href="{{ route('teacher.modules.edit', [$chapter, $module]) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('teacher.modules.destroy', [$chapter, $module]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this module?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
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
                                No modules yet. Create your first module to get started.
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <a href="{{ route('teacher.modules.create', $chapter) }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add Module
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    @endforeach
</section>

<style>
.alert-sm {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
}

.card-sm {
    margin-bottom: 0;
}

.card-sm .card-body {
    padding: 0.75rem;
}

.module-badges span {
    font-size: 0.8rem;
    white-space: nowrap;
}

.chapters-list {
    max-height: 400px;
    overflow-y: auto;
}

.profile-nav-item.active {
    background-color: #0dcaf0;
    color: white;
    border-radius: 0.375rem;
}

.content-management .class-card {
    transition: all 0.3s ease;
}

.content-management .class-card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    transform: translateY(-2px);
}
</style>
@endsection

@section('scripts')
<script>
    @if(session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#0d6efd',
                confirmButtonText: 'OK',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: true
            });
        });
    @endif
</script>
@endsection
