<?php

namespace App\Modules\TicketModule\Services;

use App\Modules\TicketModule\Contracts\TicketContract;
use App\Modules\TicketModule\Exceptions\QuotaFullException;
use App\Modules\TicketModule\Models\Ticket;

class TicketService implements TicketContract
{
    public function getTicketsByEvent(int $eventId): array
    {
        return Ticket::where('event_id', $eventId)
            ->orderBy('price', 'asc')
            ->get()
            ->toArray();
    }

    public function findTicket(int $ticketId): ?array
    {
        return Ticket::find($ticketId)?->toArray();
    }

    public function reserveQuota(int $ticketId, int $quantity): void
    {
        $ticket = Ticket::where('id', $ticketId)->lockForUpdate()->first();

        if (! $ticket || $ticket->quota < $quantity) {
            throw new QuotaFullException('Maaf, kuota tiket tidak mencukupi atau sudah habis.');
        }

        $ticket->decrement('quota', $quantity);
    }

    public function releaseQuota(int $ticketId, int $quantity): void
    {
        $ticket = Ticket::where('id', $ticketId)->lockForUpdate()->first();

        if ($ticket) {
            $ticket->increment('quota', $quantity);
        }
    }

    public function deleteTicketsByEvent(int $eventId): void
    {
        Ticket::where('event_id', $eventId)->delete();
    }
}
