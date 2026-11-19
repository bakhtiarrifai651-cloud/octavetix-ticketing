<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\EventModule\Models\Category;
use App\Modules\EventModule\Models\Event;
use App\Modules\RegistrationModule\Models\Registration;
use App\Modules\TicketModule\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OctaveTixTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_access_control_prevents_peserta_from_admin_pages(): void
    {
        $peserta = User::factory()->create(['role' => 'peserta']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($peserta)
            ->get('/categories')
            ->assertStatus(403);

        $this->actingAs($admin)
            ->get('/categories')
            ->assertStatus(200);
    }

    public function test_event_search_and_genre_filter(): void
    {
        $peserta = User::factory()->create(['role' => 'peserta']);
        $metal = Category::create(['name' => 'Death Metal', 'slug' => 'death-metal']);
        $punk = Category::create(['name' => 'Hardcore Punk', 'slug' => 'hardcore-punk']);

        $event1 = Event::create([
            'title' => 'Jakarta Deathfest',
            'description' => 'Brutal night',
            'event_date' => now()->addDays(10),
            'location' => 'Rossi Musik Jakarta',
        ]);
        $event1->categories()->attach($metal->id);

        $event2 = Event::create([
            'title' => 'Bandung Hardcore Riot',
            'description' => 'Moshpit session',
            'event_date' => now()->addDays(15),
            'location' => 'GOR Saparua Bandung',
        ]);
        $event2->categories()->attach($punk->id);

        $this->actingAs($peserta)
            ->get('/events?category=death-metal')
            ->assertSee('Jakarta Deathfest')
            ->assertDontSee('Bandung Hardcore Riot');

        $this->actingAs($peserta)
            ->get('/events?search=Saparua')
            ->assertSee('Bandung Hardcore Riot')
            ->assertDontSee('Jakarta Deathfest');
    }

    public function test_ticket_booking_overbooking_protection_and_cancellation(): void
    {
        $peserta = User::factory()->create(['role' => 'peserta']);
        $event = Event::create([
            'title' => 'Underground Showcase',
            'description' => 'Live gig',
            'event_date' => now()->addDays(5),
            'location' => 'Yogyakarta',
        ]);

        $ticket = Ticket::create([
            'event_id' => $event->id,
            'name' => 'Presale',
            'price' => 100000,
            'quota' => 2,
        ]);

        // Melebihi kuota -> gagal
        $this->actingAs($peserta)
            ->post('/registrations', [
                'ticket_id' => $ticket->id,
                'quantity' => 5,
            ])
            ->assertSessionHas('error');

        $this->assertEquals(2, $ticket->fresh()->quota);

        // Pesan 2 tiket -> berhasil, kuota habis, dan terbit 2 tiket individual dengan kode unik
        $this->actingAs($peserta)
            ->post('/registrations', [
                'ticket_id' => $ticket->id,
                'quantity' => 2,
            ])
            ->assertRedirect('/my-tickets');

        $this->assertEquals(0, $ticket->fresh()->quota);
        $this->assertDatabaseCount('registrations', 2);

        $registrations = Registration::all();
        $this->assertStringStartsWith('OCT-', $registrations[0]->ticket_code);
        $this->assertStringStartsWith('OCT-', $registrations[1]->ticket_code);
        $this->assertNotEquals($registrations[0]->ticket_code, $registrations[1]->ticket_code);

        // Batalkan 1 tiket -> 1 kuota kembali, sisa 1 tiket aktif
        $this->actingAs($peserta)
            ->delete("/registrations/{$registrations[0]->id}")
            ->assertRedirect('/my-tickets');

        $this->assertEquals(1, $ticket->fresh()->quota);
        $this->assertDatabaseCount('registrations', 1);
    }

    public function test_admin_gate_checkin_validation(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $peserta = User::factory()->create(['role' => 'peserta']);

        $event = Event::create([
            'title' => 'Metal Attack',
            'description' => 'Live concert',
            'event_date' => now()->addDays(3),
            'location' => 'Surabaya',
        ]);

        $ticket = Ticket::create([
            'event_id' => $event->id,
            'name' => 'VIP',
            'price' => 250000,
            'quota' => 10,
        ]);

        $this->actingAs($peserta)->post('/registrations', [
            'ticket_id' => $ticket->id,
            'quantity' => 1,
        ]);

        $registration = Registration::first();

        // Check-in pertama -> valid
        $this->actingAs($admin)
            ->post("/events/{$event->id}/check-in", ['ticket_code' => $registration->ticket_code])
            ->assertSessionHas('success');

        // Check-in kedua dengan kode sama -> ditolak
        $this->actingAs($admin)
            ->post("/events/{$event->id}/check-in", ['ticket_code' => $registration->ticket_code])
            ->assertSessionHas('error');
    }
}
