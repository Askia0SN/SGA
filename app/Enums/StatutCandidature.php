<?php

namespace App\Enums;

enum StatutCandidature: string
{
    case Brouillon = 'brouillon';
    case Soumise = 'soumise';
    case ComplementDemande = 'complement_demande';
    case EnTraitementAdmission = 'en_traitement_admission';
    case TransmiseAuJury = 'transmise_au_jury';
    case Admise = 'admise';
    case Refusee = 'refusee';
    case Abandonnee = 'abandonnee';

    public function libelle(): string
    {
        return match ($this) {
            self::Brouillon => 'Brouillon',
            self::Soumise => 'Candidature soumise',
            self::ComplementDemande => 'Complement demande',
            self::EnTraitementAdmission => 'En cours d etude',
            self::TransmiseAuJury => 'Transmise au jury',
            self::Admise => 'Candidature admise',
            self::Refusee => 'Candidature refusee',
            self::Abandonnee => 'Candidature abandonnee',
        };
    }

    /**
     * @return array<int, self>
     */
    public function transitionsAutorisees(): array
    {
        return match ($this) {
            self::Brouillon => [self::Soumise, self::Abandonnee],
            self::Soumise => [self::EnTraitementAdmission, self::Abandonnee],
            self::EnTraitementAdmission => [self::ComplementDemande, self::TransmiseAuJury, self::Abandonnee],
            self::ComplementDemande => [self::EnTraitementAdmission, self::Abandonnee],
            self::TransmiseAuJury => [self::ComplementDemande, self::Admise, self::Refusee],
            self::Admise, self::Refusee, self::Abandonnee => [],
        };
    }

    public function peutTransitionnerVers(self $nouveauStatut): bool
    {
        return in_array($nouveauStatut, $this->transitionsAutorisees(), true);
    }

    public function estFinal(): bool
    {
        return in_array($this, [self::Admise, self::Refusee, self::Abandonnee], true);
    }
}
