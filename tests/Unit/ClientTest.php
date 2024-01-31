<?php

namespace Tests\Unit;

use App\Actions\ChargeTimeToClient;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_charges_included_minutes_before_purchased_ones()
    {
        $client = Client::factory()->create([
            'purchased_minutes_table' => 50,
            'included_minutes_table' => 100,
            'has_subscription' => false,
            'is_resident' => false,
            'is_235' => true,
        ]);

        $this->assertEquals(50, $client->purchased_minutes_table);
        $this->assertEquals(100, $client->included_minutes_table);

        $this->assertEquals(0, ChargeTimeToClient::make($client)->run(50, 'table'));
        $this->assertEquals(50, $client->purchased_minutes_table);
        $this->assertEquals(50, $client->included_minutes_table);

        $this->assertEquals(0, ChargeTimeToClient::make($client)->run(50, 'table'));
        $this->assertEquals(50, $client->purchased_minutes_table);
        $this->assertEquals(0, $client->included_minutes_table);

        $this->assertEquals(0, ChargeTimeToClient::make($client)->run(50, 'table'));
        $this->assertEquals(0, $client->purchased_minutes_table);
        $this->assertEquals(0, $client->included_minutes_table);

        $this->assertEquals(50, ChargeTimeToClient::make($client)->run(50, 'table'));
    }

    /** @test */
    public function it_does_the_same_for_office_minutes()
    {
        $client = Client::factory()->create([
            'purchased_minutes_office' => 50,
            'included_minutes_office' => 100,
            'has_subscription' => false,
            'is_resident' => false,
            'is_235' => true,
        ]);

        $this->assertEquals(50, $client->purchased_minutes_office);
        $this->assertEquals(100, $client->included_minutes_office);

        $this->assertEquals(0, ChargeTimeToClient::make($client)->run(50, 'office'));
        $this->assertEquals(50, $client->purchased_minutes_office);
        $this->assertEquals(50, $client->included_minutes_office);

        $this->assertEquals(0, ChargeTimeToClient::make($client)->run(50, 'office'));
        $this->assertEquals(50, $client->purchased_minutes_office);
        $this->assertEquals(0, $client->included_minutes_office);

        $this->assertEquals(0, ChargeTimeToClient::make($client)->run(50, 'office'));
        $this->assertEquals(0, $client->purchased_minutes_office);
        $this->assertEquals(0, $client->included_minutes_office);

        $this->assertEquals(50, ChargeTimeToClient::make($client)->run(50, 'office'));
    }
}
