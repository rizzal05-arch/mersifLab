<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LMS Starter</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<nav class="bg-white shadow p-4 flex justify-between">
    <div class="font-bold">LMS</div>
    <div>
        @auth
            <span class="mr-4">{{ auth()->user()->name }}</span>
            <form action="/logout" method="POST" class="inline">@csrf<button>Logout</button></form>
        @else
            <a href="/login" class="mr-4">Login</a>
            <a href="/register">Register</a>
        @endauth
    </div>
</nav>
<div class="p-6">
    @yield('content')
</div>
</body>
</html>