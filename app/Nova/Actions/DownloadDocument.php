<?php

namespace App\Nova\Actions;

use App\Services\Infomaniak\InfomaniakApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

class DownloadDocument extends Action
{
    use InteractsWithQueue, Queueable;

    public $name = 'Télécharger';

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        $document = $models->first();

        $response = app(InfomaniakApi::class)->kdrive(config('services.infomaniak.drive_id'))->getTemporaryUrl($document->kdrive_file_id);

        return Action::download(
            $response['data']['temporary_url'],
            $document->name,
        );
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [];
    }
}
