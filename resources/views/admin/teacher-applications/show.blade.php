@extends('layouts.admin')

@section('title', 'Teacher Application Details - #' . $teacherApplication->id)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold">
                    <i class="fas fa-user-graduate me-2"></i>
                    Teacher Application #{{ $teacherApplication->id }}
                </h4>
                <div>
                    <a href="{{ route('admin.teacher-applications.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Applications
                    </a>
                </div>
            </div>

            <!-- Status Badge -->
            <div class="text-center mb-4">
                @if($teacherApplication->isPending())
                    <span class="badge bg-warning text-dark fs-6 px-4 py-2">
                        <i class="fas fa-clock me-2"></i>Under Review
                    </span>
                @elseif($teacherApplication->isApproved())
                    <span class="badge bg-success fs-6 px-4 py-2">
                        <i class="fas fa-check-circle me-2"></i>Approved
                    </span>
                @else
                    <span class="badge bg-danger fs-6 px-4 py-2">
                        <i class="fas fa-times-circle me-2"></i>Rejected
                    </span>
                @endif
            </div>

            <div class="row">
                <!-- Applicant Information -->
                <div class="col-lg-6">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-user me-2"></i>Applicant Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Full Name:</strong></div>
                                <div class="col-sm-8">{{ $teacherApplication->full_name }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Email:</strong></div>
                                <div class="col-sm-8">{{ $teacherApplication->email }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Phone:</strong></div>
                                <div class="col-sm-8">{{ $teacherApplication->phone }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Address:</strong></div>
                                <div class="col-sm-8">{{ $teacherApplication->address }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>User ID:</strong></div>
                                <div class="col-sm-8">{{ $teacherApplication->user_id }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Current Role:</strong></div>
                                <div class="col-sm-8">
                                    <span class="badge bg-info">{{ $teacherApplication->user->role }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Application Timeline -->
                <div class="col-lg-6">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-clock me-2"></i>Application Timeline
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Submitted:</strong></div>
                                <div class="col-sm-8">{{ $teacherApplication->created_at->format('F d, Y - g:i A') }}</div>
                            </div>
                            @if($teacherApplication->reviewed_at)
                                <div class="row mb-3">
                                    <div class="col-sm-4"><strong>Reviewed:</strong></div>
                                    <div class="col-sm-8">{{ $teacherApplication->reviewed_at->format('F d, Y - g:i A') }}</div>
                                </div>
                                @if($teacherApplication->reviewer)
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Reviewed By:</strong></div>
                                        <div class="col-sm-8">{{ $teacherApplication->reviewer->name }}</div>
                                    </div>
                                @endif
                            @endif
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Status:</strong></div>
                                <div class="col-sm-8">
                                    @if($teacherApplication->isPending())
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($teacherApplication->isApproved())
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Teaching Experience -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-graduation-cap me-2"></i>Teaching Experience
                    </h6>
                </div>
                <div class="card-body">
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0">{{ $teacherApplication->teaching_experience }}</p>
                    </div>
                </div>
            </div>

            <!-- Admin Notes -->
            @if($teacherApplication->admin_notes)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-sticky-note me-2"></i>Admin Notes
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-0">
                        {{ $teacherApplication->admin_notes }}
                    </div>
                </div>
            </div>
            @endif

            <!-- File Verification Section -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>Document Verification
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h6 class="mb-1">
                                            <i class="fas fa-id-card text-primary me-2"></i>KTP/ID Card
                                        </h6>
                                        <small class="text-muted">Identity verification document</small>
                                    </div>
                                    <a href="{{ route('admin.teacher-applications.download', [$teacherApplication, 'ktp']) }}" 
                                       class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-info ms-1" 
                                            data-bs-toggle="modal" data-bs-target="#fileViewerModal"
                                            data-file="{{ $teacherApplication->getFileUrl('ktp_file') }}"
                                            data-filename="KTP/ID Card"
                                            data-type="{{ pathinfo($teacherApplication->ktp_file, PATHINFO_EXTENSION) }}">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                </div>
                                <div class="text-center">
                                    @if(in_array(pathinfo($teacherApplication->ktp_file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png']))
                                        <img src="{{ $teacherApplication->getFileUrl('ktp_file') }}" 
                                             class="img-fluid rounded" style="max-height: 200px;" 
                                             alt="KTP/ID Card">
                                    @else
                                        <div class="bg-light p-4 rounded">
                                            <i class="fas fa-file-pdf fa-3x text-muted mb-2"></i>
                                            <p class="text-muted small mb-0">PDF Document</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h6 class="mb-1">
                                            <i class="fas fa-certificate text-primary me-2"></i>Teaching Certificate
                                        </h6>
                                        <small class="text-muted">Professional teaching qualification</small>
                                    </div>
                                    <a href="{{ route('admin.teacher-applications.download', [$teacherApplication, 'certificate']) }}" 
                                       class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-info ms-1" 
                                            data-bs-toggle="modal" data-bs-target="#fileViewerModal"
                                            data-file="{{ $teacherApplication->getFileUrl('teaching_certificate_file') }}"
                                            data-filename="Teaching Certificate"
                                            data-type="{{ pathinfo($teacherApplication->teaching_certificate_file, PATHINFO_EXTENSION) }}">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                </div>
                                <div class="text-center">
                                    @if(in_array(pathinfo($teacherApplication->teaching_certificate_file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png']))
                                        <img src="{{ $teacherApplication->getFileUrl('teaching_certificate_file') }}" 
                                             class="img-fluid rounded" style="max-height: 200px;" 
                                             alt="Teaching Certificate">
                                    @else
                                        <div class="bg-light p-4 rounded">
                                            <i class="fas fa-file-pdf fa-3x text-muted mb-2"></i>
                                            <p class="text-muted small mb-0">PDF Document</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h6 class="mb-1">
                                            <i class="fas fa-building text-primary me-2"></i>Institution ID
                                        </h6>
                                        <small class="text-muted">Educational institution identification</small>
                                    </div>
                                    <a href="{{ route('admin.teacher-applications.download', [$teacherApplication, 'institution']) }}" 
                                       class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-info ms-1" 
                                            data-bs-toggle="modal" data-bs-target="#fileViewerModal"
                                            data-file="{{ $teacherApplication->getFileUrl('institution_id_file') }}"
                                            data-filename="Institution ID"
                                            data-type="{{ pathinfo($teacherApplication->institution_id_file, PATHINFO_EXTENSION) }}">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                </div>
                                <div class="text-center">
                                    @if(in_array(pathinfo($teacherApplication->institution_id_file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png']))
                                        <img src="{{ $teacherApplication->getFileUrl('institution_id_file') }}" 
                                             class="img-fluid rounded" style="max-height: 200px;" 
                                             alt="Institution ID">
                                    @else
                                        <div class="bg-light p-4 rounded">
                                            <i class="fas fa-file-pdf fa-3x text-muted mb-2"></i>
                                            <p class="text-muted small mb-0">PDF Document</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h6 class="mb-1">
                                            <i class="fas fa-briefcase text-primary me-2"></i>Portfolio
                                        </h6>
                                        <small class="text-muted">Work samples and achievements</small>
                                    </div>
                                    <a href="{{ route('admin.teacher-applications.download', [$teacherApplication, 'portfolio']) }}" 
                                       class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-info ms-1" 
                                            data-bs-toggle="modal" data-bs-target="#fileViewerModal"
                                            data-file="{{ $teacherApplication->getFileUrl('portfolio_file') }}"
                                            data-filename="Portfolio"
                                            data-type="{{ pathinfo($teacherApplication->portfolio_file, PATHINFO_EXTENSION) }}">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                </div>
                                <div class="text-center">
                                    <div class="bg-light p-4 rounded">
                                        <i class="fas fa-file-archive fa-3x text-muted mb-2"></i>
                                        <p class="text-muted small mb-0">
                                            {{ strtoupper(pathinfo($teacherApplication->portfolio_file, PATHINFO_EXTENSION)) }} File
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            @if($teacherApplication->isPending())
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h6 class="mb-3">Application Review Actions</h6>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-success btn-lg" 
                                data-bs-toggle="modal" data-bs-target="#approveModal">
                            <i class="fas fa-check me-2"></i>Approve Application
                        </button>
                        <button type="button" class="btn btn-danger btn-lg" 
                                data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="fas fa-times me-2"></i>Reject Application
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Approve Modal -->
@if($teacherApplication->isPending())
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.teacher-applications.approve', $teacherApplication) }}" method="POST">
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
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">Applicant Details:</h6>
                            <div class="bg-light p-3 rounded">
                                <div class="mb-2"><strong>Name:</strong> {{ $teacherApplication->full_name }}</div>
                                <div class="mb-2"><strong>Email:</strong> {{ $teacherApplication->email }}</div>
                                <div class="mb-2"><strong>Phone:</strong> {{ $teacherApplication->phone }}</div>
                                <div><strong>Submitted:</strong> {{ $teacherApplication->created_at->format('F d, Y - g:i A') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">Approval Message:</h6>
                            <textarea class="form-control" name="admin_notes" rows="6" 
                                      placeholder="Add any notes for this approval (optional)..."></textarea>
                            <small class="text-muted">This message will be visible to the applicant</small>
                        </div>
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
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.teacher-applications.reject', $teacherApplication) }}" method="POST">
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
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">Applicant Details:</h6>
                            <div class="bg-light p-3 rounded">
                                <div class="mb-2"><strong>Name:</strong> {{ $teacherApplication->full_name }}</div>
                                <div class="mb-2"><strong>Email:</strong> {{ $teacherApplication->email }}</div>
                                <div class="mb-2"><strong>Phone:</strong> {{ $teacherApplication->phone }}</div>
                                <div><strong>Submitted:</strong> {{ $teacherApplication->created_at->format('F d, Y - g:i A') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">Rejection Reason <span class="text-danger">*</span></h6>
                            <textarea class="form-control" name="admin_notes" rows="6" required 
                                      placeholder="Please provide a clear reason for rejection..."></textarea>
                            <small class="text-muted">This message will be visible to the applicant and help them improve their application</small>
                        </div>
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
@endsection

<!-- File Viewer Modal -->
<div class="modal fade" id="fileViewerModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-alt me-2"></i>
                    <span id="fileViewerTitle">Document Viewer</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" style="height: 80vh;">
                <div id="fileViewerContent" class="w-100 h-100 d-flex align-items-center justify-content-center">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a id="downloadButton" href="#" class="btn btn-primary" target="_blank">
                    <i class="fas fa-download me-2"></i>Download
                </a>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf_viewer.min.css">
<style>
.pdf-viewer {
    width: 100%;
    height: 100%;
}

.pdf-viewer .page {
    border: 1px solid #ddd;
    margin-bottom: 10px;
}

.image-viewer {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.file-info {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
}

.loading-spinner {
    display: inline-block;
    width: 50px;
    height: 50px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
// Check if pdfjsLib is loaded
if (typeof pdfjsLib === 'undefined') {
    console.error('PDF.js not loaded, using fallback');
    window.pdfjsLib = {
        getDocument: function() {
            return Promise.reject(new Error('PDF.js not available'));
        }
    };
}

if (pdfjsLib && pdfjsLib.GlobalWorkerOptions) {
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
}

document.addEventListener('DOMContentLoaded', function() {
    const fileViewerModal = document.getElementById('fileViewerModal');
    const fileViewerContent = document.getElementById('fileViewerContent');
    const fileViewerTitle = document.getElementById('fileViewerTitle');
    const downloadButton = document.getElementById('downloadButton');
    
    if (!fileViewerModal || !fileViewerContent) {
        console.error('File viewer elements not found');
        return;
    }
    
    fileViewerModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const fileUrl = button.getAttribute('data-file');
        const filename = button.getAttribute('data-filename');
        const fileType = button.getAttribute('data-type').toLowerCase();
        
        console.log('Loading file:', { fileUrl, filename, fileType });
        
        fileViewerTitle.textContent = filename;
        downloadButton.href = fileUrl;
        downloadButton.download = filename + '.' + fileType;
        
        // Show loading
        fileViewerContent.innerHTML = `
            <div class="d-flex flex-column align-items-center justify-content-center h-100">
                <div class="loading-spinner mb-3"></div>
                <div class="text-muted">Loading ${fileType.toUpperCase()} file...</div>
            </div>
        `;
        
        // Add small delay to show loading
        setTimeout(() => {
            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileType)) {
                loadImage(fileUrl, filename);
            } else if (fileType === 'pdf') {
                loadPDF(fileUrl);
            } else {
                showFileInfo(filename, fileType, fileUrl);
            }
        }, 500);
    });
    
    function loadImage(url, filename) {
        console.log('Loading image:', url);
        const img = document.createElement('img');
        img.className = 'image-viewer';
        img.src = url;
        img.alt = filename;
        img.style.maxWidth = '100%';
        img.style.maxHeight = '100%';
        img.style.objectFit = 'contain';
        
        img.onload = function() {
            console.log('Image loaded successfully');
            fileViewerContent.innerHTML = '';
            fileViewerContent.appendChild(img);
        };
        
        img.onerror = function() {
            console.error('Failed to load image:', url);
            showError('Failed to load image. The file may be corrupted or not accessible.');
        };
    }
    
    function loadPDF(url) {
        console.log('Loading PDF:', url);
        
        // Check if PDF.js is available
        if (typeof pdfjsLib === 'undefined' || !pdfjsLib.getDocument) {
            console.error('PDF.js not available');
            showPDFFallback(url);
            return;
        }
        
        fileViewerContent.innerHTML = `
            <div class="d-flex flex-column align-items-center justify-content-center h-100">
                <div class="loading-spinner mb-3"></div>
                <div class="text-muted">Loading PDF document...</div>
            </div>
        `;
        
        pdfjsLib.getDocument(url).promise.then(function(pdf) {
            console.log('PDF loaded, pages:', pdf.numPages);
            
            if (pdf.numPages === 0) {
                showError('PDF has no pages');
                return;
            }
            
            // Create container for PDF pages
            const container = document.createElement('div');
            container.className = 'pdf-viewer';
            container.style.overflow = 'auto';
            container.style.height = '100%';
            container.style.padding = '10px';
            container.style.backgroundColor = '#525652';
            
            // Load first page
            pdf.getPage(1).then(function(page) {
                const scale = 1.5;
                const viewport = page.getViewport({ scale: scale });
                
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                canvas.className = 'page';
                canvas.style.margin = '0 auto 10px';
                canvas.style.display = 'block';
                canvas.style.boxShadow = '0 2px 5px rgba(0,0,0,0.1)';
                canvas.style.backgroundColor = 'white';
                
                const renderContext = {
                    canvasContext: context,
                    viewport: viewport
                };
                
                page.render(renderContext).promise.then(function() {
                    console.log('PDF page rendered');
                    fileViewerContent.innerHTML = '';
                    container.appendChild(canvas);
                    
                    // Add page navigation if more than 1 page
                    if (pdf.numPages > 1) {
                        const pageInfo = document.createElement('div');
                        pageInfo.className = 'text-center text-white mb-2';
                        pageInfo.innerHTML = `<small>Page 1 of ${pdf.numPages}</small>`;
                        container.insertBefore(pageInfo, canvas);
                    }
                    
                    fileViewerContent.appendChild(container);
                }).catch(function(error) {
                    console.error('Error rendering PDF page:', error);
                    showError('Failed to render PDF page: ' + error.message);
                });
            }).catch(function(error) {
                console.error('Error getting PDF page:', error);
                showError('Failed to read PDF page: ' + error.message);
            });
        }).catch(function(error) {
            console.error('Error loading PDF:', error);
            showPDFFallback(url);
        });
    }
    
    function showPDFFallback(url) {
        console.log('Using PDF fallback');
        fileViewerContent.innerHTML = `
            <div class="file-info">
                <i class="fas fa-file-pdf fa-4x text-danger mb-3"></i>
                <h5>PDF Document</h5>
                <p class="text-muted">PDF viewer is not available.</p>
                <p class="text-muted">Please download the file to view it.</p>
                <button class="btn btn-primary" onclick="window.open('${url}', '_blank')">
                    <i class="fas fa-download me-2"></i>Download PDF
                </button>
            </div>
        `;
    }
    
    function showFileInfo(filename, fileType, fileUrl) {
        console.log('Showing file info for:', fileType);
        const icons = {
            'doc': 'fa-file-word',
            'docx': 'fa-file-word',
            'zip': 'fa-file-archive',
            'rar': 'fa-file-archive',
            'txt': 'fa-file-alt',
            'default': 'fa-file'
        };
        
        const icon = icons[fileType] || icons['default'];
        
        fileViewerContent.innerHTML = `
            <div class="file-info">
                <i class="fas ${icon} fa-4x text-muted mb-3"></i>
                <h5>${filename}</h5>
                <p class="text-muted">File type: ${fileType.toUpperCase()}</p>
                <p class="text-muted small">This file type cannot be previewed. Please download to view.</p>
                <button class="btn btn-primary mt-2" onclick="window.open('${fileUrl}', '_blank')">
                    <i class="fas fa-download me-2"></i>Download File
                </button>
            </div>
        `;
    }
    
    function showError(message) {
        console.error('Showing error:', message);
        fileViewerContent.innerHTML = `
            <div class="file-info">
                <i class="fas fa-exclamation-triangle fa-4x text-danger mb-3"></i>
                <h5>Error Loading File</h5>
                <p class="text-muted">${message}</p>
                <button class="btn btn-outline-primary mt-2" onclick="window.open('${downloadButton.href}', '_blank')">
                    <i class="fas fa-download me-2"></i>Try Download Instead
                </button>
            </div>
        `;
    }
    
    // Clear content when modal is hidden
    fileViewerModal.addEventListener('hide.bs.modal', function() {
        fileViewerContent.innerHTML = '';
    });
});
</script>
@endpush
