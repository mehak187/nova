<?php

namespace App\Nova\Actions;

use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Laravel\Nova\Actions\Action;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Laravel\Nova\Http\Requests\NovaRequest;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GenerateQrCode extends Action
{
    use InteractsWithQueue, Queueable;

    public function name()
    {
        return __('Générer un QR Code');
    }

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
            $qr = Str::random(15);

            $model->update([
                'qr_code' => $qr,
                'qr_image' => QrCode::format('svg')
                    ->style('round')
                    ->size(400)
                    ->generate($qr)
                    ->toHtml()
            ]);
        }
    }

    public function fields(NovaRequest $request)
    {
        return [];
    }
}
