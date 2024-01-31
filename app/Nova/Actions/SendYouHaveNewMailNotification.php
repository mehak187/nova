<?php

namespace App\Nova\Actions;

use App\Models\MailNotification;
use App\Models\MailType;
use Illuminate\Support\Facades\DB;
use App\Notifications\YouHaveNewMail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Whitecube\NovaFlexibleContent\Flexible;

class SendYouHaveNewMailNotification extends Action
{
    use InteractsWithQueue, Queueable;

    public $name = 'Notifier du courrier';

    public $withoutActionEvents = true;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $model) {
            DB::transaction(function () use ($fields, $model) {
                $fields->content->each(function ($item) use ($model) {
                    $model->mailNotifications()->create([
                        'user_id' => auth('web')->id(),
                        'quantity' => $item['attributes']['qty'] ?? 1,
                        'type' => $item['attributes']['description'] ?? $item['attributes']['type'],
                    ]);
                });

                $model->update([
                    'has_mail_notifications' => true,
                    'has_unread_mail_notifications' => true,
                ]);

                $model->notify(new YouHaveNewMail($fields->content->map(function ($item) {
                    return $item['attributes'];
                })->toArray()));
            });
        }
    }

    public function fields(NovaRequest $request)
    {
        $types = MailType::pluck('title', 'title')->sortKeys();

        return [
            Flexible::make('Contenu du courrier', 'content')
                ->addLayout('Courrier', 'courrier', [
                    Number::make('Quantité', 'qty'),
                    Text::make('Autre type', 'description'),
                    Select::make('Type de courrier', 'type')->options($types)->displayUsingLabels(),
                ])
                ->button('Ajouter un courrier')
        ];
    }
}
