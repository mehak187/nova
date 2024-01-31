<?php

namespace App\Providers;

use App\Services\Exivo;
use Illuminate\Support\ServiceProvider;

class ExivoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        app()->singleton(Exivo::class, function () {
            return new Exivo(
                config('services.exivo.username'),
                config('services.exivo.password'),
                config('services.exivo.site_id'),
            );
        });
    }
}
