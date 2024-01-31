<?php

use App\Services\Exivo;
use App\Actions\CancelShift;
use App\Actions\GenerateNewAccessCode;
use App\Mail\BookingCancelled as AdminBookingCancelled;
use App\Models\Shift;
use App\Models\Workspace;
use App\Notifications\BookingCancelled;
use App\Notifications\BookingConfirmed;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

Route::get('/open-door', function () {
    dd(app(Exivo::class)
        ->unlockDoor(
            config('services.exivo.components.main_entrance')
        ));
});

Route::get('/generate-new-code', function () {
    (new GenerateNewAccessCode)->run();

    return response()->json(Cache::get('exivo:access_code'));
});

Route::get('/get-access-code', function () {
    return response()->json(Cache::get('exivo:access_code'));
});

Route::get('/schedule', function (Request $request) {
    $baseDate = request()->validate([
        'date' => 'required|date',
    ])['date'];

    $workspaces = Workspace::orderBy('sort_order', 'ASC')->get()->map->only('id', 'name');

    return $workspaces->map(function ($workspace) use ($baseDate) {
        $date = Carbon::parse($baseDate)->setHour(7)->setMinute(0);
        $endOfDay = Carbon::parse($baseDate)->setHour(21)->setMinute(0);

        $workspace['slots'] = collect();

        while ($date->isBefore($endOfDay)) {
            $workspace['slots']->push([
                'key' => $date->format('H\hi'),
                'date_time' => $date->format('Y-m-d H:i:s'),
                'disabled' => false,
            ]);

            $date->addMinutes(15);
        }

        $shifts = Shift::query()
            ->with('client')
            ->whereDate('started_at', $date)
            ->where('workspace_id', $workspace['id'])
            ->where('status', '!=', 'cancelled')
            ->get();

        $disabled = $shifts->reduce(function ($carry, $shift) {
            return array_merge($carry, $shift->disables);
        }, []);

        $shiftSlots = $shifts->reduce(function ($carry, $shift) {
            foreach ($shift->disables as $key) {
                $carry[$key] = $shift;
            }
            return $carry;
        }, []);

        $workspace['slots'] = $workspace['slots']->map(function ($slot) use ($disabled, $shiftSlots) {
            if (in_array($slot['key'], $disabled)) {
                $slot['disabled'] = true;
            }

            if (array_key_exists($slot['key'], $shiftSlots)) {
                $slot['client_name'] = $shiftSlots[$slot['key']]->client->full_name_reversed;
                $slot['client_id'] = $shiftSlots[$slot['key']]->client_id;
                $slot['shift_id'] = $shiftSlots[$slot['key']]->id;
                $slot['is_cancellable'] = $shiftSlots[$slot['key']]->is_cancellable;
                $slot['note'] = $shiftSlots[$slot['key']]->note;
            }

            return $slot;
        });

        return $workspace;
    });
});

Route::get('/schedule2', function (Request $request) {
    $baseDate = request()->validate([
        'date' => 'required|date',
    ])['date'];

    $workspaces = Workspace::orderBy('sort_order', 'ASC')->get()->map->only('id', 'name');

    return $workspaces->map(function ($workspace) use ($baseDate) {
        $startOfDay = Carbon::parse($baseDate)->setHour(7)->setMinute(0);
        $endOfDay = Carbon::parse($baseDate)->setHour(21)->setMinute(0);

        $totalMinutes = $startOfDay->diffInMinutes($endOfDay);

        $shifts = Shift::query()
            ->with('client')
            ->whereDate('started_at', $baseDate)
            ->where('workspace_id', $workspace['id'])
            ->where('status', '!=', 'cancelled')
            ->orderBy('started_at', 'ASC')
            ->get();

        $carry = [];

        foreach ($shifts as $shift) {
            $carry[] = [
                'offset' => $startOfDay->diffInMinutes($shift->started_at) * 100 / $totalMinutes,
                'duration' => $shift->started_at->diffInMinutes($shift->ended_at ?? now()) * 100 / $totalMinutes,
                'shift_id' => $shift->id,
                'client_id' => $shift->client_id,
                'status' => $shift->status,
                'client_name' => $shift->client->full_name,
                'started_at' => $shift->started_at->format('Y-m-d H:i:s'),
                'ended_at' => !empty($shift->ended_at) ? $shift->ended_at->format('Y-m-d H:i:s') : now(),
                'workspace_id' => $shift->workspace_id,
                'is_cancellable' => $shift->is_cancellable,
                'is_reservation' => $shift->is_reservation,
                'note' => $shift->note,
            ];
        }


        $workspace['shifts'] = $carry;

        return $workspace;
    });
});

Route::post('/store', function () {
    $data = request()->validate([
        'client_id' => 'required|exists:clients,id',
        'shift_id' => [
            'nullable',
            Rule::exists('shifts', 'id')
        ],
        'started_at' => 'required|date',
        'ended_at' => 'required|date',
        'workspace_id' => 'required|exists:workspaces,id',
        'note' => 'nullable',
    ]);

    $hasConflict = Shift::query()
        ->where('workspace_id', $data['workspace_id'])
        ->whereNotIn('status', ['cancelled', 'finished'])
        ->where('id', '!=', $data['shift_id'] ?? null)
        ->where(function ($query) use ($data) {
            $query
                ->orWhere(function ($query) use ($data) {
                    $query->where('started_at', '>=', $data['started_at'])
                        ->where('started_at', '<', $data['ended_at']);
                })->orWhere(function ($query) use ($data) {
                    $query->where('ended_at', '>', $data['started_at'])
                        ->where('ended_at', '<=', $data['ended_at']);
                })->orWhere(function ($query) {
                    $query->where('started_at', '<=', request('started_at'))
                        ->where('ended_at', '>=', request('ended_at'));
                });
        })->exists();

    if ($hasConflict) {
        return response()->json([
            'errors' => [
                'conflict' => ['Cette plage horaire n\'est plus valide.'],
            ]
        ], 422);
    }

    $shiftId = Arr::pull($data, 'shift_id');

    $data = [
        'client_id' => $data['client_id'],
        'workspace_id' => $data['workspace_id'],
        'started_at' => $data['started_at'],
        'ended_at' => $data['ended_at'],
        'is_reservation' => true,
        'note' => $data['note'] ?? null,
    ];

    if (!empty($shiftId)) {
        Shift::find($shiftId)->update($data);

        return;
    }

    $data['status'] = 'booked';

    $shift = Shift::create($data);

    try {
        $shift->client->notify(new BookingConfirmed($shift));
    } catch (\Exception $e) {
        Log::error($e->getMessage());
    }
});

Route::delete('/shifts/{shift}/delete', function (Shift $shift) {
    if ($shift->status !== 'booked') {
        abort(403, 'Cette réservation est déjà terminée.');
    }

    CancelShift::make($shift)->run();

    $shift->client->notify(
        new BookingCancelled($shift)
    );

    try {
        notifyAdmins(new AdminBookingCancelled($shift));
    } catch (\Exception $e) {

    }

    return response()->json([
        'status' => 'success',
        'message' => 'La réservation a été annulée.',
    ]);
});

Route::delete('/shifts/{shift}/force-delete', function (Shift $shift) {
    // DB::transaction(function () use ($shift) {
        $shift->update(['status' => 'cancelled']);
        $shift->delete();

        try {
            $shift->client->notify(
                new BookingCancelled($shift)
            );
        } catch (\Exception $e) {

        }
    // });

    return response()->json([
        'status' => 'success',
        'message' => 'La réservation a été annulée.',
    ]);
});
