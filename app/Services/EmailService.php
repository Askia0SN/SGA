<?php

namespace App\Services;

use App\Enums\StatutCandidature;
use App\Mail\CandidatureSoumiseMail;
use App\Models\Candidature;
use App\Models\EmailEnvoye;
use App\Models\ModeleEmail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use InvalidArgumentException;

class EmailService
{
    public function envoyerCandidatureSoumise(Candidature $candidature): EmailEnvoye
    {
        return $this->envoyerEvenement($candidature, 'candidature_soumise', [
            '{date_soumission}' => $candidature->soumise_le?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i'),
        ]);
    }

    public function envoyerCandidatureBrouillon(Candidature $candidature): EmailEnvoye
    {
        return $this->envoyerEvenement($candidature, 'candidature_brouillon', [
            '{date_enregistrement}' => now()->format('d/m/Y H:i'),
        ]);
    }

    public function envoyerDemandeComplement(
        Candidature $candidature,
        string $message,
        ?User $utilisateur = null,
    ): EmailEnvoye {
        return $this->envoyerEvenement($candidature, 'complement_demande', [
            '{message}' => $message,
        ], $utilisateur);
    }

    public function envoyerTransmissionJury(Candidature $candidature, ?User $utilisateur = null): EmailEnvoye
    {
        return $this->envoyerEvenement($candidature, 'candidature_transmise_jury', [
            '{date_transmission}' => $candidature->transmise_au_jury_le?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i'),
        ], $utilisateur);
    }

    public function envoyerDecision(
        Candidature $candidature,
        StatutCandidature $decision,
        ?string $commentaire,
        ?User $utilisateur = null,
    ): EmailEnvoye {
        $evenement = match ($decision) {
            StatutCandidature::Admise => 'candidature_admise',
            StatutCandidature::Refusee => 'candidature_refusee',
            default => throw new InvalidArgumentException('La décision doit être une admission ou un refus.'),
        };

        return $this->envoyerEvenement($candidature, $evenement, [
            '{message}' => trim((string) $commentaire),
            '{date_decision}' => $candidature->decision_le?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i'),
        ], $utilisateur);
    }

    /**
     * @param  array<string, string>  $variablesSpecifiques
     */
    private function envoyerEvenement(
        Candidature $candidature,
        string $evenement,
        array $variablesSpecifiques = [],
        ?User $utilisateur = null,
    ): EmailEnvoye {
        $candidature->loadMissing(['candidat', 'programme']);
        $modele = ModeleEmail::query()
            ->where('evenement', $evenement)
            ->where('actif', true)
            ->first();
        $fallback = $this->fallback($evenement);

        $variables = array_merge([
            '{nom_candidat}' => $candidature->candidat->prenom.' '.$candidature->candidat->nom,
            '{programme}' => $candidature->programme->nom,
            '{code_suivi}' => $candidature->code_suivi,
        ], $variablesSpecifiques);

        $variablesObjet = array_map(
            fn (string $valeur): string => str_replace(["\r", "\n"], ' ', strip_tags($valeur)),
            $variables,
        );
        $variablesHtml = array_map(fn (string $valeur): string => e($valeur), $variables);

        $objet = strtr($modele?->objet ?? $fallback['objet'], $variablesObjet);
        $contenu = strtr($modele?->contenu_html ?? $fallback['contenu'], $variablesHtml);
        $signature = $modele?->signature ?? 'Service Admission - EPF Africa';

        $log = EmailEnvoye::create([
            'candidature_id' => $candidature->id,
            'candidat_id' => $candidature->candidat_id,
            'user_id' => $utilisateur?->id,
            'evenement' => $evenement,
            'destinataire_email' => $candidature->candidat->email,
            'objet' => $objet,
            'contenu_html' => $contenu,
            'statut' => 'en_attente',
            'donnees' => $variables,
        ]);

        try {
            Mail::to($candidature->candidat->email)->send(
                new CandidatureSoumiseMail($candidature, $objet, $contenu, $signature, $evenement),
            );

            $log->update([
                'statut' => 'envoye',
                'envoye_le' => now(),
            ]);
        } catch (\Throwable $exception) {
            $log->update([
                'statut' => 'echec',
                'message_erreur' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        return $log;
    }

    /**
     * @return array{objet: string, contenu: string}
     */
    private function fallback(string $evenement): array
    {
        return match ($evenement) {
            'candidature_brouillon' => [
                'objet' => 'Brouillon de candidature enregistré - EPF Africa',
                'contenu' => '<p>Bonjour {nom_candidat},</p><p>Votre brouillon de candidature au programme <strong>{programme}</strong> a bien été enregistré. Vous pourrez reprendre votre saisie ultérieurement.</p>',
            ],
            'complement_demande' => [
                'objet' => 'Complément de dossier requis - EPF Africa',
                'contenu' => '<p>Bonjour {nom_candidat},</p><p>Un complément est nécessaire pour poursuivre le traitement de votre dossier.</p><p><strong>Élément demandé :</strong> {message}</p>',
            ],
            'candidature_transmise_jury' => [
                'objet' => 'Mise à jour de votre candidature - EPF Africa',
                'contenu' => '<p>Bonjour {nom_candidat},</p><p>Votre dossier pour le programme <strong>{programme}</strong> est complet et a été transmis au jury pour évaluation.</p>',
            ],
            'candidature_admise' => [
                'objet' => 'Admission - EPF Africa',
                'contenu' => '<p>Bonjour {nom_candidat},</p><p>Nous avons le plaisir de vous annoncer votre admission au programme <strong>{programme}</strong>. Félicitations pour cette réussite !</p><p>{message}</p>',
            ],
            'candidature_refusee' => [
                'objet' => 'Résultat de candidature - EPF Africa',
                'contenu' => '<p>Bonjour {nom_candidat},</p><p>Après étude de votre dossier, votre candidature au programme <strong>{programme}</strong> n’a pas été retenue.</p><p>{message}</p>',
            ],
            default => [
                'objet' => 'Confirmation de candidature - EPF Africa',
                'contenu' => '<p>Bonjour {nom_candidat},</p><p>Votre candidature au programme <strong>{programme}</strong> a bien été reçue. Notre service admission va maintenant procéder à l’étude de votre dossier.</p>',
            ],
        };
    }
}
