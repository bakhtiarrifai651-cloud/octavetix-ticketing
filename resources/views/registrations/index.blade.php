@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Tiket Saya</h1>
        <p class="text-sm text-gray-600">Daftar riwayat pemesanan tiket event Anda.</p>
    </div>
    <a href="{{ route('events.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-medium">
        + Cari Event Lain
    </a>
</div>

<div class="space-y-4">
    @forelse($registrations as $reg)
        <div class="bg-white rounded-lg shadow p-6 flex flex-wrap justify-between items-center gap-4 border-l-4 border-indigo-600">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded uppercase">
                        {{ $reg->status ?? 'Confirmed' }}
                    </span>
                    @if($reg->ticket_code ?? $reg->booking_code)
                        <span class="text-xs font-mono text-gray-500">Kode: {{ $reg->ticket_code ?? $reg->booking_code }}</span>
                    @endif
                </div>

                <h2 class="text-lg font-bold text-gray-800">
                    {{ $reg->ticket->event->title ?? 'Event' }}
                </h2>
                <p class="text-sm text-gray-600">
                    Jenis Tiket: <span class="font-semibold">{{ $reg->ticket->name ?? '-' }}</span>
                    &bull; Jumlah: <span class="font-semibold">{{ $reg->quantity }} tiket</span>
                </p>
                @if($reg->ticket?->event)
                    <p class="text-xs text-gray-500 mt-1">
                        Jadwal: {{ \Carbon\Carbon::parse($reg->ticket->event->event_date)->format('d M Y, H:i') }} WIB
                        &bull; Lokasi: {{ $reg->ticket->event->location }}
                    </p>
                @endif
            </div>

            <div class="text-right">
                <p class="text-xs text-gray-500">Total Pembayaran</p>
                <p class="text-xl font-bold text-indigo-700">
                    Rp {{ number_format($reg->total_price, 0, ',', '.') }}
                </p>
                <p class="text-xs text-gray-400 mt-1">
                    Dipesan: {{ $reg->created_at->format('d M Y, H:i') }}
                </p>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-500 mb-4">Anda belum memiliki tiket event apa pun.</p>
            <a href="{{ route('events.index') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-medium">
                Jelajahi Event Sekarang
            </a>
        </div>
    @endforelse
</div>
@endsection