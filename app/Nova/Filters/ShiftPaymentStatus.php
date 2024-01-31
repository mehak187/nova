<?php

namespace App\Nova\Filters;

use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class ShiftPaymentStatus extends Filter
{
    public $name = 'Statut du paiement';

    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

    /**
     * Apply the filter to the given query.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(NovaRequest $request, $query, $value)
    {
        if (empty($value)) {
            return $query;
        }

        return $query
            ->when($value === 'paid', function ($query) {
                return $query->whereNotNull('paid_at');
            })
            ->when($value === 'unpaid', function ($query) {
                return $query->whereNull('paid_at');
            });
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function options(NovaRequest $request)
    {
        return [
            'Payé' => 'paid',
            'Non payé' => 'unpaid',
        ];
    }
}
