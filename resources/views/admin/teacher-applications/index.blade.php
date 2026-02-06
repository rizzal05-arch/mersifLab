@extends('layouts.admin')

@section('title', 'Teacher Applications')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold">
                    <i class="fas fa-user-graduate me-2"></i>
                    Teacher Applications
                </h4>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" id="statusFilter" style="width: auto;">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                            <i class="fas fa-clock me-1"></i>Pending ({{ \App\Models\TeacherApplication::where('status', 'pending')->count() }})
                        </option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>
                            <i class="fas fa-check-circle me-1"></i>Approved ({{ \App\Models\TeacherApplication::where('status', 'approved')->count() }})
                        </option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>
                            <i class="fas fa-times-circle me-1"></i>Rejected ({{ \App\Models\TeacherApplication::where('status', 'rejected')->count() }})
                        </option>
                    </select>
                </div>
            </div>

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

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    @forelse($applications as $application)
                        <div class="application-card border-bottom p-4">
                            <div class="row align-items-center">
                                <!-- Profile Section -->
                                <div class="col-lg-3 col-md-4 mb-3 mb-md-0">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-lg position-relative">
                                            @php
                                                $photoPath = null;
                                                if($application->user && $application->user->avatar) {
                                                    $avatar = $application->user->avatar;
                                                    if(str_starts_with($avatar, 'http')) {
                                                        $photoPath = $avatar;
                                                    } else {
                                                        $photoPath = asset('storage/' . ltrim($avatar, '/'));
                                                    }
                                                }
                                            @endphp
                                            @if($photoPath)
                                                <img src="{{ $photoPath }}" 
                                                     alt="{{ $application->full_name }}" 
                                                     class="rounded-circle" 
                                                     style="width: 80px; height: 80px; object-fit: cover; border: 3px solid #e9ecef;">
                                            @else
                                                <div class="avatar-circle bg-gradient-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                     style="width: 80px; height: 80px; font-size: 32px; font-weight: bold; border: 3px solid #e9ecef;">
                                                    {{ strtoupper(substr($application->full_name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-bold text-dark">{{ $application->full_name }}</h6>
                                            <small class="text-muted d-block mb-1">
                                                <i class="fas fa-envelope me-1"></i>{{ $application->email }}
                                            </small>
                                            <small class="text-muted d-block">
                                                <i class="fas fa-phone me-1"></i>{{ $application->phone }}
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status & Date Section -->
                                <div class="col-lg-2 col-md-3 mb-3 mb-md-0">
                                    <div class="mb-2">
                                        @if($application->isPending())
                                            <span class="badge badge-lg bg-warning text-dark" style="padding: 0.5rem 0.75rem; font-size: 0.875rem;">
                                                <i class="fas fa-clock me-1"></i>Pending
                                            </span>
                                        @elseif($application->isApproved())
                                            <span class="badge badge-lg bg-success" style="padding: 0.5rem 0.75rem; font-size: 0.875rem;">
                                                <i class="fas fa-check-circle me-1"></i>Approved
                                            </span>
                                        @else
                                            <span class="badge badge-lg bg-danger" style="padding: 0.5rem 0.75rem; font-size: 0.875rem;">
                                                <i class="fas fa-times-circle me-1"></i>Rejected
                                            </span>
                                        @endif
                                    </div>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-calendar-alt me-1"></i>{{ $application->created_at->format('M d, Y') }}
                                    </small>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-clock me-1"></i>{{ $application->created_at->format('g:i A') }}
                                    </small>
                                </div>

                                <!-- Files & Notes Section -->
                                <div class="col-lg-4 col-md-5 mb-3 mb-md-0">
                                    <div class="mb-2">
                                        <span class="badge bg-light text-dark" style="padding: 0.5rem 0.75rem; font-size: 0.8rem;">
                                            <i class="fas fa-file-alt me-1"></i>KTP
                                        </span>
                                        <span class="badge bg-light text-dark" style="padding: 0.5rem 0.75rem; font-size: 0.8rem;">
                                            <i class="fas fa-certificate me-1"></i>Certificate
                                        </span>
                                        <span class="badge bg-light text-dark" style="padding: 0.5rem 0.75rem; font-size: 0.8rem;">
                                            <i class="fas fa-building me-1"></i>Institution ID
                                        </span>
                                        <span class="badge bg-light text-dark" style="padding: 0.5rem 0.75rem; font-size: 0.8rem;">
                                            <i class="fas fa-briefcase me-1"></i>Portfolio
                                        </span>
                                    </div>
                                    @if($application->admin_notes)
                                        <div class="alert alert-info alert-sm py-2 px-3 mb-0" style="font-size: 0.85rem;">
                                            <i class="fas fa-sticky-note me-1"></i>
                                            <strong>Notes:</strong> {{ Str::limit($application->admin_notes, 60) }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Action Buttons -->
                                <div class="col-lg-3 col-md-12 text-lg-end">
                                    <div class="action-buttons d-flex align-items-center justify-content-end flex-wrap gap-3">

                                        <a href="{{ route('admin.teacher-applications.show', $application) }}"
                                           style="background: transparent; color: #1976d2; border: none; padding: 0; font-size: 14px; font-weight: 500; cursor: pointer; text-decoration: none; transition: all 0.2s;"
                                           onmouseover="this.style.color='#0d5ed0'; this.style.transform='translateY(-2px)';"
                                           onmouseout="this.style.color='#1976d2'; this.style.transform='translateY(0)';"
                                           title="View Details">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>

                                        @if($application->isPending())
                                            <button type="button"
                                                    style="background: transparent; color: #27AE60; border: none; padding: 0; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.2s;"
                                                    onmouseover="this.style.color='#1e7e34'; this.style.transform='translateY(-2px)';"
                                                    onmouseout="this.style.color='#27AE60'; this.style.transform='translateY(0)';"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#approveModal{{ $application->id }}"
                                                    title="Approve Application">
                                                <i class="fas fa-check-circle me-1"></i>Approve
                                            </button>

                                            <button type="button"
                                                    style="background: transparent; color: #dc3545; border: none; padding: 0; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.2s;"
                                                    onmouseover="this.style.color='#bb2d3b'; this.style.transform='translateY(-2px)';"
                                                    onmouseout="this.style.color='#dc3545'; this.style.transform='translateY(0)';"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#rejectModal{{ $application->id }}"
                                                    title="Reject Application">
                                                <i class="fas fa-times-circle me-1"></i>Reject
                                            </button>
                                        @endif

                                        <form action="{{ route('admin.teacher-applications.destroy', $application) }}"
                                              method="POST"
                                              class="m-0 p-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    style="background: transparent; color: #c62828; border: none; padding: 0; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.2s;"
                                                    onmouseover="this.style.color='#a02622'; this.style.transform='translateY(-2px)';"
                                                    onmouseout="this.style.color='#c62828'; this.style.transform='translateY(0)';"
                                                    onclick="return confirm('Delete this application?')"
                                                    title="Delete Application">
                                                <i class="fas fa-trash me-1"></i>Delete
                                            </button>
                                        </form>

                                    </div>
                                </div>



                        <!-- Approve Modal -->
                        @if($application->isPending())
                        <div class="modal fade" id="approveModal{{ $application->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.teacher-applications.approve', $application) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                Approve Teacher Application
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Approving this application will upgrade the user's role to "teacher" and grant them access to teacher features.
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Applicant Details:</label>
                                                <div class="bg-light p-3 rounded">
                                                    <div><strong>Name:</strong> {{ $application->full_name }}</div>
                                                    <div><strong>Email:</strong> {{ $application->email }}</div>
                                                    <div><strong>Phone:</strong> {{ $application->phone }}</div>
                                                    <div><strong>Submitted:</strong> {{ $application->created_at->format('F d, Y - g:i A') }}</div>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="approve_notes_{{ $application->id }}" class="form-label">Approval Message (Optional)</label>
                                                <textarea class="form-control" id="approve_notes_{{ $application->id }}" 
                                                          name="admin_notes" rows="3" placeholder="Add any notes for this approval..."></textarea>
                                                <small class="text-muted">This message will be visible to the applicant</small>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-check me-2"></i>Approve Application
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Reject Modal -->
                        <div class="modal fade" id="rejectModal{{ $application->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.teacher-applications.reject', $application) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="fas fa-times-circle text-danger me-2"></i>
                                                Reject Teacher Application
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                Rejecting this application will prevent the user from becoming a teacher. They can submit a new application later.
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Applicant Details:</label>
                                                <div class="bg-light p-3 rounded">
                                                    <div><strong>Name:</strong> {{ $application->full_name }}</div>
                                                    <div><strong>Email:</strong> {{ $application->email }}</div>
                                                    <div><strong>Phone:</strong> {{ $application->phone }}</div>
                                                    <div><strong>Submitted:</strong> {{ $application->created_at->format('F d, Y - g:i A') }}</div>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="reject_notes_{{ $application->id }}" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                                                <textarea class="form-control" id="reject_notes_{{ $application->id }}" 
                                                          name="admin_notes" rows="4" required placeholder="Please provide a clear reason for rejection..."></textarea>
                                                <small class="text-muted">This message will be visible to the applicant and help them improve their application</small>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-times me-2"></i>Reject Application
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                    @empty
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">No teacher applications found</h5>
                            <p class="text-muted">Applications will appear here when users apply to become teachers.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Pagination -->
            @if($applications->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $applications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Status filter
    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            const url = new URL(window.location);
            if (this.value) {
                url.searchParams.set('status', this.value);
            } else {
                url.searchParams.delete('status');
            }
            window.location.href = url.toString();
        });
    }
});
</script>
@endsection

<!-- Quick File Viewer Modal -->
<div class="modal fade" id="quickFileViewerModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-folder-open me-2"></i>
                    Quick File Viewer
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-id-card me-2"></i>KTP/ID Card
                                </h6>
                            </div>
                            <div class="card-body text-center p-2">
                                <div id="ktpPreview" class="file-preview-container">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-certificate me-2"></i>Teaching Certificate
                                </h6>
                            </div>
                            <div class="card-body text-center p-2">
                                <div id="certificatePreview" class="file-preview-container">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-building me-2"></i>Institution ID
                                </h6>
                            </div>
                            <div class="card-body text-center p-2">
                                <div id="institutionPreview" class="file-preview-container">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-briefcase me-2"></i>Portfolio
                                </h6>
                            </div>
                            <div class="card-body text-center p-2">
                                <div id="portfolioPreview" class="file-preview-container">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" id="viewDetailsBtn" class="btn btn-primary">
                    <i class="fas fa-eye me-2"></i>View Full Details
                </a>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Application Card Styles */
.application-card {
    transition: all 0.3s ease;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
}

.application-card:hover {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
}

.avatar-lg {
    transition: transform 0.3s ease;
}

.application-card:hover .avatar-lg {
    transform: scale(1.05);
}

.avatar-circle {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    font-weight: bold;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

/* Badge Styling */
.badge-lg {
    font-weight: 600;
    letter-spacing: 0.5px;
}

/* Button Group Responsive */
.btn-group {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.btn-group .btn {
    font-weight: 500;
    border-radius: 0.375rem;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.btn-group .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

/* Action Buttons */
/* ===== Action Links (No Border Style) ===== */
.action-links {
    display: flex;
    gap: 14px;
    align-items: center;
    justify-content: flex-end;
    flex-wrap: wrap;
}

.action-link {
    background: none !important;
    border: none !important;
    padding: 0;
    font-size: 13px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    text-decoration: none;
    transition: opacity 0.2s ease, text-decoration 0.2s ease;
}

/* warna */
.action-link.view    { color: #1e88e5; }
.action-link.approve { color: #2e7d32; }
.action-link.reject  { color: #d32f2f; }
.action-link.delete  { color: #c62828; }

/* hover */
.action-link:hover {
    opacity: 0.75;
    text-decoration: underline;
}

/* form tetap inline */
.action-form {
    margin: 0;
    display: inline-flex;
    align-items: center;
}


/* Alert Small Styles */
.alert-sm {
    border-radius: 0.25rem;
    border: 1px solid rgba(13, 110, 253, 0.25);
    background-color: rgba(13, 110, 253, 0.05);
}

/* File Preview Container */
.file-preview-container {
    min-height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.file-preview-container img {
    max-width: 100%;
    max-height: 180px;
    object-fit: contain;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transition: transform 0.3s ease;
}

.file-preview-container img:hover {
    transform: scale(1.05);
}

.file-preview-container .file-icon {
    font-size: 3rem;
    color: #6c757d;
}

.file-preview-container .file-info {
    text-align: center;
    padding: 10px;
}

.file-preview-container .file-info small {
    color: #6c757d;
}

/* Responsive Adjustments */
@media (max-width: 1024px) {
    .btn-group {
        justify-content: flex-start;
    }
}

@media (max-width: 768px) {
    .application-card {
        padding: 1.5rem !important;
    }
    
    .btn-group {
        width: 100%;
        margin-top: 1rem;
    }
    
    .btn-group .btn {
        flex: 1;
        min-width: auto;
    }
}

/* Gradient Background */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* Card Enhancements */
.card {
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.card-body {
    padding: 0;
}

/* Text Enhancements */
h6 {
    color: #1f2937;
}

small.text-muted {
    color: #6b7280 !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const quickFileViewerModal = document.getElementById('quickFileViewerModal');
    const viewDetailsBtn = document.getElementById('viewDetailsBtn');
    
    quickFileViewerModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const ktpUrl = button.getAttribute('data-ktp');
        const certificateUrl = button.getAttribute('data-certificate');
        const institutionUrl = button.getAttribute('data-institution');
        const portfolioUrl = button.getAttribute('data-portfolio');
        
        // Set view details button href (will be updated when we know which application)
        viewDetailsBtn.href = '#';
        
        // Load previews
        loadFilePreview('ktpPreview', ktpUrl, 'KTP/ID Card');
        loadFilePreview('certificatePreview', certificateUrl, 'Teaching Certificate');
        loadFilePreview('institutionPreview', institutionUrl, 'Institution ID');
        loadFilePreview('portfolioPreview', portfolioUrl, 'Portfolio');
        
        // Find the application ID from the button's parent
        const applicationCard = button.closest('[id^="approveModal"]');
        if (applicationCard) {
            const applicationId = applicationCard.id.replace('approveModal', '');
            viewDetailsBtn.href = `/admin/teacher-applications/${applicationId}`;
        }
    });
    
    function loadFilePreview(containerId, fileUrl, filename) {
        const container = document.getElementById(containerId);
        if (!fileUrl) {
            container.innerHTML = '<div class="file-info"><i class="fas fa-times-circle fa-2x text-muted"></i><br><small>No file</small></div>';
            return;
        }
        
        console.log('Loading preview:', { containerId, fileUrl, filename });
        
        // Show loading
        container.innerHTML = '<div class="loading-spinner"></div>';
        
        const img = new Image();
        img.onload = function() {
            console.log('Image loaded successfully:', filename);
            container.innerHTML = `<img src="${fileUrl}" alt="${filename}" class="clickable-image" data-full="${fileUrl}" style="max-width:100%; max-height:180px; object-fit:contain; border-radius:4px; box-shadow:0 2px 4px rgba(0,0,0,0.1);">`;
            
            // Add click event for full view
            const clickableImg = container.querySelector('.clickable-image');
            if (clickableImg) {
                clickableImg.style.cursor = 'pointer';
                clickableImg.addEventListener('click', function() {
                    console.log('Opening full viewer for:', filename);
                    // Open in full viewer modal
                    const fullViewerBtn = document.createElement('button');
                    fullViewerBtn.setAttribute('data-bs-toggle', 'modal');
                    fullViewerBtn.setAttribute('data-bs-target', '#fileViewerModal');
                    fullViewerBtn.setAttribute('data-file', fileUrl);
                    fullViewerBtn.setAttribute('data-filename', filename);
                    fullViewerBtn.setAttribute('data-type', getFileExtension(fileUrl));
                    fullViewerBtn.click();
                });
            }
        };
        img.onerror = function() {
            console.error('Failed to load image:', fileUrl);
            const ext = getFileExtension(fileUrl);
            const icon = getFileIcon(ext);
            container.innerHTML = `
                <div class="file-info">
                    <i class="fas ${icon} fa-2x text-muted mb-2"></i><br>
                    <small>${ext.toUpperCase()} File</small><br>
                    <small class="text-muted">Click to download</small>
                </div>
            `;
            
            // Make container clickable for download
            container.style.cursor = 'pointer';
            container.addEventListener('click', function() {
                console.log('Downloading file:', fileUrl);
                window.open(fileUrl, '_blank');
            });
        };
        img.src = fileUrl;
    }
    
    function getFileExtension(url) {
        return url.split('.').pop().split('?')[0];
    }
    
    function getFileIcon(extension) {
        const icons = {
            'pdf': 'fa-file-pdf',
            'doc': 'fa-file-word',
            'docx': 'fa-file-word',
            'zip': 'fa-file-archive',
            'rar': 'fa-file-archive',
            'default': 'fa-file'
        };
        return icons[extension.toLowerCase()] || icons['default'];
    }
    
    // Clear previews when modal is hidden
    quickFileViewerModal.addEventListener('hide.bs.modal', function() {
        ['ktpPreview', 'certificatePreview', 'institutionPreview', 'portfolioPreview'].forEach(id => {
            document.getElementById(id).innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        });
    });
});
</script>
@endpush
