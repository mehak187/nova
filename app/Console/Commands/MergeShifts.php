<?php

namespace App\Console\Commands;

use App\Models\Shift;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MergeShifts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shift:merge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Quand un utilisateur démarre un shift avant la date de réservation, cela crée un nouveau shift. Cette commande permet de merger les deux';

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
        $shifts = Shift::where('status', 'running')->get();

        foreach ($shifts as $shift) {
            $bookedShift = Shift::query()
                ->where('status', 'booked')
                ->whereDate('started_at', $shift->started_at->format('Y-m-d'))
                ->where('started_at', '<=', now())
                ->where('client_id', $shift->client_id)
                ->where('workspace_id', $shift->workspace_id)
                ->first();

            if (empty($bookedShift)) {
                continue;
            }

            Log::info('Merging shift: ' . $shift->id . ' into ' . $bookedShift->id);

            DB::transaction(function () use ($bookedShift, $shift) {
                $bookedShift->update([
                    'started_at' => $shift->started_at,
                    'status' => 'running',
                ]);

                // TODO: A vérifier si c'est nécessaire
                // $shift->scans()->update([
                //     'shift_id' => $bookedShift->id,
                // ]);

                $shift->update(['status' => 'Fusionné']);
                $shift->delete();
            });
        }

        return 0;
    }
}
