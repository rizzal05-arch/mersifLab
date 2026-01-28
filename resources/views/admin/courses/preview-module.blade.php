<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Mode: {{ $module->title ?? 'Module' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f5f5;
            color: #333;
        }

        .preview-header {
            background: white;
            border-bottom: 1px solid #e0e0e0;
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
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

        .preview-meta {
            font-size: 13px;
            color: #666;
            margin-left: 12px;
        }

        .btn-close {
            background: #f5f5f5;
            color: #333;
            border: 1px solid #e0e0e0;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
            cursor: pointer;
        }

        .btn-close:hover {
            background: #e0e0e0;
            border-color: #ccc;
        }

        .preview-content {
            max-width: 1200px;
            margin: 24px auto;
            padding: 0 24px;
        }

        .content-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        /* PDF Container */
        .pdf-container {
            width: 100%;
            height: calc(100vh - 200px);
            min-height: 600px;
        }

        .pdf-container iframe,
        .pdf-container embed {
            width: 100%;
            height: 100%;
            border: none;
        }

        /* Video Container */
        .video-container {
            position: relative;
            padding: 24px;
            background: #000;
        }

        .video-container video {
            width: 100%;
            max-height: calc(100vh - 200px);
            background: #000;
        }

        .youtube-container {
            position: relative;
            padding-bottom: 56.25%; /* 16:9 aspect ratio */
            height: 0;
            overflow: hidden;
            max-width: 100%;
            background: #000;
        }

        .youtube-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        /* Text Container */
        .text-container {
            padding: 32px;
            line-height: 1.8;
            font-size: 15px;
            color: #333;
        }

        .text-container h1,
        .text-container h2,
        .text-container h3 {
            margin-top: 24px;
            margin-bottom: 12px;
            color: #1e293b;
        }

        .text-container p {
            margin-bottom: 16px;
        }

        .text-container ul,
        .text-container ol {
            margin-left: 24px;
            margin-bottom: 16px;
        }

        /* Empty State */
        .empty-state {
            padding: 64px 24px;
            text-align: center;
            color: #666;
        }

        .empty-state i {
            font-size: 48px;
            color: #ccc;
            margin-bottom: 16px;
        }

        .empty-state p {
            font-size: 14px;
        }

        /* Module Info */
        .module-info {
            background: #f8f9fa;
            padding: 16px 24px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            gap: 24px;
            flex-wrap: wrap;
            font-size: 13px;
        }

        .module-info-item {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #666;
        }

        .module-info-item i {
            color: #999;
        }

        .module-info-item strong {
            color: #333;
            margin-left: 4px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="preview-header">
        <div class="preview-header-left">
            <span class="preview-badge">
                <i class="fas fa-eye"></i> Preview Mode
            </span>
            <span class="preview-title">{{ $module->title ?? 'Untitled Module' }}</span>
            <span class="preview-meta">
                <i class="{{ $module->file_icon }}"></i>
                {{ ucfirst($module->type ?? 'Unknown') }}
            </span>
        </div>
        <a href="javascript:window.close()" class="btn-close">
            <i class="fas fa-times"></i> Close
        </a>
    </div>

    <!-- Content -->
    <div class="preview-content">
        <div class="content-container">
            <!-- Module Info -->
            <div class="module-info">
                <div class="module-info-item">
                    <i class="fas fa-book"></i>
                    Course: <strong>{{ $module->chapter->class->name ?? 'Unknown' }}</strong>
                </div>
                <div class="module-info-item">
                    <i class="fas fa-list"></i>
                    Chapter: <strong>{{ $module->chapter->title ?? 'Unknown' }}</strong>
                </div>
                @if($module->file_name)
                <div class="module-info-item">
                    <i class="fas fa-file"></i>
                    File: <strong>{{ $module->file_name }}</strong>
                </div>
                @endif
                @if($module->file_size)
                <div class="module-info-item">
                    <i class="fas fa-hdd"></i>
                    Size: <strong>{{ $module->formatted_file_size }}</strong>
                </div>
                @endif
            </div>

            <!-- Content based on type -->
            @if($module->type === 'document' && $module->file_path)
                @php
                    $fileUrl = route('admin.modules.file', $module->id);
                @endphp
                <div class="pdf-container">
                    <iframe src="{{ $fileUrl }}" type="application/pdf"></iframe>
                </div>
            @elseif($module->type === 'video')
                @if($module->video_url)
                    <!-- YouTube Video -->
                    <div class="youtube-container">
                        @php
                            // Extract YouTube video ID
                            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $module->video_url, $matches);
                            $youtubeId = $matches[1] ?? null;
                        @endphp
                        @if($youtubeId)
                            <iframe 
                                src="https://www.youtube.com/embed/{{ $youtubeId }}" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen>
                            </iframe>
                        @else
                            <div class="empty-state">
                                <i class="fas fa-exclamation-triangle"></i>
                                <p>Invalid YouTube URL</p>
                            </div>
                        @endif
                    </div>
                @elseif($module->file_path)
                    <!-- Video File -->
                    @php
                        $fileUrl = route('admin.modules.file', $module->id);
                    @endphp
                    <div class="video-container">
                        <video controls>
                            <source src="{{ $fileUrl }}" type="{{ $module->mime_type ?? 'video/mp4' }}">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-video-slash"></i>
                        <p>No video file or URL available</p>
                    </div>
                @endif
            @elseif($module->type === 'text')
                <!-- Text Content -->
                <div class="text-container">
                    {!! nl2br(e($module->content ?? 'No content available.')) !!}
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-file-alt"></i>
                    <p>No preview available for this module type.</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
