<?php

namespace Tests\Feature;

use App\Models\Client;
use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

class TaskNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_sends_an_email_when_a_customer_creates_a_task()
    {
        Mail::fake();

        $client = Client::factory()->create();

        $this->actingAs($client);

        $this->post('/support', [
            'title' => 'Test',
            'description' => 'Test',
        ]);

        Mail::assertSent(\App\Mail\NewTaskCreatedByClient::class, function ($mail) {
            return $mail->hasTo('mail@calliopee.ch');
        });
    }

    /** @test */
    public function it_sends_a_notification_to_assignee_when_due_date_is_reached()
    {
        Notification::fake();

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Task::factory()->create([
            'due_date' => now()->addDay(),
            'assignee_id' => $user1->id,
        ]);

        Task::factory()->create([
            'due_date' => now()->subDay(),
            'assignee_id' => $user2->id,
        ]);

        $this->artisan('task:send-notification');

        Notification::assertNotSentTo($user1, \App\Notifications\TaskDueDateReached::class);
        Notification::assertSentTo($user2, \App\Notifications\TaskDueDateReached::class);
    }

    /** @test */
    public function it_sends_a_notification_when_task_is_assigned()
    {
        Notification::fake();

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $task = Task::factory()->create([
            'due_date' => now()->addDay(),
            'assignee_id' => $user1->id,
        ]);

        Notification::assertSentTo($user1, \App\Notifications\TaskAssigned::class);
        Notification::assertNotSentTo($user2, \App\Notifications\TaskAssigned::class);

        $task->update([
            'assignee_id' => $user2->id,
        ]);

        Notification::assertSentTo($user2, \App\Notifications\TaskAssigned::class);
    }
}
