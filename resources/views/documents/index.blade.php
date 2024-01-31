@extends('layouts.app')

@section('content')
<div class="w-full h-full overflow-y-auto p-6">
    <div>
        <document-upload />
    </div>

    <div class="mb-1">
        <span class="text-sm uppercase font-semibold text-gray-800">Mes documents</span>
    </div>

    @forelse ($documents as $document)
        <single-document
            id="{{ $document->id }}"
            name="{{ $document->name }}"
            last-updated="{{ $document->updated_at->format('d.m.Y H:i') }}"
            human-readable-size="{{ $document->human_readable_size }}"
        ></single-document>
    @empty
        <div class="px-6 py-4 text-gray-400 font-light text-center">
            Aucun document
        </div>
    @endforelse
</div>
@endsection
