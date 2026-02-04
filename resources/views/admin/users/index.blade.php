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
                    <td style="padding: 16px 8px; vertical-align: middle;">
                        <div style="display: flex; gap: 6px; align-items: center; flex-wrap: wrap;">
                            @if(!$user->isSubscriber())
                                <!-- Subscribe Form -->
                                <form action="/admin/users/{{ $user->id }}/subscribe" method="POST" style="display: inline;" class="subscribe-user-form">
                                    @csrf
                                    <select name="days" style="border: 1px solid #ddd; padding: 4px 6px; border-radius: 4px; font-size: 11px; margin-right: 4px;" required>
                                        <option value="">Pilih durasi</option>
                                        <option value="7">7 hari</option>
                                        <option value="30">30 hari</option>
                                        <option value="90">90 hari</option>
                                        <option value="365">1 tahun</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm subscribe-user-btn" 
                                            style="background: #e8f5e8; color: #2e7d32; border: none; padding: 4px 8px; font-size: 11px; border-radius: 4px; cursor: pointer; transition: opacity 0.2s;"
                                            onmouseover="this.style.opacity='0.8'" 
                                            onmouseout="this.style.opacity='1'"
                                            title="Subscribe User">
                                        Aktivasi
                                    </button>
                                </form>
                            @else
                                <!-- Unsubscribe Button -->
                                <form action="/admin/users/{{ $user->id }}/unsubscribe" method="POST" style="display: inline;" class="unsubscribe-user-form">
                                    @csrf
                                    <button type="submit" class="btn btn-sm unsubscribe-user-btn" 
                                            style="background: #ffebee; color: #c62828; border: none; padding: 4px 8px; font-size: 11px; border-radius: 4px; cursor: pointer; transition: opacity 0.2s;"
                                            onmouseover="this.style.opacity='0.8'" 
                                            onmouseout="this.style.opacity='1'"
                                            title="Remove Subscription"
                                            onclick="return confirm('Are you sure you want to remove this user subscription?')">
                                        <i class="fas fa-times"></i> Remove
                                    </button>
                                </form>
                            @endif
                        </div>
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
