@extends('layouts.app')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-zinc-900 border border-zinc-800 border-t-4 border-t-red-600 rounded-xl p-8 shadow-2xl shadow-red-950/20">
        {{-- Header --}}
        <div class="mb-7">
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded bg-red-950/80 border border-red-800/60 text-red-400 text-[10px] font-bold uppercase tracking-widest mb-3">
                <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                <span>Gate Access</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black uppercase tracking-tight text-white">
                Masuk <span class="text-red-500">OctaveTix</span>
            </h1>
            <p class="text-zinc-400 text-xs mt-1">
                Otentikasi akun untuk mengelola jadwal gigs atau mengamankan tiket konser.
            </p>
        </div>

        {{-- Alert Error --}}
        @if ($errors->any())
            <div class="mb-5 p-3.5 rounded-lg bg-red-950/80 border border-red-700 text-red-200 text-xs font-medium">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Form Login --}}
        <form action="{{ route('login') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-2">
                    Alamat Email
                </label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="nama@octavetix.test"
                    required
                    autofocus
                    class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-4 py-3 text-sm text-white placeholder-zinc-600 focus:outline-none focus:border-red-600 focus:ring-1 focus:ring-red-600 transition"
                >
            </div>

            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-2">
                    Password
                </label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    placeholder="••••••••"
                    required
                    class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-4 py-3 text-sm text-white placeholder-zinc-600 focus:outline-none focus:border-red-600 focus:ring-1 focus:ring-red-600 transition"
                >
            </div>

            <button
                type="submit"
                class="w-full py-3.5 px-4 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white text-sm font-black uppercase tracking-widest rounded-lg shadow-lg shadow-red-600/25 border border-red-500/50 transition cursor-pointer"
            >
                Masuk Arena &rarr;
            </button>
        </form>

        {{-- Info Akun Uji Coba --}}
        <div class="mt-7 pt-5 border-t border-zinc-800">
            <div class="bg-zinc-950 border border-zinc-800/90 rounded-lg p-3.5 text-xs space-y-1.5 font-mono">
                <p class="text-[11px] font-sans font-bold uppercase tracking-wider text-zinc-400 mb-1">
                    Akun Uji Coba (Seeder):
                </p>
                <p class="text-zinc-300">
                    <span class="text-red-400 font-bold">ADMIN&nbsp;&nbsp;:</span> admin@octavetix.test
                </p>
                <p class="text-zinc-300">
                    <span class="text-red-400 font-bold">PESERTA:</span> peserta@octavetix.test
                </p>
                <p class="text-zinc-500 pt-1">
                    Password: <span class="text-zinc-300">password</span>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection