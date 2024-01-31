<?php

namespace App\Actions;

use App\Models\Shift;
use Illuminate\Support\Facades\DB;

class CancelShift
{
    public $shift;

    /**
     * Cancel shift
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

    public function run()
    {
        if ($this->shift->is_cancellable) {
            $this->cancel();
        } else {
            $this->chargeAndCancel();
        }
    }

    private function cancel()
    {
        $this->shift->update([
            'status' => 'cancelled',
            'amount_due' => 0,
            'vat' => config('calliopee.vat'),
            'total_amount_due' => 0,
            'paid_at' => now(),
        ]);
    }

    private function chargeAndCancel()
    {
        DB::transaction(function () {
            $duration = CalculateDuration::make(
                $this->shift->started_at,
                $this->shift->ended_at,
                $this->shift->workspace->minute_factor
            )->run();

            $duration *= 0.8;

            $durationLeft = ChargeTimeToClient::make($this->shift->client)
                ->run($duration, $this->shift->workspace->type->minutes_type);

            $amountWithoutVat = CalculateAmount::make($this->shift, $durationLeft)->run(false);
            $amountWithVat = CalculateAmount::make($this->shift, $durationLeft)->run(true);

            $this->shift->update([
                'prepaid_duration' => $duration - $durationLeft,
                'status' => 'cancelled',
                'amount_due' => $amountWithoutVat,
                'vat' => config('calliopee.vat'),
                'total_amount_due' => $amountWithVat,
                'paid_at' => $amountWithVat == 0 ? now() : null,
            ]);
        });
    }
}
