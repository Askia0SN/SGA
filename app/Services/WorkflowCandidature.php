<?php

namespace App\Services;

use App\Enums\StatutCandidature;
use App\Models\Candidature;
use App\Models\User;
use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class WorkflowCandidature
{
    public function prendreEnCharge(Candidature $candidature, User $utilisateur, ?string $commentaire = null): Candidature
    {
        return $this->transitionner(
            $candidature,
            $utilisateur,
            'prendreEnCharge',
            StatutCandidature::EnTraitementAdmission,
            $commentaire,
        );
    }

    public function demanderComplement(Candidature $candidature, User $utilisateur, string $message): Candidature
    {
        $message = trim($message);

        if ($message === '') {
            throw ValidationException::withMessages([
                'message' => 'Le motif de la demande de complement est obligatoire.',
            ]);
        }

        return $this->transitionner(
            $candidature,
            $utilisateur,
            'demanderComplement',
            StatutCandidature::ComplementDemande,
            $message,
            apresTransition: function (Candidature $candidature) use ($utilisateur, $message): void {
                $candidature->messages()->create([
                    'user_id' => $utilisateur->id,
                    'type' => 'demande_complement',
                    'visibilite' => 'candidat',
                    'contenu' => $message,
                ]);

                if ($utilisateur->hasRole('jury')) {
                    $candidature->avisJury()->updateOrCreate(
                        ['jury_id' => $utilisateur->id],
                        [
                            'decision' => 'demander_complement',
                            'commentaire' => $message,
                            'decide_le' => now(),
                        ],
                    );
                }
            },
        );
    }

    public function reprendreTraitement(Candidature $candidature, User $utilisateur, ?string $commentaire = null): Candidature
    {
        return $this->transitionner(
            $candidature,
            $utilisateur,
            'reprendreTraitement',
            StatutCandidature::EnTraitementAdmission,
            $commentaire ?? 'Complement recu, reprise du traitement par le service admission.',
        );
    }

    public function transmettreAuJury(Candidature $candidature, User $utilisateur, ?string $commentaire = null): Candidature
    {
        return $this->transitionner(
            $candidature,
            $utilisateur,
            'transmettreAuJury',
            StatutCandidature::TransmiseAuJury,
            $commentaire,
            avantTransition: function (Candidature $candidature): void {
                $documentsManquants = $this->documentsObligatoiresNonValides($candidature);

                if ($documentsManquants !== []) {
                    throw ValidationException::withMessages([
                        'documents' => 'Le dossier ne peut pas etre transmis. Documents obligatoires non valides : '.implode(', ', $documentsManquants).'.',
                    ]);
                }
            },
            attributs: [
                'transmise_par' => $utilisateur->id,
                'transmise_au_jury_le' => now(),
            ],
        );
    }

    public function decider(
        Candidature $candidature,
        User $utilisateur,
        StatutCandidature $decision,
        ?string $commentaire = null,
    ): Candidature {
        if (! in_array($decision, [StatutCandidature::Admise, StatutCandidature::Refusee], true)) {
            throw ValidationException::withMessages([
                'decision' => 'La decision du jury doit etre admise ou refusee.',
            ]);
        }

        if ($decision === StatutCandidature::Refusee && trim((string) $commentaire) === '') {
            throw ValidationException::withMessages([
                'commentaire' => 'Le motif du refus est obligatoire.',
            ]);
        }

        return $this->transitionner(
            $candidature,
            $utilisateur,
            'decider',
            $decision,
            $commentaire,
            apresTransition: function (Candidature $candidature) use ($utilisateur, $decision, $commentaire): void {
                $candidature->avisJury()->updateOrCreate(
                    ['jury_id' => $utilisateur->id],
                    [
                        'decision' => $decision === StatutCandidature::Admise ? 'admettre' : 'refuser',
                        'commentaire' => $commentaire,
                        'decide_le' => now(),
                    ],
                );
            },
            attributs: [
                'decision_par' => $utilisateur->id,
                'decision_le' => now(),
            ],
        );
    }

    public function dossierEstComplet(Candidature $candidature): bool
    {
        return $this->documentsObligatoiresNonValides($candidature) === [];
    }

    /**
     * @return array<int, string>
     */
    public function documentsObligatoiresNonValides(Candidature $candidature): array
    {
        $documentsObligatoires = $candidature->programme
            ->typesDocuments()
            ->wherePivot('obligatoire', true)
            ->get(['types_documents.id', 'types_documents.nom']);

        if ($documentsObligatoires->isEmpty()) {
            return [];
        }

        $typesValides = $candidature->documents()
            ->where('statut', 'valide')
            ->whereIn('type_document_id', $documentsObligatoires->pluck('id'))
            ->pluck('type_document_id')
            ->unique();

        return $documentsObligatoires
            ->reject(fn ($typeDocument) => $typesValides->contains($typeDocument->id))
            ->pluck('nom')
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $attributs
     */
    private function transitionner(
        Candidature $candidature,
        User $utilisateur,
        string $autorisation,
        StatutCandidature $nouveauStatut,
        ?string $commentaire = null,
        ?Closure $avantTransition = null,
        ?Closure $apresTransition = null,
        array $attributs = [],
    ): Candidature {
        return DB::transaction(function () use (
            $candidature,
            $utilisateur,
            $autorisation,
            $nouveauStatut,
            $commentaire,
            $avantTransition,
            $apresTransition,
            $attributs,
        ): Candidature {
            $candidatureVerrouillee = Candidature::query()
                ->lockForUpdate()
                ->findOrFail($candidature->getKey());

            Gate::forUser($utilisateur)->authorize($autorisation, $candidatureVerrouillee);

            $ancienStatut = $candidatureVerrouillee->statut;

            if (! $ancienStatut->peutTransitionnerVers($nouveauStatut)) {
                throw ValidationException::withMessages([
                    'statut' => "Le passage de {$ancienStatut->value} vers {$nouveauStatut->value} n est pas autorise.",
                ]);
            }

            $avantTransition?->call($this, $candidatureVerrouillee);

            $candidatureVerrouillee->fill($attributs);
            $candidatureVerrouillee->statut = $nouveauStatut;
            $candidatureVerrouillee->save();

            $candidatureVerrouillee->historiques()->create([
                'ancien_statut' => $ancienStatut->value,
                'nouveau_statut' => $nouveauStatut->value,
                'modifie_par' => $utilisateur->id,
                'acteur' => $this->acteur($utilisateur),
                'commentaire' => $commentaire,
            ]);

            $apresTransition?->call($this, $candidatureVerrouillee);

            return $candidatureVerrouillee->fresh([
                'candidat',
                'programme',
                'documents',
                'historiques',
                'messages',
                'avisJury',
            ]);
        });
    }

    private function acteur(User $utilisateur): string
    {
        return match (true) {
            $utilisateur->hasRole('super_admin') => 'super_admin',
            $utilisateur->hasRole('jury') => 'jury',
            default => 'service_admission',
        };
    }
}
