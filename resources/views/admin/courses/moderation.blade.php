@extends('layouts.admin')

@section('title', 'Course Moderation')

@section('content')
<div class="page-title">
    <div>
        <h1>Course Moderation</h1>
        <p style="color: #828282; margin: 5px 0 0 0; font-size: 14px;">Review and moderate course content</p>
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
                @if($course->is_published)
                    <div>
                        <a href="{{ route('admin.courses.preview', $course->id) }}" 
                           class="btn btn-sm" 
                           style="background: #e3f2fd; color: #1976d2; border: 1px solid #90caf9; padding: 6px 12px; font-size: 12px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;"
                           target="_blank"
                           title="Preview Course (Admin Mode)"
                           onmouseover="this.style.background='#1976d2'; this.style.color='white'; this.style.borderColor='#1976d2';" 
                           onmouseout="this.style.background='#e3f2fd'; this.style.color='#1976d2'; this.style.borderColor='#90caf9';">
                            <i class="fas fa-eye"></i> Preview Course
                        </a>
                    </div>
                @endif
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
                                                <th style="padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; text-align: right; border-bottom: 1px solid #f0f0f0; white-space: nowrap; min-width: 200px;">ACTION</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($chapter->modules as $module)
                                                @php
                                                    $approvalStatus = $module->approval_status ?? 'pending_approval';
                                                    $isPending = $approvalStatus === 'pending_approval';
                                                    $isApproved = $approvalStatus === 'approved';
                                                    $isRejected = $approvalStatus === 'rejected';
                                                    $isHighlighted = isset($moduleId) && $moduleId == $module->id;
                                                @endphp
                                                <tr id="module-{{ $module->id }}" style="border-bottom: 1px solid #f8f9fa; {{ $isHighlighted ? 'background: #fff9e6; border-left: 3px solid #ff9800;' : '' }}">
                                                    <td style="padding: 16px 8px; vertical-align: middle;">
                                                        <div style="display: flex; align-items: center; gap: 12px;">
                                                            <i class="{{ $module->file_icon }}" style="font-size: 16px; flex-shrink: 0;"></i>
                                                            <span style="font-weight: normal; color: #333333; font-size: 13px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $module->title ?? 'Untitled Module' }}</span>
                                                        </div>
                                                    </td>
                                                    <td style="padding: 16px 8px; vertical-align: middle; text-align: center;">
                                                        <span class="badge" style="background: #e3f2fd; color: #1976d2; font-size: 11px; padding: 4px 10px; border-radius: 6px; font-weight: 500; white-space: nowrap;">
                                                            {{ ucfirst($module->type ?? 'document') }}
                                                        </span>
                                                    </td>
                                                    <td style="padding: 16px 8px; vertical-align: middle; text-align: center;">
                                                        <div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">
                                                            @if($isPending)
                                                                <span class="badge" style="background: #fff3cd; color: #856404; font-size: 11px; padding: 4px 10px; border-radius: 6px; font-weight: 500; white-space: nowrap;">
                                                                    <i class="fas fa-clock"></i> Pending Approval
                                                                </span>
                                                            @elseif($isApproved)
                                                                <span class="badge" style="background: #e8f5e9; color: #27AE60; font-size: 11px; padding: 4px 10px; border-radius: 6px; font-weight: 500; white-space: nowrap;">
                                                                    <i class="fas fa-check-circle"></i> Approved
                                                                </span>
                                                            @elseif($isRejected)
                                                                <span class="badge" style="background: #f8d7da; color: #721c24; font-size: 11px; padding: 4px 10px; border-radius: 6px; font-weight: 500; white-space: nowrap;">
                                                                    <i class="fas fa-times-circle"></i> Rejected
                                                                </span>
                                                            @endif
                                                            @if($module->is_published)
                                                                <span class="badge" style="background: #e8f5e9; color: #27AE60; font-size: 10px; padding: 3px 8px; border-radius: 4px; font-weight: 500; white-space: nowrap; margin-top: 2px;">
                                                                    Published
                                                                </span>
                                                            @else
                                                                <span class="badge" style="background: #fce4ec; color: #c2185b; font-size: 10px; padding: 3px 8px; border-radius: 4px; font-weight: 500; white-space: nowrap; margin-top: 2px;">
                                                                    Hidden
                                                                </span>
                                                            @endif
                                                            <small style="color: #828282; font-size: 10px; margin-top: 2px;">
                                                                Views: {{ $module->view_count ?? 0 }}
                                                            </small>
                                                        </div>
                                                    </td>
                                                    <td style="padding: 16px 8px; vertical-align: middle; text-align: right;">
                                                        <div style="display: flex; gap: 6px; align-items: center; justify-content: flex-end; flex-wrap: wrap;">
                                                            <a href="{{ route('admin.modules.preview', $module->id) }}" 
                                                               style="background: transparent; color: #2F80ED; border: 1px solid #90caf9; padding: 6px 12px; font-size: 11px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; white-space: nowrap;"
                                                               title="Preview Module (Admin Mode)"
                                                               target="_blank"
                                                               onmouseover="this.style.background='#e3f2fd'; this.style.borderColor='#2F80ED';" 
                                                               onmouseout="this.style.background='transparent'; this.style.borderColor='#90caf9';">
                                                                <i class="fas fa-eye" style="font-size: 10px;"></i>Preview
                                                            </a>
                                                            @if($isPending)
                                                                <button type="button" 
                                                                        onclick="showApproveModal({{ $module->id }}, '{{ addslashes($module->title) }}')"
                                                                        style="background: #27AE60; color: white; border: none; padding: 6px 12px; font-size: 11px; border-radius: 6px; cursor: pointer; white-space: nowrap;"
                                                                        title="Approve Module">
                                                                    <i class="fas fa-check"></i> Approve
                                                                </button>
                                                                <button type="button" 
                                                                        onclick="showRejectModal({{ $module->id }}, '{{ addslashes($module->title) }}')"
                                                                        style="background: #e53935; color: white; border: none; padding: 6px 12px; font-size: 11px; border-radius: 6px; cursor: pointer; white-space: nowrap;"
                                                                        title="Reject Module">
                                                                    <i class="fas fa-times"></i> Reject
                                                                </button>
                                                            @endif
                                                        </div>
                                                        @if($module->admin_feedback)
                                                            <div style="margin-top: 8px; padding: 8px; background: #fff3cd; border-radius: 4px; border-left: 3px solid #ffc107;">
                                                                <small style="color: #856404; font-size: 10px; font-weight: 600;">Admin Feedback:</small>
                                                                <p style="color: #856404; font-size: 11px; margin: 4px 0 0 0;">{{ $module->admin_feedback }}</p>
                                                            </div>
                                                        @endif
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
                                            style="background: {{ $chapter->is_published ? '#ff9800' : '#27AE60' }}; color: white; border: none; padding: 8px; font-size: 14px; border-radius: 6px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; transition: opacity 0.2s; flex-shrink: 0;"
                                            title="{{ $chapter->is_published ? 'Suspend Chapter' : 'Activate Chapter' }}"
                                            onmouseover="this.style.opacity='0.8'" 
                                            onmouseout="this.style.opacity='1'">
                                        @if($chapter->is_published)
                                            <i class="fas fa-ban"></i>
                                        @else
                                            <i class="fas fa-check-circle"></i>
                                        @endif
                                    </button>
                                </form>
                                <!-- Delete Section Button -->
                                <form action="{{ route('admin.chapters.destroy', $chapter->id) }}" method="POST" style="display: inline; margin: 0;"
                                      onsubmit="return confirm('Are you sure you want to delete this section? All modules inside it will also be deleted.');">
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

<!-- Back Buttons (Below Chapter Panel) -->
<div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; margin-bottom: 24px; flex-wrap: wrap;">
    <a href="{{ route('admin.courses.index') }}" class="btn" style="background: #6c757d; color: white; border: none; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 500; box-shadow: 0 2px 8px rgba(108, 117, 125, 0.2); transition: all 0.3s ease;"
       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(108, 117, 125, 0.3)';"
       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(108, 117, 125, 0.2)';">
        <i class="fas fa-arrow-left me-2"></i>Back to Courses
    </a>
    <a href="{{ route('admin.dashboard') }}" class="btn" style="background: #2F80ED; color: white; border: none; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 500; box-shadow: 0 2px 8px rgba(47, 128, 237, 0.2); transition: all 0.3s ease;"
       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(47, 128, 237, 0.3)';"
       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(47, 128, 237, 0.2)';">
        <i class="fas fa-home me-2"></i>Dashboard
    </a>
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

    // Scroll ke module jika ada module_id di URL
    @if(isset($moduleId) && $moduleId)
        const moduleRow = document.getElementById('module-{{ $moduleId }}');
        if (moduleRow) {
            // Buka chapter yang berisi module ini
            const chapterCollapse = moduleRow.closest('.accordion-collapse');
            if (chapterCollapse) {
                const chapterId = chapterCollapse.getAttribute('id');
                const chapterHeader = document.querySelector('[data-bs-target="#' + chapterId + '"]');
                if (chapterHeader && !chapterCollapse.classList.contains('show')) {
                    chapterHeader.click();
                }
                // Scroll ke module setelah chapter terbuka
                setTimeout(() => {
                    moduleRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 300);
            }
        }
    @endif
});

function showApproveModal(moduleId, moduleTitle) {
    document.getElementById('approveModuleId').value = moduleId;
    document.getElementById('approveModuleTitle').textContent = moduleTitle;
    new bootstrap.Modal(document.getElementById('approveModal')).show();
}

function showRejectModal(moduleId, moduleTitle) {
    document.getElementById('rejectModuleId').value = moduleId;
    document.getElementById('rejectModuleTitle').textContent = moduleTitle;
    document.getElementById('rejectFeedback').value = '';
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>

<!-- Approve Module Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel">Approve Module</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="approveForm">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to approve module <strong id="approveModuleTitle"></strong>?</p>
                    <p class="text-muted small">Module will be published after approval.</p>
                    <input type="hidden" id="approveModuleId" name="module_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve & Publish</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Module Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Reject Module</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="rejectForm">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to reject the module <strong id="rejectModuleTitle"></strong>?</p>
                    <div class="mb-3">
                        <label for="rejectFeedback" class="form-label small">Reason for Rejection <span class="text-danger">*</span>:</label>
                        <textarea class="form-control" id="rejectFeedback" name="admin_feedback" rows="4" maxlength="1000" placeholder="Provide reason for rejection..." required></textarea>
                        <small class="text-muted">This feedback will be sent to the teacher.</small>
                    </div>
                    <input type="hidden" id="rejectModuleId" name="module_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Module</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Set form action when modal opens
document.getElementById('approveModal').addEventListener('show.bs.modal', function(event) {
    const moduleId = document.getElementById('approveModuleId').value;
    document.getElementById('approveForm').action = '{{ route("admin.modules.approve", ":id") }}'.replace(':id', moduleId);
});

document.getElementById('rejectModal').addEventListener('show.bs.modal', function(event) {
    const moduleId = document.getElementById('rejectModuleId').value;
    document.getElementById('rejectForm').action = '{{ route("admin.modules.reject", ":id") }}'.replace(':id', moduleId);
});
</script>
@endsection
