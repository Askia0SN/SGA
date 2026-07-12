<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReferentielRolesSeeder extends Seeder
{
    /**
     * Seed the simple roles and permissions used by the SGA workflow.
     */
    public function run(): void
    {
        $now = now();

        $permissions = [
            'candidatures.consulter' => 'Consulter les candidatures',
            'candidatures.traiter' => 'Traiter les candidatures',
            'candidatures.transmettre_jury' => 'Transmettre une candidature au jury',
            'candidatures.decider' => 'Admettre, refuser ou demander un complement',
            'documents.verifier' => 'Valider ou rejeter les documents',
            'programmes.gerer' => 'Gerer les programmes',
            'utilisateurs.gerer' => 'Gerer les utilisateurs',
            'roles.gerer' => 'Gerer les roles',
            'tableau_bord.consulter' => 'Consulter le tableau de bord',
            'parametres.gerer' => 'Gerer les parametres',
        ];

        foreach ($permissions as $nom => $libelle) {
            DB::table('permissions')->updateOrInsert(
                ['nom' => $nom],
                ['libelle' => $libelle, 'updated_at' => $now, 'created_at' => $now],
            );
        }

        $roles = [
            'super_admin' => [
                'libelle' => 'Super administrateur',
                'description' => 'Gere les utilisateurs, les roles, les programmes, les parametres et supervise tout le systeme.',
                'permissions' => array_keys($permissions),
            ],
            'service_admission' => [
                'libelle' => 'Service admission',
                'description' => 'Traite les dossiers, verifie les documents, demande des complements et transmet au jury.',
                'permissions' => [
                    'candidatures.consulter',
                    'candidatures.traiter',
                    'candidatures.transmettre_jury',
                    'documents.verifier',
                    'tableau_bord.consulter',
                ],
            ],
            'jury' => [
                'libelle' => 'Jury',
                'description' => 'Consulte les dossiers transmis et prend la decision finale.',
                'permissions' => [
                    'candidatures.consulter',
                    'candidatures.decider',
                ],
            ],
        ];

        foreach ($roles as $nom => $role) {
            DB::table('roles')->updateOrInsert(
                ['nom' => $nom],
                [
                    'libelle' => $role['libelle'],
                    'description' => $role['description'],
                    'updated_at' => $now,
                    'created_at' => $now,
                ],
            );
        }

        $roleIds = DB::table('roles')->pluck('id', 'nom');
        $permissionIds = DB::table('permissions')->pluck('id', 'nom');

        foreach ($roles as $nom => $role) {
            foreach ($role['permissions'] as $permission) {
                DB::table('role_permission')->insertOrIgnore([
                    'role_id' => $roleIds[$nom],
                    'permission_id' => $permissionIds[$permission],
                ]);
            }
        }
    }
}
