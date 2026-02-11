@extends('layouts.admin')

@section('title', 'Course Approval')

@section('content')
<div class="page-title" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
    <div>
        <h1>Course Approval</h1>
        <p class="text-muted">Review and approve course for publication</p>
    </div>
</div>

@session('success')
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endsession

@session('error')
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endsession

<!-- Course Overview -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1">{{ $course->name }}</h5>
                <small class="opacity-75">by {{ $course->teacher->name ?? 'Unknown' }}</small>
            </div>
            <div>
                @php
                    $statusLabel = $course->status_label;
                @endphp
                <span class="badge" style="background: 
                    @if($course->status === 'published') #d4edda; color: #155724;
                    @elseif($course->status === 'pending_approval') #fff3cd; color: #856404;
                    @elseif($course->status === 'rejected') #f8d7da; color: #721c24;
                    @else #e2e3e5; color: #383d41;
                    @endif; font-size: 12px; padding: 6px 12px; border-radius: 6px; font-weight: 500;">
                    {{ $statusLabel['text'] }}
                </span>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <p class="mb-3">{{ $course->description }}</p>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <small class="text-muted">Category</small>
                        <div><strong>{{ $course->category_name }}</strong></div>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Price</small>
                        <div><strong>Rp {{ number_format($course->price, 0, ',', '.') }}</strong></div>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Duration</small>
                        <div><strong>{{ $course->formatted_total_duration }}</strong></div>
                    </div>
                </div>
                @if($course->admin_feedback)
                    <div class="alert alert-info">
                        <small class="text-muted">Admin Feedback:</small>
                        <p class="mb-0">{{ $course->admin_feedback }}</p>
                    </div>
                @endif
            </div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            @if($course->image)
                                <img src="{{ asset('storage/' . $course->image) }}" 
                                     alt="{{ $course->name }}" 
                                     class="img-fluid rounded"
                                     style="max-height: 150px; object-fit: cover;">
                            @else
                                <div class="d-flex align-items-center justify-content-center" style="height: 150px; background: #f8f9fa; border-radius: 8px;">
                                    <i class="fas fa-book" style="font-size: 48px; color: #6c757d;"></i>
                                </div>
                            @endif
                        </div>
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="fw-bold">{{ $course->chapters_count ?? 0 }}</div>
                                <small class="text-muted">Chapters</small>
                            </div>
                            <div class="col-4">
                                <div class="fw-bold">{{ $course->modules_count ?? 0 }}</div>
                                <small class="text-muted">Modules</small>
                            </div>
                            <div class="col-4">
                                <div class="fw-bold">{{ $course->purchases_count ?? 0 }}</div>
                                <small class="text-muted">Sales</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Course Content -->
<div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0">
            <i class="fas fa-list me-2"></i>Course Content
        </h6>
    </div>
    <div class="card-body">
        @if($course->chapters->count() > 0)
            @foreach($course->chapters as $chapter)
                <div class="chapter-item mb-4" id="chapter-{{ $chapter->id }}">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="mb-1">{{ $chapter->title }}</h6>
                            <small class="text-muted">{{ $chapter->modules->count() }} modules • {{ $chapter->formatted_total_duration }}</small>
                        </div>
                        <div class="chapter-actions">
                            <a href="{{ route('admin.modules.preview', $chapter->modules->first()->id ?? '#') }}" 
                               class="btn btn-sm btn-outline-primary" 
                               target="_blank">
                                <i class="fas fa-eye me-1"></i>Preview
                            </a>
                        </div>
                    </div>
                    
                    @if($chapter->modules->count() > 0)
                        <div class="modules-list">
                            @foreach($chapter->modules as $module)
                                <div class="module-item d-flex justify-content-between align-items-center p-3 bg-light rounded mb-2" id="module-{{ $module->id }}">
                                    <div class="d-flex align-items-center">
                                        <div class="module-type-icon me-3">
                                            @if($module->type === 'text')
                                                <i class="fas fa-file-alt text-primary"></i>
                                            @elseif($module->type === 'document')
                                                <i class="fas fa-file-pdf text-danger"></i>
                                            @else
                                                <i class="fas fa-video text-info"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ $module->title }}</div>
                                            <small class="text-muted">
                                                {{ $module->type_label }} • 
                                                {{ $module->estimated_duration ?? 0 }} min • 
                                                {{ $module->view_count ?? 0 }} views
                                            </small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        @php
                                            $approvalStatus = $module->approval_status ?? 'pending_approval';
                                        @endphp
                                        <span class="badge" style="background: 
                                            @if($approvalStatus === 'approved') #d4edda; color: #155724;
                                            @elseif($approvalStatus === 'rejected') #f8d7da; color: #721c24;
                                            @else #fff3cd; color: #856404;
                                            @endif; font-size: 10px; padding: 4px 8px; border-radius: 4px;">
                                            @if($approvalStatus === 'approved')
                                                Approved
                                            @elseif($approvalStatus === 'rejected')
                                                Rejected
                                            @else
                                                Pending
                                            @endif
                                        </span>
                                        <div class="module-actions">
                                            <a href="{{ route('admin.modules.preview', $module->id) }}" 
                                               class="btn btn-sm btn-outline-secondary" 
                                               target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-muted text-center py-3">
                            <i class="fas fa-folder-open me-2"></i>No modules in this chapter
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="text-center py-4">
                <i class="fas fa-book-open me-2" style="font-size: 48px; color: #6c757d;"></i>
                <p class="text-muted">No chapters in this course</p>
            </div>
        @endif
    </div>
</div>

<!-- Approval Actions -->
@if($course->status === 'pending_approval')
<div class="card">
    <div class="card-header bg-warning text-dark">
        <h6 class="mb-0">
            <i class="fas fa-gavel me-2"></i>Course Approval
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Approve Form -->
            <div class="col-md-6">
                <form action="{{ route('admin.courses.approve', $course->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="approve-feedback" class="form-label">Feedback (Optional)</label>
                        <textarea name="admin_feedback" id="approve-feedback" class="form-control" rows="3" 
                                  placeholder="Add feedback for the teacher..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-check-circle me-2"></i>Approve Course
                    </button>
                </form>
            </div>
            
            <!-- Reject Form -->
            <div class="col-md-6">
                <form action="{{ route('admin.courses.reject', $course->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="reject-feedback" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea name="admin_feedback" id="reject-feedback" class="form-control" rows="3" 
                                  placeholder="Explain why this course is being rejected..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="fas fa-times-circle me-2"></i>Reject Course
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@elseif($course->status === 'published')
<div class="card">
    <div class="card-header bg-success text-white">
        <h6 class="mb-0">
            <i class="fas fa-check-circle me-2"></i>Course Published
        </h6>
    </div>
    <div class="card-body">
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>
            This course has been approved and is now published.
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('course.detail', $course->id) }}" target="_blank" class="btn btn-primary">
                <i class="fas fa-external-link-alt me-2"></i>View Course
            </a>
            <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Courses
            </a>
        </div>
    </div>
</div>
@elseif($course->status === 'rejected')
<div class="card">
    <div class="card-header bg-danger text-white">
        <h6 class="mb-0">
            <i class="fas fa-times-circle me-2"></i>Course Rejected
        </h6>
    </div>
    <div class="card-body">
        <div class="alert alert-danger">
            <i class="fas fa-times-circle me-2"></i>
            This course has been rejected.
        </div>
        @if($course->admin_feedback)
            <div class="mb-3">
                <strong>Rejection Reason:</strong>
                <p class="mb-0">{{ $course->admin_feedback }}</p>
            </div>
        @endif
        <div class="d-flex gap-2">
            <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Courses
            </a>
        </div>
    </div>
</div>
@endif

<style>
.chapter-item {
    border-left: 3px solid #007bff;
    padding-left: 1rem;
}

.module-item {
    transition: all 0.2s ease;
}

.module-item:hover {
    background-color: #e9ecef !important;
    transform: translateX(4px);
}

.module-type-icon {
    font-size: 1.2rem;
    width: 30px;
    text-align: center;
}

.module-actions .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
}
</style>
@endsection
