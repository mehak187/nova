<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Hidden;

class paragraph extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\paragraph>
     */
    public static $model = \App\Models\paragraph::class;

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
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable()->hideFromDetail()->hideFromIndex(),
            Hidden::make('Assistant ID', 'assistant_id')->onlyOnForms() ->withMeta([
                'value' => request('viaResourceId'),
                'readonly' => true,
            ]), 
            Textarea::make('Text')->placeholder('Enter text here')->hideFromIndex(),
            Text::make('Text', function () {
                $text = $this->text;
                $words = str_word_count($text, 1);
                $firstTenWords = implode(' ', array_slice($words, 0, 10));
                return $firstTenWords;
            })->onlyOnIndex(),
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
