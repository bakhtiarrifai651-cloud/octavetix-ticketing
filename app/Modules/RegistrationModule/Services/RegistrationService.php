<?php

namespace App\Modules\RegistrationModule\Services;

use App\Modules\RegistrationModule\Models\Registration;
use App\Modules\TicketModule\Contracts\TicketContract;
use App\Modules\TicketModule\Exceptions\QuotaFullException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class RegistrationService
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

            $code = 'TIX-'.strtoupper(Str::random(8));

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
}
