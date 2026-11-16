<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\EventModule\Models\Category;
use App\Modules\EventModule\Models\Event;
use App\Modules\TicketModule\Models\Ticket;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin & Peserta untuk Login
        User::create([
            'name' => 'Admin Event',
            'email' => 'admin@amikom.ac.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Mahasiswa Peserta',
            'email' => 'peserta@amikom.ac.id',
            'password' => Hash::make('password'),
            'role' => 'peserta',
        ]);

        // 2. Buat 4 Kategori via Factory
        $categories = Category::factory()->count(4)->create();

        // 3. Buat 4 Event via Factory & hubungkan ke Kategori (Many-to-Many Pivot)
        $events = Event::factory()->count(4)->create();

        foreach ($events as $event) {
            $event->categories()->attach(
                $categories->random(2)->pluck('id')->toArray()
            );

            // 4. Buat 2 jenis tiket untuk masing-masing Event
            Ticket::factory()->create([
                'event_id' => $event->id,
                'name' => 'Tiket Reguler',
                'price' => 50000,
                'quota' => 20,
            ]);

            Ticket::factory()->create([
                'event_id' => $event->id,
                'name' => 'Tiket VIP',
                'price' => 150000,
                'quota' => 5,
            ]);
        }
    }
}