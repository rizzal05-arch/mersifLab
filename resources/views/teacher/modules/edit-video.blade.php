@extends('layouts.app')

@section('title', 'Edit Video Module')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Video Module</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('teacher.modules.update', [$chapter, $module]) }}" method="POST" enctype="multipart/form-data">
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

                        <div class="mb-3">
                            <label class="form-label">Current Video Type</label>
                            <div class="p-3 bg-light border rounded">
                                @if($module->file_path)
                                    <i class="fas fa-video"></i> Uploaded Video File
                                    <span class="text-muted">({{ number_format($module->file_size / 1024 / 1024, 2) }} MB)</span>
                                    <br><small class="text-muted">{{ $module->file_name }}</small>
                                @elseif($module->video_url)
                                    <i class="fas fa-link"></i> Embedded Video URL
                                @else
                                    <i class="fas fa-question"></i> Unknown video type
                                @endif
                            </div>
                        </div>

                        @if($module->video_url)
                        <div class="mb-3" id="current_url_section">
                            <label class="form-label">Current Video URL</label>
                            <input type="text" class="form-control bg-light" value="{{ old('video_url', $module->video_url) }}" readonly>
                            <input type="hidden" name="video_url" value="{{ old('video_url', $module->video_url) }}">
                        </div>
                        @endif

                        <div class="mb-3">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="replace_video" name="replace_video" value="1">
                                <label class="form-check-label" for="replace_video">
                                    <strong>Ganti Video</strong>
                                </label>
                            </div>
                            <small class="text-muted">Centang untuk mengganti video dengan yang baru</small>
                        </div>
                        
                        <!-- Video Type Selection (Hidden by default) -->
                        <div id="video_replacement_section" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">New Video Type <span class="text-danger">*</span></label>
                                <div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="new_video_type" id="new_video_upload" 
                                               value="upload" required>
                                        <label class="form-check-label" for="new_video_upload">
                                            Upload Video File
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="new_video_type" id="new_video_url" 
                                               value="url">
                                        <label class="form-check-label" for="new_video_url">
                                            Embed from URL (YouTube, Vimeo, etc)
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- File Upload Field -->
                            <div class="mb-3" id="new_file_field" style="display: none;">
                                <label for="new_file" class="form-label">New Video File <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="new_file" name="new_file" accept="video/*">
                                <small class="form-text text-muted">
                                    Maximum file size: 500 MB. Supported formats: MP4, AVI, MOV, WMV
                                </small>
                            </div>

                            <!-- URL Field -->
                            <div class="mb-3" id="new_url_field" style="display: none;">
                                <label for="new_video_url" class="form-label">New Video URL <span class="text-danger">*</span></label>
                                <input type="url" class="form-control" id="new_video_url" name="new_video_url" 
                                       placeholder="https://youtube.com/watch?v=... or https://youtu.be/...">
                                <small class="form-text text-muted">
                                    <strong>Supported formats:</strong><br>
                                    • YouTube: <code>https://youtube.com/watch?v=VIDEO_ID</code><br>
                                    • YouTube Short: <code>https://youtu.be/VIDEO_ID</code><br>
                                    • YouTube Embed: <code>https://youtube.com/embed/VIDEO_ID</code><br>
                                    • Vimeo: <code>https://vimeo.com/VIDEO_ID</code>
                                </small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estimated_duration" class="form-label">Estimasi Durasi (menit) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('estimated_duration') is-invalid @enderror" 
                                           id="estimated_duration" name="estimated_duration" value="{{ old('estimated_duration', $module->estimated_duration) }}" min="1" placeholder="Contoh: 60" required>
                                    <small class="form-text text-muted">
                                        Estimasi waktu yang dibutuhkan siswa untuk menonton video ini
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
// Toggle video replacement section
document.getElementById('replace_video').addEventListener('change', function() {
    const replacementSection = document.getElementById('video_replacement_section');
    const currentUrlSection = document.getElementById('current_url_section');
    const hiddenVideoUrl = currentUrlSection ? currentUrlSection.querySelector('input[name="video_url"]') : null;
    
    if (this.checked) {
        replacementSection.style.display = 'block';
        if (currentUrlSection) currentUrlSection.style.display = 'none';
        if (hiddenVideoUrl) hiddenVideoUrl.removeAttribute('name'); // jangan kirim URL lama saat ganti video
    } else {
        replacementSection.style.display = 'none';
        if (currentUrlSection) currentUrlSection.style.display = 'block';
        if (hiddenVideoUrl) hiddenVideoUrl.setAttribute('name', 'video_url');
        // Reset radio buttons
        document.querySelectorAll('input[name="new_video_type"]').forEach(radio => radio.checked = false);
        document.getElementById('new_file_field').style.display = 'none';
        document.getElementById('new_url_field').style.display = 'none';
        document.getElementById('new_file').value = '';
        document.getElementById('new_video_url').value = '';
    }
});

// Handle new video type selection
document.querySelectorAll('input[name="new_video_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const fileField = document.getElementById('new_file_field');
        const urlField = document.getElementById('new_url_field');
        const fileInput = document.getElementById('new_file');
        const videoUrlInput = document.getElementById('new_video_url');
        
        if (this.value === 'upload') {
            fileField.style.display = 'block';
            urlField.style.display = 'none';
            fileInput.required = true;
            videoUrlInput.required = false;
        } else {
            fileField.style.display = 'none';
            urlField.style.display = 'block';
            fileInput.required = false;
            videoUrlInput.required = true;
        }
    });
});

// File validation for video upload
document.getElementById('new_file')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    const maxSize = 500 * 1024 * 1024; // 500MB in bytes
    const allowedTypes = ['video/mp4', 'video/avi', 'video/quicktime', 'video/x-ms-wmv'];
    
    // Clear previous errors
    const existingError = document.getElementById('video-size-error');
    if (existingError) {
        existingError.remove();
    }
    
    // Remove invalid class
    document.getElementById('new_file').classList.remove('is-invalid');
    
    if (file) {
        // Check file type
        if (!allowedTypes.includes(file.type)) {
            const errorDiv = document.createElement('div');
            errorDiv.id = 'video-size-error';
            errorDiv.className = 'invalid-feedback d-block';
            errorDiv.textContent = 'Format video yang diperbolehkan: mp4, avi, mov, wmv';
            
            document.getElementById('new_file').classList.add('is-invalid');
            document.getElementById('new_file').parentNode.appendChild(errorDiv);
            
            // Clear file input
            e.target.value = '';
            
            return;
        }
        
        // Check file size
        if (file.size > maxSize) {
            const errorDiv = document.createElement('div');
            errorDiv.id = 'video-size-error';
            errorDiv.className = 'invalid-feedback d-block';
            errorDiv.textContent = 'Ukuran file video tidak boleh lebih dari 500MB';
            
            document.getElementById('new_file').classList.add('is-invalid');
            document.getElementById('new_file').parentNode.appendChild(errorDiv);
            
            // Clear file input
            e.target.value = '';
            
            return;
        }
    }
});

// Form submission validation (tanpa dialog konfirmasi - langsung submit, pesan sukses & notifikasi setelah berhasil)
document.querySelector('form').addEventListener('submit', function(e) {
    const replaceVideo = document.getElementById('replace_video').checked;
    const newVideoType = document.querySelector('input[name="new_video_type"]:checked');
    const fileInput = document.getElementById('new_file');
    const videoUrlInput = document.getElementById('new_video_url');
    
    if (replaceVideo) {
        if (!newVideoType) {
            e.preventDefault();
            alert('Silakan pilih tipe video baru (Upload File atau Embed URL).');
            return false;
        }
        
        if (newVideoType.value === 'upload' && !fileInput.files.length) {
            e.preventDefault();
            alert('Silakan pilih file video untuk diupload.');
            return false;
        }
        
        if (newVideoType.value === 'url' && !videoUrlInput.value.trim()) {
            e.preventDefault();
            alert('Please enter a valid video URL.');
            return false;
        }
    }
});
</script>
@endsection
