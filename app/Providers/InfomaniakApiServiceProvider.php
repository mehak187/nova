<?php

namespace App\Providers;

use App\Services\Infomaniak\InfomaniakApi;
use Illuminate\Support\ServiceProvider;

class InfomaniakApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(InfomaniakApi::class, function () {
            return new InfomaniakApi(env('INFOMANIAK_API_TOKEN'));
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
