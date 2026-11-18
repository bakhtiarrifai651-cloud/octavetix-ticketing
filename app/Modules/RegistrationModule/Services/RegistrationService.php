<?php

namespace App\Modules\RegistrationModule\Services;

use App\Modules\RegistrationModule\Contracts\RegistrationContract;
use App\Modules\RegistrationModule\Models\Registration;
use App\Modules\TicketModule\Contracts\TicketContract;
use App\Modules\TicketModule\Exceptions\QuotaFullException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class RegistrationService implements RegistrationContract
{
    public function __construct(
        protected TicketContract $ticketContract
    ) {}

    public function bookTicket(int $userId, int $ticketId, int $quantity): Registration
    {
        return DB::transaction(function () use ($userId, $ticketId, $quantity) {
            $ticket = $this->ticketContract->findTicket($ticketId);

            if (! $ticket) {
                throw new QuotaFullException('Jenis tiket tidak ditemukan.');
            }

            $this->ticketContract->reserveQuota($ticketId, $quantity);

            $data = [
                'user_id' => $userId,
                'ticket_id' => $ticketId,
                'quantity' => $quantity,
                'ticket_code' => 'OCT-'.strtoupper(Str::random(6)),
            ];

            if (Schema::hasColumn('registrations', 'total_price')) {
                $data['total_price'] = $ticket['price'] * $quantity;
            }

            return Registration::create($data);
        });
    }

    public function cancelBooking(int $registrationId, int $userId): void
    {
        DB::transaction(function () use ($registrationId, $userId) {
            $registration = Registration::where('id', $registrationId)
                ->where('user_id', $userId)
                ->firstOrFail();

            if (Schema::hasColumn('registrations', 'checked_in_at') && $registration->checked_in_at) {
                return;
            }

            $this->ticketContract->releaseQuota($registration->ticket_id, $registration->quantity);
            $registration->delete();
        });
    }

    public function getEventParticipants(int $eventId): array
    {
        $tickets = $this->ticketContract->getTicketsByEvent($eventId);
        $ticketMap = collect($tickets)->keyBy('id');

        if ($ticketMap->isEmpty()) {
            return [
                'participants' => [],
                'total_tickets' => 0,
                'total_revenue' => 0,
            ];
        }

        $registrations = Registration::with('user')
            ->whereIn('ticket_id', $ticketMap->keys())
            ->latest()
            ->get();

        $hasCheckedInCol = Schema::hasColumn('registrations', 'checked_in_at');
        $participants = [];
        $totalTickets = 0;
        $totalRevenue = 0;

        foreach ($registrations as $reg) {
            $ticket = $ticketMap->get($reg->ticket_id);
            $price = $ticket['price'] ?? 0;
            $subtotal = $reg->total_price ?? ($price * $reg->quantity);

            $totalTickets += $reg->quantity;
            $totalRevenue += $subtotal;

            $participants[] = [
                'ticket_code' => $reg->ticket_code,
                'user_name' => $reg->user->name ?? 'Peserta',
                'user_email' => $reg->user->email ?? '-',
                'ticket_name' => $ticket['name'] ?? 'Tiket',
                'quantity' => $reg->quantity,
                'total_price' => $subtotal,
                'booked_at' => $reg->created_at?->format('d M Y, H:i') ?? '-',
                'checked_in_at' => ($hasCheckedInCol && $reg->checked_in_at)
                    ? Carbon::parse($reg->checked_in_at)->format('d M Y, H:i')
                    : null,
            ];
        }

        return [
            'participants' => $participants,
            'total_tickets' => $totalTickets,
            'total_revenue' => $totalRevenue,
        ];
    }

    public function checkInTicket(int $eventId, string $ticketCode): array
    {
        $code = strtoupper(trim($ticketCode));
        $tickets = $this->ticketContract->getTicketsByEvent($eventId);
        $ticketMap = collect($tickets)->keyBy('id');

        if ($ticketMap->isEmpty()) {
            return [
                'status' => 'invalid',
                'message' => "Kode tiket {$code} tidak terdaftar pada event ini.",
            ];
        }

        $registration = Registration::with('user')
            ->whereIn('ticket_id', $ticketMap->keys())
            ->where('ticket_code', $code)
            ->first();

        if (! $registration) {
            return [
                'status' => 'invalid',
                'message' => "TIDAK VALID: Kode tiket {$code} tidak ditemukan untuk konser ini.",
            ];
        }

        $hasCheckedInCol = Schema::hasColumn('registrations', 'checked_in_at');

        if ($hasCheckedInCol && $registration->checked_in_at) {
            $time = Carbon::parse($registration->checked_in_at)->format('d M Y, H:i');

            return [
                'status' => 'used',
                'message' => "DITOLAK: Tiket {$code} ({$registration->user->name}) sudah pernah digunakan check-in pada {$time} WIB.",
            ];
        }

        if ($hasCheckedInCol) {
            $registration->checked_in_at = now();
            $registration->save();
        }

        $ticketName = $ticketMap->get($registration->ticket_id)['name'] ?? 'Tiket';

        return [
            'status' => 'valid',
            'message' => "GATE OPEN: Tiket {$code} VALID! Pemegang: {$registration->user->name} ({$registration->quantity}x {$ticketName}).",
        ];
    }
}
