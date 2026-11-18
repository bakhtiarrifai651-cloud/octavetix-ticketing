<?php

namespace App\Modules\TicketModule\Contracts;

interface TicketContract
{
    public function getTicketsByEvent(int $eventId): array;

    public function findTicket(int $ticketId): ?array;

    public function reserveQuota(int $ticketId, int $quantity): void;

    public function releaseQuota(int $ticketId, int $quantity): void;

    public function deleteTicketsByEvent(int $eventId): void;
}
