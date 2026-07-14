<x-public-layout title="Accueil candidat - SGA EPF">
    <style>
        @keyframes floatY {0%{transform:translateY(0)}50%{transform:translateY(-10px)}100%{transform:translateY(0)}}
        @keyframes blobMove {0%{transform:translateY(0) scale(1)}50%{transform:translateY(-18px) scale(1.05)}100%{transform:translateY(0) scale(1)}}
        @keyframes gradientShift {0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        .animate-float{animation:floatY 6s ease-in-out infinite}
        .animate-blob{animation:blobMove 8s ease-in-out infinite}
        .gradient-animate{background-size:200% 200%; animation:gradientShift 10s ease infinite}
        .hero-deco{pointer-events:none}
    </style>

    <section class="relative overflow-hidden bg-transparent">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(217,20,38,0.12),_transparent_35%),radial-gradient(circle_at_bottom_right,_rgba(39,24,95,0.12),_transparent_30%)]"></div>
        <div class="absolute inset-0 animated-gradient-bg opacity-20 pointer-events-none mix-blend-overlay"></div>

        <div class="relative mx-auto flex max-w-7xl items-center justify-between gap-16 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
            <div class="w-1/2 max-w-2xl">
                <div class="inline-flex items-center rounded-full border border-[#f1dfe1] bg-[#fff5f6] px-3 py-1 text-sm font-semibold text-[#d91426] shadow-sm">
                    <span class="mr-2 h-2.5 w-2.5 rounded-full bg-[#d91426]"></span>
                    Admissions EPF Africa
                </div>

                <h1 class="mt-6 text-3xl font-black leading-tight text-[#191339] sm:text-4xl lg:text-5xl gradient-text fade-up delay-1">
                    Bienvenue à EPF Africa, votre parcours vers l'excellence commence ici.
                </h1>

                <p class="mt-5 text-base leading-7 text-[#5a5570] fade-up delay-2">
                    Admission simple, dossier facile, suivi clair.
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('programmes.index') }}" style="background: linear-gradient(90deg, #6f22de 0%, #d91426 50%, #191339 100%); color: white;" class="inline-flex items-center justify-center rounded-full px-6 py-3 text-sm font-bold shadow-lg shadow-[#d91426]/20 transition duration-200 hover:brightness-95 focus:outline-none focus:ring-2 focus:ring-[#d91426] focus:ring-offset-2">
                        Consulter les programmes
                    </a>
                    <a href="{{ route('candidatures.suivi') }}" class="inline-flex items-center justify-center rounded-full border border-[#d8d0ea] bg-white px-6 py-3 text-sm font-bold text-[#27185f] shadow-sm transition transform hover:-translate-y-0.5 hover:border-[#27185f] hover:bg-[#f7f5fb] focus:outline-none focus:ring-2 focus:ring-[#27185f] focus:ring-offset-2">
                        Suivre ma candidature
                    </a>
                </div>

                
            </div>

                <div class="relative flex w-1/2 items-center justify-start">
                    <div class="hero-deco absolute -left-8 -top-8 hidden md:block h-36 w-36 rounded-full bg-gradient-to-br from-[#6f22de]/30 to-[#d91426]/30 blur-3xl lg:block animate-blob"></div>
                    <div class="hero-deco absolute -right-6 bottom-6 hidden md:block h-28 w-28 rounded-full bg-[#6f22de]/20 blur-2xl lg:block animate-float"></div>
                    <div class="hero-deco absolute left-1/2 top-[-2rem] hidden md:block h-40 w-60 translate-x-[-50%] rounded-[2rem] bg-gradient-to-r from-[#6f22de]/10 via-[#d91426]/10 to-[#191339]/8 blur-3xl lg:block gradient-animate"></div>

                    <img src="{{ asset('images/logo-sga.png') }}" alt="Logo accueil" class="relative z-10 h-56 w-auto rounded-[1.5rem] border border-white/10 bg-white/90 p-3 shadow-[0_20px_60px_-18px_rgba(25,19,57,0.18)] object-contain sm:h-80 lg:h-72 lg:-ml-12 xl:-ml-24" />
                </div>
        </div>
    </section>

    <section class="border-y border-[#e8e2f5] bg-white/60 backdrop-blur-sm">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                <div class="max-w-2xl">
                    <p class="text-xs font-extrabold uppercase tracking-[0.24em] text-[#d91426]">Votre parcours</p>
                    <h2 class="mt-2 text-3xl font-black text-[#191339] sm:text-4xl">Une candidature fluide, claire et rassurante</h2>
                </div>
                
            </div>

            <div class="mt-8 grid gap-6 lg:grid-cols-3">
                <div class="rounded-2xl border border-[#e8e2f5] bg-white p-6 shadow-sm">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#fef2f3] text-2xl font-black text-[#d91426]">01</div>
                    <h3 class="mt-5 text-xl font-extrabold text-[#191339]">Choisir un programme</h3>
                    <p class="mt-3 text-sm leading-7 text-[#6d6684]">Explorez les formations disponibles et sélectionnez celle qui correspond le mieux à votre projet professionnel.</p>
                </div>
                <div class="rounded-2xl border border-[#e8e2f5] bg-white p-6 shadow-sm">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#f6f2ff] text-2xl font-black text-[#27185f]">02</div>
                    <h3 class="mt-5 text-xl font-extrabold text-[#191339]">Soumettre votre dossier</h3>
                    <p class="mt-3 text-sm leading-7 text-[#6d6684]">Renseignez vos informations, ajoutez les documents nécessaires et envoyez votre candidature en quelques clics.</p>
                </div>
                <div class="rounded-2xl border border-[#e8e2f5] bg-white p-6 shadow-sm">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#f7f5fb] text-2xl font-black text-[#191339]">03</div>
                    <h3 class="mt-5 text-xl font-extrabold text-[#191339]">Suivre la décision</h3>
                    <p class="mt-3 text-sm leading-7 text-[#6d6684]">Utilisez votre code de suivi pour consulter l’avancement de votre dossier et rester informé à chaque étape.</p>
                </div>
            </div>
        </div>
    </section>

    
</x-public-layout>
