@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-5">
        <a href="{{ route('events.index') }}" class="text-sm text-zinc-400 hover:text-red-400 transition">
            &larr; Kembali ke Daftar Gigs
        </a>
    </div>

    <div class="bg-zinc-900 border border-zinc-800 border-t-4 border-t-red-600 rounded-xl p-6 sm:p-8 shadow-2xl shadow-red-950/20">
        {{-- Header --}}
        <div class="mb-7">
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded bg-red-950/80 border border-red-800/60 text-red-400 text-[10px] font-bold uppercase tracking-widest mb-3">
                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                <span>Admin Stage Control</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black uppercase tracking-tight text-white">
                Rilis <span class="text-red-500">Gigs Baru</span>
            </h1>
            <p class="text-zinc-400 text-xs mt-1">
                Publikasikan jadwal konser, detail venue, dan genre musik ke daftar acara OctaveTix.
            </p>
        </div>

        {{-- Alert Validasi Error --}}
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-lg bg-red-950/80 border border-red-700 text-red-200 text-xs space-y-1">
                <p class="font-bold uppercase tracking-wider text-red-400 mb-1">Periksa kembali input berikut:</p>
                @foreach ($errors->all() as $error)
                    <p>&bull; {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('events.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Judul Event --}}
            <div>
                <label for="title" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-2">
                    Judul Event / Konser
                </label>
                <input
                    id="title"
                    type="text"
                    name="title"
                    value="{{ old('title') }}"
                    placeholder="Contoh: Jakarta Hellfest 2026"
                    required
                    class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-4 py-3 text-sm text-white placeholder-zinc-600 focus:outline-none focus:border-red-600 focus:ring-1 focus:ring-red-600 transition"
                >
            </div>

            {{-- Deskripsi --}}
            <div>
                <label for="description" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-2">
                    Deskripsi & Line-Up Band
                </label>
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    placeholder="Tulis informasi rundown, daftar band pengisi acara, atau aturan masuk arena..."
                    required
                    class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-4 py-3 text-sm text-white placeholder-zinc-600 focus:outline-none focus:border-red-600 focus:ring-1 focus:ring-red-600 transition"
                >{{ old('description') }}</textarea>
            </div>

            {{-- Tanggal & Lokasi --}}
            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label for="event_date" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-2">
                        Tanggal & Waktu
                    </label>
                    <input
                        id="event_date"
                        type="datetime-local"
                        name="event_date"
                        value="{{ old('event_date') }}"
                        required
                        class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-4 py-3 text-sm text-white [color-scheme:dark] focus:outline-none focus:border-red-600 focus:ring-1 focus:ring-red-600 transition"
                    >
                </div>

                <div>
                    <label for="location" class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-2">
                        Lokasi Venue
                    </label>
                    <input
                        id="location"
                        type="text"
                        name="location"
                        value="{{ old('location') }}"
                        placeholder="Contoh: Rossi Musik Fatmawati, Jakarta"
                        required
                        class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-4 py-3 text-sm text-white placeholder-zinc-600 focus:outline-none focus:border-red-600 focus:ring-1 focus:ring-red-600 transition"
                    >
                </div>
            </div>

            {{-- Kategori / Genre --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-zinc-300 mb-2.5">
                    Genre / Kategori Event <span class="text-zinc-500 font-normal normal-case">(Bisa pilih lebih dari satu)</span>
                </label>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 bg-zinc-950 border border-zinc-800 p-4 rounded-lg">
                    @forelse ($categories as $category)
                        <label class="flex items-center gap-2.5 p-2.5 rounded-lg bg-zinc-900/90 border border-zinc-800 hover:border-red-600/60 has-[:checked]:border-red-600 has-[:checked]:bg-red-950/40 cursor-pointer transition">
                            <input
                                type="checkbox"
                                name="categories[]"
                                value="{{ $category->id }}"
                                {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}
                                class="w-4 h-4 rounded bg-zinc-950 border-zinc-700 accent-red-600"
                            >
                            <span class="text-xs font-semibold text-zinc-200 select-none">
                                {{ $category->name }}
                            </span>
                        </label>
                    @empty
                        <p class="col-span-full text-xs text-zinc-500">
                            Belum ada genre tersedia. Tambahkan terlebih dahulu di menu Genre / Kategori.
                        </p>
                    @endforelse
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="pt-4 border-t border-zinc-800 flex flex-col sm:flex-row items-center justify-end gap-3">
                <a href="{{ route('events.index') }}" class="w-full sm:w-auto text-center px-5 py-3 text-xs font-bold uppercase tracking-wider bg-zinc-800 hover:bg-zinc-700 text-zinc-300 rounded-lg border border-zinc-700 transition">
                    Batal
                </a>
                <button
                    type="submit"
                    class="w-full sm:w-auto px-6 py-3 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white text-xs font-black uppercase tracking-widest rounded-lg shadow-lg shadow-red-600/25 border border-red-500/50 transition cursor-pointer"
                >
                    Terbitkan Gigs &rarr;
                </button>
            </div>
        </form>
    </div>
</div>
@endsection