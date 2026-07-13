<x-guest-layout>
    <div class="mb-6">
        <p class="text-xs font-extrabold uppercase text-[#d91426]">Creation de compte</p>
        <h1 class="mt-1 text-2xl font-extrabold text-[#191339]">Nouvel utilisateur</h1>
        <p class="mt-2 text-sm leading-6 text-[#6d6684]">Le super administrateur attribuera ensuite le role adapte a l'utilisateur.</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" value="Nom complet" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Mot de passe" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Confirmation du mot de passe" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="rounded-md text-sm font-semibold text-[#6d6684] underline decoration-[#d8d0ea] underline-offset-4 hover:text-[#27185f] focus:outline-none focus:ring-2 focus:ring-[#d91426] focus:ring-offset-2" href="{{ route('login') }}">
                Deja un compte ?
            </a>

            <x-primary-button class="ms-4">
                Creer
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
