@extends('layouts.app')
@section('content')
<div class="max-w-md mx-auto bg-white p-6 rounded shadow">
<h2 class="text-xl font-bold mb-4">Register</h2>

@if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<form method="POST" action="/register">@csrf
<input type="text" name="name" placeholder="Name" value="{{ old('name') }}" class="w-full border p-2 mb-3" required>
<input type="email" name="email" placeholder="Email" value="{{ old('email') }}" class="w-full border p-2 mb-3" required>
<input type="password" name="password" placeholder="Password" class="w-full border p-2 mb-3" required>
<input type="password" name="password_confirmation" placeholder="Confirm Password" class="w-full border p-2 mb-3" required>
<button type="submit" class="bg-green-600 text-white px-4 py-2 w-full">Register</button>
</form>

<p class="text-center mt-4">Already have an account? <a href="/login" class="text-blue-600">Login</a></p>
</div>
@endsection