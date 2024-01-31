<?php

namespace App\Observers;

use App\Models\Client;
use Illuminate\Support\Str;

class ClientObserver
{
    private $balanceProperties = [
        'purchased_minutes_table',
        'purchased_minutes_office',
        'included_minutes_table',
        'included_minutes_office',
    ];

    /**
     * Handle the Client "created" event.
     *
     * @param  \App\Models\Client  $client
     * @return void
     */
    public function created(Client $client)
    {
        foreach ($this->balanceProperties as $property) {
            if ($client->isDirty($property)) {
                $oldAmount = $client->getOriginal($property) ?? 0;
                $newAmount = $client->$property ?? 0;
                $user = auth('web')->user();
                $isNova = request()->route()
                    ? Str::startsWith(request()->route()->uri(), 'nova-api')
                    : null;

                $client->balanceChanges()->create([
                    'operation' => $oldAmount > $newAmount ? 'debit' : 'credit',
                    'property' => $property,
                    'amount' => $newAmount - $oldAmount,
                    'user_id' => $isNova ? optional($user)->id : null,
                    'user_name' => $isNova ? optional($user)->name ?? 'Système' : 'Système',
                ]);
            }
        }
    }

    /**
     * Handle the Client "updated" event.
     *
     * @param  \App\Models\Client  $client
     * @return void
     */
    public function updated(Client $client)
    {
        foreach ($this->balanceProperties as $property) {
            if ($client->isDirty($property)) {
                $oldAmount = $client->getOriginal($property) ?? 0;
                $newAmount = $client->$property ?? 0;
                $user = auth('web')->user();
                $isNova = request()->route()
                    ? Str::startsWith(request()->route()->uri(), 'nova-api')
                    : null;

                $client->balanceChanges()->create([
                    'operation' => $oldAmount > $newAmount ? 'debit' : 'credit',
                    'property' => $property,
                    'amount' => $newAmount - $oldAmount,
                    'user_id' => $isNova ? optional($user)->id : null,
                    'user_name' => $isNova ? optional($user)->name ?? 'Système' : 'Système',
                ]);
            }

            if ($client->credit_alert_sent && !$client->creditAlertThresholdReached()) {
                $client->update(['credit_alert_sent' => false]);
            }
        }
    }

    /**
     * Handle the Client "deleting" event.
     *
     * @param  \App\Models\Client  $client
     * @return void
     */
    public function deleting(Client $client)
    {
        $client->shifts()->delete();
    }

    /**
     * Handle the Client "deleted" event.
     *
     * @param  \App\Models\Client  $client
     * @return void
     */
    public function deleted(Client $client)
    {
        //
    }

    /**
     * Handle the Client "restored" event.
     *
     * @param  \App\Models\Client  $client
     * @return void
     */
    public function restored(Client $client)
    {
        //
    }

    /**
     * Handle the Client "force deleted" event.
     *
     * @param  \App\Models\Client  $client
     * @return void
     */
    public function forceDeleted(Client $client)
    {
        //
    }
}
