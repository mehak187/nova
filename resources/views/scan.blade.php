@extends('layouts.app')

@section('content')
<div class="w-full h-full flex items-center justify-center">
    @if (request('iphone'))
        <qr-scanner-iphone></qr-scanner-iphone>
    @else
        <qr-scanner></qr-scanner>
    @endif
</div>
@endsection
