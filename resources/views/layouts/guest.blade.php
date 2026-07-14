<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SGA EPF') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-[#191339] antialiased">
        <div class="flex min-h-screen flex-col items-center bg-[#f7f5fb] px-4 py-8 sm:justify-center">
            <div class="mb-6">
                <a href="{{ route('admission.accueil') }}" class="flex flex-col items-center">
                    <x-application-logo class="h-28 w-28 rounded-md" />
                    <span class="mt-3 text-xs font-extrabold uppercase text-[#d91426]">Admission EPF</span>
                </a>
            </div>

            <div class="w-full overflow-hidden rounded-lg border border-white bg-white px-6 py-6 shadow-xl shadow-[#27185f]/10 sm:max-w-md">
                {{ $slot }}
            </div>

            <a href="{{ route('admission.accueil') }}" class="mt-5 text-sm font-semibold text-[#6d6684] underline decoration-[#d8d0ea] underline-offset-4 hover:text-[#27185f]">
                Retour à l'espace admission
            </a>
        </div>
    </body>
</html>
