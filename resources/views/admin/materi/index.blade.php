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
                    <td style="padding: 16px 8px; vertical-align: middle;">
                        <div style="display: flex; gap: 6px; align-items: center; flex-wrap: wrap;">
                            <!-- View Button (Text Link) -->
                            <a href="/materi/{{ $item->id }}" 
                               style="color: #1976d2; text-decoration: none; font-size: 12px; font-weight: 500; padding: 4px 8px; border-radius: 4px; transition: background 0.2s;"
                               onmouseover="this.style.background='#e3f2fd'" 
                               onmouseout="this.style.background='transparent'"
                               title="View Material">
                                <i class="fas fa-eye me-1"></i>View
                            </a>
                            <!-- Delete Button -->
                            <form action="/admin/materi/{{ $item->id }}" method="POST" style="display: inline;" class="delete-materi-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm delete-materi-btn" 
                                        style="background: #ffebee; color: #c62828; border: none; padding: 4px 8px; font-size: 11px; border-radius: 4px; cursor: pointer; transition: opacity 0.2s;"
                                        onmouseover="this.style.opacity='0.8'" 
                                        onmouseout="this.style.opacity='1'"
                                        title="Delete Material"
                                        onclick="return confirm('Are you sure you want to delete this material?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="border p-2 text-center">No materials</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
