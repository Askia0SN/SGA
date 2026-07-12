<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgrammesSeeder extends Seeder
{
    /**
     * Seed EPF Africa programmes shown in the admission material.
     */
    public function run(): void
    {
        $now = now();

        $programmes = [
            [
                'nom' => 'Classes preparatoires aux grandes ecoles',
                'niveau' => 'classe_preparatoire',
                'capacite_accueil' => 120,
                'description' => 'Cycle preparatoire en 2 ans a Dakar, puis cycle ingenieur en France. Majors possibles : aeronautique, structures durables, systemes numeriques, data sciences, energie et environnement.',
                'documents' => ['cni_passeport', 'releve_notes', 'diplome', 'lettre_motivation'],
            ],
            [
                'nom' => 'Licence Concepteur de systemes d information',
                'niveau' => 'licence',
                'capacite_accueil' => 80,
                'description' => 'Former des profils capables de programmer des applications web et mobile, automatiser les deploiements, administrer des serveurs, securiser les systemes et analyser les donnees.',
                'documents' => ['cni_passeport', 'cv', 'releve_notes', 'diplome', 'lettre_motivation'],
            ],
            [
                'nom' => 'Licence Management de la transition numerique',
                'niveau' => 'licence',
                'capacite_accueil' => 80,
                'description' => 'Former des profils capables de piloter la transformation digitale, exploiter les donnees pour la prise de decision et gerer des projets numeriques.',
                'documents' => ['cni_passeport', 'cv', 'releve_notes', 'diplome', 'lettre_motivation'],
            ],
            [
                'nom' => 'Licence Energie et environnement',
                'niveau' => 'licence',
                'capacite_accueil' => 80,
                'description' => 'Former des profils sur les systemes d energie electrique et thermique, la production, le stockage, le transport et le management energetique et environnemental.',
                'documents' => ['cni_passeport', 'cv', 'releve_notes', 'diplome', 'lettre_motivation'],
            ],
            [
                'nom' => 'Master Informatique',
                'niveau' => 'master',
                'capacite_accueil' => 50,
                'description' => 'Approfondissement en genie logiciel, systemes d information, cloud, data, securite et pilotage de projets informatiques.',
                'documents' => ['cni_passeport', 'cv', 'releve_notes', 'diplome', 'lettre_motivation', 'lettre_recommandation'],
            ],
            [
                'nom' => 'Master Energie',
                'niveau' => 'master',
                'capacite_accueil' => 50,
                'description' => 'Approfondissement sur les energies, l efficacite energetique, la transition environnementale et la gestion de projets energetiques.',
                'documents' => ['cni_passeport', 'cv', 'releve_notes', 'diplome', 'lettre_motivation', 'lettre_recommandation'],
            ],
        ];

        foreach ($programmes as $programme) {
            DB::table('programmes')->updateOrInsert(
                ['nom' => $programme['nom']],
                [
                    'niveau' => $programme['niveau'],
                    'capacite_accueil' => $programme['capacite_accueil'],
                    'date_ouverture' => '2026-01-01',
                    'date_fermeture' => '2026-10-31',
                    'frais_scolarite' => null,
                    'echeancier_paiement' => null,
                    'description' => $programme['description'],
                    'actif' => true,
                    'updated_at' => $now,
                    'created_at' => $now,
                ],
            );

            $programmeId = DB::table('programmes')
                ->where('nom', $programme['nom'])
                ->value('id');

            foreach ($programme['documents'] as $ordre => $codeDocument) {
                $typeDocumentId = DB::table('types_documents')
                    ->where('code', $codeDocument)
                    ->value('id');

                if (! $typeDocumentId) {
                    continue;
                }

                DB::table('programme_type_document')->updateOrInsert(
                    [
                        'programme_id' => $programmeId,
                        'type_document_id' => $typeDocumentId,
                    ],
                    [
                        'obligatoire' => true,
                        'ordre' => $ordre + 1,
                        'updated_at' => $now,
                        'created_at' => $now,
                    ],
                );
            }
        }
    }
}
