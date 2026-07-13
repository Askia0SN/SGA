<div class="mx-auto max-w-3xl">
    <div class="overflow-hidden rounded-[2rem] border border-emerald-200 bg-gradient-to-br from-emerald-50 via-white to-purple-50 p-8 shadow-2xl shadow-emerald-100 sm:p-10">
        <div class="inline-flex rounded-2xl bg-emerald-100 p-3 text-emerald-700">
            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h1 class="mt-5 text-3xl font-bold text-slate-900">Candidature enregistrée</h1>
        <p class="mt-3 text-base text-slate-600">
            @if ($candidature->statut === 'soumise')
                Votre candidature pour <span class="font-semibold text-slate-900">{{ $candidature->programme->nom }}</span> a bien été soumise.
            @else
                Votre brouillon de candidature pour <span class="font-semibold text-slate-900">{{ $candidature->programme->nom }}</span> a bien été enregistré.
            @endif
        </p>
    </div>

    <div class="mt-6 rounded-[2rem] border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/70 sm:p-8">
        <h2 class="text-xl font-semibold text-slate-900">Votre code de suivi</h2>
        <p class="mt-2 text-sm text-slate-600">Conservez ce code pour suivre l’état de votre dossier à tout moment.</p>

        <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="inline-flex rounded-2xl border border-purple-200 bg-purple-50 px-4 py-3 text-xl font-bold tracking-[0.25em] text-purple-700">
                {{ $candidature->code_suivi }}
            </div>
            <button id="copy-code-button" type="button" class="inline-flex items-center justify-center rounded-xl border border-purple-300 bg-white px-4 py-2.5 text-sm font-semibold text-purple-700 shadow-sm transition hover:bg-purple-50">
                Copier
            </button>
        </div>
        <p id="copy-code-message" class="mt-2 text-sm text-emerald-700 hidden">Code de suivi copié dans le presse-papiers.</p>

        <div class="mt-6 rounded-2xl bg-slate-50 p-4 text-sm text-slate-700">
            <p><span class="font-semibold text-slate-900">Candidat :</span> {{ $candidature->candidat->prenom }} {{ $candidature->candidat->nom }}</p>
            <p class="mt-2"><span class="font-semibold text-slate-900">Programme :</span> {{ $candidature->programme->nom }}</p>
            <p class="mt-2"><span class="font-semibold text-slate-900">Statut :</span> {{ $candidature->statut }}</p>
        </div>
    </div>

    <div class="mt-6 text-center">
        <a href="{{ route('programmes.index') }}" class="inline-flex rounded-xl bg-gradient-to-r from-purple-700 to-red-600 px-5 py-2.75 text-sm font-semibold text-white shadow-lg shadow-purple-700/20 transition hover:opacity-95">
            Retour aux programmes
        </a>
    </div>
</div>
