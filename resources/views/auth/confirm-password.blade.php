<x-guest-layout>
    <div class="mb-6">
        <p class="text-xs font-extrabold uppercase text-[#d91426]">Confirmation requise</p>
        <h1 class="mt-1 text-2xl font-extrabold text-[#191339]">Zone securisee</h1>
        <p class="mt-2 text-sm leading-6 text-[#6d6684]">Confirmez votre mot de passe pour continuer.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Mot de passe" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                Confirmer
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
