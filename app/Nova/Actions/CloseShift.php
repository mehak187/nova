<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\DateTime;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Actions\CloseShift as ActionsCloseShift;

class CloseShift extends Action
{
    use InteractsWithQueue, Queueable;

    public function name()
    {
        return __('Terminer le shift');
    }

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $model) {
            ActionsCloseShift::make($model)->run($fields->ended_at);
        }
    }

    public function fields(NovaRequest $request)
    {
        return [
            DateTime::make('Date de fin', 'ended_at')->help('N\'oubliez pas de définir l\'heure de fin'),
        ];
    }
}
