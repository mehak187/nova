<?php

namespace Tests\Feature;

use App\Actions\ChargeTimeToClient;
use Tests\TestCase;
use App\Models\User;
use App\Models\Shift;
use App\Models\Client;
use App\Models\Workspace;
use App\Models\WorkspaceType;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class ClientBalanceChangeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_stores_the_initial_balance_when_the_client_is_created()
    {
        $client = Client::factory()->create([
            'purchased_minutes_table' => 1,
            'purchased_minutes_office' => 2,
            'included_minutes_table' => -1,
            'included_minutes_office' => -2,
        ]);

        $this->assertDatabaseHas('client_balance_changes', [
            'client_id' => $client->id,
            'operation' => 'credit',
            'property' => 'purchased_minutes_table',
            'amount' => '1',
        ]);

        $this->assertDatabaseHas('client_balance_changes', [
            'client_id' => $client->id,
            'operation' => 'credit',
            'property' => 'purchased_minutes_office',
            'amount' => '2',
        ]);

        $this->assertDatabaseHas('client_balance_changes', [
            'client_id' => $client->id,
            'operation' => 'debit',
            'property' => 'included_minutes_table',
            'amount' => '-1',
        ]);

        $this->assertDatabaseHas('client_balance_changes', [
            'client_id' => $client->id,
            'operation' => 'debit',
            'property' => 'included_minutes_office',
            'amount' => '-2',
        ]);
    }

    /** @test */
    public function it_logs_changes_when_balance_is_updated()
    {
        $client = Client::factory()->create([
            'purchased_minutes_table' => 100,
            'purchased_minutes_office' => 100,
            'included_minutes_table' => 100,
            'included_minutes_office' => 100,
        ]);

        $client->update([
            'purchased_minutes_table' => 200,
            'purchased_minutes_office' => 200,
            'included_minutes_table' => 0,
            'included_minutes_office' => 0,
        ]);

        $this->assertDatabaseHas('client_balance_changes', [
            'client_id' => $client->id,
            'operation' => 'credit',
            'property' => 'purchased_minutes_table',
            'amount' => '100',
        ]);

        $this->assertDatabaseHas('client_balance_changes', [
            'client_id' => $client->id,
            'operation' => 'credit',
            'property' => 'purchased_minutes_office',
            'amount' => '100',
        ]);

        $this->assertDatabaseHas('client_balance_changes', [
            'client_id' => $client->id,
            'operation' => 'debit',
            'property' => 'included_minutes_table',
            'amount' => '-100',
        ]);

        $this->assertDatabaseHas('client_balance_changes', [
            'client_id' => $client->id,
            'operation' => 'debit',
            'property' => 'included_minutes_office',
            'amount' => '-100',
        ]);
    }

    /** @test */
    public function it_logs_the_user_who_changes_the_balance_if_its_nova_request()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create();

        $client->update(['purchased_minutes_table' => 200]);

        $this->assertDatabaseHas('client_balance_changes', [
            'client_id' => $client->id,
            'property' => 'purchased_minutes_table',
            'user_id' => null,
            'user_name' => 'Système',
        ]);

        $data = $client->toArray();
        $data['included_minutes_table'] = 200;

        $this->actingAs($user, 'web')
            ->put('https://calliopee.test/nova-api/clients/' . $client->id . '?viaResource=&viaResourceId=&viaRelationship=&editing=true&editMode=update', $data);

        $this->assertDatabaseHas('client_balance_changes', [
            'client_id' => $client->id,
            'property' => 'included_minutes_table',
            'user_id' => $user->id,
            'user_name' => $user->name,
        ]);
    }

    /** @test */
    public function it_doesnt_logs_user_if_its_not_a_nova_request()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create();

        $this->actingAs($user, 'web');

        $client->update(['included_minutes_table' => 200]);

        $this->assertDatabaseHas('client_balance_changes', [
            'client_id' => $client->id,
            'property' => 'included_minutes_table',
            'user_id' => null,
            'user_name' => 'Système',
        ]);
    }

    /** @test */
    public function it_doesnt_log_anythins_if_the_balance_is_not_changed()
    {
        $client = Client::factory()->create();
        $this->assertDatabaseCount('client_balance_changes', 4);

        $client->update(['first_name' => 'John']);
        $this->assertDatabaseCount('client_balance_changes', 4);

        $client->update(['included_minutes_table' => 200]);
        $this->assertDatabaseCount('client_balance_changes', 5);
    }

    /** @test */
    public function it_logs_the_balance_when_a_shift_is_finished()
    {
        $client = Client::factory()->create([
            'purchased_minutes_table' => 100,
            'purchased_minutes_office' => 100,
            'included_minutes_table' => 100,
            'included_minutes_office' => 100,
        ]);

        $workspace = Workspace::factory()->create([
            'workspace_type_id' => WorkspaceType::factory()->create(['minutes_type' => 'table'])->id,
        ]);

        $shift = Shift::factory()->create([
            'client_id' => $client->id,
            'started_at' => now()->subHour(),
            'ended_at' => now()->addHour(),
            'workspace_id' => $workspace->id,
            'status' => 'running',
            'is_reservation' => true,
        ]);

        DB::table('client_balance_changes')->truncate();

        $this->actingAs($client)->post('/scan', ['code' => $workspace->qr_code]);

        $this->assertDatabaseHas('client_balance_changes', [
            'client_id' => $client->id,
            'operation' => 'debit',
            'property' => 'purchased_minutes_table',
            'amount' => '-20',
        ]);

        $this->assertDatabaseHas('client_balance_changes', [
            'client_id' => $client->id,
            'operation' => 'debit',
            'property' => 'included_minutes_table',
            'amount' => '-100',
        ]);
    }

    /** @test */
    public function it_logs_when_we_charge_time_to_client()
    {
        $client = Client::factory()->create([
            'purchased_minutes_table' => 100,
            'purchased_minutes_office' => 100,
            'included_minutes_table' => 100,
            'included_minutes_office' => 100,
        ]);

        DB::table('client_balance_changes')->truncate();

        ChargeTimeToClient::make($client)->run(150, 'table');

        $this->assertDatabaseHas('client_balance_changes', [
            'client_id' => $client->id,
            'operation' => 'debit',
            'property' => 'included_minutes_table',
            'amount' => '-100',
        ]);

        $this->assertDatabaseHas('client_balance_changes', [
            'client_id' => $client->id,
            'operation' => 'debit',
            'property' => 'purchased_minutes_table',
            'amount' => '-50',
        ]);
    }
}
