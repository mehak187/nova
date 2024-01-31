<?php

namespace App\Nova;

use App\Nova\Actions\CloseShift;
use App\Nova\Actions\MarkAsPaid;
use App\Nova\Filters\ShiftPaymentStatus;
use App\Nova\Filters\ShiftStatus;
use App\Nova\Filters\StartedDateFilter;
use App\Nova\Lenses\ShiftToFix;
use App\Nova\Lenses\ToBePaid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Status;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Shift extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Shift::class;

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
    public static $search = [];

    public static $searchRelations = [
        'client' => ['first_name', 'last_name'],
        'workspace' => ['name']
    ];

    public static $group = 'Clients';

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
            BelongsTo::make('Client', 'client', Client::class),

            BelongsTo::make('Espace de travail', 'workspace', Workspace::class),

            Boolean::make('Est une réservation', 'is_reservation')
                ->nullable()
                ->hideFromIndex(),

            Select::make('Statut', 'status')
                ->options([
                    'cancelled' => 'Annulé',
                    'booked' => 'Réservé',
                    'running' => 'En cours',
                    'finished' => 'Terminé',
                ])
                ->displayUsingLabels()
                ->required(),

            DateTime::make('Date de début', 'started_at')
                ->required()
                ->displayUsing(function ($value) {
                    return $value->format('d.m.Y H:i');
                }),

            DateTime::make('Date de fin', 'ended_at')
                ->nullable()
                ->displayUsing(function ($value) {
                    return $value?->format('d.m.Y H:i');
                }),

            Text::make('Durée (mn)', 'duration_in_minutes')
                ->hideWhenCreating()
                ->hideWhenUpdating(),

            Number::make('Durée pré-payée', 'prepaid_duration')
                ->nullable(),

            Currency::make('Montant HT', 'amount_due')
                ->nullable(),

            Text::make('Montant TVA', function () {
                if ($this->amount_due) {
                    return number_format($this->amount_due * $this->vat / 100, 2, '.', '\'');
                }

                return '-';
            }),

            Number::make('Taux TVA', 'vat')
                ->readonly()
                ->onlyOnDetail(),

            Currency::make('Montant TTC', 'total_amount_due')
                ->nullable(),

            Boolean::make('Payé', 'is_paid')
                ->onlyOnIndex(),

            DateTime::make('Date du paiement', 'paid_at')
                ->hideFromIndex()
                ->displayUsing(function ($value) {
                    return $value?->format('d.m.Y H:i');
                }),

            Textarea::make('Note interne', 'note')
                ->hideFromIndex(),

            HasMany::make('Scans', 'scans', ScanLog::class),
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
        return [
            new StartedDateFilter,
            new ShiftStatus,
            new ShiftPaymentStatus,
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
        return [
            new ToBePaid(),
            new ShiftToFix(),
        ];
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
            new CloseShift,
        ];
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        $query->when(empty($request->get('orderBy')), function (Builder $q) {
            $q->getQuery()->orders = [];

            return $q->orderBy('started_at', 'desc');
        });
    }
}
