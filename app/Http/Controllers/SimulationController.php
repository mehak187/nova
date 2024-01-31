<?php

namespace App\Http\Controllers;

use App\Actions\CloseShift;
use App\Models\Client;
use App\Models\Shift;
use App\Models\Workspace;
use Illuminate\Http\Request;

class SimulationController extends Controller
{
    public function index()
    {
        $client = Client::first();
        $workspaces = Workspace::all();

        $runningShifts = $client->shifts()->where('status', 'running')->pluck('workspace_id')->toArray();

        return view('simulation', [
            'workspaces' => $workspaces,
            'runningShifts' => $runningShifts,
        ]);
    }

    public function scan()
    {
        /** @var \App\Models\Client $client */
        $client = auth()->user();

        $workspace = Workspace::find(request('workspace_id'));

        $activeShift = $client->shifts()
            ->where('status', 'running')
            ->where('workspace_id', $workspace->id)
            ->first();

        if ($activeShift) {
            CloseShift::make($activeShift)->run();

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

            return redirect()->back();
        }

        Shift::create([
            'client_id' => $client->id,
            'workspace_id' => $workspace->id,
            'started_at' => now()->format('Y-m-d H:i:00'),
            'status' => 'running',
        ]);

        return redirect()->back();
    }
}
