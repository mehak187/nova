<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('client:refill-minutes')->monthly();
        $schedule->command('shift:merge')->everyMinute();
        $schedule->command('shift:close-expired')->everyMinute();
        $schedule->command('shift:close-running')->dailyAt('20:30');
        $schedule->command('app:renew-access-code')->everyThreeHours();
        $schedule->command('client:send-credit-alert-notification')->everyThreeHours();
        $schedule->command('task:send-notification')->dailyAt('07:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
