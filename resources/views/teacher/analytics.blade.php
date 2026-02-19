@extends('layouts.app')

@section('title', 'Teacher Analytics')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2 mb-0">Analytics & Statistics</h1>
            <p class="text-muted">Track your teaching performance and student engagement</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted small mb-1">Total Students</p>
                            <h3 class="mb-0">0</h3>
                        </div>
                        <div style="font-size: 2rem; color: #007bff; opacity: 0.2;">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted small mb-1">Avg. Rating</p>
                            <h3 class="mb-0">4.5<span style="font-size: 1rem; color: #ffc107;">★</span></h3>
                        </div>
                        <div style="font-size: 2rem; color: #ffc107; opacity: 0.2;">
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted small mb-1">Completion Rate</p>
                            <h3 class="mb-0">75%</h3>
                        </div>
                        <div style="font-size: 2rem; color: #28a745; opacity: 0.2;">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted small mb-1">Course Revenue</p>
                            <h3 class="mb-0">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</h3>
                        </div>
                        <div style="font-size: 2rem; color: #28a745; opacity: 0.2;">
                            <i class="fas fa-money-bill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-header bg-light border-0 p-4" style="border-radius: 10px 10px 0 0;">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>Student Enrollment Trend
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div style="text-align: center; padding: 2rem; color: #ccc;">
                        <i class="fas fa-chart-bar" style="font-size: 3rem;"></i>
                        <p class="mt-3 text-muted">Chart data will appear here</p>
                    </div>
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
                    <div style="text-align: center; padding: 2rem; color: #ccc;">
                        <p class="text-muted">No data available</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Student Name</th>
                                    <th>Course</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No student data available yet</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
