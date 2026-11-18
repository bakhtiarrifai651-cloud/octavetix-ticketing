@extends('layouts.app')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-black tracking-tight uppercase text-white">Jadwal Konser & Gigs</h1>
        <p class="text-zinc-400 text-sm mt-1">Amankan akses masuk pertunjukan musik cadas sebelum kuota habis.</p>
    </div>

    @if(auth()->user()->role === 'admin')
        <a href="{{ route('events.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold uppercase tracking-wider rounded-lg transition">
            + Tambah Event Baru
        </a>
    @endif
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4">
        <p class="text-xs uppercase tracking-wider text-zinc-400">Total Pertunjukan</p>
        <p class="text-2xl font-black text-white mt-1">{{ $events->count() }} Event</p>
    </div>
    <form action="{{ route('events.index') }}" method="GET" class="md:col-span-2 bg-zinc-900 border border-zinc-800 rounded-xl p-4 flex flex-col sm:flex-row gap-3">
        <input
            type="text"
            name="search"
            value="{{ $search ?? '' }}"
            placeholder="Cari nama konser atau lokasi venue..."
            class="flex-1 bg-zinc-950 border border-zinc-700 rounded-lg px-4 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-red-500"
        >
        <div class="flex gap-2">
            <button type="submit" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition">
                Cari
            </button>
            @if(!empty($search))
                <a href="{{ route('events.index') }}" class="px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-sm rounded-lg transition">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

@if($events->isEmpty())
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-10 text-center text-zinc-400">
        Tidak ada jadwal konser yang ditemukan.
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($events as $event)
            @php
                $rawDate = $event->event_date ?? $event->date ?? null;
                $formattedDate = $rawDate ? \Carbon\Carbon::parse($rawDate)->format('d M Y • H:i') . ' WIB' : '-';
            @endphp
            <div class="bg-zinc-900 border border-zinc-800 hover:border-red-600/70 rounded-xl p-6 flex flex-col justify-between transition">
                <div>
                    <div class="flex flex-wrap gap-1.5 mb-3">
                        @foreach($event->categories as $category)
                            <span class="text-[11px] font-semibold uppercase tracking-wider px-2.5 py-0.5 rounded bg-red-950/80 text-red-400 border border-red-800/50">
                                {{ $category->name }}
                            </span>
                        @endforeach
                    </div>

                    <h2 class="text-xl font-bold text-white leading-snug">{{ $event->title }}</h2>
                    <p class="text-xs text-zinc-400 mt-2">{{ $formattedDate }}</p>
                    <p class="text-xs text-zinc-400 mt-0.5">{{ $event->location }}</p>
                    <p class="text-sm text-zinc-300 mt-4 line-clamp-3">{{ $event->description }}</p>
                </div>

                <div class="mt-6 pt-4 border-t border-zinc-800 flex items-center justify-between gap-2">
                    <a href="{{ route('events.show', $event->id) }}" class="w-full text-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold uppercase tracking-wider rounded-lg transition">
                        Lihat Tiket & Detail
                    </a>

                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('events.edit', $event->id) }}" class="px-3 py-2 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 text-xs font-semibold rounded-lg transition">
                            Edit
                        </a>
                        <form action="{{ route('events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Hapus event ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-2 bg-zinc-800 hover:bg-red-900 text-red-400 text-xs font-semibold rounded-lg transition">
                                Hapus
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection