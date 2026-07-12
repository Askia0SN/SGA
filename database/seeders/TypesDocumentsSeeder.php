<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypesDocumentsSeeder extends Seeder
{
    /**
     * Seed reusable document types requested from candidates.
     */
    public function run(): void
    {
        $now = now();

        $types = [
            'cni_passeport' => 'CNI ou passeport',
            'cv' => 'CV',
            'releve_notes' => 'Releve de notes',
            'diplome' => 'Diplome',
            'lettre_motivation' => 'Lettre de motivation',
            'lettre_recommandation' => 'Lettre de recommandation',
            'autre' => 'Autre document',
        ];

        foreach ($types as $code => $nom) {
            DB::table('types_documents')->updateOrInsert(
                ['code' => $code],
                [
                    'nom' => $nom,
                    'description' => null,
                    'actif' => true,
                    'updated_at' => $now,
                    'created_at' => $now,
                ],
            );
        }
    }
}
