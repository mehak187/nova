<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Calliopée</title>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    @laravelPWA
</head>
<body class="@yield('bodyClass')">
    <div id="app">
        <notification></notification>

        <add-to-home-screen></add-to-home-screen>

        <ending-alert></ending-alert>

        <div class="h-screen overflow-hidden">
            <div class="z-50 flex items-center justify-between fixed top-0 left-0 w-screen bg-white shadow h-16 px-6">
                <img src="/logo.webp" alt="Calliopée" class="h-16 block py-2">

                @if (auth()->user()->has_unread_mail_notifications)
                    <a class="flex items-center ml-auto text-gray-700" href="/mail-notifications">
                        <span class="relative rounded-full bg-red-500 w-3 h-3 inline-flex items-center justify-center -mr-2 -mt-4"></span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                    </a>
                @endif

                <settings-menu
                    :username="{{ json_encode(auth()->user()->first_name . ' ' . auth()->user()->last_name) }}"
                    :purchased-minutes-table="{{ auth()->user()->purchased_minutes_table }}"
                    :purchased-minutes-office="{{ auth()->user()->purchased_minutes_office }}"
                    :included-minutes-table="{{ auth()->user()->included_minutes_table }}"
                    :included-minutes-office="{{ auth()->user()->included_minutes_office }}"
                    :has-door-access="{{ auth()->user()->door_access_enabled ? 'true' : 'false' }}"
                    :display-notifications="@json(auth()->user()->has_mail_notifications)"
                    :has-unread-notifications="@json(auth()->user()->has_unread_mail_notifications)"
                    csrf="{{ csrf_token() }}"
                ></settings-menu>
            </div>

            <div class="bg-gray-100 h-screen pt-16 pb-16">
                <div class="h-full overflow-y-auto">
                    @yield('content')
                </div>
            </div>

            <div class="z-40 fixed bottom-0 left-0 w-screen h-16">
                <div class="flex h-16 bg-gray-200">
                    <a href="/" class="flex-1 p-4 flex items-center justify-center border-t-2 {{ request()->is('/') ? 'text-blue-500 border-blue-500' : 'border-gray-300' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </a>

                    <a href="/scan" class="flex-1 p-4 flex items-center justify-center border-t-2 {{ request()->is('scan') ? 'text-blue-500 border-blue-500' : 'border-gray-300' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                        </svg>
                    </a>

                    <a href="/history" class="flex-1 p-4 flex items-center justify-center border-t-2 {{ request()->is('history') ? 'text-blue-500 border-blue-500' : 'border-gray-300' }}">
                        <div class="relative">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>

                            @if (!empty($activeShiftsCount))
                                <div class="absolute top-0 right-0 bg-red-500 w-5 h-5 rounded-full text-white text-xs inline-flex items-center justify-center -mt-3 -mr-3 font-semibold">{{ $activeShiftsCount }}</div>
                            @endif
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ mix('/js/app.js') }}" defer></script>
    @yield('scripts')
</body>
</html>
