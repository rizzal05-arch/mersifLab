@extends('layouts.app')

@section('title', 'Edit Document Module')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Document Module</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('teacher.modules.update', [$chapter, $module]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="{{ old('title', $module->title) }}" required>
                            @error('title')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($module->file_path)
                        <div class="mb-3">
                            <label class="form-label">Current File</label>
                            <div class="p-3 bg-light border rounded mb-3">
                                <i class="fas fa-file-pdf"></i> {{ $module->file_name }}
                                <span class="text-muted">({{ number_format($module->file_size / 1024, 2) }} KB)</span>
                            </div>
                            
                            <!-- Opsi untuk mengganti file -->
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="replace_file" name="replace_file" value="1">
                                <label class="form-check-label" for="replace_file">
                                    <strong>Ganti File PDF</strong>
                                </label>
                            </div>
                            <small class="text-muted">Centang untuk mengganti file dengan yang baru</small>
                        </div>
                        
                        <!-- File upload tersembunyi, muncul saat checkbox dicentang -->
                        <div class="mb-3" id="file_upload_section" style="display: none;">
                            <label for="file" class="form-label">File Baru <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="file" name="file" accept=".pdf,.doc,.docx,.ppt,.pptx" 
                                   onchange="validateFile(this)">
                            <small class="form-text text-muted">
                                Format yang diizinkan: PDF, DOC, DOCX, PPT, PPTX. Maksimal 20MB.
                            </small>
                            @error('file')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        @else
                        <div class="mb-3">
                            <label for="file" class="form-label">Upload File <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="file" name="file" accept=".pdf,.doc,.docx,.ppt,.pptx" 
                                   onchange="validateFile(this)" required>
                            <small class="form-text text-muted">
                                Format yang diizinkan: PDF, DOC, DOCX, PPT, PPTX. Maksimal 20MB.
                            </small>
                            @error('file')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estimated_duration" class="form-label">Estimasi Durasi (menit) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('estimated_duration') is-invalid @enderror" 
                                           id="estimated_duration" name="estimated_duration" value="{{ old('estimated_duration', $module->estimated_duration) }}" min="1" placeholder="Contoh: 45" required>
                                    <small class="form-text text-muted">
                                        Estimasi waktu yang dibutuhkan siswa untuk membaca PDF ini
                                    </small>
                                    @error('estimated_duration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <!-- Published status removed - modules follow course approval workflow -->
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <a href="{{ route('teacher.manage.content') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Module
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle file upload section
document.getElementById('replace_file').addEventListener('change', function() {
    const fileSection = document.getElementById('file_upload_section');
    const fileInput = document.getElementById('file');
    
    if (this.checked) {
        fileSection.style.display = 'block';
        fileInput.setAttribute('required', 'required');
    } else {
        fileSection.style.display = 'none';
        fileInput.removeAttribute('required');
        fileInput.value = ''; // Clear file input
    }
});

// File validation
function validateFile(input) {
    const file = input.files[0];
    const maxSize = 20 * 1024 * 1024; // 20MB
    const allowedTypes = ['application/pdf', 'application/msword', 
                         'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                         'application/vnd.ms-powerpoint', 
                         'application/vnd.openxmlformats-officedocument.presentationml.presentation'];
    
    if (file) {
        if (file.size > maxSize) {
            alert('File terlalu besar. Maksimal 20MB.');
            input.value = '';
            return false;
        }
        
        if (!allowedTypes.includes(file.type)) {
            alert('Format file tidak diizinkan. Gunakan PDF, DOC, DOCX, PPT, atau PPTX.');
            input.value = '';
            return false;
        }
    }
    
    return true;
}

// Form submission confirmation
document.querySelector('form').addEventListener('submit', function(e) {
    const replaceFile = document.getElementById('replace_file').checked;
    const fileInput = document.getElementById('file');
    
    if (replaceFile && !fileInput.files.length) {
        e.preventDefault();
        alert('Silakan pilih file baru untuk mengganti file yang ada.');
        return false;
    }
    
    // Confirmation if replacing a file that is already approved
    @if($module->approval_status === 'approved')
    if (replaceFile) {
        if (!confirm('Replacing the file will change the module status to "Pending Approval" and require re-approval from admin. Continue?')) {
            e.preventDefault();
            return false;
        }
    }
    
    // Confirmation for other changes to an already approved module
    if (!replaceFile) {
        if (!confirm('Changing an approved module will change the status to "Pending Approval" and require re-approval from admin. Continue?')) {
            e.preventDefault();
            return false;
        }
    }
    @endif
});
</script>
@endsection
