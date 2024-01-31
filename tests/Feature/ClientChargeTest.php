<?php

namespace Tests\Feature;

use App\Actions\CloseShift;
use App\Models\Client;
use App\Models\Shift;
use App\Models\Workspace;
use App\Models\WorkspaceType;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientChargeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_charges_time_to_client()
    {
        $client = Client::factory()->create([
            'purchased_minutes_table' => 100,
        ]);

        $workspaceType = WorkspaceType::factory()->create([
            'minutes_type' => 'table',
        ]);

        $workspace = Workspace::factory()->create([
            'workspace_type_id' => $workspaceType->id,
            'minute_factor' => 1.5,
        ]);

        $this->actingAs($client);

        $shift = Shift::factory()->create([
            'client_id' => $client->id,
            'status' => 'running',
            'is_reservation' => false,
            'started_at' => now()->setTime(10, 0, 0),
            'ended_at' => null,
            'workspace_id' => $workspace->id,
        ]);

        CloseShift::make($shift)->run(now()->setTime(10, 0, 0)->addMinutes(50));

        $this->assertEquals(25, $client->fresh()->purchased_minutes_table);
    }

    /** @test */
    public function it_charges_time_to_client_with_a_1_25_factor_the_evening()
    {
        $client = Client::factory()->create([
            'purchased_minutes_table' => 100,
        ]);

        $workspaceType = WorkspaceType::factory()->create([
            'minutes_type' => 'table',
        ]);

        $workspace = Workspace::factory()->create([
            'workspace_type_id' => $workspaceType->id,
            'minute_factor' => 1,
        ]);

        $this->actingAs($client);

        $shift = Shift::factory()->create([
            'client_id' => $client->id,
            'status' => 'running',
            'is_reservation' => false,
            'started_at' => now()->setTime(18, 0, 0),
            'ended_at' => null,
            'workspace_id' => $workspace->id,
        ]);

        CloseShift::make($shift)->run(now()->setTime(19, 0, 0));

        $this->assertEquals(25, $client->fresh()->purchased_minutes_table);
    }

    /** @test */
    public function it_charges_time_to_client_with_a_1_5_factor_on_saturday()
    {
        $client = Client::factory()->create([
            'purchased_minutes_table' => 100,
        ]);

        $workspaceType = WorkspaceType::factory()->create([
            'minutes_type' => 'table',
        ]);

        $workspace = Workspace::factory()->create([
            'workspace_type_id' => $workspaceType->id,
            'minute_factor' => 1,
        ]);

        $this->actingAs($client);

        $shift = Shift::factory()->create([
            'client_id' => $client->id,
            'status' => 'running',
            'is_reservation' => false,
            'started_at' => now()->nextWeekendDay(),
            'ended_at' => null,
            'workspace_id' => $workspace->id,
        ]);

        CloseShift::make($shift)->run(now()->nextWeekendDay()->addMinutes(60));

        $this->assertEquals(10, $client->fresh()->purchased_minutes_table);
    }

    /** @test */
    public function it_adds_up_factors()
    {
        $client = Client::factory()->create([
            'purchased_minutes_table' => 150,
        ]);

        $workspaceType = WorkspaceType::factory()->create([
            'minutes_type' => 'table',
        ]);

        $workspace = Workspace::factory()->create([
            'workspace_type_id' => $workspaceType->id,
            'minute_factor' => 1.5,
        ]);

        $this->actingAs($client);

        $shift = Shift::factory()->create([
            'client_id' => $client->id,
            'status' => 'running',
            'is_reservation' => false,
            'started_at' => now()->nextWeekendDay(),
            'ended_at' => null,
            'workspace_id' => $workspace->id,
        ]);

        CloseShift::make($shift)->run(now()->nextWeekendDay()->addMinutes(60));

        $this->assertEquals(15, $client->fresh()->purchased_minutes_table);
    }

    /** @test */
    public function it_only_charges_extra_time_the_evening()
    {
        $client = Client::factory()->create([
            'purchased_minutes_table' => 300,
        ]);

        $workspaceType = WorkspaceType::factory()->create([
            'minutes_type' => 'table',
        ]);

        $workspace = Workspace::factory()->create([
            'workspace_type_id' => $workspaceType->id,
            'minute_factor' => 1,
        ]);

        $this->actingAs($client);

        $shift = Shift::factory()->create([
            'client_id' => $client->id,
            'status' => 'running',
            'is_reservation' => false,
            'started_at' => now()->setTime(17, 0, 0),
            'ended_at' => null,
            'workspace_id' => $workspace->id,
        ]);

        CloseShift::make($shift)->run(now()->setTime(19, 0, 0));

        $this->assertEquals(165, $client->fresh()->purchased_minutes_table);
    }
}
