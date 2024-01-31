<?php

namespace App\Actions;

use App\Models\Shift;
use Carbon\Carbon;

class CalculateDuration
{
    public $start;

    public $end;

    public $minuteFactor;

    public $eveningRateTime;

    /**
     * Cancel shift
     *
     * @param Shift $shift
     */
    public function __construct(Carbon $start, Carbon $end, float $minuteFactor)
    {
        $this->start = $start->toImmutable();
        $this->end = $end->toImmutable();
        $this->minuteFactor = $minuteFactor;
        $this->eveningRateTime = $start->copy()->setTime(18, 0, 0)->toImmutable();
    }

    public static function make(Carbon $start, Carbon $end, float $minuteFactor)
    {
        return new self($start, $end, $minuteFactor);
    }

    public function run()
    {
        if ($this->start->isSaturday()) {
            $duration = $this->start->diffInMinutes($this->end) * $this->minuteFactor * 1.5;
        } elseif ($this->end->isAfter($this->eveningRateTime)) {
            if ($this->start->isAfter($this->eveningRateTime)) {
                $duration = $this->start->diffInMinutes($this->end) * $this->minuteFactor * 1.25;
            } else {
                $before18 = $this->start->diffInMinutes($this->eveningRateTime);
                $after18 = $this->end->diffInMinutes($this->eveningRateTime);

                $duration = ($before18 + $after18 * 1.25) * $this->minuteFactor;
            }
        } else {
            $duration = $this->start->diffInMinutes($this->end) * $this->minuteFactor;
        }

        return $duration;
    }
}
