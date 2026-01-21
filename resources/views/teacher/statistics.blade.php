@extends('layouts.app')

@section('title', 'Statistics - Teacher')

@section('styles')
<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: transform 0.2s, box-shadow 0.2s;
        height: 100%;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .stat-card .stat-label {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }

    .stat-card .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #2196f3;
        margin-bottom: 0.5rem;
    }

    .stat-card .stat-icon {
        font-size: 2.5rem;
        opacity: 0.2;
        position: absolute;
        right: 1.5rem;
        top: 50%;
        transform: translateY(-50%);
    }

    .chart-placeholder {
        text-align: center;
        padding: 3rem;
        color: #ccc;
    }

    .chart-placeholder i {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    .performance-table {
        font-size: 0.9rem;
    }

    .progress-bar-custom {
        height: 8px;
        background: #e0e0e0;
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: #28a745;
        transition: width 0.3s ease;
    }
</style>
@endsection

@section('content')
<section class="profile-section py-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                @include('teacher.partials.sidebar')
            </div>
            
            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="profile-content">
                    <div class="profile-header mb-4">
                        <h2 class="profile-title">Statistics & Analytics</h2>
                        <p class="profile-subtitle">Track your teaching performance and student engagement</p>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="stat-card position-relative">
                                <div class="stat-label">Total Courses</div>
                                <div class="stat-value">{{ $totalCourses }}</div>
                                <i class="fas fa-book stat-icon" style="color: #007bff;"></i>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="stat-card position-relative">
                                <div class="stat-label">Total Chapters</div>
                                <div class="stat-value">{{ $totalChapters }}</div>
                                <i class="fas fa-layer-group stat-icon" style="color: #28a745;"></i>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="stat-card position-relative">
                                <div class="stat-label">Total Modules</div>
                                <div class="stat-value">{{ $totalModules }}</div>
                                <i class="fas fa-file-alt stat-icon" style="color: #ffc107;"></i>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="stat-card position-relative">
                                <div class="stat-label">Total Students</div>
                                <div class="stat-value">{{ $totalStudents }}</div>
                                <i class="fas fa-users stat-icon" style="color: #dc3545;"></i>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="stat-card position-relative">
                                <div class="stat-label">Total Enrollments</div>
                                <div class="stat-value">{{ $totalEnrollments }}</div>
                                <i class="fas fa-user-plus stat-icon" style="color: #17a2b8;"></i>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="stat-card position-relative">
                                <div class="stat-label">Avg. Completion Rate</div>
                                <div class="stat-value">{{ number_format($avgCompletionRate, 1) }}%</div>
                                <i class="fas fa-chart-pie stat-icon" style="color: #28a745;"></i>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="stat-card position-relative">
                                <div class="stat-label">Published Courses</div>
                                <div class="stat-value">{{ $totalCourses }}</div>
                                <i class="fas fa-check-circle stat-icon" style="color: #28a745;"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Section -->
                    <div class="row mb-4">
                        <div class="col-lg-8 mb-4">
                            <div class="card shadow-sm border-0" style="border-radius: 10px;">
                                <div class="card-header bg-light border-0 p-4" style="border-radius: 10px 10px 0 0;">
                                    <h5 class="mb-0">
                                        <i class="fas fa-chart-line me-2"></i>Enrollment Trend (Last 6 Months)
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    @if($enrollmentTrend->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Month</th>
                                                        <th>Enrollments</th>
                                                        <th>Visual</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($enrollmentTrend as $trend)
                                                        @php
                                                            $maxCount = $enrollmentTrend->max('count');
                                                            $percentage = $maxCount > 0 ? ($trend->count / $maxCount) * 100 : 0;
                                                        @endphp
                                                        <tr>
                                                            <td>{{ date('F Y', strtotime($trend->month . '-01')) }}</td>
                                                            <td><strong>{{ $trend->count }}</strong></td>
                                                            <td>
                                                                <div class="progress-bar-custom" style="width: 200px;">
                                                                    <div class="progress-fill" style="width: {{ $percentage }}%"></div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="chart-placeholder">
                                            <i class="fas fa-chart-bar"></i>
                                            <p class="mt-3 text-muted">No enrollment data available yet</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 mb-4">
                            <div class="card shadow-sm border-0" style="border-radius: 10px;">
                                <div class="card-header bg-light border-0 p-4" style="border-radius: 10px 10px 0 0;">
                                    <h5 class="mb-0">
                                        <i class="fas fa-list me-2"></i>Top Courses
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    @if($topCourses->count() > 0)
                                        <div class="list-group list-group-flush">
                                            @foreach($topCourses as $index => $course)
                                                <div class="list-group-item px-0 py-2 border-0">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="flex-grow-1">
                                                            <div class="fw-bold">{{ $index + 1 }}. {{ Str::limit($course->name, 25) }}</div>
                                                            <small class="text-muted">
                                                                {{ $course->enrollments }} enrollment{{ $course->enrollments > 1 ? 's' : '' }}
                                                            </small>
                                                        </div>
                                                        @if($course->is_published)
                                                            <span class="badge bg-success">Published</span>
                                                        @else
                                                            <span class="badge bg-secondary">Draft</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-4 text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <p class="mb-0">No courses yet</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Student Performance Table -->
                    <div class="row">
                        <div class="col-lg-12 mb-4">
                            <div class="card shadow-sm border-0" style="border-radius: 10px;">
                                <div class="card-header bg-light border-0 p-4" style="border-radius: 10px 10px 0 0;">
                                    <h5 class="mb-0">
                                        <i class="fas fa-graduation-cap me-2"></i>Student Performance
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="table-responsive">
                                        <table class="table table-hover performance-table mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Student Name</th>
                                                    <th>Course</th>
                                                    <th>Progress</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($studentPerformance as $performance)
                                                    <tr>
                                                        <td>{{ $performance->student_name }}</td>
                                                        <td>{{ Str::limit($performance->course_name, 30) }}</td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="progress-bar-custom me-2" style="width: 100px;">
                                                                    <div class="progress-fill" style="width: {{ $performance->progress }}%"></div>
                                                                </div>
                                                                <span class="small">{{ number_format($performance->progress, 1) }}%</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @if($performance->completed_at)
                                                                <span class="badge bg-success">Completed</span>
                                                            @elseif($performance->progress >= 50)
                                                                <span class="badge bg-info">In Progress</span>
                                                            @else
                                                                <span class="badge bg-warning">Started</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center py-4 text-muted">
                                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                                            <p class="mb-0">No student data available yet</p>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
