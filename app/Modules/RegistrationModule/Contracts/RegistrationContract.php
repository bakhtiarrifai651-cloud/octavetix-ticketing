<?php

namespace App\Modules\RegistrationModule\Contracts;

interface RegistrationContract
{
    public function getEventParticipants(int $eventId): array;

    public function checkInTicket(int $eventId, string $ticketCode): array;
}
