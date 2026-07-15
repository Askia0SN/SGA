<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-extrabold uppercase text-[#d91426]">{{ $estJury ? 'Espace jury' : 'Back-office admission' }}</p>
                <h1 class="mt-1 text-2xl font-extrabold text-[#191339]">Tableau de bord</h1>
            </div>
            <a href="{{ route('candidatures.index') }}" class="inline-flex rounded-md bg-[#27185f] px-4 py-2.5 text-sm font-bold text-white hover:bg-[#3b267e]">
                {{ $estJury ? 'Évaluer les dossiers' : 'Gérer les candidatures' }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section aria-label="Indicateurs principaux" class="grid border border-[#e8e2f5] bg-white sm:grid-cols-2 lg:grid-cols-4">
                <div class="border-b border-[#e8e2f5] px-5 py-4 sm:border-r lg:border-b-0">
                    <p class="text-xs font-bold uppercase text-[#6d6684]">Dossiers accessibles</p>
                    <p class="mt-1 text-3xl font-extrabold text-[#27185f]">{{ $nombreCandidatures }}</p>
                </div>
                <div class="border-b border-[#e8e2f5] px-5 py-4 lg:border-b-0 lg:border-r">
                    <p class="text-xs font-bold uppercase text-[#6d6684]">Dossiers incomplets</p>
                    <p class="mt-1 text-3xl font-extrabold text-[#805c12]">{{ $nombreIncomplets }}</p>
                </div>
                <div class="border-b border-[#e8e2f5] px-5 py-4 sm:border-b-0 sm:border-r">
                    <p class="text-xs font-bold uppercase text-[#6d6684]">En attente du jury</p>
                    <p class="mt-1 text-3xl font-extrabold text-[#d91426]">{{ $nombreEnAttenteJury }}</p>
                </div>
                <div class="px-5 py-4">
                    <p class="text-xs font-bold uppercase text-[#6d6684]">Décisions rendues</p>
                    <p class="mt-1 text-3xl font-extrabold text-[#17603a]">{{ $nombreDecisions }}</p>
                </div>
            </section>

            <div class="grid gap-6 lg:grid-cols-[minmax(0,1.3fr)_minmax(300px,0.7fr)]">
                <section class="border border-[#e8e2f5] bg-white">
                    <div class="border-b border-[#e8e2f5] px-5 py-4">
                        <h2 class="text-lg font-extrabold text-[#27185f]">Répartition par statut</h2>
                    </div>
                    <div class="grid sm:grid-cols-2">
                        @foreach ($parStatut as $statut => $donnees)
                            <div class="flex items-center justify-between gap-4 border-b border-[#eee8f7] px-5 py-4 sm:odd:border-r">
                                <x-statut-candidature :statut="$statut" />
                                <span class="text-xl font-extrabold text-[#191339]">{{ $donnees['nombre'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="border border-[#e8e2f5] bg-white">
                    <div class="border-b border-[#e8e2f5] px-5 py-4">
                        <h2 class="text-lg font-extrabold text-[#27185f]">Repères</h2>
                    </div>
                    <dl class="divide-y divide-[#eee8f7] px-5 text-sm">
                        <div class="flex items-center justify-between gap-4 py-4">
                            <dt class="font-semibold text-[#6d6684]">Programmes actifs</dt>
                            <dd class="font-extrabold text-[#191339]">{{ $nombreProgrammes }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-4">
                            <dt class="font-semibold text-[#6d6684]">Taux de décision</dt>
                            <dd class="font-extrabold text-[#191339]">{{ $nombreCandidatures > 0 ? round(($nombreDecisions / $nombreCandidatures) * 100) : 0 }} %</dd>
                        </div>
                    </dl>

                    @if ($notifications->isNotEmpty())
                        <div class="border-t border-[#e8e2f5] px-5 py-4">
                            <h3 class="text-sm font-extrabold text-[#27185f]">Notifications récentes</h3>
                            <div class="mt-3 space-y-3">
                                @foreach ($notifications as $notification)
                                    <a href="{{ isset($notification->donnees['candidature_id']) ? route('candidatures.show', $notification->donnees['candidature_id']) : route('candidatures.index') }}" class="block border-l-2 border-[#d91426] pl-3 text-sm text-[#423b57] hover:text-[#27185f]">
                                        {{ $notification->message }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </section>
            </div>

            <section class="border border-[#e8e2f5] bg-white">
                <div class="border-b border-[#e8e2f5] px-5 py-4">
                    <h2 class="text-lg font-extrabold text-[#27185f]">Admissions et refus par programme</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-[620px] w-full divide-y divide-[#e8e2f5] text-left text-sm">
                        <thead class="bg-[#f7f5fb] text-xs uppercase text-[#6d6684]">
                            <tr>
                                <th class="px-5 py-3 font-extrabold">Programme</th>
                                <th class="px-5 py-3 text-center font-extrabold">Admissions</th>
                                <th class="px-5 py-3 text-center font-extrabold">Refus</th>
                                <th class="px-5 py-3 text-center font-extrabold">Total décidé</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#eee8f7]">
                            @forelse ($decisionsParProgramme as $ligne)
                                <tr>
                                    <td class="px-5 py-4 font-bold text-[#191339]">{{ $ligne['programme'] }}</td>
                                    <td class="px-5 py-4 text-center font-extrabold text-[#17603a]">{{ $ligne['admises'] }}</td>
                                    <td class="px-5 py-4 text-center font-extrabold text-[#b70f1e]">{{ $ligne['refusees'] }}</td>
                                    <td class="px-5 py-4 text-center font-extrabold text-[#27185f]">{{ $ligne['admises'] + $ligne['refusees'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-5 py-10 text-center text-[#6d6684]">Aucune décision enregistrée pour le moment.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="border border-[#e8e2f5] bg-white">
                <div class="flex items-center justify-between gap-4 border-b border-[#e8e2f5] px-5 py-4">
                    <h2 class="text-lg font-extrabold text-[#27185f]">Dossiers récents</h2>
                    <a href="{{ route('candidatures.index') }}" class="text-sm font-bold text-[#d91426] hover:text-[#b70f1e]">Voir tout</a>
                </div>
                <div class="divide-y divide-[#eee8f7]">
                    @forelse ($candidaturesRecentes as $candidature)
                        <a href="{{ route('candidatures.show', $candidature) }}" class="flex flex-col gap-3 px-5 py-4 hover:bg-[#fbfafe] sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-extrabold text-[#191339]">{{ $candidature->candidat->prenom }} {{ $candidature->candidat->nom }}</p>
                                <p class="mt-1 text-sm text-[#6d6684]">{{ $candidature->programme->nom }} · {{ $candidature->code_suivi }}</p>
                            </div>
                            <x-statut-candidature :statut="$candidature->statut" />
                        </a>
                    @empty
                        <p class="px-5 py-10 text-center text-sm text-[#6d6684]">Aucune candidature accessible.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
