<footer style="background: linear-gradient(90deg, #6f22de 0%, #d91426 50%, #191339 100%); color: white;" class="relative z-20 text-white shadow-2xl shadow-[#6f22de]/20">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3 items-start">
            <div class="flex flex-col gap-3">
                
                    <div>
                        <p class="text-sm font-extrabold uppercase">EPF Africa</p>
                        <p class="text-xs opacity-80">Admissions</p>
                    </div>
                </a>

                <p class="mt-2 text-sm max-w-sm text-white/90">Un espace d'admission clair, transparent et humain pour vous accompagner à chaque étape.</p>
            </div>

            <div class="flex flex-col gap-2">
                <h4 class="text-sm font-bold uppercase text-white">Liens utiles</h4>
                <nav class="mt-2 flex flex-col gap-2 text-sm">
                    <a href="{{ route('programmes.index') }}" class="hover:text-[#ffecec] text-white">Programmes</a>
                    <a href="{{ route('candidatures.suivi') }}" class="hover:text-[#ffecec] text-white">Suivre ma candidature</a>
                    <a href="{{ route('admission.accueil') }}" class="hover:text-[#ffecec] text-white">Espace admission</a>
                </nav>
            </div>

            <div class="flex flex-col gap-4">
                <h4 class="text-sm font-bold uppercase text-white">Contact</h4>
                <p class="text-sm text-white">contact@epf.africa</p>
                <div class="flex items-center gap-3">
                    <a href="#" class="social-icon" aria-label="Twitter">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="opacity-90 hover:opacity-100">
                            <path d="M22 5.92c-.63.28-1.3.48-2 .57.72-.43 1.27-1.12 1.53-1.94-.67.4-1.42.69-2.21.84C18.76 4.4 17.7 4 16.56 4c-1.86 0-3.37 1.5-3.37 3.35 0 .26.03.51.08.75C9.7 8 6.1 6.13 3.9 3.16c-.29.5-.46 1.08-.46 1.7 0 1.17.6 2.2 1.52 2.8-.55-.02-1.07-.17-1.52-.42v.04c0 1.62 1.15 2.97 2.68 3.28-.28.08-.57.12-.87.12-.21 0-.42-.02-.62-.06.42 1.32 1.65 2.28 3.11 2.31-1.14.9-2.58 1.44-4.15 1.44-.27 0-.54-.02-.8-.05 1.49.96 3.27 1.52 5.18 1.52 6.21 0 9.6-5.14 9.6-9.6v-.44c.66-.47 1.23-1.06 1.69-1.73-.6.27-1.25.45-1.92.53z" fill="white"/>
                        </svg>
                    </a>
                    <a href="#" class="social-icon" aria-label="Facebook">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="opacity-90 hover:opacity-100">
                            <path d="M22 12.07C22 6.52 17.52 2 12 2S2 6.52 2 12.07C2 17.06 5.66 21.18 10.44 21.95v-6.95H7.9v-2.98h2.54V9.41c0-2.5 1.49-3.88 3.77-3.88 1.09 0 2.23.2 2.23.2v2.45h-1.25c-1.24 0-1.63.77-1.63 1.56v1.88h2.78l-.44 2.98h-2.34v6.95C18.34 21.18 22 17.06 22 12.07z" fill="white"/>
                        </svg>
                    </a>
                    <a href="#" class="social-icon" aria-label="LinkedIn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="opacity-90 hover:opacity-100">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM8.34 17.34H6.06V10.5h2.28v6.84zM7.2 9.3c-.73 0-1.22-.5-1.22-1.13 0-.64.5-1.13 1.25-1.13.75 0 1.22.49 1.23 1.13 0 .63-.49 1.13-1.26 1.13zM18 17.34h-2.27v-3.36c0-.8-.29-1.35-1.02-1.35-.56 0-.9.38-1.05.75-.05.12-.06.29-.06.46v3.5H11.3s.03-5.68 0-6.27h2.27v.89c.3-.47.84-1.14 2.05-1.14 1.5 0 2.62.98 2.62 3.08v3.44z" fill="white"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-6 border-t border-white/20 pt-4 text-center text-xs opacity-90">
            © {{ date('Y') }} EPF Africa — Tous droits réservés
        </div>
    </div>
</footer>
