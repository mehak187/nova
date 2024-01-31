<?php

namespace App\Nova;

use App\Nova\Actions\DownloadQrCode;
use App\Nova\Actions\GenerateQrCode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Workspace extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Workspace::class;

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

    public static $group = 'Calliopée';

    public static function label()
    {
        return __('Espaces de travail');
    }

    public static function singularLabel()
    {
        return __('Espace de travail');
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
            // ID::make(__('ID'), 'id')->sortable(),
            Text::make('Nom', 'name'),
            BelongsTo::make('Type', 'type', WorkspaceType::class),
            Number::make('Facteur de temps', 'minute_factor')->step(0.1),
            Text::make('QR Code', 'qr_code')
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->hideFromIndex(),
            Text::make('QR Image', 'qr_image')
                ->asHtml()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->hideFromIndex(),
            Number::make('Numéro de tri', 'sort_order')->sortable(),
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
            new GenerateQrCode,
            (new DownloadQrCode)
                ->onlyOnTableRow(),
        ];
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        $query->when(empty($request->get('orderBy')), function (Builder $q) {
            $q->getQuery()->orders = [];

            return $q->orderBy('sort_order', 'ASC');
        });
    }
}
