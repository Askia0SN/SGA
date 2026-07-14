<?php

namespace App\Policies;

use App\Enums\StatutCandidature;
use App\Models\Candidature;
use App\Models\User;

class CandidaturePolicy
{
    public function before(User $user): ?bool
    {
        return $user->actif ? null : false;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'service_admission', 'jury']);
    }

    public function view(User $user, Candidature $candidature): bool
    {
        if ($user->hasAnyRole(['super_admin', 'service_admission'])) {
            return true;
        }

        if (! $user->hasRole('jury')) {
            return false;
        }

        if ($candidature->statut === StatutCandidature::ComplementDemande) {
            return $candidature->historiques()
                ->where('ancien_statut', StatutCandidature::TransmiseAuJury->value)
                ->where('nouveau_statut', StatutCandidature::ComplementDemande->value)
                ->exists();
        }

        return in_array($candidature->statut, [
            StatutCandidature::TransmiseAuJury,
            StatutCandidature::Admise,
            StatutCandidature::Refusee,
        ], true);
    }

    public function prendreEnCharge(User $user, Candidature $candidature): bool
    {
        return $user->hasAnyRole(['super_admin', 'service_admission'])
            && $candidature->statut === StatutCandidature::Soumise;
    }

    public function demanderComplement(User $user, Candidature $candidature): bool
    {
        $demandeAdmission = $user->hasAnyRole(['super_admin', 'service_admission'])
            && $candidature->statut === StatutCandidature::EnTraitementAdmission;

        $demandeJury = $user->hasAnyRole(['super_admin', 'jury'])
            && $candidature->statut === StatutCandidature::TransmiseAuJury;

        return $demandeAdmission || $demandeJury;
    }

    public function reprendreTraitement(User $user, Candidature $candidature): bool
    {
        return $user->hasAnyRole(['super_admin', 'service_admission'])
            && $candidature->statut === StatutCandidature::ComplementDemande;
    }

    public function transmettreAuJury(User $user, Candidature $candidature): bool
    {
        return $user->hasAnyRole(['super_admin', 'service_admission'])
            && $candidature->statut === StatutCandidature::EnTraitementAdmission;
    }

    public function decider(User $user, Candidature $candidature): bool
    {
        return $user->hasAnyRole(['super_admin', 'jury'])
            && $candidature->statut === StatutCandidature::TransmiseAuJury;
    }
}
