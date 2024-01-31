<?php

namespace App\Http\Controllers;

use App\Actions\CloseShift;
use App\Models\Client;
use App\Models\ScanLog;
use App\Models\Shift;
use App\Models\Workspace;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function index()
    {
        $client = Client::first();
        $workspaces = Workspace::all();

        $runningShifts = $client->shifts()->where('status', 'running')->pluck('workspace_id')->toArray();

        return view('scan', [
            'workspaces' => $workspaces,
            'runningShifts' => $runningShifts,
        ]);
    }

    public function scan()
    {
        /** @var \App\Models\Client $client */
        $client = auth()->user();

        $data = request()->validate([
            'code' => 'required|exists:workspaces,qr_code',
        ]);

        $workspace = Workspace::where('qr_code', $data['code'])->first();

        $activeShifts = $client->shifts()
            ->where('status', 'running')
            ->where('workspace_id', $workspace->id)
            ->get();

        if ($activeShifts->count()) {
            foreach ($activeShifts as $activeShift) {
                CloseShift::make($activeShift)->run();
            }

            return redirect()->back();
        }

        $bookedShift = $client->shifts()
            ->whereIn('status', ['booked', 'finished'])
            ->where('workspace_id', $workspace->id)
            ->where('started_at', '<=', now()->addMinutes(5))
            ->where('ended_at', '>=', now())
            ->first();

        if ($bookedShift) {
            $bookedShift->update([
                'status' => 'running',
            ]);

            ScanLog::create([
                'client_id' => $client->id,
                'workspace_id' => $workspace->id,
                'shift_id' => $bookedShift->id,
                'direction' => 'IN',
                'message' => 'Entrée réservation',
            ]);

            return redirect()->back();
        }

        $shift = Shift::create([
            'client_id' => $client->id,
            'workspace_id' => $workspace->id,
            'started_at' => now()->format('Y-m-d H:i:00'),
            'status' => 'running',
        ]);

        ScanLog::create([
            'client_id' => $client->id,
            'workspace_id' => $workspace->id,
            'shift_id' => $shift->id,
            'direction' => 'IN',
            'message' => 'Nouveau shift',
        ]);

        return redirect()->back();
    }
}
