<x-public-layout title="Accueil candidat - SGA EPF">
    <section class="border-b border-[#e8e2f5] bg-white">
        <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-10 sm:px-6 sm:py-14 lg:grid-cols-[minmax(0,1.15fr)_minmax(320px,0.85fr)] lg:gap-16 lg:px-8 lg:py-16">
            <div class="min-w-0 max-w-3xl">
                <div class="inline-flex items-center gap-2 rounded-md border border-[#f0cbd0] bg-[#fff7f8] px-3 py-2 text-xs font-extrabold uppercase text-[#b70f1e]">
                    <span class="h-2 w-2 rounded-full bg-[#d91426]"></span>
                    Admissions EPF Africa
                </div>

                <h1 class="mt-6 text-4xl font-black leading-[1.08] text-[#191339] sm:text-5xl lg:text-[3.5rem]">
                    Votre avenir d’ingénieur
                    <span class="block text-[#d91426]">commence ici.</span>
                </h1>

                <p class="mt-6 max-w-2xl text-base leading-7 text-[#5f5875] sm:text-lg sm:leading-8">
                    Découvrez les programmes EPF Africa, déposez votre dossier en ligne et suivez chaque étape de votre candidature depuis un espace simple et sécurisé.
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                    <a href="{{ route('programmes.index') }}" class="inline-flex min-h-12 items-center justify-center rounded-md bg-[#d91426] px-6 py-3 text-sm font-extrabold text-white shadow-lg shadow-[#d91426]/20 transition hover:bg-[#b70f1e] focus:outline-none focus:ring-2 focus:ring-[#d91426] focus:ring-offset-2">
                        Consulter les programmes
                    </a>
                    <a href="{{ route('candidatures.suivi') }}" class="inline-flex min-h-12 items-center justify-center rounded-md border border-[#cfc7e2] bg-white px-6 py-3 text-sm font-extrabold text-[#27185f] transition hover:border-[#27185f] hover:bg-[#f7f5fb] focus:outline-none focus:ring-2 focus:ring-[#27185f] focus:ring-offset-2">
                        Suivre ma candidature
                    </a>
                </div>

                <dl class="mt-10 hidden max-w-2xl grid-cols-3 border-y border-[#e8e2f5] py-4 sm:grid">
                    <div class="pr-3">
                        <dt class="text-xs font-bold text-[#817a94]">Dépôt</dt>
                        <dd class="mt-1 text-sm font-extrabold text-[#27185f]">100 % en ligne</dd>
                    </div>
                    <div class="border-x border-[#e8e2f5] px-3 sm:px-5">
                        <dt class="text-xs font-bold text-[#817a94]">Suivi</dt>
                        <dd class="mt-1 text-sm font-extrabold text-[#27185f]">Code personnel</dd>
                    </div>
                    <div class="pl-3 sm:pl-5">
                        <dt class="text-xs font-bold text-[#817a94]">Décision</dt>
                        <dd class="mt-1 text-sm font-extrabold text-[#27185f]">Notification email</dd>
                    </div>
                </dl>
            </div>

            <div class="relative mx-auto hidden w-full max-w-[390px] lg:mx-0 lg:block lg:justify-self-end">
                <div class="absolute -bottom-3 -left-3 h-full w-full rounded-lg bg-[#27185f]" aria-hidden="true"></div>
                <figure class="relative aspect-square overflow-hidden rounded-lg border border-[#e8e2f5] bg-white p-6 shadow-[0_24px_50px_-28px_rgba(25,19,57,0.45)] sm:p-8">
                    <img src="{{ asset('images/logo-sga.png') }}" alt="Système d’admission EPF" class="h-full w-full object-contain">
                </figure>
                <div class="relative ml-auto mt-6 w-fit border-l-4 border-[#d91426] bg-[#fff7f8] px-4 py-3 text-right">
                    <p class="text-xs font-extrabold uppercase text-[#d91426]">Rentrée 2026–2027</p>
                    <p class="mt-1 text-sm font-bold text-[#27185f]">Construisez votre projet avec EPF Africa</p>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-[#f7f5fb]">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8 lg:py-16">
            <div class="max-w-3xl">
                <p class="text-xs font-extrabold uppercase text-[#d91426]">Votre parcours</p>
                <h2 class="mt-3 text-3xl font-black leading-tight text-[#191339] sm:text-4xl">Candidatez en trois étapes</h2>
                <p class="mt-4 text-base leading-7 text-[#6d6684]">Un parcours conçu pour vous permettre de savoir où en est votre dossier à tout moment.</p>
            </div>

            <ol class="mt-10 grid border-t border-[#d8d0ea] md:grid-cols-3">
                <li class="relative border-b border-[#d8d0ea] py-7 md:border-b-0 md:border-r md:pr-8">
                    <span class="absolute -top-4 left-0 flex h-8 w-8 items-center justify-center rounded-md bg-[#d91426] text-xs font-black text-white">01</span>
                    <h3 class="mt-2 text-lg font-extrabold text-[#191339]">Choisissez votre programme</h3>
                    <p class="mt-3 text-sm leading-7 text-[#6d6684]">Comparez les formations ouvertes et sélectionnez celle qui correspond à votre projet.</p>
                </li>
                <li class="relative border-b border-[#d8d0ea] py-7 md:border-b-0 md:border-r md:px-8">
                    <span class="absolute -top-4 left-0 flex h-8 w-8 items-center justify-center rounded-md bg-[#6f22de] text-xs font-black text-white md:left-8">02</span>
                    <h3 class="mt-2 text-lg font-extrabold text-[#191339]">Déposez votre dossier</h3>
                    <p class="mt-3 text-sm leading-7 text-[#6d6684]">Renseignez vos informations et transmettez les documents demandés en toute sécurité.</p>
                </li>
                <li class="relative py-7 md:pl-8">
                    <span class="absolute -top-4 left-0 flex h-8 w-8 items-center justify-center rounded-md bg-[#27185f] text-xs font-black text-white md:left-8">03</span>
                    <h3 class="mt-2 text-lg font-extrabold text-[#191339]">Suivez votre candidature</h3>
                    <p class="mt-3 text-sm leading-7 text-[#6d6684]">Consultez l’avancement avec votre code de suivi et recevez les décisions par email.</p>
                </li>
            </ol>
        </div>
    </section>
</x-public-layout>
