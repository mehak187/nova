<?php

namespace App\Nova;

use App\Nova\Filters\BalanceType;
use App\Nova\Filters\DateFrom;
use App\Nova\Filters\DateTo;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class ClientBalanceChange extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\ClientBalanceChange::class;

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

    public static $searchRelations = [
        'client' => ['first_name', 'last_name'],
    ];

    public static $group = 'Journalisation';

    public static function label()
    {
        return 'Variations de solde';
    }

    public static function singularLabel()
    {
        return 'Variation de solde';
    }

    public static $perPageViaRelationship = 20;

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
            DateTime::make(__('Date / heure'), 'created_at')
                ->displayUsing(function ($value) {
                    return $value->format('d.m.Y H:i');
                }),
            BelongsTo::make('Client', 'client', Client::class),
            Text::make('Modifié par', 'user_name'),
            Select::make('Type', 'property')
                ->options([
                    'purchased_minutes_table' => 'Minutes restantes (tables)',
                    'purchased_minutes_office' => 'Minutes restantes (bureaux)',
                    'included_minutes_table' => 'Minutes 235 restantes (tables)',
                    'included_minutes_office' => 'Minutes 235 restantes (bureaux)',
                ])->displayUsingLabels(),
            Select::make('Opération', 'operation')->options([
                'credit' => 'Crédit',
                'debit' => 'Débit',
            ])->displayUsingLabels(),
            Text::make('Valeur', 'amount'),
        ];
    }

    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    public function authorizedToDelete(Request $request)
    {
        return false;
    }

    public function authorizedToUpdate(Request $request)
    {
        return false;
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
        return [
            new DateFrom,
            new DateTo,
            new BalanceType
        ];
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
