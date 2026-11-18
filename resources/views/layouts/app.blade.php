<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OctaveTix — Metal & Underground Gig Ticketing</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-zinc-950 text-zinc-100 min-h-screen flex flex-col antialiased selection:bg-red-600 selection:text-white">
    <nav class="bg-zinc-900 border-b border-zinc-800 sticky top-0 z-30">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center space-x-8">
                    <a href="{{ route('events.index') }}" class="flex items-center gap-2 text-xl font-black tracking-wider uppercase text-white">
                        <span class="inline-block w-3 h-3 bg-red-600 rotate-45"></span>
                        <span>Octave<span class="text-red-500">Tix</span></span>
                    </a>

                    @auth
                        <div class="hidden sm:flex items-center space-x-5 text-sm font-medium">
                            <a href="{{ route('events.index') }}" class="text-zinc-300 hover:text-red-400 transition">Daftar Gigs</a>
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('categories.index') }}" class="text-zinc-300 hover:text-red-400 transition">Genre / Kategori</a>
                            @endif
                            @if(auth()->user()->role === 'peserta')
                                <a href="{{ route('registrations.index') }}" class="text-zinc-300 hover:text-red-400 transition">Tiket Saya</a>
                            @endif
                        </div>
                    @endauth
                </div>

                <div class="flex items-center space-x-4">
                    @auth
                        <div class="text-right hidden sm:block">
                            <p class="text-xs font-semibold text-zinc-200">{{ auth()->user()->name }}</p>
                            <span class="text-[10px] uppercase tracking-widest px-2 py-0.5 rounded bg-red-950 text-red-400 border border-red-800/60">
                                {{ auth()->user()->role }}
                            </span>
                        </div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 text-xs font-semibold uppercase tracking-wider bg-zinc-800 hover:bg-red-600 text-zinc-200 hover:text-white rounded border border-zinc-700 transition">
                                Keluar
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-zinc-300 hover:text-white">Masuk</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-semibold bg-red-600 hover:bg-red-700 text-white rounded transition">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-1 max-w-6xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-6 p-4 rounded-lg bg-emerald-950/70 border border-emerald-700 text-emerald-300 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-lg bg-red-950/80 border border-red-600 text-red-200 text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="border-t border-zinc-900 py-6 text-center text-xs text-zinc-500">
        OctaveTix &copy; {{ date('Y') }} — Independent Music & Metal Concert Ticketing System
    </footer>
</body>
</html>