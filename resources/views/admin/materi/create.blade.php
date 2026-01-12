@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Tambah Materi</h1>
    
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form method="POST" action="/admin/materi" enctype="multipart/form-data" class="bg-white p-6 rounded shadow">
        @csrf
        
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2" for="title">
                Judul
            </label>
            <input class="w-full border border-gray-300 p-2 rounded @error('title') border-red-500 @enderror" 
                   type="text" name="title" id="title" value="{{ old('title') }}" required>
            @error('title')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2" for="course_id">
                Kursus
            </label>
            <select class="w-full border border-gray-300 p-2 rounded @error('course_id') border-red-500 @enderror" 
                    name="course_id" id="course_id" required>
                <option value="">Pilih Kursus</option>
                @if(isset($courses) && $courses->count() > 0)
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->title }}
                        </option>
                    @endforeach
                @else
                    <option value="" disabled>Tidak ada kursus tersedia</option>
                @endif
            </select>
            @error('course_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2" for="type">
                Tipe
            </label>
            <select class="w-full border border-gray-300 p-2 rounded @error('type') border-red-500 @enderror" 
                    name="type" id="type" required>
                <option value="">Pilih Tipe</option>
                <option value="pdf" {{ old('type') == 'pdf' ? 'selected' : '' }}>PDF</option>
                <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>Video</option>
            </select>
            @error('type')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2" for="file">
                File
            </label>
            <input class="w-full border border-gray-300 p-2 rounded @error('file') border-red-500 @enderror" 
                   type="file" name="file" id="file" required>
            @error('file')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="flex gap-2">
            <button class="bg-blue-600 text-white px-4 py-2 rounded" type="submit">
                Simpan
            </button>
            <a href="/admin/materi" class="bg-gray-400 text-white px-4 py-2 rounded">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
