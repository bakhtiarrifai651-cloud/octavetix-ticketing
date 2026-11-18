@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-black uppercase tracking-tight text-white">Tiket Saya</h1>
    <p class="text-zinc-400 text-sm mt-1">Tunjukkan kode tiket unik di gerbang masuk venue.</p>
</div>

@if($registrations->isEmpty())
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-10 text-center">
        <p class="text-zinc-400 mb-4">Kamu belum memiliki tiket konser aktif.</p>
        <a href="{{ route('events.index') }}" class="inline-block px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold uppercase rounded-lg transition">
            Jelajahi Konser
        </a>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($registrations as $registration)
            @php
                $code = $registration->ticket_code ?? $registration->booking_code ?? '-';
                $isCancelled = ($registration->status ?? 'confirmed') === 'cancelled';
            @endphp
            <div class="bg-zinc-900 border {{ $isCancelled ? 'border-zinc-800 opacity-60' : 'border-red-800/60' }} rounded-xl p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="text-xs font-mono font-bold px-3 py-1 rounded bg-zinc-950 border border-zinc-700 text-red-400">
                            {{ $code }}
                        </span>
                        <span class="text-xs uppercase font-bold px-2.5 py-0.5 rounded {{ $isCancelled ? 'bg-zinc-800 text-zinc-400' : 'bg-emerald-950 text-emerald-400 border border-emerald-800' }}">
                            {{ $isCancelled ? 'Dibatalkan' : 'Aktif' }}
                        </span>
                    </div>

                    <h2 class="text-xl font-bold text-white">{{ $registration->ticket->event->title ?? 'Event' }}</h2>
                    <p class="text-sm text-red-400 font-semibold mt-1">{{ $registration->ticket->name ?? 'Tiket' }} ({{ $registration->quantity }}x)</p>
                    <p class="text-xs text-zinc-400 mt-1">{{ $registration->ticket->event->location ?? '-' }}</p>

                    <div class="mt-4 pt-4 border-t border-zinc-800 flex justify-between text-sm">
                        <span class="text-zinc-400">Total Pembayaran</span>
                        <span class="font-bold text-white">Rp {{ number_format($registration->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>

                @if(! $isCancelled)
                    <form action="{{ route('registrations.destroy', $registration->id) }}" method="POST" class="mt-5" onsubmit="return confirm('Batalkan pesanan tiket ini? Kuota tiket akan dikembalikan.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-2 px-4 bg-zinc-800 hover:bg-red-900/80 text-red-400 hover:text-red-200 border border-zinc-700 hover:border-red-700 text-xs font-bold uppercase tracking-wider rounded-lg transition">
                            Batalkan Pesanan
                        </button>
                    </form>
                @endif
            </div>
        @endforeach
    </div>
@endif
@endsection