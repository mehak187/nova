<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\User;
use Illuminate\Console\Command;

class SendTaskAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:send-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send task alerts to assignees when due date is reached';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Sending task alerts...');

        $tasks = Task::query()
            ->whereNotNull('assignee_id')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->whereIn('status', ['draft', 'open', 'in_progress'])
            ->get();

        $tasks->groupBy('assignee_id')->each(function ($tasks, $assigneeId) {
            $assignee = User::find($assigneeId);

            $assignee->notify(new \App\Notifications\TaskDueDateReached($tasks));
        });

        return Command::SUCCESS;
    }
}
