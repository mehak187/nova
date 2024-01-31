@extends('layouts.app')

@section('bodyClass', 'disable-select')

@section('content')
    <div class="w-full h-full flex items-center justify-center">
        <entrance-controller code="{{ $accessCode }}"/>
        {{-- <entrance-controller /> --}}
    </div>
@endsection
