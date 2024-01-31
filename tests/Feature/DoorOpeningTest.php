<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Holiday;
use App\Models\OpeningTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DoorOpeningTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function clients_need_to_be_authorized_to_view_the_door_access_page()
    {
        $notAllowed = Client::factory()->create();
        $allowed = Client::factory()->create(['door_access_enabled' => true]);

        $this->actingAs($notAllowed)->get('/entrance')->assertStatus(403);
        $this->actingAs($allowed)->get('/entrance')->assertStatus(200);
    }

    /** @test */
    public function clients_need_to_be_authorized_to_open_the_door()
    {
        $notAllowed = Client::factory()->create();
        $allowed = Client::factory()->create(['door_access_enabled' => true]);

        $this->actingAs($notAllowed)->post('/entrance/open')->assertStatus(403);
        $this->actingAs($allowed)->post('/entrance/open')->assertStatus(200);
    }

    /** @test */
    public function opening_the_door_creates_a_door_opening_record()
    {
        $client = Client::factory()->create(['door_access_enabled' => true]);
        OpeningTime::factory()->create([
            'day_name' => strtolower(now()->englishDayOfWeek),
            'is_open' => true,
            'time_from' => '00:00',
            'time_to' => '23:59',
        ]);

        $this->actingAs($client)->post('/entrance/open');

        $this->assertDatabaseHas('door_openings', [
            'client_id' => $client->id,
        ]);
    }

    /** @test */
    public function it_denies_door_access_page_based_on_opening_times()
    {
        $client = Client::factory()->create(['door_access_enabled' => true]);
        $monday = OpeningTime::factory()->create([
            'day_name' => 'monday',
            'is_open' => true,
            'time_from' => '08:00',
            'time_to' => '19:00',
        ]);

        $sunday = OpeningTime::factory()->create([
            'day_name' => 'monday',
            'is_open' => false,
        ]);

        $this->actingAs($client);

        now()->setTestNow(now()->next('monday')->setTime(7, 59));
        $this->get('/entrance')->assertSee('Calliopée est fermé actuellement');
        $this->post('/entrance/open')->assertSee('Calliopée est fermé actuellement');

        now()->setTestNow(now()->next('monday')->setTime(8, 0));
        $this->get('/entrance')->assertStatus(200);
        $this->post('/entrance/open')->assertStatus(200);

        now()->setTestNow(now()->next('sunday')->setTime(12, 0));
        $this->get('/entrance')->assertSee('Calliopée est fermé aujourd\'hui');
        $this->post('/entrance/open')->assertSee('Calliopée est fermé aujourd\'hui');
    }

    /** @test */
    public function it_denies_door_access_based_on_holidays()
    {
        $client = Client::factory()->create(['door_access_enabled' => true]);

        OpeningTime::factory()->create([
            'day_name' => 'monday',
            'is_open' => true,
            'time_from' => '08:00',
            'time_to' => '19:00',
        ]);

        Holiday::create([
            'date' => now()->next('monday')->format('Y-m-d'),
            'message' => 'Fermé',
        ]);

        now()->setTestNow(now()->next('monday')->setTime(12, 0));

        $this->actingAs($client);

        $this->get('/entrance')->assertSee('Calliopée est fermé aujourd\'hui');
        $this->post('/entrance/open')->assertSee('Calliopée est fermé aujourd\'hui');
    }
}
