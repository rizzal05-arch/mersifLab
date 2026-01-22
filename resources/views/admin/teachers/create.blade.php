@extends('layouts.admin')

@section('title', 'Add New Teacher')

@section('content')
<div class="page-title">
    <h1>Add New Teacher</h1>
</div>

<div class="card-content" style="background: white; border-radius: 12px; padding: 48px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); text-align: center;">
    <i class="fas fa-chalkboard-user" style="font-size: 64px; color: #e0e0e0; margin-bottom: 20px;"></i>
    <h3 style="color: #333; margin-bottom: 12px;">Fitur Coming Soon</h3>
    <p style="color: #828282; margin-bottom: 24px; max-width: 400px; margin-left: auto; margin-right: auto;">
        Tambah guru secara manual akan tersedia di versi mendatang. Saat ini guru terdaftar melalui halaman <strong>Registrasi</strong> dengan role Teacher.
    </p>
    <a href="{{ route('admin.teachers.index') }}" class="btn" style="background: #2F80ED; color: white; border: none; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 500;">
        <i class="fas fa-arrow-left me-2"></i>Kembali ke Teachers
    </a>
</div>
@endsection
