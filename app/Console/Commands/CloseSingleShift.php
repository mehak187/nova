<?php

namespace App\Console\Commands;

use App\Actions\CloseShift;
use App\Models\Shift;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CloseSingleShift extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shift:close {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close a shift';

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
        $shift = Shift::find($this->argument('id'));

        CloseShift::make($shift)->run($shift->ended_at);

        return 0;
    }
}
