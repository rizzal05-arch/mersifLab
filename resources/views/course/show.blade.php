@extends('layouts.app')
@section('content')
<div class="max-w-4xl mx-auto">
    <a href="/dashboard" class="text-blue-600 mb-4 inline-block">← Kembali ke Dashboard</a>
    
    <h1 class="text-3xl font-bold mb-2">{{ $course->title }}</h1>
    <p class="text-gray-600 mb-6">{{ $course->description ?? 'Tidak ada deskripsi' }}</p>
    
    <h2 class="text-2xl font-bold mb-4">Materi Kursus</h2>
    
    @if($materi && $materi->count() > 0)
        <div class="grid grid-cols-1 gap-4">
            @foreach($materi as $item)
                <div class="bg-white p-4 rounded shadow hover:shadow-lg transition">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold text-lg">{{ $item->title }}</h3>
                            <p class="text-gray-500 text-sm">Tipe: <span class="badge">{{ ucfirst($item->type) }}</span></p>
                        </div>
                        <a href="/materi/{{ $item->id }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            @if($item->type === 'pdf')
                                � Lihat PDF
                            @else
                                ▶️ Tonton Video
                            @endif
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-gray-50 p-6 rounded text-center">
            <p class="text-gray-500">Belum ada materi untuk kursus ini</p>
        </div>
    @endif
</div>
@endsection