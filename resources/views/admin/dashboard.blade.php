@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<section class="py-5">
    <div class="container">
        <h1 class="mb-4">Dashboard</h1>
        
        @if(auth()->user()->isAdmin())
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title text-muted">Total Kursus</h6>
                            <h2 class="card-text">{{ $totalKursus }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title text-muted">Total Materi</h6>
                            <h2 class="card-text">{{ $totalMateri }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title text-muted">Total User</h6>
                            <h2 class="card-text">{{ $totalUsers ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title text-muted">Subscriber Aktif</h6>
                            <h2 class="card-text">{{ $activeSubscribers ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Management Section -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0">Manajemen User</h5>
                        </div>
                        <div class="col text-end">
                            <a href="/admin/users" class="link-primary">Lihat Semua →</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($users) && $users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Berlaku Sampai</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users->take(5) as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td><small class="text-muted">{{ $user->email }}</small></td>
                                            <td>
                                                @if($user->isSubscriber())
                                                    <span class="badge bg-success">✓ Aktif</span>
                                                @else
                                                    <span class="badge bg-danger">✗ Tidak Aktif</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($user->isSubscriber() && $user->subscription_expires_at)
                                                    {{ $user->subscription_expires_at->format('d M Y') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if(!$user->isSubscriber())
                                                    <form action="/admin/users/{{ $user->id }}/subscribe" method="POST" class="d-inline">
                                                        @csrf
                                                        <select name="days" class="form-select form-select-sm d-inline w-auto" required>
                                                            <option value="">Durasi</option>
                                                            <option value="7">7 hari</option>
                                                            <option value="30">30 hari</option>
                                                            <option value="90">90 hari</option>
                                                        </select>
                                                        <button type="submit" class="btn btn-success btn-sm ms-1">Aktif</button>
                                                    </form>
                                                @else
                                                    <form action="/admin/users/{{ $user->id }}/unsubscribe" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin?')">Hapus</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($users->count() > 5)
                            <small class="text-muted">Menampilkan 5 dari {{ $users->count() }} user</small>
                        @endif
                    @else
                        <p class="text-muted">Tidak ada user</p>
                    @endif
                </div>
            </div>

            <!-- Admin Action Cards -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <a href="/admin/materi" class="card bg-primary text-white text-decoration-none shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-2">📚 Kelola Materi</h5>
                            <p class="card-text small">Lihat dan manage semua materi kursus</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 mb-3">
                    <a href="/admin/materi/create" class="card bg-success text-white text-decoration-none shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-2">➕ Tambah Materi</h5>
                            <p class="card-text small">Upload materi baru untuk kursus</p>
                        </div>
                    </a>
                </div>
            </div>
        @else
            <!-- Regular Student Dashboard -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="card-title text-muted">Kursus Aktif</h6>
                            <h2 class="card-text">0</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="card-title text-muted">Materi Selesai</h6>
                            <h2 class="card-text">0</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="card-title text-muted">Progress</h6>
                            <h2 class="card-text">0%</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <p class="text-muted">Mulai belajar dengan mengunjungi halaman <a href="{{ route('courses') }}">Kursus</a></p>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection