@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Header Halaman --}}
    <div class="mb-7">
        <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded bg-red-950/80 border border-red-800/60 text-red-400 text-[10px] font-bold uppercase tracking-widest mb-2.5">
            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
            <span>Genre & Sub-Scene</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black uppercase tracking-tight text-white">
            Kelola <span class="text-red-500">Kategori Event</span>
        </h1>
        <p class="text-zinc-400 text-xs mt-1">
            Atur daftar aliran musik untuk pengelompokan konser dan filter pencarian gigs.
        </p>
    </div>

    {{-- Alert Validasi --}}
    @if ($errors->any())
        <div class="mb-6 p-4 rounded-lg bg-red-950/80 border border-red-700 text-red-200 text-xs space-y-1">
            @foreach ($errors->all() as $error)
                <p>&bull; {{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- Form Tambah Kategori --}}
    <div class="bg-zinc-900 border border-zinc-800 border-t-4 border-t-red-600 rounded-xl p-5 sm:p-6 mb-6 shadow-xl shadow-red-950/10">
        <form action="{{ route('categories.store') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
            @csrf
            <input
                type="text"
                name="name"
                value="{{ old('name') }}"
                placeholder="Nama genre baru (misal: Black Metal, Slamming Brutal, Punk Rock)..."
                required
                class="flex-1 bg-zinc-950 border border-zinc-700 rounded-lg px-4 py-3 text-sm text-white placeholder-zinc-600 focus:outline-none focus:border-red-600 focus:ring-1 focus:ring-red-600 transition"
            >
            <button
                type="submit"
                class="px-6 py-3 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white text-xs font-black uppercase tracking-widest rounded-lg shadow-lg shadow-red-600/25 border border-red-500/50 transition cursor-pointer whitespace-nowrap"
            >
                + Tambah Genre
            </button>
        </form>
    </div>

    {{-- Tabel Daftar Kategori --}}
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-zinc-950 text-zinc-400 text-[11px] font-bold uppercase tracking-wider border-b border-zinc-800">
                    <tr>
                        <th class="px-6 py-4">Nama Kategori</th>
                        <th class="px-6 py-4">Slug</th>
                        <th class="px-6 py-4">Jumlah Event</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/80">
                    @forelse ($categories as $category)
                        <tr class="hover:bg-zinc-800/40 transition">
                            <td class="px-6 py-4 font-bold text-white">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-1.5 h-1.5 bg-red-600 rotate-45"></span>
                                    <span>{{ $category->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-zinc-400">
                                {{ $category->slug }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded bg-zinc-950 border border-zinc-800 text-xs font-semibold text-zinc-300">
                                    {{ $category->events_count ?? $category->events()->count() }} event
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Hapus kategori {{ $category->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="px-3 py-1.5 bg-zinc-950 hover:bg-red-950 text-red-400 hover:text-red-300 border border-zinc-800 hover:border-red-700/70 rounded-lg text-xs font-bold uppercase tracking-wider transition cursor-pointer"
                                    >
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-sm text-zinc-500">
                                Belum ada kategori genre yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection