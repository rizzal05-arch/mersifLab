@extends('layouts.app')

@section('title', $module->title . ' - ' . $class->name)

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/welcome.css') }}">
<style>
    .module-container {
        display: flex;
        min-height: calc(100vh - 200px);
        position: relative;
    }

    .module-sidebar {
        width: 320px;
        background: #f8f9fa;
        border-right: 1px solid #e0e0e0;
        padding: 1.5rem;
        overflow-y: auto;
        position: fixed;
        height: calc(100vh - 80px);
        left: 0;
        top: 80px;
        z-index: 100;
    }

    .module-content {
        margin-left: 320px;
        flex: 1;
        padding: 2rem;
        background: white;
    }

    .course-progress {
        background: white;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1.5rem;
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
        margin-bottom: 0.5rem;
    }

    .chapter-header {
        padding: 0.75rem;
        background: white;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background 0.2s;
        border: 1px solid #e0e0e0;
    }

    .chapter-header:hover {
        background: #f0f0f0;
    }

    .chapter-header.active {
        background: #e3f2fd;
        font-weight: 600;
    }

    .module-list {
        list-style: none;
        padding: 0;
        margin: 0.5rem 0 0 0;
        display: none;
    }

    .module-list.show {
        display: block;
    }

    .module-link {
        padding: 0.75rem 1rem;
        display: flex;
        align-items: flex-start;
        text-decoration: none;
        color: #333;
        border-radius: 6px;
        transition: all 0.2s;
        margin-bottom: 0.25rem;
        position: relative;
    }

    .module-link:hover {
        background: #f0f0f0;
    }

    .module-link.active {
        background: #e3f2fd;
        color: #2196f3;
        border-left: 3px solid #2196f3;
        font-weight: 500;
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
        padding: 56.25% 0 0 0;
        position: relative;
        margin-bottom: 2rem;
    }

    .video-player-container iframe,
    .video-player-container video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
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
        padding: 2rem;
        min-height: 600px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .module-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e0e0e0;
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
    <div class="module-sidebar">
        <a href="{{ route('course.detail', $class->id) }}" class="text-decoration-none mb-3 d-block">
            <i class="fas fa-arrow-left me-2"></i>Back to Course
        </a>

        <h5 class="fw-bold mb-3">{{ $class->name }}</h5>

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
                    <div class="chapter-header {{ $ch->id == $chapter->id ? 'active' : '' }}" 
                         onclick="toggleChapter({{ $ch->id }})">
                        <span>
                            <i class="fas fa-chevron-{{ $ch->id == $chapter->id ? 'down' : 'right' }} me-2"></i>
                            {{ $ch->title }}
                        </span>
                    </div>
                    <ul class="module-list {{ $ch->id == $chapter->id ? 'show' : '' }}" id="chapter-{{ $ch->id }}">
                        @foreach($ch->modules as $mod)
                            @php
                                $isCompleted = isset($completedModules) && in_array($mod->id, $completedModules);
                            @endphp
                            <li>
                                <a href="{{ route('module.show', [$class->id, $ch->id, $mod->id]) }}" 
                                   class="module-link {{ $mod->id == $module->id ? 'active' : '' }} {{ $isCompleted ? 'completed' : '' }}">
                                    <input type="radio" class="module-radio" {{ $mod->id == $module->id ? 'checked' : '' }} disabled>
                                    <div class="module-link-content">
                                        <div class="module-link-title">{{ $mod->title }}</div>
                                        <div class="module-link-meta">
                                            @if($mod->type === 'video')
                                                <i class="fas fa-play-circle text-primary"></i>
                                                <span>{{ $mod->duration ? gmdate('i:s', $mod->duration) : 'N/A' }}</span>
                                            @elseif($mod->type === 'document')
                                                <i class="fas fa-file-pdf text-danger"></i>
                                                <span>{{ $mod->duration ? gmdate('i:s', $mod->duration) : 'N/A' }}</span>
                                            @else
                                                <i class="fas fa-align-left text-info"></i>
                                                <span>{{ $mod->duration ? $mod->duration . ' min read' : 'Text' }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="module-content">
        <!-- Module Header -->
        <div class="module-header">
            <div>
                <h2 class="fw-bold mb-1">{{ $module->title }}</h2>
                <p class="text-muted mb-0">{{ $chapter->title }}</p>
            </div>
            @if($isEnrolled)
                <button class="btn btn-primary" id="markCompleteBtn">
                    <i class="fas fa-check me-2"></i>Mark as Complete
                </button>
            @endif
        </div>

        <!-- Module Content Based on Type -->
        @if($module->type === 'video')
            <!-- Video Module -->
            <div class="video-player-container">
                @if($module->video_url)
                    @if(str_contains($module->video_url, 'youtube.com') || str_contains($module->video_url, 'youtu.be'))
                        @php
                            // Extract YouTube video ID
                            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $module->video_url, $matches);
                            $videoId = $matches[1] ?? null;
                        @endphp
                        @if($videoId)
                            <iframe src="https://www.youtube.com/embed/{{ $videoId }}" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen></iframe>
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100 text-white">
                                <div class="text-center">
                                    <i class="fas fa-play-circle fa-4x mb-3"></i>
                                    <p>Video Player</p>
                                    <a href="{{ $module->video_url }}" target="_blank" class="btn btn-light">Open Video</a>
                                </div>
                            </div>
                        @endif
                    @elseif(str_contains($module->video_url, 'vimeo.com'))
                        @php
                            preg_match('/vimeo.com\/(\d+)/', $module->video_url, $matches);
                            $videoId = $matches[1] ?? null;
                        @endphp
                        @if($videoId)
                            <iframe src="https://player.vimeo.com/video/{{ $videoId }}" 
                                    frameborder="0" 
                                    allow="autoplay; fullscreen; picture-in-picture" 
                                    allowfullscreen></iframe>
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100 text-white">
                                <div class="text-center">
                                    <i class="fas fa-play-circle fa-4x mb-3"></i>
                                    <p>Video Player</p>
                                    <a href="{{ $module->video_url }}" target="_blank" class="btn btn-light">Open Video</a>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="d-flex align-items-center justify-content-center h-100 text-white">
                            <div class="text-center">
                                <i class="fas fa-play-circle fa-4x mb-3"></i>
                                <p>Video Player</p>
                                <a href="{{ $module->video_url }}" target="_blank" class="btn btn-light">Open Video</a>
                            </div>
                        </div>
                    @endif
                @elseif($module->file_path)
                    <video controls style="width: 100%; height: 100%;">
                        <source src="{{ asset('storage/' . $module->file_path) }}" type="video/mp4">
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
                    <iframe src="{{ asset('storage/' . $module->file_path) }}#toolbar=0" 
                            style="width: 100%; height: 800px; border: none;"></iframe>
                @else
                    <div class="text-center">
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
function toggleChapter(chapterId) {
    const moduleList = document.getElementById('chapter-' + chapterId);
    const chapterHeader = moduleList.previousElementSibling;
    const icon = chapterHeader.querySelector('i');
    
    if (moduleList.classList.contains('show')) {
        moduleList.classList.remove('show');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-right');
        chapterHeader.classList.remove('active');
    } else {
        // Close all other chapters
        document.querySelectorAll('.module-list.show').forEach(list => {
            list.classList.remove('show');
            const header = list.previousElementSibling;
            header.querySelector('i').classList.remove('fa-chevron-down');
            header.querySelector('i').classList.add('fa-chevron-right');
            header.classList.remove('active');
        });
        
        moduleList.classList.add('show');
        icon.classList.remove('fa-chevron-right');
        icon.classList.add('fa-chevron-down');
        chapterHeader.classList.add('active');
    }
}

@if($isEnrolled)
document.getElementById('markCompleteBtn')?.addEventListener('click', function() {
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
    
    fetch('{{ route("module.complete", [$class->id, $module->id]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            btn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Completed';
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-success');
            
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
            
            // Reload page after 1 second to update sidebar
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check me-2"></i>Mark as Complete';
            alert(data.message || 'Failed to mark as complete');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check me-2"></i>Mark as Complete';
        alert('An error occurred. Please try again.');
    });
});
@endif

</script>
@endsection
