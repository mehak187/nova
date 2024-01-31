@extends('layouts.app')

@section('content')
<div class="pt-8 px-6">
    <h1 class="text-4xl font-bold text-center mb-4">Paramètres</h1>

    <form action="/settings" method="POST" class="bg-white rounded max-w-lg mx-auto">
        {{ csrf_field() }}

        <div class="px-6 py-4">
            <label for="email" class="block text-sm font-semibold">E-mail</label>
            <input id="email" type="email" class="px-4 py-2 rounded border block w-full" name="email" value="{{ $user->email }}" />
            @error('email')
                <span class="text-sm text-red-500">{{ $message }}</span>
            @enderror
        </div>

        <div class="px-6 py-4">
            <label for="password" class="block text-sm font-semibold">Mot de passe</label>
            <input id="password" type="password" class="px-4 py-2 rounded border block w-full" name="password" />
            @error('password')
                <span class="text-sm text-red-500">{{ $message }}</span>
            @enderror
        </div>

        <div class="px-6 py-4">
            <label for="password_confirmation" class="block text-sm font-semibold">Confirmation mot de passe</label>
            <input id="password_confirmation" type="password" class="px-4 py-2 rounded border block w-full" name="password_confirmation" />
            @error('password_confirmation')
                <span class="text-sm text-red-500">{{ $message }}</span>
            @enderror
        </div>

        <div class="px-6 py-4 text-right">
            <button class="px-4 py-2 bg-blue-500 text-white rounded">Enregistrer</button>
        </div>
    </form>
</div>
@endsection
