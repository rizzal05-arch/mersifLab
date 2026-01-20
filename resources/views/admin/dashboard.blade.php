@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="page-title">
    <h1>Welcome back, {{ auth()->user()->name }}!</h1>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="stat-card">
            <div class="stat-card-icon icon-teacher">
                <i class="fas fa-chalkboard-user"></i>
            </div>
            <div class="stat-card-label">Total Teacher</div>
            <div class="stat-card-value">{{ $totalUsers ?? 0 }}</div>
            <div class="stat-card-change positive">
                <i class="fas fa-arrow-up"></i> +12.05%
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="stat-card">
            <div class="stat-card-icon icon-student">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-card-label">Total Student</div>
            <div class="stat-card-value">{{ $activeSubscribers ?? 0 }}</div>
            <div class="stat-card-change positive">
                <i class="fas fa-arrow-up"></i> +17.55%
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="stat-card">
            <div class="stat-card-icon icon-course">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-card-label">Total Course</div>
            <div class="stat-card-value">{{ $totalKursus ?? 0 }}</div>
            <div class="stat-card-change negative">
                <i class="fas fa-arrow-down"></i> -8.04%
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card-content">
            <div class="card-content-title">
                Users Analysis
                <input type="text" placeholder="Jan 18 - Jan 24" style="border: 1px solid #e0e0e0; padding: 6px 12px; border-radius: 6px; width: 150px; font-size: 12px;">
            </div>
            <div class="chart-container">
                <canvas id="usersChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card-content">
            <div class="card-content-title">
                Course Overview
            </div>
            <div class="chart-container">
                <canvas id="courseChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Top Course Table -->
<div class="card-content">
    <div class="card-content-title">
        Top Course
        <div>
            <select class="form-select d-inline w-auto" style="font-size: 13px;">
                <option>Sort by: All Categories</option>
            </select>
            <a href="#" style="margin-left: 15px; font-size: 13px; color: #667eea; text-decoration: none;">View All →</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-sm" style="font-size: 13px;">
            <thead style="background-color: #f8f9fa;">
                <tr>
                    <th>#</th>
                    <th>Course Name</th>
                    <th>Uploaded By</th>
                    <th>Sale</th>
                    <th>Price</th>
                    <th>Lessons</th>
                    <th>Total Time</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                @forelse($courses ?? [] as $course)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <i class="fas fa-book" style="color: #667eea; font-size: 20px;"></i>
                                <div>
                                    <div style="font-weight: 600; color: #2c3e50;">{{ $course->title }}</div>
                                    <small style="color: #7f8c8d;">#{{ $course->id }}</small>
                                </div>
                            </div>
                        </td>
                        <td>Teacher's User</td>
                        <td>xxx</td>
                        <td>Rp.x.000</td>
                        <td>xx</td>
                        <td>xxx hours</td>
                        <td>
                            <button class="btn btn-sm btn-outline-danger" style="padding: 4px 8px; font-size: 12px;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            Tidak ada kursus
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    // Wait for DOM to load
    document.addEventListener('DOMContentLoaded', function() {
        // Users Analysis Chart
        const usersCtx = document.getElementById('usersChart').getContext('2d');
        new Chart(usersCtx, {
            type: 'line',
            data: {
                labels: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                datasets: [{
                    label: 'Users',
                    data: [8, 10, 9, 12, 8, 5, 3],
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false,
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Course Overview Chart
        const courseCtx = document.getElementById('courseChart').getContext('2d');
        new Chart(courseCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Courses',
                    data: [5, 6, 8, 12, 10, 7, 5, 8, 6, 9, 8, 10],
                    borderColor: '#4facfe',
                    backgroundColor: 'rgba(79, 172, 254, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: '#4facfe',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false,
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
