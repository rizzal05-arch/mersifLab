@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Kelola Langganan User</h1>
    
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    
    <table class="w-full border-collapse border">
        <thead>
            <tr class="bg-gray-200">
                <th class="border p-2">Nama</th>
                <th class="border p-2">Email</th>
                <th class="border p-2">Role</th>
                <th class="border p-2">Status Langganan</th>
                <th class="border p-2">Berlaku Sampai</th>
                <th class="border p-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td class="border p-2">{{ $user->name }}</td>
                    <td class="border p-2">{{ $user->email }}</td>
                    <td class="border p-2">
                        <span class="px-2 py-1 rounded {{ $user->isAdmin() ? 'bg-red-200 text-red-800' : 'bg-blue-200 text-blue-800' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="border p-2">
                        @if($user->isSubscriber())
                            <span class="px-2 py-1 bg-green-200 text-green-800 rounded">✓ Aktif</span>
                        @else
                            <span class="px-2 py-1 bg-red-200 text-red-800 rounded">✗ Inactive</span>
                        @endif
                    </td>
                    <td class="border p-2">
                        @if($user->isSubscriber())
                            @if($user->subscription_expires_at)
                                {{ $user->subscription_expires_at->format('d M Y') }}
                            @else
                                Unlimited
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td class="border p-2">
                        @if(!$user->isSubscriber())
                            <form action="/admin/users/{{ $user->id }}/subscribe" method="POST" class="inline-block">
                                @csrf
                                <select name="days" class="border p-1 rounded" required>
                                    <option value="">Pilih durasi</option>
                                    <option value="7">7 hari</option>
                                    <option value="30">30 hari</option>
                                    <option value="90">90 hari</option>
                                    <option value="365">1 tahun</option>
                                </select>
                                <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded text-sm">Aktivasi</button>
                            </form>
                        @else
                            <form action="/admin/users/{{ $user->id }}/unsubscribe" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded text-sm" onclick="return confirm('Are you sure you want to remove this user subscription?')">Remove</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="border p-2 text-center">No users</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
