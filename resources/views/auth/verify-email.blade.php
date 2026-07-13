<x-guest-layout>
    <div class="mb-6">
        <p class="text-xs font-extrabold uppercase text-[#d91426]">Verification email</p>
        <h1 class="mt-1 text-2xl font-extrabold text-[#191339]">Confirmez votre adresse</h1>
        <p class="mt-2 text-sm leading-6 text-[#6d6684]">Un lien de verification a ete envoye a votre adresse email.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 text-sm font-semibold text-[#27185f]">
            Un nouveau lien de verification a ete envoye.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    Renvoyer le lien
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="rounded-md text-sm font-semibold text-[#6d6684] underline decoration-[#d8d0ea] underline-offset-4 hover:text-[#27185f] focus:outline-none focus:ring-2 focus:ring-[#d91426] focus:ring-offset-2">
                Deconnexion
            </button>
        </form>
    </div>
</x-guest-layout>
