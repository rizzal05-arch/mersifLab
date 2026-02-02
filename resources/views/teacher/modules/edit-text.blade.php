@extends('layouts.app')

@section('title', 'Edit Text Module')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Text Module</h5>
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

                        <div class="mb-3">
                            <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="content" name="content" rows="10" required>{{ old('content', $module->content) }}</textarea>
                            @error('content')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estimated_duration" class="form-label">Estimasi Durasi (menit) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('estimated_duration') is-invalid @enderror" 
                                           id="estimated_duration" name="estimated_duration" value="{{ old('estimated_duration', $module->estimated_duration) }}" min="1" placeholder="Contoh: 30" required>
                                    <small class="form-text text-muted">
                                        Estimasi waktu yang dibutuhkan siswa untuk menyelesaikan module ini
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

<script src="https://cdn.tiny.cloud/1/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#content',
        height: 400,
        plugins: ['link', 'image', 'code', 'lists'],
        toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link image | code'
    });
</script>
@endsection
