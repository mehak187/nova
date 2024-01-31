@extends('layouts.app')

@section('content')
<div class="w-full md:max-w-xl mx-auto px-4 md:px-0 pt-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-yellow-500">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.712 4.33a9.027 9.027 0 011.652 1.306c.51.51.944 1.064 1.306 1.652M16.712 4.33l-3.448 4.138m3.448-4.138a9.014 9.014 0 00-9.424 0M19.67 7.288l-4.138 3.448m4.138-3.448a9.014 9.014 0 010 9.424m-4.138-5.976a3.736 3.736 0 00-.88-1.388 3.737 3.737 0 00-1.388-.88m2.268 2.268a3.765 3.765 0 010 2.528m-2.268-4.796a3.765 3.765 0 00-2.528 0m4.796 4.796c-.181.506-.475.982-.88 1.388a3.736 3.736 0 01-1.388.88m2.268-2.268l4.138 3.448m0 0a9.027 9.027 0 01-1.306 1.652c-.51.51-1.064.944-1.652 1.306m0 0l-3.448-4.138m3.448 4.138a9.014 9.014 0 01-9.424 0m5.976-4.138a3.765 3.765 0 01-2.528 0m0 0a3.736 3.736 0 01-1.388-.88 3.737 3.737 0 01-.88-1.388m2.268 2.268L7.288 19.67m0 0a9.024 9.024 0 01-1.652-1.306 9.027 9.027 0 01-1.306-1.652m0 0l4.138-3.448M4.33 16.712a9.014 9.014 0 010-9.424m4.138 5.976a3.765 3.765 0 010-2.528m0 0c.181-.506.475-.982.88-1.388a3.736 3.736 0 011.388-.88m-2.268 2.268L4.33 7.288m6.406 1.18L7.288 4.33m0 0a9.024 9.024 0 00-1.652 1.306A9.025 9.025 0 004.33 7.288" />
            </svg>

            <h2 class="font-semibold ml-3">Nouvelle demande</h2>
        </div>

        <form class="mt-6" action="/support" method="POST">
            @csrf
            <div>
                <label for="title" class="text-xs uppercase font-semibold">Quel est le sujet de votre demande ?</label>

                <input type="text" name="title" required class="px-4 py-2 block w-full border rounded" />
            </div>

            <div class="mt-6">
                <label for="title" class="text-xs uppercase font-semibold">Comment pouvons-nous vous aider ?</label>

                <textarea name="description" required class="px-4 py-2 block w-full border rounded" rows="6"></textarea>
            </div>

            <div class="mt-6 text-center">
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Envoyer</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mt-6">
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-yellow-500">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
            </svg>

            <h2 class="font-semibold ml-3">Mes demandes ouvertes</h2>
        </div>

        <div class="mt-6">
            @forelse ($tasks as $task)
                <details class="mb-2 cursor-pointer">
                    <summary class="flex items-center py-2">
                        <span>{{ $task->title }}</span>

                        @if ($task->status === 'open')
                            <span class="ml-auto text-xs bg-yellow-500 text-white px-2 py-1 rounded-full">Ouverte</span>
                        @elseif ($task->status === 'in_progress')
                            <span class="ml-auto text-xs bg-blue-500 text-white px-2 py-1 rounded-full">En cours</span>
                        @elseif ($task->status === 'closed')
                            <span class="ml-auto text-xs bg-green-500 text-white px-2 py-1 rounded-full">Terminée</span>
                        @endif
                    </summary>
                    <div class="p-2 bg-gray-50 border rounded">{!! $task->description !!}</div>
                </details>
            @empty
                <div class="text-center text-gray-500 italic">Aucune demande ouverte</div>
            @endforelse
        </div>
    </div>

    <div class="mt-2 text-sm text-gray-500 italic text-center">Les tâches terminées disparaîtront après 7 jours</div>
</div>
@endsection
