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
        $ticket = Ticket::find($ticketId);

        return $ticket ? $ticket->toArray() : null;
    }

    public function reserveQuota(int $ticketId, int $quantity): array
    {
        $ticket = Ticket::where('id', $ticketId)->lockForUpdate()->first();

        if (! $ticket || $ticket->quota < $quantity) {
            throw new QuotaFullException(
                "Kuota tiket '{$ticket?->name}' tidak mencukupi (tersisa: " . ($ticket?->quota ?? 0) . ")."
            );
        }

        $ticket->decrement('quota', $quantity);

        return $ticket->fresh()->toArray();
    }
}