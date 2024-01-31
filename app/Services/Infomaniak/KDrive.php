<?php

namespace App\Services\Infomaniak;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KDrive
{
    public InfomaniakApi $api;

    public string $driveId;

    public function __construct(InfomaniakApi $api, string $driveId)
    {
        $this->api = $api;
        $this->driveId = $driveId;
    }

    public function uploadFile($destinationPath, $content, array $options = [])
    {
        $directory = Str::beforeLast($destinationPath, '/');
        $filename = Str::afterLast($destinationPath, '/');

        $options = array_merge([
            'directory_path' => $directory,
            'file_name' => $filename,
            'total_size' => strlen($content),
        ], $options);

        return Http::withToken($this->api->token)
            ->withBody($content, 'application/octet-stream')
            ->post($this->api::BASE_URL . "/drive/{$this->driveId}/upload?" . http_build_query($options))
            ->json();
    }

    public function createDirectory($parentId, $name)
    {
        return Http::withToken($this->api->token)
            ->post($this->api::BASE_URL . "/drive/{$this->driveId}/files/{$parentId}/directory", [
                'name' => $name,
            ])
            ->json();
    }

    public function getTemporaryUrl($fileId, $duration = 3600)
    {
        return Http::withToken($this->api->token)
            ->get($this->api::BASE_URL . "/drive/{$this->driveId}/files/{$fileId}/temporary_url", [
                'duration' => $duration,
            ])
            ->json();
    }

    public function trashFile($fileId)
    {
        $result = Http::withToken($this->api->token)
            ->delete($this->api::BASE_URL . "/drive/{$this->driveId}/files/{$fileId}")
            ->json();

        Log::info($result);

        return $result;
    }
}
