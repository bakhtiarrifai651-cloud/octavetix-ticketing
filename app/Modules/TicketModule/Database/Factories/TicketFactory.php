<?php

namespace App\Modules\TicketModule\Database\Factories;

use App\Modules\TicketModule\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'event_id' => 1,
            'name' => fake()->randomElement(['Reguler', 'VIP', 'Early Bird']),
            'price' => fake()->randomElement([25000, 50000, 100000, 150000]),
            'quota' => fake()->numberBetween(10, 50),
        ];
    }
}
