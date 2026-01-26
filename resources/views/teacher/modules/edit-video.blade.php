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
                    <form action="{{ route('teacher.modules.update', [$chapter, $module]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="{{ old('title', $module->title) }}" required>
                            @error('title')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Video Type</label>
                            <div class="bg-light p-3 border rounded">
                                @if($module->file_path)
                                    <i class="fas fa-video"></i> Uploaded Video File
                                    <span class="text-muted">({{ number_format($module->file_size / 1024 / 1024, 2) }} MB)</span>
                                @elseif($module->video_url)
                                    <i class="fas fa-link"></i> Embedded Video URL
                                @else
                                    <i class="fas fa-question"></i> Unknown video type
                                @endif
                            </div>
                            <small class="text-muted">Note: To change video type, you need to delete and recreate this module.</small>
                        </div>

                        @if($module->video_url)
                        <div class="mb-3">
                            <label for="video_url" class="form-label">Video URL</label>
                            <input type="url" class="form-control @error('video_url') is-invalid @enderror" id="video_url" name="video_url" 
                                   value="{{ old('video_url', $module->video_url) }}" placeholder="https://youtube.com/watch?v=... or https://youtu.be/...">
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
                        @endif

                        <div class="mb-3">
                            <label for="duration" class="form-label">Duration (minutes)</label>
                            <input type="number" class="form-control" id="duration" name="duration" 
                                   value="{{ old('duration', $module->duration) }}" min="0" placeholder="Video duration in minutes">
                            @error('duration')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estimated_duration" class="form-label">Estimasi Durasi (menit)</label>
                                    <input type="number" class="form-control @error('estimated_duration') is-invalid @enderror" 
                                           id="estimated_duration" name="estimated_duration" value="{{ old('estimated_duration', $module->estimated_duration) }}" min="1" placeholder="Contoh: 60">
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
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" id="is_published" name="is_published" 
                                               value="1" {{ old('is_published', $module->is_published) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_published">
                                            Published
                                        </label>
                                    </div>
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
@endsection
