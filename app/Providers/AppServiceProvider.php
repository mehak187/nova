<?php

namespace App\Providers;

use App\Models\Task;
use App\Models\Client;
use App\Models\Document;
use App\Observers\TaskObserver;
use App\Observers\ClientObserver;
use App\Observers\DocumentObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Client::observe(ClientObserver::class);
        Document::observe(DocumentObserver::class);
        Task::observe(TaskObserver::class);

        View::composer('*', function ($view) {
            if (auth('app')->check()) {
                $view->with('activeShiftsCount', auth('app')->user()->shifts()->running()->count());
            }
        });
    }
}
