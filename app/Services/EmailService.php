<?php

namespace App\Services;

use App\Mail\CandidatureSoumiseMail;
use App\Models\Candidature;
use App\Models\EmailEnvoye;
use App\Models\ModeleEmail;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    public function envoyerCandidatureSoumise(Candidature $candidature): EmailEnvoye
    {
        $candidature->load(['candidat', 'programme']);
        $modele = ModeleEmail::query()
            ->where('evenement', 'candidature_soumise')
            ->where('actif', true)
            ->first();

        $variables = [
            '{nom_candidat}' => $candidature->candidat->prenom.' '.$candidature->candidat->nom,
            '{programme}' => $candidature->programme->nom,
            '{code_suivi}' => $candidature->code_suivi,
            '{date_soumission}' => $candidature->soumise_le?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i'),
        ];

        $objet = $modele?->objet ?? 'Confirmation de candidature - EPF Africa';
        $contenu = $modele?->contenu_html ?? '<p>Bonjour {nom_candidat},</p><p>Votre code de suivi est <strong>{code_suivi}</strong>.</p>';
        $signature = $modele?->signature ?? 'Service Admission - EPF Africa';

        $objet = str_replace(array_keys($variables), array_values($variables), $objet);
        $contenu = str_replace(array_keys($variables), array_values($variables), $contenu);

        $log = EmailEnvoye::create([
            'candidature_id' => $candidature->id,
            'candidat_id' => $candidature->candidat_id,
            'evenement' => 'candidature_soumise',
            'destinataire_email' => $candidature->candidat->email,
            'objet' => $objet,
            'contenu_html' => $contenu,
            'statut' => 'en_attente',
            'donnees' => $variables,
        ]);

        try {
            Mail::to($candidature->candidat->email)->send(
                new CandidatureSoumiseMail($candidature, $objet, $contenu, $signature)
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

    public function envoyerCandidatureBrouillon(Candidature $candidature): EmailEnvoye
    {
        $candidature->load(['candidat', 'programme']);
        $modele = ModeleEmail::query()
            ->where('evenement', 'candidature_brouillon')
            ->where('actif', true)
            ->first();

        $variables = [
            '{nom_candidat}' => $candidature->candidat->prenom.' '.$candidature->candidat->nom,
            '{programme}' => $candidature->programme->nom,
            '{code_suivi}' => $candidature->code_suivi,
            '{date_enregistrement}' => now()->format('d/m/Y H:i'),
        ];

        $objet = $modele?->objet ?? 'Brouillon de candidature enregistré - EPF Africa';
        $contenu = $modele?->contenu_html ?? '<p>Bonjour {nom_candidat},</p><p>Votre brouillon de candidature au programme {programme} a bien été enregistré. Votre code de suivi est <strong>{code_suivi}</strong>.</p>';
        $signature = $modele?->signature ?? 'Service Admission - EPF Africa';

        $objet = str_replace(array_keys($variables), array_values($variables), $objet);
        $contenu = str_replace(array_keys($variables), array_values($variables), $contenu);

        $log = EmailEnvoye::create([
            'candidature_id' => $candidature->id,
            'candidat_id' => $candidature->candidat_id,
            'evenement' => 'candidature_brouillon',
            'destinataire_email' => $candidature->candidat->email,
            'objet' => $objet,
            'contenu_html' => $contenu,
            'statut' => 'en_attente',
            'donnees' => $variables,
        ]);

        try {
            Mail::to($candidature->candidat->email)->send(
                new CandidatureSoumiseMail($candidature, $objet, $contenu, $signature)
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
}
