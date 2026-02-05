<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Mode: {{ $module->title ?? 'Module' }}</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon.png') }}?v={{ time() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/module.css') }}">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .preview-header {
            background: white;
            border-bottom: 1px solid #e0e0e0;
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .preview-header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .preview-badge {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .preview-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }

        .preview-close {
            background: #f5f5f5;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.2s;
        }

        .preview-close:hover {
            background: #e0e0e0;
        }

        .preview-content {
            max-width: 1200px;
            margin: 24px auto;
            padding: 0 24px;
        }

        .content-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .module-info {
            padding: 20px 24px;
            border-bottom: 1px solid #f0f0f0;
            background: #fafafa;
        }

        .module-info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .module-info-item:last-child {
            margin-bottom: 0;
        }

        .module-info-label {
            font-weight: 600;
            color: #666;
            font-size: 14px;
        }

        .module-info-value {
            color: #333;
            font-size: 14px;
        }

        .module-content-area {
            padding: 24px;
        }

        /* Override module.css for preview */
        .module-container {
            display: block !important;
            min-height: auto !important;
        }

        .module-sidebar {
            display: none !important;
        }

        .module-content {
            margin: 0 !important;
            padding: 0 !important;
        }

        .module-header {
            display: none !important;
        }
    </style>
</head>
<body>
    <!-- Preview Header -->
    <div class="preview-header">
        <div class="preview-header-left">
            <div class="preview-badge">Preview Mode</div>
            <div class="preview-title">{{ $module->title ?? 'Module' }}</div>
        </div>
        <button class="preview-close" onclick="window.close()">
            <i class="fas fa-times me-2"></i> Close
        </button>
    </div>

    <!-- Content -->
    <div class="preview-content">
        <div class="content-container">
            <!-- Module Info -->
            <div class="module-info">
                <div class="module-info-item">
                    <span class="module-info-label">Module Type:</span>
                    <span class="module-info-value">{{ ucfirst($module->type ?? 'Unknown') }}</span>
                </div>
                @if($module->duration)
                <div class="module-info-item">
                    <span class="module-info-label">Duration:</span>
                    <span class="module-info-value">{{ $module->duration }} minutes</span>
                </div>
                @endif
                @if($module->order)
                <div class="module-info-item">
                    <span class="module-info-label">Order:</span>
                    <span class="module-info-value">{{ $module->order }}</span>
                </div>
                @endif
            </div>

            <!-- Content based on type -->
            <div class="module-content-area">
            @if($module->type === 'document' && $module->file_path)
                @php
                    $fileUrl = route('module.file', [
                        'classId' => $module->class_id ?? $module->chapter->class->id ?? 1,
                        'chapterId' => $module->chapter_id ?? $module->chapter->id ?? 1,
                        'moduleId' => $module->id
                    ]);
                @endphp
                <div class="pdf-viewer-container">
                    <div id="pdf-viewer-wrapper" style="width: 100%; height: auto; background: #f5f5f5; position: relative; user-select: none; border: 1px solid #ddd; border-radius: 4px; padding: 20px 0; margin-bottom: 20px;">
                        <div id="pdf-container" style="width: 100%; height: auto; overflow: visible; background: #525252; display: flex; justify-content: center; align-items: flex-start; padding: 30px;">
                            <div id="pdf-canvas-wrapper" style="position: relative; display: inline-block; background: white;">
                                <canvas id="pdf-canvas" style="display: block; max-width: 100%;"></canvas>
                                <div id="pdf-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10000; background: transparent; user-select: none; -webkit-user-select: none; -moz-user-select: none;"></div>
                            </div>
                        </div>
                    </div>
                    <div id="pdf-controls" style="text-align: center; padding: 10px; background: #f8f9fa; border-radius: 4px; margin-bottom: 20px;">
                        <button id="prev-page" style="margin-right: 10px; padding: 5px 10px; border: 1px solid #ccc; background: white; border-radius: 4px; cursor: pointer;">Previous</button>
                        <span id="page-info" style="margin: 0 15px; font-size: 14px;">Page 1 of 1</span>
                        <button id="next-page" style="margin-left: 10px; padding: 5px 10px; border: 1px solid #ccc; background: white; border-radius: 4px; cursor: pointer;">Next</button>
                    </div>
                </div>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
                    
                    const pdfUrl = '{{ $fileUrl }}';
                    console.log('Loading PDF from:', pdfUrl);
                    let pdfDoc = null;
                    let currentPage = 1;
                    let totalPages = 0;
                    const canvas = document.getElementById('pdf-canvas');
                    const ctx = canvas.getContext('2d');
                    const pageInfo = document.getElementById('page-info');
                    const prevBtn = document.getElementById('prev-page');
                    const nextBtn = document.getElementById('next-page');

                    function renderPage(pageNum) {
                        pdfDoc.getPage(pageNum).then(page => {
                            const viewport = page.getViewport({ scale: 1.5 });
                            canvas.height = viewport.height;
                            canvas.width = viewport.width;

                            const renderContext = {
                                canvasContext: ctx,
                                viewport: viewport
                            };

                            page.render(renderContext).promise.then(() => {
                                pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;
                                prevBtn.disabled = currentPage <= 1;
                                nextBtn.disabled = currentPage >= totalPages;
                            });
                        });
                    }

                    prevBtn.addEventListener('click', () => {
                        if (currentPage > 1) {
                            currentPage--;
                            renderPage(currentPage);
                        }
                    });

                    nextBtn.addEventListener('click', () => {
                        if (currentPage < totalPages) {
                            currentPage++;
                            renderPage(currentPage);
                        }
                    });

                    pdfjsLib.getDocument(pdfUrl).promise.then(pdf => {
                        pdfDoc = pdf;
                        totalPages = pdf.numPages;
                        if(pageInfo) pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;
                        renderPage(currentPage);
                        console.log('PDF loaded successfully with', totalPages, 'pages');
                    }).catch(err => {
                        console.error('Error loading PDF:', err);
                        const wrapper = document.getElementById('pdf-viewer-wrapper');
                        wrapper.innerHTML = '<div style="text-align: center; padding: 40px; color: #666;">Error loading PDF document. Please try again.<br><small>Check console for details.</small></div>';
                    });
                });
                </script>
            @elseif($module->type === 'video')
            <!-- Video Module - Same as user view -->
            <div class="video-player-container">
                @if($module->video_url)
                    @php
                        $videoId = null;
                        $videoType = null;
                        
                        // Check for YouTube
                        if (str_contains($module->video_url, 'youtube.com') || str_contains($module->video_url, 'youtu.be')) {
                            // Try multiple YouTube URL patterns
                            if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]{11})(?:\?|$|&)/', $module->video_url, $matches)) {
                                $videoId = $matches[1];
                                $videoType = 'youtube';
                            }
                            elseif (preg_match('/youtube\.com\/watch\?.*v=([a-zA-Z0-9_-]{11})/', $module->video_url, $matches)) {
                                $videoId = $matches[1];
                                $videoType = 'youtube';
                            }
                            elseif (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/', $module->video_url, $matches)) {
                                $videoId = $matches[1];
                                $videoType = 'youtube';
                            }
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
                                    style="width: 100%; height: 400px; border-radius: 8px;"
                                    allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen></iframe>
                        </div>
                    @elseif($videoType === 'vimeo' && $videoId)
                        <div class="youtube-video-wrapper">
                            <iframe src="https://player.vimeo.com/video/{{ $videoId }}" 
                                    frameborder="0" 
                                    style="width: 100%; height: 400px; border-radius: 8px;"
                                    allow="autoplay; fullscreen; picture-in-picture" 
                                    allowfullscreen></iframe>
                        </div>
                    @else
                        <div class="d-flex align-items-center justify-content-center text-white" style="min-height: 400px; background: #000; border-radius: 8px;">
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
                    <!-- Video File -->
                    @php
                        $fileUrl = route('module.file', [
                            'classId' => $module->class_id ?? $module->chapter->class->id ?? 1,
                            'chapterId' => $module->chapter_id ?? $module->chapter->id ?? 1,
                            'moduleId' => $module->id
                        ]);
                    @endphp
                    <div class="video-container" style="background: #000; border-radius: 8px; overflow: hidden;">
                        <video controls style="width: 100%; max-width: 100%; height: auto;" preload="metadata" controlsList="nodownload" oncontextmenu="return false;">
                            <source src="{{ $fileUrl }}" type="{{ $module->mime_type ?? 'video/mp4' }}">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                @else
                    <div class="d-flex align-items-center justify-content-center text-white" style="min-height: 400px; background: #000; border-radius: 8px;">
                        <div class="text-center">
                            <i class="fas fa-play-circle fa-4x mb-3"></i>
                            <p>Video Player</p>
                            <p class="small">Video content will be displayed here</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- YouTube Video Protection Script - Same as user view -->
            @if($module->type === 'video' && $module->video_url && (str_contains($module->video_url, 'youtube.com') || str_contains($module->video_url, 'youtu.be')))
            <script>
            (function() {
                const youtubeWrapper = document.querySelector('.youtube-video-wrapper');
                if (!youtubeWrapper) return;

                function preventAll(e) {
                    const target = e.target;
                    if (target.tagName === 'IFRAME') {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }
                }

                ['contextmenu', 'selectstart', 'copy', 'cut', 'paste', 'dragstart'].forEach(eventType => {
                    youtubeWrapper.addEventListener(eventType, preventAll, true);
                });

                document.addEventListener('keydown', function(e) {
                    if (youtubeWrapper.contains(document.activeElement)) {
                        if (e.ctrlKey || e.metaKey) {
                            if (['c', 'C', 'a', 'A', 's', 'S', 'p', 'P'].includes(e.key)) {
                                e.preventDefault();
                                return false;
                            }
                        }
                    }
                }, true);

                youtubeWrapper.style.userSelect = 'none';
                youtubeWrapper.style.webkitUserSelect = 'none';
                youtubeWrapper.style.mozUserSelect = 'none';
                youtubeWrapper.style.msUserSelect = 'none';
            })();
            </script>
            @endif
            @elseif($module->type === 'text')
                <!-- Text Content - Same as user view -->
                <div class="text-content" id="text-module-content">
                    {!! $module->content ?? '<p>No content available for this module.</p>' !!}
                </div>

                <!-- Text Module Protection Script - Same as user view -->
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const textContent = document.getElementById('text-module-content');
                    if (!textContent) return;

                    textContent.addEventListener('contextmenu', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        alert('Klik kanan tidak diizinkan pada konten ini.');
                        return false;
                    }, true);

                    textContent.addEventListener('selectstart', function(e) {
                        e.preventDefault();
                        return false;
                    }, true);

                    textContent.addEventListener('dragstart', function(e) {
                        e.preventDefault();
                        return false;
                    }, true);

                    textContent.addEventListener('copy', function(e) {
                        e.preventDefault();
                        alert('Copy tidak diizinkan untuk melindungi konten.');
                        return false;
                    }, true);

                    textContent.addEventListener('cut', function(e) {
                        e.preventDefault();
                        return false;
                    }, true);

                    textContent.addEventListener('keydown', function(e) {
                        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'c') {
                            e.preventDefault();
                            alert('Copy tidak diizinkan untuk melindungi konten.');
                            return false;
                        }
                        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'x') {
                            e.preventDefault();
                            return false;
                        }
                        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'a') {
                            e.preventDefault();
                            return false;
                        }
                    }, true);

                    textContent.style.userSelect = 'none';
                    textContent.style.webkitUserSelect = 'none';
                    textContent.style.mozUserSelect = 'none';
                    textContent.style.msUserSelect = 'none';
                });
                </script>
            @else
                <div class="empty-state" style="text-align: center; padding: 60px 20px; color: #666;">
                    <i class="fas fa-file-alt fa-3x mb-3" style="color: #ccc;"></i>
                    <p>No preview available for this module type.</p>
                    <small style="color: #999;">Module type: {{ $module->type ?? 'Unknown' }}</small>
                    @if(!$module->file_path && in_array($module->type, ['document', 'video']))
                    <br><small style="color: #f44336;">No file attached to this module.</small>
                    @endif
                </div>
            @endif
        </div>
    </div>
</body>
</html>
