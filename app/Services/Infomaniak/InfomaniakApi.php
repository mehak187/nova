<?php

namespace App\Services\Infomaniak;

class InfomaniakApi
{
    const BASE_URL = 'https://api.infomaniak.com/2';

    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function kdrive($driveId)
    {
        return new KDrive($this, $driveId);
    }
}
