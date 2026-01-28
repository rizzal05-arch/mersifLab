@extends('layouts.app')

@section('title', $module->title . ' - ' . $class->name)

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/welcome.css') }}">
<style>
    .module-container {
        display: flex;
        min-height: calc(100vh - 200px);
        position: relative;
        align-items: flex-start;
    }

    .module-sidebar {
        width: 320px;
        background: #f8f9fa;
        border-right: 1px solid #e0e0e0;
        padding: 1.5rem;
        overflow-y: auto;
        overflow-x: hidden;
        position: sticky;
        height: calc(100vh - 80px);
        max-height: calc(100vh - 80px);
        top: 80px;
        align-self: flex-start;
        z-index: 100;
        pointer-events: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: #cbd5e0 #f8f9fa;
    }

    .module-sidebar::-webkit-scrollbar {
        width: 8px;
    }

    .module-sidebar::-webkit-scrollbar-track {
        background: #f8f9fa;
    }

    .module-sidebar::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 4px;
    }

    .module-sidebar::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }
    
    .module-link,
    .chapter-header {
        cursor: default;
    }

    .back-to-course-link {
        color: #2196f3 !important;
        font-size: 0.9rem;
        cursor: pointer;
        pointer-events: auto;
        transition: all 0.2s ease;
        display: inline-block;
    }

    .back-to-course-link:hover {
        color: #1976d2 !important;
        text-decoration: underline !important;
    }

    .module-content {
        flex: 1;
        padding: 2rem;
        background: white;
        min-width: 0;
    }

    .course-progress {
        background: transparent;
        border-radius: 0;
        padding: 1rem 0;
        margin-bottom: 1.5rem;
        border-bottom: 1px solid #e0e0e0;
    }

    .progress-bar-custom {
        height: 8px;
        background: #e0e0e0;
        border-radius: 4px;
        overflow: hidden;
        margin-top: 0.5rem;
    }

    .progress-fill {
        height: 100%;
        background: #2196f3;
        transition: width 0.3s ease;
    }

    .chapter-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .chapter-item {
        margin-bottom: 1.5rem;
    }

    .chapter-header {
        padding: 1rem 0 0.5rem 0;
        background: transparent;
        border-radius: 0;
        cursor: default;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s ease;
        border: none;
        border-bottom: 1px solid #e0e0e0;
        color: #999;
        font-size: 0.95rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .chapter-header:hover {
        background: transparent;
    }

    .chapter-header.active {
        background: transparent;
        color: #2196f3;
        font-weight: 600;
        border-bottom: 2px solid #2196f3;
    }

    .module-list {
        list-style: none;
        padding: 0;
        margin: 0.5rem 0 0 0;
        display: block;
        padding-left: 1rem;
    }

    .module-list.show {
        display: block;
    }

    .module-link {
        padding: 0.5rem 1rem;
        display: flex;
        align-items: flex-start;
        text-decoration: none;
        color: #999;
        border-radius: 4px;
        transition: all 0.2s ease;
        margin-bottom: 0.5rem;
        position: relative;
        pointer-events: none;
        cursor: default;
        font-size: 0.9rem;
        background: transparent;
        border: none;
        opacity: 0.7;
    }

    .module-link:hover {
        background: transparent;
        opacity: 0.7;
    }

    .module-link.active {
        background: rgba(33, 150, 243, 0.1);
        color: #2196f3;
        border-left: 3px solid #2196f3;
        font-weight: 600;
        opacity: 1;
        padding-left: calc(1rem - 3px);
    }

    .module-link.active:hover {
        background: rgba(33, 150, 243, 0.15);
    }

    .module-link-content {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .module-link-icon {
        margin-right: 0.75rem;
        width: 20px;
        text-align: center;
        margin-top: 2px;
    }

    .module-link-title {
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
    }

    .module-link-meta {
        font-size: 0.75rem;
        color: #666;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .module-radio {
        margin-right: 0.75rem;
        margin-top: 4px;
    }

    .video-player-container {
        background: #000;
        border-radius: 8px;
        padding: 0;
        position: relative;
        margin-bottom: 2rem;
        max-height: 600px;
        overflow: hidden;
        width: 100%;
    }

    .video-player-container iframe,
    .video-player-container video {
        position: relative;
        width: 100%;
        max-height: 600px;
        object-fit: contain;
        display: block;
    }

    .youtube-video-wrapper {
        position: relative;
        width: 100%;
        padding-bottom: 56.25%; /* 16:9 aspect ratio */
        height: 0;
        overflow: hidden;
        max-height: 600px;
        background: #000;
        user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
    }

    .youtube-video-wrapper iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: none;
        max-height: 600px;
    }

    .text-content {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        line-height: 1.8;
    }

    .pdf-viewer-container {
        background: #f5f5f5;
        border-radius: 8px;
        padding: 0;
        max-height: 500px;
        overflow: hidden;
        position: relative;
        z-index: 1 !important;
        isolation: isolate;
    }

    #pdf-viewer {
        background: #525252;
        padding: 20px;
        position: relative;
        z-index: 1 !important;
        isolation: isolate;
        user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        -webkit-touch-callout: none;
        -webkit-tap-highlight-color: transparent;
    }

    #pdfCanvas {
        background: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        pointer-events: auto;
        position: relative;
    }

    .pdf-watermark {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 10;
        background-image: repeating-linear-gradient(
            45deg,
            transparent,
            transparent 100px,
            rgba(255, 0, 0, 0.03) 100px,
            rgba(255, 0, 0, 0.03) 200px
        );
    }

    .pdf-watermark::before {
        content: '{{ auth()->check() ? auth()->user()->name : "Protected Content" }}';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-45deg);
        font-size: 48px;
        color: rgba(255, 0, 0, 0.1);
        font-weight: bold;
        white-space: nowrap;
        pointer-events: none;
        z-index: 11;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
    }

    .pdf-protection-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1000;
        pointer-events: none;
    }
    
    /* Ensure PDF canvas wrapper doesn't extend beyond viewer */
    #pdfCanvasWrapper {
        position: relative;
        display: inline-block;
        z-index: 2;
        overflow: hidden;
    }

    #pdfCanvasShield {
        position: absolute !important;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1001 !important;
        background: transparent;
        cursor: default;
        user-select: none !important;
        -webkit-user-select: none !important;
        -moz-user-select: none !important;
        -ms-user-select: none !important;
        pointer-events: auto !important;
        /* Ensure shield only covers canvas, not entire page */
        max-width: 100%;
        max-height: 100%;
    }
    
    /* Ensure PDF viewer container doesn't block sidebar */
    .pdf-viewer-container {
        position: relative;
        z-index: 1;
        isolation: isolate;
    }
    
    #pdf-viewer {
        position: relative;
        z-index: 1;
        isolation: isolate;
    }
    
    /* Ensure mark complete button is always clickable */
    #markCompleteBtn,
    .module-header button {
        position: relative !important;
        z-index: 99999 !important;
        pointer-events: auto !important;
        cursor: pointer !important;
        isolation: isolate !important;
    }
    
    .module-header {
        position: relative;
        z-index: 99998 !important;
        isolation: isolate !important;
    }
    
    /* Ensure no overlay blocks the button */
    .module-content {
        position: relative;
        z-index: 1;
    }
    
    /* Make sure PDF/video containers don't extend beyond their bounds */
    .video-player-container,
    .youtube-video-wrapper {
        position: relative;
        z-index: 1;
        overflow: hidden;
    }
    
    /* Ensure mark complete button is always clickable */
    #markCompleteBtn {
        position: relative;
        z-index: 10000;
        pointer-events: auto;
        cursor: pointer;
    }

    #pdfCanvasWrapper * {
        user-select: none !important;
        -webkit-user-select: none !important;
        -moz-user-select: none !important;
        -ms-user-select: none !important;
        -webkit-touch-callout: none !important;
    }

    .module-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e0e0e0;
        position: relative;
        z-index: 10000;
    }
    
    #markCompleteBtn {
        position: relative;
        z-index: 10001 !important;
        pointer-events: auto !important;
    }

    .module-navigation {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #e0e0e0;
    }

    .module-link.completed {
        position: relative;
    }

    .module-link.completed::after {
        content: '\f00c';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #28a745;
        font-size: 0.875rem;
    }

    @media (max-width: 768px) {
        .module-sidebar {
            position: relative;
            width: 100%;
            height: auto;
        }
        .module-content {
            margin-left: 0;
        }
    }
</style>
@endsection

@section('content')
<div class="module-container">
    <!-- Sidebar Course Navigation -->
    <div class="module-sidebar" id="moduleSidebar">
        <a href="{{ route('course.detail', $class->id) }}" class="back-to-course-link text-decoration-none mb-3 d-block">
            <i class="fas fa-arrow-left me-2"></i>Kembali ke Course
        </a>

        <h5 class="fw-bold mb-3" style="color: #666;">{{ $class->name }}</h5>

        @if($isEnrolled)
            <!-- Progress Bar -->
            <div class="course-progress">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="text-muted">Your Progress</small>
                    <strong>{{ number_format($progress, 0) }}%</strong>
                </div>
                <div class="progress-bar-custom">
                    <div class="progress-fill" style="width: {{ $progress }}%"></div>
                </div>
            </div>
        @endif

        <hr class="my-3">

        <!-- Chapters & Modules List -->
        <ul class="chapter-list">
            @foreach($class->chapters as $ch)
                <li class="chapter-item">
                    <div class="chapter-header {{ $ch->id == $chapter->id ? 'active' : '' }}">
                        <span>{{ $ch->title }}</span>
                    </div>
                    <ul class="module-list show" id="chapter-{{ $ch->id }}">
                        @foreach($ch->modules as $mod)
                            @php
                                $isCompleted = isset($completedModules) && in_array($mod->id, $completedModules);
                            @endphp
                            <li>
                                <div class="module-link {{ $mod->id == $module->id ? 'active' : '' }} {{ $isCompleted ? 'completed' : '' }}">
                                    <input type="radio" class="module-radio" {{ $mod->id == $module->id ? 'checked' : '' }} disabled>
                                    <div class="module-link-content">
                                        <div class="module-link-title">{{ $mod->title }}</div>
                                        <div class="module-link-meta">
                                            @if(auth()->user() && auth()->user()->isAdmin())
                                                @if($mod->approval_status === 'pending_approval')
                                                    <span class="badge bg-warning text-dark" style="font-size: 10px;">Pending</span>
                                                @elseif($mod->approval_status === 'rejected')
                                                    <span class="badge bg-danger" style="font-size: 10px;">Rejected</span>
                                                @elseif($mod->approval_status === 'approved')
                                                    <span class="badge bg-success" style="font-size: 10px;">Approved</span>
                                                @endif
                                            @endif
                                            
                                            @if($mod->type === 'video')
                                                <i class="fas fa-file-video text-primary"></i>
                                                <span>MP4</span>
                                            @elseif($mod->type === 'document')
                                                <i class="fas fa-file-word text-primary"></i>
                                                <span>DOC</span>
                                            @elseif($mod->type === 'text')
                                                <i class="fas fa-file-alt text-info"></i>
                                                <span>Text</span>
                                            @else
                                                <i class="fas fa-file text-secondary"></i>
                                                <span>{{ $mod->type_label ?? 'Unknown' }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="module-content" id="moduleContent">
        <!-- Module Header -->
        <div class="module-header">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h2 class="fw-bold mb-0">{{ $module->title }}</h2>
                    @if(auth()->user() && auth()->user()->isAdmin())
                        @if($module->approval_status === 'pending_approval')
                            <span class="badge bg-warning text-dark">Pending Approval</span>
                        @elseif($module->approval_status === 'rejected')
                            <span class="badge bg-danger">Rejected</span>
                        @elseif($module->approval_status === 'approved')
                            <span class="badge bg-success">Approved</span>
                        @endif
                    @endif
                </div>
                <p class="text-muted mb-0">{{ $chapter->title }}</p>
            </div>
            @if($isEnrolled)
                @php
                    $isModuleCompleted = isset($completedModules) && in_array($module->id, $completedModules);
                @endphp
                @if($isModuleCompleted)
                    <button class="btn btn-success" disabled>
                        <i class="fas fa-check-circle me-2"></i>Completed
                    </button>
                @else
                    <button class="btn btn-primary" id="markCompleteBtn">
                        <i class="fas fa-check me-2"></i>Mark as Complete
                    </button>
                @endif
            @endif
        </div>

        <!-- Module Content Based on Type -->
        @if($module->type === 'video')
            <!-- Video Module -->
            <div class="video-player-container">
                @if($module->video_url)
                    @php
                        $videoId = null;
                        $videoType = null;
                        
                        // Check for YouTube
                        if (str_contains($module->video_url, 'youtube.com') || str_contains($module->video_url, 'youtu.be')) {
                            // Try multiple YouTube URL patterns
                            // Pattern 1: youtu.be/VIDEO_ID (with or without query params)
                            if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]{11})(?:\?|$|&)/', $module->video_url, $matches)) {
                                $videoId = $matches[1];
                                $videoType = 'youtube';
                            }
                            // Pattern 2: youtube.com/watch?v=VIDEO_ID
                            elseif (preg_match('/youtube\.com\/watch\?.*v=([a-zA-Z0-9_-]{11})/', $module->video_url, $matches)) {
                                $videoId = $matches[1];
                                $videoType = 'youtube';
                            }
                            // Pattern 3: youtube.com/embed/VIDEO_ID
                            elseif (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/', $module->video_url, $matches)) {
                                $videoId = $matches[1];
                                $videoType = 'youtube';
                            }
                            // Pattern 4: Generic YouTube pattern (fallback)
                            elseif (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $module->video_url, $matches)) {
                                $videoId = $matches[1];
                                $videoType = 'youtube';
                            }
                        }
                        // Check for Vimeo
                        elseif (str_contains($module->video_url, 'vimeo.com')) {
                            if (preg_match('/vimeo\.com\/(\d+)/', $module->video_url, $matches)) {
                                $videoId = $matches[1];
                                $videoType = 'vimeo';
                            }
                        }
                    @endphp
                    
                    @if($videoType === 'youtube' && $videoId)
                        <div class="youtube-video-wrapper">
                            <iframe id="youtubePlayer" 
                                    src="https://www.youtube.com/embed/{{ $videoId }}?rel=0&modestbranding=1&controls=1&disablekb=1&iv_load_policy=3&playsinline=1&showinfo=0&cc_load_policy=0&fs=0" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen
                                    style="width: 100%; height: 100%;"></iframe>
                        </div>
                    @elseif($videoType === 'vimeo' && $videoId)
                        <div class="youtube-video-wrapper">
                            <iframe src="https://player.vimeo.com/video/{{ $videoId }}" 
                                    frameborder="0" 
                                    allow="autoplay; fullscreen; picture-in-picture" 
                                    allowfullscreen
                                    style="width: 100%; height: 100%;"></iframe>
                        </div>
                    @else
                        <div class="d-flex align-items-center justify-content-center h-100 text-white" style="min-height: 400px;">
                            <div class="text-center">
                                <i class="fas fa-play-circle fa-4x mb-3"></i>
                                <p>Video Player</p>
                                <p class="small mb-3">Unable to load video. Please check the video URL.</p>
                                <a href="{{ $module->video_url }}" target="_blank" class="btn btn-light">
                                    <i class="fas fa-external-link-alt me-2"></i>Open Video in New Tab
                                </a>
                            </div>
                        </div>
                    @endif
                @elseif($module->file_path)
                    <video controls controlsList="nodownload" style="width: 100%; height: 100%;" oncontextmenu="return false;">
                        <source src="{{ route('module.file', [$class->id, $chapter->id, $module->id]) }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                @else
                    <div class="d-flex align-items-center justify-content-center h-100 text-white">
                        <div class="text-center">
                            <i class="fas fa-play-circle fa-4x mb-3"></i>
                            <p>Video Player</p>
                            <p class="small">Video content will be displayed here</p>
                        </div>
                    </div>
                @endif
            </div>

        @elseif($module->type === 'document')
            <!-- PDF/Document Module -->
            <div class="pdf-viewer-container">
                @if($module->file_path)
                    <!-- User sudah login dan enroll (dijamin oleh controller) -->
                    <div id="pdf-viewer" style="width: 100%; max-height: 500px; height: 500px; overflow: auto; position: relative; background: #525252;">
                            <div id="pdf-loading" class="text-center text-white p-5" style="display: flex; align-items: center; justify-content: center; height: 100%;">
                                <div>
                                    <i class="fas fa-spinner fa-spin fa-3x mb-3"></i>
                                    <p>Memuat PDF...</p>
                                </div>
                            </div>
                            <div class="pdf-watermark"></div>
                            <div class="pdf-protection-overlay" id="pdfProtectionOverlay"></div>
                            <div id="pdfCanvasWrapper" style="position: relative; display: none; margin: 20px auto;">
                                <canvas id="pdfCanvas" style="display: block; margin: 0 auto; border: 1px solid #ccc; background: white; pointer-events: none;"></canvas>
                                <div id="pdfCanvasShield" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1000; cursor: default;"></div>
                            </div>
                        </div>
                        <div style="text-align: center; padding: 15px; background: #f0f0f0; border-radius: 0 0 8px 8px; border-top: 1px solid #ddd;">
                        <div class="mb-2">
                            <button id="prevBtn" class="btn btn-sm btn-outline-primary" style="margin-right: 10px;" disabled>
                                <i class="fas fa-chevron-left"></i> Sebelumnya
                            </button>
                            <span id="pageInfo" style="margin: 0 10px; font-weight: 500;">
                                Halaman <span id="pageNum">1</span> dari <span id="pageCount">0</span>
                            </span>
                            <button id="nextBtn" class="btn btn-sm btn-outline-primary" style="margin-left: 10px;" disabled>
                                Selanjutnya <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                        {{-- Download button removed for security --}}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-file-pdf fa-5x text-danger mb-3"></i>
                        <h5>PDF Resource</h5>
                        <p class="text-muted">PDF document will be displayed here</p>
                        @if($module->file_name)
                            <p class="small text-muted">File: {{ $module->file_name }}</p>
                        @endif
                    </div>
                @endif
            </div>

        @else
            <!-- Text Module -->
            <div class="text-content">
                {!! $module->content ?? '<p>No content available for this module.</p>' !!}
            </div>
        @endif

        <!-- Module Navigation -->
        <div class="module-navigation">
            @if($previousModule)
                <a href="{{ route('module.show', [$class->id, $previousModule->chapter_id, $previousModule->id]) }}" 
                   class="btn btn-outline-primary">
                    <i class="fas fa-chevron-left me-2"></i>Previous Module
                </a>
            @endif
            <div class="flex-grow-1"></div>
            @if($nextModule)
                <a href="{{ route('module.show', [$class->id, $nextModule->chapter_id, $nextModule->id]) }}" 
                   class="btn btn-primary">
                    Next Module<i class="fas fa-chevron-right ms-2"></i>
                </a>
            @else
                <a href="{{ route('course.detail', $class->id) }}" class="btn btn-success">
                    <i class="fas fa-check me-2"></i>Complete Course
                </a>
            @endif
        </div>
    </div>
</div>

<script>

// Sidebar is now display-only, no interactive functions needed

@if($isEnrolled)
document.addEventListener('DOMContentLoaded', function() {
    const markCompleteBtn = document.getElementById('markCompleteBtn');
    
    if (markCompleteBtn) {
        // Ensure button is always clickable - remove any overlays blocking it
        markCompleteBtn.style.position = 'relative';
        markCompleteBtn.style.zIndex = '99999';
        markCompleteBtn.style.pointerEvents = 'auto';
        markCompleteBtn.style.cursor = 'pointer';
        
        // Add multiple event listeners to ensure it works
        function handleMarkComplete(e) {
            // Stop propagation to prevent protection scripts from blocking
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            const btn = markCompleteBtn;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
            
            fetch('{{ route("module.complete", [$class->id, $module->id]) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || 'Failed to mark as complete');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show success popup
                    const popupConfig = {
                        icon: 'success',
                        title: data.courseCompleted ? 'Selamat!' : 'Berhasil!',
                        html: `
                            <div style="text-align: center;">
                                <i class="fas fa-${data.courseCompleted ? 'trophy' : 'check-circle'}" style="font-size: 3rem; color: ${data.courseCompleted ? '#ffc107' : '#28a745'}; margin-bottom: 1rem;"></i>
                                <p style="font-size: 1.1rem; margin-bottom: 0.5rem; font-weight: 600;">${data.message}</p>
                                ${data.courseCompleted ? 
                                    '<p style="color: #6c757d; font-size: 0.9rem;">Anda telah menyelesaikan semua module dalam course ini!</p>' :
                                    `<p style="color: #6c757d; font-size: 0.9rem;">Progress: ${Math.round(data.progress)}% (${data.completed}/${data.total} modules)</p>`
                                }
                            </div>
                        `,
                        confirmButtonText: data.courseCompleted ? 'Lihat Course' : 'Lanjutkan Belajar',
                        confirmButtonColor: '#667eea',
                        timer: data.courseCompleted ? 5000 : 3000,
                        timerProgressBar: true,
                        customClass: {
                            popup: 'animated-popup',
                            backdrop: 'swal2-backdrop-smooth'
                        },
                        showClass: {
                            popup: 'swal2-show-smooth',
                            backdrop: 'swal2-backdrop-show-smooth'
                        },
                        hideClass: {
                            popup: 'swal2-hide-smooth',
                            backdrop: 'swal2-backdrop-hide-smooth'
                        }
                    };
                    
                    Swal.fire(popupConfig).then((result) => {
                        if (data.courseCompleted && result.isConfirmed) {
                            // Redirect to course detail page
                            window.location.href = '{{ route("course.detail", $class->id) }}';
                        }
                    });
                    
                    // Update button
                    btn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Completed';
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-success');
                    btn.disabled = true;
                    
                    // Update progress bar
                    const progressBar = document.querySelector('.progress-fill');
                    const progressText = document.querySelector('.course-progress strong');
                    if (progressBar && progressText) {
                        progressBar.style.width = data.progress + '%';
                        progressText.textContent = Math.round(data.progress) + '%';
                    }
                    
                    // Update module link to show completed
                    const currentModuleLink = document.querySelector('.module-link.active');
                    if (currentModuleLink) {
                        currentModuleLink.classList.add('completed');
                    }
                    
                    // Update all module links in sidebar
                    const moduleLinks = document.querySelectorAll('.module-link');
                    moduleLinks.forEach(link => {
                        if (link.getAttribute('href') && link.getAttribute('href').includes('/module/{{ $module->id }}')) {
                            link.classList.add('completed');
                        }
                    });
                    
                    // Reload page after 2 seconds to update sidebar and progress
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Failed to mark as complete',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                btn.disabled = false;
                btn.innerHTML = originalText;
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: error.message || 'An error occurred. Please try again.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc3545'
                });
            });
        }
        
        // Add event listener with capture: true to intercept before protection scripts
        markCompleteBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            e.stopImmediatePropagation();
            handleMarkComplete(e);
        }, true); // Use capture phase to intercept first
        
        // Also add in bubble phase as backup
        markCompleteBtn.addEventListener('click', handleMarkComplete, false);
        
        // Add mousedown to prevent blocking
        markCompleteBtn.addEventListener('mousedown', function(e) {
            e.stopPropagation();
            e.stopImmediatePropagation();
        }, true); // Capture phase
        
        markCompleteBtn.addEventListener('mousedown', function(e) {
            e.stopPropagation();
            e.stopImmediatePropagation();
        }, false); // Bubble phase
        
        // Add mouseup as well
        markCompleteBtn.addEventListener('mouseup', function(e) {
            e.stopPropagation();
            e.stopImmediatePropagation();
        }, true);
        
        // Ensure button stays on top and clickable
        const observer = new MutationObserver(function(mutations) {
            markCompleteBtn.style.zIndex = '99999';
            markCompleteBtn.style.pointerEvents = 'auto';
            markCompleteBtn.style.position = 'relative';
            markCompleteBtn.style.cursor = 'pointer';
        });
        observer.observe(markCompleteBtn, { attributes: true, attributeFilter: ['style'] });
        
        // Force button to be on top every 100ms
        setInterval(function() {
            if (markCompleteBtn) {
                markCompleteBtn.style.zIndex = '99999';
                markCompleteBtn.style.pointerEvents = 'auto';
                markCompleteBtn.style.cursor = 'pointer';
            }
        }, 100);
        
        // Also ensure parent elements don't block
        const moduleHeader = markCompleteBtn.closest('.module-header');
        if (moduleHeader) {
            moduleHeader.style.zIndex = '10000';
            moduleHeader.style.position = 'relative';
        }
    }
});
@endif

@if($module->type === 'document' && $module->file_path)
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
(function() {
    const pdfPath = '{{ route("module.file", [$class->id, $chapter->id, $module->id]) }}';
    
    function fallbackToIframe() {
        const pdfViewer = document.getElementById('pdf-viewer');
        const loadingEl = document.getElementById('pdf-loading');
        if (pdfViewer && loadingEl) {
            loadingEl.style.display = 'none';
            const iframe = document.createElement('iframe');
            iframe.src = pdfPath + '#toolbar=0';
            iframe.style.cssText = 'width: 100%; height: 100%; border: none;';
            iframe.onerror = function() {
                // Jika iframe juga gagal, tampilkan pesan error
                pdfViewer.innerHTML = `
                    <div class="alert alert-danger m-4" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-circle fa-2x me-3"></i>
                            <div class="flex-grow-1">
                                <h5 class="alert-heading mb-2">Gagal Memuat PDF</h5>
                                <p class="mb-2">Tidak dapat memuat file PDF. Pastikan Anda sudah login dan memiliki akses ke course ini.</p>
                                <a href="{{ route('login') }}?redirect={{ urlencode(request()->fullUrl()) }}" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                `;
            };
            pdfViewer.innerHTML = '';
            pdfViewer.appendChild(iframe);
        }
    }

    function initPDFViewer() {
        if (typeof pdfjsLib === 'undefined') {
            console.error('PDF.js library not loaded');
            setTimeout(fallbackToIframe, 1000);
            return;
        }

        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        let pdfDoc = null;
        let currentPage = 1;

        async function renderPage(pageNum) {
            try {
                if (!pdfDoc) return;
                
                const page = await pdfDoc.getPage(pageNum);
                const canvas = document.getElementById('pdfCanvas');
                const loadingEl = document.getElementById('pdf-loading');
                if (!canvas) return;
                
                const ctx = canvas.getContext('2d');
                
                const viewport = page.getViewport({ scale: 1.8 });
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                
                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };
                
                await page.render(renderContext).promise;
                
                // Hide loading, show canvas wrapper
                if (loadingEl) loadingEl.style.display = 'none';
                const canvasWrapper = document.getElementById('pdfCanvasWrapper');
                if (canvasWrapper) {
                    canvasWrapper.style.display = 'block';
                    // Update shield size to match canvas exactly
                    const shield = document.getElementById('pdfCanvasShield');
                    if (shield && canvas) {
                        shield.style.width = canvas.width + 'px';
                        shield.style.height = canvas.height + 'px';
                        shield.style.left = '0';
                        shield.style.top = '0';
                    }
                    // Apply protection after canvas is rendered
                    setTimeout(function() {
                        applyCanvasProtection();
                    }, 200);
                }
                
                const pageNumEl = document.getElementById('pageNum');
                const pageCountEl = document.getElementById('pageCount');
                const prevBtn = document.getElementById('prevBtn');
                const nextBtn = document.getElementById('nextBtn');
                
                if (pageNumEl) pageNumEl.textContent = pageNum;
                if (pageCountEl) pageCountEl.textContent = pdfDoc.numPages;
                if (prevBtn) prevBtn.disabled = pageNum <= 1;
                if (nextBtn) nextBtn.disabled = pageNum >= pdfDoc.numPages;
            } catch (error) {
                console.error('Error rendering page:', error);
                const loadingEl = document.getElementById('pdf-loading');
                if (loadingEl) {
                    loadingEl.innerHTML = '<div class="text-center text-white p-5"><i class="fas fa-exclamation-triangle fa-3x mb-3"></i><p>Failed to render PDF page.</p></div>';
                }
            }
        }

        // Load PDF
        pdfjsLib.getDocument({
            url: pdfPath,
            withCredentials: true,
            httpHeaders: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).promise.then(function(doc) {
            pdfDoc = doc;
            const pageCountEl = document.getElementById('pageCount');
            if (pageCountEl) pageCountEl.textContent = doc.numPages;
            renderPage(currentPage);
        }).catch(function(error) {
            console.error('Error loading PDF:', error);
            const loadingEl = document.getElementById('pdf-loading');
            const pdfViewer = document.getElementById('pdf-viewer');
            
            if (loadingEl && pdfViewer) {
                // Cek apakah error karena unauthorized (401) atau forbidden (403)
                const errorMessage = error.message || '';
                const isAuthError = errorMessage.includes('401') || errorMessage.includes('403') || 
                                   errorMessage.includes('Unauthorized') || errorMessage.includes('Forbidden');
                
                if (isAuthError) {
                    // Tampilkan pesan peringatan untuk user yang belum login atau tidak punya akses
                    loadingEl.style.display = 'none';
                    pdfViewer.innerHTML = `
                        <div class="alert alert-warning m-4" role="alert" style="background: #fff3cd; border: 1px solid #ffc107;">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle fa-2x me-3 text-warning"></i>
                                <div class="flex-grow-1">
                                    <h5 class="alert-heading mb-2">Akses Ditolak</h5>
                                    <p class="mb-2">Anda tidak memiliki akses untuk melihat file PDF ini. Silakan login terlebih dahulu atau pastikan Anda sudah terdaftar di course ini.</p>
                                    <a href="{{ route('login') }}?redirect={{ urlencode(request()->fullUrl()) }}" class="btn btn-primary">
                                        <i class="fas fa-sign-in-alt me-2"></i>Login Sekarang
                                    </a>
                                    <span class="ms-2">atau</span>
                                    <a href="{{ route('register') }}" class="btn btn-outline-primary ms-2">
                                        <i class="fas fa-user-plus me-2"></i>Daftar Akun
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    // Error lainnya, coba fallback ke iframe
                    fallbackToIframe();
                }
            } else {
                fallbackToIframe();
            }
        });

        // Navigation buttons
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        if (prevBtn) {
            prevBtn.addEventListener('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    renderPage(currentPage);
                    const pdfViewer = document.getElementById('pdf-viewer');
                    if (pdfViewer) {
                        pdfViewer.scrollTop = 0;
                    }
                }
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function() {
                if (pdfDoc && currentPage < pdfDoc.numPages) {
                    currentPage++;
                    renderPage(currentPage);
                    const pdfViewer = document.getElementById('pdf-viewer');
                    if (pdfViewer) {
                        pdfViewer.scrollTop = 0;
                    }
                }
            });
        }

        // Comprehensive PDF Protection - Mencegah Copy, Download, Save, dan Pencurian
        const pdfViewer = document.getElementById('pdf-viewer');
        let pdfCanvas = document.getElementById('pdfCanvas');
        let pdfCanvasShield = document.getElementById('pdfCanvasShield');
        let pdfCanvasWrapper = document.getElementById('pdfCanvasWrapper');
        
        // Function to prevent all interactions
        function preventAll(e) {
            // Don't prevent events on mark complete button, sidebar, or navigation
            const target = e.target;
            if (target && (
                target.id === 'markCompleteBtn' || 
                target.closest('#markCompleteBtn') ||
                target.closest('.module-header') ||
                target.closest('.module-navigation') ||
                target.closest('.module-sidebar') ||
                target.closest('.module-link') ||
                target.closest('.chapter-header') ||
                target.closest('.back-to-course') ||
                target.closest('.chapter-list') ||
                target.closest('.module-list')
            )) {
                return true; // Allow event
            }
            // Only prevent if target is within PDF viewer
            const pdfViewer = document.getElementById('pdf-viewer');
            if (!pdfViewer || !pdfViewer.contains(target)) {
                return true; // Allow event outside PDF viewer
            }
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            return false;
        }

        // Function to prevent context menu
        function preventContextMenu(e) {
            // Don't prevent events on mark complete button, sidebar, or navigation
            const target = e.target;
            if (target && (
                target.id === 'markCompleteBtn' || 
                target.closest('#markCompleteBtn') ||
                target.closest('.module-header') ||
                target.closest('.module-navigation') ||
                target.closest('.module-sidebar') ||
                target.closest('.module-link') ||
                target.closest('.chapter-header') ||
                target.closest('.back-to-course') ||
                target.closest('.chapter-list') ||
                target.closest('.module-list')
            )) {
                return true; // Allow event
            }
            // Only prevent if target is within PDF viewer
            const pdfViewer = document.getElementById('pdf-viewer');
            if (!pdfViewer || !pdfViewer.contains(target)) {
                return true; // Allow event outside PDF viewer
            }
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            alert('Klik kanan tidak diizinkan untuk melindungi konten.');
            return false;
        }

        // Function to prevent copy
        function preventCopy(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            if (window.getSelection) {
                window.getSelection().removeAllRanges();
            }
            if (document.selection) {
                document.selection.empty();
            }
            alert('Copy tidak diizinkan untuk melindungi konten.');
            return false;
        }

        // Function to apply protection to canvas after it's rendered
        function applyCanvasProtection() {
            pdfCanvas = document.getElementById('pdfCanvas');
            pdfCanvasShield = document.getElementById('pdfCanvasShield');
            pdfCanvasWrapper = document.getElementById('pdfCanvasWrapper');
            
            if (!pdfCanvas || !pdfCanvasShield || !pdfCanvasWrapper) return;

            // Update shield size to match canvas exactly
            pdfCanvasShield.style.width = pdfCanvas.width + 'px';
            pdfCanvasShield.style.height = pdfCanvas.height + 'px';
            pdfCanvasShield.style.left = '0';
            pdfCanvasShield.style.top = '0';

            // Remove all existing event listeners and re-apply
            const newShield = pdfCanvasShield.cloneNode(true);
            pdfCanvasShield.parentNode.replaceChild(newShield, pdfCanvasShield);
            pdfCanvasShield = newShield;

            // Apply all protections to shield
            ['contextmenu', 'selectstart', 'copy', 'cut', 'paste', 'dragstart', 'mousedown', 'mouseup', 'click'].forEach(eventType => {
                pdfCanvasShield.addEventListener(eventType, function(e) {
                    // Don't prevent events on mark complete button, sidebar, or navigation
                    const target = e.target;
                    if (target && (
                        target.id === 'markCompleteBtn' || 
                        target.closest('#markCompleteBtn') ||
                        target.closest('.module-header') ||
                        target.closest('.module-navigation') ||
                        target.closest('.module-sidebar') ||
                        target.closest('.module-link') ||
                        target.closest('.chapter-header') ||
                        target.closest('.back-to-course') ||
                        target.closest('.chapter-list') ||
                        target.closest('.module-list')
                    )) {
                        return true; // Allow event
                    }
                    // Only prevent if target is within PDF viewer
                    const pdfViewer = document.getElementById('pdf-viewer');
                    if (!pdfViewer || !pdfViewer.contains(target)) {
                        return true; // Allow event outside PDF viewer
                    }
                    if (eventType === 'contextmenu') {
                        preventContextMenu(e);
                    } else if (eventType === 'copy' || eventType === 'cut') {
                        preventCopy(e);
                    } else if (eventType === 'mousedown' && (e.button === 1 || e.button === 2)) {
                        preventAll(e);
                    } else if (eventType !== 'click') {
                        preventAll(e);
                    }
                }, true);
            });

            // Ensure canvas cannot be interacted with
            pdfCanvas.style.pointerEvents = 'none';
            pdfCanvas.style.userSelect = 'none';
            pdfCanvas.style.webkitUserSelect = 'none';
            pdfCanvas.style.mozUserSelect = 'none';
            pdfCanvas.style.msUserSelect = 'none';
        }

        if (pdfViewer) {
            // Disable right-click - Multiple event listeners for maximum protection
            pdfViewer.addEventListener('contextmenu', preventContextMenu, true);
            pdfViewer.addEventListener('contextmenu', preventContextMenu, false);
            document.addEventListener('contextmenu', function(e) {
                // Don't prevent events on mark complete button, sidebar, or navigation
                const target = e.target;
                if (target && (
                    target.id === 'markCompleteBtn' || 
                    target.closest('#markCompleteBtn') ||
                    target.closest('.module-header') ||
                    target.closest('.module-navigation') ||
                    target.closest('.module-sidebar') ||
                    target.closest('.module-link') ||
                    target.closest('.chapter-header') ||
                    target.closest('.back-to-course') ||
                    target.closest('.chapter-list') ||
                    target.closest('.module-list')
                )) {
                    return true; // Allow event
                }
                // Only prevent if target is within PDF viewer
                if (pdfViewer && pdfViewer.contains(e.target) || (pdfCanvasWrapper && pdfCanvasWrapper.contains(e.target))) {
                    preventContextMenu(e);
                }
            }, true);

            // Disable text selection - Multiple methods (only within PDF viewer)
            pdfViewer.addEventListener('selectstart', function(e) {
                const target = e.target;
                if (target && (
                    target.closest('.module-sidebar') ||
                    target.closest('.module-link') ||
                    target.closest('.chapter-header') ||
                    target.closest('.back-to-course')
                )) {
                    return true;
                }
                preventAll(e);
            }, true);
            pdfViewer.addEventListener('select', preventAll, true);
            pdfViewer.addEventListener('selectionchange', function(e) {
                const target = e.target;
                if (target && (
                    target.closest('.module-sidebar') ||
                    target.closest('.module-link') ||
                    target.closest('.chapter-header') ||
                    target.closest('.back-to-course')
                )) {
                    return true;
                }
                if (window.getSelection && window.getSelection().toString().length > 0) {
                    window.getSelection().removeAllRanges();
                }
            }, true);

            pdfViewer.addEventListener('dragstart', preventAll, true);
            pdfViewer.addEventListener('drag', preventAll, true);
            pdfViewer.addEventListener('drop', preventAll, true);

            // Disable copy, cut, paste - Multiple event listeners
            pdfViewer.addEventListener('copy', preventCopy, true);
            pdfViewer.addEventListener('copy', preventCopy, false);
            pdfViewer.addEventListener('cut', preventAll, true);
            pdfViewer.addEventListener('paste', preventAll, true);
            
            // Global copy prevention when focus is on PDF
            document.addEventListener('copy', function(e) {
                const target = e.target;
                if (target && (
                    target.closest('.module-sidebar') ||
                    target.closest('.module-link') ||
                    target.closest('.chapter-header') ||
                    target.closest('.back-to-course')
                )) {
                    return true;
                }
                if (pdfViewer.contains(e.target) || (pdfCanvasWrapper && pdfCanvasWrapper.contains(e.target))) {
                    preventCopy(e);
                }
            }, true);

            // Disable keyboard shortcuts (Ctrl+C, Ctrl+A, Ctrl+S, Ctrl+P, dll)
            const disableKeys = function(e) {
                // Disable Ctrl+C, Ctrl+A, Ctrl+S, Ctrl+P, Ctrl+U, Ctrl+Shift+I, F12
                if (e.ctrlKey || e.metaKey) {
                    // Allow navigation (Ctrl+Arrow keys)
                    if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
                        return;
                    }
                    // Block all other Ctrl combinations
                    if (['c', 'C', 'a', 'A', 's', 'S', 'p', 'P', 'u', 'U', 'i', 'I', 'j', 'J'].includes(e.key)) {
                        e.preventDefault();
                        e.stopPropagation();
                        if (['c', 'C'].includes(e.key)) {
                            alert('Copy tidak diizinkan untuk melindungi konten.');
                        }
                        return false;
                    }
                }
                // Disable F12 (Developer Tools)
                if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && ['I', 'J', 'C'].includes(e.key))) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
                // Disable Print Screen
                if (e.key === 'PrintScreen') {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            };

            // Apply to both document and pdfViewer
            document.addEventListener('keydown', disableKeys, true);
            pdfViewer.addEventListener('keydown', disableKeys, true);

            // Disable print
            window.addEventListener('beforeprint', function(e) {
                e.preventDefault();
                alert('Print tidak diizinkan untuk melindungi konten.');
                return false;
            });

            // Protect canvas with shield overlay
            if (pdfCanvasWrapper) {
                // Shield overlay untuk mencegah semua interaksi langsung dengan canvas
                if (pdfCanvasShield) {
                    pdfCanvasShield.addEventListener('contextmenu', function(e) {
                        const target = e.target;
                        if (target && (
                            target.id === 'markCompleteBtn' || 
                            target.closest('#markCompleteBtn') ||
                            target.closest('.module-header') ||
                            target.closest('.module-navigation') ||
                            target.closest('.module-sidebar') ||
                            target.closest('.module-link') ||
                            target.closest('.chapter-header') ||
                            target.closest('.back-to-course') ||
                            target.closest('.chapter-list') ||
                            target.closest('.module-list')
                        )) {
                            return true;
                        }
                        preventContextMenu(e);
                    }, true);
                    pdfCanvasShield.addEventListener('selectstart', preventAll, true);
                    pdfCanvasShield.addEventListener('copy', preventCopy, true);
                    pdfCanvasShield.addEventListener('cut', preventAll, true);
                    pdfCanvasShield.addEventListener('paste', preventAll, true);
                    pdfCanvasShield.addEventListener('dragstart', preventAll, true);
                    pdfCanvasShield.addEventListener('mousedown', function(e) {
                        // Don't prevent events on mark complete button, sidebar, or navigation
                        const target = e.target;
                        if (target && (
                            target.id === 'markCompleteBtn' || 
                            target.closest('#markCompleteBtn') ||
                            target.closest('.module-header') ||
                            target.closest('.module-navigation') ||
                            target.closest('.module-sidebar') ||
                            target.closest('.module-link') ||
                            target.closest('.chapter-header') ||
                            target.closest('.back-to-course') ||
                            target.closest('.chapter-list') ||
                            target.closest('.module-list')
                        )) {
                            return true; // Allow event for sidebar and navigation
                        }
                        // Allow scroll but prevent selection
                        if (e.button === 1 || e.button === 2) { // Middle or right mouse button
                            e.preventDefault();
                            return false;
                        }
                    }, true);
                }

                // Protect canvas wrapper - but allow clicks on mark complete button, sidebar, or navigation
                pdfCanvasWrapper.addEventListener('contextmenu', function(e) {
                    const target = e.target;
                    if (target && (
                        target.id === 'markCompleteBtn' || 
                        target.closest('#markCompleteBtn') ||
                        target.closest('.module-header') ||
                        target.closest('.module-navigation') ||
                        target.closest('.module-sidebar') ||
                        target.closest('.module-link') ||
                        target.closest('.chapter-header') ||
                        target.closest('.back-to-course') ||
                        target.closest('.chapter-list') ||
                        target.closest('.module-list')
                    )) {
                        return true;
                    }
                    preventContextMenu(e);
                }, true);
                pdfCanvasWrapper.addEventListener('selectstart', function(e) {
                    const target = e.target;
                    if (target && (
                        target.closest('.module-sidebar') ||
                        target.closest('.module-link') ||
                        target.closest('.chapter-header') ||
                        target.closest('.back-to-course') ||
                        target.closest('.chapter-list') ||
                        target.closest('.module-list')
                    )) {
                        return true;
                    }
                    preventAll(e);
                }, true);
                pdfCanvasWrapper.addEventListener('copy', function(e) {
                    const target = e.target;
                    if (target && (
                        target.closest('.module-sidebar') ||
                        target.closest('.module-link') ||
                        target.closest('.chapter-header') ||
                        target.closest('.back-to-course') ||
                        target.closest('.chapter-list') ||
                        target.closest('.module-list')
                    )) {
                        return true;
                    }
                    preventCopy(e);
                }, true);
                pdfCanvasWrapper.addEventListener('cut', function(e) {
                    const target = e.target;
                    if (target && (
                        target.closest('.module-sidebar') ||
                        target.closest('.module-link') ||
                        target.closest('.chapter-header') ||
                        target.closest('.back-to-course') ||
                        target.closest('.chapter-list') ||
                        target.closest('.module-list')
                    )) {
                        return true;
                    }
                    preventAll(e);
                }, true);
                pdfCanvasWrapper.addEventListener('paste', function(e) {
                    const target = e.target;
                    if (target && (
                        target.closest('.module-sidebar') ||
                        target.closest('.module-link') ||
                        target.closest('.chapter-header') ||
                        target.closest('.back-to-course') ||
                        target.closest('.chapter-list') ||
                        target.closest('.module-list')
                    )) {
                        return true;
                    }
                    preventAll(e);
                }, true);
                pdfCanvasWrapper.addEventListener('dragstart', function(e) {
                    const target = e.target;
                    if (target && (
                        target.closest('.module-sidebar') ||
                        target.closest('.module-link') ||
                        target.closest('.chapter-header') ||
                        target.closest('.back-to-course') ||
                        target.closest('.chapter-list') ||
                        target.closest('.module-list')
                    )) {
                        return true;
                    }
                    preventAll(e);
                }, true);
            }

            // Protect canvas directly (backup protection)
            if (pdfCanvas) {
                pdfCanvas.addEventListener('dragstart', preventAll, true);
                pdfCanvas.addEventListener('contextmenu', preventContextMenu, true);
                pdfCanvas.addEventListener('selectstart', preventAll, true);
                pdfCanvas.addEventListener('copy', preventCopy, true);
                pdfCanvas.addEventListener('cut', preventAll, true);
                pdfCanvas.addEventListener('paste', preventAll, true);

                // Disable all pointer events on canvas - hanya shield yang bisa diinteraksi
                pdfCanvas.style.pointerEvents = 'none';
                pdfCanvas.style.userSelect = 'none';
                pdfCanvas.style.webkitUserSelect = 'none';
                pdfCanvas.style.mozUserSelect = 'none';
                pdfCanvas.style.msUserSelect = 'none';
                pdfCanvas.style.webkitTouchCallout = 'none';
            }

            // Disable save image (middle mouse button and all mouse interactions)
            pdfViewer.addEventListener('mousedown', function(e) {
                const target = e.target;
                if (target && (
                    target.closest('.module-sidebar') ||
                    target.closest('#markCompleteBtn') ||
                    target.closest('.module-header') ||
                    target.closest('.module-navigation')
                )) {
                    return true;
                }
                if (e.button === 1 || e.button === 2) { // Middle or right mouse button
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            }, true);

            // Prevent mouse up on right button (only within PDF viewer)
            pdfViewer.addEventListener('mouseup', function(e) {
                const target = e.target;
                if (target && (
                    target.closest('.module-sidebar') ||
                    target.closest('#markCompleteBtn') ||
                    target.closest('.module-header') ||
                    target.closest('.module-navigation')
                )) {
                    return true;
                }
                if (e.button === 2) { // Right mouse button
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            }, true);

            // Prevent all mouse interactions that could lead to selection (only within PDF viewer)
            pdfViewer.addEventListener('mousemove', function(e) {
                const target = e.target;
                if (target && (
                    target.closest('.module-sidebar') ||
                    target.closest('#markCompleteBtn') ||
                    target.closest('.module-header') ||
                    target.closest('.module-navigation')
                )) {
                    return true;
                }
                // Clear any selection that might occur
                if (window.getSelection && window.getSelection().toString().length > 0) {
                    window.getSelection().removeAllRanges();
                }
            }, true);

            // Disable text selection with CSS
            pdfViewer.style.userSelect = 'none';
            pdfViewer.style.webkitUserSelect = 'none';
            pdfViewer.style.mozUserSelect = 'none';
            pdfViewer.style.msUserSelect = 'none';
            pdfViewer.style.webkitTouchCallout = 'none';
            pdfViewer.style.webkitTapHighlightColor = 'transparent';

            // Continuously clear selection (aggressive protection) - but allow on sidebar
            setInterval(function() {
                const activeElement = document.activeElement;
                if (activeElement && (
                    activeElement.closest('.module-sidebar') ||
                    activeElement.closest('#markCompleteBtn') ||
                    activeElement.closest('.module-header') ||
                    activeElement.closest('.module-navigation')
                )) {
                    return; // Don't clear selection on sidebar/buttons
                }
                if (window.getSelection) {
                    const selection = window.getSelection();
                    // Only clear if selection is within PDF viewer
                    if (selection.rangeCount > 0) {
                        const range = selection.getRangeAt(0);
                        const pdfViewer = document.getElementById('pdf-viewer');
                        if (pdfViewer && pdfViewer.contains(range.commonAncestorContainer)) {
                            if (selection.toString().length > 0) {
                                selection.removeAllRanges();
                            }
                        }
                    }
                }
                if (document.selection) {
                    document.selection.empty();
                }
            }, 50);

            // Prevent selection on any mouse action (only within PDF viewer)
            ['mousedown', 'mouseup', 'mousemove', 'click', 'dblclick'].forEach(eventType => {
                pdfViewer.addEventListener(eventType, function(e) {
                    // Don't prevent selection on sidebar or buttons
                    const target = e.target;
                    if (target && (
                        target.closest('.module-sidebar') ||
                        target.closest('#markCompleteBtn') ||
                        target.closest('.module-header') ||
                        target.closest('.module-navigation')
                    )) {
                        return true;
                    }
                    if (window.getSelection) {
                        window.getSelection().removeAllRanges();
                    }
                }, true);
            });
        }

        // Global protection - prevent copy anywhere on page when PDF is visible
        document.addEventListener('copy', function(e) {
            const target = e.target;
            // Allow copy on sidebar and buttons
            if (target && (
                target.closest('.module-sidebar') ||
                target.closest('.module-link') ||
                target.closest('.chapter-header') ||
                target.closest('.back-to-course') ||
                target.closest('#markCompleteBtn') ||
                target.closest('.module-header') ||
                target.closest('.module-navigation')
            )) {
                return true;
            }
            const pdfViewer = document.getElementById('pdf-viewer');
            if (pdfViewer && pdfViewer.offsetParent !== null && pdfViewer.contains(target)) {
                preventCopy(e);
            }
        }, true);

        // Global protection - prevent context menu anywhere on page when PDF is visible
        document.addEventListener('contextmenu', function(e) {
            const target = e.target;
            // Allow context menu on sidebar and buttons
            if (target && (
                target.closest('.module-sidebar') ||
                target.closest('.module-link') ||
                target.closest('.chapter-header') ||
                target.closest('.back-to-course') ||
                target.closest('#markCompleteBtn') ||
                target.closest('.module-header') ||
                target.closest('.module-navigation')
            )) {
                return true;
            }
            const pdfViewer = document.getElementById('pdf-viewer');
            const pdfCanvasWrapper = document.getElementById('pdfCanvasWrapper');
            if (pdfViewer && pdfViewer.offsetParent !== null && 
                (pdfViewer.contains(e.target) || (pdfCanvasWrapper && pdfCanvasWrapper.contains(e.target)))) {
                preventContextMenu(e);
            }
        }, true);

        // Ensure sidebar and buttons are always clickable - add event listeners with highest priority
        document.addEventListener('click', function(e) {
            const target = e.target;
            if (target && (
                target.closest('.module-sidebar') ||
                target.closest('#markCompleteBtn') ||
                target.closest('.module-header') ||
                target.closest('.module-navigation')
            )) {
                // Stop propagation to prevent protection scripts from blocking
                e.stopPropagation();
                e.stopImmediatePropagation();
            }
        }, true); // Use capture phase with highest priority

        document.addEventListener('mousedown', function(e) {
            const target = e.target;
            if (target && (
                target.closest('.module-sidebar') ||
                target.closest('#markCompleteBtn') ||
                target.closest('.module-header') ||
                target.closest('.module-navigation')
            )) {
                // Stop propagation to prevent protection scripts from blocking
                e.stopPropagation();
                e.stopImmediatePropagation();
            }
        }, true); // Use capture phase with highest priority
    }

    // Wait for PDF.js to load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initPDFViewer, 100);
        });
    } else {
        setTimeout(initPDFViewer, 100);
    }
})();
</script>
@endif

@if($module->type === 'video' && $module->video_url && (str_contains($module->video_url, 'youtube.com') || str_contains($module->video_url, 'youtu.be')))
<script>
(function() {
    // YouTube Video Protection - Mencegah Share dan Copy Link
    const youtubeWrapper = document.querySelector('.youtube-video-wrapper');
    const youtubeIframe = document.getElementById('youtubePlayer');
    
    if (!youtubeWrapper || !youtubeIframe) return;

    // Function to prevent all interactions
    function preventAll(e) {
        // Don't prevent events on mark complete button or its parent
        const target = e.target;
        if (target && (
            target.id === 'markCompleteBtn' || 
            target.closest('#markCompleteBtn') ||
            target.closest('.module-header') ||
            target.closest('.module-navigation')
        )) {
            return true; // Allow event for mark complete button
        }
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        return false;
    }

    // Function to prevent context menu
    function preventContextMenu(e) {
        // Don't prevent events on mark complete button or its parent
        const target = e.target;
        if (target && (
            target.id === 'markCompleteBtn' || 
            target.closest('#markCompleteBtn') ||
            target.closest('.module-header') ||
            target.closest('.module-navigation')
        )) {
            return true; // Allow event for mark complete button
        }
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        alert('Klik kanan tidak diizinkan untuk melindungi konten video.');
        return false;
    }

    // Function to prevent copy
    function preventCopy(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        if (window.getSelection) {
            window.getSelection().removeAllRanges();
        }
        if (document.selection) {
            document.selection.empty();
        }
        alert('Copy tidak diizinkan untuk melindungi konten video.');
        return false;
    }

    // Protect YouTube wrapper
    youtubeWrapper.addEventListener('contextmenu', function(e) {
        const target = e.target;
        if (target && (
            target.id === 'markCompleteBtn' || 
            target.closest('#markCompleteBtn') ||
            target.closest('.module-header') ||
            target.closest('.module-navigation')
        )) {
            return true;
        }
        preventContextMenu(e);
    }, true);
    youtubeWrapper.addEventListener('selectstart', preventAll, true);
    youtubeWrapper.addEventListener('copy', preventCopy, true);
    youtubeWrapper.addEventListener('cut', preventAll, true);
    youtubeWrapper.addEventListener('paste', preventAll, true);
    youtubeWrapper.addEventListener('dragstart', preventAll, true);

    // Global protection for YouTube video area
    document.addEventListener('contextmenu', function(e) {
        if (youtubeWrapper && youtubeWrapper.contains(e.target)) {
            preventContextMenu(e);
        }
    }, true);

    document.addEventListener('copy', function(e) {
        if (youtubeWrapper && youtubeWrapper.contains(e.target)) {
            preventCopy(e);
        }
    }, true);

    // Disable keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (youtubeWrapper && youtubeWrapper.contains(document.activeElement)) {
            // Disable Ctrl+C, Ctrl+A, Ctrl+S, Ctrl+P
            if (e.ctrlKey || e.metaKey) {
                if (['c', 'C', 'a', 'A', 's', 'S', 'p', 'P'].includes(e.key)) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (['c', 'C'].includes(e.key)) {
                        alert('Copy tidak diizinkan untuk melindungi konten video.');
                    }
                    return false;
                }
            }
        }
    }, true);

    // Continuously clear selection
    setInterval(function() {
        if (window.getSelection) {
            const selection = window.getSelection();
            if (selection.toString().length > 0 && youtubeWrapper.contains(selection.anchorNode)) {
                selection.removeAllRanges();
            }
        }
    }, 100);

    // Prevent iframe interaction (additional protection)
    youtubeWrapper.style.userSelect = 'none';
    youtubeWrapper.style.webkitUserSelect = 'none';
    youtubeWrapper.style.mozUserSelect = 'none';
    youtubeWrapper.style.msUserSelect = 'none';
    youtubeWrapper.style.webkitTouchCallout = 'none';
})();
</script>
@endif

</script>
@endsection
