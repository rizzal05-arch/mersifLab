@extends('layouts.app')

@section('title', 'Select Module Type - ' . $chapter->title)

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-10 offset-md-1">
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
                        {{ $chapter->title }}
                    </li>
                </ol>
            </nav>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-plus me-2"></i>Add New Module
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Class: <strong>{{ $chapter->class->name }}</strong></h6>
                            <h6 class="text-muted">Chapter: <strong>{{ $chapter->title }}</strong></h6>
                        </div>
                        <div class="col-md-6 text-end">
                            <small class="text-muted">
                                Current modules: <strong>{{ $chapter->modules()->count() }}</strong>
                            </small>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="mb-4">Choose Module Type:</h6>

                    <div class="row">
                        <!-- Text Module -->
                        <div class="col-md-4 mb-3">
                            <div class="card border-2 border-primary h-100 module-type-card" style="cursor: pointer; transition: all 0.3s;">
                                <div class="card-body text-center py-5">
                                    <i class="fas fa-align-left" style="font-size: 3rem; color: #0d6efd;"></i>
                                    <h5 class="card-title mt-3">Text Module</h5>
                                    <p class="card-text text-muted small">Rich text content with formatting, images, and embedded media</p>
                                    <a href="{{ route('teacher.modules.create.text', $chapter) }}" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-arrow-right me-1"></i>Create Text
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Document Module -->
                        <div class="col-md-4 mb-3">
                            <div class="card border-2 border-success h-100 module-type-card" style="cursor: pointer; transition: all 0.3s;">
                                <div class="card-body text-center py-5">
                                    <i class="fas fa-file-pdf" style="font-size: 3rem; color: #198754;"></i>
                                    <h5 class="card-title mt-3">Document Module</h5>
                                    <p class="card-text text-muted small">Upload PDF files (max 50MB) for downloadable resources</p>
                                    <a href="{{ route('teacher.modules.create.document', $chapter) }}" 
                                       class="btn btn-success btn-sm">
                                        <i class="fas fa-arrow-right me-1"></i>Upload PDF
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Video Module -->
                        <div class="col-md-4 mb-3">
                            <div class="card border-2 border-warning h-100 module-type-card" style="cursor: pointer; transition: all 0.3s;">
                                <div class="card-body text-center py-5">
                                    <i class="fas fa-video" style="font-size: 3rem; color: #ffc107;"></i>
                                    <h5 class="card-title mt-3">Video Module</h5>
                                    <p class="card-text text-muted small">Upload video file (max 500MB) or embed from YouTube/external URL</p>
                                    <a href="{{ route('teacher.modules.create.video', $chapter) }}" 
                                       class="btn btn-warning btn-sm">
                                        <i class="fas fa-arrow-right me-1"></i>Add Video
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Help Section -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-lightbulb text-warning me-2"></i>Module Guidelines
                    </h6>
                    <ul class="small text-muted mb-0">
                        <li><strong>Text Module:</strong> Use for lessons, explanations, and content with embedded media</li>
                        <li><strong>Document Module:</strong> Upload study materials, guides, worksheets as PDF files</li>
                        <li><strong>Video Module:</strong> Upload educational videos or link to YouTube/external sources</li>
                        <li>All modules can be published/unpublished and edited at any time</li>
                    </ul>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('teacher.manage.content') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Management
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.module-type-card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.module-type-card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    transform: translateY(-4px);
}
</style>
@endsection
@endsection
