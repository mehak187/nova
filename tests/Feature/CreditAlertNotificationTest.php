<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Client;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreditAlertNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_sends_an_email_to_the_client_when_credit_reaches_alert_threshold()
    {
        Notification::fake();

        // The threshold is 600 purchased minutes or 300 included minutes.
        $clientWithoutAlert = Client::factory()->create(['purchased_minutes_table' => 400,'purchased_minutes_office' => 400]);
        $clientWithoutAlert2 = Client::factory()->create(['included_minutes_table' => 200,'included_minutes_office' => 200]);
        $clientWithAlert = Client::factory()->create(['purchased_minutes_table' => 100,'purchased_minutes_office' => 100]);
        $clientWithAlert2 = Client::factory()->create(['included_minutes_table' => 100,'included_minutes_office' => 100]);

        $this->artisan('client:send-credit-alert-notification');

        Notification::assertNotSentTo($clientWithoutAlert, \App\Notifications\CreditAlert::class);
        Notification::assertNotSentTo($clientWithoutAlert2, \App\Notifications\CreditAlert::class);
        Notification::assertSentTo($clientWithAlert, \App\Notifications\CreditAlert::class);
        Notification::assertSentTo($clientWithAlert2, \App\Notifications\CreditAlert::class);

        $this->assertEquals(0, $clientWithoutAlert->refresh()->credit_alert_sent);
        $this->assertEquals(0, $clientWithoutAlert2->refresh()->credit_alert_sent);
        $this->assertEquals(1, $clientWithAlert->refresh()->credit_alert_sent);
        $this->assertEquals(1, $clientWithAlert2->refresh()->credit_alert_sent);
    }

    /** @test */
    public function it_doesnt_send_a_second_notification_until_credit_gets_higher_than_the_threshold()
    {
        Notification::fake();

        $client = Client::factory()->create(['purchased_minutes_table' => 100, 'purchased_minutes_office' => 100]);

        $this->artisan('client:send-credit-alert-notification');
        Notification::assertSentTo($client, \App\Notifications\CreditAlert::class);
        Notification::assertCount(1, \App\Notifications\CreditAlert::class);

        $this->artisan('client:send-credit-alert-notification');
        Notification::assertCount(1, \App\Notifications\CreditAlert::class);

        $client->refresh();

        $client->update(['purchased_minutes_table' => 600]);
        $this->assertEquals(0, $client->refresh()->credit_alert_sent);
    }
}
