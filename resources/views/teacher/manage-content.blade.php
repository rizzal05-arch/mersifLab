@extends('layouts.app')

@section('title', 'Teacher Content Management')

@section('content')
<section class="profile-section py-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="profile-sidebar">
                    <!-- Profile Avatar -->
                    <div class="profile-avatar-section text-center">
                        <div class="profile-avatar mx-auto">
                            <span class="avatar-letter">{{ strtoupper(substr(Auth::user()->email ?? 'T', 0, 1)) }}</span>
                        </div>
                        <h5 class="profile-name mt-3">{{ Auth::user()->name ?? 'Teacher' }}</h5>
                        <p class="profile-email">{{ Auth::user()->email ?? 'teacher@gmail.com' }}</p>
                        <span class="badge bg-success">Teacher</span>
                    </div>
                    
                    <!-- Navigation Menu -->
                    <nav class="profile-nav mt-4">
                        <a href="{{ route('profile') }}" class="profile-nav-item">
                            <i class="fas fa-user me-2"></i> My Profile
                        </a>
                        <a href="{{ route('teacher.manage.content') }}" class="profile-nav-item active">
                            <i class="fas fa-book me-2"></i> Manage Content
                        </a>
                        <a href="{{ route('teacher.analytics') }}" class="profile-nav-item">
                            <i class="fas fa-chart-bar me-2"></i> Analytics
                        </a>
                    </nav>
                    
                    <!-- Quick Stats -->
                    <div class="card mt-4 border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title mb-3">Quick Stats</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <small class="text-muted">Classes:</small>
                                <strong>{{ $totalClasses ?? 0 }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <small class="text-muted">Chapters:</small>
                                <strong>{{ $totalChapters ?? 0 }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">Modules:</small>
                                <strong>{{ $totalModules ?? 0 }}</strong>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Logout Button -->
                    <form action="{{ route('logout') }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="profile-content">
                    <!-- Header -->
                    <div class="profile-header mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="profile-title">Manage Chapters & Modules</h2>
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
                                                <small class="text-muted">
                                                    <i class="fas fa-layer-group me-1"></i>
                                                    {{ $class->chapters()->count() }} chapters · 
                                                    {{ $class->chapters()->with('modules')->get()->sum(function($c) { return $c->modules->count(); }) }} modules
                                                </small>
                            
                            @if($class->is_published)
                                <span class="badge bg-success ms-2">Published</span>
                            @else
                                <span class="badge bg-secondary ms-2">Draft</span>
                            @endif
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
                                            @if($class->chapters()->count() > 0)
                                                <div class="chapters-section">
                                                    <h6 class="mb-3">
                                                        <i class="fas fa-list me-2"></i>Chapters
                                                        <a href="{{ route('teacher.chapters.create', $class) }}" class="btn btn-sm btn-outline-success float-end">
                                                            <i class="fas fa-plus me-1"></i>Add
                                                        </a>
                                                    </h6>
                                                    
                                                    <div class="chapters-list">
                                                        @foreach($class->chapters()->orderBy('order')->get() as $chapter)
                                                            <div class="chapter-item card card-sm mb-2 border">
                                                                <div class="card-body p-3">
                                                                    <div class="d-flex justify-content-between align-items-start">
                                                                        <div style="flex: 1;">
                                                                            <h6 class="mb-1">
                                                                                <i class="fas fa-bookmark me-2 text-primary"></i>
                                                                                {{ $chapter->title }}
                                                                            </h6>
                                                                            <small class="text-muted">
                                                                                {{ $chapter->modules()->count() }} modules
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
                                                                    @if($chapter->modules()->count() > 0)
                                                                        <div class="modules-preview mt-2 pt-2 border-top">
                                                                            <small class="d-block mb-2"><strong>Modules:</strong></small>
                                                                            <div class="module-badges">
                                                                                @foreach($chapter->modules()->limit(3)->get() as $module)
                                                                                    <span class="badge bg-light text-dark me-1 mb-1">
                                                                                        @if($module->type === 'text')
                                                                                            <i class="fas fa-align-left"></i>
                                                                                        @elseif($module->type === 'document')
                                                                                            <i class="fas fa-file-pdf"></i>
                                                                                        @else
                                                                                            <i class="fas fa-video"></i>
                                                                                        @endif
                                                                                        {{ $module->title }}
                                                                                    </span>
                                                                                @endforeach
                                                                                @if($chapter->modules()->count() > 3)
                                                                                    <span class="badge bg-light text-dark">+{{ $chapter->modules()->count() - 3 }} more</span>
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

                                                            <!-- Modules Management Modal -->
                                                            <div class="modal fade" id="modulesModal{{ $chapter->id }}" tabindex="-1">
                                                                <div class="modal-dialog modal-lg">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title">
                                                                                Manage Modules: {{ $chapter->title }}
                                                                            </h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <!-- Add Module Button -->
                                                                            <div class="mb-4">
                                                                                <a href="{{ route('teacher.modules.create', $chapter) }}" class="btn btn-success">
                                                                                    <i class="fas fa-plus me-2"></i>Add New Module
                                                                                </a>
                                                                            </div>
                                                                            
                                                                            <!-- Modules List -->
                                                                            @if($chapter->modules()->count() > 0)
                                                                                <div class="modules-list">
                                                                                    @foreach($chapter->modules()->orderBy('order')->get() as $module)
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
                                                                                                    | Views: <strong>{{ $module->view_count }}</strong>
                                                                                                    @if($module->is_published)
                                                                                                        <span class="badge bg-success ms-2">Published</span>
                                                                                                    @else
                                                                                                        <span class="badge bg-secondary ms-2">Draft</span>
                                                                                                    @endif
                                                                                                </small>
                                                                                            </div>
                                                                                            <div class="btn-group" role="group">
                                                                                                <a href="{{ route('teacher.modules.edit', [$chapter, $module]) }}" class="btn btn-sm btn-outline-primary">
                                                                                                    <i class="fas fa-edit"></i>
                                                                                                </a>
                                                                                                <form action="{{ route('teacher.modules.destroy', [$chapter, $module]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this module?');">
                                                                                                    @csrf
                                                                                                    @method('DELETE')
                                                                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
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
