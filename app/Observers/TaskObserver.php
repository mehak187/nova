<?php

namespace App\Observers;

use App\Models\Task;
use App\Models\User;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     *
     * @param  \App\Models\Task  $task
     * @return void
     */
    public function created(Task $task)
    {
        if (!empty($task->assignee_id) && $task->assignee_id !== auth('web')->id()) {
            $task->assignee->notify(new \App\Notifications\TaskAssigned($task));
        }
    }

    /**
     * Handle the Task "updated" event.
     *
     * @param  \App\Models\Task  $task
     * @return void
     */
    public function updated(Task $task)
    {
        if ($task->isDirty('assignee_id') && !empty($task->assignee_id) && $task->assignee_id !== auth('web')->id()) {
            User::findOrFail($task->assignee_id)->notify(new \App\Notifications\TaskAssigned($task));
        }

        if ($task->isDirty('status') && in_array($task->status, ['in_progress', 'closed']) && !empty($task->client_id)) {
            $task->client->notify(new \App\Notifications\SupportTicketStatusChanged($task));
        }
    }
}
