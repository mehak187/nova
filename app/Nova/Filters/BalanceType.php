<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class BalanceType extends Filter
{
    public $name = 'Type de solde';

    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

    public function apply(NovaRequest $request, $query, $value)
    {
        return $query->where('property', $value);
    }


    public function options(NovaRequest $request)
    {
        return [
            'Minutes restantes (tables)' => 'purchased_minutes_table',
            'Minutes restantes (bureaux)' => 'purchased_minutes_office',
            'Minutes 235 restantes (tables)' => 'included_minutes_table',
            'Minutes 235 restantes (bureaux)' => 'included_minutes_office',
        ];
    }
}
