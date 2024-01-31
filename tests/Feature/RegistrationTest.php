<?php

namespace Tests\Feature;

use App\Mail\NewUserRegistration;
use App\Mail\Welcome;
use Tests\TestCase;
use App\Models\Client;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_registers_a_client()
    {
        $this->assertDatabaseCount('clients', 0);

        $this->post('/register', [
            'first_name' => 'Frédéric',
            'last_name' => 'Marmillod',
            'email' => 'fmarmillod@firstpoint.ch',
            'mobile_phone' => '079 123 45 67',
            'password' => '123456',
            'password_confirmation' => '123456',
        ]);

        $this->assertDatabaseCount('clients', 1);
    }

    /** @test */
    public function it_registers_a_client_from_code()
    {
       $client = Client::factory()->create([
            'first_name' => 'Frédéric',
            'last_name' => 'Marmillod',
            'email' => 'fmarmillod@firstpoint.ch',
            'register_code' => Str::random(30),
            'password' => '',
        ]);

        $this->assertEmpty($client->password);
        $this->assertNotNull($client->register_code);

        $this->post('/register/' . $client->register_code, [
            'first_name' => 'Frédéric',
            'last_name' => 'Marmillod',
            'email' => 'fmarmillod@firstpoint.ch',
            'mobile_phone' => '079 123 45 67',
            'password' => '123456',
            'password_confirmation' => '123456',
        ]);

        $client->refresh();

        $this->assertNotEmpty($client->password);
        $this->assertNull($client->register_code);
    }

    /** @test */
    public function it_sends_an_email_to_admins_when_a_client_is_registered()
    {
        Mail::fake();

        $this->post('/register', [
            'first_name' => 'Frédéric',
            'last_name' => 'Marmillod',
            'email' => 'fmarmillod@firstpoint.ch',
            'mobile_phone' => '079 123 45 67',
            'password' => '123456',
            'password_confirmation' => '123456',
        ]);

        Mail::assertSent(NewUserRegistration::class, function ($mail) {
            return $mail->hasTo('mail@calliopee.ch');
        });
    }

    /** @test */
    public function it_sends_a_welcome_email_the_client()
    {
        Mail::fake();

        $this->post('/register', [
            'first_name' => 'Frédéric',
            'last_name' => 'Marmillod',
            'email' => 'fmarmillod@firstpoint.ch',
            'mobile_phone' => '079 123 45 67',
            'password' => '123456',
            'password_confirmation' => '123456',
        ]);

        Mail::assertSent(Welcome::class, function ($mail) {
            return $mail->hasTo('fmarmillod@firstpoint.ch');
        });
    }
}
