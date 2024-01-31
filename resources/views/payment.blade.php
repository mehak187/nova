@extends('layouts.app')

@section('content')
<div class="p-4">
    <div class="text-center">
        <h1 class="text-4xl font-bold text-center mb-4">Paiement</h1>
        <div class="text-4xl">CHF {{ $amount }}</div>
        <p class="py-4">Veuillez présenter votre QR code à la borne de paiement pour vous identifier et procéder au paiement</p>
    </div>

    <div class="p-4 bg-white rounded shadow-lg text-center">
        @if (!empty($code))
            <div class="inline-block mx-auto">{!! $code !!}</div>
        @else
            <div>Vous n'avez pas encore de QR Code, veuillez vous adresser à la réception.</div>
        @endif
    </div>
</div>
@endsection
