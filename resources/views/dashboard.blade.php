@extends('layouts.app')
@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Dashboard</h1>
    
    @if(auth()->user()->isAdmin())
        <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-gray-500 text-sm">Total Kursus</h3>
                <p class="text-3xl font-bold">{{ $totalKursus }}</p>
            </div>
            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-gray-500 text-sm">Total Materi</h3>
                <p class="text-3xl font-bold">{{ $totalMateri }}</p>
            </div>
            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-gray-500 text-sm">Total User</h3>
                <p class="text-3xl font-bold">{{ $totalUsers ?? 0 }}</p>
            </div>
            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-gray-500 text-sm">Subscriber Aktif</h3>
                <p class="text-3xl font-bold">{{ $activeSubscribers ?? 0 }}</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded shadow mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold">Manajemen User</h2>
                <a href="/admin/users" class="text-blue-600 font-semibold">Lihat Semua →</a>
            </div>
            
            @if(isset($users) && $users->count() > 0)
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100 border-b">
                            <th class="text-left p-2">Nama</th>
                            <th class="text-left p-2">Email</th>
                            <th class="text-left p-2">Status</th>
                            <th class="text-left p-2">Berlaku Sampai</th>
                            <th class="text-left p-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users->take(5) as $user)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-2">{{ $user->name }}</td>
                                <td class="p-2 text-sm text-gray-600">{{ $user->email }}</td>
                                <td class="p-2">
                                    @if($user->isSubscriber())
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">✓ Aktif</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-sm">✗ Tidak Aktif</span>
                                    @endif
                                </td>
                                <td class="p-2 text-sm">
                                    @if($user->isSubscriber() && $user->subscription_expires_at)
                                        {{ $user->subscription_expires_at->format('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="p-2">
                                    @if(!$user->isSubscriber())
                                        <form action="/admin/users/{{ $user->id }}/subscribe" method="POST" class="inline-block">
                                            @csrf
                                            <select name="days" class="border p-1 rounded text-sm" required>
                                                <option value="">Durasi</option>
                                                <option value="7">7 hari</option>
                                                <option value="30">30 hari</option>
                                                <option value="90">90 hari</option>
                                            </select>
                                            <button type="submit" class="bg-green-600 text-white px-2 py-1 rounded text-sm ml-1">Aktif</button>
                                        </form>
                                    @else
                                        <form action="/admin/users/{{ $user->id }}/unsubscribe" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" class="bg-red-600 text-white px-2 py-1 rounded text-sm" onclick="return confirm('Yakin?')">Hapus</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($users->count() > 5)
                    <p class="text-gray-600 text-sm mt-4">Menampilkan 5 dari {{ $users->count() }} user</p>
                @endif
            @else
                <p class="text-gray-500">Tidak ada user</p>
            @endif
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <a href="/admin/materi" class="bg-blue-600 text-white p-6 rounded shadow hover:bg-blue-700 transition">
                <h3 class="text-lg font-bold mb-2">📚 Kelola Materi</h3>
                <p class="text-sm">Lihat dan manage semua materi kursus</p>
            </a>
            <a href="/admin/materi/create" class="bg-green-600 text-white p-6 rounded shadow hover:bg-green-700 transition">
                <h3 class="text-lg font-bold mb-2">➕ Tambah Materi</h3>
                <p class="text-sm">Upload materi baru untuk kursus</p>
            </a>
        </div>
    @endif
    
    <h2 class="text-2xl font-bold mb-4">Kursus Tersedia</h2>
    <div class="grid grid-cols-3 gap-4">
        @forelse($courses as $course)
            <div class="bg-white p-4 rounded shadow hover:shadow-lg transition">
                <h3 class="font-semibold text-lg mb-2">{{ $course->title }}</h3>
                <p class="text-gray-600 text-sm mb-4">{{ $course->description ?? 'Tidak ada deskripsi' }}</p>
                <a href="/courses/{{ $course->id }}" class="text-blue-600 font-semibold hover:underline">Lihat Materi →</a>
            </div>
        @empty
            <div class="col-span-3 bg-gray-50 p-6 rounded text-center">
                <p class="text-gray-500">Belum ada kursus tersedia</p>
            </div>
        @endforelse
    </div>
</div>
@endsection