<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Mail\NewTaskCreatedByClient;
use Illuminate\Support\Facades\Mail;

class SupportController extends Controller
{
    public function index()
    {
        return view('support.index', [
            'tasks' => Task::query()
                ->where('client_id', auth()->user()->id)
                ->where(function ($query) {
                    $query->whereIn('status', ['open', 'closed'])
                        ->orWhere(function ($query) {
                            $query->where('status', 'closed')
                                ->where('updated_at', '>', now()->subDays(7));
                        });
                })
                ->get()
                ->sort(function ($a, $b) {
                    if ($a->status === 'closed' && $b->status !== 'closed') {
                        return 1;
                    } elseif ($a->status !== 'closed' && $b->status === 'closed') {
                        return -1;
                    } else {
                        return $a->updated_at <=> $b->updated_at;
                    }
                })
        ]);
    }

    public function store()
    {
        $data = request()->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
        ]);

        $task = Task::create([
            'title' => $data['title'],
            'description' => nl2br($data['description']),
            'client_id' => auth()->user()->id,
            'status' => 'open',
        ]);

        Mail::to('mail@calliopee.ch')->send(new NewTaskCreatedByClient($task));

        return redirect()->back();
    }
}
