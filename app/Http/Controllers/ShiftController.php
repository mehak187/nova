<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ShiftController extends Controller
{
    public function pay(Shift $shift)
    {
        if (!auth('app')->check() || (int)$shift->client_id !== auth('app')->id()) {
            abort(403);
        }

        if (!empty($shift->client->sumup_id)) {
            $code = QrCode::format('svg')
                ->style('round')
                ->size(250)
                ->generate($shift->client->sumup_id)
                ->toHtml();
        }

        return view('payment', [
            'amount' => $shift->total_amount_due,
            'code' => $code ?? null,
        ]);
    }

    public function alert()
    {
        return response()->json([
            'count' => auth('app')->user()->shifts()
                ->where('ended_at', '>', now())
                ->where('ended_at', '<', now()->addMinutes(15))
                ->count()
        ]);
    }
}
