<x-public-layout title="Espace admission - SGA EPF">
    <section class="bg-white">
        <div class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
            <div class="mx-auto max-w-2xl text-center">
                <p class="text-sm font-extrabold uppercase text-[#d91426]">Espace reserve au personnel</p>
                <h1 class="mt-3 text-3xl font-extrabold text-[#191339] sm:text-4xl">Service des admissions</h1>
                <p class="mt-4 text-base leading-7 text-[#6d6684]">
                    Connectez-vous pour traiter les dossiers, demander des complements et transmettre les candidatures au jury.
                </p>
            </div>

            @auth
                <div class="mx-auto mt-10 max-w-xl rounded-lg border border-[#e8e2f5] bg-[#f7f5fb] p-6 text-center">
                    <p class="text-sm text-[#6d6684]">Vous etes connecte en tant que <strong class="text-[#27185f]">{{ Auth::user()->name }}</strong>.</p>
                    <a href="{{ route('dashboard') }}" class="mt-5 inline-flex items-center justify-center rounded-md bg-[#27185f] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#3a2386]">
                        Acceder au tableau de bord
                    </a>
                </div>
            @else
                <div class="mx-auto mt-10 grid max-w-3xl gap-px overflow-hidden rounded-lg border border-[#e8e2f5] bg-[#e8e2f5] sm:grid-cols-2">
                    <section class="bg-white p-7">
                        <h2 class="text-xl font-extrabold text-[#27185f]">J'ai deja un compte</h2>
                        <p class="mt-3 min-h-12 text-sm leading-6 text-[#6d6684]">Retrouvez votre espace de travail avec votre adresse email professionnelle.</p>
                        <a href="{{ route('login') }}" class="mt-6 inline-flex w-full items-center justify-center rounded-md bg-[#27185f] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#3a2386]">
                            Se connecter
                        </a>
                    </section>

                    <section class="bg-white p-7">
                        <h2 class="text-xl font-extrabold text-[#27185f]">Je cree mon acces</h2>
                        <p class="mt-3 min-h-12 text-sm leading-6 text-[#6d6684]">Creez votre compte. Le super administrateur pourra ensuite vous attribuer le role adapte.</p>
                        <a href="{{ route('register') }}" class="mt-6 inline-flex w-full items-center justify-center rounded-md bg-[#d91426] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#b70f1e]">
                            S'inscrire
                        </a>
                    </section>
                </div>
            @endauth

            <div class="mt-8 text-center">
                <a href="{{ route('accueil') }}" class="text-sm font-bold text-[#6d6684] underline decoration-[#d8d0ea] underline-offset-4 hover:text-[#27185f]">
                    Retourner a l'espace candidat
                </a>
            </div>
        </div>
    </section>
</x-public-layout>
