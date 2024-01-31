<?php

namespace Calliopee\BookingCalendar;

use App\Models\Client;
use Laravel\Nova\Card;

class BookingCalendar extends Card
{
    /**
     * The width of the card (1/3, 1/2, or full).
     *
     * @var string
     */
    public $width = 'full';

    /**
     * Get the component name for the element.
     *
     * @return string
     */
    public function component()
    {
        return 'booking-calendar';
    }

    public function withClients()
    {
        $clients = Client::select('id', 'first_name', 'last_name')
            ->orderBy('last_name', 'ASC')
            ->get()
            // ->keyBy('id')
            ->map(function ($client) {
                return $client->only('id', 'full_name_reversed');
            });

        return $this->withMeta([
            'clients' => $clients,
        ]);
    }

    public function withAlerts()
    {
        return $this->withMeta([
            'clientsOnAlert' => Client::query()
                ->orWhere(function ($query) {
                    $query->whereRaw('purchased_minutes_table + purchased_minutes_office < 600')
                        ->whereRaw('purchased_minutes_table + purchased_minutes_office > 0');
                })
                ->orWhere(function ($query) {
                    $query->whereRaw('included_minutes_table + included_minutes_office < 300')
                        ->whereRaw('included_minutes_table + included_minutes_office > 0');
                })
                ->orderByRaw('purchased_minutes_table + purchased_minutes_office + included_minutes_table + included_minutes_office')
                ->get()
        ]);
    }
}
