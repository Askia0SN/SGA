<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6">
        <p class="text-xs font-extrabold uppercase text-[#d91426]">Back-office admission</p>
        <h1 class="mt-1 text-2xl font-extrabold text-[#191339]">Connexion</h1>
        <p class="mt-2 text-sm leading-6 text-[#6d6684]">Acces reserve au service admission, au jury et au super administrateur.</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Mot de passe" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-[#d8d0ea] text-[#d91426] shadow-sm focus:ring-[#d91426]" name="remember">
                <span class="ms-2 text-sm text-[#6d6684]">Se souvenir de moi</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="rounded-md text-sm font-semibold text-[#6d6684] underline decoration-[#d8d0ea] underline-offset-4 hover:text-[#27185f] focus:outline-none focus:ring-2 focus:ring-[#d91426] focus:ring-offset-2" href="{{ route('password.request') }}">
                    Mot de passe oublie ?
                </a>
            @endif

            <x-primary-button class="ms-3">
                Se connecter
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
