<div class="pb-12">
    <header class="border-b border-[#e8e2f5] bg-white">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-extrabold uppercase text-[#d91426]">{{ $estJury ? 'Espace jury' : 'Gestion des admissions' }}</p>
                    <h1 class="mt-1 text-2xl font-extrabold text-[#191339]">{{ $estJury ? 'Dossiers à évaluer' : 'Candidatures' }}</h1>
                </div>
                <p class="text-sm font-semibold text-[#6d6684]">{{ $indicateurs['total'] }} dossier(s) accessible(s)</p>
            </div>
        </div>
    </header>

    <div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        <section aria-label="Indicateurs" class="grid border border-[#e8e2f5] bg-white sm:grid-cols-2 {{ $estJury ? 'lg:grid-cols-5' : 'lg:grid-cols-4' }}">
            <div class="border-b border-[#e8e2f5] px-5 py-4 sm:border-r lg:border-b-0">
                <p class="text-xs font-bold uppercase text-[#6d6684]">Tous les dossiers</p>
                <p class="mt-1 text-2xl font-extrabold text-[#27185f]">{{ $indicateurs['total'] }}</p>
            </div>
            @if ($estJury)
                <div class="border-b border-[#e8e2f5] px-5 py-4 lg:border-b-0 lg:border-r">
                    <p class="text-xs font-bold uppercase text-[#6d6684]">À évaluer</p>
                    <p class="mt-1 text-2xl font-extrabold text-[#d91426]">{{ $indicateurs['a_evaluer'] }}</p>
                </div>
                <div class="border-b border-[#e8e2f5] px-5 py-4 sm:border-b-0 sm:border-r">
                    <p class="text-xs font-bold uppercase text-[#6d6684]">Compléments</p>
                    <p class="mt-1 text-2xl font-extrabold text-[#805c12]">{{ $indicateurs['complements'] }}</p>
                </div>
                <div class="border-b border-[#e8e2f5] px-5 py-4 lg:border-b-0 lg:border-r">
                    <p class="text-xs font-bold uppercase text-[#6d6684]">Admis</p>
                    <p class="mt-1 text-2xl font-extrabold text-[#17603a]">{{ $indicateurs['admises'] }}</p>
                </div>
                <div class="px-5 py-4">
                    <p class="text-xs font-bold uppercase text-[#6d6684]">Refusés</p>
                    <p class="mt-1 text-2xl font-extrabold text-[#b70f1e]">{{ $indicateurs['refusees'] }}</p>
                </div>
            @else
                <div class="border-b border-[#e8e2f5] px-5 py-4 lg:border-b-0 lg:border-r">
                    <p class="text-xs font-bold uppercase text-[#6d6684]">Nouvelles</p>
                    <p class="mt-1 text-2xl font-extrabold text-[#d91426]">{{ $indicateurs['nouvelles'] }}</p>
                </div>
                <div class="border-b border-[#e8e2f5] px-5 py-4 sm:border-b-0 sm:border-r">
                    <p class="text-xs font-bold uppercase text-[#6d6684]">En traitement</p>
                    <p class="mt-1 text-2xl font-extrabold text-[#185875]">{{ $indicateurs['en_traitement'] }}</p>
                </div>
                <div class="px-5 py-4">
                    <p class="text-xs font-bold uppercase text-[#6d6684]">Au jury</p>
                    <p class="mt-1 text-2xl font-extrabold text-[#4b3788]">{{ $indicateurs['jury'] }}</p>
                </div>
            @endif
        </section>

        <section class="border border-[#e8e2f5] bg-white p-5">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-[1.4fr_1fr_1fr_0.8fr_0.8fr_auto] xl:items-end">
                <div>
                    <label for="recherche" class="block text-xs font-extrabold uppercase text-[#6d6684]">Recherche</label>
                    <input id="recherche" type="search" wire:model.live.debounce.350ms="recherche" placeholder="Code, nom ou email"
                        class="mt-1 block w-full rounded-md border-[#d8d0ea] text-sm shadow-sm focus:border-[#d91426] focus:ring-[#d91426]">
                </div>
                <div>
                    <label for="programme" class="block text-xs font-extrabold uppercase text-[#6d6684]">Programme</label>
                    <select id="programme" wire:model.live="programme" class="mt-1 block w-full rounded-md border-[#d8d0ea] text-sm shadow-sm focus:border-[#d91426] focus:ring-[#d91426]">
                        <option value="">Tous</option>
                        @foreach ($programmes as $programmeOption)
                            <option value="{{ $programmeOption->id }}">{{ $programmeOption->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="statut" class="block text-xs font-extrabold uppercase text-[#6d6684]">Statut</label>
                    <select id="statut" wire:model.live="statut" class="mt-1 block w-full rounded-md border-[#d8d0ea] text-sm shadow-sm focus:border-[#d91426] focus:ring-[#d91426]">
                        <option value="">Tous</option>
                        @foreach ($statuts as $statutOption)
                            <option value="{{ $statutOption->value }}">{{ $statutOption->libelle() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="date-debut" class="block text-xs font-extrabold uppercase text-[#6d6684]">Du</label>
                    <input id="date-debut" type="date" wire:model.live="dateDebut" class="mt-1 block w-full rounded-md border-[#d8d0ea] text-sm shadow-sm focus:border-[#d91426] focus:ring-[#d91426]">
                </div>
                <div>
                    <label for="date-fin" class="block text-xs font-extrabold uppercase text-[#6d6684]">Au</label>
                    <input id="date-fin" type="date" wire:model.live="dateFin" class="mt-1 block w-full rounded-md border-[#d8d0ea] text-sm shadow-sm focus:border-[#d91426] focus:ring-[#d91426]">
                </div>
                <button type="button" wire:click="reinitialiserFiltres" class="h-10 rounded-md border border-[#d8d0ea] px-4 text-sm font-bold text-[#27185f] hover:border-[#27185f]">
                    Réinitialiser
                </button>
            </div>
        </section>

        <section class="overflow-hidden border border-[#e8e2f5] bg-white" wire:loading.class="opacity-60">
            <div class="overflow-x-auto">
                <table class="min-w-[960px] w-full divide-y divide-[#e8e2f5] text-left text-sm">
                    <thead class="bg-[#f7f5fb] text-xs uppercase text-[#6d6684]">
                        <tr>
                            <th class="px-4 py-3 font-extrabold">Candidat</th>
                            <th class="px-4 py-3 font-extrabold">Programme</th>
                            <th class="px-4 py-3 font-extrabold">Soumission</th>
                            <th class="px-4 py-3 font-extrabold">Dossier</th>
                            <th class="px-4 py-3 font-extrabold">Statut</th>
                            <th class="px-4 py-3 text-right font-extrabold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#eee8f7]">
                        @forelse ($candidatures as $candidature)
                            <tr wire:key="candidature-{{ $candidature->id }}" class="hover:bg-[#fbfafe]">
                                <td class="px-4 py-4">
                                    <p class="font-extrabold text-[#191339]">{{ $candidature->candidat->prenom }} {{ $candidature->candidat->nom }}</p>
                                    <p class="mt-1 text-xs text-[#6d6684]">{{ $candidature->candidat->email }}</p>
                                    <p class="mt-1 font-mono text-xs font-bold text-[#27185f]">{{ $candidature->code_suivi }}</p>
                                </td>
                                <td class="max-w-[260px] px-4 py-4 font-semibold text-[#423b57]">{{ $candidature->programme->nom }}</td>
                                <td class="whitespace-nowrap px-4 py-4 text-[#6d6684]">{{ $candidature->soumise_le?->format('d/m/Y H:i') ?? 'Non renseignée' }}</td>
                                <td class="px-4 py-4">
                                    @if ($candidature->dossierEstComplet())
                                        <span class="inline-flex rounded-full border border-[#b9dfcc] bg-[#f1fbf6] px-2.5 py-1 text-xs font-bold text-[#17603a]">Complet</span>
                                    @else
                                        <span class="inline-flex rounded-full border border-[#f0d8a8] bg-[#fff9e9] px-2.5 py-1 text-xs font-bold text-[#805c12]">À vérifier</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4"><x-statut-candidature :statut="$candidature->statut" /></td>
                                <td class="px-4 py-4 text-right">
                                    <a href="{{ route('candidatures.show', $candidature) }}" class="inline-flex rounded-md bg-[#27185f] px-3 py-2 text-xs font-bold text-white hover:bg-[#3b267e]">
                                        Ouvrir le dossier
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-14 text-center">
                                    <p class="font-bold text-[#27185f]">Aucune candidature trouvée</p>
                                    <p class="mt-1 text-sm text-[#6d6684]">Modifiez les filtres pour élargir les résultats.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div>{{ $candidatures->links() }}</div>
    </div>
</div>
