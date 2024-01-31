<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RefillIncludedMinutesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_refills_included_minutes()
    {
        $officeClient = Client::factory()->create([
            'is_235' => true,
            'included_minutes_table' => 0,
            'included_minutes_office' => 0,
            'refill_type' => 'office'
        ]);

        $tableClient = Client::factory()->create([
            'is_235' => true,
            'included_minutes_table' => 0,
            'included_minutes_office' => 0,
            'refill_type' => 'table'
        ]);

        $this->artisan('client:refill-minutes');

        $officeClient->refresh();
        $tableClient->refresh();

        $this->assertEquals(960, $officeClient->included_minutes_office);
        $this->assertEquals(0, $officeClient->included_minutes_table);

        $this->assertEquals(0, $tableClient->included_minutes_office);
        $this->assertEquals(4800, $tableClient->included_minutes_table);
    }

    /** @test */
    public function it_logs_refilled_minutes_in_client_balance_changes_table()
    {
        $officeClient = Client::factory()->create([
            'is_235' => true,
            'included_minutes_office' => 0,
            'refill_type' => 'office'
        ]);

        DB::table('client_balance_changes')->truncate();

        $this->artisan('client:refill-minutes');

        $this->assertDatabaseHas('client_balance_changes', [
            'client_id' => $officeClient->id,
            'operation' => 'credit',
            'property' => 'included_minutes_office',
            'amount' => '960',
        ]);
    }

    /** @test */
    public function it_doesnt_logs_the_nova_user_name()
    {
        $officeClient = Client::factory()->create([
            'is_235' => true,
            'included_minutes_office' => 0,
            'refill_type' => 'office'
        ]);

        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        DB::table('client_balance_changes')->truncate();

        $this->artisan('client:refill-minutes');

        $this->assertDatabaseHas('client_balance_changes', [
            'client_id' => $officeClient->id,
            'operation' => 'credit',
            'property' => 'included_minutes_office',
            'amount' => '960',
            'user_id' => null,
            'user_name' => 'Système',
        ]);
    }
}
