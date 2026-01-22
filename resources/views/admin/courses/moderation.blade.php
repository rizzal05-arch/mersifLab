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
            <span style="color: #828282; font-size: 13px; display: block; margin-bottom: 5px;">Total Chapters:</span>
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

<div class="card-content" style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
    <h3 style="font-size: 18px; font-weight: 700; color: #333333; margin-bottom: 20px;">Chapter</h3>
    
    @if($course->chapters && $course->chapters->count() > 0)
        <div class="accordion" id="curriculumAccordion" style="border: none; border-radius: 0;">
            @foreach($course->chapters as $chapterIndex => $chapter)
                <div class="chapter-item" style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 16px; margin-bottom: {{ $loop->last ? '0' : '16px' }}; background: white;">
                    <!-- Chapter Header (Clickable) -->
                    <div style="display: flex; align-items: center; justify-content: space-between; cursor: pointer;"
                         data-bs-toggle="collapse" 
                         data-bs-target="#chapterCollapse{{ $chapter->id }}"
                         aria-expanded="false"
                         aria-controls="chapterCollapse{{ $chapter->id }}">
                        <div style="display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0;">
                            <i class="fas fa-folder" style="color: #2F80ED; font-size: 18px; flex-shrink: 0;"></i>
                            <span style="font-size: 14px; font-weight: normal; color: #333333; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $chapter->title ?? 'Untitled Section' }}</span>
                        </div>
                        <i class="fas fa-chevron-down chapter-chevron" 
                           style="color: #333; font-size: 14px; transition: transform 0.2s ease-in-out; flex-shrink: 0; margin-left: 12px;"></i>
                    </div>
                    
                    <!-- Collapsible Content -->
                    <div id="chapterCollapse{{ $chapter->id }}" 
                         class="accordion-collapse collapse" 
                         aria-labelledby="chapterHeading{{ $chapter->id }}" 
                         data-bs-parent="#curriculumAccordion">
                        <div class="accordion-body" style="padding: 20px 0 0 0; background: transparent; position: relative;">
                            <!-- Description -->
                            <div style="margin-bottom: 20px;">
                                <span style="color: #bdbdbd; font-size: 13px;">Description</span>
                                @if($chapter->description)
                                    <p style="color: #333333; font-size: 13px; margin: 8px 0 0 0; line-height: 1.6;">{{ $chapter->description }}</p>
                                @endif
                            </div>
                            
                            @if($chapter->modules && $chapter->modules->count() > 0)
                                <!-- Module Table -->
                                <div style="margin-bottom: 20px; overflow-x: auto;">
                                    <table style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px; min-width: 600px;">
                                        <thead>
                                            <tr>
                                                <th style="padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; text-align: left; border-bottom: 1px solid #f0f0f0;">MODUL TITLE</th>
                                                <th style="padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; text-align: center; border-bottom: 1px solid #f0f0f0; white-space: nowrap;">TYPE</th>
                                                <th style="padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; text-align: center; border-bottom: 1px solid #f0f0f0; white-space: nowrap;">STATUS</th>
                                                <th style="padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; text-align: right; border-bottom: 1px solid #f0f0f0; white-space: nowrap;">ACTION</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($chapter->modules as $module)
                                                <tr style="border-bottom: 1px solid #f8f9fa;">
                                                    <td style="padding: 16px 8px; vertical-align: middle;">
                                                        <div style="display: flex; align-items: center; gap: 12px;">
                                                            <i class="fas fa-file-pdf" style="color: #2F80ED; font-size: 16px; flex-shrink: 0;"></i>
                                                            <span style="font-weight: normal; color: #333333; font-size: 13px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $module->title ?? 'Untitled Module' }}</span>
                                                        </div>
                                                    </td>
                                                    <td style="padding: 16px 8px; vertical-align: middle; text-align: center;">
                                                        <span class="badge" style="background: #e3f2fd; color: #1976d2; font-size: 11px; padding: 4px 10px; border-radius: 6px; font-weight: 500; white-space: nowrap;">
                                                            {{ ucfirst($module->type ?? 'document') }}
                                                        </span>
                                                    </td>
                                                    <td style="padding: 16px 8px; vertical-align: middle; text-align: center;">
                                                        @if($module->is_published)
                                                            <span class="badge" style="background: #e8f5e9; color: #27AE60; font-size: 11px; padding: 4px 10px; border-radius: 6px; font-weight: 500; white-space: nowrap;">
                                                                Published
                                                            </span>
                                                        @else
                                                            <span class="badge" style="background: #fce4ec; color: #c2185b; font-size: 11px; padding: 4px 10px; border-radius: 6px; font-weight: 500; white-space: nowrap;">
                                                                Hidden
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td style="padding: 16px 8px; vertical-align: middle; text-align: right;">
                                                        <a href="{{ route('module.show', ['classId' => $course->id, 'chapterId' => $chapter->id, 'moduleId' => $module->id]) }}" 
                                                           style="background: transparent; color: #2F80ED; border: 1px solid #90caf9; padding: 6px 12px; font-size: 11px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; white-space: nowrap;"
                                                           title="Open Module"
                                                           onmouseover="this.style.background='#e3f2fd'; this.style.borderColor='#2F80ED';" 
                                                           onmouseout="this.style.background='transparent'; this.style.borderColor='#90caf9';">
                                                            <i class="fas fa-external-link-alt" style="font-size: 10px;"></i>Open
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p style="color: #828282; font-size: 13px; text-align: center; padding: 20px;">No modules in this section</p>
                            @endif
                            
                            <!-- Action Buttons (Below Table, Horizontal) -->
                            <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 20px; flex-wrap: wrap;" onclick="event.stopPropagation();">
                                <!-- Hide Section Button -->
                                <form action="{{ route('admin.chapters.toggle-status', $chapter->id) }}" method="POST" style="display: inline; margin: 0;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm" 
                                            style="background: #ff9800; color: white; border: none; padding: 8px; font-size: 14px; border-radius: 6px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; transition: opacity 0.2s; flex-shrink: 0;"
                                            title="{{ $chapter->is_published ? 'Hide Section' : 'Show Section' }}"
                                            onmouseover="this.style.opacity='0.8'" 
                                            onmouseout="this.style.opacity='1'">
                                        <i class="fas fa-eye-slash"></i>
                                    </button>
                                </form>
                                <!-- Delete Section Button -->
                                <form action="{{ route('admin.chapters.destroy', $chapter->id) }}" method="POST" style="display: inline; margin: 0;"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus section ini? Semua modules di dalamnya juga akan dihapus.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm" 
                                            style="background: #e53935; color: white; border: none; padding: 8px; font-size: 14px; border-radius: 6px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; transition: opacity 0.2s; flex-shrink: 0;"
                                            title="Delete Section"
                                            onmouseover="this.style.opacity='0.8'" 
                                            onmouseout="this.style.opacity='1'">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
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

<style>
#curriculumAccordion .chapter-item {
    transition: box-shadow 0.2s ease;
}

#curriculumAccordion .chapter-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

#curriculumAccordion .chapter-chevron {
    transition: transform 0.2s ease-in-out;
}

#curriculumAccordion .chapter-chevron[aria-expanded="true"] {
    transform: rotate(180deg);
}

#curriculumAccordion .accordion-body {
    background: transparent;
    border-top: none;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    #curriculumAccordion .chapter-item {
        padding: 12px;
        margin-bottom: 12px;
    }
    
    #curriculumAccordion .chapter-item > div:first-child {
        padding: 8px 0;
    }
    
    #curriculumAccordion .chapter-item .fas.fa-folder {
        font-size: 16px;
    }
    
    #curriculumAccordion .chapter-item span {
        font-size: 13px;
    }
    
    #curriculumAccordion .accordion-body {
        padding: 16px 0 0 0;
    }
    
    #curriculumAccordion .accordion-body table {
        font-size: 12px;
    }
    
    #curriculumAccordion .accordion-body th,
    #curriculumAccordion .accordion-body td {
        padding: 12px 6px;
    }
    
    #curriculumAccordion .accordion-body .badge {
        font-size: 10px;
        padding: 3px 8px;
    }
    
    #curriculumAccordion .accordion-body a {
        font-size: 10px;
        padding: 5px 10px;
    }
}

@media (max-width: 480px) {
    #curriculumAccordion .chapter-item {
        padding: 10px;
    }
    
    #curriculumAccordion .chapter-item .fas.fa-folder {
        font-size: 14px;
    }
    
    #curriculumAccordion .chapter-item span {
        font-size: 12px;
    }
    
    #curriculumAccordion .accordion-body table {
        min-width: 500px;
    }
}
</style>

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

// Initialize chevron rotation on collapse toggle
document.addEventListener('DOMContentLoaded', function() {
    const collapseElements = document.querySelectorAll('#curriculumAccordion .accordion-collapse');
    collapseElements.forEach(function(collapseElement) {
        const targetId = collapseElement.getAttribute('id');
        const chevron = document.querySelector('[data-bs-target="#' + targetId + '"] .chapter-chevron');
        
        if (chevron) {
            collapseElement.addEventListener('shown.bs.collapse', function() {
                chevron.style.transform = 'rotate(180deg)';
                const header = document.querySelector('[data-bs-target="#' + targetId + '"]');
                if (header) {
                    header.setAttribute('aria-expanded', 'true');
                }
            });
            collapseElement.addEventListener('hidden.bs.collapse', function() {
                chevron.style.transform = 'rotate(0deg)';
                const header = document.querySelector('[data-bs-target="#' + targetId + '"]');
                if (header) {
                    header.setAttribute('aria-expanded', 'false');
                }
            });
        }
    });
});
</script>
@endsection
