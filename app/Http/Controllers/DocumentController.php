<?php

namespace App\Http\Controllers;

use App\Services\Infomaniak\InfomaniakApi;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index()
    {
        $client = auth('app')->user();

        return view('documents.index', [
            'documents' => $client->documents()->where('is_public', true)->get()
        ]);
    }

    public function store(InfomaniakApi $ik)
    {
        $data = request()->validate([
            'files' => 'array',
            'files.*' => 'required|file',
        ]);

        $client = auth('app')->user();

        $kdrive = $ik->kdrive(config('services.infomaniak.drive_id'));

        foreach ($data['files'] as $file) {
            $kdrive->createDirectory(config('services.infomaniak.root_directory_id'), auth('app')->id());

            $response = $kdrive->uploadFile(
                destinationPath: '/calliopee/clients/' . auth('app')->id() . '/' . $file->getClientOriginalName(),
                content: $file->getContent(),
                options: [
                    'conflict' => 'rename',
                ]
            );

            $client->documents()->create([
                'name' => $file->getClientOriginalName(),
                'kdrive_file_id' => $response['data']['id'],
                'size' => $response['data']['size'],
                'mime_type' => $response['data']['mime_type'],
                'is_public' => true,
            ]);
        }

        return response()->json([
            'status' => 'ok',
        ]);
    }

    public function download($id, InfomaniakApi $ik)
    {
        $client = auth('app')->user();

        $document = $client->documents()->where('id', $id)->firstOrFail();

        return redirect()->away(
            $ik->kdrive(config('services.infomaniak.drive_id'))->getTemporaryUrl($document->kdrive_file_id)['data']['temporary_url'],
        );
    }

    public function rename($id)
    {
        $data = request()->validate([
            'name' => 'required',
        ]);

        $client = auth('app')->user();

        $document = $client->documents()->where('id', $id)->firstOrFail();

        $document->update($data);

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
