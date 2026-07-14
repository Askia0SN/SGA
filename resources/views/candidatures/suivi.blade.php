<x-public-layout title="Suivre une candidature - SGA EPF">
    <section class="bg-white">
        <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
            <div class="text-center">
                <p class="text-sm font-extrabold uppercase text-[#d91426]">Suivi candidat</p>
                <h1 class="mt-3 text-3xl font-extrabold text-[#191339] sm:text-4xl">Ou en est ma candidature ?</h1>
                <p class="mt-4 text-base leading-7 text-[#6d6684]">Saisissez le code de suivi et l'adresse email utilises lors du depot de votre dossier.</p>
            </div>

            <form method="POST" action="{{ route('candidatures.suivi.rechercher') }}" class="mx-auto mt-8 max-w-xl rounded-lg border border-[#e8e2f5] bg-[#f7f5fb] p-6">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-bold text-[#27185f]">Adresse email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $email ?? '') }}" required autocomplete="email" placeholder="nom@exemple.com" class="mt-2 block w-full rounded-md border-[#d8d0ea] shadow-sm focus:border-[#d91426] focus:ring-[#d91426]">
                    @error('email')
                        <p class="mt-2 text-sm font-semibold text-[#b70f1e]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-4">
                    <label for="code_suivi" class="block text-sm font-bold text-[#27185f]">Code de suivi</label>
                    <input id="code_suivi" name="code_suivi" type="text" value="{{ old('code_suivi', $codeSuivi ?? '') }}" maxlength="20" required autofocus autocomplete="off" placeholder="Ex. SGA-2026-ABC123" class="block w-full rounded-md border-[#d8d0ea] uppercase shadow-sm focus:border-[#d91426] focus:ring-[#d91426]">
                    @error('code_suivi')
                        <p class="mt-2 text-sm font-semibold text-[#b70f1e]">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="mt-5 inline-flex w-full items-center justify-center rounded-md bg-[#d91426] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#b70f1e]">
                    Rechercher ma candidature
                </button>
            </form>

            @if (session('status'))
                <div class="mx-auto mt-6 max-w-xl rounded-md border border-[#b9dfcc] bg-[#f1fbf6] px-5 py-4 text-sm font-bold text-[#17603a]">
                    {{ session('status') }}
                </div>
            @endif

            @isset($codeSuivi)
                @if ($candidature)
                    <section class="mx-auto mt-6 max-w-xl overflow-hidden rounded-lg border border-[#e8e2f5]">
                        <div class="border-b border-[#e8e2f5] bg-[#27185f] px-6 py-5 text-white">
                            <p class="text-xs font-bold uppercase text-white/70">Statut actuel</p>
                            <h2 class="mt-1 text-xl font-extrabold">{{ $candidature->statut->libelle() }}</h2>
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

                        @if ($candidature->messages->isNotEmpty())
                            <div class="border-t border-[#e8e2f5] bg-[#fff9e9] px-6 py-5">
                                <h3 class="text-sm font-extrabold text-[#805c12]">Échanges sur le dossier</h3>
                                <div class="mt-3 space-y-3">
                                    @foreach ($candidature->messages as $messageCandidature)
                                        <article class="border-l-2 border-[#d91426] pl-3">
                                            <p class="mb-1 text-xs font-extrabold uppercase text-[#805c12]">
                                                {{ $messageCandidature->type === 'message_candidat' ? 'Votre réponse' : 'Service admission' }}
                                            </p>
                                            <p class="whitespace-pre-line text-sm leading-6 text-[#423b57]">{{ $messageCandidature->contenu }}</p>
                                            <time class="mt-1 block text-xs font-semibold text-[#6d6684]">{{ $messageCandidature->created_at?->format('d/m/Y à H:i') }}</time>
                                        </article>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($candidature->statut === \App\Enums\StatutCandidature::ComplementDemande)
                            <form method="POST" action="{{ route('candidatures.complement.envoyer') }}" enctype="multipart/form-data" class="border-t border-[#e8e2f5] bg-white px-6 py-6">
                                @csrf
                                <input type="hidden" name="code_suivi" value="{{ $candidature->code_suivi }}">
                                <input type="hidden" name="email" value="{{ $email }}">

                                <h3 class="text-lg font-extrabold text-[#27185f]">Transmettre mon complément</h3>

                                <div class="mt-4">
                                    <label for="message-complement" class="block text-sm font-bold text-[#27185f]">Message</label>
                                    <textarea id="message-complement" name="message" rows="4" maxlength="2000" class="mt-2 block w-full rounded-md border-[#d8d0ea] shadow-sm focus:border-[#d91426] focus:ring-[#d91426]">{{ old('message') }}</textarea>
                                    @error('message')
                                        <p class="mt-2 text-sm font-semibold text-[#b70f1e]">{{ $message }}</p>
                                    @enderror
                                </div>

                                @if ($candidature->programme->typesDocuments->isNotEmpty())
                                    <div class="mt-5 space-y-4">
                                        @foreach ($candidature->programme->typesDocuments as $typeDocument)
                                            @php($documentActuel = $candidature->documents->firstWhere('type_document_id', $typeDocument->id))
                                            <div>
                                                <div class="flex flex-wrap items-center justify-between gap-2">
                                                    <label for="document-{{ $typeDocument->id }}" class="text-sm font-bold text-[#27185f]">{{ $typeDocument->nom }}</label>
                                                    @if ($documentActuel)
                                                        <span class="text-xs font-semibold text-[#6d6684]">{{ $documentActuel->nom_original }}</span>
                                                    @endif
                                                </div>
                                                <input id="document-{{ $typeDocument->id }}" name="documents[{{ $typeDocument->id }}]" type="file" accept=".pdf,.jpg,.jpeg,.png" class="mt-2 block w-full rounded-md border border-[#d8d0ea] bg-[#f7f5fb] px-3 py-2 text-sm text-[#423b57] file:mr-3 file:rounded-md file:border-0 file:bg-[#27185f] file:px-3 file:py-2 file:font-bold file:text-white">
                                                @error("documents.{$typeDocument->id}")
                                                    <p class="mt-2 text-sm font-semibold text-[#b70f1e]">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @error('documents')
                                    <p class="mt-3 text-sm font-semibold text-[#b70f1e]">{{ $message }}</p>
                                @enderror

                                <button type="submit" class="mt-5 inline-flex w-full items-center justify-center rounded-md bg-[#d91426] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#b70f1e] sm:w-auto">
                                    Envoyer le complément
                                </button>
                            </form>
                        @endif
                    </section>
                @else
                    <div class="mx-auto mt-6 max-w-xl rounded-lg border border-[#f0c8ce] bg-[#fff4f5] p-5 text-center">
                        <p class="font-bold text-[#b70f1e]">Aucune candidature ne correspond a ce code.</p>
                        <p class="mt-2 text-sm text-[#6d6684]">Verifiez le code et l'adresse email figurant dans votre confirmation.</p>
                    </div>
                @endif
            @endisset
        </div>
    </section>
</x-public-layout>
