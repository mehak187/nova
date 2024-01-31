<?php

namespace App\Actions;

use App\Models\ScanLog;
use App\Models\Shift;
use App\Services\Exivo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GenerateNewAccessCode
{
    public $exivo;

    public function __construct()
    {
        $this->exivo = app(Exivo::class);
    }

    public function run()
    {
        $result = $this->exivo->createVisit();

        if ($result->status() === 200) {
            if ($id = optional(Cache::get('exivo:access_code'))['id']) {
                $this->exivo->revokeVisit($id);

                Cache::forget('exivo:access_code');
            }

            Cache::put('exivo:access_code', $result->json());
        }
    }
}
