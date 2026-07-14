<x-guest-layout>
    <div class="mb-6">
        <p class="text-xs font-extrabold uppercase text-[#d91426]">Mot de passe oublie</p>
        <h1 class="mt-1 text-2xl font-extrabold text-[#191339]">Reinitialisation</h1>
        <p class="mt-2 text-sm leading-6 text-[#6d6684]">Indiquez votre email pour recevoir un lien de reinitialisation.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                Envoyer le lien
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
