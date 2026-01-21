@extends('layouts.admin')

@section('title', 'Courses Management')

@section('content')
<div class="page-title">
    <h1>Courses Management</h1>
</div>

<div class="card-content">
    <div class="card-content-title">
        All Courses ({{ $courses->total() }} total)
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary" style="background: #6c757d; border: none; padding: 8px 16px; font-size: 13px; border-radius: 6px; color: white; text-decoration: none; margin-right: 10px;">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-sm" style="font-size: 13px; border-collapse: separate; border-spacing: 0;">
            <thead>
                <tr>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">#</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Course Name</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Instructor</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Category</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Chapters</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Modules</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Status</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Created</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($courses as $course)
                    <tr style="border-bottom: 1px solid #f8f9fa;">
                        <td style="padding: 16px 8px; vertical-align: middle; color: #333333; font-weight: 500;">{{ $loop->iteration + ($courses->currentPage() - 1) * $courses->perPage() }}</td>
                        <td style="padding: 16px 8px; vertical-align: middle;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 40px; height: 40px; background: #f8f9fa; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-book" style="color: #2F80ED; font-size: 16px;"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: #333333; margin-bottom: 2px;">{{ $course->name }}</div>
                                    <small style="color: #828282; font-size: 11px;">ID: {{ $course->id }}</small>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 16px 8px; vertical-align: middle; color: #828282;">{{ $course->teacher->name ?? 'N/A' }}</td>
                        <td style="padding: 16px 8px; vertical-align: middle;">
                            <span class="badge bg-primary">
                                {{ \App\Models\ClassModel::CATEGORIES[$course->category] ?? 'Uncategorized' }}
                            </span>
                        </td>
                        <td style="padding: 16px 8px; vertical-align: middle; color: #333333; font-weight: 500;">{{ $course->chapters_count ?? 0 }}</td>
                        <td style="padding: 16px 8px; vertical-align: middle; color: #333333; font-weight: 500;">{{ $course->modules_count ?? 0 }}</td>
                        <td style="padding: 16px 8px; vertical-align: middle;">
                            @if($course->is_published)
                                <span class="badge bg-success">Published</span>
                            @else
                                <span class="badge bg-warning">Draft</span>
                            @endif
                        </td>
                        <td style="padding: 16px 8px; vertical-align: middle; color: #828282; font-size: 12px;">
                            {{ $course->created_at ? $course->created_at->format('M d, Y') : 'N/A' }}
                        </td>
                        <td style="padding: 16px 8px; vertical-align: middle;">
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('course.detail', $course->id) }}" class="btn btn-sm" style="background: #e3f2fd; color: #1976d2; border: none; padding: 4px 8px; font-size: 11px; border-radius: 4px; text-decoration: none;" title="View Course">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('teacher.classes.edit', $course->id) }}" class="btn btn-sm" style="background: #fff3e0; color: #f57c00; border: none; padding: 4px 8px; font-size: 11px; border-radius: 4px; text-decoration: none;" title="Edit Course">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center" style="padding: 40px; color: #828282;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 10px;">
                                <i class="fas fa-book" style="font-size: 48px; color: #e0e0e0;"></i>
                                <span style="font-size: 14px;">No courses found</span>
                                <p class="text-muted small">Teachers haven't created any courses yet.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($courses->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $courses->links() }}
        </div>
    @endif
</div>
@endsection
