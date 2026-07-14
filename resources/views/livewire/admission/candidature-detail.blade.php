<div class="pb-12">
    <header class="border-b border-[#e8e2f5] bg-white">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <a href="{{ route('candidatures.index') }}" class="text-sm font-bold text-[#27185f] hover:text-[#d91426]">← Retour aux candidatures</a>
            <div class="mt-4 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-3">
                        <h1 class="text-2xl font-extrabold text-[#191339]">{{ $candidature->candidat->prenom }} {{ $candidature->candidat->nom }}</h1>
                        <x-statut-candidature :statut="$candidature->statut" />
                    </div>
                    <p class="mt-2 font-mono text-sm font-bold text-[#6d6684]">{{ $candidature->code_suivi }}</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    @can('prendreEnCharge', $candidature)
                        <button type="button" wire:click="prendreEnCharge" class="rounded-md bg-[#27185f] px-4 py-2.5 text-sm font-bold text-white hover:bg-[#3b267e]">
                            Prendre en charge
                        </button>
                    @endcan
                    @can('reprendreTraitement', $candidature)
                        <button type="button" wire:click="reprendreTraitement" class="rounded-md bg-[#27185f] px-4 py-2.5 text-sm font-bold text-white hover:bg-[#3b267e]">
                            Reprendre le traitement
                        </button>
                    @endcan
                    @can('transmettreAuJury', $candidature)
                        <button type="button" wire:click="transmettreAuJury" wire:confirm="Confirmer la transmission de ce dossier au jury ?"
                            @disabled(! $candidature->dossierEstComplet())
                            class="rounded-md bg-[#d91426] px-4 py-2.5 text-sm font-bold text-white hover:bg-[#b70f1e] disabled:cursor-not-allowed disabled:bg-[#c8c3d2]">
                            Transmettre au jury
                        </button>
                    @endcan
                </div>
            </div>
        </div>
    </header>

    <div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="border border-[#b9dfcc] bg-[#f1fbf6] px-4 py-3 text-sm font-semibold text-[#17603a]">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="border border-[#f0c8ce] bg-[#fff4f5] px-4 py-3 text-sm font-semibold text-[#b70f1e]">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1.5fr)_minmax(280px,0.7fr)]">
            <div class="space-y-6">
                <section class="border border-[#e8e2f5] bg-white">
                    <div class="border-b border-[#e8e2f5] px-5 py-4">
                        <h2 class="text-lg font-extrabold text-[#27185f]">Documents du dossier</h2>
                        <p class="mt-1 text-sm text-[#6d6684]">Les pièces obligatoires doivent être validées avant la transmission au jury.</p>
                    </div>

                    @if ($documentsManquants->isNotEmpty())
                        <div class="border-b border-[#f0d8a8] bg-[#fff9e9] px-5 py-4 text-sm text-[#805c12]">
                            <span class="font-extrabold">À compléter ou valider :</span>
                            {{ $documentsManquants->pluck('nom')->join(', ') }}
                        </div>
                    @endif

                    <div class="divide-y divide-[#eee8f7]">
                        @forelse ($candidature->documents as $document)
                            <article wire:key="document-{{ $document->id }}" class="p-5">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="break-words font-extrabold text-[#191339]">{{ $document->typeDocument?->nom ?? 'Autre document' }}</h3>
                                            @php
                                                $documentClasses = match ($document->statut) {
                                                    'valide' => 'border-[#b9dfcc] bg-[#f1fbf6] text-[#17603a]',
                                                    'rejete' => 'border-[#f0c8ce] bg-[#fff4f5] text-[#b70f1e]',
                                                    default => 'border-[#f0d8a8] bg-[#fff9e9] text-[#805c12]',
                                                };
                                                $documentLibelle = match ($document->statut) {
                                                    'valide' => 'Validé',
                                                    'rejete' => 'Rejeté',
                                                    default => 'En attente',
                                                };
                                            @endphp
                                            <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-bold {{ $documentClasses }}">{{ $documentLibelle }}</span>
                                        </div>
                                        <p class="mt-2 break-all text-sm text-[#6d6684]">{{ $document->nom_original }} · {{ number_format($document->taille_octets / 1024, 0, ',', ' ') }} Ko</p>
                                        @if ($document->verificateur)
                                            <p class="mt-1 text-xs text-[#6d6684]">Vérifié par {{ $document->verificateur->name }} le {{ $document->verifie_le?->format('d/m/Y à H:i') }}</p>
                                        @endif
                                        @if ($document->motif_rejet)
                                            <p class="mt-3 border-l-2 border-[#d91426] pl-3 text-sm text-[#b70f1e]">{{ $document->motif_rejet }}</p>
                                        @endif
                                    </div>

                                    <div class="flex shrink-0 flex-wrap gap-2">
                                        <a href="{{ route('documents.consulter', $document) }}" target="_blank" rel="noopener" class="rounded-md border border-[#d8d0ea] px-3 py-2 text-xs font-bold text-[#27185f] hover:border-[#27185f]">
                                            Consulter
                                        </a>
                                        @can('verifier', $document)
                                            <button type="button" wire:click="validerDocument({{ $document->id }})" class="rounded-md border border-[#9bcdb5] px-3 py-2 text-xs font-bold text-[#17603a] hover:bg-[#f1fbf6]">
                                                Valider
                                            </button>
                                            <button type="button" wire:click="preparerRejet({{ $document->id }})" class="rounded-md border border-[#efb9c0] px-3 py-2 text-xs font-bold text-[#b70f1e] hover:bg-[#fff4f5]">
                                                Rejeter
                                            </button>
                                        @endcan
                                    </div>
                                </div>

                                @if ($documentARejeter === $document->id)
                                    <form wire:submit="rejeterDocument" class="mt-4 border-t border-[#eee8f7] pt-4">
                                        <label for="motif-rejet-{{ $document->id }}" class="block text-sm font-extrabold text-[#27185f]">Motif du rejet</label>
                                        <textarea id="motif-rejet-{{ $document->id }}" wire:model="motifRejet" rows="3" maxlength="1000"
                                            class="mt-2 block w-full rounded-md border-[#d8d0ea] text-sm shadow-sm focus:border-[#d91426] focus:ring-[#d91426]"
                                            placeholder="Précisez clairement ce qui doit être corrigé"></textarea>
                                        @error('motifRejet') <p class="mt-1 text-sm font-semibold text-[#b70f1e]">{{ $message }}</p> @enderror
                                        <div class="mt-3 flex gap-2">
                                            <button type="submit" class="rounded-md bg-[#d91426] px-4 py-2 text-sm font-bold text-white hover:bg-[#b70f1e]">Confirmer le rejet</button>
                                            <button type="button" wire:click="fermerRejet" class="rounded-md border border-[#d8d0ea] px-4 py-2 text-sm font-bold text-[#27185f]">Annuler</button>
                                        </div>
                                    </form>
                                @endif
                            </article>
                        @empty
                            <div class="px-5 py-10 text-center">
                                <p class="font-bold text-[#27185f]">Aucun document déposé</p>
                                <p class="mt-1 text-sm text-[#6d6684]">Le dossier ne peut pas encore être transmis au jury.</p>
                            </div>
                        @endforelse
                    </div>
                </section>

                <section class="border border-[#e8e2f5] bg-white">
                    <div class="border-b border-[#e8e2f5] px-5 py-4">
                        <h2 class="text-lg font-extrabold text-[#27185f]">Historique du traitement</h2>
                    </div>
                    <div class="divide-y divide-[#eee8f7]">
                        @forelse ($candidature->historiques->sortByDesc('cree_le') as $historique)
                            <div class="px-5 py-4">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex flex-wrap items-center gap-2">
                                        @if ($historique->ancien_statut)
                                            <x-statut-candidature :statut="$historique->ancien_statut" />
                                            <span class="text-[#948da5]">→</span>
                                        @endif
                                        <x-statut-candidature :statut="$historique->nouveau_statut" />
                                    </div>
                                    <time class="text-xs font-semibold text-[#6d6684]">{{ $historique->cree_le?->format('d/m/Y à H:i') }}</time>
                                </div>
                                <p class="mt-2 text-sm text-[#6d6684]">{{ $historique->utilisateur?->name ?? ucfirst(str_replace('_', ' ', $historique->acteur)) }}</p>
                                @if ($historique->commentaire)
                                    <p class="mt-2 text-sm leading-6 text-[#423b57]">{{ $historique->commentaire }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="px-5 py-8 text-sm text-[#6d6684]">Aucune action historisée pour le moment.</p>
                        @endforelse
                    </div>
                </section>
            </div>

            <aside class="space-y-6">
                <section class="border border-[#e8e2f5] bg-white p-5">
                    <h2 class="text-base font-extrabold text-[#27185f]">Candidat</h2>
                    <dl class="mt-4 space-y-4 text-sm">
                        <div><dt class="font-bold text-[#6d6684]">Email</dt><dd class="mt-1 break-all text-[#191339]">{{ $candidature->candidat->email }}</dd></div>
                        <div><dt class="font-bold text-[#6d6684]">Téléphone</dt><dd class="mt-1 text-[#191339]">{{ $candidature->candidat->telephone ?: 'Non renseigné' }}</dd></div>
                        <div><dt class="font-bold text-[#6d6684]">Date de naissance</dt><dd class="mt-1 text-[#191339]">{{ $candidature->candidat->date_naissance?->format('d/m/Y') ?? 'Non renseignée' }}</dd></div>
                        <div><dt class="font-bold text-[#6d6684]">Pays</dt><dd class="mt-1 text-[#191339]">{{ $candidature->candidat->pays ?: 'Non renseigné' }}</dd></div>
                    </dl>
                </section>

                <section class="border border-[#e8e2f5] bg-white p-5">
                    <h2 class="text-base font-extrabold text-[#27185f]">Formation demandée</h2>
                    <p class="mt-3 font-extrabold leading-6 text-[#191339]">{{ $candidature->programme->nom }}</p>
                    <p class="mt-1 text-sm text-[#6d6684]">{{ $candidature->programme->libelleNiveau() }}</p>
                    <dl class="mt-5 space-y-4 border-t border-[#eee8f7] pt-4 text-sm">
                        <div><dt class="font-bold text-[#6d6684]">Dernière formation</dt><dd class="mt-1 text-[#191339]">{{ $candidature->derniere_formation ?: 'Non renseignée' }}</dd></div>
                        <div><dt class="font-bold text-[#6d6684]">Établissement</dt><dd class="mt-1 text-[#191339]">{{ $candidature->etablissement_origine ?: 'Non renseigné' }}</dd></div>
                        <div><dt class="font-bold text-[#6d6684]">Soumise le</dt><dd class="mt-1 text-[#191339]">{{ $candidature->soumise_le?->format('d/m/Y à H:i') ?? 'Non renseignée' }}</dd></div>
                    </dl>
                </section>

                @if ($candidature->lettre_motivation)
                    <section class="border border-[#e8e2f5] bg-white p-5">
                        <h2 class="text-base font-extrabold text-[#27185f]">Motivation</h2>
                        <p class="mt-3 whitespace-pre-line text-sm leading-6 text-[#423b57]">{{ $candidature->lettre_motivation }}</p>
                    </section>
                @endif
            </aside>
        </div>
    </div>
</div>
