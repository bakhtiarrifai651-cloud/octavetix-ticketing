@extends('layouts.app')

@section('content')
@php
    $allCategories = $categories ?? \App\Modules\EventModule\Models\Category::orderBy('name')->get();
@endphp

<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded bg-red-950/80 border border-red-800/60 text-red-400 text-[10px] font-bold uppercase tracking-widest mb-2">
                <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                <span>Live Concerts & Underground Gigs</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black uppercase tracking-tight text-white">
                Daftar <span class="text-red-500">Gigs & Konser</span>
            </h1>
        </div>

        @if (auth()->user()->isAdmin())
            <a href="{{ route('events.create') }}"
               class="inline-flex items-center justify-center px-5 py-3 bg-red-600 hover:bg-red-700 text-white text-xs font-black uppercase tracking-widest rounded-lg shadow-lg shadow-red-600/25 border border-red-500/50 transition">
                + Rilis Gigs Baru
            </a>
        @endif
    </div>

    {{-- Search & Filter Genre --}}
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4 sm:p-5 space-y-4">
        <form action="{{ route('events.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nama gigs, band, atau lokasi venue..."
                class="flex-1 bg-zinc-950 border border-zinc-700 rounded-lg px-4 py-2.5 text-sm text-white placeholder-zinc-500 focus:outline-none focus:border-red-600 transition"
            >

            <div class="flex gap-2">
                <button type="submit"
                        class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-xs font-black uppercase tracking-wider rounded-lg transition cursor-pointer">
                    Cari
                </button>
                @if(request('search') || request('category'))
                    <a href="{{ route('events.index') }}"
                       class="px-4 py-2.5 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-xs font-bold uppercase tracking-wider rounded-lg border border-zinc-700 transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        {{-- Pill Filter Kategori --}}
        @if($allCategories->isNotEmpty())
            <div class="flex flex-wrap items-center gap-2 pt-1 border-t border-zinc-800/80">
                <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 mr-1">Filter Genre:</span>

                <a href="{{ route('events.index', array_filter(['search' => request('search')])) }}"
                   class="px-3 py-1 rounded-full text-xs font-bold transition {{ !request('category') ? 'bg-red-600 text-white' : 'bg-zinc-950 text-zinc-400 border border-zinc-800 hover:border-zinc-600 hover:text-zinc-200' }}">
                    Semua
                </a>

                @foreach($allCategories as $cat)
                    @php
                        $isActive = request('category') == $cat->slug || request('category') == (string) $cat->id;
                    @endphp
                    <a href="{{ route('events.index', array_filter(['search' => request('search'), 'category' => $cat->slug])) }}"
                       class="px-3 py-1 rounded-full text-xs font-bold transition {{ $isActive ? 'bg-red-600 text-white' : 'bg-zinc-950 text-zinc-400 border border-zinc-800 hover:border-red-600/60 hover:text-zinc-200' }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Grid Kartu Gigs --}}
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($events as $event)
            <div class="bg-zinc-900 border border-zinc-800 hover:border-red-600/70 rounded-xl p-6 flex flex-col justify-between transition shadow-lg">
                <div>
                    <div class="flex flex-wrap gap-1.5 mb-3">
                        @foreach ($event->categories as $category)
                            <span class="bg-red-950/80 text-red-400 border border-red-800/50 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded">
                                {{ $category->name }}
                            </span>
                        @endforeach
                    </div>

                    <h2 class="text-xl font-black uppercase tracking-tight text-white mb-2">
                        {{ $event->title }}
                    </h2>
                    <p class="text-zinc-400 text-xs line-clamp-3 leading-relaxed mb-4">
                        {{ $event->description }}
                    </p>
                </div>

                <div>
                    <div class="bg-zinc-950 border border-zinc-800/90 rounded-lg p-3 text-xs text-zinc-300 space-y-1 mb-4">
                        <p><strong class="text-zinc-100">Jadwal:</strong> {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y, H:i') }} WIB</p>
                        <p><strong class="text-zinc-100">Venue:</strong> {{ $event->location }}</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('events.show', $event->id) }}"
                           class="flex-1 text-center bg-red-600 hover:bg-red-700 text-white text-xs font-black uppercase tracking-wider py-2.5 px-3 rounded-lg transition">
                            {{ auth()->user()->isAdmin() ? 'Kelola Tiket & Gate' : 'Lihat & Pesan Tiket' }}
                        </a>

                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('events.edit', $event->id) }}"
                               class="bg-zinc-800 hover:bg-zinc-700 text-zinc-200 border border-zinc-700 text-xs font-bold py-2.5 px-3 rounded-lg transition">
                                Edit
                            </a>
                            <form action="{{ route('events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Hapus gigs ini beserta semua tiketnya?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="bg-zinc-950 hover:bg-red-950 text-red-400 border border-zinc-800 hover:border-red-700 text-xs font-bold py-2.5 px-3 rounded-lg transition cursor-pointer">
                                    Hapus
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-zinc-900 border border-zinc-800 rounded-xl p-10 text-center">
                <p class="text-zinc-400 text-sm font-medium">Tidak ada jadwal gigs yang sesuai dengan pencarian atau filter genre.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection