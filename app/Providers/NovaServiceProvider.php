<?php

namespace App\Providers;

use App\Nova\Task;
use App\Nova\User;
use App\Nova\Shift;
use App\Nova\Holiday;
use App\Nova\ScanLog;
use App\Models\Client;
use Laravel\Nova\Nova;
use App\Nova\Workspace;
use App\Nova\DoorOpening;
use App\Nova\OpeningTime;
use App\Nova\WorkspaceType;
use App\Nova\Lenses\MyTasks;
use Illuminate\Http\Request;
use Laravel\Nova\Cards\Help;
use App\Nova\Dashboards\Main;
use App\Nova\MailNotification;
use Laravel\Nova\Menu\MenuItem;
use Calliopee\Calendar\Calendar;
use App\Nova\ClientBalanceChange;
use App\Nova\Client as NovaClient;
use Laravel\Nova\Menu\MenuSection;
use Illuminate\Support\Facades\Gate;
use Calliopee\BookingCalendar\BookingCalendar;
use Laravel\Nova\NovaApplicationServiceProvider;
use App\Nova\Dashboards\Calendar as DashboardsCalendar;
use App\Nova\MailType;
use Laravel\Nova\Menu\MenuGroup;
use Laravel\Nova\Menu\MenuList;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        config(['app.locale' => 'your-desired-locale']);
        Nova::withoutNotificationCenter();
        Nova::withoutThemeSwitcher();
        Nova::remoteScript(asset('js/light.js'));

        Nova::mainMenu(function (Request $request) {
            return [
                MenuSection::dashboard(Main::class)->icon('chart-bar'),

                MenuSection::make('Tâches')->resource(Task::class)->icon('clipboard-list'),

                MenuSection::make('Calliopée', [
                    MenuItem::resource(Workspace::class),
                    MenuItem::resource(WorkspaceType::class),
                    MenuItem::resource(User::class),
                ])->icon('office-building')->collapsable(),

                MenuSection::make('Clients', [
                    MenuItem::resource(NovaClient::class),
                    MenuItem::resource(Shift::class),
                ])->icon('user')->collapsable(),

                MenuSection::make('Accès', [
                    MenuItem::resource(OpeningTime::class),
                    MenuItem::resource(Holiday::class),
                ])->icon('key')->collapsable(),

                MenuSection::make('Journalisation', [
                    MenuItem::resource(MailNotification::class),
                    MenuItem::resource(DoorOpening::class),
                    MenuItem::resource(ScanLog::class),
                    MenuItem::resource(ClientBalanceChange::class),
                ])->icon('clipboard-list')->collapsable(),

                MenuSection::make('Configuration', [
                    MenuItem::resource(MailType::class),
                ])->icon('cog')->collapsable(),
            ];
        });
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
                ->withAuthenticationRoutes()
                ->withPasswordResetRoutes()
                ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return true;
            // return in_array($user->email, [
            //     //
            // ]);
        });
    }

    /**
     * Get the cards that should be displayed on the default Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {
        return [
            (new BookingCalendar())->withClients()->withAlerts(),
        ];
    }

    /**
     * Get the extra dashboards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [
            new Main
            // new DashboardsCalendar()
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
