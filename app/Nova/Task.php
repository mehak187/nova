<?php

namespace App\Nova;

use App\Nova\Filters\Assignee;
use App\Nova\Lenses\MyTasks;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Select;
use App\Nova\Filters\TaskStatus;
use App\Nova\Lenses\ClosedTasks;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;

class Task extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Task>
     */
    public static $model = \App\Models\Task::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'title';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'title'
    ];

    public static function label()
    {
        return 'Tâches';
    }

    public static function singularLabel()
    {
        return 'Tâche';
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
            Stack::make('Date de création', 'created_at', [
                Date::make('Date de création', 'created_at')
                    ->displayUsing(function ($due_date) {
                        return $due_date->format('d.m.Y');
                    }),

                BelongsTo::make('Créé par', 'creator', User::class)
                    ->hideWhenCreating()
                    ->hideWhenUpdating(),
            ])->sortable(),

            Badge::make('Statut', 'status')->map([
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

            BelongsTo::make('Client', 'client', Client::class)
                ->nullable()
                ->searchable(),

            Text::make('Titre', 'title')
                ->required(),

            Trix::make('Description', 'description')
                ->nullable()
                ->alwaysShow()
                ->withFiles('tasks'),

            Date::make('Date d\'échéance', 'due_date')
                ->displayUsing(function ($due_date) {
                    return $due_date?->format('d.m.Y');
                })
                ->sortable(),

            Select::make('Statut', 'status')
                ->options([
                    'draft' => 'Demande',
                    'open' => 'To do',
                    'in_progress' => 'En cours',
                    'closed' => 'Terminée',
                ])
                ->displayUsingLabels()
                ->onlyOnForms(),

            // DateTime::make('Date de création', 'created_at')
            //     ->hideWhenCreating()
            //     ->hideWhenUpdating(),

            // DateTime::make('Date de modification', 'updated_at')
            //     ->hideWhenCreating()
            //     ->hideWhenUpdating(),

            BelongsTo::make('Assigné à', 'assignee', User::class)->nullable(),
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
            new TaskStatus,
            new Assignee
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
            new MyTasks,
            new ClosedTasks,
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
        return [];
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        $hideClosed = true;

        if (!empty($request->filters)) {
            $filters = json_decode(base64_decode($request->filters));

            foreach ($filters as $filter) {
                if (property_exists($filter, 'App\\Nova\\Filters\\TaskStatus') && $filter->{'App\\Nova\\Filters\\TaskStatus'} === 'closed') {
                    $hideClosed = false;
                }
            }
        }

        return $query
            ->when($hideClosed, function ($query) {
                return $query->where('status', '!=', 'closed');
            })
            ->when(empty($request->get('orderByDirection')), function ($query) {
                $query->getQuery()->orders = [];

                return $query->orderBy('due_date', 'asc');
            });
    }
}
