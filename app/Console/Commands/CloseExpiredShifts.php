<?php

namespace App\Console\Commands;

use App\Actions\CloseShift;
use App\Models\Shift;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CloseExpiredShifts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shift:close-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for expired bookings and closes them';

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
        // On prend tous les shifts qui ont commencé aujourd'hui
        $shifts = Shift::query()
            ->whereDate('started_at', now())
            ->get();

        foreach ($shifts as $shift) {
            // Si le statut est 'booked' et que la date de fin est dépassée
            if ($shift->status === 'booked' && $shift->ended_at->lessThanOrEqualTo(now())) {
                // Alors la personne ne s'est pas présentée
                // On ferme le shift
                Log::info('Closing expired shift: ' . $shift->id . ' at ' . $shift->ended_at);
                CloseShift::make($shift)->run($shift->ended_at);

            // Si le status est 'running'
            } elseif ($shift->status === 'running') {
                // Alors la personne est actuellement présente

                // On récupère les shifts en conflits
                // càd ceux qui commencent entre la date de début du shift et la date actuelle dans la même salle pour un autre client
                $conflicts = Shift::query()
                    ->where('status', 'booked')
                    ->where('started_at', '>', $shift->started_at)
                    ->where('started_at', '<=', now())
                    ->where('workspace_id', $shift->workspace_id)
                    ->where('client_id', '!=', $shift->client_id)
                    ->where('id', '!=', $shift->id)
                    ->pluck('id');

                // S'il existe des conflits
                if ($conflicts->count()) {
                    // On ferme le shift en cours
                    try {
                        Log::info('Closing conflicting shift: ' . $shift->id . ' in conflict with ' . $conflicts->join(', ') . ' at ' . ($shift->ended_at ?? now()));
                    } catch (\Exception $e) {
                        Log::info('Exception when logging');
                    }
                    CloseShift::make($shift)->run(now());
                }
            }
        }

        return 0;
    }
}
