<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\DateTime;

class urls extends Resource
{
    /**
     * 
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\urls>
     */
    public static $model = \App\Models\urls::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    // public static function createUrl(NovaRequest $request)
    // {
    //     // Construct the create URL with the ID parameter
    //     return '/cp/resources/urls/new/' . $request->id;
    // }
    public function fields(NovaRequest $request) {
    return [
        ID::make()->sortable()->hideFromDetail()->hideFromIndex(),
        Text::make('Assistant ID', 'assistant_id')->onlyOnForms() ->withMeta([
            'value' => request('viaResourceId'),
            'readonly' => true,
        ]), 
        Text::make('URL')->placeholder('https://abc.com'),
        Text::make('contact')->placeholder('Contact (who does what)'),
        Textarea::make('services')->placeholder('Services (all the different services with specificities)'),
        Textarea::make('prices')->placeholder('Prices (clear list)'),
        Textarea::make('descriptions')->placeholder('Description (the activity, the organization etc)'),
        Textarea::make('conditions')->placeholder('The way it works (the conditions etc.)'),
        DateTime::make('Date', 'created_at')->exceptOnForms(),
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
}
