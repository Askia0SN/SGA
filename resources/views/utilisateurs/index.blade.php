<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-extrabold uppercase text-[#d91426]">Super administration</p>
            <h1 class="mt-1 text-2xl font-extrabold text-[#191339]">Utilisateurs et acces</h1>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md border border-[#b9dfcc] bg-[#f1fbf6] px-4 py-3 text-sm font-semibold text-[#17603a]">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('warning'))
                <div class="rounded-md border border-[#f1d49a] bg-[#fff9eb] px-4 py-3 text-sm font-semibold text-[#815b10]">
                    {{ session('warning') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-md border border-[#f0c8ce] bg-[#fff4f5] px-4 py-3 text-sm font-semibold text-[#b70f1e]">
                    {{ $errors->first() }}
                </div>
            @endif

            <section class="border-b border-[#e8e2f5] bg-white pb-8">
                <div class="max-w-2xl">
                    <h2 class="text-lg font-extrabold text-[#27185f]">Inviter un utilisateur</h2>
                    <p class="mt-2 text-sm leading-6 text-[#6d6684]">Le nouvel utilisateur recevra un lien temporaire pour definir son mot de passe. Aucun mot de passe n'est communique par l'administrateur.</p>
                </div>

                <form method="POST" action="{{ route('utilisateurs.store') }}" class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-5">
                    @csrf
                    <div>
                        <x-input-label for="prenom" value="Prenom" />
                        <x-text-input id="prenom" name="prenom" type="text" class="mt-1 block w-full" :value="old('prenom')" required />
                    </div>
                    <div>
                        <x-input-label for="nom" value="Nom" />
                        <x-text-input id="nom" name="nom" type="text" class="mt-1 block w-full" :value="old('nom')" required />
                    </div>
                    <div>
                        <x-input-label for="email" value="Email professionnel" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
                    </div>
                    <div>
                        <x-input-label for="role" value="Role" />
                        <select id="role" name="role" class="mt-1 block w-full rounded-md border-[#d8d0ea] shadow-sm focus:border-[#d91426] focus:ring-[#d91426]" required>
                            @foreach ($roles as $role)
                                <option value="{{ $role->nom }}" @selected(old('role') === $role->nom)>{{ $role->libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <x-primary-button class="w-full justify-center py-3">Creer et inviter</x-primary-button>
                    </div>
                </form>
            </section>

            <section>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-lg font-extrabold text-[#27185f]">Comptes internes</h2>
                        <p class="mt-1 text-sm text-[#6d6684]">La desactivation conserve l'historique des actions sans autoriser de nouvelle connexion.</p>
                    </div>
                    <p class="text-sm font-bold text-[#6d6684]">{{ $utilisateurs->total() }} utilisateur(s)</p>
                </div>

                <div class="mt-5 overflow-x-auto rounded-md border border-[#e8e2f5] bg-white">
                    <table class="min-w-full divide-y divide-[#e8e2f5] text-left text-sm">
                        <thead class="bg-[#f7f5fb] text-xs uppercase text-[#6d6684]">
                            <tr>
                                <th class="px-4 py-3 font-extrabold">Utilisateur</th>
                                <th class="px-4 py-3 font-extrabold">Role</th>
                                <th class="px-4 py-3 font-extrabold">Etat</th>
                                <th class="px-4 py-3 font-extrabold">Derniere connexion</th>
                                <th class="px-4 py-3 text-right font-extrabold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#eee8f7]">
                            @foreach ($utilisateurs as $utilisateur)
                                @php($roleActuel = $utilisateur->roles->first()?->nom)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-4">
                                        <p class="font-bold text-[#191339]">{{ $utilisateur->name }}</p>
                                        <p class="mt-1 text-xs text-[#6d6684]">{{ $utilisateur->email }}</p>
                                    </td>
                                    <td class="px-4 py-4" colspan="3">
                                        <form id="modifier-utilisateur-{{ $utilisateur->id }}" method="POST" action="{{ route('utilisateurs.update', $utilisateur) }}" class="grid min-w-[500px] grid-cols-3 items-center gap-4">
                                            @csrf
                                            @method('PATCH')
                                            <select name="role" aria-label="Role de {{ $utilisateur->name }}" class="rounded-md border-[#d8d0ea] text-sm shadow-sm focus:border-[#d91426] focus:ring-[#d91426]">
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->nom }}" @selected($roleActuel === $role->nom)>{{ $role->libelle }}</option>
                                                @endforeach
                                            </select>
                                            <select name="actif" aria-label="Etat de {{ $utilisateur->name }}" class="rounded-md border-[#d8d0ea] text-sm shadow-sm focus:border-[#d91426] focus:ring-[#d91426]">
                                                <option value="1" @selected($utilisateur->actif)>Actif</option>
                                                <option value="0" @selected(! $utilisateur->actif)>Desactive</option>
                                            </select>
                                            <span class="text-sm text-[#6d6684]">{{ $utilisateur->derniere_connexion_le?->format('d/m/Y H:i') ?? 'Jamais' }}</span>
                                        </form>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-right">
                                        <button type="submit" form="modifier-utilisateur-{{ $utilisateur->id }}" class="rounded-md border border-[#d8d0ea] px-3 py-2 text-xs font-bold text-[#27185f] hover:border-[#27185f]">
                                            Enregistrer
                                        </button>
                                        <form method="POST" action="{{ route('utilisateurs.invitation', $utilisateur) }}" class="mt-2">
                                            @csrf
                                            <button type="submit" class="text-xs font-bold text-[#d91426] underline decoration-[#f0c8ce] underline-offset-4 hover:text-[#b70f1e]">
                                                Renvoyer l'invitation
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-5">{{ $utilisateurs->links() }}</div>
            </section>
        </div>
    </div>
</x-app-layout>
