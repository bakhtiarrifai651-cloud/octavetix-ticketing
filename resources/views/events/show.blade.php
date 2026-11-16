@extends('layouts.app')

@section('content')
<div class="mb-4">
    <a href="{{ route('events.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Kembali ke Daftar Event</a>
</div>

<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="flex flex-wrap gap-2 mb-3">
        @foreach($event->categories as $category)
            <span class="bg-indigo-100 text-indigo-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                {{ $category->name }}
            </span>
        @endforeach
    </div>

    <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ $event->title }}</h1>
    <p class="text-gray-600 mb-4">{{ $event->description }}</p>

    <div class="bg-gray-50 p-4 rounded border text-sm text-gray-700 space-y-1">
        <p><strong>Jadwal Pelaksanaan:</strong> {{ \Carbon\Carbon::parse($event->event_date)->format('d F Y, H:i') }} WIB</p>
        <p><strong>Lokasi:</strong> {{ $event->location }}</p>
    </div>
</div>

{{-- Daftar Tiket (Diambil dari TicketModule via TicketContract) --}}
<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Pilihan Tiket Tersedia</h2>

    <div class="space-y-4">
        @forelse($tickets as $ticket)
            <div class="border rounded-lg p-4 flex flex-wrap justify-between items-center gap-4">
                <div>
                    <h3 class="font-bold text-gray-800">{{ $ticket['name'] }}</h3>
                    <p class="text-indigo-600 font-semibold">Rp {{ number_format($ticket['price'], 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500">Sisa Kuota: <span class="font-bold">{{ $ticket['quota'] }}</span> tiket</p>
                </div>

                @if(auth()->user()->isAdmin())
                    <form action="{{ route('tickets.destroy', $ticket['id']) }}" method="POST" onsubmit="return confirm('Hapus jenis tiket ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1.5 rounded">
                            Hapus Tiket
                        </button>
                    </form>
                @else
                    @if($ticket['quota'] > 0)
                        <form action="/registrations" method="POST" class="flex items-center space-x-2">
                            @csrf
                            <input type="hidden" name="ticket_id" value="{{ $ticket['id'] }}">
                            <input type="number" name="quantity" value="1" min="1" max="{{ $ticket['quota'] }}"
                                class="w-20 border border-gray-300 rounded px-2 py-1 text-sm">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-1.5 rounded font-medium">
                                Pesan Tiket
                            </button>
                        </form>
                    @else
                        <span class="bg-red-100 text-red-700 text-xs font-semibold px-3 py-1 rounded">Kuota Habis</span>
                    @endif
                @endif
            </div>
        @empty
            <p class="text-gray-500 text-sm">Belum ada tiket yang dibuat untuk event ini.</p>
        @endforelse
    </div>

    @if(auth()->user()->isAdmin())
        <div class="mt-8 pt-6 border-t">
            <h3 class="text-lg font-bold text-gray-800 mb-3">Tambah Jenis Tiket Baru</h3>
            <form action="{{ route('tickets.store') }}" method="POST" class="grid md:grid-cols-4 gap-3">
                @csrf
                <input type="hidden" name="event_id" value="{{ $event->id }}">
                <input type="text" name="name" placeholder="Nama Tiket (misal: VIP)" class="border rounded px-3 py-2 text-sm">
                <input type="number" name="price" placeholder="Harga (Rp)" class="border rounded px-3 py-2 text-sm">
                <input type="number" name="quota" placeholder="Kuota" class="border rounded px-3 py-2 text-sm">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded px-4 py-2 text-sm font-semibold">
                    + Tambah Tiket
                </button>
            </form>
        </div>
    @endif
</div>
@endsection