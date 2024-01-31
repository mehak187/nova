<?php

namespace App\Nova\Lenses;

use App\Nova\User;
use App\Nova\Client;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Lenses\Lens;
use App\Nova\Filters\Assignee;
use Illuminate\Support\Carbon;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\Stack;
use App\Nova\Filters\TaskStatus;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Http\Requests\NovaRequest;

class ClosedTasks extends Lens
{
    public function name()
    {
        return 'Tâches terminées';
    }

    /**
     * Get the query builder / paginator for the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\LensRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return mixed
     */
    public static function query(LensRequest $request, $query)
    {
        return $request
            ->withOrdering($request->withFilters(
                $query->where('status', 'closed')
            ))
            ->when(empty($request->get('orderByDirection')), function ($query) {
                $query->getQuery()->orders = [];

                return $query->orderBy('due_date', 'asc');
            });
    }

    /**
     * Get the fields available to the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Stack::make('Date de création', 'created_at', [
                Date::make('Date de création', 'created_at')
                    ->displayUsing(function ($value) {
                        return $value->format('d.m.Y');
                    }),

                BelongsTo::make('Créé par', 'creator', User::class),
            ])->sortable(),

            Badge::make('Status')->map([
                'draft' => 'danger',
                'open' => 'info',
                'in_progress' => 'warning',
                'closed' => 'success',
            ])->labels([
                'draft' => 'Demande',
                'open' => 'To do',
                'in_progress' => 'En cours',
                'closed' => 'Terminée',
            ]),

            BelongsTo::make('Client', 'client', Client::class),

            Text::make('Titre', 'title'),

            Trix::make('Description', 'description'),

            Date::make('Date d\'échéance', 'due_date')
                ->displayUsing(function ($value) {
                    return Carbon::parse($value)->format('d.m.Y');
                })
                ->sortable(),
        ];
    }

    /**
     * Get the cards available on the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [
            new TaskStatus,
            new Assignee,
        ];
    }

    /**
     * Get the actions available on the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
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
        return 'closed-tasks';
    }
}
