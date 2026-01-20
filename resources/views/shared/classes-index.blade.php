{{-- Unified Class Content Display for Student & Teacher Dashboards --}}
{{-- Uses @can directives to conditionally show CRUD buttons --}}

<div class="classes-section">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h2 class="section-title" style="margin: 0; border-bottom: none; padding-bottom: 0;">
                @if(auth()->user()->isTeacher() || auth()->user()->isAdmin())
                    My Classes
                @else
                    Available Classes
                @endif
            </h2>
            <p class="text-muted mt-2">
                @if(auth()->user()->isTeacher() || auth()->user()->isAdmin())
                    Manage your courses and learning content
                @else
                    Explore courses and continue learning
                @endif
            </p>
        </div>
        @can('createClass')
            <a href="{{ route('teacher.classes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Create New Class
            </a>
        @endcan
    </div>

    @if ($classes->count() > 0)
        <div class="row">
            @foreach ($classes as $class)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card class-card h-100 shadow-sm border-0 hover-shadow">
                        <!-- Card Header -->
                        <div class="card-header bg-light border-0" style="padding: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 8px 8px 0 0;">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title mb-2" style="color: white;">{{ $class->name }}</h5>
                                    <p class="card-text small mb-0" style="color: rgba(255,255,255,0.9);">
                                        @if($class->description)
                                            {{ Str::limit($class->description, 60) }}
                                        @else
                                            <em>No description</em>
                                        @endif
                                    </p>
                                </div>
                                @can('updateClass', $class)
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('teacher.classes.edit', $class) }}">
                                                    <i class="fas fa-edit me-2"></i> Edit Class
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('teacher.classes.destroy', $class) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure?')">
                                                        <i class="fas fa-trash me-2"></i> Delete Class
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                @endcan
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body">
                            <!-- Status Badge -->
                            <div class="mb-3">
                                @if($class->is_published)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i> Published
                                    </span>
                                @else
                                    <span class="badge bg-warning">
                                        <i class="fas fa-lock me-1"></i> Draft
                                    </span>
                                @endif
                            </div>

                            <!-- Statistics -->
                            <div class="stats-small mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <small class="text-muted">Chapters:</small>
                                    <small class="fw-bold">{{ $class->chapters_count ?? 0 }}</small>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">Modules:</small>
                                    <small class="fw-bold">{{ $class->modules_count ?? 0 }}</small>
                                </div>
                            </div>

                            <!-- Teacher Info (for students) -->
                            @unless(auth()->user()->isTeacher() || auth()->user()->isAdmin())
                                <div class="teacher-info mb-3" style="padding: 10px; background-color: #f8f9fa; border-radius: 6px;">
                                    <small class="text-muted d-block mb-1">Taught by</small>
                                    <small class="fw-bold">{{ $class->teacher->name ?? 'Unknown' }}</small>
                                </div>
                            @endunless

                            <!-- Action Button -->
                            <div class="d-grid gap-2">
                                @can('updateClass', $class)
                                    {{-- Teacher: Manage chapters and modules --}}
                                    <a href="{{ route('teacher.chapters.index', $class) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-cog me-2"></i> Manage Content
                                    </a>
                                @elsecan('viewClass', $class)
                                    {{-- Student: View class content --}}
                                    <a href="{{ route('student.class.detail', $class) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-book-open me-2"></i> View Class
                                    </a>
                                @endcan
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="card-footer bg-light border-top small text-muted" style="padding: 10px 15px;">
                            @if(auth()->user()->isTeacher() || auth()->user()->isAdmin())
                                Created {{ $class->created_at->diffForHumans() }}
                            @else
                                <i class="fas fa-user-circle me-1"></i> {{ $class->teacher->name ?? 'Instructor' }}
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="empty-state text-center py-5">
            <div class="empty-state-icon" style="font-size: 3rem; margin-bottom: 15px;">
                @if(auth()->user()->isTeacher() || auth()->user()->isAdmin())
                    📚
                @else
                    🔍
                @endif
            </div>
            <h5>No Classes Found</h5>
            <p class="text-muted mb-4">
                @if(auth()->user()->isTeacher() || auth()->user()->isAdmin())
                    You haven't created any classes yet. Start by creating your first class!
                @else
                    No classes are available to you at this moment. Please check back later.
                @endif
            </p>
            @can('createClass')
                <a href="{{ route('teacher.classes.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Create First Class
                </a>
            @endcan
        </div>
    @endif
</div>

<style>
    .class-card {
        transition: all 0.3s ease;
        border-radius: 10px;
        overflow: hidden;
    }

    .class-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
    }

    .section-title {
        font-size: 1.75rem;
        font-weight: 600;
        color: #333;
        border-bottom: 3px solid #667eea;
        padding-bottom: 10px;
    }

    .hover-shadow {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .empty-state {
        padding: 60px 20px;
        background-color: #f8f9fa;
        border-radius: 10px;
        margin: 20px 0;
    }

    .empty-state-icon {
        opacity: 0.5;
    }

    .stats-small small {
        font-size: 0.85rem;
    }

    .teacher-info {
        border-left: 3px solid #667eea;
        padding-left: 12px !important;
    }
</style>
