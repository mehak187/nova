<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Services\Exivo;
use App\Models\OpeningTime;
use Illuminate\Support\Facades\Cache;

class EntranceController extends Controller
{
    public function index()
    {
        if (! auth()->user()->door_access_enabled) {
            abort(403, 'Vous ne pouvez pas accéder à cette fonction.');
        }

        $holiday = Holiday::where('date', today())->first();

        if (! empty($holiday)) {
            return view('entrance-closed', [
                'title' => 'Calliopée est fermé aujourd\'hui',
                'message' => $holiday->message,
            ]);
        }

        $isOpenToday = OpeningTime::where('day_name', strtolower(now()->englishDayOfWeek))->where('is_open', true)->first();

        if (empty($isOpenToday)) {
            return view('entrance-closed', [
                'title' => 'Calliopée est fermé aujourd\'hui',
                'message' => '',
            ]);
        }

        $isOpenNow = OpeningTime::where('day_name', strtolower(now()->englishDayOfWeek))
            ->where('is_open', true)
            ->where('time_from', '<=', now()->format('H:i'))
            ->where('time_to', '>=', now()->format('H:i'))
            ->exists();

        if (! $isOpenNow) {
            return view('entrance-closed', [
                'title' => 'Calliopée est fermé actuellement',
                'message' => 'Horaire du jour: '.$isOpenToday->time_from.' - '.$isOpenToday->time_to,
            ]);
        }

        return view('entrance', [
            'accessCode' => optional(Cache::get('exivo:access_code'))['code'],
        ]);
    }

    public function open()
    {
        if (! auth()->user()->door_access_enabled) {
            abort(403, 'Vous ne pouvez pas ouvrir la porte.');
        }

        $holiday = Holiday::where('date', today())->first();

        if (! empty($holiday)) {
            return view('entrance-closed', [
                'title' => 'Calliopée est fermé aujourd\'hui',
                'message' => $holiday->message,
            ]);
        }

        $isOpenToday = OpeningTime::where('day_name', strtolower(now()->englishDayOfWeek))->where('is_open', true)->first();

        if (empty($isOpenToday)) {
            return view('entrance-closed', [
                'title' => 'Calliopée est fermé aujourd\'hui',
                'message' => '',
            ]);
        }

        $isOpenNow = OpeningTime::where('day_name', strtolower(now()->englishDayOfWeek))
            ->where('is_open', true)
            ->where('time_from', '<=', now()->format('H:i'))
            ->where('time_to', '>=', now()->format('H:i'))
            ->exists();

        if (! $isOpenNow) {
            return view('entrance-closed', [
                'title' => 'Calliopée est fermé actuellement',
                'message' => 'Horaire du jour: '.$isOpenToday->time_from.' - '.$isOpenToday->time_to,
            ]);
        }

        auth()->user()->doorOpenings()->create();

        app(Exivo::class)
            ->unlockDoor(
                config('services.exivo.components.main_entrance')
            );
    }
}
