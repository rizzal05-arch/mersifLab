@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="page-title">
    <h1>Welcome back, {{ auth()->user()->name }}!</h1>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="stat-card" style="position: relative; overflow: hidden;">
            <!-- User Profile Card -->
            <div style="position: absolute; top: 15px; right: 15px; z-index: 2;">
                <div style="display: flex; align-items: center; gap: 8px; background: white; padding: 6px 10px; border-radius: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <img src="https://picsum.photos/seed/teacher1/30/30.jpg" alt="Teacher" style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover;">
                    <img src="https://picsum.photos/seed/teacher2/30/30.jpg" alt="Teacher" style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover; margin-left: -8px;">
                    <span style="font-size: 11px; color: #64748b; font-weight: 500;">+2</span>
                </div>
            </div>
            
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div style="flex: 1;">
                    <div class="stat-card-label">Total Teacher</div>
                    <div class="stat-card-value">{{ App\Models\User::where('role', 'teacher')->count() }}</div>
                    <div class="stat-card-change positive">
                        <i class="fas fa-arrow-up"></i> +12.05%
                    </div>
                </div>
                <div class="stat-card-icon icon-teacher" style="position: absolute; bottom: 15px; right: 15px; margin-bottom: 0;">
                    <i class="fas fa-chalkboard-user"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="stat-card" style="position: relative; overflow: hidden;">
            <!-- User Profile Card -->
            <div style="position: absolute; top: 15px; right: 15px; z-index: 2;">
                <div style="display: flex; align-items: center; gap: 8px; background: white; padding: 6px 10px; border-radius: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <img src="https://picsum.photos/seed/student1/30/30.jpg" alt="Student" style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover;">
                    <img src="https://picsum.photos/seed/student2/30/30.jpg" alt="Student" style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover; margin-left: -8px;">
                    <span style="font-size: 11px; color: #64748b; font-weight: 500;">+2</span>
                </div>
            </div>
            
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div style="flex: 1;">
                    <div class="stat-card-label">Total Student</div>
                    <div class="stat-card-value">{{ App\Models\User::where('role', 'student')->count() }}</div>
                    <div class="stat-card-change positive">
                        <i class="fas fa-arrow-up"></i> +17.55%
                    </div>
                </div>
                <div class="stat-card-icon icon-student" style="position: absolute; bottom: 15px; right: 15px; margin-bottom: 0;">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="stat-card" style="position: relative; overflow: hidden;">
            <!-- User Profile Card -->
            <div style="position: absolute; top: 15px; right: 15px; z-index: 2;">
                <div style="display: flex; align-items: center; gap: 8px; background: white; padding: 6px 10px; border-radius: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <img src="https://picsum.photos/seed/course1/30/30.jpg" alt="Course" style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover;">
                    <img src="https://picsum.photos/seed/course2/30/30.jpg" alt="Course" style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover; margin-left: -8px;">
                    <span style="font-size: 11px; color: #64748b; font-weight: 500;">+3</span>
                </div>
            </div>
            
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div style="flex: 1;">
                    <div class="stat-card-label">Total Course</div>
                    <div class="stat-card-value">{{ App\Models\Course::count() }}</div>
                    <div class="stat-card-change negative">
                        <i class="fas fa-arrow-down"></i> -8.04%
                    </div>
                </div>
                <div class="stat-card-icon icon-course" style="position: absolute; bottom: 15px; right: 15px; margin-bottom: 0;">
                    <i class="fas fa-book"></i>
                </div>
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
            <select class="form-select d-inline w-auto" style="font-size: 13px; border: 1px solid #e0e0e0; border-radius: 6px;">
                <option>Sort by: All Categories</option>
            </select>
            <a href="#" style="margin-left: 15px; font-size: 13px; color: #2F80ED; text-decoration: none; font-weight: 500;">View All →</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-sm" style="font-size: 13px; border-collapse: separate; border-spacing: 0;">
            <thead>
                <tr>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">#</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Course Name</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Uploaded By</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Sale</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Price</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Lessons</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Total Time</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Remove</th>
                </tr>
            </thead>
            <tbody>
                @forelse($courses ?? [] as $course)
                    <tr style="border-bottom: 1px solid #f8f9fa;">
                        <td style="padding: 16px 8px; vertical-align: middle; color: #333333; font-weight: 500;">{{ $loop->iteration }}</td>
                        <td style="padding: 16px 8px; vertical-align: middle;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 40px; height: 40px; background: #f8f9fa; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-book" style="color: #2F80ED; font-size: 16px;"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: #333333; margin-bottom: 2px;">{{ $course->title }}</div>
                                    <small style="color: #828282; font-size: 11px;">#{{ $course->id }}</small>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 16px 8px; vertical-align: middle; color: #828282;">Teacher's User</td>
                        <td style="padding: 16px 8px; vertical-align: middle; color: #828282;">xxx</td>
                        <td style="padding: 16px 8px; vertical-align: middle; color: #333333; font-weight: 500;">Rp.x.000</td>
                        <td style="padding: 16px 8px; vertical-align: middle; color: #828282;">xx</td>
                        <td style="padding: 16px 8px; vertical-align: middle; color: #828282;">xxx hours</td>
                        <td style="padding: 16px 8px; vertical-align: middle;">
                            <button class="btn btn-sm" style="background: #FFEBEE; color: #EB5757; border: none; padding: 6px 10px; font-size: 12px; border-radius: 6px; transition: all 0.3s ease;" onmouseover="this.style.background='#EB5757'; this.style.color='white';" onmouseout="this.style.background='#FFEBEE'; this.style.color='#EB5757';">
                                <i class="fas fa-trash" style="font-size: 11px;"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 40px; color: #828282;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 10px;">
                                <i class="fas fa-book" style="font-size: 48px; color: #e0e0e0;"></i>
                                <span style="font-size: 14px;">Tidak ada kursus</span>
                            </div>
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
                    borderColor: '#2F80ED',
                    backgroundColor: 'rgba(47, 128, 237, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0,
                    pointRadius: 4,
                    pointBackgroundColor: '#2F80ED',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6
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
                        },
                        ticks: {
                            color: '#828282',
                            font: {
                                size: 11
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#828282',
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
