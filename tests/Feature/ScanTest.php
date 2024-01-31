<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Shift;
use App\Models\Workspace;
use App\Models\WorkspaceType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Unit\ShiftTest;

class ScanTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_starts_a_new_shift()
    {
        $client = Client::factory()->create();

        $this->actingAs($client);

        $workspace = Workspace::factory()->create([
            'qr_code' =>'abc123',
        ]);

        $this->assertDatabaseCount('shifts', 0);

        $this->post('/scan', ['code' => 'abc123']);

        $this->assertDatabaseCount('shifts', 1);
    }

    /** @test */
    public function it_removes_seconds_on_starting_time()
    {
        $client = Client::factory()->create();

        $this->actingAs($client);

        $workspace = Workspace::factory()->create();

        $this->post('/scan', ['code' => $workspace->qr_code]);

        $shift = Shift::first();

        $this->assertEquals('00', $shift->started_at->format('s'));
    }

    /** @test */
    public function it_logs_scan_when_client_starts_a_new_shift()
    {
        $client = Client::factory()->create();

        $this->actingAs($client);

        $workspace = Workspace::factory()->create();

        $this->assertDatabaseCount('scan_logs', 0);

        $this->post('/scan', ['code' => $workspace->qr_code]);

        $this->assertDatabaseHas('scan_logs', [
            'client_id' => $client->id,
            'workspace_id' => $workspace->id,
            'shift_id' => Shift::first()->id,
            'direction' => 'IN',
            'message' => 'Nouveau shift',
        ]);
    }

    /** @test */
    public function it_logs_scan_when_client_starts_booking()
    {
        $client = Client::factory()->create();

        $this->actingAs($client);

        $workspace = Workspace::factory()->create();

        $shift = Shift::factory()->create([
            'client_id' => $client->id,
            'started_at' => now()->subMinute(),
            'ended_at' => now()->addHour(),
            'workspace_id' => $workspace->id,
            'status' => 'booked',
            'is_reservation' => true,
        ]);

        $this->assertDatabaseCount('scan_logs', 0);

        $this->post('/scan', ['code' => $workspace->qr_code]);

        $this->assertDatabaseHas('scan_logs', [
            'client_id' => $client->id,
            'workspace_id' => $workspace->id,
            'shift_id' => $shift->id,
            'direction' => 'IN',
            'message' => 'Entrée réservation',
        ]);
    }

    /** @test */
    public function it_logs_scan_when_client_leaves_booking()
    {
        $client = Client::factory()->create();

        $this->actingAs($client);

        $workspace = Workspace::factory()->create();

        $shift = Shift::factory()->create([
            'client_id' => $client->id,
            'started_at' => now()->subHour(),
            'ended_at' => now()->addHour(),
            'workspace_id' => $workspace->id,
            'status' => 'running',
            'is_reservation' => true,
        ]);

        $this->assertDatabaseCount('scan_logs', 0);

        $this->post('/scan', ['code' => $workspace->qr_code]);

        $this->assertDatabaseHas('scan_logs', [
            'client_id' => $client->id,
            'workspace_id' => $workspace->id,
            'shift_id' => $shift->id,
            'direction' => 'OUT',
            'message' => 'Sortie réservation',
        ]);
    }
}
