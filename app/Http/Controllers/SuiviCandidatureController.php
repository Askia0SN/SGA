<?php

namespace App\Http\Controllers;

use App\Models\Candidature;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SuiviCandidatureController extends Controller
{
    public function index(): View
    {
        return view('candidatures.suivi');
    }

    public function rechercher(Request $request): View
    {
        $donnees = $request->validate([
            'code_suivi' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        $codeSuivi = strtoupper(trim($donnees['code_suivi']));
        $email = Str::lower(trim($donnees['email']));

        $candidature = Candidature::query()
            ->with([
                'programme',
                'historiques',
                'messages' => fn ($query) => $query
                    ->where('visibilite', 'candidat')
                    ->latest(),
            ])
            ->where('code_suivi', $codeSuivi)
            ->whereHas('candidat', fn ($query) => $query->whereRaw('LOWER(email) = ?', [$email]))
            ->first();

        return view('candidatures.suivi', compact('candidature', 'codeSuivi', 'email'));
    }
}
