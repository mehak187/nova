<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\HasMany; // Add this import statement
use Laravel\Nova\Button;

class assistants extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\assistants>
     */
    public static $model = \App\Models\assistants::class;

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

    public function urls()
    {
        return $this->hasMany(urls::class); // Assuming you have defined a 'hasMany' relationship in your assistants model
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
            ID::make()->sortable()->hideFromDetail()->hideFromIndex(),
            File::make('icon')->placeholder('assistant icon'),
            Text::make('name')->placeholder('name your assistant'),
            Text::make('description')->placeholder('Add a short description what this Assistant does'),
            Textarea::make('instructions')->placeholder('Add instructions as a knowledge base.'),
            DateTime::make('Date', 'created_at')->exceptOnForms(),
            Text::make('Action', function () {
                $asid = $this->id;
                return '
                <a style="background-color:blue; color:white; padding:10px 10px; margin-right:6px;border-radius:6px;" href="/cp/resources/urls/new/' . $asid . '">Add URL</a>
                <a style="background-color:green; color:white; padding:10px 10px; margin-right:6px;border-radius:6px;" href="/cp/resources/paragraph/new/' . $asid . '">Add Paragraph</a>
                <a style="background-color:violet; color:white; padding:10px 10px; margin-right:6px;border-radius:6px;" href="/cp/resources/docs/new/' . $asid . '">Add Documents</a>
                ';
            })->asHtml(),
            
            Text::make('URL Data', function () {
                $urls = \App\Models\urls::join('assistants', 'assistants.id', '=', 'urls.assistant_id')
                    ->where('assistant_id', $this->id)
                    ->get();
                $result = '';
                    foreach ($urls as $url) {
                        $result .= 
                        "<div style='font-weight:bold'>URL:</div> {$url->url}<br>
                        <div style='font-weight:bold'>Contact:</div> {$url->contact}<br>
                        <div style='font-weight:bold'>Cervices:</div> {$url->services}<br>
                        <div style='font-weight:bold'>Prices:</div> {$url->prices}<br>
                        <div style='font-weight:bold'>Descriptions:</div> {$url->descriptions}<br>
                        <div style='font-weight:bold'>Conditions:</div> {$url->conditions}<br>";
                    }
                return $result;
            })->asHtml()->onlyOnDetail(),

            Text::make('Paragraph', function () {
                $paragraphs = \App\Models\paragraph::join('assistants', 'assistants.id', '=', 'paragraphs.assistant_id')
                    ->where('assistant_id', $this->id)
                    ->get();
                $resultp = '';
                    foreach ($paragraphs as $paragraph) {
                        $resultp .= 
                        "<div style='font-weight:bold'>Text:</div> {$paragraph->text}<br>";
                    }
                return $resultp;
            })->asHtml()->onlyOnDetail(),

            Text::make('Documents', function () {
                $docs = \App\Models\docs::join('assistants', 'assistants.id', '=', 'docs.assistant_id')
                    ->where('assistant_id', $this->id)
                    ->get();
                $resultp = '';
                    foreach ($docs as $doc) {
                        $resultp .= 
                        "<div style='font-weight:bold'>File:</div> <a href='/storage/app/{$doc->file}'>{$doc->file}</a></div><br>";
                    }
                return $resultp;
            })->asHtml()->onlyOnDetail()
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

    public function detailToolbar(Request $request)
    {
        return [
            Button::make('Add URL')
                ->url('/url-to-add-url-resource/' . $this->id)
                ->style('primary'),
        ];
    }
}
