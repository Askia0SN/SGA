<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-extrabold uppercase text-[#d91426]">Back-office</p>
                <h2 class="text-2xl font-extrabold leading-tight text-[#191339]">
                    Tableau de bord
                </h2>
            </div>
            <p class="text-sm font-semibold text-[#6d6684]">Systeme d'admission EPF</p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid gap-6 px-4 sm:px-0 lg:grid-cols-[1fr_0.8fr]">
                <div class="rounded-lg border border-white bg-white p-6 shadow-xl shadow-[#27185f]/10">
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('images/logo-sga.png') }}" alt="SGA EPF" class="h-20 w-20 rounded-md object-contain">
                        <div>
                            <p class="text-xs font-extrabold uppercase text-[#d91426]">Session active</p>
                            <h3 class="mt-1 text-xl font-extrabold text-[#27185f]">Bienvenue, {{ Auth::user()->name }}</h3>
                            <p class="mt-2 text-sm leading-6 text-[#6d6684]">Le socle back-office est pret pour accueillir les prochains ecrans de gestion des candidatures.</p>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-3 lg:grid-cols-1">
                    <div class="rounded-md border border-[#eee8f7] bg-white p-5">
                        <p class="text-sm font-bold text-[#6d6684]">Candidatures</p>
                        <p class="mt-2 text-3xl font-extrabold text-[#27185f]">0</p>
                    </div>
                    <div class="rounded-md border border-[#eee8f7] bg-white p-5">
                        <p class="text-sm font-bold text-[#6d6684]">A transmettre</p>
                        <p class="mt-2 text-3xl font-extrabold text-[#d91426]">0</p>
                    </div>
                    <div class="rounded-md border border-[#eee8f7] bg-white p-5">
                        <p class="text-sm font-bold text-[#6d6684]">Programmes</p>
                        <p class="mt-2 text-3xl font-extrabold text-[#27185f]">{{ \App\Models\Programme::count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
