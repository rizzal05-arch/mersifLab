@extends('layouts.admin')

@section('title', 'Course Moderation')

@section('content')
<div class="page-title">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1>Course Moderation</h1>
            <p style="color: #828282; margin: 5px 0 0 0; font-size: 14px;">Review and moderate course content</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn" style="background: #2F80ED; color: white; border: none; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 500;">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>
</div>

<!-- Course Header Info -->
<div class="card-content mb-4" style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;">
        <div style="flex: 1;">
            <h2 style="font-size: 24px; font-weight: 700; color: #333333; margin-bottom: 10px;">
                {{ $course->name ?? 'Untitled Course' }}
            </h2>
            <div style="display: flex; gap: 20px; margin-bottom: 15px; flex-wrap: wrap; align-items: center;">
                <div>
                    <span style="color: #828282; font-size: 13px;">Teacher:</span>
                    <strong style="color: #333333; margin-left: 5px;">{{ $course->teacher->name ?? 'N/A' }}</strong>
                </div>
                <div>
                    <span style="color: #828282; font-size: 13px;">Email:</span>
                    <span style="color: #333333; margin-left: 5px;">{{ $course->teacher->email ?? 'N/A' }}</span>
                </div>
                <div>
                    <span style="color: #828282; font-size: 13px;">Status:</span>
                    @php
                        $status = $course->status ?? ($course->is_published ? 'active' : 'suspended');
                    @endphp
                    <span class="badge" style="background: {{ $status === 'active' ? '#e8f5e9' : '#ffebee' }}; color: {{ $status === 'active' ? '#27AE60' : '#c62828' }}; font-size: 12px; padding: 4px 10px; border-radius: 4px; font-weight: 500; text-transform: uppercase;">
                        {{ $status }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Panel 1: Course Details -->
<div class="card-content mb-4" style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
    <h3 style="font-size: 18px; font-weight: 700; color: #333333; margin-bottom: 20px;">Course Details</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
        <div>
            <span style="color: #828282; font-size: 13px; display: block; margin-bottom: 5px;">Category:</span>
            <span class="badge" style="background: #e3f2fd; color: #1976d2; font-size: 12px; padding: 4px 10px; border-radius: 4px;">
                {{ \App\Models\ClassModel::CATEGORIES[$course->category] ?? 'Uncategorized' }}
            </span>
        </div>
        <div>
            <span style="color: #828282; font-size: 13px; display: block; margin-bottom: 5px;">Price:</span>
            <strong style="color: #333333; font-size: 16px;">Rp {{ number_format($course->price ?? 0, 0, ',', '.') }}</strong>
        </div>
        <div>
            <span style="color: #828282; font-size: 13px; display: block; margin-bottom: 5px;">Total Sales:</span>
            <strong style="color: #333333; font-size: 16px;">{{ number_format($course->total_sales ?? 0, 0, ',', '.') }}</strong>
        </div>
        <div>
            <span style="color: #828282; font-size: 13px; display: block; margin-bottom: 5px;">Total Sections:</span>
            <strong style="color: #333333; font-size: 16px;">{{ $course->chapters_count ?? 0 }}</strong>
        </div>
        <div>
            <span style="color: #828282; font-size: 13px; display: block; margin-bottom: 5px;">Total Modules:</span>
            <strong style="color: #333333; font-size: 16px;">{{ $course->modules_count ?? 0 }}</strong>
        </div>
    </div>
    @if($course->description)
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #f0f0f0;">
            <span style="color: #828282; font-size: 13px; display: block; margin-bottom: 10px;">Description:</span>
            <p style="color: #333333; font-size: 14px; line-height: 1.6; margin: 0;">{{ $course->description }}</p>
        </div>
    @endif
    @if($course->admin_feedback)
        <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-radius: 8px; border-left: 4px solid #ffc107;">
            <span style="color: #856404; font-size: 13px; font-weight: 600; display: block; margin-bottom: 5px;">Admin Feedback:</span>
            <p style="color: #856404; font-size: 14px; line-height: 1.6; margin: 0;">{{ $course->admin_feedback }}</p>
        </div>
    @endif
</div>

<!-- Panel 2: Curriculum (Sections & Modules) -->
<div class="card-content" style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
    <h3 style="font-size: 18px; font-weight: 700; color: #333333; margin-bottom: 20px;">Curriculum</h3>
    
    @if($course->chapters && $course->chapters->count() > 0)
        <div class="accordion" id="curriculumAccordion">
            @foreach($course->chapters as $chapterIndex => $chapter)
                <div class="accordion-item" style="border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 15px; overflow: hidden;">
                    <h2 class="accordion-header" id="chapterHeading{{ $chapter->id }}">
                        <button class="accordion-button {{ $chapterIndex === 0 ? '' : 'collapsed' }}" type="button" 
                                data-bs-toggle="collapse" data-bs-target="#chapterCollapse{{ $chapter->id }}" 
                                aria-expanded="{{ $chapterIndex === 0 ? 'true' : 'false' }}" 
                                aria-controls="chapterCollapse{{ $chapter->id }}"
                                style="background: #f8f9fa; font-weight: 600; color: #333333;">
                            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; margin-right: 15px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <i class="fas fa-folder" style="color: #2F80ED; font-size: 18px;"></i>
                                    <span>{{ $chapter->title ?? 'Untitled Section' }}</span>
                                    <span class="badge" style="background: #e3f2fd; color: #1976d2; font-size: 11px; padding: 2px 8px;">
                                        {{ $chapter->modules->count() ?? 0 }} Modules
                                    </span>
                                </div>
                                <div style="display: flex; gap: 8px;">
                                    <!-- Hide Section Button -->
                                    <form action="{{ route('admin.chapters.toggle-status', $chapter->id) }}" method="POST" style="display: inline;" onclick="event.stopPropagation();">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm" 
                                                style="background: {{ $chapter->is_published ? '#ff9800' : '#27AE60' }}; color: white; border: none; padding: 4px 10px; font-size: 11px; border-radius: 4px;"
                                                title="{{ $chapter->is_published ? 'Hide Section' : 'Show Section' }}">
                                            <i class="fas {{ $chapter->is_published ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                        </button>
                                    </form>
                                    <!-- Delete Section Button -->
                                    <form action="{{ route('admin.chapters.destroy', $chapter->id) }}" method="POST" style="display: inline;" onclick="event.stopPropagation();"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus section ini? Semua modules di dalamnya juga akan dihapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm" 
                                                style="background: #ffebee; color: #c62828; border: none; padding: 4px 8px; font-size: 11px; border-radius: 4px;"
                                                title="Delete Section">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </button>
                    </h2>
                    <div id="chapterCollapse{{ $chapter->id }}" 
                         class="accordion-collapse collapse {{ $chapterIndex === 0 ? 'show' : '' }}" 
                         aria-labelledby="chapterHeading{{ $chapter->id }}" 
                         data-bs-parent="#curriculumAccordion">
                        <div class="accordion-body" style="padding: 0; background: white;">
                            @if($chapter->description)
                                <p style="color: #828282; font-size: 13px; margin: 0 0 20px 0; padding: 0 20px;">{{ $chapter->description }}</p>
                            @endif
                            
                            @if($chapter->modules && $chapter->modules->count() > 0)
                                <div class="table-responsive" style="padding: 0 20px 20px 20px;">
                                    <table class="table table-sm" style="font-size: 13px; border-collapse: separate; border-spacing: 0; margin: 0;">
                                        <thead>
                                            <tr>
                                                <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; text-align: left;">Module Title</th>
                                                <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; text-align: center; width: 120px;">Type</th>
                                                <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; text-align: center; width: 100px;">Status</th>
                                                <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; text-align: center; width: 200px;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($chapter->modules as $module)
                                                <tr style="border-bottom: 1px solid #f8f9fa;">
                                                    <td style="padding: 16px 8px; vertical-align: middle;">
                                                        <div style="display: flex; align-items: center; gap: 12px;">
                                                            <i class="fas {{ $module->type === 'video' ? 'fa-video' : ($module->type === 'document' ? 'fa-file-pdf' : 'fa-file-alt') }}" 
                                                               style="color: #2F80ED; font-size: 16px;"></i>
                                                            <span style="font-weight: 500; color: #333333; font-size: 13px;">{{ $module->title ?? 'Untitled Module' }}</span>
                                                        </div>
                                                    </td>
                                                    <td style="padding: 16px 8px; vertical-align: middle; text-align: center;">
                                                        <span class="badge" style="background: #e3f2fd; color: #1976d2; font-size: 11px; padding: 4px 10px; border-radius: 4px; font-weight: 500;">
                                                            {{ ucfirst($module->type ?? 'text') }}
                                                        </span>
                                                    </td>
                                                    <td style="padding: 16px 8px; vertical-align: middle; text-align: center;">
                                                        <span class="badge" style="background: {{ $module->is_published ? '#e8f5e9' : '#ffebee' }}; color: {{ $module->is_published ? '#27AE60' : '#c62828' }}; font-size: 11px; padding: 4px 10px; border-radius: 4px; font-weight: 500;">
                                                            {{ $module->is_published ? 'Published' : 'Hidden' }}
                                                        </span>
                                                    </td>
                                                    <td style="padding: 16px 8px; vertical-align: middle; text-align: center;">
                                                        <div style="display: flex; gap: 6px; align-items: center; justify-content: center; flex-wrap: wrap;">
                                                            <!-- Preview/Open Button -->
                                                            @if($module->type === 'video' && $module->video_url)
                                                                <button type="button" class="btn btn-sm" 
                                                                        style="background: #e3f2fd; color: #1976d2; border: none; padding: 4px 10px; font-size: 11px; border-radius: 4px; white-space: nowrap;"
                                                                        onclick="openVideoModal('{{ $module->video_url }}', '{{ $module->title }}')"
                                                                        title="Preview Video"
                                                                        onmouseover="this.style.opacity='0.8'" 
                                                                        onmouseout="this.style.opacity='1'">
                                                                    <i class="fas fa-play me-1"></i>Preview
                                                                </button>
                                                            @elseif($module->type === 'document' && $module->file_path)
                                                                <a href="{{ asset('storage/' . $module->file_path) }}" target="_blank" class="btn btn-sm" 
                                                                   style="background: #e3f2fd; color: #1976d2; border: none; padding: 4px 10px; font-size: 11px; border-radius: 4px; text-decoration: none; white-space: nowrap; display: inline-block;"
                                                                   title="Open PDF"
                                                                   onmouseover="this.style.opacity='0.8'" 
                                                                   onmouseout="this.style.opacity='1'">
                                                                    <i class="fas fa-external-link-alt me-1"></i>Open
                                                                </a>
                                                            @elseif($module->type === 'text')
                                                                <button type="button" class="btn btn-sm" 
                                                                        style="background: #e3f2fd; color: #1976d2; border: none; padding: 4px 10px; font-size: 11px; border-radius: 4px; white-space: nowrap;"
                                                                        onclick="openTextModal('{{ $module->title }}', `{{ addslashes($module->content ?? '') }}`)"
                                                                        title="View Content"
                                                                        onmouseover="this.style.opacity='0.8'" 
                                                                        onmouseout="this.style.opacity='1'">
                                                                    <i class="fas fa-eye me-1"></i>View
                                                                </button>
                                                            @endif
                                                            <!-- Suspend/Activate Module Button -->
                                                            <form action="{{ route('admin.modules.toggle-status', $module->id) }}" method="POST" style="display: inline;">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-sm" 
                                                                        style="background: {{ $module->is_published ? '#ff9800' : '#27AE60' }}; color: white; border: none; padding: 4px 10px; font-size: 11px; border-radius: 4px; cursor: pointer; white-space: nowrap; transition: opacity 0.2s;"
                                                                        title="{{ $module->is_published ? 'Suspend Module' : 'Activate Module' }}"
                                                                        onmouseover="this.style.opacity='0.8'" 
                                                                        onmouseout="this.style.opacity='1'">
                                                                    <i class="fas {{ $module->is_published ? 'fa-pause' : 'fa-play' }}"></i>
                                                                </button>
                                                            </form>
                                                            <!-- Delete Module Button -->
                                                            <form action="{{ route('admin.modules.destroy', $module->id) }}" method="POST" style="display: inline;"
                                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus module ini?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm" 
                                                                        style="background: #ffebee; color: #c62828; border: none; padding: 4px 8px; font-size: 11px; border-radius: 4px; cursor: pointer; white-space: nowrap; transition: opacity 0.2s;"
                                                                        title="Delete Module"
                                                                        onmouseover="this.style.opacity='0.8'" 
                                                                        onmouseout="this.style.opacity='1'">
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
                                <p style="color: #828282; font-size: 13px; text-align: center; padding: 20px;">No modules in this section</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div style="text-align: center; padding: 40px; color: #828282;">
            <i class="fas fa-folder-open" style="font-size: 48px; color: #e0e0e0; margin-bottom: 10px;"></i>
            <p style="font-size: 14px; margin: 0;">No sections found in this course</p>
        </div>
    @endif
</div>

<!-- Video Modal -->
<div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="videoModalLabel">Video Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="ratio ratio-16x9">
                    <iframe id="videoFrame" src="" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Text Content Modal -->
<div class="modal fade" id="textModal" tabindex="-1" aria-labelledby="textModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="textModalLabel">Text Content</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="textContent" style="line-height: 1.8; color: #333333;"></div>
            </div>
        </div>
    </div>
</div>

<script>
function openVideoModal(videoUrl, title) {
    document.getElementById('videoModalLabel').textContent = title;
    const iframe = document.getElementById('videoFrame');
    // Handle YouTube URLs
    if (videoUrl.includes('youtube.com') || videoUrl.includes('youtu.be')) {
        const videoId = videoUrl.includes('youtu.be') 
            ? videoUrl.split('youtu.be/')[1].split('?')[0]
            : videoUrl.split('v=')[1].split('&')[0];
        iframe.src = `https://www.youtube.com/embed/${videoId}`;
    } else {
        iframe.src = videoUrl;
    }
    new bootstrap.Modal(document.getElementById('videoModal')).show();
}

function openTextModal(title, content) {
    document.getElementById('textModalLabel').textContent = title;
    document.getElementById('textContent').innerHTML = content.replace(/\n/g, '<br>');
    new bootstrap.Modal(document.getElementById('textModal')).show();
}

// Close modal and reset iframe when hidden
document.getElementById('videoModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('videoFrame').src = '';
});
</script>
@endsection
