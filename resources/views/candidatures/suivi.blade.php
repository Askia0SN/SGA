<x-public-layout title="Suivre une candidature - SGA EPF">
    @php
        $libellesStatut = [
            'brouillon' => 'Brouillon',
            'soumise' => 'Candidature soumise',
            'complement_demande' => 'Complement demande',
            'en_traitement_admission' => 'En cours d etude',
            'transmise_au_jury' => 'Transmise au jury',
            'admise' => 'Candidature admise',
            'refusee' => 'Candidature refusee',
            'abandonnee' => 'Candidature abandonnee',
        ];
    @endphp

    <section class="bg-white">
        <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
            <div class="text-center">
                <p class="text-sm font-extrabold uppercase text-[#d91426]">Suivi candidat</p>
                <h1 class="mt-3 text-3xl font-extrabold text-[#191339] sm:text-4xl">Ou en est ma candidature ?</h1>
                <p class="mt-4 text-base leading-7 text-[#6d6684]">Saisissez le code de suivi recu par email apres le depot de votre dossier.</p>
            </div>

            <form method="POST" action="{{ route('candidatures.suivi.rechercher') }}" class="mx-auto mt-8 max-w-xl rounded-lg border border-[#e8e2f5] bg-[#f7f5fb] p-6">
                @csrf
                <label for="code_suivi" class="block text-sm font-bold text-[#27185f]">Code de suivi</label>
                <div class="mt-2 flex flex-col gap-3 sm:flex-row">
                    <input id="code_suivi" name="code_suivi" type="text" value="{{ old('code_suivi', $codeSuivi ?? '') }}" maxlength="20" required autofocus autocomplete="off" placeholder="Ex. SGA-2026-ABC123" class="block w-full rounded-md border-[#d8d0ea] uppercase shadow-sm focus:border-[#d91426] focus:ring-[#d91426]">
                    <button type="submit" class="inline-flex shrink-0 items-center justify-center rounded-md bg-[#d91426] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#b70f1e]">
                        Rechercher
                    </button>
                </div>
                @error('code_suivi')
                    <p class="mt-2 text-sm font-semibold text-[#b70f1e]">{{ $message }}</p>
                @enderror
            </form>

            @isset($codeSuivi)
                @if ($candidature)
                    <section class="mx-auto mt-6 max-w-xl overflow-hidden rounded-lg border border-[#e8e2f5]">
                        <div class="border-b border-[#e8e2f5] bg-[#27185f] px-6 py-5 text-white">
                            <p class="text-xs font-bold uppercase text-white/70">Statut actuel</p>
                            <h2 class="mt-1 text-xl font-extrabold">{{ $libellesStatut[$candidature->statut] ?? $candidature->statut }}</h2>
                        </div>
                        <dl class="divide-y divide-[#eee8f7] bg-white px-6">
                            <div class="py-4 sm:flex sm:items-center sm:justify-between sm:gap-5">
                                <dt class="text-sm font-semibold text-[#6d6684]">Programme</dt>
                                <dd class="mt-1 text-sm font-bold text-[#191339] sm:mt-0 sm:text-right">{{ $candidature->programme->nom }}</dd>
                            </div>
                            <div class="py-4 sm:flex sm:items-center sm:justify-between sm:gap-5">
                                <dt class="text-sm font-semibold text-[#6d6684]">Code de suivi</dt>
                                <dd class="mt-1 text-sm font-extrabold text-[#d91426] sm:mt-0">{{ $candidature->code_suivi }}</dd>
                            </div>
                            <div class="py-4 sm:flex sm:items-center sm:justify-between sm:gap-5">
                                <dt class="text-sm font-semibold text-[#6d6684]">Dossier soumis le</dt>
                                <dd class="mt-1 text-sm font-bold text-[#191339] sm:mt-0">{{ $candidature->soumise_le?->format('d/m/Y a H:i') ?? 'Date non renseignee' }}</dd>
                            </div>
                        </dl>
                    </section>
                @else
                    <div class="mx-auto mt-6 max-w-xl rounded-lg border border-[#f0c8ce] bg-[#fff4f5] p-5 text-center">
                        <p class="font-bold text-[#b70f1e]">Aucune candidature ne correspond a ce code.</p>
                        <p class="mt-2 text-sm text-[#6d6684]">Verifiez le code figurant dans votre email de confirmation.</p>
                    </div>
                @endif
            @endisset
        </div>
    </section>
</x-public-layout>
