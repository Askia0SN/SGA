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
                'contenu_html' => '<p>Bonjour {nom_candidat},</p><p>Votre candidature au programme <strong>{programme}</strong> a bien été reçue. Notre service admission va maintenant procéder à l’étude de votre dossier.</p>',
            ],
            [
                'evenement' => 'candidature_brouillon',
                'objet' => 'Brouillon enregistré - EPF Africa',
                'contenu_html' => '<p>Bonjour {nom_candidat},</p><p>Votre brouillon de candidature au programme <strong>{programme}</strong> a bien été enregistré. Vous pourrez reprendre votre saisie ultérieurement.</p>',
            ],
            [
                'evenement' => 'complement_demande',
                'objet' => 'Complement de dossier requis - EPF Africa',
                'contenu_html' => '<p>Bonjour {nom_candidat},</p><p>Un complément est nécessaire pour poursuivre le traitement de votre dossier.</p><p><strong>Élément demandé :</strong> {message}</p>',
            ],
            [
                'evenement' => 'candidature_transmise_jury',
                'objet' => 'Mise a jour de votre candidature - EPF Africa',
                'contenu_html' => '<p>Bonjour {nom_candidat},</p><p>Votre dossier pour le programme <strong>{programme}</strong> est complet et a été transmis au jury pour évaluation.</p>',
            ],
            [
                'evenement' => 'candidature_admise',
                'objet' => 'Admission - EPF Africa',
                'contenu_html' => '<p>Bonjour {nom_candidat},</p><p>Nous avons le plaisir de vous annoncer votre admission au programme <strong>{programme}</strong>. Félicitations pour cette réussite !</p><p>{message}</p>',
            ],
            [
                'evenement' => 'candidature_refusee',
                'objet' => 'Resultat de candidature - EPF Africa',
                'contenu_html' => '<p>Bonjour {nom_candidat},</p><p>Après étude de votre dossier, votre candidature au programme <strong>{programme}</strong> n’a pas été retenue.</p><p>{message}</p>',
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
