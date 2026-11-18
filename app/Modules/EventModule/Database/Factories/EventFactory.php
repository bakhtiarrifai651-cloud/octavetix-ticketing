<?php

namespace App\Modules\EventModule\Database\Factories;

use App\Modules\EventModule\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(3),
            'event_date' => fake()->dateTimeBetween('+1 week', '+3 months'),
            'location' => fake()->randomElement([
                'Auditorium Universitas Amikom Yogyakarta',
                'Gedung BSC Lt. 3 Amikom',
                'Cinema Amikom',
                'Online via Zoom Meeting',
            ]),
        ];
    }
}
