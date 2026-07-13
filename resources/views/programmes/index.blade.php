<x-public-layout title="Programmes - SGA EPF">
    <section class="bg-white">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
            <div class="max-w-3xl">
                <p class="text-sm font-extrabold uppercase text-[#d91426]">Formations EPF Africa</p>
                <h1 class="mt-3 text-3xl font-extrabold text-[#191339] sm:text-4xl">Choisissez votre programme</h1>
                <p class="mt-4 text-base leading-7 text-[#6d6684]">Explorez les programmes actuellement proposes et preparez votre projet de candidature.</p>
            </div>

            @if ($programmes->isEmpty())
                <div class="mt-10 rounded-lg border border-[#e8e2f5] bg-[#f7f5fb] p-8 text-center">
                    <p class="font-bold text-[#27185f]">Aucun programme n'est ouvert pour le moment.</p>
                    <p class="mt-2 text-sm text-[#6d6684]">Revenez prochainement pour consulter les nouvelles admissions.</p>
                </div>
            @else
                <div class="mt-10 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($programmes as $programme)
                        <article class="flex h-full flex-col rounded-lg border border-[#e8e2f5] bg-white p-6 shadow-sm">
                            <div class="flex items-center justify-between gap-3">
                                <span class="rounded-full bg-[#eee9fb] px-3 py-1 text-xs font-extrabold uppercase text-[#27185f]">
                                    {{ str_replace('_', ' ', $programme->niveau) }}
                                </span>
                                @if ($programme->date_fermeture)
                                    <span class="text-xs font-semibold text-[#6d6684]">Jusqu'au {{ $programme->date_fermeture->format('d/m/Y') }}</span>
                                @endif
                            </div>
                            <h2 class="mt-5 text-xl font-extrabold leading-7 text-[#191339]">{{ $programme->nom }}</h2>
                            <p class="mt-3 flex-1 text-sm leading-6 text-[#6d6684]">{{ $programme->description }}</p>
                            <div class="mt-6 border-t border-[#eee8f7] pt-4">
                                <p class="text-sm font-bold text-[#d91426]">Candidature en ligne prochainement</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</x-public-layout>
