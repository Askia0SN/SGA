<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'SGA EPF') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#f7f5fb] font-sans text-[#191339] antialiased">
        <div class="min-h-screen">
            <header class="border-b border-white/80 bg-white/90 backdrop-blur">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                    <a href="/" class="flex items-center gap-3">
                        <img src="{{ asset('images/logo-sga.png') }}" alt="SGA EPF" class="h-14 w-14 rounded-md object-contain">
                        <div>
                            <p class="text-sm font-extrabold uppercase text-[#27185f]">SGA EPF</p>
                            <p class="text-xs font-semibold text-[#d91426]">Systeme d'admission</p>
                        </div>
                    </a>

                    @if (Route::has('login'))
                        <nav class="flex items-center gap-2">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="rounded-md bg-[#27185f] px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-[#3a2386]">
                                    Tableau de bord
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="rounded-md px-4 py-2 text-sm font-semibold text-[#27185f] transition hover:bg-[#eee9fb]">
                                    Connexion
                                </a>

                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="hidden rounded-md bg-[#d91426] px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-[#b70f1e] sm:inline-flex">
                                        Creer un acces
                                    </a>
                                @endif
                            @endauth
                        </nav>
                    @endif
                </div>
            </header>

            <main>
                <section class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-12 sm:px-6 lg:grid-cols-[1.1fr_0.9fr] lg:px-8 lg:py-20">
                    <div>
                        <div class="inline-flex items-center rounded-full border border-[#e2d9f5] bg-white px-3 py-1 text-xs font-bold uppercase text-[#d91426] shadow-sm">
                            Gerer - Evaluer - Admettre
                        </div>

                        <h1 class="mt-6 max-w-3xl text-4xl font-extrabold leading-tight text-[#191339] sm:text-5xl">
                            Une admission EPF plus claire, plus rapide et mieux suivie.
                        </h1>

                        <p class="mt-5 max-w-2xl text-base leading-7 text-[#5a5570]">
                            Le SGA centralise les candidatures, les documents, les demandes de complement et les decisions du jury dans un parcours simple pour le candidat comme pour le service admission.
                        </p>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <a href="#" class="inline-flex items-center justify-center rounded-md bg-[#d91426] px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#b70f1e]">
                                Consulter les programmes
                            </a>
                            <a href="#" class="inline-flex items-center justify-center rounded-md border border-[#d8d0ea] bg-white px-5 py-3 text-sm font-bold text-[#27185f] shadow-sm transition hover:border-[#27185f]">
                                Suivre ma candidature
                            </a>
                        </div>
                    </div>

                    <div class="rounded-lg border border-white bg-white p-6 shadow-xl shadow-[#27185f]/10">
                        <img src="{{ asset('images/logo-sga.png') }}" alt="Systeme d'admission EPF" class="mx-auto h-56 w-56 object-contain sm:h-72 sm:w-72">
                        <div class="mt-6 grid grid-cols-3 gap-3 text-center">
                            <div class="rounded-md bg-[#f4f0fb] px-3 py-4">
                                <p class="text-2xl font-extrabold text-[#27185f]">01</p>
                                <p class="mt-1 text-xs font-semibold text-[#5a5570]">Depot</p>
                            </div>
                            <div class="rounded-md bg-[#fff0f2] px-3 py-4">
                                <p class="text-2xl font-extrabold text-[#d91426]">02</p>
                                <p class="mt-1 text-xs font-semibold text-[#5a5570]">Etude</p>
                            </div>
                            <div class="rounded-md bg-[#f4f0fb] px-3 py-4">
                                <p class="text-2xl font-extrabold text-[#27185f]">03</p>
                                <p class="mt-1 text-xs font-semibold text-[#5a5570]">Decision</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="border-y border-[#e8e2f5] bg-white">
                    <div class="mx-auto grid max-w-7xl gap-4 px-4 py-8 sm:grid-cols-3 sm:px-6 lg:px-8">
                        <div class="rounded-md border border-[#eee8f7] p-5">
                            <h2 class="text-sm font-extrabold uppercase text-[#27185f]">Candidat</h2>
                            <p class="mt-2 text-sm leading-6 text-[#5a5570]">Choix d'un programme, depot du dossier et suivi par code unique.</p>
                        </div>
                        <div class="rounded-md border border-[#eee8f7] p-5">
                            <h2 class="text-sm font-extrabold uppercase text-[#27185f]">Service admission</h2>
                            <p class="mt-2 text-sm leading-6 text-[#5a5570]">Verification des dossiers, complements et transmission au jury.</p>
                        </div>
                        <div class="rounded-md border border-[#eee8f7] p-5">
                            <h2 class="text-sm font-extrabold uppercase text-[#27185f]">Jury</h2>
                            <p class="mt-2 text-sm leading-6 text-[#5a5570]">Lecture des dossiers transmis et decision finale d'admission.</p>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </body>
</html>
