<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModelesEmailsSeeder extends Seeder
{
    /**
     * Seed editable email templates for the candidate workflow.
     */
    public function run(): void
    {
        $now = now();

        $modeles = [
            [
                'evenement' => 'candidature_soumise',
                'objet' => 'Confirmation de candidature - EPF Africa',
                'contenu_html' => '<p>Bonjour {nom_candidat},</p><p>Votre candidature au programme {programme} a bien ete recue. Votre code de suivi est <strong>{code_suivi}</strong>.</p>',
            ],
            [
                'evenement' => 'candidature_brouillon',
                'objet' => 'Brouillon enregistré - EPF Africa',
                'contenu_html' => '<p>Bonjour {nom_candidat},</p><p>Votre brouillon de candidature au programme {programme} a bien ete enregistre. Votre code de suivi est <strong>{code_suivi}</strong>.</p>',
            ],
            [
                'evenement' => 'complement_demande',
                'objet' => 'Complement de dossier requis - EPF Africa',
                'contenu_html' => '<p>Bonjour {nom_candidat},</p><p>Un complement est necessaire pour poursuivre le traitement de votre dossier : {message}.</p>',
            ],
            [
                'evenement' => 'candidature_transmise_jury',
                'objet' => 'Mise a jour de votre candidature - EPF Africa',
                'contenu_html' => '<p>Bonjour {nom_candidat},</p><p>Votre dossier pour le programme {programme} est complet et a ete transmis au jury.</p>',
            ],
            [
                'evenement' => 'candidature_admise',
                'objet' => 'Admission - EPF Africa',
                'contenu_html' => '<p>Bonjour {nom_candidat},</p><p>Nous avons le plaisir de vous annoncer votre admission au programme {programme}.</p>',
            ],
            [
                'evenement' => 'candidature_refusee',
                'objet' => 'Resultat de candidature - EPF Africa',
                'contenu_html' => '<p>Bonjour {nom_candidat},</p><p>Apres etude de votre dossier, votre candidature au programme {programme} n a pas ete retenue.</p><p>{message}</p>',
            ],
        ];

        foreach ($modeles as $modele) {
            DB::table('modeles_emails')->updateOrInsert(
                ['evenement' => $modele['evenement']],
                [
                    'objet' => $modele['objet'],
                    'contenu_html' => $modele['contenu_html'],
                    'signature' => 'Service Admission - EPF Africa',
                    'actif' => true,
                    'updated_at' => $now,
                    'created_at' => $now,
                ],
            );
        }
    }
}
