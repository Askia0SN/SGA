<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'EPF Africa - Admissions' }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(91,44,111,0.12),_transparent_35%),linear-gradient(135deg,_#faf9fc_0%,_#f5f1fa_100%)] font-sans text-slate-900 antialiased">
        <div class="min-h-screen flex flex-col">
            <header class="border-b border-red-100/50 bg-white/90 backdrop-blur-xl">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
                    <a href="{{ route('programmes.index') }}" class="flex items-center gap-3">
                        <img src="{{ asset('images/logo-sga.png') }}" alt="EPF Africa Logo" class="h-14 w-14 object-contain">
                        <div>
                            <p class="text-lg font-bold text-purple-900">EPF Africa</p>
                            <p class="text-xs text-red-600 font-semibold">Engineering School</p>
                        </div>
                    </a>


               <nav class="order-3 flex w-full items-center gap-1 overflow-x-auto border-t border-[#eee8f7] pt-3 text-sm font-semibold sm:order-2 sm:w-auto sm:border-0 sm:pt-0">
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
            </div>
        </header>

        <main class="flex-1 mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
            @if (session('message'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 shadow-sm">
                    {{ session('message') }}
                </div>
            @endif

            {{ $slot }}
        </main>

        <x-footer class="mt-auto" />

        @livewireScripts

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const button = document.getElementById('copy-code-button');
                if (!button) return;

                const message = document.getElementById('copy-code-message');
                const code = button.previousElementSibling?.textContent?.trim();

                button.addEventListener('click', async function () {
                    if (!code) return;

                    try {
                        await navigator.clipboard.writeText(code);
                        if (message) {
                            message.classList.remove('hidden');
                            setTimeout(() => message.classList.add('hidden'), 3000);
                        }
                    } catch (error) {
                        console.error('Impossible de copier le code de suivi', error);
                    }
                });
            });
        </script>
    </body>
</html>
