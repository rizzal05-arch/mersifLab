@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-8">
            <h1>{{ $course->title }}</h1>
            <p class="text-muted">{{ $course->description }}</p>

            <div class="card mt-4">
                <div class="card-header">
                    <h5>Materi Pembelajaran</h5>
                </div>
                <div class="card-body">
                    @if($materials->count() > 0)
                        <ul class="list-group">
                            @foreach($materials as $material)
                                <li class="list-group-item">
                                    <h6>{{ $material->title }}</h6>
                                    <p>{{ $material->description }}</p>
                                    @if($material->file_path)
                                        <a href="{{ route('materi.download', $material->id) }}" class="btn btn-sm btn-primary">
                                            Download
                                        </a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p>Tidak ada materi untuk kursus ini.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Info Kursus</h5>
                    <p><strong>Instruktur:</strong> {{ $course->instructor ?? 'N/A' }}</p>
                    <p><strong>Harga:</strong> Rp{{ number_format($course->price ?? 0, 0, ',', '.') }}</p>
                    <button class="btn btn-primary w-100">Daftar Sekarang</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
