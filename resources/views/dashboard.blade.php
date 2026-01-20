@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header mb-4">
        <h1>Dashboard {{ ucfirst($role) }}</h1>
        <p class="text-muted">Selamat datang kembali, {{ Auth::user()->name }}!</p>
    </div>

    @if ($role === 'student')
        @include('dashboard.student-content')
    @elseif ($role === 'teacher')
        @include('dashboard.teacher-content')
    @endif
</div>
@endsection

@section('styles')
<style>
    .dashboard-container {
        padding: 20px;
    }

    .dashboard-header {
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 20px;
    }

    .dashboard-header h1 {
        font-size: 2.5rem;
        margin-bottom: 10px;
        color: #333;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-top: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        text-align: center;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .stat-card .number {
        font-size: 2rem;
        font-weight: bold;
        color: #007bff;
        margin: 10px 0;
    }

    .stat-card .label {
        color: #666;
        font-size: 0.95rem;
    }

    .section-title {
        font-size: 1.5rem;
        margin-top: 40px;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #007bff;
    }

    .course-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .course-card {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .course-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .course-card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
    }

    .course-card-body {
        padding: 20px;
    }

    .course-title {
        font-weight: bold;
        margin-bottom: 10px;
        color: white;
    }

    .course-info {
        font-size: 0.9rem;
        margin-bottom: 5px;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
        padding: 8px 16px;
        border-radius: 4px;
        text-decoration: none;
        display: inline-block;
        margin-top: 10px;
        transition: background-color 0.2s;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        text-decoration: none;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: #999;
    }

    .empty-state-icon {
        font-size: 3rem;
        margin-bottom: 20px;
    }
</style>
@endsection
