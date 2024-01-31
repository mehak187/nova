@extends('layouts.app')

@section('content')
<form id="filterForm" class="flex flex-wrap items-center justify-end pt-6 px-6" method="POST">
    @csrf
    <span class="inline-block mr-2">Filter par date</span>
    <input type="date" name="date" value="{{ $date }}" class="border rounded  px-4 py-2" onchange="document.querySelector('#filterForm').submit()">
    <div class="w-full"></div>

    @if (!empty($date))
        <a href="{{ request()->url() }}" class="flex items-center jusify-end text-xs mt-1 text-red-600">
            Réinitialiser
        </a>
    @endif
</form>

<div class="w-full h-full overflow-y-auto p-6">
    @forelse ($shifts->groupBy(function ($shift) { return $shift->started_at->format('d.m.Y'); }) as $date => $items)
        <div class="px-6 py-4 text-gray-400 font-light text-center">
            {{ $date }}
        </div>

        @foreach ($items as $shift)
            <div class="shadow rounded-lg w-full bg-white mb-4">
                <div class="flex items-center justify-between bg-gray-50 rounded-t-lg text-sm p-4">
                    <div class="font-bold">
                        {{ $shift->started_at->format('H:i') }} - {{ optional($shift->ended_at)->format('H:i') ?? 'Maintenant' }}
                    </div>

                    @if ($shift->status === 'booked')
                        <div class="font-bold text-blue-500">{{ __('status.' . $shift->status) }}</div>
                    @elseif ($shift->status === 'running')
                        <div class="font-bold text-yellow-500">{{ __('status.' . $shift->status) }}</div>
                    @elseif ($shift->status === 'finished')
                        <div class="font-bold text-green-500">{{ __('status.' . $shift->status) }}</div>
                    @elseif ($shift->status === 'cancelled')
                        <div class="font-bold text-red-500">{{ __('status.' . $shift->status) }}</div>
                    @endif
                </div>

                <div class="p-4 text-center">
                    {{ optional($shift->workspace)->name ?? '-' }}
                </div>

                @if ($shift->status === 'booked')
                    <div class="bg-gray-50 rounded-b-lg p-4 text-sm">
                        @if (!$shift->is_cancellable)
                            <div class="text-center text-gray-400 text-xs">
                                La réservation commence dans moins de 72h, son annulation vous sera facturée 80%. Sans annulation de votre part, vous serez facturé 100%
                            </div>
                        @endif

                        <shift-booked-actions
                            :shift-id="{{ $shift->id }}"
                        ></shift-booked-actions>
                    </div>
                @elseif (in_array($shift->status, ['finished', 'cancelled']))
                    <shift-finished-actions
                        :shift-id="{{ $shift->id }}"
                        :amount="{{ $shift->total_amount_due ?? $shift->amount_due ?? 0 }}" {{-- Backward compatible --}}
                        paid-at="{{ optional($shift->paid_at)->format('d.m.Y') }}"
                    />
                @elseif ($shift->status === 'running')
                    <shift-running-actions :shift-id="{{ $shift->id }}" />
                @endif
            </div>
        @endforeach
    @empty
        @if (!empty($date))
            <div class="px-6 py-4 text-gray-400 font-light text-center">
                Pas de résultats pour le {{ \Carbon\Carbon::parse($date)->format('d.m.Y') }}
            </div>
        @endif
    @endforelse

    {{ $shifts->links() }}
</div>
@endsection
