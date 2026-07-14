<?php

namespace App\Http\Controllers;

use App\Enums\StatutCandidature;
use App\Models\Candidature;
use App\Models\Programme;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TableauBordController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $utilisateur */
        $utilisateur = $request->user();
        $candidatures = Candidature::query()
            ->visiblePour($utilisateur)
            ->with([
                'candidat',
                'programme.typesDocuments',
                'documents',
            ])
            ->get();

        $parStatut = collect(StatutCandidature::cases())
            ->mapWithKeys(fn (StatutCandidature $statut): array => [
                $statut->value => [
                    'libelle' => $statut->libelle(),
                    'nombre' => $candidatures->where('statut', $statut)->count(),
                ],
            ]);

        $decisionsParProgramme = $candidatures
            ->filter(fn (Candidature $candidature): bool => in_array($candidature->statut, [
                StatutCandidature::Admise,
                StatutCandidature::Refusee,
            ], true))
            ->groupBy('programme_id')
            ->map(function ($dossiers): array {
                /** @var Candidature $premier */
                $premier = $dossiers->first();

                return [
                    'programme' => $premier->programme->nom,
                    'admises' => $dossiers->where('statut', StatutCandidature::Admise)->count(),
                    'refusees' => $dossiers->where('statut', StatutCandidature::Refusee)->count(),
                ];
            })
            ->sortBy('programme')
            ->values();

        return view('dashboard', [
            'estJury' => $utilisateur->hasRole('jury')
                && ! $utilisateur->hasAnyRole(['super_admin', 'service_admission']),
            'nombreCandidatures' => $candidatures->count(),
            'nombreIncomplets' => $candidatures
                ->reject(fn (Candidature $candidature): bool => $candidature->statut->estFinal())
                ->filter(fn (Candidature $candidature): bool => ! $candidature->dossierEstComplet())
                ->count(),
            'nombreEnAttenteJury' => $candidatures->where('statut', StatutCandidature::TransmiseAuJury)->count(),
            'nombreDecisions' => $candidatures->whereIn('statut', [
                StatutCandidature::Admise,
                StatutCandidature::Refusee,
            ])->count(),
            'parStatut' => $parStatut,
            'decisionsParProgramme' => $decisionsParProgramme,
            'candidaturesRecentes' => $candidatures->sortByDesc('soumise_le')->take(6),
            'notifications' => $utilisateur->notificationsInternes()->latest()->limit(5)->get(),
            'nombreProgrammes' => Programme::query()->where('actif', true)->count(),
        ]);
    }
}
