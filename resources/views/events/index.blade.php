@extends('layouts.app')

@section('content')
<div class="flex flex-wrap justify-between items-center gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Daftar Event Kampus</h1>
        <p class="text-sm text-gray-600">Pilih event yang tersedia untuk melihat detail dan memesan tiket.</p>
    </div>

    @if(auth()->user()->isAdmin())
        <a href="{{ route('events.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded font-medium text-sm">
            + Tambah Event Baru
        </a>
    @endif
</div>

<div class="flex flex-wrap gap-2 mb-6">
    <a href="{{ route('events.index') }}"
       class="px-3 py-1 rounded-full text-xs font-semibold {{ !request('category') ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border' }}">
        Semua Kategori
    </a>
    @foreach($categories as $cat)
        <a href="{{ route('events.index', ['category' => $cat->slug]) }}"
           class="px-3 py-1 rounded-full text-xs font-semibold {{ request('category') === $cat->slug ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border' }}">
            {{ $cat->name }}
        </a>
    @endforeach
</div>

<div class="grid md:grid-cols-2 gap-6">
    @forelse($events as $event)
        <div class="bg-white rounded-lg shadow p-6 flex flex-col justify-between">
            <div>
                <div class="flex flex-wrap gap-1.5 mb-3">
                    @foreach($event->categories as $category)
                        <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded">
                            {{ $category->name }}
                        </span>
                    @endforeach
                </div>

                <h2 class="text-xl font-bold text-gray-800 mb-2">{{ $event->title }}</h2>
                <p class="text-gray-600 text-sm mb-4">{{ $event->description }}</p>

                <div class="text-xs text-gray-500 space-y-1 mb-4">
                    <p><strong>Waktu:</strong> {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y, H:i') }} WIB</p>
                    <p><strong>Lokasi:</strong> {{ $event->location }}</p>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t">
                <a href="{{ route('events.show', $event->id) }}" class="text-indigo-600 hover:underline font-semibold text-sm">
                    Lihat Detail & Tiket &rarr;
                </a>

                @if(auth()->user()->isAdmin())
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('events.edit', $event->id) }}" class="text-xs bg-amber-500 hover:bg-amber-600 text-white px-2.5 py-1 rounded">
                            Edit
                        </a>
                        <form action="{{ route('events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus event ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs bg-red-500 hover:bg-red-600 text-white px-2.5 py-1 rounded">
                                Hapus
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="col-span-2 bg-white p-6 rounded shadow text-center text-gray-500">
            Belum ada event yang tersedia.
        </div>
    @endforelse
</div>
@endsection