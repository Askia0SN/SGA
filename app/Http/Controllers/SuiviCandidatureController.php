<?php

namespace App\Http\Controllers;

use App\Models\Candidature;
use Illuminate\Http\Request;
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
        ]);

        $codeSuivi = strtoupper(trim($donnees['code_suivi']));

        $candidature = Candidature::query()
            ->with(['programme', 'historiques'])
            ->where('code_suivi', $codeSuivi)
            ->first();

        return view('candidatures.suivi', compact('candidature', 'codeSuivi'));
    }
}
