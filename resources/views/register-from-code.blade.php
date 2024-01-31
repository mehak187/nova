<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Inscription</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @laravelPWA
</head>
<body>
    <div class="w-screen h-screen flex items-center justify-center bg-gray-100 px-4 md:px-0">
        <div class="w-full max-w-lg bg-white shadow border rounded-lg">
            <img src="/logo.webp" alt="Calliopée" class="h-16 block mx-auto py-2">

            <div class="p-6">
                <h1 class="text-lg font-bold mb-4">
                    Inscription
                </h1>

                <form action="/register/{{ $client->register_code }}" method="POST">
                    @csrf

                    <div class="flex -mx-2">
                        <div class="flex-1 px-2 mb-4">
                            <label for="first_name" class="block text-xs font-bold text-gray-600 uppercase">Prénom</label>

                            <input type="text" name="first_name" id="first_name" class="px-4 py-2 block w-full border rounded" value="{{ old('first_name') ?? $client->first_name }}" />

                            @error('first_name')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex-1 px-2 mb-4">
                            <label for="last_name" class="block text-xs font-bold text-gray-600 uppercase">Nom</label>

                            <input type="text" name="last_name" id="last_name" class="px-4 py-2 block w-full border rounded" value="{{ old('last_name') ?? $client->last_name }}" />

                            @error('last_name')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block text-xs font-bold text-gray-600 uppercase">E-mail</label>

                        <input type="email" name="email" id="email" class="px-4 py-2 block w-full border rounded" value="{{ old('email') ?? $client->email }}" />

                        @error('email')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                     <div class="mb-4">
                        <label for="mobile_phone" class="block text-xs font-bold text-gray-600 uppercase">Téléphone mobile</label>

                        <input type="text" name="mobile_phone" id="mobile_phone" class="px-4 py-2 block w-full border rounded" value="{{ old('mobile_phone') ?? $client->phone }}" />

                        @error('mobile_phone')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex -mx-2">
                        <div class="flex-1 px-2 mb-4">
                            <label for="password" class="block text-xs font-bold text-gray-600 uppercase">Mot de passe</label>

                            <input type="password" name="password" id="password" class="px-4 py-2 block w-full border rounded" />

                            @error('password')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex-1 px-2 mb-4">
                            <label for="password_confirmation" class="block text-xs font-bold text-gray-600 uppercase">Confirmer mot de passe</label>

                            <input type="password" name="password_confirmation" id="password_confirmation" class="px-4 py-2 block w-full border rounded" />

                            @error('password_confirmation')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="text-center">
                        <button class="px-4 py-2 bg-blue-500 rounded text-white">Continuer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
