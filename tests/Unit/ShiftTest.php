<?php

namespace Tests\Unit;

use App\Actions\CloseShift;
use App\Models\Client;
use App\Models\Shift;
use App\Models\Workspace;
use App\Models\WorkspaceType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShiftTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_takes_reservation_time_when_client_is_leaving_before_end_of_reservation()
    {
        // Create a reservation
        $shift = Shift::factory()->create([
            'started_at' => now()->subMinutes(100),
            'ended_at' => now()->addMinutes(30),
            'status' => 'running',
            'is_reservation' => true,
        ]);

        CloseShift::make($shift)->run();

        $this->assertEquals(130, $shift->duration_in_minutes);
    }

    /** @test */
    public function it_takes_current_time_when_client_is_leaving_after_end_of_reservation()
    {
        // Create a reservation
        $shift = Shift::factory()->create([
            'started_at' => now()->subMinutes(100),
            'ended_at' => now()->subMinutes(30),
            'status' => 'running',
            'is_reservation' => true,
        ]);

        $this->assertEquals(70, $shift->duration_in_minutes);

        CloseShift::make($shift)->run();

        // 99 because seconds are always set to 0
        $this->assertEquals(99, $shift->duration_in_minutes);
    }

    /** @test */
    public function it_decreases_client_purchased_minutes_according_to_the_type_of_workspace()
    {
        $client = Client::factory()->create([
            'purchased_minutes_table' => 50,
            'purchased_minutes_office' => 0,
            'included_minutes_table' => 0,
            'included_minutes_office' => 0,
            'has_subscription' => false,
            'is_resident' => false,
            'is_235' => false,
        ]);

        $workspaceType = WorkspaceType::factory()->create([
            'minutes_type' => 'table',
            'base_price' => 10,
        ]);

        $workspace = Workspace::factory()->create([
            'workspace_type_id' => $workspaceType->id,
        ]);

        $shift = Shift::factory()->create([
            'started_at' => now()->subMinutes(100),
            'ended_at' => null,
            'status' => 'running',
            'is_reservation' => false,
            'workspace_id' => $workspace,
            'client_id' => $client,
        ]);

        CloseShift::make($shift)->run();

        $this->assertEquals(0, $client->refresh()->purchased_minutes_table);
    }

    /** @test */
    public function shifts_started_earlier_get_merged_with_booked_shift()
    {
        $client = Client::factory()->create();
        $workspace = Workspace::factory()->create();

        $bookedShift = Shift::factory()->create([
            'client_id' => $client->id,
            'workspace_id' => $workspace->id,
            'status' => 'booked',
            'started_at' => now()->subMinute(1),
            'ended_at' => now()->addMinutes(60),
            'is_reservation' => true,
        ]);

        $shift = Shift::factory()->create([
            'client_id' => $client->id,
            'workspace_id' => $workspace->id,
            'started_at' => now()->subMinutes(10),
            'status' => 'running',
            'is_reservation' => false,
        ]);

        $this->assertEquals(2, Shift::count());

        $this->artisan('shift:merge');

        $this->assertEquals(1, Shift::count());

        $this->assertDatabaseHas('shifts', [
            'id' => $bookedShift->id,
            'client_id' => $client->id,
            'workspace_id' => $workspace->id,
            'status' => 'running',
            'is_reservation' => true,
            'started_at' => $shift->started_at->format('Y-m-d H:i:s'),
            'ended_at' => $bookedShift->ended_at->format('Y-m-d H:i:s'),
        ]);
    }

    /** @test */
    // public function it_move_scans_when_shift_got_merged()
    // {
    //     $client = Client::factory()->create();
    //     $workspace = Workspace::factory()->create();

    //     $bookedShift = Shift::factory()->create([
    //         'client_id' => $client->id,
    //         'workspace_id' => $workspace->id,
    //         'status' => 'booked',
    //         'started_at' => now()->subMinute(1),
    //         'ended_at' => now()->addMinutes(60),
    //         'is_reservation' => true,
    //     ]);

    //     $shift = Shift::factory()->create([
    //         'client_id' => $client->id,
    //         'workspace_id' => $workspace->id,
    //         'started_at' => now()->subMinutes(10),
    //         'status' => 'running',
    //         'is_reservation' => false,
    //     ]);

    //     $shift->scans()->create([
    //         'client_id' => $shift->client_id,
    //         'workspace_id' => $shift->workspace_id,
    //         'direction' => 'IN',
    //         'message' => 'Entrée réservation'
    //     ]);

    //     $this->assertEquals(0, $bookedShift->scans()->count());
    //     $this->assertEquals(1, $shift->scans()->count());

    //     $this->artisan('shift:merge');

    //     $this->assertEquals(1, $bookedShift->scans()->count());
    //     $this->assertEquals(0, $shift->scans()->count());
    // }

    /** @test */
    public function it_closes_expired_shifts()
    {
        $client = Client::factory()->create();
        $workspace1 = Workspace::factory()->create();
        $workspace2 = Workspace::factory()->create();

        $shiftToClose = Shift::factory()->create([
            'client_id' => $client->id,
            'workspace_id' => $workspace1->id,
            'status' => 'booked',
            'started_at' => now()->subHour(1)->subMinutes(1),
            'ended_at' => now()->subMinutes(1),
            'is_reservation' => true,
        ]);

        $shiftNotToClose = Shift::factory()->create([
            'client_id' => $client->id,
            'workspace_id' => $workspace2->id,
            'status' => 'booked',
            'started_at' => now()->subHour(1)->subMinutes(1),
            'ended_at' => now()->addMinutes(1),
            'is_reservation' => true,
        ]);

        $this->artisan('shift:close-expired');

        $shiftToClose->refresh();
        $shiftNotToClose->refresh();

        $this->assertEquals('finished', $shiftToClose->status);
        $this->assertEquals('booked', $shiftNotToClose->status);
    }

    /** @test */
    public function it_closes_booked_shift_when_in_conflict_with_next_shift()
    {
        $client1 = Client::factory()->create();
        $client2 = Client::factory()->create();
        $workspace = Workspace::factory()->create();

        $shiftToClose = Shift::factory()->create([
            'client_id' => $client1->id,
            'workspace_id' => $workspace->id,
            'status' => 'running',
            'started_at' => now()->subHour(),
            'ended_at' => now(),
            'is_reservation' => true,
        ]);

        $shiftNotToClose = Shift::factory()->create([
            'client_id' => $client2->id,
            'workspace_id' => $workspace->id,
            'status' => 'booked',
            'started_at' => now(),
            'ended_at' => now()->addHour(),
            'is_reservation' => true,
        ]);

        $this->artisan('shift:close-expired');

        $shiftToClose->refresh();
        $shiftNotToClose->refresh();

        $this->assertEquals('finished', $shiftToClose->status);
        $this->assertEquals('booked', $shiftNotToClose->status);
    }

    /** @test */
    public function it_closes_normal_shift_when_in_conflict_with_next_shift()
    {
        $client1 = Client::factory()->create();
        $client2 = Client::factory()->create();
        $workspace = Workspace::factory()->create();

        $shiftToClose = Shift::factory()->create([
            'client_id' => $client1->id,
            'workspace_id' => $workspace->id,
            'status' => 'running',
            'started_at' => now()->subHour(),
            'ended_at' => now(),
            'is_reservation' => false,
        ]);

        $shiftNotToClose = Shift::factory()->create([
            'client_id' => $client2->id,
            'workspace_id' => $workspace->id,
            'status' => 'booked',
            'started_at' => now(),
            'ended_at' => now()->addHour(),
            'is_reservation' => true,
        ]);

        $this->artisan('shift:close-expired');

        $shiftToClose->refresh();
        $shiftNotToClose->refresh();

        $this->assertEquals('finished', $shiftToClose->status);
        $this->assertEquals('booked', $shiftNotToClose->status);
    }
}
