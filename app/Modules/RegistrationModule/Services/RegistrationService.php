<?php

namespace App\Modules\RegistrationModule\Services;

use App\Modules\RegistrationModule\Contracts\RegistrationContract;
use App\Modules\RegistrationModule\Models\Registration;
use App\Modules\TicketModule\Contracts\TicketContract;
use App\Modules\TicketModule\Exceptions\QuotaFullException;
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

            // Potong kuota melalui Contract (akan melempar QuotaFullException jika kuota kurang)
            $this->ticketContract->reserveQuota($ticketId, $quantity);

            $code = 'OCT-'.strtoupper(Str::random(8));

            $payload = [
                'user_id' => $userId,
                'ticket_id' => $ticket['id'],
                'quantity' => $quantity,
                'total_price' => $ticket['price'] * $quantity,
            ];

            if (Schema::hasColumn('registrations', 'ticket_code')) {
                $payload['ticket_code'] = $code;
            }

            if (Schema::hasColumn('registrations', 'booking_code')) {
                $payload['booking_code'] = $code;
            }

            if (Schema::hasColumn('registrations', 'status')) {
                $payload['status'] = 'confirmed';
            }

            return Registration::create($payload);
        });
    }

    public function cancelBooking(int $registrationId, int $userId): void
    {
        DB::transaction(function () use ($registrationId, $userId) {
            $registration = Registration::where('id', $registrationId)
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->firstOrFail();

            $hasStatus = Schema::hasColumn('registrations', 'status');

            if ($hasStatus && $registration->status === 'cancelled') {
                return;
            }

            // Kembalikan kuota tiket melalui TicketContract
            $this->ticketContract->releaseQuota($registration->ticket_id, $registration->quantity);

            if ($hasStatus) {
                $registration->update(['status' => 'cancelled']);
            } else {
                $registration->delete();
            }
        });
    }

    public function getEventParticipants(int $eventId): array
    {
        $query = Registration::with(['user', 'ticket'])
            ->whereHas('ticket', fn ($q) => $q->where('event_id', $eventId))
            ->latest();

        if (Schema::hasColumn('registrations', 'status')) {
            $query->where('status', '!=', 'cancelled');
        }

        $items = $query->get();

        return [
            'total_tickets' => (int) $items->sum('quantity'),
            'total_revenue' => (int) $items->sum('total_price'),
            'participants' => $items->map(fn ($reg) => [
                'user_name' => $reg->user->name ?? 'Peserta',
                'user_email' => $reg->user->email ?? '-',
                'ticket_name' => $reg->ticket->name ?? '-',
                'ticket_code' => $reg->ticket_code ?? $reg->booking_code ?? '-',
                'quantity' => $reg->quantity,
                'total_price' => $reg->total_price,
                'booked_at' => $reg->created_at->format('d M Y, H:i'),
            ])->toArray(),
        ];
    }
}
