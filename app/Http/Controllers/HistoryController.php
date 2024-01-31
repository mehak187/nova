<?php

namespace App\Http\Controllers;

class HistoryController extends Controller
{
    public function index()
    {
        $data = request()->validate([
            'date' => 'nullable|date',
        ]);

        $shifts = auth('app')->user()
            ->shifts();

        if (!empty($data['date'])) {
            $shifts->whereDate('started_at', $data['date']);
        }

        return view('history', [
            'shifts' => $shifts->orderBy('started_at', 'DESC')->simplePaginate(15),
            'date' => $data['date'] ?? null,
        ]);
    }
}
