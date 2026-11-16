<?php

namespace App\Modules\TicketModule\Models;

use App\Modules\TicketModule\Database\Factories\TicketFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'price',
        'quota',
    ];

    protected static function newFactory()
    {
        return TicketFactory::new();
    }
}