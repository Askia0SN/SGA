<?php

namespace Tests\Feature;

use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\Programme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortailCandidatTest extends TestCase
{
    use RefreshDatabase;

    public function test_accueil_candidat_affiche_les_actions_principales(): void
    {
        $this->get(route('accueil'))
            ->assertOk()
            ->assertSee('Consulter les programmes')
            ->assertSee('Suivre ma candidature')
            ->assertSee(route('admission.accueil'));
    }

    public function test_page_admission_propose_uniquement_la_connexion(): void
    {
        $this->get(route('admission.accueil'))
            ->assertOk()
            ->assertSee(route('login'))
            ->assertDontSee('S inscrire');
    }

    public function test_liste_des_programmes_affiche_uniquement_les_programmes_actifs(): void
    {
        Programme::create($this->donneesProgramme('Licence Informatique', true));
        Programme::create($this->donneesProgramme('Programme ferme', false));

        $this->get(route('programmes.index'))
            ->assertOk()
            ->assertSee('Licence Informatique')
            ->assertDontSee('Programme ferme');
    }

    public function test_candidat_peut_retrouver_sa_candidature_avec_son_code(): void
    {
        $programme = Programme::create($this->donneesProgramme('Master Informatique', true));
        $candidat = Candidat::create([
            'prenom' => 'Awa',
            'nom' => 'Ndiaye',
            'email' => 'awa@example.com',
        ]);

        Candidature::create([
            'candidat_id' => $candidat->id,
            'programme_id' => $programme->id,
            'code_suivi' => 'SGA-2026-ABC123',
            'statut' => 'transmise_au_jury',
            'soumise_le' => now(),
        ]);

        $this->post(route('candidatures.suivi.rechercher'), [
            'code_suivi' => 'sga-2026-abc123',
            'email' => 'awa@example.com',
        ])
            ->assertOk()
            ->assertSee('Transmise au jury')
            ->assertSee('Master Informatique');
    }

    public function test_un_email_incorrect_ne_permet_pas_de_consulter_la_candidature(): void
    {
        $programme = Programme::create($this->donneesProgramme('Master Energie', true));
        $candidat = Candidat::create([
            'prenom' => 'Awa',
            'nom' => 'Ndiaye',
            'email' => 'awa@example.com',
        ]);

        Candidature::create([
            'candidat_id' => $candidat->id,
            'programme_id' => $programme->id,
            'code_suivi' => 'SGA-2026-SECRET',
            'statut' => 'soumise',
        ]);

        $this->post(route('candidatures.suivi.rechercher'), [
            'code_suivi' => 'SGA-2026-SECRET',
            'email' => 'autre@example.com',
        ])
            ->assertOk()
            ->assertSee('Aucune candidature ne correspond')
            ->assertDontSee('Master Energie');
    }

    private function donneesProgramme(string $nom, bool $actif): array
    {
        return [
            'nom' => $nom,
            'niveau' => 'licence',
            'date_ouverture' => now()->startOfYear(),
            'date_fermeture' => now()->endOfYear(),
            'description' => 'Description du programme.',
            'actif' => $actif,
        ];
    }
}
