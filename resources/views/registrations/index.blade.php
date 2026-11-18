@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded bg-red-950/80 border border-red-800/60 text-red-400 text-[10px] font-bold uppercase tracking-widest mb-2">
            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
            <span>E-Ticket Vault</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black uppercase tracking-tight text-white">
            Tiket <span class="text-red-500">Saya</span>
        </h1>
        <p class="text-zinc-400 text-xs mt-1">
            Tunjukkan QR Code atau kode tiket unik kepada petugas gate saat memasuki area venue.
        </p>
    </div>

    <div class="grid md:grid-cols-2 gap-5">
        @forelse ($registrations as $registration)
            @php
                $event = $registration->ticket->event ?? null;
                $ticket = $registration->ticket ?? null;
                $subtotal = $registration->total_price ?? (($ticket->price ?? 0) * $registration->quantity);
                $isCheckedIn = \Illuminate\Support\Facades\Schema::hasColumn('registrations', 'checked_in_at') && $registration->checked_in_at;
            @endphp

            <div class="bg-zinc-900 border border-zinc-800 border-l-4 {{ $isCheckedIn ? 'border-l-emerald-500' : 'border-l-red-600' }} rounded-xl p-6 flex flex-col justify-between shadow-lg">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="font-mono text-xs font-black px-2.5 py-1 rounded bg-zinc-950 border border-zinc-800 text-red-400">
                            {{ $registration->ticket_code }}
                        </span>

                        @if($isCheckedIn)
                            <span class="text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded bg-emerald-950 text-emerald-400 border border-emerald-700">
                                Sudah Check-In
                            </span>
                        @else
                            <span class="text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded bg-red-950/70 text-red-300 border border-red-800/60">
                                Valid Gate Pass
                            </span>
                        @endif
                    </div>

                    <h2 class="text-xl font-black uppercase text-white mb-1">
                        {{ $event->title ?? 'Event Konser' }}
                    </h2>
                    <p class="text-xs text-zinc-400 mb-4">
                        {{ $event ? \Carbon\Carbon::parse($event->event_date)->format('d F Y, H:i') . ' WIB' : '-' }} &bull; {{ $event->location ?? '-' }}
                    </p>

                    <div class="bg-zinc-950 border border-zinc-800 rounded-lg p-3.5 text-xs space-y-1.5 mb-5">
                        <div class="flex justify-between">
                            <span class="text-zinc-400">Kategori Tiket:</span>
                            <span class="font-bold text-white">{{ $ticket->name ?? '-' }} ({{ $registration->quantity }}x)</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-400">Total Pembayaran:</span>
                            <span class="font-bold text-emerald-400">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2.5">
                    <button
                        type="button"
                        onclick="openTicketModal({
                            code: '{{ $registration->ticket_code }}',
                            event: '{{ addslashes($event->title ?? 'OctaveTix Live') }}',
                            date: '{{ $event ? \Carbon\Carbon::parse($event->event_date)->format('d F Y, H:i') . ' WIB' : '-' }}',
                            venue: '{{ addslashes($event->location ?? '-') }}',
                            tier: '{{ addslashes($ticket->name ?? '-') }} ({{ $registration->quantity }}x)',
                            holder: '{{ addslashes(auth()->user()->name) }}',
                            status: '{{ $isCheckedIn ? 'CHECKED-IN' : 'VALID PASS' }}'
                        })"
                        class="flex-1 py-2.5 px-4 bg-red-600 hover:bg-red-700 text-white text-xs font-black uppercase tracking-wider rounded-lg transition cursor-pointer"
                    >
                        Lihat E-Ticket & QR
                    </button>

                    @if(!$isCheckedIn)
                        <form action="{{ route('registrations.destroy', $registration->id) }}" method="POST" onsubmit="return confirm('Batalkan pesanan tiket ini? Kuota akan dikembalikan.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="py-2.5 px-3.5 bg-zinc-950 hover:bg-red-950 text-zinc-400 hover:text-red-300 border border-zinc-800 hover:border-red-700 text-xs font-bold uppercase tracking-wider rounded-lg transition cursor-pointer">
                                Batalkan
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-zinc-900 border border-zinc-800 rounded-xl p-10 text-center space-y-3">
                <p class="text-zinc-400 text-sm">Kamu belum memiliki tiket gigs aktif.</p>
                <a href="{{ route('events.index') }}" class="inline-block px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-xs font-black uppercase tracking-wider rounded-lg transition">
                    Jelajahi Gigs &rarr;
                </a>
            </div>
        @endforelse
    </div>
</div>

{{-- Modal Boarding Pass / E-Ticket --}}
<div id="ticketModal" class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div id="printablePass" class="w-full max-w-md bg-zinc-900 border border-zinc-700 border-t-4 border-t-red-600 rounded-2xl overflow-hidden shadow-2xl">
        <div class="p-6 bg-zinc-950 border-b border-dashed border-zinc-700 flex items-center justify-between">
            <div class="flex items-center gap-2 text-lg font-black uppercase tracking-wider text-white">
                <span class="inline-block w-2.5 h-2.5 bg-red-600 rotate-45"></span>
                <span>Octave<span class="text-red-500">Tix</span></span>
            </div>
            <span id="modalStatus" class="text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded bg-red-950 text-red-400 border border-red-800">
                VALID PASS
            </span>
        </div>

        <div class="p-6 space-y-5">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Official Gate Pass</p>
                <h3 id="modalEvent" class="text-xl font-black uppercase text-white mt-0.5">-</h3>
            </div>

            <div class="grid grid-cols-2 gap-3 text-xs bg-zinc-950 border border-zinc-800 p-4 rounded-xl">
                <div>
                    <p class="text-zinc-500 text-[10px] uppercase">Pemegang Tiket</p>
                    <p id="modalHolder" class="font-bold text-zinc-200 mt-0.5">-</p>
                </div>
                <div>
                    <p class="text-zinc-500 text-[10px] uppercase">Kategori & Qty</p>
                    <p id="modalTier" class="font-bold text-red-400 mt-0.5">-</p>
                </div>
                <div class="col-span-2">
                    <p class="text-zinc-500 text-[10px] uppercase">Jadwal & Lokasi Venue</p>
                    <p id="modalDate" class="font-semibold text-zinc-200 mt-0.5">-</p>
                    <p id="modalVenue" class="text-zinc-400 mt-0.5">-</p>
                </div>
            </div>

            {{-- QR Code Container --}}
            <div class="flex flex-col items-center justify-center bg-white p-4 rounded-xl">
                <div id="qrcodeContainer"></div>
                <p id="modalCode" class="font-mono text-sm font-black tracking-widest text-zinc-950 mt-2">OCT-XXXXXX</p>
            </div>
        </div>

        <div class="px-6 py-4 bg-zinc-950 border-t border-zinc-800 flex items-center justify-between gap-3 print:hidden">
            <button type="button" onclick="closeTicketModal()"
                    class="px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-xs font-bold uppercase tracking-wider rounded-lg transition cursor-pointer">
                Tutup
            </button>
            <button type="button" onclick="window.print()"
                    class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-black uppercase tracking-wider rounded-lg transition cursor-pointer">
                Cetak / Save PDF
            </button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    function openTicketModal(data) {
        document.getElementById('modalEvent').textContent = data.event;
        document.getElementById('modalHolder').textContent = data.holder;
        document.getElementById('modalTier').textContent = data.tier;
        document.getElementById('modalDate').textContent = data.date;
        document.getElementById('modalVenue').textContent = data.venue;
        document.getElementById('modalCode').textContent = data.code;
        document.getElementById('modalStatus').textContent = data.status;

        const qrContainer = document.getElementById('qrcodeContainer');
        qrContainer.innerHTML = '';
        new QRCode(qrContainer, {
            text: 'OCTAVETIX|' + data.code,
            width: 140,
            height: 140,
            colorDark: '#09090b',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });

        document.getElementById('ticketModal').classList.remove('hidden');
    }

    function closeTicketModal() {
        document.getElementById('ticketModal').classList.add('hidden');
    }
</script>
@endsection