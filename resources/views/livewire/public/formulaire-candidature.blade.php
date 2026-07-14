<div class="mx-auto max-w-5xl">
    <div class="mb-8 overflow-hidden rounded-[2rem] border border-purple-100 bg-gradient-to-br from-purple-700 via-red-600 to-rose-500 p-8 text-white shadow-2xl shadow-purple-900/20 sm:p-10">
        <a href="{{ route('programmes.show', $programme) }}" class="inline-flex items-center rounded-full border border-white/20 bg-white/10 px-3 py-1 text-sm font-medium text-purple-50 transition hover:bg-white/20">
            ← Retour au programme
        </a>
        <h1 class="mt-4 text-3xl font-bold sm:text-4xl">Candidature — {{ $programme->nom }}</h1>
        <p class="mt-3 max-w-2xl text-base text-purple-50/90">Remplissez les informations demandées étape par étape pour finaliser votre candidature.</p>
    </div>

    <div class="mb-8 rounded-[1.5rem] border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex items-center justify-between text-sm text-slate-600">
            <span class="font-semibold text-slate-700">Étape {{ $etape }} / 4</span>
            <span>{{ $progression }}%</span>
        </div>
        <div class="mt-3 h-2 rounded-full bg-slate-100">
            <div class="h-2 rounded-full bg-gradient-to-r from-purple-600 to-red-500" style="width: {{ $progression }}%"></div>
        </div>
    </div>

    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/70 sm:p-8">
        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-700">
                <div class="font-semibold">Des erreurs empêchent la soumission :</div>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($etape === 1)
            <div class="grid gap-6 md:grid-cols-2">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <label class="block text-sm font-semibold text-slate-700">Nom</label>
                    <input type="text" wire:model="nom" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                    @error('nom') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <label class="block text-sm font-semibold text-slate-700">Prénom</label>
                    <input type="text" wire:model="prenom" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                    @error('prenom') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <label class="block text-sm font-semibold text-slate-700">Date de naissance</label>
                    <input type="date" wire:model="date_naissance" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                    @error('date_naissance') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <label class="block text-sm font-semibold text-slate-700">Email</label>
                    <input type="email" wire:model="email" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                    @error('email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <label class="block text-sm font-semibold text-slate-700">Téléphone</label>
                    <input type="text" wire:model="telephone" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                    @error('telephone') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <label class="block text-sm font-semibold text-slate-700">Pays</label>
                    <input type="text" wire:model="pays" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                    @error('pays') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <label class="block text-sm font-semibold text-slate-700">Adresse</label>
                    <textarea wire:model="adresse" rows="3" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition focus:border-purple-500 focus:ring-2 focus:ring-purple-100"></textarea>
                    @error('adresse') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        @elseif ($etape === 2)
            <div class="grid gap-6">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <label class="block text-sm font-semibold text-slate-700">Dernière formation</label>
                    <input type="text" wire:model="derniere_formation" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                    @error('derniere_formation') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <label class="block text-sm font-semibold text-slate-700">Établissement d'origine</label>
                    <input type="text" wire:model="etablissement_origine" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                    @error('etablissement_origine') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        @elseif ($etape === 3)
            <div>
                <label class="block text-sm font-semibold text-slate-700">Lettre de motivation (max 3000 caractères)</label>
                <textarea wire:model="lettre_motivation" rows="8" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition focus:border-purple-500 focus:ring-2 focus:ring-purple-100" maxlength="3000"></textarea>
                @error('lettre_motivation') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

        @else
            <div class="space-y-6">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Pièces jointes</h2>
                    <p class="mt-1 text-sm text-slate-600">Ajoutez les documents demandés pour finaliser votre candidature.</p>
                    <p wire:loading wire:target="documents" class="mt-2 text-sm font-medium text-purple-600">Téléversement en cours…</p>
                    @error('documents')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @if ($typesDocuments->isEmpty())
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-sm text-slate-600">Aucun document n’est requis pour ce programme. Vous pouvez soumettre votre candidature sans pièces jointes.</p>
                    </div>
                @else
                    @foreach ($typesDocuments as $type)
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <label class="block text-sm font-semibold text-slate-700">
                                {{ $type->nom === 'Lettre de motivation' ? 'CV' : $type->nom }}
                                @if ($type->pivot->obligatoire)
                                    <span class="text-rose-600">*</span>
                                @endif
                            </label>
                            <input type="file" wire:model="documents.{{ $type->id }}" class="mt-3 block w-full rounded-xl border border-dashed border-slate-300 bg-white px-4 py-3 text-sm text-slate-500 file:mr-4 file:rounded-full file:border-0 file:bg-purple-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-purple-700 hover:file:bg-purple-100">
                            @if (isset($documentsPersistes[$type->id]))
                                <p class="mt-2 text-sm font-medium text-emerald-700">
                                    ✓ Fichier enregistré : {{ $documentsPersistes[$type->id]['nom_original'] }}
                                </p>
                            @endif
                            @error('documents.' . $type->id) <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    @endforeach
                @endif
            </div>
        @endif
    </div>
    <div class="mt-4" x-data="{ submitted: false }" x-on:candidature-soumise.window="submitted = true">
    @error('workflow')
        <p class="text-sm font-medium text-red-600">{{ $message }}</p>
    @enderror

    @if (session('message'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
            {{ session('message') }}
        </div>
    @endif

    <div x-show="submitted" x-cloak class="mt-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
        Redirection vers la confirmation en cours…
    </div>
</div>

    <div class="mt-6 flex flex-wrap justify-between gap-3">
        @if ($etape > 1)
            <button type="button" wire:click="etapePrecedente" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                Précédent
            </button>
        @else
            <span></span>
        @endif

        <div class="flex flex-wrap gap-3">
            <button
                type="button"
                wire:click="sauvegarderBrouillon"
                wire:loading.attr="disabled"
                wire:target="sauvegarderBrouillon"
                class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="sauvegarderBrouillon">Sauvegarder brouillon</span>
                <span wire:loading wire:target="sauvegarderBrouillon">Sauvegarde...</span>
            </button>

            @if ($etape < 4)
                <button type="button" wire:click="etapeSuivante" class="rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:opacity-95">
                    Suivant
                </button>
            @else
                <button
                    type="button"
                    wire:click="soumettre"
                    wire:loading.attr="disabled"
                    wire:target="soumettre, documents"
                    class="rounded-xl bg-gradient-to-r from-emerald-600 to-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-emerald-600/20 transition hover:opacity-95 disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="soumettre, documents">Soumettre ma candidature</span>
                    <span wire:loading wire:target="soumettre">Soumission...</span>
                    <span wire:loading wire:target="documents">Téléversement...</span>
                </button>
            @endif
        </div>
    </div>
</div>
