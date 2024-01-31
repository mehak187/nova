<?php

namespace App\Nova\Actions;

use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Laravel\Nova\Actions\Action;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Laravel\Nova\Http\Requests\NovaRequest;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DownloadQrCode extends Action
{
    use InteractsWithQueue, Queueable;

    public function name()
    {
        return __('Télécharger le QR Code');
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
        $model = $models->first();

        if (empty($model->qr_code)) {
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

        $path = storage_path('app/public/qrcodes/' . $model->id . '.svg');

        QrCode::format('svg')
            ->style('round')
            ->size(400)
            ->generate($model->qr_code, $path);

        return Action::download(Storage::disk('public')->url('qrcodes/' . $model->id . '.svg'), 'QRCode');
    }

    public function fields(NovaRequest $request)
    {
        return [];
    }
}
