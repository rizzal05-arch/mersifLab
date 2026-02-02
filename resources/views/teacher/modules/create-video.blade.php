@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">🎥 Create Video Module</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('teacher.modules.store.video', $chapter) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">Module Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Video Type Selection -->
                        <div class="mb-3">
                            <label class="form-label">Video Type <span class="text-danger">*</span></label>
                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="video_type" id="video_upload" 
                                           value="upload" {{ old('video_type') === 'upload' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="video_upload">
                                        Upload Video File
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="video_type" id="video_url" 
                                           value="url" {{ old('video_type') === 'url' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="video_url">
                                        Embed from URL (YouTube, Vimeo, etc)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- File Upload Field -->
                        <div class="mb-3" id="file-field">
                            <label for="file" class="form-label">Video File <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" 
                                   id="file" name="file" accept="video/*" required>
                            <small class="form-text text-muted">
                                Maximum file size: 500 MB. Supported formats: MP4, AVI, MOV, WMV
                            </small>
                            @error('file')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- URL Field -->
                        <div class="mb-3" id="url-field" style="display: none;">
                            <label for="video_url" class="form-label">Video URL <span class="text-danger">*</span></label>
                            <input type="url" class="form-control @error('video_url') is-invalid @enderror" 
                                   id="video_url" name="video_url" placeholder="https://youtube.com/watch?v=... or https://youtu.be/..." 
                                   value="{{ old('video_url') }}">
                            <small class="form-text text-muted">
                                <strong>Supported formats:</strong><br>
                                • YouTube: <code>https://youtube.com/watch?v=VIDEO_ID</code><br>
                                • YouTube Short: <code>https://youtu.be/VIDEO_ID</code><br>
                                • YouTube Embed: <code>https://youtube.com/embed/VIDEO_ID</code><br>
                                • Vimeo: <code>https://vimeo.com/VIDEO_ID</code>
                            </small>
                            @error('video_url')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="duration" class="form-label">Duration (seconds) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('duration') is-invalid @enderror" 
                                   id="duration" name="duration" value="{{ old('duration') }}" min="0"
                                   placeholder="e.g., 3600" required>
                            <small class="form-text text-muted">
                                Required. Enter total video duration in seconds.
                            </small>
                            @error('duration')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="estimated_duration" class="form-label">Estimasi Durasi (menit) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('estimated_duration') is-invalid @enderror" 
                                   id="estimated_duration" name="estimated_duration" value="{{ old('estimated_duration') }}" min="1" placeholder="Contoh: 60" required>
                            <small class="form-text text-muted">
                                Estimasi waktu yang dibutuhkan siswa untuk menonton video ini
                            </small>
                            @error('estimated_duration')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-danger">Create Video Module</button>
                            <a href="{{ route('teacher.modules.create', $chapter) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('input[name="video_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const fileField = document.getElementById('file-field');
            const urlField = document.getElementById('url-field');
            const fileInput = document.getElementById('file');
            const videoUrlInput = document.getElementById('video_url');
            
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

    // Trigger on page load to set initial state
    document.querySelector('input[name="video_type"]:checked')?.dispatchEvent(new Event('change'));
</script>
@endsection
