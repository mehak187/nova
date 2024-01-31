<?php

namespace App\Console\Commands;

use App\Models\Client;
use Illuminate\Console\Command;

class SendCreditAlertNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'client:send-credit-alert-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send credit alert notification to clients';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Client::chunk(100, function ($clients) {
            foreach ($clients as $client) {
                if ($client->creditAlertThresholdReached() && ! $client->credit_alert_sent) {
                    $client->notify(new \App\Notifications\CreditAlert(
                        $client->purchased_minutes_office + $client->purchased_minutes_table,
                        $client->included_minutes_office + $client->included_minutes_table
                    ));

                    $client->update(['credit_alert_sent' => true]);
                }
            }
        });

        return Command::SUCCESS;
    }
}
