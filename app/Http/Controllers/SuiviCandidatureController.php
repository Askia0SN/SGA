<?php

namespace App\Http\Controllers;

use App\Enums\StatutCandidature;
use App\Models\Candidature;
use App\Services\ComplementCandidatureService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SuiviCandidatureController extends Controller
{
    public function index(Request $request): View
    {
        $codeSuivi = $request->session()->get('suivi_code');
        $email = $request->session()->get('suivi_email');
        $candidature = $codeSuivi && $email
            ? $this->trouverCandidature($codeSuivi, $email)
            : null;

        return view('candidatures.suivi', compact('candidature', 'codeSuivi', 'email'));
    }

    public function rechercher(Request $request): View
    {
        $donnees = $request->validate([
            'code_suivi' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        $codeSuivi = strtoupper(trim($donnees['code_suivi']));
        $email = Str::lower(trim($donnees['email']));

        $candidature = $this->trouverCandidature($codeSuivi, $email);

        return view('candidatures.suivi', compact('candidature', 'codeSuivi', 'email'));
    }

    public function envoyerComplement(
        Request $request,
        ComplementCandidatureService $service,
    ): RedirectResponse {
        $donnees = $request->validate([
            'code_suivi' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'message' => ['nullable', 'string', 'max:2000'],
            'documents' => ['nullable', 'array', 'max:10'],
            'documents.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $codeSuivi = Str::upper(trim($donnees['code_suivi']));
        $email = Str::lower(trim($donnees['email']));
        $documents = $request->file('documents', []);

        if (trim((string) ($donnees['message'] ?? '')) === '' && $documents === []) {
            throw ValidationException::withMessages([
                'documents' => 'Ajoutez un message ou au moins un document.',
            ]);
        }

        $candidature = $this->trouverCandidature($codeSuivi, $email);

        if (! $candidature || $candidature->statut !== StatutCandidature::ComplementDemande) {
            throw ValidationException::withMessages([
                'code_suivi' => 'Ce dossier ne peut pas recevoir de complément.',
            ]);
        }

        $service->soumettre(
            $candidature,
            $documents,
            $donnees['message'] ?? null,
        );

        return redirect()
            ->route('candidatures.suivi')
            ->with('status', 'Votre complément a été transmis au service admission.')
            ->with('suivi_code', $codeSuivi)
            ->with('suivi_email', $email);
    }

    private function trouverCandidature(string $codeSuivi, string $email): ?Candidature
    {
        return Candidature::query()
            ->with([
                'programme.typesDocuments',
                'documents',
                'historiques',
                'messages' => fn ($query) => $query
                    ->where('visibilite', 'candidat')
                    ->latest(),
            ])
            ->where('code_suivi', Str::upper(trim($codeSuivi)))
            ->whereHas('candidat', fn ($query) => $query->whereRaw('LOWER(email) = ?', [Str::lower(trim($email))]))
            ->first();
    }
}
