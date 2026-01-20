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
                            <label for="file" class="form-label">Video File</label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" 
                                   id="file" name="file" accept="video/*">
                            <small class="form-text text-muted">
                                Maximum file size: 500 MB. Supported formats: MP4, AVI, MOV, WMV
                            </small>
                            @error('file')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- URL Field -->
                        <div class="mb-3" id="url-field" style="display: none;">
                            <label for="video_url" class="form-label">Video URL</label>
                            <input type="url" class="form-control @error('video_url') is-invalid @enderror" 
                                   id="video_url" name="video_url" placeholder="https://youtube.com/watch?v=..." 
                                   value="{{ old('video_url') }}">
                            <small class="form-text text-muted">
                                YouTube, Vimeo, or direct video URL
                            </small>
                            @error('video_url')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="duration" class="form-label">Duration (seconds)</label>
                            <input type="number" class="form-control @error('duration') is-invalid @enderror" 
                                   id="duration" name="duration" value="{{ old('duration') }}" min="0"
                                   placeholder="e.g., 3600">
                            <small class="form-text text-muted">
                                Optional. Enter total video duration in seconds.
                            </small>
                            @error('duration')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="order" class="form-label">Order</label>
                            <input type="number" class="form-control @error('order') is-invalid @enderror" 
                                   id="order" name="order" value="{{ old('order', 0) }}" min="0">
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
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
            
            if (this.value === 'upload') {
                fileField.style.display = 'block';
                urlField.style.display = 'none';
                document.getElementById('file').required = true;
                document.getElementById('video_url').required = false;
            } else {
                fileField.style.display = 'none';
                urlField.style.display = 'block';
                document.getElementById('file').required = false;
                document.getElementById('video_url').required = true;
            }
        });
    });

    // Trigger on page load to set initial state
    document.querySelector('input[name="video_type"]:checked')?.dispatchEvent(new Event('change'));
</script>
@endsection
