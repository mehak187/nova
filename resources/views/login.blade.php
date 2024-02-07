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
                @if (!empty(session('message')))
                    <div class="mb-4 text-green-500 text-center">{{ session('message') }}</div>
                @endif

                <form action="/login" method="POST">
                    @csrf

                    <div class="mb-4">c:\Users\Mehak\Desktop\nova credentials.txt
                        <label for="email" class="block text-xs font-bold text-gray-600 uppercase">E-mail</label>

                        <input type="email" name="email" id="email" class="px-4 py-2 block w-full border rounded" value="{{ old('email') }}" />

                        @error('email')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex-1 mb-4">
                        <label for="password" class="block text-xs font-bold text-gray-600 uppercase">Mot de passe</label>

                        <input type="password" name="password" id="password" class="px-4 py-2 block w-full border rounded" />

                        @error('password')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between mb-4">
                        <div class="relative flex items-start">
                            <div class="flex items-center h-5">
                                <input id="remember" name="remember" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>

                            <div class="ml-3 text-sm">
                                <label for="remember" class="font-medium text-gray-700">Rester connecté</label>
                            </div>
                        </div>

                        <a href="/password/reset" class="text-sm">Mot de passe oublié ?</a>
                    </div>

                    <div class="flex justify-between">
                        <a href="/register" class="px-4 py-2 bg-gray-200 rounded text-gray-500">Créer un compte</a>
                        <button class="px-4 py-2 bg-blue-500 rounded text-white">Connexion</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
