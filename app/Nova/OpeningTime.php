<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class OpeningTime extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\OpeningTime::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'day_name_fr';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [];

    public static $group = 'Accès';

    public static function label()
    {
        return 'Horaires d\'ouverture';
    }

    public static function singularLabel()
    {
        return 'Horaire d\'ouverture';
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Select::make('Jour', 'day_name')
                ->options([
                    'monday' => 'Lundi',
                    'tuesday' => 'Mardi',
                    'wednesday' => 'Mercredi',
                    'thursday' => 'Jeudi',
                    'friday' => 'Vendredi',
                    'saturday' => 'Samedi',
                    'sunday' => 'Dimanche',
                ])
                ->displayUsingLabels()
                ->rules('required')
                ->exceptOnForms(),
            Boolean::make('Ouvert', 'is_open'),
            Text::make('Heure d\'ouverture', 'time_from')
                ->help('Format: 08:00')
                ->rules('nullable'),
            Text::make('Heure de fermeture', 'time_to')
                ->help('Format: 19:00')
                ->rules('nullable'),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        $query->getQuery()->orders = [];

        return $query->orderBy('id', 'asc');
    }

    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    public function authorizedToDelete(Request $request)
    {
        return false;
    }
}
