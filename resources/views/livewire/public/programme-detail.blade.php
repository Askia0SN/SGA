<div class="space-y-8">
    <nav class="text-sm text-slate-500">
        <a href="{{ route('programmes.index') }}" class="font-medium text-slate-600 transition hover:text-purple-600">Programmes</a>
        <span class="mx-2">/</span>
        <span class="text-slate-900">{{ $programme->nom }}</span>
    </nav>

    <section class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-2xl shadow-slate-200/70">
        <div class="bg-gradient-to-r from-purple-700 via-red-600 to-rose-500 p-8 text-white sm:p-10">
            <div class="flex flex-wrap items-center gap-3">
                <span class="rounded-full border border-white/20 bg-white/15 px-3 py-1 text-sm font-semibold uppercase tracking-[0.2em]">
                    {{ $programme->libelleNiveau() }}
                </span>

                @if ($ouvert)
                    <span class="rounded-full bg-emerald-400/20 px-3 py-1 text-sm font-semibold text-emerald-50">
                        Candidatures ouvertes
                    </span>
                @else
                    <span class="rounded-full bg-rose-400/20 px-3 py-1 text-sm font-semibold text-rose-50">
                        Candidatures fermées
                    </span>
                @endif
            </div>

            <h1 class="mt-5 text-3xl font-bold sm:text-4xl">{{ $programme->nom }}</h1>
            <p class="mt-4 max-w-3xl text-base text-purple-50/90 sm:text-lg">{{ $programme->description }}</p>
        </div>

        <div class="p-8 sm:p-10">
            <div class="grid gap-6 lg:grid-cols-[1.25fr_0.75fr]">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">À propos de la formation</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-600">
                        Cette formation vous permet de préparer votre dossier avec une expérience fluide, claire et sécurisée.
                    </p>

                    <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <dt class="text-sm font-medium text-slate-500">Période de candidature</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900">
                                {{ $programme->date_ouverture->format('d/m/Y') }} — {{ $programme->date_fermeture->format('d/m/Y') }}
                            </dd>
                        </div>

                        @if ($programme->capacite_accueil)
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <dt class="text-sm font-medium text-slate-500">Capacité d'accueil</dt>
                                <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $programme->capacite_accueil }} places</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-6">
                    <h3 class="text-lg font-semibold text-slate-900">Documents requis</h3>
                    @if ($programme->typesDocuments->isNotEmpty())
                        <ul class="mt-4 space-y-3">
                            @foreach ($programme->typesDocuments as $document)
                                <li class="flex items-center gap-2 rounded-xl bg-white px-3 py-2 text-sm text-slate-700 shadow-sm">
                                    <svg class="h-4 w-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $document->nom }}
                                    @if ($document->pivot->obligatoire)
                                        <span class="text-xs font-semibold text-rose-600">obligatoire</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mt-4 text-sm text-slate-500">Aucun document spécifique n'est requis pour l'instant.</p>
                    @endif
                </div>
            </div>

            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('programmes.index') }}"
                   class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-5 py-2.75 text-sm font-semibold text-slate-700 transition hover:border-purple-300 hover:text-purple-700">
                    Retour aux programmes
                </a>

                @if ($ouvert)
                    <a href="{{ route('candidature.create', $programme) }}"
                       class="inline-flex items-center rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 px-5 py-2.75 text-sm font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:opacity-95">
                        Déposer ma candidature
                    </a>
                @endif
            </div>
        </div>
    </section>
</div>
