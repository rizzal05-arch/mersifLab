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
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="{{ old('title', $module->title) }}" required>
                            @error('title')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($module->file_path)
                        <div class="mb-3">
                            <label class="form-label">Current File</label>
                            <div class="p-3 bg-light border rounded">
                                <i class="fas fa-file-pdf"></i> {{ $module->file_name }}
                                <span class="text-muted">({{ number_format($module->file_size / 1024, 2) }} KB)</span>
                            </div>
                            <small class="text-muted">Note: To replace the file, you need to delete and recreate this module.</small>
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="order" class="form-label">Order</label>
                                    <input type="number" class="form-control" id="order" name="order" 
                                           value="{{ old('order', $module->order) }}" min="0">
                                    @error('order')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estimated_duration" class="form-label">Estimasi Durasi (menit)</label>
                                    <input type="number" class="form-control @error('estimated_duration') is-invalid @enderror" 
                                           id="estimated_duration" name="estimated_duration" value="{{ old('estimated_duration', $module->estimated_duration) }}" min="1" placeholder="Contoh: 45">
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
