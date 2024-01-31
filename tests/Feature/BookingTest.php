<?php

namespace Tests\Feature;

use App\Mail\BookingCancelled as MailBookingCancelled;
use App\Mail\BookingCreated;
use App\Mail\BookingUpdated;
use App\Models\Client;
use App\Models\Shift;
use App\Models\User;
use App\Models\Workspace;
use App\Notifications\BookingCancelled;
use App\Notifications\BookingConfirmed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Spatie\TestTime\TestTime;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_shift()
    {
        $client = Client::factory()->create();

        $this->actingAs($client);

        $this->assertDatabaseCount('shifts', 0);

        $this->post('/bookings', [
            'shift_id' => null,
            'started_at' => now()->addDay(),
            'ended_at' => now()->addDay()->addHour(),
            'workspace_id' => Workspace::factory()->create()->id,
        ]);

        $this->assertDatabaseCount('shifts', 1);
    }

    /** @test */
    public function it_sends_a_notification_to_the_client_when_booking_a_new_shift()
    {
        Notification::fake();

        $client = Client::factory()->create();

        $this->actingAs($client);

        $this->post('/bookings', [
            'shift_id' => null,
            'started_at' => now()->addDay(),
            'ended_at' => now()->addDay()->addHour(),
            'workspace_id' => Workspace::factory()->create()->id,
        ]);

        Notification::assertSentTo(
            $client, BookingConfirmed::class
        );
    }

    /** @test */
    public function it_sends_a_notification_to_the_client_when_updating_a_shift()
    {
        Notification::fake();

        $client = Client::factory()->create();
        $shift = Shift::factory()->create([
            'client_id' => $client->id,
            'status' => 'booked',
            'is_reservation' => true,
            'started_at' => now()->addDay(),
            'ended_at' => now()->addDay()->addHour(),
            'workspace_id' => Workspace::factory()->create()->id,
        ]);

        $this->actingAs($client);

        $this->post('/bookings', [
            'shift_id' => $shift->id,
            'started_at' => now()->addDay(),
            'ended_at' => now()->addDay()->addHour(),
            'workspace_id' => Workspace::factory()->create()->id,
        ]);

        Notification::assertSentTo(
            $client, BookingConfirmed::class
        );
    }

    /** @test */
    public function it_sends_a_new_booking_confirmation_email_to_admins()
    {
        Mail::fake();

        $client = Client::factory()->create();

        $this->actingAs($client);

        $this->post('/bookings', [
            'shift_id' => null,
            'started_at' => now()->addDay(),
            'ended_at' => now()->addDay()->addHour(),
            'workspace_id' => Workspace::factory()->create()->id,
        ]);

        Mail::assertSent(BookingCreated::class, function (BookingCreated $mail) {
            return $mail->to[0]['address'] === config('calliopee.admin_email');
        });
    }

    /** @test */
    public function it_sends_an_updated_booking_confirmation_email_to_admins()
    {
        Mail::fake();

        $client = Client::factory()->create();
        $shift = Shift::factory()->create([
            'client_id' => $client->id,
            'status' => 'booked',
            'is_reservation' => true,
            'started_at' => now()->addDay(),
            'ended_at' => now()->addDay()->addHour(),
            'workspace_id' => Workspace::factory()->create()->id,
        ]);

        $this->actingAs($client);

        $this->post('/bookings', [
            'shift_id' => $shift->id,
            'started_at' => now()->addDay(),
            'ended_at' => now()->addDay()->addHour(),
            'workspace_id' => Workspace::factory()->create()->id,
        ]);

        Mail::assertSent(BookingUpdated::class, function (BookingUpdated $mail) {
            return $mail->to[0]['address'] === config('calliopee.admin_email');
        });
    }

    /** @test */
    public function it_send_a_cancel_confirmation_to_client()
    {
        Notification::fake();

        $client = Client::factory()->create();

        $this->actingAs($client);

        $shift = Shift::factory()->create([
            'client_id' => $client->id,
            'started_at' => now()->addDay(),
            'ended_at' => now()->addDay()->addHour(),
            'workspace_id' => Workspace::factory()->create()->id,
            'status' => 'booked',
            'is_reservation' => true,
        ]);

        $this
            ->actingAs($client)
            ->delete('/bookings/' . $shift->id);

        Notification::assertSentTo(
            $client, BookingCancelled::class
        );
    }

    /** @test */
    public function it_send_a_cancel_confirmation_to_admins()
    {
        Mail::fake();

        $client = Client::factory()->create();

        $this->actingAs($client);

        $shift = Shift::factory()->create([
            'client_id' => $client->id,
            'started_at' => now()->addDay(),
            'ended_at' => now()->addDay()->addHour(),
            'workspace_id' => Workspace::factory()->create()->id,
            'status' => 'booked',
            'is_reservation' => true,
        ]);

        $this
            ->actingAs($client)
            ->delete('/bookings/' . $shift->id);

        Mail::assertSent(MailBookingCancelled::class, function (MailBookingCancelled $mail) {
            return $mail->to[0]['address'] === config('calliopee.admin_email');
        });
    }

    /** @test */
    public function shifts_are_properly_merged()
    {
        $client = Client::factory()->create();

        $this->actingAs($client);

        $workspace = Workspace::factory()->create();

        $shift = Shift::factory()->create([
            'client_id' => $client->id,
            'started_at' => now()->addMinutes(15),
            'ended_at' => now()->addHour()->addMinutes(15),
            'workspace_id' => $workspace->id,
            'status' => 'booked',
            'is_reservation' => true,
        ]);

        $this->post('/scan', [
            'code' => $workspace->qr_code,
        ]);

        TestTime::addMinutes(15);

        $this->artisan('shift:merge');

        TestTime::addMinutes(15);

        $this->post('/scan', [
            'code' => $workspace->qr_code,
        ]);

        $this->assertEquals(75, $shift->refresh()->duration_in_minutes);
    }

    /** @test */
    public function it_checks_for_conflicts()
    {
        $client = Client::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user, 'web');

        $workspace = Workspace::factory()->create();

        $shift = Shift::factory()->create([
            'client_id' => $client->id,
            'started_at' => now()->setTime(13, 00, 00),
            'ended_at' => now()->setTime(14, 00, 00),
            'workspace_id' => $workspace->id,
            'status' => 'booked',
            'is_reservation' => true,
        ]);

        $this->withoutExceptionHandling();

        $this->post('/nova-vendor/booking-calendar/store', [
            'client_id' => $client->id,
            'shift_id' => null,
            'started_at' => now()->setTime(13, 30, 00),
            'ended_at' => now()->setTime(14, 30, 00),
            'workspace_id' => $workspace->id,
        ])->assertStatus(422);

        $this->post('/nova-vendor/booking-calendar/store', [
            'client_id' => $client->id,
            'shift_id' => null,
            'started_at' => now()->setTime(12, 30, 00),
            'ended_at' => now()->setTime(13, 30, 00),
            'workspace_id' => $workspace->id,
        ])->assertStatus(422);

        $this->post('/nova-vendor/booking-calendar/store', [
            'client_id' => $client->id,
            'shift_id' => null,
            'started_at' => now()->setTime(12, 30, 00),
            'ended_at' => now()->setTime(14, 30, 00),
            'workspace_id' => $workspace->id,
        ])->assertStatus(422);

        $this->post('/nova-vendor/booking-calendar/store', [
            'client_id' => $client->id,
            'shift_id' => null,
            'started_at' => now()->setTime(14, 00, 00),
            'ended_at' => now()->setTime(14, 30, 00),
            'workspace_id' => $workspace->id,
        ])->assertStatus(200);

        $this->post('/nova-vendor/booking-calendar/store', [
            'client_id' => $client->id,
            'shift_id' => null,
            'started_at' => now()->setTime(12, 30, 00),
            'ended_at' => now()->setTime(13, 00, 00),
            'workspace_id' => $workspace->id,
        ])->assertStatus(200);
    }
}
