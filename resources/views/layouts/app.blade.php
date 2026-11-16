<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amikom Event & Ticketing</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <nav class="bg-indigo-700 text-white shadow">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="/dashboard" class="font-bold text-lg tracking-wide">Amikom Event & Tiket</a>
            <div class="flex items-center space-x-4 text-sm">
                @auth
                    <a href="/dashboard" class="hover:underline">Dashboard</a>
                    <a href="/events" class="hover:underline">Daftar Event</a>
                    @if(auth()->user()->isAdmin())
                        <a href="/categories" class="hover:underline">Kategori</a>
                    @else
                        <a href="/my-tickets" class="hover:underline">Tiket Saya</a>
                    @endif
                    <span class="bg-indigo-900 px-2.5 py-1 rounded text-xs uppercase font-semibold">
                        {{ auth()->user()->role }}
                    </span>
                    <form action="/logout" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded font-medium">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="/login" class="hover:underline">Login</a>
                    <a href="/register" class="bg-white text-indigo-700 px-3 py-1 rounded font-medium">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="max-w-6xl w-full mx-auto px-4 py-8 flex-grow">
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>