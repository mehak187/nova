<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Boolean;
use App\Services\Infomaniak\InfomaniakApi;
use Laravel\Nova\Http\Requests\NovaRequest;

class Document extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Document>
     */
    public static $model = \App\Models\Document::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name'
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
            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Boolean::make('Public ?', 'is_public')
                ->rules('required')
                ->help('Si cette case est cochée, le document sera visible par le client depuis l\'app'),

            File::make('Document', 'path')
                ->disk('public')
                ->path('documents')
                ->creationRules('required')
                ->store(function (Request $request, $model) {
                    $content = $request->file('path')->getContent();

                    $kdrive = app(InfomaniakApi::class)->kdrive(config('services.infomaniak.drive_id'));

                    $kdrive->createDirectory(config('services.infomaniak.root_directory_id'), $request->viaResourceId);

                    $response = $kdrive->uploadFile(
                        destinationPath: '/calliopee/clients/' . $request->viaResourceId . '/' . $request->file('path')->getClientOriginalName(),
                        content: $content,
                        options: [
                            'conflict' => 'rename',
                        ]
                    );

                    return [
                        'kdrive_file_id' => $response['data']['id'],
                        'size' => $response['data']['size'],
                        'mime_type' => $response['data']['mime_type'],
                    ];
                })
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
        return [
            (new Actions\DownloadDocument)->showInline()
        ];
    }

    public static function redirectAfterCreate(NovaRequest $request, $resource)
    {
        return '/resources/clients/' . $resource->client_id;
    }

    public static function redirectAfterUpdate(NovaRequest $request, $resource)
    {
        return '/resources/clients/' . $resource->client_id;
    }
}
