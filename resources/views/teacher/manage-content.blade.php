@extends('layouts.app')

@section('title', 'Manage Content')

@section('content')
<section class="manage-content-page">
    <div class="container-fluid px-3 px-md-5 py-4">
        <div class="row g-4">
            <!-- Sidebar -->
            <div class="col-lg-3">
                @include('teacher.partials.sidebar')
            </div>
            
            <!-- Main Content -->
            <div class="col-lg-9">
                <!-- Header Section -->
                <div class="header-section mb-5">
                    <div class="d-flex justify-content-between align-items-end">
                        <div class="header-content">
                            <h1 class="page-title">
                                <span class="title-icon">
                                    <i class="fas fa-book-open"></i>
                                </span>
                                Manage Content
                            </h1>
                            <p class="page-subtitle">
                                <i class="fas fa-sparkles me-2"></i>Organize and manage your learning materials
                            </p>
                        </div>
                        <a href="{{ route('teacher.classes.create') }}" class="btn btn-create-class">
                            <span class="btn-icon"><i class="fas fa-plus"></i></span>
                            <span class="btn-text">New Class</span>
                        </a>
                    </div>
                </div>

                <!-- Statistics Section -->
                @if($classes && $classes->count() > 0)
                <div class="stats-container mb-5">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="stat-item stat-classes">
                                <div class="stat-icon-box">
                                    <i class="fas fa-book-reader"></i>
                                </div>
                                <div class="stat-body">
                                    <div class="stat-value">{{ $totalClasses ?? $classes->count() }}</div>
                                    <div class="stat-label">Classes</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-item stat-chapters">
                                <div class="stat-icon-box">
                                    <i class="fas fa-list-ul"></i>
                                </div>
                                <div class="stat-body">
                                    <div class="stat-value">{{ $totalChapters ?? $classes->sum(function($c) { return $c->chapters->count(); }) }}</div>
                                    <div class="stat-label">Chapters</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-item stat-modules">
                                <div class="stat-icon-box">
                                    <i class="fas fa-layer-group"></i>
                                </div>
                                <div class="stat-body">
                                    <div class="stat-value">{{ $totalModules ?? $classes->sum(function($c) { return $c->chapters->sum(function($ch) { return $ch->modules->count(); }); }) }}</div>
                                    <div class="stat-label">Modules</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Alerts Section -->
                @if(session('success'))
                    <div class="alert alert-success-custom alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-3"></i>
                        <div class="flex-grow-1">{{ session('success') }}</div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger-custom alert-dismissible fade show" role="alert">
                        <i class="fas fa-times-circle me-3"></i>
                        <div class="flex-grow-1">{{ session('error') }}</div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-warning-custom alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-3"></i>
                        <div class="flex-grow-1">
                            <strong>Please fix the errors:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                <!-- Content Section -->
                @if($classes && $classes->count() > 0)
                    <div class="classes-container">
                        @foreach($classes as $index => $class)
                            <div class="class-card-wrapper" style="animation-delay: {{ $index * 0.1 }}s">
                                <div class="class-card">
                                    <!-- Card Header -->
                                    <div class="class-card-header">
                                        <div class="header-left">
                                            <h4 class="class-name">{{ $class->name }}</h4>
                                            <p class="class-description">{{ Str::limit($class->description, 100) }}</p>
                                        </div>
                                        <div class="header-right">
                                            <div class="class-status">
                                                @if($class->is_published)
                                                    <span class="badge-status published">
                                                        <i class="fas fa-circle me-1"></i>Published
                                                    </span>
                                                @else
                                                    <span class="badge-status draft">
                                                        <i class="fas fa-circle me-1"></i>Draft
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="card-menu">
                                                <div class="dropdown">
                                                    <button class="btn-menu" type="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-h"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('course.detail', $class->id) }}" target="_blank">
                                                                <i class="fas fa-eye me-2"></i>Preview
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('teacher.classes.edit', $class) }}">
                                                                <i class="fas fa-edit me-2"></i>Edit
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form action="{{ route('teacher.classes.destroy', $class) }}" method="POST" style="display:inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Delete this class and all its content?');">
                                                                    <i class="fas fa-trash me-2"></i>Delete
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Card Stats -->
                                    <div class="class-stats">
                                        <div class="stat-item">
                                            <span class="stat-icon"><i class="fas fa-list-ul"></i></span>
                                            <span class="stat-text">{{ $class->chapters->count() }} Chapters</span>
                                        </div>
                                        <div class="divider"></div>
                                        <div class="stat-item">
                                            <span class="stat-icon"><i class="fas fa-layer-group"></i></span>
                                            <span class="stat-text">{{ $class->chapters->sum(function($c) { return $c->modules->count(); }) }} Modules</span>
                                        </div>
                                    </div>

                                    <!-- Chapters Section -->
                                    @if($class->chapters->count() > 0)
                                        <div class="chapters-container">
                                            <div class="chapters-header">
                                                <h5 class="chapters-title">
                                                    <i class="fas fa-bookmark me-2"></i>Chapters
                                                </h5>
                                                <a href="{{ route('teacher.chapters.create', $class) }}" class="btn-add-chapter">
                                                    <i class="fas fa-plus me-1"></i>Add
                                                </a>
                                            </div>
                                            
                                            <div class="chapters-list">
                                                @foreach($class->chapters->sortBy('order') as $chapter)
                                                    <div class="chapter-row">
                                                        <div class="chapter-info">
                                                            <div class="chapter-title-box">
                                                                <h6 class="chapter-title">{{ $chapter->title }}</h6>
                                                                <span class="chapter-modules-count">{{ $chapter->modules->count() }} modules</span>
                                                            </div>
                                                            @if($chapter->modules->count() > 0)
                                                                <div class="module-tags">
                                                                    @foreach($chapter->modules->take(2) as $mod)
                                                                        <a href="{{ route('module.show', [$class->id, $chapter->id, $mod->id]) }}" target="_blank" class="module-tag" title="{{ $mod->title }}">
                                                                            @if($mod->type === 'text')
                                                                                <i class="fas fa-file-alt"></i>
                                                                            @elseif($mod->type === 'document')
                                                                                <i class="fas fa-file-pdf"></i>
                                                                            @else
                                                                                <i class="fas fa-video"></i>
                                                                            @endif
                                                                            {{ Str::limit($mod->title, 15) }}
                                                                        </a>
                                                                    @endforeach
                                                                    @if($chapter->modules->count() > 2)
                                                                        <span class="module-tag more">+{{ $chapter->modules->count() - 2 }}</span>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="chapter-actions">
                                                            <a href="{{ route('teacher.chapters.edit', [$class, $chapter]) }}" class="btn-action edit" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="#" class="btn-action manage" data-bs-toggle="modal" data-bs-target="#modulesModal{{ $chapter->id }}" title="Manage Modules">
                                                                <i class="fas fa-cog"></i>
                                                            </a>
                                                            <form action="{{ route('teacher.chapters.destroy', [$class, $chapter]) }}" method="POST" style="display:inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn-action delete" title="Delete" onclick="return confirm('Delete this chapter and all modules?');">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <div class="empty-chapters">
                                            <i class="fas fa-bookmark"></i>
                                            <p>No chapters yet</p>
                                            <a href="{{ route('teacher.chapters.create', $class) }}" class="btn-add-first">
                                                <i class="fas fa-plus me-1"></i>Create First
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state-full">
                        <div class="empty-visual">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h3 class="empty-title">No Classes Yet</h3>
                        <p class="empty-text">Get started by creating your first class to organize learning materials</p>
                        <a href="{{ route('teacher.classes.create') }}" class="btn btn-create-class">
                            <span class="btn-icon"><i class="fas fa-plus"></i></span>
                            <span class="btn-text">Create First Class</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Modules Management Modals -->
    @foreach($classes as $class)
        @foreach($class->chapters->sortBy('order') as $chapter)
        <!-- Modules Management Modal -->
        <div class="modal fade" id="modulesModal{{ $chapter->id }}" tabindex="-1" aria-labelledby="modulesModalLabel{{ $chapter->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content modal-content-modules">
                    <!-- Modal Header -->
                    <div class="modal-header modal-header-modules">
                        <div class="header-info">
                            <h5 class="modal-title" id="modulesModalLabel{{ $chapter->id }}">
                                <i class="fas fa-list me-2"></i>{{ $chapter->title }}
                            </h5>
                            <p class="modal-subtitle">Manage modules in this chapter</p>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body modal-body-modules">
                        <!-- Add Module Button -->
                        <div class="add-module-section">
                            <a href="{{ route('teacher.modules.create', $chapter) }}" class="btn-add-module">
                                <span class="btn-icon"><i class="fas fa-plus"></i></span>
                                <span class="btn-text">Add New Module</span>
                            </a>
                        </div>
                        
                        <!-- Modules List -->
                        @if($chapter->modules->count() > 0)
                            <div class="modules-container">
                                @foreach($chapter->modules->sortBy('order') as $module)
                                    @php
                                        $approvalStatus = $module->approval_status ?? 'pending_approval';
                                    @endphp
                                    <div class="module-card">
                                        <!-- Module Type Icon -->
                                        <div class="module-type-indicator">
                                            @if($module->type === 'text')
                                                <i class="fas fa-align-left"></i>
                                                <span class="type-label">Text</span>
                                            @elseif($module->type === 'document')
                                                <i class="fas fa-file-pdf"></i>
                                                <span class="type-label">PDF</span>
                                            @else
                                                <i class="fas fa-video"></i>
                                                <span class="type-label">Video</span>
                                            @endif
                                        </div>

                                        <!-- Module Info -->
                                        <div class="module-info">
                                            <h6 class="module-title">{{ $module->title }}</h6>
                                            <div class="module-meta">
                                                <span class="meta-item">
                                                    <i class="fas fa-eye me-1"></i>
                                                    {{ $module->view_count ?? 0 }} views
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Module Status -->
                                        <div class="module-status-group">
                                            <span class="status-badge approval-badge @if($approvalStatus === 'approved') approved @elseif($approvalStatus === 'rejected') rejected @else pending @endif">
                                                @if($approvalStatus === 'approved')
                                                    <i class="fas fa-check-circle me-1"></i>Approved
                                                @elseif($approvalStatus === 'rejected')
                                                    <i class="fas fa-times-circle me-1"></i>Rejected
                                                @else
                                                    <i class="fas fa-clock me-1"></i>Pending
                                                @endif
                                            </span>
                                            <span class="status-badge publish-badge @if($module->is_published) published @else draft @endif">
                                                @if($module->is_published)
                                                    <i class="fas fa-globe me-1"></i>Published
                                                @else
                                                    <i class="fas fa-lock me-1"></i>Draft
                                                @endif
                                            </span>
                                        </div>

                                        <!-- Module Actions -->
                                        <div class="module-actions">
                                            @if($approvalStatus === 'approved')
                                                <a href="{{ route('module.show', [$chapter->class_id, $chapter->id, $module->id]) }}" 
                                                   class="action-btn preview-btn" 
                                                   target="_blank"
                                                   title="View/Preview Module">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @else
                                                <button class="action-btn preview-btn disabled" 
                                                        title="Module not yet approved by admin"
                                                        disabled>
                                                    <i class="fas fa-eye-slash"></i>
                                                </button>
                                            @endif
                                            <a href="{{ route('teacher.modules.edit', [$chapter, $module]) }}" 
                                               class="action-btn edit-btn" 
                                               title="Edit Module">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('teacher.modules.destroy', [$chapter, $module]) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="action-btn delete-btn" 
                                                        title="Delete Module"
                                                        onclick="return confirm('Delete this module?');">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-modules-state">
                                <i class="fas fa-folder-open"></i>
                                <h6>No Modules Yet</h6>
                                <p>Create your first module to get started</p>
                            </div>
                        @endif
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer modal-footer-modules">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Close
                        </button>
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
/* ========== PAGE LAYOUT ========== */
.manage-content-page {
    background: linear-gradient(135deg, #f8fafc 0%, #eef2f5 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.container-fluid {
    max-width: 1600px;
}

/* ========== HEADER SECTION ========== */
.header-section {
    animation: slideInDown 0.6s ease-out;
}

.header-content .page-title {
    display: flex;
    align-items: center;
    font-size: 2.2rem;
    font-weight: 800;
    color: #1a202c;
    margin: 0 0 0.5rem 0;
    letter-spacing: -0.5px;
}

.title-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 12px;
    margin-right: 1rem;
    font-size: 1.5rem;
}

.page-subtitle {
    font-size: 0.95rem;
    color: #718096;
    margin: 0;
    font-weight: 500;
}

.btn-create-class {
    display: inline-flex;
    align-items: center;
    gap: 0.8rem;
    padding: 0.9rem 2rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
    text-decoration: none;
}

.btn-create-class:hover {
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
}

.btn-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 6px;
    font-size: 0.9rem;
}

.btn-text {
    font-weight: 600;
}

/* ========== STATISTICS SECTION ========== */
.stats-container {
    animation: slideInUp 0.6s ease-out 0.2s both;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 1.2rem;
    padding: 1.5rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}

.stat-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
}

.stat-icon-box {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 56px;
    height: 56px;
    border-radius: 12px;
    font-size: 1.5rem;
}

.stat-classes .stat-icon-box {
    background: linear-gradient(135deg, #667eea20 0%, #764ba220 100%);
    color: #667eea;
}

.stat-chapters .stat-icon-box {
    background: linear-gradient(135deg, #11998e20 0%, #38ef7d20 100%);
    color: #11998e;
}

.stat-modules .stat-icon-box {
    background: linear-gradient(135deg, #0084ff20 0%, #1dccff20 100%);
    color: #0084ff;
}

.stat-body {
    flex: 1;
}

.stat-value {
    font-size: 1.8rem;
    font-weight: 700;
    color: #1a202c;
    line-height: 1;
    margin-bottom: 0.4rem;
}

.stat-label {
    font-size: 0.85rem;
    color: #718096;
    font-weight: 500;
}

/* ========== MODULES MANAGEMENT MODAL ========== */
.modal-content-modules {
    border: none;
    border-radius: 15px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3) !important;
    background: white !important;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
}

.modal-content-modules .modal-header {
    flex-shrink: 0;
    overflow: visible;
}

.modal-content-modules .modal-body {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
}

.modal-content-modules .modal-footer {
    flex-shrink: 0;
}


.modal-dialog {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: calc(100% - 1rem);
    margin-top: 140px;
}

body.modal-open {
    overflow: hidden !important;
}

.modal-dialog-scrollable {
    display: flex;
    flex-direction: column;
    max-height: 75vh;
    overflow: hidden !important;
}

.modal-dialog-scrollable .modal-content {
    display: flex;
    flex-direction: column;
    max-height: 75vh;
    overflow: visible !important;
}

.modal-dialog-scrollable .modal-header {
    flex-shrink: 0;
    overflow: visible !important;
}

.modal-dialog-scrollable .modal-body {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
}

.modal-dialog-scrollable .modal-footer {
    flex-shrink: 0;
}

.modal-header-modules {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: white !important;
    border: none !important;
    border-radius: 15px 15px 0 0 !important;
    padding: 1.4rem 1.8rem !important;
    margin: 0 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 1.5rem !important;
    flex-wrap: wrap !important;
    flex-shrink: 0 !important;
    position: relative !important;
    z-index: auto !important;
}

.modal-header-modules .header-info {
    flex: 1 !important;
    min-width: 250px !important;
}

.modal-header-modules .modal-title {
    font-size: 1.4rem !important;
    font-weight: 700 !important;
    margin: 0 0 0.6rem 0 !important;
    display: flex !important;
    align-items: center !important;
    white-space: normal !important;
    line-height: 1.4 !important;
    color: white !important;
}

.modal-subtitle {
    font-size: 0.9rem !important;
    color: rgba(255, 255, 255, 0.9) !important;
    margin: 0 !important;
    font-weight: 500 !important;
    line-height: 1.3 !important;
}

.modal-header-modules .btn-close-white {
    filter: brightness(0) invert(1) !important;
    padding: 0.5rem !important;
}

.modal-body-modules {
    padding: 2.5rem 2rem;
    background: #f9fafb;
}

.add-module-section {
    margin-bottom: 2.2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid white;
}

.btn-add-module {
    display: inline-flex;
    align-items: center;
    gap: 0.8rem;
    padding: 1rem 1.8rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

.btn-add-module:hover {
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
}

.btn-add-module .btn-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 6px;
}

/* ========== MODULES CONTAINER ========== */
.modules-container {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.2rem;
    animation: fadeIn 0.5s ease-out;
}

.module-card {
    display: flex;
    align-items: center;
    gap: 1.2rem;
    padding: 1.2rem;
    background: white;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.module-card:hover {
    background: white;
    border-color: #667eea;
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.15);
    transform: translateY(-2px);
}

/* Module Type Indicator */
.module-type-indicator {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 60px;
    height: 60px;
    border-radius: 10px;
    font-size: 1.5rem;
    color: white;
    flex-shrink: 0;
    position: relative;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.module-type-indicator .type-label {
    font-size: 0.65rem;
    font-weight: 700;
    margin-top: 0.2rem;
    letter-spacing: 0.5px;
}

/* Module Info */
.module-info {
    flex: 1;
    min-width: 0;
}

.module-title {
    font-size: 1rem;
    font-weight: 700;
    color: #2d3748;
    margin: 0 0 0.5rem 0;
    word-break: break-word;
}

.module-meta {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.meta-item {
    font-size: 0.85rem;
    color: #718096;
    font-weight: 500;
    display: flex;
    align-items: center;
}

/* Module Status Group */
.module-status-group {
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
    min-width: fit-content;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.4rem 0.9rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    white-space: nowrap;
}

.approval-badge {
    background: #fef3e2;
    color: #92400e;
    border: 1px solid #fde68a;
}

.approval-badge.approved {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #86efac;
}

.approval-badge.rejected {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fca5a5;
}

.approval-badge.pending {
    background: #fef3e2;
    color: #92400e;
    border: 1px solid #fde68a;
}

.publish-badge {
    background: #e0f2fe;
    color: #0c4a6e;
    border: 1px solid #7dd3fc;
}

.publish-badge.published {
    background: #dbeafe;
    color: #1e40af;
    border: 1px solid #93c5fd;
}

.publish-badge.draft {
    background: #e5e7eb;
    color: #374151;
    border: 1px solid #d1d5db;
}

/* Module Actions */
.module-actions {
    display: flex;
    gap: 0.6rem;
    flex-shrink: 0;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: white;
    color: #667eea;
    cursor: pointer;
    font-size: 0.9rem;
    transition: all 0.2s ease;
    text-decoration: none;
}

.action-btn:hover:not(.disabled) {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: transparent;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.action-btn.edit-btn {
    color: #667eea;
}

.action-btn.delete-btn {
    color: #ef4444;
}

.action-btn.delete-btn:hover:not(.disabled) {
    background: #ef4444;
    border-color: #ef4444;
}

.action-btn.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    color: #cbd5e0;
    border-color: #cbd5e0;
}

.action-btn.disabled:hover {
    background: white;
    border-color: #cbd5e0;
    transform: none;
    box-shadow: none;
}

/* Empty Modules State */
.empty-modules-state {
    text-align: center;
    padding: 3rem 2rem;
    background: white;
    border-radius: 10px;
    border: 2px dashed #cbd5e0;
    color: #a0aec0;
}

.empty-modules-state i {
    font-size: 3rem;
    color: #cbd5e0;
    margin-bottom: 1rem;
    display: block;
}

.empty-modules-state h6 {
    color: #4a5568;
    font-weight: 600;
    margin: 0.5rem 0;
}

.empty-modules-state p {
    color: #718096;
    font-size: 0.9rem;
    margin: 0;
}

.modal-footer-modules {
    background: white;
    border-top: 1px solid #e2e8f0;
    border-radius: 0 0 15px 15px;
    padding: 1.8rem;
    gap: 0.8rem;
    display: flex;
    justify-content: flex-end;
    flex-shrink: 0;
}

.modal-footer-modules .btn {
    padding: 0.7rem 1.8rem;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.2s ease;
    font-size: 0.95rem;
}

.modal-footer-modules .btn-secondary {
    background: #e5e7eb;
    border-color: #e5e7eb;
    color: #374151;
}

.modal-footer-modules .btn-secondary:hover {
    background: #d1d5db;
    border-color: #d1d5db;
    color: #111827;
    transform: translateY(-2px);
}

.modal-footer-modules .btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
}

.modal-footer-modules .btn-primary:hover {
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

/* Custom scrollbar for modal body */
.modal-body-modules::-webkit-scrollbar {
    width: 8px;
}

.modal-body-modules::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.modal-body-modules::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
}

.modal-body-modules::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
}
    display: flex;
    align-items: flex-start;
    padding: 1.2rem 1.5rem;
    border: none;
    border-radius: 10px;
    margin-bottom: 1.5rem;
    font-weight: 500;
    animation: slideInDown 0.4s ease-out;
}

.alert-success-custom {
    background: linear-gradient(135deg, #d4fc79 0%, #11ddc1 100%);
    color: #1a3818;
}

.alert-danger-custom {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
    color: white;
}

.alert-warning-custom {
    background: linear-gradient(135deg, #ffa502 0%, #ffcd3b 100%);
    color: #3e2723;
}

.alert-success-custom i,
.alert-danger-custom i,
.alert-warning-custom i {
    font-size: 1.2rem;
    min-width: 24px;
}

.alert-success-custom .btn-close-white,
.alert-danger-custom .btn-close-white,
.alert-warning-custom .btn-close-white {
    filter: brightness(0) invert(1);
}

/* ========== CLASSES CONTAINER ========== */
.classes-container {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2rem;
    animation: fadeIn 0.5s ease-out;
}

.class-card-wrapper {
    animation: slideInUp 0.6s ease-out;
}

.class-card {
    background: white;
    border-radius: 14px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.class-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

/* ========== CARD HEADER ========== */
.class-card-header {
    padding: 1.8rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 2rem;
}

.header-left {
    flex: 1;
}

.class-name {
    font-size: 1.3rem;
    font-weight: 700;
    color: white;
    margin: 0 0 0.5rem 0;
    letter-spacing: -0.3px;
}

.class-description {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.85);
    margin: 0;
    line-height: 1.4;
}

.header-right {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.class-status {
    display: flex;
    gap: 0.5rem;
}

.badge-status {
    display: inline-flex;
    align-items: center;
    padding: 0.4rem 0.9rem;
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.badge-status i {
    font-size: 0.6rem;
    margin-right: 0.4rem;
}

.badge-status.draft {
    background: rgba(255, 255, 255, 0.15);
}

.btn-menu {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 1rem;
}

.btn-menu:hover {
    background: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.5);
}

/* ========== CARD STATS ========== */
.class-stats {
    display: flex;
    align-items: center;
    padding: 1.2rem 1.8rem;
    border-bottom: 1px solid #e2e8f0;
    background: #f9fafb;
}

.class-stats .stat-item {
    flex: 1;
    padding: 0;
    background: none;
    box-shadow: none;
}

.class-stats .stat-item:hover {
    transform: none;
    box-shadow: none;
}

.class-stats .stat-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 8px;
    font-size: 0.9rem;
    margin-right: 0.8rem;
}

.class-stats .stat-text {
    font-size: 0.9rem;
    font-weight: 600;
    color: #2d3748;
}

.class-stats .divider {
    width: 1px;
    height: 30px;
    background: #e2e8f0;
    margin: 0 1rem;
}

/* ========== CHAPTERS SECTION ========== */
.chapters-container {
    padding: 1.8rem;
}

.chapters-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e2e8f0;
}

.chapters-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #2d3748;
    margin: 0;
    display: flex;
    align-items: center;
}

.btn-add-chapter {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
}

.btn-add-chapter:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    color: white;
}

.chapters-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.chapter-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.2rem;
    background: #f9fafb;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
}

.chapter-row:hover {
    background: white;
    border-color: #667eea;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
}

.chapter-info {
    flex: 1;
}

.chapter-title-box {
    margin-bottom: 0.6rem;
}

.chapter-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: #2d3748;
    margin: 0;
}

.chapter-modules-count {
    font-size: 0.8rem;
    color: #718096;
    font-weight: 500;
}

.module-tags {
    display: flex;
    gap: 0.6rem;
    flex-wrap: wrap;
    margin-top: 0.6rem;
}

.module-tag {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.35rem 0.75rem;
    background: linear-gradient(135deg, #667eea20 0%, #764ba220 100%);
    color: #667eea;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
    border: 1px solid #e2e8f0;
}

.module-tag:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: transparent;
    text-decoration: none;
}

.module-tag.more {
    background: #e2e8f0;
    color: #4a5568;
    cursor: default;
}

.module-tag.more:hover {
    background: #e2e8f0;
    color: #4a5568;
    border-color: #e2e8f0;
}

.chapter-actions {
    display: flex;
    gap: 0.6rem;
    margin-left: 1rem;
}

.btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    background: white;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.85rem;
    text-decoration: none;
    color: #667eea;
}

.btn-action:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: transparent;
}

.btn-action.delete {
    color: #ef4444;
}

.btn-action.delete:hover {
    background: #ef4444;
}

/* ========== EMPTY CHAPTER STATE ========== */
.empty-chapters {
    text-align: center;
    padding: 2.5rem 1.5rem;
    background: #f9fafb;
    border-radius: 10px;
    color: #a0aec0;
}

.empty-chapters i {
    font-size: 2rem;
    margin-bottom: 1rem;
    display: block;
}

.empty-chapters p {
    font-size: 0.9rem;
    margin: 0.5rem 0 1rem 0;
    color: #718096;
}

.btn-add-first {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 1.2rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
}

.btn-add-first:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    color: white;
}

/* ========== EMPTY STATE FULL ========== */
.empty-state-full {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 14px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
}

.empty-visual {
    font-size: 4rem;
    color: #cbd5e0;
    margin-bottom: 1.5rem;
}

.empty-title {
    font-size: 1.6rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.8rem;
}

.empty-text {
    font-size: 1rem;
    color: #718096;
    margin-bottom: 2rem;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
}

/* ========== DROPDOWN MENU ========== */
.dropdown-menu {
    border: none;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
    padding: 0.5rem 0;
}

.dropdown-item {
    padding: 0.7rem 1rem;
    color: #2d3748;
    font-size: 0.9rem;
    border-radius: 0;
    transition: all 0.2s ease;
}

.dropdown-item:hover,
.dropdown-item:focus {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.dropdown-item.text-danger:hover {
    background: #ef4444;
    color: white;
}

.dropdown-divider {
    margin: 0.3rem 0;
    opacity: 0.1;
}

/* ========== ANIMATIONS ========== */
@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

/* ========== RESPONSIVE DESIGN ========== */
@media (max-width: 768px) {
    .manage-content-page {
        padding: 1rem 0;
    }

    .header-section {
        margin-bottom: 2rem;
    }

    .header-content {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .header-content .page-title {
        font-size: 1.6rem;
    }

    .title-icon {
        width: 40px;
        height: 40px;
        font-size: 1.2rem;
    }

    .class-card-header {
        flex-direction: column;
        gap: 1rem;
    }

    .header-right {
        width: 100%;
        justify-content: space-between;
    }

    .class-stats {
        flex-direction: column;
        gap: 1rem;
    }

    .class-stats .divider {
        display: none;
    }

    .chapter-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .chapter-actions {
        width: 100%;
        justify-content: flex-start;
        margin-left: 0;
    }

    .chapters-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    /* Modules Modal Responsive */
    .module-card {
        flex-wrap: wrap;
    }

    .module-info {
        min-width: 200px;
    }

    .module-status-group {
        width: 100%;
        flex-direction: row;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .module-actions {
        width: 100%;
        justify-content: flex-start;
    }

    .modal-header-modules {
        padding: 1.2rem;
    }

    .modal-body-modules {
        padding: 1.5rem;
    }
}

@media (max-width: 480px) {
    .header-content .page-title {
        font-size: 1.3rem;
    }

    .page-subtitle {
        font-size: 0.85rem;
    }

    .btn-create-class {
        padding: 0.8rem 1.5rem;
        font-size: 0.85rem;
    }

    .class-name {
        font-size: 1.1rem;
    }

    .class-description {
        font-size: 0.85rem;
    }

    .module-tags {
        gap: 0.4rem;
    }

    .module-tag {
        font-size: 0.7rem;
        padding: 0.3rem 0.6rem;
    }

    .module-card {
        gap: 0.8rem;
        padding: 0.9rem;
    }

    .module-type-indicator {
        width: 50px;
        height: 50px;
        font-size: 1.2rem;
    }

    .module-title {
        font-size: 0.9rem;
    }

    .action-btn {
        width: 32px;
        height: 32px;
        font-size: 0.8rem;
    }

    .status-badge {
        font-size: 0.75rem;
        padding: 0.3rem 0.7rem;
    }

    .modal-footer-modules .btn {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }
}
</style>
@endsection

@section('scripts')
<script>
    // Enhanced Modal Management
    document.addEventListener('DOMContentLoaded', function() {
        // Clean up any existing modals on page load
        const stuckBackdrops = document.querySelectorAll('.modal-backdrop');
        stuckBackdrops.forEach(function(backdrop) {
            backdrop.remove();
        });
        
        if (!document.querySelector('.modal.show')) {
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }
        
        // Enhanced Modal Management
        document.addEventListener('hidden.bs.modal', function () {
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(function(backdrop) {
                backdrop.remove();
            });
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });
        
        document.addEventListener('show.bs.modal', function (e) {
            const existingBackdrops = document.querySelectorAll('.modal-backdrop');
            existingBackdrops.forEach(function(backdrop) {
                backdrop.remove();
            });
            
            setTimeout(function() {
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.style.setProperty('background-color', 'rgba(0, 0, 0, 0.8)', 'important');
                    backdrop.style.setProperty('opacity', '0.8', 'important');
                }
            }, 10);

            // Animate module cards when modal opens
            const moduleCards = e.target.querySelectorAll('.module-card');
            moduleCards.forEach((card, index) => {
                card.style.animation = `slideInUp 0.5s ease-out ${index * 0.08}s both`;
            });
        });
        
        document.addEventListener('shown.bs.modal', function () {
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.style.setProperty('background-color', 'rgba(0, 0, 0, 0.8)', 'important');
                backdrop.style.setProperty('opacity', '0.8', 'important');
            }
        });
        
        // Emergency click handler for stuck backdrop
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.modal') && document.querySelector('.modal-backdrop')) {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(function(backdrop) {
                    backdrop.remove();
                });
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            }
        });
        
        // Smooth scroll animation for stats cards
        const statCards = document.querySelectorAll('.stat-item');
        statCards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
        });
        
        // Add ripple effect to buttons
        const buttons = document.querySelectorAll('.btn, .action-btn, .btn-add-module');
        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                if (this.disabled) return;
                
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.classList.add('ripple');
                
                this.appendChild(ripple);
                
                setTimeout(() => ripple.remove(), 600);
            });
        });
        
        // Stagger animation for class cards
        const classCards = document.querySelectorAll('.class-card-wrapper');
        classCards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.08}s`;
        });
        
        // Handle SweetAlert success messages
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#667eea',
                confirmButtonText: 'OK',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: true,
                background: 'linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)',
                customClass: {
                    popup: 'swal-popup-custom'
                }
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#ff6b6b',
                confirmButtonText: 'OK',
                background: 'linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)'
            });
        @endif
    });

    // Add ripple effect styles dynamically
    const style = document.createElement('style');
    style.innerHTML = `
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
            transform: scale(0);
            animation: ripple-animation 0.6s ease-out;
            pointer-events: none;
            z-index: 1;
        }

        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .stat-item {
            animation: slideInUp 0.6s ease-out backwards;
        }

        .swal-popup-custom {
            border-radius: 15px !important;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3) !important;
        }
    `;
    document.head.appendChild(style);
</script>
@endsection
