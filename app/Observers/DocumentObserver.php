<?php

namespace App\Observers;

use App\Models\Document;
use App\Services\Infomaniak\InfomaniakApi;

class DocumentObserver
{
    public function deleting(Document $document)
    {
        app(InfomaniakApi::class)->kdrive(config('services.infomaniak.drive_id'))
            ->trashFile($document->kdrive_file_id);
    }
}
