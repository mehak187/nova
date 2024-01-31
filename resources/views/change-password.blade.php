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

                <form action="{{ route('password-reset.update', ['token' => $token]) }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="password" class="block text-xs font-bold text-gray-600 uppercase">Nouveau mot de passe</label>

                        <input type="password" name="password" id="password" class="px-4 py-2 block w-full border rounded" />

                        @error('password')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="block text-xs font-bold text-gray-600 uppercase">Confirmer le mot de passe</label>

                        <input type="password" name="password_confirmation" id="password_confirmation" class="px-4 py-2 block w-full border rounded" />

                        @error('password_confirmation')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex justify-between">
                        <button class="ml-auto px-4 py-2 bg-blue-500 rounded text-white">Continuer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
