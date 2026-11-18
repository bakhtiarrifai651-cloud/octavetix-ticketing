@extends('layouts.app')

@section('content')
<div class="mb-5">
    <a href="{{ route('events.index') }}" class="text-sm text-zinc-400 hover:text-red-400 transition">&larr; Kembali ke Daftar Gigs</a>
</div>

<div class="bg-zinc-900 border border-zinc-800 border-t-4 border-t-red-600 rounded-xl p-6 mb-6">
    <div class="flex flex-wrap gap-2 mb-3">
        @foreach ($event->categories as $category)
            <span class="bg-red-950/80 text-red-400 border border-red-800/50 text-xs font-semibold uppercase tracking-wider px-2.5 py-0.5 rounded">
                {{ $category->name }}
            </span>
        @endforeach
    </div>

    <h1 class="text-2xl sm:text-3xl font-black uppercase tracking-tight text-white mb-2">{{ $event->title }}</h1>
    <p class="text-zinc-300 text-sm leading-relaxed mb-5">{{ $event->description }}</p>

    <div class="bg-zinc-950 border border-zinc-800 p-4 rounded-lg text-sm text-zinc-300 space-y-1.5">
        <p><strong class="text-zinc-100">Jadwal Pelaksanaan:</strong> {{ \Carbon\Carbon::parse($event->event_date)->format('d F Y, H:i') }} WIB</p>
        <p><strong class="text-zinc-100">Lokasi Venue:</strong> {{ $event->location }}</p>
    </div>
</div>

{{-- Pilihan Tiket --}}
<div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 mb-6">
    <h2 class="text-xl font-bold uppercase tracking-wide text-white mb-4">Pilihan Tiket Tersedia</h2>

    <div class="space-y-4">
        @forelse ($tickets as $ticket)
            <div class="bg-zinc-950 border border-zinc-800 rounded-lg p-4 flex flex-wrap justify-between items-center gap-4">
                <div>
                    <div class="flex items-center gap-2.5">
                        <h3 class="font-bold text-white text-lg">{{ $ticket['name'] }}</h3>
                        @if ($ticket['quota'] > 0 && $ticket['quota'] <= 5)
                            <span class="bg-amber-950 text-amber-400 border border-amber-700/70 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded">
                                Segera Habis!
                            </span>
                        @endif
                    </div>
                    <p class="text-red-400 font-bold mt-0.5">Rp {{ number_format($ticket['price'], 0, ',', '.') }}</p>
                    <p class="text-xs text-zinc-400 mt-1">Sisa Kuota: <span class="font-bold text-zinc-200">{{ $ticket['quota'] }}</span> tiket</p>
                </div>

                @if (auth()->user()->isAdmin())
                    <form action="{{ route('tickets.destroy', $ticket['id']) }}" method="POST" onsubmit="return confirm('Hapus jenis tiket ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-zinc-800 hover:bg-red-900 text-red-400 hover:text-red-200 border border-zinc-700 text-xs font-semibold px-3 py-1.5 rounded-lg transition cursor-pointer">
                            Hapus Tiket
                        </button>
                    </form>
                @else
                    @if ($ticket['quota'] > 0)
                        <form action="{{ route('registrations.store') }}" method="POST" class="flex items-center space-x-2">
                            @csrf
                            <input type="hidden" name="ticket_id" value="{{ $ticket['id'] }}">
                            <input type="number" name="quantity" value="1" min="1" max="{{ $ticket['quota'] }}"
                                class="w-20 bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-lg px-2.5 py-1.5 text-sm focus:outline-none focus:border-red-500">
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-1.5 rounded-lg font-bold uppercase tracking-wider transition cursor-pointer">
                                Pesan Tiket
                            </button>
                        </form>
                    @else
                        <span class="bg-zinc-800 text-zinc-400 border border-zinc-700 text-xs font-semibold uppercase px-3 py-1.5 rounded-lg">Kuota Habis</span>
                    @endif
                @endif
            </div>
        @empty
            <p class="text-zinc-400 text-sm">Belum ada tiket yang dibuat untuk event ini.</p>
        @endforelse
    </div>

    @if (auth()->user()->isAdmin())
        <div class="mt-8 pt-6 border-t border-zinc-800">
            <h3 class="text-lg font-bold text-white mb-3">Tambah Jenis Tiket Baru</h3>

            @if ($errors->hasAny(['name', 'price', 'quota']))
                <div class="mb-4 p-3 rounded-lg bg-red-950/80 border border-red-700 text-red-300 text-xs space-y-1">
                    @foreach ($errors->all() as $err)
                        <p>&bull; {{ $err }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('tickets.store') }}" method="POST" class="grid md:grid-cols-4 gap-3">
                @csrf
                <input type="hidden" name="event_id" value="{{ $event->id }}">
                <input type="text" name="name" placeholder="Nama Tiket (misal: VIP)" class="bg-zinc-950 border border-zinc-700 text-zinc-100 placeholder-zinc-500 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-red-500">
                <input type="number" name="price" placeholder="Harga (Rp)" class="bg-zinc-950 border border-zinc-700 text-zinc-100 placeholder-zinc-500 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-red-500">
                <input type="number" name="quota" placeholder="Kuota" class="bg-zinc-950 border border-zinc-700 text-zinc-100 placeholder-zinc-500 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-red-500">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white rounded-lg px-4 py-2 text-sm font-bold uppercase tracking-wider transition cursor-pointer">
                    + Tambah Tiket
                </button>
            </form>
        </div>
    @endif
</div>

{{-- Gate Check-In & Rekap Pemesan (Khusus Admin) --}}
@if (auth()->user()->isAdmin() && isset($participantReport))
    {{-- Simulasi Gate Check-In --}}
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
            <div>
                <span class="text-[10px] font-black uppercase tracking-widest text-red-500">Gate Security Scanner</span>
                <h2 class="text-lg font-bold uppercase tracking-wide text-white">Validasi & Check-In Kode Tiket</h2>
            </div>
        </div>

        <form action="{{ route('registrations.checkin', $event->id) }}" method="POST" class="flex flex-col sm:flex-row gap-3">
            @csrf
            <input
                type="text"
                name="ticket_code"
                placeholder="Masukkan kode tiket (contoh: OCT-A1B2C3)..."
                required
                class="flex-1 bg-zinc-950 border border-zinc-700 rounded-lg px-4 py-2.5 text-sm font-mono uppercase text-white placeholder-zinc-600 focus:outline-none focus:border-red-600 transition"
            >
            <button type="submit"
                    class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white text-xs font-black uppercase tracking-widest rounded-lg shadow-lg shadow-red-600/20 transition cursor-pointer">
                Validasi Tiket &rarr;
            </button>
        </form>
    </div>

    {{-- Rekap Pemesan Tiket --}}
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-5">
            <div>
                <h2 class="text-xl font-bold uppercase tracking-wide text-white">Rekap Pemesan Tiket</h2>
                <p class="text-xs text-zinc-400 mt-0.5">Data diambil lintas modul melalui RegistrationContract.</p>
            </div>
            <div class="flex gap-3">
                <div class="bg-zinc-950 border border-zinc-800 px-4 py-2 rounded-lg">
                    <p class="text-[11px] uppercase text-zinc-400">Tiket Terjual</p>
                    <p class="text-base font-black text-white">{{ $participantReport['total_tickets'] }} Tiket</p>
                </div>
                <div class="bg-zinc-950 border border-zinc-800 px-4 py-2 rounded-lg">
                    <p class="text-[11px] uppercase text-zinc-400">Total Pendapatan</p>
                    <p class="text-base font-black text-emerald-400">Rp {{ number_format($participantReport['total_revenue'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        @if (empty($participantReport['participants']))
            <p class="text-sm text-zinc-400 bg-zinc-950 border border-zinc-800 rounded-lg p-4 text-center">
                Belum ada peserta yang memesan tiket untuk event ini.
            </p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-zinc-950 text-zinc-400 text-xs uppercase border-b border-zinc-800">
                        <tr>
                            <th class="px-4 py-3">Kode Tiket</th>
                            <th class="px-4 py-3">Peserta</th>
                            <th class="px-4 py-3">Jenis Tiket</th>
                            <th class="px-4 py-3">Qty</th>
                            <th class="px-4 py-3">Total</th>
                            <th class="px-4 py-3">Status Gate</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800 text-zinc-200">
                        @foreach ($participantReport['participants'] as $row)
                            <tr>
                                <td class="px-4 py-3 font-mono text-xs text-red-400 font-bold">{{ $row['ticket_code'] }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-white">{{ $row['user_name'] }}</p>
                                    <p class="text-xs text-zinc-400">{{ $row['user_email'] }}</p>
                                </td>
                                <td class="px-4 py-3">{{ $row['ticket_name'] }}</td>
                                <td class="px-4 py-3">{{ $row['quantity'] }}x</td>
                                <td class="px-4 py-3 font-semibold text-white">Rp {{ number_format($row['total_price'], 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-xs">
                                    @if($row['checked_in_at'])
                                        <span class="px-2.5 py-1 rounded bg-emerald-950 text-emerald-400 border border-emerald-700/70 font-bold uppercase text-[10px]">
                                            Checked-In ({{ $row['checked_in_at'] }})
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded bg-zinc-950 text-zinc-400 border border-zinc-700 font-bold uppercase text-[10px]">
                                            Ready
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endif
@endsection