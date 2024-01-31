<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Shift;
use App\Models\Workspace;
use Illuminate\Support\Arr;
use App\Actions\CancelShift;
use App\Actions\CloseShift;
use App\Mail\BookingCancelled as AdminBookingCancelled;
use App\Mail\BookingCreated;
use App\Mail\BookingUpdated;
use Illuminate\Validation\Rule;
use App\Notifications\BookingCancelled;
use App\Notifications\BookingConfirmed;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function schedule()
    {
        $baseDate = request()->validate([
            'date' => 'required|date',
        ])['date'];

        $workspaces = Workspace::orderBy('sort_order', 'ASC')->get()->map->only('id', 'name', 'minute_factor');

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
                $isMe = $shift->client_id === auth('app')->id();

                $carry[] = [
                    'offset' => $startOfDay->diffInMinutes($shift->started_at) * 100 / $totalMinutes,
                    'duration' => $shift->started_at->diffInMinutes($shift->ended_at ?? now()) * 100 / $totalMinutes,
                    'shift_id' => $shift->id,
                    'is_me' => $isMe,
                    'started_at' => $shift->started_at->format('Y-m-d H:i:s'),
                    'ended_at' => !empty($shift->ended_at) ? $shift->ended_at->format('Y-m-d H:i:s') : now(),
                    'status' => $isMe ? $shift->status : '-',
                    'status_text' => $isMe ? __('status.' . $shift->status) : '-',
                    'workspace_id' => $shift->workspace_id,
                    'minute_factor' => $shift->workspace->minute_factor,
                    'is_cancellable' => $shift->is_cancellable,
                    'is_reservation' => $shift->is_reservation,
                ];
            }


            $workspace['shifts'] = $carry;

            return $workspace;
        });
    }

    public function store()
    {
        $data = request()->validate([
            'shift_id' => [
                'nullable',
                Rule::exists('shifts', 'id')->where('client_id', auth('app')->id())
            ],
            'started_at' => 'required',
            'ended_at' => 'required',
            'workspace_id' => 'required|exists:workspaces,id',
        ]);

        $shiftId = Arr::pull($data, 'shift_id');

        $data = [
            'client_id' => auth()->id(),
            'workspace_id' => $data['workspace_id'],
            'started_at' => $data['started_at'],
            'ended_at' => $data['ended_at'],
            'is_reservation' => true,
        ];

        if (!empty($shiftId)) {
            $shift = Shift::find($shiftId);

            if ($shift->status === 'running') {
                abort(403, 'Cette réservation est en cours, vous ne pouvez pas la modifier.');
            }

            $shift->update($data);

            try {
                auth()->user()->notify(new BookingConfirmed($shift));
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }

            notifyAdmins(new BookingUpdated($shift));

            return;
        }

        $data['status'] = 'booked';

        $shift = Shift::create($data);

        try {
            auth()->user()->notify(new BookingConfirmed($shift));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        notifyAdmins(new BookingCreated($shift));
    }

    public function cancel(Shift $shift)
    {
        if (!auth('app')->check() || (int)$shift->client_id !== auth('app')->id()) {
            abort(403);
        }

        if ($shift->status === 'finished' || $shift->status === 'cancelled') {
            return response()->json([
                'status' => 'error',
                'message' => 'Cette réservation est déjà terminée.',
                'refresh' => true,
            ]);
        }

        if ($shift->status === 'running') {
            return response()->json([
                'status' => 'error',
                'message' => 'Cette réservation est en cours vous ne pouvez pas l\'annuler.',
                'refresh' => true,
            ]);
        }

        CancelShift::make($shift)->run();

        $shift->client->notify(
            new BookingCancelled($shift)
        );

        notifyAdmins(new AdminBookingCancelled($shift));

        return response()->json([
            'status' => 'success',
            'message' => 'La réservation a été annulée.',
        ]);
    }

    public function close(Shift $shift)
    {
        if (!auth('app')->check() || (int)$shift->client_id !== auth('app')->id()) {
            abort(403);
        }

        if ($shift->status === 'finished') {
            return response()->json([
                'status' => 'error',
                'message' => 'Ce shift est déjà terminé',
                'refresh' => true,
            ]);
        }

        CloseShift::make($shift)->run();

        return response()->json([
            'status' => 'success',
            'message' => 'Merci.',
        ]);
    }
}
