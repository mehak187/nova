<?php

namespace Tests\Feature;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_logs_user_out()
    {
        $client = Client::factory()->create();

        $this->actingAs($client);

        $this->assertTrue(auth('app')->check());

        $this->post('/logout');

        $this->assertFalse(auth('app')->check());
    }
}
