<?php

use App\Models\Client;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. You're free to add
| as many additional routes to this file as your tool may require.
|
*/

Route::get('/{client}/total', function (Client $client) {
    return $client
        ->shifts()
        ->whereIn('status', ['finished', 'cancelled'])
        ->whereNull('paid_at')
        ->sum('total_amount_due');
});

Route::post('/{client}/pay', function (Client $client) {
    return $client
        ->shifts()
        ->whereIn('status', ['finished', 'cancelled'])
        ->whereNull('paid_at')
        ->update([
            'paid_at' => now(),
        ]);
});
