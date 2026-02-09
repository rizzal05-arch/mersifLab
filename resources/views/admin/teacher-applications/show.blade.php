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
                                    <a href="{{ $teacherApplication->getFileUrl('ktp_file') }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-info ms-1">
                                        <i class="fas fa-eye"></i> View
                                    </a>
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
                                    <a href="{{ $teacherApplication->getFileUrl('teaching_certificate_file') }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-info ms-1">
                                        <i class="fas fa-eye"></i> View
                                    </a>
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
                                    <a href="{{ $teacherApplication->getFileUrl('institution_id_file') }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-info ms-1">
                                        <i class="fas fa-eye"></i> View
                                    </a>
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
                                    <a href="{{ $teacherApplication->getFileUrl('portfolio_file') }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-info ms-1">
                                        <i class="fas fa-eye"></i> View
                                    </a>
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
