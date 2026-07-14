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
        <header class="border-b border-red-100/50 bg-white/90 backdrop-blur-xl">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
                <a href="{{ route('programmes.index') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-sga.png') }}" alt="EPF Africa Logo" class="h-14 w-14 object-contain">
                    <div>
                        <p class="text-lg font-bold text-purple-900">EPF Africa</p>
                        <p class="text-xs text-red-600 font-semibold">Engineering School</p>
                    </div>
                </a>

                <nav class="flex items-center gap-6 text-sm">
                    <a href="{{ route('programmes.index') }}" class="font-medium text-purple-700 transition hover:text-red-600">
                        Programmes
                    </a>
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
            @if (session('message'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 shadow-sm">
                    {{ session('message') }}
                </div>
            @endif

            {{ $slot }}
        </main>

        <footer class="mt-12 border-t border-white/70 bg-white/70 backdrop-blur">
            <div class="mx-auto max-w-7xl px-4 py-6 text-center text-sm text-slate-500 sm:px-6 lg:px-8">
                &copy; {{ date('Y') }} EPF Africa — Tous droits réservés
            </div>
        </footer>

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
