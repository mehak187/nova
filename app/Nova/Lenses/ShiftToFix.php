<?php

namespace App\Nova\Lenses;

use App\Nova\Client;
use App\Nova\Workspace;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Lenses\Lens;

class ShiftToFix extends Lens
{
    public $name = 'A clôturer manuellement';

    /**
     * Get the query builder / paginator for the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\LensRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return mixed
     */
    public static function query(LensRequest $request, $query)
    {
        return $request->withOrdering($request->withFilters(
            $query->whereDate('started_at', '<', now())->whereIn('status', ['booked', 'running'])
        ));
    }

    public function fields(NovaRequest $request)
    {
        return [
            BelongsTo::make('Client', 'client', Client::class),

            BelongsTo::make('Espace de travail', 'workspace', Workspace::class),

            Select::make('Statut', 'status')
                ->options([
                    'cancelled' => 'Annulé',
                    'booked' => 'Réservé',
                    'running' => 'En cours',
                    'finished' => 'Terminé',
                ])
                ->displayUsingLabels(),

            DateTime::make('Date de début', 'started_at')->required(),

            DateTime::make('Date de fin', 'ended_at'),

            Text::make('Durée (mn)', 'duration_in_minutes'),

            Number::make('Durée pré-payée', 'prepaid_duration'),

            Currency::make('Montant HT', 'amount_due'),

            Text::make('Montant TVA', function () {
                if ($this->amount_due) {
                    return number_format($this->amount_due * $this->vat / 100, 2, '.', '\'');
                }

                return '-';
            }),

            Currency::make('Montant TTC', 'total_amount_due'),
        ];
    }

    public function cards(NovaRequest $request)
    {
        return [];
    }

    public function filters(NovaRequest $request)
    {
        return [];
    }

    public function actions(NovaRequest $request)
    {
        return parent::actions($request);
    }

    /**
     * Get the URI key for the lens.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'shift-to-fix';
    }
}
