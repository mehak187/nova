<?php

namespace App\Console\Commands;

use App\Actions\CloseShift;
use App\Models\Shift;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CloseShiftsAtEndOfDay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shift:close-running';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Closes all running shifts';

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
        $shifts = Shift::query()
            ->whereNull('ended_at')
            ->where('status', 'running')
            ->get();

        foreach ($shifts as $shift) {
            Log::info('End of day closing: ' . $shift->id . ' at ' . now());

            CloseShift::make($shift)->run(now());
        }

        return 0;
    }
}
