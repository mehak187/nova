<?php

namespace Tests\Feature;

use App\Actions\CloseShift;
use Tests\TestCase;
use App\Models\Client;
use App\Models\Workspace;
use App\Models\WorkspaceType;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BugTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_2023_02_27_shift_calculation()
    {
        /**
         * Il y avait un bug dans le calcul des minutes d'un shift.
         * Celui-ci était du au fait que ChargeTimeToClient acceptair un int pour le nombre de minutes.
         * Or, le nombre de minutes (une fois multiplié par un facteur de temps) pouvait être un float.
         */
        $workspaceType = WorkspaceType::factory()->create([
            'minutes_type' => 'office',
            'base_price' => 32,
            'subscription_price' => 10,
        ]);

        $workspace = Workspace::factory()->create([
            'workspace_type_id' => $workspaceType->id,
            'minute_factor' => 1,
        ]);

        $client = Client::factory()->create([
            'purchased_minutes_office' => 45,
        ]);

        $shift = $client->shifts()->create([
            'client_id' => $client->id,
            'workspace_id' => $workspace->id,
            'started_at' => now()->setTime(17, 0, 0),
            'is_reservation' => true,
        ]);

        CloseShift::make($shift)->run(now()->setTime(18, 21, 0));

        $this->assertEquals(23.782, $shift->total_amount_due);
    }
}
