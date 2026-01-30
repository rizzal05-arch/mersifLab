@extends('layouts.admin')

@section('title', 'Courses Management')

@section('content')
<div class="page-title">
    <h1>Courses Management</h1>
</div>

<div class="card-content">
    <div class="card-content-title">
        <span>All Courses ({{ $courses->total() }} total)</span>
        <div>
            <select id="categoryFilter" class="form-select d-inline w-auto" style="font-size: 13px; border: 1px solid #e0e0e0; border-radius: 6px; padding: 6px 12px;" onchange="filterByCategory(this.value)">
                <option value="all" {{ request('category') == 'all' || !request('category') ? 'selected' : '' }}>Filter by Category: All</option>
                @foreach(\App\Models\ClassModel::getAvailableCategories() as $key => $label)
                    <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
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
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Sales</th>
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
                                <div style="width: 50px; height: 50px; background: #f8f9fa; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden; position: relative;">
                                    @if($course->image)
                                        <img src="{{ asset('storage/' . $course->image) }}" 
                                             alt="{{ $course->name }}" 
                                             style="width: 100%; height: 100%; object-fit: cover; {{ !$course->is_published ? 'opacity: 0.5; filter: grayscale(100%);' : '' }}"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div style="display: none; width: 100%; height: 100%; align-items: center; justify-content: center;">
                                            <i class="fas fa-book" style="color: #2F80ED; font-size: 20px;"></i>
                                        </div>
                                    @else
                                        <i class="fas fa-book" style="color: #2F80ED; font-size: 20px;"></i>
                                    @endif
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: #333333; margin-bottom: 2px; font-size: 14px; {{ !$course->is_published ? 'opacity: 0.6;' : '' }}">{{ $course->name }}</div>
                                    <small style="color: #828282; font-size: 11px;">ID: {{ $course->id }}</small>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 16px 8px; vertical-align: middle;">
                            <div style="display: flex; flex-direction: column;">
                                <strong style="color: #333333; font-size: 13px; margin-bottom: 2px;">{{ $course->teacher->name ?? 'N/A' }}</strong>
                                <small style="color: #828282; font-size: 11px;">{{ $course->teacher->email ?? 'N/A' }}</small>
                            </div>
                        </td>
                        <td style="padding: 16px 8px; vertical-align: middle;">
                            <span class="badge" style="background: #e3f2fd; color: #1976d2; font-size: 11px; padding: 4px 10px; border-radius: 4px; font-weight: 500;">
                                {{ $course->category_name }}
                            </span>
                        </td>
                        <td style="padding: 16px 8px; vertical-align: middle; color: #333333; font-weight: 500;">{{ $course->chapters_count ?? 0 }}</td>
                        <td style="padding: 16px 8px; vertical-align: middle; color: #333333; font-weight: 500;">{{ $course->modules_count ?? 0 }}</td>
                        <td style="padding: 16px 8px; vertical-align: middle;">
                            <span class="badge" style="background: #e8f5e9; color: #2e7d32; font-size: 11px; padding: 4px 10px; border-radius: 4px; font-weight: 500;">
                                {{ $course->purchases_count ?? 0 }}
                            </span>
                        </td>
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
                            <div style="display: flex; gap: 6px; align-items: center; flex-wrap: wrap;">
                                <!-- View Button (Text Link) -->
                                <a href="{{ route('admin.courses.moderation', $course->id) }}" 
                                   style="color: #1976d2; text-decoration: none; font-size: 12px; font-weight: 500; padding: 4px 8px; border-radius: 4px; transition: background 0.2s;"
                                   onmouseover="this.style.background='#e3f2fd'" 
                                   onmouseout="this.style.background='transparent'"
                                   title="View & Moderate">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                                <!-- Delete Button -->
                                <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST" style="display: inline;" class="delete-course-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm delete-course-btn" 
                                            style="background: #ffebee; color: #c62828; border: none; padding: 4px 8px; font-size: 11px; border-radius: 4px; cursor: pointer; transition: opacity 0.2s;"
                                            onmouseover="this.style.opacity='0.8'" 
                                            onmouseout="this.style.opacity='1'"
                                            title="Delete Course"
                                            onclick="return confirm('Are you sure you want to delete this course? This action cannot be undone.');">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
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

<script>
function filterByCategory(category) {
    const url = new URL(window.location.href);
    if (category === 'all') {
        url.searchParams.delete('category');
    } else {
        url.searchParams.set('category', category);
    }
    url.searchParams.delete('page'); // Reset to page 1
    window.location.href = url.toString();
}
</script>
@endsection
