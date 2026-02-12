@extends('layouts.app')

@section('title', 'Verifikasi Manual Email')

@section('content')
<section class="auth-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="auth-card">
                    <h2 class="auth-title mb-4">Verifikasi Email Manual</h2>
                    
                    <div class="alert alert-warning mb-4">
                        <strong>⚠️ Untuk Development Only!</strong> Halaman ini hanya untuk testing.
                    </div>
                    
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ $errors->first() }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <form action="{{ route('debug.verify') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Anda</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="your@email.com" required value="{{ old('email') }}">
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Verifikasi Email</button>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="mb-3">
                        <h5>📧 Atau gunakan CLI Command:</h5>
                        <pre><code>php artisan user:verify your@email.com</code></pre>
                    </div>
                    
                    <div class="mb-3">
                        <h5>📝 Lihat Token & Logs:</h5>
                        <p>Token dikirim ke: <code>storage/logs/laravel.log</code></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
