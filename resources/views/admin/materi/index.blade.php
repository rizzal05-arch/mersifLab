@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Manajemen Materi</h1>
    
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    
    <a href="/admin/materi/create" class="inline-block bg-blue-600 text-white px-4 py-2 rounded mb-4">
        Tambah Materi
    </a>
    
    <table class="w-full border-collapse border">
        <thead>
            <tr class="bg-gray-200">
                <th class="border p-2">Judul</th>
                <th class="border p-2">Kursus</th>
                <th class="border p-2">Tipe</th>
                <th class="border p-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($materi as $item)
                <tr>
                    <td class="border p-2">{{ $item->title }}</td>
                    <td class="border p-2">{{ $item->course->title ?? 'N/A' }}</td>
                    <td class="border p-2">{{ ucfirst($item->type) }}</td>
                    <td class="border p-2">
                        <a href="/materi/{{ $item->id }}" class="text-blue-600 mr-2">Lihat</a>
                        <form action="/admin/materi/{{ $item->id }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin hapus materi ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="border p-2 text-center">Tidak ada materi</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
