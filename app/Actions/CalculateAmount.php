<?php

namespace App\Actions;

use App\Models\Shift;

class CalculateAmount
{
    public $shift;

    public $time;

    /**
     * Cancel shift
     *
     * @param Shift $shift
     */
    public function __construct(Shift $shift, $time)
    {
        $this->shift = $shift;
        $this->time = $time;
    }

    public static function make(Shift $shift, $time)
    {
        return new self($shift, $time);
    }

    public function run($addVat)
    {
        $paysSubscriptionPrice = $this->shift->client->has_subscription || $this->shift->client->is_resident;

        $hourlyPrice = $paysSubscriptionPrice
            ? $this->shift->workspace->type->subscription_price
            : $this->shift->workspace->type->base_price;

        $amountDue = ($this->time / 60) * $hourlyPrice;

        if ($addVat) {
            $vat = 1 + (config('calliopee.vat') / 100);

            $amountDue *= $vat;
        }

        return $amountDue;
    }
}
