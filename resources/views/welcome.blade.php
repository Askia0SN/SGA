<x-public-layout title="Accueil candidat - SGA EPF">
    <section class="bg-white">
        <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-12 sm:px-6 lg:grid-cols-[1.15fr_0.85fr] lg:px-8 lg:py-16">
            <div>
                <p class="text-sm font-extrabold uppercase text-[#d91426]">Admissions EPF Africa</p>
                <h1 class="mt-4 max-w-3xl text-4xl font-extrabold leading-tight text-[#191339] sm:text-5xl">
                    Construisez votre avenir avec EPF Africa.
                </h1>
                <p class="mt-5 max-w-2xl text-base leading-7 text-[#5a5570]">
                    Decouvrez nos formations, choisissez le programme qui vous correspond et suivez chaque etape de votre candidature avec votre code personnel.
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('programmes.index') }}" class="inline-flex items-center justify-center rounded-md bg-[#d91426] px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#b70f1e] focus:outline-none focus:ring-2 focus:ring-[#d91426] focus:ring-offset-2">
                        Consulter les programmes
                    </a>
                    <a href="{{ route('candidatures.suivi') }}" class="inline-flex items-center justify-center rounded-md border border-[#d8d0ea] bg-white px-5 py-3 text-sm font-bold text-[#27185f] shadow-sm transition hover:border-[#27185f] hover:bg-[#f7f5fb] focus:outline-none focus:ring-2 focus:ring-[#27185f] focus:ring-offset-2">
                        Suivre ma candidature
                    </a>
                </div>
            </div>

            <div class="flex min-h-[340px] items-center justify-center border-l-0 border-[#eee8f7] lg:border-l">
                <img src="{{ asset('images/logo-sga.png') }}" alt="Systeme d'admission EPF" class="h-72 w-72 object-contain sm:h-80 sm:w-80">
            </div>
        </div>
    </section>

    <section class="border-y border-[#e8e2f5] bg-[#f7f5fb]">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-xs font-extrabold uppercase text-[#d91426]">Votre parcours</p>
                <h2 class="mt-2 text-2xl font-extrabold text-[#191339]">Une candidature en trois etapes</h2>
            </div>

            <div class="mt-7 grid gap-px overflow-hidden rounded-lg border border-[#e8e2f5] bg-[#e8e2f5] sm:grid-cols-3">
                <div class="bg-white p-6">
                    <p class="text-3xl font-extrabold text-[#27185f]">01</p>
                    <h3 class="mt-4 text-base font-extrabold text-[#191339]">Choisir un programme</h3>
                    <p class="mt-2 text-sm leading-6 text-[#6d6684]">Consultez les formations ouvertes et selectionnez celle qui correspond a votre projet.</p>
                </div>
                <div class="bg-white p-6">
                    <p class="text-3xl font-extrabold text-[#d91426]">02</p>
                    <h3 class="mt-4 text-base font-extrabold text-[#191339]">Soumettre le dossier</h3>
                    <p class="mt-2 text-sm leading-6 text-[#6d6684]">Renseignez vos informations et joignez les documents demandes par la formation.</p>
                </div>
                <div class="bg-white p-6">
                    <p class="text-3xl font-extrabold text-[#27185f]">03</p>
                    <h3 class="mt-4 text-base font-extrabold text-[#191339]">Suivre la decision</h3>
                    <p class="mt-2 text-sm leading-6 text-[#6d6684]">Utilisez le code recu par email pour consulter l'avancement de votre candidature.</p>
                </div>
            </div>
        </div>
    </section>
</x-public-layout>
