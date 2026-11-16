<?php

namespace App\Modules\TicketModule\Exceptions;

use Exception;

class QuotaFullException extends Exception
{
    public function __construct(string $message = 'Maaf, kuota tiket sudah habis atau tidak mencukupi jumlah pesanan Anda.')
    {
        parent::__construct($message);
    }
}