<?php

namespace App\Actions;

use App\Models\ScanLog;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CloseShift
{
    public $shift;

    /**
     * Closes a shift
     *
     * @param Shift $shift
     */
    public function __construct(Shift $shift)
    {
        $this->shift = $shift;
    }

    public static function make(Shift $shift)
    {
        return new self($shift);
    }

    /**
     * Run the action
     *
     * @return void
     */
    public function run($endedAt = null)
    {
        DB::transaction(function () use ($endedAt) {
            if (empty($endedAt)) {
                $now = now()->setSecond(0);

                if ($this->shift->is_reservation && $this->shift->ended_at->isAfter($now)) {
                    $now = $this->shift->ended_at;
                }
            } else {
                $now = $endedAt;
            }

            $duration = CalculateDuration::make(
                $this->shift->started_at,
                $now,
                $this->shift->workspace->minute_factor
            )->run();

            $durationLeft = ChargeTimeToClient::make($this->shift->client)
                ->run($duration, $this->shift->workspace->type->minutes_type);

            $amountWithoutVat = CalculateAmount::make($this->shift, $durationLeft)->run(false);
            $amountWithVat = CalculateAmount::make($this->shift, $durationLeft)->run(true);

            $this->shift->update([
                'ended_at' => $now,
                'prepaid_duration' => $duration - $durationLeft,
                'status' => 'finished',
                'amount_due' => $amountWithoutVat,
                'vat' => config('calliopee.vat'),
                'total_amount_due' => $amountWithVat,
                'paid_at' => $amountWithVat > 0 ? null : now(),
            ]);

            ScanLog::create([
                'client_id' => $this->shift->client_id,
                'workspace_id' => $this->shift->workspace_id,
                'shift_id' => $this->shift->id,
                'direction' => 'OUT',
                'message' => $this->shift->is_reservation ? 'Sortie réservation' : 'Sortie shift',
            ]);
        });
    }
}
