@extends('layouts.admin')

@section('title', 'Testimonials Management')

@section('content')
<div class="page-title" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
    <div>
        <h1>Testimonials Management</h1>
    </div>
    <div style="max-width: 350px; width: 100%; margin-top: 0;">
        <input type="text" id="testimonialSearch" placeholder="Search testimonials..." style="width: 100%; padding: 10px 15px; border: none; background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); border-radius: 20px; font-size: 13px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); transition: all 0.3s ease; outline: none;" onfocus="this.style.background='white'; this.style.boxShadow='0 2px 8px rgba(0, 0, 0, 0.1)';" onblur="this.style.background='rgba(255, 255, 255, 0.8)'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.05)';">
    </div>
</div>

<div class="card-content">
    <div class="card-content-title">
        All Testimonials ({{ $testimonials->count() }})
        <div>
            <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary" style="background: #2F80ED; border: none; padding: 8px 16px; font-size: 13px; border-radius: 6px; color: white; text-decoration: none;">
                <i class="fas fa-plus"></i> Add Testimonial
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px;">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px;">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-sm" style="font-size: 13px; border-collapse: separate; border-spacing: 0;">
            <thead>
                <tr>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">#</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Avatar</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Name</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Position</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Published</th>
                    <th style="border: none; padding: 12px 8px; color: #828282; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($testimonials as $t)
                    <tr style="border-bottom: 1px solid #f8f9fa;">
                        <td style="padding: 16px 8px; vertical-align: middle; color: #333; font-size: 13px;">{{ $loop->iteration + (($testimonials->currentPage()-1) * $testimonials->perPage()) }}</td>
                        <td style="width:70px; padding: 16px 8px; vertical-align: middle;">
                            @if($t->avatar || ($t->admin && $t->admin->avatar))
                                <img src="{{ $t->avatar ? asset('storage/' . $t->avatar) : $t->avatarUrl() }}" alt="avatar" style="width:48px; height:48px; object-fit:cover; border-radius:8px;">
                            @else
                                <div style="width:48px; height:48px; border-radius:8px; background:#f1f5f9; display:flex; align-items:center; justify-content:center; color:#64748b; font-weight:600;">{{ strtoupper(substr($t->name,0,2)) }}</div>
                            @endif
                        </td>
                        <td style="padding: 16px 8px; vertical-align: middle; color: #333; font-weight: 600; font-size: 13px;">{{ $t->name }}</td>
                        <td style="padding: 16px 8px; vertical-align: middle; color: #828282; font-size: 13px;">{{ $t->position }}</td>
                        <td style="padding: 16px 8px; vertical-align: middle;">
                            <span class="badge" style="padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 500; @if($t->is_published) background: #e8f5e8; color: #2e7d32; @else background: #fff3e0; color: #f57c00; @endif">
                                {{ $t->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </td>
                        <td style="padding: 16px 8px; vertical-align: middle;">
                            <div style="display: flex; gap: 6px; align-items: center; flex-wrap: wrap;">
                                <!-- Edit Button (Text Link) -->
                                <a href="{{ route('admin.testimonials.edit', $t->id) }}" 
                                   style="color: #1976d2; text-decoration: none; font-size: 12px; font-weight: 500; padding: 4px 8px; border-radius: 4px; transition: background 0.2s;"
                                   onmouseover="this.style.background='#e3f2fd'" 
                                   onmouseout="this.style.background='transparent'"
                                   title="Edit Testimonial">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
                                <!-- Toggle Publish Button -->
                                <form action="{{ route('admin.testimonials.togglePublish', $t->id) }}" method="POST" style="display: inline;" class="toggle-publish-form">
                                    @csrf
                                    <button type="submit" class="btn btn-sm toggle-publish-btn" 
                                            style="background: {{ $t->is_published ? '#fff3e0' : '#e8f5e8' }}; color: {{ $t->is_published ? '#f57c00' : '#2e7d32' }}; border: none; padding: 4px 8px; font-size: 11px; border-radius: 4px; cursor: pointer; transition: opacity 0.2s;"
                                            onmouseover="this.style.opacity='0.8'" 
                                            onmouseout="this.style.opacity='1'"
                                            title="{{ $t->is_published ? 'Unpublish Testimonial' : 'Publish Testimonial' }}">
                                        <i class="fas fa-{{ $t->is_published ? 'eye-slash' : 'eye' }}"></i> {{ $t->is_published ? 'Unpublish' : 'Publish' }}
                                    </button>
                                </form>
                                <!-- Delete Button -->
                                <form action="{{ route('admin.testimonials.destroy', $t->id) }}" method="POST" style="display: inline;" class="delete-testimonial-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm delete-testimonial-btn" 
                                            style="background: #ffebee; color: #c62828; border: none; padding: 4px 8px; font-size: 11px; border-radius: 4px; cursor: pointer; transition: opacity 0.2s;"
                                            onmouseover="this.style.opacity='0.8'" 
                                            onmouseout="this.style.opacity='1'"
                                            title="Delete Testimonial"
                                            onclick="return confirm('Are you sure you want to delete this testimonial? This action cannot be undone.')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 40px; color: #828282;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 10px;">
                                <i class="fas fa-quote-left" style="font-size: 48px; color: #e0e0e0;"></i>
                                <span style="font-size: 14px;">No testimonials found</span>
                                <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary" style="background: #2F80ED; border: none; padding: 8px 16px; font-size: 13px; border-radius: 6px; color: white; text-decoration: none;">
                                    <i class="fas fa-plus"></i> Add First Testimonial
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $testimonials->links() }}
    </div>
</div>

<script>
// Search functionality for testimonials
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('testimonialSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase().trim();
            const tableRows = document.querySelectorAll('tbody tr');
            
            tableRows.forEach(row => {
                // Skip empty row
                if (row.querySelector('td[colspan]')) return;
                
                const name = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() || '';
                const position = row.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || '';
                const published = row.querySelector('td:nth-child(5)')?.textContent.toLowerCase() || '';
                
                const text = name + ' ' + position + ' ' + published;
                
                if (searchTerm === '' || text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Check if all rows are hidden
            const visibleRows = Array.from(tableRows).filter(row => {
                if (row.querySelector('td[colspan]')) return false;
                return row.style.display !== 'none';
            });
            const emptyRow = document.querySelector('tbody tr td[colspan]');
            
            if (visibleRows.length === 0 && searchTerm !== '' && emptyRow) {
                emptyRow.closest('tr').style.display = '';
                const span = emptyRow.querySelector('span');
                if (span) {
                    span.textContent = `No testimonials found for "${searchTerm}"`;
                }
            } else if (emptyRow && searchTerm === '') {
                // Restore original empty message if exists
                const span = emptyRow.querySelector('span');
                if (span && !span.textContent.includes('No testimonials found')) {
                    span.textContent = 'No testimonials found';
                }
            }
        });
    }

    // Add hover effects for better UX
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
        });
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });

    // Add loading state for form submissions
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                submitBtn.disabled = true;
                
                // Re-enable after 5 seconds as fallback
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 5000);
            }
        });
    });
});
</script>

<style>
@media (max-width: 768px) {
    .page-title { flex-direction: column !important; gap: 15px; }
    .page-title > div:last-child { max-width: 100% !important; width: 100% !important; }
}
</style>
@endsection
