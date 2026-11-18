@extends('layouts.app')

@section('content')
@php
    $totalEvents = \Illuminate\Support\Facades\Schema::hasTable('events')
        ? \Illuminate\Support\Facades\DB::table('events')->count()
        : 0;

    $totalCategories = \Illuminate\Support\Facades\Schema::hasTable('categories')
        ? \Illuminate\Support\Facades\DB::table('categories')->count()
        : 0;

    $totalTicketsSold = \Illuminate\Support\Facades\Schema::hasTable('registrations')
        ? (int) \Illuminate\Support\Facades\DB::table('registrations')->sum('quantity')
        : 0;

    $myTicketsCount = \Illuminate\Support\Facades\Schema::hasTable('registrations')
        ? (int) \Illuminate\Support\Facades\DB::table('registrations')->where('user_id', auth()->id())->sum('quantity')
        : 0;
@endphp

<div class="space-y-6">
    {{-- Hero Banner / Command Center --}}
    <div class="bg-zinc-900 border border-zinc-800 border-t-4 border-t-red-600 rounded-xl p-6 sm:p-8 shadow-2xl shadow-red-950/20 relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded bg-red-950/80 border border-red-800/60 text-red-400 text-[10px] font-bold uppercase tracking-widest mb-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                    <span>{{ auth()->user()->role === 'admin' ? 'Stage Command Center' : 'Arena Access Pass' }}</span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-black uppercase tracking-tight text-white">
                    Selamat Datang, <span class="text-red-500">{{ auth()->user()->name }}</span>
                </h1>

                <p class="text-zinc-400 text-sm mt-2 max-w-2xl leading-relaxed">
                    @if(auth()->user()->role === 'admin')
                        Panel kendali utama <strong class="text-zinc-200">OctaveTix</strong>. Kelola jadwal gigs, atur kategori genre musik, pantau kuota tiket, dan cek rekap transaksi peserta secara terpusat.
                    @else
                        Portal akses tiket <strong class="text-zinc-200">OctaveTix</strong>. Jelajahi jadwal gigs terbaru, amankan tiket konser sebelum kuota habis, dan kelola kode e-ticket aktif milikmu.
                    @endif
                </p>
            </div>

            <div class="flex flex-wrap gap-3 shrink-0">
                <a href="{{ route('events.index') }}"
                   class="px-5 py-3 bg-red-600 hover:bg-red-700 text-white text-xs font-black uppercase tracking-widest rounded-lg shadow-lg shadow-red-600/25 border border-red-500/50 transition">
                    Lihat Daftar Gigs &rarr;
                </a>

                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('events.create') }}"
                       class="px-5 py-3 bg-zinc-950 hover:bg-zinc-800 text-zinc-200 text-xs font-bold uppercase tracking-wider rounded-lg border border-zinc-700 transition">
                        + Rilis Gigs Baru
                    </a>
                @else
                    <a href="{{ route('registrations.index') }}"
                       class="px-5 py-3 bg-zinc-950 hover:bg-zinc-800 text-zinc-200 text-xs font-bold uppercase tracking-wider rounded-lg border border-zinc-700 transition">
                        Tiket Saya
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Statistik Ringkas --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-5">
            <p class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Total Gigs Terjadwal</p>
            <div class="flex items-baseline justify-between mt-2">
                <p class="text-3xl font-black text-white">{{ $totalEvents }}</p>
                <span class="text-xs font-semibold text-red-400">Event Aktif</span>
            </div>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-5">
            <p class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Genre / Sub-Scene</p>
            <div class="flex items-baseline justify-between mt-2">
                <p class="text-3xl font-black text-white">{{ $totalCategories }}</p>
                <span class="text-xs font-semibold text-zinc-400">Kategori</span>
            </div>
        </div>

        @if(auth()->user()->role === 'admin')
            <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-5">
                <p class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Total Tiket Terjual</p>
                <div class="flex items-baseline justify-between mt-2">
                    <p class="text-3xl font-black text-emerald-400">{{ $totalTicketsSold }}</p>
                    <span class="text-xs font-semibold text-zinc-400">Tiket Dipesan</span>
                </div>
            </div>
        @else
            <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-5">
                <p class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Tiket Aktif Saya</p>
                <div class="flex items-baseline justify-between mt-2">
                    <p class="text-3xl font-black text-emerald-400">{{ $myTicketsCount }}</p>
                    <span class="text-xs font-semibold text-zinc-400">E-Ticket</span>
                </div>
            </div>
        @endif
    </div>

    {{-- Pintasan Menu Utama --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <a href="{{ route('events.index') }}"
           class="group bg-zinc-900 hover:bg-zinc-900/80 border border-zinc-800 hover:border-red-600/70 rounded-xl p-6 transition">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-black uppercase tracking-widest text-red-500">01 // Line-Up & Venue</span>
                <span class="text-zinc-500 group-hover:text-red-400 group-hover:translate-x-1 transition">&rarr;</span>
            </div>
            <h2 class="text-lg font-bold text-white group-hover:text-red-400 transition">Manajemen & Daftar Gigs</h2>
            <p class="text-xs text-zinc-400 mt-1 leading-relaxed">
                {{ auth()->user()->role === 'admin'
                    ? 'Lihat seluruh konser, atur jenis & kuota tiket per event, serta pantau tabel rekap pemesan.'
                    : 'Cari konser berdasarkan judul acara atau lokasi venue dan amankan kuota tiketmu.' }}
            </p>
        </a>

        @if(auth()->user()->role === 'admin')
            <a href="{{ route('categories.index') }}"
               class="group bg-zinc-900 hover:bg-zinc-900/80 border border-zinc-800 hover:border-red-600/70 rounded-xl p-6 transition">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-black uppercase tracking-widest text-red-500">02 // Taxonomy</span>
                    <span class="text-zinc-500 group-hover:text-red-400 group-hover:translate-x-1 transition">&rarr;</span>
                </div>
                <h2 class="text-lg font-bold text-white group-hover:text-red-400 transition">Kelola Genre / Kategori</h2>
                <p class="text-xs text-zinc-400 mt-1 leading-relaxed">
                    Tambah atau hapus kategori aliran musik untuk mengelompokkan setiap gigs di OctaveTix.
                </p>
            </a>
        @else
            <a href="{{ route('registrations.index') }}"
               class="group bg-zinc-900 hover:bg-zinc-900/80 border border-zinc-800 hover:border-red-600/70 rounded-xl p-6 transition">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-black uppercase tracking-widest text-red-500">02 // E-Ticket Vault</span>
                    <span class="text-zinc-500 group-hover:text-red-400 group-hover:translate-x-1 transition">&rarr;</span>
                </div>
                <h2 class="text-lg font-bold text-white group-hover:text-red-400 transition">Tiket Saya</h2>
                <p class="text-xs text-zinc-400 mt-1 leading-relaxed">
                    Cek riwayat pemesanan, salin kode tiket unik OCT-XXXXXX, atau batalkan pesanan untuk mengembalikan kuota.
                </p>
            </a>
        @endif
    </div>
</div>
@endsection