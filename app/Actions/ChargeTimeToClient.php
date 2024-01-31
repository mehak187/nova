<?php

namespace App\Actions;

use App\Models\Client;

class ChargeTimeToClient
{
    public $client;

    /**
     * Subtract minutes to client
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public static function make(Client $client)
    {
        return new self($client);
    }

    /**
     * Run the action
     *
     * @param integer $duration
     * @param string $type
     * @return integer The remaining time
     */
    public function run(float $duration, string $type)
    {
        $purchasedFieldName = 'purchased_minutes_' . $type;
        $includedFieldName = 'included_minutes_' . $type;

        $purchasedMinutes = $this->client->{$purchasedFieldName};
        $includedMinutes = $this->client->{$includedFieldName};

        if ($purchasedMinutes === 0 && $includedMinutes === 0) {
            return $duration;
        }

        if ($includedMinutes < $duration) {
            $this->client->update([$includedFieldName => 0]);

            $duration -= $includedMinutes;
        } else {
            $this->client->decrement($includedFieldName, $duration);

            $duration = 0;
        }

        if ($purchasedMinutes < $duration) {
            $this->client->update([$purchasedFieldName => 0]);

            $duration -= $purchasedMinutes;
        } else {
            $this->client->decrement($purchasedFieldName, $duration);

            $duration = 0;
        }

        return $duration;
    }
}
