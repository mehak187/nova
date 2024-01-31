<?php

namespace App\Console\Commands;

use App\Models\Client;
use Illuminate\Console\Command;

class RefillIncludedMinutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'client:refill-minutes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refill included minutes for clients "235"';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Client::where('is_235', true)->where('refill_type', 'table')->chunkById(50, function ($clients) {
            $clients->each->update([
                'included_minutes_table' => 4800,
            ]);
        });

        Client::where('is_235', true)->where('refill_type', 'office')->chunkById(50, function ($clients) {
            $clients->each->update([
                'included_minutes_office' => 960,
            ]);
        });

        return 0;
    }
}
