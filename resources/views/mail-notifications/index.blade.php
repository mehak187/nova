@extends('layouts.app')

@section('content')
<div class="w-full h-full overflow-y-auto p-6">
    @forelse ($notifications->groupBy(function ($notification) { return $notification->created_at->format('d.m.Y'); }) as $date => $items)
        <div class="px-6 py-4 text-gray-400 font-light text-center">
            {{ $date }}
        </div>

        @foreach ($items as $notification)
            <div class="shadow rounded-lg overflow-hidden w-full bg-white mb-4">
                <div class="flex items-center justify-start bg-gray-50 rounded-t-lg text-sm p-4">
                    <div class="font-medium">
                        {{ $notification->created_at->format('H:i') }}
                    </div>

                    <div class="ml-2 text-gray-700">{{ $notification->type }}</div>
                    <div class="ml-auto text-gray-700 font-medium">{{ $notification->quantity }}</div>
                </div>
            </div>
        @endforeach
    @empty
        <div class="px-6 py-4 text-gray-400 font-light text-center">
            Pas de notifications pour le moment
        </div>
    @endforelse

    {{ $notifications->links() }}
</div>
@endsection
