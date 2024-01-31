<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Exivo
{
    const BASE_URL = 'https://api.exivo.io/v1';

    private $username;
    private $password;
    private $siteId;

    public function __construct($username, $password, $siteId)
    {
        $this->username = $username;
        $this->password = $password;
        $this->siteId = $siteId;
    }

    public function getBasePath(): string
    {
        return self::BASE_URL.'/'.$this->siteId;
    }

    public function __call(string $name, array $arguments)
    {
        if (! in_array($name, ['get', 'post'])) {
            throw new \Exception('Undefined method "'.$name.'" in '.self::class);
        }

        return Http::withBasicAuth($this->username, $this->password)
            ->baseUrl($this->getBasePath())
            ->{$name}(...$arguments);
    }

    public function getSiteInfo()
    {
        return $this->get('/info')->json();
    }

    public function unlockDoor($componentId)
    {
        if (app()->environment('testing')) {
            return [];
        }

        return $this->post("/component/{$componentId}/unlock", [
            'body' => ['delegatedUser' => 'calliopee'],
        ]);
    }

    public function getVisits(): Collection
    {
        return collect($this->get('/visit')->json());
    }

    public function createVisit()
    {
        return $this->post('/visit', [
            'code' => (string)rand(10000, 99999),
            'validFrom' => now()->startOfDay()->utc()->format('Y-m-d\TH:i:s\Z'),
            'validTo' => now()->addHours(4)->utc()->format('Y-m-d\TH:i:s\Z'),
            'components' => [
                config('services.exivo.components.main_entrance'),
            ],
            'name' => 'app-calliopee',
            'email' => 'mail@calliopee.ch',
        ]);
    }

    public function revokeVisit($id)
    {
        return $this->post("/visit/{$id}/revoke", [
            'body' => (object)[]
        ])->json();
    }
}
