<div class="space-y-8">
    <section class="overflow-hidden rounded-[2rem] border border-white/70 bg-gradient-to-br from-purple-700 via-red-600 to-rose-500 p-8 text-white shadow-2xl shadow-purple-900/20 sm:p-10">
        <div class="max-w-3xl">
            <p class="text-sm font-semibold uppercase tracking-[0.35em] text-purple-100">Admissions EPF Africa</p>
            <h1 class="mt-3 text-3xl font-bold sm:text-4xl">Choisissez votre formation et démarrez votre candidature</h1>
            <p class="mt-4 max-w-2xl text-base text-purple-50/90 sm:text-lg">
                Un parcours simple, rapide et élégant pour déposer votre dossier en quelques minutes.
            </p>

            <div class="mt-6 flex flex-wrap gap-3">
                <span class="rounded-full border border-white/20 bg-white/15 px-4 py-2 text-sm font-medium">Candidature en ligne</span>
                <span class="rounded-full border border-white/20 bg-white/15 px-4 py-2 text-sm font-medium">Suivi personnalisé</span>
                <span class="rounded-full border border-white/20 bg-white/15 px-4 py-2 text-sm font-medium">Documents sécurisés</span>
            </div>
        </div>
    </section>

    <div class="grid gap-6 lg:grid-cols-2">
        @forelse ($programmes as $programme)
            <article class="group overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white shadow-lg shadow-slate-200/70 transition duration-300 hover:-translate-y-1 hover:shadow-xl">
                <div class="h-2 bg-gradient-to-r from-purple-500 via-red-500 to-rose-500"></div>
                <div class="p-6 sm:p-7">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <span class="inline-flex items-center rounded-full bg-purple-50 px-2.75 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-purple-700">
                                {{ $programme->libelleNiveau() }}
                            </span>
                            <h2 class="mt-3 text-xl font-semibold text-slate-900">{{ $programme->nom }}</h2>
                        </div>

                        @if ($programme->ouvert)
                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                Ouvert
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                Fermé
                            </span>
                        @endif
                    </div>

                    <p class="mt-4 text-sm leading-6 text-slate-600 line-clamp-3">{{ $programme->description }}</p>

                    <div class="mt-5 rounded-2xl bg-slate-50 p-4 text-sm text-slate-600">
                        <p class="font-medium text-slate-700">Période de candidature</p>
                        <p class="mt-1">Du {{ $programme->date_ouverture->format('d/m/Y') }} au {{ $programme->date_fermeture->format('d/m/Y') }}</p>
                    </div>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="{{ route('programmes.show', $programme) }}"
                           class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-purple-300 hover:text-purple-700">
                            Voir le détail
                        </a>

                        @if ($programme->ouvert)
                            <a href="{{ route('candidature.create', $programme) }}"
                               class="inline-flex items-center rounded-xl bg-gradient-to-r from-purple-700 to-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-purple-700/20 transition hover:opacity-95">
                                Déposer ma candidature
                            </a>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full rounded-[1.75rem] border border-dashed border-slate-300 bg-white/70 p-10 text-center text-slate-500 shadow-sm">
                Aucun programme n'est disponible pour le moment.
            </div>
        @endforelse
    </div>
</div>
