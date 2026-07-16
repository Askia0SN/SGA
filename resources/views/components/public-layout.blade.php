@props(['title' => config('app.name', 'SGA EPF')])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen font-sans text-[#191339] antialiased" style="background: linear-gradient(135deg, #faf7ff 0%, #f5efff 50%, #fff3f5 100%);">
        
        <div class="min-h-screen flex flex-col">
            <header class="border-b border-[#e8e5f3] bg-white/95 backdrop-blur z-30">
                <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                    <a href="{{ route('accueil') }}" class="flex items-center gap-3">
                        <img src="{{ asset('images/logo-sga.png') }}" alt="SGA EPF" class="h-14 w-14 rounded-md object-contain">
                        <div>
                            <p class="text-sm font-extrabold uppercase text-[#27185f]">EPF Africa</p>
                            <p class="text-xs font-semibold text-[#d91426]">Admissions</p>
                        </div>
                    </a>

                    
                    <nav class="order-2 flex flex-1 items-center justify-center gap-6 text-sm font-semibold">
                        <a href="{{ route('accueil') }}" class="whitespace-nowrap rounded-md px-3 py-2 transition {{ request()->routeIs('accueil') ? 'bg-[#eee9fb] text-[#27185f]' : 'text-[#6d6684] hover:bg-[#f4f0fb] hover:text-[#27185f]' }}">
                            Accueil
                        </a>
                        <a href="{{ route('programmes.index') }}" class="whitespace-nowrap rounded-md px-3 py-2 transition {{ request()->routeIs('programmes.*') ? 'bg-[#eee9fb] text-[#27185f]' : 'text-[#6d6684] hover:bg-[#f4f0fb] hover:text-[#27185f]' }}">
                            Programmes
                        </a>
                        <a href="{{ route('candidatures.suivi') }}" class="whitespace-nowrap rounded-md px-3 py-2 transition {{ request()->routeIs('candidatures.suivi*') ? 'bg-[#eee9fb] text-[#27185f]' : 'text-[#6d6684] hover:bg-[#f4f0fb] hover:text-[#27185f]' }}">
                            Suivre ma candidature
                        </a>
                    </nav>


                    <a href="{{ route('admission.accueil') }}" class="order-3 inline-flex items-center justify-center rounded-md border border-[#d8d0ea] bg-white px-4 py-2 text-sm font-bold text-[#27185f] transition hover:border-[#27185f] hover:bg-[#f4f0fb] sm:order-3">
                        Espace admission
                    </a>
                </div>
            </header>

            <main class="flex-1">
                {{ $slot }}
            </main>

            <x-footer class="mt-auto" />
        </div>
    </body>
</html>
