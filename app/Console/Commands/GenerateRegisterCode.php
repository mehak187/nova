<?php

namespace App\Console\Commands;

use App\Models\Client;
use Illuminate\Support\Str;
use Illuminate\Console\Command;

class GenerateRegisterCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'client:generate-code';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate register code';

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
        Client::chunk(100, function ($clients) {
            foreach ($clients as $client) {
                $client->update([
                    'register_code' => Str::random(30),
                ]);
            }
        });

        return 0;
    }
}
