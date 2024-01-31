<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @laravelPWA
</head>
<body>
    <div class="w-screen h-screen flex items-center justify-center bg-gray-100 px-4 md:px-0">
        <div class="w-full max-w-lg bg-white shadow border rounded-lg">
            <img src="/logo.webp" alt="Calliopée" class="h-16 block mx-auto py-2">

            <div class="p-6">
                <h1 class="text-lg font-bold mb-4">Réinitialiser mon mot de passe</h1>

                @if (session('message'))
                    <div>{{ session('message') }}</div>
                @else
                    <form action="/password/reset" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="email" class="block text-xs font-bold text-gray-600 uppercase">Quelle est votre adresse e-mail ?</label>

                            <input type="email" name="email" id="email" class="px-4 py-2 block w-full border rounded" value="{{ old('email') }}" />

                            @error('email')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex justify-between">
                            <button class="ml-auto px-4 py-2 bg-blue-500 rounded text-white">Continuer</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
