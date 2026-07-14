<?php

namespace App\Services;

use App\Models\DocumentCandidature;
use App\Models\JournalAction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class VerificationDocumentCandidature
{
    public function valider(DocumentCandidature $document, User $utilisateur): DocumentCandidature
    {
        return $this->verifier($document, $utilisateur, 'valide');
    }

    public function rejeter(DocumentCandidature $document, User $utilisateur, string $motif): DocumentCandidature
    {
        $motif = trim($motif);

        if ($motif === '') {
            throw ValidationException::withMessages([
                'motifRejet' => 'Le motif du rejet est obligatoire.',
            ]);
        }

        return $this->verifier($document, $utilisateur, 'rejete', $motif);
    }

    private function verifier(
        DocumentCandidature $document,
        User $utilisateur,
        string $nouveauStatut,
        ?string $motif = null,
    ): DocumentCandidature {
        return DB::transaction(function () use ($document, $utilisateur, $nouveauStatut, $motif): DocumentCandidature {
            $documentVerrouille = DocumentCandidature::query()
                ->with('candidature')
                ->lockForUpdate()
                ->findOrFail($document->getKey());

            Gate::forUser($utilisateur)->authorize('verifier', $documentVerrouille);

            $anciennesValeurs = $documentVerrouille->only([
                'statut',
                'motif_rejet',
                'verifie_par',
                'verifie_le',
            ]);

            $documentVerrouille->update([
                'statut' => $nouveauStatut,
                'motif_rejet' => $nouveauStatut === 'rejete' ? $motif : null,
                'verifie_par' => $utilisateur->id,
                'verifie_le' => now(),
            ]);

            JournalAction::create([
                'acteur_type' => User::class,
                'acteur_id' => $utilisateur->id,
                'action' => 'document_candidature.'.$nouveauStatut,
                'cible_type' => DocumentCandidature::class,
                'cible_id' => $documentVerrouille->id,
                'anciennes_valeurs' => $anciennesValeurs,
                'nouvelles_valeurs' => $documentVerrouille->only([
                    'statut',
                    'motif_rejet',
                    'verifie_par',
                    'verifie_le',
                ]),
            ]);

            return $documentVerrouille->fresh(['typeDocument', 'verificateur', 'candidature']);
        });
    }
}
