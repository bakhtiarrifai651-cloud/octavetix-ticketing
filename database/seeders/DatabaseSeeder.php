<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\EventModule\Models\Category;
use App\Modules\EventModule\Models\Event;
use App\Modules\TicketModule\Models\Ticket;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Default Admin & Peserta OctaveTix
        User::updateOrCreate(
            ['email' => 'admin@octavetix.test'],
            [
                'name' => 'Admin OctaveTix',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'peserta@octavetix.test'],
            [
                'name' => 'Raka Metalhead',
                'password' => Hash::make('password'),
                'role' => 'peserta',
            ]
        );

        // 2. Kategori Genre Musik
        $categoryNames = [
            'Thrash Metal',
            'Death Metal',
            'Metalcore',
            'Hardcore Punk',
            'Underground Gig',
        ];

        $categories = collect($categoryNames)->map(function ($name) {
            $data = ['name' => $name];
            if (Schema::hasColumn('categories', 'slug')) {
                $data['slug'] = Str::slug($name);
            }

            return Category::firstOrCreate(['name' => $name], $data);
        });

        // 3. Data Konser & Jenis Tiket
        $dateColumn = Schema::hasColumn('events', 'event_date') ? 'event_date' : 'date';

        $eventsData = [
            [
                'title' => 'Jogja Distortion Fest 2026',
                'description' => 'Festival musik ekstrem tahunan yang menghadirkan 12 band thrash dan death metal lokal maupun nasional dalam satu panggung penuh distorsi.',
                'location' => 'Stadion Kridosono, Yogyakarta',
                $dateColumn => now()->addDays(14)->setTime(16, 0),
                'cat_indexes' => [0, 1],
                'tickets' => [
                    ['name' => 'Early Mosh Pit', 'price' => 85000, 'quota' => 4],
                    ['name' => 'Presale Festival', 'price' => 135000, 'quota' => 50],
                    ['name' => 'VIP Backstage Pass', 'price' => 275000, 'quota' => 10],
                ],
            ],
            [
                'title' => 'Malam Kebisingan Vol. IV',
                'description' => 'Intimate underground gig dengan tata suara bertekanan tinggi khusus penikmat metalcore dan hardcore punk.',
                'location' => 'Ruang Eksperimen Audio, Sleman',
                $dateColumn => now()->addDays(21)->setTime(19, 30),
                'cat_indexes' => [2, 3, 4],
                'tickets' => [
                    ['name' => 'Regular Crowd', 'price' => 60000, 'quota' => 35],
                    ['name' => 'Bundle + Merch Zine', 'price' => 120000, 'quota' => 3],
                ],
            ],
            [
                'title' => 'Sunday Riff & Breakdown Session',
                'description' => 'Pertunjukan akhir pekan kolektif musik independen menampilkan sesi akustik berat dan set penuh band cadas.',
                'location' => 'Gedung Kesenian Condongcatur',
                $dateColumn => now()->addDays(30)->setTime(18, 0),
                'cat_indexes' => [0, 2, 4],
                'tickets' => [
                    ['name' => 'Presale Ticket', 'price' => 50000, 'quota' => 40],
                    ['name' => 'On The Spot Tier', 'price' => 75000, 'quota' => 20],
                ],
            ],
        ];

        foreach ($eventsData as $item) {
            $event = Event::create([
                'title' => $item['title'],
                'description' => $item['description'],
                'location' => $item['location'],
                $dateColumn => $item[$dateColumn],
            ]);

            $event->categories()->sync(
                collect($item['cat_indexes'])->map(fn ($idx) => $categories[$idx]->id)->all()
            );

            foreach ($item['tickets'] as $t) {
                Ticket::create([
                    'event_id' => $event->id,
                    'name' => $t['name'],
                    'price' => $t['price'],
                    'quota' => $t['quota'],
                ]);
            }
        }
    }
}
