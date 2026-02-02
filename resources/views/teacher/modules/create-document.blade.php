@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">📄 Create Document Module (PDF)</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('teacher.modules.store.document', $chapter) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">Module Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="file" class="form-label">PDF File <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" 
                                   id="file" name="file" accept=".pdf" required>
                            <small class="form-text text-muted">
                                Maximum file size: 50 MB. Only PDF files allowed.
                            </small>
                            @error('file')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="estimated_duration" class="form-label">Estimasi Durasi (menit) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('estimated_duration') is-invalid @enderror" 
                                   id="estimated_duration" name="estimated_duration" value="{{ old('estimated_duration') }}" min="1" placeholder="Contoh: 45" required>
                            <small class="form-text text-muted">
                                Estimasi waktu yang dibutuhkan siswa untuk membaca PDF ini
                            </small>
                            @error('estimated_duration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info" role="alert">
                            <strong>💡 Tip:</strong> Make sure your PDF file is properly formatted and optimized for online viewing.
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">Upload & Create</button>
                            <a href="{{ route('teacher.modules.create', $chapter) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
