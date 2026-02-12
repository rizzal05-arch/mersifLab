@extends('layouts.app')

@section('title', 'Verifikasi Email')

@section('content')
<section class="auth-section py-5">
    <div class="container">
        <div class="row align-items-center justify-content-center">
            <!-- Center - Verification Pending -->
            <div class="col-lg-6">
                <div class="auth-card text-center">
                    <div class="mb-4">
                        <i class="fas fa-envelope-circle-check" style="font-size: 4rem; color: #4CAF50;"></i>
                    </div>
                    
                    <h2 class="auth-title mb-3">Periksa Email Anda</h2>
                    
                    <p class="text-muted mb-4">
                        Kami telah mengirimkan link verifikasi ke <strong>{{ session('email', 'email@gmail.com') }}</strong>
                    </p>
                    
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle"></i>
                        <strong>Instruksi Berikutnya:</strong>
                        <ol class="mb-0 text-start mt-2">
                            <li>Buka email Anda</li>
                            <li>Cari email dari MersifLab</li>
                            <li>Klik tombol "Verifikasi Email"</li>
                            <li>Akun Anda akan langsung aktif</li>
                        </ol>
                    </div>
                    
                    <hr>
                    
                    <p class="text-muted mb-3">Belum menerima email?</p>
                    
                    <form action="{{ route('verify.resend') }}" method="POST" class="my-3">
                        @csrf
                        <input type="hidden" name="email" value="{{ session('email') }}">
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="fas fa-redo"></i> Kirim Ulang Email Verifikasi
                        </button>
                    </form>
                    
                    <div class="divider my-3">
                        <span>atau</span>
                    </div>
                    
                    <p class="text-muted mb-0">
                        <a href="{{ route('login') }}" class="text-primary fw-bold">Kembali ke Login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
